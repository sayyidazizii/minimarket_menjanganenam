<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\InvtItem;
use App\Models\InvtItemCategory;
use App\Models\InvtItemPackge;
use App\Models\InvtWarehouse;
use App\Models\PurchaseInvoice;
use Elibyy\TCPDF\Facades\TCPDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class PurchaseInvoicebyItemReportController extends Controller
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
        if(!Session::get('warehouse_id')){
            $warehouse_id     = '';
        }else{
            $warehouse_id = Session::get('warehouse_id');
        }

        $warehouse  = InvtWarehouse::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('warehouse_name','warehouse_id');
        // $data = PurchaseInvoice::join('purchase_invoice_item','purchase_invoice_item.purchase_invoice_id','=','purchase_invoice.purchase_invoice_id')
        // ->where('purchase_invoice.purchase_invoice_date','>=',$start_date)
        // ->where('purchase_invoice.purchase_invoice_date','<=',$end_date)
        // ->where('purchase_invoice.warehouse_id', $warehouse_id)
        // ->where('purchase_invoice.company_id', Auth::user()->company_id)
        // ->where('purchase_invoice.data_state',0)
        // ->get();
        $data = InvtItem::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get();
        return view('content.PurchaseInvoicebyItemReport.ListPurchaseInvoicebyItemReport',compact('warehouse', 'data','start_date','end_date'));
    }

    public function filterPurchaseInvoicebyItemReport(Request $request)
    {
        $start_date     = $request->start_date;
        $end_date       = $request->end_date;
        $warehouse_id   = $request->warehouse_id;
        
        Session::put('start_date', $start_date);
        Session::put('end_date', $end_date);
        Session::put('warehouse_id', $warehouse_id);
    
        return redirect('/purchase-invoice-by-item-report');
    }

    public function filterResetPurchaseInvoicebyItemReport()
    {
        Session::forget('start_date');
        Session::forget('end_date');
        Session::forget('warehouse_id');
    
        return redirect('/purchase-invoice-by-item-report');  
    }

    public function getCategoryName($item_category_id)
    {
        $data   = InvtItemCategory::where('item_category_id', $item_category_id)->first();

        return $data['item_category_name'];
    }
    
    public function getItemName($item_id)
    {
        $data   = InvtItem::where('item_id', $item_id)->first();

        return $data['item_name'];
    }

    public function getTotalItem($item_id)
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
        $data = PurchaseInvoice::join('purchase_invoice_item','purchase_invoice_item.purchase_invoice_id','=','purchase_invoice.purchase_invoice_id')
        ->where('purchase_invoice.purchase_invoice_date','>=',$start_date)
        ->where('purchase_invoice.purchase_invoice_date','<=',$end_date)
        ->where('purchase_invoice.company_id', Auth::user()->company_id)
        ->where('purchase_invoice_item.item_id', $item_id)
        ->where('purchase_invoice.data_state',0)
        ->get();

        $total_item = 0;
        foreach ($data as $key => $val) {
            $data_packge[$key] = InvtItemPackge::where('data_state',0)
            ->where('company_id', Auth::user()->company_id)
            ->where('item_id', $val['item_id'])
            ->where('item_unit_id', $val['item_unit_id'])
            ->first();
            $total_item += $val['quantity'] * $data_packge[$key]['item_default_quantity'];
        }

        return $total_item;
    }

    public function getTotalAmount($item_id)
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
        $data = PurchaseInvoice::join('purchase_invoice_item','purchase_invoice_item.purchase_invoice_id','=','purchase_invoice.purchase_invoice_id')
        ->where('purchase_invoice.purchase_invoice_date','>=',$start_date)
        ->where('purchase_invoice.purchase_invoice_date','<=',$end_date)
        ->where('purchase_invoice.company_id', Auth::user()->company_id)
        ->where('purchase_invoice_item.item_id', $item_id)
        ->where('purchase_invoice.data_state',0)
        ->get();

        $total_amount = 0;
        foreach ($data as $key => $val) {
            $total_amount += $val['subtotal_amount_after_discount'];
        }

        return $total_amount;
    }

    public function printPurchaseInvoicebyItemReport()
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
        if(!Session::get('warehouse_id')){
            $warehouse_id     = '';
        }else{
            $warehouse_id = Session::get('warehouse_id');
        }
        // $data = PurchaseInvoice::join('purchase_invoice_item','purchase_invoice_item.purchase_invoice_id','=','purchase_invoice.purchase_invoice_id')
        // ->where('purchase_invoice.purchase_invoice_date','>=',$start_date)
        // ->where('purchase_invoice.purchase_invoice_date','<=',$end_date)
        // ->where('purchase_invoice.warehouse_id', $warehouse_id)
        // ->where('purchase_invoice.company_id', Auth::user()->company_id)
        // ->where('purchase_invoice.data_state',0)
        // ->get();
        $data = InvtItem::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get();

        $pdf = new TCPDF('P', PDF_UNIT, 'F4', true, 'UTF-8', false);

        $pdf::SetPrintHeader(false);
        $pdf::SetPrintFooter(false);

        $pdf::SetMargins(10, 10, 10, 10); // put space of 10 on top

        $pdf::setImageScale(PDF_IMAGE_SCALE_RATIO);

        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf::setLanguageArray($l);
        }

        $pdf::SetFont('helvetica', 'B', 20);

        $pdf::AddPage();

        $pdf::SetFont('helvetica', '', 8);

        $tbl = "
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"0\">
            <tr>
                <td><div style=\"text-align: center; font-size:14px; font-weight: bold\">LAPORAN PEMBELIAN BARANG</div></td>
            </tr>
            <tr>
                <td><div style=\"text-align: center; font-size:12px\">PERIODE : ".date('d M Y', strtotime($start_date))." s.d. ".date('d M Y', strtotime($end_date))."</div></td>
            </tr>
        </table>
        ";
        $pdf::SetMargins(10, 10, 10, 10);
        $pdf::writeHTML($tbl, true, false, false, false, '');
        $no = 1;
        $tblStock1 = "
        <table cellspacing=\"0\" cellpadding=\"1\" border=\"1\" width=\"100%\">
            <tr>
                <td width=\"5%\" ><div style=\"text-align: center; font-weight: bold\">No</div></td>
                <td width=\"20%\" ><div style=\"text-align: center; font-weight: bold\">Nama Kategori</div></td>
                <td width=\"45%\" ><div style=\"text-align: center; font-weight: bold\">Nama Barang</div></td>
                <td width=\"15%\" ><div style=\"text-align: center; font-weight: bold\">Jumlah Barang</div></td>
                <td width=\"15%\" ><div style=\"text-align: center; font-weight: bold\">Jumlah Pembelian</div></td>
            </tr>
        
             ";

        $no = 1;
        $total_item = 0;
        $total_amount = 0;
        $tblStock2 =" ";
        foreach ($data as $key => $val) {
            $tblStock2 .="
                <tr>			
                    <td style=\"text-align:center\">$no.</td>
                    <td style=\"text-align:left\">".$this->getCategoryName($val['item_category_id'])."</td>
                    <td style=\"text-align:left\">".$this->getItemName($val['item_id'])."</td>
                    <td style=\"text-align:right\">".$this->getTotalItem($val['item_id'])."</td>
                    <td style=\"text-align:right\">".number_format($this->getTotalAmount($val['item_id']),2,'.',',')."</td>
                </tr>
                
            ";
            $no++;
            $total_item += $this->getTotalItem($val['item_id']);
            $total_amount += $this->getTotalAmount($val['item_id']);
        }
        $tblStock3 = " 
        <tr>
            <td colspan=\"3\"><div style=\"text-align: center;  font-weight: bold\">TOTAL</div></td>
            <td style=\"text-align: right\"><div style=\"font-weight: bold\">". $total_item ."</div></td>
            <td style=\"text-align: right\"><div style=\"font-weight: bold\">". number_format($total_amount,2,'.',',') ."</div></td>
        </tr>
        </table>
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"0\">
            <tr>
                <td style=\"text-align:right\">".Auth::user()->name.", ".date('d-m-Y H:i')."</td>
            </tr>
        </table>";

        $pdf::writeHTML($tblStock1.$tblStock2.$tblStock3, true, false, false, false, '');


        $filename = 'Laporan_Pembelian_Barang_'.$start_date.'s.d.'.$end_date.'.pdf';
        $pdf::Output($filename, 'I');
    }

    public function exportPurchaseInvoicebyItemReport()
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
        if(!Session::get('warehouse_id')){
            $warehouse_id     = '';
        }else{
            $warehouse_id = Session::get('warehouse_id');
        }
        // $data = PurchaseInvoice::join('purchase_invoice_item','purchase_invoice_item.purchase_invoice_id','=','purchase_invoice.purchase_invoice_id')
        // ->where('purchase_invoice.purchase_invoice_date','>=',$start_date)
        // ->where('purchase_invoice.purchase_invoice_date','<=',$end_date)
        // ->where('purchase_invoice.warehouse_id', $warehouse_id)
        // ->where('purchase_invoice.company_id', Auth::user()->company_id)
        // ->where('purchase_invoice.data_state',0)
        // ->get();
        $data = InvtItem::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get();

        $spreadsheet = new Spreadsheet();

        if(count($data)>=0){
            $spreadsheet->getProperties()->setCreator("IBS CJDW")
                                        ->setLastModifiedBy("IBS CJDW")
                                        ->setTitle("Purchase Invoice Report")
                                        ->setSubject("")
                                        ->setDescription("Purchase Invoice Report")
                                        ->setKeywords("Purchase, Invoice, Report")
                                        ->setCategory("Purchase Invoice Report");
                                 
            $sheet = $spreadsheet->getActiveSheet(0);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(5);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(35);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(20);
    
            $spreadsheet->getActiveSheet()->mergeCells("B1:F1");
            $spreadsheet->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
            $spreadsheet->getActiveSheet()->getStyle('B3:F3')->getFont()->setBold(true);

            $spreadsheet->getActiveSheet()->getStyle('B3:F3')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->getStyle('B3:F3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('B1',"Laporan Pembelian Barang Dari Periode ".date('d M Y', strtotime($start_date))." s.d. ".date('d M Y', strtotime($end_date)));	
            $sheet->setCellValue('B3',"No");
            $sheet->setCellValue('C3',"Nama Kategori");
            $sheet->setCellValue('D3',"Nama Barang");
            $sheet->setCellValue('E3',"Jumlah Barang");
            $sheet->setCellValue('F3',"Jumlah Pembelian");
            
            $j=4;
            $total_item = 0;
            $total_amount = 0;
            $no=0;
            
            foreach($data as $key=>$val){

                if(is_numeric($key)){
                    
                    $sheet = $spreadsheet->getActiveSheet(0);
                    $spreadsheet->getActiveSheet()->setTitle("Laporan Pembelian Barang");
                    $spreadsheet->getActiveSheet()->getStyle('B'.$j.':F'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                    $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getNumberFormat()->setFormatCode('0.00');
            
                    $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $spreadsheet->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);


                    $id = $val['purchase_invoice_id'];

                    if($val['purchase_invoice_id'] == $id){

                        $no++;
                        $sheet->setCellValue('B'.$j, $no);
                        $sheet->setCellValue('C'.$j, $this->getCategoryName($val['item_category_id']));
                        $sheet->setCellValue('D'.$j, $this->getItemName($val['item_id']));
                        $sheet->setCellValue('E'.$j, $this->getTotalItem($val['item_id']));
                        $sheet->setCellValue('F'.$j, $this->getTotalAmount($val['item_id']));
                    }
                           
                    
                }else{
                    continue;
                }
                $j++;
                $total_item += $this->getTotalItem($val['item_id']);
                $total_amount += $this->getTotalAmount($val['item_id']);
        
            }
            $spreadsheet->getActiveSheet()->mergeCells('B'.$j.':D'.$j);
            $spreadsheet->getActiveSheet()->getStyle('B'.$j.':F'.$j)->getFont()->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getNumberFormat()->setFormatCode('0.00');
            $spreadsheet->getActiveSheet()->getStyle('B'.$j.':F'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('B'.$j, 'TOTAL');
            $sheet->setCellValue('E'.$j, $total_item);
            $sheet->setCellValue('F'.$j, $total_amount);

            $j++;
            $spreadsheet->getActiveSheet()->mergeCells('B'.$j.':F'.$j);
            $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('B'.$j, Auth::user()->name.", ".date('d-m-Y H:i'));
            
            $filename='Laporan_Pembelian_Barang_'.$start_date.'_s.d._'.$end_date.'.xls';
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="'.$filename.'"');
            header('Cache-Control: max-age=0');

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
            $writer->save('php://output');
        }else{
            echo "Maaf data yang di eksport tidak ada !";
        }
    }

    public function tablePurchaseItemReport(Request $request)
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


        $users = InvtItem::where('data_state','=',0)
        ->where('company_id', Auth::user()->company_id);
        $total = $users->count();

        $totalFilter = InvtItem::where('data_state','=',0)
        ->where('company_id', Auth::user()->company_id);
        if (!empty($searchValue)) {
            $totalFilter = $totalFilter->where('item_name','like','%'.$searchValue.'%');
        }
        $totalFilter = $totalFilter->count();


        $arrData = InvtItem::where('data_state','=',0)
        ->where('company_id', Auth::user()->company_id);
        $arrData = $arrData->skip($start)->take($rowPerPage);
        $arrData = $arrData->orderBy($columnName,$columnSortOrder);

        if (!empty($searchValue)) {
            $arrData = $arrData->where('item_name','like','%'.$searchValue.'%');
        }

        $arrData = $arrData->get();

         $no = $start;
        $data = array();
        foreach ($arrData as $key => $val) {
            $no++;
            $row = array();
            $row['no'] = "<div class='text-center'>".$no.".</div>";
            $row['item_category_name'] = $this->getCategoryName($val['item_category_id']);
            $row['item_name'] = $val['item_name'];
            $row['item_total'] = "<div class='text-right'>".$this->getTotalItem($val['item_id'])."</div>";
            $row['total_amount'] = "<div class='text-right'>".number_format($this->getTotalAmount($val['item_id']),2,'.',',')."</div>";

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
}
