<!-- BEGIN: main -->
<div class="user-manager" id="user-manager">
    <div id="ims-column_left">
        {data.box_left}
    </div>
    <div id="ims-content">
        {data.content}
    </div>
</div>
<!-- END: main -->

<!-- BEGIN: main_1 -->
<div id="ims-content" class="order-review">
    {data.content}
</div>
<!-- END: main_1 -->


<!-- BEGIN: table_promotion -->
<div class="table-responsive">
    <table class="table table-bordered table-hover table-striped table_row">
        <thead>
        <tr >
            <th class="header">{LANG.global.id}</th>
            <th class="header" width="20%">{LANG.global.percent}</th>
            <th class="header" width="25%">{LANG.global.date_end}</th>
        </tr>
        </thead>
        <tbody>
        <!-- BEGIN: row_item -->
        <tr>
            <td class="cot" align="center">{row.promotion_id}</td>
            <td class="cot" align="center">{row.percent}%</td>
            <td class="cot" align="center">{row.date_end}</td>
        </tr>
        <!-- END: row_item -->
        <!-- BEGIN: row_empty -->
        <tr class="warning">
            <td align="center" colspan="5">{row.mess}</td>
        </tr>
        <!-- END: row_empty -->
        </tbody>
    </table>
</div>
<!-- END: table_promotion -->

<!-- BEGIN: table_cart -->
<div class="manage manage_table">
    <table class="table manage-table">
        <thead>
        <tr>
            <th class="cot" width="10%"></th>
            <th class="cot" >{LANG.user.col_title}</th>
            <th class="cot" width="20%" style="text-align: center;">{LANG.user.col_price}</th>
            <th class="cot" width="12%" style="text-align: center;">{LANG.user.col_quantity}</th>
            <th class="cot" width="20%" style="text-align: right;">{LANG.user.col_total}</th>
        </tr>
        </thead>
        <tbody>
        {data.row_item}
        <!-- BEGIN: row_item -->
        <!-- BEGIN: combo -->
        <tr class="tr_item {row.class_type}">
            <!-- BEGIN: item -->
            <td class="cot picture_product" align="center"><img src="{item.picture}" alt="{item.title}"/></td>
            <td class="cot title_product" colspan="4">
                <fieldset class="combo">
                    <span class="badge badge-info">{LANG.user.include}</span>
                    <div class="info d-flex flex-wrap justify-content-between">
                        <div class="title">{item.title}</div>
                        <div class="price">{item.price}</div>
                    </div>
                </fieldset>
            </td>
            <!-- END: item -->
        </tr>
        <!-- END: combo -->

        <!-- BEGIN: gift -->
        <tr class="tr_item {row.class_type}">
            <!-- BEGIN: item -->
            <td class="cot picture_product" align="center"><img src="{item.picture}" alt="{item.title}"/></td>
            <td class="cot title_product" colspan="4">
                <fieldset class="combo">
                    <span class="badge badge-success">Quà tặng</span>
                    <div class="info d-flex flex-wrap justify-content-between">
                        <div class="title">{item.title}</div>
                        <!-- <div class="price">{item.price}</div> -->
                    </div>
                </fieldset>
            </td>
            <!-- END: item -->
        </tr>
        <!-- END: gift -->

        <!-- BEGIN: default -->
        <tr class="tr_item">
            <td class="cot picture_product" align="center"><img src="{row.picture}" alt="{row.title}"/></td>
            <td class="cot title_product">{row.title} <p>{row.color_title}</p> <p class="color" style="background: {row.color_value}"></p> <p>{row.size_title}</p><p>{row.gift} {row.item_related_pic} {row.item_related_title}</p></td>
            <td class="cot" align="center" title="{LANG.user.col_price}">{row.price_buy}</td>
            <td class="cot" align="center" title="{LANG.user.col_quantity}">x {row.quantity}</td>
            <td class="cot total_product" title="{LANG.user.col_total}"><b>{row.total}</b></td>
        </tr>
        <!-- END: default -->

        <!-- END: row_item -->
        <!-- BEGIN: row_empty -->
        <tr>
            <td align="center" colspan="5">{row.mess}</td>
        </tr>
        <!-- END: row_empty -->
        <tr>
            <td class="cot" align="right" colspan="3">{LANG.user.cart_total}</td>
            <td class="cot" align="right" colspan="2">{data.cart_total}</td>
        </tr>
        <tr>
            <td class="cot" align="right" colspan="3">{LANG.user.promotion_code}</td>
            <td class="cot" align="right" colspan="2">-{data.promotion_price}</td>
        </tr>
        <tr>
            <td class="cot" align="right" colspan="3">{LANG.user.wcoin_user}</td>
            <td class="cot" align="right" colspan="2">-{data.payment_wcoin2money}</td>
        </tr>
        <tr>
            <td class="cot" align="right" colspan="3">{LANG.user.save_method}</td>
            <td class="cot" align="right" colspan="2">{data.method_price}</td>
        </tr>
        <tr>
            <td class="cot" align="right" colspan="3">{LANG.user.price_shipping}</td>
            <td class="cot" align="right" colspan="2">{data.shipping_price}</td>
        </tr>
        <tr>
            <td class="cot" align="right" colspan="3">{LANG.user.wcoin_accumulation}</td>
            <td class="cot" align="right" colspan="2">+{data.wcoin_accumulation}</td>
        </tr>
        <tr>
            <td class="cot" align="right" colspan="3">{LANG.user.cart_payment}</td>
            <td class="cot" align="right" colspan="2" style="font-size: 18px;color: red;"><b>{data.total_payment}</b></td>
        </tr>
        <tr>
            <td class="cot note_plus_wcoin" align="right" colspan="5">{LANG.user.note_plus_wcoin}</td>
        </tr>
        </tbody>
    </table>
