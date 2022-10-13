imsUser = {
	isSend : 1,
	sendOTP : function(request=0){
		var timeout = null;
		var form_mess = $("#verify_otp").find('.form_mess');
			form_mess.stop(true,true).slideUp(200).html('');
		var phone = $("#mobile").val(),
			id = $("#mobile").attr("data-id"),
			request = request;
		if (phone.length >= 10 && phone != null && imsUser.isSend==1) {
			var input = {
				"phone" : phone,
				"user_id" : id,
				"request" : request,
			};			
			imsUser.isSend = 2;
			clearTimeout(timeout);
			timeout = setTimeout(function () {
				$.ajax({
					type: "POST",
					url: ROOT+"ajax.php",
					data: { "m" : "user", "f" : "request_otp", "data" : input},
					beforeSend: function(){                    
	                    loading('show');
	                },
	                success: function(string){
	                	imsUser.isSend = 1;
	                	var data = JSON.parse(string);
	                	console.log(data);
	                	if(data.ok==1){
	                		Swal.fire({
			                    icon: 'success',
			                    title: lang_js['aleft_title'],
			                    html: data.mess,
			                })
						}else{
							Swal.fire({
			                    icon: 'error',
			                    title: lang_js['aleft_title'],
			                    html: data.mess,
			                }).then((result) => {
	                            if(data.link_go!=''){
									go_link(data.link_go);
								}
	                        });	
						}						
	                },
	                complete: function(){
	                    loading('hide');
	                }
				})
			}, 1000);  
		} else {
			form_mess.html(imsTemp.html_alert(lang_js_mod['user']['invalid_phone'],'error')).stop(true,true).slideDown(200);
		}
	},
	verifyOTP : function(){
		$("#verify").on("click",function(){			
			var otp = $("#mobileOtp").val(),
				phone = $("#mobile").val(),
				id = $("#mobile").attr("data-id"),
				link_go = $(this).attr("data-go");
			var input = {
				"otp" : otp,
				"phone" : phone,
				"user_id" : id,
			};
			console.log(input);
			$.ajax({
				type: "POST",
				url: ROOT+"ajax.php",
				data: { "m" : "user", "f" : "verify_otp", "data" : input},
				beforeSend: function(){                    
                    loading('show');
                },
                success: function(string){
                	var data = JSON.parse(string);
                	if(data.ok==1){
                		Swal.fire({
		                    icon: 'success',
		                    title: lang_js['aleft_title'],
		                    html: data.mess,
		                }).then((result) => {
		                	go_link(link_go);
                        });							
					}else{
						Swal.fire({
		                    icon: 'error',
		                    title: lang_js['aleft_title'],
		                    html: data.mess,
		                })
					}
                },
                complete: function(){
                    loading('hide');
                }
			})
		})
	},
	withdrawWcoin:function(form_id){
		$("#"+form_id).validate({
			submitHandler: function() {
				var form_mess = $("#"+form_id).find('.form_mess');
				form_mess.stop(true,true).slideUp(200).html('');
				var fData = $("#"+form_id).serializeArray();
				loading('show');
	            $.ajax({
	                type: "POST",
	                url: ROOT + "ajax.php",
	                data: {"m": "user", "f": "withdrawWcoin", "data": fData, "lang_cur": lang}
	            }).done(function (string) {
	                var data = JSON.parse(string);
	                loading('hide');
	                if(data.ok == 1) {
						Swal.fire({
						  	icon: 'success',
						  	title: lang_js['aleft_title'],
						  	text: data.mess
						})
					} else {
						Swal.fire({
						  	icon: 'error',
						  	title: lang_js['aleft_title'],
						  	html: data.mess,
						})
					}
	            });
				return false;
			},
			rules: {
				num_wcoin: {
                    required: true,
                },
				bankcode: {
                    required: true,
                },
                bankname: {
                    required: true,
                },
                bankbranch: {
                    required: true,
                },
                full_name: {
                    required: true,
                }
			},
			messages: {
				num_wcoin: lang_js['err_valid_input'],
				bankcode: lang_js['err_valid_input'],
				bankname: lang_js['err_valid_input'],
				bankbranch: lang_js['err_valid_input'],
				full_name: lang_js['err_valid_input']
			}			
		});
	},
    swap_commission:function(form_id){
        $("#"+form_id).validate({
            submitHandler: function() {
                var form_mess = $("#"+form_id).find('.form_mess');
                form_mess.stop(true,true).slideUp(200).html('');
                var fData = $("#"+form_id).serializeArray();
                // loading('show');
                $.ajax({
                    type: "POST",
                    url: ROOT + "ajax.php",
                    data: {"m": "user", "f": "swap_commission", "data": fData, "lang_cur": lang}
                }).done(function (string) {
                    var data = JSON.parse(string);
                    loading('hide');
                    if(data.ok == 1) {
                        Swal.fire({
                            icon: 'success',
                            title: lang_js['aleft_title'],
                            text: data.mess
                        }).then((result) => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: lang_js['aleft_title'],
                            html: data.mess,
                        })
                    }
                });
                return false;
            },
            rules: {
                num_commission: {
                    required: true,
                }
            },
            messages: {
                num_commission: lang_js['err_valid_input'],
            }
        });
    },


	addAddessBook:function(form_id){
		// alert('s');
		$("#"+form_id).validate({
			submitHandler: function() {
				var form_mess = $("#"+form_id).find('.form_mess');
				form_mess.stop(true,true).slideUp(200).html('');
				var fData = $("#"+form_id).serializeArray();
				loading('show');
	            $.ajax({
	                type: "POST",
	                url: ROOT + "ajax.php",
	                data: {"m": "user", "f": "addAddressBook", "data": fData, "lang_cur": lang}
	            }).done(function (string) {
	                var data = JSON.parse(string);
	                loading('hide');
	                if (data.ok == 1) {
	                    if (data.default>0) {
	                        $("#address_book").val(data.default);
	                    }
	                    $.fancybox.close();
	                    location.reload();
	                }else{
						form_mess.html(imsTemp.html_alert(data.mess,'error')).stop(true,true).slideDown(200);
	                }
	            });
				return false;
			},
			rules: {
				full_name: {
                    required: true,
                },
				// email: {
                //     required: true,
                //     email: true,
                // },
                phone: {
                    required: true,
                    phone: true,
                }
			},
			messages: {
				
			}			
		});
	},
	popupAddessBook:function(){
		$(document).on("click", ".popupAddessBook", function(e){
			e.preventDefault();
			var element = $(this);
			var id = $(this).data('value');
			if(!id) return false;
			loading('show');
			$.ajax({
				type: "POST",
				url: ROOT+"ajax.php",
				data: { "m" : "user", "f" : "popupAddessBook", "id" : id}
			}).done(function( string ) {
				loading('hide');				
				var data = JSON.parse(string);
				var status_succes = 'success';
				if(data.ok == 1) {
					$.fancybox.open(data.html);
					imsUser.addAddessBook(data.form_id);
				} else {
					Swal.fire({
	                    icon: 'error',
	                    title: lang_js['aleft_title'],
	                    text: data.mess,
	                })
				}
			});
			return false;
		});
	},
	add_favorite : function(mod){
		$(document).on('click', '.add_favorite', function(){
			var element = $(this);
			var id = $(this).attr('data-id');
			var path = window.location.pathname;
			if(!id) return false;
			loading('show');
			$.ajax({
				type: "POST",
				url: ROOT+"ajax.php",
				data: { "m" : "user", "f" : "check_favorite", "id" : id, 'mod':mod, "path" : path}
			}).done(function( string ) {
				loading('hide');				
				var data = JSON.parse(string);
				var status_succes = 'success';
				if(data.ok == 1) {
					if(data.is_favorite == 1) {
						element.addClass("added");
						element.find("i").removeClass('fal');
						element.find("i").addClass('fas');
					}else if(data.is_favorite == 0){
						element.removeClass("added");
						element.find("i").removeClass('fas');
						element.find("i").addClass('fal');
						status_succes = 'warning';
					}
					// Swal.fire({
					//   	icon: status_succes,
					//   	title: lang_js['aleft_title'],
					//   	text: data.mess
					// })
				} else {
					Swal.fire({
					  	icon: 'error',
					  	title: lang_js['aleft_title'],
					  	html: data.mess,
					})
				}
			});
			return false;
		});
	},
	post_advisory:function(form_id) {
		$("."+form_id).validate({
			submitHandler: function() {
				var form_mess = $('.'+form_id).find('.form_mess');
				form_mess.stop(true,true).slideUp(200).html('');
				var fData = $("."+form_id).serializeArray();
                link_act = $("."+form_id).attr('link-go');
				loading('show');
				$.ajax({
					type: "POST",
					url: ROOT+"ajax.php",
					data: { "m" : "user", "f" : "post_advisory", "lang_cur" : lang, "data" : fData }
				}).done(function( string ) {
					loading('hide');
					var data = JSON.parse(string);					
					var html = '';
					$('.captcha_refresh').click();
					if(data.ok == 1) {
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
	load_comment:function(form_id){
		$(document).on('click', ".btn_loadmore", function (e) {
			// var type_id = $("#"+form_id + " button[type='submit'] ").data('type_id');
			var type_id = $(this).attr('data-type_id');
	    	var start = $(this).attr('data-start');
	    	var type = $(this).attr('data-type');
			loading('show');
	    	if(!start) { return false; }
			$.ajax({
	            type: "POST",
	            url: ROOT+"ajax.php",
	            data: { "m" : "user", "f" : "load_comment", "lang_cur" : lang, "type_id" : type_id,'start' : start, 'type' : type }
	        }).done(function( string ) {
				loading('hide');
				var html = '';
				console.log(string);
	            var data = JSON.parse(string);
	       		html = data.html
				$('.div_more').before(data.html);
				if(data.start == data.max || data.start > data.max){
					$('.btn_loadmore').text('');
					$('.btn_loadmore').removeAttr('data-start');
					$('.btn_loadmore').removeAttr('data-max');
					$('.btn_loadmore').removeClass('btn_loadmore');
				}
				else{
					$('.btn_loadmore').attr("data-start", data.start);
				}
				$('.div_more .count_comment span').text(data.start);
		    });
	    });
	},
	post_comment:function(form_id) {
		$("#"+form_id).validate({
			submitHandler: function(e) {
				var form_mess = $('#'+form_id).find('.form_mess');
				form_mess.stop(true,true).slideUp(200).html('');
				var fData = $("#"+form_id).serializeArray();
                var type = $("#"+form_id + " button[type='submit'] ").data('type');
                var type_id = $("#"+form_id + " button[type='submit'] ").data('type_id');
                var type_reply = $("#"+form_id + " button[type='submit'] ").data('type_reply');
                if ( !type ) { return false; }
                if ( !type_id ) { return false; }
                fData.push({
	                name: "type",
	                value: type
	            });
	            fData.push({
	                name: "type_id",
	                value: type_id
	            });  
	            fData.push({
	                name: "type_reply",
	                value: type_reply
	            });  
				loading('show');
				$.ajax({
					type: "POST",
					url: ROOT+"ajax.php",
					data: { "m" : "user", "f" : "post_comment", "lang_cur" : lang, "data" : fData }
				}).done(function( string ) {
					loading('hide');
					var data = JSON.parse(string);
					var html = '';
					$('html, body').stop().animate({
				        scrollTop: $("#"+form_id).offset().top - 50
				    }, 500);
					if(data.ok == 1) {
						$('.captcha_refresh').click();
						location.reload(true);
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
	post_rating:function(form_id){
		$("#"+form_id).validate({
			submitHandler: function() {
				var form_mess = $('#'+form_id).find('.form_mess');
				form_mess.stop(true,true).slideUp(200).html('');
				var fData = $("#"+form_id).serializeArray();      
				var type = $("#"+form_id + " button[type='submit'] ").data('type');
	            var type_id = $("#"+form_id + " button[type='submit'] ").data('type_id');
				if ( !type ) { return false; }
	            if ( !type_id ) { return false; }
	            fData.push({
	                name: "type",
	                value: type
	            });
	            fData.push({
	                name: "type_id",
	                value: type_id
	            });        
				loading('show');
				$.ajax({
					type: "POST",
					url: ROOT+"ajax.php",
					data: { "m" : "user", "f" : "post_rating", "lang_cur" : lang, "data" : fData }
				}).done(function( string ) {
					loading('hide');					
					var data = JSON.parse(string);
					var html = '';
					$('.captcha_refresh').click();
					if(data.ok == 1) {
						form_mess.html(imsTemp.html_alert(data.mess,'success')).stop(true,true).slideDown(200);
					} else {
						form_mess.html(imsTemp.html_alert(data.mess,'error')).stop(true,true).slideDown(200);
					}
				});
				return false;
			}
		})
	},
	check_order:function (form_id) {
		$("#"+form_id).validate({
			submitHandler: function() {
				var form_mess = $('#'+form_id).find('.form_mess');
				form_mess.stop(true,true).slideUp(200).html('');
				var fData = $("#"+form_id).serializeArray();
                link_act = $("#"+form_id).attr('link-go');
				loading('show');
				$.ajax({
					type: "POST",
					url: ROOT+"ajax.php",
					data: { "m" : "user", "f" : "get_order", "lang_cur" : lang, "data" : fData }
				}).done(function( string ) {
					loading('hide');
					var data = JSON.parse(string);
					if(data.ok == 1) {
						var code = $('#'+form_id + " [name='order_code']").val();
						go_link(link_act + '' + code + '.html');
					}
					else if(data.ok == 2) {
						var code = $('#'+form_id + " [name='order_code']").val();
						go_link(link_act + '?by_phone=' + code);
					} else {
						Swal.fire({
		                    icon: 'error',
		                    title: lang_js['aleft_title'],
		                    text: data.mess,
		                })
					}
				});
				return false;
			},
			rules: {
				order_code: {
					required: true
				},
				o_email: {
					required: true,
				},
				
			},
			messages: {
				order_code: '',
				o_email: '',
			}			
		});
	},
	form_notification:function (form_id) {
		// notification_product 
		var ajax_send = false;

	    $( ".notification_product .button_no" ).click(function( event ) {
	    	if (ajax_send == true){
               return false;
	        }
	        ajax_send = true;
	        var item_id = $("input[name=item_id]").val();
	        var title = $("input[name=title]").val();
	        var image = $("input[name=picture]").val();
	        $.jAlert({ 
	            'title': '',
	            'content' : '<form id="form_notification" name="form_report" method="post" action="" novalidate="novalidate" >' +
	                        '<div class="form_mess"></div>' +
	                        '<div class="view_no_product">' +
	                        '<div class="image"><img src="' + image + '"/></div>' +
	                        '<div class="title">' + title + '</div>' +
	                        '</div>' +
	                        '<div class="send_no_product">' +
	                            '<input name="name" type= "text" placeholder="' + lang_js['full_name'] + '">' +
	                            '<input name="email" type= "text" placeholder="' + lang_js['enter_email'] + '">' +
	                            '<textarea name="content" type= "text"></textarea>' +
	                            '<input style="display: none" type="text" name="item_id" value="' + item_id + '"></textarea>' +
	                            '<div class="bottom">' +
	                                '<button type="submit" name="send" class="btn btn-primary btn-lg fr" style="width: 100%;">' + lang_js['register_now'] + '</button>' +
	                                '<div class="clear"></div>' +
	                            '</div>'+
	                            '<div class="clear"></div>' +
	                        '</div>' +
	                        '<script language="javascript"> ajax_send = false; imsUser.form_notification("form_notification");</script>' + 
	                        '</form>',
	            'size': 'md'
	        });
	    });
		$("#"+form_id).validate({
			submitHandler: function() {
				var form_mess = $('#'+form_id).find('.form_mess');
				form_mess.stop(true,true).slideUp(200).html('');
				var fData = $("#"+form_id).serializeArray();
				loading('show');
				$.ajax({
					type: "POST",
					url: ROOT+"ajax.php",
					data: { "m" : "user", "f" : "get_notification", "lang_cur" : lang, "data" : fData }
				}).done(function( string ) {
					loading('hide');
					var data = JSON.parse(string);
					if(data.ok == 1) {
						form_mess.html(imsTemp.html_alert(data.mess,'success')).stop(true,true).slideDown(200);
					} else {
						form_mess.html(imsTemp.html_alert(data.mess,'error')).stop(true,true).slideDown(200);
					}
				});
				return false;
			},
			rules: {
				name: {
					required: true
				},
				email: {
					required: true,
					email: true
				},
				content: {
					required: true
				},
			},
			messages: {
				name: lang_js['err_valid_input'],
				email: {
					required: lang_js['err_valid_input'],
					email: lang_js['err_email_input']
				},
				content: lang_js['err_valid_input'],
			}			
		});
	},
	show_signup:function (show, hide) {
		$('#'+show).slideDown(200);
		$('#'+hide).slideUp(200);
	},
	
	signup:function (form_id, link_go) {
		$("#"+form_id).validate({
			submitHandler: function() {
				var form_mess = $('#'+form_id).find('.form_mess');
				form_mess.stop(true,true).slideUp(200).html('');
				var fData = $("#"+form_id).serializeArray();
				
				loading('show');
				
				$.ajax({
					type: "POST",
					url: ROOT+"ajax.php",
					data: { "m" : "user", "f" : "signup", "lang_cur" : lang, "data" : fData }
				}).done(function( string ) {					
					loading('hide');
					var data = JSON.parse(string);
					if(data.ok == 1) {
						Swal.fire({
                            icon: 'success',
                            title: lang_js['aleft_title'],
                            text: data.mess,
                            timer: 2000
                        }).then(function() {
                            go_link(link_go);
                        });
					} else if(data.ok == 2) {						
						if(data.verify_otp != ''){
							go_link(data.verify_otp);
						}else{
							Swal.fire({
			                    icon: 'error',
			                    title: lang_js['aleft_title'],
			                    text: data.mess,
			                })
						}
					} else {
						form_mess.html(imsTemp.html_alert(data.mess,'error')).stop(true,true).slideDown(200);
					}
				});
				//e.preventDefault(); //STOP default action
				//e.unbind(); //unbind. to stop multiple form submit.
				return false;
				
			},
			rules: {
				full_name: {
					required: true
				},
				username: {
					required: true,
					email: true
				},
				phone: {
					required: true
				},
				password: {
					required: true
				},
				re_password: {
					 equalTo: '#'+form_id+' input[name*="password"]'
				},
				/*re_password: {
					equalTo: '#'+form_id+' #header_password'
				},*/				
//				address: {
//					required: true
//				},
				captcha: {
					required: true
				}
			},
			messages: {
				// full_name: lang_js['err_valid_input'],
				// username: lang_js['err_valid_input'],
				password: '',
				re_password: '',
				phone: '',
				//address: lang_js['err_valid_input'],
				// captcha: lang_js['err_valid_input']
			}			
		});
	},
	
	signin:function (form_id, link_go) {
		var form_mess = $('#'+form_id).find('.form_mess');
		var acctive = getUrlParameter('acctive');
		if(acctive == 1){
			form_mess.html(imsTemp.html_alert(lang_js['acctive_user'],'success')).stop(true,true).slideDown(200);
		}
		$("#"+form_id).validate({
			submitHandler: function() {
				form_mess.stop(true,true).slideUp(200).html('');
				var fData = $('#'+form_id).serializeArray();
				loading('show');
				$.ajax({
					type: "POST",
					url: ROOT+"ajax.php",
					data: { "m" : "user", "f" : "signin", "lang_cur" : lang, "data" : fData }
				}).done(function( string ) {
					loading('hide');
					var data = JSON.parse(string);
					if(data.ok == 1) {
						go_link(link_go);
					} else {
						form_mess.html(imsTemp.html_alert(data.mess,'error')).stop(true,true).slideDown(200);
					}
				});
				//e.preventDefault(); //STOP default action
				//e.unbind(); //unbind. to stop multiple form submit.
				return false;
			},
			rules: {
				username: {
					required: true,
					// email: true
				},
				password: {
					required: true
				}
			},
			messages: {
				// username: lang_js['err_valid_input'],
				username: '',
				// password: lang_js['err_valid_input']
				password: ''
			}
		});
		
	},
	
	signout:function (link_go) {
		
		loading('show');
		
		$.ajax({
			type: "POST",
			url: ROOT+"ajax.php",
			data: { "m" : "user", "f" : "signout" }
		}).done(function( string ) {
			
			loading('hide');
			
			var data = JSON.parse(string);
			if(data.ok == 1) {
                loading('show');
				go_link(link_go);
			}
		});
		return false;
	},
	
	// account ============================================================
	account:function (form_id, request=0) {

		$("#"+form_id).validate({			
            submitHandler: function() {
            	var formData = new FormData($("#"+form_id)[0]);

				var image = $("#img-preview").attr("src");
				if(typeof image != 'undefined' && image.includes('data:image')){
					var base64ImageContent = image.replace(/^data:image\/(png|jpg);base64,/, "");
					var blob = base64ToBlob(base64ImageContent, 'image/png');
					formData.append("picture", blob);
                }
                
                formData.append("f", "account");
                formData.append("m", "user");
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
					var data = JSON.parse(string);
					if(data.ok == 1) {						
						if(request != 0){
							Swal.fire({
	                            icon: 'success',
	                            title: lang_js['aleft_title'],
	                            text: lang_js_mod['user']['wait_accept'],
	                        }).then((result) => {
                                location.reload();
                            });
						}else{
							Swal.fire({
	                            icon: 'success',
	                            title: lang_js_mod['user']['update_success'],
	                        })
						}
					} else {
                        Swal.fire({
                            icon: 'error',
                            title: lang_js['aleft_title'],
                            html: data.mess,
                        })
					}
				});
				return false;
			},
			rules: {
				first_name: {
					required: true
				},
				phone: {
					required: true
				},
				email: {
					required: true	
				},
				province: {
					required: true
				},
				district: {
					required: true
				},
				ward: {
					required: true
				},
				address: {
					required: true
				}
			},
			messages: {
				first_name: lang_js['err_empty'].replace("[name]", lang_js_mod['user']['first_name']),
				phone: lang_js['err_empty'].replace("[name]", lang_js_mod['user']['phone']),
				email: lang_js['err_empty'].replace("[name]", lang_js_mod['user']['email']),
				province: lang_js['err_empty'].replace("[name]", lang_js_mod['user']['province']),
				district: lang_js['err_empty'].replace("[name]", lang_js_mod['user']['district']),
				ward: lang_js['err_empty'].replace("[name]", lang_js_mod['user']['ward']),
			}
		});
	},
	remove_avatar:function () {		
		Swal.fire({
            icon: 'info',            
            text: lang_js_mod['user']['are_you_sure_del'].replace("[name]", lang_js_mod['user']['avatar']),
            showCancelButton: true,
            cancelButtonText: lang_js_mod['user']['no'],
            confirmButtonText: lang_js_mod['user']['yes'],
        }).then((result) => {
            if(result.isConfirmed){
            	loading('show');
            	$.ajax({
					type: "POST",
					url: ROOT+"ajax.php",
					data: { "m" : "user", "f" : "remove_avatar" }
				}).done(function( string ) {
					loading('hide');			
					var data = JSON.parse(string);
					if(data.ok == 1) {						
						location.reload();
					}
				});
            }
        });
		
		return false;
	},
	
	// change_pass ============================================================
	change_pass:function (form_id) {
		
		$("#"+form_id).validate({
            submitHandler: function() {
				var form_mess = $('#'+form_id).find('.form_mess');
				form_mess.stop(true,true).slideUp(200).html('');
				var fData = $('#'+form_id).serializeArray();
				
				loading('show');
				
				$.ajax({
					type: "POST",
					url: ROOT+"ajax.php",
					data: { "m" : "user", "f" : "change_pass", "lang_cur" : lang, "data" : fData }
				}).done(function( string ) {
					
					loading('hide');
					
					var data = JSON.parse(string);
					if(data.ok == 1) {
						form_mess.html(imsTemp.html_alert(data.mess,'success')).stop(true,true).slideDown(200);
					} else {
						form_mess.html(imsTemp.html_alert(data.mess,'error')).stop(true,true).slideDown(200);
					}
				});
				//e.preventDefault(); //STOP default action
				//e.unbind(); //unbind. to stop multiple form submit.
				return false;
			},
			rules: {
				password_cur: {
					required: true
				},
				password: {
					required: true
				},
				re_password: {
					equalTo: '#'+form_id+' #password'
				},
			},
			messages: {
				password_cur: get_lang('err_invalid', 'user', {'[name]':get_lang('password_cur', 'user')}),
				password: get_lang('err_invalid', 'user', {'[name]':get_lang('password', 'user')}),
				re_password: get_lang('err_invalid', 'user', {'[name]':get_lang('re_password', 'user')}),
			}
		});
	},
	
	// forget_pass ============================================================
	forget_pass:function (form_id) {
        
		$("#"+form_id).validate({
            submitHandler: function() {
				var form_mess = $('#'+form_id).find('.form_mess');
				form_mess.stop(true,true).slideUp(200).html('');
				var fData = $('#'+form_id).serializeArray();
				
				loading('show');
				
				$.ajax({
					type: "POST",
					url: ROOT+"ajax.php",
					data: { "m" : "user", "f" : "forget_pass", "lang_cur" : lang, "data" : fData }
				}).done(function( string ) {
					
					loading('hide');
					
					var data = JSON.parse(string);
					if(data.ok == 1) {						
						if(data.link){
							go_link(data.link);
						}else{
							form_mess.html(imsTemp.html_alert(data.mess,'success')).stop(true,true).slideDown(200);
						}
					} else {
						form_mess.html(imsTemp.html_alert(data.mess,'error')).stop(true,true).slideDown(200);
					}
				});
				//e.preventDefault(); //STOP default action
				//e.unbind(); //unbind. to stop multiple form submit.
				return false;
			},
			rules: {
				username: {
					required: true,					
				}
			},
			messages: {
				username: {
					required: lang_js['err_valid_input'],					
				}
			}
		});
	},
	save_link:function(form_id){
		$("#"+form_id).validate({
			submitHandler: function() {
				var form_mess = $('#'+form_id).find('.form_mess');
				form_mess.stop(true,true).slideUp(200).html('');
				var fData = $('#'+form_id+' .link_shorten').val();
				// var fData = $('#'+form_id).serializeArray();
				loading('show');
				$.ajax({
					type: "POST",
					url: ROOT+"ajax.php",
					data: { "m" : "user", "f" : "save_link", "lang_cur" : lang, "data" : fData }
				}).done(function( string ) {
					// console.log(string);
					loading('hide');				
					var data = JSON.parse(string);
					if(data.ok == 1) {
						$('#'+form_id+' .link').val(data.link_shorten);
						form_mess.html(imsTemp.html_alert(data.mess,'success')).stop(true,true).slideDown(200);
						// $("#"+form_id).load(window.location.href + " #"+form_id );
					} else {
						form_mess.html(imsTemp.html_alert(data.mess,'error')).stop(true,true).slideDown(200);
					}
				});
				//e.preventDefault(); //STOP default action
				//e.unbind(); //unbind. to stop multiple form submit.
				return false;
			}
		})
	},
	form_send_inv:function(form_id){
		$("#"+form_id).validate({
			submitHandler: function(e) {
				e.preventDefault();
				var form_mess = $('#'+form_id).find('.form_mess');
				form_mess.stop(true,true).slideUp(200).html('');
				var fData = $("#"+form_id).serializeArray();
				loading('show');
				$.ajax({
					type: "POST",
					url: ROOT+"ajax.php",
					data: { "m" : "user", "f" : "send_inv", "lang_cur" : lang, "data" : fData }
				}).done(function( string ) {
					loading('hide');
					var data = JSON.parse(string);
					// console.log(data);
					if(data.ok == 1) {
						$("#"+form_id)[0].reset();
						form_mess.html(imsTemp.html_alert(data.mess,'success')).stop(true,true).slideDown(200);
					} else {
						form_mess.html(imsTemp.html_alert(data.mess,'error')).stop(true,true).slideDown(200);
					}
				});
				return false;
			},
			rules: {
				content: {
					required: true
				},
			},
			messages: {
				content: '',
			}			
		});
	},
	share_mail:function(id_selector){
		var ajax_send = false;
	    $("#"+id_selector).click(function( event ) {
	        if (ajax_send == true){
	           return false;
	        }
	        ajax_send = true;
	        var link = $(this).attr('data-link-mail');
	        var link_singin = $(this).attr('data-link-singin');
	        var content_more = $('#content_more').text();
	        if(link == '') {
	            go_link(link_singin);
	        }
	        if(!link) { return false; }
	        Swal.fire({
                icon: 'info',
                title: lang_js_mod['user']['share_link_mail'],
                html: '<div>'+lang_js_mod['user']['share_link']+'</div>'+
            			'<form id="form_send_inv" name="form_report" method="post" action="" novalidate="novalidate" >' +
                        '<div class="form_mess"></div>' +
                        '<div class="send_no_product">' +                            
                            '<input onClick="this.setSelectionRange(0, this.value.length)" name="name" type= "hidden" value="'+ link +'">' +
                            '<div class="clear"></div>' +
                            '<textarea style="margin-bottom: 10px;" name="content" type= "text" placeholder="' +  lang_js_mod['user']['placeholder_share_link'] + '"></textarea>' +
                            // '<textarea name="content_more" type= "text" placeholder="' +  lang_js['placeholder_contentshare_link'] + '">'+ content_more +'</textarea>' +
                            '<div class="bottom">' +
                                // '<button type="submit" name="send" class="btn btn-primary btn-md fr" style="width: 100%;">' + lang_js_mod['user']['send_inv'] + '</button>' +
                                '<div class="clear"></div>' +
                            '</div>'+
                            //     '<div class="clear"></div>' +
                        '</div>' +
                        // '<script language="javascript"> ajax_send = false; imsUser.form_send_inv("form_send_inv");</script>' + 
                        '</form>',
                confirmButtonText: lang_js_mod['user']['send_inv'],
            	showCancelButton: false,
				preConfirm: () => {
					var form_mess = $('#form_send_inv').find('.form_mess');
					form_mess.stop(true,true).slideUp(200).html('');
					var fData = $("#form_send_inv").serializeArray();
					loading('show');
					$.ajax({
						type: "POST",
						url: ROOT+"ajax.php",
						data: { "m" : "user", "f" : "send_inv", "lang_cur" : lang, "data" : fData }
					}).done(function( string ) {
						loading('hide');
						var data = JSON.parse(string);
						// console.log(data);
						if(data.ok == 1) {
							$("#form_send_inv")[0].reset();
							form_mess.html(imsTemp.html_alert(data.mess,'success')).stop(true,true).slideDown(200);
						} else {
							form_mess.html(imsTemp.html_alert(data.mess,'error')).stop(true,true).slideDown(200);
						}
					});
					return false;
				}
            }).then(function (result) {
			    // $("#form_send_inv").submit();
			})
	        // $.jAlert({ 
	        //     'title': lang_js['share_link'],
	        //     'content' : '<form id="form_send_inv" name="form_report" method="post" action="" novalidate="novalidate" >' +
	        //                 '<div class="form_mess"></div>' +
	        //                 '<div class="send_no_product">' +
	        //                     lang_js['share_link_mail'] +
	        //                     '<input onClick="this.setSelectionRange(0, this.value.length)" name="name" type= "hidden" value="'+ link +'">' +
	        //                     '<div class="clear"></div>' +
	        //                     '<textarea style="margin-bottom: 10px;" name="content" type= "text" placeholder="' +  lang_js['placeholder_share_link'] + '"></textarea>' +
	        //                     // '<textarea name="content_more" type= "text" placeholder="' +  lang_js['placeholder_contentshare_link'] + '">'+ content_more +'</textarea>' +
	        //                     '<div class="bottom">' +
	        //                             '<button type="submit" name="send" class="btn btn-primary btn-md fr" style="width: 100%;">' + lang_js['send_inv'] + '</button>' +
	        //                             '<div class="clear"></div>' +
	        //                         '</div>'+
	        //                         '<div class="clear"></div>' +
	        //                 '</div>' +
	        //                 '<script language="javascript"> ajax_send = false; imsUser.form_send_inv("form_send_inv");</script>' + 
	        //                 '</form>',
	        //     'size': 'md',
	        //     'theme' : 'blue'
	        // });
	        ajax_send = false;
	    });
	},
	add_deeplink:function (form_id) {
        $("#"+form_id).validate({
            submitHandler: function() {
                var form_mess = $('#'+form_id).find('.form_mess');
                form_mess.stop(true,true).slideUp(200).html('');
                var fData = $('#'+form_id).serializeArray();

                loading('show');
                $.ajax({
                    type: "POST",
                    url: ROOT+"ajax.php",
                    data: { "m" : "user", "f" : "add_deeplink", "lang_cur" : lang, 'data': fData}
                }).done(function( string ) {
                    loading('hide');
                    var data = JSON.parse(string);
                    if(data.ok == 1) {
                        location.reload();
                    } else {
                    	Swal.fire({
		                    icon: 'error',
		                    title: lang_js['aleft_title'],
		                    html: data.mess,
		                })
                        // $.jAlert({
                        //     'title': 'Thông báo',
                        //     //'size': 'xsm',
                        //     'content':data.mess,
                        //     'animationTimeout': '10',
                        //     'theme': 'red',
                        //     'closeOnClick': true,
                        //     'blurBackground': true,
                        // })
                    }
                });
                return false;
            },
            rules: {
                link_source: {
                    required: true,
                },

            },
            messages: {
                link_source: {
                    required: '',
                },
            }
        });
    },
    delete_deeplink:function (item_id) {
        $.jAlert({
            'type': 'confirm',
            'confirmQuestion': 'Bạn có thực sự muốn xóa link này?',
            'confirmBtnText': 'Xóa',
            'denyBtnText': 'Hủy bỏ',
            'onConfirm': function(e, btn){
                $.ajax({
                    type: "POST",
                    url: ROOT + "ajax.php",
                    data: {"m": "user", "f": "delete_deeplink", "item_id": item_id}
                }).done(function (string) {
                    loading('hide');
                    var data = JSON.parse(string);
                    if(data.ok == 1) {
                        location.reload();
                    } else {
                    	Swal.fire({
		                    icon: 'error',
		                    title: lang_js['aleft_title'],
		                    html: data.mess,
		                })
                        // $.jAlert({
                        //     'title': 'Thông báo',
                        //     'size': 'sm',
                        //     'content':data.mess,
                        //     'animationTimeout': '10',
                        //     'theme': 'error',
                        //     'closeOnClick': true,
                        //     'blurBackground': true,
                        // })
                    }
                });
            }
        })
    },
    create_embed:function(url){
    	var iframe = '<iframe width="250" height="400" src="'+url+'" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';    	
		$("#embed_box textarea").html(iframe)
		$.fancybox.open({ src: $("#embed_box"), type : 'inline', clickSlide : false, });
    },
    cancelOrder: function(order_code){
		var form_mess = $('.form_mess');
			form_mess.stop(true,true).slideUp(200).html('');
		fancyConfirm({
		    title     : lang_js_mod['user']['cancelOrder_message_title'],
		    message   : lang_js_mod['user']['cancelOrder_message'],
		    okButton  : lang_js_mod['user']['cancelOrder_message_ok'],
		    noButton  : lang_js_mod['user']['cancelOrder_message_no'],
		    input 	  : '<p><label>'+lang_js_mod['user']['cancelOrder_reason']+'</label></p><p><textarea name="cancel_reason" style="width: 100%;"></textarea></p>',
		    inputName : "cancel_reason",
		    callback  : function (value) {
				if (value["btn"]) {
					var fData = [];
					fData.push({name: "order_code", value: order_code});
					Object.entries(value).forEach(([key, val]) => {
					  fData.push({name: key, value: val});
					})
					// loading('show');
					$.ajax({
						type: "POST",
						url: ROOT+"ajax.php",
						data: { "m" : "user", "f" : "cancel_order", "lang_cur" : lang, "data" : fData }
					}).done(function( string ) {
						console.log(string);
						loading('hide');			
						var data = JSON.parse(string);
						if(data.ok == 1) {
							Swal.fire({
			                    icon: 'success',
			                    title: lang_js['aleft_title'],
			                    html: data.mess,
			                });
			                $("#ims-content").load(window.location.href + " #ims-content>*",function(){});
							// form_mess.html(ttHTemp.html_alert(data.mess,'success')).stop(true,true).slideDown(200);
						} else {
							Swal.fire({
			                    icon: 'error',
			                    title: lang_js['aleft_title'],
			                    html: data.mess,
			                })							
						}
					});
				}
			}
		})
	},
	//create team event
	create_team:function(form_id, html_id){
    	$("#"+form_id).validate({
            submitHandler: function() {                
                var fData = $('#'+form_id).serializeArray();
                loading('show');
                $.ajax({
                    type: "POST",
                    url: ROOT+"ajax.php",
                    data: { "m" : "user", "f" : "create_team", "lang_cur" : lang, 'data': fData}
                }).done(function( string ) {
                    loading('hide');
                    var data = JSON.parse(string);
                    console.log(data);
                    if(data.ok == 1) {
                    	// $("#"+html_id).load(window.location.href + " #"+html_id+" > *");
                    	location.reload();
                    } else {
                    	Swal.fire({
		                    icon: 'error',
		                    title: lang_js['aleft_title'],
		                    html: data.mess,
		                })
                    }
                });
                return false;
            },
            rules: {
                
            },
            messages: {
                
            }
        });
    },
    //edit team event
	edit_team:function(form_id, html_id){		
    	$("#"+form_id).validate({
            submitHandler: function(e) {
                var fData = $("#"+form_id).serializeArray();                
                loading('show');
                $.ajax({
                    type: "POST",
                    url: ROOT+"ajax.php",
                    data: { "m" : "user", "f" : "edit_team", "lang_cur" : lang, 'data': fData}
                }).done(function( string ) {
                    loading('hide');
                    var data = JSON.parse(string);
                    console.log(data);
                    if(data.ok == 1) {
                    	Swal.fire({
		                    icon: 'success',
		                    title: lang_js['aleft_title'],
		                    html: data.mess,
		                }).then((result) => {
                            // $("#"+html_id).load(window.location.href + " #"+html_id+" > *");
                            location.reload();
                        });	                    	
                    } else {
                    	Swal.fire({
		                    icon: 'error',
		                    title: lang_js['aleft_title'],
		                    html: data.mess,
		                })
                    }
                });
                return false;
            },
            rules: {
                
            },
            messages: {
                
            }
        });
    },
};

function applyFilter(array){
    var url = window.location.href,
        check = (url.split('?')[1])?"&":"?",
        title = $(document).find("title").text(),
        params = '',
        arr_params = array,
        arr = [];
    arr_params.forEach(function(el){
        url = removeURLParameter(url,el);
    })
    if($(".item").hasClass('has-active-options')){
        $("input[type='checkbox']:checked").each(function(i,e){
            arr.push({name:$(e).attr("name"),value:$(e).val()});
        })
    }
    arr = groupBy(arr,'name',function (s, t) {
        t.value = t.value ? t.value + ',' + s.value : s.value;
    });
    arr.forEach(function(el){
        url = replaceUrlParam(url, el['name'], el['value']);
    })        
    if(params.length>0){
        params = check+params;
    }        
    console.log(url);
    history.pushState(null, title, url);
    loading("show");
    $("#box-recruitment").load(window.location.href + " #box-recruitment>   *",function(){
        addCheckbox();
        loading("hide");
    });
}

function checkin_guest(selector){
	setInterval(function(){		
		if($.qrCodeReader.instance.isOpen == true){
			var arr_code = $.qrCodeReader.instance.codes;	
			var result = '';			
			if($.qrCodeReader.instance.codes.length>0){
				result = Object.values($.qrCodeReader.instance.codes).pop();
				key = Object.keys($.qrCodeReader.instance.codes).pop();
				delete $.qrCodeReader.instance.codes[key];
			}
	        var link = $(selector).data('link'),
	        	event = $(selector).data('event');	        	     	
	        if(typeof result != 'undefined' && result.length>0){
	        	console.log(link + '/' + result + '/?event='+event);
	            var promise = ping(link + '/' + result + '/?event='+event);
	            promise.then(function(string){
	            	result = '';
	                var data = JSON.parse(string);
	                console.log(data);
	                if(data.ok == 1){
	                	Swal.fire({
						  	icon: 'success',
						  	html: '<h2 class="swal2-title">'+data.mess+'<h2>'
						  			+'<div class="ticket_info"><div class="inner">'
						  			+'<div class="info"><span>'+lang_js_mod['user']['full_name']+'</span>: <b>'+data.full_name+'</b></div>'
						  			+'<div class="info"><span>'+lang_js_mod['user']['phone']+'</span>: <b>'+data.phone+'</b></div>'
						  			+'<div class="info"><span>'+lang_js_mod['user']['email']+'</span>: <b>'+data.email+'</b></div>'
						  			+'<div class="info"><span>'+lang_js_mod['user']['age']+'</span>: <b>'+data.age+'</b></div>'
						  			+'</div></div>',
						});
	                }else{
	                	Swal.fire({
						  	icon: 'error',
						  	title: data.mess,
						});
	                }
	            })
	        }
		}		
    },1000);
}

function detectFaceCrop(info, title, form){
	loading('show');
	var fData = JSON.stringify(info.list_image);
	$.ajax({
        type: "POST",
        url: ROOT+"ajax.php",
        data: { "m" : "event", "f" : "detect_face", "lang_cur" : lang, 'data': fData, 'title': title}
    }).done(function( string ) {
        loading('hide');
        var data = JSON.parse(string);
        console.log(data);
        if(info.ok == 1){
        	if(form.length > 0){
        		form.find(".progress-bar").css({"width": "100%"});
        	}
	    	Swal.fire({
			  	icon: 'success',
			  	title: lang_js_mod['user']['upload_success'],
			  	html: info.mess,
			}).then((result) => {
                location.reload();
            })
        }else if(info.ok == 2){
        	if(form.length > 0){
        		form.find(".progress-bar").css({"width": "100%"});
        	}
        	Swal.fire({
			  	icon: 'info',
			  	title: lang_js_mod['user']['upload_success'],
			  	html: info.mess,
			}).then((result) => {
                location.reload();
            })
        } 
    });
}

function upload_image(selector){
	$(selector).each(function(){
        $(this).imageUploader({
            label: lang_js['upload_here'],
            caption: lang_js_mod['user']['caption'],
            mimes: ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
            extensions: ['.jpg', '.jpeg', '.png', '.gif', '.webp'],
            maxSize: 1024 * 1024 * 10, //10mb
            maxFiles: 'undefined',
        });

        var form = $(this).parents('form'),
        	event = $(this).data('id'),
        	type = $(this).data('type'),
        	title = $(this).data('title');
        form.on('submit', function(){
        	var fData = new FormData($(form)[0]);
        	var fileData = form.find('[type="file"]')[0].files;
 			fData.append("file", fileData);
 			fData.append("title", title);
 			fData.append("event", event);
 			fData.append("type", type);
			//Custom data
			fData.append('m', 'event');
			fData.append('f', 'upload_image');
			fData.append('lang_cur', lang);
			// for(var pair of fData.entries()) {
			// 	console.log(pair[0]+ ', '+ pair[1]); 
			// }	
			form.find(".label-title")[0].scrollIntoView({behavior: "smooth"});			
			$.ajax({
				xhr: function() {
			        var xhr = new window.XMLHttpRequest();
			        xhr.upload.addEventListener("progress", function(evt) {
			            if (evt.lengthComputable) {
			                var percentComplete = (evt.loaded / evt.total) * 100;
			                if(percentComplete>30){
			                	percentComplete -= 30;
			                }
			                form.find(".progress-bar").css({"width": percentComplete+"%"});
			                console.log(percentComplete);
			                //Do something with upload progress here
			            }
			       }, false);
			       return xhr;
			    },
			    type: "POST",
			    url: ROOT + "ajax.php",
			    data: fData,
			    contentType: false,
			    cache: false,
			    processData:false,
			}).done(function (string){
			    var data = JSON.parse(string);
			    if(data.ok == 1){
			    	if(data.list_image){
			    		form.find(".progress-bar").css({"width": "85%"});
		            	detectFaceCrop(data, title, form);
		            }else{
		            	form.find(".progress-bar").css({"width": "100%"});
		            	Swal.fire({
						  	icon: 'success',
						  	title: lang_js_mod['user']['upload_success'],
						  	html: info.mess,
						}).then((result) => {
			                location.reload();
			            })
		            }
			    }else if(data.ok == 2){
			    	if(data.list_image){
			    		form.find(".progress-bar").css({"width": "85%"});
		            	detectFaceCrop(data, title, form);
		            }else{
		            	form.find(".progress-bar").css({"width": "100%"});
		            	Swal.fire({
						  	icon: 'info',
						  	title: lang_js_mod['user']['upload_success'],
						  	html: info.mess,
						}).then((result) => {
			                location.reload();
			            })
		            }
			    }else{
			    	form.find(".progress-bar").css({"width": "100%"});
			    	Swal.fire({
					  	icon: 'error',
					  	title: lang_js_mod['user']['upload_false'],
					  	html: data.mess,
					}).then((result) => {
	                    location.reload();
	                })
			    }
			})
        	return false;
        })
    })
}

function update_image(form,id_parent){
	form = (form) ? form : 'form.form_update';
	var event = 0,
		type = '';
	if ($(form).length) {                
        $(form).on("click",'.btn-update',function () {
        	event = $(this).data('id');
            type = 'update';
        });
        $(form).on("click",'.btn-remove',function () {
        	event = $(this).data('id');
            type = 'remove';
        });
        $(form).submit(function (e) {
        	var fData = $(form+" .has-change").serializeArray();        	
        	loading("show");
	        $.ajax({
	            type: "POST",
	            url: ROOT + "ajax.php",
	            data: {"m": "event", "f": "update_image", "data": fData, "type": type, "event": event}
	        }).done(function (string) {
	            var data = JSON.parse(string);	            
	            loading("hide");
	            if(data.ok == 1){
			    	Swal.fire({
					  	icon: 'success',
					  	title: lang_js_mod['user']['update_success'],
					  	html: data.mess,
					}).then((result) => {
	                    location.reload();
	                })
			    }else if(data.ok == 2){
			    	Swal.fire({
					  	icon: 'info',
					  	title: lang_js_mod['user']['update_success'],
					  	html: data.mess,
					}).then((result) => {
	                    location.reload();
	                })
			    }else{
			    	Swal.fire({
					  	icon: 'error',
					  	title: lang_js_mod['user']['update_false'],
					  	html: data.mess,
					});
			    }
	        });
        	return false;	
        })
    }

    $(document).on("click change paste keyup", "#"+id_parent+" input:not(.id_checkbox), #"+id_parent+" textarea", function(){
    	var checkbox = $(this).parents(".item").find(".checkbox input[type=checkbox]");    	
		checkbox.prop("checked", true);
		$(this).parents(".item").find("input").addClass("has-change");
		$(this).parents(".item").find("textarea").addClass("has-change");
	})

    $(document).on("click", ".id_checkbox", function(){
    	$(this).addClass("has-change");
    })

	$(document).on("click", "#"+id_parent+" .edit-image", function(){
		var parent = $(this).parents(".tab-pane"),
			list = $(this).parents(".tab-pane").find(".list_image");			
		$("#image_manager .checkbox input").prop("checked", false);
		list.removeClass("show_remove");
		if(list.hasClass("show_edit")){
			list.removeClass("show_edit");
		}else{
			list.addClass("show_edit");
		}
	})

	$(document).on("click", "#"+id_parent+" .remove-image", function(){
		var parent = $(this).parents(".tab-pane"),
			list = $(this).parents(".tab-pane").find(".list_image");
		$("#image_manager .checkbox input").prop("checked", false);
		list.removeClass("show_edit");
		if(list.hasClass("show_remove")){
			list.removeClass("show_remove");
		}else{
			list.addClass("show_remove");
		}
	})

	$(document).on("click", "#"+id_parent+" .btn-cancel", function(){
		var parent = $(this).parents(".tab-pane"),
			id = parent.attr("id");
		location.reload();			
		// $("#"+id).load(window.location.href + " #"+id+" > *", function(){
		// 	$('[data-toggle="toggle"]').bootstrapToggle();
		// 	upload_image('.input-images');
		// });
	})
}

function do_check(selector){
	var checkbox = $(selector).parents(".item").find(".id_checkbox");
	checkbox.prop("checked", true);
}

$(document).ready(function() {
    $("#form_ordering_address").on("click","#addNewAddress,.edit-address",function(){
    	var data = $(this).attr("data-id");
        loading("show");
        $.ajax({
            type: "POST",
            url: ROOT + "ajax.php",
            data: {"m": "user", "f": "load_form_address", "data": data}
        }).done(function (string) {
            var data = JSON.parse(string);
            loading("hide");
            $(".ordering_address").find(".address-form").html(data);
            $("html, body").animate({
                scrollTop: $(".address-form").offset().top-50,
            }, 700);
		    imsLocation.locationChange("province", ".select_location_province_d");
		    imsLocation.locationChange("district", ".select_location_district_d");
        });
    })
    $("#form_ordering_address").on("click",".delete-address",function(){
    	var data = $(this).attr("data-id");
    	fancyConfirm({
		    title     : lang_js_mod['user']['delete_message_title'],
		    message   : '',
		    okButton  : lang_js_mod['user']['delete_message_ok'],
		    noButton  : lang_js_mod['user']['delete_message_no'],
		    boxClass  : 'confirm_delete',
		    callback  : function (value) {
				if (value) {
					loading('show');
					$.ajax({
			            type: "POST",
			            url: ROOT + "ajax.php",
			            data: {"m": "user", "f": "delete_address", "data": data}
			        }).done(function (string) {
			        	console.log(string);
			        	$( "#form_ordering_address" ).load(window.location.href + " #form_ordering_address" );
			            loading("hide");
			        });
				}
	    	}
	 	});
    })
    $("#checkall").on("click", function () {
	    $(".select input[type=\"checkbox\"]").prop("checked", this.checked);
	    var focus = $(this).parents("form").find(".row_notification");	    
	    if($("#checkall").is(":checked")){	    	
	    	focus.addClass("focus");
	    }else{
	    	focus.removeClass("focus");
	    }
	})
	$(".select:not(.select-all) input[type=\"checkbox\"]").each(function(){
		var focus = $(this).parents(".row_notification");
		$(this).on("click",function(){			
			if(focus.hasClass("focus")){
		    	focus.removeClass("focus");
		    }else{
		    	focus.addClass("focus");
		    }
		})
	})
	$(document).on("click", ".manager_save_later .delete_item", function(){
		var id = $(this).data("id");
		loading('show');
		$.ajax({
            type: "POST",
            url: ROOT + "ajax.php",
            data: {"m": "user", "f": "removeSaveLater", "id": id}
        }).done(function (string) {     
        	var data = JSON.parse(string);
            loading('hide');
            if (data.ok == 1) {
                location.reload();
            }else{
            	Swal.fire({
				  	icon: 'error',
				  	title: lang_js['aleft_title'],
				  	html: data.mess,
				});
	        }
        });
	});
	$(".affiliate").on("click",".copy_link",function(){	
		$(this).CopyToClipboard();
		$(this).focus().select();	
	})
	$("#embed_box").on("click",".btn-copy",function(){
		var code = $(this).parents("#embed_box").find("textarea");
		code.CopyToClipboard();
		code.focus().select();
	})

	$(document).on("click", "#table_statistic .nav-link", function(){
		var type = $(this).attr("data-type"),
			title = $(document).find("title").text(),
			url = document.location.href;	
		url = replaceUrlParam(url, "type", type);
		go_link(url);
	})

	$(document).on("click", ".btn-filter", function(){
		var fData = $(this).parents("form").serializeArray(),
			title = $(document).find("title").text(),
			url = document.location.href;
		$(fData).each(function(i, e){
			url = replaceUrlParam(url, e.name, e.value);
		})
		go_link(url);
	})

	$(document).on("click", ".edit-team", function(){
		var table = $("#table_statistic table"),
			box = $(".box_editteam"),
			title = $(document).find("title").text(),
			url = document.location.href;
		if($(this).attr("href") == "#"){
			if(table.hasClass("show_edit")){
				table.removeClass("show_edit");
				box.removeClass("show_edit");
				url = removeURLParameter(url, "edit");
	        	history.pushState(null, title, url);
	        	$(".paginate").load(window.location.href + " .paginate > *");
			}else{
				table.addClass("show_edit");
				box.addClass("show_edit");
				url = replaceUrlParam(url, "edit", "1");
	        	history.pushState(null, title, url);
	        	$(".paginate").load(window.location.href + " .paginate > *");
			}
		}
	})

	$(document).on("click", ".box_editteam .btn-update", function(){
		var event_id = $(this).attr("data-id"),
			team = $("[name='team']").val(),
			ticket = [];
		$("#table_statistic .checkbox:not(.all):checked").each(function(i, e){			
			ticket[i] = $(e).val();
		})
		var fData = [];
			fData.push({name: "event_id", value:event_id});
			fData.push({name: "team", value:team});
			fData.push({name: "ticket", value:JSON.stringify(ticket)});
		loading("show");
		$.ajax({
            type: "POST",
            url: ROOT + "ajax.php",
            data: {"m": "user", "f": "update_team", "data": fData}
        }).done(function (string) {        	
        	loading("hide");
        	var data = JSON.parse(string);
            if(data.ok==1){
        		Swal.fire({
                    icon: 'success',
                    title: lang_js['aleft_title'],
                    html: data.mess,
                }).then((result) => {
                    location.reload();
                });	
			}else{
				Swal.fire({
                    icon: 'error',
                    title: lang_js['aleft_title'],
                    html: data.mess,
                })
			}
        });
	});

	$(document).on("click", ".btn-export", function(){
		var wre = $(this).attr("data-wre"),
			title = $(this).attr("data-title");
		$.ajax({
            type: "POST",
            url: ROOT + "ajax.php",
            data: {"m": "user", "f": "export_excel_ticket", "wre": wre, "title": title}
        }).done(function (string) {
        	var data = JSON.parse(string);
        	console.log(data);
        	var $a = $("<a>");
		    $a.attr("href",data.file);
		    $("body").append($a);
		    $a.attr("download",data.filename+".xls");
		    console.log($a)
		    $a[0].click();
		    $a.remove();
        });
	})

	$(document).on("change", "#file_attach", function(){
		var name = $(this).val().split('\\').pop();
		$(this).prev(".filename").text(name);
	})
	$(document).on("click", ".btn-import", function(){
		var id = $(this).attr('data-id');
		$("#importExcel").validate({
			submitHandler: function() {
				loading('show');
				formData = new FormData($("#importExcel")[0]);
                formData.append("f", "import_excel_ticket");
                formData.append("m", "user");
                formData.append("lang_cur", lang);
                formData.append("event_id", id);
				$.ajax({
                    type: 'POST',
                    url: ROOT+"ajax.php",
                    data: formData,
                    contentType: false,
                    cache: false,
                    processData:false,
				}).done(function( string ) {
					loading('hide');
					var data = JSON.parse(string);
					console.log(data);
					if(data.ok == 1) {
		              	$('.modal').modal('hide');
		                setTimeout(function(){ 
			                Swal.fire({
			                    type: 'success',
			                    title: lang_js['aleft_title'],
			                    html: data.mess,
			                }).then((result) => {
			                    location.reload();
			                });;
		                }, 300);
					} else {
						Swal.fire({
                            type: 'error',
                            title: lang_js['aleft_title'],
                            html: data.mess,
                        })
					}
				});
				return false;
			},
		});
	})
});
