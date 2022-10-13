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
<div class="box_manager_gift">
    <div class="profile_title">
        <div class="title"><i class="fad fa-gifts"></i> {LANG.user.gift}</div>
        <div class="clear"></div>
    </div>
    <!-- BEGIN: row -->
    <div class="list_promo">
        <!-- BEGIN: col -->
        <div class="col_item">
            <div class="image col-md-4 col-12"><a href="{row.link}"><img src="{row.picture}" alt="{row.title}"></a></div>
            <div class="info col-md-8 col-12">
                <h3 class="title">{row.title}</h3>
                <div class="short">{row.short}</div>
            </div>
        </div>
        <!-- END: col -->
    </div>
    {data.nav}
    <!-- END: row -->
    <!-- BEGIN: empty -->
    {data.text}
    <!-- END: empty -->
</div>
<!-- END: manage -->

<!-- BEGIN: item_detail -->
<div id="gift_detail">
    <div class="item-info">
        <div class="image col-md-4 col-12"><a href="javascript:void(0)"><img src="{data.picture}" alt="{data.title}"></a></div>
        <div class="info col-md-8 col-12">
            <h3 class="title">{data.title}</h3>
            <div class="short">{data.short}</div>
        </div>
    </div>    
    <div class="list_gift {data.class}">
        <div class="level">{data.level_gift}</div>
        <div class="countclock">
            <span class="text">{data.ongoing_event}</span>
            <div class="clocke countdown">
                <div class="item">
                    <div>
                        <span class="days"></span>
                        <span class="timeday">{LANG.global.day}</span>
                    </div>
                </div>
                <div class="item">
                    <div>
                        <span class="hours"></span>
                        <span class="timeday">{LANG.global.hour}</span>
                    </div>
                </div>
                <div class="item">
                    <div>
                        <span class="minutes"></span>
                        <span class="timeday">{LANG.global.minute}</span>
                    </div>
                </div>
                <div class="item">
                    <div>
                        <span class="seconds"></span>
                        <span class="timeday">{LANG.global.second}</span>
                    </div>
                </div>
            </div>
        </div>
        <!-- BEGIN: mess -->
        <div class="mess">
            <h3>{LANG.user.list_gift_selected}</h3>
            <p><b>{LANG.user.confirm_code}</b></p>
            <!-- BEGIN: log -->
            {log.mess}
            <!-- END: log -->
            {data.content}
        </div>
        <!-- END: mess -->
        <!-- BEGIN: row -->
        <form method="post" id="form_gift" accept-charset="utf-8" data-max="{data.value}" data-end="{data.date_end}" data-promotion="{data.item_id}">
            <!-- BEGIN: col -->
            <div class="gift_item {row.class}">
                <input id="gift{row.item_id}" class="gift" type="checkbox" name="gift[]" value="{row.item_id}" data-end="{row.date_end}" data-name="{row.title}"/>            
                <label class="info" for="gift{row.item_id}" data-title="{row.title}">
                    <div class="image lazy" data-bg="url({row.picture})"></div>
                    <div class="title"><span>{row.title}</span></div>
                </label>
            </div>
            <!-- END: col -->
            <div class="confirm">
                <button type="{data.type}" class="btn"><i class="fad fa-box-check"></i> {LANG.user.confirm}</button>
            </div>
        </form>
        <script type="text/javascript">imsUser.confirm_gift("form_gift")</script>
        <!-- END: row -->
        <!-- BEGIN: empty -->
        {data.text}
        <!-- END: empty -->
    </div>
</div>

<!-- END: item_detail -->

<!-- BEGIN: box_menu -->
<div class="list_pro_info">
    <div class="list_pro_info_img"><img src="{data.picture}"/></div>
    <div class="list_pro_info_name">Hello, {data.name}<br/>
        <span>{data.group_name}</span></div>
</div>
<a class="add_classifieds">Đăng BĐS mới</a>
<div class="list_pro_g">
    <div class="box-content">

        <!-- BEGIN: menu_sub -->
        <ul class="list_none">
            {data.content}
            <!-- BEGIN: row -->
            <li class="{row.class_li} {row.class}"><a href="{row.link}" {row.attr_link}>{row.title}</a> {row.menu_sub}
            </li>
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
    <input name="{row.input_name}" type="checkbox" data-on='{row.title}' data-off='{row.title}'
           value="{row.value}" {row.checked} data-toggle="toggle">
</label>
<!-- END: row -->
<!-- END: checkbox_inline -->