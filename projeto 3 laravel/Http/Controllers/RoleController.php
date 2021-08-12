<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
//Importing laravel-permission models
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Session;
use App\Enums\Permissoes;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'isAdmin']);//isAdmin middleware lets only users with a //specific permission permission to access these resources
        $this->middleware(['permission:'.Permissoes::AdministrarPapeisPermissoesUsuarios['id']]);
    }


    public function index()
    {
        $roles = Role::all();//Get all roles
        return view('roles.index')->with('roles', $roles);
    }


    public function create()
    {
        $adminPermissions = Permission::where('is_admin_permission', 1)->get();
        $userPermissions = Permission::where('is_admin_permission', 0)->orWhereNull('is_admin_permission')->get();

        return view('roles.create')->with(compact('adminPermissions', 'userPermissions'));
    }


    public function store(Request $request)
    {
        //Validate name and permissions field
        $messages = [
            'name.required' => 'Por favor, informe o campo "Nome do grupo de usuários".',
            'name.unique' => 'Já existe um grupo de usuários usando este nome, tente outro nome.',
            'permissions.required' => 'Por favor, selecione pelo menos uma permissão.',
        ];

        $this->validate(
            $request,
            [
                'name'=>'required|unique:roles|max:30',
                'permissions' =>'required',
            ],
            $messages
        );

        $name = $request['name'];
        $role = Role::create(['name' => $name]);

        $permissions = $request['permissions'];

        //Looping thru selected permissions
        foreach ($permissions as $key => $permission) {
            $p = Permission::find($key);
            //Fetch the newly created role and assign permission
            $role = Role::where('name', '=', $name)->first();
            $role->givePermissionTo($p);
        }

        return redirect()->route('roles.index')
            ->with(
                'message',
                'Papel '. $role->name.' adicionado com sucesso.'
            );
    }


    public function show($id)
    {
        return redirect('roles');
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::all();
        $adminPermissions = Permission::where('is_admin_permission', 1)->get();
        $userPermissions = Permission::where('is_admin_permission', 0)->orWhereNull('is_admin_permission')->get();

        return view('roles.edit')->with(compact('role', 'userPermissions', 'adminPermissions', 'permissions'));
    }


    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);//Get role with the given id
        //Validate name and permission fields
        $messages = [
            'name.required' => 'Por favor, informe o campo "Nome do grupo de usuários".',
            'name.unique' => 'Já existe um grupo de usuários usando este nome, tente outro nome.',
            'permissions.required' => 'Por favor, selecione pelo menos uma permissão.',
        ];

        $this->validate($request, [
            'name'=>'required|max:30|unique:roles,name,'.$id,
            'permissions' =>'required',
        ], $messages);

        $input = $request->except(['permissions']);
        $permissions = $request['permissions'];
        $role->fill($input)->save();

        $p_all = Permission::all();//Get all permissions

        foreach ($p_all as $p) {
            $role->revokePermissionTo($p); //Remove all permissions associated with role
        }

        foreach ($permissions as $key => $permission) {
            $p = Permission::find($key);
            $role->givePermissionTo($p);  //Assign permission to role
        }

        return redirect()->route('roles.index')
            ->with(
                'message',
                'Papel '. $role->name.' atualizado com sucesso.'
            );
    }


    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return redirect()->route('roles.index')
            ->with(
                'message',
                'Papel excluído com sucesso.'
            );
    }
}
