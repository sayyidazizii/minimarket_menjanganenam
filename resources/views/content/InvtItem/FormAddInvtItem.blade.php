@extends('adminlte::page')

@section('title', 'MOZAIC Minimarket')
@section('js')
<script>
    $(document).ready(function(){
        item_unit_id_2 = {!! json_encode($items['item_unit_id_2']) !!};
        item_unit_id_3 = {!! json_encode($items['item_unit_id_3']) !!};
        item_unit_id_4 = {!! json_encode($items['item_unit_id_4']) !!};

        if (item_unit_id_2 == null) {
            $('#item_unit_id_2').select2('val','0');
        }
        if (item_unit_id_3 == null) {
            $('#item_unit_id_3').select2('val','0');
        }
        if (item_unit_id_4 == null) {
            $('#item_unit_id_4').select2('val','0');
        }
    });

    function function_elements_add(name, value){
        var item_category_id = $('#item_category_id').val();
		$.ajax({
				type: "POST",
				url : "{{route('add-item-elements')}}",
				data : {
                    'name'      : name, 
                    'value'     : value,
                    '_token'    : '{{csrf_token()}}'
                },
				success: function(msg){
			}
		});

        if (name == 'item_cost_1') {
            $.ajax({
                    type: "POST",
                    url : "{{route('count-margin-add-item')}}",
                    data : {
                        'item_unit_cost'    : value,
                        'item_category_id'  : item_category_id,
                        '_token'            : '{{csrf_token()}}'
                    },
                    success: function(msg){
                        $('#item_price_1').val(msg);
                }
            });

        } else if (name == 'item_cost_2') {
            $.ajax({
                    type: "POST",
                    url : "{{route('count-margin-add-item')}}",
                    data : {
                        'item_unit_cost'    : value,
                        'item_category_id'  : item_category_id,
                        '_token'            : '{{csrf_token()}}'
                    },
                    success: function(msg){
                        $('#item_price_2').val(msg);
                }
            });

        } else if (name == 'item_cost_3') {
            $.ajax({
                    type: "POST",
                    url : "{{route('count-margin-add-item')}}",
                    data : {
                        'item_unit_cost'    : value,
                        'item_category_id'  : item_category_id,
                        '_token'            : '{{csrf_token()}}'
                    },
                    success: function(msg){
                        $('#item_price_3').val(msg);
                }
            });
            
        } else if (name == 'item_cost_4') {
            $.ajax({
                    type: "POST",
                    url : "{{route('count-margin-add-item')}}",
                    data : {
                        'item_unit_cost'    : value,
                        'item_category_id'  : item_category_id,
                        '_token'            : '{{csrf_token()}}'
                    },
                    success: function(msg){
                        $('#item_price_4').val(msg);
                }
            });
            
        }
	}

    function reset_add(){
		$.ajax({
				type: "GET",
				url : "{{route('add-reset-item')}}",
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
        <li class="breadcrumb-item"><a href="{{ url('item') }}">Daftar Barang</a></li>
        <li class="breadcrumb-item active" aria-current="page">Tambah Barang</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    Form Tambah Barang
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
            Form Tambah
        </h5>
        <div class="float-right">
            <button onclick="location.href='{{ url('item') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
        </div>
    </div>

    <form method="post" action="{{ route('process-add-item') }}" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" href="#barang" role="tab" data-toggle="tab">Data Barang</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#kemasan" role="tab" data-toggle="tab">Kemasan</a>
                </li>
                {{-- <li class="nav-item">
                    <a class="nav-link" href="#barcode" role="tab" data-toggle="tab">Barcode</a>
                  </li> --}}
              </ul>
              <div class="tab-content">
                <div role="tabpanel" class="tab-pane fade show active" id="barang">
                    <div class="row form-group mt-5">
                        <div class="col-md-6">
                            <div class="form-group">
                                <a class="text-dark">Nama Kategori Barang<a class='red'> *</a></a>
                                {!! Form::select('item_category_id', $category, $items['item_category_id'],['class' => 'form-control selection-search-clear select-form','name'=>'item_category_id','id'=>'item_category_id','onchange' => 'function_elements_add(this.name, this.value)']) !!}  
                            </div>
                        </div>
                        <div class="col-md-6">
                            {{-- <div class="form-group">
                                <a class="text-dark">Nama Kategori Barang<a class='red'> *</a></a>
                                <input class="form-control input-bb" name="category_name" id="category_name" type="text" autocomplete="off"/>
                            </div> --}}
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <a class="text-dark">Kode Barang<a class='red'> *</a></a>
                                <input class="form-control input-bb" name="item_code" id="item_code" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value)" value="{{ $items['item_code'] }}"/>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <a class="text-dark">Nama Barang<a class='red'> *</a></a>
                                <input class="form-control input-bb" name="item_name" id="item_name" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value)" value="{{ $items['item_name'] }}"/>
                            </div>
                        </div>
                        {{-- <div class="col-md-6">
                            <div class="form-group">
                                <a class="text-dark">Barcode Barang</a>
                                <input class="form-control input-bb" name="item_barcode" id="item_barcode" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value)" value="{{ $items['item_barcode'] }}"/>
                            </div>
                        </div> --}}
                        <div class="col-md-8 mt-3">
                            <div class="form-group">
                                <a class="text-dark">Keterangan</a>
                                <textarea class="form-control input-bb" name="item_remark" id="item_remark" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value)">{{ $items['item_remark'] }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane fade" id="kemasan">
                    <div>
                        <h6 class="mt-3"><b>Kemasan 1</b></h6>
                        <div class="row form-group mt-2">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <a class="text-dark">Satuan Barang 1<a class='red'> *</a></a>
                                    {!! Form::select('item_unit_id', $itemunits, $items['item_unit_id_1'],['class' => 'form-control selection-search-clear select-form','name'=>'item_unit_id_1','id'=>'item_unit_id_1','onchange' => 'function_elements_add(this.name, this.value)']) !!}  
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <a class="text-dark">Kuantitas Standar 1<a class='red'> *</a></a>
                                    <input class="form-control input-bb" name="item_quantity_1" id="item_quantity_1" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value)" value="{{ $items['item_quantity_1'] }}"/>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <a class="text-dark">Harga Beli 1<a class='red'> *</a></a>
                                    <input class="form-control input-bb" name="item_cost_1" id="item_cost_1" type="number" autocomplete="off" onchange="function_elements_add(this.name, this.value)" value="{{ $items['item_cost_1'] }}"/>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <a class="text-dark">Harga Jual 1<a class='red'> *</a></a>
                                    <input class="form-control input-bb" name="item_price_1" id="item_price_1" type="number" autocomplete="off" onchange="function_elements_add(this.name, this.value)" value="{{ $items['item_price_1'] }}"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5">
                        <h6 class="mt-3"><b>Kemasan 2</b></h6>
                        <div class="row form-group mt-2">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <a class="text-dark">Satuan Barang 2</a>
                                    {!! Form::select('item_unit_id', $itemunits, $items['item_unit_id_2'],['class' => 'form-control selection-search-clear select-form','name'=>'item_unit_id_2','id'=>'item_unit_id_2','onchange' => 'function_elements_add(this.name, this.value)']) !!}  
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <a class="text-dark">Kuantitas Standar 2</a>
                                    <input class="form-control input-bb" name="item_quantity_2" id="item_quantity_2" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value)" value="{{ $items['item_quantity_2'] }}"/>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <a class="text-dark">Harga Beli 2</a>
                                    <input class="form-control input-bb" name="item_cost_2" id="item_cost_2" type="number" autocomplete="off" onchange="function_elements_add(this.name, this.value)" value="{{ $items['item_cost_2'] }}"/>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <a class="text-dark">Harga Jual 2</a>
                                    <input class="form-control input-bb" name="item_price_2" id="item_price_2" type="number" autocomplete="off" onchange="function_elements_add(this.name, this.value)" value="{{ $items['item_price_2'] }}"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5">
                        <h6 class="mt-3"><b>Kemasan 3</b></h6>
                        <div class="row form-group mt-2">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <a class="text-dark">Satuan Barang 3</a>
                                    {!! Form::select('item_unit_id', $itemunits, $items['item_unit_id_3'],['class' => 'form-control selection-search-clear select-form','name'=>'item_unit_id_3','id'=>'item_unit_id_3','onchange' => 'function_elements_add(this.name, this.value)']) !!}  
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <a class="text-dark">Kuantitas Standar 3</a>
                                    <input class="form-control input-bb" name="item_quantity_3" id="item_quantity_3" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value)" value="{{ $items['item_quantity_3'] }}"/>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <a class="text-dark">Harga Beli 3</a>
                                    <input class="form-control input-bb" name="item_cost_3" id="item_cost_3" type="number" autocomplete="off" onchange="function_elements_add(this.name, this.value)" value="{{ $items['item_cost_3'] }}"/>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <a class="text-dark">Harga Jual 3</a>
                                    <input class="form-control input-bb" name="item_price_3" id="item_price_3" type="number" autocomplete="off" onchange="function_elements_add(this.name, this.value)" value="{{ $items['item_price_3'] }}"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5">
                        <h6 class="mt-3"><b>Kemasan 4</b></h6>
                        <div class="row form-group mt-2">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <a class="text-dark">Satuan Barang 4</a>
                                    {!! Form::select('item_unit_id', $itemunits, $items['item_unit_id_4'],['class' => 'form-control selection-search-clear select-form','name'=>'item_unit_id_4','id'=>'item_unit_id_4','onchange' => 'function_elements_add(this.name, this.value)']) !!}  
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <a class="text-dark">Kuantitas Standar 4</a>
                                    <input class="form-control input-bb" name="item_quantity_4" id="item_quantity_4" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value)" value="{{ $items['item_quantity_4'] }}"/>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <a class="text-dark">Harga Beli 4</a>
                                    <input class="form-control input-bb" name="item_cost_4" id="item_cost_4" type="number" autocomplete="off" onchange="function_elements_add(this.name, this.value)" value="{{ $items['item_cost_4'] }}"/>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <a class="text-dark">Harga Jual 4</a>
                                    <input class="form-control input-bb" name="item_price_4" id="item_price_4" type="number" autocomplete="off" onchange="function_elements_add(this.name, this.value)" value="{{ $items['item_price_4'] }}"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- <div role="tabpanel" class="tab-pane fade" id="barcode">
                    <div>
                        <h6 class="mt-3"><b>Kemasan 1</b></h6>
                        <div class="row form-group mt-2">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <a class="text-dark">Barcode 1<a class='red'> *</a></a>
                                    <input class="form-control input-bb" name="item_barcode_1" id="item_barcode_1" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value)" value="{{ $items['item_barcode_1'] }}"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5">
                        <h6 class="mt-3"><b>Kemasan 2</b></h6>
                        <div class="row form-group mt-2">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <a class="text-dark">Barcode 2</a>
                                    <input class="form-control input-bb" name="item_barcode_2" id="item_barcode_2" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value)" value="{{ $items['item_barcode_2'] }}"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5">
                        <h6 class="mt-3"><b>Kemasan 3</b></h6>
                        <div class="row form-group mt-2">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <a class="text-dark">Barcode 3</a>
                                    <input class="form-control input-bb" name="item_barcode_3" id="item_barcode_3" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value)" value="{{ $items['item_barcode_3'] }}"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5">
                        <h6 class="mt-3"><b>Kemasan 4</b></h6>
                        <div class="row form-group mt-2">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <a class="text-dark">Barcode 4</a>
                                    <input class="form-control input-bb" name="item_barcode_4" id="item_barcode_4" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value)" value="{{ $items['item_barcode_4'] }}"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> --}}
              </div>
            {{-- <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Kode Kategori Barang<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="category_code" id="category_code" type="text" autocomplete="off"/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Nama Kategori Barang<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="category_name" id="category_name" type="text" autocomplete="off"/>
                    </div>
                </div>
                <div class="col-md-8 mt-3">
                    <div class="form-group">
                        <a class="text-dark">Keterangan<a class='red'> *</a></a>
                        <textarea class="form-control input-bb" name="category_remark" id="category_remark" type="text" autocomplete="off"></textarea>
                    </div>
                </div>
            </div> --}}
        </div>
        <div class="card-footer text-muted">
            <div class="form-actions float-right">
                <button type="reset" name="Reset" class="btn btn-danger" onclick="reset_add();"><i class="fa fa-times"></i> Batal</button>
                <button type="submit" name="Save" class="btn btn-success" title="Save"><i class="fa fa-check"></i> Simpan</button>
            </div>
        </div>
    </div>
    </div>
</form>

@stop

@section('footer')
    
@stop

@section('css')
    
@stop