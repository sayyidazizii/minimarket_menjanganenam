<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AcctAccount;
use App\Models\AcctAccountSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AcctAccountSettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        
    }

    public function index()
    {
        $accountlist = AcctAccount::select(DB::raw("CONCAT(account_code,' - ',account_name) AS full_account"),'account_id')
        ->where('data_state',0)
        ->where('company_id',Auth::user()->company_id)
        ->get()
        ->pluck('full_account','account_id');
        $status = array(
            '0' => 'Debit',
            '1' => 'Kredit'
        );
        return view('content.AcctAccountSetting.AcctAccountSetting',compact('accountlist','status'));
    }

    public function processAddAcctAccountSetting(Request $request)
    {
        $data = array(
            '1_account_id'               => $request->input('purchase_account_id'),
            '1_account_setting_status'   => $request->input('purchase_account_status'),
            '1_account_setting_name'     => 'purchase_account',

            '2_account_id'               => $request->input('purchase_cash_account_id'),
            '2_account_setting_status'   => $request->input('purchase_cash_account_status'),
            '2_account_setting_name'     => 'purchase_cash_account',

            '3_account_id'               => $request->input('purchase_payment_account_id'),
            '3_account_setting_status'   => $request->input('purchase_payment_account_status'),
            '3_account_setting_name'     => 'purchase_payment_account',
            
            '4_account_id'               => $request->input('purchase_cash_payment_account_id'),
            '4_account_setting_status'   => $request->input('purchase_cash_payment_account_status'),
            '4_account_setting_name'     => 'purchase_cash_payment_account',

            '5_account_id'               => $request->input('purchase_payable_account_id'),
            '5_account_setting_status'   => $request->input('purchase_payable_account_status'),
            '5_account_setting_name'     => 'purchase_payable_account',

            '6_account_id'               => $request->input('purchase_cash_payable_account_id'),
            '6_account_setting_status'   => $request->input('purchase_cash_payable_account_status'),
            '6_account_setting_name'     => 'purchase_cash_payable_account',

            '7_account_id'               => $request->input('purchase_return_account_id'),
            '7_account_setting_status'   => $request->input('purchase_return_account_status'),
            '7_account_setting_name'     => 'purchase_return_account',

            '8_account_id'               => $request->input('purchase_return_cash_account_id'),
            '8_account_setting_status'   => $request->input('purchase_return_cash_account_status'),
            '8_account_setting_name'     => 'purchase_return_cash_account',

            '9_account_id'               => $request->input('sales_account_id'),
            '9_account_setting_status'   => $request->input('sales_account_status'),
            '9_account_setting_name'     => 'sales_account',

            '10_account_id'               => $request->input('sales_cash_account_id'),
            '10_account_setting_status'   => $request->input('sales_cash_account_status'),
            '10_account_setting_name'     => 'sales_cash_account',
            
            '11_account_id'               => $request->input('sales_receivable_account_id'),
            '11_account_setting_status'   => $request->input('sales_receivable_account_status'),
            '11_account_setting_name'     => 'sales_receivable_account',
            
            '12_account_id'               => $request->input('sales_cash_receivable_account_id'),
            '12_account_setting_status'   => $request->input('sales_cash_receivable_account_status'),
            '12_account_setting_name'     => 'sales_cash_receivable_account',

            '13_account_id'               => $request->input('sales_cashless_account_id'),
            '13_account_setting_status'   => $request->input('sales_cashless_account_status'),
            '13_account_setting_name'     => 'sales_cashless_account',

            '14_account_id'               => $request->input('sales_cashless_cash_account_id'),
            '14_account_setting_status'   => $request->input('sales_cashless_cash_account_status'),
            '14_account_setting_name'     => 'sales_cashless_cash_account',

            '15_account_id'               => $request->input('expenditure_account_id'),
            '15_account_setting_status'   => $request->input('expenditure_account_status'),
            '15_account_setting_name'     => 'expenditure_account',

            '16_account_id'               => $request->input('expenditure_cash_account_id'),
            '16_account_setting_status'   => $request->input('expenditure_cash_account_status'),
            '16_account_setting_name'     => 'expenditure_cash_account',
            
        );

        $company_id = AcctAccountSetting::where('company_id', Auth::user()->company_id)->first();
        if(!empty($company_id)){
            for($key = 1; $key<=16;$key++){
                $data_item = array(
                    'account_id' 				=> $data[$key."_account_id"],
                    'account_setting_status'	=> $data[$key."_account_setting_status"],
                    'account_setting_name' 		=> $data[$key."_account_setting_name"],
                    // 'account_default_status'    => $data[$key."_account_default_status"],
                    'company_id'                => Auth::user()->company_id
                );
                AcctAccountSetting::where('account_setting_name',$data_item['account_setting_name'])
                ->where('company_id', Auth::user()->company_id)
                ->update($data_item);
            }
        } else {
            for($key = 1; $key<=16;$key++){
                $data_item = array(
                    'account_id' 				=> $data[$key."_account_id"],
                    'account_setting_status'	=> $data[$key."_account_setting_status"],
                    'account_setting_name' 		=> $data[$key."_account_setting_name"],
                    // 'account_default_status'    => $data[$key."_account_default_status"],
                    'company_id'                => Auth::user()->company_id
                );
                AcctAccountSetting::create($data_item);    
            }
        }
        $msg = 'Setting Jurnal Berhasil';
        return redirect('/acct-account-setting')->with('msg',$msg);
        
    }

    public function getAccountDefault($account_id)
    {
        $data = AcctAccount::select('account_default_status')
        ->where('account_id', $account_id)
        ->first();

        return $data['account_default_status'];
    }

    public function getAccountId($account_setting_name)
    {
        $data = AcctAccountSetting::select('account_id')
        ->where('company_id', Auth::user()->company_id)
        ->where('account_setting_name', $account_setting_name)
        ->first();

        if(empty($data)){
            return ' ';
        } else{
            return $data['account_id'];
        }
    }

    public function getAccountSettingStatus($account_setting_name)
    {
        $data = AcctAccountSetting::select('account_setting_status')
        ->where('company_id', Auth::user()->company_id)
        ->where('account_setting_name', $account_setting_name)
        ->first();

        return $data['account_setting_status'];
    }
}
