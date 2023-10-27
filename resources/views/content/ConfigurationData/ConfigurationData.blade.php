@extends('adminlte::page')

@section('title', 'MOZAIC Minimarket')
@section('js')
<script>
  var data = {!! json_encode(session('msg')) !!}

  if (data == 'Tutup Kasir Berhasil') {
    var mywindow = window.open('{{ route('print-close-cashier-configuration') }}','_blank');
    mywindow.print();
  }

  function buttonClick(name) {
    if (name == "download_data") {
        $('#download_data').addClass('disabled');
        $('#download_data').html('<span class=\'spinner-grow spinner-grow-sm mb-1\' role=\'status\' aria-hidden=\'true\'></span> Checking ...');
        $('#upload_data').addClass('disabled');
        $('#close_cashier').addClass('disabled');
        $('#backup_data').addClass('disabled');

        $.ajax({
				  type: "GET",
				  url : "{{route('check-data-configuration')}}",
				  success: function(data){
            if (data != '[null]') {
              $('#modal').modal('show');
              $('#download_data').removeClass('disabled');
              $('#download_data').html('<i class="fa fa-download"></i> Download Data');
              $('#upload_data').removeClass('disabled');
              $('#close_cashier').removeClass('disabled');
              $('#backup_data').removeClass('disabled');
            } else {
              $('#download_data').addClass('disabled');
              $('#download_data').html('<span class=\'spinner-grow spinner-grow-sm mb-1\' role=\'status\' aria-hidden=\'true\'></span> Proses ...');
              $('#upload_data').addClass('disabled');
              $('#close_cashier').addClass('disabled');
              $('#backup_data').addClass('disabled');
              window.location.replace("{{ route('configuration-data-dwonload') }}");
            }
			    }
		    });

    } else if (name == "upload_data") {

      $('#upload_data').addClass('disabled');
      $('#upload_data').html('<span class=\'spinner-grow spinner-grow-sm mb-1\' role=\'status\' aria-hidden=\'true\'></span> Proses ...');
      $('#download_data').addClass('disabled');
      $('#close_cashier').addClass('disabled');
      $('#backup_data').addClass('disabled');
      window.location.replace("{{ route('configuration-data-upload') }}");

    } else if (name == "close_cashier") {
      $('#download_data').addClass('disabled');
      $('#close_cashier').html('<span class=\'spinner-grow spinner-grow-sm mb-1\' role=\'status\' aria-hidden=\'true\'></span> Checking ...');
      $('#upload_data').addClass('disabled');
      $('#close_cashier').addClass('disabled');
      $('#backup_data').addClass('disabled');

      $.ajax({
				type: "GET",
				url : "{{route('check-close-cashier-configuration')}}",
				success: function(data){
          if (data == 0) {
            $('#modalCloseCashierLabel').text('Tutup Kasir Shift 1');
            $('#modalCloseCashier').modal('show');
            $('#download_data').removeClass('disabled');
            $('#close_cashier').html('<i class="fa fa-archive"></i> Tutup Kasir');
            $('#upload_data').removeClass('disabled');
            $('#close_cashier').removeClass('disabled');
            $('#backup_data').removeClass('disabled');
          } else if (data == 1) {
            $('#modalCloseCashierLabel').text('Tutup Kasir Shift 2');
            $('#modalCloseCashier').modal('show');
            $('#download_data').removeClass('disabled');
            $('#close_cashier').html('<i class="fa fa-archive"></i> Tutup Kasir');
            $('#upload_data').removeClass('disabled');
            $('#close_cashier').removeClass('disabled');
            $('#backup_data').removeClass('disabled');
          } else {
            $('#modalCloseCashier1').modal('show');
            $('#download_data').removeClass('disabled');
            $('#close_cashier').html('<i class="fa fa-archive"></i> Tutup Kasir');
            $('#upload_data').removeClass('disabled');
            $('#close_cashier').removeClass('disabled');
            $('#backup_data').removeClass('disabled');
          }
			  }
		  });

    } else if (name == "backup_data") {

      $('#upload_data').addClass('disabled');
      $('#backup_data').html('<span class=\'spinner-grow spinner-grow-sm mb-1\' role=\'status\' aria-hidden=\'true\'></span> Proses ...');
      $('#download_data').addClass('disabled');
      $('#close_cashier').addClass('disabled');
      $('#backup_data').addClass('disabled');
      window.location.replace("{{ route('backup-data-configuration') }}");

    } else if (name == "isTrueDownload") {

      $('#modal').modal('toggle');
      $('#download_data').addClass('disabled');
      $('#download_data').html('<span class=\'spinner-grow spinner-grow-sm mb-1\' role=\'status\' aria-hidden=\'true\'></span> Proses ...');
      $('#upload_data').addClass('disabled');
      $('#close_cashier').addClass('disabled');
      $('#backup_data').addClass('disabled');
      window.location.replace("{{ route('configuration-data-dwonload') }}");

    } else if (name == "isTrueCloseCashier") {

      $('#modalCloseCashier').modal('toggle');
      $('#download_data').addClass('disabled');
      $('#close_cashier').html('<span class=\'spinner-grow spinner-grow-sm mb-1\' role=\'status\' aria-hidden=\'true\'></span> Proses ...');
      $('#upload_data').addClass('disabled');
      $('#close_cashier').addClass('disabled');
      $('#backup_data').addClass('disabled');
      window.location.replace("{{ route('close-cashier-configuration') }}");
    }
  }
