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
            {data.content}   
            <!-- END: row -->
            <!-- BEGIN: empty -->
            {data.text}
            <!-- END: empty -->
        </div>
    </div>
</div>
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
<div class="col_item col-lg-3 col-md-4 col-6 {row.class}">
    <div class="item">
        <div class="img">
            <a href="{row.link}" title="{row.title}">
                <img class="lazy" data-src="{row.picture}" data-lazy="{row.picture}" alt="{row.title}" title="{row.title}" />
                <div class="on-ribbon {row.ribbon}">-{row.percent_discount}%</div>
            </a>
        </div>        
        <div class="info">           
            <h3 class="info-title"><a href="{row.link}" title="{row.title}">{row.title}</a></h3>
            <div class="info-price {row.info_price}">
                <div class="price {row.class_price}">{row.price0}</div>
                <div class="price_buy {row.class_price}">{row.price_buy}</div>
                <div class="add_favorite {row.added}" data-id={row.item_id}><i class="{row.class_favorite}"></i></div>
            </div>
            <!-- BEGIN:price_promotion -->
            <div class="info-price price_promotion">
                <div class="price {row.class_price}">{row.price_sale}</div> 
                <div class="price_buy {row.class_price}">{row.price_buy}</div>
                <div class="add_favorite {row.added}" data-id={row.item_id}><i class="{row.class_favorite}"></i></div>
            </div>  
            <!-- END:price_promotion -->
            <!-- BEGIN: rate_view -->
            <div class="rate">
                <!-- BEGIN: star -->
                    {row.average}
                <!-- END: star -->
                {row.num_rate}
            </div>    
            <!-- END: rate_view -->
            <div class="add_cart">
                <form action="{row.link_cart}" method="post" class="form_add_cart" {row.link_go}>
                    <input name="item_id" type="hidden" value="{row.item_id}" />
                    <input name="quantity" type="hidden" value="1" />                   
                    <button class="btn_add_cart_now {row.id_disable} css_bo" type="{row.type_btn}" {row.link_go}>
                       <i class="fad fa-shopping-basket"></i> {row.btn_add_cart}
                    </button>
                    <button class="btn_add_cart {row.id_disable} css_bo" type="{row.type_btn}" {row.link_go}>
                       {row.btn_order}
                   </button>
                </form>
            </div>
            <div class="info-promo">{row.short_promotion}</div>
        </div>
        
    </div>
</div>
<!-- END: mod_item -->