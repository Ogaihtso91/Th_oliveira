<?php

namespace App\Exports;

use App\CategoryAward;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class RegistrationsSocialTecnologiesByChallengeTypeCategoryAward implements FromView
{
    private $categoryAward;

    public function __construct(CategoryAward $categoryAward)
    {
        $this->categoryAward = $categoryAward;
    }

    /**
    * @return \Illuminate\Contracts\View\View
    */
    public function view(): View
    {
        return view('admin.export.xls.awards.challenge.socialTecnologiesChallengeTable', [
            'categoryAward' => $this->categoryAward
        ]);
    }
}
