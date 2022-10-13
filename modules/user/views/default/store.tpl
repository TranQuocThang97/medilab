<!-- BEGIN: main -->
<div class="menu-page d-lg-none">
    <button><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></button>
    <label>{LANG.user.menu_user}</label>
</div>
<div class="user-manager" id="user-manager">
    <div id="ims-column_left">
        {data.box_left}
    </div>
    <div id="ims-content">
        {data.content}
    </div>
</div>
<script>
    $(document).ready(function() {
        $(window).keydown(function(e){
            if(e.keyCode == 13) {
                e.preventDefault();
                return false;
            }
        });
    });
    $(document).on("click", ".menu-page", function(){
        if($("#user-manager").hasClass("open")){
            $(".menu-page").removeClass("change");
            $("#user-manager").removeClass("open");
        }else{
            $(".menu-page").addClass("change");
            $("#user-manager").addClass("open");
        }
    });
</script>
<!-- END: main -->

<!-- BEGIN: top_page -->
<div class="top_page">
    <div class="left">
        <h1 class="title">{top_page.title}</h1>
        {top_page.box_search}
    </div>
    {top_page.right}
</div>
<div class="list_act"><a href="{top_page.list_link}" {top_page.list_cur}>{LANG.user.list}</a><a href="{top_page.trash_link}" {top_page.trash_cur}>{LANG.user.trash}</a></div>
<!-- END: top_page -->

<!-- BEGIN: box_search_top_page -->
<div class="box_search_top">
    <form action="" method="get">
        <div class="row">
            <div class="input_item">
                <div class="input_text">
                    <input type="text" name="keyword" value="{keyword}" placeholder="{LANG.user.keyword}" >
                    <img src="{CONF.rooturl}/resources/images/user/search.svg" alt="" class="search">
                </div>
            </div>
            <!-- BEGIN: price -->
            <div class="input_item">
                <div class="select">
                    <select name="price">
                        <!-- BEGIN: option -->
                            <option value="{option.value}" {option.selected}>{option.title}</option>
                        <!-- END: option -->
                    </select>
                </div>
            </div>
            <!-- END: price -->
            <div class="submit">
                {action}
                <button type="submit">{LANG.user.show}</button>
            </div>
        </div>
    </form>
</div>
<!-- END: box_search_top_page -->

<!-- BEGIN: right_top_page -->
<div class="right">
    <div class="button_scan"><a href="{data.scan_link}"><img src="{CONF.rooturl}resources/images/use/qr_scan.svg" alt=""></a></div>
    <div class="list_button">
        <!-- BEGIN: create_order -->
        <div class="item create_order"><a href="{data.create_order_link}"><img src="{CONF.rooturl}resources/images/use/new_page.svg" alt="">{LANG.user.create_order}</a></div>
        <!-- END: create_order -->
        {data.button2}
    </div>
</div>
<!-- END: right_top_page -->

<!-- BEGIN: store -->
<div class="list_store wrap_table">
    <table class="table table-responsives">
        <thead style="background: #E1E1E1;">
            <tr>
                <th scope="col" align="center" width="68%">{LANG.user.store_name}</th>
                <th scope="col" align="center" width="20%">{LANG.user.select_event}</th>
                <th scope="col" align="center" width="12%">{LANG.user.action}</th>
            </tr>
        </thead>
        <tbody>
            <!-- BEGIN: item -->
            <tr class="{row.item_id}">
                <td style="padding-left: 0">
                    <div class="store_item">
                        <div class="picture"><a href="{row.link}"><img src="{row.picture}" alt="{row.title}"></a></div>
                        <div class="info">
                            <div class="item_title"><a href="{row.link}">{row.title}</a></div>
                            <div class="num_product">{LANG.user.num_product} {row.num_product}</div>
                        </div>
                    </div>
                </td>
                <td><button class="select">{LANG.user.select}</button></td>
                <td>
                    <button id="btn{row.index}" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img src="{CONF.rooturl}resources/images/use/action.svg" alt="action">
                    </button>
                    <div class="dropdown-menu" aria-labelledby="btn{row.index}">
                        {row.action}
                    </div>
                </td>
            </tr>
            <!-- END: item -->
        </tbody>
    </table>
    <!-- BEGIN: empty -->
    <div class="empty">{LANG.user.no_have_item}</div>
    <!-- END: empty -->
    {nav}
</div>

