<!-- BEGIN: main -->
{data.box_banner}
{data.content}
<!-- END: main -->

<!-- BEGIN: box_product_bo -->
<div class="box_mid">
    <!-- BEGIN: bo -->
    <div class="box_mid-title box_header_product">
        <div class="box_mid-sort">
            {data.sort_price}
            {data.sort_nature}
        </div>
        <div class="mobile_filter d-lg-none">
            {data.sort_mobile}
            <button data-fancybox data-src="#box_filter_left">{data.filter_title}</button>
        </div>
        <div class="filter">
            {data.sort}
        </div>
        <!-- BEGIN: hidden -->
        <h1 class="title">{data.title} <span class="total">{data.num_total}</span></h1>
        <!-- END: hidden -->
        {data.text_search}
        <div class="product_groups">{data.product_group}</div>
        {data.filter_product}
    </div>
    <!-- END: bo -->
    {data.list_group}
    <div class="title_sort">
        <div class="group_title"><h1>{data.title}</h1><p>{data.total}</p></div>
        <div class="list_sort">
            <!-- BEGIN: sort_title -->
            <span class="sort_title {data.hide_sort}">{LANG.product.filter_title_sm}:</span>
            <!-- END: sort_title -->
            {data.sort_price}{data.sort_brand}{data.box_nature_bo}{data.box_origin}{data.sort_mobile}
        </div>
    </div>
    <div id="scroll_to"></div>
    <div class="filter_product">
        {data.filter_product}
    </div>
    <div class="box_mid-content box_content_product" data-link="{data.link_product}">{data.content}</div>
    {data.watched}
    {data.is_new}
</div>
<script type="text/javascript">
    // imsOrdering.add_cart("form.form_add_cart");
    // matchHeight('.box_content_product .item .brand');
    // matchHeight('.box_content_product .item .info-title');
    // matchHeight('.box_content_product .item .price_discount');
    $(document).ready(function(){
        $(".title_sort").sticky({topSpacing:51});
        //$(".title_sort").sticky({topSpacing:0});
    });
</script>
<!-- END: box_product_bo -->

<!-- BEGIN: main_detail -->
<div class="nav">
    <div class="container">{data.nav}</div>
</div>
<div class="row">
    <div id="ims-content">{data.content}</div>
    <div id="ims-column">{data.box_column}</div>
    <div id="product_other">{data.other}</div>
</div>

<!-- END: main_detail -->

<!-- BEGIN: main_qna -->
<div class="row qna_wrapper">
    <div class="col-lg-9 col-md-10 col-12">
        {data.content}
    </div>
    <div class="col-product col-lg-3 col-md-2 col-12">
        {data.box_right}
    </div>
</div>
<!-- END: main_qna -->

<!-- BEGIN: block_column -->
{data.list_product_hot}
{data.list_news_related}
<!-- END: block_column -->

<!-- BEGIN: img_detail -->
<div id="img_detail" {row.class_detail}>
    <div id="gallery_slider" >
        <!-- BEGIN: vid1 -->
        <!-- BEGIN: youtube -->
        <a href="{row.youtube1}" rel="img_detail" {row.plugin}>
            <div class="item"><iframe src="https://www.youtube.com/embed/{row.code1}?controls=0&playlist={row.code1}&loop=1&autoplay=1&mute=1" width="745" height="465" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>
        </a>            
        <!-- END: youtube -->
        <!-- BEGIN: mp4 -->
        <a href="{row.mp41}" rel="img_detail" {row.plugin}>
            <div class="item">
                <video src="{row.mp41}" autoplay muted loop></video>
            </div>
        </a>
        <!-- END: mp4 -->
        <!-- END: vid1 -->       
        <!-- BEGIN: pic -->
        <a href="{row.src_zoom}" data-fancybox="detail">
            <div class="item" {row.color_id}><img id="pzoom_{row.pid}" src="{row.src}" alt="{row.title}" data-zoom-image="{row.src_zoom}"></div>
        </a>
        <!-- END: pic -->
        <!-- BEGIN: vid2 -->
        <!-- BEGIN: youtube -->
        <a href="{row.youtube2}" rel="img_detail" {row.plugin}>
            <div class="item"><iframe src="https://www.youtube.com/embed/{row.code2}?controls=0&playlist={row.code2}&loop=1" width="745" height="465" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>
        </a>            
        <!-- END: youtube -->
        <!-- BEGIN: mp4 -->
        <a href="{row.mp42}" rel="img_detail" {row.plugin}>
            <div class="item">
                <video src="{row.mp42}" muted loop></video>
            </div>
        </a>
        <!-- END: mp4 -->
        <!-- END: vid2 -->
    </div>
    <div id="gallery_slider_thumb">      
        <!-- BEGIN: vid_thumb1 -->
        <!-- BEGIN: youtube --> 
        <div class="item" data-type="{row.vid}">
            <div class="image">
                <img src="//i3.ytimg.com/vi/{row.code1}/hqdefault.jpg" alt="{row.title}">
            </div>
        </div>
        <!-- END: youtube --> 
        <!-- BEGIN: mp4 -->
        <div class="item" data-type="{row.vid}">
            <div class="image">
                <video src="{row.mp41}"></video>
            </div>
        </div>
        <!-- END: mp4 -->
        <!-- END: vid_thumb1 -->   
        <!-- BEGIN: pic_thumb -->
        <div class="item">
            <div class="image">
                <img src="{row.src_thumb}" alt="{row.title}">
            </div>
        </div>
        <!-- END: pic_thumb -->  
        <!-- BEGIN: vid_thumb2 -->
        <!-- BEGIN: youtube --> 
        <div class="item" data-type="{row.vid}">
            <div class="image">
                <img src="//i3.ytimg.com/vi/{row.code2}/hqdefault.jpg" alt="{row.title}">
            </div>
        </div>
        <!-- END: youtube --> 
        <!-- BEGIN: mp4 -->
        <div class="item" data-type="{row.vid}">
            <div class="image">
                <video src="{row.mp42}"></video>
            </div>
        </div>
        <!-- END: mp4 -->
        <!-- END: vid_thumb2 -->         
    </div>
    <div class="hotline_share">
        <div class="col-12 col-md-auto hotline"><span>{LANG.product.call_now}:</span><a href="tel:{CONF.hotline2}">{CONF.hotline2}</a></div>
        <div class="col-12 col-md-auto list_share">
            <a href="https://facebook.com/sharer/sharer.php?u={info.link_action}" style="background: #3753a1" target="_blank"><i class="fab fa-facebook-f"></i></a>
            <!-- <a href="https://twitter.com/home?status={info.link_action}" style="background: #009df8" target="_blank"><i class="fab fa-twitter"></i></a> -->
            <!-- <a href="https://pinterest.com/pin/create/button/?url=&media=&description={info.link_action}" style="background: #e60023" target="_blank"><i class="fab fa-pinterest-p"></i></a> -->
            <a href="https://www.instagram.com/?url={info.link_action}" style="background: #366d9d" target="_blank"><i class="fab fa-instagram"></i></a>
            <!-- <a href="https://www.linkedin.com/shareArticle?mini=true&url=&title=&summary=&source={info.link_action}" style="background: #0074ac" target="_blank"><i class="fab fa-linkedin-in"></i></a> -->
            <a class="zalo-share-button" data-href="" data-oaid="579745863508352884" data-layout="2" data-color="blue" data-customize="false" style="background: #03a5fa"></a>
        </div>
        <div class="col-12 col-md-auto add_favorite {info.added}" data-id={info.item_id}><i class="{info.i_favorite}"></i> <span class="text">{LANG.global.favorite_product}</span></div>
    </div>
