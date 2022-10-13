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
<script type="text/javascript">
    $(document).on("click", ".menu-page", function(){
        if($("#user-manager").hasClass("open")){
            $(".menu-page").removeClass("change");
            $("#user-manager").removeClass("open");
        }else{
            $(".menu-page").addClass("change");
            $("#user-manager").addClass("open");
        }
    })
</script>
<!-- END: main --> 

<!-- BEGIN: main_forget -->
<div class="title_forget">{data.title}</div>
{data.content}
<div class="clear"></div>
<!-- END: main_forget --> 


<!-- BEGIN: change_pass -->
<div class="box-manager change_pass">
    <div class="box-title">{data.page_title}</div>
    <hr type="background: #C8C8C8;">
    <div class="box-content">
        <form id="form_change_pass" method="post" class="user-form">
            <div class="security_note">{LANG.user.security_note}</div>
            <div class="form_mess"></div>
            <div class="row justify-content-center">
                <div class="col-12 col-md-auto px-md-0 order2"><a href="{data.forget_password}" style="color: #FE6505">{LANG.user.forget_pass}</a></div>
                <div class="col-12 col-md-7 order1">
                    <div class="form-group">
                        <label class="title">{LANG.user.password_cur}</label>
                        <input placeholder="{LANG.user.text_pass}" id="password_cur" name="password_cur" type="password" maxlength="100" value="" class="form-control" />
                    </div>                
                    <div class="form-group">
                        <label class="title">{LANG.user.password}</label>
                        <input placeholder="{LANG.user.text_pass}" id="password" name="password" type="password" maxlength="100" value="" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label class="title">{LANG.user.re_password}</label>
                        <input placeholder="{LANG.user.re_password}" name="re_password" type="password" maxlength="100" value="" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label class="title"></label>
                        <div class="form-content mt-2">
                            <input type="hidden" name="do_submit" value="1" />
                            <button type="submit" class="btn btn-confirm">{LANG.user.confirm}</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<script language="javascript">
    imsUser.change_pass('form_change_pass');
</script>
<!-- END: change_pass -->

<!-- BEGIN: forget_pass -->
<div class="row">
    <div class="col-12">
        <div class="box-layout">
            <div class="box-layout-title">{data.page_title}</div>
            <div class="box-layout-content">
                {data.content}
                <form id="{data.form_id_pre}form_change_pass" class="user-form mt-3" method="post">
                    {data.err}
                    <div class="form_mess"></div>
                    <div class="form-group">
                        <label class="title">{LANG.user.email}/{LANG.user.phone}<span class="required">*</span></label>
                        <input id="username" name="username" type="text" maxlength="100" value="" class="form-control" />
                    </div>  
                    <div class="form-group">
                        <label class="title"></label>
                        <div class="form-content">
                            <input type="hidden" name="do_submit" value="1" />
                            <button type="submit" class="btn bg-color text-color btn_custom">{LANG.user.btn_get_pass}</button>
                        </div>
                    </div>
                </form>
                <script language="javascript">
                    imsUser.forget_pass('{data.form_id_pre}form_change_pass');
                </script>  
            </div>
        </div>
    </div>
</div>
<!-- END: forget_pass -->