<div class="modal fade" id="store" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal_title">{LANG.user.store}</div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <img src="{CONF.rooturl}resources/images/use/close_x.svg">
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="post" id="form_store">
                    <div class="list_input"></div>
                    <div class="list_button">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">{LANG.user.cancel}</button>
                        <button type="submit">{LANG.user.save}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade product" id="add_to_event" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal_title">{LANG.user.add_store_to_event}</div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <img src="{CONF.rooturl}resources/images/use/close_x.svg">
                </button>
            </div>
            <div class="modal-body">
                <div class="form_title">{LANG.user.chose_event_add_store}</div>
                <form action="" method="post" id="form_add_to_event">
                    <div class="list_input pt-0 pl-0 pr-0">
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <div class="search">
                                        <input type="text" name="search" id="search_event" placeholder="{LANG.user.find_event}">
                                        <img src="{CONF.rooturl}resources/images/user/search.svg" alt="search">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <div class="select">
                                        <select name="event_status" id="event_status">
                                            <option value="">{LANG.user.event_status}</option>
                                            <option value="0">{LANG.user.event_upcoming}</option>
                                            <option value="1">{LANG.user.event_ongoing}</option>
                                            <option value="2">{LANG.user.event_over}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="list_event wrap_table">
                            <table class="table table-responsives">
                                <thead style="background: #E1E1E1">
                                <tr>
                                    <th scope="col" align="center" width="83px">{LANG.user.select}</th>
                                    <th scope="col" align="center" style="padding-left: 0;">{LANG.user.event}</th>
                                    <th scope="col" align="center" width="120px" style="text-align: center;">{LANG.user.event_status}</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <input type="hidden" name="it" class="it" value="">
                    <div class="list_button">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">{LANG.user.cancel}</button>
                        <button type="submit">{LANG.user.save}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade product" id="add_product" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal_title">{LANG.user.add_product_to_store}</div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <img src="{CONF.rooturl}resources/images/use/close_x.svg">
                </button>
            </div>
            <div class="modal-body">
                <div class="form_title">{LANG.user.chose_product_add_store}</div>
                <form action="" method="post" id="form_add_product">
                    <div class="list_input pt-0 pl-0 pr-0">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <div class="search">
                                        <input type="text" name="search" id="search_product" placeholder="{LANG.user.find_product_store}">
                                        <img src="{CONF.rooturl}resources/images/user/search.svg" alt="search">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="list_event wrap_table">
                            <table class="table table-responsives">
                                <thead style="background: #E1E1E1">
                                <tr>
                                    <th scope="col" align="center" width="83px">{LANG.user.select}</th>
                                    <th scope="col" align="center" style="padding-left: 0;">{LANG.user.product}</th>
                                    <th scope="col" align="center" width="120px">{LANG.user.product_price_show}</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <input type="hidden" name="it" class="it" value="">
                    <div class="list_button">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">{LANG.user.cancel}</button>
                        <button type="submit">{LANG.user.save}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    // Load form store
    $(document).on('click', '.top_page .list_button .item.add_store', function () {
        loading('show');
        $.ajax({
            type: "POST",
            url: ROOT+"ajax.php",
            data: { "m" : "user", "f" : "form_store", 'lang_cur':lang}
        }).done(function( string ) {
            var data = JSON.parse(string);
            loading('hide');
            if(data.ok == 1) {
                $('#store form .list_input').html(data.html);
                $('#store').modal('show');
            }
        });
    });

    // Hiển thị 1 ảnh khi click chọn thêm ảnh store
    var imagesPreviewOne = function(input, placeToInsertImagePreview){
        if (input.files) {
            $(placeToInsertImagePreview).html("");
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
    // Thêm, sửa cửa hàng
    $("#form_store").validate({
        submitHandler: function() {
            formData = new FormData($("#form_store")[0]);
            formData.append("f", "add_edit_store");
            formData.append("m", "user");
            formData.append("lang_cur", lang);
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
                        timer: 2000
                    }).then(function() {
                            location.reload();
                        }
                    );
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: lang_js['aleft_title'],
                        html: data.mess,
                    })
                }
            });
            return false;
        },
        rules: {
            title: {
                required: true,
            }
        },
        messages: {
            title: lang_js['err_valid_input']
        }
    });

    // Sửa cửa hàng
    $(document).on('click', '.dropdown-menu li.edit', function () {
        var it = $(this).parent().parent().parent().attr('class');
        loading('show');
        $.ajax({
            type: "POST",
            url: ROOT+"ajax.php",
            data: { "m" : "user", "f" : "form_store", 'item':it, 'lang_cur':lang}
        }).done(function( string ) {
            var data = JSON.parse(string);
            loading('hide');
            if(data.ok == 1) {
                $('#store form .list_input').html(data.html);
                $('#store').modal('show');
            }
        });
    });

    // Xóa cửa hàng
    $(document).on('click', '.dropdown-menu li.delete', function () {
        Swal.fire({
            title: '{LANG.user.delete_store}',
            text: "{LANG.user.delete_store_confirm}",
            // icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#FE6505',
            cancelButtonColor: '#4A4647',
            cancelButtonText: '{LANG.user.cancel}',
            confirmButtonText: '{LANG.user.yes_delete}',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                var it = $(this).parent().parent().parent().attr('class');
                loading('show');
                $.ajax({
                    type: "POST",
                    url: ROOT+"ajax.php",
                    data: { "m" : "user", "f" : "delete_store", 'item':it, 'lang_cur': lang}
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
                        if(data.mess != ''){
                            Swal.fire({
                                icon: 'error',
                                title: lang_js['aleft_title'],
                                text: data.mess,
                            });
                        }
                    }
                });
            }
        })
    });

    // Khôi phục cửa hàng
    $(document).on('click', '.dropdown-menu li.restore', function () {
        Swal.fire({
            title: '{LANG.user.restore_store}',
            // icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#FE6505',
            cancelButtonColor: '#4A4647',
            cancelButtonText: '{LANG.user.cancel}',
            confirmButtonText: '{LANG.user.yes_restore}',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                var it = $(this).parent().parent().parent().attr('class');
                loading('show');
                $.ajax({
                    type: "POST",
                    url: ROOT+"ajax.php",
                    data: { "m" : "user", "f" : "restore_store", 'item':it, 'lang_cur': lang}
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
                        if(data.mess != ''){
                            Swal.fire({
                                icon: 'error',
                                title: lang_js['aleft_title'],
                                text: data.mess,
                            });
                        }
                    }
                });
            }
        })
    });

    // Thêm sản phẩm cửa hàng vào sự kiện
    $(document).on('click', '.list_store button.select', function () {
        var it = $(this).parent().parent().attr('class');
        loading('show');
        $('#add_to_event .it').val(it);
        load_event();
        $('#add_to_event').modal('show');
        loading('hide');
    });

    $("#add_to_event").on("hidden.bs.modal", function () {
        $('#add_to_event form .list_input table tbody').html('');
        $('#search_event').val('');
        $('#event_status').prop('selectedIndex', 0);
    });

    var timer = null;
    $(document).on('keyup', '#search_event', function() {
        clearTimeout(timer);
        timer = setTimeout(function() {
            load_event();
        }, 500);
    });

    $(document).on('change', '#event_status', function () {
        load_event();
    });

    function load_event() {
        var sort = $('#event_status').val(),
            keyword = $('#search_event').val();
        loading('show');
        $.ajax({
            type: "POST",
            url: ROOT+"ajax.php",
            data: { "m" : "user", "f" : "load_event_to_store", 'keyword': keyword, 'sort': sort, 'lang_cur': lang}
        }).done(function( string ) {
            var data = JSON.parse(string);
            loading('hide');
            if(data.ok == 1) {
                $('#add_to_event form .list_input table tbody').html(data.html);
            }else{
                Swal.fire({
                    icon: 'error',
                    title: lang_js['aleft_title'],
                    html: data.mess,
                });
            }
        });
    }

    $("#form_add_to_event").validate({
        submitHandler: function () {
            var fData = $("#form_add_to_event").serializeArray();
            loading('show');
            $.ajax({
                type: "POST",
                url: ROOT + "ajax.php",
                data: {"m": "user", "f": "add_store_to_event", "data": fData, "lang_cur": lang}
            }).done(function (string) {
                var data = JSON.parse(string);
                loading('hide');
                if (data.ok == 1) {
                    $('#add_to_event').modal('hide');
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: data.mess,
                        showConfirmButton: false,
                        timer: 2000
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: lang_js['aleft_title'],
                        html: data.mess,
                    });
                }
            });
            return false;
        }
    });

    // Thêm sản phẩm vào cửa hàng
    $(document).on('click', '.dropdown-menu li.add_product', function () {
        var it = $(this).parent().parent().parent().attr('class');
        loading('show');
        $('#add_product .it').val(it);
        load_product();
        $('#add_product').modal('show');
        loading('hide');
    });

    $(document).on('keyup', '#search_product', function() {
        clearTimeout(timer);
        timer = setTimeout(function() {
            load_product();
        }, 500);
    });

    function load_product() {
        var it = $('#add_product .it').val(),
            keyword = $('#search_product').val();
        loading('show');
        $.ajax({
            type: "POST",
            url: ROOT+"ajax.php",
            data: { "m" : "user", "f" : "load_product_to_store", 'keyword': keyword, 'it':it, 'lang_cur': lang}
        }).done(function( string ) {
            var data = JSON.parse(string);
            loading('hide');
            if(data.ok == 1) {
                $('#add_product form .list_input table tbody').html(data.html);
            }else{
                Swal.fire({
                    icon: 'error',
                    title: lang_js['aleft_title'],
                    html: data.mess,
                });
            }
        });
    }

    $("#form_add_product").validate({
        submitHandler: function () {
            var fData = $("#form_add_product").serializeArray();
            // loading('show');
            $.ajax({
                type: "POST",
                url: ROOT + "ajax.php",
                data: {"m": "user", "f": "add_product_to_store", "data": fData, "lang_cur": lang}
            }).done(function (string) {
                var data = JSON.parse(string);
                loading('hide');
                if (data.ok == 1) {
                    $('#add_to_event').modal('hide');
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
                    });
                }
            });
            return false;
        }
    });
