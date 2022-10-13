String.prototype.replaceArray = function (find, replace) {
    var replaceString = this;
    for (var i = 0; i < find.length; i++) {
        replaceString = replaceString.replace(find[i], replace[i]);
    }
    return replaceString;
};
imsTemp = {
    join: function (str) {
        var store = [str];
        return function extend(other) {
            if (other != null && 'string' == typeof other) {
                store.push(other);
                return extend;
            }
            return store.join('');
        }
    },
    html_alert: function (mess, type) {
        var html_class = "warning";
        switch (type)
        {
            case "error":
                html_class = 'alert-danger';
                break;
            case "warning":
                html_class = 'alert-warning';
                break;
            case "success":
                html_class = 'alert-success';
                break;
            default:
                html_class = 'alert-info';
                break;
        }
        return imsTemp.join('<div class="alert ' + html_class + ' alert-dismissable">' + mess + '</div>');
    },
    html_btn: function (title, type) {
        switch (type)
        {
            default:
                return imsTemp.join('<span class="l"></span><span class="r"></span><span class="text">' + title + '</span>');
                break;
        }
    },
    html_radio: function (type) {
        switch (type)
        {
            default:
                return imsTemp.join('<span class="radio_s"></span>');
                break;
        }
    },
    html_checkbox: function (type) {
        switch (type) {
            default:
                return imsTemp.join('<span class="checkbox_s"></span>');
                break;
        }
    }
};

imsGlobal = {
    autocomplete_search : function(id,searchl){
        var timeout = null;
        var availableTags = [];
        $("#" + id).on("change paste",function(){
            if($("#" + id).val().trim().length==0){
                // $("#" + id).parent("form").find(".btn_search").html('<i class="fas fa-search"></i>');
                $(".wrap-suggestion-"+id).html('');
                availableTags.length = 0;
                return false;
            }
        })      
        $(".wrap-suggestion-"+id).remove();
        $("#" + id).autocomplete({
            minLength: 1,                
            source: function (requestObj, responseFunc) {
                clearTimeout(timeout);
                // $("#" + id).parent("form").find(".btn_search").html('<i class="fad fa-spinner-third fa-spin"></i>');
                timeout = setTimeout(function () {
                    $.ajax({
                        url: ROOT+searchl+'?keyword='+requestObj.term,
                        type: 'GET',
                        async: true,
                    }).done(function(string){
                        availableTags = JSON.parse(string);
                        responseFunc(availableTags);
                    })
                }, 750);
            },
            response: function(event, ui) {
                if(ui.content.length === 0) {
                    // $("#" + id).parent("form").find(".btn_search").html('<i class="fas fa-search"></i>');
                    availableTags.length = 0;
                }
            },
            open: function (event, ui) {
                $(this).autocomplete("widget").width($(this).innerWidth());
                $(this).data("ui-autocomplete").menu.bindings = $();
                var resultsList = $("ul.ui-autocomplete > li.ui-menu-item > a");                        
                var srchTerm = $.trim($("#" + id).val()).split(/\s+/).join('|');
                resultsList.each(function () {
                    var jThis = $(this);
                    var regX = new RegExp('(' + srchTerm + ')', "ig");
                    var oldTxt = jThis.text();                            
                    jThis.html(oldTxt.replace(regX, '<b>$1</b>'));                            
                });
                auto_price_format();
            },
            select: function (event, ui) {
                // $(this).val(ui.item.value);
                window.location.href = ui.item.link;
                // $('#form_' + id).submit();
            },                
        }).autocomplete( "instance" )._renderItem = function( ul, item ) {
            ul.addClass("wrap-suggestion-"+id);
            // $("#" + id).parent("form").find(".btn_search").html('<i class="fas fa-search"></i>');
            return $( "<li>" )
                .append( "<div><a href=\""+item.link+"\" class=\"item\"><img src=\""+item.picture+"\">"+"<div class='info'><h3>"+item.title+"</h3>"+item.price+"</div></a></div>" )
                .appendTo(ul);
        };
    },

    emaillist: function (form_id) {
        $("#" + form_id).validate({
            submitHandler: function () {
                var fData = $("#" + form_id).serializeArray();

                loading('show');

                $.ajax({
                    type: "POST",
                    url: ROOT + "ajax.php",
                    data: {"m": "contact", "f": "emaillist", "data": fData, "lang_cur": lang}
                }).done(function (string) {
                    var data = JSON.parse(string);

                    loading('hide');

                    if (data.ok == 1) {
                        $('#' + form_id)[0].reset();
                        alert(data.mess);
                    } else {
                        alert(data.mess);
                    }
                });
                return false;
            },
            rules: {
                email: {
                    required: true,
                    email: true,
                }
            },
            messages: {
                //email: false
                email: ''
                // email: lang_js['err_invalid_email']
            }
        });
    },
    // uploadMuti ============================================================
    uploadMuti: function (input_name, html_id) {

        var htmllist_uploaded = $("#" + html_id + " .list_uploaded");

        $(function () {
            'use strict';
            $('#' + html_id + ' input[type="file"]').fileupload({
                //type: "POST",
                //url: ROOT+"ajax.php",
                url: ROOT + "ajax.php?m=global&f=uploadmuti&lang=" + lang,
                //data: { "m" : "global", "f" : 'uploadpicmuti', 'lang' : lang },
                dataType: 'json',
                done: function (e, data) {
                    $.each(data.result.files, function (index, file) {
                        var item = $('<div/>').addClass('pic-item').html('<a href="' + file.url + '" data-fancybox><img src="' + file.thumbnailUrl + '" alt="' + file.name + '" /></a><a class="btn-remove" href="javascript:;"><i class="far fa-times"></i></a><input type="hidden" value="' + file.url + '" name="' + input_name + '[]">');
                        //item.appendTo('#upload-files');
                        item.appendTo(htmllist_uploaded);
                    });
                    //setTimeout(function(){$('#upload-progress').removeClass('show');}, 1000)
                },
                start: function (e, data) {
                    $("#" + html_id + " .upload-progress").addClass('show');
                },
                stop: function (e, data) {
                    $("#" + html_id + " .upload-progress").removeClass('show');
                },
                progressall: function (e, data) {
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    if (progress == 100) {
                        //$('#upload-progress').removeClass('show');
                    } else {
                        $("#" + html_id + " .upload-progress").addClass('show');
                    }
                    $("#" + html_id + " .upload-progress .progress-bar").css(
                            'width',
                            progress + '%'
                            );
                }
            }).prop('disabled', !$.support.fileInput)
                    .parent().addClass($.support.fileInput ? undefined : 'disabled');


            htmllist_uploaded.on("click", ".pic-item a.btn-remove", function () {
                $(this).parent('.pic-item').remove();
            });
        });
    },
    
    box_lang: function () {
        $('#box-lang .box-lang-list a.current').clone().prependTo('#box-lang .lang-current');
        $('#box-lang a.current').click(function (e) {
            return false;
        });
        $('#box-lang').mouseenter(function (e) {
            $(this).children('.box-lang-list').stop(true, false).slideDown(200);
        }).mouseleave(function (e) {
            $(this).children('.box-lang-list').stop(true, false).slideUp(0);
        });
    },    
    captcha_img: function () {
        var src = ROOT + 'ajax.php?m=global&f=captcha&rand=' + Math.floor((Math.random() * 1000) + 1);
        $('img.captcha_img').prop('src', src);
    },
    captcha_refresh: function () {
        loading('show');
        var src = ROOT + 'ajax.php?m=global&f=captcha_refresh&rand=' + Math.floor((Math.random() * 1000) + 1);
        $('img.captcha_img').prop('src', src, loading('hide'));
    },    
    uploadPic:function (form_id,multi=1) {        
        $(function () {
            'use strict';

            $('#fileupload').fileupload({
                url: ROOT+"ajax.php?m=global&f=uploadpicmuti&lang="+lang,
                dataType: 'json',
                done: function (e, data) { 
                    $.each(data.result.files, function (index, file) {                        
                        if(multi==1){
                            var url = file.url.split("uploads/");                            
                            var item = '<img src="'+file.thumbnailUrl+'" alt="'+file.name+'" /></a><input type="hidden" value="'+url[1]+'" name="picture">';
                            $('#upload-files').html(item);
                        }else{
                            var item = $('<div/>').addClass('pic-item').html('<a href="'+file.url+'" data-fancybox><img src="'+file.thumbnailUrl+'" alt="'+file.name+'" /></a><a class="btn-remove" href="javascript:;"><i class="ficon-cancel"></i></a><input type="hidden" value="'+file.url+'" name="arr_picture[]">');
                            item.appendTo('#upload-files');
                        }
                    });
                },
                start: function (e, data) {
                    $('#upload-progress').addClass('show');
                    $('#upload-progress .progress-bar').removeAttr('style');
                },
                stop: function (e, data) {
                    setTimeout(function(){
                        $('#upload-progress').removeClass('show');
                    },1200);
                },
                progressall: function (e, data) {
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    if(progress == 100) {
                        //$('#upload-progress').removeClass('show');
                    } else {
                        $('#upload-progress').addClass('show');
                    }
                    $('#upload-progress .progress-bar').css(
                        'width',
                        progress + '%'
                    );
                }
            }).prop('disabled', !$.support.fileInput)
                .parent().addClass($.support.fileInput ? undefined : 'disabled');
            $("#upload-files").on("click", ".pic-item a.btn-remove", function () {
                $(this).parent('.pic-item').remove();
            });
        });
    },    
}

