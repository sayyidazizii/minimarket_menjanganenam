@inject('SalesInvoice','App\Http\Controllers\SalesInvoiceController' )
@extends('adminlte::page')

@section('title', 'MOZAIC Minimarket')
@section('js')
<script>

var ppn = {{ $ppn['ppn_percentage'] }};
// console.log(ppn);
    $('#item_code').keyup(function(e){
        if (e.keyCode == 13) {
            $.ajax({
                url: "{{ url('select-sales') }}"+'/'+this.value,
                type: "GET",
                dataType: "json",
                success:function(data)
                {
                    if (data != "") {
                        var html = '';
                        var i = 0;
                        var no = 1;
                        var total_amount = 0; 
                        var total_item = 0;
                        var paid_amount = $('#paid_amount').val() || 0;
                        var voucher_amount = $('#voucher_amount').val() || 0;
                        var discount_percentage_total = $('#discount_percentage_total').val() || 0;
                        var ppn_amount = ppn * 1/100;
                        while (i < data.length) {
                            if (data[i].quantity != 0) {
                                $price = parseInt(data[i].item_unit_price * ppn_amount);
                                total_amount += parseInt(data[i].subtotal_amount_after_discount);
                                total_item += parseInt(data[i].quantity);
                                html += '<tr>'+
                                        '<td class="text-center">'+no+'.</td>'+
                                        '<td>'+data[i].item_name+'</td>'+
                                        '<td>'+data[i].item_unit_name+'</td>'+
                                        '<td>'+toRp(data[i].item_unit_price)+'</td>'+
                                        '<td><input onkeyup="function_change_quantity('+data[i].item_packge_id+', this.value, '+i+')" type="number" name="'+i+'_quantity" id="'+i+'_quantity" style="width: 100%; text-align: center; height: 30px; font-weight: bold; font-size: 15px" class="form-control input-bb" value="'+data[i].quantity+'" autocomplete="off">'+
                                        '<td><div id="'+i+'_price_amount" name="'+i+'_price_amount" class="text-right">'+toRp(data[i].subtotal_amount_after_discount)+'</div></td>'+
                                        '</tr>';
    
                                no++;
                            }
                            i++;
                        }
                        
                        total_amount_af_voucher = total_amount - voucher_amount;
                        discount_amount = (total_amount_af_voucher * discount_percentage_total) / 100;
                        total_amount_af_discount = total_amount_af_voucher - discount_amount;
                        change_amount = paid_amount - total_amount_af_discount;
    
                        if (paid_amount != 0) {
                            $('#change_amount_view').val(toRp(change_amount));
                            $('#change_amount').val(change_amount);
                        }
                        $('#subtotal_amount_view').text('Rp '+toRp(total_amount_af_discount));
                        $('#subtotal_amount_change').val(total_amount_af_discount);
                        $('#subtotal_amount').val(total_amount);
                        $('#discount_amount_total').val(discount_amount);
                        $('#total_item').val(total_item);
                        $('#show_data').html(html);
    
                        $('#item_code').val('');
                    } else if (data == 0) {
                        alert('Barcode tidak ada yang sesuai!');
                    }
                }
            });
        }
    });
    // $('body').on('input','#item_code',function(){
    //     var item_code = $('#item_code').val();
    //     if (item_code.length == 8) {
    //         $.ajax({
    //             url: "{{ url('select-sales') }}"+'/'+item_code,
    //             type: "GET",
    //             dataType: "json",
    //             success:function(data)
    //             {
    //                 if (data != "") {
    //                     var html = '';
    //                     var i = 0;
    //                     var no = 1;
    //                     var total_amount = 0; 
    //                     var total_item = 0;
    //                     var paid_amount = $('#paid_amount').val() || 0;
    //                     var voucher_amount = $('#voucher_amount').val() || 0;
    //                     var discount_percentage_total = $('#discount_percentage_total').val() || 0;
    //                     while (i < data.length) {
    //                         if (data[i].quantity != 0) {
    //                             total_amount += parseInt(data[i].subtotal_amount_after_discount);
    //                             total_item += parseInt(data[i].quantity);
    //                             html += '<tr>'+
    //                                     '<td class="text-center">'+no+'.</td>'+
    //                                     '<td>'+data[i].item_name+'</td>'+
    //                                     '<td>'+data[i].item_unit_name+'</td>'+
    //                                     '<td>'+toRp(data[i].item_unit_price)+'</td>'+
    //                                     '<td><input oninput="function_change_quantity('+data[i].item_packge_id+', this.value)" type="number" name="'+i+'_quantity" id="'+i+'_quantity" style="width: 100%; text-align: center; height: 30px; font-weight: bold; font-size: 15px" class="form-control input-bb" value="'+data[i].quantity+'" autocomplete="off">'+
    //                                     '<td><div id="'+i+'_price_amount" name="'+i+'_price_amount" class="text-right">'+toRp(data[i].subtotal_amount_after_discount)+'</div></td>'+
    //                                     '</tr>';
    
    //                             no++;
    //                         }
    //                         i++;
    //                     }
                        
    //                     total_amount_af_voucher = total_amount - voucher_amount;
    //                     discount_amount = (total_amount_af_voucher * discount_percentage_total) / 100;
    //                     total_amount_af_discount = total_amount_af_voucher - discount_amount;
    //                     change_amount = paid_amount - total_amount_af_discount;
    
    //                     if (paid_amount != 0) {
    //                         $('#change_amount_view').val(toRp(change_amount));
    //                         $('#change_amount').val(change_amount);
    //                     }
    //                     $('#subtotal_amount_view').text('Rp '+toRp(total_amount_af_discount));
    //                     $('#subtotal_amount_change').val(total_amount_af_discount);
    //                     $('#subtotal_amount').val(total_amount);
    //                     $('#discount_amount_total').val(discount_amount);
    //                     $('#total_item').val(total_item);
    //                     $('#show_data').html(html);
    
    //                     $('#item_code').val('');
    //                 } else if (data == 0) {
    //                     alert('Barcode tidak ada yang sesuai!');
    //                 }
    //             }
    //         });
    //     } else if (item_code.length == 12) {
    //         $.ajax({
    //             url: "{{ url('select-sales') }}"+'/'+item_code,
    //             type: "GET",
    //             dataType: "json",
    //             success:function(data)
    //             {
    //                 if (data != "") {
    //                     var html = '';
    //                     var i = 0;
    //                     var no = 1;
    //                     var total_amount = 0; 
    //                     var total_item = 0;
    //                     var paid_amount = $('#paid_amount').val() || 0;
    //                     var voucher_amount = $('#voucher_amount').val() || 0;
    //                     var discount_percentage_total = $('#discount_percentage_total').val() || 0;
    //                     while (i < data.length) {
    //                         if (data[i].quantity != 0) {
    //                             total_amount += parseInt(data[i].subtotal_amount_after_discount);
    //                             total_item += parseInt(data[i].quantity);
    //                             html += '<tr>'+
    //                                     '<td class="text-center">'+no+'.</td>'+
    //                                     '<td>'+data[i].item_name+'</td>'+
    //                                     '<td>'+data[i].item_unit_name+'</td>'+
    //                                     '<td>'+toRp(data[i].item_unit_price)+'</td>'+
    //                                     '<td><input oninput="function_change_quantity('+data[i].item_packge_id+', this.value)" type="number" name="'+i+'_quantity" id="'+i+'_quantity" style="width: 100%; text-align: center; height: 30px; font-weight: bold; font-size: 15px" class="form-control input-bb" value="'+data[i].quantity+'" autocomplete="off">'+
    //                                     '<td><div id="'+i+'_price_amount" name="'+i+'_price_amount" class="text-right">'+toRp(data[i].subtotal_amount_after_discount)+'</div></td>'+
    //                                     '</tr>';
    
    //                             no++;
    //                         }
    //                         i++;
    //                     }
                        
    //                     total_amount_af_voucher = total_amount - voucher_amount;
    //                     discount_amount = (total_amount_af_voucher * discount_percentage_total) / 100;
    //                     total_amount_af_discount = total_amount_af_voucher - discount_amount;
    //                     change_amount = paid_amount - total_amount_af_discount;
    
    //                     if (paid_amount != 0) {
    //                         $('#change_amount_view').val(toRp(change_amount));
    //                         $('#change_amount').val(change_amount);
    //                     }
    //                     $('#subtotal_amount_view').text('Rp '+toRp(total_amount_af_discount));
    //                     $('#subtotal_amount_change').val(total_amount_af_discount);
    //                     $('#subtotal_amount').val(total_amount);
    //                     $('#discount_amount_total').val(discount_amount);
    //                     $('#total_item').val(total_item);
    //                     $('#show_data').html(html);
    
    //                     $('#item_code').val('');
    //                 } else if (data == 0) {
    //                     alert('Barcode tidak ada yang sesuai!');
    //                 }
    //             }
    //         });
    //     } else if (item_code.length == 13) {
    //         $.ajax({
    //             url: "{{ url('select-sales') }}"+'/'+item_code,
    //             type: "GET",
    //             dataType: "json",
    //             success:function(data)
    //             {
    //                 if (data != "") {
    //                     var html = '';
    //                     var i = 0;
    //                     var no = 1;
    //                     var total_amount = 0; 
    //                     var total_item = 0;
    //                     var paid_amount = $('#paid_amount').val() || 0;
    //                     var voucher_amount = $('#voucher_amount').val() || 0;
    //                     var discount_percentage_total = $('#discount_percentage_total').val() || 0;
    //                     while (i < data.length) {
    //                         if (data[i].quantity != 0) {
    //                             total_amount += parseInt(data[i].subtotal_amount_after_discount);
    //                             total_item += parseInt(data[i].quantity);
    //                             html += '<tr>'+
    //                                     '<td class="text-center">'+no+'.</td>'+
    //                                     '<td>'+data[i].item_name+'</td>'+
    //                                     '<td>'+data[i].item_unit_name+'</td>'+
    //                                     '<td>'+toRp(data[i].item_unit_price)+'</td>'+
    //                                     '<td><input oninput="function_change_quantity('+data[i].item_packge_id+', this.value)" type="number" name="'+i+'_quantity" id="'+i+'_quantity" style="width: 100%; text-align: center; height: 30px; font-weight: bold; font-size: 15px" class="form-control input-bb" value="'+data[i].quantity+'" autocomplete="off">'+
    //                                     '<td><div id="'+i+'_price_amount" name="'+i+'_price_amount" class="text-right">'+toRp(data[i].subtotal_amount_after_discount)+'</div></td>'+
    //                                     '</tr>';
    
    //                             no++;
    //                         }
    //                         i++;
    //                     }
                        
    //                     total_amount_af_voucher = total_amount - voucher_amount;
    //                     discount_amount = (total_amount_af_voucher * discount_percentage_total) / 100;
    //                     total_amount_af_discount = total_amount_af_voucher - discount_amount;
    //                     change_amount = paid_amount - total_amount_af_discount;
    
    //                     if (paid_amount != 0) {
    //                         $('#change_amount_view').val(toRp(change_amount));
    //                         $('#change_amount').val(change_amount);
    //                     }
    //                     $('#subtotal_amount_view').text('Rp '+toRp(total_amount_af_discount));
    //                     $('#subtotal_amount_change').val(total_amount_af_discount);
    //                     $('#subtotal_amount').val(total_amount);
    //                     $('#discount_amount_total').val(discount_amount);
    //                     $('#total_item').val(total_item);
    //                     $('#show_data').html(html);
    
    //                     $('#item_code').val('');
    //                 } else if (data == 0) {
    //                     alert('Barcode tidak ada yang sesuai!');
    //                 }
    //             }
    //         });
    //     }
    // });

    function function_add_item(item_id,unit_id,item_name){
        $.ajax({
            url: "{{ url('select-sales') }}"+'/'+item_id+'/'+unit_id,
            type: "GET",
            success:function(data)
            {
                if (data != "") {
                    var html = '';
                    var i = 0;
                    var no = 1;
                    var total_amount = 0; 
                    var total_item = 0;
                    var ppn = 0;
                    var paid_amount = $('#paid_amount').val() || 0;
                    var voucher_amount = $('#voucher_amount').val() || 0;
                    var discount_percentage_total = $('#discount_percentage_total').val() || 0;
                    while (i < data.length) {
                        if (data[i].quantity != 0) {
                            total_amount += parseInt(data[i].subtotal_amount_after_discount);
                            total_item += parseInt(data[i].quantity);
                            html += '<tr>'+
                                    '<td class="text-center">'+no+'.</td>'+
                                    '<td>'+data[i].item_name+'</td>'+
                                    '<td>'+data[i].item_unit_name+'</td>'+
                                    '<td>'+toRp(data[i].item_unit_price)+'</td>'+
                                    '<td><input onkeyup="function_change_quantity('+data[i].item_packge_id+', this.value, '+i+')" type="number" name="'+i+'_quantity" id="'+i+'_quantity" style="width: 100%; text-align: center; height: 30px; font-weight: bold; font-size: 15px" class="form-control input-bb" value="'+data[i].quantity+'" autocomplete="off">'+
                                    '<td><div id="'+i+'_price_amount" name="'+i+'_price_amount" class="text-right">'+toRp(data[i].subtotal_amount_after_discount)+'</div></td>'+
                                    '</tr>';
                            no++;
                        }
                        i++;
                    }
                
                    total_amount_af_voucher = total_amount - voucher_amount;
                    discount_amount = (total_amount_af_voucher * discount_percentage_total) / 100;
                    total_amount_af_discount = total_amount_af_voucher - discount_amount;
                    change_amount = paid_amount - total_amount_af_discount;

                    if (paid_amount != 0) {
                        $('#change_amount_view').val(toRp(change_amount));
                        $('#change_amount').val(change_amount);
                    }
                    $('#subtotal_amount_view').text('Rp '+toRp(total_amount_af_discount));
                    $('#subtotal_amount_change').val(total_amount_af_discount);
                    $('#subtotal_amount').val(total_amount);
                    $('#discount_amount_total').val(discount_amount);
                    $('#total_item').val(total_item);
                    $('#show_data').html(html);

                    $('#myDataTable').DataTable().search('').draw();
                }
            }
        });
    }

    $(document).ready(function(){
        var msg = {!! json_encode(session('msg')) !!};
        var data = {!! json_encode(session('data_itemses')) !!};
        if (data != null) {

            var html = '';
            var i = 0;
            var no = 1;
            var total_amount = 0;
            var total_item = 0;
            var paid_amount = $('#paid_amount').val() || 0;
            var voucher_amount = $('#voucher_amount').val() || 0;
            var discount_percentage_total = $('#discount_percentage_total').val() || 0;
            var ppn_amount = ppn * 1/100;
            while (i < data.length) {
                if (data[i].quantity != 0) {
                    total_amount += parseInt(data[i].subtotal_amount_after_discount);
                    $price = parseInt(data[i].item_unit_price * ppn_amount);
                    total_item += parseInt(data[i].quantity);
                    html += '<tr>'+
                            '<td class="text-center">'+no+'.</td>'+
                            '<td>'+data[i].item_name+'</td>'+
                            '<td>'+data[i].item_unit_name+'</td>'+
                            '<td>'+toRp(data[i].item_unit_price)+'</td>'+
                            '<td><input onkeyup="function_change_quantity('+data[i].item_packge_id+', this.value, '+i+')" type="number" name="'+i+'_quantity" id="'+i+'_quantity" style="width: 100%; text-align: center; height: 30px; font-weight: bold; font-size: 15px" class="form-control input-bb" value="'+data[i].quantity+'" autocomplete="off">'+
                            '<td><div id="'+i+'_price_amount" name="'+i+'_price_amount" class="text-right">'+toRp(data[i].subtotal_amount_after_discount)+'</div></td>'+
                            '</tr>';

                    no++;
                }
                i++;
            }
            
            total_amount_af_voucher = total_amount - voucher_amount;
            discount_amount = (total_amount_af_voucher * discount_percentage_total) / 100;
            total_amount_af_discount = total_amount_af_voucher - discount_amount;
            change_amount = paid_amount - total_amount_af_discount;

            if (paid_amount != 0) {
                $('#change_amount_view').val(toRp(change_amount));
                $('#change_amount').val(change_amount);
            }
            $('#subtotal_amount_view').text('Rp '+toRp(total_amount_af_discount));
            $('#subtotal_amount_change').val(total_amount_af_discount);
            $('#subtotal_amount').val(total_amount);
            $('#discount_amount_total').val(discount_amount);
            $('#total_item').val(total_item);
            $('#show_data').html(html);

        } else if (data == null) {
            var html =  '<tr>'+
                            '<td colspan="6" style="text-align: center; font-weight: bold">Data Kosong</td>'+
                        '</tr>';

            $('#show_data').html(html);
        }


        if (msg != null) {
            var myWindow = window.open("{{ route('print-sales-invoice') }}",'','width=800, height=600');
            myWindow.print();
            setTimeout(function() { 
                myWindow.close();
        }, 2000);
        }
    });

    function function_change_quantity(item_packge_id, value, no){
        $('#'+no+'_quantity').keyup(function(e){
            if (e.keyCode == 13) {
                $.ajax({
                    url: "{{ url('sales-invoice/change-qty') }}"+'/'+item_packge_id+'/'+value,
                    type: "GET",
                    dataType: "json",
                    success:function(data)
                    {
                        var html = '';
                        var i = 0;
                        var no = 1;
                        var total_amount = 0;
                        var total_item = 0;
                        var ppn = 0;
                        var paid_amount = $('#paid_amount').val() || 0;
                        var voucher_amount = $('#voucher_amount').val() || 0;
                        var discount_percentage_total = $('#discount_percentage_total').val() || 0;
                        while (i < data.length) {
                            if (data[i].quantity != 0) {
                                total_amount += parseInt(data[i].subtotal_amount_after_discount);
                                total_item += parseInt(data[i].quantity);
                                html += '<tr>'+
                                        '<td class="text-center">'+no+'.</td>'+
                                        '<td>'+data[i].item_name+'</td>'+
                                        '<td>'+data[i].item_unit_name+'</td>'+
                                        '<td>'+toRp(data[i].item_unit_price)+'</td>'+
                                        '<td><input onkeyup="function_change_quantity('+data[i].item_packge_id+', this.value, '+i+')" type="number" name="'+i+'_quantity" id="'+i+'_quantity" style="width: 100%; text-align: center; height: 30px; font-weight: bold; font-size: 15px" class="form-control input-bb" value="'+data[i].quantity+'" autocomplete="off">'+
                                        '<td><div id="'+i+'_price_amount" name="'+i+'_price_amount" class="text-right">'+toRp(data[i].subtotal_amount_after_discount)+'</div></td>'+
                                        '</tr>';
        
                                no++;
                            }
                            i++;
                        }
                        
                        total_amount_af_voucher = total_amount - voucher_amount;
                        discount_amount = (total_amount_af_voucher * discount_percentage_total) / 100;
                        total_amount_af_discount = total_amount_af_voucher - discount_amount;
                        change_amount = paid_amount - total_amount_af_discount;
        
                        if (paid_amount != 0) {
                            $('#change_amount_view').val(toRp(change_amount));
                            $('#change_amount').val(change_amount);
                        }
                        $('#subtotal_amount_view').text('Rp '+toRp(total_amount_af_discount));
                        $('#subtotal_amount_change').val(total_amount_af_discount);
                        $('#subtotal_amount').val(total_amount);
                        $('#discount_amount_total').val(discount_amount);
                        $('#total_item').val(total_item);
                        $('#show_data').html(html);
                    }
                });
            }
        })
        // if (value != '') {
        //     setTimeout(function(){
        //         $.ajax({
        //             url: "{{ url('sales-invoice/change-qty') }}"+'/'+item_packge_id+'/'+value,
        //             type: "GET",
        //             dataType: "json",
        //             success:function(data)
        //             {
        //                 var html = '';
        //                 var i = 0;
        //                 var no = 1;
        //                 var total_amount = 0;
        //                 var total_item = 0;
        //                 var paid_amount = $('#paid_amount').val() || 0;
        //                 var voucher_amount = $('#voucher_amount').val() || 0;
        //                 var discount_percentage_total = $('#discount_percentage_total').val() || 0;
        //                 while (i < data.length) {
        //                     if (data[i].quantity != 0) {
        //                         total_amount += parseInt(data[i].subtotal_amount_after_discount);
        //                         total_item += parseInt(data[i].quantity);
        //                         html += '<tr>'+
        //                                 '<td class="text-center">'+no+'.</td>'+
        //                                 '<td>'+data[i].item_name+'</td>'+
        //                                 '<td>'+data[i].item_unit_name+'</td>'+
        //                                 '<td>'+toRp(data[i].item_unit_price)+'</td>'+
        //                                 '<td><input oninput="function_change_quantity('+data[i].item_packge_id+', this.value)" type="number" name="'+i+'_quantity" id="'+i+'_quantity" style="width: 100%; text-align: center; height: 30px; font-weight: bold; font-size: 15px" class="form-control input-bb" value="'+data[i].quantity+'" autocomplete="off">'+
        //                                 '<td><div id="'+i+'_price_amount" name="'+i+'_price_amount" class="text-right">'+toRp(data[i].subtotal_amount_after_discount)+'</div></td>'+
        //                                 '</tr>';
        
        //                         no++;
        //                     }
        //                     i++;
        //                 }
                        
        //                 total_amount_af_voucher = total_amount - voucher_amount;
        //                 discount_amount = (total_amount_af_voucher * discount_percentage_total) / 100;
        //                 total_amount_af_discount = total_amount_af_voucher - discount_amount;
        //                 change_amount = paid_amount - total_amount_af_discount;
        
        //                 if (paid_amount != 0) {
        //                     $('#change_amount_view').val(toRp(change_amount));
        //                     $('#change_amount').val(change_amount);
        //                 }
        //                 $('#subtotal_amount_view').text('Rp '+toRp(total_amount_af_discount));
        //                 $('#subtotal_amount_change').val(total_amount_af_discount);
        //                 $('#subtotal_amount').val(total_amount);
        //                 $('#discount_amount_total').val(discount_amount);
        //                 $('#total_item').val(total_item);
        //                 $('#show_data').html(html);
        //             }
        //         });
        //     }, 1500);
        // }
    }

    function count_total(){
        var voucher_id  = $('#voucher_id').val();
        var discount_percentage_total = $('#discount_percentage_total').val() || 0;
        var subtotal_amount = $('#subtotal_amount').val() || 0;
        var paid_amount = $('#paid_amount').val() || 0;

        if (voucher_id == null) {
            $('#voucher_amount').val('');
        }

        if ((voucher_id != null) && (discount_percentage_total == 0)) {
            $.ajax({
                    type: "POST",
                    url : "{{route('select-voucher-sales-invoice')}}",
                    data : {
                        'voucher_id'    : voucher_id, 
                        '_token'        : '{{csrf_token()}}'
                    },
                    success: function(msg){
                        if (msg != '') {
                            voucher_amount = msg;
                            total_amount_af_voucher_amount = subtotal_amount - voucher_amount;
                            change_amount = paid_amount - total_amount_af_voucher_amount;
                            $('#voucher_amount').val(voucher_amount);
                            $('#subtotal_amount_view').text('Rp '+toRp(total_amount_af_voucher_amount));
                            $('#subtotal_amount_change').val(total_amount_af_voucher_amount);
                            if (paid_amount != '') {
                                $('#change_amount_view').val(toRp(change_amount));
                                $('#change_amount').val(change_amount);
                            }
                        }
                }
            });
        } else if ((voucher_id == null) && ((discount_percentage_total != 0) && (discount_percentage_total <= 100))) {
            discount_amount = (subtotal_amount * discount_percentage_total) / 100;
            total_amount_af_discount = subtotal_amount - discount_amount;
            console.log(subtotal_amount);
            console.log(discount_percentage_total);
            console.log({'paid':paid_amount});
            console.log(total_amount_af_discount);
            change_amount = paid_amount - total_amount_af_discount;
            console.log({'chabge':change_amount});
            
            $('#subtotal_amount_view').text('Rp '+toRp(total_amount_af_discount));
            $('#subtotal_amount_change').val(total_amount_af_discount);
            $('#discount_amount_total').val(discount_amount);
            if (paid_amount != '') {
                $('#change_amount_view').val(toRp(Math.round(change_amount)));
                $('#change_amount').val(change_amount);
            }
        } else if ((voucher_id != null) && ((discount_percentage_total != 0) && (discount_percentage_total <= 100))) {
            $.ajax({
                    type: "POST",
                    url : "{{route('select-voucher-sales-invoice')}}",
                    data : {
                        'voucher_id'    : voucher_id, 
                        '_token'        : '{{csrf_token()}}'
                    },
                    success: function(msg){
                        if (msg != '') {
                            voucher_amount = msg;
                            total_amount_af_voucher_amount = subtotal_amount - voucher_amount;
                            $('#voucher_amount').val(voucher_amount);

                            discount_amount = (total_amount_af_voucher_amount * discount_percentage_total) / 100;
                            total_amount_af_discount = total_amount_af_voucher_amount - discount_amount;
                            change_amount = paid_amount - total_amount_af_discount;
                            
                            $('#subtotal_amount_view').text('Rp '+toRp(total_amount_af_discount));
                            $('#subtotal_amount_change').val(total_amount_af_discount);
                            $('#discount_amount_total').val(discount_amount);
                            if (paid_amount != '') {
                                $('#change_amount_view').val(toRp(change_amount));
                                $('#change_amount').val(change_amount);
                            }
                        }
                }
            });
        } else if ((voucher_id == null) && (discount_percentage_total == 0)) {
            $('#subtotal_amount_view').text('Rp '+toRp(subtotal_amount));
            $('#subtotal_amount_change').val(subtotal_amount);

            if (paid_amount != '') {
                change_amount = paid_amount - subtotal_amount;
                $('#change_amount_view').val(toRp(change_amount));
                $('#change_amount').val(change_amount);
            }
        } else if (discount_percentage_total > 100) {
            alert('Diskon Tidak Boleh Melebihi 100%');
            var voucher_amount = $('#voucher_amount').val() || 0;
            subtotal_amount_af_voucher_amount = subtotal_amount - voucher_amount;

            $('#discount_percentage_total').val('');
            $('#subtotal_amount_view').text('Rp '+toRp(subtotal_amount_af_voucher_amount));
            $('#subtotal_amount_change').val(subtotal_amount_af_voucher_amount);
            if (paid_amount != '') {
                change_amount = paid_amount - subtotal_amount_af_voucher_amount;
                $('#change_amount_view').val(toRp(change_amount));
                $('#change_amount').val(change_amount);
            }
        }

    }
    
    function function_elements_add(name, value){
        if (name == 'sales_payment_method') {
            if (value == '1') {
                $('#label-payment').text('Kembalian');
            } else if (value == '2') {
                $('#label-payment').text('kembalian');
            }
        }
		$.ajax({
				type: "POST",
				url : "{{route('add-elements-sales-invoice')}}",
				data : {
                    'name'      : name, 
                    'value'     : value,
                    '_token'    : '{{csrf_token()}}'
                },
				success: function(msg){
			}
		});
        
        if (name == 'customer_id') {
            $.ajax({
				type: "POST",
				url : "{{route('check-customer-sales-invoice')}}",
				data : {
                    'value'     : value,
                    '_token'    : '{{csrf_token()}}'
                },
				success: function(msg){
                    if (msg == '1') {
                        alert('Anggota telah diblokir');
                        $('#customer_id').select2('val','0');
                    } else if (msg == '2') {
                        $('#notifPiutang').removeClass('d-none');
                    } else {
                        $('#notifPiutang').addClass('d-none');
                    }
                    // if (msg == '1') {
                    //     alert('Anggota telah diblokir');
                    //     $('#customer_id').select2('val','0');
                    // } else if (msg == '2') {
                    //     alert('Anggota telah melebihi limit');
                    //     $('#customer_id').select2('val','0');
                    // }
			}
		});
        }
	}

    $(document).ready(function(){
        var customer_id = {!! json_encode($datases['customer_id'] ?? '') !!}
        var sales_payment_method = {!! json_encode($datases['sales_payment_method'] ?? '') !!};

        // $('#customer_id').select2('val','1');

        if (customer_id == null) {
            $('#customer_id_view').select2('val','0');
            $('#customer_id').val('0');
        }
        if ((customer_id == 1) && (sales_payment_method == 7)) {
            $('#customer_id_view').attr('disabled','true'); 
            $('#customer_id').val(1);
            $('#purchase_invoice_no').show();
            $('#label_po').show();
            $('#tempo').hide();
            $('#label-tempo').hide();
        }else{
            $('#purchase_invoice_no').hide();
            $('#label_po').hide();
        }
        
        if(sales_payment_method == 2){
            $('#tempo').show();
            $('#label-tempo').show();
        }else if(sales_payment_method == 7){
            $('#customer_id_view').attr('disabled','true'); 
            $('#customer_id').val(1);
            $('#purchase_invoice_no').show();
            $('#label_po').show();
            $('#tempo').hide();
            $('#label-tempo').hide();
            customer_id = 1;
            console.log(customer_id);
        }else{
            $('#purchase_invoice_no').hide();
            $('#label_po').hide();
            $('#tempo').hide();
            $('#label-tempo').hide();
            
        }

        if(sales_payment_method == '1'){
            $('#tempo').hide();
            $('#label-tempo').hide();
            console.log(sales_payment_method);
        }

        if (sales_payment_method == '1') {
            $('#label-payment').text('Kembalian');
        } else if (sales_payment_method == '2') {
            $('#label-payment').text('kembalian');
        }

        if ((customer_id != null) && ((sales_payment_method == 1) || (sales_payment_method == ''))) {
            $('#label_voucher').removeClass('d-none');
            $('#customer_id').val(customer_id);
        } else {
            $('#label_voucher').addClass('d-none');
        }
        if($('#customer_id_view').val()!=''){
            $('#customer_id').val($('#customer_id_view').val());
        }
        $('#voucher_id').select2('val','0');
    });
    
                                          

    $('#sales_payment_method').change(function(){
        var customer_id = $('#customer_id_view').val();
        var sales_payment_method = $('#sales_payment_method').val();

        if ((customer_id != null) && (sales_payment_method == 1)) {
            $('#label_voucher').removeClass('d-none');
            $('#customer_id').removeAttr('disabled','true');
        }else  if(sales_payment_method == 2){
            $('#tempo').show();
            $('#label-tempo').show();
            // location.reload();
        }else if(sales_payment_method == 7){
            $('#customer_id_view').attr('disabled','true');
            $('#customer_id_view').select2('val','1');
            $('#customer_id').val(1);
            $('#purchase_invoice_no').show();
            $('#label_po').show();
            // location.reload();
            console.log(customer_id);
        }else {
            $('#label_voucher').addClass('d-none');
            $('#customer_id').val(1);
            $('#customer_id').removeAttr('readonly',true);
            $('#tempo').hide();
            $('#label-tempo').hide();
        }
        if (sales_payment_method != 7) {
            $('#customer_id_view').removeAttr('disabled','true');
            $('#customer_id').val(customer_id);
            $('#purchase_invoice_no').hide();
            $('#label_po').hide();
        } 


        if(sales_payment_method == '1'){
            $('#tempo').hide();
            $('#label-tempo').hide();
            console.log(sales_payment_method);
        }


    });

    function reset_add(){
		$.ajax({
				type: "GET",
				url : "{{route('add-reset-sales-invoice')}}",
				success: function(msg){
                    location.reload();
			}

		});
	}

    $(document).keydown(function(e){
        if (e.ctrlKey && (e.keyCode == 13)) {
            $('#form-prevent').submit();
        } else if (e.ctrlKey && e.shiftKey) {
            $('#staticBackdrop').modal('show');
            setTimeout(
                function(){
                    $('div.dataTables_filter input').focus();
                },500
            );
        } else if (e.ctrlKey && (e.keyCode == 192)) {
            $('#form-reset').click();
        }
    });

    $(document).ready(function(){
        $('#myDataTable').DataTable({
    
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "lengthMenu": [ [5, 15, 20, 100000], [5, 15, 20, "All"] ],
            "pageLength": 5,
            "ordering": false,
            "ajax": "{{ url('table-sales-item') }}",
            "columns": [
                { data: 'no', "bSortable": false},
                { data: 'item_name', "bSortable": false},
                { data: 'item_unit_name', "bSortable": false},
                { data: 'item_barcode', "bSortable": false},
                { data: 'item_unit_price', "bSortable": false},
                { data: 'action', "bSortable": false},
            ],
        });

        $('#btn_item').click(
            function() {
                // $('#myDataTable').DataTable().search('').draw();
                setTimeout(
                    function(){
                        $('div.dataTables_filter input').focus();
                    },500
                );
            }
        );
    });
