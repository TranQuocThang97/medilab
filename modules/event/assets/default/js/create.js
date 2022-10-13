imsCreate = {
	submit_step: function(form_id, step, storedFiles){
		$("#"+form_id).validate({
			submitHandler: function() {
				// var fData = $("#"+form_id).serializeArray();
				tinyMCE.triggerSave();
				var link = $('#'+form_id).attr('action');
				var formData = new FormData($("#"+form_id)[0]);
				formData.append("m", "global");
				formData.append("f", step);
				formData.append("lang_cur", lang);				
				// for(var pair of formData.entries()) {
				// 	console.log(pair[0]+ ', '+ pair[1]); 
				// }
				if(storedFiles){					
					for(var i=0, len=storedFiles.length; i<len; i++) {
						formData.append('arr_picture[]', storedFiles[i]);
					}
				}
				$.ajax({
					type: 'POST',
					url: ROOT+"ajax.php",
					data: formData,
					contentType: false,
					cache: false,
					processData:false,
					// type: "POST",
					// url: ROOT+"ajax.php",
					// data: { "m" : "product", "f" : "submit_step", "data" : fData, 'lang_cur':lang}
				}).done(function(string) {					
					var data = JSON.parse(string);
					if(data.ok == 1) {
						go_link(link);
					}else if(data.ok == 2){
						$('#step3').addClass('hide');
						loading('show');
						setTimeout(function(){ 
							$('#ims-content .col_right .view_card').addClass('show');
							$('#ims-content .col_right .view_card .content').html(data.html);
							loading('hide');
						}, 300);
					}else {
						Swal.fire({
							icon: 'error',
							title: lang_js['aleft_title'],
							text: data.mess,
						});
					}
				});
				return false;
			},
			rules: {
				title: {required: true},
				organizer: {required: true},
				organizer_phone: {required: true},
			},
			messages: {
				title: lang_js['err_valid_input'],
				organizer: lang_js['err_valid_input'],
				organizer_phone: lang_js['err_valid_input'],
			}
		});
	},
	createPromo: function(form_id){
		$("#"+form_id).validate({
			submitHandler: function() {
				var fData = $("#"+form_id).serializeArray();
				$.ajax({
					type: "POST",
					url: ROOT+"ajax.php",
					data: { "m" : "global", "f" : "createPromo", "data" : fData, 'lang_cur':lang}
				}).done(function(string) {					
					var data = JSON.parse(string);
					if(data.ok == 1) {
						$('#step4 .note').addClass('has').html(data.num);
						$('#step4').removeClass('hide');
						$('#promotion').html('');
					}else {
						Swal.fire({
							icon: 'error',
							title: lang_js['aleft_title'],
							text: data.mess,
						});
					}
				});
				return false;
			},
			rules: {
				title: {required: true},
			},
			messages: {
				title: lang_js['err_valid_input'],
			}
		});
	},
	updateStatus: function(form_id){
		$("#"+form_id).validate({
			submitHandler: function() {
				var fData = $("#"+form_id).serializeArray();
				loading('show');
				$.ajax({
					type: "POST",
					url: ROOT+"ajax.php",
					data: { "m" : "global", "f" : "updateStatus", "data" : fData, 'lang_cur':lang}
				}).done(function(string) {
					var data = JSON.parse(string);
					loading('hide');
					if(data.ok == 1) {
						go_link(data.link);
					}else {
						Swal.fire({
							icon: 'error',
							title: lang_js['aleft_title'],
							text: data.mess,
						});
					}
				});
				return false;
			},
			rules: {
				title: {required: true},
			},
			messages: {
				title: lang_js['err_valid_input'],
			}
		});
	},
}
$(document).on('click', '#addEvent .continue', function(event) {
	var link = $('#addEvent').attr('action');
	var id = '';
	if($('#addEvent input[name="id_edit"]').length){
		id = $('#addEvent input[name="id_edit"]').val();
	}

	// loading('show');
	$.ajax({
		type: "POST",
		url: ROOT+"ajax.php",
		data: { "m" : "global", "f" : "addEvent", "id_edit": id, "link": link, 'lang_cur':lang}
	}).done(function(string) {
		var data = JSON.parse(string);
		loading('hide');
		if(data.ok == 1) {
			go_link(data.link);
		}else {
			Swal.fire({
				icon: 'error',
				title: lang_js['aleft_title'],
				text: data.mess,
			});
		}
	});
});
$(function() {
	var selDiv = "";
	var storedFiles = [];
	$(document).ready(function() {
		selDiv = $(".gallery-input.gallery-default"); 
		$("#gallery-photo-add").on("change", handleFileSelect);
		$("body").on("click", ".selFile", removeFile);
	});
	function handleFileSelect(e) {
		var files = e.target.files;
		var filesArr = Array.prototype.slice.call(files);
		filesArr.forEach(function(f) {
			if(!f.type.match("image.*")) {
				return;
			}
			storedFiles.push(f);
			var reader = new FileReader();
			reader.onload = function (e) {
				var html = "<div class='item-image'><div class='img'><img src=\"" + e.target.result + "\" data-file='"+f.name+"' class='' title='Click to remove'></div><span class='selFile'><input multiple='multiple' name='arr_picture[]' value='"+ f.name +"' class='d-none'><i class='fa fa-times'></i></span></div>";
				selDiv.append(html);
			}
			reader.readAsDataURL(f); 
		});
	}
	function removeFile(e) {
		var file = $(this).data("file");
		for(var i=0;i<storedFiles.length;i++) {
			if(storedFiles[i].name === file) {
				storedFiles.splice(i,1);
				break;
			}
		}
		$(this).parent().remove();
	}

	var imagesPreviewOne = function(input,placeToInsertImagePreview){
		if (input.files) {
			$(placeToInsertImagePreview).html("");
			var filesAmount = input.files.length;
			for (i = 0; i < filesAmount; i++) {
				var reader = new FileReader();
				reader.onload = function(event) {
					$($.parseHTML('<img>')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);
				}
				reader.readAsDataURL(input.files[i]);
			}
		}
	};
	$('#photo-add').on('change', function() {
		imagesPreviewOne(this, 'div.photo-input');
	});
	imsCreate.submit_step('step1','create_step1', storedFiles);
});
imsCreate.submit_step('step2','create_step2');
imsCreate.submit_step('step3','create_step3');
imsCreate.updateStatus('create_promo');

