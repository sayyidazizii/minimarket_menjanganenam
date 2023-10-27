<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CoreMember;
use App\Models\SalesInvoice;
use Elibyy\TCPDF\Facades\TCPDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class CoreMemberReportController extends Controller
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
        $data_member = CoreMember::select('member_name', 'member_id', 'division_name')
        ->where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get();
        return view('content.CoreMemberReport.ListCoreMemberReport', compact('data_member','start_date','end_date'));
    }

    public function filterCoreMemberReport(Request $request)
    {
        $start_date = $request->start_date;
        $end_date   = $request->end_date;

        Session::put('start_date',$start_date);
        Session::put('end_date', $end_date);

        return redirect('/core-member-report');
    }

    public function resetFilterCoreMemberReport()
    {
        Session::forget('start_date');
        Session::forget('end_date');

        return redirect('/core-member-report');
    }

    public function getTotalTransaction($member_id)
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
        ->where('customer_id', $member_id)
        ->where('sales_invoice_date','>=',$start_date)
        ->where('sales_invoice_date','<=',$end_date)
        ->get();

        return count($data_sales);
    }

    public function getTotalItem($member_id)
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
        $data_sales = SalesInvoice::select('subtotal_item')
        ->where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->where('customer_id', $member_id)
        ->where('sales_invoice_date','>=',$start_date)
        ->where('sales_invoice_date','<=',$end_date)
        ->get();

        $total_item = 0;
        foreach($data_sales as $key=>$val) {
            $total_item += $val['subtotal_item'];
        }

        return $total_item;
    }

    public function getTotalAmount($member_id)
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
        $data_sales = SalesInvoice::select('total_amount')
        ->where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->where('customer_id', $member_id)
        ->where('sales_invoice_date','>=',$start_date)
        ->where('sales_invoice_date','<=',$end_date)
        ->get();;

        $total_amount = 0;
        foreach($data_sales as $key=>$val) {
            $total_amount += $val['total_amount'];
        }

        return $total_amount;
    }

    public function getTotalCredit($member_id)
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
        $data_sales = SalesInvoice::select('total_amount')
        ->where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->where('customer_id', $member_id)
        ->where('sales_payment_method', 2)
        ->where('sales_invoice_date','>=',$start_date)
        ->where('sales_invoice_date','<=',$end_date)
        ->get();;

        $total_amount = 0;
        foreach($data_sales as $key=>$val) {
            $total_amount += $val['total_amount'];
        }

        return $total_amount;
    }

    public function printCoreMemberReport()
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
        $data_member = CoreMember::select('member_name', 'member_id', 'division_name')
        ->where('data_state',0)
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
                <td><div style=\"text-align: center; font-size:14px; font-weight: bold\">LAPORAN PEMBELIAN ANGGOTA</div></td>
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
                <td width=\"35%\"><div style=\"text-align: center; font-weight: bold\">Nama Anggota</div></td>
                <td width=\"15%\"><div style=\"text-align: center; font-weight: bold\">Total Transaksi</div></td>
                <td width=\"15%\"><div style=\"text-align: center; font-weight: bold\">Total Barang</div></td>
                <td width=\"15%\"><div style=\"text-align: center; font-weight: bold\">Total Pembelian</div></td>
                <td width=\"15%\"><div style=\"text-align: center; font-weight: bold\">Total Piutang</div></td>

            </tr>
        
             ";

        $no = 1;
        $TotalTransaction = 0;
        $TotalItem = 0;
        $TotalAmount = 0;
        $TotalCredit = 0;
        $tblStock2 =" ";
        foreach ($data_member as $key => $val) {

            $tblStock2 .="
                <tr nobr=\"true\">			
                    <td style=\"text-align:center\">$no.</td>
                    <td style=\"text-align:left\">".$val['member_name']." - ".$val['division_name']."</td>
                    <td style=\"text-align:right\">".$this->getTotalTransaction($val['member_id'])."</td>
                    <td style=\"text-align:right\">".$this->getTotalItem($val['member_id'])."</td>
                    <td style=\"text-align:right\">".number_format($this->getTotalAmount($val['member_id']),2,'.',',')."</td>
                    <td style=\"text-align:right\">".number_format($this->getTotalCredit($val['member_id']),2,'.',',')."</td>
                </tr>
                
            ";
            $no++;
            $TotalTransaction += $this->getTotalTransaction($val['member_id']);
            $TotalItem += $this->getTotalItem($val['member_id']);
            $TotalAmount += $this->getTotalAmount($val['member_id']);
            $TotalCredit += $this->getTotalCredit($val['member_id']);
        }
        $tblStock3 = " 
        <tr nobr=\"true\">
            <td colspan=\"2\"><div style=\"text-align: center;  font-weight: bold\">TOTAL</div></td>
            <td style=\"text-align: right\"><div style=\"font-weight: bold\">". $TotalTransaction ."</div></td>
            <td style=\"text-align: right\"><div style=\"font-weight: bold\">". $TotalItem ."</div></td>
            <td style=\"text-align: right\"><div style=\"font-weight: bold\">". number_format($TotalAmount,2,'.',',') ."</div></td>
            <td style=\"text-align: right\"><div style=\"font-weight: bold\">". number_format($TotalCredit,2,'.',',') ."</div></td>
        </tr>
        </table>";

        $pdf::writeHTML($tblStock1.$tblStock2.$tblStock3, true, false, false, false, '');

        $filename = 'Laporan_Pembelian_Aanggota_'.$start_date.'s.d.'.$end_date.'.pdf';
        $pdf::Output($filename, 'I');
    }

    public function exportCoreMemberReport()
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
        $data_member = CoreMember::select('member_name', 'member_id', 'division_name')
        ->where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get();

        $spreadsheet = new Spreadsheet();

        if(count($data_member)>=0){
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
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(35);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(20);

    
            $spreadsheet->getActiveSheet()->mergeCells("B1:G1");
            $spreadsheet->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
            $spreadsheet->getActiveSheet()->getStyle('B3:G3')->getFont()->setBold(true);

            $spreadsheet->getActiveSheet()->getStyle('B3:G3')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->getStyle('B3:G3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('B1',"Laporan Pembelian Anggota Dari Periode ".date('d M Y', strtotime($start_date))." s.d. ".date('d M Y', strtotime($end_date)));	
            $sheet->setCellValue('B3',"No");
            $sheet->setCellValue('C3',"Nama Anggota");
            $sheet->setCellValue('D3',"Total Transaksi");
            $sheet->setCellValue('E3',"Total Barang");
            $sheet->setCellValue('F3',"Total Pembelian");
            $sheet->setCellValue('G3',"Total Piutang");
            
            $j=4;
            $no=0;
            $TotalTransaction = 0;
            $TotalItem = 0;
            $TotalAmount = 0;
            $TotalCredit = 0;
            foreach($data_member as $key=>$val){

                if(is_numeric($key)){
                    
                    $sheet = $spreadsheet->getActiveSheet(0);
                    $spreadsheet->getActiveSheet()->setTitle("Laporan Pembelian Anggota");
                    $spreadsheet->getActiveSheet()->getStyle('B'.$j.':G'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                    $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getNumberFormat()->setFormatCode('0.00');
                    $spreadsheet->getActiveSheet()->getStyle('G'.$j)->getNumberFormat()->setFormatCode('0.00');
            
                    $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $spreadsheet->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    $spreadsheet->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    $spreadsheet->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);


                        $no++;
                        $sheet->setCellValue('B'.$j, $no);
                        $sheet->setCellValue('C'.$j, $val['member_name']." - ".$val['division_name']);
                        $sheet->setCellValue('D'.$j, $this->getTotalTransaction($val['member_id']));
                        $sheet->setCellValue('E'.$j, $this->getTotalItem($val['member_id']));
                        $sheet->setCellValue('F'.$j, $this->getTotalAmount($val['member_id']));
                        $sheet->setCellValue('G'.$j, $this->getTotalCredit($val['member_id']));

                }else{
                    continue;
                }
                $j++;
                $TotalTransaction += $this->getTotalTransaction($val['member_id']);
                $TotalItem += $this->getTotalItem($val['member_id']);
                $TotalAmount += $this->getTotalAmount($val['member_id']);
                $TotalCredit += $this->getTotalCredit($val['member_id']);
        
            }
            $spreadsheet->getActiveSheet()->mergeCells('B'.$j.':C'.$j);
            $spreadsheet->getActiveSheet()->getStyle('B'.$j.':G'.$j)->getFont()->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getNumberFormat()->setFormatCode('0.00');
            $spreadsheet->getActiveSheet()->getStyle('G'.$j)->getNumberFormat()->setFormatCode('0.00');
            $spreadsheet->getActiveSheet()->getStyle('B'.$j.':G'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $spreadsheet->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $spreadsheet->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('B'.$j, 'TOTAL');
            $sheet->setCellValue('D'.$j, $TotalTransaction);
            $sheet->setCellValue('E'.$j, $TotalItem);
            $sheet->setCellValue('F'.$j, $TotalAmount);
            $sheet->setCellValue('G'.$j, $TotalCredit);

            $j++;
            $spreadsheet->getActiveSheet()->mergeCells('B'.$j.':G'.$j);
            $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('B'.$j, Auth::user()->name.", ".date('d-m-Y H:i'));
            
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

    public function openingBalenceCoreMember($member_id)
    {
        if(!$start_date = Session::get('start_date')){
            $start_date = date('Y-m-d');
        } else {
            $start_date = Session::get('start_date');
        }

        $sales_invoice = SalesInvoice::where('customer_id', $member_id)
        ->where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->where('sales_invoice_date','<',$start_date)
        ->where('sales_payment_method', 2)
        ->where('paid_amount',0)
        ->get();

        $opening = 0;
        foreach ($sales_invoice as $key => $val) {
            $opening += $val['total_amount'];
        }

        return $opening;
    }

    public function printCardCoreMemberReport($member_id)
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
        $data_member = CoreMember::where('member_id', $member_id)
        ->where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->first();

        $sales_invoice = SalesInvoice::where('customer_id', $member_id)
        ->where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->where('sales_payment_method', 2)
        ->where('sales_invoice_date','>=',$start_date)
        ->where('sales_invoice_date','<=',$end_date)
        ->where('paid_amount',0)
        ->get();

        $pdf = new TCPDF('P', PDF_UNIT, 'F4', true, 'UTF-8', false);

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
                <td><div style=\"text-align: center; font-size:14px; font-weight: bold\">KARTU PIUTANG</div></td>
            </tr>
        </table>
        ";
        $pdf::writeHTML($tbl, true, false, false, false, '');
        
        $tbl1 = "
        <table width=\"100%\" cellspacing=\"0\" cellpadding=\"2\" border=\"0\">
            <tr>
                <td width=\"13%\">Anggota</td>
                <td width=\"2%\">:</td>
                <td width=\"85%\">[".$data_member['member_no']."] ".$data_member['member_name']."</td>
            </tr>
            <tr>
                <td width=\"13%\">Periode</td>
                <td width=\"2%\">:</td>
                <td width=\"85%\">".date('d-m-Y',strtotime($start_date))." s/d ".date('d-m-Y',strtotime($end_date))."</td>
            </tr>
        ";

        $tbl2 = "
        </table>
        <div></div>
        <table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"1\">
            <div style=\"border-collapse:collapse;\">
                <tr style=\"line-height: 0%;\">
                    <td width=\"5%\"><div style=\"text-align: center; font-weight: bold;\">No</div></td>
                    <td width=\"10%\"><div style=\"text-align: center; font-weight: bold\">Tanggal</div></td>
                    <td width=\"15%\"><div style=\"text-align: center; font-weight: bold\">Nomor</div></td>
                    <td width=\"25%\"><div style=\"text-align: center; font-weight: bold\">Keterangan</div></td>
                    <td width=\"15%\"><div style=\"text-align: center; font-weight: bold\">Debit</div></td>
                    <td width=\"15%\"><div style=\"text-align: center; font-weight: bold\">Kredit</div></td>
                    <td width=\"15%\"><div style=\"text-align: center; font-weight: bold\">Saldo</div></td>
                </tr>
            </div>
        </table>    
        <table width=\"100%\" cellpadding=\"1\" border=\"0\">
        ";
        
        $no = 0;
        $tbl3 = "
        <tr>
            <td width=\"5%\"><div style=\"text-align: center;\"></div></td>
            <td width=\"45%\"><div style=\"text-align: left; font-weight: bold;\">Saldo Awal...</div></td>
            <td width=\"50%\"><div style=\"text-align: right;\">".number_format($this->openingBalenceCoreMember($member_id),2,'.',',')."</div></td>
        </tr>
        ";
        $last_balence = $this->openingBalenceCoreMember($member_id);
        $total_amount = 0;
        foreach ($sales_invoice as $val) {
            $no++;
            $last_balence += $val['total_amount'];
            $tbl3 .= "
            <tr>
                <td width=\"5%\"><div style=\"text-align: center;\">".$no.".</div></td>
                <td width=\"10%\"><div style=\"text-align: left;\">".date('d-m-Y', strtotime($val['sales_invoice_date']))."</div></td>
                <td width=\"15%\"><div style=\"text-align: left;\">".$val['sales_invoice_no']."</div></td>
                <td width=\"25%\"><div style=\"text-align: left;\">Tagihan : ".$val['sales_invoice_no']."</div></td>
                <td width=\"15%\"><div style=\"text-align: right;\">".number_format($val['total_amount'],2,'.',',')."</div></td>
                <td width=\"15%\"><div style=\"text-align: right;\">".number_format(0,2,'.',',')."</div></td>
                <td width=\"15%\"><div style=\"text-align: right;\">".number_format($last_balence,2,'.',',')."</div></td>
            </tr>
            ";
            $total_amount += $val['total_amount'];
        }
        
        $tbl4 = "
        </table>
        <table width=\"100%\" cellpadding=\"1\" border=\"0\">
            <tr>
                <td width=\"30%\"><div style=\"text-align: left; border-top: 1px solid black; font-weight: bold;\">Jumlah Mutasi</div></td>
                <td width=\"25%\"><div style=\"text-align: left; border-top: 1px solid black;\">:</div></td>
                <td width=\"15%\"><div style=\"text-align: right; border-top: 1px solid black;\">".number_format($total_amount,2,'.',',')."</div></td>
                <td width=\"15%\"><div style=\"text-align: right; border-top: 1px solid black;\">".number_format(0,2,'.',',')."</div></td>
                <td width=\"15%\"><div style=\"text-align: right; border-top: 1px solid black;\"></div></td>
            </tr>
        </table>
        ";

        $pdf::writeHTML($tbl1.$tbl2.$tbl3.$tbl4, true, false, false, false, '');


        $filename = 'Kartu Piutang.pdf';
        $pdf::Output($filename, 'I');
    }
}
