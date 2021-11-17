<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\DocumentMain;
use App\Documentstorage;
use App\User;
use App\Documenttype;
use App\Documentgroup;
use App\Documentstatus;
use App\Viewdocument;
use App\UserPermission;
use App\PermissionRequest;
use App\PermissionList;
use App\ChangeRequest;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (!Documentstatus::where('status_code', 'APPROVED')->exists()) {
            Documentstatus::create([
                "status_code" => "APPROVED", "status_desc" => "Approved"
            ]);
        }
        if (!Documentstatus::where('status_code', 'REJECTED')->exists()) {
            Documentstatus::create([
                "status_code" => "REJECTED", "status_desc" => "Rejected"
            ]);
        }
        if (!Documentstatus::where('status_code', 'CREATED')->exists()) {
            Documentstatus::create([
                "status_code" => "CREATED", "status_desc" => "Created"
            ]);
        }        
        $docs = DocumentMain::with('Documentstorage')->orderBy('created_at')->get()->values()->toArray();
        $this->updateCommonView($docs);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->is_owner == 1) {
                $data['new_docs'] = DocumentMain::where('doc_status', 'CREATED')->where('doc_group', $user->owner_group)->get()->values()->toArray();
                $data['new_doc_count'] = DocumentMain::where('doc_status', 'CREATED')->where('doc_group', $user->owner_group)->get()->count();
                return response()->view('home', $data);
            }
        }
        return response()->view('home');
    }

    public function getdoctypeandgroup()
    {
        $data['doc_type'] = Documenttype::orderBy('type_code')->get()->values();
        $data['doc_group'] = Documentgroup::orderBy('group_code')->get()->values();
        $data['doc_status'] = Documentstatus::orderBy('status_code')->get()->values();
        $permissionList = PermissionList::select('id', 'access_code', 'access_desc')->orderBy('access_desc')->get()->toArray();
        $data['permissions'] = $permissionList;
        $tmp = array();
        foreach ($permissionList as $value) {
            array_push($tmp, $value['access_code']);
        }
        $data['no_permissions'] = Documentgroup::whereNotIn('group_code', $tmp)->orderBy('group_code')->get()->values();
        unset($tmp);
        return $data;
    }

    public function getMyDocs($data)
    {
        $user = User::findOrFail($data);
        if ($user->id != 0) {  
            $docs = DocumentMain::with('Documentstorage')->orderBy('created_at')->get()->values()->toArray();
            $this->updateCommonView($docs);
            $dt['doc_group'] = Documentgroup::get()->toArray();
            $dt["all_docs"] = DocumentMain::where('doc_status', 'APPROVED')->with('Documentstorage')->get()->toArray();
            $dt['all_docs_detail'] = Viewdocument::where('status_desc', 'Approved')->get()->toArray();

            if ($user->is_owner == 0) {
                $dt['my_docs'] = Viewdocument::where('usercode', $user->usercode)->orderBy('created_at')->get()->values()->toArray();
            } else {
                $dt['my_docs'] = Viewdocument::where('group_desc', Documentgroup::select('group_desc')->where('group_code', $user->owner_group)->value('group_desc'))->get()->toArray();;
            }
            unset($user);
            return $dt;
        }
    }

    public function getActiveUsers()
    {
        $data['permissionList'] = PermissionList::get()->toArray();
        $data['users'] = User::with('UserPermission')->where('is_active', 1)->where('is_owner', 0)->orderBy('name')->get()->toArray();
        $data['permission_requests'] = PermissionRequest::where('status', 'Not Approved')->orderBy('created_at')->get()->toArray();
        return $data;
    }

    public function notice()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->is_owner == 1) {
                $data['new_docs'] = DocumentMain::where('doc_status', 'CREATED')->where('doc_group', $user->owner_group)->get()->values()->toArray();
                $data['new_doc_count'] = DocumentMain::where('doc_status', 'CREATED')->where('doc_group', $user->owner_group)->get()->count();
                return view('notice', $data);
            } else {
                return redirect()->route('home');
            }
        }
        return  abort(401, 'For registered users only.');
    }
    public function ownerNotices()
    {
        $user = Auth::user();
        $data['new_docs'] = DocumentMain::where('doc_status', 'CREATED')->where('doc_group', $user->owner_group)->with('Documentstorage')->get()->values()->toArray();
        $docIds = DocumentMain::where('doc_status', 'CREATED')->where('doc_group', $user->owner_group)->with('Documentstorage')->get()->map(function ($data) {
            return $data->only('id');
        });
        $this->updateCommonView($data['new_docs']);
        $data['new_docs'] = Viewdocument::whereIn('doc_id', $docIds)->orderBy('created_at')->get()->values()->toArray();
        $data['my_owner_docs'] = DocumentMain::where('doc_group', $user->owner_group)->get()->toArray();
        $tmp = DocumentMain::select('*')->where('doc_group', $user->owner_group)->get()->toArray();
        $tmpDocIds = array();
        foreach ($tmp as $value) {
            array_push($tmpDocIds, $value['id']);
        }
        unset($tmp);
        $data['change_requests'] = ChangeRequest::whereIn('doc_id', $tmpDocIds)->where('request_status', 'Not Checked')->orderBy('doc_id')->get()->toArray();
        unset($tmpDocIds);
        return $data;
    }
    public function searchResult($q = null)
    {
        if (!Auth::check()) {
            return abort(401);
        }
        if ($q == NULL) {
            return redirect('/');
        }
        $tmp = DocumentMain::where('doc_status','APPROVED')->where('doc_code', 'like','%'.$q.'%')
        ->orWhere('desc1', 'like', 'like','%'.$q.'%')->get();
        $docIds = $tmp->map(function ($data) {
            return $data->only('id');
        });
        $data['result'] = Viewdocument::whereIn('doc_id',$docIds)->orderByDesc('created_at')->get()->toArray();
        foreach ($data['result'] as $key => $value) {
            $data['result'][$key]['group_code'] = Documentgroup::where('group_desc',$value['group_desc'])->value('group_code');
        }

        $data['my_permission'] = UserPermission::where('usercode',Auth::user()->usercode)->get()->toArray();

        unset($tmp);
        return view('searchResult',['data'=>$data]);
    }

    public function updateCommonView($listOfDocs){
        foreach ($listOfDocs as $value) {
            $viewer = new Viewdocument();
            $original = DocumentMain::with('Documentstorage')->where('id', $value['id'])->get()->toArray()[0];
            if (Viewdocument::where('doc_id', $value['id'])->exists() == false) {
                $viewer['doc_id'] = $value['id'];
                $viewer['desc1'] = $original['desc1'];
                $viewer['doc_code'] = $original['doc_code'];
                $viewer['doc_rev'] = $original['doc_rev'];
                $viewer['doc_info'] = $original['doc_info'];
                $viewer['group_desc'] = Documentgroup::select('group_desc')->where('group_code', $value['doc_group'])->get()->toArray()[0]['group_desc'];
                $viewer['type_code'] = Documenttype::select('type_code')->where('doc_ext', $value['doc_type'])->get()->toArray()[0]['type_code'];
                $viewer['status_desc'] = Documentstatus::select('status_desc')->where('status_code', $value['doc_status'])->get()->toArray()[0]['status_desc'];
                $viewer['usercode'] = $value['handler'];
                $viewer['handler'] = User::select('name')->where('usercode', $value['handler'])->get()->toArray()[0]['name'];
                $viewer['file_path'] = $value['documentstorage']['file_path'];
                $viewer['created_at'] = $original['created_at'];
                $viewer->save();
                unset($viewer);
            } else if (Viewdocument::where('doc_id', $value['id'])->exists() == true) {

                if (strtotime($original['updated_at']) > strtotime(Viewdocument::find($value['id'])->updated_at)) {
                    $viewer = Viewdocument::find($value['id']);
                    $viewer['desc1'] = $original['desc1'];
                    $viewer['doc_code'] = $original['doc_code'];
                    $viewer['doc_rev'] = $original['doc_rev'];
                    $viewer['doc_info'] = $original['doc_info'];
                    $viewer['group_desc'] = Documentgroup::select('group_desc')->where('group_code', $original['doc_group'])->get()->toArray()[0]['group_desc'];
                    $viewer['type_code'] = Documenttype::select('type_code')->where('doc_ext', $original['doc_type'])->get()->toArray()[0]['type_code'];
                    $viewer['status_desc'] = Documentstatus::select('status_desc')->where('status_code', $original['doc_status'])->get()->toArray()[0]['status_desc'];
                    $viewer['usercode'] = $original['handler'];
                    $viewer['handler'] = User::select('name')->where('usercode', $original['handler'])->get()->toArray()[0]['name'];
                    $viewer['file_path'] = $original['documentstorage']['file_path'];
                    $viewer['created_at'] = $original['created_at'];
                    $viewer->save();
                }
                unset($viewer);
            }
            unset($original);
        }
    }
}