// var twelveHour = $('.onlytime').wickedpicker();
$('.location .select_location').on('click', 'li', function(event) {
	var id = $(this).attr('data-id');
	$('.location .select_location li').removeClass('active');
	$(this).addClass('active');
	$('.location .tab_content .tab').addClass('hide');
	$('.location .tab_content .tab#'+id).removeClass('hide');

	if(id == 'tab_event_online'){
		$('input[name="link_event"]').prop('disabled',false);
		$('.tab_location input,.tab_location select,.tab_location textarea').prop('disabled',true);
	}else{
		$('input[name="link_event"]').prop('disabled',true);            
		$('.tab_location input,.tab_location select,.tab_location textarea').prop('disabled',false);
	}
});
$('.datetime .select_datetime').on('click', 'li', function(event) {
	var repeat = $(this).attr('data-repeat');
	$('.datetime .select_datetime li').removeClass('active');
	$(this).addClass('active');
	if(repeat == 'y'){
		$('.tab_event_repeat').removeClass('hide');
		$('#frequency').prop('disabled',false);
		$('.once').prop('disabled',true);
	}else{
		$('.tab_event_repeat').addClass('hide');
		$('#frequency').prop('disabled',true);
		$('.once').prop('disabled',false);
	}
});
function previewPicture() {
	var chooseFile = $("#choose-file");
	$(document).on("change", "#choose-file", function(){
		readURL(this, "#img-preview");
	});
}
function readURL(input, previewId) {
	if (input.files && input.files[0]) {
		var reader = new FileReader();
		reader.onload = function(e) {
			$(previewId + ' img').remove();
			$(previewId).prepend('<img src="' + e.target.result + '" />');
			$(previewId).hide();
			$(previewId).fadeIn(650);
		}
		reader.readAsDataURL(input.files[0]);
	}
}
previewPicture();
$('#tag_list').on('click', '.remove', function () {
	$(this).parent().remove();
});
$('#tag_list-btn').on('click', function () {
	input_arr_list_text('tag_list');
});
$('#tag_list_input input[type="text"]').keypress(function (e) {
	if (e.which == 13 || e.which == 0 || e.which == 44) {
		input_arr_list_text('tag_list');
		return false;
	}
});
function input_arr_list_text(input_name) {
	var text_search = $('#' + input_name);
	var input_text = $('#' + input_name + '_input input[type="text"]');
	var input_type = (input_text.data('type')) ? input_text.data('type') : 'text';
	if (!text_search.children().children('input[type="text"][value="' + input_text.val() + '"]').length && input_text.val()) {
		if (input_type == 'email' && !isEmail(input_text.val())) {
			alert('Thông tin nhập vào phải là 1 email');
			return false;
		}
		var html_div = '<div class="list_text-item"><span>' + input_text.val() + '</span><input name="' + input_name + '[]" type="hidden" value="' + input_text.val() + '" /><a href="javascript:;" class="remove"><i class="fal fa-times"></i></a></div>';
		if (!text_search.children().length) {
            //alert('a');
            text_search.html(html_div);
            input_text.val('');
        } else {
            //alert('b');
            var tmp = input_text.val();
            var num = text_search.children().length;
            //var i = 0;
            text_search.children().each(function (index, element) {
                //i++;
                var e = $(this).children('input[type="hidden"]');
                if (tmp !== e.val()) {
                	$(".list_text-item:last-child").after(html_div);
                	input_text.val('');
                	return false;
                }
            });
        }
    }
}
$('.tab_create .tab_type').on('click', 'li', function(event) {
	var tab = $(this).attr('data-type');
	$('.tab_create .tab_type li').removeClass('active');
	$(this).addClass('active');
	$('.tab_info').addClass('hide');
	$('#'+tab).removeClass('hide');
    // $('.tab_info').find('input, textarea').prop('disabled',true);
    // $('#'+tab).find('input, textarea').prop('disabled',false);
});
$(document).on('click', '.edit .fa-ellipsis-v', function(event) {
	var type = $(this).parent().attr('data-type');    
	$(this).next().toggleClass('show');
});
$('input[name="day_expected"]').on('change', function(event) {
	var val = $(this).attr('id');
	if(val == 'booking'){
		$('.time_start .form-group input').prop('disabled',false);
	}else{
		$('.time_start .form-group input').prop('disabled',true);            
	}
});

