<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoryAwardEvaluationStepEvaluatorUserAdmin extends Model
{
    protected $table = 'evaluation_step_evaluators_users_admin';

    protected $fillable = [
        'evaluation_step_evaluators_id',
        'evaluator_id',
    ];

    public function categoryAwardEvaluationStepEvaluators()
    {
        return $this->belongsTo(CategoryAwardEvaluationStepEvaluators::class,'evaluation_step_evaluators_id','id');
    }

    public function evaluator()
    {
        return $this->belongsTo(UserAdmin::class,'evaluator_id','id');
    }
}
