<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Validator;
use App\Documentgroup;
use App\Documenttype;
use App\Documentstatus;
use App\PermissionList;
use App\Admin\Admin;

class UpdateoldController extends Controller
{
    public function index()
    {
        $data = Admin::get()->count();
        return view('admin.updateold', ['admins' => $data]);
    }

    public function updategroup(Request $data)
    {
        $isValid = $this->checkData($data, 'update-group');
        $isValid->validate();
        if ($isValid->fails()) {
            return $isValid->errors();
        } else {
            try {
                $group = Documentgroup::find($data->id);
                $group->group_desc = $data->group_desc;
                $group->save();
                return $group->id;
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }
    }

    public function updatetype(Request $data)
    {
        $isValid = $this->checkData($data, 'update-type');
        $isValid->validate();
        if ($isValid->fails()) {
            return $isValid->errors();
        } else {
            try {
                $type = Documenttype::find($data->id);
                $type->type_code = $data->type_code;
                $type->save();
                return $type->id;
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }
    }

    public function updatestatus(Request $data)
    {
        $isValid = $this->checkData($data, 'update-status');
        $isValid->validate();
        if ($isValid->fails()) {
            return $isValid->errors();
        } else {
            try {
                $status = Documentstatus::find($data->id);
                $status->status_desc = $data->status_desc;
                $status->save();
                return $status->id;
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }
    }

    public function updatePermissionList(Request $data)
    {
        $isValid = $this->checkData($data, 'update-permission');
        $isValid->validate();
        if ($isValid->fails()) {
            return $isValid->errors();
        } else {
            try {
                $access = PermissionList::find($data->id);
                $access->access_desc = $data->access_desc;
                $access->save();
                return $access->id;
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }
    }

    public function checkData($data, $operation)
    {
        $validator = null;
        switch ($operation) {
            case "update-group":
                $validator = Validator::make($data->all(), [
                    "group_desc" => ['required', 'min:6']
                ], [
                    "group_desc.required" => "Group Description is required"
                ]);
                break;
            case "update-type":
                $validator = Validator::make($data->all(), [
                    "type_code" => ['bail', 'required', 'min:3', 'unique:documenttypes']
                ], [
                    "type_code.required" => "Document Type is required",
                    "type_code.unique" => "This document type already exists"
                ]);
                break;
            case "update-status":
                $validator = Validator::make($data->all(), [
                    "status_desc" => ['required', 'min:2']
                ], [
                    "status_desc.required" => "Description is required"
                ]);
                break;
            case "update-permission":
                $validator = Validator::make($data->all(), [
                    "access_desc" => ["required", "min:4"]
                ], [
                    "access_desc.required" => "Access description cannot be empty",
                    "access_desc.min" => "Minimum 4 characters needed"
                ]);
        }
        return $validator;
    }
}
