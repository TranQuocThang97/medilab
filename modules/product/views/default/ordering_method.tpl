<!-- BEGIN: main -->
    {data.content}
<!-- END: main -->

<!-- BEGIN: ordering_method -->
    <div class="bs-wizard bg-text-color">
        <div class="bs-wizard-step">                
            <span class="bs-wizard-dot bg-color active"><i class="fas fa-cart-plus"></i></span>
            <div class="text-center bs-wizard-stepnum">
                <span class="hidden-xs">{LANG.product.cart}</span>
            </div>
        </div>
        <div class="progress active"><div class="progress-bar"></div></div>
        <div class="bs-wizard-step">                
            <span class="bs-wizard-dot bg-color active"><i class="far fa-dollar-sign"></i></span>
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
    <div class="order-box-content d-flex flex-wrap">          
        <div class="order-box-left">
            {data.box_address}
            <form id="form_ordering_address" name="form_ordering_address" method="post" action="{data.link_action}" >
                <input type="hidden" name="address_book" id="address_book" value="{data.default}" readonly="">
                {data.box_address_form}
                {data.content}
            </form>
        </div>
        <div class="order-box-right">
            {data.box_column}
        </div>
    </div>
    <script language="javascript">
        imsOrdering.createOrder('form_ordering_address');
        function hide_content(e) {
            $('.' + e + ' .card-content .card-item .card-item-title input').each(function () {
                var c = $(this).prop('checked');
                content = $(this).parent().parent().find('.card-item-content');
                if (c) {
                    content.stop(true, true).slideDown(200);
                } else {
                    content.stop(true, true).slideUp(200);
                }
            })
        }
        hide_content('list-shipping');
        hide_content('list-method');
        $('.list-shipping').click(function () {
            hide_content('list-shipping');
        });
        $('.list-method').click(function () {
            hide_content('list-method');
        });
        $(document).on("click", '.btn_pay', function(){
            $('#form_ordering_address').submit();
        })        
    </script>
<!-- END: ordering_method -->

