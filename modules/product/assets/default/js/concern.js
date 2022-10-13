imsConcern = {
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
}