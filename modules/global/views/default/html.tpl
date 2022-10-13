<!-- BEGIN: body -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
    <head>
        {CONF.embedcode_head_begin}
        <title>{CONF.meta_title}</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="content-language" content="vi" />
        <meta name="robots" content="noodp,index,follow" />
        <meta name="revisit-after" content="1 days" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        <link href="{CONF.rooturl}favicon.ico" rel="shortcut icon" type="image/x-icon" />
        <link href="{CONF.rooturl}favicon.ico" rel="apple-touch-icon" />
        <link href="{CONF.rooturl}favicon.ico" rel="apple-touch-icon-precomposed" />

        <meta name="description" content="{CONF.meta_desc}" />
        <meta name="keywords" itemprop="keywords" content="{CONF.meta_key}" />
        <link rel="canonical" href="{CONF.canonical}" />
        <meta property="og:type" content="website" />
        <meta property="og:title" content="{CONF.meta_title}" />
        <meta property="og:description" content="{CONF.meta_desc}" />
        <meta property="og:url" content="{CONF.canonical}" />
        {CONF.meta_more}

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">

        {CONF.include_css_file}
        <script language="javascript" >
            var ROOT = "{CONF.rooturl}";
            var DIR_IMAGE = "{DIR_IMAGE}";
            var deviceType = "{data.deviceType}";
            var lang = "{CONF.lang_cur}";
            var lang_js = new Array();
            {LANG_JS}
        </script>

        <style>
            {data.box_style}
        </style>
        <style>
            {CONF.include_css}
        </style>
        <!-- BEGIN: style_custom -->
        <style type="text/css">
            /* màu nền */
            .bg-color {background: {CONF.bg_color} !important;} 

            /* màu chữ = màu nền */
            .bg-text-color {color: {CONF.bg_color} !important;}
            
            /* màu viền */
            .border-color {border-color: {CONF.border_color} !important;}

            /* màu chữ đi cùng màu nền */
            .text-color {color: {CONF.text_color} !important;}

            /* màu chữ Header */
            .color-header {color: {CONF.text_color_header};}

            /* màu nền Footer */
            .bg-footer {background: {CONF.bg_footer};}

            /* màu nền Tag Footer */
            .bg-tag-footer {background: {CONF.bg_tag_footer} !important;}

            /* màu chữ Footer đi theo màu nền*/
            .color-footer {color: {CONF.text_color_footer};}
        </style>
        <!-- END: style_custom -->
        <style type="text/css">
            p a{
                color: #007bff;
            }
        </style>
        {CONF.embedcode_head}
    </head>

    <body>
        {CONF.embedcode_body_begin}
        <div id="ims-wrapper">
            {data.header}
            <!-- BEGIN: bo -->
            <div class="topnav">
                <div class="container">
                    <div class="row">
                        <nav class="navbar navbar-expand-lg d-none d-lg-flex bg-color" id="menu-product">
                            {data.list_menu}
                            <div class="icon arrow-down">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                        </nav>
                        <div class="promo">
                            <div class="picture">
                                {data.promo_pic}
                            </div>
                            <div class="ticker_promo">
                                <div class="title">{LANG.global.ticker_promo_title}</div>
                                <div class="list_ticker_promo">
                                    {data.promo_event}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="nav">
                <div class="container">{CONF.nav}</div>
            </div>
            <div class="message-container">
                <div class="container">
                    <div class="row">
                    </div>
                </div>
            </div>
            <!-- END: bo -->
            {data.main_slide}
            <!-- BEGIN: container_m_c -->
            <div class="full {CONF.class_full}">
                <div id="container" class="container">
                    <div class="row_m_c row m-0">
                        <div id="ims-content">{PAGE_CONTENT}</div>
                        <div id="ims-column">{PAGE_COLUMN}</div>
                    </div>
                </div>
            </div>
            <!-- END: container_m_c -->
            <!-- BEGIN: container_c_m -->
            <div class="full {CONF.class_full}">
                <div id="container" class="container">
                    <div class="row_c_m row m-0">
                        <div id="ims-column_left">{PAGE_COLUMN_LEFT}</div>
                        <div id="ims-content">{PAGE_CONTENT}</div>
                    </div>
                </div>
            </div>
            <!-- END: container_c_m -->
            <!-- BEGIN: container_c_m_c -->
            <div class="full {CONF.class_full}">
                <div id="container" class="container">
                    <div class="row_c_m_c row m-0">
                        <div id="ims-column_left">{PAGE_COLUMN_LEFT}</div>
                        <div id="ims-content">{PAGE_CONTENT}</div>
                        <div id="ims-column">{PAGE_COLUMN}</div>
                    </div>
                </div>
            </div>
            <!-- END: container_c_m_c -->
            <!-- BEGIN: container_m -->
            <div class="full {CONF.class_full}">
                <div id="container" class="container">
                    {PAGE_CONTENT}
                </div>
            </div>
            <!-- END: container_m -->
            <!-- BEGIN: container_full -->
            <div id="container">
                {PAGE_CONTENT}
            </div>
            <!-- END: container_full -->

            {data.brand_scroll}
            {data.top_footer}
            <footer class="bg-footer color-footer">
                <div class="container">
                    <div class="top">
                        <div class="footer_logo">
                            <div class="logo">
                                {data.footer_logo}
                            </div>
                            <div class="contact_footer">
                                {data.contact_footer}
                            </div>
                            {data.social}
                        </div>
                        <div class="list_menu">
                            <div class="menu menu">
                                {data.menu_footer}
                            </div>
                            <div class="menu menu1">
                                {data.menu_footer1}
                            </div>
                            <div class="menu menu2">
                                {data.menu_footer2}
                            </div>
                        </div>
                    </div>
                    <div class="bottom">
                        <div class="copyright">{LANG.global.copyright}</div>
                        <div class="menu3">{data.menu_footer3}</div>
                    </div>
                </div>
                <div class="container">{CONF.tag_footer}</div>
                {data.popup}
            </footer>
        </div>
        <div id="ims-scroll_left" class="{data.class_top}">{data.scroll_left}</div>
        <div id="ims-scroll_right" class="{data.class_top}">{data.scroll_right}</div>
        <div id="ims-loading"><div class="nb-spinner"></div></div>
        <div id="ims-data"></div>
        <div id="BactoTop" class="bg-color text-color" style="display: none;"><i class="far fa-chevron-up"></i></div>
        <div class="hotline sticky" onclick="document.location.href = 'tel:{CONF.hotline}'"><span>{CONF.hotline}</span></div>
        <div class='overlay'></div>
        <aside class="sideMenu">
            <div class="box_user" style="background: #C95B0E;"></div>
            <button class="navbar-toggler" type="button"><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></button>
            <div id="ims-side-menu">{data.menu_aside}</div>
        </aside>
        {data.menu_user}
        {CONF.include_js}
        {CONF.include_js_content}
        {CONF.embedcode_body}
        <script>
            // var check = 0;
            // $(window).on('load resize', function () {
            //     if (window.matchMedia('(max-width: 1200px)').matches) {
            //         var header = $('header').innerHeight();
            //         $('#ims-wrapper').css('padding-top', header);
            //     }else{
            //         $('#ims-wrapper').css('padding-top', 0);
            //     }
            //     if (window.matchMedia('(max-width: 1200px)').matches) {
            //         if(check == 0){
            //             var header_user = $('.header-bottom .header_user ul').html();
            //             $('.sideMenu .box_user').html('<ul class="list_none">'+header_user+'</ul>');
            //         }
            //         check = 1;
            //     }else{
            //         if(check == 1){
            //             $('#ims-side-menu > ul > li.add').remove();
            //             $('.sideMenu .box_user').html('');
            //         }
            //         check = 0;
            //     }
            // });
            // $(document).ready(function (){
            //     var html = '';
            //     html += $('.right_header .top ul.list_link').html();
            //     html += $('.right_header .bottom .right .list_right_cart').html()
            //     html += $('.bottom_header ul.right').html();
            //     $('#ims-side-menu > ul > li > .menu_sub > ul').append(html);
            //
            //     $('#ims-side-menu > ul > li > .menu_sub > ul').removeClass('item');
            //     $('#ims-side-menu > ul > li a > img, #ims-side-menu > ul > li a > i').remove();
            // });
            // $('#search i').on('click', function () {
            //     $('.box_search').toggleClass('show').find('.text_search').focus();
            // });
        </script>
    </body>
