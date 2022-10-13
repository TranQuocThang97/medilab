<!-- BEGIN: main -->
<div class="user-manager" id="user-manager">
    <div id="ims-column_left">
        <div class="user-tool d-lg-none">
            <button class="btn user-toggler" type="button">
               <i class="fad fa-user-cog"></i> Menu user
            </button> 
        </div>
        <div id="box_menu_user">
            <button class="btn user-toggler d-lg-none" type="button">
               <i class="fad fa-user-cog"></i> Menu user
            </button> 
        {data.box_left}<!--/box-menu-->
        </div>
    </div>
    <div id="ims-content">
        {data.content}
    </div>
</div>
<script async="async">
    $(".user-toggler").on("click",function(){
        $("#box_menu_user").toggleClass("openside");
    })
</script>
<!-- END: main --> 


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
<div class="table-responsive manage">
  <table class="table manage-table">
    <thead>
      <tr >
        <th class="cot" width="10%"></th>
        <th class="cot">{LANG.user.col_title}</th>
        <th class="cot" width="15%">{LANG.user.col_price}</th>
        <th class="cot" width="10%">{LANG.user.col_quantity}</th>
        <th class="cot" width="15%">{LANG.user.col_total}</th>
      </tr>
    </thead>
    <tbody>
      {data.row_item}
      <!-- BEGIN: row_item -->
      <tr>
        <td class="cot" align="center"><img src="{row.picture}" alt="{row.title}"/></td>
        <td class="cot">{row.title} <p>{row.color_title}</p></td>
        <td class="cot" align="center">{row.price_buy}</td>
        <td class="cot" align="center">{row.quantity}</td>
        <td class="cot" align="center">{row.total}</td>
      </tr>
      <!-- END: row_item --> 
      <!-- BEGIN: row_empty -->
      <tr>
        <td align="center" colspan="5">{row.mess}</td>
      </tr>
      <!-- END: row_empty --> 
      <tr>
        <td class="cot" align="right" colspan="4">{LANG.user.cart_total}</td>
        <td class="cot" align="right">{data.cart_total}</td>
      </tr>
      <tr>
        <td class="cot" align="right" colspan="4">{LANG.user.promotion_code}</td>
        <td class="cot" align="right">-{data.promotion_price}</td>
      </tr>
      <tr>
        <td class="cot" align="right" colspan="4">{LANG.user.cart_payment}</td>
        <td class="cot" align="right">{data.total_payment}</td>
      </tr>
    </tbody>
  </table>
</div>
<!-- END: table_cart --> 

<!-- BEGIN: edit -->
<div class="ordering_address">
	<div class="ordering_address_l">   
  	<h3>{LANG.user.ordering_address}</h3>   
    <div class="row">
      <label class="title">{LANG.user.full_name} : </label>
      <label class="content">{data.o_full_name}</label>
      <div class="clear"></div>
    </div>
    <div class="row">
      <label class="title">{LANG.user.email} :</label>
      <label class="content">{data.o_email}</label>
      <div class="clear"></div>
    </div>
    <div class="row">
      <label class="title">{LANG.user.phone} :</label>
      <label class="content">{data.o_phone}</label>
      <div class="clear"></div>
    </div>
    <div class="row">
      <label class="title">{LANG.user.address} :</label>
      <label class="content">{data.o_address}</label>
      <div class="clear"></div>
    </div>
  </div>
  <div class="ordering_address_r">
  	<h3>{LANG.user.delivery_address}</h3>
    <div class="row">
      <label class="title">{LANG.user.full_name} : </label>
      <label class="content">{data.d_full_name}</label>
      <div class="clear"></div>
    </div>
    <div class="row">
      <label class="title">{LANG.user.email} :</label>
      <label class="content">{data.d_email}</label>
      <div class="clear"></div>
    </div>
    <div class="row">
      <label class="title">{LANG.user.phone} :</label>
      <label class="content">{data.d_phone}</label>
      <div class="clear"></div>
    </div>
    <div class="row">
      <label class="title">{LANG.user.address} :</label>
      <label class="content">{data.d_address}</label>
      <div class="clear"></div>
    </div>  
  </div>
  <div class="clear"></div>
</div>

{data.table_cart}

<div class="ordering_method">
  <h3>{LANG.user.ordering_method}</h3>
  <div class="row">
    <label class="title">{data.method.title}</label>
    <div class="content">{data.method.content}</div>
    <div class="clear"></div>
  </div>
</div>
<div class="clear"></div>
<div class="status_order">
	<h3>{LANG.user.status_order}</h3>
  <div class="content" style="background:{data.status_order.background_color};color:{data.status_order.color};">{data.status_order.title}</div>
</div>
<div class="clear"></div>
<div class="request_more">
	<h3>{LANG.user.request_more}</h3>
  <div class="content">{data.request_more}</div>
