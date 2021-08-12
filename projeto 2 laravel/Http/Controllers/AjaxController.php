<?php

namespace App\Http\Controllers;

use App\Award;
use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\BlogRecommend;
use App\Blog;
use App\BlogComment;
use App\CategoryAward;
use App\ContentManager;
use App\Enums\AjaxGetUsersSource;
use App\Enums\AjaxRecommendSource;
use App\Enums\NotificationsSource;
use App\Enums\CommentsModules;
use App\Enums\NotificationsTarget;
use App\Event;
use App\Helpers;
use App\Institution;
use App\Keyword;
use App\NotificationsConfig;
use App\Notifications\SocialTecnologyRecommended;
use App\Notifications\FavoriteFollowSocialTecnology;
use App\Notifications\UserFavorited;
use App\Notifications\TimelineUserFavorited;
use App\Notifications\TimelineUserFavoritedUnfollow;
use App\Notifications\TimelineUserSocialTecnologyFollow;
use App\Notifications\TimelineUserEventInterest;
use App\Notifications\TimelineUserEventRemoveInterest;
use App\Notifications\UserEventFavorited;
use App\Notifications\UserEventRemoveFavorited;
use App\SocialTecnology;
use App\SocialTecnologyComment;
use App\SocialTecnologyDeploymentPlace;
use App\SocialTecnologyImage;
use App\SocialTecnologyPartner;
use App\SocialTecnologyRecommend;
use App\User;
use Exception;
use Illuminate\Support\Facades\Storage;
use App\UserFavorite;
use Illuminate\Support\Collection;
use Throwable;

class AjaxController extends Controller
{
    /**
     * Get Social Tecnologies.
     * <code>
     * $request = Illuminate\Http\Request({
     *     'keyword' => string,     // Filter for Social Tecnology's Full Text Search (Optional)
     * });
     * </code>
     * @method GET
     * @param Request $request (See above)
     * @return JsonResponse
     */
    public function get_socialtecnologies_autocomplete(Request $request)
    {

        // Cria o Objeto da query para realiar a pesquisa
        $query = SocialTecnology::query();

        // Verifica se enviou busca por Palavra Chave
        if (!empty($request->keyword)) {

            // Monta a pesquisa pelo índice fulltext
            Helpers::make_fulltext_where(
                $query,
                'social_tecnologies',
                'socialtecnology_name,fulltext_themes,fulltext_keywords,fulltext_institution',
                'social_tecnologies.id',
                $request->keyword, 5);
        }

        // Busca as tecnologias sociais de acordo com as palavras digitadas nas index fulltext
        // 4640 Colocando  where na consulta para retornar apenas itens que tenham algum tipo de certificação.
        # #4954 - permitir somente a exibição no portal as TS que já foram certificadas e publicadas
        $response_data = $query->whereNotNull('award_status')
            ->whereHas('evaluationsStep', function (Builder $query) {
                $query->join('category_award_evaluation_steps', 'social_tecnology_step_evaluation.evaluationStep_id', '=', 'category_award_evaluation_steps.id');
                $query->where('category_award_evaluation_steps.approvedListPublished_flag', 1);
                $query->whereNotNull('category_award_evaluation_steps.approvedList_published_at');
            })->get();

        // Converte para Json e retorna
        return response()->json([
            'data' => $response_data
        ]);
    }

    /**
     * Get Institutions.
     * <code>
     * $request = Illuminate\Http\Request({
     *     'keyword' => string,     // Filter for Institution's Name (Optional)
     * });
     * </code>
     * @method GET
     * @param Request $request (See above)
     * @return JsonResponse
     */
    public function get_institutions(Request $request)
    {
        // Cria o Objeto da query para realiar a pesquisa
        $query = Institution::query();

        // Verifica se enviou busca pelo nome da Instituição
        if (!empty($request->keyword)) {

            // Quebra por espaços e remove espaços em branco.
            $search_words = preg_split('/\s+/', $request->keyword, -1, PREG_SPLIT_NO_EMPTY);

            // Busca as instituições de acordo com as palavras digitadas (AND)
            $institutions = $query->where(function ($q) use ($search_words) {
                foreach ($search_words as $value) {
                    $q->where('institution_name', 'like', "%{$value}%");
                }
            });
        }

        // Busca as tecnologias sociais de acordo com as palavras digitadas nas index fulltext
        $response_data = $query->orderBy('institution_name')->get();

        // Converte para Json e retorna
        return response()->json([
            'data' => $response_data
        ]);
    }