</html>
<!-- END: body -->

<!-- BEGIN: embed -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="vi">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>{CONF.meta_title}</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />        
        <meta name="description" content="{CONF.meta_desc}" />
        <meta name="keywords" itemprop="keywords" content="{CONF.meta_key}" />
        <meta property="og:type" content="website" />
        <meta property="og:title" name="title" content="{CONF.meta_title}" />
        <meta property="og:description" itemprop="description" name="description" content="{CONF.meta_desc}" />
        <meta property="og:url" itemprop="url" content="{CONF.canonical}" />
        <meta name="theme-color" content="{CONF.bgheader}">        
        <link rel="shortcut icon" href="{CONF.rooturl}favicon.ico" type="image/x-icon" />
        <link rel="icon" href="{CONF.rooturl}favicon.ico" type="image/x-icon" />
        <link rel="canonical" href="{CONF.canonical}" />
        {CONF.meta_more}
        <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900&display=swap&subset=vietnamese" rel="stylesheet">        
        <script language="javascript" >
            var ROOT = "{CONF.rooturl}";
            var DIR_IMAGE = "{DIR_IMAGE}";
            var deviceType = "{data.deviceType}";
            var lang = "{CONF.lang_cur}";
            var lang_js = new Array();
            {LANG_JS}
        </script>
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <script src="https://www.google-analytics.com/analytics.js"></script>
        {CONF.embedcode_head}
        <style type="text/css">{CONF.embed_style}</style>
    </head>

    <body>
        <!-- BEGIN: container_full -->
        <div id="container">
            {PAGE_CONTENT}
        </div>
        <!-- END: container_full -->
    </body>
