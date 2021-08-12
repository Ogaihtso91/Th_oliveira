<?php

namespace App\Http\Controllers\Admin;

use App\Award;
use App\CategoryAward;
use App\CategoryAwardEvaluationStep;
use App\CustomStepFields;
use App\CustomStepForm;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SocialTecnology;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\View;

class AjaxController extends Controller
{
    protected $awards, $categoryAwards, $socialTecnologies, $evaluationSteps;

    public function __construct(Award $awards, CategoryAward $categoryAwards, CategoryAwardEvaluationStep $evaluationSteps)
    {
        $this->awards = $awards;
        $this->categoryAwards = $categoryAwards;
        $this->evaluationSteps = $evaluationSteps;
    }

    /**
     * @method GET
     * @param Illuminate\Http\Request $categoryAwardId
     * @return Illuminate\Http\Response::JSON
     */
    public function getEvaluationSteps(Request $request)
    {
        try {
            $evaluationSteps = $this->categoryAwards->find($request->categoryAwardId)->evaluationSteps;

            $result = array();

            foreach ($evaluationSteps as $step) {
                $aux_array = [
                    'id' => $step->id,
                    'name' => $step->name,
                    'evaluationType' => $step->evaluationType,
                    'enableTwoEvaluators' => $step->enableTwoEvaluators,
                    'previousEvaluationStep_id' => $step->previousEvaluationStep_id,
                    'awardStatusForApprovedSocialTecnology' => $step->awardStatusForApprovedSocialTecnology,
                ];

                $result[] = $aux_array;

                unset($aux_array);
            }

            return response()->json($result);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'AjaxExceptionMessage - '.$e->getMessage()]);
        }
    }

    /**
     * @method GET
     * @param Illuminate\Http\Request $request
     * @return View
     */
    public function getRenderedInscription(Request $request)
    {
        if (!empty($request->award_id)) {
            $recentAward = $this->awards->find($request->award_id);
        } else {
            // ultima premiação cadastrada
            $recentAward = $this->awards->orderBy('registrationsStartDate','desc')->first();
        }

        // trazer informações da view da table.
        $socialTecnologies = $recentAward->getRegisteredSocialTecnologies();

        $data = [
            'id' => $recentAward->id,
            'inscriptions' => [
                'complete' => [
                    'list' => $recentAward->total_social_tecnologies,
                    'primaryThemes' => $recentAward->count_social_tecnologies_primary_themes,
                    'secondaryThemes' => $recentAward->count_social_tecnologies_secondary_themes,
                    'category' => $recentAward->getCountSocialTecnologiesContentManagerGroupBy(['groupByCategaryAward' => 1, 'status' => 1]),
                    'state' => $recentAward->getCountSocialTecnologiesContentManagerGroupBy(['UF' => 1, 'status' => 1]),
                ],
                'incomplete' => [
                    'list' => $recentAward->total_incomplete_subscriptions,
                    'primaryThemes' => $recentAward->getCountSocialTecnologiesContentManagerGroupBy(['primaryTheme' => 1, 'status' => 0]),
                    'secondaryThemes' => $recentAward->getCountSocialTecnologiesContentManagerGroupBy(['secondaryTheme' => 1, 'status' => 0]),
                    'category' => $recentAward->getCountSocialTecnologiesContentManagerGroupBy(['groupByCategaryAward' => 1, 'status' => 0]),
                    'state' => $recentAward->getCountSocialTecnologiesContentManagerGroupBy(['UF' => 1, 'status' => 0]),
                ],
            ],
        ];

        // renderizamos a view e seus parametros
        $view = view('admin.administration._sections._inscriptions', compact('socialTecnologies', 'data', 'challengeCategories'));

        // extraimos da view as secções
        $viewSections = $view->renderSections();

        // retornamos objeto json com a view renderizada e suas sessões
        return response()->json([
            'view' => $view->render(),
            'sections' => $viewSections
        ]);
    }

    /**
     * @param Illuminate\Http\Request $startDate
     * @param Illuminate\Http\Request $endDate
     * @param Illuminate\Http\Request $status
     * @return Illuminate\Http\Response::JSON
     */
    public function getInscriptionDaily(Request $request) {
        // return getSocialTecnologiesContentManager
        $recentAward = $this->awards->orderBy('registrationsStartDate','desc')->first();
        // $recentAward = $this->awards->find(2);

        $result = $recentAward->getSocialTecnologiesContentManager([
            'status' => $request->status,
            'startDate' => $request->startDate,
            'endDate' => $request->endDate,
        ]);

        return response()->json($result);
    }

    /**
     * @method GET
     * @param Illuminate\Http\Resquest $evaluationStep
     * @return Illuminate\Http\Response::JSON
     */
    public function getRenderedCertificationStep(Request $request)
    {
        $evaluationStep = $this->evaluationSteps->find($request->evaluationStep);

        $socialTecnologies = $this->categoryAwards->find($evaluationStep->category_award_id)
            ->socialTecnologies()
            ->whereHas('manager', function ($query) {
                $query->where('status', 1);
            })->get();

        $data = [
            'evaluationStep_id' => $evaluationStep->id,
            'validate' => [
                'complete' => $evaluationStep->queryToDashboard(['evaluated' => 1,'validated' => 1])->count(),
                'incomplete' => $evaluationStep->queryToDashboard(['evaluated' => 1,'validated' => 0])->count(),
            ],
            'evaluation' => [
                'complete' => $evaluationStep->queryToDashboard(['evaluated' => 1])->count(),
                'incomplete' => $evaluationStep->queryToDashboard(['evaluated' => 0])->count(),
            ],
        ];

        $view = view('admin.administration._sections._certification_step', compact('socialTecnologies', 'evaluationStep', 'data'));

        $sections = $view->renderSections();

        return response()->json([
            'view' => $view->render(),
            'sections' => $sections
        ]);
    }

    /**
     * @method GET
     * @param Illuminate\Http\Request $evaluationStep
     * @return View
     */
    public function getRenderedFinalistStep(Request $request)
    {
        $evaluationStep = $this->evaluationSteps->find($request->evaluationStep);

        $previousEvaluationStep = $evaluationStep->previousEvaluationStep() ?? null;

        $categoryAward = $evaluationStep->categoryAward;

        // buscamos a lista de tecnologias que estão certificadas
        // e a carregamos na view.
        $socialTecnologies = empty($previousEvaluationStep) ? $categoryAward->socialTecnologies()->where('status', 1)->get() : $previousEvaluationStep->approved_social_tecnolgies;

        if ($categoryAward->isCertificationType) {
            $evaluators = $evaluationStep->getEvaluators()->count();
        } else {
            $evaluators = $evaluationStep->enableTwoEvaluators ? '2' : '1';
        }

        // total de avaliadores cadastrado nessa etapa
        // $evaluators = empty($previousEvaluationStep) ? $evaluationStep->getEvaluators() : $previousEvaluationStep->getEvaluators();

        // acumuladores | contadores
        $completeCount = 0;
        $incompleteCount = 0;

        foreach ($socialTecnologies as $tecnology) {
            // para cata TS que tenha o numero de avaliações igual ao
            // numero de avaliadores incrementamos o contador completo
            $evaluationsCount = $tecnology->evaluations()->where('evaluationStep_id', $evaluationStep->id)->count();

            if ($evaluationsCount == $evaluators && $evaluators != 0) {
                $completeCount++;
            } else {
                $incompleteCount++;
            }
        }

        //TODO: criar contador de avaliadores dentro de evaluationStep model

        $data = [
            'evaluationStep_id' => $evaluationStep->id,
            'evaluation' => [
                'complete' => $completeCount,
                'incomplete' => $incompleteCount,
            ],
            'evaluators' => $evaluators, //! see this result on log
        ];

        $view = view('admin.administration._sections._finalization_step', compact('evaluationStep', 'previousEvaluationStep', 'categoryAward', 'socialTecnologies', 'data'));

        $sections = $view->renderSections();

        return response()->json([
            'view' => $view->render(),
            'sections' => $sections
        ]);
    }

    /**
     * @method GET
     * @param Illuminate\Http\Request $evaluationSteps
     * @return View
     */
    public function getRenderedWinnerStep(Request $request)
    {
        $evaluationStep = $this->evaluationSteps->find($request->evaluationSteps);

        $previousEvaluationStep = $evaluationStep->previousEvaluationStep() ?? null;

        $categoryAward = $evaluationStep->categoryAward;

        if ($categoryAward->isCertificationType) {
            $socialTecnologies = $previousEvaluationStep->approved_social_tecnolgies ?? [];
            $approvedTecnology = $evaluationStep->approved_social_tecnolgies ?? [];
            $evaluators = $evaluationStep->getEvaluators()->count();
        } else {
            $socialTecnologies = empty($previousEvaluationStep) ? $categoryAward->socialTecnologies : $previousEvaluationStep->approved_social_tecnolgies;
            $approvedTecnology = $categoryAward->socialTecnologies()->where('challenge_award_status', 'w')->get();
            $evaluators = $evaluationStep->enableTwoEvaluators ? '2' : '1';
        }

        // acumuladores | contadores
        $completeCount = 0;
        $incompleteCount = 0;

        foreach ($socialTecnologies as $tecnology) {
            // para cata TS que tenha o numero de avaliações igual ao
            // numero de avaliadores incrementamos o contador completo
            $evaluationsCount = $tecnology->evaluations()->where('evaluationStep_id', $evaluationStep->id)->count();

            if ($evaluationsCount == $evaluators) {
                $completeCount++;
            } else {
                $incompleteCount++;
            }
        }

        $data = [
            'evaluationStep_id' => $evaluationStep->id,
            'evaluation' => [
                'complete' => $completeCount,
                'incomplete' => $incompleteCount,
            ],
            'status' => $evaluationStep->status,
        ];

        $view = view('admin.administration._sections._winners_step', compact('evaluationStep', 'previousEvaluationStep', 'categoryAward', 'socialTecnologies', 'data', 'approvedTecnology'));

        $sections = $view->renderSections();

        return response()->json([
            'view' => $view->render(),
            'sections' => $sections
        ]);
    }

    public function enableOrDisableCustomStepForm (Request $request)
    {
        $customForm = CustomStepForm::find($request->stepForm_id);

        if (empty($customForm)) {
            return response()->json(['Formulário não encontrado'], 502);
        }

        $customForm->update([
            'status' => (empty($customForm->status) ? '1' : '0')
        ]);

        return response()->json($customForm);
    }

    public function enableOrDisableCustomStepField (Request $request)
    {
        $customField = CustomStepFields::find($request->stepField_id);

        if (empty($customField)) {
            return response()->json(['Campo customizado não encontrado'], 502);
        }

        $customField->update([
            'status' => (empty($customField->status) ? '1' : '0')
        ]);

        return response()->json($customField);
    }

    public function publishAprovedSocialTecnologiesListByStep (Request $request)
    {
        $evaluationStep = $this->evaluationSteps->find($request->evaluationStep_id);

        if(is_null($evaluationStep->approvedListPublished_flag) && is_null($evaluationStep->approvedList_published_at) ){
            $evaluationStep->update([
                'approvedListPublished_flag' => 1,
                'approvedList_published_at' => Carbon::now(),
            ]);

            return response()->json($evaluationStep);
        }

        return response()->json(['Lista já publicadas anteriormente'], 406);
    }
}
