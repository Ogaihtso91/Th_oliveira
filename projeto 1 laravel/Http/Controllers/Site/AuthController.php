<?php
namespace App\Http\Controllers\Site;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use \App\Repositories\UserRepository;
use \App\Repositories\ServiceProviderRepository;
use \App\Repositories\TypeProviderRepository;
use Illuminate\Support\Facades\Auth;


class AuthController extends SiteBaseController
{
    private $userRepository;
    private $serviceProviderRepository;
    private $typeProviderRepository;
    
    public function __construct(
        UserRepository $userRepository,
        ServiceProviderRepository $serviceProviderRepository,
        TypeProviderRepository $typeProviderRepository
        )
    {
        $this->userRepository = $userRepository;
        $this->serviceProviderRepository = $serviceProviderRepository;
        $this->typeProviderRepository = $typeProviderRepository;
    }
    public function login(Request $r)
    {
        Auth::logout();

        if($r->isMethod('post')) {
            $data = $r->all();
            try {
                $user = $this->userRepository->login($data['email'], $data['password'], isset($data['keep_loggedin']) );
                if($user) {
                    $user = Auth::user();
                    return redirect()->route( $user->is_service_provider ? 'serviceProvider.index' : 'user.index')->with('success', 'Bem vindo!');
                } else {
                    return redirect()->route('site.login')->withInput($data)->with('error', 'Usuário ou senha Inválidos');
                }
            } catch(\Exception $e) {
                return redirect()->route('site.login')->withInput($data)->with('error', $e->getMessage());
            }
        }

        return view('site.auth.login');
    }

    public function logout()
    {
        return redirect()->route('site.login')->with('message','Você foi deslogado com sucesso');
    }

    public function signin(Request $r)
    {
        Auth::logout();

        if($r->isMethod('post')){
            $data = $r->all();
            try {
                $user = $this->userRepository->createUser( $data );
                if($user && $user->user_type == 'P'){
                    $serviceProvider = $this->serviceProviderRepository->create($data['service_provider'], $user->id);
                    $this->userRepository->update($user, ['service_provider_id' => $serviceProvider->id]);
                }
                if($this->userRepository->login($data['email'], $data['password'])){
                    if($user->is_service_provider) {
                        return redirect()->route('serviceProvider.index')->with('success','Bem vindo!');
                    } else {
                        return redirect()->route('user.index')->with('success','Bem vindo!');
                    }
                } else {
                    throw new \Exception('Erro ao se logar');
                }

            } catch(\Exception $e) {
                return redirect()->back()->withInput($data)->with('error', $e->getMessage());
            }
        }

        $typeProviders = $this->typeProviderRepository->getList();
        return view('site.auth.signin', compact('typeProviders'));
    }
    
}