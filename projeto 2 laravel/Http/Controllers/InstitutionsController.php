<?php

namespace App\Http\Controllers;

use App\ContentManager;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Enums\NotificationsTarget;
use App\Filesystem\Storage;
use App\Institution;
use App\SocialTecnology;
use App\User;
use Auth;
use Validator;
use Carbon\Carbon;
use App\Repositories\Repository\Award\GetAwardRepository;

class InstitutionsController extends Controller
{

    private $getAward;

    public function __construct()
    {
        $this->getAward = new GetAwardRepository();
    }

    /* CUSTOM VALIDATION MESSAGES */
    protected $messages = [
        'link_user_id.required'  => 'Selecione um usuário para vinculá-lo a sua instituição.',
        'link_user_id.integer'  => 'Usuário selecionado é inválido. Por favor, contate o administrador. [ERR_COD - INS001]',
        'link_user_id.exists'  => 'Usuário selecionado é inválido. Por favor, contate o administrador. [ERR_COD - INS002]',
    ];

    /**
     * Validate Institution Form
     * @param   Illuminate\Http\Request $request
     * @return  Validator
     */
    protected function validator(Request $request)
    {


        /* Valida os Campos */
        $validation = Validator::make($request->all(), [
            'institution_name' => 'required|string|max:255',
            'cnpj' => [
                'required',
                'cnpj',
                (!empty($request->id) ? Rule::unique('institutions', 'cnpj')->ignore($request->id) : Rule::unique('institutions', 'cnpj')),
            ],
            'legal_nature' => [
                'required'
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                ($request->id ? Rule::unique('institutions', 'email')->ignore($request->id) : Rule::unique('institutions', 'email')),
            ],
            'phone' => [
                'required'
            ],
            'cep' => [
                'required',
                'max:8'
            ],
            'street' => [
                'required'
            ],
            'number' => [
                'required'
            ],
            'neighborhood' => [
                'required'
            ],
            'city' => [
                'required'
            ],
            'state' => [
                'required'
            ],
            'responsible_email' => 'email'
        ], $this->messages);

        /* Valida a imagem */
        $validation->sometimes(['image'], [
            (empty($request->size_image) ? '' : 'max:'.($request->size_image * 1024)),
            function ($attribute, $value, $fail) {
                if ($value->extension() != 'png' && $value->extension() != 'jpeg' && $value->extension() != 'jpg') {
                    $fail('Foto da instituição: é permitido apenas imagens PNG, JPEG ou JPG.');
                }
            },
        ], function ($input) {
            return $input->change_image == 1;
        });

        /* Valida a imagem */
        $validation->sometimes(['id'], [function ($attribute, $value, $fail) {
            $institution = Auth::guard()->user()->institutions->where('id', $value)->first();
            if ($value != $institution->id) {
                $fail('Ocorreu um erro ao editar sua instituição. Por gentileza, entre em contato com o administrador. [ERR_COD - INS004]');
            }
        }], function ($input) {
            return $input->action !== 'admin.institution.register';
        });

        return $validation;
    }

    protected function representativeValidator(Request $request) {
        /* Valida os Campos */
        $validation = Validator::make($request->all(), [
            'responsible_name' => [
                'required'
            ],
            'responsible_email' => [
                'required'
            ],
            'office_post' => [
                'required'
            ]
        ],
        // 4746 Inserindo codigo para traduzir a msg de retorno para o usuario
        [ 'required' => 'O campo :attribute é obrigatório', ],
        [
            'responsible_name'      => 'Representante',
            'responsible_email'     => 'Email do representante',
            'office_post'  => 'Cargo do representante',
        ], $this->messages);

        return $validation;
    }

