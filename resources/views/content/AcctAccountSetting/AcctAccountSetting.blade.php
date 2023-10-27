@inject('AccountSetting','App\Http\Controllers\AcctAccountSettingController')

@extends('adminlte::page')

@section('title', 'MOZAIC Minimarket')

@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item active" aria-current="page">Daftar Setting Akun</li>
    </ol>
</nav>

@stop

@section('content')

<h3 class="page-title">
    Form Daftar Setting Akun
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
            Form Setting Akun
        </h5>
    </div>

    <form method="post" action="{{ route('process-add-acct-account-setting') }}" enctype="multipart/form-data">
    @csrf
        <div class="card-body">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" href="#pembelian" role="tab" data-toggle="tab">Pembelian</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#penjualan" role="tab" data-toggle="tab">Penjualan</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#pengeluaran" role="tab" data-toggle="tab">Pengeluaran</a>
                </li>
            </ul>
              
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane fade show active" id="pembelian">
                   <table class="table table-borderless mt-3">
                    <tr>
                        <th colspan="3" style="text-align: center !important ;width: 100% !important">Pembelian Tunai</th>
                    </tr>
                    <tr>
                        <th style="text-align: left !important; width: 40% !important">Kas</th>
                        <td style="text-align: left !important; width: 30% !important">
                            {!! Form::select(0, $accountlist, $AccountSetting->getAccountId('purchase_cash_account'),['class' => 'form-control selection-search-clear select-form','name'=>'purchase_cash_account_id','id'=>'purchase_cash_account_id']) !!}
                        </td>
                        <td style="text-align: left !important; width: 30% !important">
                            {!! Form::select(0, $status, $AccountSetting->getAccountSettingStatus('purchase_cash_account'),['class' => 'form-control selection-search-clear select-form','name'=>'purchase_cash_account_status','id'=>'purchase_cash_account_status']) !!}
                        </td>
                    </tr>
                    <tr>
                        <th style="text-align: left !important; width: 40% !important">Pembelian</th>
                        <td style="text-align: left !important; width: 30% !important">
                            {!! Form::select(0, $accountlist, $AccountSetting->getAccountId('purchase_account'),['class' => 'form-control selection-search-clear select-form','name'=>'purchase_account_id','id'=>'purchase_account_id']) !!}
                        </td>
                        <td style="text-align: left !important; width: 30% !important">
                            {!! Form::select(0, $status, $AccountSetting->getAccountSettingStatus('purchase_account'),['class' => 'form-control selection-search-clear select-form','name'=>'purchase_account_status','id'=>'purchase_account_status']) !!}
                        </td>
                    </tr>

                    <tr>
                        <th colspan="3" style="text-align: center !important ;width: 100% !important">Pembelian Hutang</th>
                    </tr>
                    <tr>
                        <th style="text-align: left !important; width: 40% !important">Pembelian</th>
                        <td style="text-align: left !important; width: 30% !important">
                            {!! Form::select(0, $accountlist, $AccountSetting->getAccountId('purchase_payable_account'),['class' => 'selection-search-clear select-form','name'=>'purchase_payable_account_id','id'=>'purchase_payable_account_id']) !!}
                        </td>
                        <td style="text-align: left !important; width: 30% !important">
                            {!! Form::select(0, $status, $AccountSetting->getAccountSettingStatus('purchase_payable_account'),['class' => 'selection-search-clear select-form','name'=>'purchase_payable_account_status','id'=>'purchase_payable_account_status']) !!}
                        </td>
                    </tr>
                    <tr>
                        <th style="text-align: left !important; width: 40% !important">Hutang</th>
                        <td style="text-align: left !important; width: 30% !important">
                            {!! Form::select(0, $accountlist, $AccountSetting->getAccountId('purchase_cash_payable_account'),['class' => 'selection-search-clear select-form','name'=>'purchase_cash_payable_account_id','id'=>'purchase_cash_payable_account_id']) !!}
                        </td>
                        <td style="text-align: left !important; width: 30% !important">
                            {!! Form::select(0, $status, $AccountSetting->getAccountSettingStatus('purchase_cash_payable_account'),['class' => 'selection-search-clear select-form','name'=>'purchase_cash_payable_account_status','id'=>'purchase_cash_payable_account_status']) !!}
                        </td>
                    </tr>

                    <tr>
                        <th colspan="3" style="text-align: center !important ;width: 100% !important">Pembayaran Hutang</th>
                    </tr>
                    <tr>
                        <th style="text-align: left !important; width: 40% !important">Hutang</th>
                        <td style="text-align: left !important; width: 30% !important">
                            {!! Form::select(0, $accountlist, $AccountSetting->getAccountId('purchase_payment_account'),['class' => 'selection-search-clear select-form','name'=>'purchase_payment_account_id','id'=>'purchase_payment_account_id']) !!}
                        </td>
                        <td style="text-align: left !important; width: 30% !important">
                            {!! Form::select(0, $status, $AccountSetting->getAccountSettingStatus('purchase_payment_account'),['class' => 'selection-search-clear select-form','name'=>'purchase_payment_account_status','id'=>'purchase_payment_account_status']) !!}
                        </td>
                    </tr>
                    <tr>
                        <th style="text-align: left !important; width: 40% !important">Kas</th>
                        <td style="text-align: left !important; width: 30% !important">
                            {!! Form::select(0, $accountlist, $AccountSetting->getAccountId('purchase_cash_payment_account'),['class' => 'selection-search-clear select-form','name'=>'purchase_cash_payment_account_id','id'=>'purchase_cash_payment_account_id']) !!}
                        </td>
                        <td style="text-align: left !important; width: 30% !important">
                            {!! Form::select(0, $status, $AccountSetting->getAccountSettingStatus('purchase_cash_payment_account'),['class' => 'selection-search-clear select-form','name'=>'purchase_cash_payment_account_status','id'=>'purchase_cash_payment_account_status']) !!}
                        </td>
                    </tr>


                    {{-- <tr>
                        <th colspan="3" style="text-align: center !important ;width: 100% !important">Pembelian Hutang</th>
                    </tr>
                    <tr>
                        <th style="text-align: left !important; width: 40% !important">Pembelian</th>
                        <td style="text-align: left !important; width: 30% !important">
                            {!! Form::select(0, $accountlist, $AccountSetting->getAccountId('purchase_payment_account'),['class' => 'selection-search-clear select-form','name'=>'purchase_payment_account_id','id'=>'purchase_payment_account_id']) !!}
                        </td>
                        <td style="text-align: left !important; width: 30% !important">
                            {!! Form::select(0, $status, $AccountSetting->getAccountSettingStatus('purchase_payment_account'),['class' => 'selection-search-clear select-form','name'=>'purchase_payment_account_status','id'=>'purchase_payment_account_status']) !!}
                        </td>
                    </tr>
                    <tr>
                        <th style="text-align: left !important; width: 40% !important">Hutang</th>
                        <td style="text-align: left !important; width: 30% !important">
                            {!! Form::select(0, $accountlist, $AccountSetting->getAccountId('purchase_payable_account'),['class' => 'selection-search-clear select-form','name'=>'account_payable_account_id','id'=>'account_payable_account_id']) !!}
                        </td>
                        <td style="text-align: left !important; width: 30% !important">
                            {!! Form::select(0, $status, $AccountSetting->getAccountSettingStatus('purchase_payable_account'),['class' => 'selection-search-clear select-form','name'=>'account_payable_account_status','id'=>'account_payable_account_status']) !!}
                        </td>
                    </tr>

                    <tr>
                        <th colspan="3" style="text-align: center !important ;width: 100% !important">Pembayaran Hutang</th>
                    </tr>
                    <tr>
                        <th style="text-align: left !important; width: 40% !important">Hutang</th>
                        <td style="text-align: left !important; width: 30% !important">
                            {!! Form::select(0, $accountlist, $AccountSetting->getAccountId('purchase_cash_payable_account'),['class' => 'selection-search-clear select-form','name'=>'account_payable_cash_account_id','id'=>'account_payable_cash_account_id']) !!}
                        </td>
                        <td style="text-align: left !important; width: 30% !important">
                            {!! Form::select(0, $status, $AccountSetting->getAccountSettingStatus('purchase_cash_payable_account'),['class' => 'selection-search-clear select-form','name'=>'account_payable_cash_account_status','id'=>'account_payable_cash_account_status']) !!}
                        </td>
                    </tr>
                    <tr>
                        <th style="text-align: left !important; width: 40% !important">Kas</th>
                        <td style="text-align: left !important; width: 30% !important">
                            {!! Form::select(0, $accountlist, $AccountSetting->getAccountId('purchase_cash_payment_account'),['class' => 'selection-search-clear select-form','name'=>'cash_purchase_payment_account_id','id'=>'cash_purchase_payment_account_id']) !!}
                        </td>
                        <td style="text-align: left !important; width: 30% !important">
                            {!! Form::select(0, $status, $AccountSetting->getAccountSettingStatus('purchase_cash_payment_account'),['class' => 'selection-search-clear select-form','name'=>'cash_purchase_payment_account_status','id'=>'cash_purchase_payment_account_status']) !!}
                        </td>
                    </tr> --}}

                   {{-- <tr>
                        <th colspan="3" style="text-align: center !important ;width: 100% !important">Pembayaran Hutang Bank</th>
                    </tr>
                    <tr>
                        <th style="text-align: left !important; width: 40% !important">Hutang Pembelian</th>
                        <td style="text-align: left !important; width: 30% !important">
                            {!! Form::select(0, $accountlist,0,['class' => 'selection-search-clear select-form','name'=>'account_payable_bank_account_id','id'=>'account_payable_bank_account_id']) !!}
                        </td>
                        <td style="text-align: left !important; width: 30% !important">
                            {!! Form::select(0, $status,0,['class' => 'selection-search-clear select-form','name'=>'account_payable_bank_account_status','id'=>'account_payable_bank_account_status']) !!}
                        </td>
                    </tr>
                    <tr>
                        <th style="text-align: left !important; width: 40% !important">Akun Bank</th>
                        <td style="text-align: left !important; width: 30% !important">
                            {!! Form::select(0, $accountlist,0,['class' => 'selection-search-clear select-form','name'=>'bank_purchase_payment_account_id','id'=>'bank_purchase_payment_account_id']) !!}
                        </td>
                        <td style="text-align: left !important; width: 30% !important">
                            {!! Form::select(0, $status,0,['class' => 'selection-search-clear select-form','name'=>'bank_purchase_payment_account_status','id'=>'bank_purchase_payment_account_status']) !!}
                        </td>
                    </tr>

                    <tr>
                        <th colspan="3" style="text-align: center !important ;width: 100% !important">Pembayaran Hutang Giro</th>
                    </tr>
                    <tr>
                        <th style="text-align: left !important; width: 40% !important">Hutang Pembelian</th>
                        <td style="text-align: left !important; width: 30% !important">
                            {!! Form::select(0, $accountlist,0,['class' => 'selection-search-clear select-form','name'=>'giro_purchase_payment_account_id','id'=>'giro_purchase_payment_account_id']) !!}
                        </td>
                        <td style="text-align: left !important; width: 30% !important">
                            {!! Form::select(0, $status,0,['class' => 'selection-search-clear select-form','name'=>'giro_purchase_payment_account_status','id'=>'giro_purchase_payment_account_status']) !!}
                        </td>
                    </tr>
                    <tr>
                        <th style="text-align: left !important; width: 40% !important">Hutang Wasel</th>
                        <td style="text-align: left !important; width: 30% !important">
                            {!! Form::select(0, $accountlist,0,['class' => 'selection-search-clear select-form','name'=>'account_payable_giro_account_id','id'=>'account_payable_giro_account_id']) !!}
                        </td>
                        <td style="text-align: left !important; width: 30% !important">
                            {!! Form::select(0, $status,0,['class' => 'selection-search-clear select-form','name'=>'account_payable_giro_account_status','id'=>'account_payable_giro_account_status']) !!}
                        </td>
                    </tr>

                    <tr>
                        <th colspan="3" style="text-align: center !important ;width: 100% !important">Pencairan Giro</th>
                    </tr>
                    <tr>
                        <th style="text-align: left !important; width: 40% !important">Hutang Pembelian</th>
                        <td style="text-align: left !important; width: 30% !important">
                            {!! Form::select(0, $accountlist,0,['class' => 'selection-search-clear select-form','name'=>'giro_purchase_liquefaction_id','id'=>'giro_purchase_liquefaction_id']) !!}
                        </td>
                        <td style="text-align: left !important; width: 30% !important">
                            {!! Form::select(0, $status,0,['class' => 'selection-search-clear select-form','name'=>'giro_purchase_liquefaction_account_status','id'=>'giro_purchase_liquefaction_account_status']) !!}
                        </td>
                    </tr>
                    <tr>
                        <th style="text-align: left !important; width: 40% !important">Akun Bank</th>
                        <td style="text-align: left !important; width: 30% !important">
                            {!! Form::select(0, $accountlist,0,['class' => 'selection-search-clear select-form','name'=>'bank_purchase_liquefaction_account_id','id'=>'bank_purchase_liquefaction_account_id']) !!}
                        </td>
                        <td style="text-align: left !important; width: 30% !important">
                            {!! Form::select(0, $status,0,['class' => 'selection-search-clear select-form','name'=>'bank_purchase_liquefaction_account_status','id'=>'bank_purchase_liquefaction_account_status']) !!}
                        </td>
                    </tr> --}}

                    <tr>
                        <th colspan="3" style="text-align: center !important ;width: 100% !important">Retur Pembelian</th>
                    </tr>
                    <tr>
                        <th style="text-align: left !important; width: 40% !important">Kas</th>
                        <td style="text-align: left !important; width: 30% !important">
                            {!! Form::select(0, $accountlist,$AccountSetting->getAccountId('purchase_return_cash_account'),['class' => 'form-control selection-search-clear select-form','name'=>'purchase_return_cash_account_id','id'=>'purchase_return_cash_account_id']) !!}
                        </td>
                        <td style="text-align: left !important; width: 30% !important">
                            {!! Form::select(0, $status,$AccountSetting->getAccountSettingStatus('purchase_return_cash_account'),['class' => 'form-control selection-search-clear select-form','name'=>'purchase_return_cash_account_status','id'=>'purchase_return_cash_account_status']) !!}
                        </td>
                    </tr>
                    <tr>
                        <th style="text-align: left !important; width: 40% !important">Retur Pembelian</th>
                        <td style="text-align: left !important; width: 30% !important">
                            {!! Form::select(0, $accountlist,$AccountSetting->getAccountId('purchase_return_account'),['class' => 'form-control selection-search-clear select-form','name'=>'purchase_return_account_id','id'=>'purchase_return_account_id']) !!}
                        </td>
                        <td style="text-align: left !important; width: 30% !important">
                            {!! Form::select(0, $status, $AccountSetting->getAccountSettingStatus('purchase_return_account'),['class' => 'form-control selection-search-clear select-form','name'=>'purchase_return_account_status','id'=>'purchase_return_account_status']) !!}
                        </td>
                    </tr>
                   </table>
                </div>
                <div role="tabpanel" class="tab-pane fade" id="penjualan">
                    <table class="table table-borderless mt-3">
                        <tr>
                            <th colspan="3" style="text-align: center !important ;width: 100% !important">Penjualan Tunai</th>
                        </tr>
                        <tr>
                            <th style="text-align: left !important; width: 40% !important">Kas</th>
                            <td style="text-align: left !important; width: 30% !important">
                                {!! Form::select(0, $accountlist, $AccountSetting->getAccountId('sales_cash_account'),['class' => 'form-control selection-search-clear select-form','name'=>'sales_cash_account_id','id'=>'sales_cash_account_id']) !!}
                            </td>
                            <td style="text-align: left !important; width: 30% !important">
                                {!! Form::select(0, $status, $AccountSetting->getAccountSettingStatus('sales_cash_account'),['class' => 'form-control selection-search-clear select-form','name'=>'sales_cash_account_status','id'=>'sales_cash_account_status']) !!}
                            </td>
                        </tr>
                        <tr>
                            <th style="text-align: left !important; width: 40% !important">Penjualan</th>
                            <td style="text-align: left !important; width: 30% !important">
                                {!! Form::select(0, $accountlist,$AccountSetting->getAccountId('sales_account'),['class' => 'form-control selection-search-clear select-form','name'=>'sales_account_id','id'=>'sales_account_id']) !!}
                            </td>
                            <td style="text-align: left !important; width: 30% !important">
                                {!! Form::select(0, $status,$AccountSetting->getAccountSettingStatus('sales_account'),['class' => 'form-control selection-search-clear select-form','name'=>'sales_account_status','id'=>'sales_account_status']) !!}
                            </td>
                        </tr>
                    
                        <tr>
                            <th colspan="3" style="text-align: center !important ;width: 100% !important">Penjualan Piutang</th>
                        </tr>
                        <tr>
                            <th style="text-align: left !important; width: 40% !important">Piutang</th>
                            <td style="text-align: left !important; width: 30% !important">
                                {!! Form::select(0, $accountlist, $AccountSetting->getAccountId('sales_cash_receivable_account'),['class' => 'selection-search-clear select-form','name'=>'sales_cash_receivable_account_id','id'=>'sales_cash_receivable_account_id']) !!}
                            </td>
                            <td style="text-align: left !important; width: 30% !important">
                                {!! Form::select(0, $status, $AccountSetting->getAccountSettingStatus('sales_cash_receivable_account'),['class' => 'selection-search-clear select-form','name'=>'sales_cash_receivable_account_status','id'=>'sales_cash_receivable_account_status']) !!}
                            </td>
                        </tr>
                        <tr>
                            <th style="text-align: left !important; width: 40% !important">Penjualan</th>
                            <td style="text-align: left !important; width: 30% !important">
                                {!! Form::select(0, $accountlist, $AccountSetting->getAccountId('sales_receivable_account'),['class' => 'selection-search-clear select-form','name'=>'sales_receivable_account_id','id'=>'sales_receivable_account_id']) !!}
                            </td>
                            <td style="text-align: left !important; width: 30% !important">
                                {!! Form::select(0, $status, $AccountSetting->getAccountSettingStatus('sales_receivable_account'),['class' => 'selection-search-clear select-form','name'=>'sales_receivable_account_status','id'=>'sales_receivable_account_status']) !!}
                            </td>
                        </tr>

                        <tr>
                            <th colspan="3" style="text-align: center !important ;width: 100% !important">Penjualan Non Tunai</th>
                        </tr>
                        <tr>
                            <th style="text-align: left !important; width: 40% !important">Kas</th>
                            <td style="text-align: left !important; width: 30% !important">
                                {!! Form::select(0, $accountlist, $AccountSetting->getAccountId('sales_cashless_cash_account'),['class' => 'form-control selection-search-clear select-form','name'=>'sales_cashless_cash_account_id','id'=>'sales_cashless_cash_account_id']) !!}
                            </td>
                            <td style="text-align: left !important; width: 30% !important">
                                {!! Form::select(0, $status, $AccountSetting->getAccountSettingStatus('sales_cashless_cash_account'),['class' => 'form-control selection-search-clear select-form','name'=>'sales_cashless_cash_account_status','id'=>'sales_cashless_cash_account_status']) !!}
                            </td>
                        </tr>
                        <tr>
                            <th style="text-align: left !important; width: 40% !important">Penjualan</th>
                            <td style="text-align: left !important; width: 30% !important">
                                {!! Form::select(0, $accountlist,$AccountSetting->getAccountId('sales_cashless_account'),['class' => 'form-control selection-search-clear select-form','name'=>'sales_cashless_account_id','id'=>'sales_cashless_account_id']) !!}
                            </td>
                            <td style="text-align: left !important; width: 30% !important">
                                {!! Form::select(0, $status,$AccountSetting->getAccountSettingStatus('sales_cashless_account'),['class' => 'form-control selection-search-clear select-form','name'=>'sales_cashless_account_status','id'=>'sales_cashless_account_status']) !!}
                            </td>
                        </tr>
                    </table>
                </div>
                <div role="tabpanel" class="tab-pane fade" id="pengeluaran">
                    <table class="table table-borderless">
                        <tr>
                            <th colspan="3" style="text-align: center !important ;width: 100% !important">Pengeluaran</th>
                        </tr>
                        <tr>
                            <th style="text-align: left !important; width: 40% !important">Kas</th>
                            <td style="text-align: left !important; width: 30% !important">
                                {!! Form::select(0, $accountlist, $AccountSetting->getAccountId('expenditure_cash_account'),['class' => 'form-control selection-search-clear select-form','name'=>'expenditure_cash_account_id','id'=>'expenditure_cash_account_id']) !!}
                            </td>
                            <td style="text-align: left !important; width: 30% !important">
                                {!! Form::select(0, $status, $AccountSetting->getAccountSettingStatus('expenditure_cash_account'),['class' => 'form-control selection-search-clear select-form','name'=>'expenditure_cash_account_status','id'=>'expenditure_cash_account_status']) !!}
                            </td>
                        </tr>
                        <tr>
                            <th style="text-align: left !important; width: 40% !important">Pengeluaran</th>
                            <td style="text-align: left !important; width: 30% !important">
                                {!! Form::select(0, $accountlist, $AccountSetting->getAccountId('expenditure_account'),['class' => 'form-control selection-search-clear select-form','name'=>'expenditure_account_id','id'=>'expenditure_account_id']) !!}
                            </td>
                            <td style="text-align: left !important; width: 30% !important">
                                {!! Form::select(0, $status, $AccountSetting->getAccountSettingStatus('expenditure_account'),['class' => 'form-control selection-search-clear select-form','name'=>'expenditure_account_status','id'=>'expenditure_account_status']) !!}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="card-footer text-muted">
            <div class="form-actions float-right">
                <button type="reset" name="Reset" class="btn btn-danger" onclick="location.reload();"><i class="fa fa-times"></i> Reset Data</button>
                <button type="submit" name="Save" class="btn btn-success" title="Save"><i class="fa fa-check"></i> Simpan</button>
            </div>
        </div>
    </form>
</div>
</div>

@stop

@section('footer')
    
@stop

@section('css')
    
@stop