<!-- BEGIN: signup -->
<div class="row">
    <div class="col-12">
        <div class="box-layout">
            <div class="box-layout-title">{LANG.user.form_signup_title}</div>
            <div class="box-layout-content">
                <!-- BEGIN: contributor -->
                <div class="contributor">
                   <b>{user.full_name}</b> {LANG.user.contributor_signup}
                </div>
                <!-- END: contributor -->
                <form id="{data.form_id_pre}form_signup" class="user-form" method="post" onSubmit="return false" >
                    <div class="form_mess"></div>
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label class="title">{LANG.user.nickname} <span class="required">*</span></label>
                                <input placeholder="{LANG.user.text_full_name}" name="full_name" type="text" maxlength="100" value="{data.fullname}" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label class="title">{LANG.user.username} <span class="required">*</span></label>
                                <input placeholder="{LANG.user.text_phone}" name="phone" type="text" maxlength="100" value="{data.phone}" class="form-control"/>
                            </div>
                            <div class="form-group">
                                <label class="title">{LANG.user.password} <span class="required">*</span></label>
                                <input placeholder="{LANG.user.text_pass}" id="{data.form_id_pre}password" name="password" type="password" maxlength="100" value="{data.password}" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label class="title">{LANG.user.re_password} <span class="required">*</span></label>
                                <input placeholder="{LANG.user.text_re_pass}" name="re_password" type="password" maxlength="100" value="{data.re_password}" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label class="title">{LANG.user.email}</label>
                                <input placeholder="{LANG.user.text_email}" name="username" type="text" maxlength="100" value="{data.username}" class="form-control" />
                                <div class="note" style="color: blue; line-height: 17px;padding-top: 5px;">{LANG.user.note_input_email}</div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label class="title">{LANG.user.province}</label>
                                <div class="form-content">{data.list_province}</div>
                            </div>
                            <div class="form-group">
                                <label class="title">{LANG.user.district}</label>
                                <div class="form-content">{data.list_district}</div>
                            </div>
                            <div class="form-group">
                                <label class="title">{LANG.user.ward}</label>
                                <div class="form-content">{data.list_ward}</div>
                            </div>
                            <div class="form-group">
                                <label class="title">{LANG.user.address}</label>
                                <input placeholder="{LANG.user.text_address}" name="address" type="text" maxlength="100" value="{data.address}" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label class="title">&nbsp;</label>
                                <div class="d-flex justify-content-between">
                                    <div class="col-5 px-0">
                                        <div class="form-group" align="right">
                                            <div class="captcha-groupd d-flex align-items-center">
                                                <img src="{data.link_root}ajax.php?m=global&f=captcha" alt="captcha" class="captcha_img" style="max-height: 34px;" />
                                                <a href="javascript:;" onclick="imsGlobal.captcha_refresh()" class="captcha_refresh"><i class="far fa-retweet"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-7 px-0 mr-lg-auto">
                                        <div class="form-group">
                                            <input placeholder="{LANG.user.text_captcha}" name="captcha" type="text" maxlength="6" value="" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="title"></label>
                                <div class="form-content">
                                    <input type="hidden" name="do_submit" value="1" />
                                    <button type="submit" class="btn bg-color text-color btn_custom">{LANG.user.btn_signup}</button>
                                </div>    
                            </div>
                        </div>
                    </div>
                </form>
                <script language="javascript">
                    imsLocation.locationChange("province", ".select_location_province");
                    imsLocation.locationChange("district", ".select_location_district");
                    imsUser.signup('{data.form_id_pre}form_signup', '{data.link_login_go}');
                </script>
            </div>
        </div>
    </div>
</div>
<!-- END: signup --> 

<!-- BEGIN: signin -->
<div class="user_form user_signin">
    <div class="col_left">
        <h2>{data.form_signin_title}</h2>
        <p>{data.form_signin_title_more}</p>
        {data.banner}
    </div>
    <div class="col_right">
        <div class="user_signin-title d-lg-none">{LANG.user.form_signin_title}</div>
        <div class="user_signin-content">
            <form id="{data.form_id_pre}form_signin" name="{data.form_id_pre}form_signin" method="post" action="{data.link_action}" onSubmit="return false" >
                <div class="form_mess"></div>    
                <div class="form-group">
                    <label class="title">{LANG.user.username}</label>
                    <span><input placeholder ="{LANG.user.text_email} / {LANG.user.phone}" name="username" type="text" maxlength="100" value="{data.username}" class="form-control" /></span>
                </div>
                <div class="form-group" style="position: relative;">
                    <label class="title">{LANG.user.password}</label>
                    <span><input placeholder="{LANG.user.text_pass}" name="password" type="password" maxlength="100" value="{data.password}" class="form-control" /></span>
                    <i class="far fa-eye show-hide-pass" style="position: absolute;right: 10px;cursor: pointer;"></i>
                </div>
                <div class="forget_password">
                   {LANG.user.forget_pass} <a href="{data.link_forget_password}" target="_top"><b>{LANG.user.click_here}</b></a>
                </div>
                <div class="row_btn">
                    <input type="hidden" name="do_submit"    value="1" />
                    <button type="submit" class="btn" value="{LANG.user.btn_signin}" >{LANG.user.btn_signin}</button>    
                    <a href="{data.url_gg}" class="btn_c btn-social btn-google"><i class="fab fa-google"></i> <span class="text">{LANG.user.login_with_gg}</span></a>
                    <a href="{data.url_fb}" class="btn_c btn-social btn-facebook"><i class="fab fa-facebook-f"></i> <span class="text">{LANG.user.login_with_fb}</span></a>
                </div>
            </form>
            <script>
                imsUser.signin('{data.form_id_pre}form_signin', '{data.link_login_go}');
                $(document).on("click", ".show-hide-pass", function(){
                    var $this = $(this);
                    if($("input[name='password']").attr("type") == "password"){
                        $this.removeClass("fa-eye").addClass("fa-eye-slash");
                        $("input[name='password']").attr("type","text");
                    }else{
                        $this.removeClass("fa-eye-slash").addClass("fa-eye");
                        $("input[name='password']").attr("type","password");
                    }
                })
            </script>
        </div>
    </div>
