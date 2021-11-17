<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use DB;
use Auth;
use App\User;
use App\Admin\Admin;
use App\UserPermission;
use App\PermissionRequest;
use App\Documentgroup;
use Illuminate\Support\Facades\Validator;

class UserManagementController extends Controller
{
    public function index()
    {
        $data = Admin::get()->count();
        return view('admin.usermanager', ['admins' => $data]);
    }
    public function changeAccess(Request $data)
    {
        $tmp = $data->all()['params'];
        $t = 0;
        foreach ($tmp["add"] as $key => $value) {
            $permission = new UserPermission;
            $permission->fill([
                'usercode' => $tmp["user"],
                'access_code' => $value
            ]);
            try {
                $permission->save();
                $t++;
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }
        unset($tmp);
        return $t;
    }

    //remove access from users
    public function removeAccess(Request $data)
    {
        $tmp = $data->all();
        $userAccess = UserPermission::where("usercode", $tmp['usercode'])->where("access_code", $tmp['access_code'])->delete();
        unset($tmp);
        return $userAccess;
    }

    //owner group manager
    public function ownerGroupIndex()
    {
        $data = Admin::get()->count();
        return view('admin.ownermanager', ['admins' => $data]);
    }

    public function getRelevantData()
    {
        $data['users'] = User::where('is_active', 1)->where('is_owner', 0)->get()->toArray();
        $data['owner_group'] = User::where('is_active', 1)->where('is_owner', 1)->get()->toArray();
        $users = User::select('owner_group')->where('is_active', 1)->where('is_owner', 1)->get()->toArray();
        $data['no_owner'] = Documentgroup::whereNotIn('group_code', $users)->get()->toArray();
        unset($users);
        return $data;
    }

    //create new owner
    public function createOwner(Request $data)
    {
        $validator = Validator::make(
            $data->all(),
            [
                "usercode" => ['bail', 'string', 'required', 'min:4', 'unique:users'],
                "name" => ['required', 'string', 'max:191'],
                "email" => ['bail', 'string', 'required', 'email', 'unique:users', 'max:191'],
                "password" => ['required', 'string', 'min:6', 'confirmed'],
                "owner_group" => ['required', 'string', 'unique:users']
            ],
            [
                "usercode.unique" => "Usercode is already taken",
                "email.unique" => "Email addess is already taken",
                "owner_group.required" => "Owner group is mandatory",
                "owner_group.unique" => "One user already owner of this group"
            ]
        );
        $validator->validate();
        if ($validator->fails()) {
            return $validator->errors();
        } else {
            try {
                $user = User::create([
                    'usercode' => $data['usercode'],
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                    'is_owner' => 1,
                    'owner_group' => $data['owner_group']
                ]);
                if ($user->id != 0) {
                    try {
                        if (UserPermission::where('usercode', $user->usercode)->where('access_code', $data['owner_group'])->exists() == false) {
                            UserPermission::create([
                                'usercode' => $user->usercode,
                                'access_code' => $data['owner_group']
                            ]);
                        }
                    } catch (Exception $e) {
                        return $e->getMessage();
                    }
                }
                return $user->id;
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }
    }
    /*remove an owner*/
    public function removeOwner(Request $data)
    {
        $user = User::find($data->id);
        $user->is_owner = 0;
        $user->owner_group = null;
        $user->save();
        return $data->id;
    }
    /*make a user an owner*/
    public function makeUserOwner(Request $data)
    {
        $user = User::find($data->id);
        $user->is_owner = 1;
        $user->owner_group = $data->group_code;
        try {
            $user->save();
            try {
                if (UserPermission::where('usercode', $user->usercode)->where('access_code', $data['owner_group'])->exists() == false) {
                    UserPermission::create([
                        'usercode' => $user->usercode,
                        'access_code' => $data->group_code
                    ]);
                }
            } catch (Exception $e) {
                return $e->getMessage();
            }
            return $data->id;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    /*permission control*/
    public function permissionAction(Request $data)
    {
        if (Auth::guard('admin')->check()) {
            $pr = PermissionRequest::findOrFail($data->id);
            try {
                $pr->status = $data->status;
                $pr->save();
                if ($data->status == 'Approved') {
                    UserPermission::create([
                        'usercode' => $pr->usercode,
                        'access_code' => $pr->access_code
                    ]);
                    return $pr->id;
                } else {
                    return 0;
                }
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }
    }
}
