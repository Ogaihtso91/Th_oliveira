<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use App\Enums\NotificationsTarget;
use App\Enums\SeoRobots;
use App\Enums\UserSex;
use App\Enums\UserSchooling;
use App\Filesystem\Storage;
use App\NotificationsConfig;
use App\User;
use App\SocialTecnologyRecommend;
use App\Theme;
use App\EventUser;
use App\Notifications\TimelineUserSocialTecnologyUnFollow;
use Auth;
use Carbon\Carbon;
use Validator;
use App\Award;

class UsersController extends Controller
{
    /**
     * Validate User Form
     * @param   Illuminate\Http\Request $request
     * @return  Validator
     */
    protected function validator(Request $request)
    {
        // Cria o validador dos campos do Usuário
        $validation = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                (empty($request->id) ? Rule::unique('users') : Rule::unique('users')->ignore($request->id)),
            ],
            'password' => 'string|min:6|confirmed',
        ]);

        // Password requerido somente quando está adicionando
        $validation->sometimes(['password'], 'required', function ($input) use($request) {
            return empty($request->id);
        });

        // Valida a foto de perfil, se enviou o tamanho máximo da foto e a extensão do arquivo (png, jpeg ou jpg)
        $validation->sometimes(['photo'], [
            (empty($request->size_photo) ? '' : 'max:'.($request->size_photo * 1024)),
            function ($attribute, $value, $fail) use ($validation) {
                if ($value->extension() != 'png' && $value->extension() != 'jpeg' && $value->extension() != 'jpg') {
                    $validation->errors()->add('photo', 'Foto do perfil: é permitido apenas imagens PNG, JPEG ou JPG.');
                }
            },
        ], function ($input) {
            return $input->change_photo == 1;
        });

        /* Valida a imagem */
        $validation->sometimes(['id'], [function ($attribute, $value, $fail) {
            if ($value != Auth::guard()->user()->id) {
                $fail('Ocorreu um erro ao editar seu usuário. Por gentileza, entre em contato com o administrador. [ERR_COD - USU001]');
            }
        }], function ($input) {
            return $input->user_action == 'user.register';
        });

        return $validation;
    }

    /**
     * User Signup/Edit Form
     * @return  \Illuminate\Http\Response
     */
    public function index()
    {
        // Verifica se usuário está fazendo inscrição e já está logado
        if (Route::is('signup') && !empty(Auth::guard()->user()->id)) {
            return redirect()->route('user.institution.index');
        }

        // Busca a instituição vinculada
        if (!empty(Auth::guard()->user()->id)) {
            $institution = Auth::guard()->user()->institution()->first();
        }

        //Busca os temas
        $themes = Theme::all();

        //Busca o objeto do usuário
        $user_obj = Auth::guard()->user();

        // Monta array com as variáveis que serão enviadas para a view
        $view_parameters = array_merge(
            compact('institution', 'themes', 'user_obj'),
            [
                'seo_title' => \Config::get('custom_configuration.seo.signup.title'),
                'seo_description' => \Config::get('custom_configuration.seo.signup.description'),
                'seo_keywords' => \Config::get('custom_configuration.seo.signup.keywords'),
            ]
        );

        // Chama a view do perfil
        return view('users.user', $view_parameters);
    }

    /**
     * User Profile Page
     * @return  \Illuminate\Http\Response
     */
    public function profile(Request $request)
    {
        // Pega a rota do usuário
        $action = Route::currentRouteName();

        switch ($action) {

            case 'user.profile':

                // Busca as notificações do perfil do usuário
                $user_notifications = Auth::guard()->user()->notifications()
                    ->where('target', NotificationsTarget::PROFILE)
                    ->paginate(4);

                //Busca o objeto do usuário
                $user_obj = Auth::guard()->user();

                // Variáveis para enviar para a view
                $view_parameters = compact('action','user_notifications','action','user_obj');

                $inscricao = "no";
                break;

            case 'user.socialtecnologies':

                // Busca as tecnologias que o usuário segue
                $socialtecnologiesrecommends = Auth::guard()->user()->socialtecnologiesrecommended()->orderBy('socialtecnology_name')->paginate(4);

                // Variáveis para enviar para a view
                $view_parameters = compact('action', 'socialtecnologiesrecommends');

                $inscricao = "no";
                break;

            case 'user.messages':

                // Busca as notificações do usuário
                $user_notifications = Auth::guard()->user()->notifications()
                ->where('target', NotificationsTarget::USER)
                ->paginate(15);

                // Busca as notificações silenciadas
                $silenced_notifications = NotificationsConfig::getUserNotificationConfig(Auth::guard()->user()->id);

                // Variáveis para enviar para a view
                $view_parameters = compact('action', 'user_notifications', 'silenced_notifications');

                $inscricao = "no";
                break;

            case 'user.favorites':

                // Busca os usuários favoritos
                $favorite_users = Auth::guard()->user()->favorites();

                // Pagina os usuários
                $favorite_users = $favorite_users->orderBy('name')->orderBy('email')->paginate(12);

                // Variáveis para enviar para a view
                $view_parameters = compact('favorite_users', 'action');

                $inscricao = "no";
                break;

            case 'user.followers':

                // Busca os seguidores do usuário
                $followers_users = Auth::guard()->user()->followers();

                // Pagina os usuários
                $followers_users = $followers_users->orderBy('name')->orderBy('email')->paginate(12);

                // Variáveis para enviar para a view
                $view_parameters = compact('followers_users', 'action');

                $inscricao = "no";
                break;

            case 'user.events':

                $events_users = Auth::user()->events()->orderBy('start_date','asc')->where('end_date', '>=', Carbon::now())->paginate(4);

                // Variáveis para enviar para a view
                $view_parameters = compact('events_users', 'action');

                $inscricao = "no";
                break;

            case 'user.institutions':

                $award_id = $request->get('premio', '');
                $inscricao = $request->get('inscricao', 'no');

                $user_institutions = Auth::guard()->user()->institutions()->where('status', 1)->paginate(8);

                $view_parameters = compact('user_institutions', 'action', 'award_id');

                break;  

            default:
                return redirect()->route('front.home');
                break;
        }

        // Adiciona nos parâmetros da view para não indexar nem seguir links desta página
        $view_parameters = Arr::add($view_parameters, 'seo_meta_robots', SeoRobots::None);      

        if($inscricao == "yes"){
            $award = Award::find($_GET['premio']);     
            $size = Auth::user()->institutions->count();
            if($size != "0"){  
                return redirect()->route('user.institution.socialtecnologies.create', $view_parameters)->with('error', 'Você deve selecionar uma Instituição para poder realizar a inscrição da Tecnologia Social na premiação que selecionou.');             
            }else{
                return redirect()->route('user.institution.socialtecnologies.create', $view_parameters)->with('error', 'Para fazer a inscrição no Prêmio '. $award->name .' você deve pertencer a uma Instituição');                                
            }
        }else{
             // Chama a view do perfil
             return view('users.profile', $view_parameters);
             // Chama a view do perfil
        }
    }

    /**
     * User Public Profile Page
     * @param   Illuminate\Http\Request $request
     * @param   String $seo_url
     * @return  \Illuminate\Http\Response
     */
    public function public_profile(Request $request, String $seo_url)
    {
        // Busca o usuário
        $user_obj = User::where('seo_url', $seo_url)->first();

        // Volta para a home se não existe
        if (empty($user_obj->id)) {
            return redirect()->route('front.home');
        }

        //Se o nome do responsável pela TS for o mesmo usuário que está logado, redireciona para o seu perfil.
        if(!empty(Auth::guard()->user()) && Auth::guard()->user()->id == $user_obj->id) {
           return redirect()->route('user.profile');
        }

        // Pega as tecnologias sociais que o usuário segue
        $user_socialtecnologies = $user_obj->socialtecnologiesrecommended()->orderBy('socialtecnology_name','asc')->paginate(4);

        // Pega os usuários que ele segue
        $user_favorites = $user_obj->favorites();

        // Pega as notificações do usuário
        $user_notifications = $user_obj->notifications()
                ->where('target', NotificationsTarget::PROFILE)
                ->paginate(4);

        // Pega os Eventos que o usuário tem interesse
        $user_events = $user_obj->events()->where('end_date', '>=', Carbon::now())->orderBy('start_date', 'asc')->orderBy('title', 'asc')->paginate(4);

        // Monta array com as variáveis que serão enviadas para a view
        $view_parameters = array_merge(
            compact('user_obj','user_socialtecnologies','user_favorites','user_notifications', 'user_events'),
            [
                'action' => Route::currentRouteName(),
                'seo_title' => str_replace("[NOMEUSUARIO]", $user_obj->name, \Config::get('custom_configuration.seo.user-profile.title')),
                'seo_description' => str_replace("[NOMEUSUARIO]", $user_obj->name, \Config::get('custom_configuration.seo.user-profile.description')),
            ]
        );

        // Chama a view do perfil
        return view('users.public_profile', $view_parameters);
    }

    /**
     * Forgot Password Page
     * @return  \Illuminate\Http\Response
     */
    public function forgot() {
        return view('auth.passwords.email', [
            'seo_meta_robots' => SeoRobots::None
        ]);
    }

    /**
     * Register all information of user
     * @method  POST
     * @param   Illuminate\Http\Request
     * @return  \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        // Pega os valores do request
        $post_fields =  array_map('trim', $request->except(['themes']));

        //Adiciona o array de temas no post fields
        $post_fields['themes'] = $request->themes;

        // Atualiza o request com os novos valores para validação
        $request->replace($post_fields);

        // Valida os campos
        $validated = $this->validator($request)->validate();

        // Trata a Imagem
        unset($validated['image']);
        if ($request->change_image == 1 && $image_name = Storage::storeImage($request, 'users')) {
            $validated['image'] = $image_name;
        }

        // Salva no BD
        $user_obj = User::store(array_merge($request->all(), $validated));

        // Direciona o usuário para a rota
        // 4587 alterando rota para exibir msg de sucesso ao gravar usuário.
        if (Route::currentRouteName() == 'user.post.register') {
            return redirect()->route('login')->with('message', 'Usuário cadastrado com sucesso!');
        } else {
            return redirect()->route('admin.user.index')->with('message', 'Usuário alterado com sucesso!');
        }
    }

    /**
     * Unfollow a Social Tecnology
     * @method  DELETE
     * @param   Integer $id Social Tecnology ID
     * @return  \Illuminate\Http\Response
     */
    public function unfollowsocialtecnology(Request $request, $id)
    {
        // Verifica se existe usuário logado
        if (empty(Auth::guard()->user()) || empty($id)) {
            redirect()->route('user.socialtecnologies');
        }

        //Criar o objeto do usuário
        $user_obj = Auth::guard()->user();

        // Remove tecnologia social dos favoritos
        $user_obj->socialtecnologiesrecommended()->detach($id);

        // Notifica na timeline a TS que o usuário deixou de seguir
        $user_obj->notify(new TimelineUserSocialTecnologyUnFollow($id));

        // Retorna para a página do perfil
        return redirect()->route('user.socialtecnologies')->with('message', trans('front.user.related_socialtecnologies.unfollow_success'));
    }

    /**
     * Remove interest from Event
     * @method  DELETE
     * @param   Integer $id Event ID
     * @return  \Illuminate\Http\Response
     */
    public function user_removeinterest(Request $request, $id)
    {
        // Verifica se existe usuário logado
        if (empty(Auth::guard()->user()) || empty($id)) {
            redirect()->route('user.events');
        }

        //Criar o objeto do usuário
        $user_obj = Auth::guard()->user();

        // Desmarca o interesse do evento
        $user_obj->events()->detach($id);

        // Retorna para a página do perfil
        return redirect()->route('user.events')->with('message', trans('front.user.events.success'));
    }

    /**
     * Remove Institution association
     * @method  DELETE
     * @return  \Illuminate\Http\Response
     */
    public function unlinkinstitution(Request $request)
    {
        // Verifica se existe usuário logado
        if (empty(Auth::guard()->user())) {
            redirect()->route('front.home');
        }

        // Desvincula o usuário das tecnologias sociais desta instituição
        Auth::guard()->user()->socialtecnologies()
            ->detach(
                Auth::guard()->user()
                    ->socialtecnologies()
                    ->pluck('id')
                    ->toArray()
            );

        // Desvincula o usuário
        Auth::guard()->user()->institution()->dissociate()->save();

        // Retorna para a página do perfil
        return redirect($request->input('source', route('user.profile')))->with('message', trans('front.user.institution-unlink-success'));
    }


    /**
     * Follow/Unfollow user
     * @method POST
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function post_notificationconfig(Request $request)
    {
        // Verifica se usuário está logado
        if (empty(Auth::guard()->user()->id))
            return redirect()->route('user.messages')->with('error', 'Usuário não está logado.');

        // Verifica se passou o ID do usuário
        if (empty($request->notification_type))
            return redirect()->route('user.messages')->with('error', 'Nenhuma notificação para silenciar.');

        $silenced = $request->silencer == 1 ? 0 : 1;

        // Busca o registro de configuração
        $notification_config_obj = NotificationsConfig::where('notifiable_id', Auth::guard()->user()->id)
                ->where('notifiable_type', get_class(Auth::guard()->user()))
                ->where('type', $request->notification_type)
                ->first();

        // Verifica se está favoritando ou não
        if (!empty($notification_config_obj)) {
            $notification_config_obj->update(['silenced' => $silenced]);
        } else {
            // Favorita o Usuário
            NotificationsConfig::create([
                'notifiable_id' => Auth::guard()->user()->id,
                'notifiable_type' => get_class(Auth::guard()->user()),
                'type' => $request->notification_type,
                'silenced' => $silenced,
            ]);
        }
        return redirect()->route('user.messages')->with('message', trans("front.user.notifications.silence_success_message_{$silenced}"));
    }
}
