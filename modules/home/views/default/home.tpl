<!-- BEGIN: main -->    
{data.content}
<!-- END: main --> 

<!-- BEGIN: banner -->
<div class="topbox">
	<div class="box_menu_product" id="menu_home">
		{data.list_menu}
		<!-- BEGIN: bo -->
		<ul class="list_none menu-all">
			<li class="menu_li "><a href="{data.link}"><div class="icon"><i class="fal fa-chevron-square-down"></i></div>{data.title}</a></li>
		</ul>
		<!-- END: bo -->
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
		<!-- BEGIN: bo -->
		<div class="title_more">
		    <!-- BEGIN: title_more -->
		    <div class="taga">
		        <div class="image">
		        	<img src="{row.icon}" alt="{row.alt}" title="{row.alt}"/>
		        </div>
		        <div class="text">
		            <b>{row.title}</b>
		            {row.short}
		        </div>
		    </div>
		    <!-- END: title_more -->
		</div>
		<!-- END: bo -->
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
		// asNavFor:".title_more",
		pauseOnHover: !1
	});
	/*$(".title_more").slick({
		autoplay: false,
		dots:!1,
		infinite:true,
		speed:200,
		slidesToShow:{data.num_total},
		asNavFor:"#main_slide .row_item",
		focusOnSelect:!0,
		vertical:!0,
		centerMode:!1,
		responsive:[
			{breakpoint:1200,settings:{slidesToShow:5}},
			{breakpoint:992,settings:{slidesToShow:3}},
			{breakpoint:576,settings:{slidesToShow:2,vertical:!1}}
		]
	});*/
</script>
<!-- END: banner -->
--------------------END--------------------
<!-- BEGIN: banner_main -->
<div class="banner_main" style="background: url('{data.background}') no-repeat center; background-size: cover">
	<img src="{data.background}" alt="banner_main">
	<div class="wrap_content">
		<div class="container">
			<div class="left">
				<!-- BEGIN: list_text -->
				<div class="list_text">
					<!-- BEGIN: text -->
					<div class="item">
						<div class="content">{row.content}</div>
						<!-- BEGIN: detail -->
						<div class="detail"><a href="{row.link}" target="{row.target}">{LANG.home.view_detail}</a></div>
						<!-- END: detail -->
					</div>
					<!-- END: text -->
				</div>
				<!-- END: list_text -->
				<!-- BEGIN: service -->
				<div class="service">
					<select id="service">
						<option value="">{LANG.home.service_option}</option>
						<!-- BEGIN: item -->
						<option value="{service.link}">{service.title}</option>
						<!-- END: item -->
					</select>
				</div>
				<!-- END: service -->
			</div>
			<div class="right">
				<!-- BEGIN: event -->
				<div class="title">{LANG.home.upcoming_events}</div>
				<div class="list_item">{event}</div>
				<!-- END: event -->
			</div>
		</div>
	</div>
</div>
<!-- BEGIN: list_text_js -->
<script>
	$('.banner_main .list_text').slick({
		autoplay: false,
		slidesToShow: 1,
		dots: true,
		arrows: false,
		swipeToSlide: true,
	});
</script>
<!-- END: list_text_js -->
<!-- BEGIN: event_js -->
<script>
	$('.banner_main .right .list_item').slick({
		autoplay: false,
		slidesToShow: 1,
		dots: false,
		arrows: true,
		swipeToSlide: true,
	});
</script>
<!-- END: event_js -->
<!-- END: banner_main -->