<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Carbon\Carbon;
use App\Filesystem\Storage;
use App\Enums\SeoRobots;
use App\Event;
use App\Theme;
use App\EventUser;

use Auth;
use Validator;

class EventsController extends Controller
{
    /* CUSTOM VALIDATION MESSAGES */
    protected $messages = [
        'formated_start_date.required' => '"Data inicial" é obrigatório.',
        'formated_start_date.date' => 'As informãções de Data e Hora de início do evento não estão corretas.',
        'formated_end_date.required' => '"Data final" é obrigatório.',
        'formated_end_date.date' => 'As informãções de Data e Hora de término do evento não estão corretas.',
    ];

    /**
     * Validate Events Form
     * @param   Illuminate\Http\Request $request
     * @return  Validator
     */
    protected function validator(Request $request)
    {
        // Cria o objeto de validação
        $validation = Validator::make($request->all(), [
            'title'=>'required',
            'summary'=>'max:250',
            'description'=>'required',
            'image' => 'image|max:2000',
            'address'=>'required',
            'neighborhood'=>'required',
            'city'=>'required',
            'state'=>'required',
            'location'=>'required',
            'formated_start_date'=>'required|date',
            'formated_end_date'=>'required|date',
            'institution_id' => 'required',
        ], $this->messages);

        // Imagem obrigatória apenas para quando está criando
        $validation->sometimes('image', 'required', function ($input) use ($request) {
            return empty($request->input('id'));
        });

        // Imagem obrigatória apenas para quando está criando
        $validation->sometimes('institution_id', function ($attribute, $value, $fail) {
                if ($value != Auth::guard()->user()->institution_id) {
                    $fail('Ocorreu um erro ao editar seu evento. Por gentileza, entre em contato com o administrador. [ERR_COD - EVN001]');
                }
            }, function ($input) use ($request) {
            return $request->input('action') == \App\Enums\FormActions::Front;
        });

        // Se adicionou imagem, valida o tamanho e a extensão
        $validation->sometimes(['image'], [
            (empty($request->size_image) ? '' : 'max:'.($request->size_image * 1024)),
            function ($attribute, $value, $fail) use ($validation) {
                if ($value->extension() != 'png' && $value->extension() != 'jpeg' && $value->extension() != 'jpg') {
                   $validation->errors()->add('photo', 'Foto do evento: é permitido apenas imagens PNG, JPEG ou JPG.');
                }
            },
        ], function ($input) {
            return $input->change_image == 1;
        });


        // Valida se as datas estão corretas
        $validation->after(function ($validator) {

            // Valida se uma data é maior que a outra
            $validated_data = $validator->getData();
            $failed_fields = $validator->failed();

            if (!isset($failed_fields['formated_start_date']) && !isset($failed_fields['formated_end_date'])) {

                // Concatena os valores em uma variável datetime para comparação
                $start_datetime = Carbon::createFromFormat('Y/m/d H:i', $validated_data['formated_start_date']);
                $end_datetime = Carbon::createFromFormat('Y/m/d H:i', $validated_data['formated_end_date']);

                // Verifica se está a data inicial e maior que a data final
                if ($start_datetime > $end_datetime) {

                        // Adiciona mensagem no validador
                        $validator->errors()->add('formated_start_date', 'Início do evento não pode ser posterior ao fim.');
                }
            }
        });

        return $validation;
    }

