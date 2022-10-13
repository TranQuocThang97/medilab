<!-- BEGIN: statistic -->
<div id="event_statistic">
    <h1>{data.page_title}</h1>    
    {data.content}    
</div>
<!-- END: statistic -->

<!-- BEGIN: info -->
<div class="box-info">
    <div class="picture"><img src="{data.picture}" alt="{data.title}" data-zoom="{data.picture_src}" ></div>
    <div class="info">
        <div class="title">{data.title}</div>
        <div class="address">{data.address}</div>
        <div class="datetime">{data.date_begin}</div>
    </div>
    <div class="btn-scan mb-3">
        <button class="qrcode-reader" type="button" data-link="{data.link_checkin}" data-event="{data.event_id}">
            <i class="icon"><img src="{data.icon_qr}"></i>
            {LANG.user.scan}
        </button>
    </div>
</div>
<script type="text/javascript">
$(function(){
    // overriding path of JS script and audio 
    $.qrCodeReader.jsQRpath = "{data.link_jsQR}";
    $.qrCodeReader.beepPath = "{data.link_audio}";

    // bind all elements of a given class
    $(".qrcode-reader").qrCodeReader({        
        audioFeedback: true,
        multiple: true,
        skipDuplicates: false,
        repeatTtimeout: 1500,
        lineColor: '#25AB0C',
    })    
    checkin_guest(".qrcode-reader");
});
</script>
<!-- END: info -->

<!-- BEGIN: general -->
<div class="box-revenue">
    <div class="total">
        <div class="inner">
            <label>{LANG.user.revenue_event}</label>
            <span class="price_format">{data.total_revenue}</span>
        </div>
    </div>
    <div class="parts">
        <div class="inner">
            <div class="item"><label>{LANG.user.revenue_ticket}</label> <span class="price_format">{data.revenue.ticket}</span></div>
            <div class="item"><label>{LANG.user.revenue_product}</label> <span class="price_format">{data.revenue.product}</span></div>
            <div class="item"><label>{LANG.user.revenue_picture}</label> <span class="price_format">{data.revenue.picture}</span></div>
        </div>
    </div>
</div>
<!-- END: general -->

<!-- BEGIN: ticket -->
<div class="detail detail_ticket">
    <h2>{LANG.user.ticket_event}</h2>
    <div class="short">
        <span>{LANG.user.ticket_total} {data.ticket_total}</span>
        <span>{LANG.user.ticket_sold} {data.ticket_sold}</span>
    </div>
    <div class="content">
        <table class="table table-responsive">
            <thead>
                <tr>
                    <th scope="col" width="25%"></th>
                    <!-- BEGIN: col_type_head -->
                    <th scope="col" class="text-center">{col.title}</th>
                    <!-- END: col_type_head -->
                </tr>
            </thead>
            <tbody>
                <!-- BEGIN: row -->
                <tr>
                    <td>{row.name_row}</td>
                    <!-- BEGIN: col_type_body -->
                    <td class="text-center">{col.num}</td>
                    <!-- END: col_type_body -->
                </tr>
                <!-- END: row -->
                <!-- BEGIN: row_empty -->
                <tr><td>{row.title}</td></tr>
                <!-- END: row_empty -->
            </tbody>
        </table>
    </div>
</div>
<!-- END: ticket -->

<!-- BEGIN: picture -->
<div class="detail detail_picture">
    <h2>{LANG.user.picture_event}</h2>
    <div class="content">
        <table class="table table-responsive">
            <thead>
                <tr>
                    <th scope="col" width="25%"></th>
                    <!-- BEGIN: col_type_head -->
                    <th scope="col" class="text-center">{col.title}</th>
                    <!-- END: col_type_head -->
                </tr>
            </thead>
            <tbody>
                <!-- BEGIN: row -->
                <tr>
                    <td>{row.name_row}</td>
                    <!-- BEGIN: col_type_body -->
                    <td class="text-center">{col.num}</td>
                    <!-- END: col_type_body -->
                </tr>
                <!-- END: row -->
                <!-- BEGIN: row_empty -->
                <tr><td>{row.title}</td></tr>
                <!-- END: row_empty -->
            </tbody>
        </table>
    </div>