/*var aSep = ',';
 var aDec = '.';*/
var a_sign = ' vnđ';
var p_sign = 's';
var m_dec = 0;
var aSep = '.';
var aDec = ',';
if (lang == 'vi') {
    aSep = '.';
    aDec = ',';
}

function SaveToDisk(fileURL, fileName) {
    // for non-IE
    if (!window.ActiveXObject) {
        var save = document.createElement('a');
        save.href = fileURL;
        save.target = '_blank';
        save.download = fileName || fileURL;
        var evt = document.createEvent('MouseEvents');
        evt.initMouseEvent('click', true, true, window, 1, 0, 0, 0, 0,
                false, false, false, false, 0, null);
        save.dispatchEvent(evt);
        (window.URL || window.webkitURL).revokeObjectURL(save.href);
    }

    // for IE
    else if (!!window.ActiveXObject && document.execCommand) {
        var _window = window.open(fileURL, "_blank");
        _window.document.close();
        _window.document.execCommand('SaveAs', true, fileName || fileURL)
        _window.close();
    }
}

function click_download() {
    $('a.download').click(function () {
        var href = $(this).attr('href');
        SaveToDisk(href);
        return false;
    });
}

function go_link(link_go) {
    if (link_go == '') {
        location.reload();
    } else {
        window.location.href = link_go;
    }
}

