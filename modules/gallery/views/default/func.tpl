<!-- BEGIN: mod_item -->
<div class="col_item {row.class}">
    <!-- BEGIN: picture -->
    <div class="item" data-fancybox="gallery-{row.item_id}" data-src="{row.picture}" data-thumb="{row.picture}">
        <div class="img"><a href="javascript:void(0)" title="{row.title}"><img src="{row.picture}" alt="{row.title}" title="{row.title}" /></a></div>
        <div class="info">
            <div class="title">
                <div class="type">{row.type_name}</div>
                <h3>{row.title}</h3>
            </div>
        </div>
        <div class="list_img">
            <!-- BEGIN: child -->
            <a href="{col.src}" data-fancybox="gallery-{col.item_id}" data-thumb="{col.src}"></a>
            <!-- END: child -->
        </div>
    </div>
    <!-- END: picture -->
    <!-- BEGIN: video -->
    <div class="item">
        <div class="img"><a href="{row.link}" title="{row.title}" data-fancybox><img src="{row.picture}" alt="{row.title}" title="{row.title}" /></a></div>
        <div class="info">
            <div class="title">
                <div class="type">{row.type_name}</div>
                <h3>{row.title}</h3>
            </div>
        </div>
    </div>
    <!-- END: video -->
</div>
<!-- END: mod_item -->

<!-- BEGIN: mod_item_no_pic -->
<div class="col_item {row.class}">    
    <div class="info">
        <h3><a href="{row.link}" title="{row.title}" class="cut_tring">{row.title}</a></h3>
        <div class="date">{row.date_update}</div>
        <div class="short">{row.short}</div>
    </div>          
</div>
<!-- END: mod_item_no_pic -->

<!-- BEGIN: mod_item_simple -->
<div class="col_item {row.class}">
    <div class="item">
        <div class="img col-md-4 col-12"><a href="{row.link}" title="{row.title}"><img src="{row.picture}" alt="{row.title}" title="{row.title}" /></a></div>
        <div class="info col-md-8 col-12">
            <h3><a href="{row.link}" title="{row.title}" class="cut_tring">{row.title}</a></h3>
            <div class="date d-none"><i class="fad fa-calendar-alt"></i> {row.date_update} {row.my_update}</div>            
            <a href="{row.link}" class="more">{LANG.gallery.view_more}<i class="fas fa-caret-right"></i></a>
        </div>
    </div>
</div>
<!-- END: mod_item_simple -->


<!-- BEGIN: mod_item_title -->
<div class="col_item {row.class}">
    <a href="{row.link}" title="{row.title}" class="cut_tring">{row.title}</a>
</div>
<!-- END: mod_item_title -->


<!-- BEGIN: mod_item_home -->
<div class="col_item {row.class}">
    <div class="item">
        <div class="img"><a href="{row.link}" title="{row.title}"><img src="{row.picture}" alt="{row.title}" title="{row.title}" /></a></div>
        <div class="info">
            <div class="inner col-12">
                <div class="title"><a href="{row.link}" title="{row.title}" class="cut_tring">{row.title}</a></div>
                <div class="short">{row.short}</div>
            </div>
        </div>
    </div>
</div>
<!-- END: mod_item_home -->


<!-- BEGIN: list_item -->
<div class="list_item list_item_gallery">
    <div class="row_item {row.class}">
        <!-- BEGIN:row_item_first -->
        <div class="col_item first_big">
            <div class="item">
                <div class="img"><a href="{row.link}" title="{row.title}"><img src="{row.picture}" alt="{row.title}" title="{row.title}" /></a></div>
                <div class="info">
                    <h3><a href="{row.link}" title="{row.title}" class="cut_tring">{row.title}</a></h3>
                    <div class="date d-none"><i class="fad fa-calendar-alt"></i> {row.date_update} {row.my_update}</div>            
                    <div class="short">{row.short}</div>
                    <a href="{row.link}" class="more">{LANG.gallery.view_more}<i class="fas fa-caret-right"></i></a>
                </div>
            </div>
        </div>
        <!-- END:row_item_first -->
        <!-- BEGIN: row_item -->
        {row.mod_item}
        <!-- END: row_item --> 
        <!-- BEGIN: 2col -->
            <div class="col_l">
                <span class="logo">{row.gallery_logo}</span>
            <!-- BEGIN: left -->
                {row.mod_item}
            <!-- END: left -->
            </div>
            <div class="col_r">
                <span class="logo">{row.gallery_logo}</span>
            <!-- BEGIN: right -->
                {row.mod_item}
            <!-- END: right -->
            </div>
        <!-- END: 2col -->
        <!-- BEGIN: row_empty -->
        <div class="row_empty">{row.mess}</div>
        <!-- END: row_empty --> 
    </div>
</div>
{data.nav}
<!-- END: list_item --> 

<!-- BEGIN: list_item_other -->
<div class="list_other">
    <div class="container px-0">
        <div class="list_other-title">
            <span>{LANG.gallery.other_gallery}</span>
            <ul class="list_none slide-control">
              <li class="btn-arrow btn-prev"><i class="fas fa-caret-left"></i></li>
              <li class="btn-arrow btn-next"><i class="fas fa-caret-right"></i></li>
            </ul>
        </div>
        <div class="list_item list_item_gallery">
            <div class="row_item {row.class}">     
                <!-- BEGIN: row_empty -->
                <div class="row_empty">{row.mess}</div>
                <!-- END: row_empty -->    
                <!-- BEGIN: row_item -->
                {row.mod_item}
                <!-- END: row_item -->
            </div>
        </div>
    </div>
</div>
<!-- END: list_item_other -->