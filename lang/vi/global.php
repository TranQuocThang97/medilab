<?php
if ( !defined('IN_ims') )	{ die('Access denied');	} 
	$lang = array(
//		'wcoin_not_enough' => 'Số điểm không đủ',
//		'verification' => 'Mã bảo mật',
//		'update_success' => 'Cập nhật thành công',
//		'update_false' => 'Cập nhật thất bại',
//		'thousand' => 'Ngàn',
//		'text_search' => 'Tìm kiếm',
//		'text_register_mail' => 'Vui lòng nhập email để nhận thông báo từ chúng tôi',
//		'success_use_wcoin' => 'Sử dụng điểm thành công',
//		'success_comment' => 'Đăng nhận xét thành công, đợi phê duyệt',
//		'spam_comment' => 'Bạn nhận xét quá nhanh ! vui lòng thử lại sau.',
//		'signin_false' => 'Vui lòng đăng nhập',
//		'send' => 'Gửi',
//		'select_title' => 'Chọn ---',
//		'save_later_false1' => 'Bạn phải đăng nhập để lưu sản phẩm mua sau',
//		'seemorecomments' => 'Xem thêm bình luận',
//		'seemore' => 'Xem thêm',
//		'seeless' => 'Rút gọn',
//		'second' => 'giây',
//		'btn_search' => 'Tìm kiếm',
//		'save_later_success' => 'Lưu sản phẩm thành công',
//		'save_later_false2' => 'Bãn đã lưu sản phẩm này rồi',
//		'reply' => 'Trả lời',
//		'save_later_false0' => 'Lưu sản phẩm thất bại. Có lỗi xảy ra',
//		'register_now' => 'Gửi Email',
//		'remaining' => 'Còn lại',
//		'register_mail' => 'Đăng ký',
//		'register_note' => '(*) là thông tin bắt buộc',
//		'btn_register_mail' => 'Gửi',
//		'rate_success' => 'Đánh giá thành công',
//		'rate_false' => 'Đánh giá không thành công',
//		'quantity' => 'Số lượng',
//		'rate_exist' => 'Bạn đã đánh giá sản phẩm này',
//		'rate' => 'Đánh giá',
//		'price_empty' => 'Liên hệ',
//		'product' => 'Sản phẩm',
//		'other_question' => 'Câu hỏi khác',
//		'over' => 'Trên',
//		'notification' => 'Thông báo của tôi',
//		'note_use_wcoin_plus' => 'Điểm được cộng khi mua hàng, với mã đơn hàng:',
//		'not_available' => 'Sản phẩm đã hết hàng',
//		'not_signin_favorite' => 'Vui lòng đăng nhập để thêm sản phẩm vào danh sách yêu thích',
//		'note_use_wcoin' => 'Bị trừ điểm khi sử dụng mua hàng, mã đơn hàng :',
//		'month_10' => 'Tháng 10',
//		'month_11' => 'Tháng 11',
//		'month_12' => 'Tháng 12',
//		'need_login' => 'Vui lòng đăng nhập để nhận xét',
//		'month_09' => 'Tháng 09',
//		'month_08' => 'Tháng 08',
//		'month_07' => 'Tháng 07',
//		'month_06' => 'Tháng 06',
//		'month_05' => 'Tháng 05',
//		'month_04' => 'Tháng 04',
//		'month_03' => 'Tháng 03',
//		'month_01' => 'Tháng 01',
//		'month_02' => 'Tháng 02',
//		'min_price_slide' => '0',
//		'minute' => 'phút',
//		'million' => 'Triệu',
//		'max_price_slide' => '10000000',
//		'max_num_file' => 'Hình bình luận tối đa là',
//		'login_now' => 'Đăng nhập ngay',
//		'loading_' => 'Vui lòng đợi...',
//		'list_comments' => 'Danh sách nhận xét',
//		'list_answer' => 'Các câu trả lời',
//		'hour' => 'giờ',
//		'favorite_success_remove' => 'Bỏ yêu thích thành công!',
//		'favorite_success' => 'Yêu thích sản phẩm thành công!',
//		'err_wcoin_expires' => 'Số điểm đã hết hạn sử dụng',
//		'error_max_favorite' => 'Chỉ cho phép tối đa [num] sản phẩm yêu thích !',
//		'error_comment' => 'Đăng nhận xét thất bại !',
//		'err_valid_input' => 'Không được để trống',
//		'err_in_stock_size' => 'Số lượng sản phẩm không đủ!',
//		'err_in_stock' => 'Số lượng sản phẩm không đủ!',
//		'err_exists_email' => 'Email đã tồn tại trong hệ thống',
//		'err_empty' => '[name] không được để trống',
//		'err_invalid' => '[name] không hợp lệ',
//		'err_email_input' => 'Nhập địa chỉ email hợp lê',
//		'enter_email' => 'Email của bạn',
//		'enter_name' => 'Họ và tên...',
//		'enter_phone' => 'Số điện thoại...',
//		'delete_success' => 'Xóa thành công',
//		'emaillist_false' => 'Nhận thông báo thất bại!',
//		'emaillist_success' => 'Nhận thông báo thành công!',
//		'empty_anwser' => 'Chưa có trả lời',
//		'empty_basket' => 'Chưa có sản phẩm',
//		'delete' => 'Xóa',
//		'delete_false' => 'Xóa thất bại. Có lỗi xảy ra',
//		'decrease' => 'Giảm',
//		'create_new_order' => 'Đã nhận đơn hàng mới {order_id} từ {order_name}',
		'copyright' => '© Copyright 2022 Nodex ASIA',
//		'comment_your' => 'Bình luận của bạn',
//		'copped' => 'Đã copy !',
//		'copped_code' => 'Đã copy ! Sử dụng mã này để được giảm giá',
//		'copped_link' => 'Đã copy ! Chia sẻ link này cho bạn bè để được hưởng hoa hồng!',
//		'comment_rate' => 'Đánh giá & nhận xét',
//		'comment' => 'Nhận xét của khách hàng',
//		'choose_price' => 'Giá',
//		'check' => 'Kiểm tra',
//		'check_order' => 'Lịch sử mua hàng',
//		'change' => 'Sửa',
//		'buy_later' => 'Mua sau',
//		'buy_now' => 'Mua ngay',
//		'billion' => 'Tỷ',
//		'below' => 'Dưới',
//		'basket' => 'Giỏ hàng',
//		'among' => 'trong số',
//		'account_info' => 'Tài khoản của tôi',
		'aleft_title' => 'Thông báo',
//		'acctive_user' => 'Vui lòng kiểm tra email để kích hoạt tài khoản!',
//		'account' => 'Tài khoản',
//		'payment' => 'Phương thức thanh toán',
//		'register_mail_title' => 'Đăng ký nhận tin',
//		'small_register_mail_title' => 'Vui lòng nhập email để nhận tin từ chúng tôi',

		'top_header' => '(Thông tin đặc biệt) Neque porro quisquam est qui dolore',
		'file' => 'Hồ sơ Nodex',
		'contact' => 'Liên hệ',
		'event_footer' => '<p>Nodex có thể giúp</p> <p>dịch vụ nhân sự của bạn</p> <p>bằng cách nào</p>',
		'upcoming_events' => 'Các sự kiện sắp tới',
        'first_name' => 'Tên',
        'last_name' => 'Họ',
        'email' => 'Email',
        'phone' => 'Số điện thoại',
        'position' => 'Chức vụ',
        'company_name' => 'Tên công ty',
        'country' => 'Quốc gia',
        'select_country' => 'Chọn quốc gia',
        'im_find' => 'Tôi đang tìm kiếm',
        'select_service' => 'Chọn dịch vụ',
        'send' => 'Gửi'
	);
?>