</div>
<!-- END: signin -->

<!-- BEGIN: otp -->
<div id="verify_otp">
    <div class="box-layout">
        <div class="box-layout-title">{data.page_title}</div>
        <div class="box-layout-content">
            <div class="user_form text-center">
                <div class="form_mess"></div>
                <!-- <form id="frm-mobile-verification" method="get"> -->
                <div class="form_row">
                    <p>{data.note}</p>
                </div>
                <div class="form_row py-3">
                    <input type="hidden" id="mobile" value="{data.phone}" data-id="{data.user_id}">
                    <input type="text" id="mobileOtp" class="form-input" placeholder="{LANG.user.enter_otp}">
                </div>
                <div class="form_row">
                    <button id="request" class="btn btn-secondary btn-request" onclick="imsUser.sendOTP(1);">{LANG.user.request_otp}</button>
                    <button id="verify" class="btn btn-primary btn-verify" data-go="{data.link_go}">{LANG.user.verify_otp}</button>
                </div>
                <!-- </form> -->
                <div class="clear"></div>
            </div>
        </div>
    </div>
</div>
<script async="async">imsUser.verifyOTP()</script>
<!-- END: otp -->

<!-- BEGIN: voucher --> 
<div class="manage">
    <table class="manage-table">
        <thead>
            <tr >
                <th class="cot" width="20%">{LANG.user.voucher_code}</th>
                <th class="cot" width="20%" >{LANG.user.amount}</th>
                <th class="cot" width="20%" >{LANG.user.amount_use}</th>
                <th class="cot" width="25%">{LANG.user.date_end}</th>
            </tr>
        </thead>
        <tbody>
            {data.row_item}
            <!-- BEGIN: row_item -->
            <tr>
                <td class="cot" align="center">{row.voucher_id}</td>
                <td class="cot" align="right">{row.amount}</td>
                <td class="cot" align="right">{row.amount_use}</td>
                <td class="cot" align="center">{row.date_end}</td>
            </tr>
            <!-- END: row_item --> 
            <!-- BEGIN: row_empty -->
            <tr>
                <td class="cot cot_empty" align="center" colspan="4">{row.mess}</td>
            </tr>
            <!-- END: row_empty --> 
        </tbody>
    </table>
</div>
<br />
{data.nav}
<!-- END: voucher --> 

<!-- BEGIN: address_book -->
<div class="box-manager">
    <div class="box-title">{data.page_title}</div>    
    <div id="address_book" class="ordering_address">
        <div id="form_ordering_address">
           <a href="javascript:void(0)" class="other_address" id="addNewAddress"><i class="far fa-plus-circle"></i> <span>{LANG.user.add_other_address}</span></a>            
            <div class="row">
                <!-- BEGIN: row -->
                <form name="form_ordering_address{row.id}" method="post" action="{data.link_action}" class="col-12 col-md-12 mb-4">
                    <div class="item" id="item-{row.id}">
                        <div class="panel panel-default address-item" style="color: {CONF.bgheader}">
                            <div class="panel-body">
                                <div class="info col-12 col-md-9">
                                    <p class="name">{row.full_name}</p>
                                    <p class="address" title="{row.address_full}"><ins>{LANG.user.address}</ins>: {row.address_full}</p>
                                    <p class="phone"><ins>{LANG.user.phone}</ins>: {row.phone}</p>
                                    <p class="email"><ins>{LANG.user.email}</ins>: {row.email}</p>
                                </div>
                                <p class="action col-12 col-md-3 pt-3 pt-md-0">
                                    <input name="address" type="hidden" value="{row.id}" />
                                    <input type="hidden" name="do_submit" value="1" />                        
                                    <button type="button" class="btn edit-address" data-id="{row.id}">{LANG.user.edit}</button>
                                    <!-- BEGIN: unremove -->
                                    <button type="button" disabled class="btn delete-address">{LANG.user.delete}</button>
                                    <!-- END: unremove -->
                                    <!-- BEGIN: remove -->
                                    <button type="button" class="btn delete-address" data-id="{row.id}">{LANG.user.delete}</button>
                                    <!-- END: remove -->
                                    <button type="button" class="btn default-address" data-id="{row.id}">{LANG.user.set_default}</button>
                                </p>                    
                                <!-- BEGIN: default -->
                                <span class="default"><i class="far fa-check-circle"></i> {LANG.user.default}</span>
                                <!-- END: default -->
                            </div>
                        </div>
                    </div>
                </form>
                <!-- END: row -->
            </div>
        </div>
        <div class="panel address-form">
            
        </div>
    </div>
