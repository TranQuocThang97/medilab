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
{data.err}
<div class="box-manager">
  <div class="box-title">{data.page_title}</div>
  <div class="box-content" id="list_contributor">
    <div class="list_user_contributor">
      <form action="{data.link_action}" method="post" name="manage" id="manage">
        <div class="box_user">
            <div class="row_header">
              <div class="th">{LANG.user.index}</div>
              <div class="th">{LANG.user.user_contributor}</div>
              <div class="th">{LANG.user.gender}</div>
              <div class="th">{LANG.user.link_contributor}</div>
              <div class="th">{LANG.user.time}</div>
              <div class="th">{LANG.user.status}</div>
            </div>    
            <!-- BEGIN: row_item -->
            <div class="item {row.item_parent}">
                <div class="name {row.item_parent}" data-id="{row.user_id}" data-children="{row.children}"">
                    <span class="pic">{row.picture}</span> 
                    <span class="text">{row.full_name}</span> 
                    <span class="count">({row.count})</span> 
                    <span class="view"><a target="_blank" href="{row.link}">(Lịch sử giao dịch)</a></span>
                </div>
                <div class="box_children"></div>
            </div>
            <!-- END: row_item --> 
            <!-- BEGIN: row_empty -->
            <div class="warning">
              <td align="center" colspan="9">{row.mess}</td>
            </div>
            <!-- END: row_empty --> 
        </div>
        <div class="table_nav">{data.nav}</div>
        <input id="do_action" type="hidden" value="" name="do_action">  
      </form>
    </div>
  </div>
</div>
<script type="text/javascript">
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