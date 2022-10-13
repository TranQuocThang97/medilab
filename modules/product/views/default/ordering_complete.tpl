<!-- BEGIN: main -->
    {data.content}
    <script type="text/javascript">localStorage.clear();</script>
<!-- END: main -->

<!-- BEGIN: ordering_complete -->
<!-- BEGIN: bo -->
<div class="bs-wizard bg-text-color">
    <div class="bs-wizard-step">
        <span class="bs-wizard-dot bg-color active"><i class="fas fa-map-marker-alt"></i></span>
        <div class="text-center bs-wizard-stepnum">
            <span class="hidden-xs">{LANG.product.shipping_address}</span>
        </div>
    </div>
    <div class="progress active"><div class="progress-bar"></div></div>
    <div class="bs-wizard-step">
        <span class="bs-wizard-dot bg-color active"><i class="far fa-dollar-sign"></i></span>
        <div class="text-center bs-wizard-stepnum">
            <span class="hidden-xs">{LANG.product.payment_orderbuy}</span>
        </div>
    </div>
    <div class="progress active"><div class="progress-bar"></div></div>
    <span class="bs-wizard-step disabled bs-wizard-last">
        <span class="bs-wizard-dot bg-color active"><i class="fas fa-check"></i></span>
        <div class="bs-wizard-stepnum">
            <span class="hidden-xs">{LANG.product.complete}</span>
        </div>
    </span>
</div>
<!-- END: bo -->
<div class="wrap_cart">
    {data.content}
    {data.ordering_method}
    {data.ordering_shipping}
</div>
<!-- END: ordering_complete -->

<!-- BEGIN: table_cart_ordering_method_mail -->
    <div class="cart_content">
        <div class="num_product">
            {LANG.product.order} ({data.num_product}) {LANG.product.product}
        </div>
        <!-- BEGIN: row_item -->
        <div class="item_Cart" style="margin-bottom: 15px;border: 1px #d4d4d4 solid;padding: 10px;margin: 10px 0px;">
            <div class="col"><b>{row.quantity} x</b> <span class="title">{row.title} {row.color}</span></div>        
            <div class="col up_total" align="center" style="text-align: left;">{row.total}</div>
            <div><span class="gift">{row.item_related_title}</span></div>
            {row.gift_include}
        </div>
        <!-- END: row_item --> 
        <!-- BEGIN: row_empty -->
        <div class="col col_empty" colspan="7">{row.mess}</div>
        <!-- END: row_empty --> 
        <div class="shipping">
            <p>{LANG.product.provisional}: <span class="provisional">{data.cart_total}</span></p>
            <!-- BEGIN:promotional_box_show -->
            <p>{LANG.product.promotional}:<span class="promotion_price">{data.promotion_price_out}</span></p>
            <!-- END:promotional_box_show -->
            <!-- BEGIN:wcoin_box_show -->
            <p>{LANG.product.discounts_wcoin_mail}:<span class="wcoin_price"> -{data.wcoin_price_out}</span></p>
            <!-- END:wcoin_box_show -->
            <!-- BEGIN:save_method -->
            <p>{LANG.product.save_method}:<span class="save_method_price"> {data.save_method}</span></p>
            <!-- END:save_method -->
            <!-- BEGIN: shipping_price -->
            <p>{LANG.product.transport_fee}: <span class="shipping_price">{data.shipping_price_out}</span></p>
            <!-- END: shipping_price -->

            <!-- BEGIN: bo -->
            <p class="wcoin_expected">({LANG.product.wcoin_expected}<b> {data.wcoin_expected}</b>)</p>
            <!-- END: bo -->
        </div>
        <span class="col-content total_price"><b>{LANG.product.total}:</b> <b>{data.cart_payment}</b></span>
    </div>
<!-- END: table_cart_ordering_method_mail -->