</script>
@stop
@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ url('sales-invoice') }}">Daftar Penjualan</a></li>
        <li class="breadcrumb-item active" aria-current="page">Tambah Penjualan</li>
    </ol>
  </nav>

@stop

@section('content')

<h3 class="page-title">
    Form Tambah Penjualan
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

<form action="{{ route('process-add-sales-invoice') }}" method="post" id="form-prevent">
    @csrf
    <div class="row">
        <div class="col">
            <div class="card border border-dark h-100">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <a class="text-dark col-form-label">Tanggal<a class='red'> *</a></a>
                        </div>
                        <div class="col-sm-8">
                            <input class="form-control input-bb" name="sales_invoice_date" id="sales_invoice_date" type="date" data-date-format="dd-mm-yyyy" autocomplete="off" value="{{ date('Y-m-d');  }}" onchange="function_elements_add(this.name, this.value)"/>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-sm-4">
                            <a class="text-dark col-form-label">Anggota<a class='red'> *</a></a>
                        </div>
                        <div class="col-sm-8">
                          {!! Form::select('member_id', $customers, $datases['customer_id'] ?? '', ['class' => 'form-control selection-search-clear select-form', 'id' => 'customer_id_view','name' => 'customer_id_view','onchange'=>'function_elements_add(this.name, this.value ) ']) !!}
                          <input hidden type="text" class="form-control input-bb" id="customer_id" name="customer_id" autocomplete="off" />
                          <small id="notifPiutang" class="text-danger d-none">
                            Anggota memiliki piutang.
                          </small>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-sm-4">
                            <a class="text-dark col-form-label">Metode Pembayaran<a class='red'> *</a></a>
                        </div>
                        <div class="col-sm-8">
                          {!! Form::select(0, $sales_payment_method_list, $datases['sales_payment_method'] ?? '', ['class' => 'form-control selection-search-clear select-form', 'id' => 'sales_payment_method','name' => 'sales_payment_method','onchange'=>'function_elements_add(this.name, this.value)']) !!}
                        </div>
                    </div>
                    <br>
                    <div class="row mb-4">
                        <div class="col-sm-4">
                            <a id="label_po" class="text-dark col-form-label">No.PO Customer</a>
                        </div>
                        <div class="col-sm-8">
                            <input class="form-control input-bb" name="purchase_invoice_no" id="purchase_invoice_no" type="text"  autocomplete="off" onchange="function_elements_add(this.name, this.value)"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border border-dark h-100">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <a class="text-dark col-form-label">Barcode</a>
                        </div>
                        <div class="col-sm-8">
                            <input class="form-control input-bb" id="item_code" autocomplete="off" autofocus />
                        </div>
                    </div>
                    <div class="row mt-5">
                        <div class="col-sm-12">
                            {{-- <input class="form-control input-bb" id="item_name" value="" autocomplete="off" data-bs-toggle="modal" data-bs-target="#staticBackdrop"/> --}}

                            <button type="button" class="btn btn-primary btn-block" id="btn_item" data-bs-toggle="modal" data-bs-target="#staticBackdrop"><i class="fa fa-search"></i> Cari Barang</button>
                              
                              <!-- Modal -->
                            <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="staticBackdropLabel">Daftar Barang<b></b></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="table-responsive">
                                                <table id="myDataTable" style="width:100%" class="table table-striped table-bordered table-hover table-full-width">
                                                    <thead>
                                                        <tr>
                                                            <th width="5%" style='text-align:center'>No</th>
                                                            <th width="40%" style='text-align:center'>Nama Barang</th>
                                                            <th width="20%" style='text-align:center'>Satuan</th>
                                                            <th width="20%" style='text-align:center'>Barcode</th>
                                                            <th width="20%" style='text-align:center'>Harga</th>
                                                            <th width="15%" style='text-align:center'>Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- <div class="col-md-4">
            <div class="card border border-dark h-100">
                <div class="card-body">
                    <div class="text-left mb-3">
                        <div style="font-weight: bold; font-size: 25px">TOTAL</div>
                    </div>
                    <div class="text-right">
                        <div class="text-danger" style="font-weight: bold; font-size: 46px" id="subtotal_amount_view">Rp 0.00</div>
                        <input type="text" id="subtotal_amount" name="subtotal_amount" hidden>
                        <input type="text" id="subtotal_amount_change" name="subtotal_amount_change" hidden>
                    </div>
                </div>
            </div>
        </div> --}}
    </div>

