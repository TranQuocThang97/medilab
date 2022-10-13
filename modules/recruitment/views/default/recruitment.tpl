<!-- BEGIN: main -->
{data.content}
<!-- END: main --> 

<!-- BEGIN: recruitment -->
<div class="box-recruitment">
  {data.box_search}
  <div class="list_item">
    {data.text_search}
    {data.content}
  </div>  
</div>
<!-- END: recruitment -->

<!-- BEGIN: list_item -->
<div class="list_item">  
  <!-- BEGIN: row_item -->
    <!-- BEGIN: col_item -->
    <div class="col_item {col.class}">      
      <h3 class="title col-md-4 col-12 px-md-0">
        <div class="icon"><img src="{col.picture}" alt="{col.title}" title="{col.title}" /></div>
        <a href="{col.link}" title="{col.title}">{col.title}</a>
      </h3>
      <div class="quantity col-md-2 col-6">{LANG.recruitment.quantity}: {col.quantity}</div>
      <div class="province col-md-2 col-6"><i class="fas fa-map-marker-alt"></i> {col.location}</div>
      <div class="date_end col-md-2 col-6">{col.date_end}</div>
      <div class="apply col-md-2 col-6 px-md-0 text-right"><button class="btn btn_apply"><a href="{col.link}" title="{col.title}">{LANG.recruitment.apply}</a></button></div>
    </div>
    <!-- END: col_item -->
  <!-- END: row_item --> 
  <!-- BEGIN: row_empty -->
  <div class="row_empty">{row.mess}</div>
  <!-- END: row_empty --> 
</div>
{data.nav}
<!-- END: list_item --> 

<!-- BEGIN: box_search -->
<form action="{data.link_search}" method="get" class="box_search col-12">
  <div class="search_title col-md-4 col-6">
    <input type="text" name="keyword" value="{data.keyword}" placeholder="{LANG.recruitment.search_text}" >
  </div>
  <div class="search_type col-md-3 col-6">
    <select name="type">
      <option value="">{LANG.recruitment.type}</option>
      <!-- BEGIN: type -->
      <option value="{row.id}" {row.selected}>{row.title}</option>
      <!-- END: type -->
    </select>      
  </div>
  <div class="search_province col-md-3 col-6">
    <select name="province">
      <option value="">{LANG.recruitment.province}</option>
      <!-- BEGIN: province -->
      <option value="{row.code}" {row.selected}>{row.title}</option>
      <!-- END: province -->
    </select>
  </div>
  <div class="search col-md-2 col-6"><button class="btn btn_search" type="submit"><i class="far fa-search"></i> {LANG.recruitment.search}</button></div>
</form>
<!-- END: box_search -->

<!-- BEGIN: item_detail -->
<div id="item_detail">
  <h1 class="title">{data.title} </h1>
  <div class="content">{data.content}</div>
  <div class="footer_info">
    
    <div class="tool-share ml-auto">      
      <ul class="list_none rrssb-buttons col-12 rrssb-1">            
        <li class="btn-facebook col-1" data-initwidth="25" style="width: 25%;">
            <a href="https://www.facebook.com/sharer/sharer.php?u={data.link_share}" class="popup">
                <span class="icon"><i class="fab fa-facebook-f"></i></span>
            </a>
        </li>              
        <li class="btn-twitter col-1" data-initwidth="25" style="width: 25%;">
            <a href="https://twitter.com/intent/tweet?text={data.link_share}" class="popup">
              <span class="icon"><i class="fab fa-twitter"></i></span>
            </a>
        </li>
        <li class="zalo-share-button" data-href="" data-oaid="579745863508352884" data-layout="3" data-color="white" data-customize=false></li>
      </ul>
    </div>
    <div class="col-12 px-0 my-3">
      <div class="fb-comments" data-href="{data.link_share}"  data-width="100%" data-numposts="5"></div>
    </div>
  </div>   
</div>
<script src="https://sp.zalo.me/plugins/sdk.js"></script>
<!-- END: item_detail -->

<button class="btn btn_recruit" data-toggle="modal" data-target="#frmRecruit">{LANG.recruitment.apply}</button>
 <!-- Modal -->
  <div class="modal fade" id="frmRecruit" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <form id="frm_apply" method="post" enctype="multipart/form-data">
        <div class="modal-header bg-primary">
          <h5 class="modal-title">{LANG.recruitment.form_title}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group input-effect">
            <label for="title" class="m-0">{LANG.recruitment.job_title}: {data.ititle}</label>
            <input name="title" type="hidden" maxlength="250" value="{data.ititle}" class="form-control effect-1" readonly/>
        </div>
          <div class="form-group input-effect">
            <input name="full_name" type="text" maxlength="250" value="" class="form-control effect-1" placeholder="{LANG.global.full_name} (*)" required/>
            <span class="focus-border"></span>
          </div>
          <div class="form-group input-effect">
            <input name="email" type="text" maxlength="250" value="" class="form-control effect-1" placeholder="{LANG.global.enter_email} (*)" required/>
            <span class="focus-border"></span>
          </div>
          <div class="form-group input-effect">
            <input name="phone" type="text" maxlength="250" value="" class="form-control effect-1" placeholder="{LANG.global.enter_phone} (*)" required/>
            <span class="focus-border"></span>
          </div>
          <div class="form-group input-effect">
            <input type="file" name="file" value="" class="form-control effect-1" required>
          </div>
        </div>
        <div class="modal-footer">        
          <button type="submit" class="btn btn-primary">{LANG.recruitment.send}</button>
        </div>
        </form>
      </div>
    </div>
  </div>
  <script>imsGlobal.send_apply('frm_apply');</script>