</html>
<!-- END: embed -->

<!-- BEGIN: search -->
{PAGE_CONTENT}
<!-- END: search -->

<!-- BEGIN: main_slide -->
<div id="main_slide">  
    <div class="row_item">
        <!-- BEGIN: row -->
        <div class="item">
            <a href="{row.link}" target="{row.target}" {row.class}>
                {row.content_img}            
            </a>
        </div>
        <!-- END: row -->
    </div>
</div>
<!-- BEGIN: bo -->
<div class="title_more">
    <!-- BEGIN: title_more -->
    <a href="{row.link}" class="item">
        <div class="image">{row.icon}</div>
        <div class="text">
            <b>{row.title}</b>
            {row.short}
        </div>
    </a>
    <!-- END: title_more -->
</div>
<!-- END: bo -->
<script type="text/javascript">
    $("#main_slide .row_item").slick({
        autoplay:!0,
        autoplaySpeed:5000,
        speed:2000,
        swipe:!1,
        dots:!1,
        infinite:!0,
        slidesToShow:1,
        arrows: !0,
    });
</script>
<!-- END: main_slide -->

<!-- BEGIN: slide_in -->
<div id="banner_in">
    <div class="row_item">
        <!-- BEGIN: row -->
        <div class="item">
            <a href="{row.link}" target="{row.target}" {row.class}>
                {row.content_img}
            </a>
        </div>
        <!-- END: row -->
    </div>
    <div class="slide_text">
        <div class="container">
            <div class="wrap_title">
                <h2 class="title">{CONF.banner_title}</h2>
                {CONF.banner_nav}
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $("#banner_in .row_item").slick({
        autoplay:!0,
        autoplaySpeed:5000,
        speed:2000,
        swipe:!1,
        dots:!1,
        infinite:!0,
        slidesToShow:1,
        lazyload:"ondemand",
    })
