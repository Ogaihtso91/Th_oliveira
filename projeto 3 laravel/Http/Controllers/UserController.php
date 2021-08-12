<?php

namespace App\Http\Controllers;

use DB;

use Auth;
use Session;
use App\User;
use App\Enums\CargosFbb;
use Illuminate\Http\Request;

//Importing laravel-permission models
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Enums\Permissoes;

//Enables us to output flash messaging
use App\Http\Requests\StoreUser;
use App\Http\Requests\UpdateUser;

class UserController extends Controller {

    private $paginate = 10;

    public function __construct() {
        $this->middleware(['auth']); //isAdmin middleware lets only users with a //specific permission permission to access these resources
        $this->middleware(['permission:'.Permissoes::AdministrarPapeisPermissoesUsuarios['id'].'|'.Permissoes::EditarAdministrarUsuarios['id']]);
    }


    public function index()
    {
        //Get all users and pass it to the view
        $users = User::orderby('name','asc')->paginate($this->paginate);
        return view('users.index')->with('users', $users);
    }


    public function create()
    {
        # Recupera todos os cargos
        $cargosFbb = CargosFbb::toSelectArray();

        //Get all roles and pass it to the view
        $roles = Role::get();

        return view('users.create', [
            'roles' => $roles,
            'cargosFbb' => $cargosFbb
        ]);
    }


    public function store(StoreUser $request)
    {
        $dados = $request->all();

        # Implode a coluna de cargos por id e vírgula.
        $dados['cargo_FBB'] = implode(',', $dados['cargo_FBB']);

        if ($request->hasfile('foto')) {
            $imagem = $request->file('foto');
            $num = rand(1111,9999);
            $dir = "img/humanograma";
            $ext = $imagem->guessClientExtension();
            $nomeimagem = "imagem_" . $num . "." . $ext;
            $imagem->move($dir,$nomeimagem);
            $caminho = $dir . "/" . $nomeimagem;
            $dados['foto'] = $caminho;
        }

        //$user = User::create($request->only('email','name', 'password', 'cargo', 'telefone')); //Retrieving only the email and password

        $user = User::create($dados);

        $roles = $dados['roles'];

        if (isset($roles)) {
            $user->roles()->sync($roles);  //If one or more role is selected associate user to roles
        }

        // $roles = $request['roles']; //Retrieving the roles field
        // //Checking if a role was selected
        // if (isset($roles)) {
        //     foreach ($roles as $role) {
        //         $role_r = Role::where('id', '=', $role)->firstOrFail();
        //         $user->assignRole($role_r); //Assigning role to user
        //     }
        // }
        //Redirect to the users.index view and display message
        return redirect()
            ->route('users.index')
            ->with('message', 'Usuário "' . $user->name . '" adicionado com sucesso.');
    }

    public function show($id) {
        return redirect('users');
    }


    public function edit($id)
    {
        $user = User::findOrFail($id); //Get user with specified id

        $cargosFbb = CargosFbb::toSelectArray();
        $cargosFbbSelected = CargosFbb::getByIds($user->cargo_FBB);

        $user = User::findOrFail($id); //Get user with specified id
        $roles = Role::get(); //Get all roles

        return view('users.edit', compact('user', 'roles', 'cargosFbb', 'cargosFbbSelected')); //pass user and roles data to view

    }

    public function update(UpdateUser $request, $id) {

        $user = User::findOrFail($id); //Get role specified by id

        $dados = $request->all();

        # Implode a coluna de cargos por id e vírgula.
        $dados['cargo_FBB'] = implode(',', $dados['cargo_FBB']);

        if ($request->hasfile('foto')) {

            //****Exclui a foto antiga****
            $user = User::findOrFail($id);

            if(!empty($user->foto)) unlink($user->foto);
            //****************************
            $imagem = $request->file('foto');
            $num = rand(1111,9999);
            $dir = "img/humanograma";
            $ext = $imagem->guessClientExtension();
            $nomeimagem = "imagem_" . $num . "." . $ext;
            $imagem->move($dir,$nomeimagem);
            $caminho = $dir . "/" . $nomeimagem;
            $dados['foto'] = $caminho;
            User::find($id)->update($dados);
        }

        $roles = $request['roles']; //Retreive all roles
        $user->fill($dados)->save();

        if (isset($roles)) {
            $user->roles()->sync($roles);  //If one or more role is selected associate user to roles
        } else {
            $user->roles()->detach(); //If no role is selected remove exisiting role associated to a user
        }

        return redirect()->route('users.index')
            ->with('message',
             'Usuário "' . $user->name . '" atualizado com sucesso.');
    }


