<?php

namespace App\Http\Controllers\Admin;

use App\Award;
use App\CategoryAward;
use App\CategoryAwardEvaluationStep as EvaluationStep;
use App\Enums\SocialTecnologiesAwardStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\SocialTecnologiesEvaluationStepRequest;
use App\Repositories\Repository\Award\GetAwardRepository;
use App\Repositories\Repository\CategoryAwardEvaluationStep\GetCategoryAwardEvaluationStepRepository;
use App\Repositories\Repository\CategoryAwardEvaluationStep\GetCategoryAwardEvaluationValidateRepository;
use App\Repositories\Repository\CategoryAward\GetCategoryAwardRepository;
use App\Repositories\Repository\SocialTecnology\GetSocialTecnologyRepository;
use App\Services\EvaluationStep\EvaluationStepService;
use App\SocialTecnology;
use App\SocialTecnologyCategoryAwardEvaluationCriteria as TSEvaluationCriteria;
use App\SocialTecnologyEvaluatorStepEvaluation as TSEvaluatorStepEvaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class SocialTecnologiesEvaluationStepController extends Controller
{

    private $getAward, $getCategoryAward, $getEvaluationStep, $getEvaluationStepValidate;
    private $evaluationStepService;

    public function __construct()
    {
        $this->getAward = new GetAwardRepository();
        $this->getCategoryAward = new GetCategoryAwardRepository();
        $this->getEvaluationStep = new GetCategoryAwardEvaluationStepRepository();
        $this->getEvaluationStepValidate = new GetCategoryAwardEvaluationValidateRepository();
        $this->evaluationStepService = new EvaluationStepService();
    }

    /**
     * registra a avalição da etapa.
     *
     * @param Illuminate\Http\Request
     * @return Illuminate\Http\Response view
     */
    public function evaluation_step_register(SocialTecnologiesEvaluationStepRequest $request, Award $award, CategoryAward $category_award, EvaluationStep $evaluation_step, SocialTecnology $socialtecnology )
    {

        try {
            # Modo de edição permite atualizar as notas de avaliação.
            $last_criteria_id = $request->get('last_criteria_evaluation_id');

            // válidamos os dados do formulário
            $validated = $request->validated();

            // organizamdo os dados extra para registro
            $extra_data = [
                "social_tecnology_id" => $socialtecnology->id,
                "evaluation_steps_id" => $evaluation_step->id,
                "bonusValue" => $request->get('bonus_value'),
            ];

            // unificamos os dados para armazenamento
            $data = array_merge($extra_data, $validated);

            # Adiciona edição da justificativa quando for certificação
            if ($evaluation_step->evaluationType == 1) {
                $data['justification'] = $request->get('notion');
            }

            // dd($evaluation_step);
            // percorremos o array de score e ajustamos os dados de acordo
            foreach($data['criterion_scores'] as $key => $value) {
                $data['evaluationScore'] = $evaluation_step->evaluationType == 2 ? $value : null;
                $data['evaluationBoolean'] = $evaluation_step->evaluationType == 1 ? $value : null;
                $data['evaluationCriterion_id'] = $key;

                # Caso esteja no modo de edição, apenas atualiza a nota.
                if (!empty($last_criteria_id)) {
                    TSEvaluationCriteria::find($last_criteria_id)->update($data);
                } else {
                    // salvamos cada nota de criterio.
                    TSEvaluationCriteria::store($data);
                }
            }

            // Issue #4901 - Procura por uma avaliação anterior (caso tenha sido feita, mesmo por outra pessoa.)
            $object = TSEvaluatorStepEvaluation::getBySocialTechAndEvaluationStep($data['social_tecnology_id'], $data['evaluation_steps_id']);
            if($object === null) {
                TSEvaluatorStepEvaluation::store($data);
            }else{
                $object->update($data);
            }

            // // se essa etapa não for a primeira
            // // varificamos se a validação e de dupla ou individual
            // // então de acordo mudamos o estado da premiaçõa
            // // para o especificado no cadastro da etapa.
            // if($evaluation_step->previousEvaluationStep_id) {
            //     $this->evaluationStepService->changeSocialTecnologyAwardState(
            //         $socialtecnology,
            //         $evaluation_step->enableTwoEvaluators,
            //         $this->getEvaluationStep->getSocialTecnologyById($evaluation_step, $socialtecnology->id),
            //         $evaluation_step->awardStatusForApprovedSocialTecnology
            //     );

            //     // mudando o valor de validação
            //     $evaluationAppraisal = $this->getEvaluationStepValidate
            //         ->getEvaluationStepForAppraisal($evaluation_step, $socialtecnology);

            //     $evaluationAppraisal->update([
            //         'validated' => '1'
            //     ]);
            // }

            /** [GG] redirecionamos para a view anterior e trazemos a msg de feedback */
            return redirect(route('admin.evaluations.tecnologies.list', [
                'award' => $award->id,
                'category_award' => $category_award->id,
                'evaluation_step' => $evaluation_step->id,
            ]))
                ->with('message', 'Avaliação registrada com sucesso!');
        }
        catch (\Throwable $throwable) {
            create_error_log($throwable, $request, 'Erro ao tentar salvar a sua avaliação.');
        }

        return redirect()
            ->back()
            ->with('error', 'Não foi possível salvar a sua avaliação. Tente novamente ou entre em contato com o administrador do site.');
    }

    /**
     *
     */
    public function evaluation_tecnology_unassigned_register(Request $request, Award $award, CategoryAward $category_award, EvaluationStep $evaluation_step, SocialTecnology $socialtecnology )
    {

        try {

            # Modo de edição permite atualizar as notas de avaliação.
            $last_criteria_id = $request->get('last_criteria_evaluation_id');

            // validar os dados do formulário.
            $validated = $request->validate([
                'criterion_scores.*' => 'required',
                'bonus_value' => 'sometimes|required|integer',
                'bonus_notion' => 'sometimes|required|string'
            ]);

            // organizamdo os dados para fazer o registro.
            $extra_data = [
                "social_tecnology_id" => $socialtecnology->id,
                "evaluation_steps_id" => $evaluation_step->id,
            ];

            // fazemos o merge dos dados.
            $data = array_merge($extra_data, $validated);

            // percorremos o array de score e ajustamos os dados de acordo
            foreach($data['criterion_scores'] as $key => $value) {
                $data['evaluationScore'] = $evaluation_step->evaluationType == 2 ? $value : null;
                $data['evaluationBoolean'] = $evaluation_step->evaluationType == 1 ? $value : null;
                $data['evaluationCriterion_id'] = $key;


                # Caso esteja no modo de edição, apenas atualiza a nota.
                if (!empty($last_criteria_id)) {
                    TSEvaluationCriteria::find($last_criteria_id)->update($data);
                } else {
                    // salvamos cada nota de criterio.
                    TSEvaluationCriteria::store($data);
                }
            }

            // salvamos os valores e a justificativa da bonificação.
            // Issue #4901 - Procura por uma avaliação anterior (caso tenha sido feita, mesmo por outra pessoa.)
            $object = TSEvaluatorStepEvaluation::getBySocialTechAndEvaluationStep($data['social_tecnology_id'], $data['evaluation_steps_id']);
            if($object === null) {
                TSEvaluatorStepEvaluation::store($data);
            }else{
                $object->update($data);
            }

            /** [GG] redirecionamos para a view anterior e trazemos a msg de feedback */
            return redirect(route('admin.evaluations.tecnologies.list', [
                'award' => $award->id,
                'category_award' => $category_award->id,
                'evaluation_step' => $evaluation_step->id,
            ]))
                ->with('message', 'Avaliação registrada com sucesso!');
        }
        catch (\Throwable $throwable) {
            create_error_log($throwable, $request, "Erro ao fazer o post das informações.");
        }

        return redirect()
            ->back()
            ->with('error', 'error')
            ->withInput();
    }

    /**
     * lista de premiações validas
     *
     * @return Illuminate\Http\Response $view
     */
    public function evaluation_award_index()
    {
        try {
            // buscamos todas as premiações
            // com categoria e etapas de avaliaçõa validas.
            $valids_awards = $this->getAward->validAwardsForEvaluations();

            // arrays com as informações organizadas
            $list_items = Collection::make();

            /** interamos as awards e formatamos os dados */
            $valids_awards->each(
                function($item, $key) use ($list_items) {
                    $arr_aux['id'] = $item->id;
                    $arr_aux['name'] = $item->name;
                    $arr_aux['desc'] = [
                        'label' => 'Total de categorias',
                        'content' => count($item->categoryAwards),
                    ];
                    $arr_aux['route_name'] = 'admin.evaluations.categories.list';
                    $arr_aux['route_params'] = [
                        'award' => $item->id,
                    ];

                    /** adicionamos os dados no array principal */
                    $list_items->push($arr_aux);

                    /** resetamos os array auxiliar */
                    unset($arr_aux);
                }
            );

            /**
             * parametros do breadcrumb
            */
            $breadcrumb_params = Collection::make([
                'award' => [
                    'active' => true,
                    'label' => __('front.evaluation_step.breadcrumb.award'),
                ],
            ]);

            return view('admin.social-tecnology.evaluation_step.evaluation_step_navigation',
                compact('list_items', 'breadcrumb_params')
            );
        }
        catch (\Throwable $throwable) {
            create_error_log($throwable, 'Erro ao acessar a lista de premiação');
        }

        return redirect()
            ->back()
            ->with('error', 'Não foi possível acessar a lista de prêmios. Tente novamente ou entre em contato com o administrador do site.');
    }

    /**
     * lista de categorias da premiação validas.
     *
     * @param Award $award
     * @return Illuminate\Http\Response view
     */
    public function evaluation_categories_index(Award $award)
    {
        try {
            $categories = $this->getCategoryAward->validsCategoriesAwardsForEvaluations($award);

            // receptáculo para dados padronizados
            $list_items = Collection::make();

            // padronizando dados para a view
            $categories->each(
                function($item, $key) use ($list_items, $award) {
                    $arr_aux['id'] = $item->id;
                    $arr_aux['name'] = $item->name;
                    $arr_aux['desc'] = [
                        'label' => 'Quantidades de etapas',
                        'content' => count($item->evaluationSteps),
                    ];
                    $arr_aux['route_name'] = 'admin.evaluations.steps.list';
                    $arr_aux['route_params'] = [
                        'award' => $award->id,
                        'category' => $item->id,
                    ];

                    // adicionamos os dados no receptáculo
                    $list_items->push($arr_aux);

                    // limpamos o array auxiliar
                    unset($arr_aux);
                }
            );

            // ajustamos dados do breadcrumb
            // sujeito a refatoração
            $breadcrumb_params = Collection::make([
                'award' => [
                    'active' => false,
                    'label' => __('front.evaluation_step.breadcrumb.award'),
                    'link' => [
                        'name' => 'admin.evaluations.awards.list',
                        'params' => []
                    ],
                ],
                'category_awards' => [
                    'active' => true,
                    'label' => __('front.evaluation_step.breadcrumb.category_awards'),
                ],
            ]);

            return view('admin.social-tecnology.evaluation_step.evaluation_step_navigation',
                compact('list_items', 'breadcrumb_params')
            );
        }
        catch (\Throwable $throwable) {
            create_error_log($throwable, $award, "Erro ao acessar a lista de categorias.");
        }

        return redirect()
            ->back()
            ->with('error', 'Não foi possível visualizar a lista de categorias. Tente novamento ou entre em contato com o administrador do site.');
    }

    /**
     * lista de etapas de avaliação validas.
     *
     * @param Award $award
     * @param CategoryAward $category_award
     *
     * @return Illuminate\Http\Response view
     */
    public function evaluation_evaluations_index(Award $award, CategoryAward $category_award)
    {
        try {
            $evaluation_step = $this->getEvaluationStep->validsStepsForEvaluations($category_award);

            // receptáculo para dados padronizados
            $list_items = Collection::make();

            // padronizando dados para view
            $evaluation_step->each(
                function($item, $key)
                use ($list_items, $award, $category_award) {
                    $arr_aux['id'] = $item->id;
                    $arr_aux['name'] = $item->name;
                    $arr_aux['desc'] = [
                        'label' => 'Tipo da avaliação',
                        'content' => $item->evaluationType == 2 ? 'por nota' : 'sim ou não',
                    ];
                    $arr_aux['route_name'] = 'admin.evaluations.tecnologies.list';
                    $arr_aux['route_params'] = [
                        'award' => $award->id,
                        'category' => $category_award->id,
                        'evaluation_step' => $item->id
                    ];

                    /** adicionamos os dados no array principal */
                    $list_items->push($arr_aux);

                    /** resetamos os array auxiliar */
                    unset($arr_aux);
                }
            );

            // criando parametros para breadcrumb
            $breadcrumb_params = Collection::make([
                'award' => [
                    'active' => false,
                    'label' => __('front.evaluation_step.breadcrumb.award'),
                    'link' => [
                        'name' => 'admin.evaluations.awards.list',
                        'params' => []
                    ],
                ],
                'category_awards' => [
                    'active' => false,
                    'label' => __('front.evaluation_step.breadcrumb.category_awards'),
                    'link' => [
                        'name' => 'admin.evaluations.categories.list',
                        'params' => [
                            'award' => $award->id
                        ]
                    ],
                ],
                'evaluation_steps' => [
                    'active' => true,
                    'label' => __('front.evaluation_step.breadcrumb.evaluation_steps'),
                ],
            ]);

            return view('admin.social-tecnology.evaluation_step.evaluation_step_navigation',
                compact('list_items', 'breadcrumb_params')
            );
        }
        catch (\Throwable $throwable) {
            create_error_log($throwable, "Erro ao acessar a lista de etapas para avaliação.");
        }
    }

    /**
     * lista de tecnologias válidas para avaliação.
     *
     * @param Award $award
     * @param CategoryAward $catagory_award
     * @param EvaluationStep $evaluation_step
     * @return Illuminate\Http\Response view
     */
    public function evaluation_tecnology_index(Award $award, CategoryAward $category_award, EvaluationStep $evaluation_step)
    {

        try {
            // busca tecnologias atribuidas a mim.
            $tecnologies = $this->getEvaluationStep->socialTecnologyForEvaluation($evaluation_step);

            // busca tecnologias não atribuidas a mim.
            $tecnologiesNotAssigned = $this->getEvaluationStep->socialTecnologyNotAssigned($evaluation_step);

            // passamos os dados de rota para compilar os dados
            $assigned_route_name = 'admin.evaluations.tecnologies.form';

            // organiza os dados das tecnologia atribuidas a mim.
            $list_items = $this->evaluationStepService->adjustData(
                $tecnologies,
                $award,
                $category_award,
                $evaluation_step,
                $assigned_route_name
            );

            // passamos os dados de rota não atribuida para compilar com os dados
            $unassigned_route_name = 'admin.evaluation.tecnology.unassigned.form';

            // organiza os dados das tecnologias não atribuidas a mim.
            $list_not_assigned = $this->evaluationStepService->adjustData(
                $tecnologiesNotAssigned,
                $award,
                $category_award,
                $evaluation_step,
                $unassigned_route_name
            );

            // parametros para o breadcrumb
            // pode ser refatorado futuramente
            $breadcrumb_params = Collection::make([
                'award' => [
                    'active' => false,
                    'label' => __('front.evaluation_step.breadcrumb.award'),
                    'link' => [
                        'name' => 'admin.evaluations.awards.list',
                        'params' => []
                    ],
                ],
                'category_awards' => [
                    'active' => false,
                    'label' => __('front.evaluation_step.breadcrumb.category_awards'),
                    'link' => [
                        'name' => 'admin.evaluations.categories.list',
                        'params' => [
                            'award' => $award->id
                        ]
                    ],
                ],
                'evaluation_step' => [
                    'active' => false,
                    'label' => __('front.evaluation_step.breadcrumb.evaluation_steps'),
                    'link' => [
                        'name' => 'admin.evaluations.steps.list',
                        'params' => [
                            'award' => $award->id,
                            'category_award' => $category_award->id,
                        ]
                    ],
                ],
                'socialtecnologies' => [
                    'active' => true,
                    'label' => __('front.evaluation_step.breadcrumb.socialtecnologies'),
                ],
            ]);

            return view('admin.social-tecnology.evaluation_step.tecnology_step',
                compact('list_items', 'list_not_assigned', 'breadcrumb_params', 'evaluation_step', 'category_award', 'award')
            );
        }
        catch(\Throwable $throwable) {
            create_error_log($throwable, $tecnologies, "Erro ao acessar a lista de tecnologias.");
        }

        return redirect()
            ->back()
            ->with('error', 'Não foi possível visualizar as tecnologias. Tente novamente ou entre em contato com o administrador do site.');
    }

    /**
     * formulário para avaliação da tecnologia.
     *
     * @param Award $award
     * @param CategoryAward $category_award
     * @param EvaluationStep $evaluation_step
     * @param SocialTecnology $socialtecnology
     * @return Illuminate\Http\Response view
     */
    public function evaluation_tecnology_form(Award $award, CategoryAward $category_award, EvaluationStep $evaluation_step, SocialTecnology $socialtecnology, Request $request)
    {
        $edit = $request->editar;
        $last_criteria_by_note = '';

        if (!empty($edit)) {
            $last_criteria_by_note = TSEvaluationCriteria::where('socialTecnology_id', $socialtecnology->id)
                ->where('evaluator_id', Auth::guard('admin')->user()->id)
                ->whereHas('categoryAwardEvaluationCriterion', function($query) use ($evaluation_step) {
                    $query->where('evaluationStep_id',$evaluation_step->id);
                })->first();
        }

        try {
            $evaluation_criterion = $evaluation_step->evaluationCriteria;

            return view('admin.social-tecnology.evaluation_step.evaluation_step_form',
                compact([
                    'award',
                    'category_award',
                    'evaluation_step',
                    'evaluation_criterion',
                    'socialtecnology',
                    'last_criteria_by_note'
                ])
            );
        }
        catch (\Throwable $throwable) {
            create_error_log($throwable, $socialtecnology, "Erro ao acessar o detalhe da tecnologia com ID: $socialtecnology->id");
        }

        return redirect()
            ->back()
            ->with('error', 'Não foi possível visualizar a tecnologia. Tente novamente ou entre em contato com o administrador do site.');
    }

    /**
     * formulário para avaliação de tecnologias
     * não atribuidas ao usuário logado.
     *
     * @param Award $award
     * @param CategoryAward $category_award
     * @param EvaluationStep $evaluation_step
     * @param SocialTecnology $socialtecnology
     * @return mixed
     */
    public function evaluation_tecnology_unassigned_form(Award $award, CategoryAward $category_award, EvaluationStep $evaluation_step, SocialTecnology $socialtecnology, Request $request)
    {

        $edit = $request->editar;
        $last_criteria_by_note = '';

        if (!empty($edit) && $edit == 'editar') {
            $last_criteria_by_note = TSEvaluationCriteria::where('socialTecnology_id', $socialtecnology->id)
                ->where('evaluator_id', Auth::guard('admin')->user()->id)
                ->whereHas('categoryAwardEvaluationCriterion', function($query) use ($evaluation_step) {
                    $query->where('evaluationStep_id',$evaluation_step->id);
                })->first();
        }

        try {
            $evaluation_criterion = $evaluation_step->evaluationCriteria;

            return view('admin.social-tecnology.evaluation_step.evaluation_unassigned_ts',
                compact([
                    'award',
                    'category_award',
                    'evaluation_step',
                    'evaluation_criterion',
                    'socialtecnology',
                    'last_criteria_by_note'
                ])
            );
        }
        catch (\Throwable $throwable) {

        }

        return redirect()
            ->back()
            ->with('error', 'Não');
    }

    /**
     * compila as notas dadas nas avaliações
     *
     * @param Award $award
     * @param CategoryAward $category_award
     * @param EvaluationStep $evaluation_step
     * @return Illuminate\Http\Response view
     */
    public function compile_score(Award $award, CategoryAward $category_award, EvaluationStep $evaluation_step)
    {
        $scoreList = $evaluation_step->getSocialTecnologyStepScore();

        return view('admin.social-tecnology.evaluation_step.socialTecnologyEvaluationScore.scoreList', compact(['evaluation_step','scoreList']));

    }

    /**
     * valida lista de aprovados para proxima etapa
     *
     * @param Request $request
     * @param Award $award
     * @param CategoryAward $category_award
     * @param EvaluationStep $evaluation_step
     * @return Illuminate\Http\Response view
     */
    public function validateFinalApprovedList(Request $request,Award $award, CategoryAward $category_award, EvaluationStep $evaluation_step)
    {
        if(strtoupper($evaluation_step->awardStatusForApprovedSocialTecnology) == 'F'  || strtoupper($evaluation_step->awardStatusForApprovedSocialTecnology) == 'W' ){
            if(Auth::guard('admin')->user()->hasRole($evaluation_step->getValidatorRole()->name ?? '')){
                $evaluation_step->validateSocialTechnologiesStepEvaluationScore();
                $evaluation_step->validateApprovedSocialTechnologies();
                $msg = 'Efetivação concluída!';
            }else{
                $msg = 'Você não tem permissão para efetuar esta ação!';
            }
        }else{
            $msg = 'Ação inválida!';
        }

        return redirect(
            route('admin.evaluations.steps.score.compile',[$award->id,$category_award->id,$evaluation_step->id,])
            )->with('message', $msg);

    }

}
