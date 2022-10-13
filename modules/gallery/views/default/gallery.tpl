<!-- BEGIN: main -->
<div id="ims-content">{data.content}</div>
<!-- END: main --> 


<!-- BEGIN: gallery -->
<div id="event_registed">
	<div class="box_header">
		<div class="title">			
			<h1>{LANG.gallery.event_registed}</h1>
		</div>		
	</div>
	<ul class="nav nav-tabs" role="tablist">
		<li class="nav-item">
			<a class="nav-link active" id="all-tab" data-toggle="tab" href="#all" role="tab" aria-controls="all" aria-selected="true">{LANG.gallery.all}</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" id="favorite-tab" data-toggle="tab" href="#favorite" role="tab" aria-controls="favorite" aria-selected="false">{LANG.gallery.favorite}</a>
		</li>
    </ul>
	<div class="list_event">
		<div class="list_item_event">
			<div class="row_item">
				<!-- BEGIN: row -->
				<div class="col_item col-6 col-md-4">
				    <div class="item">
				        <div class="img">
				            <a href="{row.link}" title="{row.title}">
				                <img class="lazyload" src="{row.loading}" data-src="{row.picture}" alt="{row.title}" />
				            </a>
				            <div class="add_favorite {row.added}" data-id={row.item_id}><i class="{row.i_favorite}"></i></div>
				        </div>
				        <div class="info">
				            <div class="info-title"><a href="{row.link}" title="{row.title}">{row.title}</a></div>
				            <div class="group_date_add">
				                <div class="date_begin">{row.date_begin}</div>
				                <div class="address" title="{row.address}">{row.address}</div>
				            </div>
				            <div class="event_owner">{row.event_owner}</div>
				            <div class="num_follow"><img src="{row.rooturl}resources/images/use/user.svg" alt="user">{row.num_follow} {LANG.event.follow}</div>
				        </div>
				    </div>
				</div>
				<!-- END: row -->
			</div>
		</div>
	</div>
	
</div>
<script type="text/javascript">
	imsUser.add_favorite();
</script>
<!-- END: gallery --> 


<!-- BEGIN: find -->
<div id="image_lookup">
	<div class="box-info">
	    <div class="info">
	    	<!-- BEGIN: logo -->
		    <div class="logo">
		        <!-- BEGIN: row -->
		        <div class="item">
		            <span class="img"><img src="{row.picture}" alt="{row.title}"></span>
		        </div>
		        <!-- END: row -->
		    </div>
		    <!-- END: logo -->
	        <div class="title">{data.title}</div>
	        <div class="organizer">{LANG.gallery.organizer}: <span>{data.organizer}</span></div>
	        <div class="address">{data.address}</div>
	        <div class="datetime">{data.date_begin}</div>
	    </div>
	    <div class="box_upload">
	    	<div class="pic">
	    		<input type="file" name="avatar" id="choose-file" accept="image/*" style="display: none;">
	    		<label for="choose-file"><img src="{data.upload_pic}" id="img-preview"></label>
	    	</div>
	    	<div id="box-crop" style="display: none;">
                <div id="img-crop"></div>
                <div class="update-img"><button id="save-crop" class="btn btn-orange">Chọn ảnh</button></div>
            </div>
	    	<div class="text">
		    	<div class="title">{LANG.gallery.upload_picture}</div>
		    	<div class="note">{LANG.gallery.upload_picture_note}</div>		    	
		    	<div class="btn btn-lookup progress" data-id="{data.event_id}">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
                    <button type="button" id="lookup">{LANG.gallery.search}</button>
                </div>
	    	</div>
	    </div>
	</div>
</div>
<div id="box-result">
	<div class="list_image">
	    <div class="row"></div>
	</div>
</div>
<script type="text/javascript">
	var group = [];
        group['crop'] = '#img-crop';
        group['upload'] = '#choose-file';
        group['result'] = '#save-crop';
        group['preview'] = '#img-preview';
    UploadWithCrop(group,235,220);
    $(document).on('click change', '#choose-file', function(){
        var files = $(this).val();
        if(files.length > 0){
            $.fancybox.open({
                src: '#box-crop',
                type: 'inline',
                touch: false,
                clickSlide: false,
                clickOutside: false,
                afterClose: function() {
                    $(group['upload']).val('');
                }
            })
        }
    })
    $(document).on('click', '#save-crop', function(){
        $.fancybox.close();
    })
</script>
<!-- END: find -->

<!-- BEGIN: list_image -->
    <!-- BEGIN: row -->
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
        </div>
    <!-- END: row -->
<!-- END: list_image -->