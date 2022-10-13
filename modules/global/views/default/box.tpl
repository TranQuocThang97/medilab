<!-- BEGIN: box_main -->
<div class="box_mid {data.class}">
    <div class="box_mid-title">
        <h1 class="mid_title_l">{data.title}</h1>
        <div class="mid_title_r">{data.more_title}</div>
    </div>
    <div class="box_mid-content">{data.content}</div>
</div>
<!-- END: box_main -->


<!-- BEGIN: box_main_news -->
<div class="box_mid {data.class}">
    <div class="box_mid-content">{data.content}</div>
</div>
<!-- END: box_main_news -->

<!-- BEGIN: box_main_product -->
    <div class="box_mid">
        <div class="box_mid-title box_header_product">
            <div class="title">{data.title}</div>
            <div class="sort_product">
                <label for="sort_product">
                    <select id="sort_product">
                        <option value="">{data.data_lang.select}</option>
                        <option value="{data.link_sort}?sort=stock-desc" data-check="stock-desc">{data.data_lang.stock_desc}</option>
                        <option value="{data.link_sort}?sort=new" data-check="new">{data.data_lang.new_product}</option>
                        <option value="{data.link_sort}?sort=price-asc" data-check="price-asc">{data.data_lang.price_asc}</option>
                        <option value="{data.link_sort}?sort=price-desc" data-check="price-desc">{data.data_lang.price_desc}</option>
                        <option value="{data.link_sort}?sort=title-asc" data-check="title-asc">{data.data_lang.title_asc}</option>
                        <option value="{data.link_sort}?sort=title-desc" data-check="title-desc">{data.data_lang.title_desc}</option>
                    </select>
                </label> 
            </div>       
        </div>
        {data.text_search}
        <div class="box_mid-content box_content_product" data-link="{data.link_product}">{data.content}</div>
    </div>
<!-- END: box_main_product -->

<!-- BEGIN: box_focus_product -->
    <div class="box_l_product hot_product d-none d-lg-block">    
        <div class="title">{LANG.product.hot_product}</div>
        <div id="product_left" class="list_product_item">    
        <!-- BEGIN: row -->
        <div class="item">
            <div class="image">
                <a href="{row.link}"><img src="{row.picture}" alt="{row.title}" title="{row.title}"></a>
            </div>
            <div class="info">
                <h3><a href="{row.link}">{row.title}</a></h3>
                <div class="rate">
                    <!-- BEGIN: rate -->                             
                        <!-- BEGIN: star -->
                            {row.average}
                        <!-- END: star -->
                        {row.num_rate}                
                    <!-- END: rate --> 
                </div>
                <div class="info-price">
                    <span class="price_buy">{row.price_buy}</span>
                    <!-- BEGIN: info_row_price -->
                    <span class="price">{price.price}</span>
                    <!-- END: info_row_price -->
                </div>
            </div>
        </div>
        <!-- END: row -->
       </div>
    </div>
<!-- END: box_focus_product -->

<!-- BEGIN: box_mid -->
    <div class="box_mid">
        <div class="box_mid-title">
            <h2 class="mid_title_l">{data.title}</h2>
            <div class="mid_title_r">{data.more_title}</div>
            <div class="clear"></div>
        </div>
        <div class="box_mid-content">{data.content}</div>
    </div>
<!-- END: box_mid -->

<!-- BEGIN: box -->
    <div class="box {data.class_box}">
        <div class="box-title"><span>{data.title}</span></div>
        <div class="box-content">{data.content}</div>
    </div>
<!-- END: box -->

<!-- BEGIN: box_notitle -->
<div class="box {data.class_box}">
    <div class="box-content">{data.content}</div>
</div>
<!-- END: box_notitle -->

<!-- BEGIN: box_menu_page -->
<div class="box_menu_left">
    <div class="title" style="color:{CONF.bgheader}">{data.title}</div>
    <ul class="list_none">
        <!-- BEGIN: row -->
        <li {row.class_li}><a href="{row.link}">{row.title}</a></li>
        <!-- END: row -->
        <!-- BEGIN: row_expand -->
        <li {row.class_li}><a href="{row.link}">{row.title}</a></li>
        <!-- END: row_expand -->
    </ul>
</div>
<!-- END: box_menu_page -->

<!-- BEGIN: box_menu_hidden -->
<div class="box box_menu_product">
        <!-- BEGIN: menu_sub -->
        <ul class="list_none">
            {data.content}
            <!-- BEGIN: row -->
            <li {row.class_li}>{row.input_choose}<a href="{row.link}" {row.class} {row.attr_link}><div>{row.title}</div>{row.open_sub}</a>{row.menu_sub}</li>
            <!-- END: row -->
        </ul>
        <!-- END: menu_sub -->
        <div class="clear"></div>
</div>
<!-- END: box_menu_hidden -->

<!-- BEGIN: box_menu -->
<div class="box box_menu_product">
        <!-- BEGIN: menu_sub -->
        <ul class="list_none">
            {data.content}
            <!-- BEGIN: row -->
            <li {row.class_li}>{row.input_choose}<a href="{row.link}" {row.class} {row.attr_link}><label for="check_box_{row.id_check}"><div>{row.title}</div>{row.open_sub}</label></a>{row.count} {row.menu_sub} </li>
            <!-- END: row -->
        </ul>
        <!-- END: menu_sub -->        
</div>
<!-- END: box_menu -->

<!-- BEGIN: box_menu_user -->
<!-- BEGIN: bo -->
<div class="user-tool d-lg-none">
    <button class="btn user-toggler" type="button">
        <i class="fad fa-user-cog"></i> Menu user
    </button>
</div>
<!-- END: bo -->
<div id="box_menu_user" {data.box_other}>
    <button class="btn user-toggler d-lg-none" type="button">
        <i class="far fa-times"></i>
    </button>
    <div class="user_info">
        <div class="user_img"><img src="{data.picture}" alt="{data.nickname}"/></div>
        <div class="user_name">
            <div class="name">{LANG.user.account_of}<span>{data.full_name}</span></div>
            <div class="level"><b>{data.level}</b></div>
        </div>
    </div>
    <div class="box box_menu_user">
        <!-- BEGIN: menu_sub -->
        <ul class="list_none">
            {data.content}
            <!-- BEGIN: row -->
            <li {row.class_li}>{row.input_choose}<a href="{row.link}" {row.class} {row.attr_link}><label>{row.title}</label>{row.open_sub}</label>{row.count}</a> {row.menu_sub} </li>
            <!-- END: row -->
        </ul>
        <!-- END: menu_sub -->
    </div>
</div>
<!-- END: box_menu_user -->

<!-- BEGIN: view_video -->
<!-- BEGIN: youtube -->
<div class="video">
    <div class="video_item">
        <a class="view" href="https://www.youtube.com/embed/{row.code_video}?rel=0&amp;modestbranding=1&amp;showinfo=0&autoplay=1" data-fancybox><img src="{row.picture}" alt="{row.title}"></a>
        <a class="title" href="https://www.youtube.com/embed/{row.code_video}?rel=0&amp;modestbranding=1&amp;showinfo=0&autoplay=1" data-fancybox><div class="button"></div>{row.titles}</a>
    </div>
