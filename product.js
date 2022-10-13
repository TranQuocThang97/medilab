imsProduct = {
	importProduct:function(form_id){
		$("#"+form_id).validate({
			submitHandler: function() {
				loading('show');
				formData = new FormData($("#"+form_id)[0]);
                formData.append("f", "importProduct");
                formData.append("m", "product");
                formData.append("lang_cur", lang);
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
		              	$('.modal').modal('hide');
		                setTimeout(function(){ 
			                Swal.fire({
			                    type: 'success',
			                    title: lang_js['success'],
			                    text: data.mess,
			                });
		                }, 300);
                        if (typeof load_admin_paginate !== 'undefined' && $.isFunction(load_admin_paginate)) {
	                        // Load manage
	                        load_admin_paginate($('.menu_ajax_select').data('p'));
	                    }
						loadPage();
					} else {
						Swal.fire({
                            type: 'error',
                            title: lang_js['An_error_occurred'],
                            text: data.mess,
                        })
					}
				});
				return false;
			},
		});
	},

	confirmsortOption:function(form_id){
		$("#"+form_id).validate({
			submitHandler: function() {
				var fData = $("#"+form_id).serializeArray();
				loading('show');
				$.ajax({
					type: "POST",
					url: ROOT+"ajax.php",
					data: { "m" : "product", "f" : "confirmsortOption", "lang_cur" : lang, "data" : fData}
				}).done(function( string ) {
					loading('hide');
					var data = JSON.parse(string);
					if(data.ok == 1) {
		                $.fancybox.close();
		                setTimeout(function(){ 
			                Swal.fire({
			                    type: 'success',
			                    title: lang_js['success'],
			                    text: data.mess,
			                });
		                }, 500);
		                $('table.ShowAllVariant').html(data.html);
						loadPage();
        				selectColor();
					} else {
						Swal.fire({
                            type: 'error',
                            title: lang_js['An_error_occurred'],
                            text: data.mess,
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

	sortOption:function () {
		var checkClick = false;
        $(document).on('click', '.sortOption', function() {
        	ProductId = $(this).data('productid');
        	if ( checkClick == true) { return false; }
            NProgress.start();
			checkClick = true;
			$.ajax({
				type: "POST",
				url: ROOT+"ajax.php",
				data: { "m" : "product", "f" : "sortOption", "ProductId" : ProductId, "lang_cur" : lang}
			}).done(function( string ) {
				checkClick = false;
            	NProgress.done();
				var data = JSON.parse(string);
				if(data.ok == 1) {
					$.fancybox.open(data.html, { touch: false });
					$( function() {
			            $("#sortable").sortable({placeholder: "ui-state-highlight"});
			            $("#sortable").disableSelection();
			            $(".sortable-option").sortable({});
			            $(".sortable-option").disableSelection();
			        });
					imsProduct.confirmsortOption("formmyModal");
				}else{
					Swal.fire({
	                    type: 'error',
	                    title: lang_js['An_error_occurred'],
	                    text: data.mess,
	                })
				}
			});
		});
	},

	confirmeditOptionList:function(form_id){     
		$("#"+form_id).validate({
			submitHandler: function() {
				var fData = $("#"+form_id).serializeArray();
				loading('show');
				$.ajax({
					type: "POST",
					url: ROOT+"ajax.php",
					data: { "m" : "product", "f" : "confirmeditOptionList", "lang_cur" : lang, "data" : fData}
				}).done(function( string ) {
					loading('hide');
					var data = JSON.parse(string);
					if(data.ok == 1) {
		                $.fancybox.close();
		                setTimeout(function() {
			                Swal.fire({
			                    type: 'success',
			                    title: lang_js['success'],
			                    text: data.mess,
			                });
		                }, 500);
		                $('table.ShowAllVariant').html(data.html);
						loadPage();
        				selectColor();
						setTimeout(function() {
							location.reload();
						}, 1500);
					} else {
						Swal.fire({
                            type: 'error',
                            title: lang_js['An_error_occurred'],
                            text: data.mess,
                        })
					}
				});
				return false;
			},
			rules: {
				'OptionList[1][Tags]': { required: true, },
				'OptionList[2][Tags]': { required: true, },
				'OptionList[3][Tags]': { required: true, },
			},
			messages: {
				'OptionList[1][Tags]': lang_js['err_valid_input'],
				'OptionList[2][Tags]': lang_js['err_valid_input'],
				'OptionList[3][Tags]': lang_js['err_valid_input'],
			}
		});
	},

	editOptionList:function () {
		var checkClick = false;
        $(document).on('click', '.editOptionList', function(){
        	ProductId = $(this).data('productid');
        	if ( checkClick == true) { return false; }
            NProgress.start();
			checkClick = true;
			$.ajax({
				type: "POST",
				url: ROOT+"ajax.php",
				data: { "m" : "product", "f" : "editOptionList", "ProductId" : ProductId, "lang_cur" : lang}
			}).done(function( string ) {
				checkClick = false;
            	NProgress.done();
				var data = JSON.parse(string);
				if(data.ok == 1) {
					$.fancybox.open(data.html, { touch: false });
					imsProduct.confirmeditOptionList("formmyModal");
					loadPage();
					setSelectOptionPopup();
					$("#myModal").on('hidden.bs.modal', function () {
				        $('#ims-data').html('');
				        amutop(); return false;
				    });
				}else{
					Swal.fire({
	                    type: 'error',
	                    title: lang_js['An_error_occurred'],
	                    text: data.mess,
	                })
				}
			});
		});
	},

	confirmEditOption:function(form_id,type){
		// Change useWarehouse
		$(document).on("change", 'select[name="useWarehouse"]', function(){
			var val = $(this).val();
			if (val == 1) {
				$('.check_trackingpolicy').removeClass('d-none');
			}else{
				$('.check_trackingpolicy').addClass('d-none');
			}
		});
		$("#"+form_id).validate({
			submitHandler: function() {
				var fData = $("#"+form_id).serializeArray();
				loading('show');
				$.ajax({
					type: "POST",
					url: ROOT+"ajax.php",
					data: { "m" : "product", "f" : "confirmEditOption", "lang_cur" : lang, "data" : fData , "type" : type}
				}).done(function( string ) {
					loading('hide');
					var data = JSON.parse(string);
					if(data.ok == 1) {
		                $.fancybox.close();
		                setTimeout(function(){ 
			                Swal.fire({
			                    type: 'success',
			                    title: lang_js['success'],
			                    text: data.mess,
			                });
		                }, 500);
		                $('table.ShowAllVariant').html(data.html);
		                loadPage();
		                selectColor();
					} else {
						Swal.fire({
                            type: 'error',
                            title: lang_js['An_error_occurred'],
                            text: data.mess,
                        })
					}
				});
				return false;
			},
			rules: {
				Option1: { required: true, },
				Option2: { required: true, },
				Option3: { required: true, },
				weight: { required: true, },
				length: { required: true, },
				width: { required: true, },
				height: { required: true, },
				
			},
			messages: {
				Option1: lang_js['err_valid_input'],
				Option2: lang_js['err_valid_input'],
				Option3: lang_js['err_valid_input'],
				weight: lang_js['err_valid_input'],
				length: lang_js['err_valid_input'],
				width: lang_js['err_valid_input'],
				height: lang_js['err_valid_input'],
			}
		});
	},

	editOption:function () {
		var checkClick = false;
        $(document).on('click', '.editOption', function(){
        	ProductId = $(this).data('productid');
        	id = $(this).data('id');
        	type = $(this).data('type');
        	stt = $(this).data('stt');
        	if ( checkClick == true) { return false; }
            NProgress.start();
			checkClick = true;
			$.ajax({
				type: "POST",
				url: ROOT+"ajax.php",
				data: { "m" : "product", "f" : "editOption", "id" : id, "type" : type,"stt" : stt, "lang_cur" : lang, "ProductId" : ProductId}
			}).done(function( string ) {
				checkClick = false;
            	NProgress.done();
				var data = JSON.parse(string);
				if(data.ok == 1) {
				    $.fancybox.open(data.html, { touch: false });
					imsProduct.confirmEditOption("formmyModal", type);
					loadPage();
        			selectColor();
				}else{
					Swal.fire({
	                    type: 'error',
	                    title: lang_js['An_error_occurred'],
	                    text: data.mess,
	                })
				}
			});
		});
	},

	delOption:function(){
		$(document).on("click", '.delOption', function(){
        	id = $(this).data('id');
			var mess_warning_title = lang_js['are_you_sure_del'];
	        var mess_warning_text = $(this).data('warning');
	        const swalWithBootstrapButtons = Swal.mixin({
	            customClass: {
	                confirmButton: 'btn btn-success',
	                cancelButton: 'btn btn-default'
	            },
	            buttonsStyling: false
	        })
	        swalWithBootstrapButtons.fire({
	            title: mess_warning_title,
	            text: mess_warning_text,
	            type: 'warning',
	            showCancelButton: true,
	            confirmButtonText: 'Có',
	            cancelButtonText: 'Không',
	            reverseButtons: true
	        }).then((result) => {
	            if (result.value) {
	                if ( !id ) { return false; }
            		NProgress.start();
					$.ajax({
						type: "POST",
						url: ROOT+"ajax.php",
						data: { "m" : "product", "f" : "delOption", "lang_cur" : lang, "id" : id }
					}).done(function( string ) {
            			NProgress.done();
						var data = JSON.parse(string);
						if(data.ok == 1) {
			                $('table.ShowAllVariant').html(data.html);
							loadPage();
        					selectColor();
						} else {
							Swal.fire({
	                            type: 'error',
	                            title: lang_js['An_error_occurred'],
	                            text: data.mess,
	                        })
						}
					});
					return false;
	            }
	        });
		});
    },

    delMutiOption:function(ProductId){
		$(document).on("click", '.delMutiOption', function(){
			// Selected
			var selectArray = [];
			$(".box-variants-detail table.ShowAllVariant tr.itemOrder input:checkbox:checked").each(function(){
			    selectArray.push($(this).val());
			});
			var mess_warning_title = lang_js['are_you_sure_del'];
	        var mess_warning_text = $(this).data('warning');
	        const swalWithBootstrapButtons = Swal.mixin({
	            customClass: {
	                confirmButton: 'btn btn-success',
	                cancelButton: 'btn btn-default'
	            },
	            buttonsStyling: false
	        })
	        swalWithBootstrapButtons.fire({
	            title: mess_warning_title,
	            text: mess_warning_text,
	            type: 'warning',
	            showCancelButton: true,
	            confirmButtonText: 'Có',
	            cancelButtonText: 'Không',
	            reverseButtons: true
	        }).then((result) => {
	            if (result.value) {
	                if (!selectArray) { return false; }
            		NProgress.start();
					$.ajax({
						type: "POST",
						url: ROOT+"ajax.php",
						data: { "m" : "product", "f" : "delMutiOption", "lang_cur" : lang, "select" : selectArray, "ProductId": ProductId}
					}).done(function( string ) {
            			NProgress.done();
						var data = JSON.parse(string);
						if(data.ok == 1) {
			                $('.modal').modal('hide');
			                setTimeout(function(){ 
				                Swal.fire({
				                    type: 'success',
				                    title: lang_js['success'],
				                    text: data.mess,
				                });
			                }, 300);
			                $('table.ShowAllVariant').html(data.html);
							loadPage();
        					selectColor();
						} else {
							Swal.fire({
	                            type: 'error',
	                            title: lang_js['An_error_occurred'],
	                            text: data.mess,
	                        })
						}
					});
					return false;
	            }
	        });
		});
    },

    change_prices:function(form_id){
    	$("#"+form_id).validate({
			submitHandler: function() {
				var fData = $("#"+form_id).serializeArray();
				var selectArray = [];
				$(".box-variants-detail table.ShowAllVariant tr.itemOrder input:checkbox:checked").each(function(){
				    selectArray.push($(this).val());
				});
				loading('show');
				$.ajax({
					type: "POST",
					url: ROOT+"ajax.php",
					data: { "m" : "product", "f" : "change_prices", "lang_cur" : lang, "data" : fData, "select" : selectArray}
				}).done(function( string ) {
					loading('hide');
					var data = JSON.parse(string);
					if(data.ok == 1) {
		              	$('.modal').modal('hide');
		                setTimeout(function(){ 
			                Swal.fire({
			                    type: 'success',
			                    title: lang_js['success'],
			                    text: data.mess,
			                });
		                }, 300);
		                $('table.ShowAllVariant').html(data.html);
						loadPage();
        				selectColor();
					} else {
						Swal.fire({
                            type: 'error',
                            title: lang_js['An_error_occurred'],
                            text: data.mess,
                        })
					}
				});
				return false;
			},
			rules: {
				Price: { required: true, },
			},
			messages: {
				Price: lang_js['err_valid_input'],
			}
		});
    },

    change_pricesBuy:function(form_id){
    	$("#"+form_id).validate({
			submitHandler: function() {
				var fData = $("#"+form_id).serializeArray();
				var selectArray = [];
				$(".box-variants-detail table.ShowAllVariant tr.itemOrder input:checkbox:checked").each(function(){
				    selectArray.push($(this).val());
				});
				loading('show');
				$.ajax({
					type: "POST",
					url: ROOT+"ajax.php",
					data: { "m" : "product", "f" : "change_pricesBuy", "lang_cur" : lang, "data" : fData, "select" : selectArray}
				}).done(function( string ) {
					loading('hide');
					var data = JSON.parse(string);
					if(data.ok == 1) {
		              	$('.modal').modal('hide');
		                setTimeout(function(){ 
			                Swal.fire({
			                    type: 'success',
			                    title: lang_js['success'],
			                    text: data.mess,
			                });
		                }, 300);
		                $('table.ShowAllVariant').html(data.html);
						loadPage();
						selectColor();
					} else {
						Swal.fire({
                            type: 'error',
                            title: lang_js['An_error_occurred'],
                            text: data.mess,
                        })
					}
				});
				return false;
			},
			rules: {
				PriceBuy: { required: true, },
			},
			messages: {
				PriceBuy: lang_js['err_valid_input'],
			}
		});
    },

    update_quantity:function(form_id){
    	$("#"+form_id).validate({
			submitHandler: function() {
				var fData = $("#"+form_id).serializeArray();
				var selectArray = [];
				$(".box-variants-detail table.ShowAllVariant tr.itemOrder input:checkbox:checked").each(function(){
				    selectArray.push($(this).val());
				});
				loading('show');
				$.ajax({
					type: "POST",
					url: ROOT+"ajax.php",
					data: { "m" : "product", "f" : "update_quantity", "lang_cur" : lang, "data" : fData, "select" : selectArray}
				}).done(function( string ) {
					loading('hide');
					var data = JSON.parse(string);
					if(data.ok == 1) {
		              	$('.modal').modal('hide');
		                setTimeout(function(){ 
			                Swal.fire({
			                    type: 'success',
			                    title: lang_js['success'],
			                    text: data.mess,
			                });
		                }, 300);
		                $('table.ShowAllVariant').html(data.html);
						loadPage();
						selectColor();
					} else {
						Swal.fire({
                            type: 'error',
                            title: lang_js['An_error_occurred'],
                            text: data.mess,
                        })
					}
				});
				return false;
			},
			rules: {
				Quantity: { required: true, },
			},
			messages: {
				Quantity: lang_js['err_valid_input'],
			}
		});
    },

    update_quantity_custom:function(form_id){
    	$("#"+form_id).validate({
			submitHandler: function() {
				var fData = $("#"+form_id).serializeArray();
            	NProgress.start();
				$.ajax({
					type: "POST",
					url: ROOT+"ajax.php",
					data: { "m" : "product", "f" : "update_quantity_custom", "lang_cur" : lang, "data" : fData}
				}).done(function( string ) {
            		NProgress.done();
					var data = JSON.parse(string);
					if(data.ok == 1) {
						$('#row_'+data.id+' .Quantity .num_old').text(data.Quantity);
						$('#row_'+data.id+' input[name="Quantity"]').val(0);
						$('#row_'+data.id+' input[name="Quantity_old"]').val(data.Quantity);
		                setTimeout(function(){ 
			                Swal.fire({
			                    type: 'success',
			                    title: lang_js['success'],
			                    text: data.mess,
			                });
		                }, 300);
		                $('.show_change').addClass('d-none');
		                $('.show_change.num_new').text(0);
					} else {
						Swal.fire({
                            type: 'error',
                            title: lang_js['An_error_occurred'],
                            text: data.mess,
                        })
					}
				});
				return false;
			},
			rules: {
				Quantity: { required: true, },
			},
			messages: {
				Quantity: lang_js['err_valid_input'],
			}
		});
    },
}

var listOptionSelected = [];
var listOption = {};
listOption['Title']     = 'Tiêu đề';
listOption['Size']      = 'Kích thước';
listOption['Color']     = 'Màu sắc';
listOption['Material']  = 'Chất liệu';
listOption['Style']     = 'Hình dạng';
listOption['Custom']    = 'Tạo tùy chọn mới';
var listDemoData = {};
listDemoData['Title']    = ['Default Title'];
listDemoData['Size']     = ['S','M','L','XL','XXL'];
listDemoData['Color']    = ['Cam','Hồng','Tím','Trắng','Xanh','Đen','Đỏ'];
listDemoData['Material'] = ['Sắt','Thép','Đồng','Nhôm','Vàng'];
listDemoData['Style']    = ['Tròn','Vuông','Dài'];
listDemoData['Custom']   = ['64GB','128GB','256GB','1T'];

imsOption = {
	createTable: function(data_color) {
		var color = data_color;
        var key_option1 = '';
        var key_option2 = '';
        var key_option3 = '';
        $.each($(".ver-name input[name^='OptionList']"), function (key, val) {
            if (key == 0) { key_option1 = $(this).val(); }
            if (key == 1) { key_option2 = $(this).val(); }
            if (key == 2) { key_option3 = $(this).val(); }
        });

        var option1 = [];
        var option2 = [];
        var option3 = [];
        $.each($(".tagsinput"), function (key, val) {
            if (key == 0) { option1 = $(this).tagsinput('items'); }
            if (key == 1) { option2 = $(this).tagsinput('items'); }
            if (key == 2) { option3 = $(this).tagsinput('items'); }
        });
        if (option1 == '') { option1 = [" "]}
        if (option2 == '') { option2 = [" "]}
        if (option3 == '') { option3 = [" "]}
        var listItems = [];
        var stt = 0;
        $.each(option1, function (k1, v1) {
            v1_original = v1
            if (key_option1 == 'Color') { 
                v1 = color[v1];
            }
            $.each(option2, function (k2, v2) {
                v2_original = v2
                if (key_option2 == 'Color') { 
                    v2 = color[v2];
                }
                $.each(option3, function (k3, v3) {
                    v3_original = v3
                    if (key_option3 == 'Color') { 
                        v3 = color[v3]; 
                    }
                    listItems[stt] = [];
                    if (key_option1 == 'Color') { 
                        listItems[stt]["Option1Color"] = v1_original;
                    }
                    if (key_option2 == 'Color') { 
                        listItems[stt]["Option2Color"] = v2_original;
                    }
                    if (key_option3 == 'Color') { 
                        listItems[stt]["Option3Color"] = v3_original;
                    }
                    v1 = v1.trim();
                    v2 = v2.trim();
                    v3 = v3.trim();
                    titleItem ='';
                    if (v1!="") {
                    	titleItem = v1;
                    }
                    if (v2!="") {
                    	titleItem = titleItem + ' / ' + v2;
                    }
                    if (v3!="") {
                    	titleItem = titleItem + ' / ' + v3;
                    }
                    listItems[stt]["Title"] = titleItem;
                    listItems[stt]["Option1"] = v1;
                    listItems[stt]["Option2"] = v2;
                    listItems[stt]["Option3"] = v3;
                    stt++;
                });
            });
        });
        if (typeof listItems !== 'undefined' && listItems.length > 0) {
            var html_items = '';
            var item_code_root = $('input[name="item_code"]').val();
            var barcode = $('input[name="barcode"]').val();
            var price = $('input[name="price"]').val();
            var pricebuy = $('input[name="price_buy"]').val();
            var quantity = $('input[name="in_stock"]').val();
            var useWarehouse = $('select[name="useWarehouse"]').val();
            if (price == '') { price = 0; }
            var order = 0;
            var clasQuantity = 'd-none';
            if (useWarehouse == 1) { clasQuantity = ''; }
            $.each(listItems, function (k_item, v_item) {
                order++;
                var item_code = item_code_root+'-'+order;
                var title     = listItems[k_item]['Title'];
                var val_op1   = listItems[k_item]['Option1'];
                var val_op2   = listItems[k_item]['Option2'];
                var val_op3   = listItems[k_item]['Option3'];
                if (key_option1 == 'Color') {
                    var val_op1   = listItems[k_item]['Option1Color'];
                }else if(key_option2 == 'Color'){
                    var val_op2   = listItems[k_item]['Option2Color'];
                }else if(key_option3 == 'Color'){
                    var val_op3   = listItems[k_item]['Option3Color'];
                }
                html_items += 
                    '<tr class="itemOrder itemOrder'+order+'">'+
                        '<td>'+
                            '<input id="cbitemOrder_'+order+'" class="checkbox" type="checkbox" value="1" name="Option['+order+'][SelectedId]" checked>'+
                            '<label for="cbitemOrder_'+order+'"></label>'+
                        '</td>'+
                        '<td>'+
                            '<input type="hidden" name="Option['+order+'][Name]" value="'+title+'" style="display:hidden;">'+
                            '<input type="hidden" name="Option['+order+'][Option1]" value="'+val_op1+'" style="display:hidden;">'+
                            '<input type="hidden" name="Option['+order+'][Option2]" value="'+val_op2+'" style="display:hidden;">'+
                            '<input type="hidden" name="Option['+order+'][Option3]" value="'+val_op3+'" style="display:hidden;">'+
                            '<span class="OptionNameDisplay">'+ listItems[k_item]['Title'] +'</span>'+
                        '</td>'+
                        '<td>'+
                            '<input type="text" size="50" value="'+pricebuy+'" class="form-control price PriceBuyOption">'+
                            '<input name="Option['+order+'][PriceBuy]" type="hidden" value="'+pricebuy+'" class="price_input">'+

                            '<input style="display: none;" type="text" size="50" value="'+price+'" class="form-control price PriceOption">'+
                            '<input style="display: none;" name="Option['+order+'][Price]" type="hidden" value="'+price+'" class="price_input">'+

                        '</td>'+
                        '<td>'+
                            '<input type="text" class="form-control SKUOption" name="Option['+order+'][SKU]" value="'+item_code+'">'+
                        '</td>'+
                        '<td>'+
                            '<input type="text" class="form-control BarcodeOption" name="Option['+order+'][Barcode]" value="'+barcode+'">'+
                        '</td>'+
                        '<td class="ShowQuantity '+clasQuantity+'">'+
                            '<input type="number" min="0" class="form-control QuantityOption" name="Option['+order+'][Quantity]" value="'+quantity+'">'+
                        '</td>'+
                    '</tr>'+
                '';
            });
            $('table.ShowAllVariant tbody').html(html_items);
        }
        auto_price_format();
	},
	loadListOption: function(data_color='', select_color='', type=''){
	    imsOption.removeVariant(type);
	    imsOption.addOtherOption(select_color);
	    imsOption.changeSelectOption(select_color, type);

	    // Change barcode
        $(document).on('change', 'input[name="barcode"]', function(){
            $('.BarcodeOption').val($(this).val());
        });

        // Change item_code
        $(document).on('change', 'input[name="item_code"]', function(){
            imsOption.createTable(data_color);
        });
        
        // Change input price 
        $(document).on('change', '.div_price input.price', function(){
            imsOption.createTable(data_color);
        });

        // Change input price buy
        $(document).on('change', '.div_price_buy input.price', function(){
            imsOption.createTable(data_color);
        });

        // Change Select Color
        $(document).on('change', 'select.form-control-color', function(event) {
            var list = $(this).val();
            $('.box_color .tagsinput').tagsinput('removeAll');
            $.each(list, function (key, val) {
                $('.box_color .tagsinput').tagsinput('add', val);
            });
            imsOption.checkMaxOption();
            imsOption.setSelectOption();
            imsOption.createTable(data_color);
        });

        // Change useWarehouse
        $(document).on('change', 'select[name="useWarehouse"]', function(){
            $('.ShowQuantity').removeClass('d-none');
            if ($(this).val() == 1) {
                $('.check_trackingpolicy').removeClass('d-none');
            }else{
                $('.ShowQuantity').addClass('d-none');
                $('.check_trackingpolicy').addClass('d-none');
            }
        });

        $(document).on('itemRemoved', '.tagsinput', function(event) {
            $.each($('.tag-item'), function (key, val) {
                if (event.item == $(this).data('value')) {
                    $(this).removeClass('disabled');
                }
            });
            imsOption.createTable(data_color);
        });
        $(document).on('itemAdded', '.tagsinput', function(event) {
            imsOption.createTable(data_color);
        });

        // Add/ remove tags
	    $(document).on('click', '.tags .item', function(){
	        var text = $(this).text();
	        var element = $(this).parent().parent().parent().parent().parent().parent();
	        if($(this).hasClass('disabled')) {
	            $(this).removeClass('disabled');
	            element.find('.tagsinput').tagsinput('remove', text);
	        }else{
	            $(this).addClass('disabled');
	            element.find('.tagsinput').tagsinput('add', text);
	        }
	    });
   
	    $('#myForm').on('keyup keypress', function(e) {
	        var keyCode = e.keyCode || e.which;
	        if (keyCode === 13) { 
	            e.preventDefault();
	            return false;
	        }
	    });

	    // btnchoosePicture
        $(document).on("click", ".btnchoosePicture", function (e) {
            $('.pic_preview').removeClass('picture_input_preview');
            $('.input_pic').removeClass('picture_input');
            $(".box-variants-detail table.ShowAllVariant tr.itemOrder input:checkbox:checked").each(function(){
                $('#'+'Option_'+$(this).val()+'_Picture_preview').addClass('picture_input_preview');
                $('#'+'Option_'+$(this).val()+'_Picture').addClass('picture_input');
            });
            $('.choosePicture a.btn-default').click();
        });

        // Change checkbox ShowAllVariant
        $(document).on('change', '.box-variants-detail table.ShowAllVariant tr.itemOrder input.checkbox, #check_all', function(){
            var numberOfChecked = $('.box-variants-detail table.ShowAllVariant tr.itemOrder input:checkbox:checked').length;
            if (numberOfChecked>0) {
                // Show select
                $('.bulk-actions').removeClass('d-none');
                $('.textSumChecked b').text(numberOfChecked);
                $('table.ShowAllVariant thead th').addClass('not_select');
            }else{
                $('.bulk-actions').addClass('d-none');
                $('.textSumChecked b').text(0);
                $('table.ShowAllVariant thead th').removeClass('not_select');
            }
        });

        // Change check_all
        $(document).on("click", "#check_all", function (e) {
            var c = $(this).prop('checked');
            var tbody = $(this).parent().parent().parent().parent().find('tbody');
            tbody.find('tr').each(function () {
                var checkbox = $(this).find('td:eq(0) input[type=checkbox]');
                if (c) {
                    checkbox.prop('checked', true);
                    $(this).addClass('active');
                } else {
                    checkbox.prop('checked', false);
                    $(this).removeClass('active');
                }
            });
        });
        
	},
	// xóa biến thể 
	removeVariant: function(){
		$(document).on('click', '.RemoveVariant', function(type) {
	        $(this).parent().parent().remove();
	        imsOption.checkMaxOption();
        	imsOption.setSelectOption();
        	if (type == 'add') {
        		imsOption.createTable();
        	}
	    });
	},
	// check biến thể tối đa
    checkMaxOption: function(){
        var numItems = $('table.table-option tr.tr_item').length;
        if (numItems>=3) { 
            $('.AddOtherOption').addClass('d-none'); 
            return false;
        } else{
            $('.AddOtherOption').removeClass('d-none'); 
        }
    },
	// check mỗi biến thể chỉ đc 1 loại
    setSelectOption: function(){
        $("select.selectOption option").each(function(index_option){
            if (listOptionSelected.includes($(this).attr('value')) == true) {
                $(this).removeAttr("disabled");
            }
        });
        listOptionSelected = [];
        $("select.selectOption").each(function(index){
            if ($(this).has('option:selected')){
                if (listOptionSelected.includes($(this).val()) == false && $(this).val() != null) {
                    listOptionSelected.push($(this).val());
                }
            }
        });
        $("select.selectOption option").each(function(index_option){
            $(this).removeAttr("disabled");
            if (listOptionSelected.includes($(this).attr('value')) == true && $(this).attr('value') != 'Custom') {
                $(this).attr("disabled", true);
            }
        });
    },
	// thêm biến thể mới
	addOtherOption: function(select_color) {
		$(document).on('click', '.AddOtherOption', function(){
			console.log('ABC');
			var check = imsOption.checkMaxOption();
		    if (check == false) { return false; }
		    imsOption.setSelectOption();

	        var numOption = $('table.table-option .tr_item').length + Math.floor(Math.random() * (100 + 1) + 1);
	        var selected, html_option, demoData = '';
	        $.each(listOption, function (key, val) {
	            if (listOptionSelected.includes(key) == false) {
	                selected = key;
	                return false;
	            }
	        });
	        $.each(listOption, function (key, val) {
	            if (selected == key) {
	                html_option += '<option value="'+key+'" selected>'+val+'</option>';
	            } else {
	                html_option += '<option value="'+key+'">'+val+'</option>';
	            }
	        });
	        $.each(listDemoData[selected], function (key, val) {
	            demoData += '<li><label class="tag-item item tag-color-'+ key +'" data-value="'+val+'">'+ val +'</label></li>';
	        });
	        if (selected == 'Color') {
	        	select_color = select_color.replace("_NUMBER_", numOption);
	        	var demoData = '';
	        	var htmlTags = '<div class="box_color"><input class="form-control tagsinput" name="OptionList['+numOption+'][Tags]" readonly="" style="display: none;">'+ select_color +'<div>';
	        } else {
	            var htmlTags = '<input class="form-control tagsinput" name=OptionList['+numOption+'][Tags] placeholder="'+lang_js['enter_value']+'">';
	        }
	        var html = ''+
	            '<tr class="tr_item">'+
	                '<td class="ver-top ver-name">'+
	                    '<input style="display:none;" type="d-none" name="OptionList['+numOption+'][SelectName]" value="'+selected+'">'+
	                    '<select name="OptionList['+numOption+'][Select]" class="selectOption" data-order="'+numOption+'">'+  html_option + '</select>'+
	                '</td>'+
	                '<td class="ver-top ver-data">'+
	                    '<div class="input_inline">'+
	                        '<div class="col-md-12">'+ htmlTags +'</div>'+
	                    '</div>'+
	                    '<div class="box_tags">'+
	                        '<div class="tags">'+
	                            '<div class="input-group">'+
	                                '<ul>'+ demoData +'</ul>'+
	                            '</div>'+
	                        '</div>'+
	                    '</div>'+
	                    '<div class="clear"></div>'+
	                '</td>'+
	                '<td class="ver-top">'+
	                    '<a class="RemoveVariant">'+
	                        '<button class="btn btn-danger" type="button"><i class="ficon-trash-1"></i></button>'+
	                    '</a>'+
	                '</td>'+
	            '</tr>'+
	        '';
		    $('table.table-option tbody').append(html);
	        imsOption.checkMaxOption();
	        imsOption.setSelectOption();
	        loadFunction();
        });
    },
	// thay đổi biến thể
    changeSelectOption: function(color_select, type) {
        $(document).on('change', '.selectOption', function() {
        	imsOption.setSelectOption();
        	if (type == 'add') {
	            var countOption = $('table.table-option tr.tr_item').length + Math.floor(Math.random() * (100 + 1) + 1);
	            var htmlTags = '';
	            var inputtags = '';
	            var i = $(this).data('order');
	            var htmlCustom = '<input class="CustomName form-control mt-1" name="OptionList['+i+'][CustomName]" placeholder="VD: Dung lượng">';
	            var optionSelected = $("option:selected", this);
	            var valueSelected = this.value;

	            $('input[name="OptionList['+i+'][SelectName]"]').val(valueSelected);
	            if (valueSelected == 'Color') {
	                color_select = color_select.replace("_NUMBER_", countOption);
	                htmlTags = '<div class="col-md-12 box_color"><input class="form-control tagsinput" name="OptionList['+i+'][Tags]" readonly="" style="display: none;">'+ color_select +'<div>';
	                $(this).parent().parent().find('.ver-data').html(htmlTags);
	            }else {
	                $.each(listDemoData[valueSelected], function (key, val) {
	                    inputtags += '<li><label class="tag-item item tag-color-'+ key +'" data-value="'+val+'">'+ val +'</label></li>';
	                });
	                var htmlTags = ''+
	                    '<div class="input_inline">'+
	                        '<div class="col-md-12">'+
	                            '<input class="form-control tagsinput" name=OptionList['+i+'][Tags] readonly="" placeholder="'+lang_js['enter_value']+'">'+
	                        '</div>'+
	                    '</div>'+
	                    '<div class="box_tags">'+
	                        '<div class="tags">'+
	                            '<div class="input-group">'+
	                                '<ul>'+ inputtags +'</ul>'+
	                            '</div>'+
	                        '</div>'+
	                    '</div>'+
	                    '<div class="clear"></div>'+
	                '';
	                $(this).parent().parent().find('.ver-data').html(htmlTags);
	            }
	            // Show custom
	            if (valueSelected == 'Custom') {
	                $(this).parent().append(htmlCustom);
	            } else{
	                $(this).parent().find('.CustomName').remove();
	            }
	            loadFunction();
	            imsOption.createTable();
        	}else{
	            var order = $(this).data('order');
	            var htmlCustom = '<input class="CustomName form-control mt5" name="OptionList['+ order +'][CustomName]" placeholder="VD: Dung lượng">';
	            var valueSelected = this.value;
	            $('input[name="OptionList['+ order +'][SelectName]"]').val(valueSelected);
	            // Show custom
	            if (valueSelected == 'Custom') {
	                if($(this).parent().find('input.CustomName').length>0) {

	                }else {
		                $(this).parent().append(htmlCustom);
	                }
	            } else if (valueSelected == 'Color') {
	                $(this).parent().find('.CustomName').remove();
	            } else{
		            $(this).parent().parent().find('.box_color').remove();
	                $(this).parent().find('.CustomName').remove();
	            }
        	}
        });
    },
    // cài đặt biến thể
    customOption: function() {
	    $(document).on('click', '.ShowMultipleOption', function(){
	        var element = $(this);
	        var text_show = 'Hủy';
	        var text_hidden = 'Thêm phiên bản';
	        if($('.box-variant-style').hasClass('d-none')) {
	            $('.box-variant-style').removeClass('d-none');
	            $('input[name="ShowMultipleOption"]').val(1);
	            element.text(text_show);
	        } else{
	            $('.box-variant-style').addClass('d-none');
	            $('input[name="ShowMultipleOption"]').val(0);
	            element.text(text_hidden);
	        }
	    });
    },
}

function changce_type_import(){
	var is_type = $('#div_type input:checked').val();
	if(is_type == 'new') {
		$("#div_import_new").slideDown(0);
		$("#div_import_has").slideUp(0);
	} else {
		$("#div_import_new").slideUp(0);
		$("#div_import_has").slideDown(0);
	}
}
function listOptionPopup(data_color='', select_color=''){
	    // Remore RemoveVariant popup
	    $(document).on('click', '.RemoveVariantPopup', function(){
	        $(this).parent().parent().remove();
	        checkAddOptionPopup();
        	setSelectOptionPopup();
	    });

	    // AddOtherOptionPopup
	    $(document).on('click', '.AddOtherOptionPopup', function(){
	    	console.log('heyClick');
	    	var check = checkAddOptionPopup();
	    	if (check == false) { return false; }
	        setSelectOptionPopup();
	        var count_tr_item = $('table.table-option-popup .tr_item').length + Math.floor(Math.random() * (100 + 1) + 1);
	        var selected = '';
	        var html_option = '';
	        var demoData = '';
	        $.each(listOption, function (key, val) {
	            if (listOptionSelected.includes(key) == false) {
	                selected = key;
	                return false;
	            }
	        });
	        $.each(listOption, function (key, val) {
	            if (selected == key) {
	                html_option += '<option value="'+key+'" selected>'+val+'</option>';
	            } else{
	                html_option += '<option value="'+key+'">'+val+'</option>';
	            }
	        });
	        $.each(listDemoData[selected], function (key, val) {
	            demoData += '<li><label class="tag-item item tag-color-'+ key +'" data-value="'+val+'">'+ val +'</label></li>';
	        });       

	        if (selected == 'Color') {
	        	color = select_color.replace("_NUMBER_", count_tr_item);
		        var html = ''+
		            '<tr class="tr_item">'+
		                '<td class="ver-top">'+
		                    '<input style="display:none;" type="d-none" name="OptionList['+count_tr_item+'][SelectName]" value="'+selected+'">'+
		                    '<select name="OptionList['+count_tr_item+'][Select]" class="selectOptionPopup" data-order="'+count_tr_item+'">'+  html_option + '</select>'+
		                '</td>'+
		                '<td class="ver-top" style="max-width: 220px;">'+
                            '<div class="dataTags input_inline pl-3 pr-3">'+
		                           color +
		                    '</div>'+
		                    '<div class="clear"></div>'+
		                '</td>'+
		                '<td class="ver-top">'+
		                    '<a class="RemoveVariantPopup">'+
		                        '<button class="btn btn-danger" type="button"><i class="ficon-trash-1"></i></button>'+
		                    '</a>'+
		                '</td>'+
		            '</tr>'+
		        '';
	        }else{
	        	var html = ''+
		            '<tr class="tr_item">'+
		                '<td class="ver-top">'+
		                    '<input style="display:none;" type="d-none" name="OptionList['+count_tr_item+'][SelectName]" value="'+selected+'">'+
		                    '<select name="OptionList['+count_tr_item+'][Select]" class="selectOptionPopup" data-order="'+count_tr_item+'">'+  html_option + '</select>'+
		                '</td>'+
		                '<td class="ver-top" style="max-width: 220px;">'+
                            '<div class="dataTags input_inline pl-3 pr-3">'+
		                        '<input class="form-control" name=OptionList['+count_tr_item+'][Tags] placeholder="'+lang_js['enter_value']+'">'+
		                    '</div>'+
		                    '<div class="clear"></div>'+
		                '</td>'+
		                '<td class="ver-top">'+
		                    '<a class="RemoveVariantPopup">'+
		                        '<button class="btn btn-danger" type="button"><i class="ficon-trash-1"></i></button>'+
		                    '</a>'+
		                '</td>'+
		            '</tr>'+
		        '';
	        }
		    $('table.table-option-popup tbody').append(html);
	        checkAddOptionPopup();
	        setSelectOptionPopup();
	        loadFunction();
        	selectColor();
	    });

	    // checkAddOptionPopup
	    function checkAddOptionPopup(){
	        var numItems = $('table.table-option-popup tr.tr_item').length;
	        if (numItems>=3) { 
	            $('.AddOtherOptionPopup').addClass('d-none'); 
	            return false;
	        } else{
	            $('.AddOtherOptionPopup').removeClass('d-none'); 
	        }
	    }

	    // Change Select
        $(document).on('change', '.selectOptionPopup', function(){
            setSelectOptionPopup();
            var htmlTags = '';
            var i = $(this).data('order');
            var htmlCustom = '<input class="CustomName form-control mt-2" name="OptionList['+i+'][CustomName]" placeholder="VD: Dung lượng">';
            var optionSelected = $("option:selected", this);
            var valueSelected = this.value;
            $('input[name="OptionList['+i+'][SelectName]"]').val(valueSelected);
            // Show custom
        	console.log('HeyChange');
        	console.log(valueSelected);
        	console.log(i);
            if (valueSelected == 'Custom') {
                $('select[name="OptionList['+ i +'][Tags]"]').remove();
                $(this).parent().parent().find('.dataTags').html('<input class="form-control" name=OptionList['+ i +'][Tags] placeholder="' + lang_js['enter_value'] + '">');
                $(this).parent().append(htmlCustom);
            }else if (valueSelected == 'Color') {
            	color = select_color.replace("_NUMBER_", i);
                $('select[name="OptionList['+ i +'][Tags]"]').remove();
                $(this).parent().parent().find('.dataTags').html(color);
            } else{
                $('select[name="OptionList['+ i +'][Tags]"]').remove();
                $(this).parent().parent().find('.dataTags').html('<input class="form-control" name=OptionList['+ i +'][Tags] placeholder="' + lang_js['enter_value'] + '">');
                $(this).parent().find('.CustomName').remove();
            }
            selectColor();
        });
}

function setSelectOptionPopup(){
    $("select.selectOptionPopup option").each(function(index_option){
        if (listOptionSelected.includes($(this).attr('value')) == true) {
            $(this).removeAttr("disabled");
        }
    });
    listOptionSelected = [];
    $("select.selectOptionPopup").each(function(index){
        if ($(this).has('option:selected')){
            if (listOptionSelected.includes($(this).val()) == false && $(this).val() != null) {
                listOptionSelected.push($(this).val());
            }
        }
    });
    $("select.selectOptionPopup option").each(function(index_option){
        $(this).removeAttr("disabled");
        if (listOptionSelected.includes($(this).attr('value')) == true && $(this).attr('value') != 'Custom') {
            $(this).attr("disabled", true);
        }
    });
    $('select.selectOptionPopup').trigger("chosen:updated");
}

function change_icon(group_element, icon_element){
	var group = $(group_element).val();
	var icon = $(icon_element).val();

	loading('show');
	$.ajax({
		type: "POST",
		url: ROOT+"ajax.php",
		data: { "m" : "product", "f" : "loadIcon", "group" : group, "icon" : icon}
	}).done(function( string ) {
		loading('hide');
		var data = JSON.parse(string);
		if(data.ok == 1) {
			$(icon_element).html(data.html).trigger('chosen:updated');
		}
	});
}

function loadBrand(group_element, brand_element){
	var group  = $(group_element).val();
	var brand  = $(brand_element).val();
	loading('show');
	$.ajax({
		type: "POST",
		url: ROOT+"ajax.php",
		data: { "m" : "product", "f" : "loadBrand", "group" : group}
	}).done(function( string ) {
		loading('hide');
		var data = JSON.parse(string);
		if(data.ok == 1) {
			$(brand_element).html(data.html);
            $(brand_element).val(brand);
            $(brand_element).trigger('change');
		}
	});
}


function loadBrandModel(group_element, brand_element){
	var group  = $(group_element).val();
	var brand  = $(brand_element).val();
	console.log(brand_element);
	loading('show');
	$.ajax({
		type: "POST",
		url: ROOT+"ajax.php",
		data: { "m" : "product", "f" : "loadBrandModel", "group" : group, "brand" : brand}
	}).done(function( string ) {
		loading('hide');
		var data = JSON.parse(string);
		if(data.ok == 1) {
			$(brand_element).html(data.html);
            $(brand_element).val(brand);
            $(brand_element).trigger('change');
		}
	});
}


// load thuộc tính
function loadNature(){
	var group = $('select[name="group_id"]').val();
	loading('show');
	$.ajax({
		type: "POST",
		url: ROOT+"ajax.php",
		data: { "m" : "product", "f" : "loadNature", "group" : group}
	}).done(function( string ) {
		loading('hide');
		var data = JSON.parse(string);
		if(data.ok == 1) {
			if ($('.box_groupnature').length>0) {
			  	$('.box_groupnature').removeClass('d-none');
			  	$('.box_groupnature').addClass('d-none');
				$.each(data.html, function( index, value ) {
				  	$('.box_groupnature.groupnature_'+value).removeClass('d-none');
				});
			}
		}
	});
}

// Load size theo nhóm sản phẩm
function loadSize(){
	var group = $('select[name="group_id"]').val();
	loading('show');
	$.ajax({
		type: "POST",
		url: ROOT+"ajax.php",
		data: { "m" : "product", "f" : "loadSize", "group" : group}
	}).done(function( string ) {
		loading('hide');
		var data = JSON.parse(string);
		if(data.ok == 1) {
			var listVal = {};
			$.each( $('.list_size'), function( key, value ) {
				listVal[key] = $(this).val();
			});
			$.each( $('.list_size'), function( key, value ) {
				$(this).html(data.html);
				$(this).val(listVal[key]);
        		$(this).trigger("chosen:updated");
			});
		}
	});
}

function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;
    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
};

function create_price(type = ''){
	color = $("#change_color");
	size = $("#change_size");
	product_id = getUrlParameter('id');
	if (color.val()) {
		if (size.val()) {
			loading('show');
			$.ajax({
	            type: "POST",
	            url: ROOT + "ajax.php",
	            data: {"m": "product", "f": "createPrice", "color": color.val(), "size": size.val(), 'product_id' : product_id, 'type': type}
	        }).done(function (string) {
	            loading('hide');
	            var data = JSON.parse(string);
	            if (data.ok == 1) {
	                $('#boxPrice').html(data.html);
	            }
	            auto_price_format();
	        });
		}
	}	
}

jQuery(document).ready(function ($) {
	$("#change_color").change(function(e, params){
		create_price('change_color');
	});
	$("#change_size").change(function(e, params){
		create_price('change_size');
	});
	create_price('load');
});