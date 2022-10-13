<!-- BEGIN: mod_item -->
<div class="col_item col-lg-4 col-md-6 col-12 {row.class}">
    <div class="item">
        <div class="img">
            <a href="{row.link}" title="{row.title}"><img src="{row.picture}" alt="{row.title}" title="{row.title}" /></a>
            <div class="date">{row.day_update}<span>{row.month_update}</span></div>
        </div>
        <div class="info">
            <h3><a href="{row.link}" title="{row.title}" class="cut_tring">{row.title}</a></h3>            
            <div class="short">{row.short}</div>            
            <a href="{row.link}" class="view_detail"><span>{LANG.project.view_detail}<i class="fas fa-caret-right"></i></span></a>
        </div>    
    </div>
</div>
<!-- END: mod_item -->

<!-- BEGIN: mod_item_pic -->
<div class="col_item {row.class}">
    <div class="item">
        <div class="img"><a href="{row.link}" title="{row.title}"><img src="{row.picture}" alt="{row.title}" title="{row.title}" /></a></div>
        <div class="info">
            <h3 class="title"><a href="{row.link}" title="{row.title}" class="cut_tring">{row.title}</a></h3>        
            <div class="short">{row.short}</div>
        </div>
    </div>
</div>
<!-- END: mod_item_pic -->

<!-- BEGIN: mod_item_simple -->
<div class="col_item {row.class}">
    <div class="img col-md-4 col-12"><a href="{row.link}" title="{row.title}"><img src="{row.picture}" alt="{row.title}" title="{row.title}" /></a></div>
    <div class="info col-md-8 col-12">
        <a href="{row.link}" title="{row.title}" class="cut_tring">{row.title}</a>
        <div class="date"><i class="fad fa-calendar-alt"></i> {row.date_update} {row.my_update}</div>            
    </div>
</div>
<!-- END: mod_item_simple -->

<!-- BEGIN: mod_item_other -->
<div class="col_item col-lg-4 col-md-6 col-12 {row.class}">
    <div class="item">
        <div class="img">
            <a href="{row.link}" title="{row.title}"><img src="{row.picture}" alt="{row.title}" title="{row.title}" /></a>            
        </div>
        <div class="info">
            <h3><a href="{row.link}" title="{row.title}" class="cut_tring">{row.title}</a></h3>            
            <div class="short">{row.short2}</div>            
            <a href="{row.link}" class="view_detail"><span>{LANG.project.view_detail}</span></a>
        </div>    
    </div>
</div>
<!-- END: mod_item_other -->

<!-- BEGIN: list_item -->
<div class="list_item list_item_project">
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
        <!-- BEGIN: row_empty -->
        <div class="row_empty">{row.mess}</div>
        <!-- END: row_empty -->         
    </div>
    <!-- BEGIN: load_more -->
    <div class="viewmore text-center"><button class="btn_viewmore" data-start="{data.start}" data-group="{data.group_id}"><span>{LANG.project.view_detail}</span></button></div>
    <!-- END: load_more -->
</div>
{data.nav}
<!-- END: list_item --> 

<!-- BEGIN: list_item_other -->
<div class="list_other lazy" {data.bg}>
    <div class="container">
        <div class="list_other-title">
            <h3><span>{LANG.project.other_project}</span></h3>        
        </div>
        <div class="list_item list_item_project">
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