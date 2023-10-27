<?php

namespace App\Http\Controllers;

use App\Models\InvtItem;
use App\Models\InvtItemBarcode;
use App\Models\InvtItemCategory;
use App\Models\InvtItemPackge;
use App\Models\InvtItemStock;
use App\Models\InvtItemUnit;
use App\Models\InvtWarehouse;
use App\Models\PurchaseInvoice;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $menus =  User::select('system_menu_mapping.*','system_menu.*')
        ->join('system_user_group','system_user_group.user_group_id','=','system_user.user_group_id')
        ->join('system_menu_mapping','system_menu_mapping.user_group_level','=','system_user_group.user_group_level')
        ->join('system_menu','system_menu.id_menu','=','system_menu_mapping.id_menu')
        ->where('system_user.user_id','=',Auth::id())
        // ->where('system_menu_mapping.company_id', Auth::user()->company_id)
        ->orderBy('system_menu_mapping.id_menu','ASC')
        ->get();
        
        $max_day = date('t') + 1;
        for ($i=1; $i < $max_day; $i++) { 
            $data[$i]['day'] = $i;
            $data[$i]['sales'] = $this->getAmountSalesInvoice($i);
            $data[$i]['purchase'] = $this->getAmountPurchaseInvoice($i);
        }


        $now 		= date('Y-m-d');
        $seminggu 	= abs(6*86400);
        $awal 		= strtotime($now)-$seminggu;
        $akhir 		= strtotime($now);
        for($i=$awal; $i <=$akhir;$i+=86400)
        {
            $date 		= date('Y-m-d', $i);
            $x 			= mktime(0, 0, 0, date("m", strtotime($date)), date("d", strtotime($date)), date("Y", strtotime($date)));
            $day 		= date("l", $x);
            $dayname = [
                'Monday'    => 'Senin',
                'Tuesday'   => 'Selasa',
                'Wednesday' => 'Rabu',
                'Thursday'  => 'Kamis',
                'Friday'    => 'Jumat',
                'Saturday'  => 'Sabtu',
                'Sunday'    => 'Minggu',
            ];

            $datasalesinvoiceweekly[$i]['day']				= $dayname[$day];
            $datasalesinvoiceweekly[$i]['sales']			= $this->getAmountSalesInvoiceWeekly($date);
            $datasalesinvoiceweekly[$i]['purchase']			= $this->getAmountPurchaseInvoiceWeekly($date);
        }

        $item_data = InvtItem::select('item_name','item_id')
        ->where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get();
        foreach ($item_data as $key => $val) {
            $item_data[$key]['item_name'] =  $val['item_name'];
            $item_data[$key]['quantity'] = $this->getQuantitySalesInvoice($val['item_id']);
        }
        if(empty($item_data)){
            $item_data = [];
        }

        return view('home',compact('menus','data','datasalesinvoiceweekly','item_data'));
    }

    public function getQuantitySalesInvoice($item_id)
    {
        $data = SalesInvoiceItem::join('sales_invoice','sales_invoice.sales_invoice_id','=','sales_invoice_item.sales_invoice_id')
        ->select('sales_invoice_item.quantity')
        ->where('sales_invoice.data_state',0)
        ->where('sales_invoice.company_id', Auth::user()->company_id)
        ->where('sales_invoice_item.item_id', $item_id)
        ->whereMonth('sales_invoice.sales_invoice_date', date('m'))
        ->whereYear('sales_invoice.sales_invoice_date',date('Y'))
        ->get();

        $amount = 0;
        foreach ($data as $val) {
            $amount += $val['quantity'];
        }
        return $amount;
        
    }

    public function getAmountSalesInvoice($day)
    {
        $data = SalesInvoice::select('total_amount')
        ->where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->whereDay('sales_invoice_date', $day)
        ->whereMonth('sales_invoice_date', date('m'))
        ->whereYear('sales_invoice_date',date('Y'))
        ->get();

        $amount = 0;
        foreach ($data as $val) {
            $amount += $val['total_amount'];
        }
        return $amount;
        
    }

    public function getAmountPurchaseInvoice($day)
    {
        $data = PurchaseInvoice::select('total_amount')
        ->where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->whereDay('purchase_invoice_date', $day)
        ->whereMonth('purchase_invoice_date', date('m'))
        ->whereYear('purchase_invoice_date',date('Y'))
        ->get();

        $amount = 0;
        foreach ($data as $val) {
            $amount += $val['total_amount'];
        }
        return $amount;
        
    }

    public function getAmountSalesInvoiceWeekly($date)
    {
        $data = SalesInvoice::select('total_amount')
        ->where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->where('sales_invoice_date', $date)
        ->get();

        $amount = 0;
        foreach ($data as $val) {
            $amount += $val['total_amount'];
        }
        return $amount;
        
    }

    public function getAmountPurchaseInvoiceWeekly($date)
    {
        $data = PurchaseInvoice::select('total_amount')
        ->where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->where('purchase_invoice_date',$date)
        ->get();

        $amount = 0;
        foreach ($data as $val) {
            $amount += $val['total_amount'];
        }
        return $amount;
        
    }

    public function selectItemCategory()
    {
        $category = InvtItemCategory::where('data_state',0)
        ->select('item_category_id','item_category_name')
        ->where('company_id', Auth::user()->company_id)
        ->get();
        
        $data = '';
        $data .= "<option value=''>--Choose One--</option>";
        foreach ($category as $mp){
            $data .= "<option value='$mp[item_category_id]'>$mp[item_category_name]</option>\n";	
        }
        return $data;

    }

    public function selectItem($id)
    {
        $item = InvtItem::where('data_state',0)
        ->select('item_id','item_name')
        ->where('company_id', Auth::user()->company_id)
        ->where('item_category_id', $id)
        ->get();
        
        $data = '';
        $data .= "<option value=''>--Choose One--</option>";
        foreach ($item as $mp){
            $data .= "<option value='$mp[item_id]'>$mp[item_name]</option>\n";	
        }
        return $data;

    }

    public function selectItemUnit($id)
    {
        $unit = InvtItemPackge::join('invt_item_unit','invt_item_unit.item_unit_id','=','invt_item_packge.item_unit_id')
        ->select('invt_item_packge.item_unit_id','invt_item_unit.item_unit_name')
        ->where('invt_item_unit.data_state',0)
        ->where('invt_item_packge.data_state',0)
        ->where('invt_item_packge.item_id', $id)
        ->where('invt_item_packge.company_id', Auth::user()->company_id)
        ->get();
        
        $data = '';
        $data .= "<option value=''>--Choose One--</option>";
        foreach ($unit as $mp){
            $data .= "<option value='$mp[item_unit_id]'>$mp[item_unit_name]</option>\n";	
        }
        return $data;

    }

    public function selectItemCost($unit_id, $item_id)
    {
        $unit = InvtItemPackge::join('invt_item_unit','invt_item_unit.item_unit_id','=','invt_item_packge.item_unit_id')
        ->select('invt_item_packge.item_unit_cost')
        ->where('invt_item_unit.data_state',0)
        ->where('invt_item_packge.data_state',0)
        ->where('invt_item_packge.item_unit_id', $unit_id)
        ->where('invt_item_packge.item_id', $item_id)
        ->where('invt_item_packge.company_id', Auth::user()->company_id)
        ->first();

        return $unit['item_unit_cost'];
    }

    public function selectItemPrice($category_id, $item_unit_id, $item_id)
    {
        $data = InvtItemPackge::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->where('item_category_id', $category_id)
        ->where('item_unit_id', $item_unit_id)
        ->where('item_id', $item_id)
        ->first();

        return $data['item_unit_price'];
    }

    public function getMarginCategory($category_id)
    {
        $data = InvtItemCategory::where('data_state', 0)
        ->where('company_id', Auth::user()->company_id)
        ->where('item_category_id', $category_id)
        ->first();

        return $data['margin_percentage'];
    }
}
