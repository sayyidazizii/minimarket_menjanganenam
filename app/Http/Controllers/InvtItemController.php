<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\InvtItem;
use App\Models\InvtItemCategory;
use App\Models\InvtItemPackge;
use App\Models\InvtItemStock;
use App\Models\InvtItemUnit;
use App\Models\InvtWarehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Yajra\DataTables\Facades\DataTables;

class InvtItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        
    }
    
    public function index()
    {
        Session::forget('items');
        $data = InvtItem::join('invt_item_category', 'invt_item_category.item_category_id', '=', 'invt_item.item_category_id')
        ->where('invt_item.data_state','=',0)
        ->where('invt_item.company_id', Auth::user()->company_id)
        ->get();
        return view('content.InvtItem.ListInvtItem', compact('data'));
    }

    public function addItem()
    {
        $items      = Session::get('items');
        $itemunits  = InvtItemUnit::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('item_unit_name','item_unit_id');
        $category   = InvtItemCategory::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('item_category_name', 'item_category_id');
        // $warehouse = InvtWarehouse::where('data_state',0)->where('company_id',Auth::user()->company_id)->get();
        // dd($warehouse);
        return view('content.InvtItem.FormAddInvtItem', compact('category','itemunits','items'));
    }

    public function addItemElements(Request $request)
    {
        $items = Session::get('items');
        if(!$items || $items == ''){
            $items['item_code']           = '';
            $items['item_category_id']    = '';
            $items['item_name']           = '';
            // $items['item_barcode']      = '';
            $items['item_remark']         = '';
            $items['item_unit_id_1']      = '';
            $items['item_quantity_1']     = '';
            $items['item_price_1']        = '';
            $items['item_cost_1']         = '';
            $items['item_unit_id_2']      = '';
            $items['item_quantity_2']     = '';
            $items['item_price_2']        = '';
            $items['item_cost_2']         = '';
            $items['item_unit_id_3']      = '';
            $items['item_quantity_3']     = '';
            $items['item_price_3']        = '';
            $items['item_cost_3']         = '';
            $items['item_unit_id_4']      = '';
            $items['item_quantity_4']     = '';
            $items['item_price_4']        = '';
            $items['item_cost_4']         = '';
            // $items['item_barcode_1']      = '';
            // $items['item_barcode_2']      = '';
            // $items['item_barcode_3']      = '';
            // $items['item_barcode_4']      = '';
        }
        $items[$request->name] = $request->value;
        Session::put('items', $items);
    }

    public function processAddItem(Request $request)
    {
        $fields = $request->validate([
            'item_category_id'  => 'required',
            'item_code'         => 'required',
            'item_name'         => 'required',
            // 'item_barcode'      => '',
            'item_remark'       => '',
            'item_unit_id_1'    => 'required',
            'item_quantity_1'   => 'required',
            'item_price_1'      => 'required',
            'item_cost_1'       => 'required'
        ]);

        $data = InvtItem::create([
            'item_category_id'      => $fields['item_category_id'],
            'item_code'             => $fields['item_code'],
            'item_name'             => $fields['item_name'],
            // 'item_barcode'          => $fields['item_barcode'],
            'item_remark'           => $fields['item_remark'],
            'item_unit_id'          => $fields['item_unit_id_1'],
            'item_default_quantity' => $fields['item_quantity_1'],
            'item_unit_price'       => $fields['item_price_1'],
            'item_unit_cost'        => $fields['item_cost_1'],
            'company_id'            => Auth::user()->company_id,
            'updated_id'            => Auth::id(),
            'created_id'            => Auth::id(),
        ]);

        $item = InvtItem::orderBy('created_at', 'DESC')->where('company_id',Auth::user()->company_id)->where('data_state',0)->first();
        
        for ($i=1; $i <= 4; $i++) { 
            $data_packge[$i] = InvtItemPackge::create([
                'item_id'               => $item['item_id'],
                'item_unit_id'          => $request['item_unit_id_'.$i],
                'item_category_id'      => $request['item_category_id'],
                'item_default_quantity' => $request['item_quantity_'.$i],
                'item_unit_price'       => $request['item_price_'.$i],
                'item_unit_cost'        => $request['item_cost_'.$i],
                'order'                 => $i,
                'company_id'            => Auth::user()->company_id,
                'updated_id'            => Auth::id(),
                'created_id'            => Auth::id(),
            ]);
        }
        $warehouse = InvtWarehouse::where('data_state',0)->where('company_id',Auth::user()->company_id)->get();
        foreach ($warehouse as $key => $val) {
            $stock[$key] = InvtItemStock::create([
                'company_id'        => $item['company_id'],
                'warehouse_id'      => $val['warehouse_id'],
                'item_id'           => $item['item_id'],
                'item_unit_id'      => $request['item_unit_id_1'],
                'item_category_id'  => $item['item_category_id'],
                'last_balance'      => 0,
                'updated_id'        => Auth::id(),
                'created_id'        => Auth::id(),       
            ]);
            
        }
        // $data_packge->save();
        // dd($data_packge);
        if($data->save()){
            $msg    = "Tambah Barang Berhasil";
            return redirect('/item/add-item')->with('msg', $msg);
        } else {
            $msg    = "Tambah Barang Gagal";
            return redirect('/item/add-item')->with('msg', $msg);
        }
    }

    public function editItem($item_id)
    {
        $itemunits    = InvtItemUnit::where('data_state','=',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('item_unit_name','item_unit_id');
        $category    = InvtItemCategory::where('data_state','=',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('item_category_name','item_category_id');
        $items  = InvtItem::select('item_category_id','item_code','item_id','item_name','item_remark')
        ->where('item_id', $item_id)
        ->first();
        $status = array(
            0 => 'Aktif',
            1 => 'Non Aktif'
        );
        $item_packge = InvtItemPackge::select('order','item_unit_id','item_default_quantity','item_packge_id','item_unit_cost','item_unit_price')
        ->where('item_id', $item_id)
        ->get();
        // dd($item_packge);
        return view('content.InvtItem.FormEditInvtItem', compact('items', 'itemunits', 'category','status','item_packge'));
    }

    public function processEditItem(Request $request)
    {
        // dd($request->all());
        $fields = $request->validate([
            'item_id'           => '',
            'item_category_id'  => 'required',
            'item_code'         => 'required',
            'item_name'         => 'required',
            'item_remark'       => '',
            'item_unit_id_1'    => 'required',
            'item_quantity_1'   => 'required',
            'item_price_1'      => 'required',
            'item_cost_1'       => 'required'
        ]);

        $table                          = InvtItem::findOrFail($fields['item_id']);
        $table->item_category_id        = $fields['item_category_id'];
        $table->item_code               = $fields['item_code'];
        $table->item_name               = $fields['item_name'];
        $table->item_remark             = $fields['item_remark'];
        $table->item_unit_id            = $fields['item_unit_id_1'];
        $table->item_default_quantity   = $fields['item_quantity_1'];
        $table->item_unit_price         = $fields['item_price_1'];
        $table->item_unit_cost          = $fields['item_cost_1'];
        $table->updated_id              = Auth::id();


        for ($i=1; $i <= 4; $i++) {
            $first_data_packge[$i] = InvtItemPackge::where('item_packge_id',$request['item_packge_id_'.$i])
            ->first();
            $data_packge[$i] = InvtItemPackge::findOrFail($first_data_packge[$i]['item_packge_id'])
            ->update([
                'item_unit_id'          => $request['item_unit_id_'.$i],
                'item_category_id'      => $request['item_category_id'],
                'item_default_quantity' => $request['item_quantity_'.$i],
                'item_unit_price'       => $request['item_price_'.$i],
                'item_unit_cost'        => $request['item_cost_'.$i],
                'updated_id'            => Auth::id(),
            ]);
            
        }
        $first_data_stock = InvtItemStock::where('item_id', $fields['item_id'])
        ->where('item_unit_id', $request['item_unit_id_1'])
        ->where('item_category_id', $request['item_category_id'])
        ->first();
        InvtItemStock::where('item_stock_id',$first_data_stock['item_stock_id'])
        ->update([
            'item_unit_id'          => $request['item_unit_id_1'],
            'item_category_id'      => $request['item_category_id'],
            'updated_id'            => Auth::id(),
        ]);
        
        // dd($table);
        if($table->save()){
            $msg = "Ubah Barang Berhasil";
            return redirect('/item')->with('msg', $msg);
        } else {
            $msg = "Ubah Barang Gagal";
            return redirect('/item')->with('msg', $msg);
        }
    }

    public function deleteItem($item_id)
    {
        $item_packge = InvtItemPackge::where('item_id', $item_id)
        ->get();
        foreach ($item_packge as $key => $val) {
            InvtItemStock::where('item_id',$val['item_id'])
            ->where('item_unit_id', $val['item_unit_id'])
            ->where('item_category_id', $val['item_category_id'])
            ->update(['data_state' => 1,'updated_id' => Auth::id()]);
            
            InvtItemPackge::where('item_id',$val['item_id'])
            ->where('item_unit_id', $val['item_unit_id'])
            ->where('item_category_id', $val['item_category_id'])
            ->update(['data_state' => 1,'updated_id' => Auth::id()]);
        }

        $table             = InvtItem::findOrFail($item_id);
        $table->data_state = 1;
        $table->updated_id = Auth::id();

        if($table->save()){
            $msg = "Hapus Barang Berhasil";
            return redirect('/item')->with('msg', $msg);
        } else {
            $msg = "Hapus Barang Gagal";
            return redirect('/item')->with('msg', $msg);
        }

    }

    public function addResetItem()
    {
        Session::forget('items');
        return redirect('/item/add-item');
    }

    public function dataTableItem(Request $request)
    {
        $draw 				= 		$request->get('draw');
        $start 				= 		$request->get("start");
        $rowPerPage 		= 		$request->get("length");
        $orderArray 	    = 		$request->get('order');
        $columnNameArray 	= 		$request->get('columns');
        $searchArray 		= 		$request->get('search');
        $columnIndex 		= 		$orderArray[0]['column'];
        $columnName 		= 		$columnNameArray[$columnIndex]['data'];
        $columnSortOrder 	= 		$orderArray[0]['dir'];
        $searchValue 		= 		$searchArray['value'];


        $users = InvtItem::join('invt_item_category', 'invt_item_category.item_category_id', '=', 'invt_item.item_category_id')
        ->where('invt_item.data_state','=',0)
        ->where('invt_item.company_id', Auth::user()->company_id);
        $total = $users->count();

        $totalFilter = InvtItem::join('invt_item_category', 'invt_item_category.item_category_id', '=', 'invt_item.item_category_id')
        ->where('invt_item.data_state','=',0)
        ->where('invt_item.company_id', Auth::user()->company_id);
        if (!empty($searchValue)) {
            $totalFilter = $totalFilter->where('item_name','like','%'.$searchValue.'%');
            $totalFilter = $totalFilter->orWhere('item_code','like','%'.$searchValue.'%');
        }
        $totalFilter = $totalFilter->count();


        $arrData = InvtItem::join('invt_item_category', 'invt_item_category.item_category_id', '=', 'invt_item.item_category_id')
        ->where('invt_item.data_state','=',0)
        ->where('invt_item.company_id', Auth::user()->company_id);
        $arrData = $arrData->skip($start)->take($rowPerPage);
        $arrData = $arrData->orderBy($columnName,$columnSortOrder);

        if (!empty($searchValue)) {
            $arrData = $arrData->where('item_name','like','%'.$searchValue.'%');
            $arrData = $arrData->orWhere('item_code','like','%'.$searchValue.'%');
        }

        $arrData = $arrData->get();

         $no = $start;
        $data = array();
        foreach ($arrData as $key => $val) {
            $no++;
            $row = array();
            $row['no'] = "<div class='text-center'>".$no.".</div>";
            $row['item_category_name'] = $val['item_category_name'];
            $row['item_code'] = $val['item_code'];
            $row['item_name'] = $val['item_name'];
            $row['barcode'] = "<div class='text-center'><a type='button' class='btn btn-outline-dark btn-sm' href='".url('/item-barcode/'. $val['item_id'])."'><i class='fa fa-barcode'></i> Barcode</a></div>";
            $row['action'] = "<div class='text-center'><a type='button' class='btn btn-outline-warning btn-sm' href='".url('/item/edit-item/'.$val['item_id'])."'>Edit</a>
            <a type='button' class='btn btn-outline-danger btn-sm' href='".url('/item/delete-item/'.$val['item_id'])."'>Hapus</a></div>";

            $data[] = $row;
        }
        $response = array(
            "draw" => intval($draw),
            "recordsTotal" => $total,
            "recordsFiltered" => $totalFilter,
            "data" => $data,
        );

        return json_encode($response);
    }

    public function countMarginAddItem(Request $request)
    {
        $item_category = InvtItemCategory::where('item_category_id', $request->item_category_id)
        ->first();
        $data = (($request->item_unit_cost * $item_category['margin_percentage']) / 100) + $request->item_unit_cost;

        return $data;
    }

    public function checkWarehouse()
    {
        $data = InvtWarehouse::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->first();

        return $data;
    }
}