<!-- BEGIN: box_address -->
    <div class="order-card list-address">    
        <div class="card-content">
            <div class="card-title">{LANG.product.order_address}</div>
            <div class="row">
                <!-- BEGIN: item -->
                <div class="col-lg-6 col-md-6 col-12">
                    <div class="item address {item.class}" data-address="{item.address}" data-province="{item.province}" data-district="{item.district}" data-ward="{item.ward}" data-value="{item.id_value}">
                        <div class="pb-1 pl-1"><b>{item.full_name}</b> <i class="far fa-pencil popupAddessBook" data-value="{item.id}"></i></div>
                        <div class="item-sub"><i class="fal fa-envelope"></i> {item.email}</div>
                        <div class="item-sub"><i class="fal fa-mobile"></i> {item.phone}</div>
                        <div class="item-sub text-ellipsis" title="{item.full_addess}"><i class="fal fa-map-marker-alt"></i> {item.full_addess}</div>
                        <!-- BEGIN: default -->
                        <div class="bg-df"></div>
                        <div class="check-df">✓</div>
                        <!-- END: default -->
                    </div>
                </div>
                <!-- END: item -->
                <div class="col-lg-6 col-md-6 col-12">
                    <a href="#form_add_address" data-fancybox data-options='{"clickSlide":false, "touch":false, "btnTpl":{"smallBtn":""}}'>
                    <div class="item add d-flex align-items-center justify-content-center">
                        <div class="text-center">
                            <div><i class="fal fa-plus fa-2x"></i></div>
                            <div>{LANG.product.add_address}</div>
                        </div>
                    </div>
                    </a>
                </div>
            </div>
            <!-- BEGIN: more -->
            <a class="text-center d-block pb-3" style="cursor: pointer;color: #74c2f4" href="javascript:void(0);" id="full-address">
                <i class="far fa-angle-double-down"></i><div>{LANG.global.seemore}</div>
            </a>
            <script type="text/javascript">
                var ajax_send = false;
                $(document).on('click', '#full-address', function(){
                    if (ajax_send == true){
                        return false;
                    }
                    ajax_send = true;
                    $header = $(this);
                    $content = $(this).next();
                    $content.slideToggle(500, function () {
                        if( $content.is(":visible") ) {
                            $header.html("<i class='far fa-angle-double-up'></i><div>"+lang_js['seemore']+"</div>");
                            ajax_send = false;
                        }
                        else{
                            $header.html("<i class='far fa-angle-double-down'></i><div>"+lang_js['seemore'] +"</div>"); 
                            ajax_send = false;
                        }
                    });
                });
            </script>
            <div class="row" style="display: none;">
                <!-- BEGIN: item_more -->
                <div class="col-lg-6 col-md-6 col-12">
                    <div class="item address {item.class}" data-address="{item.address}" data-province="{item.province}" data-district="{item.district}" data-ward="{item.ward}" data-value="{item.id_value}">
                        <div class="pb-1 pl-1"><b>{item.full_name}</b> <i class="far fa-pencil popupAddessBook" data-value="{item.id}"></i></div>
                        <div class="item-sub"><i class="fal fa-envelope"></i> {item.email}</div>
                        <div class="item-sub"><i class="fal fa-mobile"></i> {item.phone}</div>
                        <div class="item-sub text-ellipsis" title="{item.full_addess}"><i class="fal fa-map-marker-alt"></i> {item.full_addess}</div>
                        <!-- BEGIN: default -->
                        <div class="bg-df"></div>
                        <div class="check-df">✓</div>
                        <!-- END: default -->
                    </div>
                </div>
                <!-- END: item_more -->
            </div>
            <!-- END: more -->
        </div>
        <form id="form_add_address" class="form_address_book" method="post" style="display: none;">
            <div class="title">{LANG.product.order_address_new}</div>
            <div class="row">
                <div class="col-12">
                    <div class="form_mess"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="form-group">
                        <label class="title">{LANG.product.full_name} <span class="required">*</span></label>
                        <input placeholder="{LANG.product.text_full_name}" name="full_name" type="text" maxlength="100" class="form-control"/>
                    </div>
                    <div class="form-group">
                        <label class="title">{LANG.product.phone} <span class="required">*</span></label>
                        <input placeholder="{LANG.product.text_phone}" name="phone" type="text" maxlength="100" class="form-control"/>
                    </div>
                    <div class="form-group">
                        <label class="title">{LANG.product.email}</label>
                        <input placeholder="{LANG.product.text_email}" name="email" type="text" maxlength="100" class="form-control"/>
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="form-group">
                        <label class="title">{LANG.product.province} <span class="required">*</span></label>
                        {data.list_location_province}
                    </div>
                    <div class="form-group">
                        <label class="title">{LANG.product.district} <span class="required">*</span></label>
                        {data.list_location_district}
                    </div>
                    <div class="form-group">
                        <label class="title">{LANG.product.ward} <span class="required">*</span></label>
                        {data.list_location_ward}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label class="title">{LANG.product.address} <span class="required">*</span></label>
                        <input placeholder="{LANG.product.text_address}" name="address" type="text" maxlength="100" class="form-control" value="" required/>
                    </div>
                    <div class="form-group row_c">
                        <div class="row-title row-checkbox">
                            <input type="checkbox" name="is_default" class="toggle_panel" id="is_default" value="1">
                            <label for="is_default"><span>{LANG.user.default_address}</span></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-content">
                            <input type="hidden" name="submit" value="1">
                            <input type="button" onclick="$.fancybox.close()" class="btn mr-2" value="{LANG.product.cancel}"/>
                            <button type="submit" id="btn_confirm_address" class="btn bg-color text-color btn_custom">{LANG.product.confirm}</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <script async="async">
        imsLocation.locationChange("province", ".select_location_province");
        imsLocation.locationChange("district", ".select_location_district");
        imsLocation.locationChanges("province", ".select_location_provinces");
        imsLocation.locationChanges("district", ".select_location_districts");

        imsUser.addAddessBook("form_add_address");
        imsUser.popupAddessBook();
        imsOrdering.shippingFee();
        $(document).on("change", '.list-shipping input[name=shipping]', function (e) {
            imsOrdering.shippingFee();
        }); 
        $(document).on("click" , ".order-card.list-address .item.address", function(){
            var id = $(this).data("value");
            $("#address_book").val(id);
            $(".order-card.list-address .item").removeClass("check");
            $(".order-card.list-address .item").find(".bg-df").remove();
            $(".order-card.list-address .item").find(".check-df").remove();
            $(this).addClass("check");
            $(this).append('<div class="bg-df"></div>');
            $(this).append('<div class="check-df">✓</div>');
            imsOrdering.shippingFee();
        });
    </script>
