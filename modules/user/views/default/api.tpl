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

<!-- BEGIN: manage_api -->
<div class="box_manager_api manager_{data.class}">
	<div class="bg-secondary text-white p-3">
		<h4>Quản lý kênh bán hàng</h4>
		Bán cho khách hàng trực tuyến và người sử dụng các kênh bán hàng.
	</div>	
    <div class="row m-0">
        <!-- BEGIN: row -->
        <div class="col_item col-12 d-flex flex-wrap align-items-center border p-4 ">
        	<div class="text col-md-9 col-12 px-0 pr-md-3">
        		<h5 class="title">{row.title}</h5>
        		{row.content} 	
        		<div class="mess"></div>
        	</div>
        	<div class="logo col-md-3 col-12 px-0">        		
        		<div class="img">
        			<img src="{row.picture}" class="img-fluid" alt="{row.title}">
        		</div>
        		<div class="input-group align-items-center justify-content-between mt-3	">
        			<input type="checkbox" {row.is_connect} data-toggle="toggle" data-size="sm" data-onstyle="success" data-offstyle="secondary"  data-on="Kết nối" data-off="Ngắt kết nối" data-id="{row.name_action}">
        			<button class="btn btn-dark text-white rounded" data-fancybox data-src="#{row.name_action}"><i class="fad fa-cog pr-1"></i>Cấu hình</button>	
        		</div>
        		
        	</div>
        </div>  
        {row.action}
        <!-- END: row -->
        <!-- BEGIN: empty -->
        {row.text}
        <!-- END: empty -->
    </div>
</div>
{data.nav}
<script async="async">
    imsApi.save_config_sendo("config_sendo")
    $("input[data-id=\"shopee\"]").on("change",function(){
        if($(this).is(":checked")){

        }
    })
</script>
<!-- END: manage_api --> 

<!-- BEGIN: config_sendo -->
<div id="sendo" class="frm_popup" style="display: none;">
    <form id="config_sendo" method="post">
        <div class="mess"></div>
        <div class="row">
            <div class="col-12">
                <div class="form-group input-effect">
                    <label>Tên gian hàng trên Sendo</label>
                    <input name="shopname" type="text" maxlength="250" value="{data.shopname}" class="form-control effect-1 px-3"/>
                    <span class="focus-border"></span>
                </div>
            </div>
            <div class="col-12">
                <div class="form-group input-effect">
                    <label>Tài khoản đăng nhập Sendo</label>
                    <input name="username" type="text" maxlength="250" value="{data.username}" class="form-control effect-1 px-3"/>
                    <span class="focus-border"></span>
                </div>
            </div>
            <div class="col-12">
                <div class="form-group input-effect">
                    <label>Client Key (*)</label>
                    <input name="apikey" type="text" maxlength="250" value="{data.apikey}" class="form-control effect-1 px-3" required/>
                    <span class="focus-border"></span>
                </div>
            </div>
            <div class="col-12">
                <div class="form-group text-right">                    
                    <button type="submit" class="btn btn-primary px-4" data-id="{data.name_action}">Lưu</button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- END: config_sendo -->


<!-- BEGIN: config_shopee -->
<div id="shopee" class="frm_popup" style="display: none;">
    <input type="hidden" id="shopee-url" value="{data.link_shopee}">
    <button class="btn"><a href="">Kết nối</a></button>
</div>
<!-- END: config_shopee -->

<!-- BEGIN: manage_product -->

<!-- END: manage_product -->