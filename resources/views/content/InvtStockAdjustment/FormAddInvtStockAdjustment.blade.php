@inject('ISAC','App\Http\Controllers\InvtStockAdjustmentController')
@extends('adminlte::page')

@section('title', 'MOZAIC Minimarket')
@section('js')
<script>
      function function_elements_add(name, value){
        console.log("name " + name);
        console.log("value " + value);
		$.ajax({    
				type: "POST",
				url : "{{route('add-elements-purchase-return')}}",
				data : {
                    'name'      : name, 
                    'value'     : value,
                    '_token'    : '{{csrf_token()}}'
                },
				success: function(msg){
			}
		});
	}

    function function_last_balance_physical(value){
        last_data =  document.getElementById("last_balance_data").value;
        last_adjustment =  document.getElementById("last_balance_adjustment").value || 0;
        
        var last_physical = parseInt(last_data) - parseInt(last_adjustment);
        $('#last_balance_physical').val(last_physical);
    }
    // nostr = $("#no").val();
    // no = parseInt(nostr)+1;
    // for(var i = 1; i < no; i++){
    //     $('#'+i+"_last_balance_adjustment").change(function(){
    //         var last_data = $('#'+i+"_last_balance_data").val();
    //         var last_adjustment = $('#'+i+"_last_balance_adjustment").val();
    //         var last_physical = last_data - last_adjustment;
    
    //         $('#'+i+"_last_balance_physical").val(last_physical);
    //     });
    // }
    function reset_add(){
		$.ajax({
				type: "GET",
				url : "{{route('add-reset-stock-adjustment')}}",
				success: function(msg){
                    location.reload();
			}

		});
	}

    $(document).ready(function(){
        var warehouse_id    = {!! json_encode($warehouse_id) !!};
        var category_id     = {!! json_encode($category_id) !!};
        var item_id         = {!! json_encode($item_id) !!};
        var unit_id         = {!! json_encode($unit_id) !!};

        if (warehouse_id == "") {
            $('#warehouse_id').select2('val',' ');
        }
        if (category_id == "") {
            $('#item_category_id').select2('val',' ');
        }
        if (item_id == "") {
            $('#item_id').empty('val',' ');
        }
        if (unit_id == "") {
            $('#item_unit_id').empty('val',' ');
        }

        $('#item_category_id').change(function(){
            $('#item_id').empty();
            $('#item_unit_id').empty();
            var id = $('#item_category_id').val();
            $.ajax({
                url: "{{ url('select-item') }}"+'/'+id,
                type: "GET",
                dataType: "html",
                success:function(data)
                {
                    $('#item_id').html(data);

                }
            });
        });
        $('#item_id').change(function(){
            $('#item_unit_id').empty();
            var id = $('#item_id').val();
            $.ajax({
                url: "{{ url('select-item-unit') }}"+'/'+id,
                type: "GET",
                dataType: "html",
                success:function(data)
                {
                    $('#item_unit_id').html(data);

                }
            });
        });
    });