</div>
<script type="text/javascript">
    $(".default-address").on("click",function(){
        var element = $(this);
        var id = $(this).data('id');
        if(!id) return false;
        loading('show');
        $.ajax({
            type: "POST",
            url: ROOT+"ajax.php",
            data: { "m" : "user", "f" : "default_address", "id" : id}
        }).done(function( string ) {
            loading('hide');                
            var data = JSON.parse(string);
            var status_succes = 'success';
            if(data.ok == 1) {                
                location.reload();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: lang_js['aleft_title'],
                    text: data.mess,
                })
            }
        });
        return false;
    });
</script>
<!-- END: address_book -->

<!-- BEGIN: manage_product -->
<div class="box-manager">
    <div class="box-title">{data.page_title}</div>
    <div class="box-content">
        <div class="box_manager_product manager_{data.class}">
            <div class="list_item list_item_product">
                <div class="row_item" {data.id}>
                    <!-- BEGIN: row -->            
                    {row.content}   
                    <!-- END: row -->
                    <!-- BEGIN: empty -->
                    {row.text}
                    <!-- END: empty -->
                </div>
            </div>    
        </div>
        {data.nav}
    </div>
</div>
<script type="text/javascript">
    imsOrdering.add_cart("form.form_add_cart");
</script>
<!-- END: manage_product -->

<!-- BEGIN: list_favorite -->
<div id="user-product" class="box-manager">
    <div class="box-title">{data.page_title}</div>
    <div class="box-content">
        {data.content}
        <!-- BEGIN: empty -->
        {row.text}
        <!-- END: empty -->
    </div>
</div>
<!-- END: list_favorite --> 

<!-- BEGIN: mod_item_cart-->
<div class="col_item cart-responsive">
    <div class="col-img d-md-none"><span><img src="{row.picture}" alt="{row.title}"/></span></div>
    <div class="col-cart">
        <div class="col-i col1">
            <a href="{row.link}"><img src="{row.picture}" alt="{row.title}"/></a>
            {row.color_bg}
        </div>
        <div class="col-i col2">            
            <div class="title_product_cart">                
                <a href="{row.link}" class="title">{row.title}</a>
                <!-- BEGIN: option -->
                <div class="code_pro" style="color: #999;">
                    {row.option_name}: <b>{row.option_value}</b>
                </div>
                <!-- END: option -->
            </div>
            <div class="row_btn col-12 p-0">
                <div class="delete_item" data-id="{row.option_id}">{LANG.user.delete}</div>
            </div>
        </div>   
        <div class="col-i col3 up_price_buy">
             <b class="price" style="color: {CONF.bgheader}">{row.price_buy_text}</b>
            <!-- BEGIN: discount -->
            <p class="discount">
            {row.price_text}&nbsp;&nbsp;|&nbsp;&nbsp;-{row.percent_discount}%
            </p>
            <!-- END: discount -->
            <div class="add_cart">
                <form action="{row.link_cart}" method="post" class="form_add_cart">
                    <input name="item_id" type="hidden" value="{row.item_id}" />
                    <input name="option_id" type="hidden" value="{row.option_id}" />
                    <input name="quantity" type="hidden" value="1" />
                    <!-- BEGIN: ver -->
                    <input name="{row.name}" type="hidden" value="{row.value}" />                    
                    <!-- END: ver -->
                    <button class="btn btn-add-cart bg-color btn_add_cart_now {data.id_disable} css_bo" type="{data.type_btn}" {data.link_go}>
                        <i class="fal fa-shopping-cart"></i>{LANG.user.btn_add_cart_now}
                    </button>
                    <button class="btn btn-add-cart bg-color btn_add_cart {data.id_disable} css_bo" type="{data.type_btn}">
                       {LANG.user.btn_add_cart}
                   </button>
                </form>
            </div>
        </div>        
    </div>                
</div>
<!-- END: mod_item_cart-->


