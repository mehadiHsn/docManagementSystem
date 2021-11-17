<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Auth;
use Exception;
use App\ChangeRequest;
use App\DocumentMain;
use App\Viewdocument;
use App\Documentgroup;
use App\Documenttype;
use App\Documentstatus;
use App\PermissionList;


class ChangeRequestController extends Controller
{
    public function index($id = NULL)
    {
        if ($id == null) {
            return abort(401,"You need to login first.");;
        }
        if(DocumentMain::findOrFail($id)->handler != Auth::user()->usercode){            
            return abort(403,"You can only make change request for your own documents.");
        }
        return view('changerequest');
    }

    /*get the preferred document info to make change request */
    public function getDocInfo($id = NULL)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }
        if(DocumentMain::findOrFail($id)->handler != Auth::user()->usercode){            
            return abort(403,"You can only make change request for your own documents.");
        }
        if (DocumentMain::findOrFail($id)->doc_status == "REJECTED") {
            return ['rejected', '/'];
        }
        if ($id == null) {
            return DocumentMain::with('Documentstorage')->orderBy('created_at')->get()->values()->toArray();
        } else {
            $data['viewer'] = Viewdocument::where('doc_id', $id)->get()->values()->toArray()[0];
            $data['docInfo'] = DocumentMain::where('id', $id)->with('Documentstorage')->get()->values()->toArray()[0];
            if (Viewdocument::where('doc_id', $id)->exists()) {
                if (strtotime($data['viewer']['updated_at']) < strtotime($data['docInfo']['updated_at'])) {
                    $viewer = Viewdocument::find($id);
                    $viewer['desc1'] = $data['docInfo']['desc1'];
                    $viewer['doc_code'] = $data['docInfo']['doc_code'];
                    $viewer['doc_rev'] = $data['docInfo']['doc_rev'];
                    $viewer['doc_info'] = $data['docInfo']['doc_info'];
                    $viewer['group_desc'] = Documentgroup::select('group_desc')->where('group_code', $data['docInfo']['doc_group'])->get()->toArray()[0]['group_desc'];
                    $viewer['type_code'] = Documenttype::select('type_code')->where('doc_ext', $data['docInfo']['doc_type'])->get()->toArray()[0]['type_code'];
                    $viewer['status_desc'] = Documentstatus::select('status_desc')->where('status_code', $data['docInfo']['doc_status'])->get()->toArray()[0]['status_desc'];
                    $viewer['usercode'] = $data['docInfo']['handler'];
                    $viewer['handler'] = \App\User::select('name')->where('usercode', $data['docInfo']['handler'])->get()->toArray()[0]['name'];
                    $viewer['file_path'] = $data['docInfo']['documentstorage']['file_path'];
                    $viewer['created_at'] = $data['docInfo']['created_at'];
                    $viewer->save();
                }
            }
            $data['change_req'] = ChangeRequest::where('doc_id', $id)->orderByDesc('created_at')->get()->values();
            return $data;
        }
    }

    /* Save the change request*/
    public function changeRequest(Request $data)
    {
        if (!Auth::check()) {
            return redirect('/');
        }
        if(DocumentMain::findOrFail($id)->handler != Auth::user()->usercode){            
            return redirect('/');
        }
        if (ChangeRequest::where('doc_id', $data->doc_id)->where('attr_name', $data->attr_name)->where('request_status', 'Not Checked')->exists() == false) {
            if ($data->hasfile('doc_file')) {
                $extension = $data->doc_file->getClientOriginalExtension();
                $document = DocumentMain::where('id', $data->doc_id)->get()->toArray()[0];
                $changerequest = new ChangeRequest();

                $changerequest->doc_id = $data->doc_id;
                $changerequest->request_by = $data->request_by;
                $changerequest->attr_name = 'doc_file';
                $changerequest->new_value = $document['doc_code'] . '_' . $document['doc_rev'] . '.' . $extension;
                Storage::disk('public')->put($changerequest->new_value,  File::get($data->doc_file));
                try {
                    $changerequest->save();
                    return $changerequest->id;
                } catch (Exception $e) {
                    return $e->getMessage();
                }
            } else {
                $changerequest = new ChangeRequest();
                $changerequest->doc_id = $data->doc_id;
                $changerequest->attr_name = $data->attr_name;
                $changerequest->new_value = $data->new_value;
                $changerequest->request_by = $data->request_by;
                $changerequest->save();
                return $changerequest->id;
                unset($changerequest);
            }
        } else {
            $changerequest = ChangeRequest::where('doc_id', $data->doc_id)->where('attr_name', $data->attr_name)
                ->update(['new_value' => $data->new_value, 'request_status' => 'Not Checked']);
            if ($data->hasfile('doc_file')) {
                $oldFile = ChangeRequest::where('doc_id', $data->doc_id)->where('attr_name', 'doc_file')->get()->toArray()[0];
                Storage::delete($oldFile['new_value']);
                $extension = $data->doc_file->getClientOriginalExtension();
                $document = DocumentMain::where('id', $data->doc_id)->get()->toArray()[0];
                Storage::disk('public')->put($document['doc_code'] . '_' . $document['doc_rev'] . '.' . $extension,  File::get($data->doc_file));
                $changerequest = ChangeRequest::where('doc_id', $data->doc_id)->where('attr_name', 'doc_file')
                    ->update(['new_value' => $document['doc_code'] . '_' . $document['doc_rev'] . '.' . $extension]);
                return $changerequest;
            } else {
                return $changerequest;
            }
        }
    }
    /* owner action of change requests*/
    public function crAction(Request $data)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }
        if ($data->request_status == 'approve') {
            $cr = ChangeRequest::where('id', $data->id)->get()->toArray()[0];
            try {
                ChangeRequest::where('id', $data->id)->update(['request_status' => $data->request_status]);
            } catch (Exception $e) {
                return $e->getMessage();
            }
            $doc = DocumentMain::where('id', $cr['doc_id']);
            switch ($cr['attr_name']) {
                case 'desc1':
                    try {
                        $doc->update(['desc1' => $cr['new_value']]);
                        return $data->id;
                    } catch (Exception $e) {
                        return $e->getMessage();
                    }
                    break;
                case 'doc_info':
                    try {
                        $doc->update(['doc_info' => $cr['new_value']]);
                        return $data->id;
                    } catch (Exception $e) {
                        return $e->getMessage();
                    }
                    break;
                case 'doc_group':
                    try {
                        $doc->update(['doc_group' => $cr['new_value']]);
                        return $data->id;
                    } catch (Exception $e) {
                        return $e->getMessage();
                    }
                    break;
                case 'doc_type':
                    try {
                        $doc->update(['doc_type' => $cr['new_value']]);
                        ChangeRequest::where('doc_id', $data->doc_id)->where('attr_name', 'doc_file')->update(['request_status' => $data->request_status]);
                        Documentstorage::where('doc_id', $data->doc_id)->update(['file_path' => ChangeRequest::where('doc_id', $data->doc_id)->where('attr_name', 'doc_file')->get()->toArray()[0]['doc_file']]);
                        Storage::delete(Documentstorage::where('doc_id', $data->doc_id)->get()->toArray()[0]['file_path']);
                        return $data->id;
                    } catch (Exception $e) {
                        return $e->getMessage();
                    }
                    break;
            }
        } else {
            if (ChangeRequest::where('doc_id', $data->doc_id)->where('attr_name', 'doc_type')->exists() == true) {
                ChangeRequest::where('doc_id', $data->doc_id)->where('attr_name', 'doc_file')->update(['request_status' => 'reject']);
                Storage::delete(ChangeRequest::where('doc_id', $data->doc_id)->where('attr_name', 'doc_file')->get()->toArray()[0]['new_value']);
                return ChangeRequest::where('id', $data->id)->update(['request_status' => 'reject']);
            }
            return ChangeRequest::where('id', $data->id)->update(['request_status' => 'reject']);
        }
    }
}
