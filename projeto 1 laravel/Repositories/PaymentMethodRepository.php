<?php

namespace App\Repositories;

use App\PaymentMethod;

class PaymentMethodRepository
{
    private $model;

    public function __construct()
    {
        $this->model = new PaymentMethod;
    }

    public function getList()
    {
        return $this->model->orderBy('name','ASC')->pluck('name','id');
    }
}