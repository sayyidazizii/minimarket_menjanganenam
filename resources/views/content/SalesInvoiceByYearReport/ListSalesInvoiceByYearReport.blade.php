@inject('SIBYRC','App\Http\Controllers\SalesInvoicebyYearReportController' )

@extends('adminlte::page')

@section('title', 'MOZAIC Minimarket')
@section('js')
    <script>
        var table;
        $(document).ready(function(){
            table =  $('#myDataTable').DataTable({
     
             "processing": true,
             "serverSide": true,
             "pageLength": 5,
             "lengthMenu": [ [5, 15, 20, 100000], [5, 15, 20, "All"] ],
             "order": [[2, 'asc']],
             "ajax": "{{ url('table-sales-invoice-by-year') }}",
             "columns":[
                {data: 'no'},
                {data: 'item_category_name'},
                {data: 'item_name'},
                {data: 'total_item'},
                {data: 'total_amount'},
             ],
             });
        });
    </script>
@endsection
@section('content_header')

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
      <li class="breadcrumb-item active" aria-current="page">Laporan Penjualan Tahunan</li>
    </ol>
</nav>

@stop

@section('content')
<h3 class="page-title">
    <b>Laporan Penjualan Tahunan</b>
</h3>
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
  <form action="{{ route('filter-sales-invoice-by-year-report') }}" method="post">
    @csrf
    <div class="col-md-3 mt-4 ml-2">
        {!! Form::select(0, $yearlist, $year, ['class' => 'selection-search-clear select-form', 'id' => 'year_period', 'name' => 'year_period']) !!}
    <button type="submit" class="btn btn-primary mt-3 btn-sm">Cari</button>
    </div>
  </form>
    <div class="card-body">
        <div class="table-responsive">
            <table id="myDataTable" style="width:100%" class="table table-striped table-bordered table-hover table-full-width">
                <thead>
                    <tr>
                        <th style='text-align:center; width: 5%'>No</th>
                        <th style='text-align:center; width: 30%'>Nama Kategori</th>
                        <th style='text-align:center; width: 30%'>Nama Barang</th>
                        <th style='text-align:center; width: 15%'>Jumlah Penjualan</th>
                        <th style='text-align:center; width: 15%'>Total</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer text-muted">
        <div class="form-actions float-right">
            <a class="btn btn-secondary" href="{{ url('sales-invoice-by-year-report/print') }}"><i class="fa fa-file-pdf"></i> Pdf</a>
            <a class="btn btn-dark" href="{{ url('sales-invoice-by-year-report/export') }}"><i class="fa fa-download"></i> Export Data</a>
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