@inject('AttendanceReport','App\Http\Controllers\AttendanceReportController')

@extends('adminlte::page')

@section('title', 'MOZAIC Minimarket')

@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
      <li class="breadcrumb-item active" aria-current="page">Laporan Absensi </li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    <b>Laporan Absensi </b>
</h3>
<br/>
<div id="accordion">
    <form  method="post" action="{{ route('filter-attendance-report') }}" enctype="multipart/form-data">
    @csrf
        <div class="card border border-dark">
        <div class="card-header bg-dark" id="headingOne" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
            <h5 class="mb-0">
                Filter
            </h5>
        </div>
    
        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
            <div class="card-body">
                <div class = "row">
                    <div class = "col-md-6">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Tanggal
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            <input style="width: 50%" class="form-control input-bb" name="date" id="date" type="date" data-date-format="dd-mm-yyyy" autocomplete="off" value="{{ $date }}"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-muted">
                <div class="form-actions float-right">
                    <a href="{{ route('reset-filter-attendance-report') }}" type="reset" name="Reset" class="btn btn-danger"><i class="fa fa-times"></i> Batal</a>
                    <button type="submit" name="Find" class="btn btn-primary" title="Search Data"><i class="fa fa-search"></i> Cari</button>
                </div>
            </div>
        </div>
        </div>
    </form>
</div>
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
  </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="" style="width:100%" class="table table-striped table-bordered table-hover table-full-width">
                <thead>
                    <tr>
                        <th width="5%" rowspan="2" style="vertical-align : middle;text-align:center;">No</th>
                        <th width="15%" rowspan="2" style="vertical-align : middle;text-align:center;">Nama</th>
                        <th width="15%" rowspan="2" style="vertical-align : middle;text-align:center;">Nama Lengkap</th>
                        <th width="20%" rowspan="2" style="vertical-align : middle;text-align:center;">Keterangan</th>
                        <th width="10%" rowspan="2" style="vertical-align : middle;text-align:center;">Absensi</th>
                    </tr>
                </thead>
                <tbody> 
                   <?php 
                        $no =1;
                        foreach ($user as $key => $val) {
                            echo "<tr>
                                    <td style='text-align: center'>".$no++.".</td>                                
                                    <td>".$val['name']."</td>                                
                                    <td>".$val['full_name']."</td>                                
                                    <td>".$AttendanceReport->getRemark($val['user_id'])."</td>                                
                                    <td style='text-align: center'>".$AttendanceReport->getAbsensi($val['user_id'])."</td>                                
                                </tr>
                            ";
                        }
                   ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer text-muted">
        <div class="form-actions float-right">
            <a class="btn btn-dark" href="{{ url('attendance-report/export') }}"><i class="fa fa-download"></i> Export Data</a>
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