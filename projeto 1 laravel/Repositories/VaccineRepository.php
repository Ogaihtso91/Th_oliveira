<?php
namespace App\Repositories;
use App\Vaccine;
use Illuminate\Support\Facades\Mail;

class VaccineRepository {

    private $model;

    public function __construct()
    {
        $this->model = new Vaccine;
    }

    public function save($pet, $data)
    {
        if(!empty($data['date']))
            $data['date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $data['date'])->format('Y-m-d');

        if(!empty($data['remember_date']))
            $data['remember_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $data['remember_date'])->format('Y-m-d');

        if(!$pet->vaccines()->save(new Vaccine($data)))
            throw new \Exception('Ocorreu um erro ao inserir vacina.');

        return true;
    }

    public function remove($pet, $vaccine_id)
    {
        $vaccine = $pet->vaccines->where('id', $vaccine_id)->first();

        if(!$vaccine)
            throw new \Exception('Vacina não encontrada');
        if(!$vaccine->delete())
            throw new \Exception('Ocorreu um erro ao apagar esta vacina');

        return true;
    }

    public function sendAlert()
    {
        $date = new \DateTime();
        $date->add(new \DateInterval('P10D'));
        $vaccines = Vaccine::with(['pet', 'pet.user'])->where('remember_date', '=', $date->format('Y-m-d'))->get();
        $success = [];
        $failures = [];
        foreach($vaccines as $vaccine){
            $email = $vaccine->pet->user->email;
            try {
                Mail::to('binhofvieira@gmail.com')->send(new \App\Mail\VaccineAlert($vaccine));
                $success[]  = ['pet' => $vaccine->pet->name, 'email' => $email];
            } catch(\Exception $e) {
                $failures[] = ['pet' => $vaccine->pet->name, 'email' => $email, 'message' => $e->getMessage()];
            }
        }
        return [
            'sent' => true,
            'success' => $success,
            'failures' => $failures
        ];
    }
}