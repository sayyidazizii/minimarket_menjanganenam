@inject('PurchaseInvoice','App\Http\Controllers\PurchaseInvoiceController')
@extends('adminlte::page')

@section('title', 'MOZAIC Minimarket')
@section('js')
<script>
    function reset_add(){
		$.ajax({
				type: "GET",
				url : "{{route('filter-reset-purchase-invoice')}}",
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
      <li class="breadcrumb-item active" aria-current="page">Daftar Pembelian</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    <b>Daftar Pembelian</b> <small>Kelola Pembelian </small>
</h3>
<br/>
<div id="accordion">
    <form  method="post" action="{{ route('filter-purchase-invoice') }}" enctype="multipart/form-data">
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
        <button onclick="location.href='{{ url('/purchase-invoice/add') }}'" name="Find" class="btn btn-sm btn-info" title="Add Data"><i class="fa fa-plus"></i> Tambah Pembelian </button>
    </div>
  </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="example" style="width:100%" class="table table-striped table-bordered table-hover table-full-width">
                <thead>
                    <tr>
                        <th style="text-align: center">No </th>
                        <th style="text-align: center">No Pembelian</th>
                        <th style="text-align: center">Tanggal Pembelian</th>
                        <th style="text-align: center">Nama Pemasok</th>
                        <th style="text-align: center">Nama Gudang</th>
                        <th style="text-align: center">Jumlah Pembelian</th>	
                        <th style="text-align: center">Dibayar</th>
                        <th style="text-align: center">Sisa</th>		
                        <th style="text-align: center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                   @foreach ($data as $row )
                   <tr>
                        <td class="text-center">{{ $no++ }}.</td>
                        <td>{{ $row['purchase_invoice_no'] }}</td>
                        <td>{{ date('d-m-Y', strtotime($row['purchase_invoice_date'])) }}</td>
                        <td>{{ $PurchaseInvoice->getSupplierName($row['supplier_id']) }}</td>
                        <td>{{ $PurchaseInvoice->getWarehouseName($row['warehouse_id']) }}</td>
                        <td style="text-align: right">{{ number_format($row['total_amount'],2,',','.') }}</td>
                        <td style="text-align: right">{{ number_format($row['paid_amount'],2,',','.') }}</td>
                        <td style="text-align: right">{{ number_format($row['owing_amount'],2,',','.') }}</td>
                        <td class="text-center">
                            <a type="button" class="btn btn-outline-warning btn-sm" href="{{ url('/purchase-invoice/detail/'.$row['purchase_invoice_id']) }}">Detail</a>
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