$(document).on('change', 'input[name="time_code"]', function(event) {
	var leght = $('input[name="time_code"]:checked').length;
	if(leght > 0){
		$('.tab_promo .tab_pro:not(.hide) .time_all').prop('disabled',false);
	}else{
		$('.tab_promo .tab_pro:not(.hide) .time_all').prop('disabled',true);		
	}
});

$(document).on('change', 'input[name="type_code"]', function(event) {
	var type = $(this).attr('id');    
	$('.tab_promo .tab_pro').addClass('hide');
	$('.tab_promo .tab_pro#'+type).removeClass('hide');

	$('.tab_promo .tab_pro input,.tab_promo .tab_pro select,.tab_promo .tab_pro textarea').prop('disabled',true);
	$('.tab_promo .tab_pro input[type="checkbox"]').prop('checked',false);
	// $('.tab_promo .tab_pro input[name="price1"]').prop('disabled',true);
	$('.tab_promo .tab_pro#'+type+' input:not(.time_all),.tab_promo .tab_pro#'+type+' select,.tab_promo .tab_pro#'+type+' textarea').prop('disabled',false);
});
$(document).on('click', '.top_create button', function(event) {	
	loading('show');
	var id = $('#step4 .top_info .title').attr('data-id');
	$.ajax({
		type: "POST",
		url: ROOT+"ajax.php",
		data: { "m" : "global", "f" : "load_formPromo", "id": id, 'lang_cur':lang}
	}).done(function(string) {
		var data = JSON.parse(string);
		loading('hide');
		if(data.ok == 1) {
			$('#step4').addClass('hide');
			$('#promotion').html(data.html);
			$('.tab_promo .tab_pro.hide input,.tab_promo .tab_pro.hide select,.tab_promo .tab_pro.hide textarea').prop('disabled',true);
			imsCreate.createPromo('add_promo');
			$('.time_all').datetimepicker({
				minDate: 0,
			});
		}else {
			// Swal.fire({
			// 	icon: 'error',
			// 	title: lang_js['aleft_title'],
			// 	text: data.mess,
			// });
		}
	});
});

$(document).on('click', '.box_info button', function(event) {	
	// loading('show');
	var id = $('input[name=id_edit]').val();
	$.ajax({
		type: "POST",
		url: ROOT+"ajax.php",
		data: { "m" : "global", "f" : "load_preview", "id": id, 'lang_cur':lang}
	}).done(function(string) {
		var data = JSON.parse(string);
		loading('hide');
		if(data.ok == 1) {
			$('#preview_event').html(data.html);
			$.fancybox.open({
				src: "#preview_event",
				showCloseButton: false
			});
		}else {
			// Swal.fire({
			// 	icon: 'error',
			// 	title: lang_js['aleft_title'],
			// 	text: data.mess,
			// });
		}
	});
});

$(document).on('change', 'input.auto_int ', function(event) {
	var val = $(this).val();
	$(this).next().attr('value',val);
});
$(document).on('click', '#promotion .cancel', function(event) {
	$('#step4').removeClass('hide');
	$('#promotion').html('');
});
$(document).on('change', '.tab_promo .tab_pro:not(.hide) select[name="value_type"]', function(event) {
	_value_type($(this));
});
function _value_type(o) {
	$(this).children('option:checked');
	var cur = o.val();
	if (cur == 1) {
		$(".value_max").removeClass("d-none");
	}else{
		$(".value_max").addClass("d-none");
	}
	$('.tab_promo .tab_pro:not(.hide) .value_type_control').each(function (index, element) {
		var ifor  = $(this).data('for');
		var ifor1 = $(this).data('for1');
		var ifor2 = $(this).data('for2');		
		if (ifor == cur || ifor1 == cur || ifor2 == cur) {			
			// $(this).find('input.auto_int_input').prop('disabled',false);
			$(this).stop(true, false).slideDown(0);
		} else {
			// $(this).find('input.auto_int_input').prop('disabled',true);
			$(this).stop(true, false).slideUp(0);
		}
	});
}