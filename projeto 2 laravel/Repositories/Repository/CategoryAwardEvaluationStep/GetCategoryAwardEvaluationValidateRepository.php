<?php


namespace App\Repositories\Repository\CategoryAwardEvaluationStep;

use App\CategoryAwardEvaluationStep;
use App\SocialTecnology;
use App\SocialTecnologyCategoryAwardEvaluationStep;

class GetCategoryAwardEvaluationValidateRepository
{
    /**
     * @param SocialTecnology $socialTecnology
     * @param CategoryAwardEvaluationStep $evaluationStep
     * @return mixed
     */
    public function getEvaluationStepForAppraisal(CategoryAwardEvaluationStep $evaluationStep, SocialTecnology $socialTecnology)
    {
        try {
            return SocialTecnologyCategoryAwardEvaluationStep::whereHas('evaluationStep',
                function($query) use ($evaluationStep) {
                    $query->where('id', $evaluationStep->id);
                }
            )
            ->whereHas('socialTecnology',
                function($query) use ($socialTecnology) {
                    $query->where('id', $socialTecnology->id);
                }
            )
            ->first();
        } catch(\Throwable $throwable) {
            create_error_log($throwable, $evaluationStep, $socialTecnology, 'Erro ao buscar etapa de avaliação.');
        }
    }

    /**
     * @param string $award_status
     * @param SocialTecnology $model
     * @param string $validated
     * @return mixed
     */
    public function updateSocialTecnologyAwardStatus($award_status, $model, $validated) {
        if(!$validated) {
            return $model->update([ 'award_status' => null ]);
        }

        return $model->update(['award_status' => $award_status ]);
    }
}