</div>
<!-- END: edit --> 

<!-- BEGIN: manage --> 
<div class="box-manager">
    <div class="box-title">{data.page_title}</div>
    <div class="box-content" id="user_contributor">
        <div class="panel panel-default panel_toggle {data.form_search_class}">            
            <div class="panel-body">                
                <div class="row">
                    <div class="col-md-5 col-12 px-2"><label>{LANG.user.date_begin}:</label> <input name="search_date_begin" type="text" size="20" maxlength="150" value="{data.search_date_begin}" class="form-control datepicker" placeholder="{LANG.global.date_begin}"></div>
                    <div class="col-md-5 col-12 px-2"><label>{LANG.user.date_end}:</label> <input name="search_date_end" type="text" size="20" maxlength="150" value="{data.search_date_end}" class="form-control datepicker" placeholder="{LANG.global.date_end}"></div>                    
                    <div class="col-md-2 col-12 col_search_btn px-2">                            
                        <button class="btn btn-default btn-block" type="submit">{LANG.global.btn_search}</button>
                    </div>
                </div>
            </div>
        </div>
        <!--<div class="export_excel"><a href="{data.link_export_excel}" target="_blank"><img src="{DIR_IMAGE}icon_excel.png" atl="Xuất file Excel" title="Xuất file Excel"/> Xuất file Excel theo điều kiện tìm kiếm</a></div>--> 
    {data.err}
        <div class="table-responsive manage">
            <table class="table manage-table">
                <thead>
                    <tr>
                        <th class="cot" width="3%">#</th>
                        <th class="cot" width="12%">Đơn hàng</th>
                        <th class="cot" width="10%">{LANG.user.exchange_type}</th>
                        <th class="cot" width="15%" style="text-align: right;">Giá trị</th>
                        <th class="cot" width="20%" style="text-align: right;">Điểm tích lũy/Điểm rút</th>
                        <th class="cot" width="15%" style="text-align: right;">Số dư cuối </th>
                        <th class="cot" width="15%" style="text-align: right;">{LANG.user.time}</th>
                    </tr>
                </thead>
                <tbody>
                    {data.row_item}
                    <!-- BEGIN: row_item -->
                    <tr>
                        <td class="cot">{row.stt}</td>
                        <td class="cot"><b style="color:#e00000;font-weight: normal;">#{row.dbtable_id}</b></td>
                        <td class="cot">{row.exchange_type}</td>
                        <td class="cot" align="right"><b>{row.total_payment}</b></td>
                        <td class="cot" align="right"><b>{row.plus_minus}<span class="">{row.value} điểm</span></b></td>
                        <td class="cot" align="right">{row.wcoin_after} điểm</td>
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
            <div class="info_contributor text-right">
                <div class="total_contributor_all">
                    Tổng điểm đã rút : <b class="auto_price text-danger">{data.total_wcoin_withdrawals}</b>
                </div>
              <!-- <div class="total_contributor_all">Tổng điểm đã mua hàng : <b class="auto_price">{total.total_wcoin_buy}</b></div> -->
              <div class="total_contributor_all">{LANG.user.total_wcoin} : <b>{data.user_wcoin} điểm</b> </div>
              <div class="total_contributor_all col-12">Số đơn hàng bạn đã mua trong tháng này {data.m_year} : <b class="auto_price">{data.count_neworder}</b> </div>
              <div class="total_contributor_all col-12">Số thành viên bạn đã giới thiệu trong tháng này {data.m_year}: <b class="auto_price">{data.count_newuser}</b> </div>
              <div class="total_contributor_all col-12">Số thành viên cấp con của bạn đã mua đơn hàng trong tháng này {data.m_year} : <b class="auto_price">{data.count_newbuy}</b> </div>
            </div>
        </div>        
        {data.nav}

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
    <!-- <div class="total_contributor_all">{LANG.user.total_commissions} : <b>{data.total_contributor_all}</b></div> --> 
    </div>
</div>
<br />
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
    <td class="cot" colspan="3" align="right">Tổng cộng: </td>
    <td class="cot" align="right"><b>{total.total_payment}</b></td>
    <td class="cot" align="right"><b>{total.user_wcoin} điểm</b></td>
    <!--<td class="cot" align="right"><b class="auto_price">{total.wcoin_buy}</b></td>-->
    <!--<td class="cot" align="right"><b class="auto_price">{total.wcoin_withdrawals}</b></td>-->
    <!--<td class="cot" align="right"><b class="auto_price">{total.total_wcoin_withdrawals}</b></td>-->
    <!--<td class="cot" align="right"><b class="auto_price">{total.total_wcoin_buy}</b></td>-->    
</tr>
<!-- END: row_item_total --> 