</div>
<!-- BEGIN: bo -->
<span><i class="fal fa-search-plus"></i>{LANG.product.zoom_note}</span>
<!-- END: bo -->
<script src="https://sp.zalo.me/plugins/sdk.js"></script>
<script type="text/javascript">
    $("#gallery_slider").on("beforeChange",function(event, slick, currentSlide, nextSlide){
        $("#gallery_slider .slick-slide .item").each(function(){
            var isrc = $(this).find("iframe").attr("src");
            if(isrc){
                isrc = isrc.replace("&autoplay=1&mute=1", "");
                $(this).find("iframe").attr("src",isrc);
            }
            var vsrc = $(this).find("video");
            if(vsrc){
                vsrc.currentTime = 0;
                vsrc.trigger('load');
            }
        })
        var iframe = $("#gallery_slider .slick-slide").eq(nextSlide).find("iframe");
        if(iframe.length > 0){
            var src = iframe.attr("src");
            iframe.attr("src",src+'&autoplay=1&mute=1');
        }
        var video = $("#gallery_slider .slick-slide").eq(nextSlide).find("video");
        if(video.length > 0){
            video.trigger('play');
        }
    })
</script>
<!-- END: img_detail -->

<!-- BEGIN: detail -->
<div id="item_detail" data-id="{data.item_id}" data-tracking="{data.tracking_policy}" data-order="{data.order_out_stock}">
    <div class="info_left">
        {data.img_detail}
    </div>
    <div class="info_right">
        <div class="info_row_top">
            <div class="wrap_top">
                <h1 class="title_product">{data.title}</h1>
                <div class="brand_rate_favorite">
                    {data.brand_name}
                    <!-- BEGIN: rate -->
                    <div class="rate">
                        <!-- BEGIN: star -->{info.average}<!-- END: star -->{info.num}
                    </div>
                    <!-- END: rate -->                    
                </div>
                <div class="info_row info_sold mb-3 d-none" style="color: #6f6e6e;">{LANG.product.quantity_sold} {data.quantity_sold}</div>
                <div class="info_row info_price">
                    <span class="price_buy">{data.price_buy_text}</span>
                    <span class="price" {data.none}>{data.price_text}</span>
                    <span class="percent_discount" {data.none}>- <span class="percent">{data.percent_discount}</span>%</span>
                </div>                
            </div>            
            {data.short1}
            {data.short}
            <form id="form_add_cart" action="{data.link_cart}" method="post" class="form_add_cart">
                {data.version}
                <!-- BEGIN: quantity -->
                <div id="text_quantity">
                    <div class="label">{LANG.product.quantity}</div>
                    <div class="btn_grp">
                        <span class="btn_minus"><i class="fal fa-minus"></i></span>
                        <input name="quantity" type="number" value="1" min="1" max="{data.max_quantity}" class="quantity_text no-spinners"/>
                        <span class="btn_plus"><i class="fal fa-plus"></i></span>
                        <span class="num_qantity_by"></span>
                    </div>
                </div>
                <!-- END: quantity -->
                <div class="info_row info_row_btn">
                    <input name="item_id" type="hidden" value="{data.item_id}" />
                    <input name="option_id" type="hidden" value="{data.option_id}" />
                    <input name="title" type="hidden" value="{data.title}" />
                    <button class="btn btn-add-cart bg-color btn_add_cart_now {data.id_disable}" type="{data.type_btn}" {data.link_go}>
                        <span>{data.btn_add_cart}</span>
                    </button>
                    <button class="btn btn-add-cart bg-color btn_add_cart {data.id_disable}" type="{data.type_btn}" {data.link_go}>
                        <i class="fal fa-shopping-cart"></i><span>{data.btn_order}</span>
                    </button>
                </div>
            </form>
            {data.short2}
        </div>
        <div class="info_col_right">
            {data.gift_related_bo}
            {data.promotion_code}
            <!-- BEGIN: specifications -->
            <div class="info_specifications">
                <div class="specifications">{LANG.product.specifications}</div>
                <ul class="list_none">
                    <!-- BEGIN: spec_item -->
                    <li><div class="title">{nature.title}:</div><div class="content">{nature.content}</div></li>
                    <!-- END: spec_item -->
                </ul>
                <!-- BEGIN: detail_specifications -->
                <div class="detail_specifications"><a href="#specifications" data-fancybox="specifications">{LANG.product.detail_specifications}<i class="fas fa-caret-right" ></i></a></div>
                <!-- END: detail_specifications -->
                <div id="specifications" style="display:none">
                    <ul class="list_none">
                        <!-- BEGIN: spec_item_fcb -->
                        <li><div class="title">{nature.title}:</div><div class="content">{nature.content}</div></li>
                        <!-- END: spec_item_fcb -->
                    </ul>
                </div>
            </div>
            <!-- END: specifications -->
        </div>
    </div>
    {data.box_combo}
    <div class="info_left rate_cmt">
        <div class="box_content_rate">
            <div class="product-detail-content">
                <div class="detail_content_title">{LANG.product.detail_content_title}</div>
                <div class="article">
                    <div class="text"><article>{data.content}</article></div>
                    <div class="view_more"></div>
                </div>
            </div>
            <div class="product-detail-comment" id="comment_rate">
                <label class="title">{LANG.product.comment_rate}</label>
                {data.form_comment}
            </div>
        </div>
    </div>
    <div style="clear: both"></div>
