<!-- BEGIN: main -->
    {data.content}
<!-- END: main --> 

<!-- BEGIN: table_cart_empty -->
    <div class="order-card">
        <div class="card-content pb-5">
            <div class="card-title">{data.title_page}</div>
            <div class="text-center">
                <img src="{data.dir_images}images/empty_cart.png" alt="" style="max-width: 200px;">
                <div class="mess" style="color: #848788;">{data.mess}</div>
                <div class="buy_now mt-4"><a href="{data.product_link}" class="btn btn-success">{LANG.product.btn_add_cart_now}</a></div>
            </div>
        </div>
    </div>
<!-- END: table_cart_empty -->

<!-- BEGIN: table_cart -->
    <div class="bs-wizard bg-text-color">    
        <div class="bs-wizard-step">                
            <span class="bs-wizard-dot bg-color active"><i class="fas fa-cart-plus"></i></span>
            <div class="text-center bs-wizard-stepnum">
                <span class="hidden-xs">{LANG.product.cart}</span>
            </div>
        </div>
        <div class="progress"><div class="progress-bar"></div></div>
        <div class="bs-wizard-step">                
            <span class="bs-wizard-dot"><i class="far fa-dollar-sign"></i></span>
            <div class="text-center bs-wizard-stepnum">
                <span class="hidden-xs">{LANG.product.payment_orderbuy}</span>
            </div>
        </div>
        <div class="progress progress_1"><div class="progress-bar"></div></div>
        <span class="bs-wizard-step disabled bs-wizard-last">
            <span class="bs-wizard-dot"><i class="fas fa-check"></i></span>
            <div class="bs-wizard-stepnum">
                <span class="hidden-xs">{LANG.product.complete}</span>
            </div>
        </span>
    </div>
    <div class="order-box">
        <div class="order-title">{data.title_page}</div>
        <div id="form_cart">
            <div class="order-box-content d-flex flex-wrap" style="width: 100%;">
                <div class="order-box-left">
                    <div class="form_mess">{data.err}</div>
                    <div class="cart-responsive">
                        <!-- BEGIN: row_item -->
                        <ul class="list_none cart_row {row.class}" id="cart_{row.cart_id}">
                            <li class="col-img d-md-none"><span><img src="{row.picture_zoom}" alt="{row.title}"/></span></li>
                            <li class="col-cart">
                                <div class="col-i col1">
                                    <img src="{row.picture_thumb}" alt="{row.title}"/>
                                </div>
                                <div class="col-i col2">                        
                                    <div class="title_product_cart">
                                        <div class="out_stock">{row.out_stock}</div>
                                        <a class="title" href="{row.link}">{row.title}</a>
                                        <!-- BEGIN: option -->
                                        <div class="code_pro" style="color: #999;">
                                            {row.name}: {row.value}
                                        </div>
                                        <!-- END: option -->
                                    </div>
                                    <div class="row_btn d-flex col-12 p-0 mt-3">
                                        <div class="delete_cart" cart_item="{row.cart_id}">{LANG.product.col_delete}</div>
                                        <div class="cart_later ml-5" data-id="{row.option_id}" cart_item="{row.cart_id}">{LANG.product.col_cart_later}</div>
                                    </div>
                                </div>   
                                <div class="col-i col3">
                                    <div class="price bg-text-color">{row.price_buy_text}</div>
                                    <!-- BEGIN: discount -->
                                    <div class="discount">{row.price_text} <span class="pl-1 pr-1">|</span> -{row.percent_discount}%</div>
                                    <!-- END: discount -->
                                </div>
                                <div class="col-i col4 up_quantity" title="{LANG.product.col_quantity}">
                                    <label class="btn_grp" for="{row.cart_id}" class="quantity">
                                      <span class="btn_minus"><i class="fal fa-minus"></i></span>
                                      <input name="quantity[]" type="number" value="{row.quantity}" min="1" max="{row.max_quantity}" step="1" class="quantity_text no-spinners" disabled/>
                                      <span class="btn_plus"><i class="fal fa-plus"></i></span>
                                      <span class="num_qantity_by"></span>
                                    </label>
                                </div>
                            </li>
                            {row.gift_include}
                        </ul>
                        <!-- END: row_item --> 
                        <!-- BEGIN: promotional -->
                        <ul class="list_none tfoot">
                            <li class="col_total">
                                <span class="col-title">{LANG.product.promotional_code}:</span>
                                <span class="col-content cart_promotion" data-value_type="{data.promotion_value_type}" data-value="{data.promotion_value}">-<span class="percent">{data.promotion_percent}</span>% ({data.promotion_price_out})</span>
                            </li>
                         </ul>
                        <!-- END: promotional -->
                        <!-- BEGIN: cart_payment # disable code -->
                        <ul>
                            <li class="col col_total cart_payment">
                                <span class="col-title">{LANG.product.cart_payment}:</span>
                                <span class="col-content">{data.cart_payment}</span>
                            </li>
                        </ul>
                        <!-- END: promotional -->
                    </div>
                    {data.bundled_product}
                    <div class="mt-4" align="left">
                        <a onclick="go_link('{data.product_link}');" class="btn btn-continue-watch"><i class="ficon-angle-double-left"></i>  {LANG.product.btn_buy_more}</a>
                    </div>
                </div>
                <div class="order-box-right">
                    <div class="order-card">
                        <div class="card-content">
                            <div class="card-header-top">
                                <div class="text d-flex justify-content-between align-items-center">
                                    {LANG.product.general}<a class="collapse"><i class='fal fa-angle-down'></i></a>
                                </div>
                            </div>
                            <div class="card-content-center">
                                <ul class="order-summary">
                                    <li class="d-flex justify-content-between">
                                        <span class="k">{LANG.product.provisional}:</span>
                                        <span class="v temp-total-money">{data.cart_total}</span>
                                    </li>
                                    <!-- BEGIN:promotional_box_show -->
                                    <li class="d-flex justify-content-between">
                                        <span class="k">{LANG.product.promotional}:</span>
                                        <span class="v temp-total-promotion d-flex align-items-center" data-type_promotion="{promotion.type_promotion}" data-min_cart="{promotion.total_min}" data-value_max="{promotion.value_max}" data-type="{promotion.value_type}" data-value="{promotion.value}" data-price="{promotion.price}">
                                            <span class="badge badge-info mr-1 removePromotionCode" title="{LANG.global.delete}" {promotion.hide_code}><span>{promotion.promotion_id}</span> <i class="far fa-times"></i></span>
                                            {promotion.promotion_text}
                                        </span>
                                    </li>
                                    <!-- END:promotional_box_show -->
                                    <!-- BEGIN: wcoin_box_show -->
                                    <li class="d-flex justify-content-between">
                                        <span class="k">{LANG.product.discounts_wcoin}:</span>
                                        <span class="v use_wcoin">{data.payment_wcoin2money_text}</span>
                                    </li>
                                    <!-- END: wcoin_box_show -->
                                    <!-- BEGIN: bo -->
                                    <li class="justify-content-between">
                                        <span class="k">{LANG.product.transport_fee}:</span>
                                        <span class="v">0 đ</span>
                                    </li>
                                    <!-- END: bo -->
                                    <li class="sep bg-color"></li>
                                    <li class="total d-flex justify-content-between">
                                        <span class="k">{LANG.product.total}:</span>
                                        <span class="v total-payment bg-text-color">{data.cart_payment}</span>
                                    </li>
                                    <!-- BEGIN: vat -->
                                    <li class="d-flex justify-content-between">
                                        <span class="k"></span>
                                        <span class="v">{data.text_vat}</span>
                                    </li>
                                    <!-- END: vat -->
                                    <li><span class="wcoin_expected" data-percent="{data.percentforwcoin}" data-money="{data.money_to_wcoin}">({LANG.product.wcoin_expected}<b> {data.wcoin_expected}</b>)</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    {data.order_discount}
                    {data.promotional_box}
                    {data.wcoin_box}
                    <div class="mess_payment">{data.mess}</div>
                    <button type="button" class="btn btn_payment bg-color border-color text-color" {data.attr_btn} onclick="imsOrdering.updateCart('form_cart', '', '{data.link_continue}');">{LANG.product.btn_payment_ok}</button>
                </div>
            </div>
        </div>
        <script language="javascript">
            imsOrdering.cartRemoveItem();
            imsOrdering.cartremovePromotionCode();
            imsOrdering.cartSaveLater('form_cart');
        </script>
    </div>

