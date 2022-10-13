<!-- BEGIN: mod_item -->
<div class="col_item {row.class}">
    <div class="item">
        <div class="img">
            <a href="{row.link}" title="{row.title}">
                <img src="{row.picture}" alt="{row.title}" title="{row.title}" />
            </a>
        </div>
        <div class="info">
            <div class="group_date">{row.group_name}<span class="date">{row.date_create}</span></div>
            <div class="title"><a href="{row.link}" title="{row.title}">{row.title}</a></div>
            <div class="short">{row.short}</div>
        </div>    
    </div>
</div>
<!-- END: mod_item -->

<!-- BEGIN: mod_item_other -->
<div class="col_item {row.class}">
    <div class="item">
        <div class="img">
            <a href="{row.link}" title="{row.title}">
                <img src="{row.picture}" alt="{row.title}" title="{row.title}" />
            </a>
        </div>
        <div class="info">
            <div class="group_date">{row.group_name}<span class="date">{row.date_create}</span></div>
            <div class="title"><a href="{row.link}" title="{row.title}">{row.title}</a></div>
            <div class="short">{row.short}</div>
        </div>
    </div>
</div>
<!-- END: mod_item_other -->

<!-- BEGIN: list_item -->
<div class="list_item list_item_news">
    <div class="row_item {row.class}">
        <!-- BEGIN:row_item_first -->
        <div class="row_item_first">
            <div class="image"><a href="{row.link}" title="{row.title}"><img src="{row.picture}" alt="{row.title}" title="{row.title}" /></a></div>
            <div class="group_info">
                <a href="{row.link}" title="{row.title}"><div class="title">{row.title}</div>
                <div class="short">{row.short}</div>
                </a>
            </div>
        </div>
        <!-- END:row_item_first -->
        <!-- BEGIN: row_item -->
        {row.mod_item}
        <!-- END: row_item -->
    </div>
    <!-- BEGIN: row_empty -->
    <div class="row_empty">{row.mess}</div>
    <!-- END: row_empty -->
</div>
{data.nav}
<!-- END: list_item -->