</div>
<!-- BEGIN: bo -->
<div class="product-detail-content row">
    <div class="col-md-8 col-12">
        <div class="article">
            <div class="text"><article>{data.content}</article></div>
        </div>
    </div>
    <!-- BEGIN: bo -->
    <div class="col-md-4 col-12">
        {data.box_hot}
        <!-- BEGIN: natures -->
        <div class="specifications">
            <label>{LANG.product.specifications}</label>
            <ul class="list_none">
                <!-- BEGIN: row -->
                <li>
                    <span>{row.group_title}</span>
                    <span>{row.title}</span>
                </li>
                <!-- END: row -->
            </ul>
        </div>
        <!-- END: natures -->
    </div>
    <!-- END: bo -->
</div>
<div class="product-detail-comment" id="comment_rate">
    <label class="title">{LANG.product.comment_rate}</label>
    {data.form_comment}
    {data.form_rate}
</div>
<!-- END: bo -->
<div class="box_content_rate box_content_rate_mobile"></div>
{data.other}
{data.focus1_product}
<script type="text/javascript">
    imsUser.add_favorite();
    imsProduct.loadProductVersion();
    imsOrdering.add_cart("form.form_add_cart");

    if($('.product-detail-content .article .text').height() > 0){
        var h = $('.product-detail-content .article .text').height();
        if(h > 450){
            $('.product-detail-content .article .text').addClass('limit');
            $('.view_more').html('<a><span>{LANG.product.more}</span><i class="open"></i></a>');
        }
    }
    var index_view_more = $('.view_more').offset();
    $('.view_more a').on('click', function (){
        $('.product-detail-content .article .text').toggleClass('limit');
        $('.view_more').toggleClass('closes');
        $('.view_more a i').toggleClass('open');
        if($('.view_more a i').hasClass('open')){
            $('.view_more a span').text('{LANG.product.more}');
            $('html, body').animate({scrollTop: (index_view_more.top - 80)}, 600);
        }else{
            $('.view_more a span').text('{LANG.product.short_cut}');
        }
    });

    $(window).on('load', function (){
        var wrtop_h = $('#item_detail .wrap_top').innerHeight();
        var form_h = $('#form_add_cart').innerHeight();
        var bottom_h = 0;
        if ($('.product-detail-other').length){
            bottom_h += $('.product-detail-other').innerHeight();
        }
        if ($('.focus1_product').length){
            bottom_h += $('.focus1_product').innerHeight();
        }
        if ($('.brand_scroll').length){
            bottom_h += $('.brand_scroll').innerHeight();
        }
        if ($('footer').length){
            bottom_h += $('footer').innerHeight();
        }

        var col_right = $('#item_detail .info_right').offset(),
            col_right_height = $('#item_detail .info_right').innerHeight();

        var check = 1;
        if (window.matchMedia('(min-width: 993px)').matches) {
            $(document).scroll(function (){
                var y = $(this).scrollTop();
                if(y > (col_right.top + col_right_height) && check == 1){
                    $('#item_detail .wrap_top').sticky({
                        topSpacing: 61,
                        bottomSpacing: form_h+bottom_h+30
                    });
                    $('#form_add_cart').sticky({
                        topSpacing: wrtop_h+61,
                        bottomSpacing: bottom_h+30
                    });
                    check = 0;
                }else if(y < (col_right.top + col_right_height) && check == 0){
                    $('#item_detail .wrap_top').unstick();
                    $('#form_add_cart').unstick();
                    check = 1;
                }
            });
        }
    });
</script>
<!-- END: detail -->

<!-- BEGIN: promotion_code -->
<div class="promotion_code">
    <div class="list_code_title d-flex flex-wrap align-items-center pb-2">
        <div class="title mr-3 mb-2">{LANG.product.promotion_code}:</div>
        <div class="list_code d-flex flex-wrap align-items-center">
            <!-- BEGIN: title -->
            <div class="item mr-2 mb-2">{row.title}</div>
            <!-- END: title -->
            <a href="#show_promotion_code" data-fancybox><i class="fas fa-chevron-right"></i></a>
        </div>
    </div>
    <div class="list_item list_item_top">
        <div class="wrap_list">
            <!-- BEGIN: item -->
            <div class="item mb-3">
                <div class="wrap_item d-flex w-100 align-items-center">
                    <div class="left flex-grow-1 pr-2" style="cursor: pointer;" data-fancybox data-src="#{row.promotion_code}">
                        <div class="picture"><img src="{row.pic}" alt="{row.promotion_id}"></div>
                        <div class="promotion_info">
                            <div class="promotion_id">{row.promotion_id}</div>
                            <div class="text-dark">[{row.title}]</div>                            
                            <div class="date_end small">{LANG.product.hsd}: {row.date_end}</div>
                        </div>
                    </div>
                    <div class="copy"><button data-item="{row.promotion_id}" class="btn btn-primary btn-sm">{LANG.product.save_code}</button></div>
                </div>
                <div id="{row.promotion_code}" style="display: none; width: 100%; max-width: 300px;">
                    <div class="promotion_info" style="">
                        <div class="text-center"><label class="text-danger" style="font-size: 18px;"><b>{row.title}</b></label></div>
                        <div class="promotion_id" style="padding: 10px 5px;"><span style="display: inline-block; width: 150px;">Mã:</span>{row.promotion_id}</div>
                        <div class="date_end" style="background: #f1f1f1; padding: 10px 5px;"><span style="display: inline-block; width: 150px;">{LANG.product.hsd}:</span> {row.date_end}</div>
                        <div class="short_code" style="padding: 10px 5px;">
                            <p class="pb-3">Thông tin</p>
                            <div>{row.short}</div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END: item -->
            <div class="more"><a href="#show_promotion_code" data-fancybox><i class="fal fa-arrow-circle-right"></i><div>{LANG.product.more}</div></a></div>
        </div>
    </div>
    <div id="show_promotion_code" class="list_item" style="display: none">
        <!-- BEGIN: items -->
        <div class="item mb-3">
            <div class="wrap_item d-flex w-100 align-items-center">
                <div class="left flex-grow-1 pr-2">
                    <div class="short_code">{row.short}</div>
                    <div class="date_end">{LANG.product.hsd}: {row.date_end}</div>
                </div>
                <div class="copy"><button data-item="{row.promotion_id}" class="btn btn-primary btn-sm">{LANG.product.copy}</button></div>
            </div>
        </div>
        <!-- END: items -->
    </div>
</div>
<script>
    var arr_code = localStorage.getItem("saved_code")!==null?JSON.parse(localStorage.getItem("saved_code")):[];
    $('.promotion_code .copy button').on('click', function (){
        if (typeof(Storage) !== "undefined") {
            arr_code.push($(this).data('item'));
            localStorage.setItem("saved_code", JSON.stringify(arr_code));
        }else{
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val($(this).data('item')).select();
            document.execCommand("copy");
            $temp.remove();
        }
        $(this).text('{LANG.product.saved_code}');
        return_text($(this));
    });
    function return_text(select){
        setTimeout(function (){
            $(select).text('{LANG.product.save_code}');            
        }, 2000);
    }
