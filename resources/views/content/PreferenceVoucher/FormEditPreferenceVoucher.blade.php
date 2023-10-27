@extends('adminlte::page')

@section('title', 'MOZAIC Minimarket')

@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ url('preference-voucher') }}">Daftar Voucher</a></li>
        <li class="breadcrumb-item active" aria-current="page"> Ubah Voucher</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    Form Ubah Voucher
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
            <button onclick="location.href='{{ url('preference-voucher') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
        </div>
    </div>

    <form method="post" action="{{ url('preference-voucher/edit-process') }}" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Kode Voucher<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="voucher_code" id="voucher_code" type="text" autocomplete="off" value="{{ $data['voucher_code'] }}"/>
                        <input class="form-control input-bb" name="voucher_id" id="voucher_id" type="text" autocomplete="off" value="{{ $data['voucher_id'] }}" hidden/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Nominal<a class='red'> *</a></a>
                        <input class="form-control input-bb text-right" type="number" name="voucher_amount" id="voucher_amount" type="text" autocomplete="off" value="{{ $data['voucher_amount'] }}"/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Tanggal Awal<a class='red'> *</a></a>
                        <input style="width: 50%" class="form-control input-bb" name="start_voucher" id="start_voucher" type="date" data-date-format="dd-mm-yyyy" autocomplete="off" value="{{ $data['start_voucher'] }}"/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Tanggal Akhir<a class='red'> *</a></a>
                        <input style="width: 50%" class="form-control input-bb" name="end_voucher" id="end_voucher" type="date" data-date-format="dd-mm-yyyy" autocomplete="off" value="{{ $data['end_voucher'] }}"/>
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