    /**
     * Get Users.
     * <code>
     * $request = Illuminate\Http\Request({
     *     'source'         => string,     // Request Origin (Optional, Options: AjaxGetUsersSource)
     *     'keyword'        => string,     // Filter for User's Name or Email (Optional)
     * });
     * </code>
     * @method GET
     * @param Request $request (See above)
     * @return JsonResponse
     */
    public function get_users(Request $request)
    {
        // Cria o Objeto da query para realiar a pesquisa
        $query = User::query();

        // Verifica se foi enviado a origem da requisição
        if (!empty($request->source)) {

            switch ($request->source) {

                // Buscando usuários para vincular à instituição
                case AjaxGetUsersSource::Institution:

                    // Busca os usuários que não estão vinculados à instituição
                    $query = $query->whereNull('institution_id');
                    break;

                // Buscando usuários para seguir
                case AjaxGetUsersSource::Profile:
                    // Nenhuma ação específica quando estiver vindo do perfil do usuário
                    break;

                default:
                    // Nenhuma ação específica default
                    break;
            }
        }


        // Verifica se enviou busca por Palavra Chave
        if (!empty($request->keyword)) {

            // Quebra por espaços e remove espaços em branco.
            $search_words = preg_split('/\s+/', $request->keyword, -1, PREG_SPLIT_NO_EMPTY);

            // Busca os usuários de acordo com as palavras digitadas (AND)
            $query = $query->where(function ($q) use ($search_words) {
                foreach ($search_words as $value) {
                    $q->where('name', 'like', "%{$value}%")
                        ->orWhere('email', 'like', "%{$value}%");
                }
            });
        }

        // Realiza a busca dos Usuários
        $response_data = $query->orderBy('name')->get();

        // Converte para Json e retorna
        return response()->json([
            'data' => $response_data
        ]);
    }

    public function getUsersByInstitutionId(Request $request)
    {
        $institution = Institution::query()->where('id', $request->institution_id)->first();

        // $users = \App\User::join('institution_user', 'institution_user.user_id', '=', 'users.id')
        //     ->where('institution_user.institution_id', $request->institution_id);

        $users = [];

        if (!empty($request->keyword)) {
            $search_words = preg_split('/\s+/', $request->keyword, -1, PREG_SPLIT_NO_EMPTY);

            $users = $institution->users()->where(function ($q) use ($search_words) {
                foreach ($search_words as $value) {
                    $q->where('name', 'like', "%{$value}%")
                        ->orWhere('email', 'like', "%{$value}%");
                }
            });
        }

        return response()->json([
            'data' => $users->orderBy('name')->get()
        ]);
    }

    /**
     * Get CategoryAwards by Award
     * <code>
     * $request = Illuminate\Http\Request({
     *     'award_id'        => integer,    // Award ID to get their category (Required)
     * });
     * </code>
     * @method GET
     * @param Request $request (See above)
     * @return JsonResponse
     */
    public function get_category_awards_by_awards(Request $request)
    {
        // Busca as categorias
        $category_award = Award::find($request->award_id)->categoryAwards;

        foreach($category_award as $cat) {
            $category['id'] = $cat->id;
            $category['text'] = $cat->name;
            $category['type'] = $cat->isCertificationType;

            $array_response[] = $category;
            unset($category);
        }

        // retorna a view e os dados
        return response()->json($array_response);
    }

    /**
     * Get User by ID
     * <code>
     * $request = Illuminate\Http\Request({
     *     'user_id'        => integer,    // User id(Required)
     * });
     * </code>
     * @method GET
     * @param Request $request (See above)
     * @return JsonResponse
     */
    public function get_users_by_id(Request $request)
    {
        $user = User::find($request->user_id);

        return response()->json($user);
    }

    /**
     * Get Institution by ID
     * <code>
     * $request = Illuminate\Http\Request({
     *     'institution_id'        => integer,    // User id(Required)
     * });
     * </code>
     * @method GET
     * @param Illuminate\Http\Request $request (See above)
     * @return JsonResponse
     */
    public function get_institution_by_id(Request $request)
    {
        $institution = Institution::find($request->institution_id);

        return response()->json($institution);
    }

    /**
     * Get keywords by theme
     * <code>
     * $request = Illuminate\Http\Request({
     *     'theme_id'        => integer,    // User id(Required)
     * });
     * </code>
     * @method GET
     * @param Request $request (See above)
     * @return JsonResponse
     */
    public function get_keywords_by_theme_id(Request $request)
    {
        $keywords = Keyword::query()->whereIn('theme_id', [$request->first_theme_id, $request->second_theme_id])->orderBy('name')->get();

        return response()->json($keywords);
    }

