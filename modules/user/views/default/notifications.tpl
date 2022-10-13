<!-- BEGIN: main -->
<div class="user-manager" id="user-manager">
    <div id="ims-column_left">
        {data.box_left}
    </div>
    <div id="ims-content">
        {data.content}
    </div>
</div>
<script async="async">
    $(document).on("click", ".paginate button, .nav-tabs a", function(){
        var p = $(this).data("page"),
            type_of = $(this).attr("id");
            // loading('show');
        $.ajax({
            type: "POST",
            url: "{data.link_action}",
            data: {"f": "reload", "p": p, "type_of": type_of, "lang": lang}
        }).done(function (string) {
            var data = JSON.parse(string);
            // loading('hide');
            if(data.ok == 1) {
                $("#user_notification .tab-content").html(data.html);
            }
        });
        return false;
    })
    $(document).on("click", ".btn_read, #read_all", function(){        
        var id = $(this).data("id"),
            p = $(".pagecur").text(),
            type_of = $(".nav-tabs a.active").attr("id"),
            act = $(this).data("act");
            // loading('show');
        $.ajax({
            type: "POST",
            url: "{data.link_action}",
            data: {"f": "update", "id": id, "act": act, "p": p, "type_of": type_of, "lang": lang}
        }).done(function (string) {
            var data = JSON.parse(string);
            // loading('hide');
            if(data.ok == 1) {
                $("#user_notification .tab-content").html(data.html);
                $("#user_notification .nav-tabs").load(window.location.href + " #user_notification .nav-tabs>*",function(){
                    $("#user_notification .nav-link").removeClass("active");
                    $("#user_notification #"+type_of).addClass("active");
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: lang_js['aleft_title'],
                    html: data.mess,
                })
            }
        });
        return false;
    })
    $(document).on("click", ".btn_delete, #delete_all", function(){
        Swal.fire({
            title: lang_js_mod['user']['delete_warning'],
            text: lang_js_mod['user']['are_you_sure'],
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: lang_js_mod['user']['confirm'],
            cancelButtonText: lang_js_mod['user']['cancel'],
            reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    var id = $(this).data("id"),
                        p = $(".pagecur").text(),
                        type_of = $(".nav-tabs a.active").attr("id");
                        act = $(this).data("act");
                        // loading('show');
                    $.ajax({
                        type: "POST",
                        url: "{data.link_action}",
                        data: {"f": "delete", "id": id, "act": act, "p": p, "type_of": type_of, "lang": lang}
                    }).done(function (string) {
                        var data = JSON.parse(string);
                        // loading('hide');
                        if(data.ok == 1) {
                            $("#user_notification .tab-content").html(data.html);
                            $("#user_notification .nav-tabs").load(window.location.href + " #user_notification .nav-tabs>*",function(){
                                $("#user_notification .nav-link").removeClass("active");
                                $("#user_notification #"+type_of).addClass("active");
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: lang_js['aleft_title'],
                                html: data.mess,
                            })
                        }
                    });
                } else if( result.dismiss === Swal.DismissReason.cancel) {
                }

            })
        return false;
    })
</script>
<!-- END: main -->