</script>
<!-- END: promotion_code -->

<!-- BEGIN: list_watched -->
<div class="list_watched">
    <div class="list_watched-title"><span>{LANG.product.watched_product}</span></div>
    <div class="list_none">
        <!-- BEGIN: row -->
        <div class="col_item product_item">
            <div class="item">
                <div class="on-ribbon {row.ribbon}"><span>- {row.percent_discount}%</span></div>
                <div class="on-ribbon {row.sale}"><span></span></div>
                <div class="img">
                    <a href="{row.link}" title="{row.title}"><img src="{row.picture}" alt="{row.title}" title="{row.title}" /></a>

                    <a href="{row.picture_zoom}" class="fancybox ficon-zoom-in"></a>
                    <form action="{row.link_cart}" method="post" class="form_add_cart" {row.link_go}>
                        <input name="item_id" type="hidden" value="{row.item_id}" />
                        <input name="quantity" type="hidden" value="1" />
                        <button class="btn_add_cart ficon-cart-plus" type="submit" value="+" ></button>
                    </form>
                </div>
                <div class="info">
                    <div class="info-title"><h3><a href="{row.link}" title="{row.title}">{row.title}</a></h3></div>
                    <!-- BEGIN: rate -->
                    <a href="{row.link}" title="{row.title}">
                        <div class="rate">
                            <!-- BEGIN: star -->
                            {row.average}
                            <!-- END: star -->
                            {row.num_rate}
                            <div class="clear"></div>
                        </div>
                    </a>
                    <!-- END: rate -->
                    <div class="info-price">
                        <!-- BEGIN: price -->
                        <div class="price">{row.price}</div>
                        <!-- END: price -->
                        <div class="price_buy {row.class_price_buy}">{row.price_buy}</div>
                        <!-- BEGIN: price_promotion -->
                        <div class="price_buy promotion_p">{row.price_sale}</div>
                        <div class="price_promotion">{LANG.product.rest}: {row.price_buy}</div>
                        <!-- END: price_promotion -->
                    </div>
                </div>
                <div class="info_promotion">
                    {row.short_promotion}
                </div>
            </div>
        </div>
        <!-- END: row -->
    </div>
</div>
<!-- END: list_watched -->

<!-- BEGIN:list_related -->
<div class="list_related">
    <div class="title">{LANG.product.accessories_purchased}</div>
    <div class="list_related_cotent">
        <!-- BEGIN:row -->
        <div class="item_related">
            <div class="image"><a href="{row.link}"><img src="{row.src}" alt="{row.title}" title="{row.title}" /></a></div>
            <div class="info">
                <div class="title"><a href="{row.link}">{row.title}</a></div>
                <div class="price">{row.price_buy}</div>
            </div>
        </div>
        <!-- END:row -->
    </div>
</div>
<!-- END:list_related -->

<!-- BEGIN:nature -->
<div class="box_nature">
    <!-- BEGIN:group -->
    <div class="group_nature">
        <div class="title">{row_nature.title_group}</div>
        <div class="value">{row_nature.title}</div>
        <div class="clear"></div>
    </div>
    <!-- END:group -->
</div>
<!-- END:nature -->

<!-- BEGIN:nature_focus -->
<div class="box_nature_focus">
    <!-- BEGIN:group -->
    <div class="group_nature">
        <div class="title">{row_nature.title_group}</div>
        <div class="value">{row_nature.title}</div>
        <div class="clear"></div>
    </div>
    <!-- END:group -->
</div>
<!-- END:nature_focus -->

<!-- BEGIN: box_right -->
<div class="item">
    <a href="{data.link}">
		<span class="image">
			<img class="img-responsive" src="{data.picture}">
		</span>
        <span class="title">{data.title}</span>
    </a>
    <!-- BEGIN: rate -->
    <div class="rate">
        <!-- BEGIN: star -->
        {info.average}
        <!-- END: star -->
        {info.num_rate}
    </div>
    <!-- END: rate -->
    <div class="info_price">
        <span class="price_buy">{data.price_buy}</span>
        <!-- BEGIN: info_row_price -->
        <span class="price">{price.price}</span>
        <!-- END: info_row_price -->
    </div>
</div>
<!-- END: box_right -->


<!-- BEGIN: version -->
<div class="info_row info_version info_version_data" id="{data.selector}">
    <div class="d-flex align-items-center">
        <div class="label">{data.title}</div>
        <ul class="list_none">
            <!-- BEGIN: row -->
            <li>
                <input type="radio" name="{row.group_name}" id="{row.group_name}-{row.group_id}-{row.id}" value="{row.data_value}" data-option="{row.data_option}" {row.data_color} {row.active} {row.disabled}>
                <label for="{row.group_name}-{row.group_id}-{row.id}" class="{row.class} {row.active} {row.disabled}">
                    <div>{row.title}</div>
                    <i class="tick">
                        <svg enable-background="new 0 0 12 12" viewBox="0 0 12 12" x="0" y="0" class="icon-tick-bold"><g><path d="m5.2 10.9c-.2 0-.5-.1-.7-.2l-4.2-3.7c-.4-.4-.5-1-.1-1.4s1-.5 1.4-.1l3.4 3 5.1-7c .3-.4 1-.5 1.4-.2s.5 1 .2 1.4l-5.7 7.9c-.2.2-.4.4-.7.4 0-.1 0-.1-.1-.1z"></path></g></svg>
                    </i>
                </label>
            </li>
            <!--  END: row-->
        </ul>
    </div>
</div>
<!-- END: version -->

<!-- BEGIN: version_ajax -->
<div class="d-flex align-items-center">
    <div class="label">{data.title}</div>
    <ul class="list_none">
        <!-- BEGIN: row -->
        <li>
            <input type="radio" name="{row.group_name}" id="{row.group_name}-{row.group_id}-{row.id}" value="{row.data_value}" data-option="{row.data_option}" {row.data_color} {row.active} {row.disabled}>
            <label for="{row.group_name}-{row.group_id}-{row.id}" class="{row.class} {row.active} {row.disabled}">
                <div>{row.title}</div>
                <i class="tick">
                    <svg enable-background="new 0 0 12 12" viewBox="0 0 12 12" x="0" y="0" class="icon-tick-bold"><g><path d="m5.2 10.9c-.2 0-.5-.1-.7-.2l-4.2-3.7c-.4-.4-.5-1-.1-1.4s1-.5 1.4-.1l3.4 3 5.1-7c .3-.4 1-.5 1.4-.2s.5 1 .2 1.4l-5.7 7.9c-.2.2-.4.4-.7.4 0-.1 0-.1-.1-.1z"></path></g></svg>
                </i>
            </label>
        </li>
        <!--  END: row-->
    </ul>
