@inject('PurchasePayment', 'App\Http\Controllers\PurchasePaymentController')
@extends('adminlte::page')

@section('title', 'MOZAIC Minimarket')
@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ url('purchase-payment') }}">Daftar Pelunasan Hutang</a></li>
        <li class="breadcrumb-item active" aria-current="page">Pembatalan Pelunasan Hutang</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    Form Pembatalan Pelunasan Hutang
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
            Form Pembatalan
        </h5>
        <div class="float-right">
            <button onclick="location.href='{{ url('purchase-payment') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
        </div>
    </div>

    <form method="post" action="{{route('process-delete-purchase-payment')}}" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Nama Pemasok</a>
                        <input class="form-control input-bb" type="text" name="supplier_name" id="supplier_name" value="{{$supplier['supplier_name']}}" readonly/>
                        <input class="form-control input-bb" type="hidden" name="payment_id" id="payment_id" value="{{$payment_id}}" readonly/>
                        <input class="form-control input-bb" type="hidden" name="payment_no" id="payment_no" value="{{$purchasepayment['payment_no']}}" readonly/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Tanggal Pelunasan</a>
                        <input class="form-control input-bb" type="text" name="payment_date" id="payment_date" value="{{$purchasepayment['payment_date']}}" readonly/>
                    </div>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Kas</a>
                        <input class="form-control input-bb" type="text" name="cash_account_name" id="cash_account_name" value="{{$PurchasePayment->getAccountName($purchasepayment['cash_account_id'])}}" readonly/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Jumlah Pelunasan Tunai</a>
                        <input class="form-control input-bb" type="text" name="payment_total_cash_amount" id="payment_total_cash_amount" value="{{number_format($purchasepayment['payment_total_cash_amount'], 2)}}" readonly/>
                    </div>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Jumlah Pelunasan Transfer</a>
                        <input class="form-control input-bb" type="text" name="payment_total_transfer_amount" id="payment_total_transfer_amount" value="{{number_format($purchasepayment['payment_total_transfer_amount'], 2)}}" readonly/>
                    </div>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-12 ">
                    <a class="text-dark">Keterangan</a>
                    <div class="">
                        <textarea rows="3" type="text" class="form-control input-bb" name="payment_remark" onChange="function_elements_add(this.name, this.value);" id="payment_remark" readonly>{{$purchasepayment['payment_remark']}}</textarea>
                    </div>
                </div>
            </div>
    </div>
    <div class="card-footer text-muted">
        <div class="form-actions float-right">
            <a href='#deleteremark' data-toggle='modal' name="Find" class="btn btn-danger add-btn" title="Add Data">Hapus</a>
        </div>
    </div>
    </div>

    <br/>
    <div class="card border border-dark">
        <div class="card-header border-dark bg-dark">
            <h5 class="mb-0 float-left">
                Daftar Invoice
            </h5>
        </div>
    
        <div class="card-body">
            <div class="form-body form">
                <div class="table-responsive">
                    <table class="table table-bordered table-advance table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th style='text-align:center'>No.</th>
                                <th style='text-align:center'>Tanggal</th>
                                <th style='text-align:center'>No. Invoice</th>
                                <th style='text-align:center'>Jumlah Hutang</th>
                                <th style='text-align:center'>Telah Dibayarkan</th>
                                <th style='text-align:center'>Sisa Hutang</th>
                                <th style='text-align:center'>Alokasi</th>
                                <th style='text-align:center'>Pembulatan</th>
                                <th style='text-align:center'>Saldo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                if(count($purchasepaymentitem)<1){
                                    echo "<tr><th colspan='9' style='text-align  : center !important;'>Data Kosong</th></tr>";
                                } else {
                                    $no =1;
                                    $total_allocation = 0;
                                    $total_shortover  = 0;
                                    foreach ($purchasepaymentitem AS $key => $val){
                                        echo"
                                            <tr>
                                                <td style='text-align  : center'>".$no."</td>
                                                <td style='text-align  : left'>".$val['purchase_invoice_date']."</td>
                                                <td style='text-align  : left'>".$val['purchase_invoice_no']."</td>
                                                <td style='text-align  : right'>".number_format($val['total_amount'], 2)."</td>
                                                <td style='text-align  : right'>".number_format($val['paid_amount'], 2)."</td>
                                                <td style='text-align  : right'>".number_format($val['owing_amount'], 2)."</td>
                                                <td style='text-align  : right'>".number_format($val['allocation_amount'], 2)."</td>
                                                <td style='text-align  : right'>".number_format($val['shortover_value'], 2)."</td>
                                                <td style='text-align  : right'>".number_format($val['last_balance'], 2)."</td>";
                                                echo"
                                            </tr>
                                        ";
                                        $no++;
                                        $total_allocation+=$val['allocation_amount'];
                                        $total_shortover+=$val['shortover_value'];
                                    }
                                        echo"
                                        <th style='text-align  : center' colspan='6'>Total</th>
                                        <th style='text-align  : right'>".number_format($total_allocation,2)."</th>
                                        <th style='text-align  : right'>".number_format($total_shortover,2)."</th>
                                        <th style='text-align  : center'></th>
                                        ";
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <br/>
    <div class="card border border-dark">
        <div class="card-header border-dark bg-dark">
            <h5 class="mb-0 float-left">
                Daftar Transfer Bank
            </h5>
        </div>
    
        <div class="card-body">
            <div class="form-body form">
                <div class="table-responsive">
                    <table class="table table-bordered table-advance table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th style='text-align:center'>No.</th>
                                <th style='text-align:center'>Nama Bank</th>
                                <th style='text-align:center'>Nama Akun</th>
                                <th style='text-align:center'>No. Rekening</th>
                                <th style='text-align:center'>Jumlah Transfer</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                if(count($purchasepaymenttransfer)<1){
                                    echo "<tr><th colspan='5' style='text-align  : center !important;'>Data Kosong</th></tr>";
                                } else {
                                    $no =1;
                                    $total_transfer = 0;
                                    foreach ($purchasepaymenttransfer AS $key => $val){
                                        echo"
                                            <tr>
                                                <td style='text-align  : center'>".$no."</td>
                                                <td style='text-align  : left'>".$val['payment_transfer_bank_name']."</td>
                                                <td style='text-align  : left'>".$val['payment_transfer_account_name']."</td>
                                                <td style='text-align  : left'>".$val['payment_transfer_account_no']."</td>
                                                <td style='text-align  : right'>".number_format($val['payment_transfer_amount'], 2)."</td>";
                                                echo"
                                            </tr>
                                        ";
                                        $no++;
                                        $total_transfer+=$val['payment_transfer_amount'];
                                    }
                                        echo"
                                        <th style='text-align  : center' colspan=4'>Total</th>
                                        <th style='text-align  : right'>".number_format($total_transfer, 2)."</th>
                                        ";
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<br/>
<br/>
<br/>


<div class="modal fade bs-modal-lg" id="deleteremark" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"  style='text-align:left !important'>
                <h4>Keterangan Pembatalan Pelunasan Hutang</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">	
                        <div class="form-group">	
                            <a class="text-dark">Keterangan</a>
                            <textarea class="form-control input-bb" type="text" name="voided_remark" id="voided_remark"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal" id='cancel_btn_remark'>Batal</button>
                <button type="submit" class="btn btn-success">Simpan</button>
            </div>
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