<!-- BEGIN: combo_gift_include -->
    <div style="margin-top: 15px; margin-left: 20px"><b>{title}</b></div>
    <!-- BEGIN: item -->
    <div class="combo_gift" style="border: 1px #d4d4d4 solid;padding: 10px; margin: 10px 0px 10px 20px;">
        <div class="col">{row.title}</div>
        <div class="col up_total" align="center" style="text-align: left;">{row.price}</div>
    </div>
    <!-- END: item -->
<!-- END: combo_gift_include -->

<!-- BEGIN: table_cart_complete_bo -->
    <div class="table-responsive manage">
        <table class="table manage-table">
            <thead>
                <tr>
                    <th class="cot" width="120px"></th>
                    <th class="cot" >{LANG.user.col_title}</th>
                    <th class="cot" width="20%" style="text-align: center;">{LANG.user.col_price}</th>
                    <th class="cot" width="12%" style="text-align: center;">{LANG.user.col_quantity}</th>
                    <th class="cot" width="20%" style="text-align: right;">{LANG.user.col_total}</th>
                </tr>
            </thead>
            <tbody>
                {data.row_item}
                <!-- BEGIN: row_item -->
                <tr class="tr_item">
                    <td class="cot" align="center"><img src="{row.picture}" alt="{row.title}"/></td>
                    <td class="cot title_product">{row.title} <p>{row.gift} {row.item_related_pic} {row.item_related_title}</p></td>
                    <td class="cot" align="center">{row.price_buy}</td>
                    <td class="cot" align="center">{row.quantity}</td>
                    <td class="cot" align="right"><b>{row.total}</b></td>
                </tr>
                <!-- END: row_item --> 
                <!-- BEGIN: row_empty -->
                <tr>
                    <td align="center" colspan="5">{row.mess}</td>
                </tr>
                <!-- END: row_empty --> 
                <tr>
                    <td class="cot" align="right" colspan="3">{LANG.user.cart_total}</td>
                    <td class="cot" align="right" colspan="2">{data.total_order}</td>
                </tr>
                <!-- BEGIN: promotional_box_show -->
                <tr>
                    <td class="cot" align="right" colspan="3">{LANG.user.promotion_code}</td>
                    <td class="cot" align="right" colspan="2">-{data.promotion_price}</td>
                </tr>
                <!-- END: promotional_box_show -->
                <!-- BEGIN: wcoin_box_show -->
                <tr>
                    <td class="cot" align="right" colspan="3">{LANG.user.wcoin_user}</td>
                    <td class="cot" align="right" colspan="2">-{data.payment_wcoin2money}</td>
                </tr>
                <!-- END: wcoin_box_show -->
                <!-- BEGIN: hidden -->
                <tr>
                    <td class="cot" align="right" colspan="3">{LANG.user.save_method}</td>
                    <td class="cot" align="right" colspan="2">{data.method_price}</td>
                </tr>
                <!-- END: hidden -->
                <tr>
                    <td class="cot" align="right" colspan="3">{LANG.user.price_shipping}</td>
                    <td class="cot" align="right" colspan="2">{data.shipping_price}</td>
                </tr>
                <!-- BEGIN: hidden -->
                <tr>
                    <td class="cot" align="right" colspan="3">{LANG.user.wcoin_accumulation}</td>
                    <td class="cot" align="right" colspan="2">+{data.wcoin_accumulation}</td>
                </tr>
                <!-- END: hidden -->
                <tr>
                    <td class="cot" align="right" colspan="3">{LANG.user.cart_payment}</td>
                    <td class="cot" align="right" colspan="2" style="font-size: 20px;color: red;"><b>{data.total_payment}</b></td>
                </tr>
                <!-- BEGIN: bo -->
                <tr>
                    <td class="cot note_plus_wcoin" align="right" colspan="5">{LANG.user.note_plus_wcoin}</td>
                </tr>
                <!-- END: bo -->
            </tbody>
        </table>
    </div>
<!-- END: table_cart_complete_bo -->

