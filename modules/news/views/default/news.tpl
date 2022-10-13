<!-- BEGIN: main -->
{data.location}
{data.content}
<button type="button" class="btn btn-primary" id="submit">HCM</button>
<input type="text" name="location">
<script type="text/javascript">

    $(document).on("click", "#submit", function(){
        // $.ajax({
        //     type: "POST",
        //     url: "{data.link_action}",
        //     data: {"f": "load_province", "data": 'hcm'}
        // }).done(function (string) {
        //     console.log(string);
            history.pushState(null, '', '{data.link_action}'+'?location='+$('[name="location"]').val());
            $("#ims-wrapper").load(window.location.href + " #ims-wrapper>*",function(){
                // history.pushState(null, '','{data.link_action}');
            });
        // })
    })
    
</script>
<!-- END: main -->

<!-- BEGIN: main_news -->
<div class="box_mid news_content">
    {data.group_title}
    {data.tab_news}
    {data.group1}
    {data.group2}
    {data.video_group3}
    <div class="main_news">
        <div class="list_news">
            {data.content}
        </div>
        {data.most_read}
    </div>
</div>
<!-- END: main_news -->

<!-- BEGIN: detail -->
<div id="item_detail">
    <h1>{data.title}</h1>
    <div class="date">{data.group_name}<span>{data.date_create}</span></div>
    <div id="category"></div>
    <div class="item-content">{data.content}</div>

    <!-- BEGIN: bo -->
    <div class="tool-share ml-auto">
        <p class="title">{LANG.news.share_this}</p>
        <ul class="list_none rrssb-buttons rrssb-1">
            <li class="btn-facebook">
                <a href="https://www.facebook.com/sharer/sharer.php?u={data.link_share}" class="popup">
                    <span class="icon"><i class="fab fa-facebook-square"></i></span>
                </a>
            </li>
            <li class="btn-twitter">
                <a href="https://twitter.com/intent/tweet?text={data.link_share}" class="popup">
                    <span class="icon"><i class="fab fa-twitter-square"></i></span>
                </a>
            </li>
            <li class="zalo-share-button" data-href="" data-oaid="579745863508352884" data-layout="3" data-color="white" data-customize=false></li>
        </ul>
        <script src="https://sp.zalo.me/plugins/sdk.js"></script>
    </div>
    <!-- END: bo -->
</div>
{data.other}
<script>
    var htm = '',
        title = '<div class="category_title">{LANG.news.category_title}</div>';
    $('.item-content h3').each(function (index){
        if($(this).text() != ''){
            $(this).attr('id', 'cate'+index);
            htm += '<li><a href="#cate'+index+'" class="goto">'+$(this).text()+'</a></li>';
        }
    });
    if(htm != ''){
        $('#category').addClass('show').append(title+'<ul class="list_none">'+htm+'</ul>');
    }
</script>
<!-- END: detail -->

<!-- BEGIN: list_other -->
<div class="list_other">
    <div class="list_other-title">
        <span>{LANG.news.other_news}</span>
        <!-- BEGIN: bo -->
        <ul class="list_none slide-control">
            <li class="btn-arrow btn-prev"><i class="fal fa-chevron-left"></i></li>
            <li class="btn-arrow btn-next"><i class="fal fa-chevron-right"></i></li>
        </ul>
        <!-- END: bo -->
    </div>
    {content}
</div>
<script>
    $('.list_other .list_item_news .row_item').slick({
        autoplay: true,
        slidesToShow: 3,
        dots: false,
        arrows: false,
        swipeToSlide: true,
        responsive: [
            {
                breakpoint: 1101,
                settings: {
                    slidesToShow: 2,
                }
            },
            {
                breakpoint: 901,
                settings: {
                    slidesToShow: 3,
                }
            },
            {
                breakpoint: 701,
                settings: {
                    slidesToShow: 2,
                }
            },
            {
                breakpoint: 450,
                settings: {
                    slidesToShow: 1,
                    dots: true,
                }
            }
        ]
    });
    //$(".list_other .btn-prev").on("click",function(){o.slick("slickPrev");})
    //$(".list_other .btn-next").on("click",function(){o.slick("slickNext");})
</script>
<!-- END: list_other -->
-------------- END -------------------
<!-- BEGIN: tab_news -->
<div class="tab_news">
    <ul class="nav nav-pills">
        <!-- BEGIN: li -->
        <li class="nav-item"><a href="#tab_{row.group_id}" data-toggle="tab" class="{row.active}">{row.title}</a></li>
        <!-- END: li -->
    </ul>
    <div class="tab-content">
        <!-- BEGIN: content -->
        <div class="tab-pane {row.active}" id="#tab_{row.group_id}">{row.content}</div>
        <!-- END: content -->
    </div>
</div>
<!-- END: tab_news -->

