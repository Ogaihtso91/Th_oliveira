<?php

namespace App\Exports;

use App\Award;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class RegistrationsSocialTecnologiesByAwardExport implements FromView
{
    private $award;

    public function __construct(Award $award)
    {
        $this->award = $award;
    }

    /**
    * @return \Illuminate\Contracts\View\View
    */
    public function view(): View
    {
        return view('admin.export.xls.awards.registration.socialTecnologiesTable', [
            'award' => $this->award
        ]);
    }
}
