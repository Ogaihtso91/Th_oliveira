<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;
use App\Enums\SocialTecnologiesAwardStatus;
use App\Enums\SeoRobots;
use App\Filesystem\Storage;
use App\Keyword;
use App\Ods;
use App\SocialTecnology;
use App\SocialTecnologyView;
use App\Theme;
use Auth;
use Illuminate\View\View;
use Validator;
use Illuminate\Support\Facades\DB;

class SocialTecnologiesController extends Controller
{

    protected $TS;

    public function __construct(SocialTecnology $TS)
    {
        $this->TS = $TS;
    }

    /* CUSTOM VALIDATION MESSAGES */
    protected $messages = [
        'socialtecnology_name.string' => 'O campo "Título da Tecnologia" deve ser no formato de texto.',
        'socialtecnology_name.max' => 'O campo "Título da Tecnologia" deve possuir até 255 caracteres.',
        'seo_url.required' => 'Ocorreu um erro ao salvar a tecnologia. Por gentileza, entre em contato com o administrador [ERR_COD - TS001]',
        'seo_url.string' => 'Ocorreu um erro ao salvar a tecnologia. Por gentileza, entre em contato com o administrador [ERR_COD - TS002]',
        'socialtecnology_id.required' => 'Ocorreu um erro ao enviar o comentário. Por gentileza, entre em contato com o administrador [ERR_COD - CMTS001]',
        'socialtecnology_id.integer' => 'Ocorreu um erro ao enviar o comentário. Por gentileza, entre em contato com o administrador [ERR_COD - CMTS002]',
        'comment.required' => 'Escreva um comentário válido.',
        'comment.string' => 'O comentário deve ser no formato de texto.',
    ];

    /**
     * Validate Social Tecnology Form
     * @param   Illuminate\Http\Request $request
     * @return  Validator
     */
    protected function validator(Request $request)
    {
        // Cria o objeto de validação
        $validation = Validator::make($request->all(), [
            'institution_id' => 'required|integer',
            'socialtecnology_name' => 'required|string|max:255',
        ], $this->messages);

        // Valida a imagem somente quando estiver alterando
        $validation->sometimes(['image'], [
            (empty($request->size_image) ? '' : 'max:'.($request->size_image * 1024)),
            function ($attribute, $value, $fail) {
                if ($value->extension() != 'png' && $value->extension() != 'jpeg' && $value->extension() != 'jpg') {
                    $fail('Foto da tecnologia: é permitido apenas imagens PNG, JPEG ou JPG.');
                }
            },
        ], function ($input) {
            return $input->change_image == 1;
        });

        // Valida os vídeos
        $validation->sometimes(['videos'], [
            function ($attribute, $value, $fail) {
                foreach ($value as $v_video_item) {
                    if (!empty($v_video_item)) {

                        $video_validation = \App\Helpers::retrieve_youtube_information($v_video_item);

                        if (empty($video_validation['title']))
                            $fail('Vídeo informado não existe.');
                    }
                }
            },
        ], function ($input) {
            return !empty($input->videos);
        });

        return $validation;
    }