</script>
<!-- END: slide_in -->

<!-- BEGIN: banner_slide -->
<div class="{data.banner}">
    <!-- BEGIN: row -->
    <a href="{row.link}" target="{row.target}" {row.class}>
        {row.title}
    </a>
    <!-- END: row -->
</div>
<script>
    $(".{data.banner}").slick({autoplay:!0,autoplaySpeed:2000,speed:1000,swipe:!1,dots:!1,infinite:!0,slidesToShow:1,lazyload:"ondemand",})
</script>
<!-- END: banner_slide -->

<!-- BEGIN: brand_scroll -->
<div class="brand_scroll">
    <div class="container">
        <div class="brand_scroll-content {data.class}">
            <!-- BEGIN: row -->
            <div class="item"><a target="_blank" href="{row.link}">{row.content_img}</a></div>
            <!-- END: row -->
        </div>
    </div>
</div>
<script>
    $(".brand_scroll-content").slick({
        arrows: !1,
        dots: !1,
        infinite: !0,
        autoplay: !0,
        autoplaySpeed: 3500,
        speed: 500,
        slidesToShow: 6,
        swipeToSlide: !0,
        responsive: [{
            breakpoint: 993,
            settings: {
                slidesToShow: 5,
                slidesToScroll: 3,
                infinite: !0
            }
        }, {
            breakpoint: 769,
            settings: {
                slidesToShow: 4,
                slidesToScroll: 3,
                infinite: !0
            }
        }, {
            breakpoint: 501,
            settings: {
                slidesToShow: 3,
                slidesToScroll: 2,
                infinite: !0
            }
        }]
    });
</script>
<!-- END: brand_scroll --> 


<!-- BEGIN: scroll_right_gallery -->
<div id="scroll-right-gallery">
    <div id="owl-right-gallery" class="owl-carousel">   
        <!-- BEGIN: row -->
        <div class="item">
            <a href="{row.content_popup}" class="fancybox-effects-a">
                {row.content}
            </a>
        </div>
        <!-- END: row -->
    </div>
</div>
<script language="javascript">
    jQuery(document).ready(function ($) {
        $("#owl-right-gallery").owlCarousel({
            pagination: false,
            navigation: true,
            singleItem: true,
            autoPlay: true,
            stopOnHover: true
        });
    });
</script>
<!-- END: scroll_right_gallery -->


<!-- BEGIN: news_slide -->
<ul class="slider">
    <!-- BEGIN: row -->
    <li>
        <div style="{row.style}">
            <h3><a href="{row.link}">{row.title}</a></h3>
                {row.content}
        </div>
    </li>
    <!-- END: row -->
</ul>
<script language="javascript">
    jQuery(document).ready(function ($) {
        $('.news_slide .slider').bxSlider({
            controls: true,
            pager: false,
            auto: true,
            mode: 'fade',
            speed: 800
        });
    });
</script>
<!-- END: news_slide -->


<!-- BEGIN: box_lang -->
<div id="box_lang">
    <ul class="list_none">
        <!-- BEGIN: row -->
        <li><a href="{row.link}" class="flag_{row.name} {row.current}">{row.name}</a></li>
        <!-- END: row -->
    </ul>
</div>
<!-- END: box_lang -->


