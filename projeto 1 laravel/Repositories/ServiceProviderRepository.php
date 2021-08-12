<?php
namespace App\Repositories;
use App\ServiceProvider;

class ServiceProviderRepository {

    private $model;

    public function __construct()
    {
        $this->model = new ServiceProvider;
    }

    public function create($data, $user_id)
    {
        $data['user_responsible_id'] = $user_id;
        $data['cnpj'] = preg_replace('/[^0-9]+/i', '', $data['cnpj']);
        return $this->model->create($data);
    }

    public function update($serviceProvider, $data)
    {
        $this->model = $serviceProvider;
        $data['cnpj'] = preg_replace('/[^0-9]+/i', '', $data['cnpj']);
        $data = array_only($data, ['name', 'cnpj', 'type_provider_id']);
        return $this->model->update($data);
    }

}