</div>
<!-- END: version_ajax -->

<!-- BEGIN: list_other -->
<div class="product-detail-other">
    <div class="other_title {data.empty}">
        <span>{LANG.product.other_product}</span>
        <!-- BEGIN: bo -->
        <ul class="list_none slide-control">
            <li class="btn-arrow btn-prev"><i class="fal fa-chevron-left"></i></li>
            <li class="btn-arrow btn-next"><i class="fal fa-chevron-right"></i></li>
        </ul>
        <!-- END: bo -->
    </div>
    {content}
</div>
<script>
    $(".product-detail-other .row_item").slick({
        autoplay: true,
        infinite: true,
        slidesToShow: 6,
        swipeToSlide: true,
        dots: false,
        arrows: true,
        lazyload: "ondemand",
        responsive: [{
            breakpoint: 1251,
            settings: {
                slidesToShow: 5,
            }
        }, {
            breakpoint: 1101,
            settings: {
                slidesToShow: 4,
            }
        }, {
            breakpoint: 769,
            settings: {
                slidesToShow: 3,
            }
        }, {
            breakpoint: 601,
            settings: {
                slidesToShow: 2,
            }
        }, {
            breakpoint: 365,
            settings: {
                slidesToShow: 1,
            }
        }]
    });
    //$(".product-detail-other .btn-prev").on("click",function(){o.slick("slickPrev");})
    //$(".product-detail-other .btn-next").on("click",function(){o.slick("slickNext");})
    // matchHeight('.product-detail-other .item .brand');
    // matchHeight('.product-detail-other .item .info-title');
</script>
<!-- END: list_other -->

<!-- BEGIN: box_combo -->
<div class="box_combo">
    <div class="include_combo">
        <div class="combo_box_title">{LANG.product.include_combo}</div>
        <div class="box_product_in_combo">{data.product_in_combo}</div>
    </div>
    <!-- BEGIN: gift_include -->
    <div class="box_gift_include">
        <div class="combo_title"><span>{data.combo_title}</span>{data.select_button}</div>
        <!-- BEGIN: list_gift -->
        <div class="row_item_gift">
            <!-- BEGIN: gift -->
            <div class="col_item col_gift">
                <a {gift.link} class="item">
                    <div class="img"><span><img src="{gift.picture}" alt="{gift.title}"></span></div>
                    <div class="info">
                        <div class="title">{gift.title}</div>
                    </div>
                </a>
            </div>
            <!-- END: gift -->
        </div>
        <!-- END: list_gift -->
        <!-- BEGIN: list_include -->
        <div class="list_item list_item_product">
            <div class="row_item">
                <!-- BEGIN: include -->
                {row.item_include}
                <!-- END: include -->
            </div>
        </div>
        <!-- END: list_include -->
        <!-- BEGIN: empty -->
        <div class="empty" style="text-align: center; padding: 10px 0;">{empty}</div>
        <!-- END: empty -->
    </div>
    <!-- END: gift_include -->
</div>
<div id="popup-select" style="display: none;">
    <div class="box-content"></div>
</div>
<script>
    $(".box_combo .box_product_in_combo .row_item").slick({
        arrows: !0,
        dots: !1,
        infinite: !1,
        autoplay: !1,
        autoplaySpeed: 3500,
        speed: 500,
        slidesToShow: 3,
        swipeToSlide: !0,
        lazyload:"ondemand",
        responsive: [{
            breakpoint: 901,
            settings: {
                slidesToShow: 4,
                slidesToScroll: 3,
                infinite: !0
            }
        }, {
            breakpoint: 769,
            settings: {
                slidesToShow: 3,
                slidesToScroll: 3,
                infinite: !0
            }
        }, {
            breakpoint: 601,
            settings: {
                slidesToShow: 2,
                slidesToScroll: 3,
                infinite: !0
            }
        }, {
            breakpoint: 365,
            settings: {
                slidesToShow: 1,
                slidesToScroll: 2,
                infinite: !0
            }
        }]
    });

    var t = 0;
    $(document).on("click", ".btn-combo", function(){
        var combo_id = $(this).attr("data-combo");
        loading('show');
        $.ajax({
            type: "POST",
            url: ROOT + "ajax.php",
            data: {"m": "product", "f": "load_{data.type}_combo", "combo_id": combo_id}
        }).done(function (string) {
            var data = JSON.parse(string);
            $("#popup-select .box-content").html(data.html);
            $.fancybox.open({
                src  : '#popup-select',
                type : 'inline',
                clickSlide : 'false',
                clickOutside : 'false',
                "touch" : false ,
                beforeClose : function(){
                    t = 0;
                    $("#ac").remove();
                },
            })
            loading('hide');
        });
    })

    $(document).on("change", "#popup-select input", function(){
        var m = parseInt($("#popup-select .check").attr("data-max"));
        if($(this).is(":checked")){
            t++;
        }else{
            t--;
        }
        $("#popup-select .check >span").text(t);
        if(t >= m){
            $("#popup-select input:checkbox:not(:checked)").attr("disabled","");
        }else{
            $("#popup-select input").removeAttr("disabled");
        }
    })
    $(document).on("click", ".btn-confirm", function(){
        var combo_id = $(this).attr("data-combo"),
            // link_go = $(this).attr("data-go"),
            selected = [];
        $("#popup-select").find("input:checked").each(function (i, ob) {
            selected.push($(ob).val());
        });
        $.ajax({
            type: "POST",
            url: ROOT + "ajax.php",
            data: {"m": "product", "f": "update_cart_combo", "data": selected, "combo_id": combo_id, "type":"{data.type}"}
        }).done(function (string) {
            var data = JSON.parse(string);
            if(data.ok == 1){
                Swal.fire({
                    icon: 'success',
                    title: lang_js['aleft_title'],
                    text: data.mess,
                });
                $.fancybox.close();
            }else{
                Swal.fire({
                    icon: 'error',
                    title: lang_js['aleft_title'],
                    text: data.mess,
                });
            }
        });
    })
</script>
<!-- END: box_combo -->