</script>
<!-- END: store -->

<!-- BEGIN: add_edit_store -->
<div class="form-group">
    <input type="text" name="title" value="{data.title}" placeholder="{LANG.user.input_store_name} *" required>
    {data.item}
</div>
<div class="form-group">
    <div class="input_picture">
        <div class="pic_title">{LANG.user.picture} *</div>
        <input type="file" name="picture" id="photo-add" class="inputfile inputfile-1" accept="image/*" value="">
        <label for="photo-add">+</label>
        <div class="photo-input">
            <!-- BEGIN: picture -->
            <img src="{data.src}" alt="">
            <input type="hidden" name="picture_available" value="{data.src_ori}">
            <!-- END: picture -->
        </div>
    </div>
</div>
<!-- END: add_edit_store -->

<!-- BEGIN: form_add_to_store -->
    <!-- BEGIN: empty -->
    <tr>
        <td colspan="3" style="text-align: center; font-size: 18px">{LANG.user.not_found_product}</td>
    </tr>
    <!-- END: empty -->
    <!-- BEGIN: item -->
    <tr>
        <td>{row.checkbox}</td>
        <td style="padding-left: 0">
            <div class="event_item">
                <div class="picture"><img src="{row.picture}" alt="{row.title}"></div>
                <div class="info">
                    <div class="item_title">{row.title}</div>
                    {row.inventory}
                </div>
                {row.item_hidden}
            </div>
        </td>
        <td>{row.price}</td>
    </tr>
    <!-- END: item -->
