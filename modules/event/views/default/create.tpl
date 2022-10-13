<!-- BEGIN: main -->
{data.box_banner}
{data.content}
<!-- END: main -->

<!-- BEGIN: col_left -->
<div class="col_left">
    <div class="back_event">
        <a href="{data.link_event}"><i class="far fa-angle-left"></i> {LANG.event.event}</a>
    </div>
    <div class="type">{LANG.event.type} <i class="far fa-angle-down"></i></div>
    <div class="name_event">{LANG.event.name_default}</div>
    <div class="date">{data.time}</div>
    <ul class="step list_none">
        <!-- BEGIN: row -->
        <li class="{row.cur}"><a href="{row.link}"><span>{row.stt}</span> {row.title}</a></li>
        <!-- END: row -->
    </ul>
    <div class="support"><a href="">{LANG.event.support}</a></div>
</div>
<!-- END: col_left -->

<!-- BEGIN: col_right -->
<div class="col_right">
    <!-- BEGIN: step1 -->
    <form id="step1" action="{data.link}" method="post">
        <div class="scroll">
            <div class="infomation">
                <div class="img-left"><img src="{CONF.rooturl}resources/images/user/about.png" alt="" /></div>
                <div class="content-right">
                    <h2 class="main_title">{LANG.event.infomation}</h2>
                    <div class="note_before_title">{LANG.event.note_infomation}</div>
                    <input class="class_input" type="text" name="title1" value="{data.title1}" placeholder="{LANG.event.enter_title1Event}"/>
                    <input class="class_input" type="text" name="title" value="{data.title}" placeholder="{LANG.event.enter_titleEvent}*" required/>
                    <input class="class_input" type="text" name="organizer" value="{data.organizer}" placeholder="{LANG.event.enter_organizer}*" required/>
                    <input class="class_input" type="text" name="organizer_phone" value="{data.organizer_phone}" placeholder="{LANG.event.enter_organizerPhone}*" required/>
                    <div class="arr_picture">
                        <span class="name">{LANG.event.add_logo}</span>
                        <!-- BEGIN: bo -->
                        {data.arr_picture}
                        <!-- END: bo -->
                        <div id="arr_picture">
                            <input type="file" id="gallery-photo-add" class="inputfile inputfile-1" data-multiple-caption="{count} Đã chọn hình" multiple="" accept="image/*">
                            <label for="gallery-photo-add"><i class="far fa-plus"></i></label>
                            <div class="gallery-input gallery-default">
                                <!-- BEGIN: arr_picture -->
                                <div class="item-image">
                                    <div class="img">
                                        <input type="hidden" name="arr_pic[]" value="{pic.src_o}">
                                        <img src="{pic.src}" data-file="" class="" title="Click to remove">
                                    </div>
                                    <span class="selFile"><i class="fa fa-times"></i></span>
                                </div>
                                <!-- END: arr_picture -->
                            </div>
                        </div>
                    </div>
                    <div class="choose">
                        <label for="">{LANG.event.choose}</label>
                        <select name="group_id" id="group" required>
                            <option value="">{LANG.event.menu_title}</option>
                            <!-- BEGIN: option -->
                            <option value="{option.group_id}" {option.cur}>{option.title}</option>
                            <!-- END: option -->
                        </select>
                    </div>
                    <div class="tag_card">
                        <div class="title_tag">{LANG.event.tag}</div>
                        <div class="note_card">{LANG.event.note_card}</div>

                        <div class="form-group">
                            <div id="tag_list_input">
                                <input type="text" maxlength="70" value="" data-type="text" placeholder="{LANG.event.enter_tag}"/>
                                <button id="tag_list-btn" type="button">{LANG.event.add}</button>
                            </div>
                        </div>
                        <div class="limit">
                            <div class="limit_tag">0/10 {LANG.event.tag}</div>
                            <div class="limit_char">0/25</div>
                        </div>
                        <div id="tag_list" class="list_text">
                            <!-- BEGIN: list_tag -->
                            <div class="list_text-item">
                                <span>{row}</span>
                                <input name="tag_list[]" type="hidden" value="{row}">
                                <a href="javascript:;" class="remove"><i class="fal fa-times"></i></a>
                            </div>
                            <!-- END: list_tag -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="location">
                <div class="img-left">
                    <img src="{CONF.rooturl}resources/images/user/location.png" alt="">
                </div>
                <div class="content-right">
                    <h2 class="main_title">{LANG.event.location}</h2>
                    <div class="note_before_title">{LANG.event.note_location}</div>
                    <ul class="select select_location list_none">
                        <li data-id="tab_location"><input type="radio" name="type_event" value="0" id="type_event1" {data.active_offline}><label for="type_event1">{LANG.event.location}</label></li>
                        <li data-id="tab_event_online"><input type="radio" name="type_event" value="1" id="type_event2" {data.active_online}><label for="type_event2">{LANG.event.event_onl}</label></li>
                    </ul>
                    <div class="tab_content">
                        <div class="tab tab_location {data.hide_offline}" id="tab_location">
                            <div class="event_location">{LANG.event.event_location}</div>
                            <div class="form-group">
                                {data.list_province}
                                <input type="text" class="class_input" name="address" value="{data.address}" placeholder="{LANG.event.enter_location}" />
                            </div>
                            <textarea name="iframe" placeholder="Iframe">{data.frame_maps}</textarea>
                        </div>
                        <div class="tab tab_event_online {data.hide_online}" id="tab_event_online">
                            <div class="event_location">{LANG.event.link_event}</div>
                            <input type="text" class="class_input" name="link_event" placeholder="{LANG.event.link_event}" value="{data.link_event}" {data.disable_online}/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="datetime">
                <div class="img-left">
                    <img src="{CONF.rooturl}resources/images/user/datetime.png" alt="">
                </div>
                <div class="content-right">
                    <h2 class="main_title">{LANG.event.datetime}</h2>
                    <div class="note_before_title">{LANG.event.note_datetime}</div>

                    <ul class="select select_datetime list_none">
                        <li class="{data.active_once}">{LANG.event.event_only}</li>
                        <li class="{data.active_daily}" data-repeat='y'>{LANG.event.event_repeat}</li>
                    </ul>
                    <div class="tab_event_only">
                        <input type="hidden" value="once" name="frequency" class="once" {data.disabled_once}/>
                    </div>
                    <div class="tab_event_repeat {data.hide_select}">
                        <select class="select_frequency" name="frequency" id="frequency" {data.disabled_event}>
                            <!-- BEGIN: frequency_option -->
                            <option value="{row.val}" {row.selected}>{row.title}</option>
                            <!-- END: frequency_option -->
                        </select>
                    </div>
                    <div class="tab_content">
                        <div class="note_before_title">{LANG.event.note_event_only}</div>
                        <div class="row mx-lg-0">
                            <div class="form-group">
                                <div class="item">
                                    <div class="img"><i class="far fa-calendar"></i></div>
                                    <input class="class_input time" type="text" name="date_begin" value="{data.date_begin}" autocomplete="off" placeholder="{LANG.event.event_begin}*" required/>
                                </div>
                                <div class="item">
                                    <div class="img"><i class="far fa-calendar"></i></div>
                                    <input class="class_input time" type="text" name="date_end" value="{data.date_end}" autocomplete="off" placeholder="{LANG.event.event_end}*" required/>
                                </div>
                            </div>
                            <div class="form-group">
                                <input class="class_input no-pl onlytime" type="text" autocomplete="off" value="{data.time_begin}" name="time_begin" placeholder="{LANG.event.time_begin}" required/>
                                <input class="class_input no-pl onlytime" type="text" autocomplete="off" value="{data.time_end}" name="time_end" placeholder="{LANG.event.time_end}" required/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="submit">
            <div class="container">
                {data.id_edit}
                <div class="row justify-content-end">
                    <button class="cancel" type="button">{LANG.event.cancel}</button>
                    <button class="continue" type='submit'>{LANG.event.save_continue}</button>
                </div>
            </div>
        </div>
    </form>
    <!-- END: step1 -->

    <!-- BEGIN: step2 -->
    <form id="step2" action="{data.link}" method="post">
        <div class="picture_event">
            <div class="img-left">
                <img src="{CONF.rooturl}resources/images/user/picture.png" alt=""/>
            </div>
            <div class="content-right">
                <h2 class="main_title">{LANG.event.picture_main}</h2>
                <div class="note_before_title">{LANG.event.picture_note}</div>
                <div class="file">
                    <img src="{CONF.rooturl}resources/images/user/default_img.png" alt="" />
                    <h4>{LANG.event.move_picture}</h4>
                    <h5>{LANG.event.max_file}</h5>
                    <input type="file" name="picture" id="choose-file" />
                </div>
                <div class="img-preview" id="img-preview">
                    <!-- BEGIN: img -->
                    <input type="hidden" name="picture" id="choose-file" value="{data.src_o}"/>
                    <img src="{data.src}" alt="">
                    <!-- END: img -->
                </div>
            </div>
        </div>
        <div class="short_event">
            <div class="img-left">
                <img src="{CONF.rooturl}resources/images/user/short.png" alt=""/>
            </div>
            <div class="content-right">
                <h2 class="main_title">{LANG.event.short_event}</h2>
                <div class="note_before_title">{LANG.event.short_note}</div>
                <div class="summary">{LANG.event.summary}*</div>
                <textarea name="short" placeholder="{LANG.event.enter_short}">{data.short}</textarea>
                <div class="box_short">{data.html_content}</div>
            </div>
        </div>

        <div class="submit">
            <div class="container">
                {data.id_edit}
                <div class="row justify-content-end">
                    <button class="cancel" type="button">{LANG.event.cancel}</button>
                    <button class="continue" type="submit">{LANG.event.save_continue}</button>
                </div>
            </div>
        </div>
    </form>
    <!-- END: step2 -->

    <!-- BEGIN: step3 -->
    <form id="step3" action="" method="post">
        <div class="create_card">
            <div class="img-left">
                <img src="{CONF.rooturl}resources/images/user/icon_create.png" alt=""/>
            </div>
            <div class="content-right">
                <h2 class="main_title">{LANG.event.create_title}</h2>
            </div>
        </div>
        <div class="tab_create">
            <div class="title">{LANG.event.add_card}</div>
            <ul class="list_none tab_type">
                <li class="{data.type_pay}" data-type="pay">{LANG.event.pay_fees}</li>
                <li class="{data.type_free}" data-type="free">{LANG.event.free}</li>
                <li class="{data.type_donate}" data-type="donate">{LANG.event.donate}</li>
            </ul>

            <div id="pay" class="tab_info {data.hide_pay}">
                <div class="list_card">
                    <!-- BEGIN: add -->
                    <div class="item_card">
                        {clear_div}
                        <input class="class_input" type="text" name="{type_ticket}['{index}']['title']" placeholder="{LANG.event.title_card}*" required/>
                        <input class="class_input num_ticket" type="number" name="{type_ticket}['{index}']['num_ticket']" placeholder="{LANG.event.number_card}*" required/>
                        <input class="class_input" type="hidden" name="{type_ticket}['{index}']['num_ticket_remain']" value="" />
                        <input class="class_input" type="hidden" name="{type_ticket}['{index}']['type_ticket']" value="{type_ticket}" />
                        <!-- BEGIN: price -->
                        <div class="group">
                            <div class="dollar"><i class="far fa-dollar-sign"></i></div>
                            <input class="class_input" type="number" name="{type_ticket}['{index}']['price']" placeholder="{LANG.event.price_card}*" required/>
                            <span class="price">{LANG.global.unit}</span>
                        </div>
                        <!-- END: price -->
                        <!-- BEGIN: free -->
                        <input class="class_input" type="hidden" name="{type_ticket}['{index}']['price']" value="0"/>
                        <!-- END: free -->
                        <textarea name="{type_ticket}['{index}']['short']" placeholder="{LANG.event.description}"></textarea>
                    </div>
                    <!-- END: add -->

                    <!-- BEGIN: pay -->
                    <div class="item_card">
                        <p class="clear_div"><span class="del"><i class="far fa-times"></i></span></p>
                        <input class="class_input" type="text" name="pay['{col.index}']['title']" value="{col.title}" placeholder="{LANG.event.title_card}" />
                        <input class="class_input {col.num_ticket_class}" type="number" name="pay['{col.index}']['num_ticket']" min=0 value="{col.num_ticket}" placeholder="{LANG.event.number_card}"/>
                        <input class="class_input" type="{col.type_num_remain}" name="pay['{col.index}']['num_ticket_remain']" min=0 value="{col.num_ticket_remain}" placeholder="{LANG.event.number_card_remain}"/>
                        <input class="class_input" type="hidden" name="pay['{col.index}']['type_ticket']" value="pay"/>
                        <div class="group">
                            <div class="dollar"><i class="far fa-dollar-sign"></i></div>
                            <input class="class_input" type="number" name="pay['{col.index}']['price']" value="{col.price}" placeholder="{LANG.event.price_card}"/>
                            <span class="price">{LANG.global.unit}</span>
                        </div>
                        <textarea name="pay['{col.index}']['short']" placeholder="{LANG.event.description}">{col.short}</textarea>
                    </div>
                    <!-- END: pay -->
                </div>
                <div class="add_more">{LANG.event.add_more} <span><i class="far fa-plus"></i></span></div>
            </div>
            <div id="free" class="tab_info {data.hide_free}">
                <div class="list_card">
                    <!-- BEGIN: free -->
                    <div class="item_card">
                        <p class="clear_div"><span class="del"><i class="far fa-times"></i></span></p>
                        <input class="class_input" type="text" name="free['{col.index}']['title']" value="{col.title}" placeholder="{LANG.event.title_card}" />
                        <input class="class_input {col.num_ticket_class}" type="number" name="free['{col.index}']['num_ticket']" value="{col.num_ticket}" placeholder="{LANG.event.number_card}"/>
                        <input class="class_input" type="{col.type_num_remain}" name="free['{col.index}']['num_ticket_remain']" value="{col.num_ticket_remain}" placeholder="{LANG.event.number_card_remain}"/>
                        <input class="class_input" type="hidden" name="free['{col.index}']['type_ticket']" value="free"/>
                        <!-- BEGIN: bo -->
                        <div class="group">
                            <div class="dollar"><i class="far fa-dollar-sign"></i></div>
                            <input class="class_input" type="number" name="free['{col.index}']['price']" value="{col.price}" placeholder="{LANG.event.price_card}"/>
                            <span class="price">{LANG.global.unit}</span>
                        </div>
                        <!-- END: bo -->
                        <input class="class_input" type="hidden" name="free['{col.index}']['price']" value="0"/>
                        <textarea name="free['{col.index}']['short']" placeholder="{LANG.event.description}">{col.short}</textarea>
                    </div>
                    <!-- END: free -->
                </div>
                <div class="add_more">{LANG.event.add_more} <span><i class="far fa-plus"></i></span></div>
            </div>
            <div id="donate" class="tab_info {data.hide_donate}">
                <div class="list_card">
                    <!-- BEGIN: donate -->
                    <div class="item_card">
                        <p class="clear_div"><span class="del"><i class="far fa-times"></i></span></p>
                        <input class="class_input" type="text" name="donate['{col.index}']['title']" value="{col.title}" placeholder="{LANG.event.title_card}" />
                        <input class="class_input {col.num_ticket_class}" type="number" name="donate['{col.index}']['num_ticket']" value="{col.num_ticket}" placeholder="{LANG.event.number_card}"/>
                        <input class="class_input" type="{col.type_num_remain}" name="donate['{col.index}']['num_ticket_remain']" value="{col.num_ticket_remain}" placeholder="{LANG.event.number_card_remain}" />
                        <input class="class_input" type="hidden" name="donate['{col.index}']['type_ticket']" value="donate"/>
                        <div class="group">
                            <div class="dollar"><i class="far fa-dollar-sign"></i></div>
                            <input class="class_input" type="number" name="donate['{col.index}']['price']" value="{col.price}" placeholder="{LANG.event.price_card}"/>
                            <span class="price">{LANG.global.unit}</span>
                        </div>
                        <textarea name="donate['{col.index}']['short']" placeholder="{LANG.event.description}">{col.short}</textarea>
                    </div>
                    <!-- END: donate -->
                </div>
                <div class="add_more">{LANG.event.add_more} <span><i class="far fa-plus"></i></span></div>
            </div>
        </div>
        <div class="info_basic">
            <div class="title">{LANG.event.info_basic}</div>
            <div class="choose">
                <div class="title">{LANG.event.time_buy_card}*</div>
            </div>
            <div class="form-group">
                <label for="">{LANG.event.time_begin}:</label>
                <div class="row">
                    <div class="col-md-6 col-12 col">
                        <div class="item_img">
                            <div class="img"><i class="far fa-calendar-alt"></i></div>
                            <input class="time" type="text" name="date_begin" placeholder="dd/mm/YY" value="{data.date_begin}" autocomplete="off" required/>
                        </div>
                    </div>
                    <div class="col-md-6 col-12 col">
                        <div class="item">
                            <div class="img"><img src="{CONF.rooturl}resources/images/user/icon_time.png" alt=""></div>
                            <input type="text" name="time_begin" class="onlytime" placeholder="00:00" value="{data.time_begin}" autocomplete="off" required/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="">{LANG.event.time_end}:</label>
                <div class="row">
                    <div class="col-md-6 col-12 col">
                        <div class="item_img">
                            <div class="img"><i class="far fa-calendar-alt"></i></div>
                            <input class="time" type="text" name="date_end" placeholder="dd/mm/YY" value="{data.date_end}" autocomplete="off" required/>
                        </div>
                    </div>
                    <div class="col-md-6 col-12 col">
                        <div class="item">
                            <div class="img"><img src="{CONF.rooturl}resources/images/user/icon_time.png" alt=""></div>
                            <input type="text" name="time_end" class="onlytime" placeholder="00:00" value="{data.time_end}" autocomplete="off" required/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="oneperson">
            <div class="title">{LANG.event.number_card_oneperson}</div>
            <div class="row">
                <div class="col-md-6 col-12 col">
                    <input type="text" name="min_card" value="{data.min_card}" placeholder="{LANG.event.min_card}*" autocomplete="off" required/>
                </div>
                <div class="col-md-6 col-12 col">
                    <input type="text" name="max_card" value="{data.max_card}" placeholder="{LANG.event.max_card}*" autocomplete="off" required/>
                </div>
            </div>
        </div>
        <div class="submit">
            <div class="container">
                <div class="row justify-content-end">
                    {data.id_edit}
                    <button class="cancel" type="button">{LANG.event.cancel}</button>
                    <button class="continue" type="submit">{LANG.event.save_continue}</button>
                </div>
            </div>
        </div>
    </form>
    <div class="view_card">
        <div class="create_card">
            <div class="img-left">
                <img src="{CONF.rooturl}resources/images/user/icon_create.png" alt=""/>
            </div>
            <div class="content-right">
                <h2 class="main_title">{LANG.event.create_title}</h2>
            </div>
        </div>
        <div class="content"></div>
        <div class="submit">
            <div class="container">
                <form id="addEvent" action="{data.link}" method="post">
                    {data.id_edit}
                    <div class="row justify-content-end">
                        <button class="cancel" type="button">{LANG.event.back}</button>
                        <button class="continue" type="button">{LANG.event.continue}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- END: step3 -->

    <!-- BEGIN: step4 -->
    <div id="step4">
        <div class="create_card">
            <div class="img-left"></div>
            <div class="content-right">
                <h2 class="main_title">{LANG.event.export_event}</h2>
            </div>
        </div>
        <div class="box_info">
            <div class="img"><img src="{data.picture}" alt="{data.title}"></div>
            <div class="info">
                <div class="top_info">
                    <div class="title">{data.title}</div>
                    <div class="time_event">{data.time}</div>
                    <div class="address">{data.address}</div>
                    <div class="info_price">
                        <div class="price"><img src="{CONF.rooturl}resources/images/user/ticker.png" alt="ticker"/> {data.price}</div>
                        <div class="people"><img src="{CONF.rooturl}resources/images/user/user.png" alt="user"/> {data.people}</div>
                    </div>
                    <div class="short">{data.short}</div>
                </div>
                <div class="preview"><button>Preview <img src="{CONF.rooturl}resources/images/user/preview.png" alt="preview"/></button></div>
            </div>
        </div>
        <div class="top_create">
            <button>{LANG.event.create_promo}</button>
            <div class="note {data.class_note}">{data.note}</div>
        </div>
        <form id="create_promo" class="create_promotion" action="" method="post">
            <div class="form_create">
                <div class="who">
                    <div class="title">{LANG.event.who_view}</div>
                    <ul class="list_none">
                        <li>
                            <input type="radio" class="name_course" name="is_expected" id="all_view" value="1" {data.checked_all}>
                            <span class="checkmark"></span>
                            <label class="answer-radio-label" for="all_view">{LANG.event.all_view}</label>
                        </li>
                        <li>
                            <input type="radio" class="name_course" name="is_expected" id="some_people" value="0" {data.checked_people}>
                            <span class="checkmark"></span>
                            <label class="answer-radio-label" for="some_people">{LANG.event.some_people}</label>
                        </li>
                    </ul>
                </div>

                <div class="time_start">
                    <div class="title">{LANG.event.time_start_event}</div>
                    <ul class="list_none">
                        <li>
                            <input type="radio" class="name_course" name="day_expected" value="1" id="now" {data.checked_now}>
                            <span class="checkmark"></span>
                            <label class="answer-radio-label" for="now">{LANG.event.now}</label>
                        </li>
                        <li>
                            <input type="radio" class="name_course" name="day_expected" value="0" id="booking" {data.checked_booking}>
                            <span class="checkmark"></span>
                            <label class="answer-radio-label" for="booking">{LANG.event.booking}</label>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6 col-12 col">
                                        <div class="item_img">
                                            <div class="img"><i class="far fa-calendar-alt"></i></div>
                                            <input class="time" type="text" name="day_event_start" placeholder="dd/mm/YY" autocomplete="off" value="" {data.disable_day_time}/>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12 col">
                                        <div class="item">
                                            <div class="img"><img src="{CONF.rooturl}resources/images/user/icon_time.png" alt=""></div>
                                            <input class="onlytime" type="text" name="time_event_start" placeholder="00:00" autocomplete="off" {data.disable_day_time}/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="qr_code">
                <div class="wrap">
                    <img src="https://chart.googleapis.com/chart?chs=225x225&cht=qr&chl={data.link_share}&choe=UTF-8" alt="Link to {data.title}" />
                </div>
                <div class="title_qr">{LANG.event.title_qr}</div>
            </div>
            <div class="submit">
                <div class="container">
                    {data.id_edit}
                    <div class="row justify-content-end">
                        <button class="continue" type="submit">{LANG.event.export}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<div id="promotion"></div>
