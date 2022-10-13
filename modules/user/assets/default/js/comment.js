alert(fdgd);
imsComment = {
	post_comment:function (form_id) {
		//$('#'+form_id).submit(function(){return false;});
		$('#'+form_id).submit(function(e)
		{
			if($('#'+form_id).data('has_login') != 1) {
				call_popup('#header_signin_form');
				return false;
			}
			var output = $('#'+form_id+' input[name="output"]').val();
			var type = $('#'+form_id+' input[name="type"]').val();
			var type_id = $('#'+form_id+' input[name="type_id"]').val();
			var content = $('#'+form_id+' textarea[name="content"]');
			if(content.val() == '' || content.val() == content.data('textdefault')) {
				alert(lang_js['err_invalid_content']);
				return false;
			}
			
			var form_mess = $('#'+form_id).find('.form_mess');
			form_mess.stop(true,true).slideUp(200).html('');
			var fData = $(this).serializeArray();
			$.ajax({
				type: "POST",
				url: ROOT+"ajax.php",
				data: { "m" : "user", "f" : "post_comment","lang_cur" : lang, "data" : fData  }
			}).done(function( string ) {
				var data = JSON.parse(string);
				if(data.ok == 1) {
					if(data.show == 1) {
						imsComment.list_comment(output, type, type_id, 1);
					} else {
						form_mess.html(imsTemp.html_alert(data.mess,'success')).stop(true,true).slideDown(200);
					}
					$('#'+form_id)[0].reset();
				} else {
					form_mess.html(imsTemp.html_alert(data.mess,'error')).stop(true,true).slideDown(200);
				}
			});
			e.preventDefault(); //STOP default action
			//e.unbind(); //unbind. to stop multiple form submit.
			return false;
		});
	},
	
	list_comment:function (html_id, type, type_id, p) {
		$('#'+html_id).html('<div class="loading_ajax"></div>');
		$.ajax({
			type: "POST",
			url: ROOT+"ajax.php",
			data: { "m" : "user", "f" : "list_comment", "lang_cur" : lang, "html_id" : html_id, "type" : type, "type_id" : type_id, "p" : p}
		}).done(function( html ) {
			$('#'+html_id).html(html);
		});
	},
	
	like:function (o, type, type_id) {
		$.ajax({
			type: "POST",
			url: ROOT+"ajax.php",
			data: { "m" : "user", "f" : "like", "lang_cur" : lang, "type" : type, "type_id" : type_id}
		}).done(function( string ) {
			var data = JSON.parse(string);
			if(data.ok == 1) {
				o.html(data.num);
			}
		});
	},
	
	notlike:function (o, type, type_id) {
		$.ajax({
			type: "POST",
			url: ROOT+"ajax.php",
			data: { "m" : "user", "f" : "notlike", "type" : type, "lang_cur" : lang, "type_id" : type_id}
		}).done(function( string ) {
			var data = JSON.parse(string);
			if(data.ok == 1) {
				o.html(data.num);
			}
		});
	},
	
	show_form_reply:function (html_show, btn_click) {
		$("#"+html_show).stop(true, true).slideDown(200);
		$("#"+btn_click).remove();
	}
}