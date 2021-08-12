<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryAwardEvaluationCriterion extends Model
{
    use SoftDeletes;

    protected $table = 'category_award_evaluation_criteria';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'evaluationCriteria',
        'weight'
    ];

    public function CategoryAwardEvaluationStep()
    {
        return $this->belongsTo(CategoryAwardEvaluationStep::class,'evaluationStep_id','id');
    }
}
