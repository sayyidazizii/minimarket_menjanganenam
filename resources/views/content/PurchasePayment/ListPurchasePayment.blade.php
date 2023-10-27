@inject('PurchasePayment', 'App\Http\Controllers\PurchasePaymentController')
@extends('adminlte::page')

@section('title', 'MOZAIC Minimarket')
@section('js')
<script>
    function reset_add(){
		$.ajax({
				type: "GET",
				url : "{{route('reset-filter-purchase-payment')}}",
				success: function(msg){
                    location.reload();
			}

		});
	}
</script>
@stop
@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
      <li class="breadcrumb-item active" aria-current="page">Daftar Pelunasan Hutang</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    <b>Daftar Pelunasan Hutang</b> <small>Kelola Pelunasan Hutang </small>
</h3>
<br/>
<div id="accordion">
    <form  method="post" action="{{ route('filter-purchase-payment') }}" enctype="multipart/form-data">
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
                </div>
            </div>
            <div class="card-footer text-muted">
                <div class="form-actions float-right">
                    <button type="reset" name="Reset" class="btn btn-danger" onclick="reset_add();"><i class="fa fa-times"></i> Batal</button>
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
        <button onclick="location.href='{{ url('/purchase-payment/search') }}'" name="Find" class="btn btn-sm btn-info" title="Add Data"><i class="fa fa-plus"></i> Tambah Pelunasan Hutang </button>
    </div>
  </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="example" style="width:100%" class="table table-striped table-bordered table-hover table-full-width">
                <thead>
                    <tr>
                        <th style="text-align: center">No </th>
                        <th style="text-align: center">Nama Pemasok</th>
                        <th style="text-align: center">Tanggal Pelunasan</th>
                        <th style="text-align: center">No Pelunasan</th>
                        <th style="text-align: center">Jumlah Pelunasan Tunai</th>
                        <th style="text-align: center">Jumlah Pelunasan Transfer</th>	
                        <th style="text-align: center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    @foreach($purchasepayment as $payment)
                    <tr>
                        <td style='text-align:center'>{{$no}}</td>
                        <td>{{$PurchasePayment->getCoreSupplierName($payment['supplier_id'])}}</td>
                        <td>{{$payment['payment_date']}}</td>
                        <td>{{$payment['payment_no']}}</td>
                    <?php if($payment['payment_total_cash_amount']==null){?>
                        <td style='text-align:right'>0.00</td>
                    <?php }else{?>
                        <td style='text-align:right'>{{number_format($payment['payment_total_cash_amount'], 2)}}</td>
                    <?php } ?>
                    <?php if($payment['payment_total_transfer_amount']==null){?>
                        <td style='text-align:right'>0.00</td>
                    <?php }else{?>
                        <td style='text-align:right'>{{number_format($payment['payment_total_transfer_amount'], 2)}}</td>
                    <?php } ?>
                        <td class="">
                            <a type="button" class="btn btn-outline-warning btn-sm" href="{{ url('/purchase-payment/detail/'.$payment['payment_id']) }}">Detail</a>
                            <a type="button" class="btn btn-outline-danger btn-sm" href="{{ url('/purchase-payment/delete/'.$payment['payment_id']) }}">Batal</a>
                        </td>
                    </tr>
                    <?php $no++; ?>
                    @endforeach
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