<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Auth;
use Adldap\Laravel\Facades\Adldap;

// use Auth;

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

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin/timeline';

    protected function guard()
    {
        return Auth::guard('admin');
    }

    public function username()
    {
        return 'username';
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
     * Handle an authentication attempt.
     * This function override the driver default function.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return Response
     */
    // protected function attemptLogin(Request $request)
    // {
    //     $userLdap = Adldap::search()->users()->find($request->get($this->username()));
    //     // Verifica se o usuário e a senha
    //     $_username = $request->get($this->username());
    //     $_password = $request->get("password");
    //     if ($userLdap) {
    //         // Se usuário existir no LDAP, tenta validar a senha
    //         // Isso vai impedir duplicatas locais de acessarem, mas
    //         // vai dar mais garantia para os usuários do AD, caso eles existam.
    //         if (Adldap::auth()->attempt($_username, $_password, $bindAsUser = true) === false) {
    //             // Se o usuário existir no LDAP e não puder ser autenticado, retorna falso;
    //             return false;
    //         }
    //         if (env('LDAP_USE_GROUP_VALIDATION', false)) {
    //             // verifica se está em um grupo
    //             $groups = explode(',', env('LDAP_GROUP_ALLOWED', ''));
    //             $inGroup = false;
    //             foreach ($groups as $group) {
    //                 if ($userLdap->inGroup(trim($group))) {
    //                     $inGroup = true;
    //                 }
    //             }
    //             // Se a validação por grupo estiver habilitada, retorna falso
    //             // caso o usuário não esteja no grupo
    //             if ($inGroup === false) {
    //                 return false;
    //             }
    //         }
    //         $user = new \App\UserAdmin([
    //             'name' => $userLdap->getCommonName(),
    //             'objectguid' => $userLdap->getConvertedGuid(),
    //             'username' => $request->get($this->username()),
    //             'password' => bcrypt($request->get('password')),
    //         ]);

    //         $user->assignRole('Super Admin');
    //         dd($user);
    //         $user->save();
    //         // tem que salvar ele e dar uma permissão genérica
    //         // Em sequência, mantém o fluxo normal, uma vez que o usuário existe.
    //     }
    //     exit("PASSOU!");

    //     return $this->guard()->attempt(
    //         $this->credentials($request),
    //         $request->filled('remember')
    //     );
    // }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        // Se o usuário possuir o atributo objectguid ele veio do LDAP
        if($user->objectguid !== null){
            // Atribui a permissão
            $user->assignRole(env('LDAP_DEFAUL_USER_ROLE', 'Super Admin'));
            $user->save();
        }

    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        if (!empty($this->guard()->user()->id)) {
            return redirect()->route('admin.index');
        }

        return view('adminlte::login');
    }

    /**
     * The user has logged out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    protected function loggedOut(Request $request)
    {
        return redirect()->route('admin.login');
    }
}
