SharedComment = {
	load_comment:function(form_id){
		$(document).on('click', ".btn_loadmore", function (e) {
			element = $(this);
			parent = $(this).parent();
			var type_id	  = element.data('type_id');
            var parent_id = element.data('parent_id');
	    	var start     = element.data('start');
	    	var type      = element.data('type');
			element.html('<img src="'+ DIR_IMAGE +'ajax-loader.gif" />');
	    	if(!start) { return false; }
			$.ajax({
	            type: "POST",
	            url: ROOT+"ajax.php",
	            data: { "m" : "shared_comment", "f" : "load_comment", "lang_cur" : lang, "type_id" : type_id,"parent_id" : parent_id, 'start' : start, 'type' : type }
	        }).done(function( string ) {
				loading('hide');
	            var data = JSON.parse(string);
				parent.before(data.html);
				if(data.start == data.max || data.start > data.max){
					element.text('');
					element.removeAttr('data-start');
					element.removeAttr('data-max');
					element.removeAttr('data-type_id');
					element.removeAttr('data-parent_id');
					element.removeAttr('data-type');
					element.addClass('none');
					element.removeClass('btn_loadmore');
				} else{
					element.data("start", data.start);
				}
				element.html(lang_js['seemorecomments']);
				parent.find('.count_comment span').text(data.start);
				ellipsestextFunc(1);
		    });
	    });
	},
	post_comment:function(form_id) {
		$("#"+form_id).validate({
			submitHandler: function(e) {
				parent = $("#"+form_id).parent();
				var form_mess = $('#'+form_id).find('.form_mess');
				form_mess.stop(true,true).slideUp(200).html('');
				formData 	  = new FormData($("#"+form_id)[0]);
                var type      = $("#"+form_id + " button[type='submit'] ").data('type');
                var type_id   = $("#"+form_id + " button[type='submit'] ").data('type_id');
                var parent_id = $("#"+form_id + " button[type='submit'] ").data('parent_id');
                if ( !type ) { return false; }
                if ( !type_id ) { return false; }				

                formData.append("type", type);
                formData.append("type_id", type_id);
                formData.append("parent_id", parent_id);

                if ($("#"+form_id + " .file_show .item").length>0) {
	                finalFiles.forEach(function(file) {
					    formData.append('files[]', file);
					});
                }
                if ($("#"+form_id + " input[name='rate']").length>0 &&
                	$("#"+form_id + " input[name='rate']").val()=='') {
                	alert('Vui lòng chọn mức đánh giá (sao) cho sản phẩm!');
                    return false;
                }
                formData.append("f", "post_comment");
                formData.append("m", "shared_comment");
                formData.append("lang_cur", lang);
                loading('show');
				$.ajax({
                    type: 'POST',
                    url: ROOT+"ajax.php",
                    data: formData,
                    contentType: false,
                    cache: false,
                    processData:false,
				}).done(function( string ) {
					loading('hide');
					console.log(string);
					var data = JSON.parse(string);
					console.log(data);
					$('html, body').stop().animate({
				        scrollTop: $("#"+form_id).offset().top - 10
				    }, 500);
					if(data.ok == 1) {
    					finalFiles = [];
    					$(".file_show").html('');
						$('#'+form_id)[0].reset();
						if (parent_id>0) {
							// parent.prepend(data.html);
							// $('.comment'+parent_id+' .reply .num').text(data.num_comment_parent);
							// $('.comment'+parent_id+' .count_comment .total').text(data.num_comment_parent);
						}else{
							// $('.list_comment').prepend(data.html);
						}
						ellipsestextFunc(1);
						$('#file_show').html("");
						form_mess.html(imsTemp.html_alert(data.mess,'success')).stop(true,true).slideDown(200);
					} else {
						form_mess.html(imsTemp.html_alert(data.mess,'error')).stop(true,true).slideDown(200);
					}
				});
				return false;
			},
			rules: {
				txtaComment: {
					required: true
				},
				txtName: {
					required: true,
				},
				txtEmail: {
					required: true,
					email: true,
				},
				rate: {
					required: true,
				},
				captcha: {
					required: true,
				}
			},
			messages: {
				txtaComment: '',
				txtName: lang_js['err_valid_input'],
				txtEmail: {
					required: lang_js['err_valid_input'],
					email: lang_js['err_email_input']
				},
				captcha: ''
			}			
		});
	},
	postFavorite:function() {
    	$(document).on('click', ".comment-bottom .like", function (e) {
            var element = $(this);
            var like = element.data('like');
            var type = element.data('type');
            if ( !like ) { return false; }
            if ( !type ) { return false; }
            if (element.find('i').hasClass('ficon-thumbs-up')) {
            	element.find('i').removeClass('ficon-thumbs-up')
            	element.find('i').addClass('ficon-thumbs-up-alt')
            }
			element.data("type", "");
			$.ajax({
				type: "POST",
				url: ROOT+"ajax.php",
				data: { "m" : "shared_comment", "f" : "postFavorite", "lang_cur" : lang, "like" : like, "type" : type}
			}).done(function( string ) {
				var data = JSON.parse(string);
				element.data("type", type);
				if(data.ok == 1) {
					element.find('.num').text(data.num_like);
				} else if(data.ok == 2) {
					element.find('.num').text(data.num_like);
					element.find('i').addClass('ficon-thumbs-up');
            		element.find('i').removeClass('ficon-thumbs-up-alt');
				}  else {
					element.find('i').addClass('ficon-thumbs-up');
            		element.find('i').removeClass('ficon-thumbs-up-alt');
					jAlert(data.mess, lang_js['aleft_title'],' ', 'error', 1500);
				}
			});
			return false;
		});
	},
}
$(document).on('click','.comment-bottom .reply' ,function(e){
	var element = $(this).parent().next();
	if(element.hasClass('show')){
        element.removeClass('show');
    } else {
   		element.addClass('show');
    }
});
var showChar = 250;
var ellipsestext = "...";
var moretext = lang_js['seemore'];
var lesstext = lang_js['zoomout'];
function ellipsestextFunc(load = 0){
	$('.comment-content').each(function() {
	    var content = $(this).html();
	    if (load == 1) {
	        if(content.indexOf('morelink') == -1){
	        	if((content.match(/<br>/g) || []).length > 5){
			    	showChar = 300;
				}
			    if((content.match(/<br>/g) || []).length > 10 && content.length < 250){
			    	showChar = 150;
			    }
			    if(content.length > showChar || (content.match(/<br>/g) || []).length > 5) {
			        var c = content.substr(0, showChar);
			        var h = content.substr(showChar- 0, content.length - showChar);
			      	if((c.match(/<br>/g) || []).length > 5)
					{
					   var indexOf_C = nth_occurrence(c,'<br>', 5);
					   var c_more = c.substr(indexOf_C);
					   h = c_more + h;
					   c = c.substr(0, indexOf_C);
					}           
			        var html = c + '<span class="moreellipses">' + ellipsestext+ '&nbsp;</span><span class="morecontent"><span>' + h + '</span>&nbsp;&nbsp;<a href="" class="morelink">' + moretext + '</a></span>';
			        $(this).html(html);
			    }
	        }
	    }else{
		    if((content.match(/<br>/g) || []).length > 5){
		    	showChar = 300;
			}
		    if((content.match(/<br>/g) || []).length > 10 && content.length < 250){
		    	showChar = 150;
		    }
		    if(content.length > showChar || (content.match(/<br>/g) || []).length > 5) {
		        var c = content.substr(0, showChar);
		        var h = content.substr(showChar- 0, content.length - showChar);
		      	if((c.match(/<br>/g) || []).length > 5)
				{
				   var indexOf_C = nth_occurrence(c,'<br>', 5);
				   var c_more = c.substr(indexOf_C);
				   h = c_more + h;
				   c = c.substr(0, indexOf_C);
				}           
		        var html = c + '<span class="moreellipses">' + ellipsestext+ '&nbsp;</span><span class="morecontent"><span>' + h + '</span>&nbsp;&nbsp;<a href="" class="morelink">' + moretext + '</a></span>';
		        $(this).html(html);
		    }
	    }
	});
};
ellipsestextFunc();
$(document).on('click', ".morelink", function (e) {
	e.preventDefault();
    if($(this).hasClass("less")) {
    	console.log('VAO');
        $(this).removeClass("less");
        $(this).html(moretext);
    } else {
    	console.log('RA');
        $(this).addClass("less");
        $(this).html(lesstext);
    }
    console.log('CUOI');
    $(this).parent().prev().toggle();
    $(this).prev().toggle();
    return false;
});
// from http://stackoverflow.com/a/32490603
function getOrientation(file, callback) {
  var reader = new FileReader();

  reader.onload = function(event) {
    var view = new DataView(event.target.result);

    if (view.getUint16(0, false) != 0xFFD8) return callback(-2);

    var length = view.byteLength,
        offset = 2;

    while (offset < length) {
      var marker = view.getUint16(offset, false);
      offset += 2;

      if (marker == 0xFFE1) {
        if (view.getUint32(offset += 2, false) != 0x45786966) {
          return callback(-1);
        }
        var little = view.getUint16(offset += 6, false) == 0x4949;
        offset += view.getUint32(offset + 4, little);
        var tags = view.getUint16(offset, little);
        offset += 2;

        for (var i = 0; i < tags; i++)
          if (view.getUint16(offset + (i * 12), little) == 0x0112)
            return callback(view.getUint16(offset + (i * 12) + 8, little));
      }
      else if ((marker & 0xFF00) != 0xFF00) break;
      else offset += view.getUint16(offset, false);
    }
    return callback(-1);
  };

  reader.readAsArrayBuffer(file.slice(0, 64 * 1024));
};
function getMobileOperatingSystem() {
  var userAgent = navigator.userAgent || navigator.vendor || window.opera;

      // Windows Phone must come first because its UA also contains "Android"
    if (/windows phone/i.test(userAgent)) {
        return "Windows Phone";
    }

    if (/android/i.test(userAgent)) {
        return "Android";
    }

    // iOS detection from: http://stackoverflow.com/a/9039885/177710
    if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {
        return "iOS";
    }

    return "unknown";
}



