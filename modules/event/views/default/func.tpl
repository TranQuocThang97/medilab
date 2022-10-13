<!-- BEGIN: mod_item -->
<div class="col_item col-md-4 col-lg-3 col-6">
    <div class="item">
        <div class="img">
            <a href="{row.link}" title="{row.title}">
                <img class="lazyload" src="{row.loading}" data-src="{row.picture}" alt="{row.title}" />
            </a>
            <div class="add_favorite {row.added}" data-id={row.item_id}><i class="{row.i_favorite}"></i></div>
        </div>
        <div class="info">
            <div class="info-title"><a href="{row.link}" title="{row.title}">{row.title}</a></div>
            <div class="group_date_add">
                <div class="date_begin">{row.date_begin}</div>
                <div class="address" title="{row.address}">{row.address}</div>
            </div>
            <div class="event_owner">{row.event_owner}</div>
            <div class="num_follow"><img src="{row.rooturl}resources/images/use/user.svg" alt="user">{row.num_follow} {LANG.event.follow}</div>
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
<div class="list_item list_item_event" data-group="{data.cur_group}">
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
    //     imsEvent.load_more();
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