<!-- BEGIN: main -->
{data.box_banner}
{data.content}
<script>
    imsUser.add_favorite('event');
</script>
<!-- END: main -->

<!-- BEGIN: banner -->
<div class="topbox">
    <div class="box_menu_product" id="menu_home">
        {data.list_menu}
    </div>
    <div class="box_banner">
        <div id="main_slide">
            <div class="row_item">
                <!-- BEGIN: row -->
                <div class="item">
                    <a href="{row.link}" target="{row.target}" {row.class}>
                        <img src="{row.picture}" alt="{row.alt}" title="{row.alt}"/>
                    </a>
                </div>
                <!-- END: row -->
            </div>
        </div>
    </div>
</div>
<script async="async">
    $("#main_slide .row_item").slick({
        autoplay: false,
        autoplaySpeed: 5000,
        speed: 200,
        swipe: !1,
        dots: !1,
        infinite: true,
        slidesToShow: 1,
        pauseOnHover: !1
    });
</script>
<!-- END: banner -->

<!-- BEGIN: list_event -->
<h1 class="title">{title}</h1>
<div class="section list_event">
    <!-- BEGIN: main -->
    <!-- BEGIN: select -->
    <div class="select_location">
        <p>{LANG.event.popular_at}</p>
        <div class="select" id="select">
            <p id="selected" data-cur="{province_cur}">{select_location}</p>
            <ul id="location" class="list_none">
                <!-- BEGIN: all -->
                <li data-value="0">{LANG.event.all}</li>
                <!-- END: all -->
                <!-- BEGIN: item -->
                <li data-value="{prv.code}">{prv.title}</li>
                <!-- END: item -->
            </ul>
        </div>
    </div>
    <!-- END: select -->
    <div class="list_nav">
        <ul class="list_none">
            <!-- BEGIN: li -->
            <li class="nav-item"><span data-item="#tab{row.group_id}" class="{row.active}">{row.title}</span></li>
            <!-- END: li -->
        </ul>
    </div>
    <div id="location_current" class="{none_event_at}">{event_at}</div>
    <div class="tab-content">
        <!-- BEGIN: content -->
        <div class="content_item {row.active_content}" id="tab{row.group_id}">{row.content}</div>
        <!-- END: content -->
    </div>
    <!-- END: main -->
</div>
<!-- BEGIN: script -->
<script>
    load_slide();
    // ----------- content tab -----------
    $(document).on('click', '.list_nav li span', function(){
        var item = $(this).data('item');
        $('.list_nav li span').removeClass('active');
        $(this).addClass('active');
        $('.tab-content .content_item').addClass('d-none');
        $(item).removeClass('d-none');
    });
    // ----------- Select -----------
    $(document).on('click', '#location li', function(){
        var id = $(this).data('value'),
            title = $(this).text(),
            keyword = $('input[name="sort"]').data('keyword'),
            group_cur = $('.list_item_event').data('group');
        $('#selected').text(title);
        $('#location').toggleClass('show');
        $('#select').toggleClass('dropdown');

        // Load lại dữ liệu event
        var id_cur = $('#selected').data('cur');
        if(id != id_cur){
            $('#selected').data('cur', id);
            $.ajax({
                type: "POST",
                url: ROOT + "ajax.php",
                data: {"m":"event", "f":"load_event_location_main", 'group_cur': group_cur, 'keyword':keyword, 'province':id, 'lang_cur':lang}
            }).done(function (string) {
                var data = JSON.parse(string);
                $('.list_event').html(data.html);
                load_slide();
                loading('hide');
            });
        }
    });
    $(document).on('click', '#selected', function () {
        $('#select').toggleClass('dropdown');
        $('#location').toggleClass('show');
    });
    $(document).on('click', function(e) {
        if(e.target.id != 'selected' && e.target.id != 'location') {
            $('#location').removeClass('show');
            $('#select').removeClass('dropdown');
        }
    });

    function load_slide(){
        $(".list_nav .list_none").slick({
            autoplay: false,
            autoplaySpeed: 5000,
            speed: 200,
            swipe: !0,
            dots: !1,
            infinite: !1,
            variableWidth: true,
            pauseOnHover: !1,
            slidesToScroll: 3
        });
    }

    // Load more
    $(document).on('click', '.btn_viewmore button', function (){
        var id_cur = $(this).parent().parent().parent().parent().attr('id'),
            num_cur = $(this).parent().parent().find('input[name="start"]').val(),
            province = $(this).parent().parent().find('input[name="sort"]').data('province'),
            typeshow = $(this).parent().parent().find('input[name="sort"]').data('typeshow'),
            focus = $(this).parent().parent().find('input[name="sort"]').data('focus'),
            keyword = $(this).parent().parent().find('input[name="sort"]').data('keyword'),
            group_id = $(this).parent().parent().parent().data('group');
        loading('show');
        $.ajax({
            type: "POST",
            url: ROOT + "ajax.php",
            data: {"m":"event", "f":"load_event_main", 'num_cur':num_cur, 'province':province, 'typeshow':typeshow, 'group_id':group_id, 'keyword':keyword, 'lang_cur':lang}
        }).done(function (string) {
            var data = JSON.parse(string);

            $('#'+id_cur + ' .row_item').append(data.html);
            if(data.num > 0){
                $('#'+id_cur+' input[name="start"]').val(data.num);
            }else{
                $('#'+id_cur+' .btn_viewmore button').hide();
            }
            loading('hide');
        });
    });
