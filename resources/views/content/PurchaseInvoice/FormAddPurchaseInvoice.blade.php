@inject('PurchaseInvoice','App\Http\Controllers\PurchaseInvoiceController')
@extends('adminlte::page')

@section('title', 'MOZAIC Minimarket')
@section('js')
<script>
    function function_elements_add(name, value){
        console.log("name " + name);
        console.log("value " + value);
		$.ajax({
				type: "POST",
				url : "{{route('add-elements-purchase-invoice')}}",
				data : {
                    'name'      : name, 
                    'value'     : value,
                    '_token'    : '{{csrf_token()}}'
                },
				success: function(msg){
			}
		});
	}

    $(document).ready(function(){
        $("#quantity").change(function(){
            var quantity = $("#quantity").val();
            var cost = $("#item_unit_cost").val();
            var subtotal = quantity * cost;

            $("#subtotal_amount").val(subtotal);
            $("#subtotal_amount_view").val(toRp(subtotal));
            $("#subtotal_amount_after_discount_view").val(toRp(subtotal));
            $("#subtotal_amount_after_discount").val(subtotal);
        });

        $('#item_price_new_view').change(function(){
            var price_new = parseInt($('#item_price_new_view').val());
            var cost_new =  parseInt($('#item_cost_new').val());
            var margin_old = parseInt($('#margin_percentage_old').val());
            var margin = parseInt($('#margin_percentage').val());
            var margin_percentage = ((price_new - cost_new) / cost_new) * 100;
            if (margin_percentage > 100) {
                alert('Margin tidak boleh melebihi 100%');
                $('#margin_percentage').val(margin_old);
                var amount_margin = cost_new + ((cost_new * margin_old) / 100);
                $('#item_price_new_view').val(toRp(amount_margin));
                $('#item_price_new').val(amount_margin);
            } else {
                if (Number.isInteger(margin_percentage)) {
                    $('#margin_percentage').val(margin_percentage);
                } else {
                    $('#margin_percentage').val(margin_percentage.toFixed(2));
                }
                $('#item_price_new_view').val(toRp(price_new));
                $('#item_price_new').val(price_new);
            }
        });

        $('#margin_percentage').change(function(){
            var cost_new =  parseInt($('#item_cost_new').val());
            var margin_old = parseInt($('#margin_percentage_old').val());
            var margin = parseInt($('#margin_percentage').val());
            var price_new = ((margin * cost_new) / 100) + cost_new;
            if (margin > 100) {
                alert('Margin tidak boleh melebihi 100%');
                $('#margin_percentage').val(margin_old);
                var amount_margin = cost_new + ((cost_new * margin_old) / 100);
                $('#item_price_new_view').val(toRp(amount_margin));
                $('#item_price_new').val(amount_margin);
            } else {
                if (Number.isInteger(margin)) {
                    $('#margin_percentage').val(margin);
                } else {
                    $('#margin_percentage').val(margin.toFixed(2));
                }
                $('#item_price_new_view').val(toRp(price_new));
                $('#item_price_new').val(price_new);
            }
        });

        $("#item_unit_cost_view").change(function(){
            var item_category_id = $("#item_category_id").val();
            var item_unit_id = $("#item_unit_id").val();
            var item_id = $("#item_id").val();
            var cost_new     = $("#item_unit_cost_view").val();
            var cost         = $("#item_unit_cost").val();
            $.ajax({
                url: "{{ url('select-item-price') }}"+'/'+item_category_id+'/'+item_unit_id+'/'+item_id,
                type: "GET",
                dataType: "html",
                success:function(price)
                {
                    if (price != '') {
                        if (cost != cost_new) {
                            $.ajax({
                                url: "{{ url('get-margin-category') }}"+'/'+item_category_id,
                                type: "GET",
                                dataType: "html",
                                success:function(margin)
                                {
                                    if (margin != '') {
                                        $('#margin_percentage').val(margin);
                                        $('#margin_percentage_old').val(margin);
                                        var price_new = parseInt(cost_new) + ((parseInt(cost_new) * margin) / 100);
                                        $('#item_price_new_view').val(toRp(price_new));
                                        $('#item_price_new').val(price_new);
                                    }

                                }
                            });
                            $('#modal').modal('show');
                            $('#item_price_old_view').val(toRp(price));
                            $('#item_cost_old_view').val(toRp(cost));
                            $('#item_cost_new_view').val(toRp(cost_new));
                            $('#item_price_old').val(price);
                            $('#item_cost_old').val(cost);
                            $('#item_cost_new').val(cost_new);
                        }
                    }
                }
            });



            var quantity = $("#quantity").val();
            var subtotal = quantity * cost_new;

            $("#subtotal_amount").val(subtotal);
            $("#subtotal_amount_view").val(toRp(subtotal));
            $("#subtotal_amount_after_discount_view").val(toRp(subtotal));
            $("#subtotal_amount_after_discount").val(subtotal);
            $("#item_unit_cost_view").val(toRp(cost_new));
            // $("#item_unit_cost").val(cost_new);
        });

        $("#discount_percentage").change(function(){
            var subtotal = parseInt($("#subtotal_amount").val());
            var discount_percentage = parseInt($("#discount_percentage").val());
            var discount_amount = (subtotal * discount_percentage) / 100;
            
            $('#discount_amount_view').val(toRp(discount_amount));
            $('#discount_amount').val(discount_amount);

            var subtotal_amount_after_discount = parseInt($("#subtotal_amount_after_discount").val());
            var total_amount = subtotal - discount_amount;

            $("#subtotal_amount_after_discount_view").val(toRp(total_amount));
            $("#subtotal_amount_after_discount").val(total_amount);
        });

        $("#discount_amount_view").change(function(){
            var subtotal = parseInt($("#subtotal_amount").val());
            var discount_amount = parseInt($("#discount_amount_view").val());
            var total_amount = subtotal - discount_amount;
            
            $('#subtotal_amount_after_discount_view').val(toRp(total_amount));
            $('#subtotal_amount_after_discount').val(total_amount); 

            var discount_percentage = (discount_amount / subtotal) * 100;

            $('#discount_percentage').val(discount_percentage);
            $('#discount_amount').val(discount_amount);
            $('#discount_amount_view').val(toRp(discount_amount));
        });

        // $("#discount_percentage_total").change(function(){
        //     var subtotal_amount_total = $("#subtotal_amount_total").val();
        //     var tax_ppn_amount = $("#tax_ppn_amount").val();
        //     var discount_percentage_total = $("#discount_percentage_total").val();
        //     var shortover_amount = $('#shortover_amount').val();
        //     var discount_amount = (parseInt(subtotal_amount_total) * parseInt(discount_percentage_total)) / 100;
        //     var total_amount_after_discount = parseInt(subtotal_amount_total) - discount_amount;
        //     var total_amount_after_ppn_discount = parseInt(subtotal_amount_total) - (parseInt(tax_ppn_amount) || 0 + discount_amount);
        //     var final_amount = parseInt(shortover_amount) || 0 + total_amount_after_ppn_discount;

        //     $("#discount_amount_total").val(discount_amount);
        //     $("#discount_amount_total_view").val(toRp(discount_amount));
        //     $("#total_amount").val(final_amount);
        //     $("#total_amount_view").val(toRp(final_amount));
        // });

        $("#paid_amount_view").change(function(){
            if ($("#paid_amount_view").val() == '') {
                var paid_amount = 0;
            } else {
                var paid_amount = parseInt($("#paid_amount_view").val());
            }
            var total_amount = parseInt($("#total_amount").val());
            var owing_amount = paid_amount - total_amount;

            $('#paid_amount_view').val(toRp(paid_amount));
            $('#paid_amount').val(paid_amount);
            $("#owing_amount").val(Math.abs(owing_amount));
            $("#owing_amount_view").val(toRp(Math.abs(owing_amount)));
        });

        // if ($("#paid_amount_view").val() == '') {
        //     var paid_amount = 0;
        // } else {
        //     var paid_amount = parseInt($("#paid_amount_view").val());
        // }
        // var total_amount = parseInt($("#total_amount").val());
        // var owing_amount = paid_amount - total_amount;

        // $('#paid_amount_view').val(toRp(paid_amount));
        // $('#paid_amount').val(paid_amount);
        // $("#owing_amount").val(Math.abs(owing_amount));
        // $("#owing_amount_view").val(toRp(Math.abs(owing_amount)));

        // $("#tax_ppn_percentage").change(function(){
        //     var subtotal_amount_total = $("#subtotal_amount_total").val();
        //     var tax_ppn_percentage = $("#tax_ppn_percentage").val();
        //     var discount_amount_total = $("#discount_amount_total").val();
        //     var shortover_amount = $('#shortover_amount').val();
        //     var tax_ppn_amount = (parseInt(subtotal_amount_total) * parseInt(tax_ppn_percentage)) / 100;
        //     var total_amount_after_ppn = parseInt(subtotal_amount_total) - (parseInt(discount_amount_total) || 0 + tax_ppn_amount);
        //     var final_total = parseInt(shortover_amount) || 0 + total_amount_after_ppn;

        //     $("#tax_ppn_amount").val(tax_ppn_amount);
        //     $("#tax_ppn_amount_view").val(toRp(tax_ppn_amount));
        //     $("#total_amount").val(final_total);
        //     $("#total_amount_view").val(toRp(final_total));

        // });

        // $('#shortover_amount_view').change(function(){
        //     var shortover_amount = $('#shortover_amount_view').val();
        //     var discount_amount_total = $('#discount_amount_total').val();
        //     var subtotal_total = $('#subtotal_amount_total').val();
        //     var tax_ppn_amount = $("#tax_ppn_amount").val();
        //     var merge = parseInt(tax_ppn_amount) || 0 + parseInt(discount_amount_total) || 0;
        //     var after_subtotal_total = parseInt(subtotal_total) - merge;
        //     var final_total = parseInt(shortover_amount) + after_subtotal_total;

        //     $("#total_amount").val(final_total);
        //     $("#total_amount_view").val(toRp(final_total));
        //     $('#shortover_amount').val(shortover_amount);
        //     $('#shortover_amount_view').val(toRp(shortover_amount));
        // });
    });

    function process_change_cost(bool) {
        if (bool == true) {
            var item_category_id                = document.getElementById("item_category_id").value;
            var item_id		                    = document.getElementById("item_id").value;
            var item_unit_id		            = document.getElementById("item_unit_id").value;
            var item_cost_new		            = document.getElementById("item_cost_new").value;
            var item_price_new		            = document.getElementById("item_price_new").value;
            var margin_percentage		        = document.getElementById("margin_percentage").value;
    
            $.ajax({
                type: "POST",
                url : "{{route('process-change-cost-purchase-invoice')}}",
                data: {
                    'item_category_id'  : item_category_id,
                    'item_id'    	    : item_id, 
                    'item_unit_id'      : item_unit_id,
                    'item_cost_new'     : item_cost_new,
                    'item_price_new'    : item_price_new,
                    'margin_percentage' : margin_percentage,
                    '_token'            : '{{csrf_token()}}'
                },
                success: function(msg){
                    location.reload();
                }
            });
        } else {
            var item_cost_old = $('#item_unit_cost').val();

            $('#item_unit_cost_view').val(toRp(item_cost_old));
        }
    }

    function processAddArrayPurchaseInvoice(){
        var item_category_id                = document.getElementById("item_category_id").value;
        var item_id		                    = document.getElementById("item_id").value;
        var item_unit_id		            = document.getElementById("item_unit_id").value;
        var item_unit_cost		            = document.getElementById("item_unit_cost").value;
        var quantity                        = document.getElementById("quantity").value;
        var discount_percentage             = document.getElementById("discount_percentage").value;
        var discount_amount                 = document.getElementById("discount_amount").value;
        var subtotal_amount_after_discount  = document.getElementById("subtotal_amount_after_discount").value;
        var subtotal_amount                 = document.getElementById("subtotal_amount").value;
        var item_expired_date               = document.getElementById("item_expired_date").value;

        $.ajax({
            type: "POST",
            url : "{{route('add-array-purchase-invoice')}}",
            data: {
                'item_category_id'                  : item_category_id,
                'item_id'    	                    : item_id, 
                'item_unit_id'                      : item_unit_id,
                'item_unit_cost'                    : item_unit_cost,
                'quantity'                          : quantity,
                'discount_percentage'               : discount_percentage,
                'discount_amount'                   : discount_amount,
                'subtotal_amount_after_discount'    : subtotal_amount_after_discount,
                'subtotal_amount'                   : subtotal_amount,
                'item_expired_date'                 : item_expired_date,
                '_token'                            : '{{csrf_token()}}'
            },
            success: function(msg){
                location.reload();
            }
        });
    }

    function reset_add(){
		$.ajax({
				type: "GET",
				url : "{{route('add-reset-purchase-invoice')}}",
				success: function(msg){
                    location.reload();
			}

		});
	}

    $(document).ready(function(){
        $("#item_category_id").select2("val", "0");
        $("#item_unit_id").select2("val", "0");
        $("#item_id").select2("val", "0");

        $("#item_category_id").change(function(){
            $("#item_unit_id").select2("val", "0");
            $("#item_id").select2("val", "0");
			var id 	= $("#item_category_id").val();
            $.ajax({
                url: "{{ url('select-item') }}"+'/'+id,
                type: "GET",
                dataType: "html",
                success:function(data)
                {
                    $('#item_id').html(data);

                }
            });
		});

        $("#item_id").change(function(){
            $("#item_unit_id").select2("val", "0");
			var id 	= $("#item_id").val();
            $.ajax({
                url: "{{ url('select-item-unit') }}"+'/'+id,
                type: "GET",
                dataType: "html",
                success:function(data)
                {
                    $('#item_unit_id').html(data);

                }
            });
		});

        $("#item_unit_id").change(function(){
			var unit_id 	= $("#item_unit_id").val();
			var item_id 	= $("#item_id").val();
            $.ajax({
                url: "{{ url('select-item-cost') }}"+'/'+unit_id+'/'+item_id,
                type: "GET",
                dataType: "html",
                success:function(data)
                {
                    if (data != '') {
                        $('#item_unit_cost').val(data); 
                        $('#item_unit_cost_view').val(toRp(data)); 
                    }
                }
            });
		});
	});
    
    function final_total(name, value){
        var total_amount = parseInt($('#subtotal_amount_total').val());
        if (name == 'discount_percentage_total') {
            var discount_percentage_total = parseInt(value);
            var tax_ppn_amount = parseInt($('#tax_ppn_amount').val()) || 0;
            var shortover_amount = parseInt($('#shortover_amount').val()) || 0;
            var paid_amount = parseInt($("#paid_amount").val()) || 0;
            var discount_amount_total = (total_amount * discount_percentage_total) / 100;
            var final_total_amount = total_amount + shortover_amount - discount_amount_total - tax_ppn_amount;
            var owing_amount = paid_amount - final_total_amount

            $('#discount_amount_total').val(discount_amount_total);
            $('#discount_amount_total_view').val(toRp(discount_amount_total));
            $('#total_amount_view').val(toRp(final_total_amount));
            $('#total_amount').val(final_total_amount);
            $("#owing_amount").val(Math.abs(owing_amount));
            $("#owing_amount_view").val(toRp(Math.abs(owing_amount)));

        } else if (name == 'tax_ppn_percentage') {
            var tax_ppn_percentage = parseInt(value);
            var discount_amount_total = parseInt($('#discount_amount_total').val()) || 0;
            var shortover_amount = parseInt($('#shortover_amount').val()) || 0;
            var paid_amount = parseInt($("#paid_amount").val()) || 0;
            var tax_ppn_amount = (total_amount * tax_ppn_percentage) / 100;
            var final_total_amount = total_amount + shortover_amount - discount_amount_total - tax_ppn_amount;
            var owing_amount = paid_amount - final_total_amount

            $('#tax_ppn_amount').val(tax_ppn_amount);
            $('#tax_ppn_amount_view').val(toRp(tax_ppn_amount));
            $('#total_amount_view').val(toRp(final_total_amount));
            $('#total_amount').val(final_total_amount);
            $("#owing_amount").val(Math.abs(owing_amount));
            $("#owing_amount_view").val(toRp(Math.abs(owing_amount)));

        } else if (name == 'shortover_amount_view') {
            var shortover_amount_view = parseInt(value);
            var tax_ppn_amount = parseInt($('#tax_ppn_amount').val()) || 0;
            var discount_amount_total = parseInt($('#discount_amount_total').val()) || 0;
            var paid_amount = parseInt($("#paid_amount").val()) || 0;
            var final_total_amount = (total_amount - discount_amount_total - tax_ppn_amount) + shortover_amount_view ;
            var owing_amount = paid_amount - final_total_amount

            $('#shortover_amount_view').val(toRp(shortover_amount_view));
            $('#shortover_amount').val(shortover_amount_view);
            $('#total_amount_view').val(toRp(final_total_amount));
            $('#total_amount').val(final_total_amount);
            $("#owing_amount").val(Math.abs(owing_amount));
            $("#owing_amount_view").val(toRp(Math.abs(owing_amount)));

        }
        // console.log(name,value);
    }

    $(document).ready(function(){
        var total_amount = parseInt($('#subtotal_amount_total').val());
        var tax_ppn_percentage = parseInt($('#tax_ppn_percentage').val());
        var discount_amount_total = parseInt($('#discount_amount_total').val()) || 0;
        var shortover_amount = parseInt($('#shortover_amount').val()) || 0;
        var tax_ppn_amount = (total_amount * tax_ppn_percentage) / 100;
        var final_total_amount = total_amount + shortover_amount - discount_amount_total - tax_ppn_amount;

        $('#tax_ppn_amount').val(tax_ppn_amount);
        $('#tax_ppn_amount_view').val(toRp(tax_ppn_amount));
        $('#total_amount_view').val(toRp(final_total_amount));
        $('#total_amount').val(final_total_amount);

        var paid_amount = parseInt($("#paid_amount_view").val()) || 0;
        var total_amount = parseInt($("#total_amount").val());
        var owing_amount = paid_amount - total_amount;

        $('#paid_amount_view').val(toRp(paid_amount));
        $('#paid_amount').val(paid_amount);
        $("#owing_amount").val(Math.abs(owing_amount));
        $("#owing_amount_view").val(toRp(Math.abs(owing_amount)));
    });
