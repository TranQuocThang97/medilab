<!-- BEGIN: signup_bo -->
<div class="box-layout">
    <div class="left">
        <div class="wrap_left">
            <div class="box-layout-title">
                {data.logo}
                <h1>{LANG.user.signup}</h1>
            </div>
            <div class="box-layout-content">
                <!-- BEGIN: contributor -->
                <div class="contributor">
                    <b>{user.full_name}</b> {LANG.user.contributor_signup}
                </div>
                <!-- END: contributor -->
                <form id="{data.form_id_pre}form_signup" class="user-form" method="post" onSubmit="return false" >
                    <div class="form_mess"></div>
                    <div class="form-group">
                        <input placeholder="{LANG.user.full_name}" name="full_name" type="text" maxlength="100" value="{data.fullname}" class="form-control" />
                    </div>
                    <div class="form-group">
                        <input placeholder="{LANG.user.email}" name="username" type="text" maxlength="100" value="{data.username}" class="form-control" />
                    </div>
                    <div class="form-group">
                        <input placeholder="{LANG.user.phone}" name="phone" type="text" maxlength="100" value="{data.phone}" class="form-control" />
                    </div>
                    <div class="form-group">
                        <input placeholder="{LANG.user.pass}" id="{data.form_id_pre}password" name="password" type="password" maxlength="100" value="{data.password}" class="form-control" />
                    </div>
                    <div class="form-group">
                        <input placeholder="{LANG.user.re_pass}" name="re_password" type="password" maxlength="100" value="{data.re_password}" class="form-control" />
                    </div>
                    <div class="form-bottom">
                        <input type="hidden" name="do_submit" value="1" />
                        <button type="submit" class="btn btn-orange btn-sign mt-3">{LANG.user.btn_signup}</button>
                    </div>
                    <div class="fg">
                        <a href="{data.url_gg}" class="btn_c btn-social btn-google"><i class="fab fa-google"></i> <span class="text">{LANG.user.login_with_gg}</span></a>
                        <a href="{data.url_fb}" class="btn_c btn-social btn-facebook"><i class="fab fa-facebook-f"></i> <span class="text">{LANG.user.login_with_fb}</span></a>
                    </div>
                    <div class="form-switch">{data.signin_link}</div>
                </form>
            </div>
        </div>
    </div>
    <div class="right" style="background: url('{data.picture}') no-repeat center; background-size: cover"></div>
</div>
<script language="javascript">
    // imsLocation.locationChange("province", ".select_location_province");
    // imsLocation.locationChange("district", ".select_location_district");
    imsUser.signup('{data.form_id_pre}form_signup', '{data.link_login_go}');
</script>
<!-- END: signup_bo -->

<!-- BEGIN: signup -->
<div class="box-layout" style="background: url('{data.background}') no-repeat center; background-size: cover">
    <div class="container">
        <div class="wrap_form">
            <div class="box-layout-title">
                <h1>{LANG.user.signup}</h1>
                <a href="{data.link_signin}" class="button_close"><i class="fal fa-times"></i></a>
            </div>
            <div class="box-layout-content">
                <div class="logo">{data.logo}</div>
                <!-- BEGIN: contributor -->
                <div class="contributor">
                    <b>{user.full_name}</b> {LANG.user.contributor_signup}
                </div>
                <!-- END: contributor -->
                <form id="{data.form_id_pre}form_signup" class="user-form" method="post" onSubmit="return false" >
                    <div class="form_mess"></div>
                    <!-- BEGIN: bo -->
                    <div class="form-group">
                        <input placeholder="{LANG.user.full_name}" name="full_name" type="text" maxlength="100" value="{data.fullname}" class="form-control" />
                    </div>
                    <div class="form-group">
                        <input placeholder="{LANG.user.email}" name="username" type="text" maxlength="100" value="{data.username}" class="form-control" />
                    </div>
                    <!-- END: bo -->
                    <div class="form-group">
                        <label for="phone">{LANG.user.phone} (<span>*</span>)</label>
                        <input placeholder="{LANG.user.phone}" name="phone" type="text" id="phone" maxlength="100" value="{data.phone}" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label for="{data.form_id_pre}password">{LANG.user.pass} (<span>*</span>)</label>
                        <input placeholder="{LANG.user.pass}" id="{data.form_id_pre}password" name="password" type="password" maxlength="100" value="{data.password}" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label for="re_password">{LANG.user.re_pass} (<span>*</span>)</label>
                        <input placeholder="{LANG.user.re_pass}" name="re_password" id="re_password" type="password" maxlength="100" value="{data.re_password}" class="form-control" />
                    </div>
                    <div class="form-bottom pt-2">
                        <input type="hidden" name="do_submit" value="1" />
                        <button type="submit" class="btn">{LANG.user.btn_signup}</button>
                    </div>
                    <div class="form-switch">{data.signin_link}</div>
                </form>
            </div>
        </div>
    </div>
