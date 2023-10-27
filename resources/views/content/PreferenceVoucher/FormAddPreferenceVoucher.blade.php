@extends('adminlte::page')

@section('title', 'MOZAIC Minimarket')
@section('js')
<script>
    function function_elements_add(name, value){
        console.log("name " + name);
        console.log("value " + value);
		$.ajax({
				type: "POST",
				url : "{{route('add-elements-preference-voucher')}}",
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
				url : "{{route('reset-elements-preference-voucher')}}",
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
        <li class="breadcrumb-item"><a href="{{ url('/preference-voucher') }}">Daftar Voucher</a></li>
        <li class="breadcrumb-item active" aria-current="page">Tambah Voucher</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    Form Tambah Voucher
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
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel"></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Diskon Tidak Boleh Melebihi 100 %
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
    <div class="card border border-dark">
    <div class="card-header border-dark bg-dark">
        <h5 class="mb-0 float-left">
            Form Tambah
        </h5>
        <div class="float-right">
            <button onclick="location.href='{{ url('preference-voucher') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
        </div>
    </div>

    <form method="post" action="{{ route('add-process-preference-voucher') }}" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Kode Voucher<a class='red'> *</a></a>
                        <input class="form-control input-bb" name="voucher_code" id="voucher_code" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value)" value="{{ $datases['voucher_code'] }}"/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Nominal<a class='red'> *</a></a>
                        <input class="form-control input-bb text-right" type="number" name="voucher_amount" id="voucher_amount" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value)" value="{{ $datases['voucher_amount'] }}"/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Tanggal Awal<a class='red'> *</a></a>
                        <input style="width: 50%" class="form-control input-bb" name="start_voucher" id="start_voucher" type="date" data-date-format="dd-mm-yyyy" autocomplete="off" onchange="function_elements_add(this.name, this.value)" value="{{ $datases['start_voucher'] ? $datases['start_voucher'] : date('Y-m-d') }}"/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Tanggal Akhir<a class='red'> *</a></a>
                        <input style="width: 50%" class="form-control input-bb" name="end_voucher" id="end_voucher" type="date" data-date-format="dd-mm-yyyy" autocomplete="off" onchange="function_elements_add(this.name, this.value)" value="{{ $datases['end_voucher'] ? $datases['end_voucher'] : date('Y-m-d') }}"/>
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