<!-- END: box_address -->

<!-- BEGIN: box_address_form -->
<div class="order-card list_form_address">
    <div class="card-content">
        <div class="card-title">{LANG.product.order_address}</div>
        <div class="row">
            <div class="col-md-6 col-12">
                <div class="form-group">
                    <label class="title">{LANG.product.full_name} <span class="required">*</span></label>
                    <input placeholder="{LANG.product.text_full_name}" name="full_name" type="text" maxlength="100" class="form-control" required/>
                </div>
                <div class="form-group">
                    <label class="title">{LANG.product.phone} <span class="required">*</span></label>
                    <input placeholder="{LANG.product.text_phone}" name="phone" type="text" maxlength="100" class="form-control" required/>
                </div>
                <div class="form-group">
                    <label class="title">{LANG.product.email}</label>
                    <input placeholder="{LANG.product.text_email}" name="email" type="text" maxlength="100" class="form-control" />
                    <div style="color: blue; line-height: 17px;padding-top: 5px;">{LANG.product.note_input_email}</div>
                </div>
            </div>
            <div class="col-md-6 col-12">
                <div class="form-group">
                    <label class="title">{LANG.product.province} <span class="required">*</span></label>
                    {data.list_location_province}
                </div>
                <div class="form-group">
                    <label class="title">{LANG.product.district} <span class="required">*</span></label>
                    {data.list_location_district}
                </div>
                <div class="form-group">
                    <label class="title">{LANG.product.ward} <span class="required">*</span></label>
                    {data.list_location_ward}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label class="title">{LANG.product.address} <span class="required">*</span></label>
                    <input placeholder="{LANG.product.text_address}" name="address" id="address" type="text" maxlength="100" class="form-control" value="" required/>
                </div>
            </div>
        </div>
    </div>
</div>
<script async="async">
    imsLocation.locationChange("province", ".select_location_province");
    imsLocation.locationChange("district", ".select_location_district");

    $(document).on("change", '#ward', function (e) {
        imsOrdering.shippingFee();
    });
    var timeoutAddress = null;    
    $(document).on("change keyup paste", '#address', function(){
        clearTimeout(timeoutAddress);
        timeoutAddress = setTimeout(function () {
            imsOrdering.shippingFee();
        }, 1000);
    })
</script>
<!-- END: box_address_form -->

<!-- BEGIN: box_shipping -->
    <div class="order-card list-shipping">
        <div class="card-content">
            <div class="card-title">{LANG.product.choose_ordering_shipping}</div>
            <!-- BEGIN: row -->
            <div class="card-item">
                <div class="card-item-title">
                    <input type="radio" id="shipping_{row.shipping_id}" name="shipping" value="{row.shipping_id}" {row.shipping_checked} required />
                    <label for="shipping_{row.shipping_id}" class="d-flex align-items-center">{row.picture} {row.title}</label>
                </div>
                <div class="card-item-content">
                    {row.content}
                    {row.content_choose}
                </div>
            </div>
            <!-- END: row --> 
        </div>
    </div>
<script>
    $(document).on('change', 'input[name=shipping]', function () {
        imsOrdering.shippingFee();
    })
