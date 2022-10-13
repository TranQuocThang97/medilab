<?php 
if ( !defined('IN_ims') )	{ die('Access denied');	} 
$lang = array ( 
	'mod_title' => 'Sản phẩm',
	'no_have_item' => 'Chưa có sản phẩm',
//    'your_comments' => 'Ý kiến của bạn (vui lòng gõ Tiếng Việt có dấu)',
//    'wcoin_proportion' => 'Tỷ lệ 1 điểm =',
//    'wcoin_have' => 'Điểm hiện tại',
//    'wcoin_note' => 'Vui lòng điển số điểm để giảm giá vào ô bên dưới và click xác nhận',
//    'wcoin_expected' => 'Điểm tích lũy dự kiến :',
//    'wcoin_code_label' => 'Nhập điểm',
//    'watched_product' => 'Sản phẩm đã xem',
//    'ward' => 'Phường/ Xã',
//    'view_more' => 'Xem thêm',
//    'view_detail_promotion' => 'Xem chi tiết khuyến mãi',
//    'view_detail' => 'Xem chi tiết tại tây',
//    'view_by' => 'Ưu tiên xem',
//    'view' => 'Lượt xem',
//    'verification' => 'Mã xác nhận',
//    'trademark' => 'Thương hiệu',
//    'transport_fee' => 'Phí vận chuyển',
//    'total' => 'Tổng tiền',
//    'total_product' => 'Tổng cộng [num] sản phẩm',
//    'title_desc' => 'Tên: Z-A',
//    'text_note' => '(Khách hàng xem hàng trước khi nhận)',
//    'text_ok' => 'Áp dụng ngay',
//    'text_pay' => 'Tiếp tục thanh toán',
//    'text_phone' => 'Nhập điện thoại',
//    'title_asc' => 'Tên: A-Z',
//    'text_email' => 'Nhập email',
//    'text_full_name' => 'Nhập họ và tên',
//    'text_complete_order' => 'Hoàn tất đơn hàng',
//    'text_address' => 'Nhập địa chỉ',
//    'success_notification' => 'Nhận thông báo sản phẩm thành công.',
//    'tags' => 'Tags',
//    'tax_code' => 'Mã số thuế',
//    'success_order' => 'Tìm thấy đơn hàng.',
//    'success_comment' => 'Đăng bình luận thành công.',
//    'stock_desc' => 'Bán chạy',
//    'status_stock1' => 'Còn hàng',
//    'status_stock0' => 'Hết hàng',
//    'status_stock' => 'Tình trạng',
    'sku' => 'Mã SP',
//    'sort_by' => 'Sắp xếp',
//    'sort_trademark' => 'Lọc theo thương hiệu',
//    'spam_comment' => 'Bạn bình luận quá nhanh ! vui lòng thử lại sau.',
//    'share' => 'Chia sẻ sự kiện',
//    'shipping_address' => 'Địa chỉ',
//    'signup_new' => 'ĐĂNG KÍ NGAY',
//    'skip_login' => 'Nếu muốn đặt hàng không cần ĐĂNG NHẬP ấn vào nút Tiếp tục ở bên dưới.',
//    'send' => 'Gửi',
//    'series' => 'ID',
//    'server_promo' => 'Dịch vụ và khuyến mãi',
//    'select' => 'Chọn',
//    'search_price' => 'Lọc giá',
//    'save_wcoin' => 'Giảm giá với điểm tích lũy',
//    'save_method' => 'Giảm giá khi thanh toán ATM',
//    'remaining_quantity_product' => 'Số lượng còn lại của sản phẩm là',
//    'same_address' => 'Giống địa chỉ đặt hàng',
//    'review' => 'đánh giá',
//    'rest' => 'Còn lại',
//    'request_more' => 'Yêu cầu khác',
//    'rating' => 'Đánh giá',
//    'rate' => 'Đánh giá',
//    'provisional' => 'Tạm tính',
//    'quantity' => 'Số lượng',
//    'province' => 'Tỉnh/ Thành phố',
//    'promotional_code' => 'Mã giảm giá/Phiếu mua hàng',
//    'promotional_code_note' => 'Vui lòng điền mã code giảm giá (Nếu có) vào ô bên dưới và click Áp dụng',
//    'promotional_code_label' => 'Nhập mã của bạn',
//    'promotion_success' => 'Áp dụng mã khuyến mãi thành công',
//    'promotion_end' => 'Giờ vàng ngày hôm nay đã kết thúc. Mong quý khách vui lòng quay lại vào giờ vàng ngày mai',
//    'promotional' => 'Mã khuyến mãi',
//    'promotion' => 'Khuyến mãi',
//    'products' => 'sản phẩm',
//    'products_incart' => 'Sản phẩm trong giỏ hàng',
//    'product_out_stock' => 'Sản phẩm [title] đã hết hàng',
//    'product_review' => 'đánh giá sản phẩm',
//    'product_note' => '',
//    'product_group' => 'Danh mục sản phẩm',
//    'product' => 'Sản phẩm',
//    'print_order_detail' => 'In đơn hàng',
//    'print' => 'In',
//    'price_saving' => 'Tiết kiệm',
//    'price_buy' => 'Giá',
//    'price_desc' => 'Giá giảm dần',
//    'price_market' => 'Giá thị trường',
//    'price_asc' => 'Giá tăng dần',
//    'price' => 'Giá gốc',
//    'other_product' => 'Sản phẩm liên quan',
//    'out_of_stock' => 'đã hết hàng',
//    'payment_orderbuy' => 'Thanh toán',
    'phone' => 'Số điện thoại',
//    'please_login' => 'Vui lòng Đăng nhập để sử dụng điểm tích lũy',
//    'other_address' => 'Bạn muốn giao hàng đến địa chỉ khác?',
//    'orther_price' => 'Mức giá',
//    'order_info' => 'Thông tin đơn hàng',
//    'order_method_title' => 'Bạn có thể chọn một trong các hình thức thanh toán sau',
//    'ordering_shipping' => 'Phương thức giao hàng',
//    'ordering_method' => 'Phương thức thanh toán',
//    'ordering_address' => 'Thông tin đặt hàng',
//    'ordering_complete' => 'Hoàn tất đơn hàng',
//    'order_address_new' => 'Thêm địa chỉ giao hàng mới',
//    'order_address_note' => 'Chọn địa chỉ giao hàng có sẵn bên dưới:',
//    'order_address_edit' => 'Cập nhật địa chỉ giao hàng',
//    'order_address' => 'Địa chỉ giao hàng',
//    'order' => 'Đơn hàng',
//    'not_rated' => 'Không đánh giá',
//    'notification_product' => 'Nhận thông báo khi sản phẩm có lại (trường hợp khi hết hàng):',
//    'option_color' => 'Màu sắc',
//    'option_material' => 'Chất liệu',
//    'option_size' => 'Kích thước',
//    'option_style' => 'Hình dạng',
//    'new_product' => 'Mới nhất',
//    'need_login_comment' => 'Đăng nhập để gửi đánh giá.',
//    'need_login' => 'Vui lòng đăng nhập để sử dụng chức năng này.',
//    'more' => 'Xem thêm',
//    'more_nature' => 'Xem thêm tính năng khác',
//    'menu_title' => 'Danh mục',
//    'login_benefit' => 'Đăng nhập để theo dõi đơn hàng, lưu danh sách sản phẩm yêu thích, nhận nhiều ưu đãi hấp dẫn.',
//    'list' => 'Ngang',
//    'login' => 'Đăng nhập',
//    'invoice' => 'Yêu cầu xuất hóa đơn đỏ cho đơn đặt hàng này',
//    'less' => 'Thu gọn',
//    'limited_quantity' => 'Số lượng giới hạn',
//    'input_text_info' => 'Vui lòng nhập đầy đủ thông tin yêu cầu.',
//    'hot_product' => 'Sản phẩm hot nhất',
//    'guarantee' => 'Bảo hành',
//    'grid' => 'Dọc',
//    'full_name' => 'Họ và tên',
//    'general' => 'Thông tin chung',
//    'form_signup_title' => 'Khách hàng mới - Thông tin cá nhân',
//    'freeship' => 'FREESHIP',
//    'form_signin_title' => 'Khách hàng mới / Đăng nhập',
//    'filter_title' => 'Bộ lọc tìm kiếm',
//    'filter_title_sm' => 'Bộ lọc',
//    'facebook_commtent' => 'BÌNH LUẬN CỦA BẠN',
//    'filter' => 'Lọc theo',
//    'error_order' => 'Không tìm thấy đơn hàng.',
//    'error_notification1' => 'Nhận thông báo sản phẩm thất bại.',
//    'error_notification' => 'Đã nhận thông báo sản phẩm này rồi.',
//    'err_promotion_wrong' => 'Mã giảm giá không đúng',
//    'error_comment' => 'Đăng bình luận thất bại',
//    'err_promotion_timeover' => 'Mã khuyến mãi này đã hết hạn',
//    'err_promotion_user' => 'Mã khuyến mãi này không dành cho bạn',
//    'err_promotion_notyet_timetouse' => 'Mã khuyến mãi chưa đến thời gian sử dụng',
//    'err_promotion_numover' => 'Mã khuyến mãi này đã hết lượt sử dụng',
//    'err_promotion_product' => 'Sản phẩm được áp dụng mã này không có trong giỏ hàng',
//    'err_promotion_min_cart' => 'Đơn hàng phải lớn hơn hoặc bằng {min_cart} mới có thể sử dụng được mã khuyến mãi',
//    'err_promotion_max_use' => 'Mã khuyến mãi đã hết lần sử dụng',
//    'err_gift_voucher_no_amount' => 'voucher đã hết tiền',
//    'err_promotion_date_end' => 'Mã khuyến mãi hết hạn',
//    'err_gift_voucher_date_end' => 'voucher đã hết hạn',
//    'eror_order_address' => 'Vui lòng nhập địa chỉ nhận hàng',
//    'enter_code' => 'Nhập mã xác nhận',
//    'discounts_wcoin_mail' => 'Giảm giá dùng điểm tích lũy',
//    'end' => 'đã kết thúc',
//    'edit' => 'Sửa',
    'email' => 'Email',
//    'district' => 'Quận/ Huyện',
//    'discounts_wcoin' => 'Giảm giá bằng điểm',
//    'description' => 'Mô tả',
//    'delivery' => 'Vận chuyển',
//    'delivery_address' => 'Giao đến',
    'delete' => 'Xóa',
//    'delete_all' => 'Xóa tất cả',
//    'default_address' => 'Sử dụng địa chỉ này làm mặc định.',
//    'default' => 'Mặc định',
//    'current_model' => 'Bạn đang xem phiên bản',
//    'date_update' => 'Cập nhật',
//    'confirm_address' => 'Giao đến địa chỉ này',
//    'contact' => 'Liên hệ',
//    'confirm' => 'Xác nhận',
//    'completed_event' => 'Chương trình đã kết thúc',
//    'complete' => 'Hoàn tất',
//    'company_name' => 'Tên công ty',
//    'comment_rate' => 'Đánh giá & nhận xét',
//    'comment' => 'Bình luận',
//    'col_total' => 'Tổng tiền',
//    'col_title' => 'Sản phẩm',
//    'col_size' => 'Size',
//    'col_quantity' => 'Số lượng',
//    'col_price' => 'Giá',
//    'col_picture' => 'Hình ảnh',
//    'col_delete' => 'Xóa',
//    'col_color' => 'Màu',
//    'col_cart_later' => 'Mua sau',
//    'col_code_pic' => 'Mã hình',
//    'clear_nature' => 'Bỏ tất cả lựa chọn',
//    'choose_ordering_shipping' => 'Chọn gói giao hàng',
//    'choose_ordering_method' => 'Vui lòng chọn phương thức thanh toán!',
//    'choose_gift' => 'Chọn quà tặng (ấn vào hình để chọn)',
//    'choose_as_option' => 'choose as option ...',
//    'cart_total' => 'TỔNG GIỎ HÀNG',
//    'cart_payment' => 'TỔNG TIỀN',
//    'cart' => 'Giỏ hàng',
//    'cart_empty' => 'Giỏ hàng chưa có sản phẩm nào',
    'cancel' => 'Hủy',
//    'call_order' => 'Gọi đặt mua',
//    'btnsort' => 'Lọc',
//    'btnclear' => 'Bỏ tất cả lựa chọn',
//    'btn_use_code' => 'Áp dụng ngay',
//    'btn_update' => 'Cập nhật',
//    'btn_submit' => 'Xác nhận',
//    'btn_skip_login' => 'Skip login',
//    'btn_payment_ok' => 'Tiến hành đặt hàng',
//    'btn_payment' => 'Thanh toán',
//    'btn_order_now' => 'Đặt hàng ngay',
//    'btn_detail' => 'Chi tiết',
//    'btn_complete_ok_end' => 'Hoàn tất đặt hàng',
//    'btn_complete_ok' => 'Đặt mua',
//    'btn_buy_more' => '← Tiếp tục xem sản phẩm',
//    'btn_complete' => 'Hoàn tất',
//    'btn_add_cart_more' => 'Giao hàng tận nơi hoặc nhận tại cửa hàng',
//    'btn_add_cart_now' => 'Mua ngay',
//    'btn_add_cart' => 'Thêm vào giỏ',
//    'brand' => 'Thương hiệu',
//    'arr_group_nature' => 'Tính năng',
//    'add_cart_success' => 'Thêm vào giỏ hàng thành công',
//    'add_other_address' => 'Thêm địa chỉ giao hàng mới',
    'address' => 'Địa chỉ',
//    'apply' => 'Áp dụng',
//    'add_cart_false' => 'Không thể thêm vào giỏ hàng',
//    'add_address' => 'Thêm địa chỉ',
//    '5_star' => 'Tuyệt vời - 5 sao',
//    '2_star' => 'Để xem lại - 2 sao',
//    '4_star' => 'Tốt đây - 4 sao',
//    '3_star' => 'Tạm được - 3 sao',
//    '1_star' => 'Không tốt - 1 sao',
//    'err_cart_total' => 'Đơn hàng tối thiểu',
//    'no_price' => 'Liên hệ',
//    'trogia' => 'Trợ giá MÙA DỊCH',
//    'discount' => 'Giảm giá trong ngày',
//    'see_all' => 'Xem tất cả',
//    'shock_discount' => 'giảm giá sốc',
//    'endow' => 'Ưu đãi thêm',
//    'specifications' => 'Thông số sản phẩm',
//    'detail_specifications' => 'Xem chi tiết thông số',
//    'call_now' => 'Gọi ngay',
//    'detail_content_title' => 'Thông tin sự kiện',
//    'discount_day' => 'giảm giá trong ngày',
//    'short_cut' => 'Thu gọn',
//    'shock_title' => 'Deal giá sốc',
//    'view_more_trogia' => 'Xem thêm [num] sản phẩm',
//    'ordering_method_complete' => 'Các hình thức thanh toán',
//    'phone_recieve' => 'ĐT',
//    'reciever' => 'Người nhận hàng',
//    'manage_order' => 'Quản lý đơn hàng',
//    'combo_type_0' => 'Quà tặng kèm khi mua combo',
//    'combo_type_1' => 'Giảm giá khi mua combo',
//    'combo_type_2' => 'Giảm giá sản phẩm khi mua kèm combo',
//    'include_combo' => 'Combo gồm các sản phẩm',
//    'gift' => 'quà tặng',
//    'choose' => 'Chọn',
//    'out_of_gift' => 'Số lượng quà tặng đã hết',
//    'not_yet_chose_include' => 'Bạn chưa chọn sản phẩm',
//    'not_yet_chose_gift' => 'Bạn chưa chọn quà',
//    'include_out_of_stock' => 'Sản phẩm [include] giảm giá ưu đãi kèm combo đã hết',
//    'success_include' => 'Chọn sản phẩm mua kèm thành công',
//    'success_gift' => 'Chọn quà tặng thành công',
//    'list_chose_num' => 'Đã chọn',
//    'list_gift_title' => 'Chọn quà tặng của bạn',
//    'list_gift_note' => 'Thêm combo sản phẩm vào giỏ hàng trước khi chọn quà tặng',
//    'list_include_title' => 'Sản phẩm ưu đãi kèm combo',
//    'list_include_note' => 'Thêm combo sản phẩm vào giỏ hàng trước khi chọn sản phẩm trong danh sách ưu đãi',
//    'include' => 'Sản phẩm giá ưu đãi',
//    'gift_out_of_stock' => 'Quà tặng [gift] đã hết',
//    'change_gift' => 'Chọn lại quà',
//    'change_include' => 'Đổi sản phẩm mua kèm',
//    'not_enough_quantity' => 'Chỉ còn [num] sản phẩm trong kho',
//    'ordering_shipping_complete' => 'Các hình thức vận chuyển',
//    'time_apply_combo' => 'Áp dụng từ ngày [begin] đến [end]',
//    'out_of_include' => 'Sản phẩm ưu đãi mua kèm combo đã hết',
//    'origin_title' => 'Xuất xứ',
//    'note_chose_include' => 'Chỉ được chọn tối đa [num] sản phẩm trong danh sách ưu đãi',
//    'note_chose_gift' => 'Chỉ được chọn tối đa [num] phần quà kèm theo combo',
//    'decrease' => 'Giảm',
//    'hsd' => 'HSD',
//    'copy' => 'Copy',
//    'promotion_code' => 'Mã khuyến mãi',
//    'free_ship' => 'Free ship',
//    'copied' => 'Đã copy',
//    'err_promotion_login' => 'Mã khuyến mãi chỉ dành cho thành viên có trong chương trình',
//    'cancel_order' => 'Đơn hàng đã hủy thanh toán',
//    'note_input_email' => 'Nhập email để nhận thông báo về đơn hàng',
//    'save_code' => 'Lưu',
//    'saved_code' => 'Đã lưu',
//    'saved_promotion_code' => 'Mã khuyến mãi đã lưu',
//    'valid_promotion_code' => 'Mã khuyến mãi có thể dùng',
//    'quantity_sold' => 'Đã bán',
//    'err_promotion_total_price' => 'Sản phẩm này không thể áp dụng mã',
//    'order_discount_program' => 'Chương trình giảm giá cho tổng đơn hàng',
//    'not_enough_num_product' => 'Chương trình chỉ được áp dụng khi giỏ hàng có tối thiểu từ [num] sản phẩm trở lên',
//    'order_discount_program_title' => 'Giảm giá [percent] cho tổng đơn hàng',
//    'order_bundled_event' => 'Mua sản phẩm kèm theo với giá ưu đãi',
//    'chose_bundled_product' => 'Chọn sản phẩm mua kèm',
//    'change_bundled_product' => 'Đổi sản phẩm mua kèm',
//    'bundled_product' => 'Chọn Sản phẩm mua kèm giá ưu đãi',
//    'max_num_chose_bundled' => 'Chỉ được chọn 1 sản phẩm mua kèm',
//    'bundled_product_cart' => 'Sản phẩm giá ưu đãi mua kèm',
    'active' => 'Kích hoạt',
    'show' => 'Hiển thị',
    'action' => 'Hành động',
    'update_time' => 'Thời gian cập nhật',
    'status' => 'Trạng thái',
    'product_title' => 'Tên SP',
    'picture' => 'Ảnh SP',
	'text_keyword_product' => 'Tìm tên/mã sản phẩm',
	'text_keyword_concern' => 'Tên/SĐT/MST',
	'search' => 'Tìm kiếm',
	'show_button' => 'Hiện',
	'hide_button' => 'Ẩn',
	'add_button' => 'Thêm',
	'add_product_title' => 'Thêm sản phẩm',
	'select_layout' => 'Chọn giao diện hiển thị',
	'select_layout1' => 'Chọn giao diện 1',
	'select_layout2' => 'Chọn giao diện 2',
	'picture_input' => 'Ảnh đại diện sản phẩm',
	'list_picture_input' => 'Ảnh sản phẩm (tối đa 6 hình)',
	'sku_label' => 'Mã sản phẩm',
	'sku_input' => 'Nhập mã sản phẩm',
	'title_label' => 'Tên sản phẩm',
	'title_input' => 'Nhập tên sản phẩm',
	'price_label' => 'Giá sản phẩm',
	'price_input' => 'Nhập giá sản phẩm',
	'volumn_label' => 'Thể tích/Khối lượng của sản phẩm',
	'volumn_input' => 'Nhập Thể tích/Khối lượng',
	'producer_label' => 'Nhà sản xuất',
	'producer_select' => 'Chọn đơn vị sản xuất',
	'hide_producer_label' => 'Ẩn nhà sản xuất',
	'show_producer_label' => 'Hiển thị nhà sản xuất',
	'distributor_label' => 'Nhà phân phối',
	'distributor_select' => 'Chọn nhà phân phối',
	'hide_distributor_label' => 'Ẩn nhà phân phối',
	'show_distributor_label' => 'Hiển thị nhà phân phối',
	'sales_link' => 'Link bán hàng kênh thương mại điện tử',
	'sales_title_input' => 'Tên hiển thị',
	'sales_link_input' => 'Nhập link',
	'sales_price_input' => 'Giá niêm yết',
	'sales_price_sale_input' => 'Giá bán',
	'other_select' => 'Khác',
	'add_sale_channel' => 'Thêm kênh TMĐT',

    'concern_title' => 'Quản lý doanh nghiệp',
    'province_select' => 'Tỉnh/thành',
    'concern_logo' => 'Logo',
    'concern_name' => 'Tên doanh nghiệp',
    'concern_mst' => 'Mã số thuế',
    'website' => 'Website',
    'add_concern_title' => 'Thêm mới doanh nghiệp',
    'concern_picture' => 'Ảnh đại diện của bạn',
    'concern_country' => 'Quốc gia',
    'save' => 'Lưu'
);
?>