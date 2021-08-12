<?php
namespace App\Repositories;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\User;

class UserRepository {

    private $model;

    public function __construct()
    {
        $this->model = new User;
    }

    public function findByEmail($email)
    {
        return $this->model->where('email', $email);
    }

    public function checkEmailExists($email, $id = null)
    {
        if(is_null($id)){
            return $this->findByEmail($email)->exists();
        } else {
            return $this->findByEmail($email)->where('id', '!=', $id)->exists();
        }
    }

    public function updateMyAccount($user, $r)
    {
        $data = $r->all();
        if(!Hash::check($data['password'], $user->password))
            throw new \Exception('Senha não confere');

        if($this->checkEmailExists($data['email'], $user->id))
            throw new \Exception('E-mail já existe em outro usuário');

        $user->update(array_only($data, ['name','email'] ));

        if($r->hasFile('photo') && $r->file('photo')->isValid()){
            $image      = \Intervention\Image\Facades\Image::make($r->file('photo')->path());
            $extension  = $r->file('photo')->extension();
            if(!in_array($extension, ['png', 'jpeg', 'jpg']))
                throw new Exception('Formato Inválido');
            $horizontal = ($image->width() / $image->height() >= 1);
            
            if($horizontal){
                $image->heighten(500);
            } else {
                $image->widen(500);
            }
            $image->crop(500, 500);
            $filePath = $user->makeImagePath($extension);
            if($image->save($filePath, 70)){
                $fileName = explode('/', $filePath);
                $user->update(['photo' => end($fileName)]);
            }
        }

        return $user;
    }

    public function login($email, $senha, $manter_conectado = false)
    {
        return Auth::attempt(['email' => $email, 'password' => $senha], $manter_conectado);
    }

    public function createUser($data)
    {
        if($this->checkEmailExists($data['email']))
            throw new \Exception('Usuário já cadastrado');

        $data['password'] = bcrypt($data['password']);
        $user = $this->model->create( $data );
        return $user;
    }

    public function update(User $object, $data = [])
    {
        return $object->update($data);
    }
}