</script>
<!-- END: box_shipping -->

<!-- BEGIN: box_payment -->
    <div class="order-card list-method">
        <div class="card-content">
            <div class="card-title">{LANG.product.choose_ordering_method}</div>
            <!-- BEGIN: row -->
            <div class="card-item">
                <div class="card-item-title">
                    <input type="radio" id="method_{row.method_id}" name="method" value="{row.method_id}" {row.method_checked} />
                    <label for="method_{row.method_id}" class="d-flex align-items-center">{row.picture} {row.title}</label>
                </div>
                <div class="card-item-content">
                    {row.content}
                    <!-- BEGIN: nganluong -->
                        <div class="boxContent">         
                            <ul class="cardList list_none clearfix">
                                <li class="bank-online-methods ">
                                    <label for="vcb_ck_on"> 
                                        <i class="BIDV" title="Ngân hàng TMCP Đầu tư &amp; Phát triển Việt Nam"></i> 
                                        <input name="bankcode" type="radio" value="BIDV" /> 
                                    </label>
                                </li>
                                <li class="bank-online-methods ">
                                    <label for="vcb_ck_on"> 
                                        <i class="VCB" title="Ngân hàng TMCP Ngoại Thương Việt Nam"></i> 
                                        <input name="bankcode" type="radio" value="VCB" /> 
                                    </label>
                                </li>
                                <li class="bank-online-methods ">
                                    <label for="vnbc_ck_on"> 
                                        <i class="DAB" title="Ngân hàng Đông Á"></i> 
                                        <input name="bankcode" type="radio" value="DAB" /> 
                                    </label>
                                </li>
                                <li class="bank-online-methods ">
                                    <label for="tcb_ck_on">
                                        <i class="TCB" title="Ngân hàng Kỹ Thương"></i>
                                        <input name="bankcode" type="radio" value="TCB" />
                                    </label>
                                </li>
                                <li class="bank-online-methods ">
                                    <label for="sml_atm_mb_ck_on">
                                        <i class="MB" title="Ngân hàng Quân Đội"></i>
                                        <input name="bankcode" type="radio" value="MB" />
                                    </label>
                                </li>
                                <li class="bank-online-methods ">
                                    <label for="sml_atm_vib_ck_on">
                                        <i class="VIB" title="Ngân hàng Quốc tế"></i>
                                        <input name="bankcode" type="radio" value="VIB" />
                                    </label>
                                </li>
                                <li class="bank-online-methods ">
                                    <label for="sml_atm_vtb_ck_on">
                                        <i class="ICB" title="Ngân hàng Công Thương Việt Nam"></i>
                                        <input name="bankcode" type="radio" value="ICB" />
                                    </label>
                                </li>
                                <li class="bank-online-methods ">
                                    <label for="sml_atm_exb_ck_on">
                                        <i class="EXB" title="Ngân hàng Xuất Nhập Khẩu"></i>
                                        <input name="bankcode" type="radio" value="EXB" />
                                    </label>
                                </li>
                                <li class="bank-online-methods ">
                                    <label for="sml_atm_acb_ck_on">
                                        <i class="ACB" title="Ngân hàng Á Châu"></i>
                                        <input name="bankcode" type="radio" value="ACB" />
                                    </label>
                                </li>
                                <li class="bank-online-methods ">
                                    <label for="sml_atm_hdb_ck_on">
                                        <i class="HDB" title="Ngân hàng Phát triển Nhà TPHCM"></i>
                                        <input name="bankcode" type="radio" value="HDB" />
                                    </label>
                                </li>
                                <li class="bank-online-methods ">
                                    <label for="sml_atm_msb_ck_on">
                                        <i class="MSB" title="Ngân hàng Hàng Hải"></i>
                                        <input name="bankcode" type="radio" value="MSB" />
                                    </label>
                                </li>
                                <li class="bank-online-methods ">
                                    <label for="sml_atm_nvb_ck_on">
                                        <i class="NVB" title="Ngân hàng Nam Việt"></i>
                                        <input name="bankcode" type="radio" value="NVB" />
                                    </label>
                                </li>
                                <li class="bank-online-methods ">
                                    <label for="sml_atm_vab_ck_on">
                                        <i class="VAB" title="Ngân hàng Việt Á"></i>
                                        <input name="bankcode" type="radio" value="VAB" />
                                    </label>
                                </li>
                                <li class="bank-online-methods ">
                                    <label for="sml_atm_vpb_ck_on">
                                        <i class="VPB" title="Ngân Hàng Việt Nam Thịnh Vượng"></i>
                                        <input name="bankcode" type="radio" value="VPB" />
                                    </label>
                                </li>
                                <li class="bank-online-methods ">
                                    <label for="sml_atm_scb_ck_on">
                                        <i class="SCB" title="Ngân hàng Sài Gòn Thương tín"></i>
                                        <input name="bankcode" type="radio" value="SCB" />
                                    </label>
                                </li>
                                <li class="bank-online-methods ">
                                    <label for="bnt_atm_pgb_ck_on">
                                        <i class="PGB" title="Ngân hàng Xăng dầu Petrolimex"></i>
                                        <input name="bankcode" type="radio" value="PGB" />
                                    </label>
                                </li>
                                <li class="bank-online-methods ">
                                    <label for="bnt_atm_gpb_ck_on">
                                        <i class="GPB" title="Ngân hàng TMCP Dầu khí Toàn Cầu"></i>
                                        <input name="bankcode" type="radio" value="GPB" />
                                    </label>
                                </li>
                                <li class="bank-online-methods ">
                                    <label for="bnt_atm_agb_ck_on">
                                        <i class="AGB" title="Ngân hàng Nông nghiệp &amp; Phát triển nông thôn"></i>
                                        <input name="bankcode" type="radio" value="AGB" />
                                    </label>
                                </li>
                                <li class="bank-online-methods ">
                                    <label for="bnt_atm_sgb_ck_on">
                                        <i class="SGB" title="Ngân hàng Sài Gòn Công Thương"></i>
                                        <input name="bankcode" type="radio" value="SGB" />
                                    </label>
                                </li>
                                <li class="bank-online-methods ">
                                    <label for="sml_atm_bab_ck_on">
                                        <i class="BAB" title="Ngân hàng Bắc Á"></i>
                                        <input name="bankcode" type="radio" value="BAB" />
                                    </label>
                                </li>
                                <li class="bank-online-methods ">
                                    <label for="sml_atm_bab_ck_on">
                                        <i class="TPB" title="Tiền phong bank"></i>
                                        <input name="bankcode" type="radio" value="TPB" />
                                    </label>
                                </li>
                                <li class="bank-online-methods ">
                                    <label for="sml_atm_bab_ck_on">
                                        <i class="NAB" title="Ngân hàng Nam Á"></i>
                                        <input name="bankcode" type="radio" value="NAB" />
                                    </label>
                                </li>
                                <li class="bank-online-methods ">
                                    <label for="sml_atm_bab_ck_on">
                                        <i class="SHB" title="Ngân hàng TMCP Sài Gòn - Hà Nội (SHB)"></i>
                                        <input name="bankcode" type="radio" value="SHB" />
                                    </label>
                                </li>
                                <li class="bank-online-methods ">
                                    <label for="sml_atm_bab_ck_on">
                                        <i class="OJB" title="Ngân hàng TMCP Đại Dương (OceanBank)"></i>
                                        <input name="bankcode" type="radio" value="OJB" />
                                    </label>
                                </li>
                            </ul>
                            <div class="clear"></div>
                        </div>
                    <!-- END: nganluong -->
                    <!-- BEGIN: vnpay -->
                        <select name="bankcode" id="bankcode" class="form-control">
                            <option value="">Vui lòng chọn</option>
                            <option value="NCB"> Ngân hàng NCB</option>
                            <option value="AGRIBANK"> Ngân hàng Agribank</option>
                            <option value="SCB"> Ngân hàng SCB</option>
                            <option value="SACOMBANK">Ngân hàng SacomBank</option>
                            <option value="EXIMBANK"> Ngân hàng EximBank</option>
                            <option value="MSBANK"> Ngân hàng MSBANK</option>
                            <option value="NAMABANK"> Ngân hàng NamABank</option>
                            <option value="VNMART"> Vi dien tu VnMart</option>
                            <option value="VIETINBANK">Ngân hàng Vietinbank</option>
                            <option value="VIETCOMBANK"> Ngân hàng VCB</option>
                            <option value="HDBANK">Ngân hàng HDBank</option>
                            <option value="DONGABANK"> Ngân hàng Dong A</option>
                            <option value="TPBANK"> Ngân hàng TPBank</option>
                            <option value="OJB"> Ngân hàng OceanBank</option>
                            <option value="BIDV"> Ngân hàng BIDV</option>
                            <option value="TECHCOMBANK"> Ngân hàng Techcombank</option>
                            <option value="VPBANK"> Ngân hàng VPBank</option>
                            <option value="MBBANK"> Ngân hàng MBBank</option>
                            <option value="ACB"> Ngân hàng ACB</option>
                            <option value="OCB"> Ngân hàng OCB</option>
                            <option value="IVB"> Ngân hàng IVB</option>
                            <option value="VISA"> Thanh toan qua VISA/MASTER</option>
                        </select>
                    <!-- END: vnpay -->
                </div>
            </div>
            <!-- END: row --> 
        </div>
    </div>
