@inject('SalesInvoice','App\Http\Controllers\SalesInvoiceController' )
@extends('adminlte::page')

@section('title', 'MOZAIC Minimarket')
@section('js')
@stop
@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ url('ppn') }}">Daftar Ppn</a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit PPN</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    Edit PPN
</h3>
<br/>

<div class="card border border-dark">
    <div class="card-header border-dark bg-dark">
        <h5 class="mb-0 float-left">
            Daftar
        </h5>
        <div class="float-right">
            <button onclick="location.href='{{ url('ppn') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
        </div>
    </div>
    <form method="post" action="{{route('process-edit-ppn-preference-company')}}" enctype="multipart/form-data">
        @csrf
    <div class="card-body">
        <div class="row form-group">
            <div class="col-md-6">
                <div class="form-group">
                    <input class="form-control input-bb" name="company_id" id="company_id" type="text" autocomplete="off" value="{{ $preferencecompany['company_id'] }}" hidden readonly/>

                    <a class="text-dark">PPN Amount<a class='red'> *</a></a>
                    <input class="form-control input-bb" name="ppn_percentage" id="ppn_percentage" type="text" autocomplete="off" value="{{ $preferencecompany['ppn_percentage'] }}"/>
                </div>
            </div>
        </div>
        <div class="card-footer text-muted">
            <div class="form-actions float-right">
                <button type="reset" name="Reset" class="btn btn-danger btn-sm" onClick="window.location.reload();"><i class="fa fa-times"></i> Batal</button>
                <button type="submit" name="Save" class="btn btn-primary btn-sm" title="Save"><i class="fa fa-check"></i> Simpan</button>
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