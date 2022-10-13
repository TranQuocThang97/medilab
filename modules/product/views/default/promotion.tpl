<!-- BEGIN: main -->
  {data.nav}
  {data.box_banner}
  {data.content}
<!-- END: main --> 

<!-- BEGIN: promotion -->
<section class="list_promotion col-12">
	{data.content}	
</section>
<!-- END: promotion -->

<!-- BEGIN: list_item -->
<section class="promo-product" style="width: 100%;">
  <div class="container">
    <div class="row">
        {data.content}      
    </div>
  </div>
</section>
<!-- END: list_item -->


<!-- BEGIN: group_content -->
<div id="group-detail" class="{data.class}">
	{data.nav}
	{data.column_mini}
	{data.list_item}
	<div class="clear"></div>
</div>
<!-- END: group_content --> 

<!-- BEGIN: column_mini -->
<div id="column_mini">
	<!-- BEGIN: focus -->
	<div class="list_focus">
		<!-- BEGIN: row -->
		<div class="focus-item">
			<div class="img"><a href="{row.link}" title="{row.title}"><img src="{row.picture}" alt="{row.title}" title="{row.title}" /></a></div>
			<div class="info">
				<h3><a href="{row.link}" title="{row.title}">{row.title}</a></h3>
				<div class="short">{row.short}</div>
			</div>				
		</div>
		<!-- END: row -->
	</div>
	<!-- END: focus -->
	{data.banner}
	<div class="clear"></div>
</div>
<!-- END: column_mini --> 

<!-- BEGIN: focus -->
<div class="news_focus">
	<div class="news_focus-title">
  	<a href="{row.link}" title="{row.title}">{row.title}</a> <span class="date">({row.date_update})</span>
  </div>
	<div class="img"><a href="{row.link}" title="{row.title}"><img src="{row.picture}" alt="{row.title}" title="{row.title}" /></a></div>
	<div class="short">{row.short}</div>
	<div class="view_detail"><a href="{row.link}" title="{row.title}">{LANG.news.view_detail}</a></div>
	<div class="clear"></div>
</div>
<!-- END: focus --> 

<!-- BEGIN: list_group -->
<div class="list_group">
	{data.content}
  <!-- BEGIN: row_item -->
  <h2 class="row_item-title"><a href="{row_group.link}" title="{row_group.title}">{row_group.title}</a></h2> 
  <div class="row_item {row_group.class}">  	 
    <div class="img"><a href="{row.link}" title="{row.title}"><img src="{row.picture}" alt="{row.title}" title="{row.title}" /></a></div>
    <h3 class="title"><a href="{row.link}" title="{row.title}">{row.title}</a> <span class="date">({row.date_update})</span></h3>
    <div class="short">{row.short}</div>	
    <!-- BEGIN: other -->
    <ul class="other">
      <!-- BEGIN: li -->
      <li><a href="{other.link}">{other.title}</a> <span class="date">({other.date_update})</span></li>
      <!-- END: li -->  
    </ul>
    <!-- END: other -->    
    <div class="clear"></div>
  </div>
  {row_group.hr}
  <!-- END: row_item --> 
  <!-- BEGIN: row_empty -->
  <h2 class="row_item-title"><a href="{row_group.link}" title="{row_group.title}">{row_group.title}</a></h2> 
  <div class="row_empty {row_group.class}">{row_group.mess}</div>
  {row_group.hr}
  <!-- END: row_empty --> 
</div>
<!-- END: list_group --> 

<!-- BEGIN: list_item -->
<div class="list_item">
	<div class="row_item {row.class}">
		<!-- BEGIN: row_item -->
		<!-- BEGIN: col_item -->
		<div class="col_item {col.class}">
			<div class="img"><a href="{col.link}" title="{col.title}"><img src="{col.picture}" alt="{col.title}" title="{col.title}" /></a></div>
			<div class="info">
				<h3><a href="{col.link}" title="{col.title}" class="cut_tring">{col.title}</a></h3>
            <div class="date">{col.date_update}</div>
				<div class="short">{col.short}</div>
			</div>			
		</div>
		<!-- END: col_item --> 
		<!-- END: row_item --> 
		<!-- BEGIN: row_empty -->
		<div class="row_empty">{row.mess}</div>
		<!-- END: row_empty --> 
		<div class="clear"></div>
	</div>
	{data.nav}
</div>
<!-- END: list_item --> 

<!-- BEGIN: html_title_more -->
<div class="tool_page">
  <a href="javascript:print();" class="icon_print">{LANG.news.print}</a>
</div>
<!-- END: html_title_more --> 

<!-- BEGIN: detail -->
<div id="item_detail">
	{data.navigation}
	<h1>{data.title}</h1>
	<div class="item-date"><i class="ficon-calendar" style="color:red"></i> <span class="time">{data.post_time}</span><span class="date">{data.post_date}</span></div>
	{data.item_related}
	<div class="item-short">{data.short}</div>
	<div class="item-content">{data.content}</div>	
	
	<div class="tool-share">
		<span class="tool-share-item"><div class="fb-like" data-layout="button_count" data-action="like" data-show-faces="true" data-share="true"></div></span>      
      <span class="tool-share-item"><a href="https://twitter.com/share" class="twitter-share-button"{count}>Tweet</a></span>
      <script>!function (d, s, id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs')
            ;</script>
		<!-- Đặt thẻ này vào phần đầu hoặc ngay trước thẻ đóng phần nội dung của bạn. -->
		<script src="https://apis.google.com/js/platform.js" async defer>
		  {lang: 'vi'}
		</script>
		<span class="tool-share-item"><div class="g-plusone" data-size="medium"></div></span>
	</div>
	
	<!-- BEGIN: tags -->
	<div class="tags">
		<h3 class="title_tag">{LANG.news.tags}:</h3>
		<ul class="list_none">
			<!-- BEGIN: row -->
			<li><a href="{row.tag_link}">{row.tag}</a></li>
			<!-- END: row -->
		</ul>
	</div>
	<!-- END: tags -->   
	
	<!-- BEGIN: banner -->
   <div class="detail-banner">{data.banner}</div>
	<!-- END: banner -->
   
</div>

<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v2.5";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

{data.other}
<!-- END: detail --> 

<!-- BEGIN: item_related -->
<div class="item_related">
	<ul>
		<!-- BEGIN: row -->
		<li><a href="{row.link}" title="{row.title}">{row.title}</a></li>
		<!-- END: row --> 
	</ul>
</div>
<!-- END: item_related --> 

<!-- BEGIN: list_other -->
<div class="list_other">
  <div class="list_other-title">{data.title}</div>
	<ul>
  	<!-- BEGIN: row -->
  	<li><a href="{row.link}" title="{row.title}">{row.title}</a> <span class="date">({row.date_update})</span></li>
    <!-- END: row --> 
  </ul>
</div>
<!-- END: list_other --> 