<div id="popup-select" style="display: none;">
    <div class="box-content"></div>
</div>
<script>
    var t = 0;
    $(document).on("click", ".col_gift_include .select button.btn", function(){
        var combo_id = $(this).data("combo"),
            type = $(this).data("type");
        loading('show');
        $.ajax({
            type: "POST",
            url: ROOT + "ajax.php",
            data: {"m": "product", "f": "load_"+type+"_combo", "combo_id": combo_id}
        }).done(function (string) {
            var data = JSON.parse(string);
            $("#popup-select .box-content").html(data.html);
            $.fancybox.open({
                src  : '#popup-select',
                type : 'inline',
                clickSlide : 'false',
                clickOutside : 'false',
                "touch" : false ,
                beforeClose : function(){
                    t = 0;
                    $("#ac").remove();
                },
            });
            loading('hide');
        });
    });

    $(document).on("change", "#popup-select input", function(){
        var m = parseInt($("#popup-select .check").attr("data-max"));
        if($(this).is(":checked")){
            t++;
        }else{
            t--;
        }
        $("#popup-select .check >span").text(t);
        if(t >= m){
            $("#popup-select input:checkbox:not(:checked)").attr("disabled","");
        }else{
            $("#popup-select input").removeAttr("disabled");
        }
    });
    $(document).on("click", "#popup-select .btn-confirm", function(){
        var combo_id = $(this).data("combo"),
            type = $(this).data("type"),
            selected = [];
        $("#popup-select").find("input:checked").each(function (i, ob) {
            selected.push($(ob).val());
        });
        loading('show');
        $.ajax({
            type: "POST",
            url: ROOT + "ajax.php",
            data: {"m": "product", "f": "update_cart_combo", "data": selected, "combo_id": combo_id, "type":type}
        }).done(function (string) {
            var data = JSON.parse(string);
            if(data.ok == 1){
                $.fancybox.close();
                if($('#combo'+combo_id+' ul.list_none').length){
                    $('#combo'+combo_id+' ul.list_none').remove();
                }
                $('#combo'+combo_id).append(data.html);
                $('#combo'+combo_id+' button.btn').text(data.text);
                imsOrdering.cartUpdateHtml('form_cart');
            }else{
                Swal.fire({
                    icon: 'error',
                    title: lang_js['aleft_title'],
                    text: data.mess,
                });
            }
            loading('hide');
        });
    });

    $(document).on("click", ".col_gift_include button.delete", function(){
        var item_id = $(this).data("item"),
            type = $(this).data('type');
        var id = $(this).parent().parent().attr('id');
        loading('show');

        $.ajax({
            type: "POST",
            url: ROOT + "ajax.php",
            data: {"m": "product", "f": "delete_gift_include", "item_id": item_id, "type":type}
        }).done(function (string) {
            var data = JSON.parse(string);
            if(data.ok == 1){
                $('#'+id+' >ul.list_none').remove();
                $('#'+id+' >.select button.btn').text(data.text);
                imsOrdering.cartUpdateHtml('form_cart');
            }else{
                Swal.fire({
                    icon: 'error',
                    title: lang_js['aleft_title'],
                    text: data.mess,
                });
            }
            loading('hide');
        });

    }) 
