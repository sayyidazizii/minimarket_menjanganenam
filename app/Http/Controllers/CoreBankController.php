<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AcctAccount;
use App\Models\CoreBank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CoreBankController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        Session::forget('databank');
        $data = CoreBank::select('bank_name','account_id','bank_id')
        ->where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get();
        // dd($data);
        return view('content.CoreBank.ListCoreBank', compact('data'));
    }

    public function addElementsCoreBank(Request $request)
    {
        $databank = Session::get('databank');
        if(!$databank || $databank == ''){
            $databank['bank_name']      = '';
            $databank['account_id']     = '';   
        }
        $databank[$request->name] = $request->value;
        Session::put('databank', $databank);
    }

    public function resetElementsCoreBank()
    {
        Session::forget('databank');

        return redirect()->back();
    }

    public function addCoreBank()
    {
        $databank = Session::get('databank');
        $accountlist = AcctAccount::select(DB::raw("CONCAT(account_code,' - ',account_name) AS full_account"),'account_id')
        ->where('data_state',0)
        ->where('company_id',Auth::user()->company_id)
        ->get()
        ->pluck('full_account','account_id');
        return view('content.CoreBank.AddCoreBank', compact('databank','accountlist'));
    }

    public function processAddCoreBank(Request $request)
    {
        $request->validate([
            'bank_name'     => 'required',
            'account_id'    => 'required'
        ]);

        $data = CoreBank::create([
            'bank_name'     => $request->bank_name,
            'account_id'    => $request->account_id,
            'company_id'    => Auth::user()->company_id,
            'created_id'    => Auth::id(),
            'updated_id'    => Auth::id(),
        ]);

        if ($data->save()) {
            $msg = 'Tambah Bank Berhasil';
            return redirect()->back()->with('msg', $msg);
        } else {
            $msg = 'Tambah Bank Gagal';
            return redirect()->back()->with('msg', $msg);
        }
    }

    public function editCoreBank($bank_id)
    {
        $data = CoreBank::select('bank_id','bank_name','account_id')
        ->where('bank_id',$bank_id)
        ->first();
        $accountlist = AcctAccount::select(DB::raw("CONCAT(account_code,' - ',account_name) AS full_account"),'account_id')
        ->where('data_state',0)
        ->where('company_id',Auth::user()->company_id)
        ->get()
        ->pluck('full_account','account_id');
        return view('content.CoreBank.EditCoreBank', compact('data','accountlist'));
    }
    public function processEditCoreBank(Request $request)
    {
        $table = CoreBank::findOrFail($request->bank_id);
        $table->bank_name = $request->bank_name;
        $table->account_id = $request->account_id;
        $table->updated_id = Auth::id();

        if ($table->save()) {
            $msg = 'Ubah Bank Berhasil';
            return redirect('core-bank')->with('msg', $msg);
        } else {
            $msg = 'Ubah Bank Gagal';
            return redirect('core-bank')->with('msg', $msg);
        }
    }

    public function deleteCoreBank($bank_id)
    {
        $table = CoreBank::findOrFail($bank_id);
        $table->data_state = 1;
        $table->updated_id = Auth::id();

        if ($table->save()) {
            $msg = 'Hapus Bank Berhasil';
            return redirect('core-bank')->with('msg', $msg);
        } else {
            $msg = 'Hapus Bank Gagal';
            return redirect('core-bank')->with('msg', $msg);
        }
    }

    public function getAccountName($account_id)
    {
        $data = AcctAccount::select('account_code','account_name')
        ->where('account_id', $account_id)
        ->first();

        return $data['account_code'].' - '.$data['account_name'];
    }
}
