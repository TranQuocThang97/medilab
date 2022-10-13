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
<div class="box_manager_favorite">
    <div class="list_item list_item_product">
        <div class="row_item">
            <!-- BEGIN: row -->
            <div class="col_item product_item">
                <div class="title {data.class_title}">{data.group_title}
                    <!-- <div class="favorite">
                        <div class="add_favorite {data.class}" data-id="{data.id}" data-mod="{data.mod}" data-act="1"><i class="{data.class_favorite}"></i></div>
                    </div> -->
                </div>
                {data.content}
                <div class="clear"></div>
            </div>
            <!-- END: row -->
            <!-- BEGIN: empty -->
            {data.text}
            <!-- END: empty -->
        </div>
    </div>
</div>   
<script type="text/javascript">
    imsUser.add_favorite();
</script>
<!-- END: manage --> 

<!-- BEGIN: box_menu -->
<div class="list_pro_info">
    <div class="list_pro_info_img"><img src="{data.picture}"/></div>
    <div class="list_pro_info_name">Hello, {data.name}<br />
        <span>{data.group_name}</span></div>
</div>
<a class="add_classifieds">Đăng BĐS mới</a>
<div class="list_pro_g">
    <div class="box-content"> 

        <!-- BEGIN: menu_sub -->
        <ul class="list_none">
            {data.content} 
            <!-- BEGIN: row -->
            <li class="{row.class_li} {row.class}" ><a href="{row.link}" {row.attr_link}>{row.title}</a> {row.menu_sub} </li>
            <!-- END: row -->
        </ul>
        <!-- END: menu_sub --> 
    </div>
</div>
<!-- END: box_menu --> 

<!-- BEGIN: checkbox_inline -->
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.0/css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.0/js/bootstrap-toggle.min.js"></script> 
<!-- BEGIN: row -->
<label class="checkbox-inline">
    <input name="{row.input_name}" type="checkbox"  data-on='{row.title}' data-off='{row.title}' value="{row.value}" {row.checked} data-toggle="toggle">
</label>
<!-- END: row --> 
<!-- END: checkbox_inline --> 

<!-- BEGIN: mod_item -->
<div class="box_favorite {row.class}">
    <div class="item">
        <div class="img">
            <a href="{row.link}" title="{row.title}"><img src="{row.picture}" alt="{row.title}" title="{row.title}" /></a>        
        </div>
        <div class="product-view-list">
            <div class="info_view">
                <a href="{row.link}"><div class="title">{row.title}</div></a>
                <div class="model"><b>SKU:</b> {row.item_code}</div>
                <div class="description">{row.short}</div>
                <div class="rate">
                    <!-- BEGIN: rate_view -->
                    <a href="{row.link}" title="{row.title}">
                        <div class="rate">
                            <!-- BEGIN: star -->
                                {row.average}
                            <!-- END: star -->
                            {row.num_rate}
                        <div class="clear"></div>
                        </div>
                    </a>
                    <!-- END: rate_view --> 
                </div>
            </div>
            <div class="cart_view">
                <div class="price_view"><p>{LANG.product.price_buy}:</p> {row.price_buy}</div>
                <form action="{row.link_cart}" method="post" class="form_add_cart" {row.link_go}>
                    <input name="item_id" type="hidden" value="{row.item_id}" />
                    <input name="quantity" type="hidden" value="1" />
                    <button href="{row.link}" title="Mua ngay" type="submit" class="btn_add_cart btn-cart-view">
                        <i class="ficon-cart-plus"></i><span>{LANG.product.btn_add_cart}</span>
                    </button>
                </form>
                <div class="sale"></div>
            </div>   
        </div>
        <div class="info">           
            <div class="info-title"><h3><a href="{row.link}" title="{row.title}">{row.title}</a></h3></div>
            <!-- BEGIN: rate -->
                <div class="rate">
                    <!-- BEGIN: star -->
                        {row.average}
                    <!-- END: star -->
                    {row.num_rate}
                <div class="clear"></div>
                </div>
            <!-- END: rate --> 
            <div class="info-price {row.info_price}">
                <div class="price_buy {row.class_price_buy}">{row.price_buy}</div>
                <div class="price {row.class_price}">{row.price}</div>                     
                <div class="on-ribbon {row.ribbon}"><span>-{row.percent_discount}%</span></div>
                <!-- BEGIN: is_new -->
                <div class="on-ribbon-left"><span>{LANG.product.new_ribbon}</span></div>
                <!-- END: is_new -->
            </div>
            <div class="add_cart d-none">
                <form action="{row.link_cart}" method="post" class="form_add_cart" {row.link_go}>
                    <input name="item_id" type="hidden" value="{row.item_id}" />
                    <input name="quantity" type="hidden" value="1" />                   
                    <button class="btn_add_cart {data.id_disable} css_bo" type="{data.type_btn}" {data.link_go}>
                       <i class='ficon-basket'></i> {LANG.product.btn_add_cart}
                   </button>
                </form>
            </div>
            <!-- BEGIN:price_promotion -->
            <div class="info-price promotion">
                <div class="price_l">
                    <div class="price {row.class_price}">{row.price}</div>
                    <div class="price_buy {row.class_price_buy}">{row.price_sale}</div>                    
                </div>
                <div class="price_r price_promotion">{LANG.product.rest}: {row.price_buy}</div>
                <div class="on-ribbon sale"><span>- {row.percent_discount}%</span></div>
                <div class="clear"></div>
            </div>
            <!-- END:price_promotion -->            
            <div class="clear"></div>
        </div>
    </div>
</div>
<!-- END: mod_item -->