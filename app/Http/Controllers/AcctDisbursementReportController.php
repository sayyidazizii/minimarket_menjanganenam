<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Expenditure;
use App\Models\JournalVoucher;
use Elibyy\TCPDF\Facades\TCPDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class AcctDisbursementReportController extends Controller
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

        $data = Expenditure::select('expenditure_remark','expenditure_date','expenditure_amount')
        ->where('data_state', 0)
        ->where('company_id', Auth::user()->company_id)
        ->where('expenditure_date','>=',$start_date)
        ->where('expenditure_date','<=',$end_date)
        ->get();
        
        return view('content.AcctDisbursementReport.ListAcctDisbursementReport', compact('data','start_date','end_date'));
    }

    public function filterDisbursementReport(Request $request)
    {
        $start_date = $request->start_date;
        $end_date   = $request->end_date;

        Session::put('start_date', $start_date);
        Session::put('end_date', $end_date);

        return redirect('/cash-disbursement-report');
    }

    public function resetFilterDisbursementReport()
    {
        Session::forget('start_date');
        Session::forget('end_date');

        return redirect('/cash-disbursement-report');
    }

    public function printDisbursementReport()
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

        $data = Expenditure::select('expenditure_remark','expenditure_date','expenditure_amount')
        ->where('data_state', 0)
        ->where('company_id', Auth::user()->company_id)
        ->where('expenditure_date','>=',$start_date)
        ->where('expenditure_date','<=',$end_date)
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
                <td><div style=\"text-align: center; font-size:14px; font-weight: bold\">LAPORAN PENGELUARAN KAS</div></td>
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
                <td width=\"7%\" ><div style=\"text-align: center; font-weight: bold\">No</div></td>
                <td width=\"31%\" ><div style=\"text-align: center; font-weight: bold\">Keterangan</div></td>
                <td width=\"31%\" ><div style=\"text-align: center; font-weight: bold\">Tanggal</div></td>
                <td width=\"31%\" ><div style=\"text-align: center; font-weight: bold\">Nominal</div></td>
            </tr>
        
             ";

        $no = 1;
        $expenditureamount = 0;
        $tblStock2 =" ";
        foreach ($data as $key => $val) {
            $tblStock2 .="
                <tr>			
                    <td style=\"text-align:center\">$no.</td>
                    <td style=\"text-align:left\">".$val['expenditure_remark']."</td>
                    <td style=\"text-align:left\">".date('d-m-Y', strtotime($val['expenditure_date']))."</td>
                    <td style=\"text-align:right\">".number_format($val['expenditure_amount'],2,'.',',')."</td>
                </tr>
                
            ";
            $expenditureamount += $val['expenditure_amount'];
            $no++;
        }
        $tblStock3 = " 
        <tr>
            <td colspan=\"3\"><div style=\"text-align: center;  font-weight: bold\">TOTAL</div></td>
            <td style=\"text-align: right\"><div style=\"font-weight: bold\">". number_format($expenditureamount,2,'.',',') ."</div></td>
        </tr>
        </table>
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"0\">
            <tr>
                <td style=\"text-align:right\">".Auth::user()->name.", ".date('d-m-Y H:i')."</td>
            </tr>
        </table>";

        $pdf::writeHTML($tblStock1.$tblStock2.$tblStock3, true, false, false, false, '');

        $filename = 'Laporan_Pengeluaran_kas_'.$start_date.'s.d.'.$end_date.'.pdf';
        $pdf::Output($filename, 'I');
    }

    public function exportDisbursementReport()
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

        $data = Expenditure::select('expenditure_remark','expenditure_date','expenditure_amount')
        ->where('data_state', 0)
        ->where('company_id', Auth::user()->company_id)
        ->where('expenditure_date','>=',$start_date)
        ->where('expenditure_date','<=',$end_date)
        ->get();

        $spreadsheet = new Spreadsheet();

        if(count($data)>=0){
            $spreadsheet->getProperties()->setCreator("MOZAIC")
                                        ->setLastModifiedBy("MOZAIC")
                                        ->setTitle("Cash Disbursement Report")
                                        ->setSubject("")
                                        ->setDescription("Cash Disbursement Report")
                                        ->setKeywords("Cash, Disbursement, Report")
                                        ->setCategory("Cash Disbursement Report");
                                 
            $sheet = $spreadsheet->getActiveSheet(0);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(5);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(40);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(20);
    
            $spreadsheet->getActiveSheet()->mergeCells("B1:E1");
            $spreadsheet->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
            $spreadsheet->getActiveSheet()->getStyle('B3:E3')->getFont()->setBold(true);

            $spreadsheet->getActiveSheet()->getStyle('B3:E3')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->getStyle('B3:E3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('B1',"Laporan Pengeluaran Kas Dari Periode ".date('d M Y', strtotime($start_date))." s.d. ".date('d M Y', strtotime($end_date)));	
            $sheet->setCellValue('B3',"No");
            $sheet->setCellValue('C3',"Keterangan");
            $sheet->setCellValue('D3',"Tanggal");
            $sheet->setCellValue('E3',"Nominal"); 
            
            $j=4;
            $no=0;
            $total_expenditure_amount = 0;

            foreach($data as $key=>$val){
                $sheet = $spreadsheet->getActiveSheet(0);
                $spreadsheet->getActiveSheet()->setTitle("Laporan Pengeluaran Kas");
                $spreadsheet->getActiveSheet()->getStyle('B'.$j.':E'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                $spreadsheet->getActiveSheet()->getStyle('E'.$j)->getNumberFormat()->setFormatCode('0.00');
        
                $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $spreadsheet->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $spreadsheet->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $spreadsheet->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                $no++;
                $sheet->setCellValue('B'.$j, $no);
                $sheet->setCellValue('C'.$j, $val['expenditure_remark']);
                $sheet->setCellValue('D'.$j, date('d-m-Y', strtotime($val['expenditure_date'])));
                $sheet->setCellValue('E'.$j, $val['expenditure_amount']);

                $j++;
                $total_expenditure_amount += $val['expenditure_amount'];
            }

            $spreadsheet->getActiveSheet()->mergeCells('B'.$j.':D'.$j);
            $spreadsheet->getActiveSheet()->getStyle('B'.$j.':E'.$j)->getFont()->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $spreadsheet->getActiveSheet()->getStyle('B'.$j.':E'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->getStyle('E'.$j)->getNumberFormat()->setFormatCode('0.00');

            $sheet->setCellValue('B'.$j, 'TOTAL');
            $sheet->setCellValue('E'.$j, $total_expenditure_amount);
            $j++;
            $spreadsheet->getActiveSheet()->mergeCells('B'.$j.':E'.$j);
            $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('B'.$j, Auth::user()->name.", ".date('d-m-Y H:i'));
            
            $filename='Laporan_Pengeluaran_Kas_'.$start_date.'_s.d._'.$end_date.'.xls';
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
