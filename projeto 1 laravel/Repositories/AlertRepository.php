<?php

namespace App\Repositories;

use App\PetAlert;
use App\Species;
use App\Breed;
use Illuminate\Support\Facades\Mail;

class AlertRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new PetAlert;
    }
    public function getAlerts($lat, $lng, $params = [])
    {
        $alerts = $this->listByPosition(
            'last_position_lat', $lat,
            'last_position_lng', $lng,
            10,
            $this->model
        )
        ->where('status', PetAlert::OPEN)
        ->orderByRaw('TRUNCATE(distance, 0) ASC, created_at DESC');

        if(isset($params['limit']))
            $alerts->limit($params['limit']);

        return $alerts;
    }
    public function findById($id, $user = null)
    {
        if(is_null($user)){
            $alert = $this->model->where('id', $id)->first();
        } else {
            $alert = $user->alerts()->where('id', $id)->first();
        }
        if(!$alert)
            throw new \Exception("Alerta não encontrado");
        return $alert;
    }
    public function doComment($alert, $comment)
    {
        if(!$alert) throw new \Exception("Alerta não encontrado");
        $alert = $alert->comments()->create($comment);
        return $alert;
    }
    public function create($data, $photo, $user)
    {  
        $updatePhoto = false;
        $pet = false;
        $data['user_id'] = $user->id;

        if(is_numeric($data['pet_id'])) {
            $pet = $user->pets()->where('pets.id', $data['pet_id'])->get()->first();
            if(!$pet)
                throw new \Exception("Pet não localizado");

            $data['specie'] = $pet->breed->species->name;
            $data['breed'] = $pet->breed->name;
            $data['color'] = $pet->color;
            $data['gender'] = $pet->gender;

            $data = array_only($data, ['user_id', 'alert_type','name','breed','specie', 'gender', 'color', 'comment', 'localization', 'last_position_lat', 'last_position_lng']);
            $alert = $this->model->create($data);

            if(!empty($pet->photo)){
                $photo = new \Illuminate\Http\File($pet->url_photo);
                $extension = $photo->extension();
                $filePath = $alert->makeImagePath($extension);
                $image = \Intervention\Image\Facades\Image::make($photo->path());
                if($image->save($filePath, 70)){
                    $fileName = explode('/', $filePath);
                    $alert->update(['photo' => end($fileName)]);
                }
            }
        } else {
            $data['specie'] = Species::where('id', $data['specie'])->first()->name;
            $data['breed']  = Breed::where('id', $data['breed'])->first()->name;

            $data = array_only($data, ['user_id', 'alert_type','name','breed','specie', 'gender', 'color', 'comment', 'localization', 'last_position_lat', 'last_position_lng']);
            $alert = $this->model->create($data);
            if($photo){
                $image      = \Intervention\Image\Facades\Image::make($photo->path());
                $extension  = $photo->extension();
                if(!in_array($extension, ['png', 'jpeg', 'jpg']))
                    throw new Exception('Formato Inválido');
                $horizontal = ($image->width() / $image->height() >= 1);
                
                if($horizontal){
                    $image->heighten(500);
                } else {
                    $image->widen(500);
                }
                $image->crop(500, 500);
                $filePath = $alert->makeImagePath($extension);
                if($image->save($filePath, 70)){
                    $fileName = explode('/', $filePath);
                    $updatePhoto = true;
                    $alert->update(['photo' => end($fileName)]);
                }
            }
        }

        try {
            $this->sendAlert($alert);
        } catch(\Exception $e){}

        return $alert;
    }

    public function sendAlert($alert)
    {
        $positions = $this->listByPosition('lat', $alert->last_position_lat, 'lng', $alert->last_position_lng, 10, new \App\UserPosition())->groupBy('user_id')->with('user')->get()->toArray();

        $emails = array_map(function($position){
            if(filter_var($position['user']['email'], FILTER_VALIDATE_EMAIL))
                return $position['user']['email'];
        }, $positions);

        //$emails = ['binhofvieira@msn.com','xellypcampos@gmail.com'];
        try {
            Mail::to('binhofvieira@gmail.com')->bcc($emails)->send(new \App\Mail\SendAlert($alert));
        } catch(\Exception $e) {
            die($e->getMessage());
        }

    }
}