<!-- END: box_payment -->


<!-- BEGIN: box_cart -->
    <div class="order-card">
        <div class="card-content">
            <div class="card-title">{LANG.product.order_info}
                <a href="{data.link_cart_edit}" class="card-btn-right">{LANG.product.edit}</a>
            </div>
            <!-- BEGIN: row_item -->
            <div class="card-item pb-1 d-flex flex-wrap {row.class}">
                <div class="card-picture">
                    <img src="{row.picture_thumb}" alt="{row.title}">
                </div>
                <div class="card-info">
                    <div class="title"><a href="{row.link}" target="_blank">{row.title}</a></div>
                    <!-- BEGIN: option -->
                    <div class="code_pro" style="color: #999;">
                        {row.name}: {row.value}
                    </div>
                    <!-- END: option -->
                    <div class="quantity">{LANG.product.quantity}: {row.quantity}</div>
                    <div class="price bg-text-color" style="font-size: 15px;">{row.price_buy_text}</div>
                    <!-- BEGIN: discount -->
                    <div class="discount" style="color: #777; font-size: 13px;">{row.price_text} <span class="pl-1 pr-1">|</span> -{row.percent_discount}%</div>
                    <!-- END: discount -->
                    <!-- BEGIN: bo -->
                    <div>{row.gift} <em>{row.item_related_title}</em></div>
                    <!-- END: bo -->
                </div>
                {row.combo_info}
            </div>
            <!-- END: row_item -->
            <!-- BEGIN: row_empty -->
               <div class="col col_empty" colspan="7">{row.mess}</div>
            <!-- END: row_empty --> 
        </div>
        {data.bundled_product}
    </div>
    <div class="order-card">
        <div class="card-content">
        	<div class="cart-temp">
	            <div class="d-flex justify-content-between mb-2">
	            	{LANG.product.provisional}:<span class="temp-total-money">{data.cart_total}</span>
				</div>
                <!-- BEGIN: promotional_box_show -->
				<div class="d-flex justify-content-between mb-2">
	            	{LANG.product.promotional}: 
	            	<span class="total-promotion" data-min_cart="{promotion.total_min}" data-value_max="{promotion.value_max}" data-type="{promotion.value_type}" data-value="{promotion.value}">
                        <span class="badge badge-info mr-1 removePromotionCode" title="{LANG.global.delete}" {promotion.hide_code}>{promotion.promotion_id} <i class="far fa-times"></i></span>
	            		{promotion.promotion_text}
	            	</span>
				</div>
                <!-- END: promotional_box_show -->
            								
                <!-- BEGIN: wcoin_box_show -->
	            <div class="d-flex justify-content-between mb-2">
	            	{LANG.product.discounts_wcoin}:<span class="total-wcoin"> -{data.wcoin_price_out}</span>
				</div>
                <!-- END: wcoin_box_show -->

	            <div class="d-flex justify-content-between mb-2">
	            	{LANG.product.transport_fee}: <span class="shipping_price">{data.shipping_price_out}</span>
	            </div>

                <!-- BEGIN: hidden -->
	            <div class="d-flex justify-content-between mb-3">
	            	{LANG.product.save_method}: <span class="method_price"><span class="percent">{data.method_price_percent}</span> {data.method_price_out}</span>
	            </div>
                <!-- END: hidden -->
                <div class="d-none justify-content-between mb-2 vat-row">
                    VAT: <span class="vat_price">{data.vat_price}</span>
                </div>
            </div>
            <div class="col-content total-payment pt-3">
                <b>{LANG.product.total}:</b> 
                <b>{data.cart_payment}</b>    
            </div>
            <div class="wcoin_expected" data-percent="{data.percentforwcoin}" data-money="{data.money2wcoin}">({LANG.product.wcoin_expected}<b> {data.wcoin_expected}</b>)</div>
        </div>
    </div>
    <div class="mess_payment">{data.mess}</div>
    <button type="submit" class="btn btn_payment btn_pay bg-color border-color text-color" {data.attr_btn}>{LANG.product.text_complete_order}</button>
