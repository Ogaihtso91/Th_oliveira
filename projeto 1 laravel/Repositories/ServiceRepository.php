<?php
namespace App\Repositories;
use App\Service;

class ServiceRepository {

    private $model;

    public function __construct()
    {
        $this->model = new Service;
    }

    public function getList()
    {
        return $this->model->orderBy('name','ASC')->pluck('name','id');
    }
}