    /**
     * Get Comments.
     * <code>
     * $request = Illuminate\Http\Request({
     *     'item_id'        => integer,    // Item's id to get its Comments (Required)
     *     'item_type'      => string,     // Item's type (Required, Options: CommentsModules)
     * });
     * </code>
     * @method GET
     * @param Request $request (See above)
     * @return JsonResponse
     */
    public function get_comments(Request $request)
    {
        // Retorna vazio caso não envie o ID do item
        if (empty($request->item_id)) return response()->json([]);

        // Pesquisa de acordo com o tipo do objeto
        switch ($request->item_type) {

            /* TECNOLOGIAS SOCIAIS */
            case CommentsModules::SocialTecnology:

                // Busca a tecnologia social
                $item_obj = SocialTecnology::find($request->item_id);
                break;

            /* BLOG*/
            case CommentsModules::Blog:

                // Busca o blog
                $item_obj = Blog::find($request->item_id);
                break;

            default:
                return response()->json([]);
                break;
        }

        // Verifica se o Item existe
        if (empty($item_obj->id)) return response()->json([]);

        // Busca os comentários principais da tecnologia social
        $comments = $item_obj->comments()->where('comment_id', null)->orderBy('created_at', 'desc')->get();

        // Cria array que vai retornar os comentários
        $array_response = array();

        // Verifica se o usuário está logado
        $user_id = !empty(Auth::guard()->user()) ? Auth::guard()->user()->id : null;

        // Percorre os comentários principais para formatar e adicionar ao array de retorno
        foreach ($comments as $comment_item) {

            $aux_array['id'] = $comment_item->id;
            $aux_array['user_id'] = $user_id;
            $aux_array['item_id'] = $request->get('item_id');
            $aux_array['item_type'] = $request->get('item_type');
            $aux_array['time'] = Helpers::time_past($comment_item->created_at->format("m/d/Y H:i:s"));
            $aux_array['author'] = $comment_item->user()->withTrashed()->first()->name;
            $aux_array['seo_url'] = $comment_item->user()->withTrashed()->first()->seo_url;
            $aux_array['image'] = (!empty($comment_item->user()->withTrashed()->first()->image) ? asset('storage/users/'.$comment_item->user()->withTrashed()->first()->image) : "");
            $aux_array['content'] = $comment_item->content;
            $aux_array['subcomments'] = array();

            // Verifica se há sub-comentários e percorre para adicionar ao array de retorno
            foreach ($comment_item->comments as $subcomment_item) {
                $aux_array2['author'] = $subcomment_item->user()->withTrashed()->first()->name;
                $aux_array2['seo_url'] = $subcomment_item->user()->withTrashed()->first()->seo_url;
                $aux_array2['time'] = Helpers::time_past($subcomment_item->created_at->format("m/d/Y H:i:s"));
                $aux_array2['image'] = (!empty($subcomment_item->user()->withTrashed()->first()->image) ? asset('storage/users/'.$subcomment_item->user()->withTrashed()->first()->image) : "");
                $aux_array2['content'] = $subcomment_item->content;

                $aux_array['subcomments'][] = $aux_array2;

                unset($aux_array2);
            }

            // Adiciona o array de retorno
            $array_response[] = $aux_array;

            unset($aux_array);
        }

        // Converte o array em Json e retorna
        return response()->json($array_response);
    }

    /**
     * Get Comments Template to show items to user
     * <code>
     * $request = Illuminate\Http\Request({
     *     'comments'       => array,    // Array of Comments to build Template (Required)
     * });
     * </code>
     * @method GET
     * @param Request $request (See above)
     * @return JsonResponse
     * @throws Throwable
     */
    public function get_commentstemplate(Request $request)
    {
        // Retorna vazio caso não envie nenhum item
        if (empty($request->get('comments'))) return '';

        // Passa os itens para um avariável
        $comments = $request->get('comments');

        // Chama a view com o HTML dos comentários
        $view = view('layouts._lists._lists_comments', compact('comments'));

        // Responde o Json
        return response()->json([
            'content' => $view->render(),
        ]);
    }

    /**
     * Get Items Notifications for Admin Timeline
     * <code>
     * $request = Illuminate\Http\Request({
     *     'grupo'          => string,  // Filter by type of notifications (Optional)
     *     'instituicao'    => integer, // Filter by Institution (Optional)
     *     'start_date'     => date,    // Filter by Date - Above (Optional)
     *     'end_date'       => date,    // Filter by Date - Before (Optional)
     * });
     * </code>
     * @method GET
     * @param Request $request (See above)
     * @return JsonResponse
     * @throws Throwable
     */
    public function get_timelineitems(Request $request)
    {
        // Busca os itens da linha do tempo
        $timeline_items = ContentManager::query_to_timeline($request->all());

        // Chama a view com o template das notificações
        $view = view('admin.timeline._blocks.timeline-items', compact('timeline_items'));

        // Responde o Json
        return response()->json([
            'content' => $view->render(),
        ]);
    }

    /**
     * Get Activities Notification
     * <code>
     * $request = Illuminate\Http\Request({
     *     'user_id'        => integer, // Filter Notifications by Specific User (Optional)
     *     'source'         => string,  // Request Origin (Required)
     * });
     * </code>
     * @method GET
     * @param Request $request (See above)
     * @return JsonResponse
     * @throws Throwable
     */
    public function get_notifications(Request $request)
    {
        // Cria o array para receber as notificações
        $user_notifications = [];

        // Verifica para quem é a notificação
        switch ($request->source) {

            //Busca as notificações do perfil que o usuário está navegando
            case NotificationsSource::PublicProfile:

                //Busca o objeto do perfil do usuário que está consultando
                $user_obj = User::find($request->user_id);

                // Se existe usuário, busca as notificações
                if (!empty($user_obj->id))
                    $user_notifications = $user_obj->notifications()
                        ->where('target', NotificationsTarget::PROFILE)
                        ->paginate(4);
                break;

            //Busca as notificações da timeline do perfil do usuário
            case NotificationsSource::Profile:

                // Se existe usuário logado, busca as notificações
                if (!empty(Auth::guard()->user()))
                    $user_notifications = Auth::guard()->user()->notifications()
                        ->where('target', NotificationsTarget::PROFILE)
                        ->paginate(4);
                break;

            //Busca as notificações usuário da instituição
            case NotificationsSource::User:

                // Se existe usuário logado, busca as notificações
                if (!empty(Auth::guard()->user()))
                    $user_notifications = Auth::guard()->user()->notifications()
                        ->where('target', NotificationsTarget::USER)
                        ->paginate(15);
                break;

            case NotificationsSource::Institution:

                // Verifica se foi passado o ID da instituição
                $institution_obj = Institution::find($request->institution_id);

                // Busca as notificações da Instituição
                if (!empty($institution_obj->id))
                    $user_notifications = $institution_obj->notifications()
                        ->paginate(20);
                break;

            default:
                break;

        }

        // Verifica a origem para montar o template
        if ($request->source == NotificationsSource::Institution) {
            // Instituição tem um template mais robusto
            $view = view('institutions._sections._notifications', [
                'notifications' => $user_notifications
            ]);
        } else {
            // Padrão das páginas do usuário
            $view = view('users._notifications._index', [
                'user_notifications' => $user_notifications,
                'source' => $request->source,
                'user_obj' => (!empty($user_obj->id) ? $user_obj : Auth::guard()->user())
            ]);
        }

        // Responde o Json
        return response()->json([
            'content' => $view->render(),
            'count' => $user_notifications->count(),
        ]);
    }