<!-- END: step4 -->
</div>
<div id="preview_event"></div>

<script>
    $('.time').datepicker({
        dateFormat: 'dd/mm/yy',
        autoclose: true,
        changeMonth: true,
        changeYear: true,
        minDate: 0,
        // dateFormat: '',
        // timeFormat: 'hh:mm tt'
    });
    $('.onlytime').timepicker();
    $('#step3').on('click', '.add_more', function(event) {
        var type = $(this).parent().attr('id');
        var fdata = $("#step3").serializeArray();
        $.ajax({
            type: "POST",
            url: ROOT+"ajax.php",
            data: { 'm' : 'global', 'f' : 'add_price', 'type': type, 'data':fdata, 'lang_cur':lang}
        }).done(function(string) {
            var data = JSON.parse(string);
            loading('hide');
            if(data.html != '') {
                $('#'+type+' .list_card').append(data.html);
            }
        });
    });
    $(document).on('click', '.clear_div', function(event) {
        $(this).parent().remove();
    });

    var timer = null;
    $(document).on('keyup', 'input.num_ticket', function() {
        clearTimeout(timer);
        var vl = $(this).val(),
            item = $(this).next();
        timer = setTimeout(function() {
            item.val(vl);
        }, 200);
    });
</script>
<!-- END: col_right -->

