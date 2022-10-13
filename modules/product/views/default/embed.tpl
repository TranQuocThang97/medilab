<!-- BEGIN: main -->
{data.content}
<!-- END: main -->

<!-- BEGIN: list_item -->
<div class="list_item">
    <div class="row_item">
    <!-- BEGIN: row -->
    {row.mod_item}
    <!-- END: row -->
    </div>
</div>
<!-- END: list_item -->

<!-- BEGIN: mod_item -->
<div class="col_item">
    <div class="item">
        <div class="img">
            <a href="{row.link}" target="_blank" title="{row.title}">
                <img src="{row.picture}" alt="{row.title}" />
            </a>
            {row.trogia}
        </div>        
        <div class="info">
            <div class="brand">{row.brand}</div>
            <div class="info-title"><a href="{row.link}" target="_blank">{row.title}</a></div>           
            <div class="price_discount">
                <div class="info-price {row.info_price}">
                    <div class="price_buy">{row.price_buy}</div>
                    <!-- BEGIN: price -->
                    <div class="price">{price}</div>
                    <!-- END: price -->
                </div>
                {row.discount}
            </div>
        </div>
    </div>
</div>
<!-- END: mod_item -->