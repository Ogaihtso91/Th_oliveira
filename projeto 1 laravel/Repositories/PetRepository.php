<?php
namespace App\Repositories;
use App\Pet;

class PetRepository {

    private $model;
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->model = new Pet;
        $this->userRepository = $userRepository;
    }

    public function first($id, $user)
    {
        $pet = $user->pets()->where('pets.id', $id)->get()->first();
        if(!$pet) throw new \Exception('Pet não localizado :(');

        return $pet;
    }

    public function save($data, $user)
    {
        $onlyDate = array_only($data->all(), ['name', 'color', 'birth_date', 'breed_id', 'gender', 'castrated']);

        $onlyDate['birth_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $onlyDate['birth_date'])->format('Y-m-d');

        if(empty($data['id'])){
            $onlyDate['user_id'] = $user->id;
            $this->model = $this->model->create($onlyDate);
            $user->pets()->attach($this->model->id);
        } else {
            $this->model = $this->first($data['id'], $user);
            $this->model->update($onlyDate);
        }

        if($data->hasFile('photo') && $data->file('photo')->isValid()){
            $image      = \Intervention\Image\Facades\Image::make($data->file('photo')->path());
            $extension  = $data->file('photo')->extension();
            if(!in_array($extension, ['png', 'jpeg', 'jpg']))
                throw new Exception('Formato Inválido');
            $horizontal = ($image->width() / $image->height() >= 1);
            
            if($horizontal){
                $image->heighten(500);
            } else {
                $image->widen(500);
            }
            $image->crop(500, 500);
            $filePath = $this->model->makeImagePath($extension);
            if($image->save($filePath, 70)){
                $fileName = explode('/', $filePath);
                $this->model->update(['photo' => end($fileName)]);
            }
        }
        return $this->model;
    }

    public function addResponsible($pet, $responsible)
    {
        $responsibles = $pet->responsibles()->get()->toArray();
        if(in_array($responsible['email'], array_map(function($r) {
            return $r['email'];
        }, $responsibles))) throw new \Exception('Este responsavel já está associado à este pet');

        $responsible = $this->userRepository->findByEmail($responsible['email'])->first();

        if(!$responsible) throw new \Exception('Este e-mail não corresponde a um usuário registrado');

        $pet->responsibles()->attach($responsible->id);
    }

}