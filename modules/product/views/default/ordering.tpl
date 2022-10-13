<!-- BEGIN: main -->
    {data.content}
<!-- END: main --> 

<!-- BEGIN: ordering_address_login -->
    <div class="ordering_address_margin">
        <div class="bs-wizard bg-text-color">    
            <div class="bs-wizard-step">                
                <span class="bs-wizard-dot bg-color active"><i class="fas fa-map-marker-alt"></i></span>
                <div class="text-center bs-wizard-stepnum">
                    <span class="hidden-xs">{LANG.product.shipping_address}</span>
                </div>
            </div>
            <div class="progress"><div class="progress-bar"></div></div>
            <div class="bs-wizard-step">                
                <span class="bs-wizard-dot"><i class="far fa-dollar-sign"></i></span>
                <div class="text-center bs-wizard-stepnum">
                    <span class="hidden-xs">{LANG.product.payment_orderbuy}</span>
                </div>
            </div>
            <div class="progress progress_1"><div class="progress-bar"></div></div>
            <span class="bs-wizard-step disabled bs-wizard-last">        
                <span class="bs-wizard-dot"><i class="fas fa-check"></i></span>
                <div class="bs-wizard-stepnum">
                    <span class="hidden-xs">{LANG.product.complete}</span>
                </div>
            </span>
        </div>
        <div class="ordering_address is-login">
            <div class="title_address">{LANG.product.order_address}
                <span class="title_address_note">{LANG.product.order_address_note}</span>
            </div>    
            <div id="form_ordering_address">    
                <!-- BEGIN: row -->
                <form name="form_ordering_address{row.id}" method="post" action="{data.link_action}" class="col-lg-6 col-md-6 col-sm-6 col-12">
                <div class="item" id="item-{row.id}">
                    <div class="panel panel-default address-item {row.default}" style="color: {CONF.bgheader}">
                        <div class="panel-body">
                            <p class="name">{row.full_name}</p>
                            <p class="address" title="{row.address_full}"><ins>{LANG.product.address}</ins>: {row.address_full}</p>
                            <p class="phone"><ins>{LANG.product.phone}</ins>: {row.phone}</p>
                            <p class="email"><ins>{LANG.product.email}</ins>: {row.email}</p>
                            <p class="action">
                                <input name="address" type="hidden" value="{row.id}" />
                                <input type="hidden" name="do_submit" value="1" />
                                <button type="submit" data-id="{row.id}" class="btn saving-address" style="color: {row.color}; background: {row.bg}">
                                    {LANG.product.confirm_address}
                                </button>
                                <button type="button" class="btn edit-address" data-id="{row.id}">{LANG.product.edit}</button>
                                <!-- BEGIN: remove -->
                                <button type="button" class="btn delete-address" data-id="{row.id}">{LANG.product.delete}</button>
                                <!-- END: remove -->
                            </p>                    
                            <!-- BEGIN: default -->
                            <span class="default">{LANG.product.default}</span>
                            <!-- END: default -->
                        </div>
                    </div>
                </div>
                </form>
                <!-- END: row -->
                <p class="other_address col-12">
                    {LANG.product.other_address}
                    <a href="javascript:void(0)" id="addNewAddress">
                        {LANG.product.add_other_address}
                    </a>
                </p>
            
            </div>
            <div class="panel panel-default address-form">
                
            </div>
        </div>
    </div>
<!-- END: ordering_address_login --> 

 



<!-- BEGIN: ordering_method_address -->
    <div class="ordering_address_right frame">
        <div class="ordering_address_l">   
            <h3>{LANG.product.ordering_address} <a href="{data.link_address_edit}" class="btn btn-default btn-custom1 {data.edit}">{LANG.product.edit}</a></h3>   
            <div class="row_c">
                <label class="content_name">{data.o_full_name}</label>            
            </div>
            <div class="row_c">
                <label class="content">{data.o_email}</label>
            </div>
            <div class="row_c">
                <label class="title">{LANG.product.phone} :</label>
                <label class="content">{data.o_phone}</label>
            </div>
            <div class="row_c">
                <label class="title">{LANG.product.address} :</label>
                <span class="content">{data.o_address}</span>
            </div>
        </div>
        <div class="ordering_address_r">
            <h3>{LANG.product.delivery_address} <a href="{data.link_address_edit}" class="btn btn-default btn-custom1">{LANG.product.edit}</a></h3>
            <div class="row_c">
                <label class="content_name">{data.d_full_name}</label>
            </div>
            <div class="row_c">
                <label class="content">{data.d_email}</label>
            </div>
            <div class="row_c">
                <label class="title">{LANG.product.phone} :</label>
                <label class="content">{data.d_phone}</label>
            </div>
            <div class="row_c">
                <label class="title">{LANG.product.address} :</label>
                <span class="content">{data.d_address}</span>
            </div>  
        </div>
    </div>
<!-- END: ordering_method_address --> 



 

 

<!-- BEGIN: table_promotion -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped table_row">
            <thead>
                <tr >
                    <th class="header">{LANG.global.id}</th>
                    <th class="header" width="20%">{LANG.global.percent}</th>
                    <th class="header" width="25%">{LANG.global.date_end}</th>
                </tr>
            </thead>
            <tbody>
                <!-- BEGIN: row_item -->
                <tr>
                    <td class="cot" align="center">{row.promotion_id}</td>
                    <td class="cot" align="center">{row.percent}%</td>
                    <td class="cot" align="center">{row.date_end}</td>
                </tr>
                <!-- END: row_item --> 
                <!-- BEGIN: row_empty -->
                <tr class="warning">
                    <td align="center" colspan="5">{row.mess}</td>
                </tr>
                <!-- END: row_empty --> 
            </tbody>
        </table>
    </div>
<!-- END: table_promotion --> 

 