</script>
<!-- END: table_cart --> 

<!-- BEGIN: promotional_box -->
    <div class="order-card">
        <div class="card-content">
            <div class="card-header-top">
                <div class="text d-flex justify-content-between align-items-center">
                    {LANG.product.promotional_code}<a class="collapse"><i class='fal fa-angle-down'></i></a>
                </div>
            </div>
            <div class="card-content-center">
                <a href="#list_promotion_valid" class="text-primary" data-fancybox>Chọn mã khuyến mãi</a>
                <div class="box_code-note pb-2">{LANG.product.promotional_code_note}</div>
                <!-- BEGIN: bo -->
                <div class="promotion_mess">{data.err_promotion}</div>
                <!-- END: bo -->
                <form id="promotion_code" name="promotion_code" method="post" action="" >
                    <div class="form_mess">{data.err_promotion}</div>
                    <div class="input-group">
                        <input type="text" class="form-control" name="promotional_code" placeholder="{LANG.product.promotional_code_label}">
                        <div class="input-group-append"><button type="submit" class="btn btn-submit" {data.attr_btn}>{LANG.product.btn_use_code}</button></div>
                    </div>
                </form>
                <div class="promotional-note">{data.promotion_note}</div>
            </div>
        </div>
    </div>
    <div id="list_promotion_valid" class="promotion_code" style="display: none;">
        <div class="list_item">
            <label class="title">{LANG.product.valid_promotion_code}</label>
            <!-- BEGIN:row_item -->
            <div class="item mb-3">
                <div class="wrap_item d-flex w-100 align-items-center">
                    <div class="left flex-grow-1 pr-2" style="cursor: pointer;" data-fancybox data-src="#{row.promotion_code}">
                        <div class="short_code">{row.short}</div>
                        <div class="date_end">{LANG.product.hsd}: {row.date_end}</div>
                    </div>
                    <div class="apply"><button data-item="{row.promotion_id}" class="btn btn-primary btn-sm">{LANG.product.apply}</button></div>
                </div>
            </div>
            <div id="{row.promotion_code}" style="display: none; width: 100%; max-width: 300px;">
                <div class="promotion_info" style="">
                    <div class="text-center"><label class="text-danger" style="font-size: 18px;"><b>{row.title}</b></label></div>
                    <div class="promotion_id" style="padding: 10px 5px;"><span style="display: inline-block; width: 90px;">Mã:</span>{row.promotion_id}</div>
                    <div class="date_end" style="background: #f1f1f1; padding: 10px 5px;"><span style="display: inline-block; width: 90px;">{LANG.product.hsd}:</span> {row.date_end}</div>
                    <div class="short_code" style="padding: 10px 5px;">
                        <p class="pb-3">Thông tin</p>
                        <div>{row.short}</div>
                    </div>
                </div>
            </div>
            <!-- END:row_item -->
        </div>
    </div>