</div>
<!-- END: youtube -->
<!-- BEGIN: file -->
<div class="video">
    <div class="video_item">
        <a class="view" data-fancybox data-animation-duration="700" data-src="#play_video{row.item_id}" href="javascript:;"><img src="{row.picture}" alt="{row.title}"></a>
        <a class="title" data-fancybox data-animation-duration="700" data-src="#play_video{row.item_id}" href="javascript:;"><div class="button"></div>{row.titles}</a>
        <div id="play_video{row.item_id}" class="play_video" style="display: none;">
            <video width="1000" height="auto" controls>
                <source src="{row.video_file}" type="video/mp4">
            </video>
        </div>
    </div>
</div>
<!-- END: file -->
<!-- END: view_video -->

<!-- BEGIN: banner_video -->
<div class="main_slide">
    <iframe src="https://www.youtube.com/embed/{row.code_video}?modestbranding=1&autohide=1&mute=1&autoplay=1&controls=0&fs=0&loop=1&rel=0&showinfo=0&disablekb=1&playlist={row.code_video}" width="100%" style="max-width: 100%; height: 300px" frameborder="0" allowfullscreen></iframe>
</div>
<script>
    if (window.matchMedia('(min-width: 993px)').matches) {
        setTimeout(function () {
            var height = $(window).height();
            $('.main_slide iframe').css('height', height);
        }, 500);
    }
    $(window).on('load resize', function(){
        if (window.matchMedia('(min-width: 993px)').matches) {
            height = $(window).height();
            $('.main_slide iframe').css('height', height);
        }else{
            $('.main_slide iframe').css('height', '300px');
        }
    });
</script>
<!-- END: banner_video -->

<!-- BEGIN: box_statistic -->
<div class="box bo_css box_statistic">
    <div class="box-title d-none">
        <div class="box-title-icon"></div>
        <div class="box-titleb">{LANG.global.box_statistic}</div>
    </div>
    <div class="box-content">
        <script language="javascript">imsStatistic.config = ({"full_zero" : false,"split_char" : true});</script>
        <div id="ims-statistic" class="statistic_content">
            <div class="row_online">
                <span class="col col_title pl-0">{LANG.global.sonline} :</span>
                <span class="col col_content" id="ims-sonline"></span>
                <div class="clear"></div>
            </div>
            <div class="row_sday">
                <span class="col col_title pl-0">{LANG.global.sday} :</span>
                <span class="col col_content" id="ims-sday"></span>
                <div class="clear"></div>
            </div>
            <div class="row_smonth">
                <span class="col col_title pl-0">{LANG.global.smonth} :</span>
                <span class="col col_content" id="ims-smonth"></span>
                <div class="clear"></div>
            </div>
            <div class="row_visitors">
                <span class="col col_title pl-0">{LANG.global.visitors} :</span>
                <span class="col col_content" id="ims-stotal"></span>
                <div class="clear"></div>
            </div>
        </div>
    </div>
</div>
<!-- END: box_statistic -->

<!-- BEGIN: box_search -->
<div class="box_search">
    <form id="{data.form_id}" action="{data.link_search}" method="get" autocomplete="off">
        <button class="btn_search" type="button"><i class="fas fa-search"></i>{LANG.global.btn_search_bo}</button>
        <input name="keyword" class="text_search" id="{data.input}" type="text" value="{data.keyword}" placeholder="{LANG.global.text_search}">
        <button class="btn_clear" type="button"><i class="fal fa-times"></i></button>
    </form>
</div>
<!-- END: box_search -->

<!-- BEGIN: product_focus -->
<div class="product_focus {data.class}">
    <!-- BEGIN: row -->
    <div class="product_focus-row">
        <div class="img"><a href="{row.link}" title="{row.title}"><img src="{row.picture}" alt="{row.title}" title="{row.title}" /></a></div>
        <div class="info">
            <h3><a href="{row.link}" title="{row.title}">{row.title}</a></h3>
            <!-- BEGIN: info_row_price -->
            <div class="price">{row.price}</div>
            <!-- END: info_row_price --> 
            <div class="price_buy">{row.price_buy}</div>
        </div>
    </div>
    <!-- END: row -->
    <div class="clear"></div>
</div>
<!-- END: product_focus --> 

<!-- BEGIN: news_hot_first -->
    <!-- BEGIN: row_first -->
    <div class="news_focus-row row_first">
        <div class="img"><a href="{row.link}" title="{row.title}"><img src="{row.picture}" alt="{row.title}" title="{row.title}" /></a></div>
        <div class="info">
            <a href="{row.link}" title="{row.title}">{row.title}</a>
            <div class="short">{row.short}</div>
        </div>
    </div>
    <!-- END: row_first -->
    <div class="news_list">
        <!-- BEGIN: row -->
        <div class="news_focus-row">
                <a href="{row.link}" title="{row.title}">{row.title}</a>
        </div>
        <!-- END: row -->
    </div>
    <div class="clear"></div>
<!-- END: news_hot_first -->


<!-- BEGIN: news_view_desc -->
        <!-- BEGIN: row -->
        <div class="news_item">
            <div class="image">
                <img src="{row.picture}"/>
            </div>
            <div class="title">
                   <a href="{row.link}" title="{row.title}">{row.title}</a>
            </div>
            <div class="clear"></div>
        </div>
        <!-- END: row -->
    <div class="clear"></div>
<!-- END: news_view_desc -->


<!-- BEGIN: news_focus -->
<div class="news_focus {data.class}">    
    <!-- BEGIN: row_first -->
    <div class="news_focus-row row_first">
        <div class="img"><a href="{row.link}" title="{row.title}"><img src="{row.picture}" alt="{row.title}" title="{row.title}" /></a></div>
        <div class="info">
            <h3><a href="{row.link}" title="{row.title}">{row.title}</a></h3>
            <div class="short">{row.short}</div>
        </div>
    </div>
    <!-- END: row_first -->
    <div class="news_right">
        <!-- BEGIN: row -->
        <div class="news_focus-row">
            <div class="info">
                <h3><a href="{row.link}" title="{row.title}">{row.title}</a></h3>
            </div>
        </div>
        <!-- END: row -->
    </div>
    <div class="clear"></div>
</div>
<!-- END: news_focus --> 

<!-- BEGIN: footer_product -->
<ul class="list_none">
    <!-- BEGIN: row -->
    <li><a href="{row.link}" title="{row.title}">{row.title}</a></li>
    <!-- END: row -->
</ul>
<!-- END: footer_product --> 

<!-- BEGIN: footer_news -->
<!-- BEGIN: row -->
<div class="item">
    <div class="date">
        <div class="day">{row.day}</div>
        <div class="month">{row.month}</div>
        <div class="year">{row.year}</div>
    </div>
    <div class="info">
        <h3><a href="{row.link}" title="{row.title}">{row.title}</a></h3>
        <div class="short">{row.short}</div>
    </div>
</div>
<!-- END: row -->
<!-- END: footer_new --> 

<!-- BEGIN: product_scroll -->
<div class="list_item_product">
<div class="product_scroll {data.class}">
    <!-- BEGIN: row -->
    <div class="item">{row.content}</div>
    <!-- END: row -->
</div>
</div>
<script language="javascript">imsOrdering.add_cart("form.form_add_cart")</script>
<!-- END: product_scroll --> 


