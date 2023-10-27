@extends('adminlte::page')

@section('title', 'MOZAIC Minimarket')
@section('js')
<script>
    $(document).ready(function(){
        item_quantity_1 = document.getElementById("item_quantity_1").value;
        item_quantity_2 = document.getElementById("item_quantity_2").value;
        item_quantity_3 = document.getElementById("item_quantity_3").value;
        item_quantity_4 = document.getElementById("item_quantity_4").value;

        // console.log(item_unit_id_1);
        if (item_quantity_1 == '') {
            $('#item_unit_id_1').select2('val',0);
        }
        if (item_quantity_2 == '') {
            $('#item_unit_id_2').select2('val','0');
        }
        if (item_quantity_3 == '') {
            $('#item_unit_id_3').select2('val','0');
        }
        if (item_quantity_4 == '') {
            $('#item_unit_id_4').select2('val','0');
        }
    });

    function function_elements_add(name, value){
        var item_category_id = $('#item_category_id').val();
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
</script>
@stop
@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ url('item') }}">Daftar Barang</a></li>
        <li class="breadcrumb-item active" aria-current="page">Ubah Barang</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    Form Ubah Barang
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

    <form method="post" action="{{ route('process-edit-item') }}" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" href="#barang" role="tab" data-toggle="tab">Data Barang</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#kemasan" role="tab" data-toggle="tab">Kemasan</a>
                </li>
              </ul>
              <div class="tab-content">
                <div role="tabpanel" class="tab-pane fade show in active" id="barang">
                    <div class="row form-group mt-5">
                        <div class="col-md-6">
                            <div class="form-group">
                                <a class="text-dark">Nama Kategori Barang<a class='red'> *</a></a>
                                {!! Form::select('item_category_id',  $category, $items['item_category_id'], ['class' => 'form-control selection-search-clear select-form','name'=>'item_category_id','id'=>'item_category_id']) !!}
                                {{-- <select name="item_category_id" id="category_id" class="form-control">
                                    @foreach ($category as $row )
                                        <option value="{{ $row['item_category_id'] }}">{{ $row['item_category_name'] }}</option>
                                    @endforeach
                                </select> --}}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                    {{-- <a class="text-dark">Status Barang<a class='red'> *</a></a> --}}
                                {{-- <select name="item_status" id="item_status" class="form-control">
                                    <option value="0">Aktif</option>
                                    <option value="1">Not Aktif</option>
                                </select> --}}
                                {{-- {!! Form::select(0,  $status, $items['item_status'], ['class' => 'form-control selection-search-clear select-form','name' => 'item_status', 'id' => 'item_status']) !!} --}}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <a class="text-dark">Kode Barang<a class='red'> *</a></a>
                                <input class="form-control input-bb" name="item_code" id="item_code" type="text" autocomplete="off" value="{{ $items['item_code'] }}"/>
                                <input class="form-control input-bb" name="item_id" id="item_id" type="text" autocomplete="off" value="{{ $items['item_id'] }}" hidden/>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <a class="text-dark">Nama Barang<a class='red'> *</a></a>
                                <input class="form-control input-bb" name="item_name" id="item_name" type="text" autocomplete="off" value="{{ $items['item_name'] }}"/>
                            </div>
                        </div>
                        {{-- <div class="col-md-6">
                            <div class="form-group">
                                <a class="text-dark">Barcode Barang</a>
                                <input class="form-control input-bb" name="item_barcode" id="item_barcode" type="text" autocomplete="off" value="{{ $items['item_barcode'] }}"/>
                            </div>
                        </div> --}}
                        <div class="col-md-8 mt-3">
                            <div class="form-group">
                                <a class="text-dark">Keterangan</a>
                                <textarea class="form-control input-bb" name="item_remark" id="item_remark" type="text" autocomplete="off">{{ $items['item_remark'] }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane fade" id="kemasan">
                    @foreach ($item_packge as $row)
                        @if ($row['order'] == 1)
                            <div>
                                <h6 class="mt-3"><b>Kemasan 1</b></h6>
                                <div class="row form-group mt-2">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <a class="text-dark">Satuan Barang 1<a class='red'> *</a></a>
                                            {!! Form::select('item_unit_id', $itemunits, $row['item_unit_id'],['class' => 'form-control selection-search-clear select-form','name'=>'item_unit_id_1','id'=>'item_unit_id_1']) !!}  
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <a class="text-dark">Kuantitas Standar 1<a class='red'> *</a></a>
                                            <input class="form-control input-bb" name="item_quantity_1" id="item_quantity_1" type="text" autocomplete="off" value="{{ $row['item_default_quantity'] }}"/>
                                            <input class="form-control input-bb" name="item_packge_id_1" id="item_packge_id_1" type="text" autocomplete="off" value="{{ $row['item_packge_id'] }}" hidden/>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <a class="text-dark">Harga Beli 1<a class='red'> *</a></a>
                                            <input class="form-control input-bb" name="item_cost_1" id="item_cost_1" type="number" autocomplete="off" onchange="function_elements_add(this.name, this.value)" value="{{ $row['item_unit_cost'] }}"/>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <a class="text-dark">Harga Jual 1<a class='red'> *</a></a>
                                            <input class="form-control input-bb" name="item_price_1" id="item_price_1" type="number" autocomplete="off" value="{{ $row['item_unit_price'] }}"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if ($row['order'] == 2)
                            <div class="mt-5">
                                <h6 class="mt-3"><b>Kemasan 2</b></h6>
                                <div class="row form-group mt-2">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <a class="text-dark">Satuan Barang 2</a>
                                            {!! Form::select('item_unit_id', $itemunits, $row['item_unit_id'],['class' => 'form-control selection-search-clear select-form','name'=>'item_unit_id_2','id'=>'item_unit_id_2']) !!}  
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <a class="text-dark">Kuantitas Standar 2</a>
                                            <input class="form-control input-bb" name="item_quantity_2" id="item_quantity_2" type="text" autocomplete="off" value="{{ $row['item_default_quantity'] }}"/>
                                            <input class="form-control input-bb" name="item_packge_id_2" id="item_packge_id_2" type="text" autocomplete="off" value="{{ $row['item_packge_id'] }}" hidden/>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <a class="text-dark">Harga Beli 2</a>
                                            <input class="form-control input-bb" name="item_cost_2" id="item_cost_2" type="number" autocomplete="off" onchange="function_elements_add(this.name, this.value)" value="{{ $row['item_unit_cost'] }}"/>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <a class="text-dark">Harga Jual 2</a>
                                            <input class="form-control input-bb" name="item_price_2" id="item_price_2" type="number" autocomplete="off" value="{{ $row['item_unit_price'] }}"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if ($row['order'] == 3)
                            <div class="mt-5">
                                <h6 class="mt-3"><b>Kemasan 3</b></h6>
                                <div class="row form-group mt-2">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <a class="text-dark">Satuan Barang 3</a>
                                            {!! Form::select('item_unit_id', $itemunits, $row['item_unit_id'],['class' => 'form-control selection-search-clear select-form','name'=>'item_unit_id_3','id'=>'item_unit_id_3']) !!}  
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <a class="text-dark">Kuantitas Standar 3</a>
                                            <input class="form-control input-bb" name="item_quantity_3" id="item_quantity_3" type="text" autocomplete="off" value="{{ $row['item_default_quantity'] }}"/>
                                            <input class="form-control input-bb" name="item_packge_id_3" id="item_packge_id_3" type="text" autocomplete="off" value="{{ $row['item_packge_id'] }}" hidden/>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <a class="text-dark">Harga Beli 3</a>
                                            <input class="form-control input-bb" name="item_cost_3" id="item_cost_3" type="number" autocomplete="off" onchange="function_elements_add(this.name, this.value)" value="{{ $row['item_unit_cost'] }}"/>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <a class="text-dark">Harga Jual 3</a>
                                            <input class="form-control input-bb" name="item_price_3" id="item_price_3" type="number" autocomplete="off" value="{{ $row['item_unit_price'] }}"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if ($row['order'] == 4)
                            <div class="mt-5">
                                <h6 class="mt-3"><b>Kemasan 4</b></h6>
                                <div class="row form-group mt-2">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <a class="text-dark">Satuan Barang 4</a>
                                            {!! Form::select('item_unit_id', $itemunits, $row['item_unit_id'],['class' => 'form-control selection-search-clear select-form','name'=>'item_unit_id_4','id'=>'item_unit_id_4']) !!}  
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <a class="text-dark">Kuantitas Standar 4</a>
                                            <input class="form-control input-bb" name="item_quantity_4" id="item_quantity_4" type="text" autocomplete="off" value="{{ $row['item_default_quantity'] }}"/>
                                            <input class="form-control input-bb" name="item_packge_id_4" id="item_packge_id_4" type="text" autocomplete="off" value="{{ $row['item_packge_id'] }}" hidden/>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <a class="text-dark">Harga Beli 4</a>
                                            <input class="form-control input-bb" name="item_cost_4" id="item_cost_4" type="number" autocomplete="off" onchange="function_elements_add(this.name, this.value)" value="{{ $row['item_unit_cost'] }}"/>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <a class="text-dark">Harga Jual 4</a>
                                            <input class="form-control input-bb" name="item_price_4" id="item_price_4" type="number" autocomplete="off" value="{{ $row['item_unit_price'] }}"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
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
                <button type="reset" name="Reset" class="btn btn-danger" onclick="window.location.reload();"><i class="fa fa-times"></i> Batal</button>
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