    /**
     * Get Blog Itens
     * <code>
     * $request = Illuminate\Http\Request({
     * });
     * </code>
     * @method GET
     * @param Request $request (See above)
     * @return JsonResponse
     * @throws Throwable
     */
    public function get_blogs(Request $request)
    {
        // Busca as notícias
        $blogs = Blog::orderBy('created_at', 'desc')->paginate(4);

        // Retorna a view e a quantidade
        return response()->json([
            'content' => view('layouts._lists._lists_blog', [
                    'blogs'             => $blogs,
                    'show_author'       => true,
                    'show_statistics'   => true,
                    'border_top'        => true,
                ])->render(),
            'count' => $blogs->count(),
        ]);
    }

    /**
     * Get Event Itens
     * <code>
     * $request = Illuminate\Http\Request({
     *     'template' => string,         // Indicator for witch template must be returner in content (Optional; Default: App\Enums\EventTemplates::List)
     *     'theme_id' => integer         // Send if want to filter events by theme (Optional)
     *     'pagination' => integer       // Send if want to return events paginated (Optional)
     *     'page' => integet             // Indicate what page must be returned (Required if pagination was sent)
     *     'limit' => integer            // Indicate the limit of itens returned (Optional; Ignored if pagination was sent)
     * });
     * </code>
     * @method GET
     * @param Request $request (See above)
     * @return JsonResponse
     * @throws Throwable
     */
    public function get_events(Request $request)
    {
        // Template
        $view_template = $request->input('template', \App\Enums\EventTemplates::List);

        // Cria a Query
        $query_event = Event::query();

        // Verifica se passou algum tema
        if (!empty($request->input('theme_id'))) {
            $query_event->where('theme_id', $request->input('theme_id'));
        }

        // Verifica se passou algum tema
        if (!empty($request->input('institution_id'))) {
            $query_event->where('institution_id', $request->input('institution_id'));
        }

        // Verifica se está buscando os eventos de interesse de um usuário
        if (!empty($request->input('user_id'))) {
            $query_event->whereHas('users', function (Builder $query) use ($request) {
                $query->where('id', $request->input('user_id'));
            });
        }

        // Monta demais condições
        $query_event->where('end_date', '>=', Carbon::now())->orderBy('start_date', 'asc')->orderBy('title', 'asc');

        // Verifica se é para retornar paginado ou não
        if (!empty($request->input('pagination'))) {
            $events = $query_event->paginate($request->input('pagination'));
        } else {

            if (!empty($request->input('limit'))) $query_event->limit($request->input('limit'));

            $events = $query_event->get();
        }

        // Verifica o template de retorno
        switch ($view_template) {

            // Lista do tipo agenda
            case \App\Enums\EventTemplates::Schedule:

                // Agrupa pela data
                $events = $events->groupBy('event_date_string');

                $view = view('layouts._blocks._block_events_vertical', [
                    'events'        => $events,
                ]);
                break;

            // Lista padrão
            default:
                $view = view('layouts._lists._lists_event', [
                    'events'            => $events,
                    'border_top'        => true,
                    'show_institution'  => $request->input('show_institution', false),
                ]);
                break;
        }
        // Retorna a view e a quantidade
        return response()->json([
            'content' => $view->render(),
            'count' => $events->count(),
        ]);
    }

    /**
     * List Users Interested in a Event
     * <code>
     * $request = Illuminate\Http\Request({
     *     'event_id' => integer         // Event ID (Required)
     * });
     * </code>
     * @method GET
     * @param Request $request (See above)
     * @return JsonResponse
     * @throws Throwable
     */
    public function get_eventusers(Request $request)
    {
        // Valida se enviou o id
        if (empty($request->event_id)) return response()->json([
            'content' => '',
        ]);

        // Busca os Usuários interessados no evento
        $event_user = Event::find($request->event_id)->users()->paginate(4);

        // Monta o template para retornar
        $view = view('layouts._lists._lists_users_interest', compact('event_user'));

        // Retorna a view e a quantidade
        return response()->json([
            'content' => $view->render(),
            'count' => $event_user->count(),
        ]);
    }

