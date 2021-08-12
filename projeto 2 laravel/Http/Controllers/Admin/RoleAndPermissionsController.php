<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class RoleAndPermissionsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role_or_permission:Super Admin|grupo-de-usuarios.visualizacao'], ['only' => ['index']]);
        $this->middleware(['role_or_permission:Super Admin|grupo-de-usuarios.cadastro'], ['only' => ['create','store']]);
        $this->middleware(['role_or_permission:Super Admin|grupo-de-usuarios.edicao'], ['only' => ['edit','update']]);
        $this->middleware(['role_or_permission:Super Admin|grupo-de-usuarios.exclusao'], ['only' => ['destroy']]);
    }

    public function index()
    {
        $roles = Role::all();
        return view('admin.roleAndPermissions.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('admin.roleAndPermissions.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        if(!is_null(Role::where('name',$request->roleName)->first())){
            return redirect()->route('admin.roleAndPermissions.create')->with('message', 'Grupo de Usuários já existe.');
        }

        $arx = collect($request->all())->filter(
            function ($value, $key){
                return Str::contains($key,'perm');
            }
        );

        $permissions_id = $arx->values()->toArray();
        $permissions = Permission::find($permissions_id) ;

        $role = Role::create(['guard_name' => 'admin' ,'name' => $request->roleName]);
        $role->email = $request->email;
        $role->syncPermissions($permissions);
        $role->save();
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        return redirect()->route('admin.roleAndPermissions.index');
    }

    public function edit(Request $request, $id)
    {
        if(!isset($id)){
            return redirect()->route('admin.roleAndPermissions.index');
        }

        $role = Role::find($id);
        $activePermissions =$role->permissions;
        $permissions = Permission::all();
        return view('admin.roleAndPermissions.edit',compact('role','permissions','activePermissions'));
    }

    public function update(Request $request, $id)
    {
        if(is_null(Role::find($id)->first())){
            return redirect()->route('admin.roleAndPermissions.create')->with('message', 'Grupo de Usuários não Encontrado.');
        }

        $arx = collect($request->all())->filter(
            function ($value, $key){
                return Str::contains($key,'permiss');
            }
        );

        $permissions_id = $arx->values()->toArray();
        $permissions = Permission::find($permissions_id) ;

        $role = Role::find($id);
        $role->name = $request->roleName;
        $role->email = $request->email;
        $role->status = is_null($request->status) ? 0 : 1  ;
        $role->syncPermissions($permissions);
        $role->save();
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        return redirect()->route('admin.roleAndPermissions.index')->with('message', 'Grupo de Usuários atualizado.');
    }

    public function destroy($id)
    {
        Role::find($id)->delete();
        return redirect()->route('admin.roleAndPermissions.index');
    }
}