------------ Ajax_combo -----------------
<!-- BEGIN: list_gift_combo -->
<div class="list_item list_gift_combo">
    <h4>{LANG.product.list_gift_title}</h4>
    <div class="note">{note}</div>
    <div class="content">
        <!-- BEGIN: row -->
        <div class="item {row.disabled} {row.active}">
            <div class="checkbox">
                {row.input}
                <label for="cb_{row.item_id}"></label>
            </div>
            <div class="img">
                <img src="{row.picture}" alt="{row.title}">
            </div>
            <div class="info">
                <span class="type">{LANG.product.gift}</span>{row.title}
                <div class="info-price">
                    {row.price}
                    <div class="price_buy">{row.price_buy}</div>
                </div>
            </div>
        </div>
        <!-- END: row -->
    </div>
    <div class="confirm">
        <div class="check" data-max="{data.num_chose}">
            <!-- BEGIN: chose -->
            {LANG.product.list_chose_num} <span>0</span>/{data.num_chose}
            <!-- END: chose -->
        </div>
        <button class="btn btn-confirm" data-combo="{data.item_id}" data-type="{data.type}" data-go="{data.link_go}" {disable_button}>{LANG.product.btn_submit}</button>
    </div>
</div>
<!-- END: list_gift_combo -->

<!-- BEGIN: list_include_combo -->
<div class="list_item list_include_combo">
    <h4>{LANG.product.list_include_title}</h4>
    <div class="note">{note}</div>
    <div class="content">
        <!-- BEGIN: row -->
        <div class="item {row.disabled}">
            <div class="checkbox">
                {row.input}
                <label for="cb_{row.item_id}"></label>
            </div>
            <div class="img">
                <img src="{row.picture}" alt="{row.title}">
            </div>
            <div class="info">
                <span class="type">{LANG.product.include}</span>{row.title}
                <div class="info-price">
                    <div class="price {row.class_price}">{row.price}</div>
                    <div class="price_buy {row.class_price}">{row.price_buy}</div>
                </div>
            </div>
        </div>
        <!-- END: row -->
    </div>
    <div class="confirm">
        <div class="check" data-max="{data.num_chose}">
            <!-- BEGIN: chose -->
            {LANG.product.list_chose_num} <span>0</span>/{data.num_chose}
            <!-- END: chose -->
        </div>
        <button class="btn btn-confirm" data-combo="{data.item_id}" data-type="{data.type}" data-go="{data.link_go}" {disable_button}>{LANG.product.btn_submit}</button>
    </div>
</div>
<!-- END: list_include_combo -->
------------ Ajax_combo -----------------

<!-- BEGIN: combo -->
{data.banner}
{data.condition}
<div class="list_item">
    <div class="row">
        <!-- BEGIN: item -->
        <div class="item col-6">
            <div class="wrap_item">
                <div class="img"><a href="{row.link}"><img class="lazyload" src="{row.loading}" data-src="{row.picture}" alt="{row.title}" /></div>
                <div class="info">
                    <div class="time_application">{row.time_application}</div>
                    <div class="title"><a href="{row.link}">{row.title}</a></div>
                    <div class="price_info">
                        <div class="price_buy">{row.price_buy}</div>
                        {row.price}
                    </div>
                </div>
            </div>
        </div>
        <!-- END: item -->
    </div>
    <!-- BEGIN: empty -->
    <div class="empty" style="text-align: center; color: #FFF; padding-bottom: 1rem">{LANG.product.no_have_item}</div>
    <!-- END: empty -->
</div>
<!-- END: combo -->

<!-- BEGIN: list_group -->
<div class="list_group">
    <!-- BEGIN: item -->
    <div class="item">
        <!-- BEGIN: bo -->
        <div class="img">
            <a href="{row.link}"><img src="{row.picture}" alt="{row.title}"></a>
        </div>
        <!-- END: bo -->
        <div class="title"><a href="{row.link}">{row.hot}{row.title}</a></div>
    </div>
    <!-- END: item -->
</div>
<script>
    $(".list_group").slick({
        arrows: !0,
        dots: !1,
        infinite: !1,
        autoplay: !1,
        autoplaySpeed: 3500,
        speed: 500,
        slidesToShow: 9,
        slidesToScroll: 9,
        // swipeToSlide: !0,
        lazyload:"ondemand",
        responsive: [{
            breakpoint: 1367,
            settings: {
                slidesToShow: 8,
                slidesToScroll: 8
            }
        }, {
            breakpoint: 1201,
            settings: {
                slidesToShow: 6,
                slidesToScroll: 6
            }
        }, {
            breakpoint: 801,
            settings: {
                slidesToShow: 5,
                slidesToScroll: 5
            }
        }, {
            breakpoint: 601,
            settings: {
                slidesToShow: 4,
                slidesToScroll: 4
            }
        }, {
            breakpoint: 401,
            settings: {
                slidesToShow: 3,
                slidesToScroll: 3
            }
        }]
    });
</script>
<!-- END: list_group -->


<!-- BEGIN: combo_condition -->
<!-- BEGIN: title -->
<div class="combo_condition_title"><span>{row.content}</span></div>
<!-- END: title -->
<div class="combo_condition_content">
    <div class="wrap">
        <!-- BEGIN: item -->
        <div class="item">
            <div class="title"><span class="stt">{row.stt}</span><span class="text_title">{row.title}</span></div>
            <div class="content">{row.content}</div>
        </div>
        <!-- END: item -->
    </div>
</div>
<!-- END: combo_condition -->

<!-- BEGIN: header_page -->
{data.banner}
<div class="header_page">
    <div class="container">
        <!-- BEGIN: is_focus -->
        <div class="product_is_focus">
            <div class="bg">
                <div class="product_is_focus_title"><img src="{CONF.rooturl}resources/images/use/deal_price_left.png" alt="deal price left"><span>{LANG.product.shock_title}</span><img src="{CONF.rooturl}resources/images/use/deal_price_right.png" alt="deal price right"></div>
                {content}
            </div>
        </div>
        <!-- END: is_focus -->
        {data.list_product}
    </div>
</div>
<script>
    imsProduct.load_more_product_header();
</script>
<!-- END: header_page -->

<!-- BEGIN: list_product -->
    <!-- BEGIN: item_group -->
    <div class="tab_product">
        {data.slide}
        <!-- BEGIN: content_tab -->
        <ul class="nav nav-pills">
            <!-- BEGIN: li -->
            <li class="nav-item">
                <a href="#tab_{row.group_id}" data-toggle="tab" class="{row.active}">{row.title}</a>
            </li>
            <!-- END: li -->
        </ul>
        <div class="tab-content">
            <!-- BEGIN: content -->
            <div id="tab_{row.group_id}" class="tab-pane {row.active}">
                {row.content}
                {row.more}
            </div>
            <!-- END: content -->
        </div>
        <!-- END: content_tab -->
    </div>
    <!-- END: item_group -->
<!-- END: list_product -->

--------------------------- END ---------------------------

