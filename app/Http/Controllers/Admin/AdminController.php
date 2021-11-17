<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use App\Admin\Admin;
use App\Documentstatus;

class AdminController extends Controller
{
    use AuthenticatesUsers;
    public function __construct()
    {
        if(!Documentstatus::where('status_code','APPROVED')->exists()){
            Documentstatus::create([
                "status_code" => "APPROVED","status_desc"=>"Approved"
            ]);
        }
        if(!Documentstatus::where('status_code','REJECTED')->exists()){
            Documentstatus::create([
                "status_code" => "REJECTED","status_desc"=>"Rejected"
            ]);
        }
        if(!Documentstatus::where('status_code','CREATED')->exists()){
            Documentstatus::create([
                "status_code" => "CREATED","status_desc"=>"Created"
            ]);
        }
    }

    public function index()
    {
        $data['admins'] = Admin::get()->count();

        // if(Auth::guard('admin')->check()){
        //     $data['myself'] = Admin::find(Auth::guard('admin')->user()->id)->get()->toArray();
        // }
        return view('admin.admin', $data);
    }

    public function getAdminInfo()
    {
        return Auth::guard('admin')->user();
    }
    /* admin register */
    public function showAdminRegisterForm()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->intended('/admin');
        }
        $data = Admin::get()->count();
        if ($data == 0) {
            return view('admin.auth.register', ['url' => 'admin/register', 'admins' => $data]);
        } else {
            return redirect('/admin');
        }
    }

    public function adminLogin(Request $request)
    {
        $this->validate($request, [
            'usercode'   => 'required|string|min:4',
            'password' => 'required|min:6'
        ]);

        if (Auth::guard('admin')->attempt(['usercode' => $request->usercode, 'password' => $request->password], $request->get('remember'))) {
            //  return Auth::guard('admin');
            return redirect()->intended('/admin');
        }
        return back()->withInput($request->only('usercode', 'remember'));
    }

    /*admin validator */
    protected function validator($data)
    {
        return Validator::make(
            $data,
            [
                'usercode' => ['required', 'string', 'min:4', 'unique:admins'],
                'name' => ['required', 'string', 'max:191'],
                'email' => ['required', 'string', 'email', 'max:191', 'unique:admins'],
                'password' => ['required', 'string', 'min:6', 'confirmed']
            ],
            [
                'usercode.required' => 'Username field is required'
            ]
        );
    }
    protected function createAdmin(Request $request)
    {
        $valid = $this->validator($request->all());
        $valid->validate();
        if ($valid->fails()) {
            return $valid->errors();
        } else {
            $admin = Admin::create([
                'name' => $request['name'],
                'usercode' => $request['usercode'],
                'email' => $request['email'],
                'password' => Hash::make($request['password']),
            ]);
            return redirect()->intended('admin/login');
        }
    }

    public function adminLogout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect('/admin');
    }

    /* admin profile update*/
    public function updateProfile(Request $data)
    {
        $admin = Admin::find($data->id);
        if ($data->attr == 'name') {
            $admin->name = $data->value;
        } else if ($data->attr = 'email') {
            $admin->email = $data->value;
        }
        $admin->save();
        return 1;
    }
    /* update old password */
    public function updatePassword(Request $data)
    {
        $hashedPass = Auth::guard('admin')->user()->password;
        $validator = Validator::make(
            $data->all(),
            [
                "oldPass" => ["required", "min:6", "string"],
                "newPass" => ["required",  "min:6", "string"],
            ],
            [
                "oldPass.required" => "Please enter old password",
                "oldPass.min" => "Passwords are minimum of 6 characters",
                "newPass.required" => "Please enter a new password",
                "newPass.min" => "Passwords are minimum of 6 characters"
            ]
        );
        $validator->validate();
        if ($validator->fails()) {
            return $validator->errors();
        } else {
            if (Hash::check($data->oldPass, $hashedPass)) {
                $admin = Admin::findOrFail($data->id);
                unset($hashedPass);
                try {
                    $admin->password = Hash::make($data->newPass);
                    $admin->save();
                    $this->adminLogout($data);
                    unset($admin);
                    return $data->id;
                } catch (Exception $e) {
                    return $e->getMessage();
                }
            } else {
                return response()->json("123", 422);
            }
        }
    }
}
