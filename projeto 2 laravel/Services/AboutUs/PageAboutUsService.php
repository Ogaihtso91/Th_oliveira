<?php


namespace App\Services\AboutUs;


use App\Repositories\Repository\Insituition\GetInstituitionRepository;

class PageAboutUsService
{
    private const TOTAL_STATES = 27;

    private $getInstituiton;

    public function __construct()
    {
        $this->getInstituiton = new GetInstituitionRepository();
    }

    public function getNationwide(): int
    {
        $totalStates = $this->getInstituiton->getCountDistinctGroupByColumn('state');

        if (empty($totalStates)) return 0;

        return percentage_in_relation_to_the_total($totalStates, self::TOTAL_STATES);
    }
}
