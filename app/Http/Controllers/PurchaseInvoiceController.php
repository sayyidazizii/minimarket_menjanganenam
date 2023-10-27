<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AcctAccount;
use App\Models\AcctAccountSetting;
use App\Models\CoreSupplier;
use App\Models\InvtItem;
use App\Models\InvtItemCategory;
use App\Models\InvtItemPackge;
use App\Models\InvtItemStock;
use App\Models\InvtItemUnit;
use App\Models\InvtWarehouse;
use App\Models\JournalVoucher;
use App\Models\JournalVoucherItem;
use App\Models\PreferenceCompany;
use App\Models\PreferenceTransactionModule;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use function PHPUnit\Framework\isEmpty;

class PurchaseInvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        
    }
    
    public function index()
    {
        if(!$start_date = Session::get('start_date')){
            $start_date = date('Y-m-d');
        }else{
            $start_date = Session::get('start_date');
        }
        if(!$end_date = Session::get('end_date')){
            $end_date = date('Y-m-d');
        }else{
            $end_date = Session::get('end_date');
        }
        Session::forget('datases');
        Session::forget('arraydatases');
        $data = PurchaseInvoice::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->where('purchase_invoice_date', '>=', $start_date)
        ->where('purchase_invoice_date', '<=', $end_date)
        ->get();
        return view('content.PurchaseInvoice.ListPurchaseInvoice', compact('data','start_date','end_date'));
    }

    public function addPurchaseInvoice()
    {
        $categorys = InvtItemCategory::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('item_category_name', 'item_category_id');
        $items     = InvtItem::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('item_name', 'item_id');
        $units     = InvtItemUnit::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('item_unit_name','item_unit_id');
        $warehouses = InvtWarehouse::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('warehouse_name','warehouse_id');
        $datases = Session::get('datases');
        $arraydatases = Session::get('arraydatases');
        $suppliers = CoreSupplier::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('supplier_name','supplier_id');
        $purchase_payment_method = array(
            0 => 'Tunai',
            1 => 'Hutang Pemasok',
            6 => 'Konsinyasi'
        );
        $ppn_percentage = PreferenceCompany::where('company_id', Auth::user()->company_id)
        ->first();
        // dd($ppn_percentage);
        return view('content.PurchaseInvoice.FormAddPurchaseInvoice', compact('categorys', 'items', 'units','warehouses','datases','arraydatases','suppliers','purchase_payment_method','ppn_percentage'));
    }

    public function detailPurchaseInvoice($purchase_invoice_id)
    {
        $warehouses = InvtWarehouse::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('warehouse_name','warehouse_id');
        $purchaseinvoice = PurchaseInvoice::where('purchase_invoice_id', $purchase_invoice_id)->first();
        $purchaseinvoiceitem = PurchaseInvoiceItem::where('purchase_invoice_id', $purchase_invoice_id)->get();
        return view('content.PurchaseInvoice.DetailPurchaseInvoice', compact('purchaseinvoice','warehouses','purchaseinvoiceitem'));
    }

    public function addElementsPurchaseInvoice(Request $request)
    {
        $datases = Session::get('datases');
        if(!$datases || $datases == ''){
            $datases['supplier_id']   = '';
            $datases['warehouse_id']                = '';
            $datases['purchase_invoice_date']       = '';
            $datases['purchase_invoice_remark']     = '';
            $datases['purchase_payment_method']     = '';
        }
        $datases[$request->name] = $request->value;
        $datases = Session::put('datases', $datases);
    }

    public function addArrayPurchaseInvoice(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'item_category_id'  => 'required',
            'item_id'           => 'required',
            'item_unit_id'      => 'required',
            'item_unit_cost'    => 'required',
            'quantity'          => 'required',
            'subtotal_amount'   => 'required',
            'item_expired_date' => 'required'
        ]);

        $arraydatases = array(
            'item_category_id'                  => $request->item_category_id,
            'item_id'                           => $request->item_id,
            'item_unit_id'                      => $request->item_unit_id,
            'item_unit_cost'                    => $request->item_unit_cost,
            'quantity'                          => $request->quantity,
            'subtotal_amount'                   => $request->subtotal_amount,
            'discount_percentage'               => $request->discount_percentage == null ? 0 : $request->discount_percentage,
            'discount_amount'                   => $request->discount_amount == null ? 0 : $request->discount_percentage,
            'subtotal_amount_after_discount'    => $request->subtotal_amount_after_discount,
            'item_expired_date'                 => $request->item_expired_date
        );

        $lastdatases = Session::get('arraydatases');
        if($lastdatases !== null){
            array_push($lastdatases, $arraydatases);
            Session::put('arraydatases', $lastdatases);
        } else {
            $lastdatases = [];
            array_push($lastdatases, $arraydatases);
            Session::push('arraydatases', $arraydatases);
        }

        return redirect('/purchase-invoice/add');
    }

    public function deleteArrayPurchaseInvoice($record_id)
    {
        $arrayBaru			= array();
        $dataArrayHeader	= Session::get('arraydatases');
        
        foreach($dataArrayHeader as $key=>$val){
            if($key != $record_id){
                $arrayBaru[$key] = $val;
            }
        }
        Session::forget('arraydatases');
        Session::put('arraydatases', $arrayBaru);

        return redirect('/purchase-invoice/add');
    }
    
    public function processAddPurchaseInvoice(Request $request)
    {
        // dd($request->all());
        $transaction_module_code = 'PBL';
        $transaction_module_id = $this->getTransactionModuleID($transaction_module_code);
        $fields = $request->validate([
            'supplier_id'               => 'required',
            'warehouse_id'              => 'required',
            'purchase_invoice_date'     => 'required',
            'purchase_invoice_remark'   => '',
            'subtotal_item'             => 'required',
            'purchase_payment_method'   => 'required',
            'subtotal_amount_total'     => 'required',
            'total_amount'              => 'required',
            'paid_amount'               => 'required',
            'owing_amount'              => 'required'
        ]);
        if (empty($request->discount_percentage_total)){
            $discount_percentage_total = 0;
            $discount_amount_total = 0;
        }else{
            $discount_percentage_total = $request->discount_percentage_total;
            $discount_amount_total = $request->discount_amount_total;
        }
        $datases = array(
            'supplier_id'               => $fields['supplier_id'],
            'warehouse_id'              => $fields['warehouse_id'],
            'purchase_payment_method'   => $fields['purchase_payment_method'],
            'purchase_invoice_date'     => $fields['purchase_invoice_date'],
            'purchase_invoice_remark'   => $fields['purchase_invoice_remark'],
            'subtotal_item'             => $fields['subtotal_item'],
            'discount_percentage_total' => $discount_percentage_total,
            'discount_amount_total'     => $discount_amount_total,
            'tax_ppn_percentage'        => $request->tax_ppn_percentage,
            'tax_ppn_amount'            => $request->tax_ppn_amount,
            'shortover_amount'          => $request->shortover_amount,
            'subtotal_amount_total'     => $fields['subtotal_amount_total'],
            'total_amount'              => $fields['total_amount'],
            'paid_amount'               => $fields['paid_amount'],
            'owing_amount'              => $fields['owing_amount'],
            'company_id'                => Auth::user()->company_id,
            'created_id'                => Auth::id(),
            'updated_id'                => Auth::id()
        );
        PurchaseInvoice::create($datases);
        $purchase_invoice_id = PurchaseInvoice::orderBy('created_at', 'DESC')->where('company_id', Auth::user()->company_id)->first();
        $journal = array(
            'company_id'                    => Auth::user()->company_id,
            'transaction_module_id'         => $transaction_module_id,
            'transaction_module_code'       => $transaction_module_code,
            'journal_voucher_status'        => 1,
            'journal_voucher_date'          => $fields['purchase_invoice_date'],
            'journal_voucher_description'   => $this->getTransactionModuleName($transaction_module_code),
            'journal_voucher_period'        => date('Ym'),
            'transaction_journal_no'        => $purchase_invoice_id['purchase_invoice_no'],
            'journal_voucher_title'         => $this->getTransactionModuleName($transaction_module_code),
            'created_id'                    => Auth::id(),
            'updated_id'                    => Auth::id()
        );
        if(JournalVoucher::create($journal)){
            $arraydatases = Session::get('arraydatases');
            foreach ($arraydatases as $key => $val) {
                $dataarray = array(
                    'purchase_invoice_id'               => $purchase_invoice_id['purchase_invoice_id'],
                    'item_category_id'                  => $val['item_category_id'],
                    'item_unit_id'                      => $val['item_unit_id'],
                    'item_id'                           => $val['item_id'],
                    'quantity'                          => $val['quantity'],
                    'item_unit_cost'                    => $val['item_unit_cost'],
                    'subtotal_amount'                   => $val['subtotal_amount'],
                    'item_expired_date'                 => $val['item_expired_date'],
                    'discount_percentage'               => $val['discount_percentage'],
                    'discount_amount'                   => $val['discount_amount'],
                    'subtotal_amount_after_discount'    => $val['subtotal_amount_after_discount'],
                    'company_id'                        => Auth::user()->company_id,
                    'created_id'                        => Auth::id(),
                    'updated_id'                        => Auth::id()
                );
                $dataStock = array(
                    'warehouse_id'      => $fields['warehouse_id'],
                    'item_id'           => $val['item_id'],
                    'item_unit_id'      => $val['item_unit_id'],
                    'item_category_id'  => $val['item_category_id'],
                    'item_unit_id'      => $val['item_unit_id'],
                    'last_balance'      => $val['quantity'],
                    'last_update'       => date('Y-m-d H:i:s'),
                    'company_id'        => Auth::user()->company_id,
                    'created_id'        => Auth::id(),
                    'updated_id'        => Auth::id()
                );
               
                PurchaseInvoiceItem::create($dataarray);
                $stock_item = InvtItemStock::where('item_id',$dataarray['item_id'])
                ->where('warehouse_id', $dataStock['warehouse_id'])
                ->where('item_category_id',$dataarray['item_category_id'])
                ->where('company_id', Auth::user()->company_id)
                ->first();
                $item_packge = InvtItemPackge::where('item_id',$dataarray['item_id'])
                ->where('item_category_id',$dataarray['item_category_id'])
                ->where('item_unit_id', $dataarray['item_unit_id'])
                ->where('company_id', Auth::user()->company_id)
                ->first();
                if(isset($stock_item)){
                    $table = InvtItemStock::findOrFail($stock_item['item_stock_id']);
                    $table->last_balance = ($dataStock['last_balance'] * $item_packge['item_default_quantity']) + $stock_item['last_balance'];
                    $table->updated_id = Auth::id();
                    $table->save();
                } else {
                    InvtItemStock::create($dataStock);
                }
            }

            if ($fields['purchase_payment_method'] == 1) {
                // if ($fields['paid_amount'] != 0) {
                //     $account_setting_name = 'purchase_cash_account';
                //     $account_id = $this->getAccountId($account_setting_name);
                //     $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
                //     $account_default_status = $this->getAccountDefaultStatus($account_id);
                //     $journal_voucher_id = JournalVoucher::orderBy('created_at', 'DESC')->where('company_id', Auth::user()->company_id)->first();
                //     if ($account_setting_status == 0){
                //         $debit_amount = $fields['paid_amount'];
                //         $credit_amount = 0;
                //     } else {
                //         $debit_amount = 0;
                //         $credit_amount = $fields['paid_amount'];
                //     }
                //     $journal_debit = array(
                //         'company_id'                    => Auth::user()->company_id,
                //         'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                //         'account_id'                    => $account_id,
                //         'journal_voucher_amount'        => $fields['paid_amount'],
                //         'account_id_default_status'     => $account_default_status,
                //         'account_id_status'             => $account_setting_status,
                //         'journal_voucher_debit_amount'  => $debit_amount,
                //         'journal_voucher_credit_amount' => $credit_amount,
                //         'created_id'                    => Auth::id(),
                //         'updated_id'                    => Auth::id()
                //     );
                //     JournalVoucherItem::create($journal_debit);
                // }

                $account_setting_name = 'purchase_cash_payable_account';
                $account_id = $this->getAccountId($account_setting_name);
                $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
                $account_default_status = $this->getAccountDefaultStatus($account_id);
                $journal_voucher_id = JournalVoucher::orderBy('created_at', 'DESC')->where('company_id', Auth::user()->company_id)->first();
                if ($account_setting_status == 0){
                    $debit_amount = $fields['total_amount'];
                    $credit_amount = 0;
                } else {
                    $debit_amount = 0;
                    $credit_amount = $fields['total_amount'];
                }
                $journal_debit = array(
                    'company_id'                    => Auth::user()->company_id,
                    'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                    'account_id'                    => $account_id,
                    'journal_voucher_amount'        => $fields['total_amount'],
                    'account_id_default_status'     => $account_default_status,
                    'account_id_status'             => $account_setting_status,
                    'journal_voucher_debit_amount'  => $debit_amount,
                    'journal_voucher_credit_amount' => $credit_amount,
                    'created_id'                    => Auth::id(),
                    'updated_id'                    => Auth::id()
                );
                JournalVoucherItem::create($journal_debit);
                
                $account_setting_name = 'purchase_payable_account';
                $account_id = $this->getAccountId($account_setting_name);
                $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
                $account_default_status = $this->getAccountDefaultStatus($account_id);
                $journal_voucher_id = JournalVoucher::orderBy('created_at', 'DESC')->where('company_id', Auth::user()->company_id)->first();
                if ($account_setting_status == 0){
                    $debit_amount = $fields['total_amount'];
                    $credit_amount = 0;
                } else {
                    $debit_amount = 0;
                    $credit_amount = $fields['total_amount'];
                }
                $journal_credit = array(
                    'company_id'                    => Auth::user()->company_id,
                    'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                    'account_id'                    => $account_id,
                    'journal_voucher_amount'        => $fields['total_amount'],
                    'account_id_default_status'     => $account_default_status,
                    'account_id_status'             => $account_setting_status,
                    'journal_voucher_debit_amount'  => $debit_amount,
                    'journal_voucher_credit_amount' => $credit_amount,
                    'created_id'                    => Auth::id(),
                    'updated_id'                    => Auth::id()
                );
                JournalVoucherItem::create($journal_credit);
            }
            if ($fields['purchase_payment_method'] == 6) {
                // if ($fields['paid_amount'] != 0) {
                //     $account_setting_name = 'purchase_cash_account';
                //     $account_id = $this->getAccountId($account_setting_name);
                //     $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
                //     $account_default_status = $this->getAccountDefaultStatus($account_id);
                //     $journal_voucher_id = JournalVoucher::orderBy('created_at', 'DESC')->where('company_id', Auth::user()->company_id)->first();
                //     if ($account_setting_status == 0){
                //         $debit_amount = $fields['paid_amount'];
                //         $credit_amount = 0;
                //     } else {
                //         $debit_amount = 0;
                //         $credit_amount = $fields['paid_amount'];
                //     }
                //     $journal_debit = array(
                //         'company_id'                    => Auth::user()->company_id,
                //         'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                //         'account_id'                    => $account_id,
                //         'journal_voucher_amount'        => $fields['paid_amount'],
                //         'account_id_default_status'     => $account_default_status,
                //         'account_id_status'             => $account_setting_status,
                //         'journal_voucher_debit_amount'  => $debit_amount,
                //         'journal_voucher_credit_amount' => $credit_amount,
                //         'created_id'                    => Auth::id(),
                //         'updated_id'                    => Auth::id()
                //     );
                //     JournalVoucherItem::create($journal_debit);
                // }

                $account_setting_name = 'purchase_cash_payable_account';
                $account_id = $this->getAccountId($account_setting_name);
                $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
                $account_default_status = $this->getAccountDefaultStatus($account_id);
                $journal_voucher_id = JournalVoucher::orderBy('created_at', 'DESC')->where('company_id', Auth::user()->company_id)->first();
                if ($account_setting_status == 0){
                    $debit_amount = $fields['total_amount'];
                    $credit_amount = 0;
                } else {
                    $debit_amount = 0;
                    $credit_amount = $fields['total_amount'];
                }
                $journal_debit = array(
                    'company_id'                    => Auth::user()->company_id,
                    'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                    'account_id'                    => $account_id,
                    'journal_voucher_amount'        => $fields['total_amount'],
                    'account_id_default_status'     => $account_default_status,
                    'account_id_status'             => $account_setting_status,
                    'journal_voucher_debit_amount'  => $debit_amount,
                    'journal_voucher_credit_amount' => $credit_amount,
                    'created_id'                    => Auth::id(),
                    'updated_id'                    => Auth::id()
                );
                JournalVoucherItem::create($journal_debit);
                
                $account_setting_name = 'purchase_payable_account';
                $account_id = $this->getAccountId($account_setting_name);
                $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
                $account_default_status = $this->getAccountDefaultStatus($account_id);
                $journal_voucher_id = JournalVoucher::orderBy('created_at', 'DESC')->where('company_id', Auth::user()->company_id)->first();
                if ($account_setting_status == 0){
                    $debit_amount = $fields['total_amount'];
                    $credit_amount = 0;
                } else {
                    $debit_amount = 0;
                    $credit_amount = $fields['total_amount'];
                }
                $journal_credit = array(
                    'company_id'                    => Auth::user()->company_id,
                    'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                    'account_id'                    => $account_id,
                    'journal_voucher_amount'        => $fields['total_amount'],
                    'account_id_default_status'     => $account_default_status,
                    'account_id_status'             => $account_setting_status,
                    'journal_voucher_debit_amount'  => $debit_amount,
                    'journal_voucher_credit_amount' => $credit_amount,
                    'created_id'                    => Auth::id(),
                    'updated_id'                    => Auth::id()
                );
                JournalVoucherItem::create($journal_credit);
            } 
            
            else {
                $account_setting_name = 'purchase_cash_account';
                $account_id = $this->getAccountId($account_setting_name);
                $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
                $account_default_status = $this->getAccountDefaultStatus($account_id);
                $journal_voucher_id = JournalVoucher::orderBy('created_at', 'DESC')->where('company_id', Auth::user()->company_id)->first();
                if ($account_setting_status == 0){
                    $debit_amount = $fields['total_amount'];
                    $credit_amount = 0;
                } else {
                    $debit_amount = 0;
                    $credit_amount = $fields['total_amount'];
                }
                $journal_debit = array(
                    'company_id'                    => Auth::user()->company_id,
                    'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                    'account_id'                    => $account_id,
                    'journal_voucher_amount'        => $fields['total_amount'],
                    'account_id_default_status'     => $account_default_status,
                    'account_id_status'             => $account_setting_status,
                    'journal_voucher_debit_amount'  => $debit_amount,
                    'journal_voucher_credit_amount' => $credit_amount,
                    'created_id'                    => Auth::id(),
                    'updated_id'                    => Auth::id()
                );
                JournalVoucherItem::create($journal_debit);
                
                $account_setting_name = 'purchase_account';
                $account_id = $this->getAccountId($account_setting_name);
                $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
                $account_default_status = $this->getAccountDefaultStatus($account_id);
                $journal_voucher_id = JournalVoucher::orderBy('created_at', 'DESC')->where('company_id', Auth::user()->company_id)->first();
                if ($account_setting_status == 0){
                    $debit_amount = $fields['total_amount'];
                    $credit_amount = 0;
                } else {
                    $debit_amount = 0;
                    $credit_amount = $fields['total_amount'];
                }
                $journal_credit = array(
                    'company_id'                    => Auth::user()->company_id,
                    'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                    'account_id'                    => $account_id,
                    'journal_voucher_amount'        => $fields['total_amount'],
                    'account_id_default_status'     => $account_default_status,
                    'account_id_status'             => $account_setting_status,
                    'journal_voucher_debit_amount'  => $debit_amount,
                    'journal_voucher_credit_amount' => $credit_amount,
                    'created_id'                    => Auth::id(),
                    'updated_id'                    => Auth::id()
                );
                JournalVoucherItem::create($journal_credit);
            }

            $msg = 'Tambah Pembelian Berhasil';
            return redirect('/purchase-invoice/add')->with('msg',$msg);
        } else {
            $msg = 'Tambah Pembelian Gagal';
            return redirect('/purchase-invoice/add')->with('msg',$msg);
        }
    }

    public function getWarehouseName($warehouse_id)
    {
        $data = InvtWarehouse::where('warehouse_id', $warehouse_id)->first();

        return $data['warehouse_name'];
    }

    public function getItemName($item_id)
    {
        $data = InvtItem::where('item_id', $item_id)->first();

        return $data['item_name'];
    }

    public function filterPurchaseInvoice(Request $request)
    {
        $start_date = $request->start_date;
        $end_date   = $request->end_date;

        Session::put('start_date', $start_date);
        Session::put('end_date', $end_date);

        return redirect('/purchase-invoice');
    }

    public function addResetPurchaseInvoice()
    {
        Session::forget('datases');
        Session::forget('arraydatases');
        return redirect('/purchase-invoice/add');
    }

    public function filterResetPurchaseInvoice()
    {
        Session::forget('start_date');
        Session::forget('end_date');

        return redirect('/purchase-invoice');
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

    public function getAccountSettingStatus($account_setting_name)
    {
        $data = AcctAccountSetting::where('company_id', Auth::user()->company_id)
        ->where('account_setting_name', $account_setting_name)
        ->first();

        return $data['account_setting_status'];
    }

    public function getAccountId($account_setting_name)
    {
        $data = AcctAccountSetting::where('company_id', Auth::user()->company_id)
        ->where('account_setting_name', $account_setting_name)
        ->first();

        return $data['account_id'];
    }

    public function getAccountDefaultStatus($account_id)
    {
        $data = AcctAccount::where('account_id',$account_id)->first();

        return $data['account_default_status'];
    }

    public function getSupplierName($supplier_id)
    {
        $data = CoreSupplier::where('supplier_id', $supplier_id)
        ->first();

        return $data['supplier_name'];
    }

    public function processChangeCostPurchaseInvoice(Request $request)
    {
        $data = InvtItemPackge::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->where('item_category_id', $request->item_category_id)
        ->where('item_unit_id', $request->item_unit_id)
        ->where('item_id', $request->item_id)
        ->first();

        $table                      = InvtItemPackge::findOrFail($data['item_packge_id']);
        $table->margin_percentage   = $request->margin_percentage;
        $table->item_unit_cost      = $request->item_cost_new;
        $table->item_unit_price     = $request->item_price_new;
        $table->updated_id          = Auth::id();

        if ($table->save()) {
            $msg = 'Ubah Harga Barang Berhasil';
            return redirect('/purchase-invoice/add')->with('msg',$msg);
        } else {
            $msg = 'Ubah Harga Barang Gagal';
            return redirect('/purchase-invoice/add')->with('msg',$msg);
        }
    }
}