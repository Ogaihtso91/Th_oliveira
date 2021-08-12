<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SocialTecnologyCategoryAwardEvaluationStep extends Model
{
    protected $table = 'social_tecnology_step_evaluation';

    protected $fillable = [
        'notion',
        'validated',
        'audited',
        'socialTecnology_id',
        'evaluationStep_id',
        'evaluation_step_evaluators_id',
        'stepFinalScore'
    ];

    protected $casts = [
        'validated' => 'integer',
        'evaluationStep_id' => 'integer',
        'socialTecnology_id' => 'integer',
        'evaluation_step_evaluators_id' => 'integer',
        'stepFinalScore' => 'decimal:2',
    ];

    protected $appends = [
        'final_score_preview',
        'social_tecnology_evaluator_step_evaluation',
    ];

    public function getFinalScorePreviewAttribute()
    {
        // média das avaliações
        return SocialTecnologyEvaluatorStepEvaluation::
            where('evaluationStep_id', $this->evaluationStep_id)
            ->where('socialTecnology_id', $this->socialTecnology_id)
            ->get()
            ->map(function($item, $key){
                return
                    (is_null($item->bonusValue))
                    ? $item->score_preview
                    : $item->score_preview + ( $item->score_preview * ($item->bonusValue ?? 0) / 100);
            })
            ->sum()
            /
            $this->evaluationStep()
                ->first()
                ->evaluators()
                ->where('status',1)
                ->get()
                ->map(function($item){
                    return $item->evaluators->count();
                })->sum()
            ;
    }

    public function setFinalStepScore()
    {
        $this->stepFinalScore =  $this->final_score_preview;
        return ($this->save()) ? true  : false;
    }

    public function socialTecnology()
    {
        return $this->belongsTo(SocialTecnology::class,'socialTecnology_id','id');
    }

    public function getSocialTecnologyEvaluatorStepEvaluationAttribute()
    {
        return (new SocialTecnologyEvaluatorStepEvaluation)
            ->where('socialTecnology_id', $this->socialTecnology_id)
            ->where('evaluationStep_id',$this->evaluationStep_id)
            ->get();
    }

    public function evaluationStep()
    {
        return $this->belongsTo(CategoryAwardEvaluationStep::class,'evaluationStep_id','id');
    }

    public function evaluationStepEvaluators()
    {
        return $this->belongsTo(CategoryAwardEvaluationStepEvaluators::class,'evaluation_step_evaluators_id','id');
    }
}