<script>
    imsOrdering.promotionCode('promotion_code');
    $(document).on("click","#list_promotion_valid .apply",function(){
        var code = $(this).find("button").attr("data-item");
        $("input[name='promotional_code']").val(code);
        $(".fancybox-close-small").click();
    });

    if(localStorage.getItem("saved_code")){
        var promotion_save = localStorage.getItem("saved_code");
        $.ajax({
            type: "POST",
            url: ROOT+"ajax.php",
            data: { "m" : "product", "f" : "loadPromotionCode", "data" : JSON.parse(promotion_save)}
        }).done(function( string ) {
            var data = JSON.parse(string);
            // console.log(data);
            if(data.ok == 1) {
                $("#list_promotion_valid").prepend(data.html);
            }
        });
    }
</script>
<!-- END: promotional_box -->

<!-- BEGIN: wcoin_box -->
    <div class="order-card">
        <div class="card-content">
            <div class="card-header-top">
                <div class="text d-flex justify-content-between align-items-center">
                    {LANG.product.save_wcoin}<a class="collapse"><i class='fal fa-angle-down'></i></a>
                </div>
            </div>
            <div class="card-content-center">
                <!-- BEGIN: form_wcoin -->
                <div class="box_code-note">{LANG.product.wcoin_note}</div>
                <div class="show_wcoin">
                    <p>{LANG.product.wcoin_have} <span><b class="number_wcoin">{data.wcoin}</b> wcoin.</span></p>
                    <p>{LANG.product.wcoin_proportion} <span><b class="wcoin2money"> {data.wcoin_to_money}</b></span></p>
                    <p class="show_change_wcoin"></p>
                </div>
                <form id="form_wcoin" name="form_wcoin" method="post" action="" >
                    <div class="form_mess"></div>
                    <div class="input-group">
                        <input name="wcoin" type="number" class="form-control" min="0" placeholder="{LANG.product.wcoin_code_label}" />
                        <div class="input-group-append"><button type="submit" class="btn btn-submit" {data.attr_btn}>{LANG.product.btn_use_code}</button></div>
                    </div>
                </form>
                <!-- END: form_wcoin -->
                <!-- BEGIN: link_login -->
                    <a href="{data.link_login}">{LANG.product.please_login}</a>
                <!-- END: link_login -->
            </div>
        </div>
    </div>
    <script type="text/javascript"> imsOrdering.useWcoin('form_wcoin'); </script>
