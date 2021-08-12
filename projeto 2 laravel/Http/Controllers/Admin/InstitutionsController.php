<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use App\Institution;
use Auth;
use Validator;

class InstitutionsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['role_or_permission:Super Admin|instituicoes.visualizacao'], ['only' => ['index']]);
        $this->middleware(['role_or_permission:Super Admin|instituicoes.cadastro|instituicoes.edicao'], ['only' => ['institution']]);
        $this->middleware(['role_or_permission:Super Admin|instituicoes.exclusao'], ['only' => ['delete']]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Busca todos os usuários cadastrados no sistema
        $institutions = Institution::all();

        // Chama a view
        return view('admin.institution.index', compact('institutions'));
    }

    public function institution(Int $id = null) {

        // Busca o usuário
        if (!empty($id)) $institution = Institution::find($id);

        return view('admin.institution.edit', compact('institution'));
    }

    public function delete($id)
    {
        Institution::find($id)->delete();

        return redirect()->route('admin.institution.index')->with('message', 'Instituição excluída com sucesso.');
    }
}