<div class="row">
    <div class="col-md-12 mt-4">
        <div class="card border border-dark h-100">
            <div class="card-header border-dark bg-dark">
                <h5 class="mb-0 float-left">
                    Daftar Barang
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-advance table-hover">
                        <thead style="text-align: center">
                            <th style="width: 5%;">No.</th>
                            <th style="width: 19%;">Nama Barang</th>
                            <th style="width: 19%;">Satuan Barang</th>
                            <th style="width: 19%;">Harga Satuan</th>
                            <th style="width: 19%;">Jumlah Barang</th>
                            <th style="width: 19%;">Total Setelah PPN</th>
                        </thead>
                        <tbody id="show_data">
                           
                        </tbody>
                        <tr>
                            <td colspan="5" style="text-align: right;font-weight: bold;">Subtotal</td>
                            <td style="text-align: right;font-weight: bold;" id="subtotal_amount_view">Rp 0.00</td>
                            <input type="text" id="subtotal_amount" name="subtotal_amount" hidden> 
                            <input type="text" id="subtotal_amount_change" name="subtotal_amount_change" hidden>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mt-4">
        <div class="card border border-dark h-100">
            <div class="card-body">
                <div class="d-none" id="label_voucher">
                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <a class="text-dark col-form-label">Voucher</a>
                        </div>
                        <div class="col-sm-4">
                            {!! Form::select('voucher_id', $vouchers, 0, ['class' => 'form-control selection-search-clear select-form', 'id' => 'voucher_id','name' => 'voucher_id', 'onchange' => 'count_total()']) !!}
                        </div>
                        <div class="col-sm-4">
                            <input class="form-control input-bb text-right" type="text" value="" id="voucher_amount" name="voucher_amount" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <a class="text-dark col-form-label">No. Voucher</a>
                        </div>
                        <div class="col-sm-8">
                            <input class="form-control input-bb" type="text" value="" id="voucher_no" name="voucher_no" autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4">
                        <a class="text-dark col-form-label">Diskon (%)</a>
                    </div>
                    <div class="col-sm-8">
                        <input class="form-control input-bb" id="discount_percentage_total" name="discount_percentage_total" autocomplete="off" onchange="count_total()"/>
                        <input id="discount_amount_total" name="discount_amount_total" autocomplete="off" hidden/>
                        <input type="text" value="" id="total_item" name="total_item" hidden>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4">
                        <a class="text-dark col-form-label">Bayar</a><a class='red'> *</a></a>
                    </div>
                    <div class="col-sm-8">
                        <input class="form-control input-bb text-right" id="paid_amount" name="paid_amount" autocomplete="off" onchange="count_total()"/>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4">
                        <a id="label-payment" class="text-dark col-form-label">Kembalian</a><a class='red'> *</a></a>
                    </div>
                    <div class="col-sm-8">
                        <input class="form-control input-bb text-right" id="change_amount_view" name="change_amount_view" disabled/>
                        <input class="form-control input-bb" id="change_amount" name="change_amount" hidden/>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4">
                        <a id="label-tempo" class="text-dark col-form-label">Jangka Waktu</a></a>
                    </div>
                    <div class="col-sm-8">
                        <input class="form-control input-bb" id="tempo" name="tempo" />
                    </div>
                </div>
                <br>
                <div class="">
                    <div class="form-actions float-right">
                        <button type="reset" name="Reset" class="btn btn-danger" id="form-reset" onclick="reset_add();"><i class="fa fa-times"></i> Batal</button>
                        <button type="button" name="Save" class="btn btn-success button-prevent" onclick="$(this).addClass('disabled');$('form').submit();" title="Save"><i class="fa fa-check"></i> Simpan</button>
                    </div>
                </div>
            </div>
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