</script>
<!-- END: script -->
<!-- END: list_event -->

<!-- BEGIN: detail -->
<div class="event_detail">
    <div class="background"><img src="{data.background}" alt="background"></div>
    <div class="wrap_detail">
        <div class="info_top">
            <div class="picture">
                <div class="img"><a href="{data.pic_zoom}" data-fancybox><img src="{data.picture}" alt="{data.e_title}"></a></div>
                <div class="share_favorite d-md-none">
                    <div class="share"><a href="#share" class="goto"><img src="{CONF.rooturl}resources/images/use/share.svg" alt="share"></a></div>
                    <div class="add_favorite {data.added}" data-id="{data.item_id}"><i class="{data.i_favorite}"></i></div>
                </div>
            </div>
            <div class="info">
                <div class="wrap_info">
                    <h1 class="title">{data.title1}<span>{data.title}</span></h1>
                    {data.organizational}
                    <div class="date_begin">{data.date_begin}</div>
                    {data.follow}
                </div>
                <div class="price">{data.price}</div>
                <div class="btn_register d-md-none">
                    <button data-it="{data.item_id}" {data.register_disable}>{data.register_text}</button>
                </div>
            </div>
        </div>
        <div class="info_pc d-md-flex d-none">
            <div class="share_favorite">
                <div class="share"><a href="#share" class="goto"><img src="{CONF.rooturl}resources/images/use/share.svg" alt="share"></a></div>
                <div class="add_favorite {data.added}" data-id={data.item_id}><i class="{data.i_favorite}"></i></div>
            </div>
            <div class="btn_register">
                <button data-it="{data.item_id}" {data.register_disable} >{data.register_text}</button>
            </div>
        </div>
        <div class="info_bottom">
            <div class="content">
                <ul class="nav nav-pills">
                    <li class="nav-item"><a href="#tab_content" data-toggle="tab" class="nav-link active">{LANG.event.detail_content_title}</a></li>
                    <li class="nav-item"><a href="#tab_store" data-toggle="tab" class="nav-link">{LANG.event.store}</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab_content"><div class="detail_content">{data.content}</div></div>
                    <div class="tab-pane" id="tab_store">{data.event_product}</div>
                </div>
            </div>
            <div class="column">
                <div class="item time">
                    <img src="{CONF.rooturl}resources/images/use/calendar.svg" alt="time">
                    <p>{LANG.event.time}:</p>
                    {data.time}
                </div>
                <div class="item address">
                    <img src="{CONF.rooturl}resources/images/use/location.svg" alt="location">
                    <p>{data.location}:</p>
                    {data.address}
                    {data.link_event_maps}
                </div>
                <div class="qr_code">
                    <div class="wrap">
                        <img src="https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl={data.link_share}&choe=UTF-8" title="Link to {data.title}" />
                    </div>
                </div>
            </div>
        </div>
        <div id="share">
            <p>{LANG.event.share}:</p>
            <ul class="list_none">
                <li><a href="https://twitter.com/intent/tweet?url={data.link_share}" target="_blank"><img src="{CONF.rooturl}resources/images/use/twitter.svg" alt="twitter"></a></li>
                <li><a href="https://www.instagram.com/?url={data.link_share}" target="_blank"><img src="{CONF.rooturl}resources/images/use/instagram.svg" alt="instagram"></a></li>
                <li><a href="https://www.linkedin.com/shareArticle?mini=true&url=&title=&summary=&source={data.link_share}" target="_blank"><img src="{CONF.rooturl}resources/images/use/linkedin.svg" alt="linkedin"></a></li>
                <li><a href="https://facebook.com/sharer/sharer.php?u={data.link_share}" target="_blank"><img src="{CONF.rooturl}resources/images/use/facebook.svg" alt="facebook"></a></li>
            </ul>
        </div>
        <div id="address" class="{data.border}">
            <div class="title"><span>{data.title1}</span>{data.title}</div>
            <p>{data.time}</p>
            <p>{data.address}</p>
            {data.link_event_text}
            {data.maps}
        </div>
        {data.event_same_organization}
        {data.event_other}
    </div>
