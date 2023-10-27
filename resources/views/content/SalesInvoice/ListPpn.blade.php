@inject('SalesInvoice','App\Http\Controllers\SalesInvoiceController' )

@extends('adminlte::page')

@section('title', 'MOZAIC Minimarket')
@section('js')
<script>
    // window.print();
    function reset_add(){
		$.ajax({
				type: "GET",
				url : "{{route('filter-reset-sales-invoice')}}",
				success: function(msg){
                    location.reload();
			}

		});
	}

    function check_upload(sales_invoice_id)
    {
        $.ajax({
            url : "{{url('sales-invoice/check-upload-status')}}"+'/'+sales_invoice_id,
            type: "GET",
				success: function(msg){
                    if (msg == 1) {
                        alert('Data yang sudah diunggah tidak bisa dihapus!');
                    } else {
                        if (confirm('Apakah Anda Yakin Ingin Menghapus Data Ini ?')) {
                            location.href="{{ url('sales-invoice/delete') }}"+'/'+sales_invoice_id;
                        }
                    }
			}

		});
    }
</script>
@stop
@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
      <li class="breadcrumb-item active" aria-current="page">Daftar Kelola Ppn Penjualan </li>
    </ol>
</nav>

@stop
@section('content')

<h3 class="page-title">
    <b>Setting PPN</b> <small></small>
</h3>

<div class="card border border-dark">
  <div class="card-header bg-dark clearfix">
    <h5 class="mb-0 float-left">
        Daftar
    </h5>
    <div class="float-right">
        <button onclick="location.href='{{ url('home') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
    </div>
  </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="example" style="width:100%" class="table table-striped table-bordered table-hover table-full-width">
                <thead>
                    <tr>
                        <th width="2%" style='text-align:center'>No</th>
                        <th width="5%" style='text-align:center'>PPN Amount</th>
                        <th width="5%" style='text-align:center'>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    @foreach($preferencecompany as $company)
                    <tr>
                        <td style='text-align:center'><?php echo $no ?></td>
                        <td style='text-align:right'>{{$company['ppn_percentage']}}</td>
                        <td style='text-align:center'>
                            <a type="button" class="btn btn-outline-warning btn-sm" href="{{ url('/ppn/edit/'.$company['company_id']) }}">Edit</a>
                        </td>
                    </tr>
                    <?php $no++; ?>
                    @endforeach
                </tbody>
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