<!-- END: box_cart -->


<!-- BEGIN: box_requestmore --> 
    <!-- BEGIN: request_more -->
    <div class="order-card request_more">
        <div class="card-content">
            <div class="card-title">{LANG.product.request_more}</div>
            <textarea name="request_more" class="textarea" rows="5" >{data.request_more}</textarea>    
            <div class="request_custom">
                <div class="row_c row_request" style="display:none">
                    <div class="row-title row-checkbox">
                        <input type="checkbox" name="gift" class="toggle_panel" id="gift"/>
                        <label for="gift"><span>{LANG.product.gift}</span></label>    
                    </div>            
                    <div class="row-panel" style="display:none">
                    </div>
                </div>
                <div class="row_c row_request">
                    <div class="row-title row-checkbox">
                        <input type="checkbox" name="invoice" class="toggle_invoice toggle_panel" id="invoice" {data.checked}/>
                        <label for="invoice"><span>{LANG.product.invoice}</span></label>
                    </div>
                    <div class="row-panel" style="{data.display}">
                        <div class="row_input">
                            <span class="title">{LANG.product.company_name}</span> 
                            <input class="form-control" type="text" name="invoice_company" value="{data.invoice_company}" >
                        </div>
                        <div class="row_input">
                            <span class="title">{LANG.product.tax_code}</span> 
                            <input class="form-control" type="text" name="invoice_tax_code" value="{data.invoice_tax_code}" />
                        </div>
                        <div class="row_input">
                            <span class="title">{LANG.product.address}</span> 
                            <textarea class="form-control" type="text" name="invoice_address" rows="5" />{data.invoice_address}</textarea>
                        </div>
                        <div class="row_input">
                            <span class="title">{LANG.global.email} <em>{LANG.global.invoice_email_note}</em></span> 
                            <input class="form-control" type="text" name="invoice_email" value="{data.invoice_email}" />
                        </div>
                    </div>
                </div>
            </div> 
        </div>
    </div> 
    <script type="text/javascript">     
        var o_total_money = o_total_promo = o_wcoin = o_shipping_price = vat = 0;
        var timeinterval = setInterval(toggleVAT, 200);
        getValue();
        // $(document).on("change",".toggle_invoice,#district,input[name='shipping']",function(){
        //     var total0 = parseInt($(".total-payment .number").text().replace(" vnđ","").replace(".",""));
        //     console.log(total0)
        //     toggleVAT(total0);
        // })
        // $(document).on("change","input[name='shipping']",function(){
        // //     getNewValue(o_total_money);
        //     toggleVAT();
        // })

        // function getNewValue(total_money){
        //     var is_checked = $(".toggle_invoice").prop(":checked");
        //     function updateValue(){                
        //         var rt_total_money = $(".total-payment .number").text();                            
        //         if (rt_total_money != o_total_money) {
        //             clearInterval(timeinterval);
        //             console.log(parseInt($(".shipping_price .number").text().replace(" vnđ","").replace(".","")));
        //             getValue();
        //         }
        //         if(is_checked == true){
        //             $(".toggle_invoice").prop("checked",false);
        //             $(".toggle_invoice").trigger("click");
        //         }                
        //     }
        //     updateValue();
            
        // }
        function toggleVAT(){
            getValue();
            var total0 = parseInt($(".total-payment .number").text().replaceAll(" vnđ","").replaceAll(".",""));                
            var total = o_total_money + o_total_promo + o_wcoin + o_shipping_price;
            if($(".toggle_invoice").is(":checked") == true){
                total += vat;               
                $(".vat-row").addClass("d-flex").removeClass("d-none");
            }else{
                $(".vat-row").addClass("d-none").removeClass("d-flex");
            };
            if(total0 != total){
                $(".total-payment .auto_price").attr("data-value",total);
                $(".total-payment .auto_price").text(total);
                setTimeout(function(){auto_price_format();},500);
            }
        }
        function getValue(){
            o_total_money       = ($(".temp-total-money").length)?parseInt($(".temp-total-money .number").text().replaceAll(" vnđ","").replaceAll(".","")):0,
            o_total_promo       = ($(".total-promotion").length)?-1*parseInt($(".total-promotion .number").attr('data-value')):0,
            o_wcoin             = ($(".total-wcoin").length)?-1*parseInt($(".total-wcoin .number").text().replaceAll(" vnđ","").replaceAll(".","")):0,
            o_shipping_price    = ($(".shipping_price").length)?parseInt($(".shipping_price .number").attr('data-value')):0,
            vat                 = ($(".vat_price").length)?parseInt($(".vat_price .number").attr("data-value")):0;
            // bundled             = ($(".bundled_product").length)?parseInt($(".bundled_product").attr("data-value")):0;
        }
    </script>
    <!-- END: request_more --> 

    <!-- BEGIN: request_more_text -->
    <div class="request_more frame">
        <a class="frame-icon ficon-edit btn-custom1" href="{link_edit}"> {LANG.global.change}</a>
        <label class="title">{LANG.product.request_more} :</label>
        <div>{data.request_more}</div>
    </div> 
    <!-- END: request_more_text --> 
    
    <div class="row_btn" align="right">
        <a onclick="go_link('{data.link_buy_more}');" class="fl go_buy"><i class="ficon-angle-double-left"></i>  {LANG.product.btn_buy_more}</a> 
        <input type="hidden" name="do_submit" value="1" />
    </div>
<!-- END: box_requestmore -->

<!-- BEGIN: combo_gift_include -->
<div class="col_gift_include combo_info">
    <!-- BEGIN: ul -->
    <ul class="list_none">
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
</div>
<!-- END: combo_gift_include -->

<!-- BEGIN: bundled_product -->
<div class="card-content bundled_product" data-value="{bundled_product_price}">
    <div class="card-title">{LANG.product.bundled_product_cart}</div>
    <div class="list_item">
        <!-- BEGIN: item -->
        <div class="item">
            <div class="picture"><img src="{row.picture}" alt="{row.title}"></div>
            <div class="info">
                <div class="title"><a href="{row.link}">{row.title}</a></div>
                <div class="info-price">
                    <div class="endow_price">{row.endow_price}</div>
                    {row.price}
                </div>
            </div>
        </div>
        <!-- END: item -->
    </div>
</div>
<!-- END: bundled_product -->