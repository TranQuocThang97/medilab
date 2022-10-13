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

<!-- BEGIN: manage --> 
<div class="box-manager">
    <div class="box-title">{data.page_title}</div>
    <div class="box-content" id="user_promotion_code">
        <ul class="nav nav-tabs mb-0" role="tablist">
            <!-- BEGIN: row_filter -->
            <li class="nav-item">
                <a href="{row.link}" class="nav-link {row.active}" >{row.title}</a>
            </li>
            <!-- END: row_filter -->            
        </ul>
        <div class="tab-content">
        	<div class="row_item">
	            <!-- BEGIN:row_item -->
                <div class="col_item col-12 col-md-6">
    	           	<div class="item {row.class}" style="background-image: url('{row.bg}');">                        
    	           		<div class="col_l">
    	           			<div class="type">{row.type}</div>
                            <input type="hidden" value="{row.promotion_id}">
    	           		</div>
    	           		<div class="col_r">
    	           			<div class="title">{row.code_value}</div>
    	           			<div class="apply">                                
                                <p>{row.promotion_condition}</p>
                                <p class="text-danger">{row.note}</p>
                            </div>
    	           			<div class="date {row.class_date}">{row.promotion_expire}</div>                            
    	           		</div>
    	           	</div>
                    <div class="info" data-toggle="popover">
                        <i class="fal fa-info-circle"></i>
                        <div class="popper-content" style="display: none;">
                            <p class="code {row.valid} text-center text-white bg-secondary px-2">
                                <b><i class="fal fa-copy pr-2"></i> {row.promotion_id}</b>
                                <input type="hidden" value="{row.promotion_id}">
                            </p>
                            <p class="date">{LANG.user.date_expire}: {row.date_expire}</p>
                            <p class="apply">{row.promotion_condition2}<span class="px-2">{row.promotion_condition}</span></p>
                            <p class="num_use {row.class_num_use}">{row.count_num_use}</p>
                            <div class="short">{row.short}</div>
                        </div>
                    </div>
                </div>
	            <!-- END:row_item -->
            </div>  
             <!-- BEGIN: row_empty -->
	        <div class="empty bg-white px-3 py-5">{data.mess}</div>
	        <!-- END: row_empty -->
            {data.nav}
        </div>       
    </div>
</div>
<script language="javascript">
    $('input.datepicker').datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd/mm/yy',
        yearRange: "-50:+50",
    });
    $(document).on("click",".code",function(){
        var input = $(this).find("input"),
            code = input.val();
        input.CopyToClipboard();
        Swal.fire({
            icon: 'success',
            title: code,
            html: lang_js['copped'],
        })        
    })
    $('.info').popover({
        html: true,
        trigger: "manual",
        placement: "left",
        content: function() {
            return $(this).find('.popper-content').html();
        }
    }).mouseenter(function(e) {
        $(this).children("i").removeClass('fa-info-circle').addClass('fa-times-circle');
        $(this).popover('show');
    }).on("click", function(e) {
        $(this).children("i").removeClass('fa-times-circle').addClass('fa-info-circle');
        var ref = $(this);
        // timeoutObj = setTimeout(function(){
            ref.popover('hide');
        // }, 1000);
    });
</script>
<!-- END: manage --> 


<form action="{data.link_action_search}" method="get" name="form_search" id="form_search" autocomplete="off">
            <div class="row">
                <div class="col-md-3"><label>{LANG.global.date_begin}</label> 
                    <input name="date_begin" type="text" size="20" maxlength="150" value="{data.search_date_begin}" class="form-control datepicker" placeholder="{LANG.global.ddmmyy}" readonly="">
                </div>
                <div class="col-md-3"><label>{LANG.global.date_end}</label> 
                    <input name="date_end" type="text" size="20" maxlength="150" value="{data.search_date_end}" class="form-control datepicker" placeholder="{LANG.global.ddmmyy}" readonly="">
                </div>
                <div class="col-md-3"><label>{LANG.global.text_search}</label> 
                    <div class="form-group">
                        <input name="search_title" type="text" size="20" maxlength="150" value="{data.search_title}" class="form-control" placeholder="{LANG.user.promotion_code}">
                    </div>
                </div>
                <div class="col-md-3 col_search_btn">
                    <label>&nbsp;</label> 
                    <button class="btn btn-secondary btn-block" type="submit">{LANG.global.btn_search}</button>
                </div>
            </div>
        </form>
        <table class="table manage-table mt-3">
            <thead>
                <tr >
                    <th class="cot" style="text-align: center; width: 15%;">{LANG.user.promotion_code}</th>
                    <th class="cot" style="text-align: center; width: 15%;">{LANG.user.value}</th>
                    <th class="cot" style="text-align: center; width: 20%;">{LANG.user.total_min}</th>
                    <th class="cot" style="text-align: center; width: 20%;">{LANG.user.date_end}</th>
                    <th class="cot" style="text-align: right;">{LANG.user.promotion_status}</th>
                    <th class="cot" style="text-align: right; width: 10%;"></th>
                </tr>
            </thead>
            <tbody>
                {data.row_item}
                <!-- BEGIN: row_item -->
                <tr>
                    <td class="cot" style="text-align: center;">
                        <input type="text" value="{row.promotion_id}" class="code" readonly style="border: 0px;">
                    </td>
                    <td class="cot" style="text-align: center;">{row.code_value}</td>
                    <td class="cot" style="text-align: center;">{row.total_min}</td>
                    <td class="cot" style="text-align: center;">{row.date_end}</td>
                    <td class="cot promo_status" style="text-align: right;"><span style="background:{row.status.background_color};color:{row.status.color}; border: 1px solid {row.status.border_color};">{row.status.title}</span></td>
                    <td class="cot" style="text-align: right;"><button class="btn btn-danger border-none {row.class}">Copy</button></td>
                </tr>
                <!-- END: row_item --> 
                <!-- BEGIN: row_empty -->
                <tr>
                    <td class="cot cot_empty" align="center" colspan="5">{row.mess}</td>
                </tr>
                <!-- END: row_empty --> 
            </tbody>
        </table>