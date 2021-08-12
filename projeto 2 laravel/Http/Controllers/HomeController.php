<?php

namespace App\Http\Controllers;

use App\Repositories\Repository\AboutUs\GetAboutUsRepository;
use App\Repositories\Repository\Award\GetAwardRepository;
use App\Repositories\Repository\CategoryAward\GetCategoryAwardRepository;
use App\Repositories\Repository\CategoryAwardEvaluationStep\GetCategoryAwardEvaluationStepRepository;
use App\Repositories\Repository\SocialTecnology\GetSocialTecnologyRepository;
use App\Services\AboutUs\PageAboutUsService;
use App\Services\Award\AwardFileService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Blog;
use App\Theme;
use App\Event;
use App\SocialTecnology;
use App\Mail\ContactUs;
use Auth;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Validator;
use App\CategoryAward;

class HomeController extends Controller
{
    private $getAward;
    private $getAwardEvaluationStep;
    private $awardFileService;
    private $getAboutUs;
    private $pageAboutUsService;
    private $getSocialTecnology;

    public function __construct()
    {
        $this->getAward = new GetAwardRepository();
        $this->getCategoryAward = new GetCategoryAwardRepository();
        $this->getAwardEvaluationStep = new GetCategoryAwardEvaluationStepRepository();
        $this->awardFileService = new AwardFileService();
        $this->getAboutUs = new GetAboutUsRepository();
        $this->pageAboutUsService = new PageAboutUsService();
        $this->getSocialTecnology = new GetSocialTecnologyRepository();
    }

    /**
     * Site Index Page
     * @return  \Illuminate\Http\Response
     */
    public function index()
    {
        // Busca os temas
        $themes = Theme::where('show',1)->orderby('name','asc')->get();

        // Busca as tecnologias sociais mais vistas
        $socialtecnologies = SocialTecnology::withCount(['views', 'recommends'])
            ->orderBy('views_count', 'desc')
            ->orderBy('recommends_count', 'desc')
            ->limit(5)->get();

        // Busca os artigos do blog
        $blogs = Blog::orderBy('created_at', 'desc')->take(4)->get();

        // Busca os próximos eventos
        $events = Event::where('start_date', '>=', Carbon::now())->orderBy('start_date', 'asc')->limit(7)->get()->groupBy('event_date_string');

        // Monta array com as variáveis que serão enviadas para a view
        $view_parameters = array_merge(
            compact('themes','testimonies','socialtecnologies','blogs','events'),
            [
                'seo_title' => \Config::get('custom_configuration.seo.home.title'),
                'seo_description' => \Config::get('custom_configuration.seo.home.description'),
                'seo_keywords' => \Config::get('custom_configuration.seo.home.keywords'),
            ]
        );

        // Retorna a view
        return view('home', $view_parameters);

    }


    /**
     * @param Request $request
     * @return Factory|RedirectResponse|View
     */
    public function about(Request $request)
    {
        try {
            return view('about', [
                'seo_title' => \Config::get('custom_configuration.seo.about.title'),
                'seo_description' => \Config::get('custom_configuration.seo.about.description'),
                'seo_keywords' => \Config::get('custom_configuration.seo.about.keywords'),
                'aboutUs' => $this->getAboutUs->first(),
                'nationwide' => $this->pageAboutUsService->getNationwide(),
                'registeredTechnologies' => $this->getSocialTecnology->countAll(),
                'numberOfEditsMade' => $this->getAward->countAll(),
                'amountOfCertifiedTechnologies' => $this->getSocialTecnology->countCertifiedTechnologies(),
            ]);

        } catch (\Throwable $throwable) {
            create_error_log($throwable, $request, 'Erro ao acessar a página sobre nós');
        }

        return redirect()
            ->back()
            ->with('message', 'Não foi possível acessar a página sobre nós. Tente novamente ou entre em contato com o administrador do site.');

    }

