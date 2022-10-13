imsGallery = {
    load_more : function(reload=0){
        loading('show');
		// var group_id = $('.list_item_gallery').data('group'); // Lọc theo nhóm ...
		var num_cur = $('input[name="sort"]').data('start');
        // var sort = $('input[name="sort"]').val(); // Lọc sp tiêu chí khác ...
        var keyword = $('input[name="sort"]').data('keyword'); // Lọc theo từ khóa tìm kiếm ...
        var product_item = $('input[name="sort"]').data('item'); // Lọc theo sản phẩm ...
        var group_id = $('input[name="sort"]').attr('data-group'); // Lọc theo sản phẩm ...
        var design_id = $('input[name="sort"]').attr('data-design'); // Lọc theo sản phẩm ...

        if(reload == 1){
            num_cur = 0;
        }

        $.ajax({
            type: "POST",
            url: ROOT + "ajax.php",
            data: {"m":"gallery", "f":"load_more", 'num_cur':num_cur, 'group_id':group_id, 'keyword':keyword, 'product_item':product_item, 'design_id':design_id}
        }).done(function (string) {
            var data = JSON.parse(string);

            $('#load_more').data('start', data.num);
            if(reload == 0){
                $('.list_item_gallery .row_item').append(data.html);
            }else if(reload == 1){
                $('.list_item_gallery .row_item').html(data.html);
            }
            setTimeout(function(){
                mansoryInit(".list_item_gallery .row_item");
            },300);

            $(".list_item_gallery .row_item").masonry('reloadItems');
            $('#sort_result').html(data.filter_group);
			// window.history.pushState({path:data.link},"",data.link);
            $('.result span').text(data.total);
            if(data.num > 0){
                $('.btn_viewmore button').show();
                $('input[name="sort"]').data('start', data.num);
            }else{
                $('.btn_viewmore button').hide();
            }
            loading('hide');
        });
    },
    load_detail_image : function(info){
        loading('show');
        $.ajax({
            type: "POST",
            url: ROOT + "ajax.php",
            data: {"m":"gallery", "f":"load_detail_image", "info":info}
        }).done(function (string) {
            var data = JSON.parse(string);
            $('#fancybox_image .wrap_content').html(data.html);

            $('body').css('overflow', 'hidden');
            $('#fancybox_image').show();
            setTimeout(function () {
                $('#fancybox_image .tag').show();
                $('#fancybox_image button.nav.show').show();

                // scroll list product
                var h = $('#fancybox_image .info-area .info_image').innerHeight() + $('#fancybox_image .info-area .list_product .product_title').innerHeight();
                var h_s = $(window).height();
                var h_content = $('#fancybox_image .info-area .list_product .list_item').innerHeight();
                if (window.matchMedia('(min-width: 1200px)').matches) {
                    $('#fancybox_image .info-area .list_product .list_item').css('height', (h_s-h)+'px');
                }else{
                    $('#fancybox_image .info-area .list_product .list_item').css('height', 'auto');
                    $('#fancybox_image .info-area .list_product .list_item').mCustomScrollbar("disable");
                }
                Scrollbar_y('#fancybox_image .info-area .list_product .list_item');
                if((h_s - h) >= h_content){
                    $('#fancybox_image .info-area .list_product .list_item').mCustomScrollbar("disable");
                }else{
                    $('#fancybox_image .info-area .list_product .list_item').mCustomScrollbar("update");
                }
            }, 300);
            loading('hide');
        });
    },
}

function loading_picture(formData, index, list_found){
    if(index != 0){
        formData.append("index", index);
    }
    if(list_found.length){
        formData.append("founded", list_found);
    }
    $("#box-result .list_image .row").append('<div class="loading_picture"><div class="magnify"></div></div>');
    // loading('show');
    $.ajax({
        type: 'POST',
        url: ROOT+"ajax.php",
        data: formData,
        contentType: false,
        cache: false,
        processData:false,
        xhr: function() {
            var xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", function(evt) {
                if (evt.lengthComputable) {
                    var percentComplete = (evt.loaded / evt.total) * 100;
                    $(".progress-bar").css({"width": percentComplete+"%"});
                    console.log(percentComplete);                    
                }
           }, false);
           return xhr;
        },
    }).done(function(string){
        $("#lookup").text(lang_js_mod['gallery']['searching']);
        var data = JSON.parse(string);
        if(data.item_id != ''){
            list_found.push(data.item_id);
        }
        console.log(data);
        // loading('hide');
        $(".progress-bar").css({"width": "100%"});        
        $("#box-result .list_image .row").find('.loading_picture').remove();       
        if(data.html){
            $("#box-result .list_image .row").append(data.html);
        }
        if(data.end == 0){
            loading_picture(formData, data.index, list_found);
        }else{
            $("#lookup").text(lang_js_mod['gallery']['search_finish']);
            $(".progress-bar").css({"width": "0%"});
            setTimeout(function(){
                $("#lookup").text(lang_js_mod['gallery']['search']);
            },500);
        }
    })
}

$(document).ready(function(){
    $(document).on("change", "#choose-file", function(){
        $(".progress-bar").css({"width": "0%"});
    })
    var list_found = new Array();
    $(document).on("click", ".btn-lookup", function(){
        // $("#box-result .list_image .row").html('');
        $("#lookup").text(lang_js_mod['gallery']['analyzing']);
        var formData = new FormData(),
            id = $(this).data('id'),            
            image = $("#img-preview").attr("src");
        if(typeof image != 'undefined' && image.includes('data:image')){
            var base64ImageContent = image.replace(/^data:image\/(png|jpg);base64,/, "");
            var blob = base64ToBlob(base64ImageContent, 'image/png');
            formData.append("picture", blob);
        }
        formData.append("id", id);
        formData.append("f", "picture_search");
        formData.append("m", "event");
        formData.append("lang_cur", lang);

        loading_picture(formData, 0, list_found);
        return false;    
    })

    $(document).on("click", ".logo", function(){
        console.log(list_found);
    })
})