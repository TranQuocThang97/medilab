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

<!-- BEGIN: manage -->
{data.err}
<div class="box-manager">
    <div class="box-title">{data.page_title}</div>
    <div class="box-content" id="list_contributor">
        <div class="list_user_contributor manage_table">
            <div class="wrap_table">
                <table class="table manage-table">
                    <thead>
                    <tr>
                        <th class="cot" width="5%" style="font-weight: bold;">{LANG.user.index}</th>
                        <th class="cot" width="18%" style="font-weight: bold;">{LANG.user.recommend_link}</th>
                        <th class="cot" width="18%" style="font-weight: bold;">{LANG.user.referred_user}</th>
                        <th class="cot" width="12%" style="font-weight: bold;">{LANG.user.phone}</th>
                        <th class="cot" width="22%" style="font-weight: bold;">{LANG.user.email}</th>
                        <th class="cot" width="10%" style="font-weight: bold; text-align: right;">{LANG.user.time}</th>
                        <th class="cot" width="15%" style="font-weight: bold; text-align: right;">{LANG.user.order_history}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <!-- BEGIN: row_item -->
                    <tr>
                        <td class="cot">{row.stt}</td>
                        <td class="cot" style="padding-right: 1rem;"><b class="text-ellipsis"><a href="{row.recommend_link}" target="_blank" title="{row.recommend_link}">{row.recommend_link}</a></b></td>
                        <td class="cot" style="padding-right: 1rem;">{row.full_name}</td>
                        <td class="cot" style="padding-right: 1rem;">{row.phone}</td>
                        <td class="cot" style="padding-right: 1rem;">{row.email}</td>
                        <td class="cot" align="right">{row.date_create}</td>
                        <td class="cot" align="right"><a href="{row.link}" target="_blank" class="btn btn-primary">{LANG.user.see}</a></td>
                    </tr>
                    <!-- END: row_item -->
                    <!-- BEGIN: row_empty -->
                    <tr>
                        <td class="cot cot_empty" align="center" colspan="10">{row.mess}</td>
                    </tr>
                    <!-- END: row_empty -->
                    </tbody>
                </table>
            </div>
        </div>
        <div class="table_nav">{data.nav}</div>
        <input id="do_action" type="hidden" value="" name="do_action">
    </div>
</div>
<script type="text/javascript">
    $('.text-ellipsis a').tooltip();
    $(document).on("click",".box_user .name .view a",function(e) {
        e.preventDefault();
        var href = $(this).attr('href');
        window.open(href);
    });
    var ajax_send = false;
    $(document).on("click",".name.item_parent",function(e) {
        var this_click = $(this);
        var element = $(this).next();
        var id = $(this).data('id');
        var children = $(this).data('children');
        if(this_click.hasClass('open')){
            this_click.removeClass('open');
        }else{
            this_click.addClass('open');
        }
        if(ajax_send == true){
            return false;
        }
        ajax_send = true;
        if(!id) return false;
        if(!children) return false;
        if(children != 1) return false;
        $.ajax({
            type: "POST",
            url: ROOT+"ajax.php",
            data: { "m" : "user", "f" : "load_tree", "id" : id}
        }).done(function(string) {
            var data = JSON.parse(string);
            var html = '';
            ajax_send = false;
            $.each(data.data, function (key, obj){
                html += '<div class="item ' + obj.item_parent + '">' +
                    '<div class="name ' + obj.item_parent + '" data-id="' + obj.id + '" data-children="' + obj.children + '">'+
                    '<span class="text">' + obj.text + '</span>' +
                    '<span class="count"> (' + obj.count + ')</span>' +
                    '<span class="view"><a target="_blank" href="' + obj.link +'"> (Lịch sử giao dịch)</a></span>' +
                    '</div>' +
                    obj.box_children +
                    '</div>';
            });
            element.append(html);
            this_click.data('children',0);
            return false;
        });
        return false;
    });
</script>
<!-- END: manage -->

