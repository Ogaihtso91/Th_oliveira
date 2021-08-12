<?php

namespace App\Http\Controllers\User;

use App\Repositories\UserRepository;
use Illuminate\Http\Request;


class UserController extends UserBaseController
{
    private $userRepository;
    public function __construct(UserRepository $userRepository)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
    }
    public function edit(Request $r)
    {
        $this->setMeta('page_title', 'Minha Conta');

        if($r->isMethod('post')){
            try {
                if($this->userRepository->updateMyAccount( $this->user, $r )){
                    return redirect()->back()->with('success', 'Usuário Salvo com Sucesso!');
                }
            } catch(\Exception $e) {
                return redirect()->back()->withInput()->with('error', $e->getMessage());
            }
        }
        $user = $this->user;

        return view('user.user.edit', compact('user'));
    }
}
