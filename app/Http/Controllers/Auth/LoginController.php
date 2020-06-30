<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use DateTime;
use DateTimeZone;
use DB;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

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
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function authenticated(Request $request, $user)
    {
        $email = $request->get("email");
        $token = $request->get("_token");

        $date = new DateTime(date("Y-m-d H:i:s"), new DateTimeZone('Asia/Taipei'));
        $currenttime = $date->format('Y-m-d H:i:s');

        DB::update("UPDATE users SET remember_token = '$token', lastlogintime = '$currenttime' WHERE email = '$email'");
    }
}