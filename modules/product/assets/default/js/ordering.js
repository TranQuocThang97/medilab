imsOrdering = {
    createOrder:function(form_id){
        $("#"+form_id).validate({
            submitHandler: function() {
                var form_mess = $('#'+form_id).find('.form_mess');
                form_mess.stop(true,true).slideUp(200).html('');
                var fData = $("#"+form_id).serializeArray();
                $('.btn_pay').addClass("loading_spin");
                var text = $('.btn_pay').text();
                $('.btn_pay').text(lang_js['loading_']);
                $.ajax({
                    type: "POST",
                    url: ROOT+"ajax.php",
                    data: { "m" : "product", "f" : "createOrder", "data" : fData}
                }).done(function( string ) {
                    var data = JSON.parse(string);
                    if(data.ok == 1) {
                        if (data.link!="") { go_link(data.link); }
                    } else {
                        if (data.link!="") {
                            go_link(data.link);
                        }else{
                            Swal.fire({
                                icon: 'error',
                                title: lang_js['aleft_title'],
                                text: data.mess,
                            });
                            $('.btn_pay').removeClass('loading_spin');
                            $('.btn_pay').text(text);
                        }
                    }
                });
                return false;
            },
            rules: {
            },
            messages: {
            }           
        });
    },
    shippingFee:function(){
        var total_money       = $('.temp-total-money span.number').data('value');
        var total_promotion   = $('.total-promotion span.number').data('value');
        var total_wcoin       = $('.total-wcoin span.number').data('value');
        if($('.list-shipping').length > 0){
            var shipping     = $('.list-shipping input[name=shipping]:checked').val();
            var method       = $('.list-method input[name=method]:checked').val();
            var address = '',
                province = 0,
                district = 0,
                ward = 0;
            if($(".address.check").length>0){
                address     = $(".address.check").attr("data-address");
                province    = $(".address.check").attr("data-province");
                district    = $(".address.check").attr("data-district");
                ward        = $(".address.check").attr("data-ward");
            }
            if($('.list_form_address').length>0){
                address     = $('#address').val();
                province    = $('#province').val();
                district    = $('#district').val();
                ward        = $('#ward').val();
            }
            var oaddress = [];
            oaddress.push({name: 'address',value: address});
            oaddress.push({name: 'province',value: province});
            oaddress.push({name: 'district',value: district});
            oaddress.push({name: 'ward',value: ward});

            if(!shipping) return false;
            loading("show");
            $.ajax({
               type: "POST",
               url: ROOT + "ajax.php",
               data: {"m": "product", "f": "shippingFee", "shipping_id": shipping , "method_id": method, "total_money" : total_money, "total_promotion" : total_promotion, "total_wcoin" : total_wcoin, "oaddress" : oaddress }
            }).done(function (string) {
               loading('hide');
                var data = JSON.parse(string);   
                console.log(data);
                if (data.ok == 1 || data.ok == 3 || data.ok == 2) {
                    $('.shipping_price .price_format .number').attr("data-value", data.price_out_no_format);
                    $('.shipping_price .price_format .number').html(data.price_out);
                    $('.total-payment .price_format .number').text(data.total_payment);
                    
                    $('.method_price span.percent').text(data.method_value);
                    $('.method_price .price_format .number').text(data.method_price_out);
                }
                // else if(data.ok == 2){
                //     $('.shipping_price .price_format .number').text(lang_js['free']);
                //     $('.total-payment .price_format .number').text(data.total_payment);
                //
                //     $('.method_price span.percent').text(data.method_value);
                //     $('.method_price .price_format .number').text(data.method_price_out);
                // }
                else{
                    $('.shipping_price .price_format .number').text(lang_js['free']);
                    $('.total-payment .price_format .number').text(total_money);
                }
                setTimeout(function(){auto_price_format();},500);
            });
        }
        setTimeout(function(){auto_price_format();},500);
    },
    promotionCode:function(form_id) {
        $("#"+form_id).validate({
            submitHandler: function() {
                var form_mess = $('#'+form_id).find('.form_mess');
                form_mess.stop(true,true).slideUp(200).html('');
                var fData = $("#"+form_id).serializeArray();
                var cart_total = $(".temp-total-money span.number").data('value');
                fData.push({
                    name: "cart_total",
                    value: cart_total
                });
                // $('#'+form_id).find('button').addClass('loading_spin loading_red');
                $.ajax({
                    type: "POST",
                    url: ROOT+"ajax.php",
                    data: { "m" : "product", "f" : "promotionCode", "data" : fData}
                }).done(function( string ) {
                    var data = JSON.parse(string);
                    if(data.ok == 1) {
                        if(data.type == 1){
                            form_mess.html(imsTemp.html_alert(data.mess,'success')).stop(true,true).slideDown(200);
                            setTimeout(function () {
                                imsOrdering.load_order_discount();
                            }, 500);
                        }else{
                            $('#order_discount .form_mess').html(imsTemp.html_alert(data.mess,'success')).stop(true,true).slideDown(200);
                            $('#order_discount button.btn-primary').prop('disabled', true);
                            $('input[name="promotional_code"]').val('');
                        }
                        $(".temp-total-promotion .badge-info").show().find('span').text(data.promotion_id);
                        if(data.freeship == 0){
                            $(".temp-total-promotion").attr("data-type_promotion",data.promotion_type_promotion);
                            $(".temp-total-promotion").attr("data-min_cart",data.promotion_price_min);
                            $(".temp-total-promotion").attr("data-value",data.promotion_value);
                            $(".temp-total-promotion").attr("data-type",data.promotion_value_type);
                            $(".temp-total-promotion").attr("data-value_max",data.promotion_value_max);
                            $(".temp-total-promotion .number").attr("data-value",data.promotion_price);
                            $(".temp-total-promotion .minus").show();
                            if(data.promotion_type_promotion == 'apply_product'){
                                $(".temp-total-promotion").attr("data-price",data.promotion_price);
                            }else{
                                $(".temp-total-promotion").attr("data-price", 0);
                            }
                        }else{
                            $(".temp-total-promotion").attr("data-type_promotion",'');
                            $(".temp-total-promotion").attr("data-min_cart",'');
                            $(".temp-total-promotion").attr("data-value",'');
                            $(".temp-total-promotion").attr("data-type",'');
                            $(".temp-total-promotion").attr("data-value_max",'');
                            $(".temp-total-promotion").attr("data-price",'');
                            $(".temp-total-promotion .number").attr("data-value",'');
                            $(".temp-total-promotion .minus").hide();
                        }

                        // $(".temp-total-promotion").html(data.promotion_price_out);

                        imsOrdering.cartUpdateHtml('form_cart');
                        if(data.freeship == 1){
                            $(".temp-total-promotion .price_format span.number").text('');
                        }
                    } else {
                        if(data.type == 1){
                            form_mess.html(data.mess).stop(true,true).slideDown(200);
                            setTimeout(function () {
                                imsOrdering.load_order_discount();
                            }, 500);
                        }else{
                            $('#order_discount .form_mess').html(data.mess).stop(true,true).slideDown(200);
                        }
                    }
                    $('#'+form_id).find('button').removeClass('loading_spin loading_red');
                    auto_price_format();
                });
                return false;
            },
            rules: {
                promotional_code: {
                    required: true
                },
            },
            messages: {
                promotional_code: '',
            }           
        });
    },
    cartremovePromotionCode:function() {
        $(document).on("click", ".removePromotionCode", function () {
            $(this).addClass('loading_spin');
            $.ajax({
                type: "POST",
                url: ROOT+"ajax.php",
                data: { "m" : "product", "f" : "cartremovePromotionCode"}
            }).done(function( string ) {
                var data = JSON.parse(string);
                if(data.ok == 1) {
                    location.reload();
                } else {
                    form_mess.html(data.mess).stop(true,true).slideDown(200);
                }
                $(this).removeClass('loading_spin');
            });
            return false;
        });
    },
    useWcoin:function(form_id) {
        $("#"+form_id).validate({
            submitHandler: function() {
                var form_mess = $('#'+form_id).find('.form_mess');
                form_mess.stop(true,true).slideUp(200).html('');
                var fData = $("#"+form_id).serializeArray();
                var cart_total = $(".temp-total-money span.number").data('value');
                fData.push({
                    name: "cart_total",
                    value: cart_total
                });
                // $('#'+form_id).find('button').addClass('loading_spin loading_red');
                $.ajax({
                    type: "POST",
                    url: ROOT+"ajax.php",
                    data: { "m" : "user", "f" : "useWcoin", "data" : fData}
                }).done(function( string ) {
                    var data = JSON.parse(string);                  
                    if(data.ok == 1) {
                        form_mess.html(imsTemp.html_alert(data.mess,'success')).stop(true,true).slideDown(200);
                        $('.use_wcoin').html(data.price_wcoin);
                        // $('.number_wcoin').text(data.user_wcoin);
                        imsOrdering.cartUpdateHtml('form_cart');
                        auto_price_format();
                    } else {
                        form_mess.html(imsTemp.html_alert(data.mess,'error')).stop(true,true).slideDown(200);
                    }
                    $('#'+form_id).find('button').removeClass('loading_spin loading_red');
                });
                return false;
            },
            rules: {
                wcoin: {
                    required: true
                },
            },
            messages: {
                wcoin: '',
            }           
        });
    },
    is_buynow : false,
    add_cart: function (form) {        
        form = (form) ? form : 'form.form_add_cart';        
        if ($(form).length) {                
            $(form).on("click",'.btn_add_cart',function () {
                imsOrdering.is_buynow = false;
            });
            $(form).on("click",'.btn_add_cart_now',function () {
                imsOrdering.is_buynow = true;
            });
            $(form).submit(function (e) {
                var fData = $(this).serializeArray();
                var form = $(this),
                    link_go_detail = form.attr('data-go'),
                    link_go = $(this).attr('action');
                if (typeof link_go_detail !== typeof undefined && link_go_detail !== false) {
                    go_link(link_go_detail);
                    return false;
                }
                if (imsOrdering.is_buynow == true) {  form.find('.btn_add_cart_now').addClass('loading_spin'); }
                if (imsOrdering.is_buynow == false) { form.find('.btn_add_cart').addClass('loading_spin'); }
                $.ajax({
                    type: "POST",
                    url: ROOT + "ajax.php",
                    data: {"m": "product", "f": "addCart", "data": fData}
                }).done(function (string) {
                    var data = JSON.parse(string);
                    if (imsOrdering.is_buynow == true) { form.find('.btn_add_cart_now').removeClass('loading_spin'); }
                    if (imsOrdering.is_buynow == false) { form.find('.btn_add_cart').removeClass('loading_spin'); }                    
                    if(data.ok == 1){
                        if(imsOrdering.is_buynow == true) {                        
                            go_link(link_go);
                        }else{
                            $("html, body").animate({
                                scrollTop: 0
                            }, 600);
                            $('#header_cart .add-to-cart-success').show();
                            setTimeout(function() { $('#header_cart .add-to-cart-success').hide(); }, 3000);
                        }                        
                    }else{
                        Swal.fire({
                            icon: 'error',
                            title: lang_js['aleft_title'],
                            text: data.mess,
                        })
                    }                    
                    header_cart();
                });
                return false;
            });
        }
    },
    cartUpdateHtml: function (form_id, value_max=0) {
        var temp_total_money = 0;
        var temp_total_money_include_combo = 0;
        var total_payment = 0;
        var wcoin_use = $('.use_wcoin .price_format .number').attr("data-value");
        var wcoin = localStorage.getItem('wcoin_input')!=null ? localStorage.getItem('wcoin_input') : 0;
        $('#'+form_id+' .cart_row').each(function () {
            var price_buy = parseInt($(this).find('.price span.number').data('value'));
            var quantity  = parseInt($(this).find('input.quantity_text').val());
            temp_total_money   += price_buy * quantity;
            temp_total_money_include_combo   += price_buy * quantity;
        });

        if($('#'+form_id+' .cart_row .col_gift_include .col_gift.include').length){
            $('#'+form_id+' .cart_row .col_gift_include .col_gift.include').each(function () {
                var price_buy_include = parseInt($(this).find('.info-price .price_buy').data('value'));
                temp_total_money_include_combo   += price_buy_include;
            });
        }

        if($('#order_bundled_product .list_item_chose').length){
            var bundled_endow_price = parseInt($('#order_bundled_product .list_item_chose').attr('data-endow_price'));
            console.log(bundled_endow_price);
            temp_total_money_include_combo += bundled_endow_price; // Cộng thêm tiền sp mua kèm giá ưu đãi
        }

        $('#'+form_id).find('.temp-total-money .number').data({value: temp_total_money_include_combo});

        total_payment = temp_total_money_include_combo;
        total_payment -= wcoin_use;

        if ($('.temp-total-promotion').length) {
            var promotion_price = 0;
            if (temp_total_money_include_combo >= parseInt($('.temp-total-promotion').attr('data-min_cart'))) {
                var promotion_value_max  = $('.temp-total-promotion').attr('data-value_max');
                var promotion_type = $('.temp-total-promotion').attr('data-type');
                var promotion_value      = $('.temp-total-promotion').attr('data-value');
                temp_total_money = parseFloat(temp_total_money);

                var type_promotion = $('.temp-total-promotion').attr('data-type_promotion');
                if(type_promotion == 'apply_product'){
                    tmp_price = parseInt($('.temp-total-promotion').attr('data-price'));
                }else{
                    tmp_price = parseInt($('.temp-total-promotion .number').attr('data-value'));
                    // var tmp_price   = 0;
                    // var tmp_percent = 0;
                    // if (promotion_type == 1) {
                    //     tmp_percent = parseFloat(promotion_value);
                    //     tmp_price = ((tmp_percent * temp_total_money) / 100).toFixed(0);
                    // } else {
                    //     tmp_price = parseFloat(promotion_value);
                    // }
                }

                if(promotion_value_max > 0 && tmp_price > parseFloat(promotion_value_max)){
                    tmp_price = promotion_value_max;
                }else if(promotion_value_max == 0 && tmp_price > temp_total_money){
                    tmp_price = temp_total_money;
                }

                promotion_price   = tmp_price;
                total_payment    -= tmp_price;
            }
            $('#' + form_id).find('.temp-total-promotion .number').data({value: promotion_price});
        }
        if ($('.cart_voucher', '#' + form_id).length) {
            cart_voucher = $('.cart_voucher', '#' + form_id).attr('value');
            cart_voucher_out = cart_voucher;
            if (cart_voucher > total_payment) {
                cart_voucher_out = total_payment;
                total_payment = 0;
            } else {
                total_payment -= cart_voucher;
            }
            $('#' + form_id).find('.data-value .number').data({value: cart_voucher_out});
        }

        if ($('.total-payment', '#' + form_id).length) {
            if(wcoin != 0){
                var promise = imsOrdering.load_wcoin(wcoin, temp_total_money);
                promise.then(function(data){
                    data = JSON.parse(data);
                    $('.use_wcoin').html(data.price_wcoin);
                    $('.number_wcoin').text(data.user_wcoin);
                    $('#' + form_id).find('.total-payment .number').attr("data-value", data.total_amount);
                    $('#' + form_id).find('.total-payment .number').text(data.total_amount);
                    $('#' + form_id).find('.total-payment .number').data({value: total_payment});
                    auto_price_format();
                    // $('#' + form_id).find('.total_payment .number').data({value: data.total_amount});
                })
            }
            else{
                // $('#' + form_id).find('.total-payment .number').attr("data-value",total_payment);
                $('#' + form_id).find('.total-payment .number').text(total_payment);
                $('#' + form_id).find('.total-payment .number').data({value: total_payment});
            }
        }

        if($('#'+form_id+' .order-summary .wcoin_expected').length){
            var percent = $('#'+form_id+' .order-summary .wcoin_expected').data('percent'),
                money = $('#'+form_id+' .order-summary .wcoin_expected').data('money');
            var wcoin_expected = Math.round(total_payment/100*percent/money);
            $('#'+form_id+' .order-summary .wcoin_expected b').text(' '+wcoin_expected);
        }
        auto_rate_exchange();
        return false;
    },
    cartUpdateHtml_bo: function (form_id, value_max=0) {
        var temp_total_money = 0;
        var total_payment = 0;
        var wcoin_use = $('.use_wcoin .price_format .number').attr("data-value");
        var wcoin = localStorage.getItem('wcoin_input')!=null ? localStorage.getItem('wcoin_input') : 0;
        $('#'+form_id+' .cart_row').each(function () {
            var price_buy = parseInt($(this).find('.price span.number').data('value'));
            var quantity  = parseInt($(this).find('input.quantity_text').val());
            temp_total_money   += price_buy * quantity;
        });

        if($('#'+form_id+' .cart_row .col_gift_include .col_gift.include').length){
            $('#'+form_id+' .cart_row .col_gift_include .col_gift.include').each(function () {
                var price_buy_include = parseInt($(this).find('.info-price .price_buy').data('value'));
                temp_total_money   += price_buy_include;
            });
        }

        $('#'+form_id).find('.temp-total-money .number').data({value: temp_total_money});

        total_payment = temp_total_money;
        total_payment -= wcoin_use;

        if ($('.temp-total-promotion').length) {
            var promotion_price = 0;
            if (temp_total_money >= parseInt($('.temp-total-promotion').attr('data-min_cart'))) {
                var promotion_value_max  = $('.temp-total-promotion').attr('data-value_max');
                var promotion_type = $('.temp-total-promotion').attr('data-type');
                var promotion_value      = $('.temp-total-promotion').attr('data-value');
                temp_total_money = parseFloat(temp_total_money);

                var type_promotion = $('.temp-total-promotion').attr('data-type_promotion');
                if(type_promotion == 'apply_product'){
                    tmp_price = parseInt($('.temp-total-promotion').attr('data-price'));
                }else{
                    var tmp_price   = 0;
                    var tmp_percent = 0;
                    if (promotion_type == 1) {
                        tmp_percent = parseFloat(promotion_value);
                        tmp_price = ((tmp_percent * temp_total_money) / 100).toFixed(0);
                    } else {
                        tmp_price = parseFloat(promotion_value);
                    }
                }

                if(promotion_value_max > 0 && tmp_price > parseFloat(promotion_value_max)){
                    tmp_price = promotion_value_max;
                }else if(promotion_value_max == 0 && tmp_price > temp_total_money){
                    tmp_price = temp_total_money;
                }

                promotion_price   = tmp_price;
                total_payment    -= tmp_price;
            }
            $('#' + form_id).find('.temp-total-promotion .number').data({value: promotion_price});
        }
        if ($('.cart_voucher', '#' + form_id).length) {
            cart_voucher = $('.cart_voucher', '#' + form_id).attr('value');
            cart_voucher_out = cart_voucher;
            if (cart_voucher > total_payment) {
                cart_voucher_out = total_payment;
                total_payment = 0;
            } else {
                total_payment -= cart_voucher;
            }
            $('#' + form_id).find('.data-value .number').data({value: cart_voucher_out});
        }
        
        if ($('.total-payment', '#' + form_id).length) {
            if(wcoin != 0){
                var promise = imsOrdering.load_wcoin(wcoin, temp_total_money);
                promise.then(function(data){
                    data = JSON.parse(data);                
                    $('.use_wcoin').html(data.price_wcoin);
                    $('.number_wcoin').text(data.user_wcoin);                    
                    $('#' + form_id).find('.total-payment .number').attr("data-value", data.total_amount);
                    $('#' + form_id).find('.total-payment .number').text(data.total_amount);
                    $('#' + form_id).find('.total-payment .number').data({value: total_payment});
                    auto_price_format();
                    // $('#' + form_id).find('.total_payment .number').data({value: data.total_amount});  
                })
            }
            else{
                $('#' + form_id).find('.total-payment .number').attr("data-value",total_payment); 
                $('#' + form_id).find('.total-payment .number').text(total_payment);  
                $('#' + form_id).find('.total-payment .number').data({value: total_payment});
            }
        }

        if($('#'+form_id+' .order-summary .wcoin_expected').length){
            var percent = $('#'+form_id+' .order-summary .wcoin_expected').data('percent'),
                money = $('#'+form_id+' .order-summary .wcoin_expected').data('money');
            var wcoin_expected = Math.round(total_payment/100*percent/money);
            $('#'+form_id+' .order-summary .wcoin_expected b').text(' '+wcoin_expected);
        }
        auto_rate_exchange();
        return false;
    },
    updateCart: function (form_id, element, link_go) {
        var form_mess = $('#' + form_id).find('.form_mess');
        // form_mess.stop(true, true).slideUp(200).html('');
        var quantity = {};
        $('#' + form_id + ' .cart_row').each(function () {
            var cid = $(this).attr('id').replace('cart_', ''),
            val = $(this).find('[for="'+cid+'"]').find('input.quantity_text').val();
            quantity[cid] = val;
        })
        $.ajax({
            type: "POST",
            url: ROOT + "ajax.php",
            data: {"m": "product", "f": "updateCart", "quantity": quantity}
        }).done(function (string) {
            var data = JSON.parse(string);
            if (data.ok == 1) {
                if (data.mess_class == 'success') {
                    header_cart();
                    if(data.update_promotion == 1){
                        if ($('.temp-total-promotion').length) {
                            if(data.freeship == 0){
                                $(".temp-total-promotion").attr("data-type_promotion",data.promotion_type_promotion);
                                $(".temp-total-promotion").attr("data-min_cart",data.promotion_price_min);
                                $(".temp-total-promotion").attr("data-value",data.promotion_value);
                                $(".temp-total-promotion").attr("data-type",data.promotion_value_type);
                                $(".temp-total-promotion").attr("data-value_max",data.promotion_value_max);
                                $(".temp-total-promotion .minus").show();
                                $(".temp-total-promotion").attr("data-price", data.promotion_price);
                                // if(data.promotion_type_promotion == 'apply_product'){
                                //     $(".temp-total-promotion").attr("data-price", data.promotion_price);
                                // }else{
                                //     $(".temp-total-promotion").attr("data-price", 0);
                                // }
                                if(data.promotion_price > 0){
                                    $('#promotion_code .form_mess').html('');
                                    $('.temp-total-promotion .badge-info').show().find('span').text(data.promotion_id);
                                    $('.temp-total-promotion .price_format .number').attr('data-value', data.promotion_price).text(data.promotion_price);
                                }else{
                                    $('.temp-total-promotion .badge-info').hide();
                                    $('#promotion_code .form_mess').slideDown().html(data.promotion_mess);
                                }
                            }else{
                                if(data.promotion_id != ''){
                                    $(".temp-total-promotion").attr("data-type_promotion",'');
                                    $(".temp-total-promotion").attr("data-min_cart",'');
                                    $(".temp-total-promotion").attr("data-value",'');
                                    $(".temp-total-promotion").attr("data-type",'');
                                    $(".temp-total-promotion").attr("data-value_max",'');
                                    $(".temp-total-promotion").attr("data-price",'');
                                    $(".temp-total-promotion .minus").hide();
                                    $('.temp-total-promotion .badge-info').show().find('span').text(data.promotion_id);
                                    $('#promotion_code .form_mess').html('');
                                }
                            }
                        }
                    }
                    setTimeout(function () {
                        imsOrdering.cartUpdateHtml('form_cart');
                    }, 1200);
                    if(data.update_promotion == 1 && data.freeship == 1){
                        $(".temp-total-promotion .price_format span.number").text('');
                    }
                    if (link_go) {
                        go_link(link_go);
                    } else {
                    }
                } else {
                    Swal.fire({
                        icon: data.mess_class,
                        title: lang_js['aleft_title'],
                        html: data.mess,
                    });
                }
            } else {
                form_mess.html(imsTemp.html_alert(data.mess, 'error')).stop(true, true).slideDown(200);
            }
            if (element!="") {
                setTimeout(function() {
                    element.removeClass('loading_spin loading_red');
                }, 500);
            }            
        });
        return false;
    },
    cartRemoveItem: function () {
        $('#form_cart .delete_cart').click(function () {
            var form_mess = $('#form_cart').find('.form_mess');
            form_mess.stop(true, true).slideUp(200).html('');
            var cart_item = $(this).attr('cart_item');
            loading('show');
            $.ajax({
                type: "POST",
                url: ROOT + "ajax.php",
                data: {"m": "product", "f": "cartRemoveItem", "cart_item": cart_item}
            }).done(function (string) {
                loading('hide');
                var data = JSON.parse(string);
                if (data.ok == 1) {
                    $('#form_cart #cart_' + cart_item).remove();
                    header_cart();
                    if(data.empty == 1){
                        $('.order-box').remove();
                        location.reload();
                    }else{
                        if(data.delete_promotion == 1){
                            if ($('.temp-total-promotion').length) {
                                $(".temp-total-promotion").attr("data-type_promotion",'');
                                $(".temp-total-promotion").attr("data-min_cart",'');
                                $(".temp-total-promotion").attr("data-value",'');
                                $(".temp-total-promotion").attr("data-type",'');
                                $(".temp-total-promotion").attr("data-value_max",'');
                                $(".temp-total-promotion").attr("data-price",'');
                                $('.temp-total-promotion .badge-info').hide().find('span').text('');
                                $('#promotion_code .form_mess').slideDown().html(data.promotion_mess);
                            }
                        }
                        imsOrdering.cartUpdateHtml('form_cart');
                        setTimeout(function () {
                            imsOrdering.load_order_discount();
                            imsOrdering.load_bundled_product();
                        }, 1000);
                    }
                } else {
                    form_mess.html(imsTemp.html_alert(lang_js['delete_false'], 'error')).stop(true, true).slideDown(200);
                }
            });
        });
        return false;
    },
    cartSaveLater: function (form_id) {
        $(document).on("click", "#"+form_id+" .cart_later", function () {
            var form_mess = $("#"+form_id).find('.form_mess');
            form_mess.stop(true, true).slideUp(200).html('');
            var cart_item = $(this).attr('cart_item'),
                id = $(this).attr('data-id'),
                path = window.location.pathname;
            loading('show');
            $.ajax({
                type: "POST",
                url: ROOT + "ajax.php",
                data: {"m": "user", "f": "cartSaveLater", "cart_item": cart_item, "id": id, "path": path}
            }).done(function (string) {
                loading('hide');
                var data = JSON.parse(string);
                if (data.ok == 1) {
                    $('#form_cart #cart_' + cart_item).remove();
                    header_cart();
                    if(data.delete_promotion == 1){
                        if ($('.temp-total-promotion').length) {
                            $(".temp-total-promotion").attr("data-type_promotion",'');
                            $(".temp-total-promotion").attr("data-min_cart",'');
                            $(".temp-total-promotion").attr("data-value",'');
                            $(".temp-total-promotion").attr("data-type",'');
                            $(".temp-total-promotion").attr("data-value_max",'');
                            $(".temp-total-promotion").attr("data-price",'');
                            $('.temp-total-promotion .badge-info').hide().find('span').text('');
                            $('#promotion_code .form_mess').slideDown().html(data.promotion_mess);
                        }
                    }
                    setTimeout(function () {
                        imsOrdering.load_order_discount();
                        imsOrdering.load_bundled_product();
                    }, 800);
                    setTimeout(function () {
                        imsOrdering.cartUpdateHtml('form_cart');
                    }, 1200);
                    Swal.fire({
                        icon: 'success',
                        title: lang_js['aleft_title'],
                        text: data.mess,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            if (data.empty_cart == 1) {
                                $('.order-box').remove();
                                location.reload();
                            }
                        }
                    })
                } else {
                    if(data.saved == 1){
                        Swal.fire({
                            icon: 'error',
                            title: lang_js['aleft_title'],
                            text: data.mess,
                        })
                    }else{
                        Swal.fire({
                            title: lang_js['aleft_title'],
                            html: data.mess,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            cancelButtonText: 'Hủy',
                            confirmButtonText: 'Ok'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                go_link(data.link);
                            }
                        })
                    }
                }
            });
        });
        return false;
    },
    load_order_discount: function () {
        $.ajax({
            type: "POST",
            url: ROOT+"ajax.php",
            data: { "m" : "product", "f" : "load_order_discount"}
        }).done(function( string ) {
            var data = JSON.parse(string);
            if(data.ok == 1) {
                $("#order_discount").removeClass('d-none').html(data.html);
            }else{
                $("#order_discount").addClass('d-none').html('');
            }
        });
        return false;
    },
    load_bundled_product: function () {
        $.ajax({
            type: "POST",
            url: ROOT+"ajax.php",
            data: { "m" : "product", "f" : "load_bundled_product"}
        }).done(function( string ) {
            var data = JSON.parse(string);
            if(data.ok == 1) {
                $("#order_bundled_product").removeClass('d-none').html(data.html);
            }else{
                $("#order_bundled_product").addClass('d-none').html('');
            }
        });
        return false;
    },
};
$(document).ready(function() {
    $("#form_cart .btn_grp").each(function(){
        $(this).children('.quantity_text').on("change",function(){
            $(this).parent().addClass('loading_spin loading_red');
            imsOrdering.updateCart('form_cart', $(this).parent());
        });
        $(this).children(".btn_minus").on("click",function(){
            $(this).parent().addClass('loading_spin loading_red');
            imsOrdering.updateCart('form_cart', $(this).parent());
            setTimeout(function () {
                imsOrdering.load_order_discount();
                imsOrdering.load_bundled_product();
            }, 800);
        });
        $(this).children('.btn_plus').on('click',function(){
            $(this).parent().addClass('loading_spin loading_red');
            imsOrdering.updateCart('form_cart', $(this).parent());
            setTimeout(function () {
                imsOrdering.load_order_discount();
                imsOrdering.load_bundled_product();
            }, 800);
        });
    })
});