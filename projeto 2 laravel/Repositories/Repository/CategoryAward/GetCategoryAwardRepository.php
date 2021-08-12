<?php


namespace App\Repositories\Repository\CategoryAward;

use App\Award;
use App\CategoryAward;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GetCategoryAwardRepository
{
    /**
     * busca todas as categorias de premiação
     * validas para avaliação.
     *
     * @param Award $award
     * @return mixed
     */
    public function validsCategoriesAwardsForEvaluations(Award $award) {
        return CategoryAward::whereExists(
            function($query) {
                $query->select(DB::raw(1))
                    ->from('evaluation_step_evaluators_users_admin as t1')
                    ->join('evaluation_step_evaluators as t2', 't1.evaluation_step_evaluators_id', '=', 't2.id')
                    ->join('category_award_evaluation_steps as t3','t2.evaluationStep_id','=','t3.id')
                    ->whereRaw('t3.category_award_id = category_awards.id')
                    ->whereRaw('t2.status = 1')
                    ->whereRaw('t1.evaluator_id = '. Auth::guard('admin')->user()->id);
            }
        )
        ->where('award_id', $award->id)
        ->get();
    }
}