<!-- BEGIN: header_user --> 
<div class="header_user">    
    <!-- BEGIN: is_login --> 
    <ul class="list_none log_group">
        <li class="info">
            <div class="text">{row.picture}<span>{row.full_name}</span><i class="far fa-angle-down"></i></div>
            <div class="box_signin">
                <div class="list_link">
                    <a href="{row.user.link}" class="link">{row.user.title}</a>
                    <a href="{row.change_pass.link}" class="link">{row.change_pass.title}</a>
                    <a href="{row.signout.link}" class="link" {row.signout.attr_link}>{row.signout.title}</a>                
                </div>
            </div>
        </li>
        <!-- BEGIN: bo -->
        <li class="noti"><a href="{row.notifications_link}"><i class="fal fa-bell"></i></a><span class="num">{row.num_no}</span></li>
        <!-- END: bo -->
    </ul>
    <!-- END: is_login -->
    <!-- BEGIN: not_login -->
    <!-- BEGIN: bo -->
    <div class="user_link">
        <i class="fad fa-user-circle"></i>
        <span class="text">
            {LANG.user.account}
            {row.product_watcheds}
        </span>
    </div>
    <div id="box_signin" class="box_signin">
        <a href="{data.link_signin}"><i class="fas fa-sign-in-alt"></i> <span class="text">{LANG.user.signin}</span></a>
        <a href="{data.link_signup}"><i class="fas fa-user"></i> <span class="text">{LANG.user.signup}</span></a>        
        <a href="{data.url_gg}" class="btn_c btn-social btn-google"><i class="fab fa-google"></i> <span class="text">{LANG.user.login_with_gg}</span></a>
        <a href="{data.url_fb}" class="btn_c btn-social btn-facebook"><i class="fab fa-facebook-f"></i> <span class="text">{LANG.user.login_with_fb}</span></a>
    </div>
    <!-- END: bo -->
    <ul class="list_none sign_group">
        <li><a href="{data.link_signup}"><i class="fas fa-user"></i> <span class="text">{LANG.user.signup}</span></a></li>
        <li><a href="{data.link_signin}"><!-- BEGIN: bo --><i class="fas fa-sign-in-alt"></i><!-- END: bo --><span class="text">{LANG.user.signin}</span></a></li>
    </ul>
    <!-- END: not_login -->
</div>
<!-- END: header_user -->


<!-- BEGIN: header_cart --> 
<div class="header_cart" id="header_cart">
    <a href="{data.link_cart}">
        <i class="fad fa-shopping-cart">
            <span class="num_cart">0</span>
        </i>
        <span class="text">
            <span class="num"><span class="num_cart">0</span> {LANG.global.products}</span>
            <b>{LANG.global.basket}</b>
        </span>
    </a>
    <div class="add-to-cart-success" style="display: none;">
        <span class="btn-close"><i class="far fa-times"></i></span>
        <p class="text"><i class="far fa-check-circle"></i>
            Thêm vào giỏ hàng thành công!
        </p>
        <a href="{data.link_cart}"><button class="btn btn-success">Xem giỏ hàng và thanh toán</button></a>
    </div>
    <script async="async" language="javascript">
        window.onload = function(e){ 
            header_cart();
        }
    </script>
</div>
<!-- END: header_cart -->


<!-- BEGIN: header_cart_old --> 
<a id="header_cart" href="{data.link_cart}">(<span class="num_cart">0<script language="javascript">header_cart();</script></span>)</a>
<!-- END: header_cart_old -->


<!-- BEGIN: menu_main_sub -->
<div class="sf-mega">
    <!-- BEGIN: menu_sub -->
    <div class="sf-mega-section">
        <h2><a href="{row.link}" target="{row.target}" {row.class}>{row.title}</a></h2>
        <ul class="list_none">
            <!-- BEGIN: row -->
            <li {col.class_li}><a href="{col.link}" target="{col.target}" {col.class}>{col.title}</a></li>
            <!-- END: row -->
        </ul>
    </div>
    <!-- END: menu_sub -->
</div>
<!-- END: menu_main_sub -->