var finalFiles = [];
$(document).on('change', '.file_input', function() {
	form_select = $(this).data('form');
	var files = this.files;
	if (parseInt($(this).get(0).files.length)>3){
        jAlert(lang_js['max_num_file'] + '3', lang_js['aleft_title'],' ', 'error');
        $("#"+form_select + " .file_input").val('');
        $("#"+form_select + " .file_show").html('');
        return false;
    }
	if($("#"+form_select + " .file_show .item").length >= 3 ){
        jAlert(lang_js['max_num_file'] + '3', lang_js['aleft_title'],' ', 'error');
        return false;
    }
    $(".file_show").html('');
    $("#"+form_select + " .file_show").html('');
    finalFiles = [];
  	$(files).each(function(index, file) {
  		finalFiles[index] = file;
   		$("#"+form_select + " .file_show").append(''+
   			'<div class="item">'+
   				'<a class="btn-remove" href="javascript:;" data-id="'+index+'" data-form="'+form_select+'"><i class="ficon-cancel"></i></a>'+
   				'<img id="pic_pre" src="'+URL.createObjectURL(file)+'">'+
   			'</div>');
    	$("#"+form_select + " .file_show").css('opacity', '1');
	 	getOrientation(file, function(orientation) {
	 		var os = getMobileOperatingSystem();
		   	if (orientation == 6 && os != 'iOS') {
    			$("#"+form_select + " .box_picture").addClass("horizontal_box");
    			$("#"+form_select + " .file_show img").addClass("horizontal");
		   	}else{
    			$("#"+form_select + " .box_picture").removeClass("horizontal_box");
		   	}
		});
	});
    $("#"+form_select + " .file_input").val('');
});
$(document).on('click', 'a.btn-remove', function() {
    var index = $(this).data('id');
	var form_select = $(this).data('form');
    $(this).parent().remove();
    delete finalFiles[index];
});