<!-- BEGIN: form_signin_order -->
<form id="{data.form_id_pre}form_signin" name="{data.form_id_pre}form_signin" method="post" action="{data.link_action}" onSubmit="return false" >
    <div class="form_mess"></div>
    <div class="form-group">
        <label class="title">{LANG.user.username}</label>
        <span><input placeholder ="{LANG.user.text_email}" name="username" type="text" maxlength="100" value="{data.username}" class="form-control" /></span>
    </div>
    <div class="form-group">
        <label class="title">{LANG.user.password}</label>
        <span><input placeholder="{LANG.user.text_pass}" name="password" type="password" maxlength="100" value="{data.password}" class="form-control" /></span>
    </div>
    <div class="forget_password">
       {LANG.user.forget_pass} <a href="{data.link_forget_password}" target="_top"><b>{LANG.user.click_here}</b></a>
    </div>
    <div class="row_btn">
        <input type="hidden" name="do_submit"    value="1" />
        <button type="submit" class="btn" value="{LANG.user.btn_signin}" >{LANG.user.btn_signin}</button> 
        <a href="{data.link_signup}" class="btn justify-content-center" value="{LANG.user.btn_signup}" >{LANG.user.btn_signup}</a>    
        <a href="{data.url_gg}" class="btn_c btn-social btn-google"><i class="fab fa-google"></i> <span class="text">{LANG.user.login_with_gg}</span></a>
        <a href="{data.url_fb}" class="btn_c btn-social btn-facebook"><i class="fab fa-facebook-f"></i> <span class="text">{LANG.user.login_with_fb}</span></a>
    </div>
</form>
<!--<button class="skip_login_btn">
    {LANG.user.next_order}<i class="ficon-angle-double-right"></i></a>
</button>-->
<script>
    imsUser.signin('{data.form_id_pre}form_signin', '{data.link_login_go}');
</script>
<!-- END: form_signin_order -->


<!-- BEGIN: html_navigation -->
<div class="ims_navigation">
    <ol itemscope itemtype="http://schema.org/BreadcrumbList">
        <!-- BEGIN: row -->
        <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem" {row.class}>
            <a itemprop="item" href="{row.link}" {row.class}><span itemprop="name">{row.title}</span></a>
            <meta itemprop="position" content="{row.position}" />
        </li>
        <!-- END: row -->
    </ol>
    <div class="clear"></div>
</div>
<!-- END: html_navigation -->

<!-- BEGIN: html_list_share -->
<div class="list_share">
    <iframe src="//www.facebook.com/plugins/like.php?href={data.link_share}&amp;width=90px&amp;layout=button_count&amp;action=like&amp;show_faces=true&amp;share=false&amp;height=21;width=90" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:90px; height:21px;" allowTransparency="true"></iframe>

    <!-- Đặt thẻ này vào nơi bạn muốn Nút +1 kết xuất. -->
    <div class="g-plusone" data-size="medium" data-href="{data.link_share}"></div>

    <!-- Đặt thẻ này sau thẻ Nút +1 cuối cùng. -->
    <script type="text/javascript">
        window.___gcfg = {lang: 'vi'};

        (function () {
                var po = document.createElement('script');
                po.type = 'text/javascript';
                po.async = true;
                po.src = 'https://apis.google.com/js/platform.js';
                var s = document.getElementsByTagName('script')[0];
                s.parentNode.insertBefore(po, s);
            })();
    </script>

    <a href="https://twitter.com/share" class="twitter-share-button" data-lang="en">Tweet</a>

    <script>!function (d, s, id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document, "script", "twitter-wjs")
            ;</script>
</div>
<!-- END: html_list_share -->

<!-- BEGIN: html_alert_info -->
<div class="alert alert-info alert-dismissable">
    {data.mess}
</div>
<!-- END: html_alert_info -->

<!-- BEGIN: html_alert_error -->
<div class="alert alert-danger alert-dismissable">
    {data.mess}
</div>
<!-- END: html_alert_error -->

<!-- BEGIN: html_alert_warning -->
<div class="alert alert-warning alert-dismissable">
    {data.mess}
</div>
<!-- END: html_alert_warning -->

<!-- BEGIN: html_alert_success -->
<div class="alert alert-success alert-dismissable">
    {data.mess}
</div>
<!-- END: html_alert_success -->

<!-- BEGIN: alert -->
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>{CONF.page_title}</title>
    </head>

    <body>
        {data.content}
    </body>
</html>
<!-- END: alert -->


<!-- BEGIN: box_register_mail -->
<div class="register_email">
    <div class="footer_title">{LANG.global.register_mail_title}</div>
    <div class="small_title">{LANG.global.small_register_mail_title}</div>
    <form id="form_res_email" action="" method="post">
        <input type="text" name="email" id="email" class="register_input" placeholder="{LANG.global.enter_email} (*)"/>        
        <button class="btn_send" type="submit">{LANG.global.register_now}</button>
    </form>
    <script language="javascript">imsGlobal.emaillist('form_res_email');</script>
</div>
<!-- END: box_register_mail -->

<!-- BEGIN: product_register_mail -->
 <div class="register_email">
    <div class="container">
        <form id="form_res_email2" action="" method="post" class="row">
            <div class="title_register">
                {LANG.global.title_register_product}
            </div>
            <input type="text" name="email" id="email" class="email" placeholder="{LANG.global.text_register_mail}"/>
            <input class="btn_send" type="submit" value="{LANG.global.register_now}" />
            <div class="clear"></div>
        </form>
        <script language="javascript">imsGlobal.emaillist('form_res_email2');</script>
    </div>
</div>
<!-- END: product_register_mail -->


<!-- BEGIN:news_scroll -->
<div class="news_scroll">
<div class="title"><i></i>{data.title}</div>
<div class="news_scroll_content">
    <!-- BEGIN:row -->
    <div class="item_new">
        <div class="info">
            <div class="title">{row.title}</div>
            <div class="short">{row.short}</div>
            <div class="view_now"><a href="{row.link}">Xem ngay</a></div>
        </div>
        <div class="image">
            <img src="{row.picture}" title="{row.title}"  alt="{row.title}"/>
        </div>
        <div class="clear"></div>
    </div>
<!-- END:row -->
</div>
</div>
<!-- END:news_scroll -->

<!-- BEGIN: sort_product_top -->
<div class="sort_product">
    <span>{data.data_lang.sort_by}:</span>
    <div class="select">
        <select id="sort_product" class="centerSelect" onChange="window.location.href=this.value">
            <!-- BEGIN: bo -->
            <option value="">{data.data_lang.sort_by}</option>
            <!-- END: bo -->
            <!-- BEGIN: row -->
            <option value="{row.link}" {row.selected}>{row.title}</option>
            <!-- END: row -->
        </select>
    </div>
</div>
<!-- END: sort_product_top -->

<!-- BEGIN: sort_product_top_ajax -->
<div class="sort_product">
    <span>{data.data_lang.sort_by}:</span>
    <div class="select">
        <select id="sort_product">
            <!-- BEGIN: bo -->
            <option value="">{data.data_lang.sort_by}</option>
            <!-- END: bo -->
            <!-- BEGIN: row -->
            <option value="{row.link}" {row.selected}>{row.title}</option>
            <!-- END: row -->
        </select>
    </div>
</div>
<script>
    $('#sort_product').on('change', function (){
        var vl = $(this).val();
        $('input[name="order_by"]').val(vl);
        imsProduct.load_more(1);
    });
