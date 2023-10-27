<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AcctBalanceSheetReport;
use App\Models\JournalVoucher;
use Elibyy\TCPDF\Facades\TCPDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class AcctBalanceSheetReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        
    }

    public function index()
    {
        if(!$month = Session::get('month')){
            $month = date('m');
        }else{
            $month = Session::get('month');
        }
        if(!$year = Session::get('year')){
            $year = date('Y');
        }else{
            $year = Session::get('year');
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
            '12' => 'Desember'
        );
        $year_now 	=	date('Y');
        for($i=($year_now-2); $i<($year_now+2); $i++){
            $yearlist[$i] = $i;
        } 

        $acctbalancesheetreport_left = AcctBalanceSheetReport::select('report_tab1','report_bold1','report_type1','account_name1','account_code1','report_no','report_formula1','report_operator1','account_id1')
        ->where('data_state', 0)
        ->where('company_id', Auth::user()->company_id)
        ->get();

        $acctbalancesheetreport_right = AcctBalanceSheetReport::select('report_tab2','report_bold2','report_type2','account_name2','account_code2','report_no','report_formula2','report_operator2','account_id2')
        ->where('data_state', 0)
        ->where('company_id', Auth::user()->company_id)
        ->get();

        return view('content.AcctBalanceSheetReport.ListAcctBalanceSheetReport', compact('monthlist','yearlist','month','year','acctbalancesheetreport_left','acctbalancesheetreport_right'));
    }

    public function filterAcctBalanceSheetReport(Request $request)
    {
        $month = $request->month;
        $year = $request->year;

        Session::put('month', $month);
        Session::put('year', $year);

        return redirect('/balance-sheet-report');
    }

    public function resetFilterAcctBalanceSheetReport()
    {
        Session::forget('month');
        Session::forget('year');

        return redirect('/balance-sheet-report');
    }

    public function getMonthName($month_id)
    {
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
            '12' => 'Desember'
        );

        return $monthlist[$month_id];
    }

    public function getAmountAccount($account_id)
    {
        if(!$month = Session::get('month')){
            $month = date('m');
        }else{
            $month = Session::get('month');
        }
        if(!$year = Session::get('year')){
            $year = date('Y');
        }else{
            $year = Session::get('year');
        }

        $data = JournalVoucher::select('acct_journal_voucher_item.account_id_status','acct_journal_voucher_item.journal_voucher_amount')
        ->join('acct_journal_voucher_item','acct_journal_voucher_item.journal_voucher_id','acct_journal_voucher.journal_voucher_id')
        ->whereMonth('acct_journal_voucher.journal_voucher_date', $month)
        ->whereYear('acct_journal_voucher.journal_voucher_date', $year)
        ->where('acct_journal_voucher.data_state',0)
        ->where('acct_journal_voucher_item.account_id', $account_id)
        ->where('acct_journal_voucher.company_id', Auth::user()->company_id)
        ->get();
        $data_first = JournalVoucher::select('acct_journal_voucher_item.account_id_status')
        ->join('acct_journal_voucher_item','acct_journal_voucher_item.journal_voucher_id','acct_journal_voucher.journal_voucher_id')
        ->whereMonth('acct_journal_voucher.journal_voucher_date', $month)
        ->whereYear('acct_journal_voucher.journal_voucher_date', $year)
        ->where('acct_journal_voucher.data_state',0)
        ->where('acct_journal_voucher.company_id', Auth::user()->company_id)
        ->where('acct_journal_voucher_item.account_id', $account_id)
        ->first();
        
        $amount = 0;
        $amount1 = 0;
        $amount2 = 0;
        foreach ($data as $key => $val) {

            if ($val['account_id_status'] == $data_first['account_id_status']) {
                $amount1 += $val['journal_voucher_amount'];
            } else {
                $amount2 += $val['journal_voucher_amount'];

            }
            $amount = $amount1 - $amount2;
        }
        
        return $amount;
    }

    public function printAcctBalanceSheetReport()
    {
        if(!$month = Session::get('month')){
            $month = date('m');
        }else{
            $month = Session::get('month');
        }
        if(!$year = Session::get('year')){
            $year = date('Y');
        }else{
            $year = Session::get('year');
        }

        $acctbalancesheetreport_left = AcctBalanceSheetReport::select('report_tab1','report_bold1','report_type1','account_name1','account_code1','report_no','report_formula1','report_operator1','account_id1')
        ->where('data_state', 0)
        ->where('company_id', Auth::user()->company_id)
        ->get();

        $acctbalancesheetreport_right = AcctBalanceSheetReport::select('report_tab2','report_bold2','report_type2','account_name2','account_code2','report_no','report_formula2','report_operator2','account_id2')
        ->where('data_state', 0)
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

        $pdf::SetFont('helvetica', '', 10);

        $tbl = "
            <table cellspacing=\"0\" cellpadding=\"5\" border=\"0\">
                <tr>
                    <td colspan=\"5\"><div style=\"text-align: center; font-size:14px\">LAPORAN NERACA<BR>Periode ".$this->getMonthName($month)." ".$year."</div></td>
                </tr>
            </table>
        ";

        $pdf::writeHTML($tbl, true, false, false, false, '');
        
        $tblHeader = "
        <table id=\"items\" width=\"100%\" cellspacing=\"1\" cellpadding=\"2\" border=\"1\">			        
            <tr>";
                $tblheader_left = "
                    <td style=\"width: 50%\">	
                        <table id=\"items\" width=\"100%\" cellspacing=\"1\" cellpadding=\"2\" border=\"0\">";	
                            $tblitem_left = "";
                            $grand_total_account_amount1 = 0;
                            $grand_total_account_amount2 = 0;
                            foreach ($acctbalancesheetreport_left as $keyLeft => $valLeft) {
                                if($valLeft['report_tab1'] == 0){
                                    $report_tab1 = '';
                                } else if($valLeft['report_tab1'] == 1){
                                    $report_tab1 = '&nbsp;&nbsp;&nbsp;';
                                } else if($valLeft['report_tab1'] == 2){
                                    $report_tab1 = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                                } else if($valLeft['report_tab1'] == 3){
                                    $report_tab1 = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                                }

                                if($valLeft['report_bold1'] == 1){
                                    $report_bold1 = 'bold';
                                } else {
                                    $report_bold1 = 'normal';
                                }									

                                if($valLeft['report_type1'] == 1){
                                    $tblitem_left1 = "
                                        <tr>
                                            <td colspan=\"2\" style=\"width: 100%\"><div style=\"font-weight:".$report_bold1."\">".$report_tab1."".$valLeft['account_name1']."</div></td>
                                        </tr>";
                                } else {
                                    $tblitem_left1 = "";
                                }



                                if($valLeft['report_type1']	== 2){
                                    $tblitem_left2 = "
                                        <tr>
                                            <td style=\"width: 70%\"><div style=\"font-weight:".$report_bold1."\">".$report_tab1."".$valLeft['account_name1']."</div></td>
                                            <td style=\"width: 30%\"><div style=\"font-weight:".$report_bold1."\"></div></td>
                                        </tr>";
                                } else {
                                    $tblitem_left2 = "";
                                }									

                                if($valLeft['report_type1']	== 3){
                                    $last_balance1 	= $this->getAmountAccount($valLeft['account_id1']);		

                                    $tblitem_left3 = "
                                        <tr>
                                            <td><div style=\"font-weight:".$report_bold1."\">".$report_tab1."(".$valLeft['account_code1'].") ".$valLeft['account_name1']."</div> </td>
                                            <td style=\"text-align:right;\">".number_format($last_balance1, 2)."</td>
                                        </tr>";

                                    $account_amount1_top[$valLeft['report_no']] = $last_balance1;

                                } else {
                                    $tblitem_left3 = "";
                                }

                                if($valLeft['report_type1']	== 10){
                                    $last_balance10 	= $this->getAmountAccount($valLeft['account_id1']);		


                                    $account_amount10_top[$valLeft['report_no']] = $last_balance10;

                                } else {
                                }
                                

                                if($valLeft['report_type1'] == 11){
                                    if(!empty($valLeft['report_formula1']) && !empty($valLeft['report_operator1'])){
                                        $report_formula1 	= explode('#', $valLeft['report_formula1']);
                                        $report_operator1 	= explode('#', $valLeft['report_operator1']);

                                        $total_account_amount10	= 0;
                                        for($i = 0; $i < count($report_formula1); $i++){
                                            if($report_operator1[$i] == '-'){
                                                if($total_account_amount10 == 0 ){
                                                    $total_account_amount10 = $total_account_amount10 + $account_amount10_top[$report_formula1[$i]];
                                                } else {
                                                    $total_account_amount10 = $total_account_amount10 - $account_amount10_top[$report_formula1[$i]];
                                                }
                                            } else if($report_operator1[$i] == '+'){
                                                if($total_account_amount10 == 0){
                                                    $total_account_amount10 = $total_account_amount10 + $account_amount10_top[$report_formula1[$i]];
                                                } else {
                                                    $total_account_amount10 = $total_account_amount10 + $account_amount10_top[$report_formula1[$i]];
                                                }
                                            }
                                        }

                                        $grand_total_account_amount1 = $grand_total_account_amount1 + $total_account_amount10;

                                        $tblitem_left10 = "
                                            <tr>
                                                <td><div style=\"font-weight:".$report_bold1."\">".$report_tab1."".$valLeft['account_name1']."</div></td>
                                                <td style=\"text-align:right;\"><div style=\"font-weight:".$report_bold1."\">".number_format($total_account_amount10, 2)."</div></td>
                                            </tr>";
                                    } else {
                                        $tblitem_left10 = "";
                                    }
                                } else {
                                    $tblitem_left10 = "";
                                }

                                if($valLeft['report_type1']	== 7){
                                    $last_balance1 	= $this->getAmountAccount($valLeft['account_id1']);		

                                    $tblitem_left7 = "
                                        <tr>
                                            <td><div style=\"font-weight:".$report_bold1."\">".$report_tab1."(".$valLeft['account_code1'].") ".$valLeft['account_name1']."</div> </td>
                                            <td style=\"text-align:right;\">(".number_format($last_balance1, 2).")</td>
                                        </tr>";

                                    $account_amount1_top[$valLeft['report_no']] = $last_balance1;

                                } else {
                                    $tblitem_left7 = "";
                                }
                                

                                if($valLeft['report_type1'] == 5){
                                    if(!empty($valLeft['report_formula1']) && !empty($valLeft['report_operator1'])){
                                        $report_formula1 	= explode('#', $valLeft['report_formula1']);
                                        $report_operator1 	= explode('#', $valLeft['report_operator1']);

                                        $total_account_amount1	= 0;
                                        for($i = 0; $i < count($report_formula1); $i++){
                                            if($report_operator1[$i] == '-'){
                                                if($total_account_amount1 == 0 ){
                                                    $total_account_amount1 = $total_account_amount1 + $account_amount1_top[$report_formula1[$i]];
                                                } else {
                                                    $total_account_amount1 = $total_account_amount1 - $account_amount1_top[$report_formula1[$i]];
                                                }
                                            } else if($report_operator1[$i] == '+'){
                                                if($total_account_amount1 == 0){
                                                    $total_account_amount1 = $total_account_amount1 + $account_amount1_top[$report_formula1[$i]];
                                                } else {
                                                    $total_account_amount1 = $total_account_amount1 + $account_amount1_top[$report_formula1[$i]];
                                                }
                                            }
                                        }

                                        $grand_total_account_amount1 = $grand_total_account_amount1 + $total_account_amount1;

                                        // $tblitem_left5 = "
                                        //     <tr>
                                        //         <td><div style=\"font-weight:".$report_bold1."\">".$report_tab1."".$valLeft['account_name1']."</div></td>
                                        //         <td style=\"text-align:right;\"><div style=\"font-weight:".$report_bold1."\">".number_format($total_account_amount1+$total_account_amount10, 2)."</div></td>
                                        //     </tr>";
                                        $tblitem_left5 = "
                                            <tr>
                                                <td><div style=\"font-weight:".$report_bold1."\">".$report_tab1."".$valLeft['account_name1']."</div></td>
                                                <td style=\"text-align:right;\"><div style=\"font-weight:".$report_bold1."\">".number_format($total_account_amount1, 2)."</div></td>
                                            </tr>";
                                    } else {
                                        $tblitem_left5 = "";
                                    }
                                } else {
                                    $tblitem_left5 = "";
                                }

                                $tblitem_left .= $tblitem_left1.$tblitem_left2.$tblitem_left3.$tblitem_left10.$tblitem_left7.$tblitem_left5;

                                // if($valLeft['report_type1'] == 6){
                                // 	if(!empty($valLeft['report_formula1']) && !empty($valLeft['report_operator1'])){
                                // 		$report_formula1 	= explode('#', $valLeft['report_formula1']);
                                // 		$report_operator1 	= explode('#', $valLeft['report_operator1']);

                                // 		$total_account_amount1	= 0;
                                // 		for($i = 0; $i < count($report_formula1); $i++){
                                // 			if($report_operator1[$i] == '-'){
                                // 				if($total_account_amount1 == 0 ){
                                // 					$total_account_amount1 = $total_account_amount1 + $account_amount1_top[$report_formula1[$i]];
                                // 				} else {
                                // 					$total_account_amount1 = $total_account_amount1 - $account_amount1_top[$report_formula1[$i]];
                                // 				}
                                // 			} else if($report_operator1[$i] == '+'){
                                // 				if($total_account_amount1 == 0){
                                // 					$total_account_amount1 = $total_account_amount1 + $account_amount1_top[$report_formula1[$i]];
                                // 				} else {
                                // 					$total_account_amount1 = $total_account_amount1 + $account_amount1_top[$report_formula1[$i]];
                                // 				}
                                // 			}
                                // 		}
                                        
                                // 	} else {
                                        
                                // 	}
                                // } else {
                                    
                                // }

                            }

                $tblfooter_left	= "
                        </table>
                    </td>";

                /* print_r("tblitem_left ");
                print_r($tblitem_left);
                exit; */

                $tblheader_right = "
                    <td style=\"width: 50%\">	
                        <table id=\"items\" width=\"100%\" cellspacing=\"1\" cellpadding=\"2\" border=\"0\">";		
                            $tblitem_right = "";
                            foreach ($acctbalancesheetreport_right as $keyRight => $valRight) {
                                if($valRight['report_tab2'] == 0){
                                    $report_tab2 = '';
                                } else if($valRight['report_tab2'] == 1){
                                    $report_tab2 = '&nbsp;&nbsp;&nbsp;';
                                } else if($valRight['report_tab2'] == 2){
                                    $report_tab2 = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                                } else if($valRight['report_tab2'] == 3){
                                    $report_tab2 = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                                }

                                if($valRight['report_bold2'] == 1){
                                    $report_bold2 = 'bold';
                                } else {
                                    $report_bold2 = 'normal';
                                }									

                                if($valRight['report_type2'] == 1){
                                    $tblitem_right1 = "
                                        <tr>
                                            <td colspan=\"2\"><div style=\"font-weight:".$report_bold2."\">".$report_tab2."".$valRight['account_name2']."</div></td>
                                        </tr>";
                                } else {
                                    $tblitem_right1 = "";
                                }



                                if($valRight['report_type2'] == 2){
                                    $tblitem_right2 = "
                                        <tr>
                                            <td style=\"width: 70%\"><div style=\"font-weight:".$report_bold2."\">".$report_tab2."".$valRight['account_name2']."</div></td>
                                            <td style=\"width: 30%\"><div style=\"font-weight:".$report_bold2."\"></div></td>
                                        </tr>";
                                } else {
                                    $tblitem_right2 = "";
                                }									

                                if($valRight['report_type2']	== 3){
                                    $last_balance2 	= $this->getAmountAccount($valRight['account_id2']);

                                    $tblitem_right3 = "
                                        <tr>
                                            <td><div style=\"font-weight:".$report_bold2."\">".$report_tab2."(".$valRight['account_code2'].") ".$valRight['account_name2']."</div> </td>
                                            <td style=\"text-align:right;\">".number_format($last_balance2, 2)."</td>
                                        </tr>";

                                    $account_amount2_bottom[$valRight['report_no']] = $last_balance2;
                                } else {
                                    $tblitem_right3 = "";
                                }

                                if($valRight['report_type2']	== 10){
                                    $last_balance210 	= $this->getAmountAccount($valRight['account_id2']);		


                                    $account_amount210_top[$valRight['report_no']] = $last_balance210;

                                } else {
                                }
                                

                                if($valRight['report_type2'] == 11){
                                    if(!empty($valRight['report_formula2']) && !empty($valRight['report_operator2'])){
                                        $report_formula2 	= explode('#', $valRight['report_formula2']);
                                        $report_operator2 	= explode('#', $valRight['report_operator2']);

                                        $total_account_amount210	= 0;
                                        for($i = 0; $i < count($report_formula2); $i++){
                                            if($report_operator2[$i] == '-'){
                                                if($total_account_amount210 == 0 ){
                                                    $total_account_amount210 = $total_account_amount210 + $account_amount210_top[$report_formula2[$i]];
                                                } else {
                                                    $total_account_amount210 = $total_account_amount210 - $account_amount210_top[$report_formula2[$i]];
                                                }
                                            } else if($report_operator1[$i] == '+'){
                                                if($total_account_amount210 == 0){
                                                    $total_account_amount210 = $total_account_amount210 + $account_amount210_top[$report_formula2[$i]];
                                                } else {
                                                    $total_account_amount210 = $total_account_amount210 + $account_amount210_top[$report_formula2[$i]];
                                                }
                                            }
                                        }

                                        $grand_total_account_amount2 = $grand_total_account_amount2 + $total_account_amount210;

                                        $tblitem_right10 = "
                                            <tr>
                                                <td><div style=\"font-weight:".$report_bold2."\">".$report_tab2."".$valRight['account_name2']."</div></td>
                                                <td style=\"text-align:right;\"><div style=\"font-weight:".$report_bold2."\">".number_format($total_account_amount210, 2)."</div></td>
                                            </tr>";
                                    } else {
                                        $tblitem_right10 = "";
                                    }
                                } else {
                                    $tblitem_right10 = "";
                                }

                                if($valRight['report_type2'] == 8){
                                    $sahu_tahun_lalu = $this->AcctBalanceSheetReportNew1_model->getSHUTahunLalu($month, $year);

                                    if(empty($sahu_tahun_lalu)){
                                        $sahu_tahun_lalu = 0;
                                    }


                                    
                                    $tblitem_right8 = "
                                        <tr>
                                            <td><div style=\"font-weight:".$report_bold2."\">".$report_tab2."(".$valRight['account_code2'].") ".$valRight['account_name2']."</div> </td>
                                            <td style=\"text-align:right;\">".number_format($sahu_tahun_lalu, 2)."</td>
                                        </tr>
                                        ";

                                    $account_amount2_bottom[$valRight['report_no']] = $sahu_tahun_lalu;
                                } else {
                                    $tblitem_right8 = "";
                                }

                                if($valRight['report_type2'] == 7){
                                    $profit_loss = $this->AcctBalanceSheetReportNew1_model->getProfitLossAmount($month, $year);

                                    if(empty($profit_loss)){
                                        $profit_loss = 0;
                                    }

                                    
                                    $tblitem_right7 = "
                                        <tr>
                                            <td><div style=\"font-weight:".$report_bold2."\">".$report_tab2."(".$valRight['account_code2'].") ".$valRight['account_name2']."</div> </td>
                                            <td style=\"text-align:right;\">".number_format($profit_loss, 2)."</td>
                                        </tr>
                                        ";

                                    $account_amount2_bottom[$valRight['report_no']] = $profit_loss;
                                } else {
                                    $tblitem_right7 = "";
                                }
                                

                                if($valRight['report_type2'] == 5){
                                    if(!empty($valRight['report_formula2']) && !empty($valRight['report_operator2'])){
                                        $report_formula2 	= explode('#', $valRight['report_formula2']);
                                        $report_operator2 	= explode('#', $valRight['report_operator2']);

                                        $total_account_amount2	= 0;
                                        for($i = 0; $i < count($report_formula2); $i++){
                                            if($report_operator2[$i] == '-'){
                                                if($total_account_amount2 == 0 ){
                                                    $total_account_amount2 = $total_account_amount2 + $account_amount2_bottom[$report_formula2[$i]];
                                                } else {
                                                    $total_account_amount2 = $total_account_amount2 - $account_amount2_bottom[$report_formula2[$i]];
                                                }
                                            } else if($report_operator2[$i] == '+'){
                                                if($total_account_amount2 == 0){
                                                    $total_account_amount2 = $total_account_amount2 + $account_amount2_bottom[$report_formula2[$i]];
                                                } else {
                                                    $total_account_amount2 = $total_account_amount2 + $account_amount2_bottom[$report_formula2[$i]];
                                                }
                                            }
                                        }

                                        $grand_total_account_amount2 = $grand_total_account_amount2 + $total_account_amount2;

                                        $tblitem_right5 = "
                                            <tr>
                                                <td><div style=\"font-weight:".$report_bold2."\">".$report_tab2."".$valRight['account_name2']."</div></td>
                                                <td style=\"text-align:right;\"><div style=\"font-weight:".$report_bold2."\">".number_format($total_account_amount2+$total_account_amount210, 2)."</div></td>
                                            </tr>";
                                        // $tblitem_right5 = "
                                        //     <tr>
                                        //         <td><div style=\"font-weight:".$report_bold2."\">".$report_tab2."".$valRight['account_name2']."</div></td>
                                        //         <td style=\"text-align:right;\"><div style=\"font-weight:".$report_bold2."\">".number_format($total_account_amount2, 2)."</div></td>
                                        //     </tr>";
                                    } else {
                                        $tblitem_right5 = "";
                                    }
                                } else {
                                    $tblitem_right5 = "";
                                }


                                

                                $tblitem_right .= $tblitem_right1.$tblitem_right2.$tblitem_right3.$tblitem_right10.$tblitem_right8.$tblitem_right7.$tblitem_right5;

                                
                            }

                $tblfooter_right = "
                        </table>
                    </td>";

        $tblFooter = "
            </tr>
            <tr>
                <td style=\"width: 50%\">
                    <table id=\"items\" width=\"100%\" cellspacing=\"1\" cellpadding=\"1\" border=\"0\">
                        <tr>
                            <td style=\"width: 60%\"><div style=\"font-weight:".$report_bold1.";font-size:12px\">".$report_tab1."".$valLeft['account_name1']."</div></td>
                            <td style=\"width: 40%; text-align:right;\"><div style=\"font-weight:".$report_bold1."; font-size:14px\">".number_format($grand_total_account_amount1, 2)."</div></td>
                        </tr>
                    </table>
                </td>
                <td style=\"width: 50%\">
                    <table id=\"items\" width=\"100%\" cellspacing=\"1\" cellpadding=\"1\" border=\"0\">
                        <tr>
                            <td style=\"width: 60%\"><div style=\"font-weight:".$report_bold2.";font-size:12px\">".$report_tab2."".$valRight['account_name2']."</div></td>
                            <td style=\"width: 40%; text-align:right;\"><div style=\"font-weight:".$report_bold2."; font-size:14px\">".number_format($grand_total_account_amount2, 2)."</div></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>";
            
        $table = $tblHeader.$tblheader_left.$tblitem_left.$tblfooter_left.$tblheader_right.$tblitem_right.$tblfooter_right.$tblFooter;
            /*print_r("table ");
            print_r($table);
            exit;*/

        $pdf::writeHTML($table, true, false, false, false, '');


        $filename = 'Laporan_Neraca.pdf';
        $pdf::Output($filename, 'I');
    }

    public function exportAcctBalanceSheetReport()
    {
        if(!$month = Session::get('month')){
            $month = date('m');
        }else{
            $month = Session::get('month');
        }
        if(!$year = Session::get('year')){
            $year = date('Y');
        }else{
            $year = Session::get('year');
        }

        $acctbalancesheetreport_left = AcctBalanceSheetReport::select('report_tab1','report_bold1','report_type1','account_name1','account_code1','report_no','report_formula1','report_operator1','account_id1')
        ->where('data_state', 0)
        ->where('company_id', Auth::user()->company_id)
        ->get();

        $acctbalancesheetreport_right = AcctBalanceSheetReport::select('report_tab2','report_bold2','report_type2','account_name2','account_code2','report_no','report_formula2','report_operator2','account_id2')
        ->where('data_state', 0)
        ->where('company_id', Auth::user()->company_id)
        ->get();

        if(!empty($acctbalancesheetreport_left && $acctbalancesheetreport_right)){
            $spreadsheet = new Spreadsheet();
            $spreadsheet->getProperties()->setCreator("SIS Integrated System")
                                    ->setLastModifiedBy("SIS Integrated System")
                                    ->setTitle("Laporan Neraca")
                                    ->setSubject("")
                                    ->setDescription("Laporan Neraca")
                                    ->setKeywords("Neraca, Laporan, SIS, Integrated")
                                    ->setCategory("Laporan Neraca");
                                    
            $spreadsheet->setActiveSheetIndex(0);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(50);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(50);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            
            $spreadsheet->getActiveSheet()->mergeCells("B1:E1");
            $spreadsheet->getActiveSheet()->mergeCells("B2:E2");
            // $spreadsheet->getActiveSheet()->mergeCells("B3:E3");
            $spreadsheet->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
            $spreadsheet->getActiveSheet()->getStyle('B2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('B2')->getFont()->setBold(true)->setSize(12);

            // $spreadsheet->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            // $spreadsheet->getActiveSheet()->getStyle('B3')->getFont()->setBold(true)->setSize(12);

            $spreadsheet->getActiveSheet()->getStyle('B4:E4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('B4:E4')->getFont()->setBold(true);	
            $spreadsheet->getActiveSheet()->setCellValue('B1',"Laporan Neraca ");	
            // $spreadsheet->getActiveSheet()->setCellValue('B2',$preferencecompany['company_name']);	
            $spreadsheet->getActiveSheet()->setCellValue('B2',"Periode ".$this->getMonthName($month)." ".$year."");	
            
            $j = 4;
            $no = 0;
            $grand_total = 0;
            $grand_total_account_amount1 = 0;
            $grand_total_account_amount2 = 0;
            
            foreach($acctbalancesheetreport_left as $keyLeft =>$valLeft){
                if(is_numeric($keyLeft)){
                    
                    $spreadsheet->setActiveSheetIndex(0);
                    /*$spreadsheet->getActiveSheet()->getStyle('B'.$j.':C'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);*/
            
                    $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                
                    if($valLeft['report_tab1'] == 0){
                        $report_tab1 = ' ';
                    } else if($valLeft['report_tab1'] == 1){
                        $report_tab1 = '     ';
                    } else if($valLeft['report_tab1'] == 2){
                        $report_tab1 = '          ';
                    } else if($valLeft['report_tab1'] == 3){
                        $report_tab1 = '               ';
                    }

                    if($valLeft['report_bold1'] == 1){
                        $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getFont()->setBold(true);	
                        $spreadsheet->getActiveSheet()->getStyle('C'.$j)->getFont()->setBold(true);	
                    } else {
                        
                    }									

                    if($valLeft['report_type1'] == 1){
                        $spreadsheet->getActiveSheet()->mergeCells("B".$j.":C".$j."");
                        $spreadsheet->getActiveSheet()->setCellValue('B'.$j, $valLeft['account_name1']);
                    } else {

                    }



                    if($valLeft['report_type1']	== 2){
                        $spreadsheet->getActiveSheet()->setCellValue('B'.$j, $report_tab1.$valLeft['account_name1']);
                    } else {

                    }									

                    if($valLeft['report_type1']	== 3){
                        $last_balance1 = $this->getAmountAccount($valLeft['account_id1']);		

                        if (empty($last_balance1)){
                            $last_balance1 = 0;
                        }

                        $spreadsheet->getActiveSheet()->setCellValue('B'.$j, $report_tab1.$valLeft['account_name1']);
                        $spreadsheet->getActiveSheet()->setCellValue('C'.$j, $report_tab1.$last_balance1);

                        $account_amount1_top[$valLeft['report_no']] = $last_balance1;

                    } else {

                    }

                    if($valLeft['report_type1']	== 10){
                        $last_balance10 = $this->getAmountAccount($valLeft['account_id1']);		

                        if (empty($last_balance10)){
                            $last_balance10 = 0;
                        }

                        $account_amount10_top[$valLeft['report_no']] = $last_balance10;

                    } else {

                    }
                    

                    if($valLeft['report_type1'] == 11){
                        if(!empty($valLeft['report_formula1']) && !empty($valLeft['report_operator1'])){
                            $report_formula1 	= explode('#', $valLeft['report_formula1']);
                            $report_operator1 	= explode('#', $valLeft['report_operator1']);

                            $total_account_amount10	= 0;
                            for($i = 0; $i < count($report_formula1); $i++){
                                if($report_operator1[$i] == '-'){
                                    if($total_account_amount10 == 0 ){
                                        $total_account_amount10 = $total_account_amount10 + $account_amount10_top[$report_formula1[$i]];
                                    } else {
                                        $total_account_amount10 = $total_account_amount10 - $account_amount10_top[$report_formula1[$i]];
                                    }
                                } else if($report_operator1[$i] == '+'){
                                    if($total_account_amount10 == 0){
                                        $total_account_amount10 = $total_account_amount10 + $account_amount10_top[$report_formula1[$i]];
                                    } else {
                                        $total_account_amount10 = $total_account_amount10 + $account_amount10_top[$report_formula1[$i]];
                                    }
                                }
                            }

                            $spreadsheet->getActiveSheet()->setCellValue('B'.$j, $report_tab1.$valLeft['account_name1']);
                            $spreadsheet->getActiveSheet()->setCellValue('C'.$j, $report_tab1.$total_account_amount10);
                            
                            $grand_total_account_amount1 +=  $total_account_amount10;

                            
                        } else {
                            
                        }
                    } else {
                        
                    }

                    if($valLeft['report_type1']	== 7){
                        $last_balance1 = $this->getAmountAccount($valLeft['account_id1']);		

                        if (empty($last_balance1)){
                            $last_balance1 = 0;
                        }

                        $spreadsheet->getActiveSheet()->setCellValue('B'.$j, $report_tab1.$valLeft['account_name1']);
                        $spreadsheet->getActiveSheet()->setCellValue('C'.$j, $report_tab1.$last_balance1);

                        $account_amount1_top[$valLeft['report_no']] = $last_balance1;

                    } else {

                    }
                    

                    if($valLeft['report_type1'] == 5){
                        if(!empty($valLeft['report_formula1']) && !empty($valLeft['report_operator1'])){
                            $report_formula1 	= explode('#', $valLeft['report_formula1']);
                            $report_operator1 	= explode('#', $valLeft['report_operator1']);

                            $total_account_amount1	= 0;
                            for($i = 0; $i < count($report_formula1); $i++){
                                if($report_operator1[$i] == '-'){
                                    if($total_account_amount1 == 0 ){
                                        $total_account_amount1 = $total_account_amount1 + $account_amount1_top[$report_formula1[$i]];
                                    } else {
                                        $total_account_amount1 = $total_account_amount1 - $account_amount1_top[$report_formula1[$i]];
                                    }
                                } else if($report_operator1[$i] == '+'){
                                    if($total_account_amount1 == 0){
                                        $total_account_amount1 = $total_account_amount1 + $account_amount1_top[$report_formula1[$i]];
                                    } else {
                                        $total_account_amount1 = $total_account_amount1 + $account_amount1_top[$report_formula1[$i]];
                                    }
                                }
                            }

                            $spreadsheet->getActiveSheet()->setCellValue('B'.$j, $report_tab1.$valLeft['account_name1']);
                            // $spreadsheet->getActiveSheet()->setCellValue('C'.$j, $report_tab1.($total_account_amount1+$total_account_amount10));
                            $spreadsheet->getActiveSheet()->setCellValue('C'.$j, $report_tab1.($total_account_amount1));
                            
                            $grand_total_account_amount1 +=  $total_account_amount1;

                            
                        } else {
                            
                        }
                    } else {
                        
                    }

                    if($valLeft['report_type1'] == 6){
                        if(!empty($valLeft['report_formula1']) && !empty($valLeft['report_operator1'])){
                            $report_formula1 	= explode('#', $valLeft['report_formula1']);
                            $report_operator1 	= explode('#', $valLeft['report_operator1']);

                            $grand_total_account_amount1	= 0;
                            for($i = 0; $i < count($report_formula1); $i++){
                                if($report_operator1[$i] == '-'){
                                    if($grand_total_account_amount1 == 0 ){
                                        $grand_total_account_amount1 = $grand_total_account_amount1 + $account_amount1_top[$report_formula1[$i]];
                                    } else {
                                        $grand_total_account_amount1 = $grand_total_account_amount1 - $account_amount1_top[$report_formula1[$i]];
                                    }
                                } else if($report_operator1[$i] == '+'){
                                    if($grand_total_account_amount1 == 0){
                                        $grand_total_account_amount1 = $grand_total_account_amount1 + $account_amount1_top[$report_formula1[$i]];
                                    } else {
                                        $grand_total_account_amount1 = $grand_total_account_amount1 + $account_amount1_top[$report_formula1[$i]];
                                    }
                                }
                            }
                            
                        } else {
                            
                        }
                    } else {
                        
                    }	

                }else{
                    continue;
                }

                $j++;
            }

            $total_row_left = $j;

            $j = 4;
            $no = 0;
            $grand_total = 0;

            foreach($acctbalancesheetreport_right as $keyRight =>$valRight){
                if(is_numeric($keyRight)){
                    
                    $spreadsheet->setActiveSheetIndex(0);
                    /*$spreadsheet->getActiveSheet()->getStyle('D'.$j.':E'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);*/
            
                    $spreadsheet->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                
                    if($valRight['report_tab2'] == 0){
                        $report_tab2 = ' ';
                    } else if($valRight['report_tab2'] == 1){
                        $report_tab2 = '     ';
                    } else if($valRight['report_tab2'] == 2){
                        $report_tab2 = '          ';
                    } else if($valRight['report_tab2'] == 3){
                        $report_tab2 = '               ';
                    }

                    if($valRight['report_bold2'] == 1){
                        $spreadsheet->getActiveSheet()->getStyle('D'.$j)->getFont()->setBold(true);	
                        $spreadsheet->getActiveSheet()->getStyle('E'.$j)->getFont()->setBold(true);	
                    } else {
                        
                    }									

                    if($valRight['report_type2'] == 1){
                        $spreadsheet->getActiveSheet()->mergeCells("D".$j.":E".$j."");
                        $spreadsheet->getActiveSheet()->setCellValue('D'.$j, $valRight['account_name2']);
                    } else {

                    }



                    if($valRight['report_type2']	== 2){
                        $spreadsheet->getActiveSheet()->setCellValue('D'.$j, $report_tab2.$valRight['account_name2']);
                    } else {

                    }									

                    if($valRight['report_type2']	== 3){
                        $last_balance2 = $this->getAmountAccount($valRight['account_id2']);		

                        if (empty($last_balance2)){
                            $last_balance2 = 0;
                        }

                        $spreadsheet->getActiveSheet()->setCellValue('D'.$j, $report_tab2.$valRight['account_name2']);
                        $spreadsheet->getActiveSheet()->setCellValue('E'.$j, $report_tab2.$last_balance2);

                        $account_amount2_bottom[$valRight['report_no']] = $last_balance2;

                    } else {

                    }

                    if($valRight['report_type2']	== 10){
                        $last_balance210 = $this->getAmountAccount($valRight['account_id2']);		

                        if (empty($last_balance210)){
                            $last_balance210 = 0;
                        }

                        $account_amount210_top[$valRight['report_no']] = $last_balance210;

                    } else {

                    }
                    

                    if($valRight['report_type2'] == 11){
                        if(!empty($valRight['report_formula2']) && !empty($valRight['report_operator2'])){
                            $report_formula2 	= explode('#', $valRight['report_formula2']);
                            $report_operator2 	= explode('#', $valRight['report_operator2']);

                            $total_account_amount210	= 0;
                            for($i = 0; $i < count($report_formula2); $i++){
                                if($report_operator2[$i] == '-'){
                                    if($total_account_amount210 == 0 ){
                                        $total_account_amount210 = $total_account_amount210 + $account_amount210_top[$report_formula2[$i]];
                                    } else {
                                        $total_account_amount210 = $total_account_amount210 - $account_amount210_top[$report_formula2[$i]];
                                    }
                                } else if($report_operator2[$i] == '+'){
                                    if($total_account_amount210 == 0){
                                        $total_account_amount210 = $total_account_amount210 + $account_amount210_top[$report_formula2[$i]];
                                    } else {
                                        $total_account_amount210 = $total_account_amount210 + $account_amount210_top[$report_formula2[$i]];
                                    }
                                }
                            }

                            $spreadsheet->getActiveSheet()->setCellValue('D'.$j, $report_tab1.$valRight['account_name2']);
                            $spreadsheet->getActiveSheet()->setCellValue('E'.$j, $report_tab1.$total_account_amount210);
                            
                            $grand_total_account_amount2 +=  $total_account_amount210;

                            
                        } else {
                            
                        }
                    } else {
                        
                    }

                    if($valRight['report_type2']	== 8){
                        $sahu_tahun_lalu = $this->AcctBalanceSheetReportNew1_model->getSHUTahunLalu($month, $year);

                        if(empty($sahu_tahun_lalu)){
                            $sahu_tahun_lalu = 0;
                        }


                        $spreadsheet->getActiveSheet()->setCellValue('D'.$j, $report_tab2.$valRight['account_name2']);
                        $spreadsheet->getActiveSheet()->setCellValue('E'.$j, $report_tab2.$sahu_tahun_lalu);

                        $account_amount2_bottom[$valRight['report_no']] = $sahu_tahun_lalu;

                    } else {

                    }

                    if($valRight['report_type2']	== 7){
                        $profit_loss = $this->AcctBalanceSheetReportNew1_model->getProfitLossAmount($month, $year);

                        if(empty($profit_loss)){
                            $profit_loss = 0;
                        }

                        $spreadsheet->getActiveSheet()->setCellValue('D'.$j, $report_tab2.$valRight['account_name2']);
                        $spreadsheet->getActiveSheet()->setCellValue('E'.$j, $report_tab2.$profit_loss);

                        $account_amount2_bottom[$valRight['report_no']] = $profit_loss;

                    } else {

                    }
                    

                    if($valRight['report_type2'] == 5){
                        if(!empty($valRight['report_formula2']) && !empty($valRight['report_operator2'])){
                            $report_formula2 	= explode('#', $valRight['report_formula2']);
                            $report_operator2 	= explode('#', $valRight['report_operator2']);

                            $total_account_amount2	= 0;
                            for($i = 0; $i < count($report_formula2); $i++){
                                if($report_operator2[$i] == '-'){
                                    if($total_account_amount2 == 0 ){
                                        $total_account_amount2 = $total_account_amount2 + $account_amount2_bottom[$report_formula2[$i]];
                                    } else {
                                        $total_account_amount2 = $total_account_amount2 - $account_amount2_bottom[$report_formula2[$i]];
                                    }
                                } else if($report_operator2[$i] == '+'){
                                    if($total_account_amount2 == 0){
                                        $total_account_amount2 = $total_account_amount2 + $account_amount2_bottom[$report_formula2[$i]];
                                    } else {
                                        $total_account_amount2 = $total_account_amount2 + $account_amount2_bottom[$report_formula2[$i]];
                                    }
                                }
                            }

                            $spreadsheet->getActiveSheet()->setCellValue('D'.$j, $report_tab2.$valRight['account_name2']);
                            // $spreadsheet->getActiveSheet()->setCellValue('E'.$j, $report_tab2.$total_account_amount2+$total_account_amount210);
                            $spreadsheet->getActiveSheet()->setCellValue('E'.$j, $report_tab2.$total_account_amount2);

                            
                            $grand_total_account_amount2 += $total_account_amount2;

                            
                        } else {
                            
                        }
                    } else {
                        
                    }
                    

                    if($valRight['report_type2'] == 6){
                        if(!empty($valRight['report_formula2']) && !empty($valRight['report_operator2'])){
                            $report_formula2 	= explode('#', $valRight['report_formula2']);
                            $report_operator2 	= explode('#', $valRight['report_operator2']);

                            $grand_total_account_amount2	= 0;
                            for($i = 0; $i < count($report_formula2); $i++){
                                if($report_operator2[$i] == '-'){
                                    if($grand_total_account_amount2 == 0 ){
                                        $grand_total_account_amount2 = $grand_total_account_amount2 + $account_amount2_bottom[$report_formula2[$i]];
                                    } else {
                                        $grand_total_account_amount2 = $grand_total_account_amount2 - $account_amount2_bottom[$report_formula2[$i]];
                                    }
                                } else if($report_operator2[$i] == '+'){
                                    if($grand_total_account_amount2 == 0){
                                        $grand_total_account_amount2 = $grand_total_account_amount2 + $account_amount2_bottom[$report_formula2[$i]];
                                    } else {
                                        $grand_total_account_amount2 = $grand_total_account_amount2 + $account_amount2_bottom[$report_formula2[$i]];
                                    }
                                }
                            }
                        } else {
                            
                        }
                    } else {
                        
                    }	

                }else{
                    continue;
                }

                $j++;
            }

            $total_row_right = $j;

            if ($total_row_left > $total_row_right){
                $total_row_right = $total_row_left;
            } else if ($total_row_left < $total_row_right){
                $total_row_left = $total_row_right;
            }

            $spreadsheet->getActiveSheet()->getStyle('B'.$total_row_left)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
            $spreadsheet->getActiveSheet()->getStyle('C'.$total_row_left)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

            $spreadsheet->getActiveSheet()->getStyle('D'.$total_row_right)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
            $spreadsheet->getActiveSheet()->getStyle('E'.$total_row_right)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

            $spreadsheet->getActiveSheet()->getStyle("B".$total_row_left.":E".$total_row_right)->getFont()->setBold(true);	

            $spreadsheet->getActiveSheet()->setCellValue('B'.$total_row_left, $report_tab1.$valLeft['account_name1']);
            $spreadsheet->getActiveSheet()->setCellValue('C'.$total_row_left, $report_tab1.$grand_total_account_amount1);

            $spreadsheet->getActiveSheet()->setCellValue('D'.$total_row_right, $report_tab2.$valRight['account_name2']);
            $spreadsheet->getActiveSheet()->setCellValue('E'.$total_row_right, $report_tab2.$grand_total_account_amount2);

        
            $filename='Laporan_Rugi_Laba_01_'.$month.'_'.$year.'.xls';
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