<!-- BEGIN: menu_main -->
<ul class="list_none sf-menu {data.class}" {data.ul_ext}>
    <!-- BEGIN: item -->
    <li class="menu_li {row.class_li}" {row.attr_menu_li}><a href="{row.link}" target="{row.target}"  class="menu_link css_bo {row.class}">{row.title}</a>
        {row.menu_sub}
    </li>
    <!-- END: item -->
</ul>
<!-- END: menu_main -->

<!-- BEGIN: menu -->
<ul class="list_none menu-wrapper {data.class}" {data.ul_ext}>
    <!-- BEGIN: item -->
    <li class="menu_li {row.class_li}"><a href="{row.link}" target="{row.target}"  class="menu_link css_bo {row.class}">{row.icon_pic}<span class="text">{row.title}</span></a>
        {row.menu_sub}
        <!-- BEGIN: menu_sub -->
        <ul class="list_none">
            {row.content}
            <!-- BEGIN: row -->
            <li class="{row.class_li}"><a href="{row.link}" target="{row.target}" class="{row.class}">{row.icon_pic}<span class="text">{row.title}</span></a>{row.menu_sub}</li>
            <!-- END: row -->
        </ul>
        <!-- END: menu_sub -->
    </li>
    <!-- END: item -->
</ul>
<!-- END: menu -->

<!-- BEGIN: menu_aside -->
<ul class="list_none {data.class}" {data.ul_ext}>
    <!-- BEGIN: item -->
    <li class="menu_li {row.class_li}">
        <a href="{row.link}" target="{row.target}"  class="menu_link css_bo {row.class}">{row.title}</a>
        {row.menu_sub}
        <!-- BEGIN: menu_sub -->
        <div class="menu_sub">
            <!-- BEGIN: bo -->
            <h3>{row.title}</h3>
            <span class="menu_back" href="#"><i class="fad fa-chevron-left moveLeft"></i> {LANG.global.back}</span>
            <!-- END: bo -->
            <ul class="list_none">
                {row.content}
                <!-- BEGIN: row -->
                <li class="{row.class_li}">
                    <a href="{row.link}" target="{row.target}" class="{row.class}">{row.icon_pic}{row.title}</a>
                    {row.menu_sub}
                </li>
                <!-- END: row -->
            </ul>
        </div>
        <!-- END: menu_sub -->
    </li>
    <!-- END: item -->
</ul>
<!-- END: menu_aside -->

<!-- BEGIN: menu_bootstrap -->
<ul class="nav navbar-nav menu-wrapper {data.class}" {data.ul_ext}>
    <!-- BEGIN: item -->
    <li class="nav-item {row.class_li}"><a href="{row.link}" target="{row.target}"  class="nav-link dropdown-toggle {row.class}">{row.icon}<span class="text">{row.title}</span></a>
        {row.menu_sub}
        <!-- BEGIN: menu_sub -->
        <ul class="dropdown-menu">
            <div class="{data.class}">
                {row.overview}
                <div class="menu_sub">{row.content}</div>
            </div>
            <!-- BEGIN: row -->
            <li class="dropdown-item {row.class_li}"><a href="{row.link}" target="{row.target}" {row.class}>{row.icon_pic}<span class="text">{row.title}</span></a>{row.menu_sub}</li>
            <!-- END: row -->
        </ul>
        <!-- END: menu_sub -->
    </li>
    <!-- END: item -->
</ul>
<!-- END: menu_bootstrap -->

<!-- BEGIN: menu_footer -->
    <div class="footer-menu">
        <!-- BEGIN: item -->
        <div class="menu-item">
            <div class="footer-menu-title">{row.title}</div>
            {row.menu_sub}
            <!-- BEGIN: menu_sub -->
            <ul class="list_none">
                {row.content}
                <!-- BEGIN: row -->
                <li {row.class_li}><a href="{row.link}" target="{row.target}" {row.class}><i class="icon-angle-right"></i><span>{row.title}</span></a>{row.menu_sub}</li>
                <!-- END: row -->
            </ul>
            <!-- END: menu_sub -->
        </div>
        <!-- END: item -->
    </div>