</script>
<!-- END: sort_product_top_ajax -->

<!-- BEGIN: sort_product_radio_top -->
<div class="sort_product">
    <span>{data.data_lang.view_by}</span>
    <!-- BEGIN: row -->
    <a href="{row.link}" {row.data}>{row.title}</a>
    <!-- END: row -->
</div>
<!-- END: sort_product_radio_top -->

<!-- BEGIN: sort_price_top -->
<div class="sort_price">    
    <div class="title">{data.title}</div>
    <div class="content_list_price horizontal">
        <!-- BEGIN: row -->
        <label class="{row.class}">
            <input id="check_box_price_{row.id}" type="checkbox" name="product_price[]" class="product_price_view" value="{row.value}">
            <div>{row.title}</div>
        </label>
        <!-- END: row -->
    </div>
</div>
<script>
    filter_checkbox(".content_list_price","price",1);
</script>
<!-- END: sort_price_top -->

<!-- BEGIN: sort_nature_top -->
<div class="sort_nature">    
    <span data-fancybox data-src="#boxNature" class="sort_nature_title">{LANG.product.arr_group_nature} <i class="fas fa-caret-down"></i></span>
    <div id="boxNature" style="display: none">
        <div class="content_list_nature">
            <!-- BEGIN: group -->
            <div class="group_nature {group.hidden}">
                <label><div>{group.title}</div></label>
                <ul class="list_none">
                <!-- BEGIN: item -->
                    <li class="{item.class}">
                        <input id="check_box_nature_{item.item_id}" type="checkbox" name="product_nature[]" class="product_nature_view" value="{item.item_id}">
                        <label for="check_box_nature_{item.item_id}">
                            <div>{item.title}</div>
                        </label>
                    </li>
                <!-- END: item -->
                </ul>
            </div>
            <!-- END: group -->
            <div class="btn_more {data.show}">{LANG.product.more_nature} <i class="fas fa-caret-down"></i></div>
        </div>
    </div>
</div>
<script>
    $("#boxNature").on("click",".btn_more",function(){
        $(".group_nature").removeClass('d-none');
        $(this).removeClass('show');
    })
</script>
<!-- END: sort_nature_top -->

<!-- BEGIN: product_group_top -->
<div class="list_group">
    <!-- BEGIN: row -->
    <a href="{row.link}" class="item">{row.title}</a>
    <!-- END: row -->
</div>
<!-- END: product_group_top -->

<!-- BEGIN: filter_product_top -->
<div class="box_filter">
    <p>{LANG.product.filter}:</p>
    <ul class="list_none filter_items list_none">
        <!-- BEGIN: filter_item -->
        <li data-type="{item.type}" data-value="{item.value}"><span>{item.title}</span><i class="fal fa-times"></i></li>
        <!-- END: filter_item -->
        <li class="clear-all" data-value="clear-all">{LANG.product.delete_all}</li>
    </ul>
</div>
<!-- END: filter_product_top -->


<!-- BEGIN: product_group_left -->
<div class="box_l_product">
    <label class="title">{LANG.product.product_group}</label>
    <div id="content_list_group">       
        <!-- BEGIN: item -->
        <h3 class="{item.class}">
            {item.action}    
        </h3>
        <div class="sub_group {item.box_class}">
        <!-- BEGIN: sub_item -->        
            <ul class="list_none">{row.menu_sub}</ul>        
        <!-- END: sub_item -->
        </div>
        <!-- END: item -->
    </div>
</div>
<script>
    $(function(){
        $( "#content_list_group" ).accordion({
            heightStyle: "content",
            collapsible: true,
            active: {data.cur_id},
        });
        $( "#content_list_group a" ).click(function(e) {
            e.stopPropagation();
        });
    });
</script>
<!-- END: product_group_left -->


<!-- BEGIN: product_trademark_left -->
<div class="box_l_product box_trademark">
    <div class="search input-effect">
        <!-- BEGIN: bo -->
        <input class="search_trademark effect-20" type="text" name="search">
        <!-- END: bo -->
        <label class="title">{LANG.product.trademark}</label>
        <!-- BEGIN: bo -->
        <span class="focus-border"><i></i></span>
        <i class="far fa-search mr-2"></i>
        <!-- END: bo -->
    </div>
    <ul id="trademarkLeft" class="content_list_trademark list_none">
        <!-- BEGIN: item -->
        <li>
            <input type="checkbox" name="brand" id="check_box_item_{item.id}" value="{item.id}">
            <label for="check_box_item_{item.id}"><div>
                {item.title}
                    <!-- BEGIN: bo -->
                <span class="num">({item.num_product})</span>
                    <!-- END: bo -->
            </div></label>

        </li>
        <!-- END: item -->
    </ul>
</div>
<script type="text/javascript">
    imsProduct.search_trademark();
    // wrapFilter(".content_list_trademark",4);
    filter_checkbox(".content_list_trademark","brand");
    $(".search .fa-search").on('click',function(){
        $("input.search_trademark").focus();
    })
</script>
<!-- END: product_trademark_left -->


<!-- BEGIN: arr_group_nature_left -->
<!-- BEGIN: group -->
<div class="box_l_product box_nature">
    <div class="title"><span>{group.title}</span></div>
    <ul class="content_list_nature list_none">
        <!-- BEGIN: item -->
        <li class="{item.class}">
            <input id="check_box_nature_{item.item_id}" type="checkbox" name="product_nature[]" class="product_nature_view" value="{item.item_id}">
            <label for="check_box_nature_{item.item_id}">
                <div>{item.title}
                    <!-- BEGIN: bo -->
                    <span class="num">({item.num_product})</span>
                    <!-- END: bo -->
                </div>
            </label>
        </li>
        <!-- END: item -->
    </ul>
</div>
<!-- END: group -->
<script type="text/javascript">
    filter_checkbox(".content_list_nature","nature");
    if($(".content_list_nature input:checkbox").is(':checked')){
        $(".content_list_nature").append('<div class="btn_clear">'+lang_js_mod['product']['clear_nature']+'</div>');
    }else{
        $(".content_list_nature .btn_clear").remove();
    }
    $(".content_list_nature").on("click",".btn_clear",function(){
        $(".content_list_nature input:checkbox").removeAttr('checked');
        window.location = removeURLParameter(document.URL, 'nature');
    })
</script>
<!-- END: arr_group_nature_left -->


<!-- BEGIN: search_price_left -->
    <div class="box_l_product box_price">
        <div class="title d-none">{LANG.product.search_price}</div>
        <ul class="content_list_price" {data.display}>
            <!-- BEGIN: item -->
            <li>
                <input id="check_box_price_{item.id}" type="checkbox" name="product_price[]" class="product_price_view" value="{item.value}">
                <label for="check_box_price_{item.id}" class="{item.class}">
                    <div>{item.title}</div>
                </label>                    
            </li>
            <!-- END: item -->
        </ul>
        <div id="slider_range_price">
            <div class="other_title">{LANG.product.orther_price}</div>
            <div id="search_price">
                <div id="slider-range-good"></div>
                <div class="row">
                    <div class="col-box">
                        <input type="text" name="price_min_search" class="input_search price auto_price" id="price_min" value="{data.price_min_search}">
                    </div>
                    <span class="min_max">-</span>
                    <div class="col-box">
                        <input type="text" name="price_max_search" class="input_search price auto_price" id="price_max" value="{data.price_max_search}">
                    </div>
                    <div class="col-button"><button type="button" class="btn-price-filter">{LANG.product.apply}<i class="fad fa-caret-right"></i></button></div>
                </div>
            </div>
        </div>
    </div>
