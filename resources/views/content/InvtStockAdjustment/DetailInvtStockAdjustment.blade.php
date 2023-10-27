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

    function function_last_balance_physical(kunci,value){
        last_data =  document.getElementById((kunci)+"_last_balance_data").value;
        last_adjustment =  document.getElementById((kunci)+"_last_balance_adjustment").value;
        
        var last_physical = parseInt(last_data) - parseInt(last_adjustment);
        $('#'+kunci+"_last_balance_physical").val(last_physical);
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

</script>
@stop
@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ url('stock-adjustment') }}">Daftar Penyesuaian Stok</a></li>
        <li class="breadcrumb-item active" aria-current="page">Detail Penyesuaian Stok</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    Detail Penyesuaian Stok
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
            Form Detail
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
                        <a class="text-dark">Nama Kategori Barang<a class='red'> *</a></a>
                        {!! Form::select('item_category_id',  $categorys, $data['item_category_id'], ['class' => 'form-control selection-search-clear select-form', 'id' => 'item_category_id', 'name' => 'item_category_id', 'onchange' => 'function_elements_add(this.name, this.value)' ,'disabled']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Nama Barang<a class='red'> *</a></a>
                        {!! Form::select('item_id',  $items, $data['item_id'], ['class' => 'form-control selection-search-clear select-form', 'id' => 'item_id', 'name' => 'item_id', 'onchange' => 'function_elements_add(this.name, this.value)' ,'disabled']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Kode Satuan<a class='red'> *</a></a>
                        {!! Form::select('item_unit_id',  $units, $data['item_unit_id'], ['class' => 'form-control selection-search-clear select-form', 'id' => 'item_unit_id', 'name' => 'item_unit_id', 'onchange' => 'function_elements_add(this.name, this.value)' ,'disabled']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Nama Gudang<a class='red'> *</a></a>
                        {!! Form::select('warehouse_id',  $warehouse, $data['warehouse_id'], ['class' => 'form-control selection-search-clear select-form', 'id' => 'warehouse_id', 'name' => 'warehouse_id', 'onchange' => 'function_elements_add(this.name, this.value)' ,'disabled']) !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <a class="text-dark">Tanggal Penyesuaian Stok<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="stock_adjustment_date" id="stock_adjustment_date" type="date" data-date-format="dd-mm-yyyy" autocomplete="off" value="{{ $data['stock_adjustment_date'] }}" onchange="function_elements_add(this.name, this.value)" readonly/>
                    </div>
                </div>
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
                            @foreach ($data_item as $row)
                                <tr>
                                  <td> {{ $ISAC->getItemName($row['item_id']) }}</td>
                                  <td>{{ $ISAC->getItemUnitName($row['item_unit_id']) }}</td>
                                  <td>{{ $ISAC->getWarehouseName($row['warehouse_id']) }} </td>
                                  <td style="text-align: right">{{ $row['last_balance_data'] }}</td>
                                  <td style="text-align: right">{{ $row['last_balance_adjustment'] }}</td>
                                  <td style="text-align: right">{{ $row['last_balance_physical'] }}</td>
                                  <td>{{ $row['stock_adjustment_item_remark'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
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