<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\InvtItem;
use App\Models\InvtItemCategory;
use App\Models\InvtItemUnit;
use App\Models\SalesInvoice;
use App\Models\User;
use Elibyy\TCPDF\Facades\TCPDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class SalesInvoiceByUserReportController extends Controller
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
        if(!$user_id = Session::get('user_id')){
            $user_id = '';
        } else {
            $user_id = Session::get('user_id');
        }
        $user = User::where('data_state',0)
        ->where('user_id', '!=', 55)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('name','user_id');
        $data = SalesInvoice::where('sales_invoice_date','>=',$start_date)
        ->where('sales_invoice_date','<=',$end_date)
        ->where('created_id', $user_id)
        ->where('company_id', Auth::user()->company_id)
        ->where('data_state',0)
        ->get();
        return view('content.SalesInvoiceByUserReport.ListSalesInvoiceByUserReport', compact('user','data','start_date','end_date'));
    }

    public function filterSalesInvoicebyUserReport(Request $request)
    {
        $start_date = $request->start_date;
        $end_date   = $request->end_date;
        $user_id    = $request->user_id;

        Session::put('start_date', $start_date);
        Session::put('end_date', $end_date);
        Session::put('user_id', $user_id);

        return redirect('/sales-invoice-by-user-report');
    }

    public function filterResetSalesInvoicebyUserReport()
    {
        Session::forget('start_date');
        Session::forget('end_date');
        Session::forget('user_id');

        return redirect('/sales-invoice-by-user-report');
    }

    public function getUserName($created_id)
    {
        $data = User::where('user_id',$created_id)->first();

        return $data['name'];
    }

    public function getItemName($item_id)
    {
        $data = InvtItem::where('item_id',$item_id)->first();

        return $data['item_name'];
    }

    public function getItemUnitName($item_unit_id)
    {
        $data = InvtItemUnit::where('item_unit_id', $item_unit_id)->first();

        return $data['item_unit_name'];
    }

    public function getCategoryName($item_category_id)
    {
        $data = InvtItemCategory::where('item_category_id',$item_category_id)->first();

        return $data['item_category_name'];
    }

    public function printSalesInvoicebyUserReport()
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
        if(!$user_id = Session::get('user_id')){
            $user_id = '';
        } else {
            $user_id = Session::get('user_id');
        }

        $data = SalesInvoice::where('sales_invoice_date','>=',$start_date)
        ->where('sales_invoice_date','<=',$end_date);
        if ($user_id != null) {
            $data = $data->where('created_id', $user_id);
        }
        $data = $data->where('company_id', Auth::user()->company_id)
        ->where('data_state',0)
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
                <td><div style=\"text-align: center; font-size:14px; font-weight: bold\">LAPORAN PENJUALAN BY USER</div></td>
            </tr>
            <tr>
                <td><div style=\"text-align: center; font-size:12px\">PERIODE : ".date('d M Y', strtotime($start_date))." s.d. ".date('d M Y', strtotime($end_date))."</div></td>
            </tr>
        </table>
        ";
        $pdf::writeHTML($tbl, true, false, false, false, '');
        
        $no = 1;
        $tblStock1 = "
        <table cellspacing=\"0\" cellpadding=\"1\" border=\"1\" width=\"100%\">
            <tr>
                <td width=\"5%\"><div style=\"text-align: center; font-weight: bold\">No</div></td>
                <td width=\"17%\"><div style=\"text-align: center; font-weight: bold\">Nama User</div></td>
                <td width=\"15%\"><div style=\"text-align: center; font-weight: bold\">Tanggal</div></td>
                <td width=\"15%\"><div style=\"text-align: center; font-weight: bold\">No. Penjualan</div></td>
                <td width=\"12%\"><div style=\"text-align: center; font-weight: bold\">Jumlah Barang</div></td>
                <td width=\"12%\"><div style=\"text-align: center; font-weight: bold\">Subtotal</div></td>
                <td width=\"12%\"><div style=\"text-align: center; font-weight: bold\">Diskon</div></td>
                <td width=\"12%\"><div style=\"text-align: center; font-weight: bold\">Total</div></td>
            </tr>
        
             ";

        $no = 1;
        $subtotal_item = 0;
        $subtotal_amount = 0;
        $discount_amount = 0;
        $total_amount = 0;
        $tblStock2 =" ";
        foreach ($data as $key => $val) {
            $tblStock2 .="
                <tr nobr=\"true\">			
                    <td style=\"text-align:center\">$no.</td>
                    <td style=\"text-align:left\">".$this->getUserName($val['created_id'])."</td>
                    <td style=\"text-align:left\">".date('d-m-Y', strtotime($val['sales_invoice_date']))."</td>
                    <td style=\"text-align:left\">".$val['sales_invoice_no']."</td>
                    <td style=\"text-align:right\">".$val['subtotal_item']."</td>
                    <td style=\"text-align:right\">".number_format($val['subtotal_amount'],2,'.',',')."</td>
                    <td style=\"text-align:right\">".number_format($val['discount_amount_total'],2,'.',',')."</td>
                    <td style=\"text-align:right\">".number_format($val['total_amount'],2,'.',',')."</td>
                </tr>
                
            ";
            $no++;
            $subtotal_item += $val['subtotal_item'];
            $subtotal_amount += $val['subtotal_amount'];
            $discount_amount += $val['discount_amount_total'];
            $total_amount += $val['total_amount'];
        }
        $tblStock3 = " 
        <tr nobr=\"true\">
            <td colspan=\"4\"><div style=\"text-align: center;  font-weight: bold\">TOTAL</div></td>
            <td style=\"text-align:right;\"><div style=\"font-weight: bold\">". $subtotal_item ."</div></td>
            <td style=\"text-align: right\"><div style=\"font-weight: bold\">". number_format($subtotal_amount,2,'.',',') ."</div></td>
            <td style=\"text-align: right\"><div style=\"font-weight: bold\">". number_format($discount_amount,2,'.',',') ."</div></td>
            <td style=\"text-align: right\"><div style=\"font-weight: bold\">". number_format($total_amount,2,'.',',') ."</div></td>
        </tr>

        </table>";

        $pdf::writeHTML($tblStock1.$tblStock2.$tblStock3, true, false, false, false, '');

        $filename = 'Laporan_Penjualan_By_User_'.$start_date.'s.d.'.$end_date.'.pdf';
        $pdf::Output($filename, 'I');
    }

    public function exportSalesInvoicebyUserReport()
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
        if(!$user_id = Session::get('user_id')){
            $user_id = '';
        } else {
            $user_id = Session::get('user_id');
        }
        $data = SalesInvoice::where('sales_invoice_date','>=',$start_date)
        ->where('sales_invoice_date','<=',$end_date)
        ->where('created_id', $user_id)
        ->where('company_id', Auth::user()->company_id)
        ->where('data_state',0)
        ->get();

        $spreadsheet = new Spreadsheet();

        if(count($data)>=0){
            $spreadsheet->getProperties()->setCreator("CST MOZAIC POS")
                                        ->setLastModifiedBy("CST MOZAIC POS")
                                        ->setTitle("Laporan Penjualan By User")
                                        ->setSubject("")
                                        ->setDescription("Laporan Penjualan By User")
                                        ->setKeywords("Laporan, Penjualan")
                                        ->setCategory("Laporan Penjualan");;
                                 
            $sheet = $spreadsheet->getActiveSheet(0);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(5);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(30);
            $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(20);
    
            $spreadsheet->getActiveSheet()->mergeCells("B1:I1");
            $spreadsheet->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
            $spreadsheet->getActiveSheet()->getStyle('B3:I3')->getFont()->setBold(true);

            $spreadsheet->getActiveSheet()->getStyle('B3:I3')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->getStyle('B3:I3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('B1',"Laporan Penjualan By User Dari Periode ".date('d M Y', strtotime($start_date))." s.d. ".date('d M Y', strtotime($end_date)));	
            $sheet->setCellValue('B3',"No");
            $sheet->setCellValue('C3',"Nama User");
            $sheet->setCellValue('D3',"Tanggal");
            $sheet->setCellValue('E3',"No. Penjualan");
            $sheet->setCellValue('F3',"Jumlah Barang");
            $sheet->setCellValue('G3',"Subtotal");
            $sheet->setCellValue('H3',"Diskon");
            $sheet->setCellValue('I3',"Total");
            
            $j=4;
            $no=0;
            $subtotal_item = 0;
            $subtotal_amount = 0;
            $discount_amount = 0;
            $total_amount = 0;
            
            foreach($data as $key=>$val){

                if(is_numeric($key)){
                    
                    $sheet = $spreadsheet->getActiveSheet(0);
                    $spreadsheet->getActiveSheet()->setTitle("Laporan Penjualan By User");
                    $spreadsheet->getActiveSheet()->getStyle('B'.$j.':I'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                    $spreadsheet->getActiveSheet()->getStyle('G'.$j.':I'.$j)->getNumberFormat()->setFormatCode('0.00');
            
                    $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $spreadsheet->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    $spreadsheet->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    $spreadsheet->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    $spreadsheet->getActiveSheet()->getStyle('I'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);


                        $no++;
                        $sheet->setCellValue('B'.$j, $no);
                        $sheet->setCellValue('C'.$j, $this->getUserName($val['created_id']));
                        $sheet->setCellValue('D'.$j, date('d-m-Y', strtotime($val['sales_invoice_date'])));
                        $sheet->setCellValue('E'.$j, $val['sales_invoice_no']);
                        $sheet->setCellValue('F'.$j, $val['subtotal_item']);
                        $sheet->setCellValue('G'.$j, $val['subtotal_amount']);
                        $sheet->setCellValue('H'.$j, $val['discount_amount_total']);
                        $sheet->setCellValue('I'.$j, $val['total_amount']);
                }
                $j++;
                $subtotal_item += $val['subtotal_item'];
                $subtotal_amount += $val['subtotal_amount'];
                $discount_amount += $val['discount_amount_total'];
                $total_amount += $val['total_amount'];
        
            }
            $spreadsheet->getActiveSheet()->getStyle('G'.$j.':I'.$j)->getNumberFormat()->setFormatCode('0.00');

            $spreadsheet->getActiveSheet()->getStyle('B'.$j.':I'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->mergeCells('B'.$j.':E'.$j);
            $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('B'.$j.':I'.$j)->getFont()->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $spreadsheet->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $spreadsheet->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $spreadsheet->getActiveSheet()->getStyle('I'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

            $sheet->setCellValue('B'.$j, 'TOTAL');
            $sheet->setCellValue('F'.$j, $subtotal_item);
            $sheet->setCellValue('G'.$j, $subtotal_amount);
            $sheet->setCellValue('H'.$j, $discount_amount);
            $sheet->setCellValue('I'.$j, $total_amount);

            $j++;
            $spreadsheet->getActiveSheet()->mergeCells('B'.$j.':I'.$j);
            $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('B'.$j, Auth::user()->name.", ".date('d-m-Y H:i'));
            
            $filename='Laporan_Penjualan_By_User_'.$start_date.'_s.d._'.$end_date.'.xls';
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