</div>

<div class="modal fade" id="register" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true"></span>
            </button>
            <div class="modal-body">
                <form id="event_register" class="register_form" name="register" method="post" action="">
                    <div class="group has_ev_info">
                        <div class="left"></div>
                        <div class="right">
                            <div class="event_pic mobile"><div class="img"><a><img src="{data.picture_form}"></a></div></div>
                        </div>
                    </div>
                    <div class="group form">
                        <div class="left">
                            <div class="content_form"></div>
                        </div>
                        <div class="right">
                            <div class="event_pic"><div class="img"><a><img src="{data.picture_form}"></a></div></div>
                            <div class="cart_info"></div>
                        </div>
                    </div>
                    <div class="submit">
                        <div class="wrap">
                            <button type="button">
                                {LANG.event.register}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="detail_store" tabindex="-1" role="dialog" aria-hidden="true" style="opacity: 0">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="detail_store_item">
                </div>
            </div>
        </div>
    </div>
</div>
<div id="event_ticket" style="position: absolute; z-index: -10000000">
    {data.event_ticket}
</div>
<script>
    $(document).ready(function(){
        $(".info_pc").sticky({topSpacing:54});
    });
    $(".same_organization .list_item_event .row_item").slick({
        arrows: !1,
        dots: !1,
        infinite: !1,
        autoplay: !1,
        autoplaySpeed: 3000,
        speed: 500,
        slidesToShow: 4,
        slidesToScroll: 4,
        // swipeToSlide: !0,
        lazyload:"ondemand",
        responsive: [{
            breakpoint: 993,
            settings: {
                slidesToShow: 3,
                slidesToScroll: 3
            }
        }, {
            breakpoint: 769,
            settings: {
                slidesToShow: 2,
                slidesToScroll: 2
            }
        }, {
            breakpoint: 351,
            settings: {
                slidesToShow: 1,
                slidesToScroll: 1
            }
        }]
    });
    $(".event_other .list_item_event .row_item").slick({
        arrows: !1,
        dots: !1,
        infinite: !1,
        autoplay: !1,
        autoplaySpeed: 3000,
        speed: 500,
        slidesToShow: 4,
        slidesToScroll: 4,
        // swipeToSlide: !0,
        lazyload:"ondemand",
        responsive: [{
            breakpoint: 993,
            settings: {
                slidesToShow: 3,
                slidesToScroll: 3
            }
        }, {
            breakpoint: 769,
            settings: {
                slidesToShow: 2,
                slidesToScroll: 2
            }
        }, {
            breakpoint: 351,
            settings: {
                slidesToShow: 1,
                slidesToScroll: 1
            }
        }]
    });
    $(document).on('click', '.btn_follow button', function () {
        var event_item = $(this).data('item');
        $.ajax({
            type: "POST",
            url: ROOT + "ajax.php",
            data: {"m":"event", "f":"follow", 'event_item':event_item, 'lang_cur':lang}
        }).done(function (string) {
            var data = JSON.parse(string);

            if(data.mess == ''){
                if(data.ok != 0){
                    $('.follow .num span').html(data.num_follow);
                    $('.btn_follow button').text(data.text_follow);
                }
                if(data.ok == 1){
                    $('.btn_follow button').addClass('followed');
                }else if (data.ok == 2){
                    $('.btn_follow button').removeClass('followed');
                }
            }else{
                Swal.fire({
                    icon: 'error',
                    title: lang_js['aleft_title'],
                    text: data.mess,
                });
            }
        });
    });
    // ----------------- register -----------------
    // -------- show form register --------
    $(document).on('click', '.btn_register button', function () {
        // imsEvent.load_complete_order_event();
        // imsEvent.load_cart_info(3);
        // $('#register').modal('show');

        var event_item = $(this).data('it');
        $('form.register_form').addClass('step1').removeClass('step2 step3');
        $.ajax({
            type: "POST",
            url: ROOT + "ajax.php",
            data: {'m':'event', 'f':'load_form_register', 'event_item':event_item, 'lang_cur':lang}
        }).done(function (string) {
            var data = JSON.parse(string);
            if(data.ok == 1){
                $('#register .group.has_ev_info .left').html(data.event_info);
                $('#register .group.form .content_form').html(data.html_form);
                imsEvent.load_cart_info(1);
                $('#register').modal('show');
            }else{
                if(data.ok == 2 && data.link_go != ''){
                    go_link(data.link_go)
                }else if(data.mess != ''){
                    Swal.fire({
                        icon: 'error',
                        title: lang_js['aleft_title'],
                        text: data.mess,
                    });
                }
            }
        });
    });
    $("#register").on("hidden.bs.modal", function () {
        $('#register .group.has_ev_info .left').html('');
        $('#register .group.form .content_form').html('');
        $('#register .group.form .cart_info').html('');
        $('form.register_form').removeClass('step1 step2 step3');
        $('#register .submit .wrap').html('<button type="button">{LANG.event.register}</button>');
        imsEvent.load_cart_info(0);
    });
    $(document).on('change', '#register .list_ticket .item select', function () {
        var type = $(this).data('type'),
            vl = $(this).val();
        if(type == 'donate'){
            if(vl > 0){
                $(this).parent().parent().next().slideDown();
            }else{
                $(this).parent().parent().next().slideUp();
            }
        }
        imsEvent.load_cart_info(1);
    });

    var timer = null;
    $(document).on('keyup', '#register .list_ticket .item input', function() {
        clearTimeout(timer);
        timer = setTimeout(function() {
            imsEvent.load_cart_info(1);
        }, 300);
    });

    $(document).on('change', '#vat', function () {
        imsEvent.load_cart_info(2);
    });
    $(document).on('click', '#register .approve', function () { // Approve mã giảm giá
        imsEvent.load_cart_info(2);
    });
    $(document).on('click', '#register .cancel', function () { // Hủy mã giảm giá
        $('.promotion_code').val('');
        imsEvent.load_cart_info(2);
    });
    // -------- register step 2 --------
    $(document).on('click', '.register_form.step1 .submit button', function () {
        var event_item = $('.btn_register button').data('it'),
            data = $('form.step1').serializeArray();
        $.ajax({
            type: "POST",
            url: ROOT + "ajax.php",
            data: {'m':'event', 'f':'register_step1', 'event_item':event_item, 'lang_cur':lang}
        }).done(function (string) {
            var data = JSON.parse(string);
            if(data.ok == 1){
                $('form.register_form').removeClass('step1').addClass('step2');
                $('#register .group.has_ev_info .left').html('');
                $('#register .group.form .content_form').html(data.html);
                $('#register .submit .wrap button').text('{LANG.event.btn_payment}').attr('type', 'submit');
                imsEvent.load_cart_info(2);
            }else{
                Swal.fire({
                    icon: 'error',
                    title: lang_js['aleft_title'],
                    text: data.mess,
                });
            }
        });
    });
    $(document).on('change', '.list_payment .payment_title input', function(){
        $('.payment_content').hide();
        $(this).parent().next().slideDown();
    });
    var timer1 = null;
    $(document).on('keyup', '#register .list_item_info input.email', function() {
        clearTimeout(timer1);
        timer1 = setTimeout(function() {
            imsEvent.load_cart_info(2);
        }, 300);
    });
    // -------- register step 3 --------
    $(document).on('click', '.register_form.step2 .submit button', function(){
        $("#event_register").validate({
            submitHandler: function() {
                var fData = $("#event_register").serializeArray(),
                    event_item = $('.btn_follow button').data('item');
                $.ajax({
                    type: "POST",
                    url: ROOT+"ajax.php",
                    data: { "m" : "event", "f" : "register_step2", "data" : fData, 'lang_cur':lang, 'event_item':event_item}
                }).done(function( string ) {
                    var data = JSON.parse(string);
                    if(data.ok == 1) {
                        if (data.link != '') {
                            go_link(data.link);
                        }else{
                            if(data.event_ticket != ''){
                                $('#event_ticket').html(data.event_ticket).promise().done(function(){
                                    imsEvent.upload_ticket(data.arr_name);
                                });
                            }
                        }
                    } else {
                        if (data.link != '') {
                            go_link(data.link);
                        }else{
                            Swal.fire({
                                icon: 'error',
                                title: lang_js['aleft_title'],
                                text: data.mess,
                            });
                        }
                    }
                });
                return false;
            },
            rules: {
                o_email: {
                    required: true
                }
            },
            messages: {
                o_email: lang_js['err_valid_input'],
            }
        });
    });
    $(document).on('click', 'button.send_other_mail', function () {
        $(this).next().slideToggle();
    });

    $(document).on('click', 'button.submit_send_mail', function () {
        var mail = $('input.input_other_mail').val();
        $.ajax({
            type: "POST",
            url: ROOT+"ajax.php",
            data: { "m" : "event", "f" : "send_other_mail", 'mail':mail, 'lang_cur':lang}
        }).done(function( string ) {
            var data = JSON.parse(string);
            if(data.ok == 1) {
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: data.mess,
                    showConfirmButton: false,
                    timer: 3000
                }).then(function() {
                    $('input.input_other_mail').val('');
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: lang_js['aleft_title'],
                    text: data.mess,
                });
            }
        });
    });