    public function destroy($id) {
    //Find a user with a given id and delete

        $user = User::findOrFail($id);
        $caminho = $user['foto'];

        if (filled($user->foto)){

            unlink($caminho);

        }

        $user->delete();
        return redirect()->route('users.index')
            ->with('message',
             'Usuário "' . $user->name . '" excluído com sucesso.');
    }


     public function indexPerfil() {

        $user = Auth::guard()->user();
        $roles = $user->getRoleNames()->implode('');
        return view('users.perfil.index')->with('user', $user);
    }


    public function editarPerfil() {

        $user = Auth::guard()->user();
        //$roles = Role::get(); //Get all roles
        return view('users.perfil.editar', compact('user')); //pass user and roles data to view

    }

    public function atualizarPerfil(Request $request, $id) {

        $user = User::findOrFail($id); //Get role specified by id

        $messages = [
            'name.required' => 'Campo "Nome" em branco.',
            'cargo.required' => 'Campo "Cargo" em branco.',
            'cargo.max' => 'Máximo de 40 caracteres permitido no campo "Cargo".',
            'cargo_FBB.required' => 'Campo "Cargo FBB" em branco.',
            'cargo_FBB.max' => 'Máximo de 40 caracteres permitido no campo "Cargo FBB".',
            'telefone.required' => 'Campo "Telefone fixo" em branco.',
            'telefone_celular.required' => 'Campo "Telefone celular" em branco.',
            'password.required' => 'Campo "Senha" em branco.',
            'password.min' => 'A senha tem que ter no mínimo 6 caracteres.',
            'password.confirmed' => 'As senhas que você digitou são diferentes.',
            'foto.mimes' => 'Extensão não permitida no campo "Foto".',
            'foto.max' => 'Tamanho máximo permitido da imagem é 1MB'

        ];
    //Validate name, email and password fields
        $this->validate($request, [
            'name'=>'required|max:120',
            'cargo'=>'required|max:40',
            'cargo_FBB'=>'required|max:40',
            'telefone'=>'required',
            'telefone_celular'=>'required',
            //'email'=>'required|email|unique:users,email,'.$id,
            'password'=>'required|min:6|confirmed',
            'foto' => 'mimes:jpeg,bmp,png|max:1000',
        ], $messages);

        $dados = $request->all();

        if ($request->hasfile('foto')) {

            //****Exclui a foto antiga****
            if(!empty($user->foto)) unlink($user->foto);
            //****************************
            $imagem = $request->file('foto');
            $num = rand(1111,9999);
            $dir = "img/humanograma";
            $ext = $imagem->guessClientExtension();
            $nomeimagem = "imagem_" . $num . "." . $ext;
            $imagem->move($dir,$nomeimagem);
            $caminho = $dir . "/" . $nomeimagem;
            $dados['foto'] = $caminho;
            User::find($id)->update($dados);

        }

        //$input = $request->only(['name','password', 'cargo', 'telefone']); //Retreive the name, email and password fields , retirado ='password'
        //$roles = $request['roles']; //Retreive all roles
        //$user->fill($input)->save();

        User::find($id)->update($dados);

        //if (isset($roles)) {
        //    $user->roles()->sync($roles);  //If one or more role is selected associate user to roles
        //}
        //else {
        //    $user->roles()->detach(); //If no role is selected remove exisiting role associated to a user
        //}
        return redirect()->route('users.perfil.index')
        ->with('message',
           'Perfil atualizado com sucesso.');
    }

    public function humanograma() {


        $users = User::role(['Conselho Fiscal', 'Conselho Curador', 'Comitê de Investimentos', 'Secex'])->orderby('name','asc')->get();

        $roles = Role::whereNotIn('name',['Admin'])->get();

        //$users = DB::table('users')
        //    ->select('*')
        //    ->join('model_has_roles', 'model_id', '=', 'users.id')
        //    ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
        //
        //    ->get();

        //$user->getRoleNames()
        return view('users.humanograma.index', compact('users', 'roles'));

    }

}