function datepickerRange(html_check_in, html_check_out) {
    var startDateTextBox = $(html_check_in);
    var endDateTextBox = $(html_check_out);

    startDateTextBox.datetimepicker({ 
        // timeFormat: 'HH:mm z',
        onClose: function(dateText, inst) {
            if (endDateTextBox.val() != '') {
                var testStartDate = startDateTextBox.datetimepicker('getDate');
                var testEndDate = endDateTextBox.datetimepicker('getDate');
                if (testStartDate > testEndDate)
                    endDateTextBox.datetimepicker('setDate', testStartDate);
            }
            else {
                endDateTextBox.val(dateText);
            }
        },
        onSelect: function (selectedDateTime){
            endDateTextBox.datetimepicker('option', 'minDate', startDateTextBox.datetimepicker('getDate') );
        }
    });
    endDateTextBox.datetimepicker({ 
        // timeFormat: 'HH:mm z',
        onClose: function(dateText, inst) {
            if (startDateTextBox.val() != '') {
                var testStartDate = startDateTextBox.datetimepicker('getDate');
                var testEndDate = endDateTextBox.datetimepicker('getDate');
                if (testStartDate > testEndDate)
                    startDateTextBox.datetimepicker('setDate', testEndDate);
            }
            else {
                startDateTextBox.val(dateText);
            }
        },
        onSelect: function (selectedDateTime){
            startDateTextBox.datetimepicker('option', 'maxDate', endDateTextBox.datetimepicker('getDate') );
        }
    });
}

function dateRange(html_check_in, html_check_out, time=0){
    var startDateTextBox = $(html_check_in);
    var endDateTextBox = $(html_check_out);
    if(time == 1){
        $.timepicker.datetimeRange(
            startDateTextBox,
            endDateTextBox,
            {
                dateFormat: 'dd/mm/yy',
                timeFormat: "HH:mm",
                start: {}, // start picker options
                end: {}, // end picker options
                showTimezone: false,
                showSecond: false,
                showMillisec: false,
                showMicrosec: false,

                monthNames: ['Tháng 1','Tháng 2','Tháng 3','Tháng 4','Tháng 5','Tháng 6','Tháng 7','Tháng 8','Tháng 9','Tháng 10','Tháng 11','Tháng 12'],
                dayNamesMin: ['CN', 'T2','T3','T4','T5','T6','T7'],
                timeText: 'Thời gian',
                firstDay: 1,
                hourText: 'Giờ',
                minuteText: 'Phút',
                secondText: 'Giây',
                currentText: 'Hiện tại',
                closeText: 'Chọn',
            }
        );
    }else{
        $.timepicker.dateRange(
            startDateTextBox,
            endDateTextBox,
            {
                dateFormat: 'dd/mm/yy',
                timeFormat: "HH:mm",
                start: {}, // start picker options
                end: {}, // end picker options

                monthNames: ['Tháng 1','Tháng 2','Tháng 3','Tháng 4','Tháng 5','Tháng 6','Tháng 7','Tháng 8','Tháng 9','Tháng 10','Tháng 11','Tháng 12'],
                dayNamesMin: ['CN', 'T2','T3','T4','T5','T6','T7'],
                firstDay: 1,
            }
        );
    }
}

function datepicker(html) {
    var today = new Date();
    $(html).datepicker({
        changeMonth: true,
        changeYear: true,
        yearRange: "c-100:" + (today.getFullYear() + 10),
        dateFormat: 'dd/mm/yy',
        //showWeek: true,
        firstDay: 1
    });
}

function header_cart() {
    $.ajax({
        type: "POST",
        url: ROOT + "ajax.php",
        data: {"m": "product", "f": "getCart"}
    }).done(function (string) {        
        var data = JSON.parse(string);
        // if(data.num_cart < 10){
        //     data.num_cart = data.num_cart;
        // }
        $('#header_cart .num_cart').html(data.num_cart);
    });
    return false;
}

function auto_rate_exchange() {
    var rate = 1;
    var a_sign = ' đ';
    var p_sign = 's';
    var m_dec = 0;

    $('.price_format .number').each(function (index, element) {
        $(this).text($(this).data('value') * rate).autoNumeric('init', {aSign: a_sign, pSign: p_sign, mDec: m_dec}).autoNumeric('update', {aSign: a_sign, pSign: p_sign, mDec: m_dec});
    });
}

function _update_number(o, type) {
    var v = o.autoNumeric('get');
    o.parent().find('.' + type + '_input').val(v).change();
}
function update_number(type) {
    $('.' + type).change(function (e) {
        _update_number($(this), type);
    }).keydown(function (e) {
        _update_number($(this), type);
    }).keypress(function (e) {
        _update_number($(this), type);
    });
}
function auto_price_format() {
    $('.auto_price').autoNumeric('init', {aSign: a_sign, pSign: p_sign, mDec: m_dec, aSep: aSep, aDec: aDec}).autoNumeric('update', {aSign: a_sign, pSign: p_sign, mDec: m_dec, aSep: aSep, aDec: aDec});
    $('.price_format .number').autoNumeric('init', {aSign: a_sign, pSign: p_sign, mDec: m_dec, aSep: aSep, aDec: aDec}).autoNumeric('update', {aSign: a_sign, pSign: p_sign, mDec: m_dec, aSep: aSep, aDec: aDec});
    $('.price_input').autoNumeric('init', {aSign: ' ₫', pSign: 's', mDec: 0});    
    update_number('auto_price');
    update_number('price_input');
}
function auto_number_format() {
    $('.auto_number').autoNumeric('init', {mDec: 0, aSep: aSep, aDec: aDec});
    update_number('auto_number');

    $('.auto_number_positive').autoNumeric('init', {mDec: 0, vMin: 1, aSep: aSep, aDec: aDec});
    update_number('auto_number_positive');

    $('.auto_float').autoNumeric('init', {mDec: 10, aSep: aSep, aDec: aDec});
    update_number('auto_float');

    $('.auto_float_positive').autoNumeric('init', {mDec: 2, vMin: 1, aSep: aSep, aDec: aDec});
    update_number('auto_float_positive');
}
function auto_quantity_format() {
    $('.auto_quantity').autoNumeric('init', {mDec: 0, vMin: 1, vMax: 100, aSep: aSep, aDec: aDec});
    update_number('auto_quantity');

    $('.auto_float').autoNumeric('init', {mDec: 10, aSep: aSep, aDec: aDec});
    update_number('auto_float');
}
function auto_percent_format() {
    $('.auto_percent').autoNumeric('init', {aSign: ' %', pSign: 's', mDec: 2, vMin: 0, vMax: 100, aSep: aSep, aDec: aDec});
    update_number('auto_percent');
}
function auto_numeric() {
    $('.auto_numeric').autoNumeric('init');
    update_number('auto_numeric');
}

