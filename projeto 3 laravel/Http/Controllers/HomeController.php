<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Enums\Permissoes;

class HomeController extends Controller
{

	public function __construct() {
        $this->middleware(['auth']);//isAdmin middleware lets only users with a //specific permission permission to access these resources
        $this->middleware(['permission:'.Permissoes::LerComiteDeInvestimentos['id'].'|'.Permissoes::EditarAdministrarUsuarios['id']]);
    }

    function index(){


        //Redireciona o usuário com Permissão "Ler Curador"
        if ( Auth::user()->hasPermissionTo(1) ) {
            return redirect()->route('user.conselho-curador.conselho-curador');
        }

        //Redireciona o usuário com Permissão "Ler Fiscal"
        if ( Auth::user()->hasPermissionTo(2) ) {
            return redirect()->route('user.conselho-fiscal.conselho-fiscal');
        }

        //Redireciona o usuário com Permissão "Ler Comitê de Investimentos"
        if ( Auth::user()->hasPermissionTo(3) ) {
            return redirect()->route('user.comite-investimentos.comite-investimentos');
        }

        //Redireciona o usuário com Permissão "Administrar papéis, permissões e usuários" ou "Editar e administrar usuários"
        if ( Auth::user()->hasAnyPermission([4,6])) {
            return redirect()->route('admin.conselho-curador');
        }


    }

}

