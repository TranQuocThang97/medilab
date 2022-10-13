imsEvent = {
    load_more : function(reload=0){
        loading('show');
        var num_cur = $('input[name="start"]').val();
        var group_id = $('.box_content_event .list_item_event').data('group');
        var order_by = $('input[name="order_by"]').val(); // Lọc sp theo thứ tự, tiêu đề
        var sort = $('input[name="sort"]').val(); // Lọc sp theo giá, thương hiệu, ...
        var keyword = $('input[name="sort"]').data('keyword'); // Lọc sp theo từ khóa tìm kiếm ...
        var focus = $('input[name="sort"]').data('focus'); // Lọc sp theo is_focus ...
        if(reload == 1){
            num_cur = 0;
        }

        $.ajax({
            type: "POST",
            url: ROOT + "ajax.php",
            data: {"m":"event", "f":"load_events_ajax", 'num_cur':num_cur, 'group_id':group_id, 'order_by':order_by, 'sort':sort, 'keyword':keyword, 'focus':focus}
        }).done(function (string) {
            var data = JSON.parse(string);
            if(reload == 0){
                $('.box_content_event .list_item_event .row_item').append(data.html);
            }else{
                var scroll_to = $('#scroll_to').offset();
                $('html, body').animate({scrollTop: scroll_to.top-$('.sticky-wrapper').height()-51}, 600);
                $('.group_title p span').text(data.total);
                $('.box_content_event .list_item_event .row_item').html(data.html);
            }
            if(data.filter_event != ''){
                $('.filter_event').html(data.filter_event);
            }

            if(data.num > 0){
                $('.btn_viewmore button').show();
                $('input[name="start"]').val(data.num);
                $('.btn_viewmore button span').text(data.more);
            }else{
                $('.btn_viewmore button').hide();
            }
            loading('hide');
        });
    },
    
    load_cart_info : function(step=1){
        var data = '',
            event_item = $('.btn_register button').data('it');
		if(step == 1){
            data = $('form.step1').serializeArray();
		}else if(step == 2){
            data = $('form.step2').serializeArray();
        }
		$.ajax({
			type: "POST",
			url: ROOT + "ajax.php",
			data: {"m":"event", "f":"load_cart_info", 'lang_cur':lang, 'step': step, 'data':data, 'event_item':event_item}
		}).done(function (string) {
			var data = JSON.parse(string);

			if(data.mess == ''){
				$('#register .cart_info').html(data.html);
            }else{
				Swal.fire({
					icon: 'error',
					title: lang_js['aleft_title'],
					text: data.mess,
				});
			    if(step == 2){
					$('#register .cart_info').html(data.html);
				}
            }
			loading('hide');
		});
    },
	load_complete_order_event: function(){
		$.ajax({
			type: "POST",
			url: ROOT+"ajax.php",
			data: { "m" : "event", "f" : "load_complete_order_event", 'lang_cur':lang}
		}).done(function( string ) {
			var data = JSON.parse(string);
			$('form.register_form').removeClass('step2').addClass('step3');
			$('#register .group.form .content_form').html(data.html);
			$('#register .submit .wrap').html(data.complete_bottom);
		});
	},
    upload_ticket: function(id){
        if(id != ''){
            const arr = id.split(',');
            var ct = 0;
            loading('show');
            $.each(arr, function( index, value ) {
                html2canvas(document.getElementById(value),{
                    allowTaint: true,
                    useCORS: true
                }).then(function (canvas) {
                    dataURL = canvas.toDataURL("image/png");
                    $.ajax({
                        type: "POST",
                        url: ROOT + "ajax.php",
                        data: {"m":"event", "f":"upload_ticket_render", 'imgBase64':dataURL, 'index':value, 'lang_cur':lang}
                    }).done(function (string) {
                        loading('hide');
                        var data = JSON.parse(string);
                        if(data.ok == 1){
                            ct++;
                            if(ct == arr.length){
                                imsEvent.send_mail_ticket();
                            }
                        }
                    });
                });
            });
        }
	},
    send_mail_ticket: function(){
        loading('show');
        $.ajax({
            type: "POST",
            url: ROOT+"ajax.php",
            data: { "m" : "event", "f" : "send_mail_ticket", 'lang_cur':lang}
        }).done(function (string) {
            loading('hide');
            $("#register").modal("show");
            imsEvent.load_complete_order_event();
            imsEvent.load_cart_info(3);
        });
	}
}
