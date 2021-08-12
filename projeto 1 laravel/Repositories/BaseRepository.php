<?php


namespace App\Repositories;

abstract class BaseRepository
{
    private $model;

    public function listByPosition($lat_field, $lat, $lng_field, $lng, $distance = 5, $model)
    {
        $where = "(6371 * ACOS( COS( RADIANS(?) ) * COS( RADIANS( " . $lat_field . " ) ) * COS( RADIANS( " . $lng_field . " ) - RADIANS(?) ) + SIN( RADIANS(?) ) * SIN(RADIANS(" . $lat_field . ")) ) )";
        $result = $model->selectRaw("{$model->getTable()}.*, TRUNCATE({$where}, 2) AS distance", [$lat, $lng, $lat] )
            ->havingRaw('distance <= ?', [ $distance ]);

        return $result;
    }

}