@inject('SIDRC','App\Http\Controllers\SalesInvoiceDetailReportController' )

@extends('adminlte::page')

@section('title', 'MOZAIC Minimarket')
@section('js')
<script>
    function reset_add(){
		$.ajax({
				type: "GET",
				url : "{{route('filter-reset-sales-invoice-report-detail')}}",
				success: function(msg){
                    location.reload();
			}

		});
	}
    $(document).ready(function(){
        var sales_payment_method = {!! json_encode($sales_payment_method) !!}

        if (sales_payment_method == 0) {
            $('#sales_payment_method').select2('val','0');
        }
    });
</script>
@stop
@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
      <li class="breadcrumb-item active" aria-current="page">Laporan Penjualan Terperinci</li>
    </ol>
</nav>

@stop

@section('content')

<h3 class="page-title">
    <b>Laporan Penjualan Terperinci</b>
</h3>
<br/>
<div id="accordion">
    <form  method="post" action="{{ route('filter-sales-invoice-report-detail') }}" enctype="multipart/form-data">
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
                            <section class="control-label">Metode Pembayaran
                            </section>
                            {!! Form::select(0, $sales_payment_method_list, $sales_payment_method, ['class' => 'form-control selection-search-clear select-form','name'=>'sales_payment_method','id'=>'sales_payment_method']) !!}
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
  </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="example" style="width:100%" class="table table-striped table-bordered table-hover table-full-width">
                <thead>
                    <tr>
                        <th style='text-align:center; width: 5%'>No</th>
                        <th style='text-align:center; width: 10%'>Anggota</th>
                        <th style='text-align:center; width: 10%'>Tanggal</th>
                        <th style='text-align:center; width: 15%'>No. Penjulan</th>
                        <th style='text-align:center; width: 12%'>Nama Barang</th>
                        <th style='text-align:center; width: 10%'>Satuan</th>
                        <th style='text-align:center; width: 5%'>Qty</th>
                        <th style='text-align:center; width: 8%'>Harga</th>
                        <th style='text-align:center; width: 8%'>Subtotal</th>
                        <th style='text-align:center; width: 8%'>Diskon</th>
                        <th style='text-align:center; width: 10%'>Total</th>
                    </tr>
                </thead>
                <tbody>
                  <?php $no=1; ?>
                  @foreach ($data as $row)
                  <tr>
                    <td class="text-center">{{ $no++ }}.</td>
                    <td>{{ $SIDRC->getCustomerName($row['customer_id']) }}</td>
                    <td>{{ date('d-m-Y', strtotime($row['sales_invoice_date'])) }}</td>
                    <td>{{ $row['sales_invoice_no'] }}</td>
                    <td>{{ $SIDRC->getItemName($row['item_id']) }}</td>
                    <td>{{ $SIDRC->getItemUnitName($row['item_unit_id']) }}</td>
                    <td class="text-right">{{ $row['quantity'] }}</td>
                    <td style="text-align: right">{{ number_format($row['item_unit_price'],2,'.',',') }}</td>
                    <td style="text-align: right">{{ number_format($row['subtotal_amount'],2,'.',',') }}</td>
                    <td style="text-align: right">{{ number_format($row['discount_amount'],2,'.',',') }}</td>
                    <td style="text-align: right">{{ number_format($row['subtotal_amount_after_discount'],2,'.',',') }}</td>
                  </tr> 
                  @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer text-muted">
        <div class="form-actions float-right">
            <a class="btn btn-secondary" href="{{ url('sales-invoice-report-detail/print') }}"><i class="fa fa-file-pdf"></i> Pdf</a>
            <a class="btn btn-dark" href="{{ url('sales-invoice-report-detail/export') }}"><i class="fa fa-download"></i> Export Data</a>
            {{-- <p class="mt-2">
                <a class="btn btn-secondary" href="{{ url('sales-invoice-report/print-detail') }}"><i class="fa fa-file-pdf"></i> Pdf Detail</a>
            </p> --}}
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