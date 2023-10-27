<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AcctAccount;
use App\Models\AcctAccountBalance;
use App\Models\AcctAccountBalanceDetail;
use App\Models\JournalVoucher;
use Elibyy\TCPDF\Facades\TCPDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class AcctLedgerReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        
    }

    public function index()
    {
        if(!$start_month = Session::get('start_month')){
            $start_month = date('m');
        }else{
            $start_month = Session::get('start_month');
        }
        if(!$end_month = Session::get('end_month')){
            $end_month = date('m');
        }else{
            $end_month = Session::get('end_month');
        }
        if(!$year = Session::get('year')){
            $year = date('Y');
        }else{
            $year = Session::get('year');
        }
        if(!$account_id = Session::get('account_id')){
            $account_id = '';
        }else{
            $account_id = Session::get('account_id');
        }
        $monthlist = array(
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        );

        $year_now 	=	date('Y');
        for($i=($year_now-2); $i<($year_now+2); $i++){
            $yearlist[$i] = $i;
        } 
        $accountlist = AcctAccount::select(DB::raw("CONCAT(account_code,' - ',account_name) AS full_account"),'account_id')
        ->where('data_state',0)
        ->where('company_id',Auth::user()->company_id)
        ->get()
        ->pluck('full_account','account_id');

        $account = AcctAccount::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->where('account_id', $account_id)
        ->first();
        
        $accountbalancedetail = AcctAccountBalanceDetail::join('acct_account', 'acct_account.account_id','=','acct_account_balance_detail.account_id')
        ->select('acct_account_balance_detail.transaction_id','acct_account_balance_detail.last_balance','acct_account_balance_detail.account_in','acct_account_balance_detail.account_out','acct_account_balance_detail.transaction_date','acct_account_balance_detail.account_id')
        ->where('acct_account_balance_detail.account_id' ,$account_id)
        ->whereMonth('acct_account_balance_detail.transaction_date','>=',$start_month)
        ->whereMonth('acct_account_balance_detail.transaction_date','<=',$end_month)
        ->whereYear('acct_account_balance_detail.transaction_date',$year)
        ->where('acct_account_balance_detail.company_id', Auth::user()->company_id)
        ->orderBy('acct_account_balance_detail.transaction_date', 'ASC')
        ->orderBy('acct_account_balance_detail.account_balance_detail_id', 'ASC')
        ->get();
        $accountbalancedetail_old = AcctAccountBalanceDetail::join('acct_account', 'acct_account.account_id','=','acct_account_balance_detail.account_id')
        ->select('acct_account_balance_detail.last_balance')
        ->where('acct_account_balance_detail.account_id' ,$account_id)
        ->whereMonth('acct_account_balance_detail.transaction_date',$start_month-1)
        ->whereYear('acct_account_balance_detail.transaction_date',$year)
        ->where('acct_account_balance_detail.company_id', Auth::user()->company_id)
        ->orderBy('acct_account_balance_detail.transaction_date', 'DESC')
        ->orderBy('acct_account_balance_detail.account_balance_detail_id', 'DESC')
        ->first();

        $acctgeneralledgerreport = array();
        foreach($accountbalancedetail as $val){
            $description = JournalVoucher::where('journal_voucher_id', $val['transaction_id'])->first('journal_voucher_description');
            $no_journal = JournalVoucher::where('journal_voucher_id', $val['transaction_id'])->first('journal_voucher_no');
            $data_state = JournalVoucher::where('journal_voucher_id', $val['transaction_id'])->first('data_state');
            
            if($account['account_default_status'] == 0 || $val['last_balance'] >= 0 ){
                    $debit 	= $val['account_in'];
                    $credit = $val['account_out'];

                    if($val['last_balance'] >= 0){
                        $last_balance_debit 	= $val['last_balance'];
                        $last_balance_credit 	= 0;
                    } else {
                        $last_balance_debit 	= 0;
                        $last_balance_credit 	= $val['last_balance'];
                    }
                } else {
                    $debit 	= $val['account_out'];
                    $credit = $val['account_in'];

                    if($val['last_balance'] >= 0){
                        $last_balance_debit 	= 0;
                        $last_balance_credit 	= $val['last_balance'];
                    } else {
                        
                        $last_balance_debit 	= $val['last_balance'];
                        $last_balance_credit 	= 0;
                    }
                }
        
            $acctgeneralledgerreport_detail = array(
                'date' => $val['transaction_date'],
                'no_journal' => $no_journal['journal_voucher_no'],
                'description' => $description['journal_voucher_description'],
                'account_id' => $val['account_id'],
                'account_in' => $debit,
                'account_out' => $credit,
                'last_balance_debit' => $last_balance_debit,
                'last_balance_credit' => $last_balance_credit,
                'data_state' => $data_state['data_state']
            );
            array_push($acctgeneralledgerreport, $acctgeneralledgerreport_detail);
        }
  
        return view('content.AcctLedgerReport.ListLedgerReport', compact('monthlist','yearlist','accountlist', 'acctgeneralledgerreport','accountbalancedetail_old','account','year','start_month','end_month','account_id'));
    }

    public function filterLedgerReport(Request $request)
    {
        $start_month = $request->start_month;
        $end_month   = $request->end_month;
        $year        = $request->year;
        $account_id  = $request->account_id;

        Session::put('start_month',$start_month);
        Session::put('end_month',$end_month);
        Session::put('year',$year);
        Session::put('account_id',$account_id);

        return redirect('/ledger-report');
    }

    public function resetFilterLedgerReport()
    {
        Session::put('start_month');
        Session::put('end_month');
        Session::put('year');
        Session::put('account_id');

        return redirect('/ledger-report');
    }

    public function getAccountName($account_id)
    {
        $data = AcctAccount::select(DB::raw("CONCAT(account_code,' - ',account_name) AS full_account"),'account_id')
        ->where('account_id', $account_id)
        ->first();

        return $data['full_account'];
    }

    public function getAccountStatus($account_id)
    {
        $data = AcctAccount::select('account_default_status')
        ->where('account_id', $account_id)
        ->first();
        $account_status = array(
            '0' => 'Debit',
            '1' => 'Kredit',
            '3' => ''
        );
        if(isset($data)){
            return $account_status[$data['account_default_status']];
        } else {
            return $account_status[3];
        }
    }

    public function printLedgerReport()
    {
        if(!$start_month = Session::get('start_month')){
            $start_month = date('m');
        }else{
            $start_month = Session::get('start_month');
        }
        if(!$end_month = Session::get('end_month')){
            $end_month = date('m');
        }else{
            $end_month = Session::get('end_month');
        }
        if(!$year = Session::get('year')){
            $year = date('Y');
        }else{
            $year = Session::get('year');
        }
        if(!$account_id = Session::get('account_id')){
            $account_id = '';
        }else{
            $account_id = Session::get('account_id');
        }
        $monthlist = array(
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        );

        $year_now 	=	date('Y');
        for($i=($year_now-2); $i<($year_now+2); $i++){
            $yearlist[$i] = $i;
        } 
        $accountlist = AcctAccount::where('data_state',0)->where('company_id',Auth::user()->company_id)->get()->pluck('account_name','account_id');
        $account = AcctAccount::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->where('account_id', $account_id)
        ->first();
        
        $accountbalancedetail = AcctAccountBalanceDetail::join('acct_account', 'acct_account.account_id','=','acct_account_balance_detail.account_id')
        ->select('acct_account_balance_detail.transaction_id','acct_account_balance_detail.last_balance','acct_account_balance_detail.account_in','acct_account_balance_detail.account_out','acct_account_balance_detail.transaction_date','acct_account_balance_detail.account_id')
        ->where('acct_account_balance_detail.account_id' ,$account_id)
        ->whereMonth('acct_account_balance_detail.transaction_date','>=',$start_month)
        ->whereMonth('acct_account_balance_detail.transaction_date','<=',$end_month)
        ->whereYear('acct_account_balance_detail.transaction_date','<=',$year)
        ->where('acct_account_balance_detail.company_id', Auth::user()->company_id)
        ->orderBy('acct_account_balance_detail.transaction_date', 'ASC')
        ->orderBy('acct_account_balance_detail.account_balance_detail_id', 'ASC')
        ->get();
        $accountbalancedetail_old = AcctAccountBalanceDetail::join('acct_account', 'acct_account.account_id','=','acct_account_balance_detail.account_id')
        ->select('acct_account_balance_detail.last_balance')
        ->where('acct_account_balance_detail.account_id' ,$account_id)
        ->whereMonth('acct_account_balance_detail.transaction_date',$start_month-1)
        ->whereYear('acct_account_balance_detail.transaction_date',$year)
        ->where('acct_account_balance_detail.company_id', Auth::user()->company_id)
        ->orderBy('acct_account_balance_detail.transaction_date', 'DESC')
        ->orderBy('acct_account_balance_detail.account_balance_detail_id', 'DESC')
        ->first();

        $acctgeneralledgerreport = array();
        foreach($accountbalancedetail as $val){
            $description = JournalVoucher::where('journal_voucher_id', $val['transaction_id'])->first('journal_voucher_description');
            $no_journal = JournalVoucher::where('journal_voucher_id', $val['transaction_id'])->first('journal_voucher_no');
            $data_state = JournalVoucher::where('journal_voucher_id', $val['transaction_id'])->first('data_state');
            if($val['last_balance'] <= 0){
                $debit = 0;
                $credit = $val['last_balance'];
            } else {
                $debit = $val['last_balance'];
                $credit = 0;
            }
        
            $acctgeneralledgerreport_detail = array(
                'date' => $val['transaction_date'],
                'no_journal' => $no_journal['journal_voucher_no'],
                'description' => $description['journal_voucher_description'],
                'account_id' => $val['account_id'],
                'account_in' => $val['account_in'],
                'account_out' => $val['account_out'],
                'debit' => $debit,
                'credit' => $credit,
                'data_state' => $data_state['data_state']
            );
            array_push($acctgeneralledgerreport, $acctgeneralledgerreport_detail);
        }

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
                <td><div style=\"text-align: center; font-size:14px; font-weight: bold\">BUKU BESAR</div></td>
            </tr>
            <tr>
                <td><div style=\"text-align: center; font-size:12px\">PERIODE : ".$monthlist[$start_month]." - ".$monthlist[$end_month] ." ".$year."</div></td>
            </tr>
        </table>
        ";
        $pdf::writeHTML($tbl, true, false, false, false, '');

        $tbl = "
        <br>
        <br>
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"0\">
            <tr>
                <td width=\"20%\"><div style=\"text-align: lef=ft; font-size:12px;font-weight: bold\">Nama. Perkiraan</div></td>
                <td width=\"5%\"><div style=\"text-align: center; font-size:12px; font-weight: bold\">:</div></td>
                <td width=\"65%\"><div style=\"text-align: left; font-size:12px; font-weight: bold\">".$this->getAccountName($account_id)."</div></td>
            </tr>
            <tr>
                <td width=\"20%\"><div style=\"text-align: lef=ft; font-size:12px;font-weight: bold\">Posisi Saldo</div></td>
                <td width=\"5%\"><div style=\"text-align: center; font-size:12px; font-weight: bold\">:</div></td>
                <td width=\"65%\"><div style=\"text-align: left; font-size:12px; font-weight: bold\">".$this->getAccountStatus($account_id)."</div></td>
            </tr>
            <tr>
                <td width=\"20%\"><div style=\"text-align: lef=ft; font-size:12px;font-weight: bold\">Saldo Awal</div></td>
                <td width=\"5%\"><div style=\"text-align: center; font-size:12px; font-weight: bold\">:</div></td>
                <td width=\"65%\"><div style=\"text-align: left; font-size:12px; font-weight: bold\">".number_format($accountbalancedetail_old['last_balance'],2,'.',',')."</div></td>
            </tr>
        </table>";
        $pdf::writeHTML($tbl, true, false, false, false, '');
        

        $no = 1;
        $tblStock1 = "
        <table cellspacing=\"0\" cellpadding=\"1\" border=\"1\" width=\"100%\">
            <tr>
                <td width=\"5%\" rowspan=\"2\"><div style=\"text-align: center; font-weight: bold\">No</div></td>
                <td width=\"12%\" rowspan=\"2\"><div style=\"text-align: center; font-weight: bold\">Tanggal</div></td>
                <td width=\"25%\" rowspan=\"2\"><div style=\"text-align: center; font-weight: bold\">Uraian</div></td>
                <td width=\"15%\" rowspan=\"2\"><div style=\"text-align: center; font-weight: bold\">Debet </div></td>
                <td width=\"15%\" rowspan=\"2\"><div style=\"text-align: center; font-weight: bold\">Kredit </div></td>
                <td width=\"30%\" colspan=\"2\"><div style=\"text-align: center; font-weight: bold\">Saldo </div></td>
            </tr>
            
            <tr>
                <td width=\"15%\"><div style=\"text-align: center; font-weight: bold\">Debet </div></td>
                <td width=\"15%\"><div style=\"text-align: center; font-weight: bold\">Kredit </div></td>
            </tr>
        
             ";

        $tblStock2 = " ";
        $no = 1;
        foreach ($acctgeneralledgerreport as $key => $val) {
            $tblStock2 .="
                    <tr>			
                        <td style=\"text-align:center\">$no.</td>
                        <td style=\"text-align:center\">".$val['date']."</td>
                        <td>".$val['description']."</td>
                        <td><div style=\"text-align: right;\">".number_format($val['account_in'],2,'.',',')."</div></td>
                        <td><div style=\"text-align: right;\">".number_format($val['account_out'],2,'.',',')."</div></td>
                        <td><div style=\"text-align: right;\">".number_format($val['debit'],2,'.',',')."</div></td>
                        <td><div style=\"text-align: right;\">".number_format($val['credit'],2,'.',',')."</div></td>
                    </tr>
                    
                ";
            $no++;
        }
        $tblStock4 = " 
        </table>
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"0\">
            <tr>
                <td style=\"text-align:right\">".Auth::user()->name.", ".date('d-m-Y H:i')."</td>
            </tr>
        </table>";

        $pdf::writeHTML($tblStock1.$tblStock2.$tblStock4, true, false, false, false, '');


        $filename = 'Buku_Besar_.pdf';
        $pdf::Output($filename, 'I');

        return redirect('/ledger');
    }

    public function exportLedgerReport()
    {
        if(!$start_month = Session::get('start_month')){
            $start_month = date('m');
        }else{
            $start_month = Session::get('start_month');
        }
        if(!$end_month = Session::get('end_month')){
            $end_month = date('m');
        }else{
            $end_month = Session::get('end_month');
        }
        if(!$year = Session::get('year')){
            $year = date('Y');
        }else{
            $year = Session::get('year');
        }
        if(!$account_id = Session::get('account_id')){
            $account_id = '';
        }else{
            $account_id = Session::get('account_id');
        }
        $monthlist = array(
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        );

        $year_now 	=	date('Y');
        for($i=($year_now-2); $i<($year_now+2); $i++){
            $yearlist[$i] = $i;
        } 
        $accountlist = AcctAccount::where('data_state',0)->where('company_id',Auth::user()->company_id)->get()->pluck('account_name','account_id');
        $account = AcctAccount::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->where('account_id', $account_id)
        ->first();
        
        $accountbalancedetail = AcctAccountBalanceDetail::join('acct_account', 'acct_account.account_id','=','acct_account_balance_detail.account_id')
        ->select('acct_account_balance_detail.transaction_id','acct_account_balance_detail.last_balance','acct_account_balance_detail.account_in','acct_account_balance_detail.account_out','acct_account_balance_detail.transaction_date','acct_account_balance_detail.account_id')
        ->where('acct_account_balance_detail.account_id' ,$account_id)
        ->whereMonth('acct_account_balance_detail.transaction_date','>=',$start_month)
        ->whereMonth('acct_account_balance_detail.transaction_date','<=',$end_month)
        ->whereYear('acct_account_balance_detail.transaction_date','<=',$year)
        ->where('acct_account_balance_detail.company_id', Auth::user()->company_id)
        ->orderBy('acct_account_balance_detail.transaction_date', 'ASC')
        ->orderBy('acct_account_balance_detail.account_balance_detail_id', 'ASC')
        ->get();
        $accountbalancedetail_old = AcctAccountBalanceDetail::join('acct_account', 'acct_account.account_id','=','acct_account_balance_detail.account_id')
        ->select('acct_account_balance_detail.last_balance')
        ->where('acct_account_balance_detail.account_id' ,$account_id)
        ->whereMonth('acct_account_balance_detail.transaction_date',$start_month-1)
        ->whereYear('acct_account_balance_detail.transaction_date',$year)
        ->where('acct_account_balance_detail.company_id', Auth::user()->company_id)
        ->orderBy('acct_account_balance_detail.transaction_date', 'DESC')
        ->orderBy('acct_account_balance_detail.account_balance_detail_id', 'DESC')
        ->first();

        $acctgeneralledgerreport = array();
        foreach($accountbalancedetail as $val){
            $description = JournalVoucher::where('journal_voucher_id', $val['transaction_id'])->first('journal_voucher_description');
            $no_journal = JournalVoucher::where('journal_voucher_id', $val['transaction_id'])->first('journal_voucher_no');
            $data_state = JournalVoucher::where('journal_voucher_id', $val['transaction_id'])->first('data_state');
            if($val['last_balance'] <= 0){
                $debit = 0;
                $credit = $val['last_balance'];
            } else {
                $debit = $val['last_balance'];
                $credit = 0;
            }
        
            $acctgeneralledgerreport_detail = array(
                'date' => $val['transaction_date'],
                'no_journal' => $no_journal['journal_voucher_no'],
                'description' => $description['journal_voucher_description'],
                'account_id' => $val['account_id'],
                'account_in' => $val['account_in'],
                'account_out' => $val['account_out'],
                'debit' => $debit,
                'credit' => $credit,
                'data_state' => $data_state['data_state']
            );
            array_push($acctgeneralledgerreport, $acctgeneralledgerreport_detail);
        }
        
        $spreadsheet = new Spreadsheet();
        
        if(count($acctgeneralledgerreport)>=0){
            
            $spreadsheet->getProperties()->setCreator("MOZAIC Minimarket")
                                 ->setLastModifiedBy("MOZAIC Minimarket")
                                 ->setTitle("Buku Besar")
                                 ->setSubject("")
                                 ->setDescription("Buku Besar")
                                 ->setKeywords("Buku Besar")
                                 ->setCategory("Buku Besar");
                                 
            $sheet = $spreadsheet->getActiveSheet(0);
            $spreadsheet->getActiveSheet()->setTitle("Buku Besar");
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(5);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(40);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(20);
    
            $spreadsheet->getActiveSheet()->mergeCells("B1:G1");

            $spreadsheet->getActiveSheet()->mergeCells("B8:B9");
            $spreadsheet->getActiveSheet()->mergeCells("C8:C9");
            $spreadsheet->getActiveSheet()->mergeCells("D8:D9");
            $spreadsheet->getActiveSheet()->mergeCells("E8:E9");
            $spreadsheet->getActiveSheet()->mergeCells("F8:F9");

            
            $spreadsheet->getActiveSheet()->mergeCells("G8:H8");
            $spreadsheet->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);

            $spreadsheet->getActiveSheet()->getStyle('B8:H8')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->getStyle('B9:H9')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            $spreadsheet->getActiveSheet()->getStyle('B8:H8')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('B9:H9')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $spreadsheet->getActiveSheet()->mergeCells("B5:C5");
            $spreadsheet->getActiveSheet()->getStyle('B5:D5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
            $spreadsheet->getActiveSheet()->getStyle('B5:D5')->getFont()->setBold(true);

            $spreadsheet->getActiveSheet()->mergeCells("B6:C6");
            $spreadsheet->getActiveSheet()->getStyle('B6:D6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
            $spreadsheet->getActiveSheet()->getStyle('B6:D6')->getFont()->setBold(true);

            $spreadsheet->getActiveSheet()->mergeCells("B7:C7");
            $spreadsheet->getActiveSheet()->getStyle('B7:D7')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
            $spreadsheet->getActiveSheet()->getStyle('B7:D7')->getFont()->setBold(true);


            
            $sheet->setCellValue('B1',"Buku Besar Dari Periode ".$monthlist[$start_month]." s.d ".$monthlist[$end_month]." ".$year);	
            $sheet->setCellValue('B5',"Nama Perkiraan");
            $sheet->setCellValue('D5', $this->getAccountName($account_id));
            $sheet->setCellValue('B6',"Posisi Saldo");
            $sheet->setCellValue('D6', $this->getAccountStatus($account_id));
            $sheet->setCellValue('B7',"Saldo Awal");
            $sheet->setCellValue('D7', number_format($accountbalancedetail_old['last_balance'],2,'.',','));
            $sheet->setCellValue('B8',"No");
            $sheet->setCellValue('C8',"Tanggal");
            $sheet->setCellValue('D8',"Uraian");
            $sheet->setCellValue('E8',"Debet");
            $sheet->setCellValue('F8',"Kredit");
            $sheet->setCellValue('G8',"Saldo");

            
            $sheet->setCellValue('G9',"Debet");
            $sheet->setCellValue('H9',"Kredit");
            
            
            $j=10;
            $no=0;
            
            foreach($acctgeneralledgerreport as $key=>$val){

                if(is_numeric($key)){
                    
                    $spreadsheet->setActiveSheetIndex(0);
                    $spreadsheet->getActiveSheet()->getStyle('B'.$j.':H'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    
                    $spreadsheet->getActiveSheet()->getStyle('E'.$j.':H'.$j)->getNumberFormat()->setFormatCode('0.00');

                    $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $spreadsheet->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    $spreadsheet->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    $spreadsheet->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                        $no++;
                        $sheet->setCellValue('B'.$j, $no);
                        $sheet->setCellValue('C'.$j, $val['date']);
                        $sheet->setCellValue('D'.$j, $val['description']);
                        $sheet->setCellValue('E'.$j, $val['account_in']);
                        $sheet->setCellValue('F'.$j, $val['account_out']);
                        $sheet->setCellValue('G'.$j, $val['debit']);
                        $sheet->setCellValue('H'.$j, $val['credit']);
                        
                        
                    
                }else{
                    continue;
                }
                $j++;
        
            }
            $spreadsheet->getActiveSheet()->mergeCells('B'.$j.':H'.$j);
            $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('B'.$j, Auth::user()->name.", ".date('d-m-Y H:i'));

            $filename='Buku_Besar.xls';
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