function loading(s, o) {
    var l;

    if (!o) {
        o = $('body');
        if (!o.children('#ims-loading').length) {
            $('<div id="ims-loading"></div>').appendTo(o);
        }
        l = o.children('#ims-loading');
    } else {
        if (!o.children('.loading').length) {
            $('<div class="loading"></div>').appendTo(o);
        }
        l = o.children('.loading');
    }

    if (o.css('position') == 'static') {
        o.css('position', 'relative');
    }

    if (s == 'show') {
        l.stop(true, true).fadeIn();
    } else {
        l.stop(true, true).fadeOut();
    }
}

function get_lang(key, module, arr_replace) {
    module = (module) ? module : 'global';

//    if (lang_js_mod[module][key]) {
//        $ims->func->load_language($module);
//    }
    var output = (module != 'global' && lang_js_mod[module][key]) ? lang_js_mod[module][key] : ((lang_js[key]) ? lang_js[key] : key);
    if ((typeof arr_replace === "object") && (arr_replace !== null)) {
        var arr_key = new Array();
        var arr_value = new Array();
        $.each(arr_replace, function (index, value) {
            arr_key.push(index);
            arr_value.push(value);
        });
        //var arr_key = arr_replace.keys();
        //var arr_value = arr_replace.values();
        output = output.replaceArray(arr_key, arr_value);
    }
    return output;
}

function main_menu() {
    //init sidemenu    
    $("#ims-side-menu ul li").each(function(){($(this).children().length>1)?$(this).addClass("has-submenu"):"";})
    $("#ims-side-menu li.has-submenu >a").after('<i class="icon"></i>');
    $(".sideMenu .box_user").prepend($("header .header_bottom .header_user ul").clone());
    //open close sidemenu
    $(".navbar-toggle, .navbar-toggler").on("click", function() {
        $(".navbar-toggle, .navbar-toggler").toggleClass('change');
        $(".sideMenu, .overlay").toggleClass("open");
        $(".sideMenu").hasClass("open")?$("[class*=\"wrap-suggestion\"]").removeClass("d-block"):'';
    });
    $(".overlay").on("click", function() {
        $(this).removeClass("open"), $(".sideMenu").removeClass("open");
        $(".navbar-toggle, .navbar-toggler").toggleClass('change');
    });
    $("body").on("click", ".sideMenu.open .nav-item", function() {
        $(this).hasClass("dropdown") || $(".sideMenu, .overlay").toggleClass("open");
    });
    $(window).resize(function(){$(window).width()>=1200 ? $(".sideMenu, .overlay").hide() : $(".sideMenu, .overlay").show()});
    //control
    $("i.icon").on("click",function(){ //open
        $(this).toggleClass("open"),$(this).siblings(".menu_sub").toggleClass("open");
    });
    $('#ims-side-menu li.has-submenu > a').on('mouseover', function (e) {
        if(!$(this).siblings(".menu_sub").hasClass('open')){
            $(this).next().toggleClass("open");
        }
        $(this).siblings(".menu_sub").addClass("open");
    });
    $(".menu_back").on("click",function(){ //back
        $(this).parent().removeClass("open"),$(this).parent().siblings(".icon").removeClass("open");
    });
}

function scrollTop() {
    $(window).scroll(function () {
        if ($(this).scrollTop() > 100) {
            $('#ims-scrollup').fadeIn();
        } else {
            $('#ims-scrollup').fadeOut();
        }
    });
    $('#ims-scrollup').click(function () {
        $("html, body").animate({
            scrollTop: 0
        }, 600);
        return false;
    });
}


