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
use App\Models\PreferenceTransactionModule;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PurchaseReturnController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        
    }
    
    public function index()
    {
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

        Session::put('editarraystate', 0);
        Session::forget('datases');
        Session::forget('arraydatases');
        $data = PurchaseReturn::where('data_state',0)
        ->where('purchase_return_date', '>=', $start_date)
        ->where('purchase_return_date', '<=', $end_date)
        ->where('company_id', Auth::user()->company_id)
        ->where('data_state',0)
        ->get();
        return view('content.PurchaseReturn.ListPurchaseReturn', compact('data', 'start_date', 'end_date'));
    }

    public function filterPurchaseReturn(Request $request)
    {
        $start_date = $request->start_date;
        $end_date   = $request->end_date;

        Session::put('start_date', $start_date);
        Session::put('end_date', $end_date);

        return redirect('/purchase-return');
    }

    public function addPurchaseReturn()
    {
        $categorys = InvtItemCategory::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('item_category_name','item_category_id');
        $warehouses = InvtWarehouse::where('data_State',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('warehouse_name','warehouse_id');
        $units     = InvtItemUnit::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('item_unit_name','item_unit_id');
        $items     = InvtItem::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('item_name','item_id');
        $datases   = Session::get('datases');
        $arraydatases = Session::get('arraydatases');
        $suppliers = CoreSupplier::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('supplier_name','supplier_id');
        return view('content.PurchaseReturn.FormAddPurchaseReturn', compact('items', 'units', 'categorys', 'warehouses','datases','arraydatases','suppliers'));
    }

    public function addResetPurchaseReturn()
    {
        Session::forget('datases');
        Session::forget('arraydatases');
        return redirect('/purchase-return/add');
    }

    public function addElementsPurchaseReturn(Request $request)
    {
        $datases = Session::get('datases');
        if(!$datases || $datases == ''){
            $datases['supplier_id']    = '';
            $datases['warehouse_id']                = '';
            $datases['purchase_return_date']        = '';
            $datases['purchase_return_remark']      = '';
        }
        $datases[$request->name] = $request->value;
        $datases = Session::put('datases', $datases);
    }

    public function processAddPurchaseReturn(Request $request)
    {
        $transaction_module_code = 'RPBL';
        $transaction_module_id  = $this->getTransactionModuleID($transaction_module_code);
        $fields = $request->validate([
            'supplier_id'              => 'required',
            'warehouse_id'             => 'required',
            'purchase_return_date'     => 'required',
            'purchase_return_remark'   => '',
            'total_quantity'           => 'required',
            'subtotal'                 => 'required'
        ]);

        $datases = array(
            'supplier_id'               => $fields['supplier_id'],
            'warehouse_id'              => $fields['warehouse_id'],
            'purchase_return_date'      => $fields['purchase_return_date'],
            'purchase_return_remark'    => $fields['purchase_return_remark'],
            'purchase_return_quantity'  => $fields['total_quantity'],
            'purchase_return_subtotal'  => $fields['subtotal'],
            'company_id'                => Auth::user()->company_id,
            'updated_id'                => Auth::id(),
            'created_id'                => Auth::id()
        );
        
        if(PurchaseReturn::create($datases)){
            $purchase_return_id = PurchaseReturn::orderBy('created_at', 'DESC')->where('company_id', Auth::user()->company_id)->first();
            $journal = array(
                'company_id'                    => Auth::user()->company_id,
                'transaction_module_id'         => $transaction_module_id,
                'transaction_module_code'       => $transaction_module_code,
                'transaction_journal_no'        => $purchase_return_id['purchase_return_no'],
                'journal_voucher_status'        => 1,
                'journal_voucher_date'          => $fields['purchase_return_date'],
                'journal_voucher_description'   => $this->getTransactionModuleName($transaction_module_code),
                'journal_voucher_period'        => date('Ym'),
                'journal_voucher_title'         => $this->getTransactionModuleName($transaction_module_code),
                'updated_id'                    => Auth::id(),
                'created_id'                    => Auth::id()
            );
            JournalVoucher::create($journal);
            $arraydatases       = Session::get('arraydatases');
            foreach ($arraydatases AS $key => $val){
                $dataarray = array (
                    'purchase_return_id'        => $purchase_return_id['purchase_return_id'],
                    'item_category_id'          => $val['item_category_id'],
                    'item_id'                   => $val['item_id'],
                    'item_unit_id'              => $val['item_unit_id'],
                    'purchase_item_cost'        => $val['purchase_return_cost'],
                    'purchase_item_quantity'    => $val['purchase_return_quantity'],
                    'purchase_item_subtotal'    => $val['purchase_return_subtotal'],
                    'company_id'                => Auth::user()->company_id,
                    'updated_id'                => Auth::id(),
                    'created_id'                => Auth::id()
                );
                PurchaseReturnItem::create($dataarray);
                $stock_item = InvtItemStock::where('item_id',$dataarray['item_id'])
                ->where('warehouse_id', $datases['warehouse_id'])
                ->where('item_category_id',$dataarray['item_category_id'])
                ->where('company_id', Auth::user()->company_id)
                ->first();
                $item_packge = InvtItemPackge::where('item_id',$dataarray['item_id'])
                ->where('item_category_id',$dataarray['item_category_id'])
                ->where('item_unit_id', $dataarray['item_unit_id'])
                ->where('company_id', Auth::user()->company_id)
                ->first();
                if(isset($stock_item)){
                    $table                  = InvtItemStock::findOrFail($stock_item['item_stock_id']);
                    $table->last_balance    = $stock_item['last_balance'] - ($dataarray['purchase_item_quantity'] * $item_packge['item_default_quantity']);
                    $table->updated_id      = Auth::id();
                    $table->save();

                }
            }

            $account_setting_name = 'purchase_return_cash_account';
            $account_id = $this->getAccountId($account_setting_name);
            $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
            $account_default_status = $this->getAccountDefaultStatus($account_id);
            $journal_voucher_id = JournalVoucher::orderBy('created_at', 'DESC')->where('company_id', Auth::user()->company_id)->first();
            if ($account_setting_status == 0){
                $debit_amount = $fields['subtotal'];
                $credit_amount = 0;
            } else {
                $debit_amount = 0;
                $credit_amount = $fields['subtotal'];
            }
            $journal_debit = array(
                'company_id'                    => Auth::user()->company_id,
                'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                'account_id'                    => $account_id,
                'journal_voucher_amount'        => $fields['subtotal'],
                'account_id_default_status'     => $account_default_status,
                'account_id_status'             => $account_setting_status,
                'journal_voucher_debit_amount'  => $debit_amount,
                'journal_voucher_credit_amount' => $credit_amount,
                'updated_id'                    => Auth::id(),
                'created_id'                    => Auth::id()
            );
            JournalVoucherItem::create($journal_debit);

            $account_setting_name = 'purchase_return_account';
            $account_id = $this->getAccountId($account_setting_name);
            $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
            $account_default_status = $this->getAccountDefaultStatus($account_id);
            $journal_voucher_id = JournalVoucher::orderBy('created_at', 'DESC')->where('company_id', Auth::user()->company_id)->first();
            if ($account_setting_status == 0){
                $debit_amount = $fields['subtotal'];
                $credit_amount = 0;
            } else {
                $debit_amount = 0;
                $credit_amount = $fields['subtotal'];
            }
            $journal_credit = array(
                'company_id'                    => Auth::user()->company_id,
                'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                'account_id'                    => $account_id,
                'journal_voucher_amount'        => $fields['subtotal'],
                'account_id_default_status'     => $account_default_status,
                'account_id_status'             => $account_setting_status,
                'journal_voucher_debit_amount'  => $debit_amount,
                'journal_voucher_credit_amount' => $credit_amount,
                'updated_id'                    => Auth::id(),
                'created_id'                    => Auth::id()
            );
            JournalVoucherItem::create($journal_credit);
            
            $msg = 'Tambah Retur Pembelian Berhasil';
            return redirect('/purchase-return/add')->with('msg',$msg);
        }else{
            $msg = 'Tambah Retur Pembelian Gagal';
            return redirect('/purchase-return/add')->with('msg',$msg);
        }
    }

    public function addArrayPurchaseReturn(Request $request)
    {
        $request->validate([
            'item_category_id'          => 'required',
            'item_id'                   => 'required',
            'item_unit_id'              => 'required',
            'purchase_return_cost'      => 'required',
            'purchase_return_quantity'  => 'required',
            'purchase_return_subtotal'  => 'required'
        ]);

        $arraydatases = array(
            'item_category_id'          => $request->item_category_id,
            'item_id'                   => $request->item_id,
            'item_unit_id'              => $request->item_unit_id,
            'purchase_return_cost'      => $request->purchase_return_cost,
            'purchase_return_quantity'  => $request->purchase_return_quantity,
            'purchase_return_subtotal'  => $request->purchase_return_subtotal,
        );
        $lastdatases = Session::get('arraydatases');
        if($lastdatases!== null){
            array_push($lastdatases, $arraydatases);
            Session::put('arraydatases', $lastdatases);
        } else {
            $lastdatases= [];
            array_push($lastdatases, $arraydatases);
            Session::push('arraydatases', $arraydatases);
        }
        Session::put('editarraystate', 1);
        return redirect('/purchase-return/add');
    }

    public function getItemName($item_id){
        $item = InvtItem::where('item_id', $item_id)->first();
        return $item['item_name'];
    }

    public function deleteArrayPurchaseReturn($record_id)
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

        return redirect('/purchase-return/add');
    }

    public function getWarehouseName($warehouse_id)
    {
        $warehouse = InvtWarehouse::where('warehouse_id', $warehouse_id)->first();
        return $warehouse['warehouse_name'];
    }

    public function detailPurchaseReturn($purchase_return_id)
    {
        $categorys = InvtItemCategory::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('item_category_name','item_category_id');
        $warehouses = InvtWarehouse::where('data_State',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('warehouse_name','warehouse_id');
        $units     = InvtItemUnit::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('item_unit_name','item_unit_id');
        $items     = InvtItem::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('item_name','item_id');
        $purchasereturn = PurchaseReturn::where('purchase_return_id', $purchase_return_id)
        ->where('data_state',0)
        ->first();
        $purchasereturnitem = PurchaseReturnItem::where('purchase_return_id', $purchase_return_id)->get();
        $suppliers = CoreSupplier::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('supplier_name','supplier_id');
        return view('content.PurchaseReturn.FormDetailPurchaseReturn',compact('purchasereturn','categorys','warehouses','units','items', 'purchasereturnitem','suppliers'));
    }

    public function filterResetPurchaseReturn()
    {
        Session::forget('start_date');
        Session::forget('end_date');
        return redirect('/purchase-return');
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
}
