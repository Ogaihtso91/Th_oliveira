<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;

//Importing laravel-permission models
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use Session;

class PermissionController extends Controller {

    public function __construct() {
        $this->middleware(['auth', 'isAdmin']); //isAdmin middleware lets only users with a //specific permission permission to access these resources
    }


    public function index() {
        $permissions = Permission::all(); //Get all permissions
        return view('permissions.index')->with('permissions', $permissions);
    }


    public function create() {
        $roles = Role::get(); //Get all roles

        return view('permissions.create')->with('roles', $roles);
    }


    public function store(Request $request) {

        $messages = [
            'name.required' => 'Campo "Nome da permissão" em branco.'];
        $this->validate($request, [
            'name'=>'required|max:40',],
        $messages);

        $name = $request['name'];
        $permission = new Permission();
        $permission->name = $name;

        $roles = $request['roles'];

        $permission->save();

        if (!empty($request['roles'])) { //If one or more role is selected
            foreach ($roles as $role) {
                $r = Role::where('id', '=', $role)->firstOrFail(); //Match input role to db record

                $permission = Permission::where('name', '=', $name)->first(); //Match input //permission to db record
                $r->givePermissionTo($permission);
            }
        }

        return redirect()->route('permissions.index')
            ->with('message',
             'Permissão '. $permission->name.' adicionada com sucesso.');

    }


    public function show($id) {
        return redirect('permissions');
    }


    public function edit($id) {
        $permission = Permission::findOrFail($id);

        return view('permissions.edit', compact('permission'));
    }


    public function update(Request $request, $id) {
        $permission = Permission::findOrFail($id);
        $messages = [
            'name.required' => 'Campo "Nome da permissão" em branco.',
            'name.max' => 'Máximo de 50 caracteres permitido no campo "Nome da permissão".'

        ];
        $this->validate($request, [
            'name'=>'required|max:50',],
        $messages);
        $input = $request->all();
        $permission->fill($input)->save();

        return redirect()->route('permissions.index')
            ->with('message',
             'Permissão '. $permission->name.' atualizada com sucesso.');

    }


    public function destroy($id) {
        $permission = Permission::findOrFail($id);

    //Make it impossible to delete this specific permission
    if ($permission->name == "Administrar papeis & permissoes") {
            return redirect()->route('permissions.index')
            ->with('message',
             'Permissão não pode ser excluída');
        }

        $permission->delete();

        return redirect()->route('permissions.index')
            ->with('message',
             'Permissão excluída com sucesso.');

    }
}
