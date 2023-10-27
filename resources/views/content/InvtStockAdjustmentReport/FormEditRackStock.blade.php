@extends('adminlte::page')

@section('title', 'MOZAIC Minimarket')
@section('js')
    <script>
        $(document).ready(function(){
            var rack_line = {!! json_encode($data['rack_line']) !!};
            var rack_column = {!! json_encode($data['rack_column']) !!};
            if (rack_line == null) {
                $('#rack_line').select2('val',' ');
            }
            if (rack_column == null) {
                $('#rack_column').select2('val',' ');
            }
        });
    </script>
@endsection
@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ url('/stock-adjustment-report') }}">Stok Barang</a></li>
        <li class="breadcrumb-item active" aria-current="page">Ubah Rak Barang</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    Form Ubah Rak Barang
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
            <button onclick="location.href='{{ url('stock-adjustment-report') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
        </div>
    </div>

    <form method="post" action="{{ route('process-edit-rack-stock-adjustment-report') }}" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Baris</a>
                        {!! Form::select(0, $rack_line, $data['rack_line'],['class' => 'form-control selection-search-clear select-form','name'=>'rack_line','id'=>'rack_line']) !!} 
                        <input class="form-control input-bb" name="item_stock_id" id="item_stock_id" type="text" autocomplete="off" value="{{ $data['item_stock_id'] }}" hidden/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Kolom</a>
                        {!! Form::select(0, $rack_column, $data['rack_column'],['class' => 'form-control selection-search-clear select-form','name'=>'rack_column','id'=>'rack_column']) !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer text-muted">
            <div class="form-actions float-right">
                <button type="reset" name="Reset" class="btn btn-danger" onclick="location.href='{{ url('stock-adjustment-report') }}'"><i class="fa fa-times"></i> Batal</button>
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