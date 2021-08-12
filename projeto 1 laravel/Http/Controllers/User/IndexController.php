<?php

namespace App\Http\Controllers\User;

class IndexController extends UserBaseController
{
    // Por enquanto vazio

    public function index()
    {
        return view('user.index.index');
    }

}