    /**
     * Send Contact US Form
     * @method  POST
     * @param   Illuminate\Http\Request $request
     * @return  \Illuminate\Http\Response
     */
    public function contactus(Request $request)
    {
        // Valida Campos
        $validator = Validator::make($request->all(), [
            'cf_name'=>'required',
            'cf_email'=>'required|email',
            'cf_subject'=>'required',
            'cf_image' => 'mimes:jpeg,bmp,png|max:2000'
        ]);

        // Se houver erros, retorna à página que o usuário se encontrava
        if ($validator->fails()) {
            return back()
                    ->withErrors($validator, 'contactus')
                    ->withInput();
        }

        // Monta o assunto do e-mail
        $subject = __("front.contact_us.subject.pre_subject").__("front.contact_us.subject.".$request->get('cf_subject'));

        // Envia e-mail
        Mail::to(config("mail.customparameters.targetaddress"))->send(new ContactUs([
            'name' => $request->get('cf_name'),
            'email' => $request->get('cf_email'),
            'phone' => $request->get('cf_phone'),
            'subject' => $subject,
            'message' => $request->get('cf_message'),
            'file' => $request->hasfile('cf_image') && $request->file('cf_image')->isValid() ? $request->file('cf_image') : '',
        ]));

        return back()->with('contactus_message', 'Sua mensagem foi enviada com sucesso!');
    }

    /**
     * @param Request $request
     * @return Factory|RedirectResponse|View
     */
    public function awards(Request $request)
    {

        try {
            $awards = $this->getAward->allOrderbyAndPaginate();
            return view('awards', compact('awards'));

        } catch (\Throwable $throwable) {
            create_error_log($throwable, $request, 'Erro ao acessar a tela de prêmios');
        }

        return redirect()
            ->back()
            ->with('error', 'Não foi possível acessar os prêmios. Tente novamente ou entre em contato com o administrador do site.');
    }

    /**
     * @param Request $request
     * @param int $id
     * @return Factory|RedirectResponse|View
     */
    public function awardDetail(Request $request, int $id)
    {
        try {
            $award = $this->getAward->byId($id);
            $images = $award->images;
            $videos = Collection::make();

            // buscamos as informações dos videos
            // para mostrarmos na pagina inicial
            foreach($award->videos as $video) {
                $video_info = youtube_information($video->video_url);

                $videos->push($video_info);

                unset($video_info);
            }

            $steps = $this->getAwardEvaluationStep->byAwardId($id);

            if (empty($award)) return redirect()->back()->with('warning', 'Prêmio não encontrado na base de dados.');

            $redirectData = [
                'redirect_url' => \urlencode(route("user.institution.socialtecnologies.create", ['award_id' => $id]))
            ];
            return view('award', compact('award', 'steps', 'images', 'videos', 'redirectData'));

        } catch (\Throwable $throwable) {
            create_error_log($throwable, $request, "Erro ao acessar o detalhe do prêmio com ID: $id");
        }

        return redirect()
            ->back()
            ->with('error', 'Não foi possível visualizar o prêmio. Tente novamente ou entre em contato com o administrador do site.');
    }


    public function CategoryDetail(Request $request, int $id)
    {
        try {
            $categoryAward = CategoryAward::find($id);
            $steps = $this->getAwardEvaluationStep->byAwardId($id);
            $etapas =  $categoryAward->evaluationSteps;

            if (empty($categoryAward)) {
                return redirect()->back()->with('message', 'Categoria do Prêmio não encontrado na base de dados.');
            }
            
            return view('category', compact('categoryAward', 'steps', 'etapas', 'approveds'));

        } catch (\Throwable $throwable) {
            create_error_log($throwable, $request, "Erro ao acessar o detalhe do prêmio com ID: $id");
        }
        return redirect()
            ->back()
            ->with('error', 'Não foi possível visualizar a categoria prêmio. Tente novamente ou entre em contato com o administrador do site.');
    }

    /**
     * @param Request $request
     * @param int $fileId
     * @return RedirectResponse|BinaryFileResponse
     */
    public function awardFileDownload(Request $request, int $fileId)
    {
        try {
            $pathToFile = $this->awardFileService->getPath($fileId);

            return response()->download($pathToFile);

        } catch (\Throwable $throwable) {
            create_error_log($throwable, $request, "Erro ao fazer download de um anexo");
        }

        return redirect()
            ->back()
            ->with('error', 'Não foi possível fazer download do anexo. Tente novamente ou entre em contato com o administrador do site.');

    }

}
