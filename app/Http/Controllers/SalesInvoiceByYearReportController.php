<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\InvtItem;
use App\Models\InvtItemCategory;
use App\Models\InvtItemPackge;
use App\Models\SalesInvoice;
use Elibyy\TCPDF\Facades\TCPDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class SalesInvoiceByYearReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        
    }

    public function index()
    {
        if(!$year = Session::get('year')){
            $year = date('Y');
        } else {
            $year = Session::get('year');
        }
        // $data = SalesInvoice::join('sales_invoice_item','sales_invoice.sales_invoice_id','=','sales_invoice_item.sales_invoice_id')
        // ->whereYear('sales_invoice.sales_invoice_date',$year)
        // ->where('sales_invoice.company_id', Auth::user()->company_id)
        // ->where('sales_invoice.data_state',0)
        // ->get();
        $data = InvtItem::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get();
        $year_now 	=	date('Y');
        for($i=($year_now-2); $i<($year_now+2); $i++){
            $yearlist[$i] = $i;
        } 

        return view('content.SalesInvoiceByYearReport.ListSalesInvoiceByYearReport',compact('data','year','yearlist'));
    }

    public function filterSalesInvoicebyYearReport(Request $request)
    {
        $year = $request->year_period;
        Session::put('year',$year);
        return redirect('/sales-invoice-by-year-report');
    }

    public function getItemName($item_id)
    {
        $data = InvtItem::where('item_id',$item_id)->first();

        return $data['item_name'];
    }

    public function getCategoryName($item_category_id)
    {
        $data = InvtItemCategory::where('item_category_id', $item_category_id)->first();

        return $data['item_category_name'];
    }

    public function getTotalItem($item_id)
    {
         if(!$year = Session::get('year')){
            $year = date('Y');
        } else {
            $year = Session::get('year');
        }

        $data = SalesInvoice::join('sales_invoice_item','sales_invoice.sales_invoice_id','=','sales_invoice_item.sales_invoice_id')
        ->whereYear('sales_invoice.sales_invoice_date',$year)
        ->where('sales_invoice.company_id', Auth::user()->company_id)
        ->where('sales_invoice_item.item_id', $item_id)
        ->where('sales_invoice.data_state',0)
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
         if(!$year = Session::get('year')){
            $year = date('Y');
        } else {
            $year = Session::get('year');
        }

        $data = SalesInvoice::join('sales_invoice_item','sales_invoice.sales_invoice_id','=','sales_invoice_item.sales_invoice_id')
        ->whereYear('sales_invoice.sales_invoice_date',$year)
        ->where('sales_invoice.company_id', Auth::user()->company_id)
        ->where('sales_invoice_item.item_id', $item_id)
        ->where('sales_invoice.data_state',0)
        ->get();

        $total_amount = 0;
        foreach ($data as $key => $val) {
            $total_amount += $val['subtotal_amount_after_discount'];
        }

        return $total_amount;
    }

    public function printSalesInvoicebyYearReport()
    {
        if(!$year = Session::get('year')){
            $year = date('Y');
        } else {
            $year = Session::get('year');
        }
        // $data = SalesInvoice::join('sales_invoice_item','sales_invoice.sales_invoice_id','=','sales_invoice_item.sales_invoice_id')
        // ->whereYear('sales_invoice.sales_invoice_date',$year)
        // ->where('sales_invoice.company_id', Auth::user()->company_id)
        // ->where('sales_invoice.data_state',0)
        // ->get();
        $data = InvtItem::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get();

        $pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $pdf::setHeaderCallback(function($pdf){
            $pdf->SetFont('helvetica', '', 8);
            $header = "
            <div></div>
                <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
                    <tr>
                        <td rowspan=\"3\" width=\"6%\"><img src=\"".asset('resources/assets/img/logo_kopkar.png')."\" width=\"35\"></td>
                        <td rowspan=\"2\" width=\"70%\"><div style=\"text-align: left; font-weight: bold; font-size: 10pt;\">Koperasi Menjangan Enam</div></td>
                        <td width=\"10%\"><div style=\"text-align: left;\">Halaman</div></td>
                        <td width=\"2%\"><div style=\"text-align: center;\">:</div></td>
                        <td width=\"12%\"><div style=\"text-align: left;\">".$pdf->getAliasNumPage()." / ".$pdf->getAliasNbPages()."</div></td>
                    </tr>  
                    <tr>
                        <td width=\"10%\"><div style=\"text-align: left;\">Dicetak</div></td>
                        <td width=\"2%\"><div style=\"text-align: center;\">:</div></td>
                        <td width=\"12%\"><div style=\"text-align: left;\">".ucfirst(Auth::user()->name)."</div></td>
                    </tr>
                    <tr>
                        <td width=\"70%\">Kota Semarang</td>
                        <td width=\"10%\"><div style=\"text-align: left;\">Tgl. Cetak</div></td>
                        <td width=\"2%\"><div style=\"text-align: center;\">:</div></td>
                        <td width=\"12%\"><div style=\"text-align: left;\">".date('d-m-Y H:i')."</div></td>
                    </tr>
                </table>
                <hr>
            ";

            $pdf->writeHTML($header, true, false, false, false, '');
        });
        $pdf::SetPrintFooter(false);

        $pdf::SetMargins(10, 20, 10, 10); // put space of 10 on top

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
                <td><div style=\"text-align: center; font-size:14px; font-weight: bold\">LAPORAN PENJUALAN TAHUNAN</div></td>
            </tr>
            <tr>
                <td><div style=\"text-align: center; font-size:12px\">TAHUN : ".$year."</div></td>
            </tr>
        </table>
        ";
        $pdf::writeHTML($tbl, true, false, false, false, '');
        
        $no = 1;
        $tblStock1 = "
        <table cellspacing=\"0\" cellpadding=\"1\" border=\"1\" width=\"100%\">
            <tr>
                <td width=\"5%\"><div style=\"text-align: center; font-weight: bold\">No</div></td>
                <td width=\"20%\"><div style=\"text-align: center; font-weight: bold\">Kategori Barang</div></td>
                <td width=\"45%\"><div style=\"text-align: center; font-weight: bold\">Nama Barang</div></td>
                <td width=\"15%\"><div style=\"text-align: center; font-weight: bold\">Jumlah Penjualan</div></td>
                <td width=\"15%\"><div style=\"text-align: center; font-weight: bold\">Total</div></td>

            </tr>
        
             ";

        $no = 1;
        $totalitem = 0;
        $totalamount = 0;
        $tblStock2 =" ";
        foreach ($data as $key => $val) {
            $tblStock2 .="
                <tr nobr=\"true\">			
                    <td style=\"text-align:center\">$no.</td>
                    <td style=\"text-align:left\">".$this->getCategoryName($val['item_category_id'])."</td>
                    <td style=\"text-align:left\">".$this->getItemName($val['item_id'])."</td>
                    <td style=\"text-align:right\">".$this->getTotalItem($val['item_id'])."</td>
                    <td style=\"text-align:right\">".number_format($this->getTotalAmount($val['item_id']),2,'.',',')."</td>
                </tr>
                
            ";
            $totalitem += $this->getTotalItem($val['item_id']);
            $totalamount += $this->getTotalAmount($val['item_id']);
            $no++;
        }
        $tblStock3 = " 
        <tr nobr=\"true\">
            <td colspan=\"3\"><div style=\"text-align: center;  font-weight: bold\">TOTAL</div></td>
            <td style=\"text-align:right;\"><div style=\"font-weight: bold\">". $totalitem ."</div></td>
            <td style=\"text-align: right\"><div style=\"font-weight: bold\">". number_format($totalamount,2,'.',',') ."</div></td>
        </tr>
        </table>";

        $pdf::writeHTML($tblStock1.$tblStock2.$tblStock3, true, false, false, false, '');

        $filename = 'Laporan_Penjualan_Tahunan_'.$year.'.pdf';
        $pdf::Output($filename, 'I');
    }

    public function exportSalesInvoicebyYearReport()
    {
        if(!$year = Session::get('year')){
            $year = date('Y');
        } else {
            $year = Session::get('year');
        }
        // $data = SalesInvoice::join('sales_invoice_item','sales_invoice.sales_invoice_id','=','sales_invoice_item.sales_invoice_id')
        // ->whereYear('sales_invoice.sales_invoice_date',$year)
        // ->where('sales_invoice.company_id', Auth::user()->company_id)
        // ->where('sales_invoice.data_state',0)
        // ->get();
        $data = InvtItem::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get();

        $spreadsheet = new Spreadsheet();

        if(count($data)>=0){
            $spreadsheet->getProperties()->setCreator("CST MOZAIQ POS")
                                        ->setLastModifiedBy("CST MOZAIQ POS")
                                        ->setTitle("Laporan Penjualan")
                                        ->setSubject("")
                                        ->setDescription("Laporan Penjualan")
                                        ->setKeywords("Laporan, Penjualan")
                                        ->setCategory("Laporan Penjualan");
                                 
            $sheet = $spreadsheet->getActiveSheet(0);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(5);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(40);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(20);

    
            $spreadsheet->getActiveSheet()->mergeCells("B1:F1");
            $spreadsheet->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
            $spreadsheet->getActiveSheet()->getStyle('B3:F3')->getFont()->setBold(true);

            $spreadsheet->getActiveSheet()->getStyle('B3:F3')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->getStyle('B3:F3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('B1',"Laporan Penjualan Tahunan Periode ".$year);	
            $sheet->setCellValue('B3',"No");
            $sheet->setCellValue('C3',"Nama Kategori");
            $sheet->setCellValue('D3',"Nama Barang");
            $sheet->setCellValue('E3',"Jumlah Penjualan");
            $sheet->setCellValue('F3',"Total");
            
            $j=4;
            $no=0;
            $totalitem = 0;
            $totalamount = 0;
            
            foreach($data as $key=>$val){

                if(is_numeric($key)){
                    
                    $sheet = $spreadsheet->getActiveSheet(0);
                    $spreadsheet->getActiveSheet()->setTitle("Laporan Penjualan Tahunan");
                    $spreadsheet->getActiveSheet()->getStyle('B'.$j.':F'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                    $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getNumberFormat()->setFormatCode('0.00');
            
                    $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $spreadsheet->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);



                        $no++;
                        $sheet->setCellValue('B'.$j, $no);
                        $sheet->setCellValue('C'.$j, $this->getCategoryName($val['item_category_id']));
                        $sheet->setCellValue('D'.$j, $this->getItemName($val['item_id']));
                        $sheet->setCellValue('E'.$j, $this->getTotalItem($val['item_id']));
                        $sheet->setCellValue('F'.$j, $this->getTotalAmount($val['item_id']));

                }
                $j++;
                $totalitem += $this->getTotalItem($val['item_id']);
                $totalamount += $this->getTotalAmount($val['item_id']);
        
            }
            $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getNumberFormat()->setFormatCode('0.00');
            $spreadsheet->getActiveSheet()->getStyle('B'.$j.':F'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->mergeCells('B'.$j.':D'.$j);
            $spreadsheet->getActiveSheet()->getStyle('B'.$j.':F'.$j)->getFont()->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

            $sheet->setCellValue('B'.$j, 'TOTAL');
            $sheet->setCellValue('E'.$j, $totalitem);
            $sheet->setCellValue('F'.$j, $totalamount);

            $j++;
            $spreadsheet->getActiveSheet()->mergeCells('B'.$j.':F'.$j);
            $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('B'.$j, Auth::user()->name.", ".date('d-m-Y H:i'));
            
            $filename='Laporan_Penjualan_Tahunan_'.$year.'.xls';
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="'.$filename.'"');
            header('Cache-Control: max-age=0');

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
            $writer->save('php://output');
        }else{
            echo "Maaf data yang di eksport tidak ada !";
        }
    }

    public function tableSalesInvoiceByYear(Request $request)
    {   
        $draw 				= $request->get('draw');
        $start 				= $request->get("start");
        $rowPerPage 		= $request->get("length");
        $orderArray 	    = $request->get('order');
        $columnNameArray 	= $request->get('columns');
        $searchArray 		= $request->get('search');
        $columnIndex 		= $orderArray[0]['column'];
        $columnName 		= $columnNameArray[$columnIndex]['data'];
        $columnSortOrder 	= $orderArray[0]['dir'];
        $searchValue 		= $searchArray['value'];
        $valueArray         = explode (" ",$searchValue);


        $users = InvtItem::where('data_state','=',0)
        ->where('company_id', Auth::user()->company_id);
        $total = $users->count();

        $totalFilter = InvtItem::where('data_state','=',0)
        ->where('company_id', Auth::user()->company_id);
        if (!empty($searchValue)) {
            if (count($valueArray) != 1) {
                foreach ($valueArray as $key => $val) {
                    $totalFilter = $totalFilter->where('item_name','like','%'.$val.'%');
                }
            } else {
                $totalFilter = $totalFilter->where('item_name','like','%'.$searchValue.'%');
            }
        }
        $totalFilter = $totalFilter->count();


        $arrData = InvtItem::where('data_state','=',0)
        ->where('company_id', Auth::user()->company_id);
        $arrData = $arrData->skip($start)->take($rowPerPage);
        $arrData = $arrData->orderBy($columnName,$columnSortOrder);

        if (!empty($searchValue)) {
            if (count($valueArray) != 1) {
                foreach ($valueArray as $key => $val) {
                    $arrData = $arrData->where('item_name','like','%'.$val.'%');
                }
            } else {
                $arrData = $arrData->where('item_name','like','%'.$searchValue.'%');
            }
        }

        $arrData = $arrData->get();

         $no = $start;
        $data = array();
        foreach ($arrData as $key => $val) {
            $no++;
            $row = array();
            $row['no'] = "<div class='text-center'>".$no.".</div>";
            $row['item_category_name'] = $this->getCategoryName($val['item_category_id']);
            $row['item_name'] = $this->getItemName($val['item_id']);
            $row['total_item'] = "<div class='text-right'>".$this->getTotalItem($val['item_id'])."</div>";
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