</script>
<!-- END: detail -->

<!-- BEGIN: event_product -->
<div class="store">
    <div class="row list_item">
        <!-- BEGIN: item_product -->
        <div class="item col-lg-4 col-6">
            <div class="wrap_item">
                <div class="picture"><img src="{row.picture}" alt="{row.title}"></div>
                <div class="info" data-item="{row.item_id}">
                    <div class="title">{row.title1}<p title="{row.title}">{row.title}</p></div>
                    <div class="price">{row.price}</div>
                </div>
            </div>
        </div>
        <!-- END: item_product -->
    </div>
    {show_more}
</div>

<!-- BEGIN: detail_product -->
<div class="picture">
    <div id="slider" >
        <!-- BEGIN: item -->
        <div class="img"><img src="{pic}"></div>
        <!-- END: item -->
    </div>
    <div id="slider_thumb">
        <!-- BEGIN: thumb -->
        <div class="img"><img src="{pic_thumb}"></div>
        <!-- END: thumb -->
    </div>
</div>
<div class="info">
    <div class="title">{row.title1}<p title="{row.title}">{row.title}</p></div>
    {row.content}
    <div class="price">{row.price}</div>
    <div class="remaining">{row.remaining_store_item}</div>
</div>
<!-- END: detail_product -->
<script>
    $(document).on('click', '.store .item .info', function () {
        var item = $(this).data('item');
        loading('show');
        $.ajax({
            type: "POST",
            url: ROOT + "ajax.php",
            data: {"m":"event", "f":"load_detail_event_product", 'item':item, 'lang_cur':lang}
        }).done(function (string) {
            var data = JSON.parse(string);
            if(data.ok == 1){
                $('#detail_store .detail_store_item').html(data.html);
                $('#detail_store').modal('show');
                setTimeout(function () {
                    var sync1 = $("#slider"),
                        sync2 = $("#slider_thumb");
                    sync1.slick({
                        slidesToShow: 1,
                        arrows: true,
                        fade: true,
                        asNavFor: "#slider_thumb",
                        swipe: false,
                        lazyload: "ondemand",
                        infinite: false,
                    })
                    sync2.slick({
                        slidesToShow: 5,
                        asNavFor: "#slider",
                        infinite: false,
                        dots: false,
                        arrows: true,
                        swipeToSlide: true,
                        focusOnSelect: true,
                        vertical: false,
                        lazyload: "ondemand",
                        responsive: [
                            {
                                breakpoint: 992,
                                settings: {
                                    slidesToShow: 4,
                                    vertical: false,
                                }
                            }
                        ]
                    })
                }, 300);
            }
            setTimeout(function () {
                loading('hide');
                $('#detail_store').css('opacity', 1);
            }, 400);
        });
    });
    $("#detail_store").on("hidden.bs.modal", function () {
        $('#detail_store').css('opacity', 0);
        $('#detail_store .detail_store_item').html('');
    });
    $(document).on('click', '.store .show_more button', function () {
        var num_cur = $(this).parent().find('input[name="start"]').val(),
            event_item = $(this).parent().find('input[name="start"]').data('it');
        loading('show');
        $.ajax({
            type: "POST",
            url: ROOT + "ajax.php",
            data: {"m":"event", "f":"load_event_product", 'num_cur':num_cur, 'event_item':event_item, 'lang_cur':lang}
        }).done(function (string) {
            var data = JSON.parse(string);

            $('.store .list_item').append(data.html);
            if(data.num > 0){
                $('.store input[name="start"]').val(data.num);
            }else{
                $('.store .show_more').remove();
            }
            loading('hide');
        });
    });