<!-- BEGIN: manage -->
<div class="box-manager">
    <div class="box-title">{data.page_title}</div>
    <div class="box-content" id="user_notification">
        <form method="post" accept-charset="utf-8">
            <ul class="nav nav-tabs mb-0" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="general" data-toggle="tab" href="#general" role="tab" title="{LANG.user.noti_general}"><img src="{data.icon_general}" alt="{LANG.user.noti_general}"><span class="{data.general}"></span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="promotion" data-toggle="tab" href="#promo" role="tab" title="{LANG.user.noti_promo}"><img src="{data.icon_promo}" alt="{LANG.user.noti_promo}"><span class="{data.promo}"></span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="normal" data-toggle="tab" href="#normal" role="tab" title="{LANG.user.noti_normal}"><img src="{data.icon_normal}" alt="{LANG.user.noti_normal}"><span class="{data.normal}"></span></a>
                </li>
                <li class="dropdown-toggle setting" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span>{LANG.user.all}</span>
                    <i class="far fa-ellipsis-v"></i>
                </li>
                <div class="dropdown-menu box_action">
                    <div><button type="submit" name="read" value="1" class="no-btn">{LANG.user.check_read}</button></div>
                    <div><button type="submit" name="unread" value="1" class="no-btn">{LANG.user.check_unread}</button></div>
                    <div id="read_all" data-act="readed_all" data-id="0">{LANG.user.read_all}</div>
                    <div id="delete_all" data-act="delete_all" data-id="0">{LANG.user.delete_all}</div>
                </div>
            </ul>
            <div class="tab-content">
                {data.row_item}
                <!-- BEGIN:row -->
                <div class="row_notification {row.class}">
                    <div class="select">
                        <input type="checkbox" id="cd_{row.item_id}" value="{row.item_id}" name="selectid[]">
                        <label for="cd_{row.item_id}"></label>
                    </div>
                    <div class="icon"><img src="{row.icon}" alt="{row.type_of}"></div>
                    <div class="date">{row.time}</div>
                    <div class="title_notification">
                        <a href="{row.link}" class="content">{row.title}</a>
                    </div>
                    <div class="group_btn">
                        <!-- BEGIN: reading -->
                        <button type="button" data-act="readed" data-id="{row.item_id}" class="btn btn_read">{LANG.user.check_read}</button>
                        <!-- END: reading -->
                        <button type="button" data-act="delete" data-id="{row.item_id}" class="btn btn_delete">{LANG.user.delete}</button>
                    </div>
                </div>
                <!-- END:row -->
                <!-- BEGIN: empty -->
                <div class="empty bg-white px-3 py-5">{data.mess}</div>
                <!-- END: empty -->
                {data.nav}
            </div>        
        </form>
    </div>
</div>
<!-- END: manage -->

<!-- BEGIN: list_content -->
{data.row_item}
    <!-- BEGIN: empty -->
    <div class="empty bg-white px-3 py-5">{data.mess}</div>
    <!-- END: empty -->
{data.nav}
<!-- END: list_content -->

<!-- BEGIN: item_detail -->
<div class="box-manager">
    <div class="box-title">{data.page_title}</div>
    <div class="box-content" id="user_notification">
        <form method="post" accept-charset="utf-8">
            <ul class="nav nav-tabs mb-0" role="tablist">
               <li class="nav-item">
                    <a class="nav-link" id="general" data-toggle="tab" href="#general" role="tab" title="{LANG.user.noti_general}"><img src="{data.icon_general}" alt="{LANG.user.noti_general}"><span class="{data.general}"></span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {data.active_promo}" id="promotion" data-toggle="tab" href="#promo" role="tab" title="{LANG.user.noti_promo}"><img src="{data.icon_promo}" alt="{LANG.user.noti_promo}"><span class="{data.promo}"></span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {data.active_normal}" id="normal" data-toggle="tab" href="#normal" role="tab" title="{LANG.user.noti_normal}"><img src="{data.icon_normal}" alt="{LANG.user.noti_normal}"><span class="{data.normal}"></span></a>
                </li>
                <li class="dropdown-toggle setting" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span>{LANG.user.all}</span>
                    <i class="far fa-ellipsis-v"></i>
                </li>
                <div class="dropdown-menu box_action">
                    <div><button type="submit" name="read" value="1" class="no-btn">{LANG.user.check_read}</button></div>
                    <div><button type="submit" name="unread" value="1" class="no-btn">{LANG.user.check_unread}</button></div>
                    <div id="read_all" data-act="readed_all" data-id="0">{LANG.user.read_all}</div>
                    <div id="delete_all" data-act="delete_all" data-id="0">{LANG.user.delete_all}</div>
                </div>
            </ul>
            <div class="tab-content mt-3">
                <div id="item_detail">
                    <div class="item-title pb-3">
                        <div class="title">{data.title}</div>
                        <div class="date">{data.time}</div>
                    </div>
                    <div class="item-short my-3"><b>{data.short}</b></div>
                    <div class="item-content">{data.content}</div>
                    <!-- BEGIN: tags -->
                    <div class="tags">
                        <h3 class="title_tag">{LANG.service.tags}:</h3>
                        <ul class="list_none">
                            <!-- BEGIN: row -->
                            <li><a href="{row.tag_link}">{row.tag}</a></li>
                            <!-- END: row -->
                        </ul>
                    </div>
                    <!-- END: tags -->
                </div>
            </div>
        </form>
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