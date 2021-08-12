<?php
namespace App\Repositories;
use App\TypeProvider;

class TypeProviderRepository {

    private $model;

    public function __construct()
    {
        $this->model = new TypeProvider;
    }

    public function getList()
    {
        return $this->model->orderBy('description','ASC')->pluck('description','id');
    }
}