</script>
@stop
@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ url('stock-adjustment') }}">Daftar Penyesuaian Stok</a></li>
        <li class="breadcrumb-item active" aria-current="page">Tambah Penyesuaian Stok</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    Form Tambah Penyesuaian Stok
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
            Filter
        </h5>
        <div class="float-right">
            <button onclick="location.href='{{ url('stock-adjustment') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
        </div>
    </div>

    <?php 
            // if (empty($coresection)){
            //     $coresection['section_name'] = '';
            // }
        ?>
    <form method="post" action="{{ route('filter-add-stock-adjustment') }}" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Nama Gudang<a class='red'> *</a></a>
                        {!! Form::select('warehouse_id',  $warehouse, $warehouse_id, ['class' => 'form-control selection-search-clear select-form', 'id' => 'warehouse_id', 'name' => 'warehouse_id', 'onchange' => 'function_elements_add(this.name, this.value)']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Nama Kategori Barang<a class='red'> *</a></a>
                        {!! Form::select('item_category_id',  $categorys, $category_id, ['class' => 'form-control selection-search-clear select-form', 'id' => 'item_category_id', 'name' => 'item_category_id', 'onchange' => 'function_elements_add(this.name, this.value)']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Nama Barang<a class='red'> *</a></a>
                        {!! Form::select('item_id',  $items, $item_id, ['class' => 'form-control selection-search-clear select-form', 'id' => 'item_id', 'name' => 'item_id', 'onchange' => 'function_elements_add(this.name, this.value)']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Kode Satuan<a class='red'> *</a></a>
                        {!! Form::select('item_unit_id',  $units, $unit_id, ['class' => 'form-control selection-search-clear select-form', 'id' => 'item_unit_id', 'name' => 'item_unit_id', 'onchange' => 'function_elements_add(this.name, this.value)']) !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <a class="text-dark">Tanggal Penyesuaian Stok<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="stock_adjustment_date" id="stock_adjustment_date" type="date" data-date-format="dd-mm-yyyy" autocomplete="off" value="{{ $date }}" onchange="function_elements_add(this.name, this.value)"/>
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
    </form>    
</div>

<div class="card border border-dark">
    <div class="card-header border-dark bg-dark">
        <h5 class="mb-0 float-left">
            Daftar
        </h5>
    </div>
    <form method="POST" action="{{ route('process-add-stock-adjustment') }}" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="form-body form">
                <div class="table-responsive">
                    <table class="table table-bordered table-advance table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th style='text-align:center'>Nama Barang</th>
                                <th style='text-align:center'>Satuan Barang</th>
                                <th style='text-align:center'>Gudang</th>
                                <th style='text-align:center'>Stock Sistem</th>
                                <th style='text-align:center'>Penyesuaian Sistem</th>
                                <th style='text-align:center'>Selisih Stock</th>
                                <th style='text-align:center'>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                          <?php $no = 1; ?>
                            @foreach ($data as $row)
                              <?php $no++ ?>
                                <tr>
                                    <td>
                                        {{ $ISAC->getItemName($row['item_id']) }}
                                        <input type="text" name="item_id" id="item_id" value="{{ $row['item_id'] }}" hidden>
                                        <input type="text" name="item_category_id" id="item_category_id" value="{{ $row['item_category_id'] }}" hidden>
                                    </td>
                                    <td>
                                        {{ $ISAC->getItemUnitName($row['item_unit_id']) }}
                                        <input type="text" name="item_unit_id" id="item_unit_id" value="{{ $row['item_unit_id'] }}" hidden>
                                    </td>
                                    <td>
                                        {{ $ISAC->getWarehouseName($row['warehouse_id']) }}
                                        <input type="text" name="warehouse_id" id="warehouse_id" value="{{ $row['warehouse_id'] }}" hidden>
                                    </td>
                                    <td style="text-align: right">
                                        {{ $ISAC->getItemStock($row['item_id'],$row['item_unit_id'],$row['item_category_id'],$row['warehouse_id']) }}
                                        <input type="text" name="last_balance_data" id="last_balance_data" value="{{ $ISAC->getItemStock($row['item_id'],$row['item_unit_id'],$row['item_category_id'],$row['warehouse_id']) }}" hidden >
                                    </td>
                                    <td style="text-align: center">
                                        <input style="text-align: right" class="form-control input-bb" type="text" name="last_balance_adjustment" id="last_balance_adjustment" onchange="function_last_balance_physical(this.value)" autocomplete="off">
                                    </td>
                                    <td style="text-align: center">
                                        <input style="text-align: right" class="form-control input-bb" type="text" name="last_balance_physical" id="last_balance_physical" readonly>
                                    </td>
                                    <td style="text-align: center">
                                        <input class="form-control input-bb" type="text" name="stock_adjustment_item_remark" id="stock_adjustment_item_remark" autocomplete="off">
                                    </td>
                                  {{-- <td>
                                      {{ $ISAC->getItemName($row['item_id']) }}
                                      <input type="text" name="{{ $row['purchase_invoice_item_id'] }}no" id="no" value="{{ $row['purchase_invoice_item_id'] }}" hidden>
                                      <input type="text" name="{{ $row['purchase_invoice_item_id'] }}_item_id" id="{{ $no }}_item_id" value="{{ $row['item_id'] }}" hidden>
                                      <input type="text" name="{{ $row['purchase_invoice_item_id'] }}_item_category_id" id="{{ $no }}_item_category_id" value="{{ $row['item_category_id'] }}" hidden>
                                  </td>
                                  <td>
                                      {{ $ISAC->getItemUnitName($row['item_unit_id']) }}
                                      <input type="text" name="{{ $row['purchase_invoice_item_id'] }}_item_unit_id" id="{{ $no }}_item_unit_id" value="{{ $row['item_unit_id'] }}" hidden>
                                  </td>
                                  <td>
                                      {{ $ISAC->getWarehouseName($row['warehouse_id']) }}
                                      <input type="text" name="{{ $row['purchase_invoice_item_id'] }}_warehouse_id" id="{{ $no }}_warehouse_id" value="{{ $row['item_unit_id'] }}" hidden>
                                  </td>
                                  <td>
                                      {{ $ISAC->getItemStock($row['item_id'],$row['item_unit_id'],$row['item_category_id'],$row['warehouse_id']) }}
                                      <input type="text" name="{{ $row['purchase_invoice_item_id'] }}_last_balance_data" id="{{ $no }}_last_balance_data" onchange="function_last_balance_physical({{ $no }}, this.value)" value="{{ $ISAC->getItemStock($row['item_id'],$row['item_unit_id'],$row['item_category_id'],$row['warehouse_id']) }}" hidden >
                                  </td>
                                  <td style="text-align: center">
                                      <input class="form-control input-bb" type="text" name="{{ $row['purchase_invoice_item_id'] }}_last_balance_adjustment" id="{{ $no }}_last_balance_adjustment" onchange="function_last_balance_physical({{ $no }}, this.value)" autocomplete="off">
                                  </td>
                                  <td style="text-align: center">
                                      <input class="form-control input-bb" type="text" name="{{ $row['purchase_invoice_item_id'] }}_last_balance_physical" id="{{ $no }}_last_balance_physical" readonly>
                                  </td>
                                  <td style="text-align: center">
                                      <input class="form-control input-bb" type="text" name="{{ $row['purchase_invoice_item_id'] }}_stock_adjustment_item_remark" id="{{ $no }}_stock_adjustment_item_remark" autocomplete="off">
                                  </td> --}}
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card-footer text-muted">
            <div class="form-actions float-right">
                <button type="submit" name="Save" class="btn btn-success" title="Save"><i class="fa fa-check"></i> Simpan</button>
            </div>
        </div>
    </form>
</div>
</div>



@stop

@section('footer')
    
@stop

@section('css')
    
@stop