<!-- BEGIN: referred_list_order -->
<div class="box-manager">
    <div class="box-title">{data.page_title}</div>
    <div class="box-content" id="list_contributor">
        <div class="list_user_contributor manage_table">
            <div class="wrap_table">
                <table class="table manage-table">
                    <!-- BEGIN: normal -->
                    <thead>
                    <tr>
                        <th class="cot" width="5%" style="font-weight: bold;">{LANG.user.index}</th>
                        <th class="cot" width="11%" style="font-weight: bold;">{LANG.user.order_code}</th>
                        <th class="cot" width="18%" style="font-weight: bold;">{LANG.user.status_order}</th>
                        <th class="cot" width="12%" style="font-weight: bold; text-align: right; padding-right: 1rem;">{LANG.user.total_order}</th>
                        <th class="cot" width="12%" style="font-weight: bold; text-align: right; padding-right: 1rem;">{LANG.user.after_promotion}</th>
                        <th class="cot" width="12%" style="font-weight: bold; text-align: right; padding-right: 1rem;">{LANG.user.commission}</th>
                        <th class="cot" width="18%" style="font-weight: bold; padding-left: 1rem;">{LANG.user.commission_status}</th>
                        <th class="cot" width="12%" style="font-weight: bold; text-align: right;">{LANG.user.time}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <!-- BEGIN: row_item -->
                    <tr>
                        <td class="cot">{row.stt}</td>
                        <td class="cot" style="padding-right: 1rem;"><a target="_blank">#{row.order_code}</a></td>
                        <td class="cot" style="padding-right: 1rem;">{row.status_order}</td>
                        <td class="cot" style="padding-right: 1rem; text-align: right"><span class="auto_price">{row.total_order}</span></td>
                        <td class="cot" style="padding-right: 1rem; text-align: right"><span class="auto_price">{row.total_order_after_promotion}</span></td>
                        <td class="cot" style="padding-right: 1rem; text-align: right"><span class="auto_price">{row.deeplink_total}</span></td>
                        <td class="cot" style="padding-right: 1rem; padding-left: 1rem;">{row.commission_status}</td>
                        <td class="cot" align="right">{row.date_create}</td>
                    </tr>
                    <!-- END: row_item -->
                    <!-- END: normal -->
                    <!-- BEGIN: show_detail_order -->
                    <thead>
                    <tr>
                        <th class="cot" width="5%" style="font-weight: bold;">{LANG.user.index}</th>
                        <th class="cot" width="11%" style="font-weight: bold;">{LANG.user.order_code}</th>
                        <th class="cot" width="15%" style="font-weight: bold;">{LANG.user.status_order}</th>
                        <th class="cot" width="11%" style="font-weight: bold; text-align: right; padding-right: 1rem;">{LANG.user.total_order}</th>
                        <th class="cot" width="11%" style="font-weight: bold; text-align: right; padding-right: 1rem;">{LANG.user.after_promotion}</th>
                        <th class="cot" width="11%" style="font-weight: bold; text-align: right; padding-right: 1rem;">{LANG.user.commission}</th>
                        <th class="cot" width="16%" style="font-weight: bold; padding-left: 1rem;">{LANG.user.commission_status}</th>
                        <th class="cot" width="11%" style="font-weight: bold; text-align: right;">{LANG.user.time}</th>
                        <th class="cot" width="9%" style="font-weight: bold; text-align: right;"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <!-- BEGIN: row_item -->
                    <tr>
                        <td class="cot">{row.stt}</td>
                        <td class="cot" style="padding-right: 1rem;"><a href="{row.link}" target="_blank">#{row.order_code}</a></td>
                        <td class="cot" style="padding-right: 1rem;">{row.status_order}</td>
                        <td class="cot" style="padding-right: 1rem; text-align: right"><span class="auto_price">{row.total_order}</span></td>
                        <td class="cot" style="padding-right: 1rem; text-align: right"><span class="auto_price">{row.total_order_after_promotion}</span></td>
                        <td class="cot" style="padding-right: 1rem; text-align: right"><span class="auto_price">{row.deeplink_total}</span></td>
                        <td class="cot" style="padding-right: 1rem; padding-left: 1rem;">{row.commission_status}</td>
                        <td class="cot" align="right">{row.date_create}</td>
                        <td class="cot" align="right"><a href="{row.link}" target="_blank" class="btn btn-primary">{LANG.user.detail}</a></td>
                    </tr>
                    <!-- END: row_item -->
                    <!-- END: show_detail_order -->
                    <!-- BEGIN: row_empty -->
                    <tr>
                        <td class="cot cot_empty" align="center" colspan="10">{row.mess}</td>
                    </tr>
                    <!-- END: row_empty -->
                    </tbody>
                </table>
            </div>
            <div class="info_contributor text-right pt-4">
                <!-- BEGIN: bo -->
                <div class="total_contributor_all pb-2">{LANG.user.total_expected_commission}: <b class="auto_price">{data.total_expected_commission}</b></div>
                <!-- END: bo -->
                <div class="total_contributor_all">{LANG.user.total_commission_received}: <b class="auto_price">{data.total_commission_received}</b> </div>
            </div>
        </div>
        <div class="table_nav">{data.nav}</div>
        <input id="do_action" type="hidden" value="" name="do_action">
    </div>
