@extends('adminlte::page')

@section('title', 'MOZAIC Minimarket')

@section('js')
<script>
    function check_all(){
        $(':checkbox').each(function() {
            this.checked = true;                        
        });
    }
    function uncheck_all(){
        $(':checkbox').each(function() {
            this.checked = false;                        
        });
    }
</script>
@stop

@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ url('system-user-group') }}">Daftar System User Group</a></li>
        <li class="breadcrumb-item active" aria-current="page">Tambah System User Group</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    Form Tambah System User Group
</h3>
<br/>
@if(session('msg'))
<div class="alert alert-info" role="alert">
    {{session('msg')}}
</div>
@endif
    <div class="card border border-dark">
    <div class="card-header border-dark bg-dark">
        <h5 class="mb-0 float-left">
            Form Tambah
        </h5>
        <div class="float-right">
            <button onclick="location.href='{{ url('system-user-group') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
        </div>
    </div>

    <form method="post" action="{{route('process-add-system-user-group')}}" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Nama Group<a class='red'> *</a></a>
                        <input class="form-control input-bb" type="text" name="user_group_name" id="user_group_name" value=""/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">User Group Level<a class='red'> *</a></a>
                        <input class="form-control input-bb" type="text" name="user_group_level" id="user_group_level" value=""/>
                    </div>
                </div>
            </div>

            <br/>
            <div class="row">
                <div class="col-md-10">
                    <h5 class="form-section"><b>Privilage Menu<a class='red'> *</a></b></h5>
                </div>
                <div class="col-md-2" style="padding-left: 3%;">
                    <a onclick="check_all()" name="Find" class="btn btn-sm btn-info" title="Back"> Check All</a>
                    <a onclick="uncheck_all()" name="Find" class="btn btn-sm btn-info" title="Back"> UnCheck All</a>
                </div>
            </div>
            <hr style="margin:0;">
            <br/>
            <?php foreach($systemmenu as $key => $val) {
                    if($val['indent_level']==1){
            ?>
                <div class="indent_first">
                    <input type='checkbox' class='checkboxes' name='checkbox_{{$val['id_menu']}}' id='checkbox_{{$val['id_menu']}}' value='1'  OnClick='checkboxSalesOrderChange({{$val['id_menu']}})';/> {{$val['text']}}
                </div>
            <?php   }else if($val['indent_level']==2){ ?>
                <div class="indent_second">
                    <input type='checkbox' class='checkboxes' name='checkbox_{{$val['id_menu']}}' id='checkbox_{{$val['id_menu']}}' value='1'  OnClick='checkboxSalesOrderChange({{$val['id_menu']}})';/> {{$val['text']}}
                </div>
            <?php   }else if($val['indent_level']==3){ ?>
                <div class="indent_third">
                    <input type='checkbox' class='checkboxes' name='checkbox_{{$val['id_menu']}}' id='checkbox_{{$val['id_menu']}}' value='1'  OnClick='checkboxSalesOrderChange({{$val['id_menu']}})';/> {{$val['text']}}
                </div>
            <?php   } 
            } ?>
        </div>
        <div class="card-footer text-muted">
            <div class="form-actions float-right">
                <button type="reset" name="Reset" class="btn btn-danger" onClick="window.location.reload();"><i class="fa fa-times"></i> Batal</button>
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