<!-- BEGIN: col_price -->
<div class="col_price">
    <div class="row">
        <div class="col-md-5 col-5">
            <div class="title">{row.title}</div>
            <ul class="list_none">
                <li class="color"><i class="fas fa-circle"></i> {LANG.event.saleing}</li>
                <li><i class="fas fa-circle"></i> {LANG.event.duration} {row.date}</li>
            </ul>
        </div>
        <div class="col-md-2 col-2" align="center">
            <div class="title">{LANG.event.quantityL}</div>
            <ul class="list_none center">
                <li>0/{row.num_ticket}</li>
            </ul>
        </div>
        <div class="col-md-4 col-4" align="center">
            <div class="title">{LANG.event.price_buy} <span>({LANG.global.unit})</span></div>
            <ul class="list_none center">
                <li>{row.price_text}</li>
            </ul>
        </div>
        <div class="col-md-1 col-1">
            <div class="edit" data-type="{row.type_ticket}">
                <i class="far fa-ellipsis-v"></i>
                <ul class="list_none">
                    <li><a href="{row.link}"><i class="fal fa-edit"></i> Edit</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- END: col_price -->

<!-- BEGIN: form_promotion -->
<div class="form_promotion">
    <form action="" id="add_promo" method="post">
        <input type="hidden" name="id" value="{data.id}"/>
        <div class="create_card">
            <div class="content-right">
                <h2 class="main_title">{LANG.event.title_promo}</h2>
            </div>
        </div>
        <ul class="list_none type_code">
            <li>
                <input type="radio" name="type_code" id="every_one" value="apply_product" checked />
                <span class="checkmark"></span>
                <label class="answer-radio-label" for="every_one">{LANG.event.every_one}</label>
            </li>
            <li>
                <input type="radio" name="type_code" id="by_email" value="apply_email" />
                <span class="checkmark"></span>
                <label class="answer-radio-label" for="by_email">{LANG.event.by_email}</label>
            </li>
        </ul>
        <div class="tab_promo">
            <div id="every_one" class="tab_pro">
                <div class="code">{LANG.event.code}</div>
                <input type="text" name="hand_code" value=""/>
                <div class="note_code">{LANG.event.note_code}</div>
                <div class="num_used">{LANG.event.num_used}</div>
                <input type="text" name="max_use" value=""/>
                <div class="discount_price">{LANG.event.discount_price}</div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="col-md-6 col-12 pl-0">
                            <div class="input-group-prepend">
                                <select name="value_type"  class="form-control">
                                    <option value="0"><i class="far fa-dollar-sign"></i> {LANG.event.discount_money}</option>
                                    <option value="1">{LANG.event.discount_percent}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 col-12 pr-0">
                            <div class="d-flex">
                                <div data-for="0" class="value_type_control" style="padding: 0px; display: block;">
                                    <input type="text" size="50" maxlength="10" value="0" class="auto_int">
                                    <input name="price0" type="hidden" maxlength="10" class="auto_int_input">
                                </div>
                                <div data-for="1" class="value_type_control" style="padding: 0px; display: none;">
                                    <input type="text" size="50" maxlength="10" value="" class="auto_int">
                                    <input name="price1" type="hidden" maxlength="10" class="auto_int_input" disabled>
                                </div>
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <div data-for="0" class="input-group-addon value_type_control" style="padding: 0px; display: block;">{LANG.global.unit}</div>
                                        <div data-for="1" class="input-group-addon value_type_control" style="padding: 0px; display: none;">%</div>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="time_code">
                    <input type="checkbox" name="time_code" id="check_every"/>
                    <label for="check_every">{LANG.event.time_code}</label>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6 col-12">
                            <div class="title">{LANG.event.time_begin}</div>
                            <div class="item">
                                <div class="img"><i class="fal fa-calendar-alt"></i></div>
                                <input class="class_input time_all" type="text" name="date_begin" autocomplete="off" placeholder="00:00 AM, dd/mm/yyyy" disabled />
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="title">{LANG.event.time_end}</div>
                            <div class="item">
                                <div class="img"><i class="fal fa-calendar-alt"></i></div>
                                <input class="class_input time_all" type="text" name="date_end" autocomplete="off" placeholder="00:00 AM, dd/mm/yyyy" disabled />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="list_email">
                    <div class="title">{LANG.event.list_mail}</div>
                    <textarea name="list_email" id="" rows="5"></textarea>
                </div>
                <div class="content_email">
                    <div class="title">{LANG.event.content_email}</div>
                    <textarea name="content_email" id="" rows="5"></textarea>
                </div>
            </div>
            <div id="by_email" class="tab_pro hide">
                <div class="code">{LANG.event.code}</div>
                <input type="text" name="hand_code" value=""/>
                <div class="note_code">{LANG.event.note_code}</div>
                <div class="list_email">
                    <div class="title">{LANG.event.list_mail}</div>
                    <textarea name="list_email" id="" rows="5" required></textarea>
                </div>
                <div class="discount_price">{LANG.event.discount_price}</div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="col-md-6 col-12 pl-0">
                            <div class="input-group-prepend">
                                <select name="value_type"  class="form-control">
                                    <option value="0"><i class="far fa-dollar-sign"></i> {LANG.event.discount_money}</option>
                                    <option value="1">{LANG.event.discount_percent}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 col-12 pr-0">
                            <div class="d-flex">
                                <div data-for="0" class="value_type_control" style="padding: 0px; display: block;">
                                    <input type="text" size="50" maxlength="10" value="0" class="auto_int">
                                    <input name="price0" type="hidden" maxlength="10" class="auto_int_input">
                                </div>
                                <div data-for="1" class="value_type_control" style="padding: 0px; display: none;">
                                    <input type="text" size="50" maxlength="10" value="" class="auto_int">
                                    <input name="price1" type="hidden" maxlength="10" class="auto_int_input" disabled>
                                </div>
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <div data-for="0" class="input-group-addon value_type_control" style="padding: 0px; display: block;">{LANG.global.unit}</div>
                                        <div data-for="1" class="input-group-addon value_type_control" style="padding: 0px; display: none;">%</div>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="time_code">
                    <input type="checkbox" name="time_code" id="check_byemail" />
                    <label for="check_byemail">{LANG.event.time_code}</label>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6 col-12">
                            <div class="title">{LANG.event.time_begin}</div>
                            <div class="item">
                                <div class="img"><i class="fal fa-calendar-alt"></i></div>
                                <input class="class_input time_all" type="text" name="date_begin" autocomplete="off" placeholder="00:00 AM, dd/mm/yyyy" disabled />
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="title">{LANG.event.time_end}</div>
                            <div class="item">
                                <div class="img"><i class="fal fa-calendar-alt"></i></div>
                                <input class="class_input time_all" type="text" name="date_end" autocomplete="off" placeholder="00:00 AM, dd/mm/yyyy" disabled />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content_email">
                    <div class="title">{LANG.event.content_email}</div>
                    <textarea name="content_email" id="" rows="5"></textarea>
                </div>
            </div>
        </div>
        <div class="submit">
            <div class="container">
                <form id="addEvent" action="{data.link}" method="post">
                    <div class="row justify-content-end">
                        <button class="cancel" type="button">{LANG.event.cancel}</button>
                        <button class="continue" type="submit">{LANG.event.create}</button>
                    </div>
                </form>
            </div>
        </div>
    </form>
