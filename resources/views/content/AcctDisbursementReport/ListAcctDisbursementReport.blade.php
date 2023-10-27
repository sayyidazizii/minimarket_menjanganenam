@extends('adminlte::page')

@section('title', 'MOZAIC Minimarket')

@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
      <li class="breadcrumb-item active" aria-current="page">Laporan Pengeluaran Kas </li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    <b>Laporan Pengeluaran Kas </b>
</h3>
<br/>
<div id="accordion">
    <form  method="post" action="{{ route('filter-cash-disbursement-report') }}" enctype="multipart/form-data">
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
                            <input style="width: 50%" class="form-control input-bb" name="start_date" id="start_date" type="date" data-date-format="dd-mm-yyyy" autocomplete="off" value="{{ $start_date }}"/>
                        </div>
                    </div>

                    <div class = "col-md-6">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Tanggal Akhir
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            <input style="width: 50%" class="form-control input-bb" name="end_date" id="end_date" type="date" data-date-format="dd-mm-yyyy" autocomplete="off" value="{{ $end_date }}"/>
                        </div>
                    </div>

                </div>
            </div>
            <div class="card-footer text-muted">
                <div class="form-actions float-right">
                    <a href="{{ route('reset-filter-cash-disbursement-report') }}" type="reset" name="Reset" class="btn btn-danger"><i class="fa fa-times"></i> Batal</a>
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
                        <th width="20%" rowspan="2" style="vertical-align : middle;text-align:center;">Keterangan</th>
                        <th width="15%" rowspan="2" style="vertical-align : middle;text-align:center;">Tanggal</th>
                        <th width="15%" rowspan="2" style="vertical-align : middle;text-align:center;">Nominal</th>
                    </tr>
                </thead>
                <tbody>
                   <?php 
                    $no = 1;
                    $total_amount = 0;
                    ?>
                   @foreach ($data as $row)
                       <tr>
                        <td class="text-center">{{ $no++ }}.</td>
                        <td>{{ $row['expenditure_remark'] }}</td>
                        <td>{{ date('d-m-Y', strtotime($row['expenditure_date'])) }}</td>
                        <td style="text-align: right">{{ number_format($row['expenditure_amount'],2,'.',',') }}</td>
                       </tr>
                       <?php $total_amount += $row['expenditure_amount'] ?>
                   @endforeach
                   <tr>
                    <th style="text-align: right" colspan="3">Total Pengeluaran Kas</th>
                    <td style="text-align: right">{{ number_format($total_amount,2,'.',',') }}</td>
                   </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer text-muted">
        <div class="form-actions float-right">
            <a class="btn btn-secondary" href="{{ url('cash-disbursement-report/print') }}"><i class="fa fa-file-pdf"></i> Pdf</a>
            <a class="btn btn-dark" href="{{ url('cash-disbursement-report/export') }}"><i class="fa fa-download"></i> Export Data</a>
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