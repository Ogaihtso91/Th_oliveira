<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Blog;
use App\Event;
use App\Institution;
use App\Ods;
use App\SocialTecnology;
use App\Theme;
use App\User;

class GenerateSitemap extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the sitemap.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Cria variável que receberá o índice dos sitemaps
        $sitemaps = [];

        /*=========== TECNOLOGIAS SOCIAIS =========*/
        $items = SocialTecnology::orderByDesc('updated_at')->get();

        // Cria o XML
        $view_xml = view('sitemap.detailpages', [
            'items' => $items,
            'routename' => 'front.socialtecnology.detail',
        ]);

        // Salva o arquivo XML
        Storage::put('sitemap/socialtecnologies.xml', $view_xml->render());

        // Adiciona ao índice de Sitemaps
        $sitemaps[] = Storage::url('sitemap/socialtecnologies.xml');

        /*================= BLOG ==================*/
        $items = Blog::orderByDesc('updated_at')->get();

        // Cria o XML
        $view_xml = view('sitemap.detailpages', [
            'items' => $items,
            'routename' => 'front.blog.detail',
        ]);

        // Salva o arquivo XML
        Storage::put('sitemap/blogs.xml', $view_xml->render());

        // Adiciona ao índice de Sitemaps
        $sitemaps[] = Storage::url('sitemap/blogs.xml');

        /*================ EVENTOS ================*/
        $items = Event::orderByDesc('updated_at')->get();

        // Cria o XML
        $view_xml = view('sitemap.detailpages', [
            'items' => $items,
            'routename' => 'front.event.detail',
        ]);

        // Salva o arquivo XML
        Storage::put('sitemap/events.xml', $view_xml->render());

        // Adiciona ao índice de Sitemaps
        $sitemaps[] = Storage::url('sitemap/events.xml');

        /*============= INSTITUIÇÕES ==============*/
        $items = Institution::orderByDesc('updated_at')->get();

        // Cria o XML
        $view_xml = view('sitemap.detailpages', [
            'items' => $items,
            'routename' => 'front.institution.profile.public',
        ]);

        // Salva o arquivo XML
        Storage::put('sitemap/institutions.xml', $view_xml->render());

        // Adiciona ao índice de Sitemaps
        $sitemaps[] = Storage::url('sitemap/institutions.xml');

        /*=============== USUÁRIOS ================*/
        $items = User::orderByDesc('updated_at')->get();

        // Cria o XML
        $view_xml = view('sitemap.detailpages', [
            'items' => $items,
            'routename' => 'front.user.profile.public',
        ]);

        // Salva o arquivo XML
        Storage::put('sitemap/users.xml', $view_xml->render());

        // Adiciona ao índice de Sitemaps
        $sitemaps[] = Storage::url('sitemap/users.xml');

        /*================= TEMAS =================*/
        $items = Theme::orderByDesc('updated_at')->get();

        // Cria o XML
        $view_xml = view('sitemap.detailpages', [
            'items' => $items,
            'routename' => 'front.socialtecnology.theme',
            'changefreq' => 'monthly',
        ]);

        // Salva o arquivo XML
        Storage::put('sitemap/themes.xml', $view_xml->render());

        // Adiciona ao índice de Sitemaps
        $sitemaps[] = Storage::url('sitemap/themes.xml');

        /*=================== ODS =================*/
        $items = Ods::orderByDesc('updated_at')->get();

        // Cria o XML
        $view_xml = view('sitemap.detailpages', [
            'items' => $items,
            'routename' => 'front.socialtecnology.ods',
            'changefreq' => 'monthly',
        ]);

        // Salva o arquivo XML
        Storage::put('sitemap/ods.xml', $view_xml->render());

        // Adiciona ao índice de Sitemaps
        $sitemaps[] = Storage::url('sitemap/ods.xml');

        /*============= SITEMAP INDEX =============*/
        $view_xml = view('sitemap.index', [
            'sitemaps' => $sitemaps,
            'lastmod' => Carbon::now()->toAtomString(),
        ]);

        // Salva o arquivo do Sitemap
        Storage::disk('root')->put('sitemap.xml', $view_xml);
    }
}