<script>
    $(".btn-price-filter").on("click",function(){
        var ROOT_PRODUCT = document.URL;
        var val_min = $("#price_min").val().replace(" đ",'');
        var val_max = $("#price_max").val().replace(" đ",'');
        var val = val_min+"-"+val_max;
        val = val.replace(".",'');
        if(document.URL.indexOf('/?') > -1){
            var view_group = getUrlParameter('price');
            if(document.URL.indexOf('price=') > -1 || document.URL.indexOf('?price=') > -1 && view_group != ''){
                if(view_group != ''){
                    window.location = replaceUrlParam(ROOT_PRODUCT,'price',val);
                    return false;
                }
                else{
                    window.location = document.URL.replace('price=','price='+val);
                    return false;
                }
            }else{
                window.location = ROOT_PRODUCT + '&price=' + val;
                return false;
            }
        }
        var lastChar = ROOT_PRODUCT.substr(ROOT_PRODUCT.length - 1); // => "1"
        if(lastChar == '/'){
            window.location = ROOT_PRODUCT + '?price=' + val;
        }else{
            window.location = ROOT_PRODUCT + '/?price=' + val;
        }
    })
</script>
<!-- END: search_price_left -->


<!-- BEGIN: box_search_rate_left -->
    <div class="box_l_product box_rate">
        <div class="title">{LANG.global.rate}</div>
        <ul>
            <!-- BEGIN: item -->
            <li><a href="{item.link}" class="">{item.title}</a></li>
            <!-- END: item -->
        </ul>
    </div>
<!-- END: box_search_rate_left -->


<!-- BEGIN: box_tags_checkbox -->
<div class="box_l_product">
    <div class="title">{data.title}</div>
    <ul class="content_list_tag">
        <!-- BEGIN: row -->
        <li>
            <input type="checkbox" name="tag" id="check_box_tag_{item.id}" value="{item.title}">
            <label for="check_box_tag_{item.id}"><div>{item.title}</div></label>
        </li>
        <!-- END: row -->
    </ul>
</div>
<script>
    filter_checkbox(".content_list_tag","tag");
</script>
<!-- END: box_tags_checkbox -->


<!-- BEGIN: box_tags_link -->
    <div class="box_r">
        <div class="title">{data.title}</div>
        <ul class="content_list_taglink">
            <!-- BEGIN: row -->
            <li>
                <a href="{item.tag_link}">{item.title}</a>
            </li>
            <!-- END: row -->
        </ul>
    </div>
<!-- END: box_tags_link -->


<!-- BEGIN: box_product_color_left -->
<div class="box_l_product">
    <div class="title">{LANG.product.color}</div>
    <ul id="colorLeft" class="content_list_color">
        <!-- BEGIN: row -->
        <li>
            <input type="checkbox" name="brand" id="check_box_color_{item.id}" value="{item.color_id}">
            <label for="check_box_color_{item.id}"><div style="color:{item.color};"><i class="fas fa-tint"></i> {item.title}</div></label>
        </li>
        <!-- END: row -->
    </ul>
</div>
<script type="text/javascript">
    wrapFilter(".content_list_color",4);
    filter_checkbox(".content_list_color","color");
</script>
<!-- END: box_product_color_left -->

<!-- BEGIN: box_origin -->
<div class="box_l_product box_origin">
    <div class="title"><span>{title}</span></div>
    <ul class="content_list_origin list_none">
        <!-- BEGIN: item -->
        <li class="{item.class}">
            <input id="check_box_origin_{row.item_id}" type="checkbox" name="product_origin[]" class="product_origin_view" value="{row.item_id}">
            <label for="check_box_origin_{row.item_id}">
                <div>{row.title}
                    <!-- BEGIN: bo -->
                    <span class="num">({row.num_product})</span>
                    <!-- END: bo -->
                </div>
            </label>
        </li>
        <!-- END: item -->
    </ul>
</div>
<script type="text/javascript">
    filter_checkbox(".content_list_origin","origin");
</script>
<!-- END: box_origin -->


<!-- BEGIN: box_product_column -->
    <div class="box_product_column">
        <div class="title">{data.title}</div>
        <div class="content">
            {data.content}
            <button class="btn-next"><i class="fas fa-chevron-down"></i></button>
        </div>
    </div>
<!-- END: box_product_column -->


<!-- BEGIN: box_banner_top -->
    <div class="box_banner_product">
        <!-- BEGIN: row -->
        <div class="item"><img src="{row.picture}" alt="banner"></div>
        <!-- END: row -->
    </div>
<!-- END: box_banner_top -->


<!-- BEGIN: box_notification -->
    <div class="box_mid notification">
        <div class="box_mid-content">{data.content}</div>
    </div>
<!-- END: box_notification -->


<!-- BEGIn: list_promo_slider -->
    <!-- BEGIN: row -->
    <div class="item"><a href="{row.link}">{row.title}</a></div>
    <!-- END: row -->
    <script async="async">
        //initialize tiny slider    
        let slider = tns({
            container: ".list_ticker_promo",
            items: 1,
            slideBy: "page",
            autoplay: true,
            autoplayButtonOutput: false,
            loop: true,
            mouseDrag: true,
            controls: false,
            navPosition: "bottom",
            nav: true,
        });
    </script>
<!-- END: list_promo_slider -->


<!-- BEGIN: form_comment -->
<div id="tab-comment_rate">
    <div class="list_comment_title d-none">{LANG.global.list_comments}</div>
    <div id="list_comment" class="list_comment">
        <!-- BEGIN: list_comment -->
        <!-- BEGIN: item_comment -->
        {row.item_comment}
        <!-- END: item_comment -->
        <div class="div_more">
            <div class="fr">
                <div class="count_comment">
                    <span class="start">{data.start}</span> {LANG.global.among} <b class="total">{data.total_comment}</b>
                </div>
            </div>
            <div class="btn_loadmore {data.class}" data-start={data.start} data-max={data.max} data-type={data.type} data-type_id="{data.item_id}">{LANG.global.seemorecomments}</div>
        </div>
        <!-- END:list_comment -->
        <div class="clear"></div>
    </div>
    <div class="media">
        <div class="media-body">
            <!-- BEGIN: not_login -->
            <div class="text-center"><a href="{data.link_login}">{LANG.global.need_login}</a></div>
            <!-- END: not_login -->
            <!-- BEGIN: is_login -->
            {data.content_comment}
            <!-- END: is_login -->
        </div>
    </div>
    <div class="clear"></div>
    <script type="text/javascript">
        SharedComment.load_comment('root_form');
        SharedComment.postFavorite();
    </script>
</div>
<script type="text/javascript">
    jQuery(window).on("load",function () {
        setInterval(function(){
            $('.timeline-wrapper').remove();
            $('.timeline-none').removeClass('timeline-none');
        },1000);
    });
    $( document ).ready(function() {
        $(".load_iframe").click(function(e){
            e.preventDefault();
            var data_link = $(this).attr('data-link')+'?autoplay=1';
            var data_width = $(this).attr('data-width');
            var data_height = $(this).attr('data-height');
            loading('show');
            $(this).parent().html('<iframe width="'+ data_width +'" height="'+ data_height +'" src="'+ data_link +'" frameborder="0" allowfullscreen="allowfullscreen"></iframe>');
            loading('hide');
        });
    });