    /**
     * Get Notification Messages for Institutions
     * <code>
     * $request = Illuminate\Http\Request({
     *     'institution_id' => integer         // Institution ID (Required)
     * });
     * </code>
     * @method GET
     * @param Request $request (See above)
     * @return Response::view
     */
    /*public function get_notificationitems(Request $request)
    {
        // Verifica se foi passado o ID da instituição
        if (empty($request->institution_id)) return false;

        // Busca as notificações da Instituição
        $notifications = Institution::find($request->institution_id)->notifications()->paginate(20);

        // Monta o template para retornar
        return view('institutions._sections._notifications', compact('notifications'));
    }*/

    /**
     * Register User Recommendation
     * <code>
     * $request = Illuminate\Http\Request({
     *     'item_id' => integer          // Item ID (Required)
     *     'source' => string            // Source Origin (Required)
     * });
     * </code>
     * @method POST
     * @param Request $request
     * @return JsonResponse
     */
    public function post_recommend(Request $request)
    {
        // Verifica se enviou o ID do item
        if (empty($request->item_id) || empty($request->source))
            return response()->json(['status'=>'error']);

        // Verifica o tipo do item que o usuário está recomendando
        switch ($request->source) {

            /* TECNOLOGIA SOCIAL */
            case AjaxRecommendSource::SocialTecnology:

                // Verifica se usuário está logado
                $user_obj = Auth::guard()->user();
                if (empty($user_obj->id))
                    return response()->json(['status'=>'erro']);

                // Cria o Objeto da Tecnologia Social
                $socialtecnology_obj = SocialTecnology::find($request->item_id);

                // Verifica se existe
                if (empty($socialtecnology_obj->id))
                    return response()->json(['status'=>'erro']);

                // Verifica se o usuário já recomendou a TEcnologia Social
                $exist_relation = $user_obj->socialtecnologiesrecommended->contains($socialtecnology_obj->id);

                if (!$exist_relation) {

                    // Recomenda a tecnologia social
                    $user_obj->socialtecnologiesrecommended()->attach($socialtecnology_obj->id);

                    // Envia a notificação para a instituição da tecnologia social
                    foreach ($socialtecnology_obj->users as $user_item) {
                        $user_item->notify(new SocialTecnologyRecommended($user_obj->name, $user_obj->id, $socialtecnology_obj));
                    }

                    // Notifica os usuários que favoritaram sobre o novo comentário
                    foreach($user_obj->followers as $user_notify) {
                        try {
                            $user_notify->notify(new FavoriteFollowSocialTecnology($user_obj->id, $socialtecnology_obj->id));
                        } catch (\Exception $e) {
                             Log::error("Send E-mail Error Favorite: ".$e->getMessage());
                             continue;
                        }
                    }

                    // Notifica na timeline a TS que o usuário seguiu
                    $user_obj->notify(new TimelineUserSocialTecnologyFollow($socialtecnology_obj->id));
                } else {
                    return response()->json(['status'=>'already']);
                }
                break;

            /* BLOG */
            case AjaxRecommendSource::Blog:

                // Busca o IP do usuário e remove os caracteres não numéricos
                $local_ip = \App\Helpers::get_user_ip();
                $local_ip = preg_replace("/\D/", "", $local_ip);

                // Verifica se este IP já recomendou a tecnologia
                $check_recommend = BlogRecommend::where('ip', $local_ip)->where('blog_id', $request->item_id)->count();

                if (empty($check_recommend)) {

                    // Recomenda o blog
                    BlogRecommend::create([
                        'ip' => $local_ip,
                        'blog_id' => $request->item_id,
                    ]);
                } else {
                    return response()->json(['status'=>'already']);
                }
                break;

            default:
                return response()->json(['status'=>'error']);
                break;
        }
        return response()->json(['status'=>'ok']);
    }

    /**
     * Follow/Unfollow user
     * <code>
     * $request = Illuminate\Http\Request({
     *     'user_id' => integer          // User ID (Required)
     * });
     * </code>
     * @method POST
     * @param Request $request
     * @return JsonResponse
     */
    public function post_favoriteuser(Request $request)
    {
        // Verifica se usuário está logado
        if (empty(Auth::guard()->user()->id))
            return response()->json(['status'=>'error']);

        // Verifica se passou o ID do usuário
        if (empty($request->user_id))
            return response()->json(['status'=>'error']);

        // Verifica se o usuário que está favoritando existe
        $user_obj = User::find($request->user_id);
        if (empty($user_obj->id))
            return response()->json(['status'=>'error']);

        // Cria o Objeto do Usuário
        $logged_user_obj = Auth::guard()->user();

        // Verifica se já é favorito
        $exist_relation = $logged_user_obj->favorites->contains($user_obj->id);

        // Verifica se está favoritando ou não
        if ($exist_relation) {

            // Deixa de seguir o usuário
            $logged_user_obj->favorites()->detach($user_obj->id);

            // Notifica o usuário na sua timeline
            $logged_user_obj->notify(new TimelineUserFavoritedUnfollow($user_obj->id));

        } else {

            // Favorita o Usuário
            $logged_user_obj->favorites()->attach($user_obj->id);

            // Notifica o usuário
            $user_obj->notify(new UserFavorited($logged_user_obj->id));

            // Notifica o usuário na sua timeline
            $logged_user_obj->notify(new TimelineUserFavorited($user_obj->id));

        }

        return response()->json([
            'status'=>'ok',
            'existe_relation'=> !$exist_relation
        ]);
    }

