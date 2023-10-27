@inject('ItemBarcode', 'App\Http\Controllers\InvtItemBarcodeController')
@extends('adminlte::page')

@section('title', 'MOZAIC Minimarket')
@section('js')
<script>
    $('#form-prevent').on('keyup keypress', function(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) { 
            e.preventDefault();
            return false;
        }
    }); 
</script>
@endsection
@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ url('item') }}">Daftar Barang</a></li>
        <li class="breadcrumb-item active" aria-current="page">Tambah Barcode Barang</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    Form Tambah Barcode Barang
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
<div class="row">
    <div class="col-md-12">
        <div class="card border border-dark">
            <div class="card-header border-dark bg-dark">
                <h5 class="mb-0 float-left">
                    Form Tambah Barcode {{ $data_item['item_name'] }}
                </h5>
                <div class="float-right">
                    <button onclick="location.href='{{ url('item') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
                </div>
            </div>
        
            <form method="post" id="form-prevent" action="{{ route('process-add-item-barcode') }}" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="row form-group">
                        <div class="col-md-6">
                            <div class="form-group">
                                <a class="text-dark">Satuan Barang<a class='red'> *</a></a>
                                {!! Form::select('item_unit_id', $list_unit, 0,['class' => 'form-control selection-search-clear select-form','name'=>'item_unit_id','id'=>'item_unit_id']) !!}  
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <a class="text-dark">Barcode<a class='red'> *</a></a>
                                <input class="form-control input-bb" name="item_barcode" id="item_barcode" type="text" autocomplete="off" value="" autofocus/>
                                <input class="form-control input-bb" name="item_id" id="item_id" type="text" autocomplete="off" value="{{ $data_item['item_id'] }}" hidden/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-muted">
                    <div class="form-actions float-right">
                        <button type="reset" name="Reset" class="btn btn-danger" onclick="location.reload()"><i class="fa fa-times"></i> Batal</button>
                        <button type="submit" name="Save" class="btn btn-success" title="Save"><i class="fa fa-check"></i> Simpan</button>
                    </div>
                </div>
            </form>
        </div>
        </div>
    </div>
    
    <div class="card border border-dark">
        <div class="card-header border-dark bg-dark">
            <h5 class="mb-0 float-left">
                Daftar Barcode {{ $data_item['item_name'] }}
            </h5>
        </div>
    
        <div class="card-body">
            <div class="table-responsive">
                <table id="example" style="width:100%" class="table table-striped table-bordered table-hover table-full-width">
                    <thead>
                        <tr>
                            <th width="5%" style='text-align:center'>No</th>
                            <th width="40%" style='text-align:center'>Satuan Barang</th>
                            <th width="40%" style='text-align:center'>Barcode Barang</th>
                            <th width="10%" style='text-align:center'>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        @foreach($data as $row)
                        <tr>
                            <td style='text-align:center'>{{ $no++ }}.</td>
                            <td>{{ $ItemBarcode->getItemUnitName($row['item_unit_id']) }}</td>
                            <td>{{ $row['item_barcode'] }}</td>
                            <td class="text-center">
                                <a type="button" class="btn btn-outline-danger btn-sm" href="{{ url('/item-barcode/delete/'.$row['item_barcode_id']) }}">Hapus</a>
                            </td>
                        </tr>
                        @endforeach
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