<!-- BEGIN: box_product -->
<div class="box_mid">
    <div class="title_sort">
        <div class="list_sort">
            <!-- BEGIN: sort_title -->
            <span class="sort_title {data.hide_sort}">{LANG.product.filter_title_sm}:</span>
            <!-- END: sort_title -->
            {data.sort_price}{data.sort_brand}{data.box_nature_bo}{data.box_origin}{data.sort_mobile}
        </div>
    </div>
</div>
<!-- END: box_product -->

<!-- BEGIN: product -->
<div class="list_product box_item">
    <h1 class="title">{LANG.product.mod_title}</h1>
    <div class="header_content">
        <div class="search">
            <form action="" method="get">
                <div class="form-group"><input type="text" name="keyword" placeholder="{LANG.product.text_keyword_product}"></div>
                <div class="form-group">
                    <select name="status" id="status">
                        <option value="">Trạng thái sản phẩm</option>
                        <option value="">Chưa kích hoạt</option>
                        <option value="">Đã kích hoạt</option>
                        <option value="">Chưa hiển thị</option>
                        <option value="">Đã hiển thị</option>
                        <option value="">Đã xóa</option>
                    </select>
                </div>
                <div class="form-group"><button type="submit">{LANG.product.search}</button></div>
                <div class="form-group"><div class="reset"><img src="{CONF.rooturl}resources/images/use/reset.svg" alt="reset"></div></div>
            </form>
        </div>
        <ul class="list_none list_action_root">
            <li><button class="delete">{LANG.product.delete}</button></li>
            <li><button class="show">{LANG.product.show_button}</button></li>
            <li><button class="hide">{LANG.product.hide_button}</button></li>
            <li><button class="default active"><a href="{add_link}"><i class="fal fa-plus-circle"></i>{LANG.product.add_button}</a></button></li>
        </ul>
    </div>
    <div class="wrap_table">
        <table class="table-responsives">
            <thead>
            <tr>
                <th scope="col" width="88"><div class="checkbox"><input type="checkbox" class="check_all" id="check_all"><label for="check_all"></label></div></th>
                <th scope="col" width="100">{LANG.product.picture}</th>
                <th scope="col" width="140">{LANG.product.sku}</th>
                <th scope="col" width="auto">{LANG.product.product_title}</th>
                <th scope="col" width="96">{LANG.product.status}</th>
                <th scope="col" width="190">{LANG.product.update_time}</th>
                <th scope="col" width="150">{LANG.product.action}</th>
            </tr>
            </thead>
            <tbody>
            <!-- BEGIN: item -->
            <tr class="{row.item_id}">
                <td><div class="checkbox"><input type="checkbox" id="check_{row.stt}"><label for="check_{row.stt}"></label></div></td>
                <td><img src="{row.picture}" alt="{row.title}" width="60"></td>
                <td>{row.item_code}</td>
                <td>{row.title}</td>
                <td>
                    <ul class="list_action_status list_none">
                        <li><button class="active">{LANG.product.active}</button></li>
                        <li><button class="show">{LANG.product.show}</button></li>
                    </ul>
                </td>
                <td>{row.date_update}</td>
                <td>
                    <ul class="list_action list_none">
                        <li><button class="create_qr"><img src="{CONF.rooturl}resources/images/use/create_qr.svg" alt="create_qr"></button></li>
                        <li><button class="edit"><img src="{CONF.rooturl}resources/images/use/edit.svg" alt="edit"></button></li>
                        <li><button class="delete"><img src="{CONF.rooturl}resources/images/use/delete.svg" alt="delete"></button></li>
                    </ul>
                </td>
            </tr>
            <!-- END: item -->
            </tbody>
        </table>
        <!-- BEGIN: empty -->
        <div class="empty">{LANG.product.no_have_item}</div>
        <!-- END: empty -->
    </div>
    {nav}
</div>
<!-- END: product -->

