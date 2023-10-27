<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AcctAccount;
use App\Models\AcctAccountSetting;
use App\Models\AcctProfitLossReport;
use App\Models\CloseCashierLog;
use App\Models\CoreEmployee;
use App\Models\CoreMemberKopkar;
use App\Models\Expenditure;
use App\Models\InvtItem;
use App\Models\InvtItemBarcode;
use App\Models\InvtItemCategory;
use App\Models\InvtItemPackge;
use App\Models\InvtItemRack;
use App\Models\InvtItemStock;
use App\Models\InvtItemUnit;
use App\Models\InvtWarehouse;
use App\Models\JournalVoucher;
use App\Models\JournalVoucherItem;
use App\Models\PreferenceTransactionModule;
use App\Models\PreferenceVoucher;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Models\SystemLoginLog;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    public function getDataItem(){
        $data = InvtItem::get();

        return json_encode($data);
    }

    public function getDataItemUnit()
    {
        $data = InvtItemUnit::get();

        return json_encode($data);
    }

    public function getDataItemCategory()
    {
        $data = InvtItemCategory::get();

        return json_encode($data);
    }

    public function getDataItemWarehouse()
    {
        $data = InvtWarehouse::get();

        return json_encode($data);
    }

    public function getDataItemBarcode()
    {
        $data = InvtItemBarcode::get();

        return json_encode($data);
    }

    public function getDataItemPackge()
    {
        $data = InvtItemPackge::get();

        return json_encode($data);
    }

    public function getDataItemStock()
    {
        $data = InvtItemStock::get();

        return json_encode($data);
    }

    public function postDataSalesInvoice(Request $request)
    {
        $transaction_module_code = 'PJL';
        $transaction_module_id  = $this->getTransactionModuleID($transaction_module_code);

        $data_journal = array(
            'company_id'                    => $request['company_id'],
            'journal_voucher_status'        => 1,
            'journal_voucher_description'   => $this->getTransactionModuleName($transaction_module_code),
            'journal_voucher_title'         => $this->getTransactionModuleName($transaction_module_code),
            'transaction_module_id'         => $transaction_module_id,
            'transaction_module_code'       => $transaction_module_code,
            'journal_voucher_date'          => $request['sales_invoice_date'],
            'transaction_journal_no'        => $request['sales_invoice_no'],
            'journal_voucher_period'        => date('Ym', strtotime($request['sales_invoice_date'])),
            'journal_voucher_segment'       => 2,
            'updated_id'                    => $request['updated_id'],
            'created_id'                    => $request['created_id']
        );
        JournalVoucher::create($data_journal);

        if ($request['sales_payment_method'] == 1) {
            $account_setting_name = 'sales_cash_account';
            $account_id = $this->getAccountId($account_setting_name);
            $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
            $account_default_status = $this->getAccountDefaultStatus($account_id);
            $journal_voucher_id = JournalVoucher::orderBy('created_at', 'DESC')->where('company_id', $request['company_id'])->first();
            if ($account_setting_status == 0){
                $debit_amount = $request['total_amount'];
                $credit_amount = 0;
            } else {
                $debit_amount = 0;
                $credit_amount = $request['total_amount'];
            }
            $journal_debit = array(
                'company_id'                    => $request['company_id'],
                'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                'account_id'                    => $account_id,
                'journal_voucher_amount'        => $request['total_amount'],
                'account_id_default_status'     => $account_default_status,
                'account_id_status'             => $account_setting_status,
                'journal_voucher_debit_amount'  => $debit_amount,
                'journal_voucher_credit_amount' => $credit_amount,
                'updated_id'                    => $request['updated_id'],
                'created_id'                    => $request['created_id']
            );
            JournalVoucherItem::create($journal_debit);

            $account_setting_name = 'sales_account';
            $account_id = $this->getAccountId($account_setting_name);
            $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
            $account_default_status = $this->getAccountDefaultStatus($account_id);
            $journal_voucher_id = JournalVoucher::orderBy('created_at', 'DESC')->where('company_id', $request['company_id'])->first();
            if ($account_setting_status == 0){
                $debit_amount = $request['total_amount'];
                $credit_amount = 0;
            } else {
                $debit_amount = 0;
                $credit_amount = $request['total_amount'];
            }
            $journal_credit = array(
                'company_id'                    => $request['company_id'],
                'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                'account_id'                    => $account_id,
                'journal_voucher_amount'        => $request['total_amount'],
                'account_id_default_status'     => $account_default_status,
                'account_id_status'             => $account_setting_status,
                'journal_voucher_debit_amount'  => $debit_amount,
                'journal_voucher_credit_amount' => $credit_amount,
                'updated_id'                    => $request['updated_id'],
                'created_id'                    => $request['created_id']
            );
            JournalVoucherItem::create($journal_credit);
        } else if ($request['sales_payment_method'] == 2) {
            $account_setting_name = 'sales_cash_receivable_account';
            $account_id = $this->getAccountId($account_setting_name);
            $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
            $account_default_status = $this->getAccountDefaultStatus($account_id);
            $journal_voucher_id = JournalVoucher::orderBy('created_at', 'DESC')->where('company_id', $request['company_id'])->first();
            if ($account_setting_status == 0){
                $debit_amount = $request['total_amount'];
                $credit_amount = 0;
            } else {
                $debit_amount = 0;
                $credit_amount = $request['total_amount'];
            }
            $journal_debit = array(
                'company_id'                    => $request['company_id'],
                'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                'account_id'                    => $account_id,
                'journal_voucher_amount'        => $request['total_amount'],
                'account_id_default_status'     => $account_default_status,
                'account_id_status'             => $account_setting_status,
                'journal_voucher_debit_amount'  => $debit_amount,
                'journal_voucher_credit_amount' => $credit_amount,
                'updated_id'                    => $request['updated_id'],
                'created_id'                    => $request['created_id']
            );
            JournalVoucherItem::create($journal_debit);

            $account_setting_name = 'sales_receivable_account';
            $account_id = $this->getAccountId($account_setting_name);
            $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
            $account_default_status = $this->getAccountDefaultStatus($account_id);
            $journal_voucher_id = JournalVoucher::orderBy('created_at', 'DESC')->where('company_id', $request['company_id'])->first();
            if ($account_setting_status == 0){
                $debit_amount = $request['total_amount'];
                $credit_amount = 0;
            } else {
                $debit_amount = 0;
                $credit_amount = $request['total_amount'];
            }
            $journal_credit = array(
                'company_id'                    => $request['company_id'],
                'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                'account_id'                    => $account_id,
                'journal_voucher_amount'        => $request['total_amount'],
                'account_id_default_status'     => $account_default_status,
                'account_id_status'             => $account_setting_status,
                'journal_voucher_debit_amount'  => $debit_amount,
                'journal_voucher_credit_amount' => $credit_amount,
                'updated_id'                    => $request['updated_id'],
                'created_id'                    => $request['created_id']
            );
            JournalVoucherItem::create($journal_credit);
        } else {
            $account_setting_name = 'sales_cashless_cash_account';
            $account_id = $this->getAccountId($account_setting_name);
            $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
            $account_default_status = $this->getAccountDefaultStatus($account_id);
            $journal_voucher_id = JournalVoucher::orderBy('created_at', 'DESC')->where('company_id', $request['company_id'])->first();
            if ($account_setting_status == 0){
                $debit_amount = $request['total_amount'];
                $credit_amount = 0;
            } else {
                $debit_amount = 0;
                $credit_amount = $request['total_amount'];
            }
            $journal_debit = array(
                'company_id'                    => $request['company_id'],
                'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                'account_id'                    => $account_id,
                'journal_voucher_amount'        => $request['total_amount'],
                'account_id_default_status'     => $account_default_status,
                'account_id_status'             => $account_setting_status,
                'journal_voucher_debit_amount'  => $debit_amount,
                'journal_voucher_credit_amount' => $credit_amount,
                'updated_id'                    => $request['updated_id'],
                'created_id'                    => $request['created_id']
            );
            JournalVoucherItem::create($journal_debit);

            $account_setting_name = 'sales_cashless_account';
            $account_id = $this->getAccountId($account_setting_name);
            $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
            $account_default_status = $this->getAccountDefaultStatus($account_id);
            $journal_voucher_id = JournalVoucher::orderBy('created_at', 'DESC')->where('company_id', $request['company_id'])->first();
            if ($account_setting_status == 0){
                $debit_amount = $request['total_amount'];
                $credit_amount = 0;
            } else {
                $debit_amount = 0;
                $credit_amount = $request['total_amount'];
            }
            $journal_credit = array(
                'company_id'                    => $request['company_id'],
                'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                'account_id'                    => $account_id,
                'journal_voucher_amount'        => $request['total_amount'],
                'account_id_default_status'     => $account_default_status,
                'account_id_status'             => $account_setting_status,
                'journal_voucher_debit_amount'  => $debit_amount,
                'journal_voucher_credit_amount' => $credit_amount,
                'updated_id'                    => $request['updated_id'],
                'created_id'                    => $request['created_id']
            );
            JournalVoucherItem::create($journal_credit);
        }

        if ($request['data_state'] == 1) {
            $transaction_module_code = 'HPSPJL';
            $transaction_module_id  = $this->getTransactionModuleID($transaction_module_code);
            $journal = array(
                'company_id'                    => $request['company_id'],
                'journal_voucher_status'        => 1,
                'journal_voucher_description'   => $this->getTransactionModuleName($transaction_module_code),
                'journal_voucher_title'         => $this->getTransactionModuleName($transaction_module_code),
                'transaction_module_id'         => $transaction_module_id,
                'transaction_module_code'       => $transaction_module_code,
                'transaction_journal_no'        => $request['sales_invoice_no'],
                'journal_voucher_date'          => $request['sales_invoice_date'],
                'journal_voucher_period'        => date('Ym', strtotime($request['sales_invoice_date'])),
                'updated_id'                    => $request['updated_id'],
                'created_id'                    => $request['created_id']
            );
            JournalVoucher::create($journal);
            if ($request['sales_payment_method'] == 1) {
                $account_setting_name = 'sales_cash_account';
                $account_id = $this->getAccountId($account_setting_name);
                $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
                $account_default_status = $this->getAccountDefaultStatus($account_id);
                $journal_voucher_id = JournalVoucher::orderBy('created_at', 'DESC')->where('company_id', $request['company_id'])->first();
                if($account_setting_status == 0){
                    $account_setting_status = 1;
                } else {
                    $account_setting_status = 0;
                }
                if ($account_setting_status == 0){ 
                    $debit_amount = $request['total_amount'];
                    $credit_amount = 0;
                } else {
                    $debit_amount = 0;
                    $credit_amount = $request['total_amount'];
                }
                $journal_debit = array(
                    'company_id'                    => $request['company_id'],
                    'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                    'account_id'                    => $account_id,
                    'journal_voucher_amount'        => $request['total_amount'],
                    'account_id_default_status'     => $account_default_status,
                    'account_id_status'             => $account_setting_status,
                    'journal_voucher_debit_amount'  => $debit_amount,
                    'journal_voucher_credit_amount' => $credit_amount,
                    'updated_id'                    => $request['updated_id'],
                    'created_id'                    => $request['created_id']
                );
                JournalVoucherItem::create($journal_debit);
        
                $account_setting_name = 'sales_account';
                $account_id = $this->getAccountId($account_setting_name);
                $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
                $account_default_status = $this->getAccountDefaultStatus($account_id);
                $journal_voucher_id = JournalVoucher::orderBy('created_at', 'DESC')->where('company_id', $request['company_id'])->first();
                if($account_setting_status == 1){
                    $account_setting_status = 0;
                } else {
                    $account_setting_status = 1;
                }
                if ($account_setting_status == 0){
                    $debit_amount = $request['total_amount'];
                    $credit_amount = 0;
                } else {
                    $debit_amount = 0;
                    $credit_amount = $request['total_amount'];
                }
                $journal_credit = array(
                    'company_id'                    => $request['company_id'],
                    'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                    'account_id'                    => $account_id,
                    'journal_voucher_amount'        => $request['total_amount'],
                    'account_id_default_status'     => $account_default_status,
                    'account_id_status'             => $account_setting_status,
                    'journal_voucher_debit_amount'  => $debit_amount,
                    'journal_voucher_credit_amount' => $credit_amount,
                    'updated_id'                    => $request['updated_id'],
                    'created_id'                    => $request['created_id']
                );
                JournalVoucherItem::create($journal_credit);
            } else if ($request['sales_payment_method'] == 2) {
                $account_setting_name = 'sales_cash_receivable_account';
                $account_id = $this->getAccountId($account_setting_name);
                $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
                $account_default_status = $this->getAccountDefaultStatus($account_id);
                $journal_voucher_id = JournalVoucher::orderBy('created_at', 'DESC')->where('company_id', $request['company_id'])->first();
                if($account_setting_status == 0){
                    $account_setting_status = 1;
                } else {
                    $account_setting_status = 0;
                }
                if ($account_setting_status == 0){ 
                    $debit_amount = $request['total_amount'];
                    $credit_amount = 0;
                } else {
                    $debit_amount = 0;
                    $credit_amount = $request['total_amount'];
                }
                $journal_debit = array(
                    'company_id'                    => $request['company_id'],
                    'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                    'account_id'                    => $account_id,
                    'journal_voucher_amount'        => $request['total_amount'],
                    'account_id_default_status'     => $account_default_status,
                    'account_id_status'             => $account_setting_status,
                    'journal_voucher_debit_amount'  => $debit_amount,
                    'journal_voucher_credit_amount' => $credit_amount,
                    'updated_id'                    => $request['updated_id'],
                    'created_id'                    => $request['created_id']
                );
                JournalVoucherItem::create($journal_debit);
        
                $account_setting_name = 'sales_receivable_account';
                $account_id = $this->getAccountId($account_setting_name);
                $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
                $account_default_status = $this->getAccountDefaultStatus($account_id);
                $journal_voucher_id = JournalVoucher::orderBy('created_at', 'DESC')->where('company_id', $request['company_id'])->first();
                if($account_setting_status == 1){
                    $account_setting_status = 0;
                } else {
                    $account_setting_status = 1;
                }
                if ($account_setting_status == 0){
                    $debit_amount = $request['total_amount'];
                    $credit_amount = 0;
                } else {
                    $debit_amount = 0;
                    $credit_amount = $request['total_amount'];
                }
                $journal_credit = array(
                    'company_id'                    => $request['company_id'],
                    'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                    'account_id'                    => $account_id,
                    'journal_voucher_amount'        => $request['total_amount'],
                    'account_id_default_status'     => $account_default_status,
                    'account_id_status'             => $account_setting_status,
                    'journal_voucher_debit_amount'  => $debit_amount,
                    'journal_voucher_credit_amount' => $credit_amount,
                    'updated_id'                    => $request['updated_id'],
                    'created_id'                    => $request['created_id']
                );
                JournalVoucherItem::create($journal_credit);
            } else {
                $account_setting_name = 'sales_cashless_cash_account';
                $account_id = $this->getAccountId($account_setting_name);
                $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
                $account_default_status = $this->getAccountDefaultStatus($account_id);
                $journal_voucher_id = JournalVoucher::orderBy('created_at', 'DESC')->where('company_id', $request['company_id'])->first();
                if($account_setting_status == 0){
                    $account_setting_status = 1;
                } else {
                    $account_setting_status = 0;
                }
                if ($account_setting_status == 0){ 
                    $debit_amount = $request['total_amount'];
                    $credit_amount = 0;
                } else {
                    $debit_amount = 0;
                    $credit_amount = $request['total_amount'];
                }
                $journal_debit = array(
                    'company_id'                    => $request['company_id'],
                    'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                    'account_id'                    => $account_id,
                    'journal_voucher_amount'        => $request['total_amount'],
                    'account_id_default_status'     => $account_default_status,
                    'account_id_status'             => $account_setting_status,
                    'journal_voucher_debit_amount'  => $debit_amount,
                    'journal_voucher_credit_amount' => $credit_amount,
                    'updated_id'                    => $request['updated_id'],
                    'created_id'                    => $request['created_id']
                );
                JournalVoucherItem::create($journal_debit);
        
                $account_setting_name = 'sales_cashless_account';
                $account_id = $this->getAccountId($account_setting_name);
                $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
                $account_default_status = $this->getAccountDefaultStatus($account_id);
                $journal_voucher_id = JournalVoucher::orderBy('created_at', 'DESC')->where('company_id', $request['company_id'])->first();
                if($account_setting_status == 1){
                    $account_setting_status = 0;
                } else {
                    $account_setting_status = 1;
                }
                if ($account_setting_status == 0){
                    $debit_amount = $request['total_amount'];
                    $credit_amount = 0;
                } else {
                    $debit_amount = 0;
                    $credit_amount = $request['total_amount'];
                }
                $journal_credit = array(
                    'company_id'                    => $request['company_id'],
                    'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                    'account_id'                    => $account_id,
                    'journal_voucher_amount'        => $request['total_amount'],
                    'account_id_default_status'     => $account_default_status,
                    'account_id_status'             => $account_setting_status,
                    'journal_voucher_debit_amount'  => $debit_amount,
                    'journal_voucher_credit_amount' => $credit_amount,
                    'updated_id'                    => $request['updated_id'],
                    'created_id'                    => $request['created_id']
                );
                JournalVoucherItem::create($journal_credit);
            }
        }


       $data =  SalesInvoice::create($request->all());
        
        return $data;
    }

    public function getAccountDefaultStatus($account_id)
    {
        $data = AcctAccount::where('account_id',$account_id)->first();

        return $data['account_default_status'];
    }

    public function getAccountSettingStatus($account_setting_name)
    {
        $data = AcctAccountSetting::where('account_setting_name', $account_setting_name)
        ->first();

        return $data['account_setting_status'];
    }

    public function getAccountId($account_setting_name)
    {
        $data = AcctAccountSetting::where('account_setting_name', $account_setting_name)
        ->first();

        return $data['account_id'];
    }

    public function getTransactionModuleID($transaction_module_code)
    {
        $data = PreferenceTransactionModule::where('transaction_module_code',$transaction_module_code)->first();

        return $data['transaction_module_id'];
    }

    public function getTransactionModuleName($transaction_module_code)
    {
        $data = PreferenceTransactionModule::where('transaction_module_code',$transaction_module_code)->first();

        return $data['transaction_module_name'];
    }

    public function postDataSalesInvoiceItem(Request $request)
    {
        $data_packge = InvtItemPackge::where('company_id',$request['company_id'])
        ->where('item_id', $request['item_id'])
        ->where('item_unit_id', $request['item_unit_id'])
        ->where('item_category_id', $request['item_category_id'])
        ->first();

        $data_stock = InvtItemStock::where('company_id',$request['company_id'])
        ->where('item_id', $request['item_id'])
        ->where('item_unit_id', $request['item_unit_id'])
        ->where('item_category_id', $request['item_category_id'])
        ->first();

        if(isset($data_stock) && ($request['data_state'] == 0)){
            $table = InvtItemStock::findOrFail($data_stock['item_stock_id']);
            $table->last_balance = $data_stock['last_balance'] - ($request['quantity'] * $data_packge['item_default_quantity']);
            // $table->updated_id = $request['updated_id'];
            $table->save();

        }
        $data = SalesInvoiceItem::create($request->all());
        return $data;
        // return $request->all();
    }

    public function getDataSalesInvoice()
    {
        $data = SalesInvoice::where('sales_invoice.data_state',0)
        ->get();

        return json_encode($data);
    }

    public function getDataExpenditure()
    {
        $data = Expenditure::where('data_state',0)
        ->get();

        return json_encode($data);
    }

    public function getDataProfitLossReport()
    {
        $data = AcctProfitLossReport::where('data_state',0)
        ->get();

        return json_encode($data);
    }

    public function getDataJournalVoucher()
    {
        $data = JournalVoucher::join('acct_journal_voucher_item','acct_journal_voucher_item.journal_voucher_id','acct_journal_voucher.journal_voucher_id')
        ->where('acct_journal_voucher.data_state',0)
        ->get();

        return json_encode($data);
    }

    // public function getDataCoreEmployee()
    // {
       

    //     $core_member = CoreEmployee::where('data_state',0)
    //     ->get();

        
    //     return json_encode($core_member);
    // }

    // public function postDataCoreEmployee(Request $request)
    // {
    //     $data_member = CoreEmployee::where('employee_number',$request->employee_number)
    //     ->first();
    //     $data = CoreEmployee::where('employee_number',$request->member_no)
    //     ->update(['amount_debt' => $data_member['amount_debt'] + $request->member_account_receivable_amount_temp]);

    //     return $data;
    // }

    public function getDataItemRack()
    {
        $data = InvtItemRack::get();

        return json_encode($data);
    }

    // public function postDataCoreMemberKopkar(Request $request)
    // {
    //     $data_member = CoreMemberKopkar::where('member_no',$request->member_no)
    //     ->first();
    //     $data = CoreMemberKopkar::where('member_no',$request->member_no)
    //     ->update(['member_account_receivable_amount' => $data_member['member_account_receivable_amount'] + $request->member_account_receivable_amount_temp, 'member_account_credits_store_debt' => $data_member['member_account_credits_store_debt'] + $request->member_account_receivable_amount_temp]);

    //     return $data;
    // }

    public function getDataPreferenceVoucher()
    {
        $data = PreferenceVoucher::get();

        return json_encode($data);
    }

    public function postDataLoginLog(Request $request)
    {
        $data = SystemLoginLog::create($request->all());

        return $data;
    }

    public function postDataCloseCashier(Request $request)
    {
        $data = CloseCashierLog::create($request->all());

        return $data;
    }

    public function postData(Request $request)
    {
        DB::beginTransaction();
        try {
            CloseCashierLog::insert($request->closeCashier);
            SalesInvoice::insert($request->sales);
            SalesInvoiceItem::insert($request->salesItem);

            foreach ($request->salesRemove as $key => $val) {
                $data = array(
                    'company_id'                => $val['company_id'],
                    'sales_invoice_id'          => $val['sales_invoice_id'],
                    'sales_invoice_item_id'     => $val['sales_invoice_item_id'],
                    'sales_invoice_no'          => $val['sales_invoice_no'],
                    'sii_amount'                => $val['sii_amount'],
                    'data_state'                => $val['data_state'],
                    'created_id'                => $val['created_id'],
                    'updated_id'                => $val['updated_id'],
                    'created_at'                => $val['created_at'],
                    'updated_at'                => $val['updated_at'],
                );
                SIIRemoveLog::insert($data);
            }

            foreach ($request->loginLog as $key => $val) {
                $data = array(
                    'user_id'       => $val['user_id'],
                    'company_id'    => $val['company_id'],
                    'log_time'      => $val['log_time'],
                    'log_status'    => $val['log_status'],
                    'status_upload' => $val['status_upload'],
                    'created_at'    => $val['created_at'],
                    'updated_at'    => $val['updated_at'],
                );
                SystemLoginLog::insert($data);
            }

            // foreach ($request->member as $key => $val) {
            //     $data_member = CoreMember::where('member_no',$val['member_no'])
            //     ->first();

            //     CoreMember::where('member_no', $val['member_no'])
            //     ->update(['member_account_receivable_amount' => $data_member['member_account_receivable_amount'] + $val['member_account_receivable_amount_temp']]);
    

            //      }

            foreach ($request->sales as $key => $val) {
                $transaction_module_code = 'PJL';
                $transaction_module_id  = $this->getTransactionModuleID($transaction_module_code);

                $data_journal = array(
                    'company_id'                    => $val['company_id'],
                    'journal_voucher_status'        => 1,
                    'journal_voucher_description'   => $this->getTransactionModuleName($transaction_module_code),
                    'journal_voucher_title'         => $this->getTransactionModuleName($transaction_module_code),
                    'transaction_module_id'         => $transaction_module_id,
                    'transaction_module_code'       => $transaction_module_code,
                    'journal_voucher_date'          => $val['sales_invoice_date'],
                    'transaction_journal_no'        => $val['sales_invoice_no'],
                    'journal_voucher_period'        => date('Ym', strtotime($val['sales_invoice_date'])),
                    'updated_id'                    => $val['updated_id'],
                    'created_id'                    => $val['created_id']
                );
                JournalVoucher::create($data_journal);

                if ($val['sales_payment_method'] == 1) {
                    $account_setting_name = 'sales_cash_account';
                    $account_id = $this->getAccountId($account_setting_name);
                    $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
                    $account_default_status = $this->getAccountDefaultStatus($account_id);
                    $journal_voucher_id = JournalVoucher::orderBy('journal_voucher_id', 'DESC')->where('company_id', $val['company_id'])->first();
                    if ($account_setting_status == 0){
                        $debit_amount = $val['total_amount'];
                        $credit_amount = 0;
                    } else {
                        $debit_amount = 0;
                        $credit_amount = $val['total_amount'];
                    }
                    $journal_debit = array(
                        'company_id'                    => $val['company_id'],
                        'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                        'account_id'                    => $account_id,
                        'journal_voucher_amount'        => $val['total_amount'],
                        'account_id_default_status'     => $account_default_status,
                        'account_id_status'             => $account_setting_status,
                        'journal_voucher_debit_amount'  => $debit_amount,
                        'journal_voucher_credit_amount' => $credit_amount,
                        'updated_id'                    => $val['updated_id'],
                        'created_id'                    => $val['created_id']
                    );
                    JournalVoucherItem::create($journal_debit);

                    $account_setting_name = 'sales_account';
                    $account_id = $this->getAccountId($account_setting_name);
                    $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
                    $account_default_status = $this->getAccountDefaultStatus($account_id);
                    $journal_voucher_id = JournalVoucher::orderBy('journal_voucher_id', 'DESC')->where('company_id', $val['company_id'])->first();
                    if ($account_setting_status == 0){
                        $debit_amount = $val['total_amount'];
                        $credit_amount = 0;
                    } else {
                        $debit_amount = 0;
                        $credit_amount = $val['total_amount'];
                    }
                    $journal_credit = array(
                        'company_id'                    => $val['company_id'],
                        'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                        'account_id'                    => $account_id,
                        'journal_voucher_amount'        => $val['total_amount'],
                        'account_id_default_status'     => $account_default_status,
                        'account_id_status'             => $account_setting_status,
                        'journal_voucher_debit_amount'  => $debit_amount,
                        'journal_voucher_credit_amount' => $credit_amount,
                        'updated_id'                    => $val['updated_id'],
                        'created_id'                    => $val['created_id']
                    );
                    JournalVoucherItem::create($journal_credit);
                } else if ($val['sales_payment_method'] == 2) {
                    $account_setting_name = 'sales_cash_receivable_account';
                    $account_id = $this->getAccountId($account_setting_name);
                    $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
                    $account_default_status = $this->getAccountDefaultStatus($account_id);
                    $journal_voucher_id = JournalVoucher::orderBy('journal_voucher_id', 'DESC')->where('company_id', $val['company_id'])->first();
                    if ($account_setting_status == 0){
                        $debit_amount = $val['total_amount'];
                        $credit_amount = 0;
                    } else {
                        $debit_amount = 0;
                        $credit_amount = $val['total_amount'];
                    }
                    $journal_debit = array(
                        'company_id'                    => $val['company_id'],
                        'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                        'account_id'                    => $account_id,
                        'journal_voucher_amount'        => $val['total_amount'],
                        'account_id_default_status'     => $account_default_status,
                        'account_id_status'             => $account_setting_status,
                        'journal_voucher_debit_amount'  => $debit_amount,
                        'journal_voucher_credit_amount' => $credit_amount,
                        'updated_id'                    => $val['updated_id'],
                        'created_id'                    => $val['created_id']
                    );
                    JournalVoucherItem::create($journal_debit);

                    $account_setting_name = 'sales_receivable_account';
                    $account_id = $this->getAccountId($account_setting_name);
                    $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
                    $account_default_status = $this->getAccountDefaultStatus($account_id);
                    $journal_voucher_id = JournalVoucher::orderBy('journal_voucher_id', 'DESC')->where('company_id', $val['company_id'])->first();
                    if ($account_setting_status == 0){
                        $debit_amount = $val['total_amount'];
                        $credit_amount = 0;
                    } else {
                        $debit_amount = 0;
                        $credit_amount = $val['total_amount'];
                    }
                    $journal_credit = array(
                        'company_id'                    => $val['company_id'],
                        'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                        'account_id'                    => $account_id,
                        'journal_voucher_amount'        => $val['total_amount'],
                        'account_id_default_status'     => $account_default_status,
                        'account_id_status'             => $account_setting_status,
                        'journal_voucher_debit_amount'  => $debit_amount,
                        'journal_voucher_credit_amount' => $credit_amount,
                        'updated_id'                    => $val['updated_id'],
                        'created_id'                    => $val['created_id']
                    );
                    JournalVoucherItem::create($journal_credit);
                } else {
                    $account_setting_name = 'sales_cashless_cash_account';
                    $account_id = $this->getAccountId($account_setting_name);
                    $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
                    $account_default_status = $this->getAccountDefaultStatus($account_id);
                    $journal_voucher_id = JournalVoucher::orderBy('journal_voucher_id', 'DESC')->where('company_id', $val['company_id'])->first();
                    if ($account_setting_status == 0){
                        $debit_amount = $val['total_amount'];
                        $credit_amount = 0;
                    } else {
                        $debit_amount = 0;
                        $credit_amount = $val['total_amount'];
                    }
                    $journal_debit = array(
                        'company_id'                    => $val['company_id'],
                        'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                        'account_id'                    => $account_id,
                        'journal_voucher_amount'        => $val['total_amount'],
                        'account_id_default_status'     => $account_default_status,
                        'account_id_status'             => $account_setting_status,
                        'journal_voucher_debit_amount'  => $debit_amount,
                        'journal_voucher_credit_amount' => $credit_amount,
                        'updated_id'                    => $val['updated_id'],
                        'created_id'                    => $val['created_id']
                    );
                    JournalVoucherItem::create($journal_debit);

                    $account_setting_name = 'sales_cashless_account';
                    $account_id = $this->getAccountId($account_setting_name);
                    $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
                    $account_default_status = $this->getAccountDefaultStatus($account_id);
                    $journal_voucher_id = JournalVoucher::orderBy('journal_voucher_id', 'DESC')->where('company_id', $val['company_id'])->first();
                    if ($account_setting_status == 0){
                        $debit_amount = $val['total_amount'];
                        $credit_amount = 0;
                    } else {
                        $debit_amount = 0;
                        $credit_amount = $val['total_amount'];
                    }
                    $journal_credit = array(
                        'company_id'                    => $val['company_id'],
                        'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                        'account_id'                    => $account_id,
                        'journal_voucher_amount'        => $val['total_amount'],
                        'account_id_default_status'     => $account_default_status,
                        'account_id_status'             => $account_setting_status,
                        'journal_voucher_debit_amount'  => $debit_amount,
                        'journal_voucher_credit_amount' => $credit_amount,
                        'updated_id'                    => $val['updated_id'],
                        'created_id'                    => $val['created_id']
                    );
                    JournalVoucherItem::create($journal_credit);
                }


                

                if ($val['data_state'] == 1) {
                    $transaction_module_code = 'HPSPJL';
                    $transaction_module_id  = $this->getTransactionModuleID($transaction_module_code);
                    $journal = array(
                        'company_id'                    => $val['company_id'],
                        'journal_voucher_status'        => 1,
                        'journal_voucher_description'   => $this->getTransactionModuleName($transaction_module_code),
                        'journal_voucher_title'         => $this->getTransactionModuleName($transaction_module_code),
                        'transaction_module_id'         => $transaction_module_id,
                        'transaction_module_code'       => $transaction_module_code,
                        'transaction_journal_no'        => $val['sales_invoice_no'],
                        'journal_voucher_date'          => $val['sales_invoice_date'],
                        'journal_voucher_period'        => date('Ym', strtotime($val['sales_invoice_date'])),
                        'updated_id'                    => $val['updated_id'],
                        'created_id'                    => $val['created_id']
                    );
                    JournalVoucher::create($journal);
                    if ($val['sales_payment_method'] == 1) {
                        $account_setting_name = 'sales_cash_account';
                        $account_id = $this->getAccountId($account_setting_name);
                        $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
                        $account_default_status = $this->getAccountDefaultStatus($account_id);
                        $journal_voucher_id = JournalVoucher::orderBy('journal_voucher_id', 'DESC')->where('company_id', $val['company_id'])->first();
                        if($account_setting_status == 0){
                            $account_setting_status = 1;
                        } else {
                            $account_setting_status = 0;
                        }
                        if ($account_setting_status == 0){ 
                            $debit_amount = $val['total_amount'];
                            $credit_amount = 0;
                        } else {
                            $debit_amount = 0;
                            $credit_amount = $val['total_amount'];
                        }
                        $journal_debit = array(
                            'company_id'                    => $val['company_id'],
                            'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                            'account_id'                    => $account_id,
                            'journal_voucher_amount'        => $val['total_amount'],
                            'account_id_default_status'     => $account_default_status,
                            'account_id_status'             => $account_setting_status,
                            'journal_voucher_debit_amount'  => $debit_amount,
                            'journal_voucher_credit_amount' => $credit_amount,
                            'updated_id'                    => $val['updated_id'],
                            'created_id'                    => $val['created_id']
                        );
                        JournalVoucherItem::create($journal_debit);
                
                        $account_setting_name = 'sales_account';
                        $account_id = $this->getAccountId($account_setting_name);
                        $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
                        $account_default_status = $this->getAccountDefaultStatus($account_id);
                        $journal_voucher_id = JournalVoucher::orderBy('journal_voucher_id', 'DESC')->where('company_id', $val['company_id'])->first();
                        if($account_setting_status == 1){
                            $account_setting_status = 0;
                        } else {
                            $account_setting_status = 1;
                        }
                        if ($account_setting_status == 0){
                            $debit_amount = $val['total_amount'];
                            $credit_amount = 0;
                        } else {
                            $debit_amount = 0;
                            $credit_amount = $val['total_amount'];
                        }
                        $journal_credit = array(
                            'company_id'                    => $val['company_id'],
                            'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                            'account_id'                    => $account_id,
                            'journal_voucher_amount'        => $val['total_amount'],
                            'account_id_default_status'     => $account_default_status,
                            'account_id_status'             => $account_setting_status,
                            'journal_voucher_debit_amount'  => $debit_amount,
                            'journal_voucher_credit_amount' => $credit_amount,
                            'updated_id'                    => $val['updated_id'],
                            'created_id'                    => $val['created_id']
                        );
                        JournalVoucherItem::create($journal_credit);
                    } else if ($val['sales_payment_method'] == 2) {
                        $account_setting_name = 'sales_cash_receivable_account';
                        $account_id = $this->getAccountId($account_setting_name);
                        $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
                        $account_default_status = $this->getAccountDefaultStatus($account_id);
                        $journal_voucher_id = JournalVoucher::orderBy('journal_voucher_id', 'DESC')->where('company_id', $val['company_id'])->first();
                        if($account_setting_status == 0){
                            $account_setting_status = 1;
                        } else {
                            $account_setting_status = 0;
                        }
                        if ($account_setting_status == 0){ 
                            $debit_amount = $val['total_amount'];
                            $credit_amount = 0;
                        } else {
                            $debit_amount = 0;
                            $credit_amount = $val['total_amount'];
                        }
                        $journal_debit = array(
                            'company_id'                    => $val['company_id'],
                            'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                            'account_id'                    => $account_id,
                            'journal_voucher_amount'        => $val['total_amount'],
                            'account_id_default_status'     => $account_default_status,
                            'account_id_status'             => $account_setting_status,
                            'journal_voucher_debit_amount'  => $debit_amount,
                            'journal_voucher_credit_amount' => $credit_amount,
                            'updated_id'                    => $val['updated_id'],
                            'created_id'                    => $val['created_id']
                        );
                        JournalVoucherItem::create($journal_debit);
                
                        $account_setting_name = 'sales_receivable_account';
                        $account_id = $this->getAccountId($account_setting_name);
                        $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
                        $account_default_status = $this->getAccountDefaultStatus($account_id);
                        $journal_voucher_id = JournalVoucher::orderBy('journal_voucher_id', 'DESC')->where('company_id', $val['company_id'])->first();
                        if($account_setting_status == 1){
                            $account_setting_status = 0;
                        } else {
                            $account_setting_status = 1;
                        }
                        if ($account_setting_status == 0){
                            $debit_amount = $val['total_amount'];
                            $credit_amount = 0;
                        } else {
                            $debit_amount = 0;
                            $credit_amount = $val['total_amount'];
                        }
                        $journal_credit = array(
                            'company_id'                    => $val['company_id'],
                            'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                            'account_id'                    => $account_id,
                            'journal_voucher_amount'        => $val['total_amount'],
                            'account_id_default_status'     => $account_default_status,
                            'account_id_status'             => $account_setting_status,
                            'journal_voucher_debit_amount'  => $debit_amount,
                            'journal_voucher_credit_amount' => $credit_amount,
                            'updated_id'                    => $val['updated_id'],
                            'created_id'                    => $val['created_id']
                        );
                        JournalVoucherItem::create($journal_credit);
                    } else {
                        $account_setting_name = 'sales_cashless_cash_account';
                        $account_id = $this->getAccountId($account_setting_name);
                        $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
                        $account_default_status = $this->getAccountDefaultStatus($account_id);
                        $journal_voucher_id = JournalVoucher::orderBy('journal_voucher_id', 'DESC')->where('company_id', $val['company_id'])->first();
                        if($account_setting_status == 0){
                            $account_setting_status = 1;
                        } else {
                            $account_setting_status = 0;
                        }
                        if ($account_setting_status == 0){ 
                            $debit_amount = $val['total_amount'];
                            $credit_amount = 0;
                        } else {
                            $debit_amount = 0;
                            $credit_amount = $val['total_amount'];
                        }
                        $journal_debit = array(
                            'company_id'                    => $val['company_id'],
                            'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                            'account_id'                    => $account_id,
                            'journal_voucher_amount'        => $val['total_amount'],
                            'account_id_default_status'     => $account_default_status,
                            'account_id_status'             => $account_setting_status,
                            'journal_voucher_debit_amount'  => $debit_amount,
                            'journal_voucher_credit_amount' => $credit_amount,
                            'updated_id'                    => $val['updated_id'],
                            'created_id'                    => $val['created_id']
                        );
                        JournalVoucherItem::create($journal_debit);
                
                        $account_setting_name = 'sales_cashless_account';
                        $account_id = $this->getAccountId($account_setting_name);
                        $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
                        $account_default_status = $this->getAccountDefaultStatus($account_id);
                        $journal_voucher_id = JournalVoucher::orderBy('journal_voucher_id', 'DESC')->where('company_id', $val['company_id'])->first();
                        if($account_setting_status == 1){
                            $account_setting_status = 0;
                        } else {
                            $account_setting_status = 1;
                        }
                        if ($account_setting_status == 0){
                            $debit_amount = $val['total_amount'];
                            $credit_amount = 0;
                        } else {
                            $debit_amount = 0;
                            $credit_amount = $val['total_amount'];
                        }
                        $journal_credit = array(
                            'company_id'                    => $val['company_id'],
                            'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                            'account_id'                    => $account_id,
                            'journal_voucher_amount'        => $val['total_amount'],
                            'account_id_default_status'     => $account_default_status,
                            'account_id_status'             => $account_setting_status,
                            'journal_voucher_debit_amount'  => $debit_amount,
                            'journal_voucher_credit_amount' => $credit_amount,
                            'updated_id'                    => $val['updated_id'],
                            'created_id'                    => $val['created_id']
                        );
                        JournalVoucherItem::create($journal_credit);
                    }
                }
            }

            foreach ($request->salesItem as $key => $val) {
                $data_packge = InvtItemPackge::where('company_id',$val['company_id'])
                ->where('item_id', $val['item_id'])
                ->where('item_unit_id', $val['item_unit_id'])
                ->where('item_category_id', $val['item_category_id'])
                ->first();

                $data_stock = InvtItemStock::where('company_id',$val['company_id'])
                ->where('item_id', $val['item_id'])
                ->where('item_unit_id', $val['item_unit_id'])
                ->where('item_category_id', $val['item_category_id'])
                ->first();

                if(isset($data_stock) && ($val['data_state'] == 0)){
                    $table = InvtItemStock::findOrFail($data_stock['item_stock_id']);
                    $table->last_balance = (int)$data_stock['last_balance'] - ((int)$val['quantity'] * (int)$data_packge['item_default_quantity']);
                    $table->save();
                }
            }

            DB::commit();
            return 'true';

        } catch (\Throwable $th) {

            DB::rollback();
            return 'false';

        }
    }
}
