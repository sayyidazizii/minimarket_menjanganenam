@inject('DashboardReview', 'App\Http\Controllers\DashboardReviewController')

@extends('adminlte::page')

@section('title', 'SMArT Baznas Sragen')

@section('js')
<script>
	$(document).ready(function(){
        var service_requisition_id        = {!! json_encode($service_requisition_id) !!};
        
        if(service_requisition_id == null){
            $("#service_requisition_id").select2("val", "0");
        }
    });

    function searchTracking(){
		var service_requisition_id = document.getElementById("service_requisition_id").value;
		$.ajax({
				type: "POST",
				url : "{{route('search-dashboard-review')}}",
				data : {
                    'service_requisition_id'      : service_requisition_id, 
                    '_token'                      : '{{csrf_token()}}'
                },
				success: function(msg){
                    location.reload();
			}
		});
	}
    public
</script>
@stop

@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ url('dashboard-review') }}">Dashboard Review</a></li>
        <li class="breadcrumb-item active" aria-current="page">Tracking</li>
    </ol>
</nav>

@stop

@section('content')

<h3 class="page-title">
    <b>Tracking</b> <small></small>
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
            Cari
        </h5>
        <div class="form-actions float-right">
            <button onclick="location.href='{{ url('dashboard-review') }}'" name="Find" class="btn btn-sm btn-info" title="Add Data"><i class="fa fa-angle-left"></i> Kembali</button>
        </div>
    </div>

    <div class="card-body">
        <div class="row form-group">
            <div class="col-md-12">
                <div class="form-group">
                    <a class="text-dark">Nomor Layanan<a class='red'> *</a></a>
                    {!! Form::select('service_requisition_id',  $servicerequisitionno, $service_requisition_id, ['class' => 'selection-search-clear select-form', 'id' => 'service_requisition_id']) !!}
                </div>
            </div>
        </div>
    </div>
        
    <div class="card-footer text-muted">
        <div class="form-actions float-right">
            <a type="submit" name="Save" class="btn btn-primary" onClick="searchTracking()">Cari</a>
        </div>
    </div>
</div>
<div class="card border border-dark">
    <div class="card-header bg-dark clearfix">
        <h5 class="mb-0 float-left">
            History
        </h5>
    </div>

    <div class="card-body">
        <div class="row form-group">
            <div class="col-md-6">
                <div class="form-group">
                    <a class="text-dark">Nama Layanan<a class='red'> *</a></a>
                    <input class="form-control input-bb" type="text" name="service_name" id="service_name" value="{{$DashboardReview->getServiceName($transservicerequisition['service_id'])}}" onChange="function_elements_add(this.name, this.value);" autocomplete="off" readonly/>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <a class="text-dark">Nama Pemohon<a class='red'> *</a></a>
                    <input class="form-control input-bb" type="text" name="service_requisition_name" id="service_requisition_name" value="{{$transservicerequisition['service_requisition_name']}}" onChange="function_elements_add(this.name, this.value);" autocomplete="off" readonly/>
                </div>
            </div>
        </div>
        <div class="row form-group">
            <div class="table-responsive">
                <table class="table table-bordered table-advance table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th style='text-align:center'>No</th>
                            <th style='text-align:center'>Tanggal</th>
                            <th style='text-align:center'>Posisi</th>
                            <th style='text-align:center'>Status</th>
                            <th style='text-align:center'>User</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            if(!isset($transservicerequisition)){
                                echo "<tr><th colspan='5' style='text-align  : center !important;'>Data Kosong</th></tr>";
                            } else {
                                $no = 1;
                                foreach($servicelog as $val){
                                    if($val['service_status'] == 1 || $val['service_status'] == 5 ||$val['service_status'] == 8){
                                    echo"
                                        <tr>
                                            <td style='text-align  : center !important;'>".$no."</td>
                                            <td style='text-align  : left !important;'>".$val['created_at']."</td>
                                            <td style='text-align  : left !important;'>".$DashboardReview->getSectionName1(1)."</td>
                                            <td style='text-align  : left !important;'>".$DashboardReview->getServiceStatus($val['service_status'])."</td>
                                            <td style='text-align  : left !important;'>".$DashboardReview->getUserName($val['created_id'])."</td>
                                        </tr>
                                    ";
                                    }
                                    else {
                                        $transservicedisposition = $DashboardReview->getServiceDisposition($transservicerequisition['service_requisition_id']);
                                        echo"
                                            <tr>
                                                <td style='text-align  : center !important;'>".$no."</td>
                                                <td style='text-align  : left !important;'>".$val['created_at']."</td>
                                                <td style='text-align  : left !important;'>".$DashboardReview->getSectionName1($val['section_id'])."</td>
                                                <td style='text-align  : left !important;'>".$DashboardReview->getServiceStatus($val['service_status'])."</td>
                                                <td style='text-align  : left !important;'>".$DashboardReview->getUserName($val['created_id'])."</td>
                                            </tr>
                                        ";
                                    }
                                    $no++;
                                }
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>

@stop

@section('footer')
    
@stop

@section('css')
    
@stop