</script>
<!-- END: form_comment -->


<!-- BEGIN: item_comment -->
<div class="timeline-wrapper">
    <div class="timeline-item">
        <div class="animated-background">
            <div class="background-masker header-top"></div>
            <div class="background-masker header-left"></div>
            <div class="background-masker header-right"></div>
            <div class="background-masker header-bottom"></div>
            <div class="background-masker subheader-left"></div>
            <div class="background-masker subheader-right"></div>
            <div class="background-masker subheader-bottom"></div>
            <div class="background-masker content-top"></div>
            <div class="background-masker content-first-end"></div>
            <div class="background-masker content-second-line"></div>
            <div class="background-masker content-second-end"></div>
            <div class="background-masker content-third-line"></div>
            <div class="background-masker content-third-end"></div>
        </div>
    </div>
</div>
<div class="comment timeline-none comment{row.item_id}">
    <div class="comment-body">
        <div class="border_bottom">
            <div class="avatar">
                {row.full_name_first}
            </div>
            <div class="comment-box">
                <div class="comment-name"> {row.full_name} {row.rated}</div>
                <div class="comment-content">{row.content}</div>
                <div class="comment-picture">
                    <!-- BEGIN: video -->
                    <div class="video">
                        <a href="javascript:void(0)" class="load_iframe" data-link="{row.video}" data-width="280" data-height="220">
                            <div class="show_iframe"><i class="ficon-youtube-play"></i></div>
                        </a>
                    </div>
                    <!-- END: video -->
                    <!-- BEGIN: pic -->
                    <div class="item">
                        <a href="{row.picture_full}" class="fancybox-effects-a">
                            <img src="{row.picture}" alt="{row.full_name}">
                        </a>
                    </div>
                    <!-- END: pic -->
                </div>
                <div class="comment-bottom">
                    <div class="like" data-like="{row.item_id_base64}" data-type="shared_comment">
                        <i class="{row.class_like}"></i> {LANG.global.like}
                        <span class="num">{row.num_like}</span>
                    </div>
                    <div class="reply">
                        <i class="fal fa-comment-alt"></i> {LANG.global.reply}
                        <span class="num">{row.num_comment}</span>
                    </div>
                    <div class="date">{row.time}</div>
                </div>
                <div class="comment-form-sub">
                    {row.item_comment}
                    <!-- BEGIN: more -->
                    <div class="div_more">
                        <div class="fr">
                            <div class="count_comment">
                                <span class="start">{row.start}</span> {LANG.global.among} <b class="total">{row.num_comment}</b>
                            </div>
                        </div>
                        <div class="btn_loadmore {data.classs}" data-start="{row.start}" data-max="{row.num_comment}" data-type="{row.type}"  data-parent_id="{row.item_id}" data-type_id="{row.type_id}">{LANG.global.seemorecomments}</div>
                    </div>
                    <!-- END: more -->
                    {row.content_comment}
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END: item_comment -->


<!-- BEGIN: content_comment -->
<form id="{data.form_id_pre}form" name="{data.form_id_pre}form"  class="form" method="post" onSubmit="return false" >
    <div class="form_mess"></div>
    {data.rate}
    <div class="group-form">
        <textarea name="txtaComment" cols="30" rows="5" class="input" placeholder="{LANG.global.your_comments}" id="txtaComment"></textarea>
    </div>
    <div class="group-form boxemotion_bottom">
        <!-- BEGIN: bo -->
        <div class="boxemotion fl">
            <div class="button_file">
                <span><i class="ficon-camera"></i> Gửi ảnh</span>
                <input data-form="{data.form_id_pre}form" accept="image/gif, image/jpeg, image/png" type="file" name="files[]" class="file_input" multiple>
            </div>
            <input type="text" placeholder="Gửi video bằng link Youtube(nếu có)" class="form-control video" name=txtVideo>
        </div>
        <!-- END: bo -->
        <button href="javascript:void(0);" data-parent_id="{data.parent_id}" data-type_id="{data.item_id}" data-type="{data.type}" type="submit" class="btn bg-color text-color button-submit">{LANG.global.send}</button>
    </div>
    <div class="box_picture">
        <div class="Uploadbtn"></div>
        <div class="file_show">{data.picture}</div>
    </div>
</form>
<script type="text/javascript">
    SharedComment.post_comment('{data.form_id_pre}form');
</script>
<!-- END: content_comment -->


<!-- BEGIN: form_rate -->
<div class="boxRatingCmt" id="boxRatingCmt">
    <div class="hrt" id="danhgia">
        <div class="tltRt ">
            <h3>{data.num} {LANG.product.rate} <b>{data.product.title}</b></h3>
        </div>
    </div>
    <div class="toprt">
        <div class="crt">
            <div class="lcrt bg-text-color">
                <b>{data.average}<i class="ficon-star"></i></b>
            </div>
            <div class="rcrt">
                <div class="r">
                    <span class="t">5 <i class="ficon-star"></i></span>
                    <div class="bgb">
                        <div class="bgb-in bg-color" style="width: {data.count_5percent}%"></div>
                    </div>
                    <span class="c"><strong>{data.count_5star}</strong> {LANG.product.rate}</span>
                </div>
                <div class="r">
                    <span class="t">4 <i class="ficon-star"></i></span>
                    <div class="bgb">
                        <div class="bgb-in bg-color" style="width: {data.count_4percent}%"></div>
                    </div>
                    <span class="c"><strong>{data.count_4star}</strong> {LANG.product.rate}</span>
                </div>
                <div class="r">
                    <span class="t">3 <i class="ficon-star"></i></span>
                    <div class="bgb">
                        <div class="bgb-in bg-color" style="width: {data.count_3percent}%"></div>
                    </div>
                    <span class="c n"><strong>{data.count_3star}</strong> {LANG.product.rate}</span>
                </div>
                <div class="r">
                    <span class="t">2 <i class="ficon-star"></i></span>
                    <div class="bgb">
                        <div class="bgb-in bg-color" style="width: {data.count_2percent}%"></div>
                    </div>
                    <span class="c n"><strong>{data.count_2star}</strong> {LANG.product.rate}</span>
                </div>
                <div class="r">
                    <span class="t">1 <i class="ficon-star"></i></span>
                    <div class="bgb">
                        <div class="bgb-in bg-color" style="width: {data.count_1percent}%"></div>
                    </div>
                    <span class="c n"><strong>{data.count_1star}</strong> {LANG.product.rate}</span>
                </div>
            </div>
            <div class="bcrt">
                <a href="javascript:void(0)" class="showInputRating bg-color text-color">Gửi đánh giá của bạn</a>
            </div>
        </div>
        <div class="clr"></div>
        <form class="input fRatingComment" name="fRatingComment" style="display: none;">
            <input type="hidden" name="hdfStar" id="hdfStar" value="3">
            <input type="hidden" name="hdfProductID" id="hdfProductID" value="203207">
            <input type="hidden" name="hdfRatingImg" class="hdfRatingImg">
            <div class="ips">
                <span>Chọn đánh giá của bạn</span>
                <span class="lStar">
                    <div id="rate"></div>
                    <script type="text/javascript">
                        $('#rate').starrr({
                            max: 5,
                            rating: 0,
                            change: function(e, value){
                                loading('show');
                                $.ajax({
                                    type: "POST",
                                    url: ROOT+"ajax.php",
                                    data: { "m" : "shared_comment", "f" : "postRate", "lang_cur" : lang, "value" : value, "type_id" : {data.product.item_id},  "type" : "product" }
                                }).done(function( string ) {
                                    loading('hide');
                                    var data = JSON.parse(string);
                                    var html = '';
                                    if(data.ok == 1) {
                                        jAlert(data.mess, lang_js['aleft_title'],' ', 'success');
                                    }else{
                                        jAlert(data.mess, lang_js['aleft_title'],' ', 'error');
                                    }
                                });
                            }
                        });
                    </script>
                </span>
            </div>
        </form>
    </div>
