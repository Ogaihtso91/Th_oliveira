<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use App\UserAdmin;
use Spatie\Permission\Models\Role;
use Auth;
use Validator;

class UserAdminController extends Controller
{
    protected $messages = [
        'name.required' => '"Nome" é obrigatório.',
        'name.max' => 'O campo "Nome" deve possuir até 255 caracteres.',
        'name.string' => 'O campo "Nome" deve ser no formato de texto.',

        'email.required'  => '"E-mail" é obrigatório.',
        'email.string'  => 'O campo "E-mail" deve ser no formato de texto.',
        'email.email'  => 'O e-mail informado é inválido.',
        'email.max' => 'O campo "E-mail" deve possuir até 255 caracteres.',
        'email.unique'  => 'Já existe um usuário cadastrado com este E-mail.',

        'password.required'  => '"Senha" é obrigatório.',
        'password.string'  => 'O campo "E-mail" deve ser no formato de texto.',
        'password.min'  => 'A senha deve possuir pelo menos :min caracteres.',
        'password.confirmed'  => 'As senhas não conferem.',
    ];

    protected function validator(array $data)
    {        
        if (!empty($data['id'])) {
            return Validator::make($data, [
                'name' => 'required|string|max:255',
                'password' => (!empty($data['password']) ? 'string|min:6|confirmed' : ''),
                'username' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('users_admin')->ignore($data['id']),
                ],
            ], $this->messages);
        } else {
            return Validator::make($data, [
                'name' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:users_admin',
                'password' => 'required|string|min:6|confirmed',
            ], $this->messages);
        }
    }

    protected function store(array $data)
    {
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        if (!empty($data['id'])) {
            $user_admin = UserAdmin::find($data['id']);
            $user_admin->update($data);
        } else {
            $user_admin = UserAdmin::create($data);
        }
        $roles = !empty($data['roles']) ? $data['roles'] : [];
        $user_admin->syncRoles($roles);
        return $user_admin;
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Busca todos os usuários cadastrados no sistema
        $users = UserAdmin::all();

        // Chama a view
        return view('admin.user.index', compact('users'));
    }

    public function user(Int $id = null) {

        // Busca o usuário
        if (!empty($id)) $user = UserAdmin::find($id);
        $roles = Role::all();
        return view('admin.user.edit', compact('user', 'roles'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        // Valida os campos
        $this->validator($request->all())->validate();

        // Salva os dados
        $this->store($request->all());

        // Retorna a listagem
        return redirect()->route('index.useradmin')->with('message', 'Administrador salvo com sucesso.');
    }

    public function delete($id)
    {
        UserAdmin::find($id)->delete();

        return redirect()->route('index.useradmin')->with('message', 'Administrador excluído com sucesso.');
    }
}