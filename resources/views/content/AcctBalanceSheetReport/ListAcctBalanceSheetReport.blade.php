@inject('ABSR','App\Http\Controllers\AcctBalanceSheetReportController')
@extends('adminlte::page')

@section('title', 'MOZAIC Minimarket')

@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
      <li class="breadcrumb-item active" aria-current="page">Laporan Neraca </li>
    </ol>
  </nav>

@stop

@section('content')
<h3 class="page-title">
    <b>Laporan Neraca</b>
</h3>
<br/>
<div id="accordion">
    <form action="{{ route('filter-balance-sheet-report') }}" method="post">
        @csrf
        <div class="card border border-dark">
            <div class="card-header bg-dark" id="headingOne" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                <h5 class="mb-0">
                    Filter
                </h5>
            </div>
            <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                <div class="card-body">
                    <div class="row">
                        <div class = "col-md-6">
                            <div class="form-group form-md-line-input" style="width: 50%">
                                <section class="control-label">Bulan
                                    <span class="required text-danger">
                                        *
                                    </span>
                                </section>
                                {!! Form::select(0, $monthlist, $month,['class' => 'selection-search-clear select-form','name'=>'month','id'=>'month']) !!}
                            </div>
                        </div>

                        <div class = "col-md-6">
                            <div class="form-group form-md-line-input" style="width: 50%">
                                <section class="control-label">Tahun
                                    <span class="required text-danger">
                                        *
                                    </span>
                                </section>
                                {!! Form::select(0, $yearlist, $year,['class' => 'selection-search-clear select-form','name'=>'year','id'=>'year']) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-muted">
                    <div class="form-actions float-right">
                        <a href="{{ route('reset-filter-balance-sheet-report') }}" type="reset" name="Reset" class="btn btn-danger"><i class="fa fa-times"></i> Batal</a>
                        <button type="submit" name="Find" class="btn btn-primary" title="Search Data"><i class="fa fa-search"></i> Cari</button>
                    </div>
                </div>
            </div>
        </div>

    </form>
</div>
<br/>
@if(session('msg'))
<div class="alert alert-info" role="alert">
    {{session('msg')}}
</div>
@endif 
<div class="card border border-dark">
  <div class="card-header bg-dark clearfix">
    <h5 class="mb-0 float-left">
        Daftar
    </h5>
  </div>

    <div class="card-body">
        <div class="table-responsive pt-5">
            <table id="" style="width:100%" class="table table-bordered table-full-width">
                <thead>
                    <tr>
                        <td colspan='2' style='text-align:center;'>
                            <div style='font-weight:bold'>Laporan Neraca
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan='2' style='text-align:center;'>
                            <div>
                                Periode {{ $ABSR->getMonthName($month) }} {{ $year }}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan='2'></td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style='width: 50%'>
                            <table class="table table-bordered table-advance table-hover">
                                <?php
                                $grand_total_account_amount1 = 0;
                                $grand_total_account_amount2 = 0;
                                    foreach ($acctbalancesheetreport_left as $key => $val) {
                                        if($val['report_tab1'] == 0){
                                            $report_tab1 = ' ';
                                        } else if($val['report_tab1'] == 1){
                                            $report_tab1 = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                                        } else if($val['report_tab1'] == 2){
                                            $report_tab1 = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                                        } else if($val['report_tab1'] == 3){
                                            $report_tab1 = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                                        }

                                        if($val['report_bold1'] == 1){
                                            $report_bold1 = 'bold';
                                        } else {
                                            $report_bold1 = 'normal';
                                        }

                                        echo "
                                            <tr>";

                                            if($val['report_type1'] == 1){
                                                echo "
                                                    <td colspan='2'><div style='font-weight:".$report_bold1."'>".$report_tab1."".$val['account_name1']."</div></td>
                                                    ";
                                            }
                                            
                                        echo "
                                            </tr>";

                                        echo "
                                            <tr>";

                                            if($val['report_type1']	== 2){
                                                echo "
                                                    <td style='width: 75%'><div style='font-weight:".$report_bold1."'>".$report_tab1."".$val['account_name1']."</div></td>
                                                    <td style='width: 25%'><div style='font-weight:".$report_bold1."'></div></td>
                                                    ";
                                            }
                                                
                                        echo "
                                            </tr>";

                                        echo "
                                            <tr>";

                                            if($val['report_type1']	== 3){
                                                $last_balance1 	= $ABSR->getAmountAccount($val['account_id1']);
                                                // print_r($last_balance1);

                                                echo "
                                                    <td><div style='font-weight:".$report_bold1."'>".$report_tab1."(".$val['account_code1'].") ".$val['account_name1']."</div> </td>
                                                    <td style='text-align:right'><div style='font-weight:".$report_bold1."'>".number_format($last_balance1, 2)."</div></td>
                                                ";

                                                $account_amount1_top[$val['report_no']] = $last_balance1;
                                            }

                                            
                                                
                                        echo "
                                            </tr>";

                                        echo "
                                            <tr>";
                                            if($val['report_type1']	== 10){
                                                $last_balance10 	= $ABSR->getAmountAccount($val['account_id1']);

                                                $account_amount10_top[$val['report_no']] = $last_balance10;
                                            }	
                                        echo "
                                            </tr>";
                                        echo "
                                            <tr>";

                                            if($val['report_type1'] == 11){
                                                if(!empty($val['report_formula1']) && !empty($val['report_operator1'])){
                                                    $report_formula1 	= explode('#', $val['report_formula1']);
                                                    $report_operator1 	= explode('#', $val['report_operator1']);

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

                                                    echo "
                                                        <td><div style='font-weight:".$report_bold1."'>".$report_tab1."".$val['account_name1']."</div></td>
                                                        <td style='text-align:right'><div style='font-weight:".$report_bold1."'>".number_format($total_account_amount10, 2)."</div></td>
                                                        ";
                                                }
                                                
                                            }

                                        echo "			
                                            </tr>";

                                        echo "
                                            <tr>";

                                            if($val['report_type1']	== 7){
                                                $last_balance1 	= $ABSR->getAmountAccount($val['account_id1']);
                                                // print_r($last_balance1);
                            

                                                echo "
                                                    <td><div style='font-weight:".$report_bold1."'>".$report_tab1."(".$val['account_code1'].") ".$val['account_name1']."</div> </td>
                                                    <td style='text-align:right'><div style='font-weight:".$report_bold1."'>( ".number_format($last_balance1, 2)." )</div></td>
                                                ";

                                                $account_amount1_top[$val['report_no']] = $last_balance1;
                                            }

                                            
                                                
                                        echo "
                                            </tr>";

                                            // print_r($account_amount1_top[57]);

                                        echo "
                                            <tr>";

                                            if($val['report_type1'] == 5){
                                                if(!empty($val['report_formula1']) && !empty($val['report_operator1'])){
                                                    $report_formula1 	= explode('#', $val['report_formula1']);
                                                    $report_operator1 	= explode('#', $val['report_operator1']);

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

                                                    // print_r("total_amount1 ".$total_account_amount1." ");
                                                    

                                                    

                                                    // echo "
                                                    //     <td><div style='font-weight:".$report_bold1."'>".$report_tab1."".$val['account_name1']."</div></td>
                                                    //     <td style='text-align:right'><div style='font-weight:".$report_bold1."'>".number_format($total_account_amount1+$total_account_amount10, 2)."</div></td>
                                                    //     ";
                                                    echo "
                                                        <td><div style='font-weight:".$report_bold1."'>".$report_tab1."".$val['account_name1']."</div></td>
                                                        <td style='text-align:right'><div style='font-weight:".$report_bold1."'>".number_format($total_account_amount1, 2)."</div></td>
                                                        ";
                                                }
                                                
                                            }

                                        echo "			
                                            </tr>";

                                            

                                        echo "
                                            <tr>";

                                            if($val['report_type1'] == 6){

                                                if(!empty($val['report_formula1']) && !empty($val['report_operator1'])){

                                                    $grand_total_account_name1 = $val['account_name1'];
                                                }
                                            }

                                        echo "			
                                            </tr>";	

                                    }
                                ?>
                            </table>
                        </td>
                        <td style='width: 50%'>
                            <table class="table table-bordered table-advance table-hover">
                                <?php
                                    foreach ($acctbalancesheetreport_right as $key => $val) {
                                        if($val['report_tab2'] == 0){
                                            $report_tab2 = ' ';
                                        } else if($val['report_tab2'] == 1){
                                            $report_tab2 = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                                        } else if($val['report_tab2'] == 2){
                                            $report_tab2 = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                                        } else if($val['report_tab2'] == 3){
                                            $report_tab2 = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                                        }

                                        if($val['report_bold2'] == 1){
                                            $report_bold2 = 'bold';
                                        } else {
                                            $report_bold2 = 'normal';
                                        }

                                        echo "
                                            <tr>";

                                            if($val['report_type2'] == 1){
                                                echo "
                                                    <td colspan='2'><div style='font-weight:".$report_bold2."'>".$report_tab2."".$val['account_name2']."</div></td>
                                                    ";
                                            }
                                            
                                        echo "
                                            </tr>";

                                        echo "
                                            <tr>";

                                            if($val['report_type2']	== 2){
                                                echo "
                                                    <td style='width: 75%'><div style='font-weight:".$report_bold2."'>".$report_tab2."".$val['account_name2']."</div></td>
                                                    <td style='width: 25%'><div style='font-weight:".$report_bold2."'></div></td>
                                                    ";
                                            }
                                                
                                        echo "
                                            </tr>";

                                        echo "
                                            <tr>";

                                            if($val['report_type2']	== 3){
                                                
                                                $last_balance2 	= $ABSR->getAmountAccount($val['account_id2']);
                                                // print_r($last_balance2);

                                                echo "
                                                    <td><div style='font-weight:".$report_bold2."'>".$report_tab2."(".$val['account_code2'].") ".$val['account_name2']."</div> </td>
                                                    <td style='text-align:right'><div style='font-weight:".$report_bold2."'>".number_format($last_balance2, 2)."</div></td>
                                                ";

                                                $account_amount2_bottom[$val['report_no']] = $last_balance2;
                                            }

                                            
                                                
                                        echo "
                                            </tr>";

                                            echo "
                                                <tr>";
                                                if($val['report_type2']	== 10){
                                                    $last_balance210 	= $ABSR->getAmountAccount($val['account_id2']);

                                                    $account_amount210_top[$val['report_no']] = $last_balance210;
                                                }	
                                            echo "
                                                </tr>";
                                            echo "
                                                <tr>";

                                                if($val['report_type2'] == 11){
                                                    if(!empty($val['report_formula2']) && !empty($val['report_operator2'])){
                                                        $report_formula2 	= explode('#', $val['report_formula2']);
                                                        $report_operator2 	= explode('#', $val['report_operator2']);

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

                                                        $grand_total_account_amount2 = $grand_total_account_amount2 + $total_account_amount210;

                                                        echo "
                                                            <td><div style='font-weight:".$report_bold2."'>".$report_tab2."".$val['account_name2']."</div></td>
                                                            <td style='text-align:right'><div style='font-weight:".$report_bold2."'>".number_format($total_account_amount210, 2)."</div></td>
                                                            ";
                                                    }
                                                    
                                                }

                                            echo "			
                                                </tr>";

                                        echo "
                                            <tr>";

                                            if($val['report_type2']	==8){
                                                $sahu_tahun_lalu = $this->AcctBalanceSheetReportNew1_model->getSHUTahunLalu($data['branch_id'], $month, $year);

                                                if(empty($sahu_tahun_lalu)){
                                                    $sahu_tahun_lalu = 0;
                                                }

                                                echo "
                                                    <td><div style='font-weight:".$report_bold2."'>".$report_tab2."(".$val['account_code2'].") ".$val['account_name2']."</div> </td>
                                                    <td style='text-align:right'><div style='font-weight:".$report_bold2."'>".number_format($sahu_tahun_lalu, 2)."</div></td>
                                                ";

                                                $account_amount2_bottom[$val['report_no']] = $sahu_tahun_lalu;
                                            }
                                                
                                        echo "
                                            </tr>";

                                        echo "
                                            <tr>";

                                            if($val['report_type2']	== 7){
                                                $profit_loss = $this->AcctBalanceSheetReportNew1_model->getProfitLossAmount($data['branch_id'], $month, $year);

                                                if(empty($profit_loss)){
                                                    $profit_loss = 0;
                                                }

                                                echo "
                                                    <td><div style='font-weight:".$report_bold2."'>".$report_tab2."(".$val['account_code2'].") ".$val['account_name2']."</div> </td>
                                                    <td style='text-align:right'><div style='font-weight:".$report_bold2."'>".number_format($profit_loss, 2)."</div></td>
                                                ";

                                                $account_amount2_bottom[$val['report_no']] = $profit_loss;
                                            }
                                        echo "
                                            </tr>";

                                        echo "
                                            <tr>";

                                            if($val['report_type2'] == 5){
                                                if(!empty($val['report_formula2']) && !empty($val['report_operator2'])){
                                                    $report_formula2 	= explode('#', $val['report_formula2']);
                                                    $report_operator2 	= explode('#', $val['report_operator2']);

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

                                                    echo "
                                                        <td><div style='font-weight:".$report_bold2."'>".$report_tab2."".$val['account_name2']."</div></td>
                                                        <td style='text-align:right'><div style='font-weight:".$report_bold2."'>".number_format($total_account_amount2+$total_account_amount210, 2)."</div></td>
                                                        ";
                                                    // echo "
                                                    //     <td><div style='font-weight:".$report_bold2."'>".$report_tab2."".$val['account_name2']."</div></td>
                                                    //     <td style='text-align:right'><div style='font-weight:".$report_bold2."'>".number_format($total_account_amount2, 2)."</div></td>
                                                    //     ";
                                                }	
                                            }
                                        echo "			
                                            </tr>";

                                        echo "
                                            <tr>";

                                            if($val['report_type2'] == 6){
                                                if(!empty($val['report_formula2']) && !empty($val['report_operator2'])){

                                                    $grand_total_account_name2 = $val['account_name2'];
                                                }	
                                            }

                                        echo "			
                                            </tr>";	

                                        
                                    }
                                ?>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style='width: 50%'>
                            <table class="table table-bordered table-advance table-hover">
                                <tr>
                                    <?php
                                        // echo "
                                        //     <td style=\"width: 70%\"><div style=\"font-weight:".$report_bold1.";font-size:14px\">".$report_tab1."".$grand_total_account_name1."</div>
                                        //     </td>
                                        //     <td style=\"width: 28%; text-align:right;\"><div style=\"font-weight:".$report_bold1."; font-size:14px\">".number_format($grand_total_account_amount1, 2)."</div>
                                        //     </td>
                                        // ";
                                        echo "
                                            <td style=\"width: 28%; text-align:right;\"><div style=\"font-weight:".$report_bold1."; font-size:14px\">".number_format($grand_total_account_amount1, 2)."</div>
                                            </td>
                                        ";
                                    ?>
                                </tr>
                            </table>
                        </td>

                        <td style='width: 50%'>
                            <table class="table table-bordered table-advance table-hover">
                                <tr>
                                    <?php 
                                        // echo "
                                        //     <td style=\"width: 70%\"><div style=\"font-weight:".$report_bold2.";font-size:14px\">".$report_tab2."".$grand_total_account_name2."</div></td>
                                        //     <td style=\"width: 28%; text-align:right;\"><div style=\"font-weight:".$report_bold2."; font-size:14px\">".number_format($grand_total_account_amount2, 2)."</div></td>
                                        // ";
                                        echo "
                                            <td style=\"width: 28%; text-align:right;\"><div style=\"font-weight:".$report_bold2."; font-size:14px\">".number_format($grand_total_account_amount2, 2)."</div></td>
                                        ";
                                    ?>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="text-muted mt-3">
            <div class="form-actions float-right">
                <a class="btn btn-secondary" href="{{ url('balance-sheet-report/print') }}"><i class="fa fa-file-pdf"></i> Pdf</a>
                <a class="btn btn-dark" href="{{ url('balance-sheet-report/export') }}"><i class="fa fa-download"></i> Export Data</a>
            </div>
        </div>
  </div>
</div>
</div>

@stop

@section('footer')
    
@stop

@section('css')
    
@stop

@section('js')
    
@stop   