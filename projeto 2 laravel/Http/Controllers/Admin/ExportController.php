<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CategoryAwardEvaluationStep as EvaluationStep;
use App\Award;
use App\CategoryAward;
use App\Exports\SocialTecnologiesByEvaluationStepExport;
use App\Exports\RegistrationsSocialTecnologiesByAwardExport;
use App\Exports\RegistrationsSocialTecnologiesByChallengeTypeCategoryAward;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function socialTecnologiesListByEvaluationStepToXLS (EvaluationStep $evaluationStep)
    {
        return Excel::download(
            new SocialTecnologiesByEvaluationStepExport($evaluationStep),
            'TecnologiasSociais.xls'
        );
    }

    public function awardSubscriptionsSocialTecnologiesToXLS (Award $award)
    {
        // return view('admin.export.xls.awards.registration.socialTecnologiesTable', [
        //     'award' => $award
        // ]);
        return Excel::download(
            new RegistrationsSocialTecnologiesByAwardExport($award),
            'TecnologiasSociais.xls'
        );
    }

    public function challengeCategoryAwardSubscriptionsSocialTecnologiesToXLS (Award $award, CategoryAward $categoryAward)
    {
        return Excel::download(
            new RegistrationsSocialTecnologiesByChallengeTypeCategoryAward($categoryAward),
            'TecnologiasSociais-Desafios.xls'
        );
    }
}