    /**
     * Institution's Page
     * @param   Illuminate\Http\Request $request
     * @return  \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (empty(Auth::guard()->user()->institution_id)) {
            return redirect()->route('user.profile');
        }

        // Busca a instituição vinculada
        $institution = Auth::guard()->user()->institution()->first();

        // Busca as notificações
        $notifications = Auth::guard()->user()
                            ->notifications()
                            ->where('target', NotificationsTarget::INSTITUTION)
                            ->paginate(20);

        $unread_notifications = Auth::guard()->user()->unreadNotifications->where('target', NotificationsTarget::INSTITUTION)->count();

        // Chama a view da instituição
        return view('institutions.index', compact('institution', 'notifications', 'unread_notifications'));
    }

    /**  */
    public function show(Request $request) {

        $wizard = $request->get('wizard', 0);

        /**
         * verificar se o usuario logado tem acesso aos detalhes internos da instituição
         * se não redirecionar para pagina publica da instituição.
         */
        if (empty(Auth::guard()->user()->institutions->where('seo_url', $request->seo_url)->first())) {
            return redirect()->route('front.institution.profile.public', ['seo_url' => $request->seo_url]);
        } else {

            // Busca a instituição vinculada
            $institution = Auth::guard()->user()->institutions->where('seo_url', $request->seo_url)->first();

            // Busca as notificações
            $notifications = Auth::guard()->user()
                                ->notifications()
                                ->where('target', NotificationsTarget::INSTITUTION)
                                ->paginate(20);

            $unread_notifications = Auth::guard()->user()->unreadNotifications->where('target', NotificationsTarget::INSTITUTION)->count();

            /** mudar qeury */
            $contentManager = ContentManager::query()
                ->where('user_id', Auth::guard()->user()->id)
                ->where('institution_id', $institution->id)
                ->where('status', 0)
                ->whereNotNull('wizard_step')->get();

            // $contentManager = $contentManager->whereNotNull('wizard_step')->get();

            //busca premios que ainda podem ter inscricoes
            $premiosAtivos = $this->getAward->allAwardsActive();

            // caso tenha premios que possam ter inscricoes ele coloca true na variavel, caso contrario a variavel fica false
            if(empty($premiosAtivos->toArray())){
                $awardsActive = false;
            }else{
                $awardsActive = true;
            }
            
            // Este é o DONO da instituição cadastratada (ele quem fez o cadastro)
            $representative = $institution->representatives()->get()->first();
            // Chama a view da instituição
            return view('institutions.show', compact('institution', 'representative', 'notifications', 'unread_notifications', 'contentManager','awardsActive', 'wizard'));
        }
    }

    /**
     * Edit Information od Institution Page
     * @param   Illuminate\Http\Request
     * @return  \Illuminate\Http\Response
     */
    public function institution(Request $request)
    {
        # Caso a requisição venha do Wizard seta como verdadeiro.
        $wizard = $request->get('wizard', 0);

        $award_id = $request->get('award_id');

        // Busca a instituição vinculada
        // if (Route::currentRouteName() == 'user.institution.register') {
        //     $institution = Auth::guard()->user()->institution()->first();
        // }

        // get institution an data to view
        if(!empty($request->seo_url)) {
            $institution = Auth::guard()->user()->institutions
                ->where('seo_url', $request->seo_url)
                ->first();
        }

        $representative = Auth::guard()->user();

        // Chama a view ddo perfil
        return view('institutions.register', compact('institution', 'wizard', 'representative', 'award_id'));
    }

    /**
     * Register all information of institution
     * @method  POST
     * @param   Illuminate\Http\Request
     * @return  \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $wizard = $request->get('wizard');

        /* ========= ALTERA OS VALORES ANTES DE VALIDAR ==========*/
        $request_inputs =  array_map('trim', $request->all());

        // check cnpj
        $request_inputs['cnpj'] = preg_replace("/\D/", "", $request_inputs['cnpj']);
        $request_inputs['cep'] = preg_replace("/\D/", "", $request_inputs['cep']);
        $request_inputs['phone'] = preg_replace("/\D/", "", $request_inputs['phone']);
        $request_inputs['phone2'] = preg_replace("/\D/", "", $request_inputs['phone2']);