function filter_checkbox(selector, type, quantity="more"){
    // View choose brand product
    // var selected = [];
    // $(selector+' input:checkbox').each(function() {
    //     var checkbox = getUrlParameter(type);
    //     if (typeof checkbox === "undefined") { return false; }
    //     checkbox = checkbox.split(',');
    //     if(jQuery.inArray($(this).val(), checkbox) !== -1){
    //         $(this).attr('checked','checked')
    //             selected.push($(this).val());
    //             $(this).addClass('current');
    //     }
    // });
    // $(selector).on('change','input', function (e) {
    //     var val = [];
    //     var ROOT_PRODUCT = document.URL;
    //     if(quantity=="more"){
    //         $(selector+' :checkbox:checked').each(function(i){
    //             val[i] = $(this).val();
    //         });
    //         val = val.join(",");
    //     }else{
    //         // $(selector+' :checkbox:checked').each(function(i){
    //         val = $(this).val();
    //         // });
    //     }
    //     if(document.URL.indexOf('/?') > -1){
    //         var typeValue = getUrlParameter(type);
    //         if(document.URL.indexOf(type+'=') > -1 || document.URL.indexOf('?'+type+'=') > -1 && typeValue != ''){
    //             if(typeValue != ''){
    //                window.location = replaceUrlParam(ROOT_PRODUCT,type,val);
    //                return false;
    //             }
    //             else{
    //                window.location = document.URL.replace(type+'=',type+'='+val)
    //                return false;
    //             }
    //         }else{
    //             window.location = ROOT_PRODUCT + '&'+type+'=' + val;
    //             return false;
    //         }
    //     }
    //     var lastChar = ROOT_PRODUCT.substr(ROOT_PRODUCT.length - 1); // => "1"
    //     if(lastChar == '/'){
    //         window.location = ROOT_PRODUCT + '?'+type+'=' + val;
    //     }else{
    //         window.location = ROOT_PRODUCT + '/?'+type+'=' + val;
    //     }
    // });

    $(selector).on('change','input', function (e) {
        var val = [];
        var ROOT_PRODUCT = $('input[name="sort"]').val();

        if(quantity=="more"){
            $(selector+' :checkbox:checked').each(function(i){
                val[i] = $(this).val();
            });
            val = val.join(",");
            $(this).toggleClass('current');
        }else{
            // $(selector+' :checkbox:checked').each(function(i){
            val = $(this).val();
            // });
            $(selector + ' input').removeClass('current');
            $(this).toggleClass('current');
        }

        var typeValue = getUrlParameter(type);
        if(ROOT_PRODUCT.indexOf(type+'=') > -1 || ROOT_PRODUCT.indexOf('?'+type+'=') > -1 && typeValue != ''){
            if(typeValue != ''){
                $('input[name="sort"]').val(replaceUrlParam(ROOT_PRODUCT,type,val));
                imsProduct.load_more(1);
                return false;
            }
            else{
                $('input[name="sort"]').val(ROOT_PRODUCT.replace(type+'=',type+'='+val));
                imsProduct.load_more(1);
                return false;
            }
        }else{
            $('input[name="sort"]').val(ROOT_PRODUCT + '&'+type+'=' + val);
            imsProduct.load_more(1);
            return false;
        }
    });
}

// - quantity +
function cal_quantity(){
    $(".btn_grp").each(function(){
        var quantity = $(this).children('.quantity_text').val(),
        max = $(this).children('.quantity_text').attr("max");
        $(this).children('.quantity_text').on('propertychange change click keyup input paste',function(){
            quantity = $(this).val();
        })
        $(this).children('.btn_plus').on('click',function(){
            quantity = parseInt(quantity)+1;        
            if(quantity>max)  quantity=max;
            $(this).parent().children('.quantity_text').val(quantity);
        })
        $(this).children('.btn_minus').on('click',function(){            
            quantity = parseInt(quantity)-1;
            if(quantity<1) quantity=1;
            $(this).parent().children('.quantity_text').val(quantity);
        })    
    })
}

function load_ver_product(data){
    $.ajax({
        type: "POST",
        url: ROOT+"ajax.php",
        data: { "m" : "product", "f" : 'loadProductOption', 'lang' : lang, 'data': data },
    }).done(function(string){
        console.log(string);
    })
}

function appendBtnMore(selector){
    $(selector+" .text").each(function(){
        if(!$(this).hasClass("hide")) $(this).addClass("hide");
        if($(this).children("article").height()>400){
            if($(this).parent().find(".btn_more").length==0){
                $(this).parent().append("<span class='btn_more'>"+lang_js_mod["product"]["more"]+"</span>");    
            }            
            $(selector+" .btn_more").on("click",function(){
                $(this).parent().find(".text").removeClass("hide"),$(this).remove();
            })
        }
    })
}

function wrapFilter(selector,n) {    
    var members = $(selector+" >li");
    for (var i = 0; i < members.length; i += n) {
        members.slice(i, i + n).wrapAll("<div class='group'></div>");
    }   
    $(selector).find(".group").not(':eq(0)').addClass("toggle");
    btnMoreLess(selector,n);
}