<!-- END: wcoin_box -->

<!-- BEGIN: combo_gift_include -->
<li class="col_gift_include" id="combo{data.combo_id}">
    <div class="select"><button class="btn" data-combo="{data.combo_id}" data-type="{data.type}">{data.select}</button></div>
    <!-- BEGIN: ul -->
    <ul class="list_none">
        <button class="delete" data-item="{row.item_id}" data-type="{row.type}">{LANG.product.delete}</button>
        <!-- BEGIN: gift -->
        <li class="col_gift">
            <div class="img"><img src="{gift.picture}" alt="{gift.title}"/></div>
            <div class="info"><a {gift.link}><span class="type">{LANG.product.gift}</span><span class="title">{gift.title}</span></a></div>
        </li>
        <!-- END: gift -->
        <!-- BEGIN: include -->
        <li class="col_gift include">
            <div class="img"><img src="{incl.picture}" alt="{incl.title}"/></div>
            <div class="info">
                <a href="{incl.link}">
                    <span class="type">{LANG.product.include}</span><span class="title">{incl.title}</span>
                </a>
                <div class="info-price">
                    <div class="price_buy" data-value="{incl.price_buy}">{incl.price_buy_text}</div>
                    <div class="price {incl.class_price}">{incl.price}</div>
                </div>
            </div>
        </li>
        <!-- END: include -->
    </ul>
    <!-- END: ul -->
</li>
<!-- END: combo_gift_include -->

<!-- BEGIN: order_discount -->
<div id="order_discount" class="order-card d-none">
    <!-- BEGIN: content_order_discount -->
    <div class="card-content">
        <div class="card-header-top">
            <div class="text d-flex justify-content-between align-items-center">
                {LANG.product.order_discount_program}<a class="collapse"><i class="fal fa-angle-down"></i></a>
            </div>
        </div>
        <div class="card-content-center">
            <div class="form_mess">{row.mess}</div>
            <div class="title text-primary mb-2 text-uppercase">{row.title}</div>
            <div class="apply"><button data-item="{row.promotion_id}" class="btn btn-primary btn-sm" {row.disabled}>{LANG.product.apply}</button></div>
        </div>
    </div>
    <!-- END: content_order_discount -->
</div>
<script>
    imsOrdering.load_order_discount();
    $(document).on('click', '#order_discount .apply button', function () {
        var vl = $(this).data('item');
        if(vl != ''){
            $('input[name="promotional_code"]').val(vl);
            $('#promotion_code').submit();
        }
    });
</script>
<!-- END: order_discount -->

<!-- BEGIN: bundled_product -->
<div id="order_bundled_product" class="d-none">
    <!-- BEGIN: content_bundled_product -->
    <div class="event_title">{LANG.product.order_bundled_event}</div>
    <div class="form_mess">{data.mess}</div>
    <!-- BEGIN: list_item_chose -->
    <div class="list_item_chose" data-endow_price="{endow_price}">
        <div class="delete"><button class="btn-delete">{LANG.product.delete}</button></div>
        <!-- BEGIN: item -->
        <div class="item">
            <div class="picture">
                <img src="{row.picture}" alt="{row.title}">
            </div>
            <div class="info">
                <div class="title"><a href="{row.link}">{row.title}</a></div>
                <div class="price">
                    {row.price_buy}
                    <div class="endow_price">{row.endow_price}</div>
                </div>
            </div>
        </div>
        <!-- END: item -->
    </div>
    <!-- END: list_item_chose -->
    <div class="apply"><button class="btn btn-primary btn-sm" {data.disabled}>{data.btn_apply}</button></div>
    <!-- END: content_bundled_product -->
