@inject('JournalMemorial','App\Http\Controllers\AcctJournalMemorialController')
@extends('adminlte::page')

@section('title', 'MOZAIC Minimarket')

@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
      <li class="breadcrumb-item active" aria-current="page">Daftar Jurnal Memorial </li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    <b>Daftar Jurnal Memorial </b> <small>Kelola Daftar Jurnal Memorial  </small>
</h3>
<br/>
<div id="accordion">
    <form  method="post" action="{{ route('filter-journal-memorial') }}" enctype="multipart/form-data">
    @csrf
        <div class="card border border-dark">
        <div class="card-header bg-dark" id="headingOne" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
            <h5 class="mb-0">
                Filter
            </h5>
        </div>
    
        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
            <div class="card-body">
                <div class = "row">
                    <div class = "col-md-6">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Tanggal Awal
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            <input style="width: 50%" class="form-control input-bb" name="start_date" id="start_date" type="date" data-date-format="dd-mm-yyyy" autocomplete="off" onchange="function_elements_add(this.name, this.value)" value="{{ $start_date }}"/>
                        </div>
                    </div>

                    <div class = "col-md-6">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Tanggal Akhir
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            <input style="width: 50%" class="form-control input-bb" name="end_date" id="end_date" type="date" data-date-format="dd-mm-yyyy" autocomplete="off" onchange="function_elements_add(this.name, this.value)" value="{{ $end_date }}"/>
                        </div>
                    </div>

                </div>
            </div>
            <div class="card-footer text-muted">
                <div class="form-actions float-right">
                    <a href="{{ route('reset-filter-journal-memorial') }}" type="reset" name="Reset" class="btn btn-danger"><i class="fa fa-times"></i> Batal</a>
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
        <div class="table-responsive">
            <table id="" style="width:100%" class="table table-striped table-bordered table-hover table-full-width">
                <thead>
                    <tr>
                        <th width="5%" rowspan="2" style="vertical-align : middle;text-align:center;">No</th>
                        <th width="10%" rowspan="2" style="vertical-align : middle;text-align:center;">Bukti</th>
                        <th width="20%" rowspan="2" style="vertical-align : middle;text-align:center;">Uraian</th>
                        <th width="15%" rowspan="2" style="vertical-align : middle;text-align:center;">Tanggal</th>
                        <th width="15%" rowspan="2" style="vertical-align : middle;text-align:center;">No. Per</th>
                        <th width="15%" rowspan="2" style="vertical-align : middle;text-align:center;">Perkiraan</th>
                        <th width="15%" rowspan="2" style="vertical-align : middle;text-align:center;">Nominal</th>
                        <th width="10%" rowspan="2" style="vertical-align : middle;text-align:center;">D / K</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $no = 1;
                        $total_debit = 0;
                        $total_credit = 0;
                        if(empty($data)){
                            echo "
                                <tr>
                                    <td colspan='8' align='center'>Data Kosong</td>
                                </tr>
                            ";
                        } else {
                            foreach ($data as $key=>$val){	
                                $id = $JournalMemorial->getMinID($val['journal_voucher_id']);

                                    if($val['journal_voucher_debit_amount'] <> 0 ){
                                        $nominal = $val['journal_voucher_debit_amount'];
                                        $status = 'D';
                                    } else if($val['journal_voucher_credit_amount'] <> 0){
                                        $nominal = $val['journal_voucher_credit_amount'];
                                        $status = 'K';
                                    } else {
                                        $nominal = 0;
                                        $status = 'Kosong';
                                    }


                                if($val['journal_voucher_item_id'] == $id){
                                    echo"
                                        <tr class='table-active'>			
                                            <td style='text-align:center'>$no.</td>
                                            <td>".$val['transaction_module_code']."</td>
                                            <td>".$val['journal_voucher_description']."</td>
                                            <td>".date('d-m-Y', strtotime($val['journal_voucher_date']))."</td>
                                            <td>".$JournalMemorial->getAccountCode($val['account_id'])."</td>
                                            <td>".$JournalMemorial->getAccountName($val['account_id'])."</td>
                                            <td style='text-align: right'>".number_format($nominal,2,'.',',')."</td>
                                            <td style='text-align: right'>".$status."</td>
                                        </tr>
                                    ";
                                    $no++;
                                } else {
                                    echo"
                                        <tr>			
                                            <td style='text-align:center'></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td>".$JournalMemorial->getAccountCode($val['account_id'])."</td>
                                            <td>".$JournalMemorial->getAccountName($val['account_id'])."</td>
                                            <td style='text-align: right'>".number_format($nominal,2,'.',',')."</td>
                                            <td style='text-align: right'>".$status."</td>
                                        </tr>
                                    ";
                                }
                                $total_debit += $val['journal_voucher_debit_amount'];
                                $total_credit += $val['journal_voucher_credit_amount'];
                            } 
                        }
                        
                    ?>
                    <tr>
                        <th style="text-align: right" colspan="6">Total Debit</th>
                        <td style="text-align: right">{{ number_format($total_debit,2,'.',',') }}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <th style="text-align: right" colspan="6">Total Kredit</th>
                        <td style="text-align: right">{{ number_format($total_credit,2,'.',',') }}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
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