    /**
     * Follow/Unfollow user
     * <code>
     * $request = Illuminate\Http\Request({
     *     'event_id' => integer          // Event ID (Required)
     * });
     * </code>
     * @method POST
     * @param Request $request
     * @return JsonResponse
     */
    public function post_eventinterest(Request $request)
    {
        // Verifica se usuário está logado
        if (empty(Auth::guard()->user()->id))
            return response()->json(['status'=>'error']);

        // Verifica se passou o ID do evento
        if (empty($request->event_id))
            return response()->json(['status'=>'error']);

        // Cria o Objeto do Evento
        $event_obj = Event::find($request->event_id);

        // Verifica se evento existe
        if (empty($event_obj->id))
            return response()->json(['status'=>'error']);

        // Cria o Objeto do Usuário
        $user_obj = Auth::guard()->user();

        // Verifica se existe interesse
        $exist_relation = $user_obj->events->contains($event_obj->id);

        // Verifica se já tem interesse ou não
        if ($exist_relation) {

            // Desmarca o interesse do evento
            $user_obj->events()->detach($event_obj->id);

            //Notifica na timeline do usuário que o mesmo removeu o interesse no evento
            $user_obj->notify(new TimelineUserEventRemoveInterest($event_obj->id));

            //Notifica os seguidores do usuário que o mesmo não tem mais interesse no evento
            foreach ($user_obj->followers as $user) {
                $user->notify(new UserEventRemoveFavorited($user_obj->id, $event_obj->id));
            }

        } else {

            // Confirmar interesse pelo evento
            $user_obj->events()->attach($event_obj->id);

            //Notifica na timeline do usuário que o mesmo mostrou interesse no evento
            $user_obj->notify(new TimelineUserEventInterest($event_obj->id));

            //Notifica os seguidores do usuário que mostrou interesse no evento
            foreach ($user_obj->followers as $user) {
                $user->notify(new UserEventFavorited($user_obj->id, $event_obj->id));
            }

        }

        // Número de pessoas interessadas
        $count_aux = $event_obj->users->count();

        // Monta o Json para retornar
        return response()->json([
            'status'=>'ok',
            'count_aux' => $count_aux,
            'label' => trans_choice('front.event.interested_user', $count_aux)
        ]);
    }

    /**
     * Mark Admin Timeline Item as Read
     * <code>
     * $request = Illuminate\Http\Request({
     *     'item_id' => integer          // Content Manager ID (Required)
     * });
     * </code>
     * @method POST
     * @param Request $request
     * @return JsonResponse
     */
    public function post_timeline_markasread(Request $request)
    {
        // Verifica se enviou o ID
        if (empty($request->item_id) )
            return response()->json(['status'=>'error']);

        // Marca o item como lido
        ContentManager::find($request->item_id)->markAsRead();

        return response()->json(['status'=>'ok']);
    }