</div>
<!-- END: table_cart -->

<!-- BEGIN: edit -->
<div class="order_code mb-3">
    <span>{LANG.user.order_detail} #{data.order_code} {data.cancel}</span>
    <!-- BEGIN: row_cancel -->
    <button onclick="imsUser.cancelOrder('{data.order_code}')" class="btn btn-danger ml-auto"><i class="fal fa-cancel"></i> {LANG.user.cancel_order}</button>
    <!-- END: row_cancel -->
    <!-- <button onclick="window.print()" class="btn print_page"><i class="fal fa-print"></i> {LANG.product.print_order_detail}</button> -->
</div>
<div class="row order_detail">
    <div class="info-print col-12 px-0">
        <p><b>{data.o_full_name}</b></p>
        <p><b>{LANG.user.email}: </b>{data.o_email}</p>
        <p><b>{LANG.user.phone}: </b>{data.o_phone}</p>
        <p><b>{LANG.user.address}: </b>{data.o_address}</p>
    </div>
    <div class="box_left bg-white">
        <h3>{LANG.user.order_detail}</h3>
        {data.table_cart}
        <!-- BEGIN: invoice -->
        <div class="invoice">
            <h3>{LANG.user.invoice}</h3>
            <p><b>{LANG.user.invoice_company}: </b>{data.invoice_company}</p>
            <p><b>{LANG.user.invoice_tax_code}: </b>{data.invoice_tax_code}</p>
            <p><b>{LANG.user.invoice_address}: </b>{data.invoice_address}</p>
        </div>
        <!-- END: invoice -->
    </div>
    <div class="col_right pl-md-3">
        <div class="ordering_address">
            <div class="ordering_address_l">
                <h3>{LANG.user.ordering_address}</h3>
                <div class="row_c">
                    <label class="title">{LANG.user.full_name} : </label>
                    <label class="content">{data.o_full_name}</label>
                </div>
                <div class="row_c">
                    <label class="title">{LANG.user.email} :</label>
                    <label class="content">{data.o_email}</label>
                </div>
                <div class="row_c">
                    <label class="title">{LANG.user.phone} :</label>
                    <label class="content">{data.o_phone}</label>
                </div>
                <div class="row_c">
                    <label class="title">{LANG.user.address} :</label>
                    <label class="content">{data.o_address}</label>
                </div>
            </div>
            <div class="ordering_address_r">
                <h3>{LANG.user.delivery_address}</h3>
                <div class="row_c">
                    <label class="title">{LANG.user.full_name} : </label>
                    <label class="content">{data.d_full_name}</label>
                </div>
                <div class="row_c">
                    <label class="title">{LANG.user.email} :</label>
                    <label class="content">{data.d_email}</label>
                </div>
                <div class="row_c">
                    <label class="title">{LANG.user.phone} :</label>
                    <label class="content">{data.d_phone}</label>
                </div>
                <div class="row_c">
                    <label class="title">{LANG.user.address} :</label>
                    <label class="content">{data.d_address}</label>
                </div>
            </div>
            <div class="ordering_method">
                <h3>{LANG.user.ordering_method}</h3>
                <div class="row_c" style="display: block;">
                    <label class="title">{data.method.title}</label>
                    <div class="content">{data.method.content}</div>
                    <!-- BEGIN: province_ship -->
                    <label>{LANG.user.consignee_at} <span class="province_ship">{data.district}  {data.province}</span></label>
                    <!-- END: province_ship -->
                </div>
            </div>
            <div class="request_more">
                <h3>{LANG.user.request_more}</h3>
                <div class="content">{data.request_more}</div>
            </div>
        </div>
        <!-- BEGIN: row_cancel2 -->
        <div class="d-flex mt-5">
            <button onclick="imsUser.cancelOrder('{data.order_code}')" class="btn btn-danger ml-auto"><i class="fal fa-cancel"></i> {LANG.user.cancel_order}</button>
        </div>
        <!-- END: row_cancel2 -->
    </div>
    <!-- BEGIN: order_log -->
    <div class="order_log">
        <div class="list_log">
            <h3>{LANG.user.order_log}</h3>
            <!-- BEGIN: row_log -->
            <div class="log_item">
                <div class="message">
                    <div class="log"><i class="fas fa-circle"></i>{row.title}</div>
                    <div class="datetime"><span>{row.time}</span></div>
                </div>
            </div>
            <!-- END: row_log -->
        </div>
    </div>
    <!-- END: order_log -->
    <!-- END: edit -->

    <!-- BEGIN: manage -->
    <div class="box-manager">
        <div class="box-title">{data.page_title}</div>
        <div class="box-content" id="user_ordering">
            {data.err}
            <div class="manage">
                <ul class="nav nav-tabs mb-0" role="tablist">
                    <!-- BEGIN: row_filter -->
                    <li class="nav-item">
                        <a href="{row.link}" class="nav-link {row.active}" >{row.title}</a>
                    </li>
                    <!-- END: row_filter -->
                </ul>
                <div class="manage_table tab-content">
                    <div class="wrap_table">
                        <table class="table manage-table">
                            <thead>
                            <tr >
                                <th class="cot" width="12%" style="text-align: left;">{LANG.user.order_code}</th>
                                <th class="cot" width="15%" style="text-align: left;">{LANG.user.date_create}</th>
                                <th class="cot">{LANG.user.col_product}</th>
                                <!-- <th class="cot" width="13%" style="text-align: right;">{LANG.user.status_delivery}</th> -->
                                <th class="cot" width="15%" style="text-align: right;">{LANG.user.total_order}</th>
                                <th class="cot pl-md-2" width="15%" style="text-align: right;">{LANG.user.status_order}</th>
                                <!-- <th class="cot" width="5%" style="text-align: right;">{LANG.user.source}</th> -->
                            </tr>
                            </thead>
                            <tbody>
                            {data.row_item}
                            <!-- BEGIN: row_item -->
                            <tr id="row_{row.order_id}" class="{row.class}">
                                <td class="cot" align="left"><a href="{row.link}" style="color:#006cff">{row.order_code}</a></td>
                                <td class="cot" align="left"><div>{row.time_create}</div>{row.date_create}</td>
                                <td class="cot">
                                    {row.product}
                                    <!-- BEGIN: gift -->
                                    <div class="col_gift">
                                        <div class="info"><b class="type">[{LANG.product.gift}]</b> {col.title}</div>
                                    </div>
                                    <!-- END: gift -->
                                </td>
                                <!-- <td class="cot" align="right"><span style="color:{row.status_delivery.color_title}; background-color:{row.status_delivery.color_bg}; border: 1px solid {row.status_delivery.color_border}">{row.status_delivery.title}</span></td> -->
                                <td class="cot" align="right">{row.total_payment}</td>
                                <td class="cot pl-md-2" align="center" style="padding-left: 1rem !important;"><div style="color:{row.status_order.color_title}; background-color:{row.status_order.color_bg}; border: 0px solid {row.status_order.color_border}; padding: 2px;">{row.status_order.title}</div></td>
                                <!-- <td class="cot" align="right">{row.sales_channel}</td> -->
                            </tr>
                            <!-- END: row_item -->
                            <!-- BEGIN: row_empty -->
                            <tr>
                                <td class="cot cot_empty" align="center" colspan="6">{row.mess}</td>
                            </tr>
                            <!-- END: row_empty -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        {data.nav}
    </div>
</div>
<script language="javascript">
    $('input.datepicker').datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd/mm/yy',
        yearRange: "-100:+0",
    });
</script>
<!-- END: manage -->