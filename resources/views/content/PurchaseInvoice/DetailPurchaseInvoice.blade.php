@inject('PurchaseInvoice','App\Http\Controllers\PurchaseInvoiceController')
@extends('adminlte::page')

@section('title', 'MOZAIC Minimarket')
@section('js')
<script>

    
</script>
@stop
@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ url('purchase-invoice') }}">Daftar Pembelian</a></li>
        <li class="breadcrumb-item active" aria-current="page">Detail Pembelian</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    Detail Pembelian
</h3>
<br/>
@if(session('msg'))
<div class="alert alert-info" role="alert">
    {{session('msg')}}
</div>
@endif

@if(count($errors) > 0)
<div class="alert alert-danger" role="alert">
    @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
    @endforeach
</div>
@endif
    <div class="card border border-dark">
    <div class="card-header border-dark bg-dark">
        <h5 class="mb-0 float-left">
            Daftar
        </h5>
        <div class="float-right">
            <button onclick="location.href='{{ url('purchase-invoice') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
        </div>
    </div>

    <?php 
            // if (empty($coresection)){
            //     $coresection['section_name'] = '';
            // }
        ?>

        <div class="card-body">
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Nama Pemasok<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="purchase_invoice_supplier" id="purchase_invoice_supplier" type="text" autocomplete="off" value="{{ $PurchaseInvoice->getSupplierName($purchaseinvoice['supplier_id']) }}" readonly/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Nama Gudang<a class='red'> *</a></a>
                        {!! Form::select('warehouse_id', $warehouses, $purchaseinvoice['warehouse_id'], ['class' => 'form-control selection-search-clear select-form', 'id' => 'warehouse_id', 'name' => 'warehouse_id', 'onchange' => 'function_elements_add(this.name, this.value)', 'disabled']) !!}
                        
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <a class="text-dark">Tanggal Invoice Pembelian<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="purchase_invoice_date" id="purchase_invoice_date" type="date" data-date-format="dd-mm-yyyy" autocomplete="off" value="{{ $purchaseinvoice['purchase_invoice_date'] }}" readonly/>
                    </div>
                </div>
                <div class="col-md-6">

                </div>
                <div class="col-md-9 mt-3">
                    <div class="form-group">
                        <a class="text-dark">Keterangan</a>
                        <textarea class="form-control input-bb" name="purchase_invoice_remark" id="purchase_invoice_remark" type="text" autocomplete="off" readonly>{{ $purchaseinvoice['purchase_invoice_remark'] }}</textarea>
                    </div>
                </div>
    </div>
    </div>
    </div>


<div class="card border border-dark">
    <div class="card-header border-dark bg-dark">
        <h5 class="mb-0 float-left">
            Daftar
        </h5>
    </div>
        <div class="card-body">
            <div class="form-body form">
                <div class="table-responsive">
                    <table class="table table-bordered table-advance table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th style='text-align:center'>Barang</th>
                                <th style='text-align:center'>Jumlah</th>
                                <th style='text-align:center'>Harga Satuan</th>
                                <th style='text-align:center'>Subtotal</th>
                                <th style='text-align:center'>Kadaluarsa</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                foreach ($purchaseinvoiceitem AS $key => $val){
                                    echo"
                                    <tr>
                                                <td style='text-align  : left !important;'>".$PurchaseInvoice->getItemName($val['item_id'])."</td>
                                                <td style='text-align  : right !important;'>".$val['quantity']."</td>
                                                <td style='text-align  : right !important;'>".number_format($val['item_unit_cost'],2,',','.')."</td>
                                                <td style='text-align  : right !important;'>".number_format($val['subtotal_amount_after_discount'],2,',','.')."</td>
                                                <td style='text-align  : right !important;'>".date('d-m-Y', strtotime($val['item_expired_date']))."</td>
                                    </tr>";
                                }
                            @endphp
                        
                        <tr>
                            <td colspan = "3"><b>Subtotal</b></td>
                            <td colspan = "2" style='text-align  : right !important;'>{{ number_format($purchaseinvoice['subtotal_amount_total'],2,',','.') }}</td>
                        </tr>
                        <tr>
                            <td colspan = "3"><b>Diskon (%)</b></td>
                            <td colspan = "1" style='text-align  : right !important;'>{{ $purchaseinvoice['discount_percentage_total'] }}
                            </td>
                            <td colspan = "1" style='text-align  : right !important;'>{{ number_format($purchaseinvoice['discount_amount_total'],2,',','.') }}</td>
                        </tr>
                        <tr>
                            <td colspan = "3"><b>PPN (%)</b></td>
                            <td colspan = "1" style='text-align  : right !important;'>{{ $purchaseinvoice['tax_ppn_percentage'] }}
                            </td>
                            <td colspan = "1" style='text-align  : right !important;'>{{ number_format($purchaseinvoice['tax_ppn_amount'],2,',','.') }}</td>
                        </tr>
                        <tr>
                            <td colspan = "3"><b>Selisih</b></td>
                            <td colspan = "2" style='text-align  : right !important;'>{{ number_format($purchaseinvoice['shortover_amount'],2,',','.') }}</td>
                        </tr>
                        <tr>
                            <td colspan = "3"><b>Total Jumlah</b></td>
                            <td colspan = "2" style='text-align  : right !important;'>{{ number_format($purchaseinvoice['total_amount'],2,',','.') }}</td>
                        </tr>
                        <tr>
                            <td colspan = "3"><b>Dibayar</b></td>
                            <td colspan = "2" style='text-align  : right !important;'>{{ number_format($purchaseinvoice['paid_amount'],2,',','.') }}</td>
                        </tr>
                        <tr>
                            <td colspan = "3"><b>Sisa</b></td>
                            <td colspan = "2" style='text-align  : right !important;'>{{ number_format($purchaseinvoice['owing_amount'],2,',','.') }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
</div>
</div>



@stop

@section('footer')
    
@stop

@section('css')
    
@stop