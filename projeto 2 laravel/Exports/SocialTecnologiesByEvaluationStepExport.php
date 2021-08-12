<?php

namespace App\Exports;

use App\CategoryAwardEvaluationStep as EvaluationStep;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class SocialTecnologiesByEvaluationStepExport implements FromView
{
    private $evaluationStep;

    public function __construct(EvaluationStep $evaluationStep)
    {
        $this->evaluationStep = $evaluationStep;
    }

    public function view(): View
    {
        if($this->evaluationStep->evaluationType == 1){
            $viewName = 'admin.export.xls.awards.certification.socialTecnologiesTable';
        }elseif($this->evaluationStep->evaluationType == 2){
            $viewName = 'admin.export.xls.awards.semifinalOrFinalStep.socialTecnologiesTable';
        }
        return view($viewName, [
            'evaluationStep' => $this->evaluationStep
        ]);
    }
}