<!-- END: form_add_to_store -->

<!-- BEGIN: product -->
<div class="list_product wrap_table">
    <table class="table table-responsives">
        <thead style="background: #E1E1E1;">
        <tr>
            <th scope="col" align="center" width="53%">{LANG.user.product_name}</th>
            <th scope="col" align="center" width="12.6%">{LANG.user.event}</th>
            <th scope="col" align="center" width="12%">{LANG.user.product_price_show}</th>
            <th scope="col" align="center" width="11%">{LANG.user.sold}</th>
            <th scope="col" align="center" width="11.4%">{LANG.user.action}</th>
        </tr>
        </thead>
        <tbody>
        <!-- BEGIN: item -->
        <tr class="{row.item_id}">
            <td style="padding-left: 0">
                <div class="store_item">
                    <div class="picture"><a href="{row.link}"><img src="{row.picture}" alt="{row.title}"></a></div>
                    <div class="info">
                        <div class="item_title">{row.title1}{row.title}</div>
                        <div class="inventory">{LANG.user.inventory} {row.num_product}</div>
                    </div>
                </div>
            </td>
            <td><button class="select">{LANG.user.select}</button></td>
            <td>{row.price}</td>
            <td>{row.num_sold}</td>
            <td>
                <button id="btn{row.index}" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img src="{CONF.rooturl}resources/images/use/action.svg" alt="action">
                </button>
                <div class="dropdown-menu" aria-labelledby="btn{row.index}">
                    {row.action}
                </div>
            </td>
        </tr>
        <!-- END: item -->
        </tbody>
    </table>
    <!-- BEGIN: empty -->
    <div class="empty">{LANG.user.no_have_item}</div>
    <!-- END: empty -->
    {nav}