<!-- BEGIN: review_bo -->
    <div class="order-box">
        <div class="order-title"><i class="fas fa-check-circle"></i>{data.title_page}</div>
        <div class="content_page">{data.content_page}</div>
        <div class="view_cart"><a id="view_cart">{LANG.product.view_cart}</a></div>
        <div class="order-box-content flex-wrap" style="display: none">
            <!-- BEGIN: output_mess -->
            <div class="alert alert-{data.status_payment} alert-dismissable" style="width: 100%;">
                <button class="close" aria-hidden="true" data-dismiss="alert" type="button">×</button>
                <div class="check_succes"><i class="fa-ok"></i></div>
                {data.notification_payment}
            </div>
            <!-- END: output_mess -->
            <div class="order-box-left">
                <div class="order-card">
                    <div class="card-content">
                        <div class="card-title">
                            {LANG.user.order_detail}: <span>#{data.order_code}</span>
                        </div>
                        {data.table_cart}
                    </div>
                </div>
                <!-- BEGIN: bo -->
                <div class="row_btn" align="right">
                    <input type="button" class="btn btn-default" value="{LANG.product.btn_buy_more}" onclick="go_link('{data.link_buy_more}');"/>
                </div>
                <!-- END: bo -->
            </div>
            <div class="order-box-right">
                <div class="order-card">
                    <div class="card-content">
                        <div class="card-title">{LANG.user.delivery_address}</div>
                        <div class="card-item">
                            <div class="pb-1">{LANG.user.full_name}: {data.d_full_name}</div>
                            <div class="pb-1">{LANG.user.email}: {data.d_email}</div>
                            <div class="pb-1">{LANG.user.phone}: {data.d_phone}</div>
                            <div class="pb-1">{LANG.user.address}: {data.d_address}</div>
                        </div>
                    </div>
                </div>
                <!-- BEGIN: invoice -->
                <div class="order-card invoice">
                    <div class="card-content">
                        <div class="card-title">{LANG.user.invoice}</div>
                        <div class="card-item">
                            <div>{LANG.user.invoice_company}: {data.invoice_company}</div>
                            <div>{LANG.user.invoice_tax_code}: {data.invoice_tax_code}</div>
                            <div>{LANG.user.invoice_address}: {data.invoice_address}</div>
                        </div>
                    </div>
                </div>
                <!-- END: invoice -->
                <div class="order-card">
                    <div class="card-content">
                        <div class="card-title">{LANG.user.ordering_method}</div>
                        <div class="card-item">
                            <label class="title">{data.method.title}</label>
                            <div class="content">{data.method.content}</div>
                            <!-- BEGIN: province_ship --> 
                            <label>{LANG.user.consignee_at} <span class="province_ship">{data.district}  {data.province}</span></label>
                            <!-- END: province_ship --> 
                        </div>
                    </div>
                </div>
                <div class="order-card">
                    <div class="card-content">
                        <div class="card-title">{LANG.user.request_more}</div>
                        <div class="card-item">
                            {data.request_more}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row_btn" align="right">
            <input type="button" class="btn btn-default" value="{LANG.product.btn_buy_more}" onclick="go_link('{data.link_buy_more}');" style="background: #ffd302"/>
        </div>
    </div>
<script>
    $('a#view_cart').on('click', function (){
        $(this).toggleClass('hide');
        $('.order-box .order-box-content').slideToggle();
    });
</script>
<!-- END: review_bo -->