</div>
<script language="javascript">
    // imsLocation.locationChange("province", ".select_location_province");
    // imsLocation.locationChange("district", ".select_location_district");
    imsUser.signup('{data.form_id_pre}form_signup', '{data.link_login_go}');
</script>
<!-- END: signup -->

<!-- BEGIN: signin_bo -->
<div class="box-layout">
    <div class="left">
        <div class="wrap_left">
            <div class="box-layout-title">
                {data.logo}
                <h1>{LANG.user.signin}</h1>
            </div>
            <div class="box-layout-content">
                <form id="{data.form_id_pre}form_signin" name="{data.form_id_pre}form_signin" method="post" action="{data.link_action}" onSubmit="return false" >
                    <div class="form_mess"></div>
                    <div class="form-group">
                        <span><input placeholder ="{LANG.user.email}" autocomplete="off" name="username" type="text" maxlength="100" value="{data.username}" class="form-control" /></span>
                    </div>
                    <div class="form-group">
                        <span><input placeholder="{LANG.user.pass}" autocomplete="off" name="password" type="password" maxlength="100" value="{data.password}" class="form-control" /></span>
                    </div>
                    <div class="form-group d-flex flex-wrap justify-content-between">
                        <div class="remember_pass mb-2">
                            <input type="checkbox" name="remember" id="remember" value="1">
                            <label for="remember">{LANG.user.remember}</label>
                        </div>
                        <div class="forget_password mb-2" style="color: #FE6505">
                            <a href="{data.link_forget_password}" target="_top">{LANG.user.forget_pass}</a>
                        </div>
                    </div>
                    <div class="form-bottom">
                        <input type="hidden" name="do_submit" value="1" />
                        <button type="submit" class="btn btn-orange" value="{LANG.user.btn_signin}">{LANG.user.btn_signin}</button>
                    </div>
                    <div class="form-switch">{data.signup_link}</div>
                </form>
            </div>
        </div>
    </div>
    <div class="right" style="background: url('{data.picture}') no-repeat center; background-size: cover"></div>
</div>
<script>
    imsUser.signin('{data.form_id_pre}form_signin', '{data.link_login_go}');
</script>
<!-- END: signin_bo -->