    /**
     * Search Action
     * @param Request $request
     * @param String $seo_url
     * @return RedirectResponse|Factory|View
     */
    public function search(Request $request, String $seo_url = null)
    {
        //DB::enableQueryLog();

        // Pega os parâmetros do request
        $filters = $request->all();

//        dd($filters);

        // Informações de SEO da página
        $seo_title = \Config::get('custom_configuration.seo.socialtecnology.title');
        $seo_description = \Config::get('custom_configuration.seo.socialtecnology.description');
        $seo_meta_robots = SeoRobots::None;

        // Verifica a Rota para montar o objeto
        if (Route::is('front.socialtecnology.ods')) {

            // Busca o ODS
            $ods = Ods::where('seo_url', $seo_url)->first();

            // Volta para a home se não existe
            if (empty($ods->id)) {
                return redirect()->route('front.home');
            }

            // Adiciona ODS ao filtro
            $filters['ods'] = $ods->id;

            // Informações de SEO da página
            $seo_title = str_replace("[TITULOODS]", $ods->name, \Config::get('custom_configuration.seo.socialtecnology.ods-title'));
            $seo_description = str_replace("[TITULOODS]", $ods->name, \Config::get('custom_configuration.seo.socialtecnology.ods-description'));
            $seo_meta_robots = SeoRobots::All;

        } else if (Route::is('front.socialtecnology.theme')) {

            // Busca o tema
            $theme = Theme::where('seo_url', $seo_url)->first();

            // Volta para a home se não existe
            if (empty($theme->id)) {
                return redirect()->route('front.home');
            }

            // Adiciona Tema ao filtro
            $filters['theme'] = $theme->id;

            // Informações de SEO da página
            $seo_title = str_replace("[TITULOTEMA]", $theme->name, \Config::get('custom_configuration.seo.socialtecnology.theme-title'));
            $seo_description = str_replace("[TITULOTEMA]", $theme->name, \Config::get('custom_configuration.seo.socialtecnology.theme-description'));
            $seo_meta_robots = SeoRobots::All;
        }

        // Cria o objeto para pesquisa
        $query = SocialTecnology::query();

        $query->leftJoin('institutions', 'institutions.id', '=', 'social_tecnologies.institution_id');

        /*===============================
        =            FILTROS            =
        ===============================*/

        // Resultado premiação
        if(!empty($filters['award_status'])) {
            $query->where('award_status', $filters['award_status']);
        }

        // Ano de participação
        if(!empty($filters['award_year'])) {
            $query->where('award_year', $filters['award_year']);
        }

        // UF
        if (!empty($filters['state'])) {
            $query->where('institutions.state', $filters['state']);
        }

        // Tema
        if(!empty($filters['theme'])) {
            $query->whereHas('themes', function ($q) use ($filters){
                $q->where('theme_id', $filters['theme']);
            });
        }

        // Subtema
        if(!empty($filters['sub_theme'])) {
            $query->whereHas('secondaryTheme', function ($q) use ($filters){
                $q->where('theme_id', $filters['sub_theme']);
            });
        }

        // ODS
        if(!empty($filters['ods'])) {
            $arr_ods = explode(",", $filters['ods']);
            $query->orWhereHas('ods', function ($q) use ($arr_ods){
                $q->whereIn('ods_id', $arr_ods);
            });
        }

        // Palavra Chave
        if(!empty($filters['keyword'])) {

            \App\Helpers::make_fulltext_where(
                $query,
                'social_tecnologies',
                'socialtecnology_name,fulltext_themes,fulltext_keywords,fulltext_institution',
                'social_tecnologies.id',
                $filters['keyword']);
        }

        // Instituição
        if(!empty($filters['institution'])) {

            // Quebra por espaços e remove espaços em branco.
            $aux_institution_query = preg_split('/\s+/', $filters['institution'], -1, PREG_SPLIT_NO_EMPTY);

            // Busca as instituições de acordo com as palavras digitadas (AND)
            $query->where(function ($q) use ($aux_institution_query) {
                foreach ($aux_institution_query as $value) {
                    $q->where('fulltext_institution', 'like', "%{$value}%");
                }
            });
        }
        /*=====  End of FILTER  ======*/

        // Busca as tecnologias, paginando se existirem mais que 10 registros
        $socialtecnologies = $query->orderBy('socialtecnology_name');

        /*========================================
        =            FILTRO DE LETRAS            =
        ========================================*/
        // Busca os caracteres para o filtro
        $letters_filter = array();
        
        $queryLetters = $query->get();

        foreach ($queryLetters as $socialtecnology) {
            $letter_code = ord($socialtecnology->socialtecnology_name);

            if ($letter_code < 65 || $letter_code > 90) $letter_code = 48;

            if (!isset($letters_filter[$letter_code])) $letters_filter[$letter_code] = chr($letter_code);
        }

        // Filta pela primeira letra
        if(!empty($filters['letter'])) {

            // Pega o Código ACII da letra
            $letter_filter = ord($filters['letter']);

            // Verifica se é para buscar todos que não são numéricos
            if ($letter_filter < 65 || $letter_filter > 90) {
                $query->whereRaw("ASCII(SUBSTRING(socialtecnology_name, 1, 1)) < 65")->orWhereRaw("ASCII(SUBSTRING(socialtecnology_name, 1, 1)) > 90");
            } else {
                $query->whereRaw("ASCII(SUBSTRING(socialtecnology_name, 1, 1)) = ?", [$letter_filter]);
            }
        }

        //Orderna as letras por ordem ascendente
        array_multisort($letters_filter, SORT_ASC);

        /*=====  End of FILTRO DE LETRAS  ======*/

        // Salva em uma variável auxiliar a query para preencher os filtros
        //$socialtecnologies_filter_ids = $query->get()->pluck('id')->toArray();


        //Busca os temas
        $themes = Theme::orderBy('name')->get();

        //Busca as ODS
        $ods_all = Ods::orderBy('id')->get();

        // Busca os Anos de Premiação
        $award_years = SocialTecnology::select('award_year')
            ->whereNotNull('award_year')
            ->orderBy('award_year')
            ->distinct()
            ->get()
            ->pluck('award_year')
            ->toArray();

        // Busca os Anos de Premiação
        $award_status = SocialTecnologiesAwardStatus::toArray();

        # Seta o nome das colunas para evitar os erros de duplicidade dos nomes quando usado *
        $query->selectRaw('social_tecnologies.id, social_tecnologies.summary, social_tecnologies.image, social_tecnologies.seo_url, social_tecnologies.socialtecnology_name, social_tecnologies.institution_id');

        // Busca as tecnologias, paginando se existirem mais que 10 registros
        $socialtecnologies = $query->paginate(10)
            ->withPath($request->url() . ($request->getQueryString() ? ('?' . $request->getQueryString()) : ''));

        // Monta array com as variáveis que serão enviadas para a view
        $view_parameters = array_merge(
            compact('themes', 'ods_all', 'socialtecnologies', 'letters_filter', 'theme', 'ods', 'award_years', 'award_status', 'filters'),
            [
                'seo_meta_robots' => $seo_meta_robots,
                'seo_title' => $seo_title,
                'seo_description' => $seo_description,
            ]
        );

        // Retorna o HTML
        return view('search', $view_parameters);
    }