</script>
@stop
@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ url('purchase-invoice') }}">Daftar Pembelian</a></li>
        <li class="breadcrumb-item active" aria-current="page">Tambah Pembelian</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    Form Tambah Pembelian
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
<div class="modal fade" id="modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title" id="staticBackdropLabel">Informasi Perubahan Harga</h5>
            </div>
            <div class="modal-body">
                <div class="row form-group">
                    <div class="col-md-12">
                        <div class="form-group">
                            <a class="text-dark">Margin Barang (%)</a>
                            <input style="text-align: left" class="form-control input-bb" name="margin_percentage" id="margin_percentage" type="text" autocomplete="off" value=""/>
                            <input style="text-align: left" class="form-control input-bb" name="margin_percentage_old" id="margin_percentage_old" type="text" autocomplete="off" value="" hidden/>
                        </div>
                    </div>
                </div>
                <h6 class="text-bold">Harga Beli</h6>
                <div class="row form-group">
                    <div class="col-md-6">
                        <div class="form-group">
                            <a class="text-dark">Lama</a>
                            <input style="text-align: right" class="form-control input-bb" name="item_cost_old_view" id="item_cost_old_view" type="text" autocomplete="off" value="" readonly/>
                            <input style="text-align: right" class="form-control input-bb" name="item_cost_old" id="item_cost_old" type="text" autocomplete="off" value="" hidden/>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <a class="text-dark">Baru</a>
                            <input style="text-align: right" class="form-control input-bb" name="item_cost_new_view" id="item_cost_new_view" type="text" autocomplete="off" value="" readonly/>
                            <input style="text-align: right" class="form-control input-bb" name="item_cost_new" id="item_cost_new" type="text" autocomplete="off" value="" hidden/>
                        </div>
                    </div>
                </div>
                <h6 class="text-bold">Harga Jual</h6>
                <div class="row form-group">
                    <div class="col-md-6">
                        <div class="form-group">
                            <a class="text-dark">Lama</a>
                            <input style="text-align: right" class="form-control input-bb" name="item_price_old_view" id="item_price_old_view" type="text" autocomplete="off" value="" readonly/>
                            <input style="text-align: right" class="form-control input-bb" name="item_price_old" id="item_price_old" type="text" autocomplete="off" value="" hidden/>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <a class="text-dark">Baru</a>
                            <input style="text-align: right" class="form-control input-bb" name="item_price_new_view" id="item_price_new_view" type="text" autocomplete="off" value=""/>
                            <input style="text-align: right" class="form-control input-bb" name="item_price_new" id="item_price_new" type="text" autocomplete="off" value="" hidden/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="process_change_cost(true);" class="btn btn-success">Iya</button>
                <button type="button" onclick="process_change_cost(false);" class="btn btn-danger" data-bs-dismiss="modal">Tidak</button>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card border border-dark">
        <div class="card-header border-dark bg-dark">
            <h5 class="mb-0 float-left">
                Form Tambah
            </h5>
            <div class="float-right">
                <button onclick="location.href='{{ url('purchase-invoice') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
            </div>
        </div>
    
    
        <form method="post" action="{{ route('process-add-purchase-invoice') }}" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="row form-group">
                    <div class="col-md-6">
                        <div class="form-group">
                            <a class="text-dark">Nama Pemasok<a class='red'> *</a></a>
                            {{-- <input class="form-control input-bb" name="purchase_invoice_supplier" id="purchase_invoice_supplier" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value)" value="{{ $datases['purchase_invoice_supplier'] }}"/> --}}
                            {!! Form::select('supplier_id', $suppliers, $datases['supplier_id'], ['class' => 'form-control selection-search-clear select-form', 'id' => 'supplier_id', 'name' => 'supplier_id', 'onchange' => 'function_elements_add(this.name, this.value)']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <a class="text-dark">Nama Gudang<a class='red'> *</a></a>
                            {!! Form::select('warehouse_id', $warehouses, $datases['warehouse_id'], ['class' => 'form-control selection-search-clear select-form', 'id' => 'warehouse_id', 'name' => 'warehouse_id', 'onchange' => 'function_elements_add(this.name, this.value)']) !!}
                            
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <a class="text-dark">Tanggal Invoice Pembelian<a class='red'> *</a></a>
                            <input style="width: 40%" class="form-control input-bb" name="purchase_invoice_date" id="purchase_invoice_date" type="date" data-date-format="dd-mm-yyyy" autocomplete="off" onchange="function_elements_add(this.name, this.value)" value="{{ $datases['purchase_invoice_date'] ? $datases['purchase_invoice_date'] : date('Y-m-d') }}"/>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <a class="text-dark">Metode Pembayaran<a class='red'> *</a></a>
                            {!! Form::select(0, $purchase_payment_method, $datases['purchase_payment_method'], ['class' => 'form-control selection-search-clear select-form', 'id' => 'purchase_payment_method', 'name' => 'purchase_payment_method', 'onchange' => 'function_elements_add(this.name, this.value)']) !!}
                        </div>
                    </div>
                    <div class="col-md-9 mt-3">
                        <div class="form-group">
                            <a class="text-dark">Keterangan</a>
                            <textarea class="form-control input-bb" name="purchase_invoice_remark" id="purchase_invoice_remark" type="text" autocomplete="off" onchange="function_elements_add(this.name, this.value)">{{ $datases['purchase_invoice_remark'] }}</textarea>
                        </div>
                    </div>
    
                    <h6 class="col-md-8 mt-4 mb-3"><b>Data Invoice Pembelian Barang</b></h6>
    
                    <div class="col-md-6">
                        <div class="form-group">
                            <a class="text-dark">Nama Kategori Barang<a class='red'> *</a></a>
                            {!! Form::select('item_category_id', $categorys, 0, ['class' => 'form-control selection-search-clear select-form', 'id' => 'item_category_id']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <a class="text-dark">Nama Barang<a class='red'> *</a></a>
                            {{-- {!! Form::select('item_id', $items, 0, ['class' => 'form-control selection-search-clear select-form', 'id' => 'item_id']) !!} --}}
                            <select name="item_id" id="item_id" class="form-control selection-search-clear select-form"></select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <a class="text-dark">Kode Satuan<a class='red'> *</a></a>
                            {{-- {!! Form::select('item_unit_id', $units, 0, ['class' => 'form-control selection-search-clear select-form', 'id' => 'item_unit_id']) !!} --}}
                            <select name="item_unit_id" id="item_unit_id" class="form-control selection-search-clear select-form"></select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <a class="text-dark">Biaya Barang Satuan<a class='red'> *</a></a>
                            <input style="text-align: right" class="form-control input-bb" name="item_unit_cost_view" id="item_unit_cost_view" type="text" autocomplete="off" value=""/>
                            <input class="form-control input-bb" name="item_unit_cost" id="item_unit_cost" type="text" autocomplete="off" value="" hidden/>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <a class="text-dark">Jumlah<a class='red'> *</a></a>
                            <input class="form-control input-bb text-right" name="quantity" id="quantity" type="text" autocomplete="off" value=""/>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <a class="text-dark">Subtotal<a class='red'> *</a></a>
                            <input style="text-align: right" class="form-control input-bb" name="subtotal_amount_view" id="subtotal_amount_view" type="text" autocomplete="off" value="" disabled/>
                            <input class="form-control input-bb" name="subtotal_amount" id="subtotal_amount" type="text" autocomplete="off" value="" hidden/>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <a class="text-dark">Diskon (%)</a>
                            <input style="text-align: right" class="form-control input-bb" name="discount_percentage" id="discount_percentage" type="text" autocomplete="off" value=""/>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <a class="text-dark">Jumlah Diskon</a>
                            <input style="text-align: right" class="form-control input-bb" name="discount_amount_view" id="discount_amount_view" type="text" autocomplete="off" value=""/>
                            <input style="text-align: right" class="form-control input-bb" name="discount_amount" id="discount_amount" type="text" autocomplete="off" value="" hidden/>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <a class="text-dark">Total<a class='red'> *</a></a>
                            <input style="text-align: right" class="form-control input-bb" name="subtotal_amount_after_discount_view" id="subtotal_amount_after_discount_view" type="text" autocomplete="off" value="" disabled/>
                            <input style="text-align: right" class="form-control input-bb" name="subtotal_amount_after_discount" id="subtotal_amount_after_discount" type="text" autocomplete="off" value="" hidden/>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <a class="text-dark">Tanggal Kadaluarsa<a class='red'> *</a></a>
                            <input class="form-control input-bb" name="item_expired_date" id="item_expired_date" type="date" data-date-format="dd-mm-yyyy" autocomplete="off" value="{{ date('Y-m-d') }}"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-muted">
                <div class="form-actions float-right">
                    <a type="submit" name="Save" class="btn btn-success" title="Save" onclick="processAddArrayPurchaseInvoice()"> Tambah</a>
                </div>
            </div>
        </div>
        </div>
    </div>
    
    <div class="card border border-dark">
        <div class="card-header border-dark bg-dark">
            <h5 class="mb-0 float-left">
                Daftar
            </h5>
        </div>
            <div class="card-body">
                <div class="form-body form">
                    <div class="table-responsive">
                        <table class="table table-bordered table-advance table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th style='text-align:center'>Barang</th>
                                    <th style='text-align:center'>Jumlah</th>
                                    <th style='text-align:center'>Harga Satuan</th>
                                    <th style='text-align:center'>Subtotal</th>
                                    <th style='text-align:center'>Kadaluarsa</th>
                                    <th style='text-align:center'>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $quantity = 0;
                            $subtotal_amount = 0;
                                if(!is_array($arraydatases)){
                                    echo "<tr><th colspan='6' style='text-align  : center !important;'>Data Kosong</th></tr>";
                                } else {
                                    foreach ($arraydatases AS $key => $val){
                                        echo"
                                        <tr>
                                                    <td style='text-align  : left !important;'>".$PurchaseInvoice->getItemName($val['item_id'])."</td>
                                                    <td style='text-align  : right !important;'>".$val['quantity']."</td>
                                                    <td style='text-align  : right !important;'>".number_format($val['item_unit_cost'],2,',','.')."</td>
                                                    <td style='text-align  : right !important;'>".number_format($val['subtotal_amount_after_discount'],2,',','.')."</td>
                                                    <td style='text-align  : right !important;'>".date('d-m-Y', strtotime($val['item_expired_date']))."</td>";
                                                    ?>
                                                    
                                                    <td style='text-align  : center'>
                                                        <a href="{{route('delete-array-purchase-invoice', ['record_id' => $key])}}" name='Reset' class='btn btn-danger btn-sm' onclick="return confirm('Apakah Anda Yakin Ingin Menghapus Data Ini ?')"></i> Hapus</a>
                                                    </td>
                                                    
                                                    <?php
                                                    echo"
                                                </tr>
                                            ";
    
                                        $quantity += $val['quantity'];
                                        $subtotal_amount += $val['subtotal_amount_after_discount'];
                                        
                                    }
                                }
                            ?>
                                <tr>
                                    <td colspan="2">Sub Total</td>
                                    <td style='text-align  : right !important;'>
                                        <input type="text" style="text-align  : right !important;" class="form-control input-bb" name="subtotal_item" id="subtotal_item" value="{{ $quantity }}" readonly/>
                                    </td>
                                    <td style='text-align  : right !important;'>
                                        <input type="text" style="text-align  : right !important;" class="form-control input-bb" name="subtotal_amount_total_view" id="subtotal_amount_total_view" value="{{ number_format($subtotal_amount,2,',','.') }}" readonly/>
                                        <input type="text" style="text-align  : right !important;" class="form-control input-bb" name="subtotal_amount_total" id="subtotal_amount_total" value="{{ $subtotal_amount }}" hidden/>
                                    </td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="2">Diskon (%)</td>
                                    <td style='text-align  : right !important;'>
                                        <input type="text" style="text-align  : right !important;" class="form-control input-bb" name="discount_percentage_total" id="discount_percentage_total" value="" autocomplete="off" onchange="final_total(this.name, this.value)"/>
                                    </td>
                                    <td style='text-align  : right !important;'>
                                        <input type="text" style="text-align  : right !important;" class="form-control input-bb" name="discount_amount_total_view" id="discount_amount_total_view" value="" readonly/>
                                        <input type="text" style="text-align  : right !important;" class="form-control input-bb" name="discount_amount_total" id="discount_amount_total" value="" hidden/>
                                    </td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="2">PPN (%)</td>
                                    <td style='text-align  : right !important;'>
                                        <input type="text" style="text-align  : right !important;" class="form-control input-bb" name="tax_ppn_percentage" id="tax_ppn_percentage" value="{{ $ppn_percentage['ppn_percentage'] }}" autocomplete="off" onchange="final_total(this.name, this.value)"/>
                                    </td>
                                    <td style='text-align  : right !important;'>
                                        <input type="text" style="text-align  : right !important;" class="form-control input-bb" name="tax_ppn_amount_view" id="tax_ppn_amount_view" value="" readonly/>
                                        <input type="text" style="text-align  : right !important;" class="form-control input-bb" name="tax_ppn_amount" id="tax_ppn_amount" value="" hidden/>
                                    </td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="3">Selisih</td>
                                    <td style='text-align  : right !important;'>
                                        <input type="text" style="text-align  : right !important;" class="form-control input-bb" name="shortover_amount_view" id="shortover_amount_view" value="" autocomplete="off" onchange="final_total(this.name, this.value)"/>
                                        <input type="text" style="text-align  : right !important;" class="form-control input-bb" name="shortover_amount" id="shortover_amount" value="" hidden/>
                                    </td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="3">Jumlah Total</td>
                                    <td style='text-align  : right !important;'>
                                        <input type="text" style="text-align  : right !important;" class="form-control input-bb" name="total_amount_view" id="total_amount_view" value="{{ number_format($subtotal_amount,2,',','.') }}" readonly/>
                                        <input type="text" style="text-align  : right !important;" class="form-control input-bb" name="total_amount" id="total_amount" value="{{ $subtotal_amount }}" hidden/>
                                    </td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="3">Di Bayar</td>
                                    <td style='text-align  : right !important;'>
                                        <input type="text" style="text-align  : right !important;" class="form-control input-bb" name="paid_amount_view" id="paid_amount_view" value="" autocomplete="off"/>
                                        <input type="text" style="text-align  : right !important;" class="form-control input-bb" name="paid_amount" id="paid_amount" value="" autocomplete="off" hidden/>
                                    </td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="3">Sisa</td>
                                    <td style='text-align  : right !important;'>
                                        <input type="text" style="text-align  : right !important;" class="form-control input-bb" name="owing_amount_view" id="owing_amount_view" value="" readonly/>
                                        <input type="text" style="text-align  : right !important;" class="form-control input-bb" name="owing_amount" id="owing_amount" value="" hidden/>
                                    </td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card-footer text-muted">
                <div class="form-actions float-right">
                    <button type="reset" name="Reset" class="btn btn-danger" onclick="reset_add();"><i class="fa fa-times"></i> Reset Data</button>
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