<?php
namespace App\Repositories;
use App\Species;

class SpeciesRepository {

    private $model;

    public function __construct()
    {
        $this->model = new Species;
    }

    public function getList()
    {
        return $this->model->orderBy('name','ASC')->pluck('name','id');
    }
}