<!-- BEGIN: signin -->
<div class="box-layout" style="background: url('{data.background}') no-repeat center; background-size: cover">
    <div class="container">
        <div class="wrap_form">
            <div class="box-layout-title">
                <h1>{LANG.user.signin}</h1>
            </div>
            <div class="box-layout-content">
                <div class="logo">{data.logo}</div>
                <form id="{data.form_id_pre}form_signin" name="{data.form_id_pre}form_signin" method="post" action="{data.link_action}" onSubmit="return false" >
                    <div class="form_mess"></div>
                    <div class="form-group">
                        <label for="phone">{LANG.user.phone} (<span>*</span>)</label>
                        <input placeholder ="{LANG.user.phone}" autocomplete="off" name="username" id="phone" type="text" maxlength="100" value="{data.phone}" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label for="phone">{LANG.user.pass} (<span>*</span>)</label>
                        <input placeholder="{LANG.user.pass}" autocomplete="off" name="password" id="password" type="password" maxlength="100" value="{data.password}" class="form-control" />
                    </div>
                    <div class="form-group d-flex flex-wrap justify-content-between">
                        <div class="remember_pass">
                            <input type="checkbox" name="remember" id="remember" value="1">
                            <label for="remember">{LANG.user.remember}</label>
                        </div>
                        <div class="forget_password" style="color: #224893">
                            <a href="{data.link_forget_password}" target="_top">{LANG.user.forget_pass}</a>
                        </div>
                    </div>
                    <div class="form-bottom">
                        <input type="hidden" name="do_submit" value="1" />
                        <button type="submit" class="btn" value="{LANG.user.btn_signin}">{LANG.user.btn_signin}</button>
                    </div>
                    <!-- BEGIN: bo -->
                    <div class="or"><p></p><span>{LANG.user.or}</span><p></p></div>
                    <div class="fg">
                        <p><a href="{data.url_fb}" class="btn_c btn-social btn-facebook"><i class="fab fa-facebook" style="color: #1877f2"></i> <span class="text">{LANG.user.login_with_fb}</span></a></p>
                        <p><a href="{data.url_gg}" class="btn_c btn-social btn-google"><img src="{CONF.rooturl}resources/images/use/google.png" alt="google"> <span class="text">{LANG.user.login_with_gg}</span></a></p>
                    </div>
                    <!-- END: bo -->
                    <div class="form-switch">{data.signup_link}</div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    imsUser.signin('{data.form_id_pre}form_signin', '{data.link_login_go}');
</script>
<!-- END: signin -->

<!-- BEGIN: forget_pass_bo -->
<div class="box-layout">
    <div class="left">
        <div class="wrap_left">
            <div class="box-layout-title">
                <h1>{data.page_title}</h1>
                <a href="{data.link_signin}" class="button_close"><i class="fal fa-times"></i></a>
            </div>
            <div class="box-layout-content">
                {data.logo}
                {data.content}
                <form id="{data.form_id_pre}form_change_pass" class="user-form mt-3" method="post">
                    {data.err}
                    <div class="form_mess"></div>
                    <div class="form-group">
                        <label class="title">{LANG.user.email}/{LANG.user.phone}<span class="required">*</span></label>
                        <input id="username" name="username" type="text" maxlength="100" value="" class="form-control" />
                    </div>
                    <div class="form-bottom">
                        <label class="title"></label>
                        <input type="hidden" name="do_submit" value="1" />
                        <button type="submit" class="btn btn-orange">{LANG.user.btn_get_pass}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="right" style="background: url('{data.picture}') no-repeat center; background-size: cover"></div>
</div>
<script language="javascript">
    imsUser.forget_pass('{data.form_id_pre}form_change_pass');
</script>
<!-- END: forget_pass_bo -->

<!-- BEGIN: forget_pass -->
<div class="box-layout" style="background: url('{data.background}') no-repeat center; background-size: cover">
    <div class="container">
        <div class="wrap_form">
            <div class="box-layout-title">
                <h1>{data.page_title}</h1>
                <a href="{data.link_signin}" class="button_close"><i class="fal fa-times"></i></a>
            </div>
            <div class="box-layout-content">
                <div class="logo">{data.logo}</div>
                {data.content}
                <form id="{data.form_id_pre}form_change_pass" class="user-form mt-3" method="post">
                    {data.err}
                    <div class="form_mess"></div>
                    <div class="form-group">
                        <label class="title" for="username">{LANG.user.phone} (<span class="required">*</span>)</label>
                        <input id="username" name="username" type="text" maxlength="100" value="" class="form-control" placeholder="{LANG.user.phone}"/>
                    </div>
                    <div class="form-bottom pb-0">
                        <label class="title"></label>
                        <input type="hidden" name="do_submit" value="1" />
                        <button type="submit" class="btn">{LANG.user.btn_get_pass}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script language="javascript">
    imsUser.forget_pass('{data.form_id_pre}form_change_pass');
</script>
<!-- END: forget_pass -->