</div>
<!-- END: picture -->

<!-- BEGIN: chart -->
<div class="detail detail_chart">
    <div class="box_tab">
        <h2>{LANG.user.chart}</h2>
        <ul class="nav nav-pills" id="pills-tab" role="tablist">
            <!-- <li class="nav-item" role="presentation">
                <a href="javascript:void(0)" class="nav-link active" id="pills-total-tab" data-toggle="pill" data-target="#pills-total" data-tab="total" role="tab" aria-controls="pills-total" aria-selected="true" data-chart='{data.arr_date_total}'>{LANG.user.revenue_total}</a>
            </li> -->
            <li class="nav-item" role="presentation">
                <a href="javascript:void(0)" class="nav-link active" id="pills-ticket-tab" data-toggle="pill" data-target="#pills-ticket" data-tab="ticket" role="tab" aria-controls="pills-ticket" aria-selected="false" data-chart='{data.arr_date_ticket}'>{LANG.user.revenue_ticket}</a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="javascript:void(0)" class="nav-link" id="pills-product-tab" data-toggle="pill" data-target="#pills-product" data-tab="product" role="tab" aria-controls="pills-product" aria-selected="false" data-chart='{data.arr_date_product}'>{LANG.user.revenue_product}</a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="javascript:void(0)" class="nav-link" id="pills-picture-tab" data-toggle="pill" data-target="#pills-picture" data-tab="picture" role="tab" aria-controls="pills-picture" aria-selected="false" data-chart='{data.arr_date_picture}'>{LANG.user.revenue_picture}</a>
            </li>
        </ul>
    </div>
    <div class="date_range">
        <div class="date">
            <label>{LANG.user.date_begin}</label>
            <div class="form-group">
                <i class="far fa-calendar-alt"></i>
                <input type="text" name="date_begin" class="datepicker start" value="{data.search_date_begin}" autocomplete="off">
            </div>
        </div>
        <div class="date">
            <label>{LANG.user.date_end}</label>
            <div class="form-group">
                <i class="far fa-calendar-alt"></i>
                <input type="text" name="date_end" class="datepicker end" value="{data.search_date_end}" autocomplete="off">
            </div>
        </div>
        <div class="filter">
            <button type="button" class="btn btn-filter">{LANG.user.show}</button>
        </div>
    </div>
    <div class="tab-content" id="pills-tabContent">
        <div id="chartdiv"></div>        
    </div>
</div>
<script type="text/javascript">
    var title = $(document).find("title").text();
    var url = document.location.href;    
    $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
        // var type = $(e.target).attr('data-tab');
        // const arr = [{name:'type',value:type}];
        // arr.forEach(function(el){
        //     url = replaceUrlParam(url, el['name'], el['value']);
        // })
        // history.pushState(null, title, url);
        // loading("show");
        $("#pills-tabContent").load(window.location.href + " #pills-tabContent> *",function(){
            lineChart("chartdiv", $(".nav-link.active").attr("data-chart"));
            // loading("hide");
        });
    })
    $(document).on("click", ".btn-filter", function(){
        var date_begin = $("[name='date_begin']").val(),
            date_end = $("[name='date_end']").val();
        const arr = [
                        {name:'date_begin',value:date_begin},
                        {name:'date_end',value:date_end},
                    ];
        arr.forEach(function(el){
            url = replaceUrlParam(url, el['name'], el['value']);
        })
        history.pushState(null, title, url);
        loading("show");
        $("#pills-tabContent").load(window.location.href + " #pills-tabContent> *",function(){
            lineChart("chartdiv", $(".nav-link.active").attr("data-chart"));
            loading("hide");
        });
    })
    dateRange(".datepicker.start",".datepicker.end");
    lineChart("chartdiv", $(".nav-link.active").attr("data-chart"));
