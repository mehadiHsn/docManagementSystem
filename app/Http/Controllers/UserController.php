<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Auth;
use Exception;
use App\User;
use App\UserPermission;
use App\PermissionList;
use App\ChangeRequest;
use App\PermissionRequest;

class UserController extends Controller
{
	public function __construct()
	{ }
	public function index()
	{
		if (!Auth::check()) {
			return abort(401);
		}
		return view('settings');
	}
	public function getUserInfo()
	{
		return Auth::user();
	}
	/* user profile update*/
	public function updateProfileGet()
	{
		if (!Auth::check()) {
			return abort(401);
		}
	}
	public function updateProfile(Request $data)
	{
		$user = User::find($data->id);
		if ($data->attr == 'name') {
			$user->name = $data->value;
		} else if ($data->attr = 'email') {
			$user->email = $data->value;
		}
		try {
			$user->save();
			return $data->id;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}
	/* update old password */
	public function updatePassword(Request $data)
	{
		$hashedPass = Auth::user()->password;
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
				$user = User::findOrFail($data->id);
				unset($hashedPass);
				try {
					$user->password = Hash::make($data->newPass);
					$user->save();
					unset($user);
					Auth::logout();
					return $data->id;
				} catch (Exception $e) {
					return $e->getMessage();
				}
			} else {
				return response()->json("123", 422);
			}
		}
	}

	/* user permission request */

	public function getCurrentRequest()
	{
		if (!Auth::check()) {
			return abort(401);
		}
		return PermissionRequest::where('usercode', Auth::user()->usercode)->orderBy('created_at')->get()->values();
	}

	public function makePermissionRequest(Request $data)
	{
		if (Auth::check()) {
			if (PermissionRequest::where('usercode', Auth::user()->usercode)->where('access_code', $data->access_code)->exists() == false) {
				try {
					PermissionRequest::create([
						'usercode' => Auth::user()->usercode,
						'access_code' => $data->access_code,
						'status' => 'Not Approved',
						'comment' => $data->comment
					]);
					return 1;
				} catch (Exception $e) {
					return $e->getMessage();
				}
			} else {
				return response()->json("111", 422);
			}
		} else {
			return abort(401);
		}
	}

	public function deletePermissionRequest(Request $data)
	{
		$tmp = PermissionRequest::findOrFail($data->id);
		try {
			$tmp->delete();
			return $data->id;
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}
}
