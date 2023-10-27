<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CoreSupplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CoreSupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        Session::forget('datasupplier');
        $data = CoreSupplier::select('supplier_name','supplier_phone','supplier_address','supplier_id')
        ->where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get();
        return view('content.CoreSupplier.ListCoreSupplier', compact('data'));
    }

    public function addElementsCoreSupplier(Request $request)
    {
        $datasupplier = Session::get('datasupplier');
        if(!$datasupplier || $datasupplier == ''){
            $datasupplier['supplier_name']      = '';
            $datasupplier['supplier_phone']     = '';   
            $datasupplier['supplier_address']   = '';   
        }
        $datasupplier[$request->name] = $request->value;
        Session::put('datasupplier', $datasupplier);
    }

    public function resetElementsCoreSupplier()
    {
        Session::forget('datasupplier');

        return redirect()->back();
    }

    public function addCoreSupplier()
    {
        $suppliers = Session::get('datasupplier');

        return view('content.CoreSupplier.AddCoreSupplier', compact('suppliers'));
    }

    public function processAddCoreSupplier(Request $request)
    {
        $request->validate(['supplier_name' => 'required']);

        $data = CoreSupplier::create([
            'supplier_name'     => $request->supplier_name,
            'supplier_phone'    => $request->supplier_phone,
            'supplier_address'  => $request->supplier_address,
            'company_id'        => Auth::user()->company_id,
            'created_id'        => Auth::id(),
            'updated_id'        => Auth::id(),
        ]);

        if ($data->save()) {
            $msg = 'Tambah Pemasok Berhasil';
            return redirect()->back()->with('msg', $msg);
        } else {
            $msg = 'Tambah Pemasok Gagal';
            return redirect()->back()->with('msg', $msg);
        }
    }

    public function editCoreSupplier($supplier_id)
    {
        $data = CoreSupplier::select('supplier_name','supplier_phone','supplier_address','supplier_id')
        ->where('supplier_id', $supplier_id)
        ->first();

        return view('content.CoreSupplier.EditCoreSupplier', compact('data'));
    }

    public function processEditCoreSupplier(Request $request)
    {
        $table                      = CoreSupplier::findOrFail($request->supplier_id);
        $table->supplier_name       = $request->supplier_name;
        $table->supplier_phone      = $request->supplier_phone;
        $table->supplier_address    = $request->supplier_address;
        $table->updated_id          = Auth::id();

        if ($table->save()) {
            $msg = 'Ubah Pemasok Berhasil';
            return redirect()->back()->with('msg', $msg);
        } else {
            $msg = 'Ubah Pemasok Gagal';
            return redirect()->back()->with('msg', $msg);
        }
    }

    public function deleteCoreSupplier($supplier_id)
    {
        $table              = CoreSupplier::findOrFail($supplier_id);
        $table->data_state  = 1;
        $table->updated_id  = Auth::id();

        if ($table->save()) {
            $msg = 'Hapus Pemasok Berhasil';
            return redirect()->back()->with('msg', $msg);
        } else {
            $msg = 'Hapus Pemasok Gagal';
            return redirect()->back()->with('msg', $msg);
        }
    }
}
