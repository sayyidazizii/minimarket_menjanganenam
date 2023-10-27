@extends('adminlte::page')

@section('title', 'MOZAIC Minimarket')

@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ url('core-supplier') }}">Daftar Pemasok</a></li>
        <li class="breadcrumb-item active" aria-current="page"> Ubah Pemasok</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    Form Ubah Pemasok
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
@endif
</div>
    <div class="card border border-dark">
    <div class="card-header border-dark bg-dark">
        <h5 class="mb-0 float-left">
            Form Ubah
        </h5>
        <div class="float-right">
            <button onclick="location.href='{{ url('core-supplier') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
        </div>
    </div>

    <form method="post" action="{{ url('core-supplier/process-edit') }}" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Nama Pemasok<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="supplier_name" id="supplier_name" type="text" autocomplete="off" value="{{ $data['supplier_name'] }}"/>
                        <input class="form-control input-bb" name="supplier_id" id="supplier_id" type="text" autocomplete="off" value="{{ $data['supplier_id'] }}" hidden/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Telp Pemasok</a>
                        <input class="form-control input-bb" name="supplier_phone" id="supplier_phone" type="text" autocomplete="off" value="{{ $data['supplier_phone'] }}"/>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="form-group">
                        <a class="text-dark">Alamat</a>
                        <textarea class="form-control input-bb" name="supplier_address" id="supplier_address" type="text" autocomplete="off">{{ $data['supplier_address'] }}</textarea>
                    </div>
                </div>
            </div>
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