</script>
<!-- END: event_product -->

<!-- BEGIN: form_register1 -->
    <!-- BEGIN: event_info -->
    <div class="event_info">
        <!-- BEGIN: list_logo -->
        <div class="list_logo">
            <!-- BEGIN: logo -->
            <div class="item"><img src="{logo}" alt="logo"></div>
            <!-- END: logo -->
        </div>
        <!-- END: list_logo -->
        <div class="title">{data.title1}<span>{data.title}</span></div>
        {data.organizational}
        <div class="date_begin">{data.date_begin}</div>
    </div>
    <!-- END: event_info -->
    <!-- BEGIN: form -->
    <div class="form_title">{LANG.event.choose_ticket}</div>
    <div class="expiry">{expiry}</div>
    <div class="list_ticket">
        <!-- BEGIN: ticket -->
        <div class="item">
            <div class="wrap_item">
                <div class="info_ticket">
                    <div class="title">{ticket.title}</div>
                    <div class="price">{ticket.price}</div>
                    <div class="content">
                        <p>{ticket.num_ticket}</p>
                        {ticket.short}
                    </div>
                </div>
                <!-- BEGIN: select -->
                <div class="select">
                    <select name="book['{ticket_id}']['num']" data-type="{type_ticket}">
                        <!-- BEGIN: option -->
                        <option value="{option.val}">{option.title}</option>
                        <!-- END: option -->
                    </select>
                </div>
                <!-- END: select -->
            </div>
            {ticket.input_price_donate}
        </div>
        <!-- END: ticket -->
    </div>
    <!-- END: form -->