</script>
<!-- END: chart -->

<!-- BEGIN: percent -->
<div class="detail detail_percent">
    <h2>{LANG.user.num_of_register} {data.total_registed}</h2>
    <div class="box_percent">
        <div class="inner">
            <label>
                {LANG.user.participants_per_total}
                <a href="{data.link_participants}">{LANG.user.list}</a>
            </label>
            <div class="short">
                {LANG.user.num_of_participants}: <b>{data.participants}/{data.total_registed}</b>
            </div>
            <div class="content">
                <div class="row">
                    <div class="col_item">
                        <div class="item">
                            <div class="percent"><span>{data.percent_participants}%</span></div>
                            <div class="title">{data.title_participants}</div>
                        </div>
                    </div>
                    <!-- BEGIN: participants -->
                    <div class="col_item">
                        <div class="item">
                            <div class="percent"><span>{col.percent_participants}%</span></div>
                            <div class="title">{col.title_participants}</div>
                        </div>
                    </div>
                    <!-- END: participants -->
                </div>
            </div>
        </div>
    </div>
    <div class="box_percent">
        <div class="inner">
            <label>
                {LANG.user.supporters_per_participants}
                <a href="{data.link_supporters}">{LANG.user.list}</a>
            </label>
            <div class="short">
                {LANG.user.num_of_supporters}: <b>{data.supporters}/{data.participants}</b>
            </div>
            <div class="content">
                <div class="row">
                    <div class="col_item">
                        <div class="item">
                            <div class="percent"><span>{data.percent_supporters}%</span></div>
                            <div class="title">{data.title_supporters}</div>
                        </div>
                    </div>
                    <!-- BEGIN: supporters -->
                    <div class="col_item">
                        <div class="item">
                            <div class="percent"><span>{col.percent_supporters}%</span></div>
                            <div class="title">{col.title_supporters}</div>
                        </div>
                    </div>
                    <!-- END: supporters -->
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END: percent -->

