<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\InvtItemRack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class InvtItemRackController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        Session::forget('rack');
        $data = InvtItemRack::select('rack_name','rack_status','item_rack_id')
        ->where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get();
        return view('content.InvtItemRack.ListInvtItemRack', compact('data'));
    }

    public function addInvtItemRack()
    {
        $rack_status = array(
            0 => 'Baris',
            1 => 'Kolom',
        );
        $datases = Session::get('rack');
        return view('content.InvtItemRack.AddInvtItemRack', compact('rack_status','datases'));
    }

    public function processAddInvtItemRack(Request $request)
    {
        $fields = $request->validate([
            'rack_name'     => 'required',
            'rack_status'   => 'required',
        ]);

        $data = InvtItemRack::create([
            'rack_name'    => $fields['rack_name'],
            'rack_status'  => $fields['rack_status'],
            'company_id'   => Auth::user()->company_id,
            'created_id'   => Auth::id(),
            'updated_id'   => Auth::id(),
        ]);

        if($data->save()){
            $msg = "Tambah Rak Berhasil";
            return redirect('/item-rack/add')->with('msg', $msg);
        } else {
            $msg = "Tambah Rak Gagal";
            return redirect('/item-rack/add')->with('msg', $msg);
        }
    }

    public function addElementsInvtItemRack(Request $request)
    {
        $rack = Session::get('rack');
        if(!$rack || $rack == ''){
            $rack['rack_name']    = '';
            $rack['rack_status']  = '';
        }
        $rack[$request->name] = $request->value;
        Session::put('rack', $rack);
    }

    public function resetElementsInvtItemRack()
    {
        Session::forget('rack');
        
        return redirect()->back();
    }

    public function editInvtItemRack($item_rack_id)
    {
        $data = InvtItemRack::select('rack_name','rack_status','item_rack_id')
        ->where('item_rack_id',$item_rack_id)
        ->first();
        $rack_status = array(
            0 => 'Baris',
            1 => 'Kolom',
        );
        return view('content.InvtItemRack.EditInvtItemRack', compact('data','rack_status'));
    }

    public function processEditInvtItemRack(Request $request)
    {
        $request->validate([
            'rack_name'     => 'required',
            'rack_status'   => 'required',
        ]);

        $table              = InvtItemRack::findOrFail($request->item_rack_id);
        $table->rack_name   = $request->rack_name;
        $table->rack_status = $request->rack_status;
        $table->updated_id  = Auth::id();

        if($table->save()){
            $msg = "Ubah Rak Berhasil";
            return redirect('/item-rack')->with('msg', $msg);
        } else {
            $msg = "Ubah Rak Gagal";
            return redirect('/item-rack')->with('msg', $msg);
        }
    }

    public function deleteInvtItemRack($item_rack_id)
    {
        $table              = InvtItemRack::findOrFail($item_rack_id);
        $table->data_state  = 1;
        $table->updated_id  = Auth::id(); 

        if($table->save()){
            $msg = "Hapus Rak Berhasil";
            return redirect('/item-rack')->with('msg',$msg);
        } else {
            $msg = "Hapus Rak Gagal";
            return redirect('/item-rack')->with('msg',$msg);
        }
    }

    public function getStatusRack($rack_status)
    {
        $data = array(
            0 => 'Baris',
            1 => 'Kolom',
        );

        return $data[$rack_status];
    }
}

