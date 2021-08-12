<?php

namespace App\Http\Controllers;

use App\Award;
use App\ContentManager;
use App\Institution;
use App\Keyword;
use App\Repositories\Repository\SocialTecnologyWizard\SocialTecnologyWizardRepository;
use App\Services\SocialTecnologyWizard\SocialTecnologyWizardService;
use App\SocialTecnology;
use App\Theme;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class SocialTecnologyWizardController extends Controller
{
    protected $social_tecnology, $content_manager, $awards, $institution, $wizardService, $wizardRepository;

    public function __construct(SocialTecnology $social_tecnology, ContentManager $content_manager, Award $awards, Institution $institution)
    {
        $this->social_tecnology = $social_tecnology;
        $this->content_manager = $content_manager;
        $this->awards = $awards;
        $this->institution = $institution;
        $this->wizardService = new SocialTecnologyWizardService();
        $this->wizardRepository = new SocialTecnologyWizardRepository();
    }

    /**
     * show start form as wizard
     *
     * @param Request $request
     * @return Factory|View
     */
    public function start(Request $request)
    {
        /** Novo registro */
        $route_step = 0;
        $user_institutions = \Auth::guard()->user()->institutions()->where('status', 1)->get();
        $start = true;

        $institution_seo_url = $request->get('institution_seo_url');
        $registration = $request->get('registration');
        $award_id = $request->get('award_id');

        if (!empty($registration) && !empty($institution_seo_url)) {
            $institution = $this->institution->where('seo_url', $institution_seo_url)->first();
        } else {
            $institution = $user_institutions;
        }

        return view('socialtecnology.wizard.wizard', compact('route_step', 'start', 'institution', 'user_institutions', 'award_id', 'registration', 'institution_seo_url'));
    }

    /**
     * show create form as wizard
     *
     * @param Request $request
     * @return Factory|RedirectResponse|Redirector|View
     */
    public function create(Request $request)
    {
        /** variaveis da rota */
        $route_step = empty($request->steps) ? 1 : $request->steps;

        $registration = $request->registration;

        $award_id = $request->get('award_id');

        $awardProperties = $this->awards->find($award_id);

        /** === DADOS PARA VIEW === */

        /** buscamos a premiação com uma data de registro valida */
        $awards = $this->awards->query()->has('categoryAwards')
            ->where(function ($q) {
                $q->where('registrationsStartDate', '<=', Carbon::now()->format('yy-m-d'))
                    ->where('registrationsEndDate', '>=', Carbon::now()->format('yy-m-d'));
            })->orderBy('name')->get();

        /** buscando os temas */
        $themes = Theme::orderBy('name')->get();

        /** Busca as palavras-chave */
        $keywords = Keyword::orderBy('name')->get();

        $user_institutions = \Auth::guard()->user()->institutions()->where('status', 1)->get();

        $canCreateInstitution = empty($registration) && count($user_institutions) == 0 ? true : false;

        $institution_seo_url = $request->get('institution_seo_url');

        // Se não possuir registro, defini a instituição pelo SEO_URL na request.
        if (empty($registration) && !empty($institution_seo_url)) {
            $institution = $this->institution->where('seo_url', $institution_seo_url)->first();
        }

        // verificamos se já temos registro
        if (isset($registration)) {

            // Buscamos os dados do ContentManager e SocialTecnology
            $content = $this->content_manager->all()->where('registration', $registration)->first();

            $socialtecnology = $this->social_tecnology->all()->where('registration', $registration)->first();

            $institution = $socialtecnology->institution;
            // step navigation
            if ($route_step <= $content->wizard_step) {
                // TODO: organizar dados dos representantes na view!
                // buscamos os dados adicionais da tecnologia
                $representatives = $socialtecnology->users ?? array();
                // $partners = $tecnology->partners->all() ?? array();

                // retornamos a view com os dados adicionais
                return view('socialtecnology.wizard.wizard',
                    compact('route_step', 'awards', 'registration', 'institution', 'canCreateInstitution', 'socialtecnology', 'representatives', 'themes', 'keywords', 'user_institutions', 'award_id', 'awardProperties'));

            } else { // redirecionamos o usuário para a ultima etapa registrada
                return redirect(route('user.institution.socialtecnologies.create.steps', [
                    'registration' => $registration,
                    'institution_seo_url' => $institution->seo_url,
                    'steps' => $content->wizard_step,
                    'institution' => $institution
                ]))->withErrors('para seguir com cadastro por favos preencha os campos obrigatorios');
            }
        } else {
            /** Novo registro */
            return view('socialtecnology.wizard.wizard', compact('route_step', 'awards', 'institution', 'canCreateInstitution', 'user_institutions', 'award_id', 'awardProperties', 'registration'));
        }
    }


    public function first(Request $request)
    {
        return $this->wizardService->wizardControlStartStep($request, false);
    }

    /**
     * Registra ou edita uma tecnologia social
     *
     * @param Request $request
     * @return mixed
     */
    public function register(Request $request)
    {
        // sempre validamos os dados enviado pelo formulário
        $validated = $request->validate([
            'registration' => 'sometimes|required|exists:content_manager,registration'
        ]);

        //* SE o cadastro já foi iniciado
        //* ENTÂO continuamos de ondem paramos.
        if (isset($validated['registration'])) {
            // buscamos o registro do content manager
            $contentManager = $this->content_manager->all()->where('registration', $validated['registration'])->first();

            // Switch case
            // e separo as funções para cada etapa
            return $this->wizardService->wizardNavigationManager($contentManager, $request->step,
                function ($validStep, $previousStep) use ($request, $contentManager) {
                    switch ($validStep) {
                        case $this->wizardService::START_STEP:
                            # Não deve entrar mais aqui
                            die("Página inválida.");
                            break;
                        case $this->wizardService::FIRST_STEP:
                            return $this->wizardService->wizardControlFirstStep($request, $previousStep);
                            break;
                        case $this->wizardService::SECOND_STEP:
                            return $this->wizardService->wizardControlSecondStep($request, $previousStep);
                            break;
                        case $this->wizardService::THIRD_STEP:
                            return $this->wizardService->wizardControlThirdStep($request, $previousStep);
                            break;
                        case $this->wizardService::FOURTH_STEP:
                            return $this->wizardService->wizardControlFourthStep($request, $previousStep);
                            break;
                        case $this->wizardService::FIFTH_STEP:
                            return $this->wizardService->wizardControlFifthStep($request, $previousStep);
                            break;
                        case $this->wizardService::SIXTH_STEP:
                            return $this->wizardService->wizardControlSixthStep($request, $previousStep);
                            break;
                        case $this->wizardService::SEVENTH_STEP:
                            return $this->wizardService->wizardControlSeventhStep($request, $previousStep);
                            break;
                        case $this->wizardService::EIGHTH_STEP:
                            return $this->wizardService->wizardControlEighthStep($request, $previousStep);
                            break;
                        case $this->wizardService::NINTH_STEP:
                            return $this->wizardService->wizardControlNinthStep($request, $previousStep);
                            break;
                        case $this->wizardService::TENTH_STEP:
                            return $this->wizardService->wizardControlTenthStep($request, $previousStep);
                            break;
                        case $this->wizardService::REVIEW_STEP:
                            return $this->wizardService->wizardControlReviewStep($request, $previousStep);

                    }
                }
            );
        } else //* SENÂO criamos um novo cadastro
        {
            if ($request->step == 1) {
                return $this->wizardService->wizardControlFirstStep($request, 0);
            } else {
                return $this->wizardService->wizardControlStartStep($request, false);
            }
        }
    }

    public function edit(Request $request)
    {
        if (!empty($request->id)) {
            $socialtecnology = $this->social_tecnology->find($request->id);
        } else {
            return redirect()->route('user.institution.index')->with('error', 'Você não pode editar esta Tecnologia Social.');
        }

        $award_id = "";

        $awardProperties = $socialtecnology->award;

        /** === DADOS PARA VIEW === */

        $contentManager = $this->content_manager->where('registration', $socialtecnology->registration)->first();

        /** buscamos a premiação com uma data de registro valida */
        $query = $this->awards->query()->has('categoryAwards')
            ->where(function ($q) {
                $q->where('registrationsStartDate', '<=', Carbon::now()->format('yy-m-d'))
                    ->where('registrationsEndDate', '>=', Carbon::now()->format('yy-m-d'));
            });
        // Corrige erro de não listar Prêmio na tela de edição para prêmios
        // caso o prêmio já tenha "expirado"
        if ($awardProperties !== null) {
            $query->orWhere('id', '=', $awardProperties->id);
        }
        $awards = $query->orderBy('name')->get();

        /** buscando os temas */
        $themes = Theme::orderBy('name')->get();

        /** Busca as palavras-chave */
        $keywords = Keyword::orderBy('name')->get();

        /** buscamos informações da instituição */
        $institution = $socialtecnology->institution;

        $representatives = $socialtecnology->users;

        $partners = $socialtecnology->partners;

        return view('socialtecnology.edit',
            compact('awards', 'institution', 'socialtecnology', 'representatives', 'partners', 'themes', 'keywords', 'contentManager', 'award_id', 'awardProperties'));
    }

    public function update(Request $request)
    {
        return $this->wizardService->wizardControlReviewStep($request, false, true);
    }

    /**
     * TODO: fazer a exclusão de cadastros incompletos
     *  tanto a instância do content_manager quanto a
     *  da tecnologia_social
     */

    /**
     * Delete um cadastro do ContentManager
     *
     * @param Request $request
     * @return RedirectResponse|Redirector
     */
    public function deleteIncomplete(Request $request)
    {

        if (empty($request->registration)) {
            return redirect(route('user.institution.show', ['seo_url' => $request->institution_seo_url]))
                ->withErrors('Não foi possivel excluir a inscrição.');
        }

        // find content manager
        ContentManager::query()->where('registration', $request->registration)
            ->where('status', ContentManager::STATUS_PENDING)
            ->delete();

        SocialTecnology::query()->where('registration', $request->registration)
            ->where('status', ContentManager::STATUS_PENDING)
            ->delete();

        return redirect(route('user.institution.show', ['seo_url' => $request->institution_seo_url]))
            ->with('message', 'Inscrição excluida com sucesso!');

    }
}