    /**
     * Event Index Page
     * @return  \Illuminate\Http\Response
     */
    public function index()
    {
        // Busca os Eventos
        $events = Event::where('end_date', '>=', Carbon::now())->orderBy('start_date', 'asc')->orderBy('title', 'asc')->paginate(4);

        // Monta as 4 eventos mais visualizadas na página principal
        $events_featured = Event::withCount('users')
                                    ->where('start_date', '>=', Carbon::now())
                                    ->orderBy('users_count', 'desc')
                                    ->take(3)
                                    ->get();

        //Busca os temas que possuem eventos
        $themes = Theme::whereHas('events', function (Builder $query) {
            $query->where('start_date', '>=', Carbon::now());
        })->orderBy('name')->get();

        // Se não há tema com eventos de interesse do usuário, seleciona um tema aleatório
        if ($themes->count() <= 0) {
            $themes = Theme::all();
        }

        // Verifica se usuário está logado
        if(!empty(Auth::user())) {
            // Busca um tema aleatório de interesse do usuário para filtrar eventos de seu interesse na sidebar, desde que exista um evento em algum tema de interesse
            $selected_theme = Theme::whereHas('users', function (Builder $query) {
                $query->where("user_id", Auth::user()->id);
            })->inRandomOrder()->first();
        }


        if (empty($selected_theme)) {
            $selected_theme = Arr::random($themes->toArray());
        }

        // Busca os próximos eventos por tema
        $events_schedule = Event::where('theme_id', $selected_theme['id'])
                                ->where('start_date', '>=', Carbon::now())
                                ->orderBy('start_date', 'asc')
                                ->limit(7)
                                ->get()
                                ->groupBy('event_date_string');

        // Monta array com as variáveis que serão enviadas para a view
        $view_parameters = array_merge(
            compact('events', 'events_featured', 'themes', 'events_schedule', 'selected_theme'),
            [
                'seo_title' => \Config::get('custom_configuration.seo.events.title'),
                'seo_description' => \Config::get('custom_configuration.seo.events.description'),
                'seo_keywords' => \Config::get('custom_configuration.seo.events.keywords'),
            ]
        );

        // Retorna a View
        return view('event.index', $view_parameters);
    }

    /**
     * Event Results Page
     * @param   Illuminate\Http\Request $request
     * @return  \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        //Pega o parâmetro do request
        $search_event = $request->all();

        //Busca todos os temas
        $themes = Theme::orderBy('name','asc')->get();

        //Cria o objeto para a pesquisa
        $events = Event::query();

        /*========== FILTROS ==========*/
        if(!empty($search_event['title'])) $events->where('title','like', '%' . $search_event['title'] . '%');
        if(!empty($search_event['institution'])) $events->whereHas('institution', function ($query) use($search_event)  {
            $query->where('institution_name', 'like', '%' . $search_event['institution'] . '%');
        });
        if(!empty($search_event['theme'])) $events->where('theme_id', $search_event['theme']);
        if(!empty($search_event['start_date'])) {
            $where_start_date = Carbon::createFromFormat('d/m/Y H:i', $search_event['start_date']." 00:00");
            $events->where('end_date', '>=', $where_start_date);
        } else {
            $events->where('end_date', '>=', Carbon::now());
        }
        if(!empty($search_event['end_date'])) {
            $where_end_date = Carbon::createFromFormat('d/m/Y H:i', $search_event['end_date']." 23:59");
            $events->where('end_date', '<=', $where_end_date);
        }
        /*=====  End of FILTROS  ======*/

        /*========== FILTRO DE LETRAS ==========*/
        // Busca os caracteres para o filtro
        $letters_filter = array();
        foreach ($events->get() as $blog) {
            $letter_code = ord($blog->title);

            if ($letter_code < 65 || $letter_code > 90) $letter_code = 48;

            if (!isset($letters_filter[$letter_code])) $letters_filter[$letter_code] = chr($letter_code);
        }

        // Filtra pela primeira letra
        if(!empty($search_event['letter'])) {

            // Pega o Código ACII da letra
            $letter_filter = ord($search_event['letter']);

            // Verifica se é para buscar todos que não são numéricos
            if ($letter_filter < 65 || $letter_filter > 90) {
                $events->whereRaw("ASCII(SUBSTRING(title, 1, 1)) < 65")->orWhereRaw("ASCII(SUBSTRING(title, 1, 1)) > 90");
            } else {
                $events->whereRaw("ASCII(SUBSTRING(title, 1, 1)) = ?", [$letter_filter]);

            }
        }
        //Orderna as letras por ordem ascendente
        array_multisort($letters_filter, SORT_ASC);

        /*=====  End of FILTRO DE LETRAS  ======*/

        //Ordena o resultado por páginação
        $events = $events->orderBy('start_date', 'asc')->paginate(10)
         ->withPath($request->url() . ($request->getQueryString() ? ('?' . $request->getQueryString()) : ''));

