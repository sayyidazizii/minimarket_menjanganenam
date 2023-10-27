@inject('PurchaseReturn', 'App\Http\Controllers\PurchaseReturnController')
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
        <li class="breadcrumb-item"><a href="{{ url('purchase-return') }}">Daftar Retur Pembelian</a></li>
        <li class="breadcrumb-item active" aria-current="page">Detail Retur Pembelian</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    Detail Retur Pembelian
</h3>
<br/>

<div class="card border border-dark">
    <div class="card-header border-dark bg-dark">
        <h5 class="mb-0 float-left">
            Daftar
        </h5>
        <div class="float-right">
            <button onclick="location.href='{{ url('purchase-return') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
        </div>
    </div>
    <div class="card-body">
        <div class="row form-group">
            <div class="col-md-6">
                <div class="form-group">
                    <a class="text-dark">Nama Pemasok<a class='red'> *</a></a>
                    {!! Form::select('supplier_id', $suppliers, $purchasereturn['supplier_id'], ['class' => 'form-control selection-search-clear select-form', 'id' => 'supplier_id', 'name' => 'supplier_id', 'onchange' => 'function_elements_add(this.name, this.value)', 'disabled']) !!}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <a class="text-dark">Nama Gudang<a class='red'> *</a></a>
                    {!! Form::select('warehouse_id',  $warehouses, $purchasereturn['warehouse_id'], ['class' => 'form-control selection-search-clear select-form', 'id' => 'warehouse_id', 'name' => 'warehouse_id', 'onchange' => 'function_elements_add(this.name, this.value)', 'disabled']) !!}
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <a class="text-dark">Tanggal Retur Pembelian<a class='red'> *</a></a>
                    <input class="form-control input-bb" name="purchase_return_date" id="purchase_return_date" type="date" data-date-format="dd-mm-yyyy" autocomplete="off" value="{{ $purchasereturn['purchase_return_date'] }}" readonly />
                </div>
            </div>
            <div class="col-md-6">

            </div>
            <div class="col-md-9 mt-3">
                <div class="form-group">
                    <a class="text-dark">Keterangan</a>
                    <textarea class="form-control input-bb" name="purchase_return_remark" id="purchase_return_remark" type="text" autocomplete="off" readonly >{{ $purchasereturn['purchase_return_remark'] }}</textarea>
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
                            <th style='text-align:center'>Biaya Satuan</th>
                            <th style='text-align:center'>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if(!$purchasereturnitem){
                            echo "<tr><th colspan='7' style='text-align  : center !important;'>Data Kosong</th></tr>";
                        } else {
                            $total_quantity = 0;
                            $subtotal = 0;
                            foreach ($purchasereturnitem AS $val){
                                echo"
                                <tr>
                                            <td style='text-align  : left !important;'>".$PurchaseReturn->getItemName($val['item_id'])."</td>
                                            <td style='text-align  : right !important;'>".$val['purchase_item_quantity']."</td>
                                            <td style='text-align  : right !important;'>".number_format($val['purchase_item_cost'],2,',','.')."</td>
                                            <td style='text-align  : right !important;'>".number_format($val['purchase_item_subtotal'],2,',','.')."</td>";
                                            ?>
                                            
                                            <?php
                                            echo"
                                        </tr>
                                    ";
                                    $subtotal += $val['purchase_item_subtotal'];
                                    $total_quantity += $val['purchase_item_quantity'];

                            }
                            echo"
                            <th style='text-align  : center' colspan='1'>Total</th>
                            <th style='text-align  : right'>".$total_quantity."</th>
                            <th style='text-align  : center'></th>
                            <th style='text-align  : right'>".number_format($subtotal,2,',','.')."</th>
                            ";
                        }
                    ?>
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