<?php


namespace App\Repositories\Repository\Award;


use App\Award;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GetAwardRepository
{
    /**
     * @param int $quantityPerPage
     * @param string $column
     * @param string $order
     * @return mixed
     */
    public function allOrderbyAndPaginate(int $quantityPerPage = 10, string $column = 'id', string $order = 'desc')
    {
        return Award::orderBy($column, $order)->paginate($quantityPerPage);
    }

    /**
     * @param int $id
     * @return Award
     */
    public function byId(int $id): Award
    {
        return Award::find($id);
    }

    /**
     * get all valid awards for evaluations
     *
     * @return mixed
     */
    public function validAwardsForEvaluations()
    {
        return Award::whereExists(
            function($query) {
                $query->select(DB::raw(1))
                    ->from('category_awards as t1')
                    ->join('category_award_evaluation_steps as t2', 't1.id', '=', 't2.category_award_id')
                    ->join('evaluation_step_evaluators as t3', 't2.id', '=', 't3.evaluationStep_id')
                    ->join('evaluation_step_evaluators_users_admin as t4', 't3.id', '=', 't4.evaluation_step_evaluators_id')
                    ->whereRaw('t1.award_id = awards.id')
                    ->whereRaw('t3.status = 1')
                    ->whereRaw('t4.evaluator_id = '. Auth::guard('admin')->user()->id);
            }
        )
        ->get();
    }
        
    /*
     * @return int
     */
    public function countAll(): int
    {
        return Award::count();
    }

    public function allAwardsActive()
    {
        // Pega a data atual e busca alguma premiacao que esteja disponivel para cadastro
        $hoje = date('Y-m-d');
        return DB::table('awards')->where('registrationsStartDate', '<=', $hoje)->where('registrationsEndDate', '>=', $hoje)->get();
    }
}
