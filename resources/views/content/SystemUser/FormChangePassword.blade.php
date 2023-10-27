@extends('adminlte::page')

@section('title', 'MOZAIC Minimarket')

@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ url('/system-user/change-password', Auth::id()) }}">Ubah Password</a></li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    Form Ubah Password
</h3>
<br/>
@if(session('msg'))
<div class="alert alert-info" role="alert">
    {{session('msg')}}
</div>
@endif
    <div class="card border border-dark">
    <div class="card-header border-dark bg-dark">
        <h5 class="mb-0 float-left">
            Form Ubah Password
        </h5>
        <div class="float-right">
            <button onclick="location.href='{{ url('home') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
        </div>
    </div>

    <form method="post" action="{{ url('system-user/process-change-password') }}" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="row form-group">
                <div class="col-md-4">
                    <div class="form-group">
                        <a class="text-dark">Password Lama<a class='red'> *</a></a>
                        <input class="form-control input-bb" type="password" name="password" id="password" value="" required>
                    </div>
                    <div class="form-group">
                        <a class="text-dark">Password Baru<a class='red'> *</a></a>
                        <input class="form-control input-bb" type="password" name="new_password" id="new_password" value="" required>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer text-muted">
            <div class="form-actions float-right">
                <button type="reset" name="Reset" class="btn btn-danger" onClick="window.location.reload();"><i class="fa fa-times"></i> Batal</button>
                <button type="submit" name="Save" class="btn btn-primary" title="Save"><i class="fa fa-check"></i> Simpan</button>
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

@section('js')
    
@stop







{{-- @extends('adminlte::page')

@section('title', 'KAROTA KING')
@section('body')
<h3 class="page-title">
    Form Edit Password
</h3>
<br/>
@if(session('msg'))
<div class="alert alert-info" role="alert">
    {{session('msg')}}
</div>
@endif
    <form method="post" action="/system-user/process-change-password" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="row form-group">
                <div class="col-md-4">
                    <div class="form-group">
                        <a class="text-dark">Password<a class='red'> *</a></a>
                        <input class="form-control input-bb" type="password" name="password" id="password" value="" required>
                    </div>
                    <div class="form-group">
                        <a class="text-dark">New Password<a class='red'> *</a></a>
                        <input class="form-control input-bb" type="password" name="new_password" id="new_password" value="" required>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer text-muted">
            <div class="form-actions float-right">
                <a href="{{ url('home') }}" type="reset" name="Reset" class="btn btn-danger" ><i class="fa fa-times"></i> Kembali</a>
                <button type="submit" name="Save" class="btn btn-primary" title="Save"><i class="fa fa-check"></i> Simpan</button>
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

@section('js')
    
@stop --}}