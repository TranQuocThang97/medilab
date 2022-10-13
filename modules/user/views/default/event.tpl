<!-- BEGIN: event -->
<div id="event_manager">
    <div class="box_header">
        <h1>{LANG.user.event_manager}</h1>
        <a href="{data.link_package}"><button class="btn btn-orange"><i class="far fa-plus pr-2"></i> {LANG.user.buy_package}</button></a>
        <a href="{data.link_create}" class="ml-3"><button class="btn btn-green"><i class="far fa-plus pr-2"></i> {LANG.user.create_event}</button></a>
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
                    <th scope="col" align="center" width="15%">{LANG.user.list}</th>
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
                        <div><a href="{row.link_list}">{LANG.user.list}</a></div>                        
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