        // ajusta urls
        $request_inputs['url'] = preg_replace("/^(https?:\/\/)/", "", $request_inputs['url']);
        $request_inputs['social_facebook_page'] = preg_replace("/^(https?:\/\/)/", "", $request_inputs['social_facebook_page']);
        $request_inputs['social_twitter_page'] = preg_replace("/^(https?:\/\/)/", "", $request_inputs['social_twitter_page']);
        $request_inputs['social_youtube_page'] = preg_replace("/^(https?:\/\/)/", "", $request_inputs['social_youtube_page']);
        $request_inputs['social_instagram_page'] = preg_replace("/^(https?:\/\/)/", "", $request_inputs['social_instagram_page']);

        $request->replace($request_inputs);
        /*=====  End of ALTERA OS VALORES ANTES DE VALIDAR  ======*/

        $data = [];
        // $isEditing = !empty($request_inputs['id']);

        // Valida os campos
        $validated = $this->validator($request)->validate();

        // Trata a Imagem
        unset($validated['image']);
        if ($request->change_image == 1 && $image_name = Storage::storeImage($request, 'institutions')) {
            $validated['image'] = $image_name;
        }

        // Validar Representante
        $responsibleValidated = $this->representativeValidator($request)->validate();

        # Faz o tratamento do CARGO DO RESPONSÁVEL para texto
        $office_post_description = \App\Enums\InstitutionOfficePosts::getDescription((int) $request_inputs['office_post']);
        $request_inputs['responsible_office_post'] = $request_inputs['office_post'];
        $request_inputs['responsible_other_office_post'] = $request_inputs['office_post'] == 11 ? $request_inputs['other_responsible_office_post'] : NULL;

        $data = array_merge($request_inputs, $validated, $responsibleValidated);
        
        // Salva no BD
        $institution = Institution::store($data);

        if(empty($request_inputs['id'])) {
            // salva a pessoa como vinculado
            $user = $representative = Auth::guard()->user();
            // salvar relacionamente do representante e seu posto
            $institution->representatives()->attach($user, [
                "office_post" => null,
                "other_office_post" => null,
                "representative" => 1
            ]);
        }