<!-- END: form_register1 -->

<!-- BEGIN: form_register2 -->
<div class="main_form_title">{LANG.event.payment_procedures}</div>
<div class="list_item_info">
    <div class="form_group">
        <div class="row">
            <div class="form_group_title col-12">{LANG.event.customer_information}</div>
            <div class="col-12">
                <div class="form-group">
                    <input name="o_full_name" type="text" maxlength="250" value="{user.full_name}" class="form-control" placeholder="{LANG.event.full_name} (*)" required />
                </div>
            </div>
            <div class="col-md-6 col-12">
                <div class="form-group">
                    <input name="o_email" type="text" maxlength="250" value="{user.email}" class="form-control email" placeholder="{LANG.event.email} (*)" required />
                </div>
            </div>
            <div class="col-md-6 col-12">
                <div class="form-group">
                    <input name="o_phone" type="text" maxlength="250" value="{user.phone}" class="form-control" placeholder="{LANG.event.phone} (*)" required />
                </div>
            </div>
        </div>
    </div>
    <!-- BEGIN: item_ticket -->
    <div class="form_group">
        <div class="row">
            <div class="form_group_title col-12">{ticket.ticket_name}</div>
            <div class="col-12">
                <div class="form-group">
                    <input name="ticket_info['{ticket.id}'][{ticket.index}]['full_name']" type="text" maxlength="250" value="" class="form-control" placeholder="{LANG.event.full_name} (*)" required />
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <input name="ticket_info['{ticket.id}'][{ticket.index}]['email']" type="text" maxlength="250" value="" class="form-control" placeholder="{LANG.event.email} (*)" required />
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <input name="ticket_info['{ticket.id}'][{ticket.index}]['phone']" type="text" maxlength="250" value="" class="form-control" placeholder="{LANG.event.phone} (*)" required />
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <input name="ticket_info['{ticket.id}'][{ticket.index}]['age']" type="number" maxlength="250" value="" class="form-control" placeholder="{LANG.event.age}" min="0" />
                </div>
            </div>
        </div>
    </div>
    <!-- END: item_ticket -->
    <div class="form_group box_payment">
        <div class="form_group_title">{LANG.event.ordering_method}</div>
        <div class="list_payment">
            <!-- BEGIN: item_payment -->
            <div class="item">
                <div class="payment_title">
                    <input type="radio" id="method_{payment.method_id}" name="method" value="{payment.method_id}" {payment.checked_bo} />
                    <label for="method_{payment.method_id}" class="d-flex align-items-center justify-content-between"><span>{payment.title}</span> <img src="{payment.picture}" alt="{payment.title}"></label>
                </div>
                <div class="payment_content" {payment.none} style="display: none">{payment.content}</div>
            </div>
            <!-- END: item_payment -->
        </div>
    </div>
    <div class="form_group">
        <div class="note">{LANG.event.book_ticket_note}</div>
    </div>
</div>
<!-- END: form_register2 -->

<!-- BEGIN: cart_info -->
<div class="block list_ticket_choose">
    <!-- BEGIN: item -->
    <div class="item">
        <p class="title">{row.num} x {row.title}</p>
        <p class="price">{row.price}</p>
    </div>
    <!-- END: item -->
</div>
<!-- BEGIN: step2 -->
<div class="block">
    <!-- BEGIN: total_money_tmp -->
    <div class="item provisional_sum">
        <p class="title">{LANG.event.provisional_price}:</p>
        <p class="price">{data.total_money_tmp}</p>
    </div>
    <!-- END: total_money_tmp -->
    <div class="item discount_input">
        <p class="input">
            <input type="text" name="promotion_code" placeholder="{LANG.event.discount_input}" value="{data.promotion_code}" class="promotion_code" {data.disable_code}>
            {data.button_code}
        </p>
        <p class="price">- {data.discount_price}</p>
    </div>
    <div class="item vat">
        <div class="check_vat">
            <input type="checkbox" id="vat" name="vat" value="1" {data.vat_checked}>
            <label for="vat" class="mb-0">{LANG.event.get_vat}</label>
        </div>
    </div>
    <!-- BEGIN: vat_fee -->
    <div class="item vat_fee">
        <p>{LANG.event.surcharge}</p>
        <p>{data.surcharge_fee}</p>
    </div>
    <!-- END: vat_fee -->