<!-- BEGIN: lasted_focus1 -->
<div class="lasted_focus1">
    <!-- BEGIN: left -->
    <div class="left">
        <div class="img"><a href="{row.link}"><img src="{row.picture}" alt="{row.title}"></a></div>
        <div class="title"><a href="{row.link}">{row.title}</a></div>
    </div>
    <!-- END: left -->
    <div class="list_right">
        <div class="list_item">
            <!-- BEGIN: item -->
            <div class="item">
                <div class="img"><a href="{row.link}"><img src="{row.picture}" alt="{row.title}"></a></div>
                <div class="info">
                    <div class="group_date">{row.group_name} <span>{row.date_create}</span></div>
                    <div class="title"><a href="{row.link}">{row.title}</a></div>
                </div>
            </div>
            <!-- END: item -->
        </div>
    </div>
</div>
<!-- END: lasted_focus1 -->

<!-- BEGIN: group1 -->
<div class="group1">
    <div class="group_title"><a href="{group.link}">{group.title}</a></div>
    <div class="list_item">
        <!-- BEGIN: item -->
        <div class="item">
            <div class="img"><a href="{row.link}"><img src="{row.picture}" alt="{row.title}"></a></div>
            <div class="info">
                <div class="group_date">{row.group_name} <span>{row.date_create}</span></div>
                <div class="title"><a href="{row.link}">{row.title}</a></div>
            </div>
        </div>
        <!-- END: item -->
    </div>
</div>
<script>
    $(".group1 .list_item").slick({
        arrows: !0,
        dots: !1,
        infinite: !1,
        autoplay: !1,
        autoplaySpeed: 3500,
        speed: 500,
        slidesToShow: 3,
        swipeToSlide: !0,
        lazyload:"ondemand",
        responsive: [{
            breakpoint: 769,
            settings: {
                slidesToShow: 3,
                slidesToScroll: 3,
                infinite: !0
            }
        }, {
            breakpoint: 701,
            settings: {
                slidesToShow: 2,
                slidesToScroll: 3,
                infinite: !0
            }
        }, {
            breakpoint: 426,
            settings: {
                slidesToShow: 1,
                slidesToScroll: 2,
                infinite: !0
            }
        }]
    });
</script>
<!-- END: group1 -->

<!-- BEGIN: group2 -->
<div class="group2">
    <div class="group_title"><a href="{group.link}">{group.title}</a></div>
    <div class="list">
        <!-- BEGIN: left -->
        <div class="left">
            <div class="img"><a href="{row.link}"><img src="{row.picture}" alt="{row.title}"></a></div>
            <div class="info">
                <div class="group_date">{row.group_name} <span>{row.date_create}</span></div>
                <div class="title"><a href="{row.link}">{row.title}</a></div>
            </div>
        </div>
        <!-- END: left -->
        <div class="list_right">
            <div class="list_item">
                <!-- BEGIN: item -->
                <div class="item">
                    <div class="img"><a href="{row.link}"><img src="{row.picture}" alt="{row.title}"></a></div>
                    <div class="info">
                        <div class="group_date">{row.group_name} <span>{row.date_create}</span></div>
                        <div class="title"><a href="{row.link}">{row.title}</a></div>
                    </div>
                </div>
                <!-- END: item -->
            </div>
        </div>
    </div>
</div>
<!-- END: group2 -->

<!-- BEGIN: video_group3 -->
<div class="video_group3">
    <div class="video {data.class_full} {data.video_none}">
        <div class="video_title"><a href="{data.video_link}">{LANG.news.video_title}</a></div>
        <div class="list_item">
            <!-- BEGIN: video_item -->
            {vd.item}
            <!-- END: video_item -->
        </div>
    </div>
    <div class="group3 {data.class_full} {data.group3_none}">
        <div class="group3_title"><a href="{data.group_link}">{data.group_title}</a></div>
        <ul class="list_none">
            <!-- BEGIN: item_news -->
            <li><a href="{row.link}">{row.title}</a></li>
            <!-- END: item_news -->
        </ul>
    </div>
</div>
<script>
    $(".video_group3 .list_item").slick({
        arrows: !0,
        dots: !1,
        infinite: !1,
        autoplay: !1,
        autoplaySpeed: 3500,
        speed: 500,
        slidesToShow: 2,
        swipeToSlide: !0,
        rows: 2,
        lazyload:"ondemand",
        // responsive: [{
        //     breakpoint: 1101,
        //     settings: {
        //         slidesToShow: 4,
        //     }
        // }, {
        //     breakpoint: 769,
        //     settings: {
        //         slidesToShow: 3,
        //     }
        // }, {
        //     breakpoint: 601,
        //     settings: {
        //         slidesToShow: 2,
        //     }
        // }, {
        //     breakpoint: 365,
        //     settings: {
        //         slidesToShow: 1,
        //     }
        // }]
    });
</script>
<!-- END: video_group3 -->

<!-- BEGIN: most_read -->
<div class="most_read">
    <div class="most_read_title">{LANG.news.most_read_title}</div>
    <div class="list_item">
        <!-- BEGIN: item -->
        <div class="item">
            <div class="img"><a href="{row.link}"><img src="{row.picture}" alt="{row.title}"></a></div>
            <div class="title"><a href="{row.link}">{row.title}</a></div>
        </div>
        <!-- END: item -->
    </div>
</div>
<!-- END: most_read -->