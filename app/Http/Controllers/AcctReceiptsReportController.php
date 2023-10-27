<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\JournalVoucher;
use App\Models\SalesInvoice;
use Elibyy\TCPDF\Facades\TCPDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class AcctReceiptsReportController extends Controller
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

        // $data = JournalVoucher::join('acct_journal_voucher_item','acct_journal_voucher.journal_voucher_id','=','acct_journal_voucher_item.journal_voucher_id')
        // ->where('acct_journal_voucher_item.account_id',9)
        // ->where('acct_journal_voucher.company_id', Auth::user()->company_id)
        // ->where('acct_journal_voucher.journal_voucher_date','>=',$start_date)
        // ->where('acct_journal_voucher.journal_voucher_date','<=',$end_date)
        // ->get();
        $data = SalesInvoice::select('sales_invoice.sales_invoice_date','sales_invoice.total_amount')
        ->where('sales_invoice.data_state',0)
        ->where('sales_invoice.sales_invoice_date','>=', $start_date)
        ->where('sales_invoice.sales_invoice_date','<=', $end_date)
        ->where('sales_invoice.company_id', Auth::user()->company_id)
        ->get();
        // dd($data);
        return view('content.AcctReceiptsReport.ListAcctReceiptsReport', compact('data','start_date','end_date'));
    }

    public function filterAcctReceiptsReport(Request $request)
    {
        $start_date = $request->start_date;
        $end_date   = $request->end_date;

        Session::put('start_date', $start_date);
        Session::put('end_date', $end_date);

        return redirect('/cash-receipts-report');
    }

    public function resetFilterAcctReceiptsReport()
    {
        Session::forget('start_date');
        Session::forget('end_date');

        return redirect('/cash-receipts-report');
    }

    public function printAcctReceiptsReport()
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

        // $data = JournalVoucher::join('acct_journal_voucher_item','acct_journal_voucher.journal_voucher_id','=','acct_journal_voucher_item.journal_voucher_id')
        // ->where('acct_journal_voucher_item.account_id',9)
        // ->where('acct_journal_voucher.company_id', Auth::user()->company_id)
        // ->where('acct_journal_voucher.journal_voucher_date','>=',$start_date)
        // ->where('acct_journal_voucher.journal_voucher_date','<=',$end_date)
        // ->get();
        $data = SalesInvoice::select('sales_invoice.sales_invoice_date','sales_invoice.total_amount')
        ->where('sales_invoice.data_state',0)
        ->where('sales_invoice.sales_invoice_date','>=', $start_date)
        ->where('sales_invoice.sales_invoice_date','<=', $end_date)
        ->where('sales_invoice.company_id', Auth::user()->company_id)
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
                <td><div style=\"text-align: center; font-size:14px; font-weight: bold\">LAPORAN PENERIMAAN KAS</div></td>
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
        $totalamount = 0;
        $tblStock2 =" ";
        foreach ($data as $key => $val) {
            $tblStock2 .="
                <tr>			
                    <td style=\"text-align:center\">$no.</td>
                    <td style=\"text-align:left\">Penjualan Produk</td>
                    <td style=\"text-align:left\">".date('d-m-Y', strtotime($val['sales_invoice_date']))."</td>
                    <td style=\"text-align:right\">".number_format($val['total_amount'],2,'.',',')."</td>
                </tr>
                
            ";
            $totalamount += $val['total_amount'];
            $no++;
        }
        $tblStock3 = " 
        <tr>
            <td colspan=\"3\"><div style=\"text-align: center;  font-weight: bold\">TOTAL</div></td>
            <td style=\"text-align: right\"><div style=\"font-weight: bold\">". number_format($totalamount,2,'.',',') ."</div></td>
        </tr>
        </table>
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"0\">
            <tr>
                <td style=\"text-align:right\">".Auth::user()->name.", ".date('d-m-Y H:i')."</td>
            </tr>
        </table>";

        $pdf::writeHTML($tblStock1.$tblStock2.$tblStock3, true, false, false, false, '');


        $filename = 'Laporan_Penerimaan_kas_'.$start_date.'s.d.'.$end_date.'.pdf';
        $pdf::Output($filename, 'I');
    }

    public function exportAcctReceiptsReport()
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

        // $data = JournalVoucher::join('acct_journal_voucher_item','acct_journal_voucher.journal_voucher_id','=','acct_journal_voucher_item.journal_voucher_id')
        // ->where('acct_journal_voucher_item.account_id',9)
        // ->where('acct_journal_voucher.company_id', Auth::user()->company_id)
        // ->where('acct_journal_voucher.journal_voucher_date','>=',$start_date)
        // ->where('acct_journal_voucher.journal_voucher_date','<=',$end_date)
        // ->get();
        $data = SalesInvoice::select('sales_invoice.sales_invoice_date','sales_invoice.total_amount')
        ->where('sales_invoice.data_state',0)
        ->where('sales_invoice.sales_invoice_date','>=', $start_date)
        ->where('sales_invoice.sales_invoice_date','<=', $end_date)
        ->where('sales_invoice.company_id', Auth::user()->company_id)
        ->get();

        $spreadsheet = new Spreadsheet();

        if(count($data)>=0){
            $spreadsheet->getProperties()->setCreator("MOZAIC")
                                        ->setLastModifiedBy("MOZAIC")
                                        ->setTitle("Cash Receipts Report")
                                        ->setSubject("")
                                        ->setDescription("Cash Receipts Report")
                                        ->setKeywords("Cash, Receipts, Report")
                                        ->setCategory("Cash Receipts Report");
                                 
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

            $sheet->setCellValue('B1',"Laporan Penerimaan Kas Dari Periode ".date('d M Y', strtotime($start_date))." s.d. ".date('d M Y', strtotime($end_date)));	
            $sheet->setCellValue('B3',"No");
            $sheet->setCellValue('C3',"Keterangan");
            $sheet->setCellValue('D3',"Tanggal");
            $sheet->setCellValue('E3',"Nominal"); 
            
            $j=4;
            $no=0;
            $totalamount = 0;
            
            foreach($data as $key=>$val){

                $sheet = $spreadsheet->getActiveSheet(0);
                $spreadsheet->getActiveSheet()->setTitle("Laporan Penerimaan Kas");
                $spreadsheet->getActiveSheet()->getStyle('B'.$j.':E'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                $spreadsheet->getActiveSheet()->getStyle('E'.$j)->getNumberFormat()->setFormatCode('0.00');
        
                $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $spreadsheet->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $spreadsheet->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $spreadsheet->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);



                $no++;
                $sheet->setCellValue('B'.$j, $no);
                $sheet->setCellValue('C'.$j, 'Penjualan Produk');
                $sheet->setCellValue('D'.$j, date('d-m-Y', strtotime($val['sales_invoice_date'])));
                $sheet->setCellValue('E'.$j, $val['total_amount']);

                $j++;
                $totalamount += $val['total_amount'];
        
            }
            $spreadsheet->getActiveSheet()->mergeCells('B'.$j.':D'.$j);
            $spreadsheet->getActiveSheet()->getStyle('B'.$j.':E'.$j)->getFont()->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle('B'.$j.':E'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $spreadsheet->getActiveSheet()->getStyle('E'.$j)->getNumberFormat()->setFormatCode('0.00');
            $sheet->setCellValue('B'.$j, 'TOTAL');
            $sheet->setCellValue('E'.$j, $totalamount);
            $j++;
            $spreadsheet->getActiveSheet()->mergeCells('B'.$j.':E'.$j);
            $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('B'.$j, Auth::user()->name.", ".date('d-m-Y H:i'));
            
            $filename='Laporan_Penerimaan_Kas_'.$start_date.'_s.d._'.$end_date.'.xls';
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
