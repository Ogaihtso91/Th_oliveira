<?php

namespace App\Http\Controllers\Admin;

use App\CategoryAwardEvaluationStep;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Repository\CategoryAwardEvaluationStep\GetCategoryAwardEvaluationValidateRepository;
use App\SocialTecnology;
use App\SocialTecnologyCategoryAwardEvaluationCriteria;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class SocialTecnologyEvaluationValidateController extends Controller
{
    protected $getEvaluationStepValidate;

    public function __construct()
    {
        $this->getEvaluationStepValidate = new GetCategoryAwardEvaluationValidateRepository();
    }
    /**
     * Lista de avaliações
     *
     * @return Illuminate\Http\Response view
     */
    public function evaluation_index() {

        $evaluationAppraisal = CategoryAwardEvaluationStep::where('enableTwoEvaluators', 1)
            ->where('evaluationType', 1)
            ->orderBy('created_at', 'desc')
            ->get();

        $list_items = Collection::make();

        /** busca informações sobre a ts */
        /** busca informação sobre a etapa de avaliação */
        $evaluationAppraisal->each(
            function($item, $key) use ($list_items) {
                $arr_aux = [
                    'name' => $item->name,
                    'desc' => [
                        'label' => 'Categoria',
                        'content' => $item->categoryAward->name,
                    ],
                    'link' => [
                        'name' => 'admin.evaluation.validate.ts.index',
                        'params' => [
                            'evaluation_step' => $item->id,
                        ]
                    ]
                ];

                $list_items->push($arr_aux);

                unset($arr_aux);
            }
        );

        /** retornamos a view e seus parâmetros */
        return view('admin.social-tecnology.evaluation_step.evaluation_validate.list',
            compact('list_items')
        );
    }

    /**
     * @param Request $request
     * @param CategoryAwardEvaluationStep $evaluationStep
     * @return mixed
     */
    public function tecnologies_index(Request $request, CategoryAwardEvaluationStep $evaluationStep)
    {
        $socialTecnologyStep = $evaluationStep->socialTecnologyEvaluationStep;

        $ts = SocialTecnology::whereHas('evaluations',
            function($query) use ($evaluationStep) {
                $query->where('evaluationStep_id', $evaluationStep->id);
            }
        )
        ->get();

        //dd($ts->toArray());

        $socialTecnologies = Collection::make();

        //dd($socialTecnologies)->toArray();

        $ts->map(
            function($item, $key) use ($socialTecnologyStep, $socialTecnologies, $evaluationStep) {

                $step = $socialTecnologyStep->where('socialTecnology_id', $item->id)->first();

                // dupla avaliadora
                $evaluators = $step->evaluationStepEvaluators;

                // individuo da dupla
                $first_evaluator = $evaluators->evaluators->first();
                $secondy_evaluator = $evaluators->evaluators->last();

                $validated = Collection::make();

                $item->evaluations->map(
                    function($evaluation, $key) use ($first_evaluator, $secondy_evaluator, $validated) {
                        if(
                            $evaluation->where('evaluator_id', $first_evaluator->id) ||
                            $evaluation->where('evaluator_id', $secondy_evaluator->id)
                        )
                        {
                            $validated->push($evaluation);
                        }
                    }
                );

                $arr_aux = [
                    'id' => $item->id,
                    'name' => $item->socialtecnology_name,
                    'evaluators' => $first_evaluator->name .', '. $secondy_evaluator->name,
                    'evaluations' => $validated->all(),
                    'status' => $validated->count() >= 1 ? true : false,
                    'validated' => $step->validated ?? null,
                    'link' => [
                        'name' => 'admin.evaluation.validate.show',
                        'params' => [
                            'evaluationStep' => $evaluationStep->id,
                            'socialTecnology' => $item->id,
                        ],
                    ],
                ];

                $socialTecnologies->push($arr_aux);

                unset($arr_aux);

            }
        );

        return view('admin.social-tecnology.evaluation_step.evaluation_validate.ts_list',
            compact('socialTecnologies', 'evaluationStep')
        );
    }

    /**
     * detalhes da avaliação
     *
     * @param SocialTecnology $socialTecnology
     * @param CategoryAwardEvaluationStep $evaluationStep
     * @return Factory|View
     */
    public function evaluation_show(CategoryAwardEvaluationStep $evaluationStep, SocialTecnology $socialTecnology)
    {
        $category_award = $evaluationStep->categoryAward;

        /** criando receptaculo de dados */
        $list_items = Collection::make();

        /** busco informações relacionadas */
        $socialTecnology->evaluations->each(
            function($item, $key) use ($list_items, $socialTecnology) {
                $evaluation_criteria = SocialTecnologyCategoryAwardEvaluationCriteria::where('socialTecnology_id', $socialTecnology->id)
                    ->where('evaluator_id', $item->evaluator->id)->get();

                $arr_aux = array();

                $arr_aux['evaluator'] = [
                    'id' => $item->evaluator->id,
                    'name' => $item->evaluator->name,
                    'username' => $item->evaluator->username,
                ];
                $arr_aux['criterias'] = $evaluation_criteria->all();
                $arr_aux['notion'] = $item->notion;

                /** passamos os dados para o receptaculo */
                $list_items->push($arr_aux);

                /** limpamos o array auxiliar */
                unset($arr_aux);
            }
        );

        // passando instancias para
        // variaveis com o nomes compativeis
        // com o component de detalhe da ts.
        $socialtecnology = $socialTecnology;
        $evaluation_step = $evaluationStep;

        // dd($list_items);
        /** retornamos a view com os detalhes da avaliação */
        return view('admin.social-tecnology.evaluation_step.evaluation_validate.validate',
            compact('list_items', 'socialtecnology', 'evaluation_step','category_award')
        );
    }

    /**
     * registra a validação do coordenador
     *
     * @param Illuminate\Http\Request $request
     * @param CategoryAwardEvaluationStep $evaluationStep
     * @param SocialTecnology $socialTecnology
     * @return Illuminate\Http\Response redirect
     */
    public function evaluation_validate_register(Request $request, CategoryAwardEvaluationStep $evaluationStep, SocialTecnology $socialTecnology)
    {
        // Validation
        $validated = $request->validate([
            'validated' => 'required',
        ]);

        // buscando etapa de avaliação para modificação
        // dos dados.
        $evaluationAppraisal = $this->getEvaluationStepValidate
            ->getEvaluationStepForAppraisal($evaluationStep, $socialTecnology);

        $evaluationAppraisal->update([
            'validated' => $validated['validated'],
        ]);

        /** salva avaliação no db */
        // socialTecnologyCategoryAwardEvaluationStep::create([
        //     'validated' => $validated['validated'],
        //     'socialTecnology_id' => $socialTecnology->id,
        //     'evaluationStep_id' => $evaluationStep->id,
        // ]);

        $this->getEvaluationStepValidate->updateSocialTecnologyAwardStatus(
            $evaluationStep->awardStatusForApprovedSocialTecnology,
            $socialTecnology,
            $validated['validated']
        );

        // if($validated['validated'] == 1) {
        //     $socialTecnology->update([
        //         'award_status' => $evaluationStep->awardStatusForApprovedsocialTecnology
        //     ]);
        // } else {
        //     $socialTecnology->update([
        //         'award_status' => null
        //     ]);
        // }

        /** redireciona para a view anterior */
        return redirect(route('admin.evaluation.validate.ts.index', [
            'evaluationStep' => $evaluationStep->id
        ]))
            ->with('message', 'Validado com sucesso!');
    }
}
