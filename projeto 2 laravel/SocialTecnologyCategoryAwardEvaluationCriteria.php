<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class SocialTecnologyCategoryAwardEvaluationCriteria extends Model
{
    protected $table = 'social_tecnology_criteria_evaluation';

    protected $fillable = [
        'evaluationScore',
        'evaluationBoolean',
        'justification',
        'evaluationCriterion_id',
        'socialTecnology_id',
        'evaluator_id',
    ];

    protected $appends = [
        'evaluation_step_id',
    ];
    public function getEvaluationStepIdAttribute()
    {
        return $this->categoryAwardEvaluationCriterion->evaluationStep_id;
    }

    public function categoryAwardEvaluationCriterion()
    {
        return $this->belongsTo(CategoryAwardEvaluationCriterion::class,'evaluationCriterion_id','id');
    }

    public function socialTecnology()
    {
        return $this->belongsTo(SocialTecnology::class,'socialTecnology_id','id');
    }

    public function evaluator()
    {
        return $this->belongsTo(UserAdmin::class,'evaluator_id','id');
    }

    /**
     * registra os dados da avaliação do critério.
     *
     * @param Array $data
     * @return SocialTecnologyCategoryAwardEvaluationCriteria Instance
     */
    public static function store(array $data)
    {
        /** criamos uma Instancia da model */
        $evaluation_criteria_obj = new self([
            'socialTecnology_id' => $data['social_tecnology_id'],
            'evaluator_id' => Auth::guard('admin')->user()->id,
            'evaluationBoolean' => $data['evaluationBoolean'] ?? null,
            'evaluationScore' => $data['evaluationScore'] ?? null,
            'evaluationCriterion_id' => $data['evaluationCriterion_id']
        ]);

        /** registramos o obj no banco de dados */
        $evaluation_criteria_obj->save();

        /** retornamos o obj criado */
        return $evaluation_criteria_obj;
    }
}