    /**
     * Social Tecnology Detail Page
     * @param   String $seo_url
     * @return  \Illuminate\Http\Response
     */
    public function detail(String $seo_url)
    {
        // Busca a instituição vinculada
        $socialtecnology = SocialTecnology::where('seo_url', $seo_url)->first();

        // Volta para a home se não existe
        if (empty($socialtecnology->id)) {
            return redirect()->route('front.home');
        }

        // Busca a instituição vinculada
        $institution =  $socialtecnology->institution()->first();

        // Busca os eventos da instituição
        $events = $institution->events()
                    ->where('start_date', '>=', Carbon::now())
                    ->orderBy('start_date', 'asc')
                    ->limit(7)
                    ->get()
                    ->groupBy('event_date_string');

        //Grava o ip do usuário que acessou a TS. Se o ip já estiver no banco de dados, não grava.
        if(empty($socialtecnology->getSeenAttribute())) {

            // Retorna o ip real do usuário que acessou a notícia
            $local_ip = \App\Helpers::get_user_ip();
            $local_ip = preg_replace("/\D/", "", $local_ip);

            SocialTecnologyView::create([
                'socialtecnology_id' => $socialtecnology->id,
                'ip' =>  $local_ip
            ]);
        }

        // Busca as palavras-chave
        $keywords = Keyword::all();

        /*========== TECNOLOGIAS VINCULADAS ==========*/

        // Cria o array que receberá as TS relacionadas
        $related_socialtecnologies = new Collection([]);

        // Pega as palavras chaves da tecnologia social
        $arr_keywords = $socialtecnology->keywords->pluck('name')->toArray();

        // Cria o objeto para pesquisa
        $query = SocialTecnology::query()->where('id', '<>', $socialtecnology->id);

        // Adiciona condicional para trazer as TS relacionadas
        if (!empty($arr_keywords)) {

            \App\Helpers::make_fulltext_where(
                $query,
                'social_tecnologies',
                'fulltext_keywords',
                'social_tecnologies.id',
                $arr_keywords,
                4, true);

            // Busca as TS relacionadas
            $related_socialtecnologies = $query->limit(4)->get();
        }
        unset($query);

        // Caso não encontre quatro TS com as palavras chaves, busca por tema
        if ($related_socialtecnologies->count() < 4) {

            // Pega os temas da tecnologia social
            $arr_themes = $socialtecnology->themes->pluck('name')->toArray();

            // Pega os ids para exclusão
            $arr_exclude = $related_socialtecnologies->pluck('id')->toArray();
            $arr_exclude[] = $socialtecnology->id;

            // Cria o objeto para pesquisa
            $query = SocialTecnology::query()->whereNotIn('id', $arr_exclude);

            // Adiciona condicional para trazer as TS relacionadas
            if (!empty($arr_themes)) {

                \App\Helpers::make_fulltext_where(
                    $query,
                    'social_tecnologies',
                    'fulltext_themes',
                    'social_tecnologies.id',
                    $arr_themes,
                    4, true);

                // Busca as TS por temas
                $related_socialtecnologies_aux = $query->limit(4)->get();

                // Adiciona no array com as TS até chegar ao limite de tecnologias sociais para mostrar na tela
                $i = 0;
                while($related_socialtecnologies->count() < 4) {
                    $related_socialtecnologies->push($related_socialtecnologies_aux[$i]);
                    $i++;
                }
            }
        }
        /*=====  End of TECNOLOGIAS VINCULADAS  ======*/
        // Monta array com as variáveis que serão enviadas para a view
        $view_parameters = array_merge(
            compact('socialtecnology','related_socialtecnologies','institution', 'events','keywords'),
            [
                'seo_title' => str_replace("[NOMETECNOLOGIASOCIAL]", $socialtecnology->socialtecnology_name, \Config::get('custom_configuration.seo.socialtecnology.detail')),
                'seo_description' => $socialtecnology->summary,
                'seo_keywords' => $socialtecnology->keywords->implode('name', ','),
            ]
        );


        // Chama a view de detalhe
        return view('socialtecnology.detail', $view_parameters);
    }