<!-- BEGIN: table -->
<div id="event_statistic">
    {data.info}
    <div id="table_statistic">
        <div class="box_tab">            
            <ul class="nav nav-pills" id="pills-tab" role="tablist">                
                <li class="nav-item" role="presentation">
                    <a href="javascript:void(0)" class="nav-link {data.active_registed}" id="pills-registed-tab" data-type="registed" data-toggle="pill" data-target="#pills-registed" role="tab" aria-controls="pills-registed" aria-selected="false">{LANG.user.list_registed}</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="javascript:void(0)" class="nav-link {data.active_checkin}" id="pills-checkin-tab" data-type="checkin" data-toggle="pill" data-target="#pills-checkin" role="tab" aria-controls="pills-checkin" aria-selected="false">{LANG.user.list_checkin}</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="javascript:void(0)" class="nav-link {data.active_notcheckin}" id="pills-notcheckin-tab" data-type="notcheckin" data-toggle="pill" data-target="#pills-notcheckin" role="tab" aria-controls="pills-notcheckin" aria-selected="false">{LANG.user.list_notcheckin}</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="javascript:void(0)" class="nav-link {data.active_supporters}" id="pills-supporters-tab" data-type="supporters" data-toggle="pill" data-target="#pills-supporters" role="tab" aria-controls="pills-supporters" aria-selected="false">{LANG.user.list_supporters}</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="javascript:void(0)" class="nav-link {data.active_buyers}" id="pills-buyers-tab" data-type="buyers" data-toggle="pill" data-target="#pills-buyers" role="tab" aria-controls="pills-buyers" aria-selected="false">{LANG.user.list_buyers}</a>
                </li>
            </ul>
        </div>
        <form action="{data.link_action}" method="get" class="box_search_list">
            <div class="row mb-3">                
                <div class="select col-6 col-md-auto">
                    <div class="form-group">
                        <input type="text" name="search_keyword" class="form-control" placeholder="{LANG.user.keyword}" value="{data.search_keyword}">
                        <button type="submit" class="icon"><img src="{data.src}/search.svg" alt="search"></button>
                    </div>
                </div>
                <div class="select col-6 col-md-auto">
                    <div class="form-group">
                        {data.search_team}                        
                    </div>
                </div>
                <div class="select col-6 col-md-auto">
                    <div class="form-group">
                        {data.search_ticket}                        
                    </div>
                </div>
                <div class="select col-6 col-md-auto">
                    <div class="form-group">
                        {data.search_checkin}
                    </div>
                </div>
                <div class="filter">
                    <button type="button" class="btn btn-filter">{LANG.user.show}</button>
                </div>
            </div>
            {data.link_back}
        </form>
        <div class="box_title">
            <label class="table_title">{LANG.user.list_registed}: {data.total_registed}</label>
            <div class="dropdown">
                <button class="btn btn-team dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {LANG.user.config_team}
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item" href="{data.link_createteam}">{LANG.user.create_team}</a>
                    <a class="dropdown-item edit-team" href="{data.link_editteam}">{LANG.user.edit_team_list}</a>
                </div>
            </div>
        </div>
        <div class="box_editteam {data.class}">
            <div class="row">
                <div class="form-group col-3">
                    <select name="team" class="">
                        <!-- BEGIN: team -->
                        <option value="{row.value}">{row.title}</option>
                        <!-- END: team -->
                    </select>
                </div>
                <div class="form-group col">
                    <button class="btn btn-update" data-id="{data.event_id}">{LANG.user.update}</button>
                </div>
            </div>
        </div>
        <div class="content">
            <table class="table {data.class}">
                <thead>
                    <tr>
                        <th class="checkbox" width="4%"><input id="checkall" class="checkbox all" type="checkbox" name="checkall" value="all"><label for="checkall"></label></th>
                        <th scope="col" width="4%">{LANG.user.index}</th>
                        <th scope="col" width="10%">{LANG.user.date_registed}</th>
                        <th scope="col" >{LANG.user.full_name}</th>
                        <th scope="col" width="17%">{LANG.user.email}</th>
                        <th scope="col" width="11%">{LANG.user.phone}</th>
                        <th scope="col" width="12%">{LANG.user.type_ticket}</th>
                        <th scope="col" width="11%">{LANG.user.team}</th>
                        <th scope="col" width="10%">{LANG.user.send_ticket}</th>
                        <th scope="col" width="10%">{LANG.user.date_checkin}</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- BEGIN: row -->
                    <tr>
                        <td class="cot checkbox" align="">
                            <input class="checkbox" type="checkbox" id="cb_{row.detail_id}" value="{row.detail_id}" name="selected_id[]"><label for="cb_{row.detail_id}"></label>
                        </td>
                        <td class="text-center">{row.index}</td>
                        <td>{row.date_create}</td>
                        <td>{row.full_name}</td>
                        <td>{row.email}</td>
                        <td>{row.phone}</td>
                        <td>{row.title}</td>
                        <td>{row.team}</td>
                        <td>
                            <button type="button" class="btn {row.btn_class}" {row.is_send}>{row.send}</button>
                        </td>
                        <td>{row.date_checkin}</td>
                    </tr>
                    <!-- END: row -->
                    <!-- BEGIN: row_empty -->
                    <tr><td colspan="9">{row.title}</td></tr>
                    <!-- END: row_empty -->
                </tbody>
            </table>            
        </div>
        {data.nav}
        <div class="bottom_row">
            <div class="row align-items-center justify-content-end">
                <div class="item col-auto p-3">
                    <button class="btn btn-excel" data-toggle="modal" data-target="#box_import">Import</button>
                </div>
                <div class="item col-auto p-3">
                    <button class="btn btn-excel btn-export" data-wre="{data.wre}" data-title="{data.excel_title}">Export</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade show" id="box_import" role="dialog" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Nhập danh sách</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" enctype="multipart/form-data" name="importExcel" id="importExcel" role="form" onSubmit="return false">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="fw400">Chọn file: </label>
                        <div class="js">
                            <span class="filename"></span>
                            <input name="file_attach" id="file_attach" type="file" class="form-control inputfile" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                            <label for="file_attach"><i class="fas fa-upload"></i> <span>Chọn file tải lên</span></label>
                        </div>
                    </div>
                    <div class="form-group">
                       <!--  <label class="fw400">Chọn dạng file sẽ nhập: </label>
                        <input type="radio" class="radio" value="1" name="fileType" id="fileType1" checked=""> -->
                        <label for="fileType1" class="inline"><span>Nhập từ file .xls (Excel)</span></label>
                        <a href="{data.link_import_excel}" target+="_blank" download style="color: #007bff;">Tải về file excel mẫu</a>
                        <div class="note" style="margin-top: 10px;">
                            <b>Lưu ý:</b>
                            <div> - Dung lượng tối đa là 10Mb hoặc 300 dòng dữ liệu</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{LANG.user.cancel}</button>
                    <button type="submit" class="btn btn-success btn-import" data-id="{data.event_id}">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">do_checkall();</script>
