<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SalesCustomer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesCustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        
    }

    public function index()
    {
        $data = SalesCustomer::where('data_state',0)->where('company_id', Auth::user()->company_id)->get();
        return view('content.SalesCustomer.ListSalesCustomer', compact('data'));
    }

    public function addSalesCustomer()
    {
        $listgender = array(
            '0' => 'Laki-Laki',
            '1' => 'Perempuan'
        );
        return view('content.SalesCustomer.FormAddSalesCustomer', compact('listgender'));
    }

    public function processAddSalesCustomer(Request $request)
    {
        $request->validate([
            'customer_name'     => 'required',
            'customer_gender'   => 'required'
        ]);

        $data = SalesCustomer::create([
            'customer_name'     => $request->customer_name,
            'customer_gender'   => $request->customer_gender,
            'company_id'        => Auth::user()->company_id,
            'created_id'        => Auth::id(),
            'updated_id'        => Auth::id(),
        ]);

        if($data->save()){
            $msg = 'Tambah Anggota Berhasil';
            return redirect('/sales-customer/add')->with('msg',$msg);
        } else {
            $msg = 'Tambah Anggota Gagal';
            return redirect('/sales-customer/add')->with('msg',$msg);
        }
    }

    public function editSalesCustomer($customer_id)
    {
        $data = SalesCustomer::where('customer_id', $customer_id)->first();
        $listgender = array(
            '0' => 'Laki-Laki',
            '1' => 'Perempuan'
        );
        return view('content.SalesCustomer.FormEditSalesCustomer', compact('data','listgender'));
    }

    public function processEditSalesCustomer(Request $request)
    {
        $table                  = SalesCustomer::findOrFail($request->customer_id);
        $table->customer_name   = $request->customer_name;
        $table->customer_gender = $request->customer_gender;
        $table->customer_status = $request->customer_status;
        $table->updated_id      = Auth::id();

        if($table->save()){
            $msg = 'Ubah Anggota Berhasil';
            return redirect('/sales-customer')->with('msg',$msg);
        } else {
            $msg = 'Ubah Anggota Gagal';
            return redirect('/sales-customer')->with('msg',$msg);
        }
    }

    public function deleteSalesCustomer($customer_id)
    {
        $table              = SalesCustomer::findOrFail($customer_id);
        $table->data_state  = 1;
        $table->updated_id  = Auth::id();

        if($table->save()){
            $msg = 'Hapus Anggota Berhasil';
            return redirect('/sales-customer')->with('msg',$msg);
        } else {
            $msg = 'Hapus Anggota Gagal';
            return redirect('/sales-customer')->with('msg',$msg);
        }
    }

    public function getGenderName($gender_id)
    {
        $listgender = array(
            '0' => 'Laki-Laki',
            '1' => 'Perempuan'
        );

        return $listgender[$gender_id];
    }
}
