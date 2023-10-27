<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CoreMember;
use App\Models\PreferenceVoucher;
use App\Models\SalesInvoice;
use Elibyy\TCPDF\Facades\TCPDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class PreferenceVoucherReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        
    }

    public function index()
    {
        if(!Session::get('voucher_id')){
            $voucher_id     = '';
        }else{
            $voucher_id = Session::get('voucher_id');
        }
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

        $listVoucher = PreferenceVoucher::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('voucher_code','voucher_id');

        if ($voucher_id == '') {
            $data = SalesInvoice::where('data_state', 0)
            ->where('company_id',Auth::user()->company_id)
            ->where('sales_invoice_date','>=',$start_date)
            ->where('sales_invoice_date','<=',$end_date)
            ->where('voucher_id', '!=', null)
            ->get();
        } else {
            $data = SalesInvoice::where('data_state', 0)
            ->where('company_id',Auth::user()->company_id)
            ->where('sales_invoice_date','>=',$start_date)
            ->where('sales_invoice_date','<=',$end_date)
            ->where('voucher_id', $voucher_id)
            ->get();
        }
        return view('content.PreferenceVoucherReport.ListPreferenceVoucherReport', compact('listVoucher','voucher_id', 'start_date','end_date','data'));
    }

    public function filterVoucherReport(Request $request)
    {
        $voucher_id = $request->voucher_id;
        $start_date     = $request->start_date;
        $end_date       = $request->end_date;
        
        Session::put('start_date', $start_date);
        Session::put('end_date', $end_date);
        Session::put('voucher_id', $voucher_id);
    
        return redirect('/preference-voucher-report');
    }

    public function resetFilterVoucherReport()
    {
        Session::forget('start_date');
        Session::forget('end_date');
        Session::forget('voucher_id');
    
        return redirect('/preference-voucher-report');
    }

    public function getVoucherCode($voucher_id)
    {
        $data = PreferenceVoucher::where('voucher_id', $voucher_id)
        ->first();

        return $data['voucher_code'];
    }

    public function getMemberName($member_id)
    {
        $data = CoreMember::where('member_id',$member_id)
        ->first();

        return $data['member_name'];
    }

    public function getDivisionName($member_id)
    {
        $data = CoreMember::where('member_id',$member_id)
        ->first();

        return $data['division_name'];
    }

    public function printVoucherReport()
    {
        if(!Session::get('voucher_id')){
            $voucher_id     = '';
        }else{
            $voucher_id = Session::get('voucher_id');
        }
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

        if ($voucher_id == '') {
            $data = SalesInvoice::where('data_state', 0)
            ->where('company_id',Auth::user()->company_id)
            ->where('sales_invoice_date','>=',$start_date)
            ->where('sales_invoice_date','<=',$end_date)
            ->where('voucher_id', '!=', null)
            ->get();
        } else {
            $data = SalesInvoice::where('data_state', 0)
            ->where('company_id',Auth::user()->company_id)
            ->where('sales_invoice_date','>=',$start_date)
            ->where('sales_invoice_date','<=',$end_date)
            ->where('voucher_id', $voucher_id)
            ->get();
        }

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
                <td><div style=\"text-align: center; font-size:14px; font-weight: bold\">LAPORAN VOUCHER</div></td>
            </tr>
            <tr>
                <td><div style=\"text-align: center; font-size:12px\">PERIODE : ".date('d M Y', strtotime($start_date))." s.d. ".date('d M Y', strtotime($end_date))."</div></td>
            </tr>
        </table>
        
        ";
        $pdf::writeHTML($tbl, true, false, false, false, '');
        
        $tblStock1 = "
        <table cellspacing=\"0\" cellpadding=\"1\" border=\"1\" width=\"100%\">
            <tr>
                <th width=\"5%\" ><div style=\"text-align: center; font-weight: bold\">No</div></th>
                <th width=\"20%\" ><div style=\"text-align: center; font-weight: bold\">Kode Voucher</div></th>
                <th width=\"20%\" ><div style=\"text-align: center; font-weight: bold\">No. Voucher</div></th>
                <th width=\"15%\" ><div style=\"text-align: center; font-weight: bold\">Tanggal</div></th>
                <th width=\"20%\" ><div style=\"text-align: center; font-weight: bold\">Nama Anggota</div></th>
                <th width=\"20%\" ><div style=\"text-align: center; font-weight: bold\">Jabatan</div></th>
            </tr>
        ";

        $no = 1;
        $tblStock2 = "";
        foreach ($data as $key => $val) {
            $tblStock2 .="
                <tr nobr=\"true\">			
                    <td style=\"text-align:center\">$no.</td>
                    <td>".$this->getVoucherCode($val['voucher_id'])."</td>
                    <td>".$val['voucher_no']."</td>
                    <td>".date('d-m-Y', strtotime($val['sales_invoice_date']))."</td>
                    <td>".$this->getMemberName($val['customer_id'])."</td>
                    <td>".$this->getDivisionName($val['customer_id'])."</td>
                    
                </tr>
                
            ";
            $no++;
        }

        $tblStock3 = "
        </table>
        ";

        $pdf::writeHTML($tblStock1.$tblStock2.$tblStock3, true, false, false, false, '');

        $filename = 'Laporan_Voucher.pdf';
        $pdf::Output($filename, 'I');
    }

    public function exportVoucherReport()
    {
        if(!Session::get('voucher_id')){
            $voucher_id     = '';
        }else{
            $voucher_id = Session::get('voucher_id');
        }
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

        if ($voucher_id == '') {
            $data = SalesInvoice::where('data_state', 0)
            ->where('company_id',Auth::user()->company_id)
            ->where('sales_invoice_date','>=',$start_date)
            ->where('sales_invoice_date','<=',$end_date)
            ->where('voucher_id', '!=', null)
            ->get();
        } else {
            $data = SalesInvoice::where('data_state', 0)
            ->where('company_id',Auth::user()->company_id)
            ->where('sales_invoice_date','>=',$start_date)
            ->where('sales_invoice_date','<=',$end_date)
            ->where('voucher_id', $voucher_id)
            ->get();
        }

        $spreadsheet = new Spreadsheet();

        if(count($data)>=0){
            $spreadsheet->getProperties()->setCreator("IBS CJDW")
                                        ->setLastModifiedBy("IBS CJDW")
                                        ->setTitle("Voucher Report")
                                        ->setSubject("")
                                        ->setDescription("Voucher Report")
                                        ->setKeywords("Voucher, Report")
                                        ->setCategory("Voucher Report");
                                 
            $sheet = $spreadsheet->getActiveSheet(0);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(5);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(20);
    
            $spreadsheet->getActiveSheet()->mergeCells("B1:G1");
            $spreadsheet->getActiveSheet()->mergeCells("B2:G2");
            $spreadsheet->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('B2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
            $spreadsheet->getActiveSheet()->getStyle('B4:G4')->getFont()->setBold(true);

            $spreadsheet->getActiveSheet()->getStyle('B4:G4')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->getStyle('B4:G4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('B1',"Laporan Voucher".date('d M Y', strtotime($start_date))." s.d. ".date('d M Y', strtotime($end_date)));	
            $sheet->setCellValue('B2',date('d M Y', strtotime($start_date))." s.d. ".date('d M Y', strtotime($end_date)));	
            $sheet->setCellValue('B4',"No");
            $sheet->setCellValue('C4',"Kode Voucher");
            $sheet->setCellValue('D4',"No. Voucher");
            $sheet->setCellValue('E4',"Tanggal");
            $sheet->setCellValue('F4',"Nama Anggota");
            $sheet->setCellValue('G4',"Jabatan");
            
            $j=5;
            $no=0;

            foreach($data as $key=>$val){

                if(is_numeric($key)){
                    
                    $sheet = $spreadsheet->getActiveSheet(0);
                    $spreadsheet->getActiveSheet()->setTitle("Laporan Voucher");
                    $spreadsheet->getActiveSheet()->getStyle('B'.$j.':G'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            
                    $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $spreadsheet->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

                    $no++;
                    $sheet->setCellValue('B'.$j, $no);
                    $sheet->setCellValue('C'.$j, $this->getVoucherCode($val['voucher_id']));
                    $sheet->setCellValue('D'.$j, $val['voucher_no']);
                    $sheet->setCellValue('E'.$j, date('d-m-Y', strtotime($val['sales_invoice_date'])));
                    $sheet->setCellValue('F'.$j, $this->getMemberName($val['customer_id']));
                    $sheet->setCellValue('G'.$j, $this->getDivisionName($val['customer_id']));
                }
                $j++;
        
            }
            $spreadsheet->getActiveSheet()->mergeCells('B'.$j.':G'.$j);
            $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('B'.$j, Auth::user()->name.", ".date('d-m-Y H:i'));


            $filename='Laporan_Voucher.xls';
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
