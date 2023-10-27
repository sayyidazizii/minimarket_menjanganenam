<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\InvtItem;
use App\Models\InvtItemBarcode;
use App\Models\InvtItemPackge;
use App\Models\InvtItemUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvtItemBarcodeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index($item_id)
    {
        $data_item = InvtItem::where('item_id', $item_id)
        ->where('company_id', Auth::user()->company_id)
        ->first(); 
        $data = InvtItemPackge::join('invt_item_barcode','invt_item_packge.item_packge_id','=','invt_item_barcode.item_packge_id')
        ->select('invt_item_barcode.item_unit_id','invt_item_barcode.item_barcode','invt_item_barcode.item_barcode_id')
        ->where('invt_item_barcode.data_state',0)
        ->where('invt_item_barcode.company_id', Auth::user()->company_id)
        ->where('invt_item_barcode.item_id', $item_id)
        ->get();
        $list_unit = InvtItemUnit::join('invt_item_packge','invt_item_packge.item_unit_id','=','invt_item_unit.item_unit_id')
        ->where('invt_item_packge.company_id', Auth::user()->company_id)
        ->where('invt_item_packge.data_state', 0)
        ->where('invt_item_packge.item_id', $item_id)
        ->get()
        ->pluck('item_unit_name', 'item_unit_id');
        // dd($data);

        return view('content.InvtItemBarcode.ListInvtItemBarcode', compact('data','data_item','list_unit'));
    }

    public function processAddItemBarcode(Request $request)
    {
        $data_packge = InvtItemPackge::select('item_packge_id')
        ->where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->where('item_id', $request->item_id)
        ->where('item_unit_id', $request->item_unit_id)
        ->first();

        $request->validate([
            'item_barcode' => 'required',
            'item_unit_id' => 'required'
        ]);

        $data = InvtItemBarcode::create([
            'item_unit_id'      => $request->item_unit_id,
            'item_id'           => $request->item_id,
            'item_packge_id'    => $data_packge['item_packge_id'],
            'item_barcode'      => $request->item_barcode,
            'company_id'        => Auth::user()->company_id,
            'created_id'        => Auth::id(),
            'updated_id'        => Auth::id()
        ]);

        if($data->save()){
            $msg    = "Tambah Barcode Berhasil";
            return redirect()->back()->with('msg', $msg);
        } else {
            $msg    = "Tambah Barcode Gagal";
            return redirect()->back()->with('msg', $msg);
        }
    }

    public function deleteItemBarcode($item_barcode_id)
    {
        $table = InvtItemBarcode::findOrFail($item_barcode_id);
        $table->data_state = 1;
        $table->updated_id = Auth::id();

        if($table->save()){
            $msg    = "Hapus Barcode Berhasil";
            return redirect()->back()->with('msg', $msg);
        } else {
            $msg    = "Hapus Barcode Gagal";
            return redirect()->back()->with('msg', $msg);
        }
    }

    public function getItemUnitName($item_unit_id)
    {
        $data = InvtItemUnit::select('item_unit_name')
        ->where('item_unit_id', $item_unit_id)
        ->first();

        return $data['item_unit_name'];
    }
}
