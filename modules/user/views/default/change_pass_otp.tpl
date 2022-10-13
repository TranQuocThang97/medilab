<!-- BEGIN: main -->
<div class="user-manager justify-content-center" id="user-manager">    
    <div id="ims-content">
        {data.content}
    </div>
</div>
<!-- END: main -->

<!-- BEGIN: change_pass_otp -->
<div class="box-manager">
    <div class="box-title">{data.page_title}</div>
    <div class="box-content">
        <form id="form_change_pass" action="" method="post" class="user-form">
            <div class="security_note">{LANG.user.security_note}</div>
            <div class="form_mess">{data.mess}</div>
            <div class="row justify-content-center">                
                <div class="col-12 col-md-7 order1">
                    <div class="form-group">
                        <label class="title">{LANG.user.otp}</label>
                        <input placeholder="{LANG.user.enter_otp}" name="otp" type="text" maxlength="100" value="" class="form-control" />
                        <input name="phone" type="hidden" maxlength="100" value="{data.phone}" class="form-control" />
                    </div>                
                    <div class="form-group" style="position: relative;">
                        <label class="title">{LANG.user.password}</label>
                        <input placeholder="{LANG.user.text_pass}" id="password" name="password" type="password" maxlength="100" value="" class="form-control pw" />
                        <i class="far fa-eye show-hide-pass" style="position: absolute;right: 10px;cursor: pointer;"></i>
                    </div>
                    <div class="form-group" style="position: relative;">
                        <label class="title">{LANG.user.re_password}</label>
                        <input placeholder="{LANG.user.text_re_pass}" name="re_password" type="password" maxlength="100" value="" class="form-control pw" />
                        <i class="far fa-eye show-hide-pass" style="position: absolute;right: 10px;cursor: pointer;"></i>
                    </div>
                    <div class="form-group">
                        <label class="title"></label>
                        <div class="form-content">
                            <input type="hidden" name="do_submit" value="1" />
                            <button type="submit" class="btn btn-confirm">{LANG.user.confirm}</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>        
    </div>
</div>
<script type="text/javascript">
    $(document).on("click", ".show-hide-pass", function(){
        var $this = $(this),
            input = $this.parent(".form-group").find(".pw");
        if(input.attr("type") == "password"){
            $this.removeClass("fa-eye").addClass("fa-eye-slash")
            input.attr("type","text");
        }else{
            $this.removeClass("fa-eye-slash").addClass("fa-eye")
            input.attr("type","password");
        }
    })
    $("#form_change_pass").validate({
        submitHandler: function() {            
            var form_mess = $("#form_change_pass").find('.form_mess');
            form_mess.stop(true,true).slideUp(200).html('');
            var fData = $("#form_change_pass").serializeArray();
            loading('show');
            $.ajax({
                type: "POST",
                url: ROOT+"ajax.php",
                data: { "m" : "user", "f" : "change_pass_otp", "lang_cur" : lang, "data" : fData }
            }).done(function( string ) {
                loading('hide');
                console.log(string);
                var data = JSON.parse(string);
                console.log(data);
                if(data.ok == 1) {
                    Swal.fire({
                        icon: 'success',
                        title: lang_js['aleft_title'],
                        text: data.mess,
                        timer: 2000
                    }).then(function() {
                        go_link(data.link);
                    });
                } else {
                    form_mess.html(imsTemp.html_alert(data.mess,'error')).stop(true,true).slideDown(200);
                }
            });
            return false;
        },
        rules: {
            otp: {
                required: true,
            },
            password: {
                required: true,
            },
            re_password: {
                required: true,
                equalTo: '#form_change_pass input[name*="password"]'
            },
        },
        messages: {           
            otp: lang_js['err_valid_input'],
            password: lang_js['err_valid_input'],
            re_password: lang_js['err_valid_input']
        }           
    });
</script>
<!-- END: change_pass_otp -->