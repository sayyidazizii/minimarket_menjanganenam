@inject('SystemUser', 'App\Http\Controllers\SystemUserController')

@extends('adminlte::page')

@section('title', 'MOZAIC Minimarket')

@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
      <li class="breadcrumb-item active" aria-current="page">Daftar Pengeluaran</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    <b>Daftar Pengeluaran</b> <small>Kelola Pengeluaran </small>
</h3>
<br/>

@if(session('msg'))
<div class="alert alert-info" role="alert">
    {{session('msg')}}
</div>
@endif 
<div class="card border border-dark">
  <div class="card-header bg-dark clearfix">
    <h5 class="mb-0 float-left">
        Daftar
    </h5>
    <div class="form-actions float-right">
        <button onclick="location.href='{{ url('/expenditure/add') }}'" name="Find" class="btn btn-sm btn-info" title="Add Data"><i class="fa fa-plus"></i> Tambah Pengeluaran </button>
    </div>
  </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="example" style="width:100%" class="table table-striped table-bordered table-hover table-full-width">
                <thead>
                    <tr>
                        <th width="2%" style='text-align:center'>No</th>
                        <th width="10%" style='text-align:center'>Tanggal</th>
                        <th width="20%" style='text-align:center'>Keterangan</th>
                        <th width="20%" style='text-align:center'>Nominal</th>
                        <th width="10%" style='text-align:center'>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                   <?php $no = 1; ?>
                   @foreach ($data as $row)
                       <tr>
                        <td style="text-align: center">{{ $no++ }}.</td>
                        <td style="text-align: left">{{ date('d-m-Y',strtotime($row['expenditure_date'])) }}</td>
                        <td>{{ $row['expenditure_remark'] }}</td>
                        <td style="text-align: right">{{ number_format($row['expenditure_amount'],2,'.',',') }}</td>
                        <td style="text-align: center">
                            <a href="{{ url('expenditure/delete/'.$row['expenditure_id']) }}" class="btn btn-outline-danger btn-sm">Hapus</a>
                        </td>
                       </tr>
                   @endforeach
                </tbody>
            </table>
        </div>
    </div>
  </div>
</div>

@stop

@section('footer')
    
@stop

@section('css')
    
@stop

@section('js')
    
@stop