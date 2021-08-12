<?php
namespace App\Repositories;
use App\Medicine;

class MedicineRepository {

    private $model;

    public function __construct()
    {
        $this->model = new Medicine;
    }

    public function save($pet, $data)
    {
        if(!empty($data['first_day']))
            $data['first_day'] = \Carbon\Carbon::createFromFormat('d/m/Y', $data['first_day'])->format('Y-m-d');

        if(!empty($data['last_day']))
            $data['last_day'] = \Carbon\Carbon::createFromFormat('d/m/Y', $data['last_day'])->format('Y-m-d');

        if(!$pet->medicines()->save(new Medicine($data)))
            throw new \Exception('Ocorreu um erro ao inserir medicamento.');

        return true;
    }

    public function remove($pet, $medicine_id)
    {
        $medicine = $pet->medicines->where('id', $medicine_id)->first();

        if(!$medicine)
            throw new \Exception('Medicamento não encontrado');
        if(!$medicine->delete())
            throw new \Exception('Ocorreu um erro ao apagar este medicamento');

        return true;
    }
}