    /**
     * Social Tecnology's Edit Page
     * @param   Illuminate\Http\Request $request
     * @param   Integer $st_id
     * @return  \Illuminate\Http\Response
     */
    public function socialtecnology(Request $request, Int $st_id = null)
    {
        // Verifica se o usuário possui permissão para editar esta tecnologia
        if (!empty($st_id)) {
            $socialtecnology = Auth::guard()->user()->socialtecnologies()->find($st_id);

            if (empty($socialtecnology)) {
                return redirect()->route('user.institution.index')->with('error', 'Você não pode editar esta Tecnologia Social.');
            }
        }

        // Busca os temas
        $themes = Theme::orderBy('name')->get();

        // Busca as palavras-chave
        $keywords = Keyword::orderBy('name')->get();

        // Busca as ods
        $ods = Ods::all();

        // Busca a instituição vinculada
        $institution = Auth::guard()->user()->institution()->first();

        // Busca os usuários da instituição
        $users_institutions = $institution->users()->where('id', '!=', Auth::guard()->user()->id);

        // Clausula para não mostrar os já vinculados
        if (!empty($st_id)) {
            $users_institutions->whereDoesntHave('socialtecnologies', function ($q) use ($st_id) {
                $q->where('id', $st_id);
            });
        }
        $users_institutions = $users_institutions->orderBy('name')->get();

        // Chama a view de detalhe
        return view('socialtecnology.detail', compact('socialtecnology', 'institution', 'themes', 'ods', 'keywords', 'users_institutions'));
    }

     /**
     * Register all information of institution
     * @method  POST
     * @param   Illuminate\Http\Request $request
     * @return  \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        // Verifica se está chamando do Admin ou da página da Instituição
        if ($request->input('action') == 'admin') {
            $this->middleware('admin');
        } else {
            $this->middleware('auth');
        }

        // Valida os campos
        $validated = $this->validator($request)->validate();

        // Trata a Imagem
        unset($validated['image']);
        if ($request->change_image == 1 && $image_name = Storage::storeImage($request, 'socialtecnologies')) {
            $validated['image'] = $image_name;
        }

        // Salva no BD
        SocialTecnology::store(array_merge($request->all(), $validated));

        // Redireciona usuário para página
        if ($request->action == 'admin') {
            return redirect()->route('admin.socialtecnology.index')->with('message', 'Tecnologia salva com sucesso!');
        } else {
            return redirect()->route('user.institution.index')->with('message', 'Tecnologia salva com sucesso!');
        }
    }

    /**
     * Show Followers of Social Tecnology
     * @method  GET
     * @param   Integer $st_id
     * @return  \Illuminate\Http\Response
     */
    public function followers(Int $st_id)
    {
        // Busca a Tecnologia Social
        $socialtecnology_obj = SocialTecnology::find($st_id);

        // Verifica se a tecnologia social existe
        if (empty($socialtecnology_obj->id)) {
            return redirect()->route('user.institution.index');
        }

        // Busca os seguidores da tecnologia Social
        $socialtecnology_followers = $socialtecnology_obj->recommends()->orderBy('name')->paginate(18);

        // Chama a view da instituição
        return view('institutions._socialtecnology._followers', compact('socialtecnology_obj', 'socialtecnology_followers'));
    }
}