    /**
     * Register Comment
     * <code>
     * $request = Illuminate\Http\Request({
     *     'item_id' => integer          // Item ID (Required)
     *     'item_type' => string         // Item Type witch is receiving a comment (Required)
     *     'comment_id' => integer       // Comment Parent ID (Optional)
     *     'content' => integer          // Comment Content (Required)
     * });
     * </code>
     * @method POST
     * @param Request $request
     * @return JsonResponse
     */
    public function post_comments(Request $request)
    {

        // Verifica se passou o ID do item que está recebendo o comentário
        if (empty($request->item_id))
            return response()->json([
                'status'=>'erro',
                'message' => trans('front.errors.cod-cm0001')
            ]);

        // Verifica se usuário está logado
        $user_obj = Auth::guard()->user();
        if (empty($user_obj))
            return response()->json(['status'=>'erro']);

        try {

            // Verifica o item que está recebendo o comentário
            switch ($request->item_type) {

                /* TECNOLOGIA SOCIAL */
                case CommentsModules::SocialTecnology:

                    // Registra o Comentário
                    if (!SocialTecnologyComment::store([
                            'user_id'               => $user_obj->id,
                            'socialtecnology_id'    => $request->item_id,
                            'comment_id'            => $request->comment_id,
                            'content'               => $request->content,
                        ], $user_obj)) {
                            return response()->json([
                                'status'=>'erro',
                                'message' => trans('front.errors.cod-cm0002')
                            ]);
                        }

                    break;

                /* BLOG */
                case CommentsModules::Blog:

                    // Registra o comentário
                    if (!BlogComment::store([
                        'user_id'       => $user_obj->id,
                        'blog_id'       => $request->item_id,
                        'comment_id'    => $request->comment_id,
                        'content'       => $request->content,
                        ], $user_obj)) {
                            return response()->json([
                                'status'=>'erro',
                                'message' => trans('front.errors.cod-cm0003')
                            ]);
                        }
                    break;

                default:
                    return response()->json(['status'=>'erro', 'message' => trans('front.errors.cod-cm0004')]);
                    break;
            }

            /* Retorna o JSON */
            return response()->json([
                'status'=>'ok',
                'message' => trans('front.comments.added-successfuly'),
            ]);

        } catch (Exception $e) {
            return response()->json(['status'=>'erro', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Login Through Social Media
     * <code>
     * $request = Illuminate\Http\Request({
     *     'type' => string             // Social Media Origin (Required)
     *     'name' => string             // User Name (Required)
     *     'email' => string            // User E-mail (Required)
     *     'social_id' => string        // User Social Media ID (Optional)
     *     'action' => string           // Perform some after login action (Optional)
     * });
     * </code>
     * @method POST
     * @param Request $request
     * @return JsonResponse
     */
    public function post_sociallogin(Request $request)
    {
        // Verifica se as informações obrigatórias foram passados
        if (empty($request->name) || empty($request->email)) {
            return response()->json([
                'status'=>'erro',
                'message' => trans('front.errors.cod-sl0001')
            ]);
        }

        try {

            // Verifica qual rede social foi realizado o login
            switch ($request->type) {

                /* FACEBOOK */
                case 'facebook':

                    // Verifica se já existe usuário com este e-mail
                    $user_obj = User::where('email', $request->email)->first();

                    if (!empty($user_obj->id)) {

                        // Atualiza o usuário com as informações
                        $user_obj->update([
                            'name' => $request->name,
                            'fb_login' => $request->social_id,
                        ]);

                    } else {

                        // Cria a url amigável para o usuário
                        $data['seo_url'] = Helpers::slug($request->name);
                        $data['seo_url'] = Helpers::generate_unique_friendly_url($data, new User);

                        // Cria o usuário
                        $user_obj = User::create([
                            'name' => $request->name,
                            'email' => $request->email,
                            'seo_url' => $data['seo_url'],
                            'password' => Hash::make(uniqid()),
                            'fb_login' => $request->social_id,
                        ]);
                    }

                    // Loga com o usuário
                    Auth::loginUsingId($user_obj->id);
                    break;

                /* GOOGLE */
                case 'google':

                    // Verifica se já existe usuário com este e-mail
                    $user_obj = User::where('email', $request->email)->first();

                    if (!empty($user_obj->id)) {

                        // Atualiza o usuário com as informações
                        $user_obj->update([
                            'name' => $request->name,
                            'google_login' => $request->social_id,
                        ]);

                    } else {

                        // Cria a url amigável para o usuário
                        $data['seo_url'] = Helpers::slug($request->name);
                        $data['seo_url'] = Helpers::generate_unique_friendly_url($data, new User);

                        // Cria o usuário
                        $user_obj = User::create([
                            'name' => $request->name,
                            'email' => $request->email,
                            'seo_url' => $data['seo_url'],
                            'password' => Hash::make(uniqid()),
                            'google_login' => $request->social_id,
                        ]);
                    }

                    // Loga com o usuário
                    Auth::loginUsingId($user_obj->id);
                    break;

                default:
                    return response()->json(['status'=>'erro', 'message' => trans('front.errors.cod-sl0002')]);
                    break;
            }

            // Verifica se tem alguma ação posterior ao login
            if ($request->action == 'comment') {

                // Valida as informações
                $data_request = $request->form_data;
                if (empty($data_request['item_id'])
                    || empty($data_request['item_type'])
                    || empty($data_request['content'])) {
                    return response()->json(['status'=>'erro', 'message' => trans('front.errors.cod-cm0005')]);
                }

                // Comenta o item
                if (!$user_obj->commentItem($data_request['item_type'], [
                    'item_id' => $data_request['item_id'],
                    'comment_id' => $data_request['parent_id'],
                    'content' => $data_request['content'],
                ])) {
                    return response()->json(['status'=>'erro', 'message' => trans('front.errors.cod-cm0006')]);
                }
            }


            /* Retorna o JSON */
            return response()->json([
                'status'=>'ok',
                'message' => trans('auth.logged-successfuly'),
            ]);

        } catch (Exception $e) {
            return response()->json(['status'=>'erro', 'message' => 'AjaxExceptionMessage001 - '.$e->getMessage()]);
        }
    }

    public function get_socialtecnologies(Request $request)
    {
        // Cria a Query
        $query_socialtecnology = SocialTecnology::query();

        // Verifica se passou algum tema
        if (!empty($request->input('institution_id'))) {
            $query_socialtecnology->where('institution_id', $request->input('institution_id'));
        }

        // Verifica se está buscando os eventos de interesse de um usuário
        if (!empty($request->input('user_id'))) {
            $query_socialtecnology->whereHas('recommends', function (Builder $query) use ($request) {
                $query->where('id', $request->input('user_id'));
            });
        }

        // Ordena
        $query_socialtecnology->orderBy('socialtecnology_name','asc');

        // Verifica se é para retornar paginado ou não
        if (!empty($request->input('pagination'))) {
            $socialtecnologies = $query_socialtecnology->paginate($request->input('pagination'));
        } else {

            if (!empty($request->input('limit'))) $query_socialtecnology->limit($request->input('limit'));

            $socialtecnologies = $query_socialtecnology->get();
        }

        $view = view('layouts._lists._lists_socialtecnologies', [
            'socialtecnologies' => $socialtecnologies,
            'border_top'        => true,
        ]);

        // Retorna a view e a quantidade
        return response()->json([
            'content' => $view->render(),
            'count' => $socialtecnologies->count(),
        ]);
    }

    /*===========================================
     =            Wizard Ajax Handlers         =
    ===========================================*/
    /**
     * Cria relacionamento entre a tecnologia e o local de implantação
     *
     * <code>
     * $place = request({
     *     'socialtecnology_id' => string             // Social Media Origin (Required)
     *     'neighborhood' => string             // User Name (Required)
     *     'city' => string            // User E-mail (Required)
     *     'state' => string        // User Social Media ID (Optional)
     *     'active' => string           // Perform some after login action (Optional)
     * });
     * </code>
     * @method POST
     * @param Illuminate\Http\Request $place
     * @return \Illuminate\Http\Response::JSON
     */
    public function storeDeploymentPlaces(Request $request)
    {
        $deploymentPlace = SocialTecnologyDeploymentPlace::create([
            'socialtecnology_id' => $request->place['socialtecnology_id'],
            'neighborhood' => $request->place['neighborhood'],
            'city' => $request->place['city'],
            'state' => $request->place['state'],
            'active' => $request->place['active']
        ]);

        return response()->json($deploymentPlace);
    }

    /**
     * Removendo um local de implatação da tecnologia
     *
     * @param Illuminate\Http\Request $request->id
     * @return Illuminate\Http\Response::JSON
     */
    public function removeDeploymentPlace(Request $request)
    {
        $deploymentPlace = SocialTecnologyDeploymentPlace::find($request->id);

        if(!empty($deploymentPlace)) $deploymentPlace->delete();

        return response()->json(['status' => 200, 'message' => 'Local deletado com sucesso!']);
    }

    /**
     * Adicionando representante a tecnologia social
     *
     * @param Illuminate\Http\Request $request->user
     * @return Illuminate\Http\Response::JSON
     */
    public function storeSocialTecnologyUsers(Request $request)
    {
        $tecnology = SocialTecnology::find($request->user['socialtecnology_id']);

        if(!empty($tecnology->users->where('id', $request->user['user_id'])->first())) {
            $result = [
                'error_title' => __('front.wizard.steps.3.messages.already_linked.title'),
                'message' => __('front.wizard.steps.3.messages.already_linked.message')
            ];

            return response()->json($result, 406);
        }

        $tecnology->users()->attach($request->user['user_id']);

        $representative = $tecnology->users()->where('id', $request->user['user_id'])->first();

        return response()->json($representative);
    }

    /**
     * removemos um representante dessa instituição
     *
     * @param Illuminate\Http\Request $request->user
     * @return Illuminate\Http\Response::JSON
     */
    public function removeSocialTecnologyUser(Request $request)
    {
        $tecnology = SocialTecnology::find($request->user['socialtecnology_id']);

        if(!empty($tecnology)) $tecnology->users()->detach($request->user['user_id']);

        $result = [
            'error_title' => __('front.wizard.steps.3.messages.remove_user.title'),
            'message' => __('front.wizard.steps.3.messages.remove_user.message')
        ];

        return response()->json($result);
    }

    /**
     * adicionamos uma instituição parceira a tecnologia social
     *
     * @param Illuminate\Http\Request $request->partner
     * @param Illuminate\Http\Request $request->socialtecnology_id
     * @return Illuminate\Http\Response::JSON
     */
    public function setSocialTecnologyPartner(Request $request)
    {
        $result = SocialTecnologyPartner::create([
            'socialtecnology_id' => $request->socialtecnology_id,
            'institution_name' => $request->partner['institution_name'],
            'acting' => $request->partner['acting']
        ]);

        return response()->json($result);
    }

    public function removeSocialTecnologyPartner(Request $request)
    {
        SocialTecnologyPartner::find($request->partner_id)->delete();

        return response()->json(['message' => __('front.wizard.steps.8.messages.remove_partner')]);
    }

    /**
     * deletando uma imagem da tecnologia
     * @method POST
     * @param Illuminate\Http\Request $request->images
     * @return Illuminate\Http\Response::JSON
     */
    public function removeSocialTecnologyImagem(Request $request)
    {
        // buscamos o item para excluir da tabela
        $data = SocialTecnologyImage::find($request->images['id']);

        // excluir imagem do local storage
        Storage::delete('socialtecnologies/'. $request->images['tecnology_id'] .'/images/'. $data['image']);

        return response()->json($data->delete());
    }

    /**
     * deletamos um inscrição incompleta de tecnologia social
     * @method POST
     * @param Illuminate\Http\Request $request->registration
     * @param Illuminate\Http\Response::JSON
     */
    public function removeIncompleteSocialTecnologyInscription(Request $request)
    {
        ContentManager::where('registration', $request->registration)
            ->where('status', ContentManager::STATUS_PENDING)
            ->delete();
        SocialTecnology::where('registration', $request->registration)
            ->where('status', ContentManager::STATUS_PENDING)
            ->delete();

        return response()->json(['message' => 'Inscrição deletado com sucesso.']);
    }
}
