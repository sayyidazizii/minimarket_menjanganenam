<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SalesCustomer;
use App\Models\SalesInvoice;
use Elibyy\TCPDF\Facades\TCPDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class SalesCustomerReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        
    }

    public function index()
    {
        if(!$start_date = Session::get('start_date')){
            $start_date = date('Y-m-d');
        } else {
            $start_date = Session::get('start_date');
        }
        if(!$end_date = Session::get('end_date')){
            $end_date = date('Y-m-d');
        } else {
            $end_date = Session::get('end_date');
        }
        $data_customer = SalesCustomer::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get();
        return view('content.SalesCustomerReport.ListSalesCustomerReport', compact('data_customer','start_date','end_date'));
    }

    public function filterSalesCustomerReport(Request $request)
    {
        $start_date = $request->start_date;
        $end_date   = $request->end_date;

        Session::put('start_date',$start_date);
        Session::put('end_date', $end_date);

        return redirect('/sales-customer-report');
    }

    public function resetFilterSalesCustomerReport()
    {
        Session::forget('start_date');
        Session::forget('end_date');

        return redirect('/sales-customer-report');
    }

    public function getTotalTransaction($customer_id)
    {
        if(!$start_date = Session::get('start_date')){
            $start_date = date('Y-m-d');
        } else {
            $start_date = Session::get('start_date');
        }
        if(!$end_date = Session::get('end_date')){
            $end_date = date('Y-m-d');
        } else {
            $end_date = Session::get('end_date');
        }
        $data_sales = SalesInvoice::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->where('customer_id', $customer_id)
        ->where('sales_invoice_date','>=',$start_date)
        ->where('sales_invoice_date','<=',$end_date)
        ->get();

        return count($data_sales);
    }

    public function getTotalItem($customer_id)
    {
        if(!$start_date = Session::get('start_date')){
            $start_date = date('Y-m-d');
        } else {
            $start_date = Session::get('start_date');
        }
        if(!$end_date = Session::get('end_date')){
            $end_date = date('Y-m-d');
        } else {
            $end_date = Session::get('end_date');
        }
        $data_sales = SalesInvoice::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->where('customer_id', $customer_id)
        ->where('sales_invoice_date','>=',$start_date)
        ->where('sales_invoice_date','<=',$end_date)
        ->get();

        $total_item = 0;
        foreach($data_sales as $key=>$val) {
            $total_item += $val['subtotal_item'];
        }

        return $total_item;
    }

    public function getTotalAmount($customer_id)
    {
        if(!$start_date = Session::get('start_date')){
            $start_date = date('Y-m-d');
        } else {
            $start_date = Session::get('start_date');
        }
        if(!$end_date = Session::get('end_date')){
            $end_date = date('Y-m-d');
        } else {
            $end_date = Session::get('end_date');
        }
        $data_sales = SalesInvoice::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->where('customer_id', $customer_id)
        ->where('sales_invoice_date','>=',$start_date)
        ->where('sales_invoice_date','<=',$end_date)
        ->get();;

        $total_amount = 0;
        foreach($data_sales as $key=>$val) {
            $total_amount += $val['total_amount'];
        }

        return $total_amount;
    }

    public function printSalesCustomerReport()
    {
        if(!$start_date = Session::get('start_date')){
            $start_date = date('Y-m-d');
        } else {
            $start_date = Session::get('start_date');
        }
        if(!$end_date = Session::get('end_date')){
            $end_date = date('Y-m-d');
        } else {
            $end_date = Session::get('end_date');
        }
        $data_customer = SalesCustomer::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get();

        $pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

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
                <td><div style=\"text-align: center; font-size:14px; font-weight: bold\">LAPORAN PEMBELIAN ANGGOTA</div></td>
            </tr>
            <tr>
                <td><div style=\"text-align: center; font-size:12px\">PERIODE : ".date('d M Y', strtotime($start_date))." s.d. ".date('d M Y', strtotime($end_date))."</div></td>
            </tr>
        </table>
        ";
        $pdf::SetMargins(30, 10, 10, 10);
        $pdf::writeHTML($tbl, true, false, false, false, '');
        
        $no = 1;
        $tblStock1 = "
        <table cellspacing=\"0\" cellpadding=\"1\" border=\"1\" width=\"100%\">
            <tr>
                <td width=\"5%\"><div style=\"text-align: center;\">No</div></td>
                <td width=\"20%\"><div style=\"text-align: center;\">Nama Anggota</div></td>
                <td width=\"20%\"><div style=\"text-align: center;\">Total Transaksi</div></td>
                <td width=\"20%\"><div style=\"text-align: center;\">Total Barang</div></td>
                <td width=\"20%\"><div style=\"text-align: center;\">Total Pembelian</div></td>

            </tr>
        
             ";

        $no = 1;
        $tblStock2 =" ";
        foreach ($data_customer as $key => $val) {

            $tblStock2 .="
                <tr>			
                    <td style=\"text-align:center\">$no.</td>
                    <td style=\"text-align:left\">".$val['customer_name']."</td>
                    <td style=\"text-align:center\">".$this->getTotalTransaction($val['customer_id'])."</td>
                    <td style=\"text-align:center\">".$this->getTotalItem($val['customer_id'])."</td>
                    <td style=\"text-align:right\">".number_format($this->getTotalAmount($val['customer_id']),2,'.',',')."</td>
                </tr>
                
            ";
            $no++;
        }
        $tblStock3 = " 

        </table>";

        $pdf::writeHTML($tblStock1.$tblStock2.$tblStock3, true, false, false, false, '');

        $filename = 'Laporan_Pembelian_Anggota_'.$start_date.'s.d.'.$end_date.'.pdf';
        $pdf::Output($filename, 'I');
    }

    public function exportSalesCustomerReport()
    {
        if(!$start_date = Session::get('start_date')){
            $start_date = date('Y-m-d');
        } else {
            $start_date = Session::get('start_date');
        }
        if(!$end_date = Session::get('end_date')){
            $end_date = date('Y-m-d');
        } else {
            $end_date = Session::get('end_date');
        }
        $data_customer = SalesCustomer::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get();

        $spreadsheet = new Spreadsheet();

        if(count($data_customer)>=0){
            $spreadsheet->getProperties()->setCreator("CST MOZAIQ POS")
                                        ->setLastModifiedBy("CST MOZAIQ POS")
                                        ->setTitle("Laporan Pembelian Anggota")
                                        ->setSubject("")
                                        ->setDescription("Laporan Pembelian Anggota")
                                        ->setKeywords("Laporan, Pembelian, Anggota")
                                        ->setCategory("Laporan Pembelian Anggota");
                                 
            $sheet = $spreadsheet->getActiveSheet(0);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(5);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(20);

    
            $spreadsheet->getActiveSheet()->mergeCells("B1:F1");
            $spreadsheet->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);

            $spreadsheet->getActiveSheet()->getStyle('B3:F3')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->getStyle('B3:F3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('B1',"Laporan Pembelian Anggota Dari Periode ".date('d M Y', strtotime($start_date))." s.d. ".date('d M Y', strtotime($end_date)));	
            $sheet->setCellValue('B3',"No");
            $sheet->setCellValue('C3',"Nama Anggota");
            $sheet->setCellValue('D3',"Total Transaksi");
            $sheet->setCellValue('E3',"Total Barang");
            $sheet->setCellValue('F3',"Total Pembelian");
            
            $j=4;
            $no=0;
            
            foreach($data_customer as $key=>$val){

                if(is_numeric($key)){
                    
                    $sheet = $spreadsheet->getActiveSheet(0);
                    $spreadsheet->getActiveSheet()->setTitle("Laporan Pembelian Anggota");
                    $spreadsheet->getActiveSheet()->getStyle('B'.$j.':F'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                    $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getNumberFormat()->setFormatCode('0.00');
            
                    $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $spreadsheet->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $spreadsheet->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);


                    $id = $val['sales_invoice_id'];

                    if($val['sales_invoice_id'] == $id){

                        $no++;
                        $sheet->setCellValue('B'.$j, $no);
                        $sheet->setCellValue('C'.$j, $val['customer_name']);
                        $sheet->setCellValue('D'.$j, $this->getTotalTransaction($val['customer_id']));
                        $sheet->setCellValue('E'.$j, $this->getTotalItem($val['customer_id']));
                        $sheet->setCellValue('F'.$j, number_format($this->getTotalAmount($val['customer_id']),2,'.',','));

                    }
                           
                    
                }else{
                    continue;
                }
                $j++;
        
            }
            
            $filename='Laporan_Pembelian_Anggota_'.$start_date.'_s.d._'.$end_date.'.xls';
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="'.$filename.'"');
            header('Cache-Control: max-age=0');

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
            $writer->save('php://output');
        }else{
            echo "Maaf data yang di eksport tidak ada !";
        }
    }
}