</div>
<!-- END: step2 -->
<div class="block">
    <div class="item total">
        <p>{LANG.event.total_money}:</p>
        <p>{data.total_money}</p>
    </div>
</div>
<!-- END: cart_info -->

<!-- BEGIN: order_complete -->
<div class="main_title">
    <span><i class="fas fa-check-circle"></i>{LANG.event.order_complete}</span>
    <span>#{data.order_code}</span>
</div>
<div class="event">
    <p>{LANG.event.booked_event}</p>
    <div class="event_info">
        <div class="top">
            <div class="left">
                <div class="title">{data.title1}<span>{data.title}</span></div>
                {data.organizational}
                <div class="date_begin">{data.date_begin}</div>
            </div>
            <div class="qr_code">
                <img src="https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl={data.link}&choe=UTF-8" title="Link to {data.title}" />
            </div>
        </div>
        <div class="content">{data.content}</div>
    </div>
    <div class="ticket_content">
        <div class="item">
            <div class="title"><img src="{CONF.rooturl}resources/images/use/ticket.svg" alt="">{data.mail_sent}</div>
            <div class="content_item">
                <p>{data.o_email}</p>
                <button class="send_other_mail" type="button">{LANG.event.send_other_mail}</button>
                <div class="input_mail" style="display: none">
                    <input type="text" name="other_mail" class="input_other_mail" placeholder="{LANG.event.text_email}">
                    <button type="button" class="submit_send_mail">{LANG.event.send}</button>
                </div>
            </div>
        </div>
        <div class="item">
            <div class="title"><img src="{CONF.rooturl}resources/images/use/calendar1.svg" alt="">{LANG.event.event_date}</div>
            <div class="content_item">
                <p>{data.date_begin}</p>
            </div>
        </div>
        <div class="item">
            <div class="title"><img src="{CONF.rooturl}resources/images/use/maps.svg" alt="">{LANG.event.location}</div>
            <div class="content_item">
                <p>{data.address}</p>
            </div>
        </div>
        <!-- BEGIN: support -->
        <div class="item support">
            <div class="title1">{LANG.event.support_group}</div>
            <div class="content_item1">
                <p>{LANG.event.support_group_note}</p>
                <ul class="list_none">{data.button_support}</ul>
            </div>
        </div>
        <!-- END: support -->
    </div>
</div>
<!-- END: order_complete -->

