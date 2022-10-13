<!-- BEGIN: main -->
{data.content}
<!-- END: main -->

<!-- BEGIN: box_advisory -->
<div class="box_advisory">
    <span class="text">{data.text}</span>
    <div class="content_advisory">{data.content}</div>
    <div id="box_comment">
        {data.advisory_form}
    </div>
</div>
<!-- END: box_advisory -->

<!-- BEGIN: group_advisory -->
<div class="group_advisory" id="form_advisory">
    <!-- BEGIN: row_item -->
    <!-- BEGIN: col_item -->
    <div class="title_advisory show_reply">
        <div class="no">{col.stt}</div>
        {col.title}
        <div class="no_show_info info_advisory_{col.num}">
            <div class="info_nickname">{col.owner_nickname}</div>
            <div class="info_email">{col.owner_email}</div>
            <div class="info_date">{col.date_update}</div>
        </div>
    </div>
    <div class="none content_advisory_sub content_advisory_{col.num} ">
        {col.content}
        <div>
            <div class="title">{LANG.advisory.new_question} !</div>
            <form class="{data.form_id_pre}form_advisory{col.num}" name="{data.form_id_pre}form_advisory" method="post" onSubmit="return false" >
                <div class="form_mess"></div>
                <div class="media">
                    <div class="media-body">
                        <div class="tg-line-form">
                            <textarea name="txtaComment" cols="30" rows="5" class="input" placeholder="{LANG.advisory.your_comments}" id="txtaComment"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="tg-line-form">
                                    <input type="text" class="input" placeholder="{LANG.advisory.full_name}" name="txtName" value="">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="tg-line-form">
                                    <input type="email" class="input" placeholder="Email" name="txtEmail" value="" >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-7">
                                <div class="captcha">
                                    <b>{LANG.advisory.verification}:</b>
                                    <div class="captcha-group">
                                        <img src="{col.link_root}ajax.php?m=global&f=captcha" alt="captcha" class="captcha_img" />
                                        <a href="javascript:;" onclick="imsGlobal.captcha_refresh()" class="captcha_refresh"><i class="ficon-arrows-cw"></i></a>
                                    </div>
                                    <input placeholder="{LANG.advisory.enter_code}" name="captcha" type="text" maxlength="6" value="" class="input" />
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="tg-line-form">
                                    <button href="javascript:void(0);" type="submit" class="button button-blue" id="idGui">{LANG.advisory.send_question}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <script type="text/javascript"> imsUser.post_advisory('form_advisory{col.num}'); </script>
        </div>
    </div>
    <!-- END: col_item -->
    <!-- END: row_item -->
    <!-- BEGIN: row_empty -->
    <div class="row_empty">{row.mess}</div>
    <!-- END: row_empty -->
    <form class="{data.form_id_pre}form_advisory_post" name="{data.form_id_pre}form_advisory" method="post" onSubmit="return false" >
        <div class="form_mess"></div>
        <div class="media">
            <div class="media-body">
                <div class="tg-line-form">
                    <textarea name="txtaComment" cols="30" rows="5" class="input" placeholder="{LANG.advisory.your_comments}" id="txtaComment"></textarea>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="tg-line-form">
                            <input type="text" class="input" placeholder="{LANG.advisory.full_name}" name="txtName" value="">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="tg-line-form">
                            <input type="email" class="input" placeholder="Email" name="txtEmail" value="" >
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-7">
                        <div class="captcha">
                            <b>{LANG.advisory.verification}:</b>
                            <div class="captcha-group">
                                <img src="{col.link_root}ajax.php?m=global&f=captcha" alt="captcha" class="captcha_img" />
                                <a href="javascript:;" onclick="imsGlobal.captcha_refresh()" class="captcha_refresh"><i class="ficon-arrows-cw"></i></a>
                            </div>
                            <input placeholder="{LANG.advisory.enter_code}" name="captcha" type="text" maxlength="6" value="" class="input" />
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="tg-line-form">
                            <button href="javascript:void(0);" type="submit" class="button button-blue" id="idGui">{LANG.advisory.send_question}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <script type="text/javascript"> imsUser.post_advisory('form_advisory_post'); </script>
</div>
<div class="total_page" style="float: left;width: 100%;text-align: center;">
    {data.nav}
</div>
<!-- END: group_advisory -->


<!-- BEGIN: list_group -->
<div class="list_advisory" data-group="{data.group_id}">
    <h1 class="group_title">{data.title}</h1>
    <div class="list_item">
        <!-- BEGIN: item -->
        <div class="item {row.active}">
            <div class="wrap_title">
                <div class="title"><span class="{row.active}">{row.title}</span><i class="{row.class_i}"></i></div>
                <div class="date_create">{row.date}</div>
            </div>
            <div class="content" {row.none}>
                <div class="writter">{LANG.advisory.writter}</div>
                {row.content}
            </div>
        </div>
        <!-- END: item -->
        <!-- BEGIN: empty -->
        <div class="empty">{LANG.advisory.no_have_item}</div>
        <!-- END: empty -->
    </div>
    {data.view_more}
    <input type="hidden" name="start" value="{data.start}">
</div>
<script>
    $(document).on('click', '.list_advisory .item .wrap_title', function (){
        $('.list_advisory .item').removeClass('active').find('span').removeClass('active');
        $('.list_advisory .item i').removeClass('close');
        $('.list_advisory .item .content').slideUp();
        $(this).parent().addClass('active').find('span').addClass('active');
        $(this).find('i').addClass('close');
        $(this).next().slideDown();
    });
</script>
<!-- END: list_group -->