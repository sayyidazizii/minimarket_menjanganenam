@inject('ISARC','App\Http\Controllers\InvtStockAdjustmentReportController' )

@extends('adminlte::page')

@section('title', 'MOZAIC Minimarket')
@section('js')
    <script>
        $(document).ready(function(){
            var warehouse_id    = {!! json_encode($warehouse_id) !!}
            var category_id     = {!! json_encode($category_id) !!}

            if (warehouse_id == "") {
                $('#warehouse_id').select2('val', ' ');
            }
            if (category_id == "") {
                $('#category_id').select2('val', ' ');
            }
        });

        // function function_change_stock(key,value){
        //     change_stock = document.getElementById('change_stock_'+(key)).value;
        //     stock_amount = document.getElementById('stock_amount_'+(key)).value;
        //     btn_submit = document.getElementById('btn_submit_'+(key));
        //     var difference_stock = stock_amount - change_stock;

        //     if (change_stock == '') {
        //         $('#alert_'+key).html('<div id="alert_'+(key)+'"></div>');
        //         btn_submit.classList.add('disabled');
        //     } else if (change_stock != '' && difference_stock < 0) {
        //         $('#alert_'+key).html('<div class="alert alert-danger mb-3" role="alert" id="alert_'+(key)+'">Sisa Stock Kurang Dari '+change_stock+'</div>');
        //         btn_submit.classList.add('disabled');
        //     } else if (change_stock != 0 && difference_stock >= 0) {
        //         $('#alert_'+key).html('<div id="alert_'+(key)+'"></div>');
        //         btn_submit.classList.remove('disabled');
        //     } else if (change_stock == 0 && change_stock != '') {
        //         $('#alert_'+key).html('<div id="alert_'+(key)+'"></div>');
        //         btn_submit.classList.add('disabled');
        //     }
            
            // if (difference_stock < 0 && change_stock != NaN) {
            //     $('#alert_'+key).html('<div class="alert alert-danger mb-3" role="alert" id="alert_'+(key)+'">Sisa Stock Kurang Dari '+change_stock+'</div>');
            //     btn_submit.classList.add('disabled');
            // } else {
            //     $('#alert_'+key).html('<div id="alert_'+(key)+'"></div>');
            //     btn_submit.classList.remove('disabled');
            // }



            // console.log(difference_stock);
        // }

        var table;
        $(document).ready(function(){
            table =  $('#myDataTable').DataTable({
     
             "processing": true,
             "serverSide": true,
             "pageLength": 5,
             "lengthMenu": [ [5, 15, 20, 100000], [5, 15, 20, "All"] ],
             "order": [[3, 'asc']],
             "ajax": "{{ url('table-stock-item') }}",
             "columns":[
                {data: 'no'},
                {data: 'warehouse_name'},
                {data: 'item_category_name'},
                {data: 'item_name'},
                {data: 'item_unit_name'},
                {data: 'total_stock'},
                {data: 'rack_name'},
                {data: 'action'},
             ],
        
             });
        });
    </script>
@endsection

@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
      <li class="breadcrumb-item active" aria-current="page">Stok Barang</li>
    </ol>
</nav>

@stop

@section('content')

<h3 class="page-title">
    <b>Stok Barang</b>
</h3>
<br/>
@if(session('msg'))
<div class="alert alert-info" role="alert">
    {{session('msg')}}
</div>
@endif 
<div id="accordion">
    <form  method="post" action="{{ route('stock-adjustment-report-filter') }}" enctype="multipart/form-data">
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
                            <section class="control-label">Nama Gudang
                                
                            </section>
                            {!! Form::select('warehouse_id',  $warehouse, $warehouse_id, ['class' => 'selection-search-clear select-form', 'id' => 'warehouse_id', 'name' => 'warehouse_id']) !!}
                        </div>
                    </div>
                    <div class = "col-md-6">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Nama Kategori Barang
                                
                            </section>
                            {!! Form::select('item_category_id',  $category, $category_id, ['class' => 'selection-search-clear select-form', 'id' => 'category_id', 'name' => 'category_id']) !!}
                        </div>
                    </div>

                    {{-- <div class = "col-md-6">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Nama Gudang</section>
                            {!! Form::select('warehouse_id',  $warehouse, 0, ['class' => 'selection-search-clear select-form', 'id' => 'warehouse_id', 'name' => 'warehouse_id']) !!}
                            
                        </div>
                    </div> --}}
                </div>
            </div>
            <div class="card-footer text-muted">
                <div class="form-actions float-right">
                    <a href="{{ route('stock-adjustment-report-reset') }}" type="reset" name="Reset" class="btn btn-danger"><i class="fa fa-times"></i> Batal</a>
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
            <table id="myDataTable" style="width:100%" class="table table-striped table-bordered table-hover table-full-width">
                <thead>
                    <tr>
                        <th style='text-align:center; width: 5%'>No</th>
                        <th style='text-align:center'>Nama Gudang</th>
                        <th style='text-align:center'>Kategori Barang</th>
                        <th style='text-align:center'>Nama Barang</th>
                        <th style='text-align:center'>Satuan Barang</th>
                        <th style='text-align:center'>Stok Sistem</th>
                        <th style='text-align:center'>Daftar Rak</th>
                        <th style='text-align:center'>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer text-muted">
        <div class="form-actions float-right">
            <a class="btn btn-secondary" href="{{ url('stock-adjustment-report/print') }}"><i class="fa fa-file-pdf"></i> Pdf</a>
            <a class="btn btn-dark" href="{{ url('stock-adjustment-report/export') }}"><i class="fa fa-download"></i> Export Data</a>
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