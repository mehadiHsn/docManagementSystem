<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

use Auth;
use App\User;
use App\DocumentMain;
use App\Documentstorage;
use App\Documenttype;

class DocumentMainController extends Controller
{

    public function __construct()
    {
        // $this->middleware('auth');
    }
    public function index()
    {
        if (Auth::check()) {

            return view('newdocument');
        }
        return redirect('/login');
    }

    public function addNewDoc(Request $data)
    {
        if (Auth::check()) {
            $data->doc_code = strtoupper(str_replace(" ", "", trim($data->doc_code, " ")));
            $isValid = $this->validateData($data);
            $isValid->validate();
            $extension = $data->file_path->getClientOriginalExtension();
            if ($isValid->fails()) {
                return $isValid->errors();
            } else if ($extension != $data->doc_type) {
                return "Document type and file extension not matched";
            } else {
                try {
                    $document = new DocumentMain();
                    $document->doc_code = $data->doc_code;
                    $document->doc_rev = $data->doc_rev;
                    $document->desc1 = $data->desc1;
                    $document->doc_info = $data->doc_info;
                    $document->doc_group = $data->doc_group;
                    $document->doc_class = 'PUBLIC';
                    $document->doc_type = $data->doc_type;
                    $document->handler = $data->handler;
                    $document->doc_status = $data->doc_status;
                    $document->save();
                    if ($document->id != 0) {
                        $doc_file = new Documentstorage();
                        if ($data->hasfile('file_path')) {
                            $doc_file->file_path = $document->doc_code . '_' . $document->doc_rev . '.' . $extension;
                            Storage::disk('public')->put($doc_file->file_path,  File::get($data->file_path));
                            $doc_file->doc_id = $document->id;
                            $doc_file->save();
                        } else {
                            # do nothing
                        }
                    }
                    return $document->id;
                } catch (Exception $e) {
                    return $e->getMessage();
                }
            }
        } else {
            return redirect('/login');
        }
    }
    /* Approve document by owner group */
    public function approveDoc(Request $data)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }
        $doc = DocumentMain::find($data->id);
        try {
            $doc->doc_status = 'APPROVED';
            $doc->save();
            return $data->id;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    /* Reject document by owner group */
    public function rejectDoc(Request $data)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }
        $doc = DocumentMain::find($data->id);
        try {
            $doc->doc_status = 'REJECTED';
            $doc->save();
            return $data->id;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    public function validateData($data)
    {
        $validation = Validator::make(
            $data->all(),
            [
                'doc_code' => ['required', 'string', 'min:4', 'unique:document_mains,doc_code,NULL,id,doc_rev,' . $data->doc_rev],
                'desc1' => ['required', 'string', 'max:255'],
                'doc_group' => ['required', 'string', 'max:255'],
                'doc_type' => ['required', 'string', 'max:255'],
                'doc_status' => ['required', 'string', 'max:255'],
                'handler' => ['required', 'string', 'max:255'],
            ],
            [
                'doc_code.required' => 'Document Code can\'t be empty',
                'doc_code.min' => 'Document code must be 4 characters',
                'doc_code.unique' => 'This document code and version is already taken',
                'desc1.required' => 'Description1 is mandatory',
                'doc_group.required' => 'Select one Group',
                'doc_type.required' => 'Select one Type',
            ]
        );

        return $validation;
    }
}
