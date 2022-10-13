<!-- BEGIN: event -->
<div id="event_manager">
    <div class="box_header">
        <h1>{LANG.user.event_choose}</h1>        
    </div>
    <form action="{data.link_action}" method="get" class="box_search_event">
        <div class="row">
            <div class="col-12 col-md-4">
                <div class="form-group">
                    <input type="text" name="search_keyword" class="form-control" placeholder="{LANG.user.event_filter}" value="{data.search_keyword}">
                    <button type="submit" class="icon"><img src="{data.src}/search.svg" alt="search"></button>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group">
                    {data.status}
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group">
                    {data.organizer}
                </div>
            </div>
        </div>
    </form>
    <div class="box_content" style="overflow-x: auto;">
        <table class="table table-responsives">
            <thead>
                <tr>
                    <th scope="col">{LANG.user.event}</th>
                    <th scope="col" align="center" width="15%">{LANG.user.ticket}</th>
                    <th scope="col" align="center" width="15%">{LANG.user.revenue}</th>
                    <th scope="col" align="center" width="15%">{LANG.user.participations}</th>
                    <th scope="col" align="center" width="15%"></th>
                </tr>
            </thead>
            <tbody>
                <!-- BEGIN: row -->
                <tr>
                    <td>
                        <div class="detail">
                            <div class="picture"><img src="{row.picture}" alt="{row.title}"></div>
                            <div class="info">
                                <div class="title">{row.title}</div>
                                <div class="address">{row.address}</div>
                                <div class="datetime">{row.date_begin}</div>
                                <div class="status">{row.status}</div>
                            </div>
                        </div>
                    </td>
                    <td>{row.ticket_remain}/{row.ticket_total}</td>
                    <td>{row.revenue}</td>
                    <td>{row.participations}</td>
                    <td>
                        <div><a href="{row.link_image}">{LANG.user.image_manager}</a></div>                        
                    </td>
                </tr>
                <!-- END: row -->
                <!-- BEGIN: row_empty -->
                <tr><td colspan="5">{row.title}</td></tr>
                <!-- END: row_empty -->
            </tbody>
        </table>
    </div>
    {data.nav}
</div>
<!-- END: event -->