<!-- BEGIN: add_product -->
<div class="add_product box_item">
    <h1 class="title">{LANG.product.add_product_title}</h1>
    <form action="" method="post">
        <div class="row">
            <div class="form-group col-12">{LANG.product.select_layout}</div>
            <div class="form-group col-12">
                <ul class="list_none list_layout">
                    <li>
                        <div class="item checked">
                            <div class="picture"><img src="{CONF.rooturl}resources/images/layout1.png" alt="layout1"></div>
                            <div class="checkbox">
                                <input type="radio" name="layout" value="1" id="layout1" checked>
                                <label for="layout1">{LANG.product.select_layout1}</label>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="item">
                            <div class="picture"><img src="{CONF.rooturl}resources/images/layout2.png" alt="layout2"></div>
                            <div class="checkbox">
                                <input type="radio" name="layout" value="2" id="layout2">
                                <label for="layout2">{LANG.product.select_layout2}</label>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="form-group col-12">
                <div class="input_picture">
                    <div class="input_title">{LANG.product.picture_input}</div>
                    <div class="input_view">
                        <input type="file" name="picture" id="photo-add" class="inputfile inputfile-1" accept="image/*" value="">
                        <div class="photo-input">
                            <!-- BEGIN: picture -->
                            <img src="{data.src}" alt="">
                            <input type="hidden" name="picture_available" value="{data.src_ori}">
                            <!-- END: picture -->
                        </div>
                        <label for="photo-add" class="add_photo"></label>
                    </div>
                </div>
            </div>
            <div class="form-group col-12">
                <div class="input_picture">
                    <div class="input_title">{LANG.product.list_picture_input}</div>
                    <div class="gallery-input">
                        <input type="file" name="arr_picture_tmp[]" id="gallery-photo-add" class="inputfile inputfile-1" multiple="" accept="image/*">
                        <label for="gallery-photo-add" class="add_photo"></label>
                        <!-- BEGIN: arr_picture -->
                        <div class="item-image">
                            <input type="hidden" name="arr_picture_available[]" value="{pic.src_ori}">
                            <img src="{pic.src}" data-file="" class="" title="Click to remove">
                            <span class="selFile"><i class="fa fa-times"></i></span>
                        </div>
                        <!-- END: arr_picture -->
                    </div>
                </div>
            </div>
            <div class="form-group col-12">
                <label class="input_title">{LANG.product.sku_label}</label>
                <input type="text" name="item_code" placeholder="{LANG.product.sku_input}">
            </div>
            <div class="form-group col-12">
                <label class="input_title">{LANG.product.title_label}</label>
                <input type="text" name="title" placeholder="{LANG.product.title_input}">
            </div>
            <div class="form-group col-12 col-md-6">
                <label class="input_title">{LANG.product.price_label}</label>
                <input type="number" min="0" name="price" placeholder="{LANG.product.price_input}">
            </div>
            <div class="form-group col-12 col-md-6">
                <label class="input_title">{LANG.product.volumn_label}</label>
                <input type="text" name="volumn" placeholder="{LANG.product.volumn_input}">
            </div>

            <div class="form-group col-12 col-md-6">
                <label class="input_title">{LANG.product.producer_label}</label>
                <div class="select">
                    <select name="producer" id="producer">
                        <option value="">{LANG.product.producer_select}</option>
                        <!-- BEGIN: producer -->
                        <option value="{concern.item_id}" {concern.producer_selected}>{concern.title}</option>
                        <!-- END: producer -->
                    </select>
                </div>
            </div>
            <div class="form-group col-12 col-sd-6 col-md-3 d-flex align-items-end">
                <div class="checkbox">
                    <input type="radio" name="is_show_producer" value="0" id="hide_producer">
                    <label for="hide_producer">{LANG.product.hide_producer_label}</label>
                </div>
            </div>
            <div class="form-group col-12 col-sd-6 col-md-3 d-flex align-items-end">
                <div class="checkbox">
                    <input type="radio" name="is_show_producer" value="1" id="show_producer" checked>
                    <label for="show_producer">{LANG.product.show_producer_label}</label>
                </div>
            </div>

            <div class="form-group col-12 col-md-6">
                <label class="input_title">{LANG.product.distributor_label}</label>
                <div class="select">
                    <select name="distributor" id="distributor">
                        <option value="">{LANG.product.distributor_select}</option>
                        <!-- BEGIN: distributor -->
                        <option value="{concern.item_id}" {concern.distributor_selected}>{concern.title}</option>
                        <!-- END: distributor -->
                    </select>
                </div>
            </div>
            <div class="form-group col-12 col-sd-6 col-md-3 d-flex align-items-end">
                <div class="checkbox">
                    <input type="radio" name="is_show_distributor" value="0" id="hide_distributor">
                    <label for="hide_distributor">{LANG.product.hide_distributor_label}</label>
                </div>
            </div>
            <div class="form-group col-12 col-sd-6 col-md-3 d-flex align-items-end">
                <div class="checkbox">
                    <input type="radio" name="is_show_distributor" value="1" id="show_distributor" checked>
                    <label for="show_distributor">{LANG.product.show_distributor_label}</label>
                </div>
            </div>

            <div class="form-group col-12">
                <label class="input_title">{LANG.product.sales_link}</label>
                <div class="list_input_sale">
                    <div class="item">
                        <div class="input_picture">
                            <input type="file" name="sale[{sale.index}][picture]" id="sale_picture{sale.index}" class="picture_sale inputfile inputfile-1" accept="image/*" value="">
                            <label for="sale_picture{sale.index}" class="add_photo"></label>
                        </div>
                        <input type="text" name="sale[{sale.index}][title]" placeholder="{LANG.product.sales_title_input} (*)">
                        <input type="text" name="sale[{sale.index}][link]" placeholder="{LANG.product.sales_link_input} (*)">
                        <input type="number" min="0" name="sale[{sale.index}][price]" placeholder="{LANG.product.sales_price_input} (*)">
                        <input type="number" min="0" name="sale[{sale.index}][price_sale]" placeholder="{LANG.product.sales_price_sale_input} (*)">
                        <div class="select">
                            <select name="sale[{sale.index}][channel]">
                                <option value="other">{LANG.product.other}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="text-right"><button class="add_sale_channel"><i class="fal fa-plus-circle"></i>{LANG.product.add_sale_channel}</button></div>
            </div>
            <div class="border"></div>
        </div>
    </form>
</div>
<script>
    $(document).on('change', 'input[name=layout]', function () {
        $('.list_layout li .item').removeClass('checked');
        $(this).parent().parent().addClass('checked');
    });
    // Hiển thị 1 ảnh khi click chọn thêm ảnh store
    var imagesPreviewOne = function(input, placeToInsertImagePreview){
        if (input.files) {
            $(placeToInsertImagePreview).html('').addClass('show');
            var filesAmount = input.files.length;
            for (i = 0; i < filesAmount; i++) {
                var reader = new FileReader();
                reader.onload = function(event) {
                    $($.parseHTML('<img>')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);
                }
                reader.readAsDataURL(input.files[i]);
            }
        }
    };
    $(document).on('change', '#photo-add', function() {
        imagesPreviewOne(this, 'div.photo-input');
    });
    $(document).on('change', '.picture_sale', function() {
        imagesPreviewOne(this, $(this).next());
    });
    // Hiển thị nhiều ảnh khi chọn arr_picture product
    var storedFiles = [];
    $(function() {
        var selDiv = "";
        $(document).on('change', '#gallery-photo-add', handleFileSelect);
        $(document).on("click", ".selFile", removeFile);
        function handleFileSelect(e) {
            selDiv = $(".gallery-input");
            var files = e.target.files;
            var filesArr = Array.prototype.slice.call(files);
            filesArr.forEach(function(f) {
                if(!f.type.match("image.*")) {
                    return;
                }
                storedFiles.push(f);
                var reader = new FileReader();
                reader.onload = function (e) {
                    var html = "<div class='item-image'><img src=\"" + e.target.result + "\" data-file='"+f.name+"' class='' title='Click to remove'><span class='selFile'><input name='arr_picture_name[]' value='"+ f.name +"' class='d-none'><i class='fa fa-times'></i></span></div>";
                    selDiv.append(html);
                }
                reader.readAsDataURL(f);
            });
        }

        function removeFile(e) {
            var file = $(this).find('input').val();
            for(var i=0; i<storedFiles.length; i++) {
                if(storedFiles[i].name === file) {
                    storedFiles.splice(i,1);
                    break;
                }
            }
            $(this).parent().remove();
        }
    });

    // Thêm sản phẩm
    $("#form_product").validate({
        submitHandler: function() {
            formData = new FormData($("#form_product")[0]);
            formData.append("f", "add_edit_product");
            formData.append("m", "user");
            formData.append("lang_cur", lang);
            for(var i=0, len=storedFiles.length; i<len; i++) {
                formData.append('arr_picture[]', storedFiles[i]);
            }
            loading('show');
            $.ajax({
                type: 'POST',
                url: ROOT+"ajax.php",
                data: formData,
                contentType: false,
                cache: false,
                processData:false,
            }).done(function( string ) {
                loading('hide');
                var data = JSON.parse(string);
                if(data.ok == 1) {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: data.mess,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: lang_js['aleft_title'],
                        html: data.mess,
                    })
                }
            });
            loading('hide');
            return false;
        },
        rules: {
            title: {
                required: true,
            },
            num_item: {
                required: true,
            },
            price: {
                required: true,
            }
        },
        messages: {
            title: lang_js['err_valid_input'],
            num_item: lang_js['err_valid_input'],
            price: lang_js['err_valid_input'],
        }
    });
</script>
<!-- END: add_product -->