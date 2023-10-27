<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AcctAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Arabic;

class AcctAccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        Session::forget('datases');
        $data = AcctAccount::select('account_code','account_name','account_group','account_type_id','account_status','account_id')
        ->where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get();
        return view('content.AcctAccount.ListAcctAccount',compact('data'));
    }

    public function addAcctAccount()
    {
        $datases = Session::get('datases');
        $status = array(
            '0' => 'Debit',
            '1' => 'Kredit'
        );
        $account_type = array(
            '0' => 'NA - Neraca Aktif',
            '1' => 'NP - Neraca Pasif',
            '2' => 'RA - Rugi Laba (A)',
            '3' => 'RP - Rugi Laba (B)',
        );
        return view('content.AcctAccount.FormAddAcctAccount', compact('datases','status','account_type'));
    }

    public function addElementsAcctAccount(Request $request)
    {
        $datases = Session::get('datases');
        if(!$datases || $datases == ''){
            $datases['account_code']        = '';
            $datases['account_name']        = '';   
            $datases['account_group']       = '';
            $datases['account_status']      = '';
            $datases['account_type_id']     = '';
        }
        $datases[$request->name] = $request->value;
        Session::put('datases', $datases);
    }

    public function processAddAcctAccount(Request $request)
    {
        $fields = $request->validate([
            'account_code'      => 'required',
            'account_name'      => 'required',
            'account_group'     => 'required',
            'account_status'    => 'required',
            'account_type_id'   => 'required',
        ]);
        $data = AcctAccount::create([
            'account_code'              => $fields['account_code'],
            'account_name'              => $fields['account_name'],
            'account_group'             => $fields['account_group'],
            'account_default_status'    => $fields['account_status'],
            'account_status'            => $fields['account_status'],
            'account_type_id'           => $fields['account_type_id'],
            'company_id'                => Auth::user()->company_id,
            'created_id'                => Auth::id(),
            'updated_id'                => Auth::id()
        ]);

        if($data->save()){
            $msg = 'Tambah Perkiraan Berhasil';
            return redirect('/acct-account/add')->with('msg',$msg);
        } else {
            $msg = 'Tambah Perkiraan Gagal';
            return redirect('/acct-account/add')->with('msg',$msg);
        }
    }

    public function addResetAcctAccount()
    {
        Session::forget('datases');
        return redirect('/acct-account/add');
    }

    public function getStatus($account_status)
    {
        $status = array(
            '0' => 'Debit',
            '1' => 'Kredit'
        );
        return $status[$account_status];
    }

    public function getType($account_type_id)
    {
        $account_type = array(
            '0' => 'NA - Neraca Aktif',
            '1' => 'NP - Neraca Pasif',
            '2' => 'RA - Rugi Laba (A)',
            '3' => 'RP - Rugi Laba (B)',
        );
        return $account_type[$account_type_id];
    }

    public function editAcctAccount($account_id)
    {
        $data = AcctAccount::select('account_code','account_id','account_name','account_group','account_status','account_type_id')
        ->where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->where('account_id',$account_id)
        ->first();
        $status = array(
            '0' => 'Debit',
            '1' => 'Kredit'
        );
        $account_type = array(
            '0' => 'NA - Neraca Aktif',
            '1' => 'NP - Neraca Pasif',
            '2' => 'RA - Rugi Laba (A)',
            '3' => 'RP - Rugi Laba (B)',
        );
        return view('content.AcctAccount.FormEditAcctAccount',compact('data','status','account_type'));
    }

    public function processEditAcctAccount(Request $request)
    {
        $fields = $request->validate([
            'account_id'        => '',
            'account_code'      => 'required',
            'account_name'      => 'required',
            'account_group'     => 'required',
            'account_status'    => 'required',
            'account_type_id'   => 'required',
        ]);
        $table                          = AcctAccount::findOrFail($fields['account_id']);
        $table->account_code            = $fields['account_code'];
        $table->account_name            = $fields['account_name'];
        $table->account_group           = $fields['account_group'];
        $table->account_default_status  = $fields['account_status'];
        $table->account_status          = $fields['account_status'];
        $table->account_type_id         = $fields['account_type_id'];
      

        if($table->save()){
            $msg = 'Ubah Perkiraan Berhasil';
            return redirect('/acct-account')->with('msg',$msg);
        } else {
            $msg = 'Ubah Perkiraan Gagal';
            return redirect('/acct-account')->with('msg',$msg);
        }
    }
    
    public function deleteAcctAccount($account_id)
    {
        $table             = AcctAccount::findOrFail($account_id);
        $table->data_state = 1;
        $table->updated_id = Auth::id();

        if($table->save()){
            $msg = 'Hapus Perkiraan Berhasil';
            return redirect('/acct-account')->with('msg',$msg);
        } else {
            $msg = 'Hapus Perkiraan Gagal';
            return redirect('/acct-account')->with('msg',$msg);
        }
    }
}