</div>
<div class="modal fade product" id="products" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal_title">{LANG.user.store}</div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <img src="{CONF.rooturl}resources/images/use/close_x.svg">
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="post" id="form_product">
                    <div class="list_input"></div>
                    <div class="list_button">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">{LANG.user.cancel}</button>
                        <button type="submit">{LANG.user.save}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade product" id="add_to_event" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal_title">{LANG.user.add_to_event}</div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <img src="{CONF.rooturl}resources/images/use/close_x.svg">
                </button>
            </div>
            <div class="modal-body">
                <div class="form_title">{LANG.user.chose_event_add_product}</div>
                <form action="" method="post" id="form_add_to_event">
                    <div class="list_input pt-0 pl-0 pr-0">
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <div class="search">
                                        <input type="text" name="search" id="search_event" placeholder="{LANG.user.find_event}">
                                        <img src="{CONF.rooturl}resources/images/user/search.svg" alt="search">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <div class="select">
                                        <select name="event_status" id="event_status">
                                            <option value="">{LANG.user.event_status}</option>
                                            <option value="0">{LANG.user.event_upcoming}</option>
                                            <option value="1">{LANG.user.event_ongoing}</option>
                                            <option value="2">{LANG.user.event_over}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="list_event wrap_table">
                            <table class="table table-responsives">
                                <thead style="background: #E1E1E1">
                                    <tr>
                                        <th scope="col" align="center" width="83px">{LANG.user.select}</th>
                                        <th scope="col" align="center" style="padding-left: 0;">{LANG.user.event}</th>
                                        <th scope="col" align="center" width="120px" style="text-align: center;">{LANG.user.event_status}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <input type="hidden" name="it" class="it" value="">
                    <div class="list_button">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">{LANG.user.cancel}</button>
                        <button type="submit">{LANG.user.save}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    // Load form thêm sản phẩm
    $(document).on('click', '.top_page .list_button .item.add_product', function () {
        loading('show');
        $.ajax({
            type: "POST",
            url: ROOT+"ajax.php",
            data: { "m" : "user", "f" : "form_product", 'lang_cur': lang}
        }).done(function( string ) {
            var data = JSON.parse(string);
            if(data.ok == 1) {
                $('#products form .list_input').html(data.html);
                $('#products').modal('show');
                dateRange('#date_begin', '#date_end', 1);
            }else{
                Swal.fire({
                    icon: 'error',
                    title: lang_js['aleft_title'],
                    html: data.mess,
                });
            }
        });
        loading('hide');
    });
    $(document).on('change', '#set_time', function () {
        $('.set_time').slideToggle();
    });

    // Hiển thị nhiều ảnh khi chọn arr_picture product
    var storedFiles = [];
    $(function() {
        var selDiv = "";
        $(document).on('change', '#gallery-photo-add', handleFileSelect);
        $(document).on("click", ".selFile", removeFile);
        function handleFileSelect(e) {
            selDiv = $(".gallery-input.gallery-default");
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

    // Sửa sản phẩm
    $(document).on('click', '.dropdown-menu li.edit', function () {
        var it = $(this).parent().parent().parent().attr('class');
        loading('show');
        $.ajax({
            type: "POST",
            url: ROOT+"ajax.php",
            data: { "m" : "user", "f" : "form_product", 'it':it, 'lang_cur': lang}
        }).done(function( string ) {
            var data = JSON.parse(string);
            if(data.ok == 1) {
                $('#products form .list_input').html(data.html);
                $('#products').modal('show');
                dateRange('#date_begin', '#date_end', 1);
            }else{
                Swal.fire({
                    icon: 'error',
                    title: lang_js['aleft_title'],
                    html: data.mess,
                });
            }
        });
        loading('hide');
    });

    // Xóa sản phẩm
    $(document).on('click', '.dropdown-menu li.delete', function () {
        Swal.fire({
            title: '{LANG.user.delete_product}',
            text: "{LANG.user.delete_product_confirm}",
            // icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#FE6505',
            cancelButtonColor: '#4A4647',
            cancelButtonText: '{LANG.user.cancel}',
            confirmButtonText: '{LANG.user.yes_delete}',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                var it = $(this).parent().parent().parent().attr('class');
                loading('show');
                $.ajax({
                    type: "POST",
                    url: ROOT+"ajax.php",
                    data: { "m" : "user", "f" : "delete_product", 'item':it, 'lang_cur': lang}
                }).done(function( string ) {
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
                        if(data.mess != ''){
                            Swal.fire({
                                icon: 'error',
                                title: lang_js['aleft_title'],
                                text: data.mess,
                            });
                        }
                    }
                });
                loading('hide');
            }
        })
    });

    // Khôi phục sản phẩm
    $(document).on('click', '.dropdown-menu li.restore', function () {
        Swal.fire({
            title: '{LANG.user.restore_product}',
            // icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#FE6505',
            cancelButtonColor: '#4A4647',
            cancelButtonText: '{LANG.user.cancel}',
            confirmButtonText: '{LANG.user.yes_restore}',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                var it = $(this).parent().parent().parent().attr('class');
                loading('show');
                $.ajax({
                    type: "POST",
                    url: ROOT+"ajax.php",
                    data: { "m" : "user", "f" : "restore_product", 'item':it, 'lang_cur': lang}
                }).done(function( string ) {
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
                        if(data.mess != ''){
                            Swal.fire({
                                icon: 'error',
                                title: lang_js['aleft_title'],
                                text: data.mess,
                            });
                        }
                    }
                });
                loading('hide');
            }
        })
    });

    // Thêm sản phẩm vào sự kiện
    $(document).on('click', '.list_product button.select', function () {
        var it = $(this).parent().parent().attr('class');
        loading('show');
        $('#add_to_event .it').val(it);
        load_event();
        $('#add_to_event').modal('show');
        loading('hide');
    });

    $("#add_to_event").on("hidden.bs.modal", function () {
        $('#add_to_event form .list_input table tbody').html('');
        $('#search_event').val('');
        $('#event_status').prop('selectedIndex',0);
    });

    var timer = null;
    $(document).on('keyup', '#search_event', function() {
        clearTimeout(timer);
        timer = setTimeout(function() {
            load_event();
        }, 500);
    });

    $(document).on('change', '#event_status', function () {
        load_event();
    });
    
    function load_event() {
        var it = $('#add_to_event .it').val(),
            sort = $('#event_status').val(),
            keyword = $('#search_event').val();
        loading('show');
        $.ajax({
            type: "POST",
            url: ROOT+"ajax.php",
            data: { "m" : "user", "f" : "load_event", 'it':it, 'keyword': keyword, 'sort': sort, 'lang_cur': lang}
        }).done(function( string ) {
            var data = JSON.parse(string);
            loading('hide');
            if(data.ok == 1) {
                $('#add_to_event form .list_input table tbody').html(data.html);
            }else{
                Swal.fire({
                    icon: 'error',
                    title: lang_js['aleft_title'],
                    html: data.mess,
                });
            }
        });
    }
    $("#form_add_to_event").validate({
        submitHandler: function () {
            var fData = $("#form_add_to_event").serializeArray();
            loading('show');
            $.ajax({
                type: "POST",
                url: ROOT + "ajax.php",
                data: {"m": "user", "f": "add_product_to_event", "data": fData, "lang_cur": lang}
            }).done(function (string) {
                var data = JSON.parse(string);
                loading('hide');
                if (data.ok == 1) {
                    $('#add_to_event').modal('hide');
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: data.mess,
                        showConfirmButton: false,
                        timer: 2000
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: lang_js['aleft_title'],
                        html: data.mess,
                    });
                }
            });
            return false;
        }
    });
