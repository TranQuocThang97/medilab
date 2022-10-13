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

<!-- BEGIN: deeplink -->
<div class="box-manager">
    <div class="box-title">{data.page_title}</div>
    <div class="box-content" id="user_deeplink">
		<div class="affili">
		    <form name="form_deeplink" class="" action="#" method="post" id="form_deeplink">
		        <h4>{LANG.user.deeplink_create_title}</h4>        
		        <div class="form-group">                
		            <span class="mb-3 mb-lg-0"><input placeholder="{LANG.user.deeplink_text}" name="link_source" type="text" class="form-control"></span>
		            <button type="submit" class="btn btn-add">{LANG.user.deeplink_create}</button>            
		        </div>
		    </form>
		    <h4>Danh sách link deeplink</h4>
            <div class="manage_table">
                <div class="wrap_table">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th class="pl-md-0" style="width: 5%; text-align: center;">STT</th>
                            <th style="width: 15%; text-align: left;">Ngành hàng</th>
                            <th>Link short</th>
                            <th style="width: 12%;">Lượt click</th>
                            <th style="width: 12%;">Ngày tạo</th>
                            <th class="pr-md-0" style="width: 10%; text-align: right;">Thao tác</th>
                        </tr>
                        </thead>
                        <tbody>
                        <!-- BEGIN: row -->
                        <tr>
                            <td class="pl-md-0" style="text-align: center;">{row.stt}</td>
                            <td style="text-align: left;"><a href="{row.group_link}" target="_blank">{row.group_name}</a></td>
                            <td><input type="text" class="copy_link" value="{row.link_short}" readonly></td>
                            <td>{row.num_view}</td>
                            <td>{row.date_create}</td>
                            <td class="pr-md-0" style="text-align: right;">
                                <div class="dropleft">
                                    <button class="btn btn-action" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-angle-down"></i></button>
                                    <div class="dropdown-menu">
                                        <button class="dropdown-item" type="button" onclick="imsUser.create_embed('{row.link_embed}')">{LANG.user.embed}</button>
                                        <button class="dropdown-item" type="button" onclick="imsUser.delete_deeplink({row.id})">{LANG.user.delete}</button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <!-- END: row -->
                        </tbody>
                    </table>
                </div>
            </div>
		</div>
		<div id="embed_box" style="display: none">
		    <textarea id="embed_code"></textarea>
		    <button type="button" class="btn btn-copy btn-danger btn-sm text-light border-none"><i class="fal fa-code"></i><span class="text">Copy</span></button>
		</div>
	</div>
</div>
<!-- END: deeplink -->


<!-- BEGIN: deeplink_statistics -->
<div class="box-manager">
    <div class="box-title">{data.page_title}</div>
    <div class="box-content" id="user_deeplink">
		<div class="affili statistics">
		    <form action="" method="post" style="margin-top: 5px">
		        <div class="row">
		            <div class="col-md-3">
		                <div class="form-group">
		                    <label for="">{LANG.user.date_begin}</label>
		                    <input placeholder="dd/mm/yyyy" required autocomplete="off" type="datetime" name="search_date_begin" id="" class="form-control datepicker">
		                </div>
		            </div>
		            <div class="col-md-3">
		                <div class="form-group">
		                    <label for="">{LANG.user.date_end}</label>
		                    <input placeholder="dd/mm/yyyy" required autocomplete="off" type="datetime" name="search_date_end" id="" class="form-control datepicker">
		                </div>
		            </div>
		            <div class="col-md-3">
		                <div class="form-group">
		                    <label>&nbsp;</label> 
		                    <button type="submit" class="btn btn-search">Xem kết quả</button>
		                </div>
		            </div>
		        </div>
		    </form>
		     <p style="font-weight: 700; text-align: right">{data.curdeeplink_statistics}: <span class="price_format"><b style="color:red;" class="number autoUpdate">{data.total_offer_by_month}</b></span> </p>
		    <table class="table table-striped table-hover">
		        <thead>
		        <tr>
		            <th class="pl-md-0" width="10%">Mã ĐH</th>
		            <th>Code link</th>
		            <th width="20%">Điểm chiếu khấu</th>
		            <th width="20%">Thời gian đặt</th>
		            <th class="pr-md-0 text-right" width="10%">Trạng thái</th>
		        </tr>
		        </thead>
		        <tbody>
		        <!-- BEGIN: row -->
		        <tr>
		            <td class="pl-md-0"><a target="_blank" href="{row.link_order}">#{row.order_code}</a></td>
		            <td><a href="javascript:void(0)" class="deeplink" title="{row.deeplink}">{row.deeplink}</a></td>
		            <td><b class="autoUpdate"><span class="price_format"><span class="number autoUpdate">{row.deeplink_total}</span></span></b></td>
		            <td>{row.date_create}</td>
		            <td class="pr-md-0 text-right">{row.status}</td>
		        </tr>
		        <!-- END: row -->
		        </tbody>
		    </table>
		</div>
	</div>
