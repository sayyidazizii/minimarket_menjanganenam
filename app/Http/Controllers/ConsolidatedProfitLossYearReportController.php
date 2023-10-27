<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AcctProfitLossCombinedReport;
use App\Models\AcctProfitLossReport;
use App\Models\JournalVoucher;
use Elibyy\TCPDF\Facades\TCPDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ConsolidatedProfitLossYearReportController extends Controller
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

        // $profit_mi = curl_init();
        // curl_setopt($profit_mi, CURLOPT_URL,'https://ciptapro.com/minimarket_menjanganenam/api/get-data-profit-loss-report');
        // curl_setopt($profit_mi, CURLOPT_RETURNTRANSFER, true);
        // $response_profit_mi = curl_exec($profit_mi);
        // $result_profit_mi = json_decode($response_profit_mi,TRUE);
        // curl_close($profit_mi);

        // $profit_merge = [];
        // if (!empty($result_profit_mi)) {
        //     for ($i=0; $i < count($result_profit_mi) ; $i++) { 
        //         if ($result_profit_mi[$i]['company_id'] == Auth::user()->company_id) {
        //             array_push($profit_merge, $result_profit_mi[$i]);
        //         }
        //     }
        // }

        // $profit_mo = curl_init();
        // curl_setopt($profit_mo, CURLOPT_URL,'https://ciptapro.com/kasihibu_mozaic/api/get-data-profit-loss-report');
        // curl_setopt($profit_mo, CURLOPT_RETURNTRANSFER, true);
        // $response_profit_mo = curl_exec($profit_mo);
        // $result_profit_mo = json_decode($response_profit_mo,TRUE);
        // curl_close($profit_mo);

        // if (!empty($result_profit_mo)) {
        //     for ($i=0; $i < count($result_profit_mo) ; $i++) { 
        //         if ($result_profit_mo[$i]['company_id'] == Auth::user()->company_id) {
        //             array_push($profit_merge, $result_profit_mo[$i]);
        //         }
        //     }
        // }

        // $profit_unique = [];
        // for ($i=0; $i < count($profit_merge) ; $i++) { 
        //     if (!empty($result_profit_mi[$i]) || !empty($result_profit_mo[$i])) {

        //         if (($result_profit_mi[$i]['account_code'] == $result_profit_mo[$i]['account_code']) && ($result_profit_mi[$i]['account_name'] == $result_profit_mo[$i]['account_name'])) {
        //             array_push($profit_unique, $result_profit_mi[$i]);
        //         } else {
        //             array_push($profit_unique, $result_profit_mi[$i]);
        //             array_push($profit_unique, $result_profit_mo[$i]);
        //         }
        //     }
        // }


        // for ($i=0; $i < count($profit_unique) ; $i++) { 
        //     if ($profit_unique[$i]['account_type_id'] == 2) {
        //         $income[$i] = $profit_unique[$i];
        //     } 
        // }

        // for ($i=0; $i < count($profit_unique) ; $i++) { 
        //     if ($profit_unique[$i]['account_type_id'] == 3) {
        //         $expenditure[$i] = $profit_unique[$i];
        //     }
        // }

        $income = AcctProfitLossCombinedReport::where('data_state',0)
        ->where('account_type_id',2)
        ->where('company_id', Auth::user()->company_id)
        ->get();

        $expenditure = AcctProfitLossCombinedReport::where('data_state',0)
        ->where('account_type_id',3)
        ->where('company_id', Auth::user()->company_id)
        ->get();

        return view('content.ConsolidatedProfitLossYearReport.ListConsolidatedProfitLossYearReport', compact('monthlist','yearlist','month','year','income','expenditure'));
    }

    // public function getAmountAccount($account_id)
    // {
    //     if(!$month = Session::get('month')){
    //         $month = date('m');
    //     }else{
    //         $month = Session::get('month');
    //     }
    //     if(!$year = Session::get('year')){
    //         $year = date('Y');
    //     }else{
    //         $year = Session::get('year');
    //     }

    //     $journal_mi = curl_init();
    //     curl_setopt($journal_mi, CURLOPT_URL,'https://ciptapro.com/minimarket_menjanganenam/api/get-data-journal-voucher');
    //     curl_setopt($journal_mi, CURLOPT_RETURNTRANSFER, true);
    //     $response_journal_mi = curl_exec($journal_mi);
    //     $result_journal_mi = json_decode($response_journal_mi,TRUE);
    //     curl_close($journal_mi);

    //     $amount_mi = 0;
    //     $amount1_mi = 0;
    //     $amount2_mi = 0;
    //     for ($i=0; $i < count($result_journal_mi) ; $i++) { 
    //         if (($result_journal_mi[$i]['company_id'] == Auth::user()->company_id) && ($result_journal_mi[$i]['account_id'] == $account_id) && (date('m', strtotime($result_journal_mi[$i]['journal_voucher_date'])) >= 1) && (date('m', strtotime($result_journal_mi[$i]['journal_voucher_date'])) <= $month) && (date('Y', strtotime($result_journal_mi[$i]['journal_voucher_date'])) == $year)) {
    //             $data_journal_mi[$i] = $result_journal_mi[$i];
    //             $first_data_journal_mi = key($data_journal_mi);
    //             if($data_journal_mi[$i]['account_id_status'] == $data_journal_mi[$first_data_journal_mi]['account_id_status']) {
    //                 $amount1_mi += $data_journal_mi[$i]['journal_voucher_amount'];
    //             } else {
    //                 $amount2_mi += $data_journal_mi[$i]['journal_voucher_amount'];
    //             }
    //         }
    //     }
    //     $amount_mi = $amount1_mi - $amount2_mi;
        
    //     $journal_mo = curl_init();
    //     curl_setopt($journal_mo, CURLOPT_URL,'https://ciptapro.com/kasihibu_mozaic/api/get-data-journal-voucher');
    //     curl_setopt($journal_mo, CURLOPT_RETURNTRANSFER, true);
    //     $response_journal_mo = curl_exec($journal_mo);
    //     $result_journal_mo = json_decode($response_journal_mo,TRUE);
    //     curl_close($journal_mo);

    //     $amount_mo = 0;
    //     $amount1_mo = 0;
    //     $amount2_mo = 0;
    //     for ($i=0; $i < count($result_journal_mo) ; $i++) { 
    //         if (($result_journal_mo[$i]['company_id'] == Auth::user()->company_id) && ($result_journal_mo[$i]['account_id'] == $account_id) && (date('m', strtotime($result_journal_mi[$i]['journal_voucher_date'])) >= 1) && (date('m', strtotime($result_journal_mi[$i]['journal_voucher_date'])) <= $month) && (date('Y', strtotime($result_journal_mi[$i]['journal_voucher_date'])) == $year)) {
    //             $data_journal_mo[$i] = $result_journal_mo[$i];
    //             $first_data_journal_mo = key($data_journal_mo);
    //             if($data_journal_mo[$i]['account_id_status'] == $data_journal_mo[$first_data_journal_mo]['account_id_status']) {
    //                 $amount1_mo += $data_journal_mo[$i]['journal_voucher_amount'];
    //             } else {
    //                 $amount2_mo += $data_journal_mo[$i]['journal_voucher_amount'];
    //             }
    //         }
    //     }
    //     $amount_mo = $amount1_mo - $amount2_mo;

    //     return $amount_mi + $amount_mo;
    // }

    public function getAmountAccount($account_id1, $account_id2)
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

        $journal_mi = curl_init();
        curl_setopt($journal_mi, CURLOPT_URL,'https://ciptapro.com/minimarket_menjanganenam/api/get-data-journal-voucher');
        curl_setopt($journal_mi, CURLOPT_RETURNTRANSFER, true);
        $response_journal_mi = curl_exec($journal_mi);
        $result_journal_mi = json_decode($response_journal_mi,TRUE);
        curl_close($journal_mi);
        
        $journal_mo = curl_init();
        curl_setopt($journal_mo, CURLOPT_URL,'https://ciptapro.com/kasihibu_mozaic/api/get-data-journal-voucher');
        curl_setopt($journal_mo, CURLOPT_RETURNTRANSFER, true);
        $response_journal_mo = curl_exec($journal_mo);
        $result_journal_mo = json_decode($response_journal_mo,TRUE);
        curl_close($journal_mo);

        if ($account_id2 != 0) {
            $amount1_mi = 0;
            $amount2_mi = 0;
            $amount_mi = 0;
            for ($i=0; $i < count($result_journal_mi) ; $i++) { 
                if (($result_journal_mi[$i]['company_id'] == Auth::user()->company_id) && ($result_journal_mi[$i]['account_id'] == $account_id1) && (date('m', strtotime($result_journal_mi[$i]['journal_voucher_date'])) >= 1) && (date('m', strtotime($result_journal_mi[$i]['journal_voucher_date'])) <= $month) && (date('Y', strtotime($result_journal_mi[$i]['journal_voucher_date'])) == $year)) {
                    $data_journal_mi[$i] = $result_journal_mi[$i];
                    $first_data_journal_mi = key($data_journal_mi);
                    if($data_journal_mi[$i]['account_id_status'] == $data_journal_mi[$first_data_journal_mi]['account_id_status']) {
                        $amount1_mi += $data_journal_mi[$i]['journal_voucher_amount'];
                    } else {
                        $amount2_mi += $data_journal_mi[$i]['journal_voucher_amount'];
                    }
                }
                
            }
            $amount_mi = $amount1_mi - $amount2_mi;

            $amount1_mo = 0;
            $amount2_mo = 0;
            $amount_mo = 0;
            for ($i=0; $i < count($result_journal_mo) ; $i++) { 
                if (($result_journal_mo[$i]['company_id'] == Auth::user()->company_id) && ($result_journal_mo[$i]['account_id'] == $account_id2) && (date('m', strtotime($result_journal_mo[$i]['journal_voucher_date'])) >= 1) && (date('m', strtotime($result_journal_mo[$i]['journal_voucher_date'])) <= $month) && (date('Y', strtotime($result_journal_mo[$i]['journal_voucher_date'])) == $year)) {
                    $data_journal_mo[$i] = $result_journal_mo[$i];
                    $first_data_journal_mo = key($data_journal_mo);
                    if($data_journal_mo[$i]['account_id_status'] == $data_journal_mo[$first_data_journal_mo]['account_id_status']) {
                        $amount1_mo += $data_journal_mo[$i]['journal_voucher_amount'];
                    } else {
                        $amount2_mo += $data_journal_mo[$i]['journal_voucher_amount'];
                    }
                }
                
            }
            $amount_mo = $amount1_mo - $amount2_mo;

            return $amount_mi + $amount_mo;
        } else {
            $amount1_mi = 0;
            $amount2_mi = 0;
            $amount_mi = 0;
            for ($i=0; $i < count($result_journal_mi) ; $i++) { 
                if (($result_journal_mi[$i]['company_id'] == Auth::user()->company_id) && ($result_journal_mi[$i]['account_id'] == $account_id1) && (date('m', strtotime($result_journal_mi[$i]['journal_voucher_date'])) >= 1) && (date('m', strtotime($result_journal_mi[$i]['journal_voucher_date'])) <= $month) && (date('Y', strtotime($result_journal_mi[$i]['journal_voucher_date'])) == $year)) {
                    $data_journal_mi[$i] = $result_journal_mi[$i];
                    $first_data_journal_mi = key($data_journal_mi);
                    if($data_journal_mi[$i]['account_id_status'] == $data_journal_mi[$first_data_journal_mi]['account_id_status']) {
                        $amount1_mi += $data_journal_mi[$i]['journal_voucher_amount'];
                    } else {
                        $amount2_mi += $data_journal_mi[$i]['journal_voucher_amount'];
                    }
                }
                
            }
            $amount_mi = $amount1_mi - $amount2_mi;

            return $amount_mi;
        }

    }

    public function filterConsolidatedProfitLossYearReport(Request $request)
    {
        $month = $request->month;
        $year = $request->year;

        Session::put('month', $month);
        Session::put('year', $year);

        return redirect('/consolidated-profit-loss-year-report');
    }

    public function resetFilterConsolidatedProfitLossYearReport()
    {
        Session::forget('month');
        Session::forget('year');

        return redirect('/consolidated-profit-loss-year-report');
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

    public function printConsolidatedProfitLossYearReport()
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
       
        $year_now 	=	date('Y');
        for($i=($year_now-2); $i<($year_now+2); $i++){
            $yearlist[$i] = $i;
        } 

        // $profit_mi = curl_init();
        // curl_setopt($profit_mi, CURLOPT_URL,'https://ciptapro.com/minimarket_menjanganenam/api/get-data-profit-loss-report');
        // curl_setopt($profit_mi, CURLOPT_RETURNTRANSFER, true);
        // $response_profit_mi = curl_exec($profit_mi);
        // $result_profit_mi = json_decode($response_profit_mi,TRUE);
        // curl_close($profit_mi);

        // $profit_merge = [];
        // if (!empty($result_profit_mi)) {
        //     for ($i=0; $i < count($result_profit_mi) ; $i++) { 
        //         if ($result_profit_mi[$i]['company_id'] == Auth::user()->company_id) {
        //             array_push($profit_merge, $result_profit_mi[$i]);
        //         }
        //     }
        // }

        // $profit_mo = curl_init();
        // curl_setopt($profit_mo, CURLOPT_URL,'https://ciptapro.com/kasihibu_mozaic/api/get-data-profit-loss-report');
        // curl_setopt($profit_mo, CURLOPT_RETURNTRANSFER, true);
        // $response_profit_mo = curl_exec($profit_mo);
        // $result_profit_mo = json_decode($response_profit_mo,TRUE);
        // curl_close($profit_mo);

        // if (!empty($result_profit_mo)) {
        //     for ($i=0; $i < count($result_profit_mo) ; $i++) { 
        //         if ($result_profit_mo[$i]['company_id'] == Auth::user()->company_id) {
        //             array_push($profit_merge, $result_profit_mo[$i]);
        //         }
        //     }
        // }

        // $profit_unique = [];
        // for ($i=0; $i < count($profit_merge) ; $i++) { 
        //     if (!empty($result_profit_mi[$i]) || !empty($result_profit_mo[$i])) {

        //         if (($result_profit_mi[$i]['account_code'] == $result_profit_mo[$i]['account_code']) && ($result_profit_mi[$i]['account_name'] == $result_profit_mo[$i]['account_name'])) {
        //             array_push($profit_unique, $result_profit_mi[$i]);
        //         } else {
        //             array_push($profit_unique, $result_profit_mi[$i]);
        //             array_push($profit_unique, $result_profit_mo[$i]);
        //         }
        //     }
        // }


        // for ($i=0; $i < count($profit_unique) ; $i++) { 
        //     if ($profit_unique[$i]['account_type_id'] == 2) {
        //         $income[$i] = $profit_unique[$i];
        //     } 
        // }

        // for ($i=0; $i < count($profit_unique) ; $i++) { 
        //     if ($profit_unique[$i]['account_type_id'] == 3) {
        //         $expenditure[$i] = $profit_unique[$i];
        //     }
        // }
        $income = AcctProfitLossCombinedReport::where('data_state',0)
        ->where('account_type_id',2)
        ->where('company_id', Auth::user()->company_id)
        ->get();

        $expenditure = AcctProfitLossCombinedReport::where('data_state',0)
        ->where('account_type_id',3)
        ->where('company_id', Auth::user()->company_id)
        ->get();

        $pdf = new TCPDF('P', PDF_UNIT, 'F4', true, 'UTF-8', false);

        $pdf::SetPrintHeader(false);
        $pdf::SetPrintFooter(false);

        $pdf::SetMargins(40, 10, 40, 10); // put space of 10 on top

        $pdf::setImageScale(PDF_IMAGE_SCALE_RATIO);

        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf::setLanguageArray($l);
        }

        $pdf::SetFont('helvetica', 'B', 20);

        $pdf::AddPage();

        $pdf::SetFont('helvetica', '', 10);

        $tbl = "
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"0\">
            <tr>
                <td><div style=\"text-align: center; font-size:14px; font-weight: bold\">LAPORAN KONSOLIDASI PERHITUNGAN RUGI / LABA TAHUNAN</div></td>
            </tr>
            <tr>
                <td><div style=\"text-align: center; font-size:12px\">Januari - ".$this->getMonthName($month).' '. $year."</div></td>
            </tr>
            <br>
            <br>
        </table>
        ";
        $pdf::writeHTML($tbl, true, false, false, false, '');
        
        $no = 1;
        
        $tblHeader = "
			<table id=\"items\" width=\"100%\" cellspacing=\"1\" cellpadding=\"1\" border=\"0\">";
		        $tblheader_top = "
		        	<tr>
		        		<td width=\"5%\"></td>
		        		<td width=\"100%\" style=\"border-top:1px black solid;border-left:1px black solid;border-right:1px black solid\">	
			        		
		        			<table id=\"items\" width=\"100%\" cellspacing=\"1\" cellpadding=\"2\" border=\"0\">";	
			        			$tblitem_top = "";
			        			foreach ($income as $keyTop => $valTop) {
									if($valTop['report_tab'] == 0){
										$report_tab = ' ';
									} else if($valTop['report_tab'] == 1){
										$report_tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
									} else if($valTop['report_tab'] == 2){
										$report_tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
									} else if($valTop['report_tab'] == 3){
										$report_tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
									}

									if($valTop['report_bold'] == 1){
										$report_bold = 'bold';
									} else {
										$report_bold = 'normal';
									}									

									if($valTop['report_type'] == 1){
										$tblitem_top1 = "
											<tr>
												<td colspan=\"2\" style='width: 100%'><div style=\"font-weight:".$report_bold."\">".$report_tab."".$valTop['account_name']."</div></td>
											</tr>";
									} else {
										$tblitem_top1 = "";
									}


									if($valTop['report_type']	== 2){

										$tblitem_top2 = "
											<tr>
												<td style=\"width: 73%\"><div style='font-weight:".$report_bold."'>".$report_tab."".$valTop['account_name']."</div></td>
												<td style=\"width: 25%\"><div style='font-weight:".$report_bold."'></div></td>
											</tr>";
									} else {
										$tblitem_top2 = "";
									}									

									if($valTop['report_type']	== 3){
										$account_subtotal 	= $this->getAmountAccount($valTop['account_id1'], $valTop['account_id2']);

										$tblitem_top3 = "
											<tr>
												<td style=\"width: 73%\"><div style='font-weight:".$report_bold."'>".$report_tab."(".$valTop['account_code1'].") ".$valTop['account_name']."</div> </td>
												<td style=\"text-align:right;width: 25%\">".number_format($account_subtotal, 2)."</td>
											</tr>";

										$account_amount[$valTop['report_no']] = $account_subtotal;

									} else {
										$tblitem_top3 = "";
									}
									

									if($valTop['report_type'] == 5){
										if(!empty($valTop['report_formula']) && !empty($valTop['report_operator'])){
											$report_formula 	= explode('#', $valTop['report_formula']);
											$report_operator 	= explode('#', $valTop['report_operator']);

											$total_account_amount	= 0;
											for($i = 0; $i < count($report_formula); $i++){
												if($report_operator[$i] == '-'){
													if($total_account_amount == 0 ){
														$total_account_amount = $total_account_amount + $account_amount[$report_formula[$i]];
													} else {
														$total_account_amount = $total_account_amount - $account_amount[$report_formula[$i]];
													}
												} else if($report_operator[$i] == '+'){
													if($total_account_amount == 0){
														$total_account_amount = $total_account_amount + $account_amount[$report_formula[$i]];
													} else {
														$total_account_amount = $total_account_amount + $account_amount[$report_formula[$i]];
													}
												}
											}
											$tblitem_top5 = "
												<tr>
													<td><div style='font-weight:".$report_bold."'>".$report_tab."".$valTop['account_name']."</div></td>
													<td style=\"text-align:right;\"><div style='font-weight:".$report_bold."'>".number_format($total_account_amount, 2)."</div></td>
												</tr>";
										} else {
											$tblitem_top5 = "";
										}
									} else {
										$tblitem_top5 = "";
									}

									$tblitem_top .= $tblitem_top1.$tblitem_top2.$tblitem_top3.$tblitem_top5;

									if($valTop['report_type'] == 6){
										if(!empty($valTop['report_formula']) && !empty($valTop['report_operator'])){
											$report_formula 	= explode('#', $valTop['report_formula']);
											$report_operator 	= explode('#', $valTop['report_operator']);

											$grand_total_account_amount1	= 0;
											for($i = 0; $i < count($report_formula); $i++){
												if($report_operator[$i] == '-'){
													if($grand_total_account_amount1 == 0 ){
														$grand_total_account_amount1 = $grand_total_account_amount1 + $account_amount[$report_formula[$i]];
													} else {
														$grand_total_account_amount1 = $grand_total_account_amount1 - $account_amount[$report_formula[$i]];
													}
												} else if($report_operator[$i] == '+'){
													if($grand_total_account_amount1 == 0){
														$grand_total_account_amount1 = $grand_total_account_amount1 + $account_amount[$report_formula[$i]];
													} else {
														$grand_total_account_amount1 = $grand_total_account_amount1 + $account_amount[$report_formula[$i]];
													}
												}
											}
											
										} else {
											
										}
									} else {
										
									}

								}

		        $tblfooter_top	= "
		        		</table>
		        	</td>
		        	<td width=\"10%\"></td>
		        </tr>";

			       /* print_r("tblitem_top ");
			        print_r($tblitem_top);
			        exit; */

				$tblheader_bottom = "
					<tr>
						<td width=\"5%\"></td>
			        	<td width=\"100%\" style=\"border-bottom:1px black solid;border-left:1px black solid;border-right:1px black solid\">	
			        		<table id=\"items\" width=\"100%\" cellspacing=\"1\" cellpadding=\"2\" border=\"0\">";		
			        			$tblitem_bottom = "";
			        			foreach ($expenditure as $keyBottom => $valBottom) {
									if($valBottom['report_tab'] == 0){
										$report_tab = ' ';
									} else if($valBottom['report_tab'] == 1){
										$report_tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
									} else if($valBottom['report_tab'] == 2){
										$report_tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
									} else if($valBottom['report_tab'] == 3){
										$report_tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
									}

									if($valBottom['report_bold'] == 1){
										$report_bold = 'bold';
									} else {
										$report_bold = 'normal';
									}									

									if($valBottom['report_type'] == 1){
										$tblitem_bottom1 = "
											<tr>
												<td colspan=\"2\"><div style=\"font-weight:".$report_bold."\">".$report_tab."".$valBottom['account_name']."</div></td>
											</tr>";
									} else {
										$tblitem_bottom1 = "";
									}



									if($valBottom['report_type'] == 2){
										$tblitem_bottom2 = "
											<tr>
												<td style=\"width: 73%\"><div style=\"font-weight:".$report_bold."\">".$report_tab."".$valBottom['account_name']."</div></td>
												<td style=\"width: 25%\"><div style=\"font-weight:".$report_bold."\"></div></td>
											</tr>";
									} else {
										$tblitem_bottom2 = "";
									}									

									if($valBottom['report_type']	== 3){
										$account_subtotal 	= $this->getAmountAccount($valBottom['account_id1'], $valBottom['account_id2']);

										// print_r("account_subtotal ");
										// print_r($account_subtotal);
										// exit;

										$tblitem_bottom3 = "
											<tr>
												<td style=\"width: 73%\"><div style=\"font-weight:".$report_bold."\">".$report_tab."(".$valBottom['account_code1'].") ".$valBottom['account_name']."</div> </td>
												<td style=\"text-align:right;width: 25%\">".number_format($account_subtotal, 2)."</td>
											</tr>";

										$account_amount[$valBottom['report_no']] = $account_subtotal;

									} else {
										$tblitem_bottom3 = "";
									}
									

									if($valBottom['report_type'] == 5){
										if(!empty($valBottom['report_formula']) && !empty($valBottom['report_operator'])){
											$report_formula 	= explode('#', $valBottom['report_formula']);
											$report_operator 	= explode('#', $valBottom['report_operator']);

											$total_account_amount2	= 0;
											for($i = 0; $i < count($report_formula); $i++){
												if($report_operator[$i] == '-'){
													if($total_account_amount2 == 0 ){
														$total_account_amount2 = $total_account_amount2 + $account_amount[$report_formula[$i]];
													} else {
														$total_account_amount2 = $total_account_amount2 - $account_amount[$report_formula[$i]];
													}
												} else if($report_operator[$i] == '+'){
													if($total_account_amount2 == 0){
														$total_account_amount2 = $total_account_amount2 + $account_amount[$report_formula[$i]];
													} else {
														$total_account_amount2 = $total_account_amount2 + $account_amount[$report_formula[$i]];
													}
												}
											}
											$tblitem_bottom5 = "
												<tr>
													<td><div style=\"font-weight:".$report_bold."\">".$report_tab."".$valBottom['account_name']."</div></td>
													<td style=\"text-align:righr;\"><div style=\"font-weight:".$report_bold."\">".number_format($total_account_amount2, 2)."</div></td>
												</tr>";
										} else {
											$tblitem_bottom5 = "";
										}
									} else {
										$tblitem_bottom5 = "";
									}

									$tblitem_bottom .= $tblitem_bottom1.$tblitem_bottom2.$tblitem_bottom3.$tblitem_bottom5;


									if($valBottom['report_type'] == 6){
										if(!empty($valBottom['report_formula']) && !empty($valBottom['report_operator'])){
											$report_formula 	= explode('#', $valBottom['report_formula']);
											$report_operator 	= explode('#', $valBottom['report_operator']);

											$grand_total_account_amount2	= 0;
											for($i = 0; $i < count($report_formula); $i++){
												if($report_operator[$i] == '-'){
													if($grand_total_account_amount2 == 0 ){
														$grand_total_account_amount2 = $grand_total_account_amount2 + $account_amount[$report_formula[$i]];
													} else {
														$grand_total_account_amount2 = $grand_total_account_amount2 - $account_amount[$report_formula[$i]];
													}
												} else if($report_operator[$i] == '+'){
													if($grand_total_account_amount2 == 0){
														$grand_total_account_amount2 = $grand_total_account_amount2 + $account_amount[$report_formula[$i]];
													} else {
														$grand_total_account_amount2 = $grand_total_account_amount2 + $account_amount[$report_formula[$i]];
													}
												}
											}
										} else {
											
										}
									} else {
										
									}

								}
								// exit;

		       	$tblfooter_bottom = "
		       			</table>
		        	</td>
		        	<td width=\"5%\"></td>
		        </tr>";


			        $shu = $grand_total_account_amount1 - $grand_total_account_amount2;

			$tblFooter = "
			   
			    <tr>
			    	<td width=\"5%\"></td>
			    	<td style=\"border:1px black solid;\">
			    		<table id=\"items\" width=\"100%\" cellspacing=\"1\" cellpadding=\"2\" border=\"0\">
							<tr>
								<td style=\"width: 75%\"><div style=\"font-weight:bold;font-size:14px\">RUGI / LABA</div></td>
								<td style=\"width: 23%; text-align:right;\"><div style=\"font-weight:bold; font-size:14px\">".number_format($shu, 2)."</div></td>
							</tr>
			    		</table>
			    	</td>
			    	<td width=\"10%\"></td>
			    </tr>
			</table>";

        $pdf::writeHTML($tblHeader.$tblheader_top.$tblitem_top.$tblfooter_top.$tblheader_bottom.$tblitem_bottom.$tblfooter_bottom.$tblFooter, true, false, false, false, '');


        $filename = 'Laporan_Konsolidasi_Rugi_Laba_1_'.$month.'_'.$year.'.pdf';
        $pdf::Output($filename, 'I');
    }

    public function exportConsolidatedProfitLossYearReport()
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

        $year_now 	=	date('Y');
        for($i=($year_now-2); $i<($year_now+2); $i++){
            $yearlist[$i] = $i;
        } 

        
        // $profit_mi = curl_init();
        // curl_setopt($profit_mi, CURLOPT_URL,'https://ciptapro.com/minimarket_menjanganenam/api/get-data-profit-loss-report');
        // curl_setopt($profit_mi, CURLOPT_RETURNTRANSFER, true);
        // $response_profit_mi = curl_exec($profit_mi);
        // $result_profit_mi = json_decode($response_profit_mi,TRUE);
        // curl_close($profit_mi);

        // $profit_merge = [];
        // if (!empty($result_profit_mi)) {
        //     for ($i=0; $i < count($result_profit_mi) ; $i++) { 
        //         if ($result_profit_mi[$i]['company_id'] == Auth::user()->company_id) {
        //             array_push($profit_merge, $result_profit_mi[$i]);
        //         }
        //     }
        // }

        // $profit_mo = curl_init();
        // curl_setopt($profit_mo, CURLOPT_URL,'https://ciptapro.com/kasihibu_mozaic/api/get-data-profit-loss-report');
        // curl_setopt($profit_mo, CURLOPT_RETURNTRANSFER, true);
        // $response_profit_mo = curl_exec($profit_mo);
        // $result_profit_mo = json_decode($response_profit_mo,TRUE);
        // curl_close($profit_mo);

        // if (!empty($result_profit_mo)) {
        //     for ($i=0; $i < count($result_profit_mo) ; $i++) { 
        //         if ($result_profit_mo[$i]['company_id'] == Auth::user()->company_id) {
        //             array_push($profit_merge, $result_profit_mo[$i]);
        //         }
        //     }
        // }

        // $profit_unique = [];
        // for ($i=0; $i < count($profit_merge) ; $i++) { 
        //     if (!empty($result_profit_mi[$i]) || !empty($result_profit_mo[$i])) {

        //         if (($result_profit_mi[$i]['account_code'] == $result_profit_mo[$i]['account_code']) && ($result_profit_mi[$i]['account_name'] == $result_profit_mo[$i]['account_name'])) {
        //             array_push($profit_unique, $result_profit_mi[$i]);
        //         } else {
        //             array_push($profit_unique, $result_profit_mi[$i]);
        //             array_push($profit_unique, $result_profit_mo[$i]);
        //         }
        //     }
        // }


        // for ($i=0; $i < count($profit_unique) ; $i++) { 
        //     if ($profit_unique[$i]['account_type_id'] == 2) {
        //         $income[$i] = $profit_unique[$i];
        //     } 
        // }

        // for ($i=0; $i < count($profit_unique) ; $i++) { 
        //     if ($profit_unique[$i]['account_type_id'] == 3) {
        //         $expenditure[$i] = $profit_unique[$i];
        //     }
        // }
        $income = AcctProfitLossCombinedReport::where('data_state',0)
        ->where('account_type_id',2)
        ->where('company_id', Auth::user()->company_id)
        ->get();

        $expenditure = AcctProfitLossCombinedReport::where('data_state',0)
        ->where('account_type_id',3)
        ->where('company_id', Auth::user()->company_id)
        ->get();

        $spreadsheet = new Spreadsheet();

        // if(!empty($sales_invoice || $purchase_invoice || $expenditure)){
        $spreadsheet->getProperties()->setCreator("MOZAIC")
                                    ->setLastModifiedBy("MOZAIC")
                                    ->setTitle("Profit Loss Year Report")
                                    ->setSubject("")
                                    ->setDescription("Profit Loss Year Report")
                                    ->setKeywords("Profit, Loss, Year, Report")
                                    ->setCategory("Profit Loss Year Report");
                                
        $sheet = $spreadsheet->getActiveSheet(0);
        $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
        $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(40);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(25);

        $spreadsheet->getActiveSheet()->mergeCells("B1:C1");
        $spreadsheet->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
        $spreadsheet->getActiveSheet()->mergeCells("B2:C2");
        $spreadsheet->getActiveSheet()->getStyle('B2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('B1',"LAPORAN KONSOLIDASI RUGI LABA TAHUNAN");	
        $sheet->setCellValue('B2', 'Januari - '.$this->getMonthName($month).' '. $year);

        $j = 4;

        foreach($income as $keyTop => $valTop){
            if(is_numeric($keyTop)){
                
                $spreadsheet->setActiveSheetIndex(0);
                $spreadsheet->getActiveSheet()->getStyle('B'.$j.':C'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        
                $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $spreadsheet->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                

                if($valTop['report_tab'] == 0){
                    $report_tab = ' ';
                } else if($valTop['report_tab'] == 1){
                    $report_tab = '     ';
                } else if($valTop['report_tab'] == 2){
                    $report_tab = '          ';
                } else if($valTop['report_tab'] == 3){
                    $report_tab = '               ';
                }

                if($valTop['report_bold'] == 1){
                    $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getFont()->setBold(true);	
                    $spreadsheet->getActiveSheet()->getStyle('C'.$j)->getFont()->setBold(true);	
                } else {
                
                }

                if($valTop['report_type'] == 1){
                    $spreadsheet->getActiveSheet()->mergeCells("B".$j.":C".$j."");
                    $spreadsheet->getActiveSheet()->setCellValue('B'.$j, $valTop['account_name']);

                    $j++;
                }
                    
                
                if($valTop['report_type']	== 2){
                    $spreadsheet->getActiveSheet()->setCellValue('B'.$j, $valTop['account_name']);

                    $j++;
                }
                        

                if($valTop['report_type']	== 3){
                    $account_subtotal 	= $this->getAmountAccount($valTop['account_id1'], $valTop['account_id2']);

                    $spreadsheet->getActiveSheet()->setCellValue('B'.$j, $report_tab.$valTop['account_name']);
                    $spreadsheet->getActiveSheet()->setCellValue('C'.$j, $report_tab.$account_subtotal);

                    $account_amount[$valTop['report_no']] = $account_subtotal;

                    $j++;
                }


                if($valTop['report_type'] == 5){
                    if(!empty($valTop['report_formula']) && !empty($valTop['report_operator'])){
                        $report_formula 	= explode('#', $valTop['report_formula']);
                        $report_operator 	= explode('#', $valTop['report_operator']);

                        $total_account_amount	= 0;
                        for($i = 0; $i < count($report_formula); $i++){
                            if($report_operator[$i] == '-'){
                                if($total_account_amount == 0 ){
                                    $total_account_amount = $total_account_amount + $account_amount[$report_formula[$i]];
                                } else {
                                    $total_account_amount = $total_account_amount - $account_amount[$report_formula[$i]];
                                }
                            } else if($report_operator[$i] == '+'){
                                if($total_account_amount == 0){
                                    $total_account_amount = $total_account_amount + $account_amount[$report_formula[$i]];
                                } else {
                                    $total_account_amount = $total_account_amount + $account_amount[$report_formula[$i]];
                                }
                            }
                        }

                        $spreadsheet->getActiveSheet()->setCellValue('B'.$j, $report_tab.$valTop['account_name']);
                        $spreadsheet->getActiveSheet()->setCellValue('C'.$j, $report_tab.$total_account_amount);

                        $j++;
                    }
                }

                if($valTop['report_type'] == 6){
                    if(!empty($valTop['report_formula']) && !empty($valTop['report_operator'])){
                        $report_formula 	= explode('#', $valTop['report_formula']);
                        $report_operator 	= explode('#', $valTop['report_operator']);

                        $grand_total_account_amount1	= 0;
                        for($i = 0; $i < count($report_formula); $i++){
                            if($report_operator[$i] == '-'){
                                if($grand_total_account_amount1 == 0 ){
                                    $grand_total_account_amount1 = $grand_total_account_amount1 + $account_amount[$report_formula[$i]];
                                } else {
                                    $grand_total_account_amount1 = $grand_total_account_amount1 - $account_amount[$report_formula[$i]];
                                }
                            } else if($report_operator[$i] == '+'){
                                if($grand_total_account_amount1 == 0){
                                    $grand_total_account_amount1 = $grand_total_account_amount1 + $account_amount[$report_formula[$i]];
                                } else {
                                    $grand_total_account_amount1 = $grand_total_account_amount1 + $account_amount[$report_formula[$i]];
                                }
                            }
                        }

                        $spreadsheet->getActiveSheet()->setCellValue('B'.$j, $report_tab.$valTop['account_name']);
                        $spreadsheet->getActiveSheet()->setCellValue('C'.$j, $report_tab.$grand_total_account_amount1);

                        $j++;
                    }

                }
                        

            }else{
                continue;
            }

            
        }

        // $j--;

        foreach($expenditure as $keyBottom => $valBottom){
            if(is_numeric($keyTop)){
                
                $spreadsheet->setActiveSheetIndex(0);
                $spreadsheet->getActiveSheet()->getStyle('B'.$j.':C'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        
                $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $spreadsheet->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                

                if($valBottom['report_tab'] == 0){
                    $report_tab = ' ';
                } else if($valBottom['report_tab'] == 1){
                    $report_tab = '     ';
                } else if($valBottom['report_tab'] == 2){
                    $report_tab = '          ';
                } else if($valBottom['report_tab'] == 3){
                    $report_tab = '               ';
                }

                if($valBottom['report_bold'] == 1){
                    $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getFont()->setBold(true);	
                    $spreadsheet->getActiveSheet()->getStyle('C'.$j)->getFont()->setBold(true);	
                } else {
                
                }

                if($valBottom['report_type'] == 1){
                    $spreadsheet->getActiveSheet()->mergeCells("B".$j.":C".$j."");
                    $spreadsheet->getActiveSheet()->setCellValue('B'.$j, $valBottom['account_name']);
                }
                    
                
                if($valBottom['report_type']	== 2){
                    $spreadsheet->getActiveSheet()->setCellValue('B'.$j, $valBottom['account_name']);
                }
                        

                if($valBottom['report_type']	== 3){
                    $account_subtotal 	= $this->getAmountAccount($valBottom['account_id1'], $valBottom['account_id2']);

                    $spreadsheet->getActiveSheet()->setCellValue('B'.$j, $report_tab.$valBottom['account_name']);
                    $spreadsheet->getActiveSheet()->setCellValue('C'.$j, $report_tab.$account_subtotal);

                    $account_amount[$valBottom['report_no']] = $account_subtotal;
                }


                if($valBottom['report_type'] == 5){
                    if(!empty($valBottom['report_formula']) && !empty($valBottom['report_operator'])){
                        $report_formula 	= explode('#', $valBottom['report_formula']);
                        $report_operator 	= explode('#', $valBottom['report_operator']);

                        $total_account_amount	= 0;
                        for($i = 0; $i < count($report_formula); $i++){
                            if($report_operator[$i] == '-'){
                                if($total_account_amount == 0 ){
                                    $total_account_amount = $total_account_amount + $account_amount[$report_formula[$i]];
                                } else {
                                    $total_account_amount = $total_account_amount - $account_amount[$report_formula[$i]];
                                }
                            } else if($report_operator[$i] == '+'){
                                if($total_account_amount == 0){
                                    $total_account_amount = $total_account_amount + $account_amount[$report_formula[$i]];
                                } else {
                                    $total_account_amount = $total_account_amount + $account_amount[$report_formula[$i]];
                                }
                            }
                        }

                        $spreadsheet->getActiveSheet()->setCellValue('B'.$j, $report_tab.$valBottom['account_name']);
                        $spreadsheet->getActiveSheet()->setCellValue('C'.$j, $report_tab.$total_account_amount);
                    }
                }

                if($valBottom['report_type'] == 6){
                    if(!empty($valBottom['report_formula']) && !empty($valBottom['report_operator'])){
                        $report_formula 	= explode('#', $valBottom['report_formula']);
                        $report_operator 	= explode('#', $valBottom['report_operator']);

                        $grand_total_account_amount2	= 0;
                        for($i = 0; $i < count($report_formula); $i++){
                            if($report_operator[$i] == '-'){
                                if($grand_total_account_amount2 == 0 ){
                                    $grand_total_account_amount2 = $grand_total_account_amount2 + $account_amount[$report_formula[$i]];
                                } else {
                                    $grand_total_account_amount2 = $grand_total_account_amount2 - $account_amount[$report_formula[$i]];
                                }
                            } else if($report_operator[$i] == '+'){
                                if($grand_total_account_amount2 == 0){
                                    $grand_total_account_amount2 = $grand_total_account_amount2 + $account_amount[$report_formula[$i]];
                                } else {
                                    $grand_total_account_amount2 = $grand_total_account_amount2 + $account_amount[$report_formula[$i]];
                                }
                            }
                        }

                        $spreadsheet->getActiveSheet()->setCellValue('B'.$j, $report_tab.$valBottom['account_name']);
                        $spreadsheet->getActiveSheet()->setCellValue('C'.$j, $report_tab.$grand_total_account_amount2);
                    }

                }
                        

            }else{
                continue;
            }

            $j++;
        }

        $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $spreadsheet->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

        $spreadsheet->getActiveSheet()->getStyle('B'.$j.':C'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $spreadsheet->getActiveSheet()->getStyle("B".($j).":C".$j)->getFont()->setBold(true);	

        $shu = $grand_total_account_amount1 - $grand_total_account_amount2;

        $spreadsheet->getActiveSheet()->setCellValue('B'.($j), "RUGI / LABA");
        $spreadsheet->getActiveSheet()->setCellValue('C'.($j), $shu);

        
        $filename='Laporan_Konsolidasi_Rugi_Laba_01_'.$month.'_'.$year.'.xls';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
        $writer->save('php://output');
    }
}