</script>
<!-- END: product -->

<!-- BEGIN: add_edit_product -->
<div class="row">
    <div class="col-12 col-sm-6">
        <div class="form-group">
            <input type="text" name="title1" value="{data.title1}" placeholder="{LANG.user.product_name_secondary}">
        </div>
        <div class="form-group">
            <input type="text" name="title" value="{data.title}" placeholder="{LANG.user.product_name} *">
        </div>
        <div class="form-group">
            <input type="number" min="0" name="num_item" value="{data.num_item}" placeholder="{LANG.user.num_item_product} *">
        </div>
        <div class="form-group">
            <img src="{CONF.rooturl}resources/images/use/dollar.svg" alt="dollar">
            <input type="number" min="0" name="price" placeholder="{LANG.user.product_price} *" value="{data.price}">
            <p class="note_text">{LANG.global.unit}</p>
        </div>
        <div class="form-group">
            <textarea name="content" id="" cols="30" rows="10" placeholder="{LANG.user.product_content}">{data.content}</textarea>
        </div>
    </div>
    <div class="col-12 col-sm-6">
        <div class="form-group">
            <div class="input_picture">
                <div class="pic_title">{LANG.user.picture} *</div>
                <div class="gallery-input gallery-default">
                    <input type="file" name="arr_picture_tmp[]" id="gallery-photo-add" class="inputfile inputfile-1" multiple="" accept="image/*">
                    <label for="gallery-photo-add">+</label>
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
        <div class="form-group checkbox">
            <input type="checkbox" name="set_time" id="set_time" {data.checked}>
            <label for="set_time">{LANG.user.begin_end_date}</label>
        </div>
        <div class="set_time form-group" {data.show_date}>
            <div class="row">
                <div class="begin col-12 col-md-6 mb-3">
                    <label>{LANG.user.date_begin}</label>
                    <input type="text" name="date_begin" id="date_begin" autocomplete="off" value="{data.date_begin}">
                </div>
                <div class="end col-12 col-md-6 mb-3">
                    <label>{LANG.user.date_begin}</label>
                    <input type="text" name="date_end" id="date_end" autocomplete="off" value="{data.date_end}">
                </div>
            </div>
        </div>
        <input type="hidden" value="{data.type}" name="type">
        <input type="hidden" value="{data.item}" name="item">
    </div>
</div>
<!-- END: add_edit_product -->

<!-- BEGIN: form_add_to_event -->
<!-- BEGIN: empty -->
<tr>
    <td colspan="3" style="text-align: center; font-size: 18px">{LANG.user.no_have_event_found}</td>
</tr>
<!-- END: empty -->
<!-- BEGIN: item -->
<tr>
    <td>{row.checkbox}</td>
    <td style="padding-left: 0">
        <div class="event_item">
            <div class="picture"><img src="{row.picture}" alt="{row.title}"></div>
            <div class="info">
                <div class="item_title">{row.title}</div>
                {row.address}
                {row.date_begin}
            </div>
            {row.item_hidden}
        </div>
    </td>
    <td style="text-align: center; vertical-align: middle;">{row.event_status}</td>
</tr>
<!-- END: item -->
<!-- END: form_add_to_event -->

