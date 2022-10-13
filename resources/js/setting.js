main_menu();
// auto_price_format();
$('.centerSelect').each(function(){
   centerSelect($(this));
});
$('.centerSelect').on('change', function(){
   centerSelect($(this));
});

// select2 select 
$('select.form-control').select2({
    // placeholder: "Vui lòng chọn",
    // allowClear: true
}).on('change', function() {
    $(this).valid();
});

// box_menu();
$(document).on("click", ".add-to-cart-success .btn-close" , function(){
     $('#header_cart .add-to-cart-success').hide();
 });
if ( window.history.replaceState ) {
    window.history.replaceState( null, null, window.location.href );
}
// tablescroll_fix();
// $(window).resize(function () {
//     tablescroll_fix();
// });
// if(deviceType == 'computer'){
//    $("select.form-control:not(.no-chosen)").chosen();
// }

//
//$('.box_search .text_search').focusin(function(event) {
//	$(this).parent('form').addClass('focus');
//}).focusout(function(event) {
//	$(this).parent('form').removeClass('focus');
//});
//
//$('.box_search .btn_search').click(function(event) {
//	$(this).siblings('.text_search').focus();
//});
//
//$('.box_search form').submit(function(event) {
//   var keyword = $(this).children('.text_search').val()
//	if(keyword == '') {
//		return false;
//	}
//   go_link($(this).attr('action')+'?keyword='+keyword);
//   return false;
//});
//
// $(function() {
// 	// $('.short, .cut_tring').dotdotdot();
// 	// scrollTop ();
// 	// column_right_bottom ();
// 	sh_scroll_banner();
// 	setTimeout(function(){sh_scroll_banner();}, 500);
// });
// $(window).scroll(function () {
// 	// column_right_bottom ();
// 	sh_scroll_banner();
// });
// $(window).resize(function(e) {
// 	// $('.short, .cut_tring').dotdotdot();
// 	// column_right_bottom ();
// 	sh_scroll_banner();
// });

$(document).on('click', 'header .button_menu', function () {
    $('.menu_logo').toggleClass('collapse');
    $('.main_content').toggleClass('extend');
});
$(document).on('change', '.check_all', function () {
    if($(this).is(':checked')){
        $('.table-responsives tbody .checkbox input').prop("checked", true);
        $('.header_content .list_action_root li button:not(.default)').addClass('active');
    }else{
        $('.table-responsives tbody .checkbox input').prop("checked", false);
        $('.header_content .list_action_root li button:not(.default)').removeClass('active');
    }
});
$(document).on('change', '.table-responsives tbody .checkbox input', function () {
    var check = 0;
    $('.table-responsives tbody .checkbox input').each(function (i, v) {
        if($(this).is(':checked')){
            check++;
        }
    });
    if(check > 0){
        $('.header_content .list_action_root li button:not(.default)').addClass('active');
    }else{
        $('.header_content .list_action_root li button:not(.default)').removeClass('active');
    }
});