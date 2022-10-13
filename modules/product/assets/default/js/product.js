imsProduct = {
	search_trademark : function(){
		$(document).on("keyup",".search_trademark",function(e) {
			e.preventDefault();
			var dInput = this.value;

			if(dInput.length < 2 && dInput!=""){
				return false;
			}
			
			element = $(this).parent().find('.far').removeClass('fa-search').addClass('fa-spinner fa-pulse');
			if(dInput != ''){				
				$.ajax({
					type: "POST",
					url: ROOT+"ajax.php",
					data: { "m" : "product", "f" : "search_trademark", "dInput" : dInput }
				}).done(function( string ) {
					$('.far').removeClass('fa-spinner fa-pulse').addClass('fa-search');
					var html = '';
					var data = JSON.parse(string);
					$.each(data, function (key, obj){
						html += '<li>' +
						            '<input type="checkbox" name="brand" id="check_box_' + obj.id + '" value = "' + obj.id + '">' +
						            '<label for="check_box_' + obj.id + '"><div>' + obj.title + '</div></label>' +
						            '<span class="num d-none"> (' + obj.num_product + ')</span>' +
						        '</li>';
					});
					$('.content_list_trademark').html(html);
					// $('.content_list_trademark').mCustomScrollbar("update");
					wrapFilter(".content_list_trademark",4);
				});
			}else{
				$.ajax({
					type: "POST",
					url: ROOT+"ajax.php",
					data: { "m" : "product", "f" : "load_all_trademark", "dInput" : dInput }
				}).done(function( string ) {					
					$('.far').removeClass('fa-spinner fa-pulse').addClass('fa-search');
					var html = '';
					var data = JSON.parse(string);
					$.each(data, function (key, obj){
						html += '<li>' +
						            '<input type="checkbox" name="brand" id="check_box_' + obj.id + '" value = "' + obj.id + '">' +
						            '<label for="check_box_' + obj.id + '"><div>' + obj.title + '</div></label>' +
						            '<span class="num d-none"> (' + obj.num_product + ')</span>' +
						        '</li>';
					});
					$('.content_list_trademark').html(html);
					// $('.content_list_trademark').mCustomScrollbar("update");
					wrapFilter(".content_list_trademark",4);
				});
			}
			// '<span class="num"> (' + obj.num_product + ')</span>' +
			return false;
		});
	},
	loadProductVersion:function(){
	    if ($('#gallery_slider').length) {
	        var sync1 = $("#gallery_slider"),
	            sync2 = $("#gallery_slider_thumb");
	        sync1.slick({
	            slidesToShow: 1,
	            arrows: true,
	            fade: true,
	            asNavFor: "#gallery_slider_thumb",
	            swipe: false,
	            lazyload: "ondemand",
	        })
	        sync2.slick({
	            slidesToShow: 6,
	            asNavFor: "#gallery_slider",
	            infinite: true,
	            dots: false,
	            arrows: true,
	            swipeToSlide: true,
	            focusOnSelect: true,
	            vertical: false,
	            lazyload: "ondemand",
	            responsive: [
	                {
	                    breakpoint: 992,
	                    settings: {
	                        slidesToShow: 3,
	                        vertical: false,
	                    }
	                }
	            ]
	        })
	        // if($("#pzoom_0").length){
	        //     $("#pzoom_0").elevateZoom({
	        //         zoomType: "inner",
	        //         zoomWindowFadeIn: 500,
	        //         zoomWindowFadeOut: 500,
	        //         lensFadeIn: 500,
	        //         lensFadeOut: 500,
	        //         cursor: "zoom-in",
	        //         scrollZoom: true
	        //     });
	        // }
	        // sync1.on("afterChange", function(event, slick, currentSlide, nextSlide){
	        //     $(".zoomContainer").remove();
	        //     $("#pzoom_"+currentSlide).elevateZoom({
	        //         zoomType: "inner",
	        //         zoomWindowFadeIn: 500,
	        //         zoomWindowFadeOut: 500,
	        //         lensFadeIn: 500,
	        //         lensFadeOut: 500,
	        //         cursor: "zoom-in",
	        //         scrollZoom: true
	        //     });  
	        // });    
	    }
	    $(document).on("click", ".info_version_data input",function(){   
            var productId       = $("#item_detail").data("id");
            var thisClickOption = $(this).data("option");
            var thisClickValue  = $(this).val();
            var max = $(".info_version_data").length;
            optionData = versionChecked(".info_version_data", thisClickOption);
            loading('show');
            $.ajax({
                type: "POST",
                url: ROOT+"ajax.php",
                data: { "m":"product", "f":'loadProductVersion', 'lang_cur':lang, 'max':max, 'data':optionData, 'id':productId, 'thisClickOption':thisClickOption, 'thisClickValue':thisClickValue},
            }).done(function(string){
                var data = JSON.parse(string);
                loading('hide');

                if(typeof data.option_id !== "undefined" && data.option_id>0) {
                    $("#item_detail input[name='option_id']").val(data.option_id);
                    $("#item_detail input[name='quantity']").attr("max",data.max_quantity).val(1);
                    $("#item_detail .price_buy").html(data.price_buy_text);
                    // $("#item_detail #item_code").html(data.item_code);
                    if(typeof data.percent_discount !== "undefined") {
                        if(data.percent_discount>0){
                            $("#item_detail .price, #item_detail .percent_discount").show();
                            $("#item_detail .price").html(data.price_text);
                            $("#item_detail .percent_discount .percent").html(data.percent_discount);
                            // $("#item_detail .percent_discount .amount").html(data.amount_discount);
                        }else{
                            $("#item_detail .price, #item_detail .percent_discount").hide();
                        }
                    }
					$("#item_detail .btn_add_cart_now").attr('type', data.type_btn).find('span').text(data.btn_add_cart);
					$("#item_detail .btn_add_cart").attr('type', data.type_btn).find('span').text(data.btn_order);
                }else{
                    $("#item_detail input[name='option_id']").val(0);
                }

                if(data.lvl=='0'){
                    $("#selector-option-0").html(data.html.op0);
                    $("#selector-option-1").html(data.html.op1);
                    $("#selector-option-2").html(data.html.op2);
                }
                // auto_price_format();
                // var sPo = $("#gallery_slider .owl-item [data-value=\'"+ data.option_id +"\']").parents(".owl-item");
                // var sIn = sPo.index()?sPo.index():0;
                // sync1.trigger("to.owl.carousel", [sIn, 5000, true]);
                // $("#gallery_slider_thumb .owl-item").eq(0).addClass("sync_cur");
            })
	        var color = $(this).attr('data-color');
	        if(typeof color !== 'undefined' && color.length>0){
	            var sPo = $("#gallery_slider .slick-slide [data-color=\'"+color+"\']").parents(".slick-slide");
	            var sIn = sPo.length?sPo.data("slick-index"):0;
	            sync1.slick("slickGoTo",sIn);
	        }
	    })
	},
    load_more : function(reload=0){
        loading('show');
        var num_cur = $('input[name="start"]').val();
        var group_id = $('.box_content_product .list_item_product').data('group');
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
            data: {"m":"product", "f":"load_products_ajax", 'num_cur':num_cur, 'group_id':group_id, 'order_by':order_by, 'sort':sort, 'keyword':keyword, 'focus':focus}
        }).done(function (string) {
            var data = JSON.parse(string);
            if(reload == 0){
                $('.box_content_product .list_item_product .row_item').append(data.html);
            }else{
                var scroll_to = $('#scroll_to').offset();
                $('html, body').animate({scrollTop: scroll_to.top-$('.sticky-wrapper').height()-51}, 600);
                $('.group_title p span').text(data.total);
                $('.box_content_product .list_item_product .row_item').html(data.html);
            }
            if(data.filter_product != ''){
                $('.filter_product').html(data.filter_product);
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
    load_more_product_header : function(){
	    $('.see_more_header a').on('click', function (){
            loading('show');
            var group_id = $(this).data("group");
            var start = $(this).data('start');
            var limit = $(this).data('limit');
            var where = $(this).data('where');
            var htm = $(this).parent().parent().attr('id');

            $.ajax({
                type: "POST",
                url: ROOT + "ajax.php",
                data: {"m":"product", "f":"load_product_header_ajax", 'group_id':group_id, 'start':start, 'limit':limit, 'where':where}
            }).done(function (string) {
                var data = JSON.parse(string);
                $('#'+htm + ' .list_item_product .row_item').append(data.html);
                $('#'+htm + ' .see_more_header').remove();
                loading('hide');
            });
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
			data: {"m":"product", "f":"load_cart_info", 'lang_cur':lang, 'step': step, 'data':data, 'event_item':event_item}
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
			data: { "m" : "product", "f" : "load_complete_order_event", 'lang_cur':lang}
		}).done(function( string ) {
			var data = JSON.parse(string);
			$('form.register_form').removeClass('step2').addClass('step3');
			$('#register .group.form .content_form').html(data.html);
			$('#register .submit .wrap').html(data.complete_bottom);
		});
	}
}

function checked_input_color() {
	$('.list_input_color input[type=radio]').each(function() {
		var li = $(this).parent();
    if($( this ).is(':checked')) {
			if(!li.hasClass('checked')) {
				li.addClass('checked');
			}
		} else {
			li.removeClass('checked');
		}
  });
}
function checked_input_size() {
	$('.list_input_size input[type=radio]').each(function() {
		var li = $(this).parent();
    if($( this ).is(':checked')) {
			if(!li.hasClass('checked')) {
				li.addClass('checked');
			}
		} else {
			li.removeClass('checked');
		}
  });
}
function list_input_color() {
	checked_input_color();
	$('.list_input_color li').click(function(){
		checked_input_color();
	});
}
function list_input_size() {
	checked_input_size();
	$('.list_input_size li').click(function(){
		checked_input_size();
	});
}
function detail_quantity() {
	var price_buy = $('#info_row-price_buy .number').data('value');
	$('input[name=quantity]').change(function(){
		var total = price_buy * $(this).val();
		$('#detail-total .number').data('value',total).text(total);
		auto_rate_exchange();
	});
}
function versionChecked(selector, thisClickOption){
    var selected = {};
    var checked = $(selector+" input:radio[data-option='"+thisClickOption+"']:checked").val();
    var enabled = $(selector+" input:radio[data-option='"+thisClickOption+"']:checked").next().hasClass('checked');
    if (checked!="" && enabled==true) {
        $(selector+" input:radio[data-option='"+thisClickOption+"']:checked").val("");
    }

    var value1 = $(selector+" input:radio[data-option='Option1']:checked").val();
    var value2 = $(selector+" input:radio[data-option='Option2']:checked").val();
    var value3 = $(selector+" input:radio[data-option='Option3']:checked").val();
    if(typeof(value1) ==="undefined") value1 = "";
    if(typeof(value2) ==="undefined") value2 = "";
    if(typeof(value3) ==="undefined") value3 = "";
    selected["Option1"] = value1;
    selected["Option2"] = value2;
    selected["Option3"] = value3;
    // console.log(value1);
    // console.log(value2);
    // console.log(value3);
    return selected;
}
function list_combine() {
	$('.list_combine .list_combine-title').click(function(){
		$(this).find('.list_combine-arrow').toggleClass('show');
		$(this).parent().find('ul').slideToggle(200);
	})
	$('.list_combine li input[type=radio]').click(function(){
		$('.list_combine .list_combine-title span').text($(this).data('title'));
	})
	$('.list_combine').mouseleave(function(){
		$(this).find('.list_combine-arrow').removeClass('show');
		$(this).find('ul').slideUp(200);
	})
}
//
//function add_cart(form_id) {
//	$("#"+form_id).submit(function(){
//		var output = true;
//		
//		if($("#"+form_id+" select[name^='size']")) {
//			var size = $("#"+form_id+" select[name^='size']").val();
//			if(size <= 0) {
//				jAlert(lang_js['err_invalid_size'], lang_js['aleft_title'], null, 'error');
//				output = false;
//			}
//		}
//		
//		if($("#"+form_id+" select[name^='quantity']")) {
//			var quantity = $("#"+form_id+" select[name^='quantity']").val();
//			if(quantity <= 0) {
//				jAlert(lang_js['err_invalid_quantity'], lang_js['aleft_title'], null, 'error');
//				output = false;
//			}
//		}
//		
//		return output;
//	});
//}