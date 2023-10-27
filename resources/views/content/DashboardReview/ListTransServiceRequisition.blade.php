@inject('DashboardReview', 'App\Http\Controllers\DashboardReviewController')

@extends('adminlte::page')

@section('title', 'SMArT Baznas Sragen')

@section('js')
<script>
	$(document).ready(function(){
        var section_id        = {!! json_encode($section_id) !!};
        var service_id        = {!! json_encode($service_id) !!};
        
        if(section_id == null){
            $("#section_id").select2("val", "0");
        }
        if(service_id == null){
            $("#service_id").select2("val", "0");
        }
    });
</script>
@stop

@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item active" aria-current="page">Dashboard Review</li>
    </ol>
</nav>

@stop

@section('content')

<h3 class="page-title">
    <b>Dashboard Review</b> <small>Mengelola Dashboard Review</small>
</h3>
<br/>
<div id="accordion">
    <form  method="post" action="{{route('filter-dashboard-review')}}" enctype="multipart/form-data">
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
                            <section class="control-label">Tanggal Mulai
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            <input type ="date" class="form-control form-control-inline input-medium date-picker input-date" data-date-format="dd-mm-yyyy" type="text" name="start_date" id="start_date" onChange="function_elements_add(this.name, this.value);" value="{{$start_date}}" style="width: 15rem;"/>
                        </div>
                    </div>

                    <div class = "col-md-6">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Tanggal Akhir
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            <input type ="date" class="form-control form-control-inline input-medium date-picker input-date" data-date-format="dd-mm-yyyy" type="text" name="end_date" id="end_date" onChange="function_elements_add(this.name, this.value);" value="{{$end_date}}" style="width: 15rem;"/>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">	
                        <div class="form-group">	
                            <a class="text-dark">Layanan<a class='red'> *</a></a>
                            {!! Form::select('service_id',  $coreservice, $service_id, ['class' => 'selection-search-clear select-form', 'id' => 'service_id']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">	
                        <div class="form-group">	
                            <a class="text-dark">Bagian<a class='red'> *</a></a>
                            {!! Form::select('section_id',  $coresection, $section_id, ['class' => 'selection-search-clear select-form', 'id' => 'section_id']) !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-muted">
                <div class="form-actions float-right">
                    <button type="reset" name="Reset" class="btn btn-danger" onClick="window.location.reload();"><i class="fa fa-times"></i> Batal</button>
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
            Daftar Disposisi Bantuan
        </h5>
        <div class="form-actions float-right">
            <button onclick="location.href='{{ url('dashboard-review/tracking') }}'" name="Find" class="btn btn-sm btn-info" title="Add Data"><i class="fa fa-history"></i> Tracking</button>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="example" style="width:100%" class="table table-striped table-bordered table-hover table-full-width">
                <thead>
                    <tr>
                        <th width="5%" style='text-align:center'>No</th>
                        <th width="10%" style='text-align:center'>Nomor Pengajuan</th>
                        <th width="10%" style='text-align:center'>Nama Pemohon</th>
                        <th width="20%" style='text-align:center'>Nama Layanan</th>
                        <th width="15%" style='text-align:center'>Tanggal Pengajuan</th>
                        <th width="20%" style='text-align:center'>Posisi</th>
                        <th width="10%" style='text-align:center'>Tanggal Perubahan Status</th>
                        <th width="20%" style='text-align:center'>Jangka Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    @foreach($transservicerequisition as $service)
                    
                    <?php 
                    if($service['service_requisition_status']==4) {
                        $date       = $DashboardReview->getServiceDispositionReviewDate($service['service_requisition_id']);
                        $position   = "Selesai Review";
                    } else if($service['service_requisition_status']==3){
                        $date       = $DashboardReview->getServiceDispositionApprovedDate($service['service_requisition_id']);
                        $position   = "Persetujuan Disposisi di ".$DashboardReview->getSectionName($service['service_requisition_id']);
                    } else if($service['service_requisition_status']==1){
                        $date       = $DashboardReview->getServiceDispositionDate($service['service_requisition_id']);
                        $position   = "Disposisi di ".$DashboardReview->getSectionName($service['service_requisition_id']);
                    }else {
                        $date       = $service['created_at'];
                        $position   = "Pengajuan Bantuan";
                    }
                    $date1 = new DateTime($service['created_at']);
                    $date2 = new DateTime($date);
                    $interval = $date1->diff($date2);
                    ?>
                    <tr>
                        <td style='text-align:center'>{{$no}}</td>
                        <td>{{$service['service_requisition_no']}}</td>
                        <td>{{$service['service_requisition_name']}}</td>
                        <td>{{$DashboardReview->getServiceName($service['service_id'])}}</td>
                        <td>{{$service['created_at']}}</td>
                        <td>{{$position}}</td>
                        <td>{{$date}}</td>
                        <td>{{$interval->days." Hari ".$interval->h." Jam"}}</td>
                    </tr>
                    <?php $no++; ?>
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