</script>
@endsection
@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
      <li class="breadcrumb-item active" aria-current="page">Konfigurasi Data</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
  <b>Konfigurasi Data</b>
</h3>
<br/>
@if(session('msg'))
<div class="alert alert-info" role="alert">
    {{session('msg')}}
</div>
<br/>
@endif 

<div class="modal fade" id="modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-danger">
        <h5 class="modal-title" id="staticBackdropLabel">Data Stok Ada yang Berbeda</h5>
      </div>
      <div class="modal-body">
        Apakah anda ingin mengganti data yang sudah ada?
      </div>
      <div class="modal-footer">
        <button onclick="buttonClick('isTrueDownload')" class="btn btn-success">Iya</button>
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tidak</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalCloseCashier" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalCloseCashierLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-danger">
        <h5 class="modal-title" id="modalCloseCashierLabel">Tutup Kasir</h5>
      </div>
      <div class="modal-body">
        Apakah anda yakin ingin menutup kasir?
      </div>
      <div class="modal-footer">
        <button onclick="buttonClick('isTrueCloseCashier')" class="btn btn-success">Iya</button>
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tidak</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalCloseCashier1" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalCloseCashierLabel1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-danger">
        <h5 class="modal-title" id="modalCloseCashierLabel1">Tutup Kasir Gagal</h5>
      </div>
      <div class="modal-body">
        Anda sudah Tutup Kasir 2 kali !
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Keluar</button>
      </div>
    </div>
  </div>
</div>

<div style="display: flex; justify-content: center; align-items: center; height: 200px; ">
  <button onclick="buttonClick('download_data');" id="download_data" class="btn btn-success btn-lg mx-2"><i class="fa fa-download"></i> Download Data</button>
  <button onclick="buttonClick('upload_data');" id="upload_data" class="btn btn-success btn-lg mx-2"><i class="fa fa-upload"></i> Upload Data</button>
  <button onclick="buttonClick('close_cashier');" id="close_cashier" class="btn btn-success btn-lg mx-2"><i class="fa fa-archive"></i> Tutup Kasir</button>
  <button onclick="buttonClick('backup_data');" id="backup_data" class="btn btn-success btn-lg mx-2"><i class="fa fa-cloud"></i> Candangkan Data</button>
</div>

@stop

@section('footer')
    
@stop

@section('css')
    
@stop

@section('js')
    
@stop