<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\InvtItemUnit;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class InvtItemUnitController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        
    }

    public function index()
    {
        Session::forget('itemunits');
        $data = InvtItemUnit::select('item_unit_code','item_unit_name','item_unit_id')
        ->where('data_state', '=', 0)
        ->where('company_id', Auth::user()->company_id)
        ->get();
        return view('content.InvtItemUnit.ListInvtItemUnit', compact('data'));
    }

    public function addInvtItemUnit()
    {
        $itemunits  = Session::get('itemunits');
        return  view('content.InvtItemUnit.FormAddInvtItemUnit', compact('itemunits'));
    }

    public function elementAddElementsInvtItemUnit(Request $request)
    {
        $itemunits  = Session::get('itemunits');
        if(!$itemunits || $itemunits == ''){
            $itemunits['item_unit_code'] = '';
            $itemunits['item_unit_name'] = '';
            $itemunits['item_unit_remark'] = '';
        }
        $itemunits[$request->name] = $request->value;
        Session::put('itemunits', $itemunits);
    }

    public function processAddElementsInvtItemUnit(Request $request)
    {
        $fields = $request->validate([
            'item_unit_code'     => 'required',
            'item_unit_name'     => 'required',
            'item_unit_remark'   => ''
        ]);

        $data = InvtItemUnit::create([
            'item_unit_code'    => $fields['item_unit_code'],
            'item_unit_name'    => $fields['item_unit_name'],
            'item_unit_remark'  => $fields['item_unit_remark'],
            'company_id'        => Auth::user()->company_id,
            'created_id'        => Auth::id(),
            'updated_id'        => Auth::id()
        ]);
        

        if($data->save()){
            $msg = 'Tambah data Berhasil';
            return redirect('/item-unit/add')->with('msg',$msg);
        } else {
            $msg = 'Tambah data Gagal';
            return redirect('/item-unit/add')->with('msg',$msg);
        }
    }

    public function addReset()
    {
        Session::forget('itemunits');

        return redirect('/item-unit/add');
    }

    public function editInvtItemUnit($item_unit_id)
    {
        $itemunits     = InvtItemUnit::select('item_unit_code','item_unit_id','item_unit_name','item_unit_remark')
        ->where('item_unit_id',$item_unit_id)
        ->first();


        return view('content.InvtItemUnit.FormEditListInvtItemUnit', compact('itemunits'));

    }

    public function processEditInvtItemUnit(Request $request)
    {
        $fields = $request->validate([
            'item_unit_id'       => '',
            'item_unit_code'     => 'required',
            'item_unit_name'     => 'required',
            'item_unit_remark'   => ''
        ]);

        $table                      = InvtItemUnit::findOrFail($fields['item_unit_id']);
        $table->item_unit_code      = $fields['item_unit_code'];
        $table->item_unit_name      = $fields['item_unit_name'];
        $table->item_unit_remark    = $fields['item_unit_remark'];
        $table->updated_id          = Auth::id();
        
        if($table->save()){
            $msg = "Edit Data Berhasil";
            return redirect('/item-unit')->with('msg', $msg);
        } else {
            $msg = "Edit Data gagal";
            return redirect('/item-unit')->with('msg', $msg);
        }
    }

    public function deleteInvtItemUnit($item_unit_id)
    {
        $table             = InvtItemUnit::findOrFail($item_unit_id);
        $table->data_state = 1;
        $table->updated_id = Auth::id();

        if($table->save())
        {
            $msg = 'Hapus Data Berhasil';
            return redirect('/item-unit')->with('msg', $msg);
        }else{
            $msg = 'Hapus Data Gagal';
            return redirect('/item-unit')->with('msg', $msg);
        }

        
    }
}
