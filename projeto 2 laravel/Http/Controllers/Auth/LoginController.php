<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Auth;
use App\User;
use Carbon\Carbon;

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
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except(['logout', 'ajax']);
    }

    public function showLoginForm(Request $request)
    {
        $redirect_url = $request->url();
        if(!empty($request->redirect_url)){
            $redirect_url = $request->redirect_url;
        }
        return view('auth.login', compact('redirect_url'));
    }

    public function ajax(Request $request)
    {
        if ($this->login($request)) {

            //Grava no BD a data e a hora em o usuário realizou o login
            $user = Auth::guard()->user();
            $user->last_login = Carbon::now();
            $user->save();

            // Check if action of login is comment
            if ($request->get('action') == 'comment') {

                // Check required fields
                $data_request = $request->all();
                if (empty($data_request['comment_item_id'])
                    || empty($data_request['comment_item_type'])
                    || empty($data_request['comment_content'])) {
                    return response()->json(['login'=>'comment_error']);
                }

                if (!Auth::guard()->user()->commentItem($data_request['comment_item_type'], [
                    'item_id' => $data_request['comment_item_id'],
                    'comment_id' => $data_request['comment_parent_id'],
                    'content' => $data_request['comment_content'],
                ])) {
                    return response()->json(['login'=>'comment_error']);
                }

                // Authentication passed...
                return response()->json(['login'=>'comment_ok']);
            }

            // Authentication passed...
            return response()->json(['login'=>'ok']);
        }
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->middleware('auth');

        $this->guard()->logout();

        $request->session()->invalidate();

        return $this->loggedOut($request) ? redirect($request->input('redirect', route('front.home'))) : redirect()->back();
    }
}