</div>
<!-- END: form_promotion -->


<!-- BEGIN: detail -->
<div class="container">
    <div class="event_detail">
        <div class="background"><img src="{data.background}" alt="background"></div>
        <div class="wrap_detail">
            <div class="edit_event">
                <div class="edit"><a href="{data.link_edit}">{LANG.event.change_edit} <i class="fas fa-pen-alt"></i></a></div>
                <div class="exit"><a href="#" onclick='$.fancybox.close()'>{LANG.event.exit_edit} <i class="far fa-times"></i></a></div>
            </div>
            <div class="info_top">
                <div class="picture">
                    <div class="img"><a href="{data.pic_zoom}" data-fancybox><img src="{data.picture}" alt="{data.e_title}"></a></div>
                    <div class="share_favorite d-md-none">
                        <div class="share"><a href="#share" class="goto"><img src="{CONF.rooturl}resources/images/use/share.svg" alt="share"></a></div>
                        <div class="add_favorite {data.added}" data-id="{data.item_id}"><i class="{data.i_favorite}"></i></div>
                    </div>
                </div>
                <div class="info">
                    <div class="wrap_info">
                        <h1 class="title">{data.title1}<span>{data.title}</span></h1>
                        {data.organizational}
                        <div class="date_begin">{data.date_begin}</div>
                        {data.follow}
                    </div>
                    <div class="price">{data.price}</div>
                    <div class="btn_register d-md-none">
                        <button data-it="{data.item_id}">{LANG.event.register}</button>
                    </div>
                </div>
            </div>
            <div class="info_pc d-md-flex d-none">
                <div class="share_favorite">
                    <div class="share"><a href="#share" class="goto"><img src="{CONF.rooturl}resources/images/use/share.svg" alt="share"></a></div>
                    <div class="add_favorite {data.added}" data-id={data.item_id}><i class="{data.i_favorite}"></i></div>
                </div>
                <div class="btn_register">
                    <button data-it="{data.item_id}" {data.register_disable} >{LANG.event.register}</button>
                </div>
            </div>
            <div class="info_bottom">
                <div class="content">
                    <ul class="nav nav-pills">
                        <li class="nav-item"><a href="#tab_content" data-toggle="tab" class="nav-link active">{LANG.event.detail_content_title}</a></li>
                        <li class="nav-item"><a href="#tab_store" data-toggle="tab" class="nav-link">{LANG.event.store}</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_content"><div class="detail_content">{data.content}</div></div>
                        <div class="tab-pane" id="tab_store">{data.event_product}</div>
                    </div>
                </div>
                <div class="column">
                    <div class="item time">
                        <img src="{CONF.rooturl}resources/images/use/calendar.svg" alt="time">
                        <p>{LANG.event.time}:</p>
                        {data.time}
                    </div>
                    <div class="item address">
                        <img src="{CONF.rooturl}resources/images/use/location.svg" alt="location">
                        <p>{data.location}:</p>
                        {data.address}
                        {data.link_event_maps}
                    </div>
                    <div class="qr_code">
                        <div class="wrap">
                            <img src="https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=Y1lHK2xjOWpMOU5oWVBmd0VMUXMrckpvQVlJNFJoZ2t6aGZuRjg5RWpGcGtRdis1TVMrbU1mT244US93ODh1WmkzN3FFdm92U0c2UHc2aFdrdjBmRldvUkZsV2xrVlB2QlRDNDdtVGE3Q0ZPczg4d1JGMjZDSTYyWVMxSWVFNUk=&choe=UTF-8" title="Link to {data.title}" />
                        </div>
                    </div>
                </div>
            </div>
            <div id="share">
                <p>{LANG.event.share}:</p>
                <ul class="list_none">
                    <li><a href="https://twitter.com/intent/tweet?url={data.link_share}" target="_blank"><img src="{CONF.rooturl}resources/images/use/twitter.svg" alt="twitter"></a></li>
                    <li><a href="https://www.instagram.com/?url={data.link_share}" target="_blank"><img src="{CONF.rooturl}resources/images/use/instagram.svg" alt="instagram"></a></li>
                    <li><a href="https://www.linkedin.com/shareArticle?mini=true&url=&title=&summary=&source={data.link_share}" target="_blank"><img src="{CONF.rooturl}resources/images/use/linkedin.svg" alt="linkedin"></a></li>
                    <li><a href="https://facebook.com/sharer/sharer.php?u={data.link_share}" target="_blank"><img src="{CONF.rooturl}resources/images/use/facebook.svg" alt="facebook"></a></li>
                </ul>
            </div>
            <div id="address" class="{data.border}">
                <div class="title"><span>{data.title1}</span>{data.title}</div>
                <p>{data.time}</p>
                <p>{data.address}</p>
                {data.link_event_text}
                {data.maps}
            </div>
            {data.event_same_organization}
            {data.event_other}
        </div>
    </div>
    <div class="modal fade" id="register" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"></span>
                </button>
                <div class="modal-body">
                    <form id="event_register" class="register_form" name="register" method="post" action="">
                        <div class="group has_ev_info">
                            <div class="left"></div>
                            <div class="right">
                                <div class="event_pic mobile"><div class="img"><a><img src="{data.picture_form}"></a></div></div>
                            </div>
                        </div>
                        <div class="group form">
                            <div class="left">
                                <div class="content_form"></div>
                            </div>
                            <div class="right">
                                <div class="event_pic"><div class="img"><a><img src="{data.picture_form}"></a></div></div>
                                <div class="cart_info"></div>
                            </div>
                        </div>
                        <div class="submit">
                            <div class="wrap">
                                <button type="button">
                                    {LANG.event.register}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="detail_store" tabindex="-1" role="dialog" aria-hidden="true" style="opacity: 0">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="detail_store_item">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<footer class="bg-footer color-footer">
    <div class="container">
        <div class="top">
            <div class="wrap_top">
                <div class="footer_logo">
                    {data.footer_logo}
                </div>
                <div class="contact_footer">
                    <div class="footer_title">{data.contact_footer_title}</div>
                    {data.contact_footer}
                </div>
                <div class="menu menu1">
                    {data.menu_footer1}
                </div>
                <div class="menu menu2">
                    {data.menu_footer2}
                    {data.social}
                </div>
            </div>
            <div class="menu_footer">{data.menu_footer}</div>
        </div>
        <div class="bottom">
            <div class="copyright">{data.copyright}</div>
        </div>
    </div>
    <div class="container">{CONF.tag_footer}</div>
    {data.popup}
</footer>
<!-- END: detail -->