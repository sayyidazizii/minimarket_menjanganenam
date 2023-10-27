@inject('APCC','App\Http\Controllers\AcctPayableCardController')

@extends('adminlte::page')

@section('title', 'MOZAIC Minimarket')

@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
      <li class="breadcrumb-item active" aria-current="page">Kartu Hutang Pemasok </li>
    </ol>
  </nav>

@stop

@section('content')
<h3 class="page-title">
    <b>Kartu Hutang Pemasok</b>
</h3>
<br/>
<div id="accordion">
    <form action="{{ route('filter-payable-card') }}" method="post">
        @csrf
        <div class="card border border-dark">
            <div class="card-header bg-dark" id="headingOne" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                <h5 class="mb-0">
                    Filter
                </h5>
            </div>
            <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                <div class="card-body">
                    <div class="row">
                        <div class = "col-md-6">
                            <div class="form-group form-md-line-input">
                                <section class="control-label">Tanggal Awal
                                    <span class="required text-danger">
                                        *
                                    </span>
                                </section>
                                <input style="width: 50%" class="form-control input-bb" name="start_date" id="start_date" type="date" data-date-format="dd-mm-yyyy" autocomplete="off" value="{{ $start_date }}"/>
                            </div>
                        </div>

                        <div class = "col-md-6">
                            <div class="form-group form-md-line-input">
                                <section class="control-label">Tanggal Akhir
                                    <span class="required text-danger">
                                        *
                                    </span>
                                </section>
                                <input style="width: 50%" class="form-control input-bb" name="end_date" id="end_date" type="date" data-date-format="dd-mm-yyyy" autocomplete="off" value="{{ $end_date }}"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-muted">
                    <div class="form-actions float-right">
                        <a href="{{ route('reset-filter-payable-card') }}" type="reset" name="Reset" class="btn btn-danger"><i class="fa fa-times"></i> Batal</a>
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
            <table id="example" style="width:100%" class="table table-striped table-bordered table-hover table-full-width">
                <thead>
                    <tr>
                        <th width="5%" style='text-align:center'>No</th>
                        <th width="20%" style='text-align:center'>Nama Pemasok</th>
                        <th width="15%" style='text-align:center'>Saldo Awal</th>
                        <th width="15%" style='text-align:center'>Hutang Baru</th>
                        <th width="15%" style='text-align:center'>Pembayaran</th>
                        <th width="15%" style='text-align:center'>Saldo Akhir</th>
                        <th width="15%" style='text-align:center'>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                  <?php $no = 1 ?>
                  @foreach ($data_supplier as $val)
                    <tr>
                        <td class="text-center">{{ $no++ }}.</td>
                        <td>{{ $val['supplier_name'] }}</td>
                        <td class="text-right">{{ number_format($APCC->getOpeningBalance($val['supplier_id']),2 ,',','.') }}</td>
                        <td class="text-right">{{ number_format($APCC->getPayableAmount($val['supplier_id']),2 ,',','.') }}</td>
                        <td class="text-right">{{ number_format($APCC->getPaymentAmount($val['supplier_id']),2 ,',','.') }}</td>
                        <td class="text-right">{{ number_format($APCC->getLastBalance($val['supplier_id']),2 ,',','.') }}</td>
                        <td class="text-center">
                            <a class="btn btn-secondary" href="{{ url('payable-card/print/'.$val['supplier_id']) }}"><i class="fa fa-file-pdf"></i> Pdf</a>
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