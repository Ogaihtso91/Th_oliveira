<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoryAwardEvaluationStepEvaluators extends Model
{
    protected $table = 'evaluation_step_evaluators';

    protected $fillable = [
        'evaluationStep_id',
        'status'
    ];

    protected $appends = ['count_social_tecnologies','evaluators'];

    public function evaluationStep()
    {
        return $this->belongsTo(CategoryAwardEvaluationStep::class,'evaluationStep_id','id');
    }

    public function socialTecnologyEvaluationSteps()
    {
        return $this->hasMany(SocialTecnologyCategoryAwardEvaluationStep::class,'evaluation_step_evaluators_id','id');
    }

    public function getCountSocialTecnologiesAttribute()
    {
        return $this->socialTecnologyEvaluationSteps()->count();
    }

    public function socialTecnologyEvaluationCriteria()
    {
        return $this->hasMany(SocialTecnologyCategoryAwardEvaluationCriteria::class,'evaluationCriterion_id','id');
    }

    public function evaluators()
    {
        return $this->belongsToMany(
            UserAdmin::class,
            (new CategoryAwardEvaluationStepEvaluatorUserAdmin)->getTable(),
            'evaluation_step_evaluators_id',
            'evaluator_id'
        );
    }

    public function getEvaluatorsAttribute()
    {
        return $this->evaluators()->get();
    }
}