        // Monta array com as variáveis que serão enviadas para a view
        $view_parameters = array_merge(
            compact('events','themes', 'letters_filter'),
            [
                'seo_meta_robots' => SeoRobots::None,
                'seo_title' => \Config::get('custom_configuration.seo.events.title'),
                'seo_description' => \Config::get('custom_configuration.seo.events.description'),
                'seo_keywords' => \Config::get('custom_configuration.seo.events.keywords'),
            ]
        );

        // Retorna o HTML
        return view('event.search', $view_parameters);
    }

    /**
     * Event Detail Page
     * @param   String $seo_url
     * @return  \Illuminate\Http\Response
     */
    public function detail($seo_url)
    {
        // Busca o objeto do evento
        $event = Event::where('seo_url', $seo_url)->first();

        // Se não existe, volta para a home dos Eventos
        if (empty($event)) {
            return redirect()->route('front.event.index');
        }

        // Busca os Usuários interessados no Evento
        $event_user = $event->users()->paginate(4);

        // Retorna se o usuário está interessado ou não no evento
        if(!empty(Auth::user())) {
            $hasinterest = Auth::user()->events()
            ->where('id', $event->id)
            ->get();
        }

        // Monta array com as variáveis que serão enviadas para a view
        $view_parameters = array_merge(
            compact('event','hasinterest','event_user'),
            [
                'seo_title' => str_replace("[NOMEEVENTO]", $event->title, \Config::get('custom_configuration.seo.events.detail')),
                'seo_description' => $event->summary,
                'seo_keywords' => (!empty($event->theme) ? $event->theme->name : ''),
            ]
        );

        return view('event.detail', $view_parameters);
    }

    /**
     * Edit Information of Events Page
     * @param   Illuminate\Http\Request $request
     * @param   Integer $ev_id
     * @return  \Illuminate\Http\Response
     */
    public function event(Request $request, Int $ev_id = null)
    {
        // Busca a instituição vinculada
        if (!empty($ev_id)) {
            $event = Event::find($ev_id);

            if ($event->institution_id != Auth::guard()->user()->institution_id) {
                return redirect()->route('user.institution.index')->with('error', 'Você não pode editar este Evento.');
            }
        }

        // Busca a instituição vinculada
        $institution = Auth::guard()->user()->institution()->first();

        //Busca todos os temas
        $themes = Theme::all();

        // Chama a view de detalhe
        return view('event.register', compact('event', 'institution', 'themes'));
    }

    /**
     * Register all information of event
     * @method  POST
     * @param   Illuminate\Http\Request $request
     * @return  \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        if ($request->input('action') == \App\Enums\FormActions::Admin) {
            $this->middleware('admin');
        } else {
            $this->middleware('auth');
        }

        // Altera o formato da data no request para validação
        $request_inputs = $request->all();

        // Preenche os campos de hora
        $aux_start_time = !empty($request->input('start_time')) ? $request->input('start_time') : '00:00';
        $aux_end_time = !empty($request->input('end_time')) ? $request->input('end_time') : '23:59';

        // Cria a data formatada para validar
        $request_inputs['formated_start_date'] = empty($request_inputs['start_date']) ? $request_inputs['start_date'] : \App\Helpers::format_date($request_inputs['start_date'])." ".$aux_start_time;
        $request_inputs['formated_end_date'] = empty($request_inputs['end_date']) ? $request_inputs['end_date'] : \App\Helpers::format_date($request_inputs['end_date'])." ".$aux_end_time;

        // Coloca os valores formatados no request para validação
        $request->replace($request_inputs);

        // Valida os campos
        $validated = $this->validator($request)->validate();

        // Trata a Imagem
        unset($validated['image']);
        if ($request->change_image == 1 && $image_name = Storage::storeImage($request, 'events')) {
            $validated['image'] = $image_name;
        }

        // Salva no BD
        Event::store(array_merge($request_inputs, $validated));

        if($request->input('action') == \App\Enums\FormActions::Admin)
            return redirect('admin/agenda')->with('message', 'Evento salvo com sucesso.')->withInput();
        else
            return redirect()->route('user.institution.index', ['activetab' => 'events'])->with('message', 'Evento salvo com sucesso.');
    }
}