<!-- BEGIN: image -->
<div id="image_manager">
    <!-- BEGIN: logo -->
    <div class="logo">
        <!-- BEGIN: row -->
        <div class="item">
            <span class="img"><img src="{row.picture}" alt="{row.title}"></span>
        </div>
        <!-- END: row -->
    </div>
    <!-- END: logo -->
    <div class="info">
        <h1>{data.title}</h1>
        <div class="date">{data.date_begin}</div>
        <div class="organizer">{LANG.user.organizer}: <span>{data.organizer}</span></div>
    </div>
    <div class="box_tab">
        <ul class="nav nav-pills" id="pills-tab" role="tablist">            
            <li class="nav-item" role="presentation">
                <a href="javascript:void(0)" class="nav-link active" id="pills-event-tab" data-toggle="pill" data-target="#pills-event" data-tab="event" role="tab" aria-controls="pills-event" aria-selected="false">{LANG.user.image_event}</a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="javascript:void(0)" class="nav-link" id="pills-personal-tab" data-toggle="pill" data-target="#pills-personal" data-tab="personal" role="tab" aria-controls="pills-personal" aria-selected="false">{LANG.user.image_personal}</a>
            </li>
        </ul>
    </div>
    <div class="box_content">
        <div class="tab-content">
            <div class="tab-pane fade show active" id="pills-event" role="tabpanel" aria-labelledby="pills-event-tab">
                <div class="title">
                    <h2>{LANG.user.image_event}</h2>
                    <button type="button" class="btn btn-add btn-orange" data-src="#image-event"><i class="far fa-plus pr-2"></i> {LANG.user.image_add}</button>                    
                    <div class="dropdown d-inline-block">
                        <button type="button" class="btn btn-red dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="far fa-cog pr-2"></i> {LANG.user.image_edit}
                        </button>
                        <div class="dropdown-menu">
                            <div class="dropdown-item edit-image">{LANG.user.image_edit}</div>
                            <div class="dropdown-item remove-image">{LANG.user.image_remove}</div>
                        </div>
                    </div>
                </div>
                <div class="box_form" id="image-event" style="display: none;">
                    <form action="" method="post" name="event" enctype="multipart/form-data">
                        <div class="form-group mb-0">
                            <label class="label-title">{LANG.user.upload_image_title} {LANG.user.image_event}</label>
                        </div>
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
                        </div>
                        <div class="input-images" data-id="{data.event_id}" data-type="event" data-title="{data.title}"></div>
                        <div class="form-group text-right mb-0 mt-3">
                            <button type="submit" class="btn btn-upload btn-orange">{LANG.user.upload_image_button}</button>    
                        </div>
                    </form>
                </div>
                <form class="list_image" method="post" name="form_event">
                    <div class="row">
                    <!-- BEGIN: event -->
                        <div class="item">
                            <div class="checkbox">
                                <input class="id_checkbox" type="checkbox" id="cb_{row.item_id}" value="{row.item_id}" name="selected_id[]">
                                <label for="cb_{row.item_id}"></label>
                            </div>
                            <div class="img" data-fancybox data-src="{row.picture}" data-caption="{row.title}">
                                <img class="lazyload" data-src="{row.thumb}" width="190" height="170">
                            </div>
                            <div class="title mb-0" data-fancybox data-src="{row.picture}" data-caption="{row.title}">
                                {row.title}
                            </div>
                            <div class="box-edit">
                                <input type="checkbox" class="toggle_type" checked data-toggle="toggle" data-on="{LANG.user.image_event}" data-off="{LANG.user.image_personal}" data-onstyle="outline-danger" data-offstyle="outline-warning" name="image[{row.item_id}][type]">
                                <textarea name="image[{row.item_id}][title]" placeholder="{LANG.user.caption}" class="border-danger">{row.title}</textarea>
                            </div>
                        </div>
                    <!-- END: event -->
                    </div>
                    <div class="bottom">
                        <div class="row mx-0">
                            <button type="submit" class="btn btn-orange btn-update" value="1" data-id="{data.event_id}">{LANG.user.image_update}</button>
                            <button type="submit" class="btn btn-orange btn-remove" value="0" data-id="{data.event_id}">{LANG.user.image_remove}</button>
                            <button type="reset" class="btn btn-cancel">{LANG.user.image_cancel}</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="tab-pane fade" id="pills-personal" role="tabpanel" aria-labelledby="pills-personal-tab">
                <div class="title">
                    <h2>{LANG.user.image_personal}</h2>
                    <button type="button" class="btn btn-add btn-orange" data-src="#image-personal"><i class="far fa-plus pr-2"></i> {LANG.user.image_add}</button>                    
                    <div class="dropdown d-inline-block">
                        <button type="button" class="btn btn-red dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="far fa-cog pr-2"></i> {LANG.user.image_edit}
                        </button>
                        <div class="dropdown-menu">
                            <div class="dropdown-item edit-image">{LANG.user.image_edit}</div>
                            <div class="dropdown-item remove-image">{LANG.user.image_remove}</div>
                        </div>
                    </div>
                </div>
                <div class="box_form" id="image-personal" style="display: none;">
                    <form action="" method="post" name="personal" enctype="multipart/form-data">
                        <div class="form-group mb-0">
                            <label class="label-title">{LANG.user.upload_image_title} {LANG.user.image_event}</label>
                        </div>
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
                        </div>
                        <div class="input-images" data-id="{data.event_id}" data-type="personal" data-title="{data.title}"></div>
                        <div class="form-group text-right mb-0 mt-3">
                            <button type="submit" class="btn btn-upload btn-orange">{LANG.user.upload_image_button}</button>    
                        </div>
                    </form>
                </div>
                <form class="list_image" method="post" name="form_personal">
                    <div class="row">
                    <!-- BEGIN: personal -->
                        <div class="item">
                            <div class="checkbox">
                                <input class="id_checkbox" type="checkbox" id="cb_{row.item_id}" value="{row.item_id}" name="selected_id[]">
                                <label for="cb_{row.item_id}"></label>
                            </div>
                            <div class="img" data-fancybox data-src="{row.picture}" data-caption="{row.title}">
                                <img class="lazyload" data-src="{row.thumb}" width="190" height="170">
                            </div>
                            <div class="title mb-0" data-fancybox data-src="{row.picture}" data-caption="{row.title}">
                                {row.title}
                            </div>
                            <div class="box-edit">
                                <input type="checkbox" class="toggle_type" data-toggle="toggle" data-on="{LANG.user.image_event}" data-off="{LANG.user.image_personal}" data-onstyle="outline-danger" data-offstyle="outline-warning" name="image[{row.item_id}][type]">
                                <textarea name="image[{row.item_id}][title]" placeholder="{LANG.user.caption}" class="border-warning">{row.title}</textarea>
                            </div>
                        </div>
                    <!-- END: personal -->
                    </div>
                    <div class="bottom">
                        <div class="row mx-0">
                            <button type="submit" class="btn btn-orange btn-update" value="1" data-id="{data.event_id}">{LANG.user.image_update}</button>
                            <button type="submit" class="btn btn-orange btn-remove" value="0" data-id="{data.event_id}">{LANG.user.image_remove}</button>
                            <button type="reset" class="btn btn-cancel">{LANG.user.image_cancel}</button>
                        </div>
                    </div> 
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    upload_image('.input-images');
    update_image('.list_image','image_manager');
    $(document).on('click', '.btn-add', function(){
        var srcBox = $(this).data('src');
        $.fancybox.open({
            type: 'inline',
            src: srcBox,
            clickSlide: false,
            clickOutside: false,
            touch: false,
        })
    })    
</script>
<!-- END: image -->