</div>
<!-- END: referred_list_order -->

<!-- BEGIN: detail_order -->
<div class="box-manager">
    <div class="box-title">{data.page_title}</div>
    <div class="box-content" id="list_contributor">
        <div class="list_user_contributor manage_table">
            <div class="wrap_table">
                <table class="table manage-table">
                    <thead>
                    <tr>
                        <th class="cot" width="10%"></th>
                        <th class="cot" width="20%" style="font-weight: bold; padding-right: 1rem;">{LANG.user.product}</th>
                        <th class="cot" width="10%" style="font-weight: bold; padding-right: 1rem; text-align: right">{LANG.user.col_price}</th>
                        <th class="cot" width="10%" style="font-weight: bold; padding-right: 1rem; text-align: right">{LANG.user.col_quantity}</th>
                        <th class="cot" width="14%" style="font-weight: bold; padding-right: 1rem; text-align: right">{LANG.user.col_total}</th>
                        <th class="cot" width="12%" style="font-weight: bold; padding-right: 1rem; text-align: right">{LANG.user.minus_promotion}</th>
                        <th class="cot" width="12%" style="font-weight: bold; padding-right: 1rem; text-align: right">{LANG.user.percent_commission}</th>
                        <th class="cot" width="12%" style="font-weight: bold; padding-right: 1rem; text-align: right;">{LANG.user.commission}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <!-- BEGIN: row_item -->
                    <tr>
                        <td class="cot" style="padding-right: 1rem;"><img src="{row.picture}" alt="{row.title}"></td>
                        <td class="cot" style="padding-right: 1rem;">{row.include}<p>{row.title}</p></td>
                        <td class="cot" style="padding-right: 1rem; text-align: right"><span class="auto_price">{row.price_buy}</span></td>
                        <td class="cot" style="padding-right: 1rem; text-align: right">x{row.quantity}</td>
                        <td class="cot" style="padding-right: 1rem; text-align: right"><span class="auto_price">{row.into_money}</span></td>
                        <td class="cot" style="padding-right: 1rem; text-align: right">- <span class="auto_price">{row.price_minus}</span></td>
                        <td class="cot" style="padding-right: 1rem; text-align: right">{row.percent_deeplink}%</td>
                        <td class="cot" style="padding-right: 1rem; text-align: right"><span class="auto_price">{row.commission}</span></td>
                    </tr>
                    <!-- END: row_item -->
                    <!-- BEGIN: row_empty -->
                    <tr>
                        <td class="cot cot_empty" align="center" colspan="10">{row.mess}</td>
                    </tr>
                    <!-- END: row_empty -->
                    </tbody>
                </table>
            </div>
            <div class="note pt-4" style="color: #0057ff">{LANG.user.max_commission_per_item}: <b class="auto_price">{data.amount_deeplink_default}</b></div>
            <div class="info_contributor text-right pt-4">
                <div class="total_contributor_all pb-2">{LANG.user.total_order}: <b class="auto_price">{data.total_order}</b></div>
                <div class="total_contributor_all pb-2">{LANG.user.total_order} {LANG.user.after_promotion}: <b class="auto_price">{data.total_order_after_promotion}</b></div>
                <div class="total_contributor_all pb-2">{LANG.user.total_expected_commission}: <b class="auto_price">{data.deeplink_total}</b></div>
                <div class="total_contributor_all pb-2">{LANG.user.order_status}: <b>{data.order_status}</b></div>
                <div class="total_contributor_all pb-2">{LANG.user.commission_status}: <b>{data.commission_status}</b> </div>
            </div>
        </div>
        <div class="table_nav">{data.nav}</div>
        <input id="do_action" type="hidden" value="" name="do_action">
    </div>
</div>
<!-- END: detail_order -->