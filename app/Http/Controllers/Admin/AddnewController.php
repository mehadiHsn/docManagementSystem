<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;

use App\Admin\Admin;

use Illuminate\Support\Facades\Validator;
use App\Documentgroup;
use App\Documenttype;
use App\Documentstatus;
use App\PermissionList;

class AddnewController extends Controller
{
    public function index()
    {
        $data = Admin::get()->count();
        return view('admin.addnew', ['admins' => $data]);
    }

    public function newgroup(Request $data)
    {
        $isValid = $this->checkData($data, 'add-new-group');
        $isValid->validate();
        if ($isValid->fails()) {
            return $isValid->errors();
        } else {
            try {
                return Documentgroup::create($data->all())->id;
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }
    }

    public function newtype(Request $data)
    {
        $isValid = $this->checkData($data, 'add-new-type');
        $isValid->validate();
        if ($isValid->fails()) {
            return $isValid->errors();
        } else {
            try {
                return Documenttype::create($data->all())->id;
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }
    }

    public function newstatus(Request $data)
    {
        $isValid = $this->checkData($data, 'add-new-status');
        $isValid->validate();
        if ($isValid->fails()) {
            return $isValid->errors();
        } else {
            try {
                return Documentstatus::create($data->all())->id;
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }
    }

    public function newaccess(Request $data)
    {
        $isValid = $this->checkData($data, 'add-new-access');
        $isValid->validate();
        if ($isValid->fails()) {
            return $isValid->errors();
        } else {
            try {
                return PermissionList::create($data->all())->id;
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }
    }

    public function checkData($data, $operation)
    {
        $validator = null;
        switch ($operation) {
            case "add-new-group":
                $validator = Validator::make($data->all(), [
                    "group_code" => ['bail', 'required', 'min:4', 'unique:documentgroups'],
                    "group_desc" => ['required', 'min:6']
                ], [
                    "group_code.required" => "Group Code is required",
                    "group_code.unique" => "This group code already exists",
                    "group_desc.required" => "Group Description is required"
                ]);
                break;
            case "add-new-type":
                $validator = Validator::make($data->all(), [
                    "type_code" => ['bail', 'required', 'min:3', 'unique:documenttypes'],
                    "doc_ext" => ['required', 'min:2', 'unique:documenttypes']
                ], [
                    "type_code.required" => "Document Type is required",
                    "type_code.unique" => "This document type already exists",
                    "doc_ext.required" => "Extension is required"
                ]);
                break;
            case "add-new-status":
                $validator = Validator::make($data->all(), [
                    "status_code" => ['bail', 'required', 'min:3', 'unique:documentstatuses'],
                    "status_desc" => ['required', 'min:2']
                ], [
                    "status_code.required" => "Document status is required",
                    "status_code.unique" => "This document status already exists",
                    "status_desc.required" => "Description is required"
                ]);
                break;
            case "add-new-access":
                $validator = Validator::make($data->all(), [
                    "access_code" => ['bail', 'required', 'min:3', 'unique:permission_lists'],
                    "access_desc" => ['required', 'min:2']
                ], [
                    "access_code.required" => "Access code is required",
                    "access_code.unique" => "This access already exists",
                    "access_desc.required" => "Description is required"
                ]);
                break;
        }
        return $validator;
    }
}
