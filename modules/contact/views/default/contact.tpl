<!-- BEGIN: main -->
{data.content}
<!-- END: main -->

<!-- BEGIN: html_contact -->
<div class="nav">
    <div class="container">{data.nav}</div>
</div>
<div id="contact_content">
    <div class="contact_left col-lg-6 col-md-12 col-12 pl-lg-0">
        <div id="contact_map">
            <div id="map_canvas" data-centermaplat="{data.centerMaplat}" data-centermaplng="{data.centerMaplng}"></div>
        </div>
    </div>
    <div class="contact_right col-lg-6 col-md-12 col-12">
        <span class="title_more">{data.contact_info}</span>
        <div id="contact_form">
            {data.err}
            <form id="form_contact" name="form_contact" method="post" action="{data.link_action}" >
                <div class="form_note"><span class="required">(*) {LANG.contact.required}</span></div>
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <input name="full_name" type="text" maxlength="250" value="{data.full_name}" class="form-control" placeholder="{LANG.contact.full_name} (*)" />
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <input name="email" type="text" maxlength="250" value="{data.email}" class="form-control" placeholder="{LANG.contact.email} (*)" />
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <input name="phone" type="text" maxlength="250" value="{data.phone}" class="form-control" placeholder="{LANG.contact.phone}" />
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <input name="address" type="text" maxlength="250" value="{data.address}" class="form-control" placeholder="{LANG.contact.address}" />
                        </div>
                    </div>
                    <div class="col-12 d-none">
                        <div class="form-group">
                            <input name="title" type="text" maxlength="250" value="{data.title}" class="form-control" placeholder="{LANG.contact.title}"  />
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <textarea name="content" class="form-control" rows="5" placeholder="{LANG.contact.content} (*)" >{data.content}</textarea>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <input type="hidden" name="do_submit" value="1" />
                            <button type="submit" class="btn btn-contact bg-color text-color">
                                {LANG.contact.btn_send}
                                <i class="fab fa-telegram-plane ml-2"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- END: html_contact -->