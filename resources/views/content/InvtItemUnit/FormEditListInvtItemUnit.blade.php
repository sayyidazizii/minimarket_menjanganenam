@extends('adminlte::page')

@section('title', 'MOZAIC Minimarket')

@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ url('item-unit') }}">Daftar Bagian</a></li>
        <li class="breadcrumb-item active" aria-current="page">Ubah Barang Satuan</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    Form Ubah Barang Satuan
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
            <button onclick="location.href='{{ url('item-unit') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
        </div>
    </div>

    <form method="post" action="{{ url('item-unit/process-edit-item-unit') }}" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Kode Barang Satuan<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="item_unit_code" id="item_unit_code" type="text" autocomplete="off" value="{{ $itemunits['item_unit_code'] }}{{ old('item_unit_code') }}"/>
                        <input class="form-control input-bb" name="item_unit_id" id="item_unit_id" type="text" autocomplete="off" value="{{ $itemunits['item_unit_id'] }}" hidden/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Nama Barang Satuan<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="item_unit_name" id="item_unit_name" type="text" autocomplete="off" value="{{ $itemunits['item_unit_name'] }}{{ old('item_unit_name') }}"/>
                    </div>
                </div>
                <div class="col-md-8 mt-3">
                    <div class="form-group">
                        <a class="text-dark">Keterangan</a>
                        <textarea class="form-control input-bb" name="item_unit_remark" id="item_unit_remark" type="text" autocomplete="off" >{{ $itemunits['item_unit_remark'] }}{{ old('item_unit_remark') }}</textarea>
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