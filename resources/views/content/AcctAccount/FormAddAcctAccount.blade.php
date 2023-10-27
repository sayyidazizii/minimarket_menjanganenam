@inject('AcctAccount','App\Http\Controllers\AcctAccountController')
@extends('adminlte::page')

@section('title', 'MOZAIC Minimarket')
@section('js')
<script>
    function function_elements_add(name, value){
        console.log("name " + name);
        console.log("value " + value);
		$.ajax({
				type: "POST",
				url : "{{route('add-elements-acct-account')}}",
				data : {
                    'name'      : name, 
                    'value'     : value,
                    '_token'    : '{{csrf_token()}}'
                },
				success: function(msg){
			}
		});
	}

    function reset_add(){
		$.ajax({
				type: "GET",
				url : "{{route('add-reset-acct-account')}}",
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
        <li class="breadcrumb-item"><a href="{{ url('acct-account') }}">Daftar Perkiraan</a></li>
        <li class="breadcrumb-item active" aria-current="page">Tambah Perkiraan</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    Form Tambah Perkiraan
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
            <button onclick="location.href='{{ url('acct-account') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
        </div>
    </div>

    <?php 
            // if (empty($coresection)){
            //     $coresection['section_name'] = '';
            // }
        ?>

    <form method="post" action="{{ route('process-add-acct-account') }}" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Nomor Perkiraan<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="account_code" id="account_code" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value);" value="{{ $datases['account_code'] }}"/>

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Nama Perkiraan<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="account_name" id="account_name" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value);" value="{{ $datases['account_name'] }}"/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Golongan Perkiraan<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="account_group" id="account_group" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value);" value="{{ $datases['account_group'] }}"/>
                    </div>
                </div>
                <div class="col-md-6 mt-4">
                    <div class="form-group">
                        {!! Form::select(0, $status, $datases['account_status'],['class' => 'selection-search-clear select-form','name'=>'account_status','id'=>'account_status', 'onchange' => 'function_elements_add(this.name, this.value)']) !!} 
                        {{-- <select name="account_status" id="account_status" class="selection-search-clear select-form">
                            <option value="0">Debit</option>
                            <option value="1">Kredit</option>
                        </select> --}}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Kelompok Perkiraan<a class='red'> *</a></a>
                        {!! Form::select(0, $account_type, $datases['account_type_id'],['class' => 'selection-search-clear select-form','name'=>'account_type_id','id'=>'account_type_id', 'onchange' => 'function_elements_add(this.name, this.value)']) !!} 
                        {{-- <select name="account_type_id" id="account_type_id" class="selection-search-clear select-form">
                            <option value="0">NA - Neraca Aktif</option>
                            <option value="1">NP - Neraca Pasif</option>
                            <option value="2">RA - Rugi Laba (A)</option>
                            <option value="3">RP - Rugi Laba (B)</option>
                        </select> --}}
                    </div>
                </div>
            </div>
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