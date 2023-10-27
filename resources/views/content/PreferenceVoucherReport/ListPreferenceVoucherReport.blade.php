@inject('PVRC','App\Http\Controllers\PreferenceVoucherReportController' )

@extends('adminlte::page')

@section('title', 'MOZAIC Minimarket')
@section('js')
    <script>
        $(document).ready(function(){
            var voucher_id = {!! json_encode($voucher_id) !!} 
            console.log(voucher_id);
            if (voucher_id == '') {
                $('#voucher_id').select2('val', ' ');
            }
        });
    </script>
@endsection
@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
      <li class="breadcrumb-item active" aria-current="page">Laporan Voucher</li>
    </ol>
</nav>

@stop

@section('content')

<h3 class="page-title">
    <b>Laporan Voucher</b>
</h3>
<br/>
@if(session('msg'))
<div class="alert alert-info" role="alert">
    {{session('msg')}}
</div>
@endif 
<div id="accordion">
    <form  method="post" action="{{ route('filter-preference-voucher-report') }}" enctype="multipart/form-data">
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
                    <div class = "col-md-4">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Kode Voucher
                            
                            </section>
                            {!! Form::select('voucher_id',  $listVoucher, $voucher_id, ['class' => 'selection-search-clear select-form', 'id' => 'voucher_id', 'name' => 'voucher_id']) !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-muted">
                <div class="form-actions float-right">
                    <a href="{{ route('reset-filter-preference-voucher-report') }}" type="reset" name="Reset" class="btn btn-danger"><i class="fa fa-times"></i> Batal</a>
                    <button type="submit" name="Find" class="btn btn-primary" title="Search Data"><i class="fa fa-search"></i> Cari</button>
                </div>
            </div>
        </div>
        </div>
    </form>
</div>
<br/>
<div class="card border border-dark">
  <div class="card-header bg-dark clearfix">
    <h5 class="mb-0 float-left">
        Daftar
    </h5>
  </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="example" style="width:100%" class="table table-striped table-bordered table-hover table-full-width">
                <thead>
                    <tr>
                        <th style='text-align:center; width: 5%'>No</th>
                        <th style='text-align:center'>Kode Voucher</th>
                        <th style='text-align:center'>No. Voucher</th>
                        <th style='text-align:center'>Tanggal</th>
                        <th style='text-align:center'>Nama Anggota</th>
                        <th style='text-align:center'>Jabatan</th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;?>
                    @foreach ($data as $row)
                        <tr>
                            <td class="text-center">{{ $no++ }}.</td>
                            <td>{{ $PVRC->getVoucherCode($row['voucher_id']) }}</td>
                            <td>{{ $row['voucher_no'] }}</td>
                            <td>{{ date('d-m-Y', strtotime($row['sales_invoice_date'])) }}</td>
                            <td>{{ $PVRC->getMemberName($row['customer_id']) }}</td>
                            <td>{{ $PVRC->getDivisionName($row['customer_id']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer text-muted">
        <div class="form-actions float-right">
            <a class="btn btn-secondary" href="{{ url('preference-voucher-report/print') }}"><i class="fa fa-file-pdf"></i> Pdf</a>
            <a class="btn btn-dark" href="{{ url('preference-voucher-report/export') }}"><i class="fa fa-download"></i> Export Data</a>
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