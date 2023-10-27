@extends('adminlte::page')

@section('title', 'MOZAIC Minimarket')

@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ url('sales-customer') }}">Daftar Anggota</a></li>
        <li class="breadcrumb-item active" aria-current="page">Ubah Anggota</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    Form Ubah Anggota
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
            Form Ubah
        </h5>
        <div class="float-right">
            <button onclick="location.href='{{ url('sales-customer') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
        </div>
    </div>

    <?php 
            // if (empty($coresection)){
            //     $coresection['section_name'] = '';
            // }
        ?>

    <form method="post" action="{{ route('process-edit-sales-customer') }}" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Nama Anggota<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="customer_name" id="customer_name" type="text" autocomplete="off" value="{{ $data['customer_name'] }}"/>
                        <input class="form-control input-bb" name="customer_id" id="customer_id" type="text" autocomplete="off" value="{{ $data['customer_id'] }}" hidden/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Status<a class='red'> *</a></a>
                        <?php 
                            $status = [
                                0 => 'Aktif',
                                1 => 'Non Aktif'
                            ];
                        ?>
                        {!! Form::select(0, $status, $data['customer_status'], ['class' => 'selection-search-clear select-form', 'id' => 'customer_status', 'name' => 'customer_status']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Jenis Kelamin<a class='red'> *</a></a>
                        {!! Form::select(0, $listgender, $data['customer_gender'], ['class' => 'selection-search-clear select-form', 'id' => 'customer_gender', 'name' => 'customer_gender']) !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer text-muted">
            <div class="form-actions float-right">
                <button type="reset" name="Reset" class="btn btn-danger" onclick="location.reload();"><i class="fa fa-times"></i> Batal</button>
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