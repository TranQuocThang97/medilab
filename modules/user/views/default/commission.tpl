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
<div class="box-manager">
    <div class="box-title">{data.page_title}</div>
    <div class="box-content" id="user_contributor">
        <div class="panel panel-default panel_toggle {data.form_search_class}">
            <div class="panel-body">
                <form action="" method="get">
                    <div class="row">
                        <div class="col-md-5 col-12 px-2 mb-3 mb-md-0"><label>{LANG.user.date_begin}:</label> <input name="search_date_begin" type="text" size="20" maxlength="150" value="{data.search_date_begin}" class="form-control datepicker" placeholder="{LANG.user.date_begin}" autocomplete="off"></div>
                        <div class="col-md-5 col-12 px-2 mb-3 mb-md-0"><label>{LANG.user.date_end}:</label> <input name="search_date_end" type="text" size="20" maxlength="150" value="{data.search_date_end}" class="form-control datepicker" placeholder="{LANG.user.date_end}" autocomplete="off"></div>
                        <div class="col-md-2 col-12 col_search_btn px-2">
                            <button class="btn btn-default btn-block" type="submit">{LANG.global.btn_search}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!--<div class="export_excel"><a href="{data.link_export_excel}" target="_blank"><img src="{DIR_IMAGE}icon_excel.png" atl="Xuất file Excel" title="Xuất file Excel"/> Xuất file Excel theo điều kiện tìm kiếm</a></div>-->
        {data.err}
        <div class="manage">
            <div class="manage_table">
                <div class="wrap_table">
                    <table class="table manage-table">
                        <thead>
                        <tr>
                            <th class="cot" width="5%">{LANG.user.index}</th>
                            <th class="cot" width="11%">{LANG.user.order_code}</th>
                            <th class="cot" width="22%">{LANG.user.recommend_link}</th>
                            <th class="cot" width="16%">{LANG.user.buyer}</th>
                            <th class="cot" width="15%" style="text-align: right;">{LANG.user.total_payment}</th>
                            <th class="cot" width="15%" style="text-align: right;">{LANG.user.commission}</th>
                            <th class="cot" width="16%" style="text-align: right;">{LANG.user.time}</th>
                        </tr>
                        </thead>
                        <tbody>
                        {data.row_item}
                        <!-- BEGIN: row_item -->
                        <tr>
                            <td class="cot">{row.stt}</td>
                            <td class="cot" style="padding-right: 1rem;"><a {row.detail_order_link} target="_blank">#{row.order_code}</a></td>
                            <td class="cot" style="padding-right: 2rem;"><b class="text-ellipsis"><a href="{row.recommend_link}" target="_blank" title="{row.recommend_link}">{row.recommend_link}</a></b></td>
                            <td class="cot" style="padding-right: 1rem;">{row.full_name}</td>
                            <td class="cot" align="right"><b class="auto_price">{row.total_order}</b></td>
                            <td class="cot" align="right"><b>+ <span class="auto_price">{row.commission_add}</span></b></td>
                            <td class="cot" align="right">{row.date_create}</td>
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
            <div class="info_contributor text-right pt-3">
                <div class="total_contributor_all col-12">{LANG.user.total_commission_receive}: <b class="auto_price">{data.total_commissions}</b> </div>
                <div class="total_contributor_all col-12">{LANG.user.total_swap_commission}: <b>- <span class="auto_price">{data.total_swap_commmission}</span></b> </div>
                <div class="total_contributor_all col-12">{LANG.user.total_commission_withdraw}: <b>- <span class="auto_price">0</span></b> </div>
                <div class="total_contributor_all col-12">{LANG.user.total_commission_current}: <b class="auto_price">{data.user_commission}</b> </div>
            </div>
        </div>
        {data.nav}

        <!-- BEGIN: warning_wcoin -->
        <div class="warning_wcoin">
            <div class="block_l">
                <div class="wcoin_payment">
                    <form id="swap_commission" name="{data.form_id_pre}wcoin_payment" method="post">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    {LANG.user.note_swap_commission_form}
                                </div>
                                <div class="form_mess"></div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <input name="num_commission" type="number" value="" class="form-control" placeholder="{LANG.user.num_commission}" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <input type="submit" id="btn_update" class="btn btn-default btn_custom" value="{LANG.user.swap_button}" />
                                </div>
                            </div>
                        </div>
                    </form>
                    <script language="javascript">
                        imsUser.swap_commission('swap_commission');
                    </script>
                </div>
            </div>
            <div class="block_r">
                {LANG.user.note_swap_commission}
                <div class="title">{LANG.user.wcoin_proportion}<span><b> {data.wcoin2money}</b></span></div>
            </div>
            <div class="clear"></div>
        </div>
        <!-- END: warning_wcoin -->
    </div>
</div>
<br />
<script type="text/javascript">
    $('.text-ellipsis a').tooltip();
    $('.datepicker').datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd/mm/yy'
    });
</script>
<!-- END: manage -->
<!-- BEGIN: row_item_total -->
<tr id="row_total">
    <td class="cot" colspan="4" align="right">{LANG.user.amount}: </td>
    <td class="cot" align="right"><b class="auto_price">{total.total_order}</b></td>
    <td class="cot" align="right"><b>+ <span class="auto_price">{total.total_commissions}</span></b></td>
</tr>
<!-- END: row_item_total -->
