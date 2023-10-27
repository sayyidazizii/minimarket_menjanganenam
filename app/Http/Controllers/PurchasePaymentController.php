<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AcctAccount;
use App\Models\CoreBank;
use App\Models\CoreSupplier;
use App\Models\JournalVoucher;
use App\Models\JournalVoucherItem;
use App\Models\PreferenceCompany;
use App\Models\PreferenceTransactionModule;
use App\Models\PurchaseInvoice;
use App\Models\PurchasePayment;
use App\Models\PurchasePaymentItem;
use App\Models\PurchasePaymentTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PurchasePaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        
    }

    public function index()
    {
        Session::forget('purchasepaymentelements');
        Session::forget('datapurchasepaymenttransfer');

        if(!Session::get('start_date')){
            $start_date     = date('Y-m-d');
        }else{
            $start_date = Session::get('start_date');
        }
        if(!Session::get('end_date')){
            $end_date     = date('Y-m-d');
        }else{
            $end_date = Session::get('end_date');
        }

        // $supplier_id        = Session::get('supplier_id');

        $purchasepayment    = PurchasePayment::where('data_state', 0)
        ->where('payment_date', '>=', $start_date)
        ->where('payment_date', '<=',$end_date)
        ->get();

        // dd($purchasepayment);
        return view('content.PurchasePayment.ListPurchasePayment', compact('start_date','end_date','purchasepayment'));
    }

    public function filterPurchasePayment(Request $request)
    {
        $start_date     = $request->start_date;
        $end_date       = $request->end_date;
        
        Session::put('start_date', $start_date);
        Session::put('end_date', $end_date);
    
        return redirect()->back();
    }

    public function resetFilterPurchasePayment()
    {
        Session::forget('start_date');
        Session::forget('end_date');
    
        return redirect()->back();
    }

    public function searchPurchasePayment()
    {
        Session::forget('purchasepaymentelements');
        Session::forget('datapurchasepaymenttransfer');

        $coresupplier = PurchaseInvoice::select('purchase_invoice.supplier_id', 'purchase_invoice.purchase_payment_method', 'core_supplier.supplier_name', 'core_supplier.supplier_address', DB::raw("SUM(purchase_invoice.owing_amount) as total_owing_amount"))
        ->join('core_supplier', 'core_supplier.supplier_id', 'purchase_invoice.supplier_id')
        ->where('purchase_invoice.data_state', 0)
        ->where('purchase_invoice.purchase_payment_method', 1)
        ->where('purchase_invoice.company_id', Auth::user()->company_id)
        ->where('core_supplier.data_state', 0)
        ->groupBy('purchase_invoice.supplier_id')
        ->orderBy('core_supplier.supplier_name', 'ASC')
        ->get();
        // dd($coresupplier);
        return view('content.PurchasePayment.SearchPurchasePayment', compact('coresupplier'));
    }

    public function getPayableAmount($supplier_id)
    {
        $data_purchase = PurchaseInvoice::where('supplier_id',$supplier_id)
        ->where('purchase_payment_method',1)
        ->get();

        $payable_amount = 0;
        foreach ($data_purchase as $key => $val) {
            $payable_amount += $val['total_amount'];
        }

        return $payable_amount;
    }

    public function selectSupplierPurchasePayment($supplier_id)
    {
        $purchaseinvoiceowing = PurchaseInvoice::select('purchase_invoice.purchase_invoice_id', 'purchase_invoice.purchase_payment_method', 'purchase_invoice.supplier_id', 'purchase_invoice.owing_amount', 'purchase_invoice.purchase_invoice_date', 'purchase_invoice.paid_amount', 'purchase_invoice.purchase_invoice_no', 'purchase_invoice.subtotal_amount_total', 'purchase_invoice.discount_percentage_total', 'purchase_invoice.discount_amount_total', 'purchase_invoice.tax_ppn_amount', 'purchase_invoice.total_amount','purchase_invoice.company_id','purchase_invoice.data_state')
        ->where('purchase_invoice.supplier_id', $supplier_id)
        ->where('purchase_invoice.company_id', Auth::user()->company_id)
        ->where('purchase_invoice.purchase_payment_method', 1)
        ->where('purchase_invoice.owing_amount', '!=',0)
        ->where('purchase_invoice.data_state', 0)
        ->get();
        // dd($purchaseinvoiceowing);

        $accountlist = AcctAccount::select(DB::raw("CONCAT(account_code,' - ',account_name) AS full_account"),'account_id')
        ->where('data_state',0)
        ->where('company_id',Auth::user()->company_id)
        ->get()
        ->pluck('full_account','account_id');

        $supplier = CoreSupplier::findOrfail($supplier_id);

        $banklist = CoreBank::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('bank_name','bank_id');
        // dd($data_payable);
        $purchasepaymentelements = Session::get('purchasepaymentelements');
        $purchasepaymenttransfer = Session::get('datapurchasepaymenttransfer');
        return view('content.PurchasePayment.AddPurchasePayment', compact('purchaseinvoiceowing','accountlist','banklist','purchasepaymentelements','purchasepaymenttransfer','supplier_id','supplier'));
    }

    public function elements_add(Request $request){
        $purchasepaymentelements= Session::get('purchasepaymentelements');
        if(!$purchasepaymentelements || $purchasepaymentelements == ''){
            $purchasepaymentelements['payment_date']                = '';
            $purchasepaymentelements['payment_remark']              = '';
            $purchasepaymentelements['cash_account_id']             = '';
            $purchasepaymentelements['payment_total_cash_amount']   = '';
        }
        $purchasepaymentelements[$request->name] = $request->value;
        Session::put('purchasepaymentelements', $purchasepaymentelements);
    }

    public function processAddTransferArray(Request $request)
    {
        $purchasepaymenttransfer = array(
            'bank_id'                       => $request->bank_id,
            'payment_transfer_account_name' => $request->payment_transfer_account_name,
            'payment_transfer_account_no'   => $request->payment_transfer_account_no,
            'payment_transfer_amount'       => $request->payment_transfer_amount,
        );

        $lastpurchasepaymenttransfer = Session::get('datapurchasepaymenttransfer');
        if($lastpurchasepaymenttransfer !== null){
            array_push($lastpurchasepaymenttransfer, $purchasepaymenttransfer);
            Session::put('datapurchasepaymenttransfer', $lastpurchasepaymenttransfer);
        }else{
            $lastpurchasepaymenttransfer = [];
            array_push($lastpurchasepaymenttransfer, $purchasepaymenttransfer);
            Session::push('datapurchasepaymenttransfer', $purchasepaymenttransfer);
        }
    }

    public function processAddPurchasePayment(Request $request)
    {
        // dd($request->all());

        $allrequest = $request->all();
        $datapurchasepaymenttransfer = Session::get('datapurchasepaymenttransfer');

        $fields = $request->validate([
            'payment_date'                      => 'required',
        ]);

        $data = array (
            'payment_date'                      => $fields['payment_date'],
            'company_id'                        => Auth::user()->company_id,
            'cash_account_id'				    => $request->cash_account_id,
            'supplier_id'						=> $request->supplier_id,
            'payment_remark'					=> $request->payment_remark,
            'payment_amount'					=> $request->payment_amount,
            'payment_allocated'					=> $request->allocation_total,
            'payment_shortover'					=> $request->shortover_total,
            'payment_total_amount'				=> $request->payment_amount,
            'payment_total_cash_amount'			=> $request->payment_total_cash_amount,
            'payment_total_transfer_amount'		=> $request->payment_total_transfer_amount,
            'created_id'						=> Auth::id(),
            'updated_id'						=> Auth::id(),
        );

        $payment_total_amount = $data['payment_allocated'] + $data['payment_shortover'];

        $selisih_shortover = $data['payment_total_amount'] - $payment_total_amount;

        $transaction_module_code 	= "PH";

        $transactionmodule 		    = PreferenceTransactionModule::where('transaction_module_code', $transaction_module_code)
        ->first();

        $transaction_module_id 		= $transactionmodule['transaction_module_id'];

        $preferencecompany 			= PreferenceCompany::first();
        
        if(PurchasePayment::create($data)){
            $PurchasePayment_last 		= PurchasePayment::select('payment_id', 'payment_no')
            ->where('created_id', $data['created_id'])
            ->orderBy('payment_id', 'DESC')
            ->first();
            
            $journal_voucher_period 	= date("Ym", strtotime($data['payment_date']));

            $data_journal = array(
                'company_id'				    => $data['company_id'],
                'journal_voucher_period' 		=> $journal_voucher_period,
                'journal_voucher_date'			=> $data['payment_date'],
                'journal_voucher_status'        => 1,
                'journal_voucher_title'			=> 'Pelunasan Hutang '.$PurchasePayment_last['payment_no'],
                'journal_voucher_no'			=> $PurchasePayment_last['payment_no'],
                'journal_voucher_description'	=> $data['payment_remark'],
                'transaction_module_id'			=> $transaction_module_id,
                'transaction_module_code'		=> $transaction_module_code,
                'transaction_journal_no' 		=> $PurchasePayment_last['payment_no'],
                'created_id' 					=> $data['created_id'],
                'updated_id' 					=> $data['created_id'],
            );
            
            JournalVoucher::create($data_journal);		

            $journalvoucher = JournalVoucher::where('created_id', $data['created_id'])
            ->orderBy('journal_voucher_id', 'DESC')
            ->first();

            $journal_voucher_id 	= $journalvoucher['journal_voucher_id'];

            $payment = PurchasePayment::where('created_id', $data['created_id'])
            ->orderBy('payment_id', 'DESC')
            ->first();

            $payment_id = $payment['payment_id'];

            for($i = 1; $i < $request->item_total; $i++){
                $data_paymentitem = array(
                    'payment_id'		 		=> $payment_id,
                    'purchase_invoice_id' 		=> $allrequest[$i.'_purchase_invoice_id'],
                    'purchase_invoice_no' 		=> $allrequest[$i.'_purchase_invoice_no'],
                    'purchase_invoice_date' 	=> $allrequest[$i.'_purchase_invoice_date'],
                    'purchase_invoice_amount'	=> $allrequest[$i.'_purchase_invoice_amount'],
                    'total_amount' 				=> $allrequest[$i.'_total_amount'],
                    'paid_amount' 				=> $allrequest[$i.'_paid_amount'],
                    'owing_amount' 				=> $allrequest[$i.'_owing_amount'],
                    'allocation_amount' 		=> $allrequest[$i.'_allocation'],
                    'shortover_amount'	 		=> $allrequest[$i.'_shortover'],
                    'last_balance' 				=> $allrequest[$i.'_last_balance']
                );

                if($data_paymentitem['allocation_amount'] > 0){
                    if(PurchasePaymentItem::create($data_paymentitem)){

                        $purchaseinvoice = PurchaseInvoice::where('data_state', 0)
                        ->where('purchase_invoice_id', $data_paymentitem['purchase_invoice_id'])
                        ->first();

                        $purchaseinvoice->paid_amount       = $purchaseinvoice['paid_amount'] + $data_paymentitem['allocation_amount'] + $data_paymentitem['shortover_amount'];
                        $purchaseinvoice->owing_amount      = $data_paymentitem['last_balance'];
                        $purchaseinvoice->shortover_amount  = $purchaseinvoice['shortover_amount'] + $data_paymentitem['shortover_amount'];
                        $purchaseinvoice->save();

                        $msg = "Tambah Pelunasan Hutang Berhasil";
                        continue;
                    }else{
                        $msg = "Tambah Pelunasan Hutang Gagal";
                        return redirect('/purchase-payment/select-supplier/'.$data['supplier_id'])->with('msg',$msg);
                    }
                }
                
            }

            $account 		= AcctAccount::where('account_id', $preferencecompany['account_payable_id'])
            ->where('data_state', 0)
            ->first();

            $account_id_default_status 		= $account['account_default_status'];

            $data_debit = array (
                'journal_voucher_id'			=> $journal_voucher_id,
                'account_id'					=> $preferencecompany['account_payable_id'],
                'journal_voucher_description'	=> $data_journal['journal_voucher_description'],
                'journal_voucher_amount'		=> $payment_total_amount,
                'journal_voucher_debit_amount'	=> $payment_total_amount,
                'account_id_default_status'		=> $account_id_default_status,
                'account_id_status'				=> 0,
                'company_id'                    => Auth::user()->company_id,
                'created_id'                    => Auth::id(),
                'updated_id'                    => Auth::id(),
            );

            JournalVoucherItem::create($data_debit);

            if($selisih_shortover > 0){

                $account 		= AcctAccount::where('account_id', $preferencecompany['account_shortover_id'])
                ->where('data_state', 0)
                ->first();

                $account_id_default_status 		= $account['account_default_status'];

                $data_debit = array (
                    'journal_voucher_id'			=> $journal_voucher_id,
                    'account_id'					=> $preferencecompany['account_shortover_id'],
                    'journal_voucher_description'	=> $data_journal['journal_voucher_description'],
                    'journal_voucher_amount'		=> $selisih_shortover,
                    'journal_voucher_debit_amount'	=> $selisih_shortover,
                    'account_id_default_status'		=> $account_id_default_status,
                    'account_id_status'				=> 0,
                    'company_id'                    => Auth::user()->company_id,
                    'created_id'                    => Auth::id(),
                    'updated_id'                    => Auth::id(),
                );

                JournalVoucherItem::create($data_debit);
            } else if($selisih_shortover < 0){

                $account 		= AcctAccount::where('account_id', $preferencecompany['account_shortover_id'])
                ->where('data_state', 0)
                ->first();

                $account_id_default_status 		= $account['account_default_status'];

                $data_credit = array (
                    'journal_voucher_id'			=> $journal_voucher_id,
                    'account_id'					=> $preferencecompany['account_shortover_id'],
                    'journal_voucher_description'	=> $data_journal['journal_voucher_description'],
                    'journal_voucher_amount'		=> $selisih_shortover,
                    'journal_voucher_credit_amount'	=> $selisih_shortover,
                    'account_id_default_status'		=> $account_id_default_status,
                    'account_id_status'				=> 1,
                    'company_id'                    => Auth::user()->company_id,
                    'created_at'                    => Auth::id(),
                    'updated_at'                    => Auth::id(),
                );

                JournalVoucherItem::create($data_credit);
            }


            if($data['payment_total_cash_amount'] != '' || $data['payment_total_cash_amount'] != 0){

                $account 		= AcctAccount::where('account_id', $data['cash_account_id'])
                ->where('data_state', 0)
                ->first();

                $account_id_default_status 		= $account['account_default_status'];

                $data_credit = array (
                    'journal_voucher_id'			=> $journal_voucher_id,
                    'account_id'					=> $data['cash_account_id'],
                    'journal_voucher_description'	=> $data_journal['journal_voucher_description'],
                    'journal_voucher_amount'		=> $data['payment_total_cash_amount'],
                    'journal_voucher_credit_amount'	=> $data['payment_total_cash_amount'],
                    'account_id_default_status'		=> $account_id_default_status,
                    'account_id_status'				=> 1,
                    'company_id'                    => Auth::user()->company_id,
                    'created_id'                    => Auth::id(),
                    'updated_id'                    => Auth::id(),
                );

                JournalVoucherItem::create($data_credit);
            }

            if(is_array($datapurchasepaymenttransfer) && !empty($datapurchasepaymenttransfer) && $datapurchasepaymenttransfer != null){
                foreach ($datapurchasepaymenttransfer as $keyTransfer => $valTransfer) {
                    $transfer_bank = CoreBank::where('bank_id', $valTransfer['bank_id'])
                    ->first();

                    $transfer_account_id = $transfer_bank['account_id'];

                    $datatransfer = array(
                        'payment_id'							=> $payment_id,
                        'bank_id'							    => $valTransfer['bank_id'],
                        'account_id'							=> $transfer_account_id,
                        'payment_transfer_bank_name'			=> $transfer_bank['bank_name'],
                        'payment_transfer_amount'				=> $valTransfer['payment_transfer_amount'],
                        'payment_transfer_account_name'			=> $valTransfer['payment_transfer_account_name'],
                        'payment_transfer_account_no'			=> $valTransfer['payment_transfer_account_no'],
                    );

                    if(PurchasePaymentTransfer::create($datatransfer)){

                        $account 		= AcctAccount::where('account_id', $transfer_account_id)
                        ->where('data_state', 0)
                        ->first();
        
                        $account_id_default_status 		= $account['account_default_status'];

                        $data_credit = array (
                            'journal_voucher_id'			=> $journal_voucher_id,
                            'account_id'					=> $transfer_account_id,
                            'journal_voucher_description'	=> $data_journal['journal_voucher_description'],
                            'journal_voucher_amount'		=> $datatransfer['payment_transfer_amount'],
                            'journal_voucher_credit_amount'	=> $datatransfer['payment_transfer_amount'],
                            'account_id_default_status'		=> $account_id_default_status,
                            'account_id_status'				=> 1,
                            'company_id'                    => Auth::user()->company_id,
                            'created_id'                    => Auth::id(),
                            'updated_id'                    => Auth::id(),
                        );

                        JournalVoucherItem::create($data_credit);

                            
                    }
                }
            }

            $msg = "Tambah Pelunasan Hutang Berhasil";            
            return redirect('/purchase-payment')->with('msg',$msg);
        }else{
            $msg = "Tambah Pelunasan Hutang Gagal";
            return redirect('/purchase-payment')->with('msg',$msg);
        }
    }

    public function deleteTransferArray($record_id, $supplier_id)
    {
        $arrayBaru			= array();
        $dataArrayHeader	= Session::get('datapurchasepaymenttransfer');
        
        foreach($dataArrayHeader as $key=>$val){
            if($key != $record_id){
                $arrayBaru[$key] = $val;
            }
        }
        Session::forget('datapurchasepaymenttransfer');
        Session::put('datapurchasepaymenttransfer', $arrayBaru);

        return redirect('/purchase-payment/select-supplier/'.$supplier_id);
    }

    public function getCoreBankName($bank_id)
    {
        $data = CoreBank::where('bank_id', $bank_id)
        ->where('data_state', 0)
        ->first();

        return $data['bank_name'];
    }

    public function getCoreSupplierName($supplier_id)
    {
        $supplier = CoreSupplier::where('data_state', 0)
        ->where('supplier_id', $supplier_id)
        ->first();

        return $supplier['supplier_name'];
    }

    public function getAccountName($account_id)
    {
        $account = AcctAccount::where('data_state', 0)
        ->where('account_id', $account_id)
        ->first();

        return $account['account_name'];
    }

    public function detailPurchasePayment($payment_id)
    {

        $purchasepayment = PurchasePayment::findOrFail($payment_id);

        $purchasepaymentitem = PurchasePaymentItem::select('purchase_payment_item.*', 'purchase_invoice.purchase_invoice_date', 'purchase_invoice.purchase_invoice_no', 'purchase_payment_item.shortover_amount AS shortover_value')
        ->join('purchase_invoice', 'purchase_invoice.purchase_invoice_id', 'purchase_payment_item.purchase_invoice_id')
        ->where('payment_id', $purchasepayment['payment_id'])
        ->get();

        $purchasepaymenttransfer = PurchasePaymentTransfer::where('payment_id', $purchasepayment['payment_id'])
        ->get();

        $supplier = CoreSupplier::where('data_state', 0)
        ->where('supplier_id', $purchasepayment['supplier_id'])
        ->first();
        
        return view('content/PurchasePayment/DetailPurchasePayment',compact('payment_id', 'purchasepayment', 'purchasepaymentitem', 'purchasepaymenttransfer',  'supplier'));
    }

    public function deletePurchasePayment($payment_id)
    {

        $purchasepayment = PurchasePayment::findOrFail($payment_id);

        $purchasepaymentitem = PurchasePaymentItem::select('purchase_payment_item.*', 'purchase_invoice.purchase_invoice_date', 'purchase_invoice.purchase_invoice_no', 'purchase_payment_item.shortover_amount AS shortover_value')
        ->join('purchase_invoice', 'purchase_invoice.purchase_invoice_id', 'purchase_payment_item.purchase_invoice_id')
        ->where('payment_id', $purchasepayment['payment_id'])
        ->get();

        $purchasepaymenttransfer = PurchasePaymentTransfer::where('payment_id', $purchasepayment['payment_id'])
        ->get();

        $supplier = CoreSupplier::where('data_state', 0)
        ->where('supplier_id', $purchasepayment['supplier_id'])
        ->first();
        
        return view('content.PurchasePayment.DeletePurchasePayment',compact('payment_id', 'purchasepayment', 'purchasepaymentitem', 'purchasepaymenttransfer',  'supplier'));
    }

    public function processDeletePurchasePayment(Request $request)
    {
        // dd($request->all());
        $payment_no 			        = $request->payment_no;
        
        $purchasepayment                = PurchasePayment::findOrFail($request->payment_id);
        $purchasepayment->voided_remark = $request->voided_remark;
        $purchasepayment->voided_on     = date('Y-m-d H:i:s');
        $purchasepayment->voided_id     = Auth::id();
        $purchasepayment->data_state    = 2;
        
        $purchasepaymenttransfer = PurchasePaymentTransfer::where('payment_id', $request->payment_id)->get();

        if($purchasepayment->save()){
            $purchasepaymentitem 	= PurchasePaymentItem::where('payment_id', $request->payment_id)->get();

            $subtotal_amount = 0;
            foreach ($purchasepaymentitem as $ki => $vi){
                $purchaseinvoice = PurchaseInvoice::where('purchase_invoice_id', $vi['purchase_invoice_id'])->first();

                $purchaseinvoice->paid_amount       = $purchaseinvoice['paid_amount'] - ($vi['allocation_amount'] + $vi['shortover_amount']);
                $purchaseinvoice->owing_amount      = $purchaseinvoice['owing_amount'] + ($vi['allocation_amount'] + $vi['shortover_amount']);
                $purchaseinvoice->shortover_amount  = $purchaseinvoice['shortover_amount'] - $vi['shortover_amount'];

                $purchaseinvoice->save();

                $subtotal_amount += $vi['allocation_amount'] + $vi['shortover_amount'];
            }

            $journalvoucher = JournalVoucher::where('transaction_journal_no', $payment_no)->where('company_id', Auth::user()->company_id)->first();

            $data_journal = array(
                'company_id'				    => $journalvoucher['company_id'],
                'journal_voucher_period' 		=> $journalvoucher['journal_voucher_period'],
                'journal_voucher_status'        => 1,
                'journal_voucher_date'			=> date('Y-m-d'),
                'journal_voucher_title'			=> 'Pembatalan Pelunasan Hutang '.$payment_no,
                'journal_voucher_no'			=> $payment_no,
                'journal_voucher_description'	=> $request->voided_remark,
                'transaction_module_id'			=> 10,
                'transaction_module_code'		=> 'BPH',
                'transaction_journal_no' 		=> $payment_no,
                'created_id' 					=> $journalvoucher['created_id'],
                'updated_id' 					=> $journalvoucher['created_id'],
            );
            
            JournalVoucher::create($data_journal);	
            
            $journal_voucher_id = JournalVoucher::where('transaction_journal_no', $payment_no)->orderBy('created_at', 'DESC')->where('company_id', Auth::user()->company_id)->first();

            $journalvoucher_item_first = JournalVoucherItem::where('company_id', Auth::user()->company_id)->where('journal_voucher_id', $journalvoucher['journal_voucher_id'])->first();
            $account_setting_status_first =  $journalvoucher_item_first['account_id_status'];

            $journalvoucher_item_end = JournalVoucherItem::where('company_id', Auth::user()->company_id)->where('journal_voucher_item_id', $journalvoucher_item_first['journal_voucher_item_id'] + 1)->first();
            $account_setting_status_end =  $journalvoucher_item_end['account_id_status'];

            $datapurchasepaymenttransfer = JournalVoucherItem::where('company_id', Auth::user()->company_id)->where('journal_voucher_item_id', $journalvoucher_item_first['journal_voucher_item_id'] + 2)->first();
            $account_setting_status_transfer =  $datapurchasepaymenttransfer['account_id_status'];

            if($account_setting_status_end == 0){
                $account_setting_status_end = 1;
            } else {
                $account_setting_status_end = 0;
            }
            if ($account_setting_status_end == 0){ 
                $debit_amount = $journalvoucher_item_end['journal_voucher_amount'];
                $credit_amount = 0;
            } else {
                $debit_amount = 0;
                $credit_amount = $journalvoucher_item_end['journal_voucher_amount'];
            }
            $journal_debit = array(
                'company_id'                    => Auth::user()->company_id,
                'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                'account_id'                    => $journalvoucher_item_end['account_id'],
                'journal_voucher_amount'        => $journalvoucher_item_end['journal_voucher_amount'],
                'account_id_default_status'     => $journalvoucher_item_end['account_id_default_status'],
                'account_id_status'             => $account_setting_status_end,
                'journal_voucher_debit_amount'  => $debit_amount,
                'journal_voucher_credit_amount' => $credit_amount,
                'updated_id'                    => Auth::id(),
                'created_id'                    => Auth::id()
            );
            JournalVoucherItem::create($journal_debit);
    
            if($account_setting_status_first == 1){
                $account_setting_status_first = 0;
            } else {
                $account_setting_status_first = 1;
            }
            if ($account_setting_status_first == 0){
                $debit_amount = $journalvoucher_item_first['journal_voucher_amount'];
                $credit_amount = 0;
            } else {
                $debit_amount = 0;
                $credit_amount = $journalvoucher_item_first['journal_voucher_amount'];
            }
            $journal_credit = array(
                'company_id'                    => Auth::user()->company_id,
                'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                'account_id'                    => $journalvoucher_item_first['account_id'],
                'journal_voucher_amount'        => $journalvoucher_item_first['journal_voucher_amount'],
                'account_id_default_status'     => $journalvoucher_item_first['account_id_default_status'],
                'account_id_status'             => $account_setting_status_first,
                'journal_voucher_debit_amount'  => $debit_amount,
                'journal_voucher_credit_amount' => $credit_amount,
                'updated_id'                    => Auth::id(),
                'created_id'                    => Auth::id()
            );
            JournalVoucherItem::create($journal_credit);

            if(!empty($purchasepaymenttransfer) && $purchasepaymenttransfer != null){
                if($account_setting_status_transfer == 0){
                    $account_setting_status_transfer = 1;
                } else {
                    $account_setting_status_transfer = 0;
                }
                if ($account_setting_status_transfer == 0){ 
                    $debit_amount = $datapurchasepaymenttransfer['journal_voucher_amount'];
                    $credit_amount = 0;
                } else {
                    $debit_amount = 0;
                    $credit_amount = $datapurchasepaymenttransfer['journal_voucher_amount'];
                }
                $journal_debit = array(
                    'company_id'                    => Auth::user()->company_id,
                    'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                    'account_id'                    => $datapurchasepaymenttransfer['account_id'],
                    'journal_voucher_amount'        => $datapurchasepaymenttransfer['journal_voucher_amount'],
                    'account_id_default_status'     => $datapurchasepaymenttransfer['account_id_default_status'],
                    'account_id_status'             => $account_setting_status_transfer,
                    'journal_voucher_debit_amount'  => $debit_amount,
                    'journal_voucher_credit_amount' => $credit_amount,
                    'updated_id'                    => Auth::id(),
                    'created_id'                    => Auth::id()
                );
                JournalVoucherItem::create($journal_debit);
            }


            // $journalvoucher 	    = JournalVoucher::where('transaction_journal_no', $payment_no)->where('company_id', Auth::user()->company_id)->first();
            // $journal_voucher_id 	= $journalvoucher['journal_voucher_id'];

            // $acctjournalvoucheritem = JournalVoucherItem::where('journal_voucher_id', $journal_voucher_id)->where('company_id', Auth::user()->company_id)->get();

            // $journalvoucher 	            = JournalVoucher::where('journal_voucher_id', $journal_voucher_id)->first();
            // $journalvoucher->voided         = 1;
            // $journalvoucher->voided_id      = Auth::id();
            // $journalvoucher->voided_on      = date('Y-m-d H:i:s');
            // $journalvoucher->voided_remark  = $request->voided_remark;
            // $journalvoucher->data_state     = 2;

            // if ($journalvoucher->save()){
            //     foreach ($acctjournalvoucheritem as $keyItem => $valItem) {
            //         $journalvoucheritem = JournalVoucherItem::where('journal_voucher_item_id', $valItem['journal_voucher_item_id'])->first();
            //         $journalvoucheritem->data_state = 2;

            //         $journalvoucheritem->save();
            //     }
            // }

            $msg = "Pembatalan Pelunasan Hutang Berhasil";
            return redirect('/purchase-payment')->with('msg',$msg);
        }else{
            $msg = "Pembatalan Pelunasan Hutang Gagal";
            return redirect('/purchase-payment/delete/'.$request->payment_id)->with('msg',$msg);
        }
    }
}
