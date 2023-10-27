<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AcctAccount;
use App\Models\AcctProfitLossReport;
use App\Models\Expenditure;
use App\Models\JournalVoucher;
use App\Models\PurchaseInvoice;
use App\Models\SalesInvoice;
use Elibyy\TCPDF\Facades\TCPDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class AcctProfitLossReportController extends Controller
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

        $income = AcctProfitLossReport::select('report_tab','report_bold','report_type','account_name','account_id','account_code','report_no','report_formula','report_operator')
        ->where('data_state',0)
        ->where('account_type_id',2)
        ->where('company_id', Auth::user()->company_id)
        ->get();

        $expenditure = AcctProfitLossReport::select('report_tab','report_bold','report_type','account_name','account_id','account_code','report_no','report_formula','report_operator')
        ->where('data_state',0)
        ->where('account_type_id',3)
        ->where('company_id', Auth::user()->company_id)
        ->get();

        return view('content.AcctProfitLossReport.ListAcctProfitLossReport',compact('start_date','end_date','income','expenditure'));
    }

    public function filterProfitLossReport(Request $request)
    {
        $start_date = $request->start_date;
        $end_date   = $request->end_date;

        Session::put('start_date', $start_date);
        Session::put('end_date', $end_date);

        return redirect('/profit-loss-report');
    }

    public function resetFilterProfitLossReport()
    {
        Session::forget('start_date');
        Session::forget('end_date');

        return redirect('/profit-loss-report');
    }

    public function getAmountAccount($account_id)
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

        $data = JournalVoucher::join('acct_journal_voucher_item','acct_journal_voucher_item.journal_voucher_id','acct_journal_voucher.journal_voucher_id')
        ->select('acct_journal_voucher_item.journal_voucher_amount','acct_journal_voucher_item.account_id_status')
        ->where('acct_journal_voucher.journal_voucher_date', '>=', $start_date)
        ->where('acct_journal_voucher.journal_voucher_date', '<=', $end_date)
        ->where('acct_journal_voucher.data_state',0)
        ->where('acct_journal_voucher_item.account_id', $account_id)
        ->where('acct_journal_voucher.company_id', Auth::user()->company_id)
        ->get();
        $data_first = JournalVoucher::join('acct_journal_voucher_item','acct_journal_voucher_item.journal_voucher_id','acct_journal_voucher.journal_voucher_id')
        ->select('acct_journal_voucher_item.account_id_status')
        ->where('acct_journal_voucher.journal_voucher_date', '>=', $start_date)
        ->where('acct_journal_voucher.journal_voucher_date', '<=', $end_date)
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

    public function printProfitLossReport()
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

        $income = AcctProfitLossReport::select('report_tab','report_bold','report_type','account_name','account_id','account_code','report_no','report_formula','report_operator')
        ->where('data_state',0)
        ->where('account_type_id',2)
        ->where('company_id', Auth::user()->company_id)
        ->get();

        $expenditure = AcctProfitLossReport::select('report_tab','report_bold','report_type','account_name','account_id','account_code','report_no','report_formula','report_operator')
        ->where('data_state',0)
        ->where('account_type_id',3)
        ->where('company_id', Auth::user()->company_id)
        ->get();

        $pdf = new TCPDF('P', PDF_UNIT, 'F4', true, 'UTF-8', false);

        $pdf::SetPrintHeader(false);
        $pdf::SetPrintFooter(false);

        $pdf::SetMargins(30, 10, 40, 10); // put space of 10 on top

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
                <td><div style=\"text-align: center; font-size:14px; font-weight: bold\">LAPORAN RUGI LABA</div></td>
            </tr>
            <tr>
                <td><div style=\"text-align: center; font-size:12px\">PERIODE : ".date('d M Y', strtotime($start_date))." s.d. ".date('d M Y', strtotime($end_date))."</div></td>
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
										$account_subtotal 	= $this->getAmountAccount($valTop['account_id']);

										$tblitem_top3 = "
											<tr>
												<td style=\"width: 73%\"><div style='font-weight:".$report_bold."'>".$report_tab."(".$valTop['account_code'].") ".$valTop['account_name']."</div> </td>
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
										$account_subtotal 	= $this->getAmountAccount($valBottom['account_id']);

										// print_r("account_subtotal ");
										// print_r($account_subtotal);
										// exit;

										$tblitem_bottom3 = "
											<tr>
												<td style=\"width: 73%\"><div style=\"font-weight:".$report_bold."\">".$report_tab."(".$valBottom['account_code'].") ".$valBottom['account_name']."</div> </td>
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
			</table>
            <table cellspacing=\"0\" cellpadding=\"2\" border=\"0\">
                <tr>
                    <td style=\"text-align:right\">".Auth::user()->name.", ".date('d-m-Y H:i')."</td>
                </tr>
            </table>";

        $pdf::writeHTML($tblHeader.$tblheader_top.$tblitem_top.$tblfooter_top.$tblheader_bottom.$tblitem_bottom.$tblfooter_bottom.$tblFooter, true, false, false, false, '');

        $filename = 'Laporan_Rugi_Laba_'.$start_date.'s.d.'.$end_date.'.pdf';
        $pdf::Output($filename, 'I');
    }

    public function exportProfitLossReport()
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
       
        $income = AcctProfitLossReport::select('report_tab','report_bold','report_type','account_name','account_id','account_code','report_no','report_formula','report_operator')
        ->where('data_state',0)
        ->where('account_type_id',2)
        ->where('company_id', Auth::user()->company_id)
        ->get();

        $expenditure = AcctProfitLossReport::select('report_tab','report_bold','report_type','account_name','account_id','account_code','report_no','report_formula','report_operator')
        ->where('data_state',0)
        ->where('account_type_id',3)
        ->where('company_id', Auth::user()->company_id)
        ->get();

        $spreadsheet = new Spreadsheet();

        // if(!empty($sales_invoice || $purchase_invoice || $expenditure)){
            $spreadsheet->getProperties()->setCreator("MOZAIC")
                                        ->setLastModifiedBy("MOZAIC")
                                        ->setTitle("Profit Loss Report")
                                        ->setSubject("")
                                        ->setDescription("Profit Loss Report")
                                        ->setKeywords("Profit, Loss, Report")
                                        ->setCategory("Profit Loss Report");
                                 
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

            $sheet->setCellValue('B1',"LAPORAN RUGI LABA");	
            $sheet->setCellValue('B2', 'Period '.date('d M Y', strtotime($start_date))." s.d. ".date('d M Y', strtotime($end_date)));
            $j = 4;

            foreach($income as $keyTop => $valTop){
                if(is_numeric($keyTop)){
                    
                    $spreadsheet->setActiveSheetIndex(0);
                    $spreadsheet->getActiveSheet()->getStyle('B'.$j.':C'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    $spreadsheet->getActiveSheet()->getStyle('C'.$j)->getNumberFormat()->setFormatCode('0.00');
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
                        $account_subtotal 	= $this->getAmountAccount($valTop['account_id']);

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
                        $account_subtotal 	= $this->getAmountAccount($valBottom['account_id']);

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
            $j++;
            $spreadsheet->getActiveSheet()->mergeCells('B'.$j.':C'.$j);
            $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('B'.$j, Auth::user()->name.", ".date('d-m-Y H:i'));

            
            $filename='Laporan_Rugi_Laba_'.$start_date.'_s.d._'.$end_date.'.xls';
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="'.$filename.'"');
            header('Cache-Control: max-age=0');

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
            $writer->save('php://output');
        // }else{
        //     echo "Maaf data yang di eksport tidak ada !";
        // }
    }

}