</div>
<!-- END: form_rate -->


<!-- BEGIN: form_comment_rate -->
<div class="boxRatingCmt" id="boxRatingCmt">
    <div class="hrt" id="danhgia">
        <div class="tltRt ">
            <h3>{data.num} {LANG.product.rate} <b>{data.product.title}</b></h3>
        </div>
    </div>
    <div id="tab-comment_rate" class="toprt">
        <div class="crt">
            <div class="lcrt bg-text-color">
                <div class="average_rate">{LANG.product.average_rate}</div>
                <b>{data.average}/5
                    <span>
                        <!-- BEGIN: star -->
                        {data.star}
                        <!-- END: star -->
                    </span>
                </b>
                <!-- BEGIN: total_rate -->
                <div class="total_rate">({data.total_rate} {LANG.product.rate})</div>
                <!-- END: total_rate -->
            </div>
            <div class="rcrt">
                <div class="r">
                    <span class="t">5 <i class="fas fa-star"></i></span>
                    <div class="bgb">
                        <div class="bgb-in bg-color" style="width: {data.count_5percent}%"></div>
                    </div>
                    <span class="c"><strong>{data.count_5star}</strong> {LANG.product.rate}</span>
                </div>
                <div class="r">
                    <span class="t">4 <i class="fas fa-star"></i></span>
                    <div class="bgb">
                        <div class="bgb-in bg-color" style="width: {data.count_4percent}%"></div>
                    </div>
                    <span class="c"><strong>{data.count_4star}</strong> {LANG.product.rate}</span>
                </div>
                <div class="r">
                    <span class="t">3 <i class="fas fa-star"></i></span>
                    <div class="bgb">
                        <div class="bgb-in bg-color" style="width: {data.count_3percent}%"></div>
                    </div>
                    <span class="c n"><strong>{data.count_3star}</strong> {LANG.product.rate}</span>
                </div>
                <div class="r">
                    <span class="t">2 <i class="fas fa-star"></i></span>
                    <div class="bgb">
                        <div class="bgb-in bg-color" style="width: {data.count_2percent}%"></div>
                    </div>
                    <span class="c n"><strong>{data.count_2star}</strong> {LANG.product.rate}</span>
                </div>
                <div class="r">
                    <span class="t">1 <i class="fas fa-star"></i></span>
                    <div class="bgb">
                        <div class="bgb-in bg-color" style="width: {data.count_1percent}%"></div>
                    </div>
                    <span class="c n"><strong>{data.count_1star}</strong> {LANG.product.rate}</span>
                </div>
            </div>
            <div class="bcrt">
                <a href="javascript:void(0)" class="showInputRating bg-color text-color">Gửi đánh giá của bạn</a>
            </div>
        </div>
        <div class="clr"></div>
        <div class="fRatingComment" style="display: none">
            <div class="ips">
                <span>Chọn đánh giá của bạn</span>
                <span class="lStar">
                    <div id="rate"></div>
                </span>
            </div>
            <div class="media">
                <div class="media-body">
                    <!-- BEGIN: not_login -->
                    <div class="text-center"><a href="{data.link_login}">{LANG.global.need_login}</a></div>
                    <!-- END: not_login -->
                    <!-- BEGIN: is_login -->
                    {data.content_comment}
                    <!-- END: is_login -->
                </div>
            </div>
            <div class="clear"></div>
            <script type="text/javascript">
                SharedComment.load_comment('root_form');
                SharedComment.postFavorite();
            </script>
        </div>
        <div class="list_comment_title d-none">{LANG.global.list_comments}</div>
        <div id="list_comment" class="list_comment">
            <!-- BEGIN: list_comment -->
            <!-- BEGIN: item_comment -->
            {row.item_comment}
            <!-- END: item_comment -->
            <div class="div_more">
                <div class="fr">
                    <div class="count_comment">
                        <span class="start">{data.start}</span> {LANG.global.among} <b class="total">{data.total_comment}</b>
                    </div>
                </div>
                <div class="btn_loadmore {data.class}" data-start={data.start} data-max={data.max} data-type={data.type} data-type_id="{data.item_id}">{LANG.global.seemorecomments}</div>
            </div>
            <!-- END:list_comment -->
            <div class="clear"></div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(window).on("load",function () {
        setInterval(function(){
            $('.timeline-wrapper').remove();
            $('.timeline-none').removeClass('timeline-none');
        },1000);
    });
    $( document ).ready(function() {
        $('#rate').starrr({
            max: 5,
            rating: 0,
            change: function(e, value){
                $("#root_form input[name='rate']").val(value);
            }
        })
        $(".load_iframe").click(function(e){
            e.preventDefault();
            var data_link = $(this).attr('data-link')+'?autoplay=1';
            var data_width = $(this).attr('data-width');
            var data_height = $(this).attr('data-height');
            loading('show');
            $(this).parent().html('<iframe width="'+ data_width +'" height="'+ data_height +'" src="'+ data_link +'" frameborder="0" allowfullscreen="allowfullscreen"></iframe>');
            loading('hide');
        });
    });
</script>
<!-- END: form_comment_rate -->


<!-- BEGIN: form_contact -->
    <form id="{data.id}" name="form_contact" method="post" data-go="{data.link_go}">
        <div class="form_note">(*) {LANG.contact.required}</div>
        <div class="row">
            <div class="col-md-6 col-12 pr-md-2">            
                <input name="title" type="hidden" value="{data.title}"/>
                <div class="form-group input-effect">
                    <input name="full_name" type="text" maxlength="250" value="{data.full_name}" class="form-control effect-1" placeholder="{LANG.contact.full_name} (*)" />
                    <span class="focus-border"></span>
                </div>
            </div>
            <div class="col-md-6 col-12 pl-md-2">
                <div class="form-group input-effect">
                    <input name="email" type="text" maxlength="250" value="{data.email}" class="form-control effect-1" class="form-control effect-1" placeholder="{LANG.contact.email} (*)" />
                    <span class="focus-border"></span>
                </div>
            </div>
            <div class="col-md-6 col-12 pr-md-2">
                <div class="form-group input-effect">
                    <input name="phone" type="text" maxlength="250" value="{data.phone}" class="form-control effect-1" placeholder="{LANG.contact.phone}" />
                    <span class="focus-border"></span>
                </div>
            </div>
            <div class="col-md-6 col-12 pl-md-2">
                <div class="form-group input-effect">
                    <input name="address" type="text" maxlength="250" value="{data.address}" class="form-control effect-1" placeholder="{LANG.contact.address}" />
                    <span class="focus-border"></span>
                </div>
            </div>
            <div class="col-12">
                <div class="form-group input-effect">
                    <textarea name="content" class="form-control effect-1" rows="3" placeholder="{LANG.contact.content} (*)" >{data.content}</textarea>
                    <span class="focus-border"></span>
                </div>
            </div>
            <div class="col-12">
                <div class="form-group input-effect">
                    <input type="hidden" name="do_submit" value="1" />
                    <input type="submit" class="btn btn-contact" value="{LANG.contact.btn_register}"/>
                </div>
            </div>
        </div>
    </form>
