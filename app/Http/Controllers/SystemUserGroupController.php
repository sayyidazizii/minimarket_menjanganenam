<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Models\SystemUserGroup;
use App\Models\SystemMenu;
use App\Models\SystemMenuMapping;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

class SystemUserGroupController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $systemusergroup = SystemUserGroup::where('data_state','=',0)
        ->where('company_id', Auth::user()->company_id)
        ->get();
        return view('content/SystemUserGroup/ListSystemUserGroup',compact('systemusergroup'));
    }

    public function addSystemUserGroup(Request $request)
    {
        $systemusergroup    = SystemUserGroup::where('data_state','=',0)
        ->where('company_id', Auth::user()->company_id)
        ->get();

        $systemmenu         = SystemMenu::get();

        return view('content/SystemUserGroup/FormAddSystemUserGroup',compact('systemusergroup', 'systemmenu'));
    }

    public function processAddSystemUserGroup(Request $request)
    {
        $fields = $request->validate([
            'user_group_name'           => 'required',
            'user_group_level'          => 'required',
        ]);

        $systemmenu = SystemMenu::get();

        $allrequest = $request->all();

        $usergroup = array(
            'user_group_name'           => $fields['user_group_name'],
            'user_group_level'          => $fields['user_group_level'],
            'company_id'                => Auth::user()->company_id,
        );

        if(SystemUserGroup::create($usergroup)){
            foreach($systemmenu as $key => $val){
                if(isset($allrequest['checkbox_'.$val['id_menu']])){
                    $menumapping = array(
                        'user_group_level' => $fields['user_group_level'],
                        'id_menu'          => $val['id_menu'],
                        'company_id'       => Auth::user()->company_id
                    );
                    SystemMenuMapping::create($menumapping);
                }
            }
        }else{
            $msg = 'Tambah System User Group Gagal';
            return redirect('/system-user-group/add')->with('msg',$msg);
        }

        $msg = 'Tambah System User Group Berhasil';
        return redirect('/system-user-group')->with('msg',$msg);
    }

    public function editSystemUserGroup($user_group_id)
    {
        $systemusergroup = SystemUserGroup::where('user_group_id',$user_group_id)
        ->first();

        $systemmenu         = SystemMenu::get();

        $systemmenumapping  = SystemMenuMapping::where('user_group_level', $systemusergroup['user_group_level'])
        ->get();

        return view('content/SystemUserGroup/FormEditSystemUserGroup',compact('systemusergroup', 'user_group_id', 'systemmenu'));
    }

    public function processEditSystemUserGroup(Request $request)
    {
        $fields = $request->validate([
            'user_group_id'             => 'required',
            'user_group_name'           => 'required',
            'user_group_level'          => 'required'
        ]);

        $systemmenu = SystemMenu::get();

        $allrequest = $request->all();

        $usergroup                   = SystemUserGroup::findOrFail($fields['user_group_id']);
        $user_group_level_last       = $usergroup['user_group_level'];
        $usergroup->user_group_name  = $fields['user_group_name'];
        $usergroup->user_group_level = $fields['user_group_level'];

        if($usergroup->save()){
            foreach($systemmenu as $key => $val){
                $menumapping_last = SystemMenuMapping::where('user_group_level', $user_group_level_last)
                ->where('id_menu', $val['id_menu'])
                ->first();

                if($menumapping_last){
                    $menumapping_last->delete();
                }

                if(isset($allrequest['checkbox_'.$val['id_menu']])){
                    $menumapping = array(
                        'user_group_level' => $fields['user_group_level'],
                        'id_menu'          => $val['id_menu'],
                    );
                    SystemMenuMapping::create($menumapping);
                }
            }

            $msg = 'Edit System User Group Berhasil';
            return redirect('/system-user-group')->with('msg',$msg);
        }else{
            $msg = 'Edit System User Group Gagal';
            return redirect('/system-user-group/edit/'.$fields['user_group_id'])->with('msg',$msg);
        }
    }

    public function deleteSystemUserGroup($user_group_id)
    {
        $user = SystemUserGroup::findOrFail($user_group_id);
        $user->data_state = 1;
        if($user->save())
        {
            $alluser = User::where('data_state', 0)
            ->where('user_group_id', $user_group_id)
            ->get();

            $allmenumapping = SystemMenuMapping::where('user_group_level', $user['user_group_level'])
            ->get();

            foreach($alluser as $key => $val){
                $userdata = User::where('user_id', $val['user_id'])
                ->first();

                if($userdata){
                    $userdata->delete();
                }
            }

            foreach($allmenumapping as $key => $val){
                $menumapping = SystemMenuMapping::where('user_group_level', $user['user_group_level'])
                ->where('id_menu', $val['id_menu'])
                ->first();

                if($menumapping){
                    $menumapping->delete();
                }
            }

            $msg = 'Hapus System User Group Berhasil';
        }else{
            $msg = 'Hapus System User Group Gagal';
        }

        return redirect('/system-user-group')->with('msg',$msg);
    }

    public function getUserGroupName($user_group_id)
    {
        $usergroupname =  User::select('system_user_group.user_group_name')->join('system_user_group','system_user_group.user_group_id','=','system_user.user_group_id')->where('system_user.user_group_id','=',$user_group_id)->first();

        return $usergroupname['user_group_name'];
    }

    public function getMenuMappingStatus($user_group_level, $id_menu){
        $menumapping =  SystemMenuMapping::where('user_group_level', $user_group_level)
        ->where('id_menu', $id_menu)
        ->count();

        return $menumapping;
    }
}
