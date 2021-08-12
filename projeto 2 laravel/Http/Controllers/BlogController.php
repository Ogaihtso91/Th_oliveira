<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Enums\SeoRobots;
use App\Blog;
use App\BlogView;
use App\Theme;
use Carbon\Carbon;

class BlogController extends Controller
{
    /**
     * Blog Index Page
     * @return  \Illuminate\Http\Response
     */
    public function index()
    {
        // Monta as notícias na página principal com paginação
        $blogs = Blog::orderBy('created_at', 'desc')->paginate(4);

        // Monta as 3 noticias promovidas na página principal
        $promoted = Blog::orderBy('created_at','desc')->where('promote','=', 1)->get();

        // Monta as 4 notícias mais visualizadas na página principal
        $most_viewed = Blog::withCount('view')->orderBy('view_count', 'desc')->take(4)->get();

        // Monta array com as variáveis que serão enviadas para a view
        $view_parameters = array_merge(
            compact('blogs', 'most_viewed','promoted'),
            [
                'seo_title' => \Config::get('custom_configuration.seo.blog.title'),
                'seo_description' => \Config::get('custom_configuration.seo.blog.description'),
                'seo_keywords' => \Config::get('custom_configuration.seo.blog.keywords'),
            ]
        );

        return view('blog.index', $view_parameters);
    }

    /**
     * Event Results Page
     * @param   Illuminate\Http\Request $request
     * @return  \Illuminate\Http\Response
     */
    public function search(Request $request)
    {

        //Pega o parâmetro do request
        $filters = $request->all();

        //Busca todos os temas
        $themes = Theme::orderBy('name','asc')->get();

        //Cria o objeto para a pesquisa
        $blogs = Blog::query();

        /*===============================
        =            FILTROS            =
        ===============================*/
        if(!empty($filters['title'])) $blogs->where('title','like', '%' . $filters['title'] . '%');
        if(!empty($filters['author'])) $blogs->where('author','like', '%' . $filters['author'] . '%');
        if(!empty($filters['theme'])) $blogs->where('theme_id', $filters['theme']);
        if(!empty($filters['start_date'])) {
            $where_start_date = Carbon::createFromFormat('d/m/Y H:i', $filters['start_date']." 00:00");
            $blogs->where('created_at', '>=', $where_start_date);
        }
        if(!empty($filters['end_date'])) {
            $where_end_date = Carbon::createFromFormat('d/m/Y H:i', $filters['end_date']." 23:59");
            $blogs->where('created_at', '<=', $where_end_date);
        }

        /*=====  End of FILTROS  ======*/

        /*========================================
        =            FILTRO DE LETRAS            =
        ========================================*/

        // Busca os caracteres para o filtro
        $letters_filter = array();
        foreach ($blogs->get() as $blog) {
            $letter_code = ord($blog->title);

            if ($letter_code < 65 || $letter_code > 90) $letter_code = 48;

            if (!isset($letters_filter[$letter_code])) $letters_filter[$letter_code] = chr($letter_code);
        }

        // Filtra pela primeira letra
        if(!empty($filters['letter'])) {

            // Pega o Código ACII da letra
            $letter_filter = ord($filters['letter']);

            // Verifica se é para buscar todos que não são numéricos
            if ($letter_filter < 65 || $letter_filter > 90) {
                $blogs->whereRaw("ASCII(SUBSTRING(title, 1, 1)) < 65")->orWhereRaw("ASCII(SUBSTRING(title, 1, 1)) > 90");
            } else {
                $blogs->whereRaw("ASCII(SUBSTRING(title, 1, 1)) = ?", [$letter_filter]);

            }
        }
        //Orderna as letras por ordem ascendente
        array_multisort($letters_filter, SORT_ASC);

        /*=====  End of FILTRO DE LETRAS  ======*/

        //Ordena o resultado por páginação
        $blogs = $blogs->orderBy('created_at', 'desc')->paginate(10)
         ->withPath($request->url() . ($request->getQueryString() ? ('?' . $request->getQueryString()) : ''));

        // Monta array com as variáveis que serão enviadas para a view
        $view_parameters = array_merge(
            compact('blogs','themes', 'letters_filter'),
            [
                'seo_meta_robots' => SeoRobots::None,
                'seo_title' => \Config::get('custom_configuration.seo.blog.title'),
                'seo_description' => \Config::get('custom_configuration.seo.blog.description'),
                'seo_keywords' => \Config::get('custom_configuration.seo.blog.keywords'),
            ]
        );

        // Retorna o HTML
        return view('blog.search', $view_parameters);

    }

    /**
     * Event Detail Page
     * @param   String $seo_url
     * @return  \Illuminate\Http\Response
     */
    public function detail($seo_url)
    {
        // Busca o objeto do evento
        $news = Blog::where('seo_url', $seo_url)->first();

        if (empty($news)) {
            return redirect()->route('front.blog.index');
        }

        //Grava o ip do usuário que acessou o blog. Se o ip já estiver no banco de dados, não grava.
        if(empty($news->getSeenAttribute())) {

            // Retorna o ip real do usuário que acessou a notícia
            $local_ip = \App\Helpers::get_user_ip();
            $local_ip = preg_replace("/\D/", "", $local_ip);

            BlogView::create([
                'blog_id' => $news->id,
                'ip' => $local_ip
            ]);
        }

        // Monta array com as variáveis que serão enviadas para a view
        $view_parameters = array_merge(
            compact('news'),
            [
                'seo_title' => str_replace("[TITULOBLOG]", $news->title, \Config::get('custom_configuration.seo.blog.detail')),
                'seo_description' => $news->summary,
                'seo_keywords' => (!empty($news->theme) ? $news->theme->name : ''),
            ]
        );

        // Retorna a View
    	return view('blog.detail', $view_parameters);
    }

}


