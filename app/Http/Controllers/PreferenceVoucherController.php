<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PreferenceVoucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class PreferenceVoucherController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        
    }

    public function index()
    {
        Session::forget('datases');
        $data = PreferenceVoucher::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get();
        return view('content.PreferenceVoucher.ListPreferenceVoucher',compact('data'));
    }

    public function addPreferenceVoucher()
    {
        $datases = Session::get('datases');
        return view('content.PreferenceVoucher.FormAddPreferenceVoucher', compact('datases'));
    }

    public function addElementsPreferenceVoucher(Request $request)
    {
        $datases = Session::get('datases');
        if(!$datases || $datases == ''){
            $datases['voucher_code']        = '';
            $datases['voucher_amount']      = '';
            $datases['start_voucher']       = '';
            $datases['end_voucher']         = '';
        }
        $datases[$request->name] = $request->value;
        $datases = Session::put('datases', $datases);
    }

    public function resetElementsPreferenceVoucher()
    {
        Session::forget('datases');

        return redirect('/preference-voucher');
    }

    public function addProcessPreferenceVoucher(Request $request)
    {
        $request->validate([
            'voucher_code'        => 'required',
            'voucher_amount'      => 'required',
            'start_voucher'       => 'required',
            'end_voucher'         => 'required',
        ]);

        $data = array(
            'voucher_code'        => $request->voucher_code,
            'voucher_amount'      => $request->voucher_amount,
            'start_voucher'       => $request->start_voucher,
            'end_voucher'         => $request->end_voucher,
            'company_id'          => Auth::user()->company_id,
            'created_id'          => Auth::id(),
            'updated_id'          => Auth::id(),
        );

        if (PreferenceVoucher::create($data)) {
            $msg = "Tambah Voucher Berhasil";
            return redirect('/preference-voucher/add')->with('msg', $msg);
        } else {
            $msg = "Tambah Voucher Gagal";
            return redirect('/preference-voucher/add')->with('msg', $msg);
        }
    }

    public function editPreferenceVoucher($voucher_id)
    {
        $data = PreferenceVoucher::where('voucher_id', $voucher_id)
        ->first();
        return view('content.PreferenceVoucher.FormEditPreferenceVoucher', compact('data'));
    }

    public function editProcessPreferenceVoucher(Request $request)
    {
        $request->validate([
            'voucher_code'        => 'required',
            'voucher_amount'      => 'required',
            'start_voucher'       => 'required',
            'end_voucher'         => 'required',
        ]);

        $table                      = PreferenceVoucher::findOrFail($request->voucher_id);
        $table->voucher_code        = $request->voucher_code;
        $table->voucher_amount      = $request->voucher_amount;
        $table->start_voucher       = $request->start_voucher;
        $table->end_voucher         = $request->end_voucher;
        $table->updated_id          = Auth::id();

        if ($table->save()) {
            $msg = "Ubah Voucher Berhasil";
            return redirect('/preference-voucher')->with('msg', $msg);
        } else {
            $msg = "Ubah Voucher Gagal";
            return redirect('/preference-voucher')->with('msg', $msg);
        }
    }

    public function deletePreferenceVoucher($voucher_id)
    {
        $table              = PreferenceVoucher::findOrFail($voucher_id);
        $table->data_state  = 1;
        $table->updated_id  = Auth::id();

        if ($table->save()) {
            $msg = "Hapus Voucher Berhasil";
            return redirect('/preference-voucher')->with('msg', $msg);
        } else {
            $msg = "Hapus Voucher Gagal";
            return redirect('/preference-voucher')->with('msg', $msg);
        }
    }
}
