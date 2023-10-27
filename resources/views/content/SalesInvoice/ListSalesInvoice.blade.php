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
      <li class="breadcrumb-item active" aria-current="page">Daftar Penjualan</li>
    </ol>
</nav>

@stop
@section('content')

<h3 class="page-title">
    <b>Daftar Penjualan</b> <small>Kelola Penjualan </small>
</h3>
<br/>
@if(session('msg'))
<div class="alert alert-info" role="alert">
    {{session('msg')}}
</div>
@endif 
<div id="accordion">
    <form  method="post" action="{{ route('filter-sales-invoice') }}" enctype="multipart/form-data">
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
                            <input type ="date" class="form-control form-control-inline input-medium date-picker input-date" data-date-format="dd-mm-yyyy" type="text" name="end_date" id="end_date"  value="{{ $end_date }}" style="width: 15rem;"/>
                        </div>
                    </div>

                    {{-- <div class = "col-md-6">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Status Pembayaran
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            <select  class="form-control "  type="text" name="" id=""  value="" >
                                <option value=""></option>
                            </select>
                        </div>
                    </div> --}}

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

<div class="card border border-dark">
  <div class="card-header bg-dark clearfix">
    <h5 class="mb-0 float-left">
        Daftar
    </h5>
    <div class="form-actions float-right">
        <button onclick="location.href='{{ url('/sales-invoice/add') }}'" name="Find" class="btn btn-sm btn-info" title="Add Data"><i class="fa fa-plus"></i> Tambah Penjualan</button>
    </div>
  </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="example" style="width:100%" class="table table-striped table-bordered table-hover table-full-width">
                <thead>
                    <tr>
                        <th width="2%" style='text-align:center'>No</th>
                        <th width="15%" style='text-align:center'>Tanggal Invoice</th>
                        <th width="15%" style='text-align:center'>Nomor Invoice</th>
                        <th width="15%" style='text-align:center'>Anggota</th>
                        <th width="15%" style='text-align:center'>Subtotal</th>
                        <th width="15%" style='text-align:center'>Payment Method</th>
                        <th width="15%" style='text-align:center'>No PO Customer</th>
                        <th width="10%" style='text-align:center'>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    @foreach($data as $row)
                    <tr>
                        <td style='text-align:center'>{{ $no++ }}.</td>
                        <td>{{ date('d-m-Y', strtotime($row['sales_invoice_date'])) }}</td>
                        <td>{{ $row['sales_invoice_no'] }}</td>
                        <td>{{ $SalesInvoice->getCustomerName($row['customer_id']) }}</td>
                        <td style="text-align: right">{{ number_format($row['total_amount'],2,'.',',') }}</td>
                        @if ($row['sales_payment_method'] == 1)
                        <td>Tunai</td>
                        @endif
                        @if ($row['sales_payment_method'] == 2)
                        <td>Piutang</td>
                        @endif
                        @if ($row['sales_payment_method'] == 3)
                        <td>Gopay</td>
                        @endif
                        @if ($row['sales_payment_method'] == 4)
                        <td>Ovo</td>
                        @endif
                        @if ($row['sales_payment_method'] == 5)
                        <td>Shopeepay</td>
                        @endif
                        @if ($row['sales_payment_method'] == 6)
                        <td>Konsinyasi</td>
                        @endif
                        @if ($row['sales_payment_method'] == 7)
                        <td>PJL Varian</td>
                        @endif


                        @if ($row['purchase_invoice_no'] == 0)
                        <td style="text-align: center">-</td>
                        @else
                        <td style="text-align: right">{{ $row['purchase_invoice_no']}}</td>
                        @endif
                        <td class="text-center">
                            <a type="button" class="btn btn-outline-info btn-sm" href="{{ url('/sales-invoice/detail/'.$row['sales_invoice_id']) }}">Detail</a>
                            <a type="button" class="btn btn-outline-success btn-sm" href="{{ url('/sales-invoice/print-repeat/'.$row['sales_invoice_id']) }}">Nota</a>
                            <a type="button" class="btn btn-outline-warning btn-sm" href="{{ url('/sales-invoice/edit/'.$row['sales_invoice_id']) }}">Edit</a>
                            <a type="button" class="btn btn-outline-danger btn-sm" onclick="check_upload({{ $row['sales_invoice_id'] }});$(this).addClass('disabled');">Hapus</a>
                        </td>
                    </tr>
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