@inject('AcctAccount','App\Http\Controllers\AcctAccountController')
@extends('adminlte::page')

@section('title', 'MOZAIC Minimarket')

@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
      <li class="breadcrumb-item active" aria-current="page">Daftar Perkiraan</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    <b>Daftar Perkiraan</b> <small>Kelola Perkiraan </small>
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
        <button onclick="location.href='{{ url('/acct-account/add') }}'" name="Find" class="btn btn-sm btn-info" title="Add Data"><i class="fa fa-plus"></i> Tambah Perkiraan </button>
    </div>
  </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="example" style="width:100%" class="table table-striped table-bordered table-hover table-full-width">
                <thead>
                    <tr>
                        <th style='text-align:center; width: 5%'>No</th>
                        <th style='text-align:center; width: 15%'>No Perkiraan</th>
                        <th style='text-align:center; width: 15%'>Nama Perkiraan</th>
                        <th style='text-align:center; width: 18%'>Golongan Perkiraan</th>
                        <th style='text-align:center; width: 18%'>Kelompok Perkiraan</th>
                        <th style='text-align:center; width: 15%'>D/K</th>
                        <th style='text-align:center; width: 15%'>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    @foreach($data as $row)
                    <tr>
                        <td style='text-align:center'>{{ $no++ }}.</td>
                        <td>{{ $row['account_code'] }}</td>
                        <td>{{ $row['account_name'] }}</td>
                        <td>{{ $row['account_group'] }}</td>
                        <td>{{ $AcctAccount->getType($row['account_type_id']) }}</td>
                        <td>{{ $AcctAccount->getStatus($row['account_status']) }}</td>
                        <td style="text-align: center">
                            <a type="button" class="btn btn-outline-warning btn-sm" href="{{ url('/acct-account/edit/'.$row['account_id']) }}">Edit</a>
                            <a type="button" class="btn btn-outline-danger btn-sm" href="{{ url('/acct-account/delete/'.$row['account_id']) }}">Hapus</a>
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