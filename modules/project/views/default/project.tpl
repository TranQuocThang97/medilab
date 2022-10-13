<!-- BEGIN: main -->
{data.content}
<!-- END: main --> 

<!-- BEGIN: project -->
<div id="project">
    <div class="box_header"><h1 class="title"><span>{data.title}</span></h1></div>
    <div class="project_group nav nav-tabs d-none d-lg-flex">        
        <!-- BEGIN: group -->
        <a href="#pg{row.group_id}" class="nav-item nav-link item" data-toggle="tab" role="tab" data-id="{row.group_id}">            
            <span>{row.title}</span>
        </a>
        <!-- END: group -->
    </div>
    <select class="form-control no-chosen d-lg-none">
        <option>-- Chọn --</option>
        <!-- BEGIN: select -->
        <option value="{row.group_id}">{row.title}</option>
        <!-- END: select -->
    </select>
    <div class="project_item">
        {data.content}
    </div>    
</div>
<script async="async">
    $('#project .project_group a').on('click',function(){
        loading('show');
        var group_id = $(this).attr('data-id');
        $.ajax({
            type: "POST",
            url: ROOT + "ajax.php",
            data: {"m":"project", "f":"load_project", 'group_id':group_id, 'lang_cur':lang}
        }).done(function (string) {
            var data = JSON.parse(string);
            $('#project .project_item').html(data.html);
            $('#project input#cur_group').val(data.cur_group);           
         });
        loading('hide');    
    });
    $('#project select').on('change',function(){
        loading('show');
        var group_id = $(this).val();
        $.ajax({
            type: "POST",
            url: ROOT + "ajax.php",
            data: {"m":"project", "f":"load_project", 'group_id':group_id, 'lang_cur':lang}
        }).done(function (string) {
            var data = JSON.parse(string);
            $('#project .project_item').html(data.html);
            $('#project input#cur_group').val(data.cur_group);           
         });
        loading('hide');    
    });
     //project load more
    $('#project').on('click','.btn_viewmore',function(){
        loading('show');
        var num_cur = $(this).attr('data-start');
        var group_id = $(this).attr('data-group');
        $.ajax({
            type: "POST",
            url: ROOT + "ajax.php",
            data: {"m":"project", "f":"load_more", 'num_cur':num_cur, 'group_id':group_id, 'lang_cur':lang}
        }).done(function (string) {
            var data = JSON.parse(string);
            if(data.html != ''){
                $('#project .project_item .row_item').append(data.html);
            }else{
                $('#project .viewmore').remove();
            }
            if(data.num < 6) $('#project .viewmore').remove();
            loading('hide');
        });
    })
</script>
<!-- END: project -->

<!-- BEGIN: tab_project -->
<div class="tab_project">
    <div class="box_header"><h1 class="title"><span>{data.title}</span></h1></div>
    <ul class="ul_project list_none">
        <!-- BEGIN: li -->
        <li class="{group.active}" rel="content{group.group_id}">{group.title}</li>
        <!-- END: li -->
    </ul>
    <div class="select">
        <select>
            <!-- BEGIN: select -->
            <option value="content{group.group_id}">{group.title}</option>
            <!-- END: select -->
        </select>
    </div>
    <div class="content_project">
        <!-- BEGIN: content_tab -->
        <div class="content_tab content{group.group_id} {group.none}">
            <div class="list_item">
                <!-- BEGIN: item -->
                <div class="item {row.class_other}">
                    <div class="img">
                        <a href="{row.link}" data-fancybox data-src="{row.link}">
                            <img src="{row.picture}" alt="{row.title}">
                        </a>
                    </div>
                    <div class="info">
                        <div class="title"><a href="{row.link}">{row.title}</a></div>
                        <div class="short">{row.short}</div>
                    </div>
                </div>
                <!-- END: item -->
            </div>
            <!-- BEGIN: no_data -->
            <div class="no_data">{LANG.home.no_have_item}</div>
            <!-- END: no_data -->
        </div>
        <!-- END: content_tab -->
    </div>
</div>
<script>
    content_tab('tab_project','ul_project','content_project');
</script>
<!-- END: tab_project -->

<!-- BEGIN: item_detail -->
<div id="item_detail">		
	<div id="gallery_slider">
		<div class="row_item">
		    <!-- BEGIN: pic -->
		    <a href="javascript:void(0)" data-fancybox="gallery" data-src="{row.src_zoom}">
		      	<div class="item"><img src="{row.src}" alt="{row.title}"></div>
		    </a>
		    <!-- END: pic --> 
	    </div>
	</div>
	<h1>{data.title}</h1>
	<div class="item-content">{data.content}</div>		
</div>
{data.other}
<script async="async">
	var sync1 = $("#gallery_slider .row_item");
	sync1.slick({
		slidesToShow: 3,
		swipeToSlide: !0,
		dots: !1,
		arrows: !0,
		lazyload: "ondemand",
		responsive: [
			{
				breakpoint: 992,
				settings: {
					slidesToShow: 2,
				}
			},
			{
				breakpoint: 576,
				settings: {
					slidesToShow: 1,
				}
			},
		]
  	})
    $('[data-fancybox="gallery"]').fancybox({
        buttons: [
            "zoom",
            "share",
            "slideShow",
            "fullScreen",
            "download",
            "thumbs",
            "close"
        ],
        transitionEffect: "zoom-in-out",
        transitionDuration: 1000,
        animationDuration: 500,
        thumbs : {
          autoStart : true,
          axis      : $(window).width()>=768?'x':'y',
        }    
    });
    $(".item-content img").on("click",function(){   
        $.fancybox.open({
            src  : $(this).attr("src"),
            type : 'image',
        });
    })
</script>
<!-- END: item_detail --> 