<!-- BEGIN: view_ticket -->
<div class="view_ticket">
    <div class="event_info">
        <div class="left">
            <div class="booked">{LANG.event.booked}</div>
            <div class="title">{event.title1} <span>{event.title}</span></div>
            <div class="info">
                <p>{event.date_begin}</p>
                <p>{LANG.event.event_info}: {event.event_info}</p>
            </div>
        </div>
        <div class="right">
            <div class="qr_code"><img src="https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl={event.link}&choe=UTF-8" title="Link to {event.title}" /></div>
            <p>{LANG.event.event_qr}</p>
        </div>
    </div>
    <div class="tiket_info">
        <div class="column">
            <ul class="list_none list_button">
                <li><button class="print">{LANG.event.print_ticket}</button></li>
                <!-- BEGIN: cancel -->
                <li><button class="cancel">{LANG.event.cancel_ticket}</button></li>
                <!-- END: cancel -->
                <li><button class="contact" data-toggle="modal" data-target="#contact">{LANG.event.contact}</button></li>
            </ul>
        </div>
        <div class="form_content">
            <{data.elm} {data.form_info} class="edit_ticket">
            <!-- BEGIN: item_ticket -->
            <div class="item">
                <div class="form_group_title"><span>{ticket.title}</span> {ticket.button_edit}</div>
                <div class="customer_info">{LANG.event.customer_info}</div>
                <div class="list_info">
                    <div class="form-group">
                        <label>{LANG.event.full_name_label} <span>*</span></label>
                        <input name="ticket['{ticket.detail_id}']['full_name']" type="text" maxlength="250" value="{ticket.full_name}" placeholder="{LANG.event.full_name} (*)" {ticket.disable} required />
                    </div>
                    <div class="form-group">
                        <label>{LANG.event.email} <span>*</span></label>
                        <input name="ticket['{ticket.detail_id}']['email']" type="text" maxlength="250" value="{ticket.email}" placeholder="{LANG.event.email} (*)" {ticket.disable} required />
                    </div>
                    <div class="form-group">
                        <label>{LANG.event.phone} <span>*</span></label>
                        <input name="ticket['{ticket.detail_id}']['phone']" type="text" maxlength="250" value="{ticket.phone}" placeholder="{LANG.event.phone} (*)" {ticket.disable} required />
                    </div>
                    <div class="form-group">
                        <label>{LANG.event.age} <span>*</span></label>
                        <input name="ticket['{ticket.detail_id}']['age']" type="number" maxlength="250" value="{ticket.age}" placeholder="{LANG.event.age}" min="0" {ticket.disable} />
                    </div>
                </div>
            </div>
            <!-- END: item_ticket -->
                <!-- BEGIN: list_button -->
                <div class="list_button">
                    <button class="cancel" type="button">{LANG.event.cancel}</button>
                    <button type="submit">{LANG.event.save}</button>
                </div>
                <!-- END: list_button -->
            </{data.elm}>
        </div>
    </div>

    <div class="modal fade" id="contact" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal_title contact_title">{LANG.event.contact_title}</div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="first">
                        <!-- BEGIN: list_advisory -->
                        <div class="list_frequently_qa">
                            <div class="modal_title content_title">{LANG.event.frequently_qa}</div>
                            <ul class="list_none">
                                <!-- BEGIN: advisory -->
                                <li><a href="{row.link}" target="_blank">{row.title}</a></li>
                                <!-- END: advisory -->
                            </ul>
                        </div>
                        <!-- END: list_advisory -->
                        <div class="modal_title content_title">{LANG.event.frequently_qa}</div>
                        <div class="check_event_info">{LANG.event.check_event_info}</div>
                        <button class="contact">{LANG.event.contact}</button>
                    </div>
                    <div class="second" style="display: none">
                        <form class="form_contact" action="" method="post" id="form_contact">
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <input name="full_name" type="text" maxlength="250" value="" placeholder="{LANG.event.full_name} (*)" required />
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <input name="email" type="text" maxlength="250" value="" placeholder="{LANG.event.email} (*)" required />
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <input name="title" type="text" maxlength="250" value="" placeholder="{LANG.event.contact_reason} (*)" required />
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <textarea name="content" id="" cols="30" rows="10" placeholder="{LANG.event.contact_content} (*)" required></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="list_button">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">{LANG.event.cancel}</button>
                                <button type="submit" class="submit">{LANG.event.send}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).on('click', '.form_content .cancel', function () {
        go_link('{event.link}/?view_ticket=1');
    });
    $("#edit_ticket").validate({
        submitHandler: function() {
            var fData = $("#edit_ticket").serializeArray();
            $.ajax({
                type: "POST",
                url: ROOT+"ajax.php",
                data: { "m" : "event", "f" : "edit_ticket", "data" : fData, 'lang_cur':lang}
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
                        go_link(data.link);
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: lang_js['aleft_title'],
                        text: data.mess,
                    });
                }
            });
            return false;
        }
    });
    $(document).on('click', '.column .cancel', function () {
        Swal.fire({
            title: '{LANG.event.cancel_ticket_title}',
            text: "{LANG.event.cancel_ticket_confirm}",
            // icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#FE6505',
            cancelButtonColor: '#4A4647',
            cancelButtonText: '{LANG.event.do_not_cancel}',
            confirmButtonText: '{LANG.event.yes_cancel}',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                loading('show');
                $.ajax({
                    type: "POST",
                    url: ROOT+"ajax.php",
                    data: { "m" : "event", "f" : "cancel_ticket_booked", 'lang_cur': lang}
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
                            go_link('{event.link}');
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
    $(document).on('click', '#contact button.contact', function () {
        loading('show');
        $('#contact .first').hide();
        $('#contact .second').show();
        loading('hide');
    });
    $("#contact").on("hidden.bs.modal", function () {
        $('#contact .first').show();
        $('#contact .second').hide();
    });

    $("#form_contact").validate({
        submitHandler: function() {
            var fData = $("#form_contact").serializeArray();
            loading('show');
            $.ajax({
                type: "POST",
                url: ROOT+"ajax.php",
                data: { "m" : "event", "f" : "contact", "data" : fData, 'lang_cur':lang}
            }).done(function( string ) {
                var data = JSON.parse(string);
                if(data.ok == 1) {
                    $('#form_contact')[0].reset();
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: data.mess,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    $('#contact').modal('hide');
                    loading('hide');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: lang_js['aleft_title'],
                        text: data.mess,
                    });
                }
            });
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
<!-- END: view_ticket -->