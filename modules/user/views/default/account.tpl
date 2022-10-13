<!-- BEGIN: profile -->
<div id="user_profile">
    <h1>{LANG.user.account}</h1>
    <hr style="background: #C8C8C8;">
    <div class="content">
        {data.err}
        <form id="{data.form_id_pre}form_profile" method="post" action="" class="user-form">
            <div class="form_mess"></div>
            <div class="form-group box-avatar">
                <label class="title">{LANG.user.avatar}</label>                
                <div class="avatar text-center">                    
                    <div class="img-preview">
                        <img src="{data.picture}" id="img-preview"/>
                    </div>
                    <div class="btn-group">
                        <label class="btn btn-crop">
                            <input type="file" id="choose-file" accept="image/*" />
                            <i class="icon"><img src="{data.icon_crop}"></i> 
                            {LANG.user.upload_avatar}
                        </label>
                        <button type="button" class="btn btn-remove remove-avatar">
                            <i class="icon"><img src="{data.icon_remove}"></i>
                            {LANG.user.remove_avatar}
                        </button>
                    </div>
                </div>
                <div id="box-crop" style="display: none;">
                    <div id="img-crop"></div>
                    <div class="update-img"><button id="save-crop" class="btn btn-orange">Chọn ảnh</button></div>
                </div>
            </div>
            <hr>
            <div class="group">
                <label class="group-title">{LANG.user.contact_info}</label>
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="title">{LANG.user.last_name}</label>
                            <input name="last_name" type="text" maxlength="100" value="{data.last_name}" class="form-control" />
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="title">{LANG.user.first_name} <span class="required">*</span></label>
                            <input name="first_name" type="text" maxlength="100" value="{data.first_name}" class="form-control" />
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="title">{LANG.user.phone} <span class="required">*</span></label>
                            <input name="phone" type="text" maxlength="100" value="{data.phone}" class="form-control" />
                            <!-- <div class="verify"> -->
                                <!-- <span name="phone" class="form-control" id="mobile" data-id="{data.user_id}">{data.phone}<span/> -->
                                <!-- <button type="button" class="btn btn-default btn_custom" id="send_verification" onclick="imsUser.sendOTP(1);">{LANG.user.send_verification}</button> -->
                            <!-- </div> -->
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="title">{LANG.user.email} <span class="required">*</span></label>
                            <input type="text" value="{data.email}" name="email" class="form-control">
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="title">{LANG.user.landline}</label>
                            <input name="landline" type="text" maxlength="100" value="{data.landline}" class="form-control" />
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="title">{LANG.user.website}</label>
                            <input name="website" type="text" maxlength="100" value="{data.website}" class="form-control" />
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="title">{LANG.user.job}</label>
                            <input name="job" type="text" maxlength="100" value="{data.job}" class="form-control" />
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label class="title">{LANG.user.company}</label>
                            <input name="company" type="text" maxlength="100" value="{data.company}" class="form-control" />
                        </div>
                    </div>
                    <!-- BEGIN: bo -->
                    <div class="form-group">
                        <label class="title">{LANG.user.verification} <span class="required">*</span></label>
                        <input name="verification" type="text" maxlength="100" value="" class="form-control" />
                    </div>
                    <!-- END: bo -->
                    
                    <!-- BEGIN: check_fb_gg -->
                    <div class="form-group">
                        <label class="title">{LANG.user.username} <span class="required">*</span></label>
                        <input name="username" type="text" maxlength="100" value="{data.username}" class="form-control" />
                    </div>
                    <!-- END: check_fb_gg -->
                   <!--  <div class="form-group">
                        <label class="title">{LANG.user.birthday}</label>
                        <div class="birthday">
                            <select name="date" required>{data.list_date}</select>
                            <select name="month" required>{data.list_month}</select>
                            <select name="year" required>{data.list_year}</select>
                        </div>
                    </div> -->
                </div>
            </div>
            <div class="group">
                <label class="group-title">{LANG.user.address} <span class="required">*</span></label>
                <div class="row">
                    <div class="form-group col-12 mb-0">
                        <label class="title">{LANG.user.address_info} <span class="required">*</span></label>
                        <div class="row">
                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    {data.list_province}
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    {data.list_district}
                                </div>
                            </div>                            
                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    {data.list_ward}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label class="title">{LANG.user.street} <span class="required">*</span></label>
                            <input name="address" type="text" maxlength="100" value="{data.address}" class="form-control" />
                        </div>
                    </div>
                    <!-- <div class="col-12">
                        <div class="form-group">
                            <label class="title">{LANG.user.country}</label>
                            {data.list_country}
                        </div>
                    </div> -->
                </div>
            </div>
            <div class="form-group form-submit">
                <label class="title"></label>
                <div class="form-content text-right">
                    <!-- <input type="hidden" name="do_submit" value="1" /> -->
                    <button type="submit" class="btn btn-orange update_account">{LANG.user.btn_update}</button>
                </div>    
            </div>    
        </form>
    </div>
</div>
<script type="text/javascript">    
    imsLocation.locationChange("province", ".select_location_province");
    imsLocation.locationChange("district", ".select_location_district");
    var group = [];
        group['crop'] = '#img-crop';
        group['upload'] = '#choose-file';
        group['result'] = '#save-crop';
        group['preview'] = '#img-preview';
    UploadWithCrop(group,235,220);
    $(document).on('click change', '#choose-file', function(){
        var files = $(this).val();
        if(files.length > 0){
            $.fancybox.open({
                src: '#box-crop',
                type: 'inline',
                touch: false,
                clickSlide: false,
                clickOutside: false,
                afterClose: function() {
                    $(group['upload']).val('');
                }
            })
        }
    })
    $(document).on('click', '#save-crop', function(){
        $.fancybox.close();
    })
    $(document).on('click', '.remove-avatar', function(){
        imsUser.remove_avatar();
    })
    imsUser.account('{data.form_id_pre}form_profile');
</script>
<!-- END: profile -->