function btnMoreLess(selector,n){
    var visible = false;
    $(selector+" a.togglemenu").remove();
    if($(selector+" .group").length>1){
        $(selector).append("<a class='togglemenu'>"+lang_js['seemore']+"</a>");  
        visible = true;
    }else{
        $(selector+" a.togglemenu").remove();
        visible = false;
    }
    $(selector+" a.togglemenu").on("click",function() {        
        var link = $(this);
        $(this).parent().find('.toggle').slideToggle('fast', function() {
            $(this).is(":visible")?link.text(lang_js['seeless']):link.text(lang_js['seemore']);
        });
    });    
}
var getUrlParameter = function getUrlParameter(sParam) {
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
function replaceUrlParam(url, paramName, paramValue) {
    if (paramValue == null) {
        paramValue = '';
    }
    var pattern = new RegExp('\\b('+paramName+'=).*?(&|#|$)');
    if (url.search(pattern)>=0) {
        return url.replace(pattern,'$1' + paramValue + '$2');
    }
    url = url.replace(/[?#]$/,'');
    return url + (url.indexOf('?')>0 ? '&' : '?') + paramName + '=' + paramValue;
}
function removeURLParameter(url, parameter) {
    var urlparts= url.split('?');   
    if (urlparts.length>=2) {
        var prefix= encodeURIComponent(parameter)+'=';
        var pars= urlparts[1].split(/[&;]/g);
        for (var i= pars.length; i-- > 0;) {    
            if (pars[i].lastIndexOf(prefix, 0) !== -1) {  
                pars.splice(i, 1);
            }
        }
        url= urlparts[0] + (pars.length > 0 ? '?' + pars.join('&') : "");
        return url;
    } else {
        return url;
    }
}
function updateDiv(selector){ 
    $(selector).load(window.location.href + selector);
}
function getTimeRemaining(endtime) {
  var t = Date.parse(endtime) - Date.parse(new Date());
  var seconds = Math.floor((t / 1000) % 60);
  var minutes = Math.floor((t / 1000 / 60) % 60);
  var hours = Math.floor((t / (1000 * 60 * 60)) % 24);
  var days = Math.floor(t / (1000 * 60 * 60 * 24));
  return {
    'total': t,
    'days': days,
    'hours': hours,
    'minutes': minutes,
    'seconds': seconds
  };
}
function initializeClock(selector, endtime) {  
  var clock = $(selector);
  var daysSpan = clock.children().find(".days");
  var hoursSpan = clock.children().find(".hours");
  var minutesSpan = clock.children().find(".minutes");
  var secondsSpan = clock.children().find(".seconds");
  function updateClock() {
    var t = getTimeRemaining(endtime);
    var days = t.days;
    var hours = ('0' + t.hours).slice(-2);
    var minutes = ('0' + t.minutes).slice(-2);
    var seconds = ('0' + t.seconds).slice(-2);    
    daysSpan.text(days);
    hoursSpan.text(hours);
    minutesSpan.text(minutes);
    secondsSpan.text(seconds);
    if (t.total <= 0) {
        clearInterval(timeinterval);         
        clock.fadeOut("fast");
    }    
    $(selector+" >div >div").removeClass("highlight");
    if(days==0 && hours=="00" && minutes=="00"){        
        secondsSpan.parent().addClass("highlight");
    }else if(days==0 && hours=="00"){
        minutesSpan.parent().addClass("highlight");
    }else if(days==0){
        hoursSpan.parent().addClass("highlight");
    }else{
        daysSpan.parent().addClass("highlight");
    }    
  }
  updateClock();
  var timeinterval = setInterval(updateClock, 1000);
}
function countDown(selector,endtime,status=1){
    var selector = $(selector);    
    function updateTime(){
        var t = getTimeRemaining(endtime);        
        if (t.total <= 0) {
            clearInterval(timeinterval);            
            status==1?selector.fadeIn("fast"):selector.fadeOut("fast");
        }    
    }
    updateTime();
    var timeinterval = setInterval(updateTime, 1000);
}
function xoaDau(str) {
    str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g, "a");
    str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g, "e");
    str = str.replace(/ì|í|ị|ỉ|ĩ/g, "i");
    str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g, "o");
    str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, "u");
    str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g, "y");
    str = str.replace(/đ/g, "d");
    str = str.replace(/À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ/g, "A");
    str = str.replace(/È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ/g, "E");
    str = str.replace(/Ì|Í|Ị|Ỉ|Ĩ/g, "I");
    str = str.replace(/Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ/g, "O");
    str = str.replace(/Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ/g, "U");
    str = str.replace(/Ỳ|Ý|Ỵ|Ỷ|Ỹ/g, "Y");
    str = str.replace(/Đ/g, "D");
    return str;
}
function centerSelect($elm) {
    $(window).on("load resize",function(){
        var optionWidth = getTextWidth($elm.children(":selected").html()),
        emptySpace =   $elm.width()- optionWidth;
        $elm.css("text-indent", (emptySpace/2) - 10);// -10 for some browers to remove the right toggle control width    
    })    
}
function countUpWaypoint(selector,duration=5000){
    new Waypoint( {
        element: selector,
        handler: function() { 
            countUp(selector,duration);
            this.destroy()
        },
        offset: 'bottom-in-view',
    })    
}
function countUp(selector,duration=5000){
    $(selector).each(function() {
        var $this = $(this),
          countTo = $this.attr('data-count');
        $({ countNum: $this.text()}).animate({
            countNum: countTo
        },{
            duration: duration,
            easing:'linear',
            step: function() { $this.text(Math.floor(this.countNum));},
            complete: function() {$this.text(this.countNum);}
        });
    });
}

function base64ToBlob(base64, mime){
    mime = mime || '';
    var sliceSize = 1024;
    var byteChars = window.atob(base64);
    var byteArrays = [];
    for (var offset = 0, len = byteChars.length; offset < len; offset += sliceSize) {
        var slice = byteChars.slice(offset, offset + sliceSize);
        var byteNumbers = new Array(slice.length);
        for (var i = 0; i < slice.length; i++) {
            byteNumbers[i] = slice.charCodeAt(i);
        }
        var byteArray = new Uint8Array(byteNumbers);
        byteArrays.push(byteArray);
    }
    return new Blob(byteArrays, {type: mime});
}

