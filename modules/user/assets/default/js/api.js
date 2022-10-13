imsApi = {
	save_config_sendo : function(form_id){
		$("#"+form_id).validate({
			submitHandler: function(){
				var id = $("#"+form_id+" button[type='submit']").attr("data-id"),
					status = $("input[data-id='"+id+"']").is(":checked")==true?1:0,
					fData = $("#"+form_id).serializeArray(),
					mess = $("#"+form_id).find(".mess");
				mess.stop(true,true).slideUp(200).html('');
				loading('show');
				$.ajax({
					type: "POST",
					url: ROOT+"ajax.php",
					data: { "m" : "user", "f" : "save_config_sendo", "id" : id, "status" : status, "data" : fData }
				}).done(function( string ) {
					console.log(string);
					var data = JSON.parse(string);
					console.log(data);
					loading('hide');
					mess.html(imsTemp.html_alert(data.mess,data.type)).stop(true,true).slideDown(200);
					if(data.ok==0 && status==1){
						console.log($("input[data='"+id+"']"));
						$("input[data-id='"+id+"']").prop('checked', false).change();
					}else{
						// $("input[data='"+id+"']").prop('checked', false).change()
					}
				})
			}
		})
	},
}
$(document).ready(function(){
	//toggle button
	$(".box_manager_api .col_item input:checkbox").each(function(){
		var $this = $(this),
			id = $(this).attr("data-id"),
			mess = $(this).parents(".col_item").find(".mess");
			mess.stop(true,true).slideUp(200).html('');
		$(this).on("change",function(){
			var status = $(this).is(":checked")==true?1:0;
			$.ajax({
			 	type: "POST",
		        url: ROOT+"ajax.php",
		        data: { "m" : "user", "f" : 'update_api_status', 'id': id, 'status': status },
		    }).done(function(string){	
		    	console.log(string);
		        var data = JSON.parse(string);
                console.log(data);
                mess.html(imsTemp.html_alert(data.mess,data.type)).stop(true,true).slideDown(200);
                if(data.ok==1){    		        
    		        setTimeout(function(){mess.stop(true,true).slideUp(200).html('')},5000)
                }else if(data.ok==2){
                    if(status==1){                    	
                        $.fancybox.open($('#'+id));
                    }
                }else if(data.ok==3){
                	$this.prop('checked', false).change();
                }
		    })
		})
	})
})