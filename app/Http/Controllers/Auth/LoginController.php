<?php

namespace App\Http\Controllers\Auth;

use App\Admin\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    */

    use AuthenticatesUsers;

    /*  Where to redirect users after login. */
    protected $redirectTo = '/';

    /*  Create a new controller instance. */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        // $this->middleware('guest:admin')->except('logout');
    }

    /* admin login */
    public function showAdminLoginForm()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->intended('/admin');
        }
        $data = Admin::get()->count();
        return view('admin.auth.login', ['url' => 'admin/login', 'admins' => $data]);
    }
}