function UploadWithCrop(group_selector,frame_width = 100, frame_height = 100) {
    var $uploadCrop;    
    var sCrop = $(group_selector['crop']), //Khung cắt ảnh
        sUpload = $(group_selector['upload']), //nút chọn hình
        sResult = $(group_selector['result']), //nút xuất hình
        sImg = $(group_selector['preview']); //khung hình preview
    function readFile(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();            
            reader.onload = function (e) {
                sCrop.addClass('ready');
                $uploadCrop.croppie('bind', {
                    url: e.target.result
                }).then(function(){
                    console.log('jQuery bind complete');
                });
                
            }            
            reader.readAsDataURL(input.files[0]);
        }
        else {
            swal("Sorry - you're browser doesn't support the FileReader API");
        }
    }

    $uploadCrop = sCrop.croppie({
        viewport: {
            width: frame_width,
            height: frame_height,            
        },
        enableExif: true
    });

    sUpload.on('change', function () { readFile(this); });
    sResult.on('click', function (ev) {
        $uploadCrop.croppie('result', {
            type: 'canvas',
            size: 'viewport'
        }).then(function (resp) {
            $(sImg).attr("src",resp);
            sUpload.val(''); 
        });
    });
}

function lineChart(html_id, data){
    am4core.ready(function() {

    // Themes begin
    am4core.useTheme(am4themes_animated);
    // Themes end

    // Create chart instance
    var chart = am4core.create(html_id, am4charts.XYChart);
    chart.paddingRight = 20;

    // Add data
    data = decodeURIComponent(escape(window.atob(data)));    
    data = JSON.parse(data);    
    chart.data = data;    
    // Create axes
    var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
    categoryAxis.dataFields.category = "date";
    categoryAxis.renderer.minGridDistance = 50;
    categoryAxis.renderer.grid.template.location = 0.5;
    categoryAxis.startLocation = 0.5;
    categoryAxis.endLocation = 0.5;

    // Create value axis
    var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
    valueAxis.baseValue = 0;

    // Create series
    var series = chart.series.push(new am4charts.LineSeries());
    series.dataFields.valueY = "value";
    series.dataFields.categoryX = "date";
    series.strokeWidth = 2;
    series.stroke = am4core.color("#FE6505");        
    // series.tensionX = 0.77;

    // bullet is added because we add tooltip to a bullet for it to change color
    var bullet = series.bullets.push(new am4charts.CircleBullet());
    bullet.circle.strokeWidth = 2;
    bullet.circle.radius = 4;
    bullet.circle.fill = am4core.color("#FE6505");
    bullet.tooltipText = "{valueY}";    
    series.tooltip.getFillFromObject = false;
    series.tooltip.background.fill = am4core.color("#FE6505");

    bullet.adapter.add("fill", function(fill, target){
        if(target.dataItem.valueY < 0){
            return am4core.color("#FF0000");
        }
        return fill;
    })

    // var range = valueAxis.createSeriesRange(series);
    // range.value = 0;
    // range.endValue = -1000;
    // range.contents.stroke = am4core.color("#FE6505");
    // range.contents.fill = range.contents.stroke;

    // Add scrollbar
    // var scrollbarX = new am4charts.XYChartScrollbar();
    // scrollbarX.series.push(series);
    // chart.scrollbarX = scrollbarX;

    // chart.cursor = new am4charts.XYCursor(); //zoom

    }); // end am4core.ready()
}

function do_checkall() {
    $(document).on("click", "#checkall", function (e) {
        var c = $(this).prop('checked');
        var tbody = $(this).parents("table").find('tbody');        
        tbody.find('tr').each(function () {
            var checkbox = $(this).find('td.cot:eq(0) input[type=checkbox]');            
            if (c) {
                checkbox.prop('checked', true);
                $(this).addClass('active');
                //return true;
            } else {
                checkbox.prop('checked', false);
                $(this).removeClass('active');
                //return false; 
            }
        });
    });
}

function ping(url){
    return Promise.resolve($.ajax({
        type: "POST",
        url: url, 
        error: function(result){
            Swal.fire({
                icon: 'error',
                title: lang_js['aleft_title'],
                html: lang_js['server_error'],
            });
        }       
    }));
}

