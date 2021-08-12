<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    protected function login(Request $request)
    {
        $this->validateLogin($request);

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        if (!$user = Auth::attempt(['email' => $request->email, 'password' => $request->password, 'acesso' => 1])) {
            $this->incrementLoginAttempts($request);

            return redirect()->back()->withErrors([
                'invalid' => 'Esta conta parece não existir ou está inativa, Contate o administrador do sistema.'
            ]);

        } else {
            return $this->authenticated($request, $user);
        }

    }

    protected function authenticated(Request $request, $user)
    {
        //Redireciona o usuário com Permissão "Ler Curador"
        if ( Auth::user()->hasPermissionTo(1)) {
            return redirect()->route('user.conselho-curador.conselho-curador');
        }

        //Redireciona o usuário com Permissão "Ler Fiscal"
        if ( Auth::user()->hasPermissionTo(2)) {
            return redirect()->route('user.conselho-fiscal.conselho-fiscal');
        }

        //Redireciona o usuário com Permissão "Ler Comitê de Investimentos"
        if ( Auth::user()->hasPermissionTo(3)) {
            return redirect()->route('user.comite-investimentos.comite-investimentos');
        }

        //Redireciona o usuário com Permissão "Administrar papéis, permissões e usuários" ou "Editar e administrar usuários"
        if ( Auth::user()->hasAnyPermission([4,6])) {
            return redirect()->route('admin.conselho-curador');
        }
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            $this->username() => [trans('Login ou senha inválido')],
        ]);
    }

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
