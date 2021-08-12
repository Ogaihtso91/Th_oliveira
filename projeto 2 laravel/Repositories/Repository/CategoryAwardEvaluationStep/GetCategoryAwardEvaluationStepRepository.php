<?php


namespace App\Repositories\Repository\CategoryAwardEvaluationStep;

use App\CategoryAward;
use App\CategoryAwardEvaluationStep;
use App\SocialTecnologyCategoryAwardEvaluationStep as STEvaluationStep;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GetCategoryAwardEvaluationStepRepository
{
    public function byAwardId(int $categoryAwardId): Collection
    {
        return CategoryAwardEvaluationStep::where('category_award_id', $categoryAwardId)->get();
    }

    /**
     * busca etapas de avaliações validas
     * para avaliação.
     *
     * @param CategoryAward $category_award
     * @return mixed
     */
    public function validsStepsForEvaluations(CategoryAward $category_award)
    {
        return CategoryAwardEvaluationStep::whereExists(
            function ($query) use ($category_award) {
                $query->select(DB::raw(1))
                    ->from('evaluation_step_evaluators_users_admin as t1')
                    ->join('evaluation_step_evaluators as t2','t1.evaluation_step_evaluators_id','=','t2.id')
                    ->whereRaw('t2.evaluationStep_id = category_award_evaluation_steps.id')
                    ->whereRaw('category_award_evaluation_steps.category_award_id = '. $category_award->id)
                    ->whereRaw('t1.evaluator_id = ' . Auth::guard('admin')->user()->id);
            }
        )
        ->get();
    }

    /**
     * @param CategoryAwardEvaluationStep $evaluationStep
     * @return mixed
     */
    public function socialTecnologyForEvaluation(CategoryAwardEvaluationStep $evaluationStep)
    {
        return STEvaluationStep::whereHas('evaluationStep',
            function($query) use ($evaluationStep) {
                $query->where('id', $evaluationStep->id);
            }
        )
        ->whereHas('evaluationStepEvaluators',
            function($query) {
                $query->whereHas('evaluators',
                    function($query) {
                        $query->where('evaluator_id', Auth::guard('admin')->user()->id);
                    }
                );
            }
        )
        ->get();
    }

    /**
     * @param CategoryAwardEvaluationStep $evaluationStep
     * @return mixed
     */
    public function socialTecnologyNotAssigned(CategoryAwardEvaluationStep $evaluationStep)
    {
        return STEvaluationStep::whereHas('evaluationStep',
            function($query) use ($evaluationStep) {
                $query->where('id', $evaluationStep->id);
            }
        )
        ->whereHas('evaluationStepEvaluators',
            function($query) {
                $query->whereDoesntHave('evaluators',
                    function($query) {
                        $query->where('evaluator_id', Auth::guard('admin')->user()->id);
                    }
                );
            }
        )
        ->get();
    }

    /**
     * @param CategoryAwardEvaluationStep $model
     * @param int $socialTecnolodyId
     * @return mixed
     */
    public function getSocialTecnologyById(CategoryAwardEvaluationStep $model, int $socialTecnolodyId)
    {
        return $model->socialTecnologyEvaluation
            ->where('socialTecnology_id', $socialTecnolodyId)->all();
    }
}
