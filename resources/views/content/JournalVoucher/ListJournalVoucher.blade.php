@inject('JournalVoucher','App\Http\Controllers\JournalVoucherController')
@extends('adminlte::page')

@section('title', 'MOZAIC Minimarket')

@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
      <li class="breadcrumb-item active" aria-current="page">Daftar Jurnal Umum </li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    <b>Daftar Jurnal Umum </b> <small>Kelola Daftar Jurnal Umum  </small>
</h3>
<br/>
<div id="accordion">
    <form  method="post" action="{{ route('filter-journal-voucher') }}" enctype="multipart/form-data">
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
                            <section class="control-label">Tanggal Mulai
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            <input type ="date" class="form-control form-control-inline input-medium date-picker input-date" data-date-format="dd-mm-yyyy" type="text" name="start_date" id="start_date" value="{{ $start_date }}" style="width: 15rem;"/>
                        </div>
                    </div>

                    <div class = "col-md-6">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Tanggal Akhir
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            <input type ="date" class="form-control form-control-inline input-medium date-picker input-date" data-date-format="dd-mm-yyyy" type="text" name="end_date" id="end_date" value="{{ $end_date }}" style="width: 15rem;"/>
                        </div>
                    </div>

                    {{-- <div class = "col-md-6">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Nama Pemasok
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            <select  class="form-control "  type="text" name="end_date" id="end_date" onChange="function_elements_add(this.name, this.value);" value="" >
                                <option value=""></option>
                            </select>
                        </div>
                    </div>

                    <div class = "col-md-6">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Nama Gudang
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            <select class="form-control"  type="text" name="end_date" id="end_date" onChange="function_elements_add(this.name, this.value);" value="">
                                <option value=""></option>
                            </select>
                        </div>
                    </div> --}}
                </div>
            </div>
            <div class="card-footer text-muted">
                <div class="form-actions float-right">
                    <a href="{{ route('reset-filter-journal-voucher') }}" type="reset" name="Reset" class="btn btn-danger"><i class="fa fa-times"></i> Batal</a>
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
    <div class="form-actions float-right">
        <button onclick="location.href='{{ url('/journal-voucher/add') }}'" name="Find" class="btn btn-sm btn-info" title="Add Data"><i class="fa fa-plus"></i> Tambah Jurnal Umum </button>
    </div>
  </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="example" style="width:100%" class="table table-striped table-bordered table-hover table-full-width">
                <thead>
                    <tr>
                        <th style="text-align: center; width: 5%">No </th>
                        <th style="text-align: center; width: 12%">Tanggal </th>
                        <th style="text-align: center; width: 20%">Uraian</th>
                        <th style="text-align: center; width: 15%">No. Perkiraan</th>
                        <th style="text-align: center; width: 15%">Nama Perkiraan</th>		
                        <th style="text-align: center; width: 10%">Jumlah</th>
                        <th style="text-align: center; width: 10%">D/K</th>
                        <th style="text-align: center; width: 10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $no = 1;
                        if(empty($data)){
                            echo "
                                <tr>
                                    <td colspan='8' align='center'>Data Kosong</td>
                                </tr>
                            ";
                        } else {
                            foreach ($data as $key=>$val){	
                                $id = $JournalVoucher->getMinID($val['journal_voucher_id']);

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
                                            <td>".date('d-m-Y', strtotime($val['journal_voucher_date']))."</td>
                                            <td>".$val['journal_voucher_description']."</td>
                                            <td>".$JournalVoucher->getAccountCode($val['account_id'])."</td>
                                            <td>".$JournalVoucher->getAccountName($val['account_id'])."</td>
                                            <td style='text-align: right'>".number_format($nominal,2,'.',',')."</td>
                                            <td>".$status."</td>
                                            <td style='text-align:center'>
                                                <a href='".url('journal-voucher/print/'.$val['journal_voucher_id'])."' class='btn btn-secondary btn-sm' >Cetak Bukti</a>
                                            </td>
                                        </tr>
                                    ";
                                    $no++;
                                } else {
                                    echo"
                                        <tr>			
                                            <td style='text-align:center'></td>
                                            <td></td>
                                            <td></td>
                                            <td>".$JournalVoucher->getAccountCode($val['account_id'])."</td>
                                            <td>".$JournalVoucher->getAccountName($val['account_id'])."</td>
                                            <td style='text-align: right'>".number_format($nominal,2,'.',',')."</td>
                                            <td>".$status."</td>
                                        </tr>
                                    ";
                                }
                            } 
                        }
                        
                    ?>
                   
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