$( document ).ready(function() {
    // Change quantity
    $('.quantity_text').on('change', function (e) {
        var ajax_send = false;
        if (ajax_send == true){
            return false;
        }
        ajax_send = true;
        var val = $(this).val();
        var valueAttr =  $(this).attr('max');
        if(!val) { return false; }
        if(!valueAttr) { return false; }
        if(parseInt(val) > parseInt(valueAttr)){
            alert('Số lượng phải <= ' + valueAttr);
            $(this).val(1);
            ajax_send = false;

        }
        else if(parseInt(val) < 0){
            $(this).val(1);
            ajax_send = false;
        }
    });
    cal_quantity();
    // Click toggle right cart
    var ajax_send = false;
    $(document).on('click', '.order-box-right .collapse', function () {
        if (ajax_send == true){
            return false;
        }
        ajax_send = true;
        $header = $(this);
        $content = $header.parent().parent().next();
        $content.slideToggle(500, function () {
            if( $content.is(":visible") ) {
                $header.html("<i class='fal fa-angle-down'></i>");
                ajax_send = false;
            }
            else{
                $header.html("<i class='fal fa-angle-up'></i>"); 
                ajax_send = false;
            }
        });
    });
    
    // Remove filter
    // $( ".filter_items" ).on("click","li", function( event ) {
    //     event.preventDefault();
    //     var ROOT_PRODUCT = document.URL,
    //     valueAttr = $(this).attr('data-value'),
    //     typeAttr = $(this).attr('data-type'),
    //     view_group = getUrlParameter(typeAttr);
    //     if(!valueAttr) { return false; }
    //     if(valueAttr == 'clear-all'){
    //         window.location = document.URL.substring(0, document.URL.indexOf('?'));
    //     }
    //     if(typeof view_group === "undefined") { return false; }
    //     if(view_group.indexOf(',') > -1){
    //         view_group = view_group.split(',');
    //         view_group = jQuery.grep(view_group, function(value) {
    //           return value != valueAttr;
    //         });
    //         valueAttr = view_group.join(",")
    //         window.location = replaceUrlParam(ROOT_PRODUCT, typeAttr, valueAttr);
    //     }
    //     else{
    //         window.location = removeURLParameter(document.URL, typeAttr);
    //     }
    // });

    $(document).on("click", ".filter_items li", function( event ) {
        event.preventDefault();
        var ROOT_PRODUCT = $('input[name="sort"]').val(),
            valueAttr = $(this).attr('data-value'),
            typeAttr = $(this).attr('data-type');
        if(!valueAttr) { return false; }
        if(valueAttr == 'clear-all'){
            $('input[name="sort"]').val('');
            $('.box_l_product li input, .content_list_price input').removeClass('current').prop('checked', false);
            $('.filter_product .box_filter').remove();
        }else{
            if(typeAttr == 'brand'){
                $('.box_trademark input[value='+valueAttr+']').click();
            }
            if(typeAttr == 'price'){
                ROOT_PRODUCT = ROOT_PRODUCT.replace('&'+typeAttr+'='+valueAttr, '');
                $('.content_list_price input[value='+valueAttr+']').prop('checked', false).removeClass('current');
                $('input[name="sort"]').val(ROOT_PRODUCT);
            }
            if(typeAttr == 'nature'){
                $('.content_list_nature input[value='+valueAttr+']').click();
            }
            if(typeAttr == 'origin'){
                $('.content_list_origin input[value='+valueAttr+']').click();
            }
        }
        imsProduct.load_more(1);
        $(this).remove();
        if($('.filter_items li').length == 1){
            $('.filter_product .box_filter').remove();
        }
    });

    $(window).scroll(function() {
      if ($(window).scrollTop() != 0) {
          $('#BactoTop').fadeIn();
      } else {
          $('#BactoTop').fadeOut();
      }
    });
    $('#BactoTop').click(function() {        
        $('html, body').animate({scrollTop: 0}, 600);
    });    

    // showInputRating
    var check_send = false;
    $(document).on("click", ".showInputRating", function (e) {
        e.preventDefault();
        if (check_send == true){
            return false;
        }
        check_send = true;
        $header = $(this);
        $content = $('.fRatingComment');
        $content.slideToggle(500, function () {
            if( $content.is(":visible") ) {
                check_send = false;
            }
            else{
                check_send = false;
            }
        });
    });
    //autocomplete search
    $(".text_search").on("change keyup",function(){
        let id = $(this).attr("id");
        if($(this).val() != ''){
            $(this).parent().find(".btn_clear").css("display","block");
            $(".wrap-suggestion-"+id).addClass("d-block")
        }else{            
            $(".btn_clear").removeAttr("style");
            $(".wrap-suggestion-"+id).removeClass("d-block");
        }
    })
    $("form[id^=form_search]").on("click",".btn_clear",function(){
        let input = $(this).parent().find(".text_search");
        if(input.val() != ''){
            $(this).removeAttr("style");
            input.val(''); $(".wrap-suggestion-"+input.attr("id")).removeClass("d-block");            
        }
    })
    
    $(window).on("load resize",function(){
        $("[class*=\"wrap-suggestion\"]").hasClass("d-block")?$("[class*=\"wrap-suggestion\"]").removeClass("d-block"):'';        
        if($(window).width()>992){
            // $.fancybox.close()
            $("#box_filter_left .fancybox-close-small").click();            
            $("#box_filter_left").css("display","");
        }
        //show more less article 
        
        // $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        //     appendBtnMore(".article");    
        // })        
    })
    
    // appendBtnMore(".article");
});

$(window).on("load resize", function(){
    var h = $('header').innerHeight() + $('footer').innerHeight(),
        hsc = $(window).height();
    var hcss = hsc - h;
    $('#container').css('min-height', hcss);
});

$(function () {
    var msie6 = $.browser == 'msie' && $.browser.version < 7;
    if (!msie6) {
        var check_header = $('.header-top').innerHeight();
        // var check_header = 1;
        // var h = $('header-top').innerHeight() + $('header-bottom').innerHeight();
        var h = $('.header-bottom').innerHeight();
        $(document).scroll(function (event) {
            var y = $(this).scrollTop();
            if (window.matchMedia('(min-width: 319px)').matches) {
                if(y > check_header){
                    // $('header').addClass('fixed_menu');
                    $('.header-bottom').addClass('fixed_menu');
                    // $('#ims-wrapper').css('padding-top', 51+'px');
                    $('#ims-wrapper').css('padding-top', h+'px');
                }else{
                    // $('header').removeClass('fixed_menu');
                    $('.header-bottom').removeClass('fixed_menu');
                    $('#ims-wrapper').css('padding-top', '0px');
                }
            }else{
                $('.header-bottom').removeClass('fixed_menu');
            }
        });
    }
});

$(document).on('click', 'a.goto', function () {
    var id = $(this).attr('href');
    var offset = $(id).offset();
    var other = 0;
    if($('.info_pc').length){
        other = $('.info_pc').innerHeight();
    }
    $('html, body').animate({scrollTop: offset.top-52-other});
});

$(document).on('click', '.menu-wrapper li.dropdown > a', function (e) {
    e.preventDefault();
    $(this).toggleClass('open').next().toggleClass('open').slideToggle();
});