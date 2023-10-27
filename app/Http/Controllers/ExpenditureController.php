<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AcctAccount;
use App\Models\AcctAccountSetting;
use App\Models\Expenditure;
use App\Models\JournalVoucher;
use App\Models\JournalVoucherItem;
use App\Models\PreferenceTransactionModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ExpenditureController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        Session::forget('dataexpenditure');
        $data = Expenditure::select('expenditure_date','expenditure_remark','expenditure_amount','expenditure_id')
        ->where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get();
        return view('content.Expenditure.ListExpenditure', compact('data'));
    }

    public function addExpenditure()
    {
        $expenditure = Session::get('dataexpenditure');
        return view('content.Expenditure.FormAddExpenditure', compact('expenditure'));
    }

    public function addElementsExpenditure(Request $request)
    {
        $dataexpenditure = Session::get('dataexpenditure');
        if(!$dataexpenditure || $dataexpenditure == ''){
            $dataexpenditure['expenditure_remark']    = '';
            $dataexpenditure['expenditure_amount']    = '';   
        }
        $dataexpenditure[$request->name] = $request->value;
        Session::put('dataexpenditure', $dataexpenditure);
    }

    public function addResetExpenditure()
    {
        Session::forget('dataexpenditure');
        return redirect('/expenditure/add');
    }

    public function processAddExpenditure(Request $request)
    {
        $transaction_module_code    = 'PGL';
        $transaction_module_id      = $this->getTransactionModuleID($transaction_module_code);

        $request->validate([
            'expenditure_date'      => 'required',
            'expenditure_remark'    => '',
            'expenditure_amount'    => 'required',
        ]);

        $data = array(
            'expenditure_date'      => $request->expenditure_date,
            'expenditure_remark'    => $request->expenditure_remark,
            'expenditure_amount'    => $request->expenditure_amount,
            'company_id'            => Auth::user()->company_id,
            'created_id'            => Auth::id()
        );
        $journal = array(
            'company_id'                    => Auth::user()->company_id,
            'journal_voucher_status'        => 1,
            'journal_voucher_description'   => $this->getTransactionModuleName($transaction_module_code),
            'journal_voucher_title'         => $this->getTransactionModuleName($transaction_module_code),
            'transaction_module_id'         => $transaction_module_id,
            'transaction_module_code'       => $transaction_module_code,
            'journal_voucher_date'          => $request->expenditure_date,
            'journal_voucher_period'        => date('Ym'),
            'updated_id'                    => Auth::id(),
            'created_id'                    => Auth::id()
        );
        
        if(Expenditure::create($data) && JournalVoucher::create($journal)){
            $account_setting_name = 'expenditure_cash_account';
            $account_id = $this->getAccountId($account_setting_name);
            $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
            $account_default_status = $this->getAccountDefaultStatus($account_id);
            $journal_voucher_id = JournalVoucher::orderBy('created_at', 'DESC')->where('company_id', Auth::user()->company_id)->first();
            if ($account_setting_status == 0){
                $debit_amount = $request->expenditure_amount;
                $credit_amount = 0;
            } else {
                $debit_amount = 0;
                $credit_amount = $request->expenditure_amount;
            }
            $journal_debit = array(
                'company_id'                    => Auth::user()->company_id,
                'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                'account_id'                    => $account_id,
                'journal_voucher_amount'        => $request->expenditure_amount,
                'account_id_default_status'     => $account_default_status,
                'account_id_status'             => $account_setting_status,
                'journal_voucher_debit_amount'  => $debit_amount,
                'journal_voucher_credit_amount' => $credit_amount,
                'updated_id'                    => Auth::id(),
                'created_id'                    => Auth::id()
            );
            JournalVoucherItem::create($journal_debit);

            $account_setting_name = 'expenditure_account';
            $account_id = $this->getAccountId($account_setting_name);
            $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
            $account_default_status = $this->getAccountDefaultStatus($account_id);
            $journal_voucher_id = JournalVoucher::orderBy('created_at', 'DESC')->where('company_id', Auth::user()->company_id)->first();
            if ($account_setting_status == 0){
                $debit_amount = $request->expenditure_amount;
                $credit_amount = 0;
            } else {
                $debit_amount = 0;
                $credit_amount = $request->expenditure_amount;
            }
            $journal_credit = array(
                'company_id'                    => Auth::user()->company_id,
                'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                'account_id'                    => $account_id,
                'journal_voucher_amount'        => $request->expenditure_amount,
                'account_id_default_status'     => $account_default_status,
                'account_id_status'             => $account_setting_status,
                'journal_voucher_debit_amount'  => $debit_amount,
                'journal_voucher_credit_amount' => $credit_amount,
                'updated_id'                    => Auth::id(),
                'created_id'                    => Auth::id()
            );
            JournalVoucherItem::create($journal_credit);

            $msg = 'Tambah Pengeluaran Berhasil';
            return redirect('/expenditure/add')->with('msg',$msg);
        } else {
            $msg = 'Tambah Pengeluaran Gagal';
            return redirect('/expenditure/add')->with('msg',$msg);
        }
        
    }

    public function deleteExpenditure($expenditure_id)
    {
        $transaction_module_code = 'HPSPGL';
        $transaction_module_id  = $this->getTransactionModuleID($transaction_module_code);
        $expenditure = Expenditure::where('expenditure_id', $expenditure_id)->first();
        $journal_voucher = JournalVoucherItem::where('created_at', $expenditure['created_at'])->where('company_id',Auth::user()->company_id)->first();
        $journal = array(
            'company_id'                    => Auth::user()->company_id,
            'journal_voucher_status'        => 1,
            'journal_voucher_description'   => $this->getTransactionModuleName($transaction_module_code),
            'journal_voucher_title'         => $this->getTransactionModuleName($transaction_module_code),
            'transaction_module_id'         => $transaction_module_id,
            'transaction_module_code'       => $transaction_module_code,
            'journal_voucher_date'          => date('Y-m-d'),
            'journal_voucher_period'        => date('Ym'),
            'updated_id'                    => Auth::id(),
            'created_id'                    => Auth::id()
        );
        JournalVoucher::create($journal);
            
        $account_setting_name = 'expenditure_cash_account';
        $account_id = $this->getAccountId($account_setting_name);
        $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
        $account_default_status = $this->getAccountDefaultStatus($account_id);
        $journal_voucher_id = JournalVoucher::orderBy('created_at', 'DESC')->where('company_id', Auth::user()->company_id)->first();
        if($account_setting_status == 0){
            $account_setting_status = 1;
        } else {
            $account_setting_status = 0;
        }
        if ($account_setting_status == 0){ 
            $debit_amount = $journal_voucher['journal_voucher_amount'];
            $credit_amount = 0;
        } else {
            $debit_amount = 0;
            $credit_amount = $journal_voucher['journal_voucher_amount'];
        }
        $journal_debit = array(
            'company_id'                    => Auth::user()->company_id,
            'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
            'account_id'                    => $account_id,
            'journal_voucher_amount'        => $journal_voucher['journal_voucher_amount'],
            'account_id_default_status'     => $account_default_status,
            'account_id_status'             => $account_setting_status,
            'journal_voucher_debit_amount'  => $debit_amount,
            'journal_voucher_credit_amount' => $credit_amount,
            'updated_id'                    => Auth::id(),
            'created_id'                    => Auth::id()
        );
        JournalVoucherItem::create($journal_debit);

        $account_setting_name = 'expenditure_account';
        $account_id = $this->getAccountId($account_setting_name);
        $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
        $account_default_status = $this->getAccountDefaultStatus($account_id);
        $journal_voucher_id = JournalVoucher::orderBy('created_at', 'DESC')->where('company_id', Auth::user()->company_id)->first();
        if($account_setting_status == 1){
            $account_setting_status = 0;
        } else {
            $account_setting_status = 1;
        }
        if ($account_setting_status == 0){
            $debit_amount = $journal_voucher['journal_voucher_amount'];
            $credit_amount = 0;
        } else {
            $debit_amount = 0;
            $credit_amount = $journal_voucher['journal_voucher_amount'];
        }
        $journal_credit = array(
            'company_id'                    => Auth::user()->company_id,
            'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
            'account_id'                    => $account_id,
            'journal_voucher_amount'        => $journal_voucher['journal_voucher_amount'],
            'account_id_default_status'     => $account_default_status,
            'account_id_status'             => $account_setting_status,
            'journal_voucher_debit_amount'  => $debit_amount,
            'journal_voucher_credit_amount' => $credit_amount,
            'updated_id'                    => Auth::id(),
            'created_id'                    => Auth::id()
        );
        JournalVoucherItem::create($journal_credit);

        $table              = Expenditure::findOrFail($expenditure_id);
        $table->data_state  = 1;
        
        if($table->save()){
            $msg = "Hapus Pengeluaran Berhasil";
            return redirect('/expenditure')->with('msg', $msg);
        } else {
            $msg = "Hapus Pengeluaran Gagal";
            return redirect('/expenditure')->with('msg', $msg);
        }
    }

    public function getTransactionModuleID($transaction_module_code)
    {
        $data = PreferenceTransactionModule::select('transaction_module_id')
        ->where('transaction_module_code',$transaction_module_code)
        ->first();

        return $data['transaction_module_id'];
    }

    public function getTransactionModuleName($transaction_module_code)
    {
        $data = PreferenceTransactionModule::select('transaction_module_code')
        ->where('transaction_module_code',$transaction_module_code)
        ->first();

        return $data['transaction_module_name'];
    }
    
    public function getAccountSettingStatus($account_setting_name)
    {
        $data = AcctAccountSetting::select('account_setting_status')
        ->where('company_id', Auth::user()->company_id)
        ->where('account_setting_name', $account_setting_name)
        ->first();

        return $data['account_setting_status'];
    }

    public function getAccountId($account_setting_name)
    {
        $data = AcctAccountSetting::select('account_id')
        ->where('company_id', Auth::user()->company_id)
        ->where('account_setting_name', $account_setting_name)
        ->first();

        return $data['account_id'];
    }

    public function getAccountDefaultStatus($account_id)
    {
        $data = AcctAccount::select('account_default_status')
        ->where('account_id',$account_id)
        ->first();

        return $data['account_default_status'];
    }
}