<!-- BEGIN: review -->
<div class="order-box">
    <div class="icon"><i class="fas fa-check-circle"></i></div>
    <div class="order-title_detail">{data.title_page}</div>
    <div class="content_page">{data.content_page}</div>
    <!-- BEGIN: bo -->
    <div class="view_cart"><a id="view_cart">{LANG.product.view_cart}</a></div>
    <!-- END: bo -->
    <div class="order-box-content">
        <!-- BEGIN: output_mess -->
        <div class="alert alert-{data.status_payment} alert-dismissable" style="width: 100%;">
            <button class="close" aria-hidden="true" data-dismiss="alert" type="button">×</button>
            <div class="check_succes"><i class="fa-ok"></i></div>
            {data.notification_payment}
        </div>
        <!-- END: output_mess -->
        <div class="order-box-content">
            <div class="order-card">
                <div class="card-content">
                    <div class="card-title"><span>{LANG.user.order_detail}: #{data.order_code}</span><a href="{data.manage_order_link}">{LANG.product.manage_order}</a></div>
                    {data.table_cart}
                </div>
            </div>
            <!-- BEGIN: bo -->
            <div class="row_btn" align="right">
                <input type="button" class="btn btn-default" value="{LANG.product.btn_buy_more}" onclick="go_link('{data.link_buy_more}');"/>
            </div>
            <!-- END: bo -->
        </div>
        <!-- BEGIN: bo -->
        <div class="order-box-right">
            <div class="order-card">
                <div class="card-content">
                    <div class="card-title">{LANG.user.delivery_address}</div>
                    <div class="card-item">
                        <div class="pb-1">{LANG.user.full_name}: {data.d_full_name}</div>
                        <div class="pb-1">{LANG.user.email}: {data.d_email}</div>
                        <div class="pb-1">{LANG.user.phone}: {data.d_phone}</div>
                        <div class="pb-1">{LANG.user.address}: {data.d_address}</div>
                    </div>
                </div>
            </div>
            <!-- BEGIN: invoice -->
            <div class="order-card invoice">
                <div class="card-content">
                    <div class="card-title">{LANG.user.invoice}</div>
                    <div class="card-item">
                        <div>{LANG.user.invoice_company}: {data.invoice_company}</div>
                        <div>{LANG.user.invoice_tax_code}: {data.invoice_tax_code}</div>
                        <div>{LANG.user.invoice_address}: {data.invoice_address}</div>
                    </div>
                </div>
            </div>
            <!-- END: invoice -->
            <div class="order-card">
                <div class="card-content">
                    <div class="card-title">{LANG.user.ordering_method}</div>
                    <div class="card-item">
                        <label class="title">{data.method.title}</label>
                        <div class="content">{data.method.content}</div>
                        <!-- BEGIN: province_ship -->
                        <label>{LANG.user.consignee_at} <span class="province_ship">{data.district}  {data.province}</span></label>
                        <!-- END: province_ship -->
                    </div>
                </div>
            </div>
            <div class="order-card">
                <div class="card-content">
                    <div class="card-title">{LANG.user.request_more}</div>
                    <div class="card-item">
                        {data.request_more}
                    </div>
                </div>
            </div>
        </div>
        <!-- END: bo -->
    </div>
</div>
<!-- END: review -->

<!-- BEGIN: table_cart_complete -->
<div class="table-responsive manage">
    <ul class="list_none">
        <li><b>{LANG.product.reciever}:</b> {data.d_full_name}, {LANG.product.phone_recieve}:{data.d_phone}</li>
        <li><b>{LANG.product.delivery_address}:</b> {data.delivery_address}</li>
        <li><b>{LANG.product.total}:</b> <span>{data.total_payment}</span></li>
    </ul>
</div>
<!-- END: table_cart_complete -->

<!-- BEGIN: ordering_method -->
<div class="ordering_method">
    <div class="method_title">{LANG.product.ordering_method_complete}</div>
    <div class="list_item">
        <!-- BEGIN: item -->
        <div class="item col-sm-4 col-12">
            <div class="wrap_item">
                <div class="content"><span>{row.title}</span>{row.picture}</div>
            </div>
        </div>
        <!-- END: item -->
    </div>
</div>
<!-- END: ordering_method -->

<!-- BEGIN: ordering_shipping -->
<div class="ordering_shipping">
    <div class="method_title">{LANG.product.ordering_shipping_complete}</div>
    <div class="list_item">
        <!-- BEGIN: item -->
        <div class="item col-sm-6 col-12">
            <div class="wrap_item">
                <div class="content"><span>{row.title}</span>{row.picture}</div>
            </div>
        </div>
        <!-- END: item -->
    </div>
</div>
<!-- END: ordering_shipping -->