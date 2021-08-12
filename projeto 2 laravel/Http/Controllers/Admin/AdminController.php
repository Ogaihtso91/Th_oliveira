<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\SiteConfiguration\SaveSiteConfigurationRequest;
use App\Repositories\Repository\AboutUs\GetAboutUsRepository;
use App\Services\Admin\SiteConfiguration\SiteConfigurationService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class AdminController extends Controller
{
    private const MESSAGE_ERROR_SUBMIT_CONFIGURATION = 'Não foi possível salvar as configurações do site. Tente novamente.';
    private const MESSAGE_ERROR_VIEW_CONFIGURATION = 'Não foi possível acessar as configurações do site.';
    private const ROUTE_ADMIN_CONFIGURATION = 'admin.configuration.';
    private const MESSAGE_ERROR_ABOUT_US = "Não foi possível salvar todos os dados da página sobre nós. Tente novamente.";

    private $siteConfigurationService, $getAboutUs;

    public function __construct()
    {
        $this->middleware(['role_or_permission:Super Admin|configuracoes-do-site.visualizacao'], ['only' => ['index', 'configuration']]);
        $this->middleware(['role_or_permission:Super Admin|configuracoes-do-site.edicao'], ['only' => ['submitconfiguration']]);
        $this->siteConfigurationService = new SiteConfigurationService();
        $this->getAboutUs = new GetAboutUsRepository();
    }

    /**
     * Admin Dashboard
     * @return RedirectResponse
     */
    public function index()
    {
        return redirect()->route('admin.configuration.index');
    }

    /**
     * Site Configuration Form
     * @param Request $request
     * @return Factory|View|RedirectResponse
     */
    public function configuration(Request $request)
    {
        try {
            $aboutUs = $this->getAboutUs->first();

            return view(self::ROUTE_ADMIN_CONFIGURATION . 'index', compact('aboutUs'));

        } catch (\Throwable $throwable) {
            create_error_log($throwable, $request);
        }

        return redirect()->back()->with(status_message(), self::MESSAGE_ERROR_VIEW_CONFIGURATION);
    }

    /**
     * @param SaveSiteConfigurationRequest $request
     * @return RedirectResponse
     */
    public function submitconfiguration(SaveSiteConfigurationRequest $request)
    {
        try {
            $updatedConfigurationFile = $this->siteConfigurationService->updateConfigurationFileData($request);

            if (empty($updatedConfigurationFile)) {
                return redirect()->back()->with(status_error(), error_message_uploading_image())->withInput();
            }

            $savedData = $this->siteConfigurationService->savePageDataAboutUs($request);

            if (empty($savedData)) {
                return redirect()->back()->with(status_error(), self::MESSAGE_ERROR_ABOUT_US)->withInput();
            }

            return redirect()->back()->with(status_message(), trans('adminlte.configuration.submit-success'));

        } catch (\Throwable $throwable) {
            create_error_log($throwable, $request);
        }

        return redirect()->back()->with(status_error(), self::MESSAGE_ERROR_SUBMIT_CONFIGURATION)->withInput();
    }
}
