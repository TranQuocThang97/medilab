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
                        <div class="col-md-5 col-12 px-2 mb-3 mb-md-0"><label>{LANG.user.date_begin}:</label> <input name="search_date_begin" type="text" size="20" maxlength="150" value="{data.search_date_begin}" class="form-control datepicker" placeholder="{LANG.global.date_begin}" autocomplete="off"></div>
                        <div class="col-md-5 col-12 px-2 mb-3 mb-md-0"><label>{LANG.user.date_end}:</label> <input name="search_date_end" type="text" size="20" maxlength="150" value="{data.search_date_end}" class="form-control datepicker" placeholder="{LANG.global.date_end}" autocomplete="off"></div>
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
                            <th class="cot" width="3%">{LANG.user.index}</th>
                            <th class="cot" width="12%">{LANG.user.order}</th>
                            <th class="cot" width="10%">{LANG.user.exchange_type}</th>
                            <th class="cot" width="20%" style="text-align: right;">{LANG.user.total_order_swap_commission}</th>
                            <th class="cot" width="15%" style="text-align: right;">{LANG.user.accumulation_use}</th>
                            <th class="cot" width="15%" style="text-align: right;">{LANG.user.final_surplus}</th>
                            <th class="cot" width="15%" style="text-align: right;">{LANG.user.time}</th>
                        </tr>
                        </thead>
                        <tbody>
                        {data.row_item}
                        <!-- BEGIN: row_item -->
                        <tr>
                            <td class="cot">{row.stt}</td>
                            <td class="cot"><b style="color:#e00000;font-weight: normal;">{row.order_code}</b></td>
                            <td class="cot">{row.exchange_type_text}</td>
                            <td class="cot" align="right"><b>{row.total_payment}</b></td>
                            <td class="cot" align="right"><b>{row.plus_minus} {row.value}</b></td>
                            <td class="cot" align="right">{row.wcoin_after} {LANG.user.wcoin}</td>
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
            {data.nav}
            <div class="info_contributor text-right">
                <div class="total_contributor_all">{LANG.user.total_wcoin_buy} : <b>- {data.total_wcoin_buy} {LANG.user.wcoin}</b></div>
                <div class="total_contributor_all">{LANG.user.total_wcoin_buy_not_complete} : <b>- {data.total_wcoin_buy_not_complete} {LANG.user.wcoin}</b></div>
                <div class="total_contributor_all">{LANG.user.total_wcoin} : <b>{data.user_wcoin} {LANG.user.wcoin}</b> </div>
                <!-- BEGIN: bo -->
                <div class="total_contributor_all col-12">{LANG.user.total_order_this_month} {data.m_year} : <b>{data.count_neworder}</b> </div>
                <div class="total_contributor_all col-12">Số thành viên bạn đã giới thiệu trong tháng này {data.m_year}: <b class="auto_price">{data.count_newuser}</b> </div>
                <div class="total_contributor_all col-12">Số thành viên cấp con của bạn đã mua đơn hàng trong tháng này {data.m_year} : <b class="auto_price">{data.count_newbuy}</b> </div>
                <!-- END: bo -->
            </div>
        </div>
        <!-- BEGIN: warning_wcoin -->
        <div class="warning_wcoin">
            <div class="block_l">
                <div class="wcoin_payment">
                    <form id="{data.form_id_pre}wcoin_payment" name="{data.form_id_pre}wcoin_payment" method="post">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    {LANG.user.note_wcoin_payment}
                                </div>
                                <div class="form_mess"></div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <input name="num_wcoin" type="text" value="" class="form-control" placeholder="{LANG.user.num_wcoin}"/>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <input name="bankcode" type="text" class="form-control" placeholder="{LANG.user.bankcode}" />
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <input name="bankname" type="text" value="" class="form-control" placeholder="{LANG.user.bankname}"/>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <input name="bankbranch" type="text" class="form-control" placeholder="{LANG.user.bankbranch}"/>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <input name="full_name" type="text" class="form-control" placeholder="{LANG.user.full_name}"/>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <input type="hidden" name="do_submit" value="1" />
                                    <input type="submit" id="btn_update" class="btn btn-default btn_custom" value="{LANG.user.wcoin_payment_money}" />
                                </div>
                            </div>
                        </div>
                    </form>
                    <script language="javascript">
                        imsUser.withdrawWcoin('{data.form_id_pre}wcoin_payment');
                    </script>
                </div>
            </div>
            <div class="block_r">
                {LANG.user.warning_wcoin}
                <div class="title">
                    {LANG.user.wcoin_expires}<span><b> {data.wcoin_expires}</b></span>
                </div>
                <div class="title">
                    {LANG.user.date_expires}<span> <b>{data.wcoin_dayexpired} {LANG.user.day}</b>.</span>
                </div>
                <div class="title">
                    {LANG.user.wcoin_proportion}<span><b> {data.wcoin2money}</b></span>
                </div>
            </div>
            <div class="clear"></div>
        </div>
        <!-- END: warning_wcoin -->
    </div>
</div>
<script type="text/javascript">
    $('.datepicker').datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd/mm/yy'
    });
</script>
<!-- END: manage -->

<!-- BEGIN: row_item_total -->
<tr id="row_total">
    <td class="cot" colspan="3" align="right">{LANG.user.amount}: </td>
    <td class="cot" align="right"><b class="auto_price">{total.total_payment}</b></td>
    <td class="cot" align="right"><b>{total.user_wcoin} {LANG.user.wcoin}</b></td>
    <!-- BEGIN: bo -->
    <td class="cot" align="right"><b class="auto_price">{data.wcoin_buy}</b></td>
    <td class="cot" align="right"><b class="auto_price">{data.wcoin_withdrawals}</b></td>
    <td class="cot" align="right"><b class="auto_price">{data.total_wcoin_withdrawals}</b></td>
    <td class="cot" align="right"><b class="auto_price">{data.total_wcoin_buy}</b></td>
    <!-- END: bo -->
</tr>
<!-- END: row_item_total -->
