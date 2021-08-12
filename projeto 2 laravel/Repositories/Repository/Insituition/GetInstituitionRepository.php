<?php


namespace App\Repositories\Repository\Insituition;


use App\Institution;

class GetInstituitionRepository
{
    public function getCountDistinctGroupByColumn(string $column): int
    {
        return Institution::distinct()->whereNotNull($column)->where($column, '<>', '')->count($column);
    }
}
