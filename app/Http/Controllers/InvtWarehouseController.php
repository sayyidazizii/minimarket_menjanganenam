<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\InvtWarehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class InvtWarehouseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        
    }
    
    public function index()
    {
        Session::forget('warehouses');
        $data = InvtWarehouse::where('data_state','=',0)
        ->where('company_id', Auth::user()->company_id)
        ->get();
        return view('content.InvtWarehouse.ListInvtWarehouse', compact('data'));
    }

    public function addWarehouse()
    {
        $warehouses = Session::get('warehouses');
        return view('content.InvtWarehouse.FormAddInvtWarehouse', compact('warehouses'));
    }

    public function addElementsWarehouse(Request $request)
    {
        $warehouses  = Session::get('warehouses');
        if(!$warehouses || $warehouses == ''){
            $warehouses['warehouse_code']       = '';
            $warehouses['warehouse_name']       = '';
            $warehouses['warehouse_phone']      = '';
            $warehouses['warehouse_address']    = '';
        }
        $warehouses[$request->name] = $request->value;
        Session::put('warehouses', $warehouses);
    }

    public function processAddWarehouse(Request $request)
    {
        $fields = $request->validate([
            'warehouse_code'    => 'required',
            'warehouse_name'    => 'required',
            'warehouse_phone'   => 'required',
            'warehouse_address' => 'required'
        ]);

        $data = InvtWarehouse::create([
            'warehouse_code'    => $fields['warehouse_code'],
            'warehouse_name'    => $fields['warehouse_name'],
            'warehouse_phone'   => $fields['warehouse_phone'],
            'warehouse_address' => $fields['warehouse_address'],
            'company_id'        => Auth::user()->company_id,
            'created_id'        => Auth::id(),
            'updated_id'        => Auth::id(),
        ]);

        if($data->save()){
            $msg = "Tambah Gudang Berhasil";
            return redirect('/warehouse/add-warehouse')->with('msg', $msg);
        } else {
            $msg = "Tambah Gudang Gagal";
            return redirect('/warehouse/add-warehouse')->with('msg', $msg);
        }
    }

    public function editWarehouse($warehouse_id)
    {
        $data   = InvtWarehouse::where('warehouse_id',$warehouse_id)->first();
        return view('content.InvtWarehouse.FormEditInvtWarehouse', compact('data'));
    }

    public function processEditWarehouse(Request $request)
    {
        $fields = $request->validate([
            'warehouse_id'      => '',
            'warehouse_code'    => 'required',    
            'warehouse_name'    => 'required',
            'warehouse_phone'   => 'required',
            'warehouse_address' => 'required'
        ]);

        $table                      = InvtWarehouse::findOrFail($fields['warehouse_id']);
        $table->warehouse_code      = $fields['warehouse_code'];
        $table->warehouse_name      = $fields['warehouse_name'];
        $table->warehouse_phone     = $fields['warehouse_phone'];
        $table->warehouse_address   = $fields['warehouse_address'];
        $table->updated_id          = Auth::id();

        if($table->save()){
            $msg = "Ubah Gudang Berhasil";
            return redirect('/warehouse')->with('msg',$msg);
        } else {
            $msg = "Ubah Gudang Gagal";
            return redirect('/warehouse')->with('msg',$msg);
        }
    }

    public function deleteWarehouse($warehouse_id)
    {
        $table             = InvtWarehouse::findOrFail($warehouse_id);
        $table->data_state = 1;
        $table->updated_id = Auth::id();

        if($table->save()){
            $msg = "Hapus Gudang Berhasil";
            return redirect('/warehouse')->with('msg',$msg);
        } else {
            $msg = "Hapus Gudang Gagal";
            return redirect('/warehouse')->with('msg',$msg);
        }
    }

    public function addResetWarehouse()
    {
        Session::forget('warehouses');
        return redirect('/warehouse/add-warehouse');
    }
}