</div>
<script language="javascript">
    $('input.datepicker').datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd/mm/yy',
        yearRange: "-100:+0",
    });
</script>
<!-- END: deeplink_statistics -->


<!-- BEGIN: deeplink_account -->
<div class="box-manager">
    <div class="box-title">{data.page_title}</div>
    <div class="box-content" id="user_deeplink">
		<div class="affili">
		    <form id="{data.form_id_pre}form_profile" name="{data.form_id_pre}form_profile" method="post" action="{data.link_action}" >
		        <div class="reg-border">
		            <div class="form-horizontal">
		                <p><b><i>{LANG.user.deeplink_note}</i></b></p>
		                <br>
		                <div class="row" style="display:flex;flex-wrap: wrap">
		                    <div class="col-md-6">
		                        <div class="form-group">
		                            <label class="title">Email <span class="required">*</span> :</label>
		                            <input readonly placeholder="Nhập email" type="text" maxlength="100" value="{data.email}" class="form-control">
		                        </div>
		                    </div>
		                    <div class="col-md-6">
		                        <div class="form-group">
		                            <label class="title">{LANG.user.company_person_name}<span class="required">*</span> :</label>
		                            <input required  placeholder="" required name="full_name" type="text" maxlength="100" value="{data.full_name}" class="form-control">
		                        </div>
		                    </div>
		                    <div class="col-md-6">
		                        <div class="form-group">
		                            <label class="title">{LANG.user.birthday}</label>
			                        <div class="birthday">
			                            <select name="date" required>{data.list_date}</select>
			                            <select name="month" required>{data.list_month}</select>
			                            <select name="year" required>{data.list_year}</select>
			                        </div>
		                        </div>
		                    </div>
		                    <div class="col-md-6">
		                        <div class="form-group">
		                            <label class="title">{LANG.user.phone} :</label>
		                            <input required  placeholder="0963123456" name="phone" type="text" maxlength="100" value="{data.phone}" class="form-control">
		                        </div>
		                    </div>
		                    <div class="col-md-6">
		                        <div class="form-group">
		                            <label class="title">{LANG.user.province} :</label>
		                            {data.list_province}
		                        </div>
		                    </div>
		                    <div class="col-md-6">
		                        <div class="form-group">
		                            <label class="title">{LANG.user.district} :</label>
		                            {data.list_district}
		                        </div>
		                    </div>
		                    <div class="col-md-6">
		                        <div class="form-group">
		                            <label class="title">{LANG.user.ward} :</label>
		                            {data.list_ward}
		                        </div>
		                    </div>
		                    <div class="col-md-6">
		                        <div class="form-group">
		                            <label class="title">{LANG.user.address}:</label>
		                            <input required  placeholder="123 Quang Trung" name="address" type="text" maxlength="100" value="{data.address}" class="form-control">
		                        </div>
		                    </div>


                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="title">{LANG.user.bank_account_number} :</label>
                                    <input required  placeholder="" name="bank_account_number" type="text" maxlength="100" value="{data.bank_account_number}" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="title">{LANG.user.bank_account_owner} :</label>
                                    <input required  placeholder="" name="bank_account_owner" type="text" maxlength="100" value="{data.bank_account_owner}" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="title">{LANG.user.bank_name} :</label>
                                    <input required  placeholder="" name="bank_name" type="text" maxlength="100" value="{data.bank_name}" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="title">{LANG.user.bank_branch} :</label>
                                    <input required  placeholder="" name="bank_branch" type="text" maxlength="100" value="{data.bank_branch}" class="form-control">
                                </div>
                            </div>


                            <div class="col-12">
		                        <div class="form-group">
                                    <label class="title">{LANG.user.note_upload_file_deeplink_register}</label>
		                            {data.upload_pic}
		                        </div>
		                    </div>
		                </div>
		                <div class="form-group row">
		                    <div class="col-sm-12">
		                        <span id="register_error" class="text-error">&nbsp;</span>
		                        <span class="text-error" id="reg_error_email">&nbsp;</span>
		                    </div>
		                </div>
		                <div class="form-group row">
		                    <div class="col-md-6">
		                        <div class="form-group">
		                            <input type="hidden" name="do_submit" value="1" />
		                            <input type="hidden" name="is_request_affiliates" value="1" />
		                            <input type="submit" id="btn_update" class="btn btn-default btn_custom" value="{LANG.user.btn_deeplink}" />
		                        </div>
		                    </div>
		                </div>
		            </div>
		        </div>
		    </form>
		</div>
	</div>
</div>
<script language="javascript">
    imsLocation.locationChange("province", ".select_location_province");
    imsLocation.locationChange("district", ".select_location_district");
    imsGlobal.captcha_refresh();
    imsUser.account('{data.form_id_pre}form_profile', 1);
    $( function() {
	    $( ".datepicker" ).datepicker();
	 } );
</script>
<!-- END: deeplink_account -->