<!-- END: form_contact -->


<!-- BEGIN: form_address_book -->
    <form id="form_address_book{data.form_id}" class="form_address_book" method="post" style="display: none;">
        <div class="title">{LANG.product.order_address_edit}</div>
        <div class="row">
            <div class="col-12">
                <div class="form_mess"></div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-12">
                <div class="form-group">
                    <label class="title">{LANG.product.full_name} <span class="required">*</span></label>
                    <input placeholder="{LANG.product.text_full_name}" name="full_name" type="text" maxlength="100" class="form-control" required value="{data.full_name}"/>
                </div>
            </div>
            <div class="col-md-6 col-12">
                <div class="form-group">
                    <label class="title">{LANG.product.province} <span class="required">*</span></label>
                    {data.list_location_province}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-12">
                <div class="form-group">
                    <label class="title">{LANG.product.email} <span class="required">*</span></label>
                    <input placeholder="{LANG.product.text_email}" name="email" type="text" maxlength="100" class="form-control" required value="{data.email}"/>
                </div>
            </div>
            <div class="col-md-6 col-12">
                <div class="form-group">
                    <label class="title">{LANG.product.district} <span class="required">*</span></label>
                    {data.list_location_district}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-12">
                <div class="form-group">
                    <label class="title">{LANG.product.phone} <span class="required">*</span></label>
                    <input placeholder="{LANG.product.text_phone}" name="phone" type="text" maxlength="100" class="form-control" required value="{data.phone}"/>
                </div>
            </div>
            <div class="col-md-6 col-12">
                <div class="form-group">
                    <label class="title">{LANG.product.ward} <span class="required">*</span></label>
                    {data.list_location_ward}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label class="title">{LANG.product.address} <span class="required">*</span></label>
                    <input placeholder="{LANG.product.text_address}" name="address" type="text" maxlength="100" class="form-control" value="{data.address}" required/>
                </div>
                <div class="form-group row_c">
                    <div class="row-title row-checkbox">
                        <input type="checkbox" name="is_default" class="toggle_panel" id="is_default_{data.id_form}" value="1" {data.checked}>
                        <label for="is_default_{data.id_form}"><span>{LANG.user.default_address}</span></label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="form-content">
                        <input type="hidden" name="submit" value="1">
                        <input type="hidden" name="type" value="{data.type}">
                        <input type="hidden" name="id" value="{data.id}">
                        <input type="button" onclick="$.fancybox.close()" class="btn mr-2" value="{LANG.product.cancel}"/>
                        <button type="submit" id="btn_confirm_address" class="btn bg-color text-color btn_custom">{LANG.product.confirm}</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
<!-- END: form_address_book -->

<!-- BEGIN: watched_product -->
<div class="watched_product">
    <div class="watched_title"><span>{LANG.product.watched_product}</span></div>
    {content}
</div>
<script>
    $(".watched_product .row_item").slick({
        arrows: !0,
        dots: !1,
        infinite: !0,
        autoplay: !0,
        autoplaySpeed: 3500,
        speed: 500,
        slidesToShow: 5,
        swipeToSlide: !0,
        lazyload:"ondemand",
        responsive: [{
            breakpoint: 1101,
            settings: {
                slidesToShow: 4,
            }
        }, {
            breakpoint: 769,
            settings: {
                slidesToShow: 3,
            }
        }, {
            breakpoint: 601,
            settings: {
                slidesToShow: 2,
            }
        }, {
            breakpoint: 365,
            settings: {
                slidesToShow: 1,
            }
        }]
    });
    // matchHeight('.watched_product .item .brand');
    // matchHeight('.watched_product .item .info-title');
</script>
<!-- END: watched_product -->


<!-- BEGIN: box_menu_user_checkin -->
<div id="box_menu_user_checkin" {data.box_other}>
    <div class="box_menu_user_checkin">        
        <ul class="list_none">
            {data.content}
            <!-- BEGIN: row -->
            <li {row.class_li}>
                <a href="{row.link}" title="{row.title}" {row.class} {row.attr_link}>
                    {row.icon}{row.icon_active}{row.open_sub}{row.count}
                </a> {row.menu_sub}
            </li>
            <!-- END: row -->
        </ul>
    </div>
</div>
<!-- END: box_menu_user_checkin -->

--------------------------------------END--------------------------------------
<!-- BEGIN: top_footer -->
<div class="top_footer">
    <div class="container">
        <div class="left">
            <div class="event_title">{LANG.global.event_footer}</div>
            <div class="list_item">{event}</div>
            <div class="event_title2">{LANG.global.upcoming_events}</div>
        </div>
        <div class="form">
            <form action="" method="post">
                <div class="row">
                    <div class="form-group col-12 col-md-6">
                        <label class="input_title">{LANG.global.first_name} <span>*</span></label>
                        <input type="text" name="first_name">
                    </div>
                    <div class="form-group col-12 col-md-6">
                        <label class="input_title">{LANG.global.last_name} <span>*</span></label>
                        <input type="text" name="last_name">
                    </div>
                    <div class="form-group col-12 col-md-6">
                        <label class="input_title">{LANG.global.email} <span>*</span></label>
                        <input type="text" name="email">
                    </div>
                    <div class="form-group col-12 col-md-6">
                        <label class="input_title">{LANG.global.phone} <span>*</span></label>
                        <input type="text" name="phone">
                    </div>
                    <div class="form-group col-12 col-md-6">
                        <label class="input_title">{LANG.global.position} <span>*</span></label>
                        <input type="text" name="position">
                    </div>
                    <div class="form-group col-12 col-md-6">
                        <label class="input_title">{LANG.global.company_name} <span>*</span></label>
                        <input type="text" name="company_name">
                    </div>
                    <div class="form-group col-12">
                        <label class="input_title">{LANG.global.country} <span>*</span></label>
                        <div class="select">
                            <select name="country">
                                <option value="">{LANG.global.select_country}</option>
                                <!-- BEGIN: country -->
                                <option value="{row.code}">{row.title}</option>
                                <!-- END: country -->
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-12">
                        <label class="input_title">{LANG.global.im_find} <span>*</span></label>
                        <div class="select">
                            <select name="service">
                                <option value="">{LANG.global.select_service}</option>
                                <!-- BEGIN: service -->
                                <option value="{row.item_id}">{row.title}</option>
                                <!-- END: service -->
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-12 text-right">
                        <button>{LANG.global.send}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- BEGIN: event_js -->
<script>
    $('.top_footer .left .list_item').slick({
        autoplay: false,
        slidesToShow: 1,
        dots: false,
        arrows: true,
        swipeToSlide: true,
    });
</script>
<!-- END: event_js -->
<!-- END: top_footer -->