<!-- END: table -->

<!-- BEGIN: team -->
<div id="event_statistic">
    {data.info}    
</div>
<div id="event_team">
    <h1>{LANG.user.step1}</h1>
    <form id="form_create_team" method="post" class="box_content">
        <div class="row">
            <input type="hidden" name="event_id" value="{data.item_id}">
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <input type="text" name="title" class="form-control" placeholder="{LANG.user.team_name}" required>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <input type="text" name="quantity" class="form-control" placeholder="{LANG.user.team_quantity}" required>
                </div>
            </div>
            <div class="col-12">
                <button class="btn create_team ml-auto" type="submit">
                    {LANG.user.team_create}
                    <i class="icon"><img src="{data.icon_create}"></i>
                </button>
            </div>
        </div>
    </form>
    <div id="box_created">
        <h1>{data.title_list}</h1>
        <!-- BEGIN: row -->
        <form id="form_edit_team{row.index}" class="item mb-5" method="post">
            <div class="row">
                <input type="hidden" name="event_id" value="{row.item_id}">
                <input type="hidden" name="key" value="{row.key}">
                <div class="col-12 col-md-6">
                    <div class="form-group mb-1">
                        <input type="text" name="title" class="form-control" placeholder="{LANG.user.team_name}" value="{row.title}">
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group mb-1">
                        <input type="text" name="quantity" class="form-control" placeholder="{LANG.user.team_quantity}" value="{row.quantity}">
                    </div>
                </div>
                <div class="col-12">
                    <div class="row">
                        <label class="btn edit_team ml-auto">
                            {LANG.user.edit_team}
                            <i class="fas fa-money-check-edit"></i>
                            <input type="submit" name="submit" value="edit" class="d-none">
                        </label>
                        <label class="btn edit_team ml-3">
                            {LANG.user.remove_team}
                            <i class="fas fa-trash-alt"></i>
                            <input type="submit" name="submit" value="remove" class="d-none">
                        </label>
                    </div>
                </div>
            </div>
        </form>
        <!-- END: row -->
    </div>
</div>
<div id="bottom_team">
    <div class="row mx-0">
        <a href="{data.link_cancel}"><button class="btn btn-cancel">{LANG.user.cancel}</button></a>
        <a href="{data.link_save}"><button class="btn btn-next">{LANG.user.save_next}</button></a>
    </div>
</div>
<script type="text/javascript">
    imsUser.create_team("form_create_team","box_created");
    $("#box_created .item").each(function(i, e){        
        imsUser.edit_team($(e).attr("id"),"box_created");
    })
</script>
<!-- END: team -->