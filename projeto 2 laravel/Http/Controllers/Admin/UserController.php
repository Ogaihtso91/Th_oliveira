<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Auth;
use Validator;
use App\User;
use App\Institution;
use App\Theme;
use Carbon\Carbon;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role_or_permission:Super Admin|usuarios-participantes.visualizacao'], ['only' => ['index']]);
        $this->middleware(['role_or_permission:Super Admin|usuarios-participantes.cadastro|usuarios-participantes.edicao'], ['only' => ['restore','user']]);
        $this->middleware(['role_or_permission:Super Admin|usuarios-participantes.exclusao'], ['only' => ['delete']]);
    }

    public function index(Request $request)
    {
        // Busca todos os usuários cadastrados no sistema
        $users = User::withTrashed()->get();
        // Chama a view
        return view('admin.user-front.index', compact('users'));
    }

    public function user(Int $id = null) {

        // Busca o usuário
        if (!empty($id)) $user_obj = User::find($id);

        $institutions = Institution::orderBy('institution_name','asc')->get();

        $themes = Theme::all();

        return view('admin.user-front.edit', compact('user_obj','institutions','themes'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */    

    public function delete($id)
    {
        User::find($id)->delete();

        return redirect()->route('admin.user.index')->with('message', 'Usuário excluído com sucesso.');
    }

    public function restore($id)
    {
        $user = User::withTrashed()->find($id)->restore();

        return redirect()->route('admin.user.index')->with('message', 'Usuário restaurado com sucesso.');
    }
}
