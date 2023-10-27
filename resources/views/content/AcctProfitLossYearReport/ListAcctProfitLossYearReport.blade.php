@inject('APLYR','App\Http\Controllers\AcctProfitLossYearReportController')

@extends('adminlte::page')

@section('title', 'MOZAIC Minimarket')

@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
      <li class="breadcrumb-item active" aria-current="page">Laporan Rugi / Laba Tahunan </li>
    </ol>
  </nav>

@stop

@section('content')
<h3 class="page-title">
    <b>Laporan Perhitungan Rugi / Laba Tahunan</b>
</h3>
<br/>
<div id="accordion">
    <form action="{{ route('filter-profit-loss-year-report') }}" method="post">
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
                        <a href="{{ route('reset-filter-profit-loss-year-report') }}" type="reset" name="Reset" class="btn btn-danger"><i class="fa fa-times"></i> Batal</a>
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
                            <div style='font-weight:bold'>Laporan Perhitungan Rugi / Laba
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan='2' style='text-align:center;'>
                            <div>
                                Januari - {{ $APLYR->getMonthName($month) }} {{ $year }}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan='2'></td>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($income as $val)
                        <?php
                            if($val['report_tab'] == 0){
                                $report_tab = ' ';
                            } else if($val['report_tab'] == 1){
                                $report_tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                            } else if($val['report_tab'] == 2){
                                $report_tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                            } else if($val['report_tab'] == 3){
                                $report_tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                            } else if($val['report_tab'] == 4){
                                $report_tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                            } else if($val['report_tab'] == 5){
                                $report_tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                            } else if($val['report_tab'] == 6){
                                $report_tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                            } else if($val['report_tab'] == 7){
                                $report_tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                            } else if($val['report_tab'] == 8){
                                $report_tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                            }

                            if($val['report_bold'] == 1){
                                $report_bold = 'bold';
                            } else {
                                $report_bold = 'normal';
                            }

                            echo "
                                <tr>";

                                if($val['report_type'] == 1){
                                    echo "
                                        <td colspan='2'><div style='font-weight:".$report_bold."'>".$report_tab."".$val['account_name']."</div></td>
                                        ";
                                }
                                
                            echo "
                                </tr>";

                            echo "
                                <tr>";

                                if($val['report_type']	== 2){
                                    echo "
                                        <td style='width: 75%'><div style='font-weight:".$report_bold."'>".$report_tab."".$val['account_name']."</div></td>
                                        <td style='width: 25%'><div style='font-weight:".$report_bold."'></div></td>
                                        ";
                                }
                                    
                            echo "
                                </tr>";

                            echo "
                                <tr>";

                                if($val['report_type']	== 3){
                                    $income_subtotal 	= $APLYR->getAmountAccount($val['account_id']);

                                    echo "
                                        <td><div style='font-weight:".$report_bold."'>".$report_tab."(".$val['account_code'].") ".$val['account_name']."</div> </td>
                                        <td style='text-align:right'><div style='font-weight:".$report_bold."'>".number_format($income_subtotal, 2)."</div></td>
                                    ";

                                    $account_amount[$val['report_no']] = $income_subtotal;
                                }

                                
                                    
                            echo "
                                </tr>";

                            echo "
                                <tr>";

                                if($val['report_type'] == 5){
                                    if(!empty($val['report_formula']) && !empty($val['report_operator'])){
                                        $report_formula 	= explode('#', $val['report_formula']);
                                        $report_operator 	= explode('#', $val['report_operator']);

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

                                        echo "
                                            <td><div style='font-weight:".$report_bold."'>".$report_tab."".$val['account_name']."</div></td>
                                            <td style='text-align:right'><div style='font-weight:".$report_bold."'>".number_format($total_account_amount, 2)."</div></td>
                                            ";
                                }

                            }

                            // print_r($total_account_amount);

                            echo "			
                                </tr>";


                            if($val['report_type'] == 6){
                                if(!empty($val['report_formula']) && !empty($val['report_operator'])){
                                    $report_formula 	= explode('#', $val['report_formula']);
                                    $report_operator 	= explode('#', $val['report_operator']);

                                    
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

                                    echo "
                                        <td><div style='font-weight:".$report_bold."'>".$report_tab."".$val['account_name']."</div></td>
                                        <td style='text-align:right'><div style='font-weight:".$report_bold."'>".number_format($grand_total_account_amount1, 2)."</div></td>
                                        ";
                                }

                            }
                         ?>
                    @endforeach
                    <tr>
                        <td colspan='2'></td>
                    </tr>
                    @foreach ($expenditure as $val)
                        <?php
                            if($val['report_tab'] == 0){
                                $report_tab = ' ';
                            } else if($val['report_tab'] == 1){
                                $report_tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                            } else if($val['report_tab'] == 2){
                                $report_tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                            } else if($val['report_tab'] == 3){
                                $report_tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                            } else if($val['report_tab'] == 4){
                                $report_tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                            } else if($val['report_tab'] == 5){
                                $report_tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                            } else if($val['report_tab'] == 6){
                                $report_tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                            } else if($val['report_tab'] == 7){
                                $report_tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                            } else if($val['report_tab'] == 8){
                                $report_tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                            }

                            if($val['report_bold'] == 1){
                                $report_bold = 'bold';
                            } else {
                                $report_bold = 'normal';
                            }

                            echo "
                                <tr>";

                                if($val['report_type'] == 1){
                                    echo "
                                        <td colspan='2'><div style='font-weight:".$report_bold."'>".$report_tab."".$val['account_name']."</div></td>
                                        ";
                                }
                                
                            echo "
                                </tr>";

                            echo "
                                <tr>";

                                if($val['report_type']	== 2){
                                    echo "
                                        <td style='width: 75%'><div style='font-weight:".$report_bold."'>".$report_tab."".$val['account_name']."</div></td>
                                        <td style='width: 25%'><div style='font-weight:".$report_bold."'></div></td>
                                        ";
                                }
                                    
                            echo "
                                </tr>";

                            echo "
                                <tr>";

                                if($val['report_type']	== 3){
                                    $expenditure_subtotal 	= $APLYR->getAmountAccount($val['account_id']);

                                    echo "
                                        <td><div style='font-weight:".$report_bold."'>".$report_tab."(".$val['account_code'].") ".$val['account_name']."</div> </td>
                                        <td style='text-align:right'><div style='font-weight:".$report_bold."'>".number_format($expenditure_subtotal, 2)."</div></td>
                                    ";

                                    $account_amount[$val['report_no']] = $expenditure_subtotal;
                                }

                                
                                    
                            echo "
                                </tr>";

                            echo "
                                <tr>";

                                if($val['report_type'] == 5){
                                    if(!empty($val['report_formula']) && !empty($val['report_operator'])){
                                        $report_formula 	= explode('#', $val['report_formula']);
                                        $report_operator 	= explode('#', $val['report_operator']);

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

                                        echo "
                                            <td><div style='font-weight:".$report_bold."'>".$report_tab."".$val['account_name']."</div></td>
                                            <td style='text-align:right'><div style='font-weight:".$report_bold."'>".number_format($total_account_amount, 2)."</div></td>
                                            ";
                                }

                            }

                            // print_r($total_account_amount);

                            echo "			
                                </tr>";


                            if($val['report_type'] == 6){
                                if(!empty($val['report_formula']) && !empty($val['report_operator'])){
                                    $report_formula 	= explode('#', $val['report_formula']);
                                    $report_operator 	= explode('#', $val['report_operator']);

                                    
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

                                    echo "
                                        <td><div style='font-weight:".$report_bold."'>".$report_tab."".$val['account_name']."</div></td>
                                        <td style='text-align:right'><div style='font-weight:".$report_bold."'>".number_format($grand_total_account_amount2, 2)."</div></td>
                                        ";
                                }

                            }
                        ?> 
                    @endforeach
                    <tr>
                        <td colspan='2'></td>
                    </tr>
                    <tr>
                        <th style="width: 80%">RUGI / LABA</th>
                        <th style="width: 20%; text-align: right">{{ number_format($grand_total_account_amount1 - $grand_total_account_amount2,2,'.',',') }}</th>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="text-muted mt-3">
            <div class="form-actions float-right">
                <a class="btn btn-secondary" href="{{ url('profit-loss-year-report/print') }}"><i class="fa fa-file-pdf"></i> Pdf</a>
                <a class="btn btn-dark" href="{{ url('profit-loss-year-report/export') }}"><i class="fa fa-download"></i> Export Data</a>
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