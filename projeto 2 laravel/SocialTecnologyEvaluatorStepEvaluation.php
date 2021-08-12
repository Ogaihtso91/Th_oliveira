<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class SocialTecnologyEvaluatorStepEvaluation extends Model
{
    protected $table = 'social_tecnology_evaluator_step_evaluation';

    protected $fillable = [
        'notion',
        'bonusValue',
        'bonusNotion',
        'socialTecnology_id',
        'evaluationStep_id',
        'evaluator_id'
    ];

    protected $appends = [
        'score_preview',
        'social_tecnology_evaluation_criteria',
    ];

    public function getSocialTecnologyEvaluationCriteriaAttribute()
    {
        return (new SocialTecnologyCategoryAwardEvaluationCriteria)
            ->where('socialTecnology_id', $this->socialTecnology_id)
            ->where('evaluator_id', $this->evaluator_id)
            ->whereHas('categoryAwardEvaluationCriterion', function($query){
                $query->where('evaluationStep_id',$this->evaluationStep_id);
            })
            ->get();
    }

    public function getScorePreviewAttribute()
    {
        // média ponderada
        return SocialTecnologyCategoryAwardEvaluationCriteria::
            where('evaluator_id', $this->evaluator_id)
            ->where('socialTecnology_id', $this->socialTecnology_id)
            ->whereHas('categoryAwardEvaluationCriterion', function($query){
                $query->where('evaluationStep_id',$this->evaluationStep_id);
            })
            ->get()
            ->map(function($item, $key){
                return $item->evaluationScore * ($item->categoryAwardEvaluationCriterion->weight ?? 1);
            })
            ->sum()
            /
            $this->social_tecnology_evaluation_criteria
                ->map(function ($item){
                    return $item->categoryAwardEvaluationCriterion->weight ?? 1;
                })->sum()
            ;
    }

    public function socialtecnology()
    {
        return $this->belongsTo(SocialTecnology::class,'socialTecnology_id','id');
    }

    public function evaluation_step()
    {
        return $this->belongsTo(CategoryAwardEvaluationStep::class,'evaluationStep_id','id');
    }

    public function evaluator()
    {
        return $this->belongsTo(UserAdmin::class,'evaluator_id','id');
    }

    /**
     * salva instancia no DB
     *
     * @param array $data
     * @return SocialTecnologyEvaluatorStepEvaluation $evaluator_step_evaluation
     */
    public static function store(array $data)
    {
        // criamos uma nova Instancia dessa model
        $evaluator_step_evaluation = new self([
            'socialTecnology_id' => $data['social_tecnology_id'],
            'evaluationStep_id' => $data['evaluation_steps_id'],
            'evaluator_id' => Auth::guard('admin')->user()->id,
            'notion' => $data['notion'] ?? null,
            'bonusValue' => $data['bonus_value'] ?? null,
            'bonusNotion' => $data['bonus_notion'] ?? null,
        ]);

        // registramos a instancia no banco de dados
        // e retornamos o objeto da instancia
        return $evaluator_step_evaluation->save();
    }

    public static function getBySocialTechAndEvaluationStep($social_tecnology_id, $evaluation_steps_id) {
        return self::where('socialTecnology_id', $social_tecnology_id)
                ->where('evaluator_id', Auth::guard('admin')->user()->id) // Issue #4964 - estava editando a nota de outro
                ->where('evaluationStep_id', $evaluation_steps_id)
                ->first();
    }
}