</div>
<div id="popup-select-bundled" style="display: none;">
    <div class="box-content"></div>
</div>
<script>
    imsOrdering.load_bundled_product();
    var t = 0;
    $(document).on('click', '#order_bundled_product .apply button', function () {
        // loading('show');
        $.ajax({
            type: "POST",
            url: ROOT + "ajax.php",
            data: {"m": "product", "f": "load_bundled_select"}
        }).done(function (string) {
            var data = JSON.parse(string);
            $("#popup-select-bundled .box-content").html(data.html);
            $.fancybox.open({
                src  : '#popup-select-bundled',
                type : 'inline',
                clickSlide : 'false',
                clickOutside : 'false',
                "touch" : false ,
                beforeClose : function(){
                    t = 0;
                },
            });
            loading('hide');
        });
    });

    $(document).on("change", "#popup-select-bundled input", function(){
        // var m = parseInt($("#popup-select-bundled .check").attr("data-max"));
        var m = 1;
        if($(this).is(":checked")){
            t++;
        }else{
            t--;
        }
        $("#popup-select-bundled .check >span").text(t);
        if(t >= m){
            $("#popup-select-bundled input:checkbox:not(:checked)").attr("disabled","");
        }else{
            $("#popup-select-bundled input").removeAttr("disabled");
        }
    });

    $(document).on("click", "#popup-select-bundled .btn-confirm", function(){
        var selected = [];
        $("#popup-select-bundled").find("input:checked").each(function (i, ob) {
            selected.push($(ob).val());
        });
        // loading('show');
        $.ajax({
            type: "POST",
            url: ROOT + "ajax.php",
            data: {"m": "product", "f": "update_cart_bundled", "data": selected}
        }).done(function (string) {
            var data = JSON.parse(string);
            if(data.ok == 1){
                $.fancybox.close();
                setTimeout(function () {
                    imsOrdering.load_bundled_product();
                }, 800);
                setTimeout(function () {
                    imsOrdering.cartUpdateHtml('form_cart');
                }, 1500);
            }else{
                Swal.fire({
                    icon: 'error',
                    title: lang_js['aleft_title'],
                    text: data.mess,
                });
            }
            loading('hide');
        });
    });

    $(document).on("click", "#order_bundled_product .list_item_chose button.btn-delete", function(){
        // loading('show');

        $.ajax({
            type: "POST",
            url: ROOT + "ajax.php",
            data: {"m": "product", "f": "delete_bundled_product"}
        }).done(function (string) {
            var data = JSON.parse(string);
            if(data.ok == 1){
                setTimeout(function () {
                    imsOrdering.load_bundled_product();
                }, 800);
                setTimeout(function () {
                    imsOrdering.cartUpdateHtml('form_cart');
                }, 1500);
            }else{
                Swal.fire({
                    icon: 'error',
                    title: lang_js['aleft_title'],
                    text: data.mess,
                });
            }
            loading('hide');
        });

    });
</script>
<!-- END: bundled_product -->

<!-- BEGIN: list_bundled_product -->
<div class="list_item list_bundled_product">
    <h4>{LANG.product.bundled_product}</h4>
    <div class="note">{note}</div>
    <div class="content">
        <!-- BEGIN: row -->
        <div class="item {row.disabled}">
            <div class="checkbox">
                {row.input}
                <label for="bd_{row.item_id}"></label>
            </div>
            <div class="img">
                <img src="{row.picture}" alt="{row.title}">
            </div>
            <div class="info">
                <div class="title">{row.title}</div>
                <div class="info-price">
                    <div class="price">{row.price_buy}</div>
                    <div class="price_buy">{row.endow_price}</div>
                </div>
            </div>
        </div>
        <!-- END: row -->
    </div>
    <div class="confirm">
        <!-- BEGIN: bo -->
        <div class="check" data-max="{data.num_chose}">
            <!-- BEGIN: chose -->
            {LANG.product.list_chose_num} <span>0</span>/{data.num_chose}
            <!-- END: chose -->
        </div>
        <!-- END: bo -->
        <button class="btn btn-confirm">{LANG.product.btn_submit}</button>
    </div>
</div>
<!-- END: list_bundled_product -->