        if ($wizard == true) {
            $award_id = $request->get('award_id');
            return redirect()->route('user.institution.socialtecnologies.create', ['award_id' => $award_id])->with('message', 'Instituição cadastrada com sucesso!');
        } else if ($request->action == 'admin.institution.register') {
           return redirect()->route('admin.institution.index')->with('message', 'Instituição alterada com sucesso!');
        } else {
           return redirect()->route('user.institution.show', ['seo_url' => $institution->seo_url])->with('message', 'Instituição alterada com sucesso!');
        }
        /*=====  End of SALVA DADOS NO BD  ======*/
    }

    /**
     * Link a new user to this institution
     * @method  POST
     * @param   Illuminate\Http\Request
     * @return  \Illuminate\Http\Response
     */
    public function linkusers(Request $request)
    {
        $validated = Validator::make($request->all() ,[
            'link_user_id' => 'required|integer|exists:users,id',
            'institution_id' => 'required|integer|exists:institutions,id',
            'office_post' => 'sometimes|required|integer',
        ],
        [ 'required' => 'O campo :attribute é obrigatório', ],
        [
            'office_post'      => 'Cargo do usuário na instituição',
        ], $this->messages)->validate();


        $user = User::find($validated['link_user_id']);
        $institution = Institution::find($validated['institution_id']);

        if($user->institutions->where('id', $institution->id)->first() == null) {

            if(Route::is('user.institution.post.link.repre')) {
                // attach user into institution with pivot var
                $institution->representatives()->attach($user, [
                    "office_post" => $validated['office_post'],
                    "other_office_post" => $request->get('other_responsible_office_post'),
                    "representative" => 1
                ]);

                // Chama a view do perfil
                return redirect()
                    ->route('user.institution.show', ['seo_url' => $institution->seo_url,'activetab' => 'repre'])
                    ->with('message', 'Representante vinculado com sucesso!');
            } else {
                // dump($validated['link_user_id']);
                $institution->representatives()->attach($user);

                // Chama a view do perfil
                return redirect()
                    ->route('user.institution.show', ['seo_url' => $institution->seo_url,'activetab' => 'users'])
                    ->with('message', 'Usuário vinculado com sucesso!');
            }

        } else {
            if(Route::is('user.institution.post.link.repre')) {
                return redirect()
                    ->route('user.institution.show', ['seo_url' => $institution->seo_url,'activetab' => 'repre'])
                    ->withErrors('Usuário já vinculado a instituição!');
            } else {
                return redirect()
                    ->route('user.institution.show', ['seo_url' => $institution->seo_url,'activetab' => 'users'])
                    ->withErrors('Usuário já vinculado a instituição!');
            }
        }
    }

    /**
     * Unlink a new user to this institution
     * @method  POST
     * @param   Illuminate\Http\Request
     * @return  \Illuminate\Http\Response
     */
    public function unlinkusers(Request $request)
    {
        // Validation
        $validated = $request->validate([
            'unlink_user_id' => 'required|integer|exists:users,id',
            'institution_id' => 'required|integer|exists:institutions,id',
        ]);
        /*=====  End of VALIDATE POST FIELDS  ======*/

        $user = User::find($validated['unlink_user_id']);
        $institution = Institution::find($validated['institution_id']);

        if(Route::is('user.institution.post.unlink.repre')) {
            // dump(Route::currentRouteName());
            $institution->representatives()->detach($user);

            // Chama a view do perfil
            return redirect()
                ->route('user.institution.show', ['seo_url' => $institution->seo_url, 'activetab' => 'repre'])
                ->with('message', 'Representante desvinculado com sucesso!');
        } else {
            // dump(Route::currentRouteName());
            $institution->persons()->detach($user);

            // Chama a view do perfil
            return redirect()
                ->route('user.institution.show', ['seo_url' => $institution->seo_url, 'activetab' => 'users'])
                ->with('message', 'Usuário desvinculado com sucesso!');
        }
    }

    /**
     * Disable a instituition
     * @param   Illuminate\Http\Request $request
     */
    public function disableinstitution(Request $request) {

        // Verifica se existe usuário logado
        if (empty(Auth::guard()->user())) {
            redirect()->route('front.home');
        }

        $institution = Institution::find($request->id);

        if(!empty($institution->representatives->where('id', Auth::guard()->user()->id)->first())) {

            // desvinculando representantes e pessoas vinculadas da instituição.
            $institution->representatives()->detach();
            $institution->persons()->detach();
            // TODO: futuramente desvincular tbm as TS da instituição.

            Institution::find($request->id)->delete();

            // Retorna para a página do perfil
            return redirect($request->input('source', route('user.institutions')))->with('message', trans('front.user.institution-unlink-success'));
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
        $institution = Institution::where('seo_url', $seo_url)->first();

        // Volta para a home se não existe
        if (empty($institution->id)) {
            return redirect()->route('front.home');
        }

        // Tecnologias Sociais
        $socialtecnologies = $institution->socialtecnologies()->orderBy('socialtecnology_name')->paginate(4);

        // Busca os eventos da instituição
        $events = $institution->events()->where('end_date', '>=', Carbon::now())->orderBy('start_date', 'asc')->orderBy('title', 'asc')->paginate(4);

        // Busca os usuários vinculados
        $users_institution = $institution->persons()->orderBy('name', 'asc')->get();

        // Buscar os representantes
        $representatives = $institution->representatives()->orderBy('name', 'asc')->get();

        // Monta array com as variáveis que serão enviadas para a view
        $view_parameters = array_merge(
            compact('institution', 'events', 'socialtecnologies','users_institution', 'representatives'),
            [
                'seo_title' => str_replace("[NOMEINSTITUICAO]", $institution->institution_name, \Config::get('custom_configuration.seo.institution-profile.title')),
                'seo_description' => str_replace("[NOMEINSTITUICAO]", $institution->institution_name, \Config::get('custom_configuration.seo.institution-profile.description')),
            ]
        );

        // Chama a view do perfil
        return view('institutions.public_profile', $view_parameters);
    }

}
