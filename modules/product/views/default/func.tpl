<!-- BEGIN: mod_item -->
<div class="col_item col-md-4 col-lg-3 col-6">
    <div class="item">
        <div class="img">
            <a href="{row.link}" title="{row.title}">
                <img class="lazyload" src="{row.loading}" data-src="{row.picture}" alt="{row.title}" />
            </a>
            <!-- BEGIn: promo_bo -->
            <a href="{row.promo_link}" class="on-promotion">{LANG.global.ticker_promo_title}</a>
            <!-- END: promo_bo -->
            <div class="add_favorite {row.added}" data-id={row.item_id}><i class="{row.i_favorite}"></i></div>
        </div>
        <div class="info">
            <div class="info-title"><a href="{row.link}" title="{row.title}">{row.title}</a></div>
            <div class="group_date_add">
                <div class="date_begin">{row.date_begin}</div>
                <div class="address" title="{row.address}">{row.address}</div>
            </div>
            <div class="event_owner">{row.event_owner}</div>
            <div class="num_follow"><img src="{row.rooturl}resources/images/use/user.svg" alt="user">{row.num_follow} {LANG.product.follow}</div>
            <!-- BEGIN: bo -->
            <div class="review-wrap">
                <!-- BEGIN: rate_view -->
                <div class="rate">
                    <!-- BEGIN: star -->
                        {row.average}
                    <!-- END: star -->
                </div>
                <!-- END: rate_view -->
            </div>
            <div class="price_discount">
                <div class="info-price {row.info_price}">
                    <div class="price_buy">{row.price_buy}</div>
                    <!-- BEGIN: price -->
                    <div class="price">{price}</div>
                    <!-- END: price -->
                    <!-- BEGIN: bo -->
                    <div class="add_favorite d-none {row.added}" data-id={row.item_id}><i class="{row.class_favorite}"></i> <span class="text d-md-none">{LANG.global.favorite_product}</span></div>
                    <!-- END: bo -->
                </div>
                {row.discount}
            </div>
            <div class="add_cart">
                <form action="" method="post" class="form_add_cart text-center" {row.link_go}>
                    <input name="item_id" type="hidden" value="{row.item_id}" />
                    <input name="option_id" type="hidden" value="{row.option_id}" />
                    <input name="quantity" type="hidden" value="1" />                   
                    <button class="bg-color border-color btn_add_cart css_bo {row.id_disable}" type="{row.type_btn}" {row.link_go}>
                       <i class="fad fa-shopping-basket mr-1"></i> {row.btn_add_cart}
                    </button>
                </form>
            </div>
            <div class="info-promo">{row.short_promotion}</div>
            <!-- END: bo -->
        </div>
    </div>
</div>
<!-- END: mod_item -->

<!-- BEGIN: mod_item_user -->
<div class="col_item {row.class}">
    <div class="item">
        <div class="img">
            <a href="{row.link}" title="{row.title}">
                <img class="lazyload" src="{row.loading}" data-src="{row.picture}" alt="{row.title}" />
                {row.discount}
            </a>
        </div>
        <div class="info">
            <div class="info-title"><a href="{row.link}"">{row.title}</a></div>

            <div class="info-price {row.info_price}">
                <!-- BEGIN: price -->
                <div class="price">{price}</div>
                <!-- END: price -->
                <div class="price_buy">{row.price_buy}</div>
            </div>
            <div class="review-wrap">
                <!-- BEGIN: rate_view -->
                <div class="rate">
                    <!-- BEGIN: star -->
                    {row.average}
                    <!-- END: star -->
                </div>
                <!-- END: rate_view -->
            </div>
        </div>
    </div>
</div>
<!-- END: mod_item_user -->

<!-- BEGIN: list_item -->
<div class="list_item list_item_product" data-group="{data.cur_group}">
    <!-- BEGIN: promotion -->
    <div class="promotion-box">
        <div class="title d-none">{data.cur_item.title}</div>
        <div class="content">{data.cur_item.content}</div>
    </div>
    <!-- END: promotion -->
    <div class="row_item {data.empty}">
        <!-- BEGIN: row_item -->
        {row.mod_item}
        <!-- END: row_item -->
    </div>
    <!-- BEGIN: row_empty -->
    <div class="row_empty">{row.mess}</div>
    <!-- END: row_empty -->
    <!-- BEGIN: viewmore_ajax -->
    <div class="list_view_more">
        <div class="btn_viewmore"><button {data.hide_view_more}>{data.view_more} <i></i></button></div>
        <input type="hidden" name="start" value="{data.start}">
        <input type="hidden" name="order_by" value="">
        <input type="hidden" name="sort" data-keyword="{data.keyword}" data-focus="{data.focus}" value="" data-province="{data.province}" data-typeshow="{data.type_show}">
    </div>
    <!-- END: viewmore_ajax -->
</div>
{data.nav}
<script async="async">
    // $('.btn_viewmore button').on('click', function (){
    //     imsProduct.load_more();
    // });
</script>
<!-- END: list_item -->

<!-- BEGIN: list_item_ajax -->
<!-- BEGIN: row_item -->
{row.mod_item}
<!-- END: row_item -->
<!-- BEGIN: row_empty -->
<div class="row_empty">{row.mess}</div>
<!-- END: row_empty -->
<!-- END: list_item_ajax -->



<!-- BEGIN: list_item_promotion_code -->
<div class="list_item">
    <label class="title">{data.title}</label>
    <!-- BEGIN: row_item -->
    <div class="item mb-3">
        <div class="wrap_item d-flex w-100 align-items-center">
            <div class="left flex-grow-1 pr-2" style="cursor: pointer;" data-fancybox data-src="#{row.promotion_code}">
                <div class="short_code">{row.short}</div>
                <div class="date_end">{row.date_end}</div>
            </div>
            <div class="apply"><button data-item="{row.promotion_id}" class="btn btn-primary btn-sm">{row.copy}</button></div>
        </div>
    </div>
    <div id="{row.promotion_code}" style="display: none; width: 100%; max-width: 300px;">
        <div class="promotion_info" style="">
            <div class="text-center"><label class="text-danger" style="font-size: 18px;"><b>{row.title}</b></label></div>
            <div class="promotion_id" style="padding: 10px 5px;"><span style="display: inline-block; width: 90px;">Mã:</span>{row.promotion_id}</div>
            <div class="date_end" style="background: #f1f1f1; padding: 10px 5px;"><span style="display: inline-block; width: 90px;">{LANG.product.hsd}:</span> {row.date_end}</div>
            <div class="short_code" style="padding: 10px 5px;">
                <p class="pb-3">Thông tin</p>
                <div>{row.short}</div>
            </div>
        </div>
    </div>
    <!-- END: row_item -->
</div>
<!-- END: list_item_promotion_code -->