<!-- END: menu_footer -->

<!-- BEGIN: menu_footer1 -->
<ul class="list_none">
    <!-- BEGIN: item -->
    <li><a href="{row.link}" target="{row.target}">{row.title}</a></li>
    {row.menu_sub}
    <!-- BEGIN: menu_sub -->
    <ul class="list_none">
        {row.content}
        <!-- BEGIN: row -->
        <li {row.class_li}><a href="{row.link}" target="{row.target}" {row.class}><i class="icon-angle-right"></i><span>{row.title}</span></a>{row.menu_sub}</li>
        <!-- END: row -->
    </ul>
    <!-- END: menu_sub -->
    <!-- END: item -->
</ul>
<!-- END: menu_footer1 -->

<!-- BEGIN: footer_contact --> 
<div class="footer_contact">
    <!-- BEGIN: row --> 
    <div class="footer_contact-detail css_bo">
        <div class="contact_short">{row.short}</div>
        <div class="contact_map">
            <div id="footer_map_view_{row.map_id}" class="map_view"></div>
            {row.contact_map}
        </div>
    </div>
    <!-- END: row -->
    <div class="clear"></div>
</div>
<!-- END: footer_contact -->


<!-- BEGIN: header -->
    <header class="bg-color color-header">
        <div class="header-top">
            <div class="container">
                <div class="text">{LANG.global.top_header}</div>
                <div class="right">
                    <div class="item"><a href=""><img src="{CONF.rooturl}resources/images/use/file.svg" alt="file">{LANG.global.file}</a></div>
                    <div class="item"><a href="mailto:{CONF.email}"><img src="{CONF.rooturl}resources/images/use/email_top.svg" alt="email">{CONF.email}</a></div>
                    <div class="item"><a href="tel:{CONF.hotline}"><img src="{CONF.rooturl}resources/images/use/phone_top.svg" alt="hotline">{CONF.hotline}</a></div>
                    <div class="item"><a href="tel:{CONF.hotline}"><img src="{CONF.rooturl}resources/images/use/contact_top.svg" alt="contact">{LANG.global.contact}</a></div>
                    {data.box_lang}
                </div>
            </div>
        </div>
        <div class="header-bottom">
            <div class="container">
                <div class="row m-0 flex-nowrap justify-content-between align-items-center">
                    <div class="logo">{data.logo}</div>
                    <div class="right_header">
                        <div class="top">
                            <div id="main_menu">
                                {data.list_menu}
                                <div id="search"><i class="fa-regular fa-magnifying-glass"></i>search</div>
                            </div>
                            {data.header_user}
                        </div>
                        <!-- BEGIN: bo -->
                        <div class="box_check">
                            <div class="check_order"><i class="fas fa-clipboard-list"></i> <span class="text">{LANG.global.check_order}</span></div>
                            <div class="panel_check">
                                <form id="check_order" method="post" name="check_order" novalidate="novalidate">
                                    <input class="text_input" name="order_code" type="" placeholder=" {LANG.global.text_order}" />
                                    <button class="btn-check" type="submit"> {LANG.global.check}</button>
                                </form>
                            </div>
                        </div>
                        <div class="box_notification">
                            <a class="noti_header" href="{data.notifications_link}">
                                <i class="fas fa-bell"><span class="num">{data.num_no}</span></i> <span class="text">{LANG.global.notification}</span>
                            </a>
                        </div>
                        <div class="user-tool">
                            <button class="btn user-toggler" type="button">{data.pic_user}</button>
                        </div>
                        <!-- END: bo -->
                        <div class="header-tool">
                            <button class="navbar-toggler" type="button" data-target="#ims-main-menu" aria-controls="ims-main-menu" aria-expanded="false" aria-label="Toggle navigation">
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
<!-- END: header -->