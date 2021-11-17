<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\PermissionList;
use App\UserPermission;
use Auth;

class UserPermissionController extends Controller
{
    public function getUserPermissions()
    {
        $usercode = Auth::user()->usercode;
        $accessList = UserPermission::where('usercode', $usercode)->get()->values()->toArray();
        return $accessList;
    }

    // public function changeAccess(Request $data)
    // {
    //     dd($data->all());
    // }
}