<!-- BEGIN: create_order -->
<div class="create_order">
    <h1 class="title">{LANG.user.create_order}</h1>
    <form action="" method="post" id="create_order" class="pt-3">
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <label class="input_title"><img src="{CONF.rooturl}resources/images/use/user_blue.svg" alt="user">{LANG.user.event}</label>
                    <div class="select">
                        <select name="event" id="event" class="form-control">
                            <!-- BEGIN: event -->
                            <option value="{event.item_id}">{event.title}</option>
                            <!-- END: event -->
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <label class="input_title"><img src="{CONF.rooturl}resources/images/use/user_blue.svg" alt="user">{LANG.user.event_user}</label>
                    <div class="select">
                        <select name="user" id="user" class="form-control">
                            <option value="">{LANG.user.select_user}</option>
                            <!-- BEGIN: user -->
                            <option value="{user.detail_id}">{user.full_name}</option>
                            <!-- END: user -->
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <h2 class="title mt-3">{LANG.user.order_info}</h2>
        <div class="info_user mb-3">
            <div class="row">
                <!-- BEGIN: info_user -->
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <input type="text" name="full_name" value="{data.full_name}" placeholder="{LANG.user.full_name} *" required>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <input type="text" name="phone" value="{data.phone}" placeholder="{LANG.user.phone} *" required>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <input type="text" name="email" value="{data.email}" placeholder="{LANG.user.email} *" required>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <input type="text" name="address" value="" placeholder="{LANG.user.address}">
                    </div>
                </div>
                <!-- END: info_user -->
            </div>
        </div>
        <div class="row pt-3">
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <div class="search">
                        <input type="text" name="search" id="search_product" placeholder="{LANG.user.find_product}">
                        <img src="{CONF.rooturl}resources/images/user/search.svg" alt="search">
                    </div>                    
                </div>
            </div>
        </div>
        <div class="list_product wrap_table">
            <table class="table table-responsives">
                <thead>
                    <tr>
                        <th scope="col" align="center" width="30px"></th>
                        <th scope="col" align="center" style="padding-left: 0;">{LANG.user.product_name}</th>
                        <th scope="col" align="center" width="16%" style="padding-left: 0;">{LANG.user.num_item_product}</th>
                        <th scope="col" align="center" width="17%" style="padding-left: 0;">{LANG.user.price}</th>
                        <th scope="col" align="center" width="15%" style="padding-left: 0;">{LANG.user.payment}</th>
                    </tr>
                </thead>
                <tbody>
                <!-- BEGIN: empty -->
                <tr>
                    <td colspan="5" style="text-align: center; font-size: 18px">{LANG.user.no_have_product}</td>
                </tr>
                <!-- END: empty -->
                <!-- BEGIN: item -->
                <tr>
                    <td class="pl-0"><span class="delete_item" style="cursor: pointer"><img src="{CONF.rooturl}resources/images/use/close_x.svg" alt="close" width="14"></span></td>
                    <td style="padding-left: 0">
                        <div class="store_item">
                            <div class="picture"><img src="{row.picture}" alt="{row.title}"></div>
                            <div class="info">
                                <div class="item_title">{row.title1}{row.title}</div>
                            </div>
                        </div>
                        <input type="hidden" name="list_product[{row.index}]['item']" value="{row.item_id}">
                    </td>
                    <td style="padding-left: 0;">
                        <div class="btn_grp">
                            <span class="btn_minus"><i class="far fa-minus"></i></span>
                            <input name="list_product[{row.index}]['quantity']" type="number" value="{row.quantity}" min="1" max="{row.max_quantity}" class="quantity_text no-spinners" />
                            <span class="btn_plus"><i class="far fa-plus"></i></span>
                        </div>
                    </td>
                    <td>{row.price}</td>
                    <td>{row.into_money}</td>
                </tr>
                <!-- END: item -->
                <!-- BEGIN: total -->
                <tr class="total_payment">
                    <td></td>
                    <td style="padding-left: 0;">
                        <!-- BEGIN: payment -->
                        <div class="select">
                            <select name="method" id="method">
                                <option value="">{LANG.user.payment_method}</option>
                                <!-- BEGIN: item -->
                                <option value="{payment}" {selected}>{payment}</option>
                                <!-- END: item -->
                            </select>
                        </div>
                        <!-- END: payment -->
                    </td>
                    <td style="text-align: right"><span>{LANG.user.total_payment}</span></td>
                    <td colspan="2" class="total" style="text-align: right" align="center">{total}</td>
                </tr>
                <!-- END: total -->
                </tbody>
            </table>
        </div>
        <div class="list_button">
            <button type="button" class="cancel">{LANG.user.cancel}</button>
            <button type="submit">{LANG.user.creat_order}</button>
        </div>
    </form>
