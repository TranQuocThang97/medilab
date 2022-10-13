<!-- BEGIN: main -->
{data.content}
<!-- END: main -->

<!-- BEGIN: list_item -->
<div class="list_item">
    <!-- BEGIN: row_item -->
    <div class="row_item {row.class}">
        <!-- BEGIN: col_item -->
        <div class="col_item {col.class}">
            <div class="img"><a href="{col.link}" title="{col.title}"><img src="{col.picture}" alt="{col.title}" title="{col.title}" /></a></div>
            <h3><a href="{col.link}" title="{col.title}">{col.title}</a></h3>
            <div class="short">{col.short}</div>
        </div>
        <!-- END: col_item -->
        <div class="clear"></div>
    </div>
    {row.hr}
    <!-- END: row_item -->
    <!-- BEGIN: row_empty -->
    <div class="row_empty">{row.mess}</div>
    <!-- END: row_empty -->
</div>
{data.nav}
<!-- END: list_item -->

<!-- BEGIN: item_detail -->
<div id="item_detail">
    <h1 class="page_title">{data.title}</h1>
    <div class="content">{data.content}</div>
</div>
<!-- END: item_detail -->

<!-- BEGIN: list_other -->
<div class="list_other">
    <div class="hr"></div>
    <div class="list_other-title">{LANG.page.other_page}</div>
    <ul class="list_none">
        <!-- BEGIN: row -->
        <li><a href="{row.link}" title="{row.title}">{row.title}</a></li>
        <!-- END: row -->
    </ul>
</div>
<!-- END: list_other -->