</div>
<script>
    $(document).on('change', '#event', function () {
        var it = $(this).val();
        loading('show');
        $.ajax({
            type: "POST",
            url: ROOT+"ajax.php",
            data: { "m" : "user", "f" : "load_event_user", 'item':it, 'lang_cur': lang}
        }).done(function( string ) {
            var data = JSON.parse(string);
            if(data.ok == 1) {
                $('#user').html(data.html);
                $('.info_user .row input').val('');
                load_cart(0); // Cập nhật giỏ hàng hiện tại theo event
            }
        });
        loading('hide');
    });
    $(document).on('change', '#user', function () {
        var it = $(this).val();
        // loading('show');
        $.ajax({
            type: "POST",
            url: ROOT+"ajax.php",
            data: { "m" : "user", "f" : "load_info_user", 'item':it, 'lang_cur': lang}
        }).done(function( string ) {
            var data = JSON.parse(string);
            if(data.ok == 1) {
                $('.info_user .row').html(data.html);
            }
        });
        loading('hide');
    });

    // Search product
    var timeout = null;
    var availableTags = [];
    var id = 'search_product';
    $("#" + id).on("change paste",function(){
        if($("#" + id).val().trim().length==0){
            $(".wrap-suggestion-"+id).html('');
            availableTags.length = 0;
            return false;
        }
    });
    $(".wrap-suggestion-"+id).remove();
    $("#" + id).autocomplete({
        minLength: 1,
        source: function (requestObj, responseFunc) {
            clearTimeout(timeout);
            timeout = setTimeout(function () {
                var event = $('#event').val();
                $.ajax({
                    type: "POST",
                    url: ROOT+'ajax.php',
                    data: { 'm' : 'user', 'f' : 'load_search_product', 'keyword':requestObj.term, 'event':event, 'lang_cur': lang}
                }).done(function(string){
                    availableTags = JSON.parse(string);
                    responseFunc(availableTags);
                })
            }, 750);
        },
        response: function(event, ui) {
            if(ui.content.length === 0) {
                availableTags.length = 0;
            }
        },
        open: function (event, ui) {
            $(this).autocomplete("widget").width($(this).innerWidth());
            $(this).data("ui-autocomplete").menu.bindings = $();
            var resultsList = $("ul.ui-autocomplete > li.ui-menu-item > a");
            var srchTerm = $.trim($("#" + id).val()).split(/\s+/).join('|');
            resultsList.each(function () {
                var jThis = $(this);
                var regX = new RegExp('(' + srchTerm + ')', "ig");
                var oldTxt = jThis.text();
                jThis.html(oldTxt.replace(regX, '<b>$1</b>'));
            });
        },
        select: function (event, ui) {
            load_cart(ui.item.item_id);
        },
    }).autocomplete( "instance" )._renderItem = function( ul, item ) {
        ul.addClass("wrap-suggestion-"+id);
        return $( "<li>" )
            .append( '<div class="item" data-it="'+item.item_id+'"><img src="'+item.picture+'"\>'+'<div class="info"><div class="title">'+item.title+'</div>'+item.info+'</div></div>' )
            .appendTo(ul);
    };
    $(document).on('click', '.list_product span.delete_item', function () {
        $(this).parent().parent().remove();
        load_cart(0);
    });
    $(document).on('click', '.btn_grp span', function () {
        load_cart(0);
    });
    $(document).on('click', '.list_button button.cancel', function () {
        location.reload();
    });
    function load_cart(item_add) {
        var fData = $("#create_order").serializeArray();
        loading('show');
        $.ajax({
            type: "POST",
            url: ROOT+"ajax.php",
            data: { "m" : "user", "f" : "load_cart", 'data':fData, 'item_add':item_add, 'lang_cur': lang}
        }).done(function( string ) {
            var data = JSON.parse(string);
            if(data.ok == 1) {
                $('.list_product tbody').html(data.html);
                cal_quantity();
            }else{
                if(data.mess != ''){
                    Swal.fire({
                        icon: 'error',
                        title: lang_js['aleft_title'],
                        text: data.mess,
                    });
                }
            }
        });
        loading('hide');
    }
    $(document).ready(function() {
        $(window).keydown(function(e){
            if(e.keyCode == 13) {
                e.preventDefault();
                return false;
            }
        });
    });

    // Submit form create_order
    $("#create_order").validate({
        submitHandler: function() {
            var fData = $("#create_order").serializeArray();
            loading('show');
            $.ajax({
                type: "POST",
                url: ROOT+"ajax.php",
                data: { "m" : "user", "f" : "create_order", "data" : fData, 'lang_cur':lang}
            }).done(function( string ) {
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
                        text: data.mess,
                    });
                }
            });
            loading('hide');
            return false;
        },
        rules: {
            email: {
                required: true,
                email: true
            }
        },
        messages: {
            email: lang_js['err_invalid_email'],
        }
    });
</script>
<!-- END: create_order -->