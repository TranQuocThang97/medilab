<?php
if (!defined('IN_ims')) { die('Access denied'); }

function load_setting ()
{
	global $ims;
	
	$ims->site_func->setting('user');
	$ims->site_func->setting('product');
	$ims->site_func->setting('promotion');
	return true;
}
load_setting ();
$nts = new sMain();

use \Firebase\JWT\JWT;

class sMain
{
	var $modules = "user";
	var $action  = "ajax";
	
	/**
		* Khởi tạo
		* Ajax user
	**/
	function __construct ()
	{
		global $ims;
		$ims->func->load_language($this->modules);
		$ims->conf['lang_cur'] = (isset($ims->post['lang_cur'])) ? $ims->post['lang_cur'] : 'vi';
		$fun = (isset($ims->post['f'])) ? $ims->post['f'] : '';

		switch ($fun) {
			// Đăng ký tài khoản
			case "signup":
				echo $this->do_signup ();
				exit;
				break;
			// Đăng nhập tài khoản
			case "signin":
				echo $this->do_signin ();
				exit;
				break;
			// Đăng xuất tài khoản
			case "signout":
				echo $this->do_signout ();
				exit;
				break;
			// Cập nhật tài khoản
			case "account":
				echo $this->do_account ();
				exit;
				break;
			// Cập nhật tài khoản
			case "remove_avatar":
				echo $this->do_remove_avatar ();
				exit;
				break;				
			// Thay đổi mật khẩu
			case "change_pass":
				echo $this->do_change_pass ();
				exit;
				break;
			// Thay đổi mật khẩu qua OTP
			case "change_pass_otp":
				echo $this->do_change_pass_otp ();
				exit;
				break;
			// Quên mật khẩu
			case "forget_pass":
				echo $this->do_forget_pass ();
				exit;
				break;
			// Lấy danh sách thông báo
			case "get_notification":
				echo $this->do_get_notification ();
				exit;
				break;
			// Lấy danh sách đơn hàng
			case "get_order":
				echo $this->do_get_order ();
				exit;
				break;
			case "check_favorite":
				echo $this->do_check_favorite ();
				exit;
				break;
			case "post_comment":
				echo $this->do_post_comment ();
				exit;
				break;
			case "load_comment":
				echo $this->do_load_comment ();
				exit;
				break;
			case "post_advisory":
				echo $this->do_post_advisory ();
				exit;
				break;
			// Sử dụng điểm tích lũy khi đặt hàng			
			case "useWcoin":
				echo $this->do_useWcoin ();
				exit;
				break;
			case "wcoin_payment":
				echo $this->do_wcoin_payment ();
				exit;
				break;
			case "save_link":
				echo $this->do_save_link ();
				exit;
				break;
			case "send_inv":
				echo $this->do_send_inv ();
				exit;
				break;
			case "add_deeplink":
                echo $this->do_add_deeplink ();
                exit;
                break;
            case "delete_deeplink":
                echo $this->do_delete_deeplink ();
                exit;
                break;			
			case "load_form_address":
				echo $this->do_load_form_address();
				exit;
				break;
			case "default_address":
				echo $this->do_default_address();
				exit;
				break;
			case "delete_address":
				echo $this->do_delete_address();
				exit;
				break;
			case "cartSaveLater":
				echo $this->do_cartSaveLater ();
				exit;
				break;
			case "removeSaveLater":
				echo $this->do_removeSaveLater();
				exit;
				break;
			case "update_api_status":
				echo $this->do_update_api_status();
				exit;
				break;
			case "save_config_sendo":
				echo $this->do_save_config_sendo();
				exit;
				break;

			// Thêm sổ địa chỉ
			case "addAddressBook":
				echo $this->do_addAddressBook();
				exit;
				break;
			// Sửa sổ địa chỉ
			case "popupAddessBook":
				echo $this->do_popupAddessBook();
				exit;
				break;
			// Yêu cầu rút điểm
			case "withdrawWcoin":
				echo $this->do_withdrawWcoin();
				exit;
				break;
            // Đổi hoa hồng sang điểm
			case "swap_commission":
				echo $this->do_swap_commission();
				exit;
				break;
			case "cancel_order":
				echo $this->cancel_order();
				exit;
				break;
            case "request_otp":
                echo $this->do_request_otp();
                exit;
                break;
            case "verify_otp":
                echo $this->do_verify_otp();
                exit;
                break;


            //EVENT
            case "create_team":
                echo $this->do_create_team();
                exit;
                break;
            case "edit_team":
                echo $this->do_edit_team();
                exit;
                break;
            case "update_team":
                echo $this->do_update_team();
                exit;
                break;

            // Store
            case "form_store":
                echo $this->do_form_store();
                exit;
                break;
            case "add_edit_store":
                echo $this->do_add_edit_store();
                exit;
                break;
            case "delete_store":
                echo $this->do_delete_store();
                exit;
                break;
            case "restore_store":
                echo $this->do_restore_store();
                exit;
                break;
            case "load_event_to_store":
                echo $this->do_load_event_to_store();
                exit;
                break;
            case "add_store_to_event":
                echo $this->do_add_store_to_event();
                exit;
                break;
            case "load_product_to_store":
                echo $this->do_load_product_to_store();
                exit;
                break;
            case "add_product_to_store":
                echo $this->do_add_product_to_store();
                exit;
                break;

            // Product
            case "form_product":
                echo $this->do_form_product();
                exit;
                break;
            case "add_edit_product":
                echo $this->do_add_edit_product();
                exit;
                break;
            case "delete_product":
                echo $this->do_delete_product();
                exit;
                break;
            case "restore_product":
                echo $this->do_restore_product();
                exit;
                break;
            case "load_event_user":
                echo $this->do_load_event_user();
                exit;
                break;
            case "load_info_user":
                echo $this->do_load_info_user();
                exit;
                break;
            case "load_search_product":
                echo $this->do_load_search_product();
                exit;
                break;
            case "load_cart":
                echo $this->do_load_cart();
                exit;
                break;
            case "create_order":
                echo $this->do_create_order();
                exit;
                break;
            case "load_event":
                echo $this->do_load_event();
                exit;
                break;
            case "add_product_to_event":
                echo $this->do_add_product_to_event();
                exit;
                break;
            //EXCEL
                // Import ticket
            case "import_excel_ticket":
                echo $this->do_import_excel_ticket ();
                exit;
                break;
                // export ticket
            case "export_excel_ticket":
                echo $this->do_export_excel_ticket();
                exit;
                break;
			default:
				echo '';
				exit;
				break;

		}
		flush();
		exit;
	}

	function do_import_excel_ticket(){
        global $ims;

        $output = array(
            'ok' => 0,
            'mess' => ''
        );
        $err = array();
        $input = $ims->func->if_isset($ims->post, array()); 
        $info = $ims->db->load_row('event','is_show=1 and lang="'.$ims->conf['lang_cur'].'" and item_id="'.$input['event_id'].'"');
        if(!$info){
        	$output['mess'] = $ims->lang['user']['event_notvalid_mes'];
        	return json_encode($output);
        }
        $is_file = 0;
        if(isset($_FILES) && !empty($_FILES['file_attach']['name'])){
            $maxsize    = 10485760;
            $acceptable = array(
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            );
            $allowedExts = array("xlsx", "xls");
            $temp = explode(".", $_FILES["file_attach"]["name"]);
            $extension = end($temp);
            if (!in_array($extension, $allowedExts)) {
                $output['mess'] = "Không đúng định dạng!";
        		return json_encode($output);
            }
            elseif((!in_array($_FILES['file_attach']['type'], $acceptable)) && (!empty($_FILES["file_attach"]["type"]))) {
                $output['mess'] = "Không đúng định dạng!";
        		return json_encode($output);
            }
            elseif(($_FILES['file_attach']['size'] >= $maxsize) || ($_FILES["file_attach"]["size"] == 0)) {               
                $output['mess'] = "Vượt quá dung lượng cho phép, tối đa 10MB !";
        		return json_encode($output);
            }
            elseif ($_FILES["file_attach"]["error"] > 0) {
                $output['mess'] = "Lỗi mở tập tin";
        		return json_encode($output);
            }
            else {
                // rmkdir
                $ims->func->rmkdir("import");
                // Save file
                $_FILES["file_attach"]["name"] = str_replace(' ', '_', $_FILES["file_attach"]["name"]);
                move_uploaded_file($_FILES["file_attach"]["tmp_name"], $ims->conf['rootpath_web'].'uploads/import/'.time().'_'.$_FILES["file_attach"]["name"]);
                $file_import = $ims->conf['rooturl_web'].'uploads/import/'.time().'_'.$_FILES["file_attach"]["name"];
            }
        }

        $check_ins = 0;
        $item_id = 0;
        if(isset($file_import) && $file_import != '') {
            error_reporting(E_ALL);
            ini_set('display_errors', TRUE);
            ini_set('display_startup_errors', TRUE);
            date_default_timezone_set('Asia/Ho_Chi_Minh');
            require_once ($ims->conf["rootpath"] .DS. "library" .DS. "PHPExcel" .DS. "PHPExcel" .DS. "IOFactory.php");
            $inputFileName = str_replace($ims->conf["rooturl_web"],$ims->conf["rootpath_web"], $file_import);
            try {
                $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($inputFileName);
            } catch (Exception $e) {
                die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) 
                . '": ' . $e->getMessage());
            }
            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow();
            if ($highestRow > 300) {
                $highestRow = 300;
            }
            $highestColumn = 'Z';

            $arr_price = $ims->func->unserialize($info['arr_price']);
			$arr_ticket = array();
			$arr_type_ticket = array();
			if($arr_price){
				foreach ($arr_price as $key => $value) {
					$arr_ticket[$value['title']] = $value;
					$arr_type_ticket[] = $value['title'];
				}
			}
            for($row = 2; $row <= $highestRow; $row++) {
                $rowData = $sheet->rangeToArray('A'.$row.':'.$highestColumn.$row , NULL, TRUE, FALSE);
                if($rowData[0] && $check_ins <= 300){
                    if (isset($rowData[0][1]) && $rowData[0][1]!='') {
                        $col_ins                    = array();
                        $col_ins['detail_id']       = $ims->db->getAutoIncrement('event_order_detail');
                        $col_ins['order_id']        = 0;
                        $col_ins['ticket_code']     = 'import';
                        $col_ins['event_id']	    = $input['event_id'];
                        $col_ins['full_name']	    = $rowData[0][1];
                        $col_ins['age']	    		= $rowData[0][2];
                        $col_ins['email']	    	= $rowData[0][3];
                        $col_ins['phone']	    	= $rowData[0][4];
                        $col_ins['ticket_info']	    = '';
                        $col_ins['ticket']	   		= '';
                        $col_ins['team']	   		= '';
                        $col_ins['is_checkin']	   	= !empty($rowData[0][7])?1:0;
                        $col_ins['date_checkin']	= !empty($rowData[0][7])?$ims->func->time_str2int($rowData[0][7]):0;
                        $col_ins['is_send']			= 0;
                        $col_ins['title']			= $rowData[0][5];
                        $col_ins['price_buy']		= $rowData[0][6];
                        $col_ins['quantity']		= 1;
                        $col_ins['date_create']		= !empty($rowData[0][8])?$ims->func->time_str2int($rowData[0][8]):time();
                        $col_ins['date_update']		= !empty($rowData[0][8])?$ims->func->time_str2int($rowData[0][8]):time();
                        if(in_array($col_ins['title'], $arr_type_ticket)){
                            if($check_ins > $arr_ticket[$col_ins['title']]['num_ticket_remain']){
                            	$err[] = 'STT '.$rowData[0][0].' - '.$rowData[0][5].' không đủ số lượng';
                            	break;
                            }else{
		                        $ok = $ims->db->do_insert("event_order_detail", $col_ins);
		                        // $ok = 1;
		                        if($ok){	                            
		                            if($arr_ticket[$col_ins['title']]['num_ticket_remain'] > 0){
		                        		$arr_ticket[$col_ins['title']]['num_ticket_remain'] -= 1;
		                        		foreach ($arr_price as $k => $v) {
		                        			if($v['title'] == $col_ins['title']){
		                        				$arr_price[$k]['num_ticket_remain'] -= 1;
		                        			}
		                        		}
		                        		$arr_up = $ims->func->serialize($arr_price);
		                        		$output['arr_price'] = $arr_price;
	        							$ims->db->do_update("event", array('arr_price' => $arr_up), " item_id = '".$input['event_id']."'");
		                        	}
		                            $check_ins++;
		                        }
							}		                        
                        }else{
                        	$err[] = 'Loại vé STT '.$rowData[0][0].' - '.$rowData[0][5].' không hợp lệ';
                        }
                    }
                }
                // End excel
            }
        }
        if($check_ins > 0){
            $output['ok'] = 1;
            $output['mess'] = $check_ins.$ims->lang["user"]["thread_import_success"];    
        }else{
            $output['mess'] = $ims->lang["user"]["import_false"];               
        }
        if(count($err)>0){
            $ims->site_func->setting('event');
            $err[] = "Ấn <a href='".$ims->site_func->get_link('event', $ims->setting['event']['create_link'])."?step=3&edit=".$input['event_id']."'>vào đây</a> để chỉnh sửa";
        }
        $output['mess'] .= '<br>'. implode('<br>', $err);
        return json_encode($output);       
    }

    function do_export_excel_ticket() {
        global $ims;
        /** Error reporting */
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);
        date_default_timezone_set('Europe/London');

        if (PHP_SAPI == 'cli') {
            die('This example should only be run from a Web Browser');
        }

        if($ims->site_func->checkuserlogin() != 1) {
			$output['mess'] = $ims->lang['global']['signin_false'];
			return json_encode($output);
		}

        /** Include functuion */
        require_once ("user_func.php");
        //load_setting_ordering ();

        /** Include PHPExcel */
        require_once ($ims->conf["rootpath"] . DS . "library" . DS . "PHPExcel" . DS . "PHPExcel.php");

        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        //print_arr($ims->data); die;
        // Set document properties
         // $objPHPExcel->getProperties()->setCreator($ims->data['admin']['full_name'])
         //  ->setLastModifiedBy($ims->data['admin']['full_name'])
         //  ->setTitle('Danh sách khách ('.date('d/m/Y').')')
         //  ->setSubject('Danh sách khách ('.date('d/m/Y').')')
         //  ->setDescription('Danh sách khách ('.date('d/m/Y').')')
         //  ->setKeywords('Danh sách khách ('.date('d/m/Y').')')
         //  ->setCategory('Danh sách khách ('.date('d/m/Y').')'); 
        $objPHPExcel->getProperties()->setCreator($ims->data['user_cur']['full_name'])
                ->setLastModifiedBy($ims->data['user_cur']['full_name'])
                ->setTitle("Office 2007 XLSX Test Document")
                ->setSubject("Office 2007 XLSX Test Document")
                ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                ->setKeywords("office 2007 openxml php")
                ->setCategory("Test result file");
        $style = array(
            'alignment' => array(
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
        $objPHPExcel->getDefaultStyle()->applyFromArray($style);

        // Add some data
        $rowname = 5;
        $colname = 'A';
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colname.$rowname, 'STT'); $colname++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colname.$rowname, 'Ngày đăng ký'); $colname++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colname.$rowname, 'Họ và tên'); $colname++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colname.$rowname, 'Địa chỉ email'); $colname++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colname.$rowname, 'Điện thoại'); $colname++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colname.$rowname, 'Loại vé'); $colname++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colname.$rowname, 'Team'); $colname++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colname.$rowname, 'Gửi lại vé'); $colname++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colname.$rowname, 'Ngày check-in'); $colname++;
        
        $title = $ims->lang['user']['list_registed'];
		if (isset($ims->input['title'])) {
            $tmp = $ims->func->encrypt_decrypt('decrypt', $ims->input['title'], 'excel', 'export');
            $title = $ims->lang['user']['list_'.$tmp];
        }

        $where = '';
        if (isset($ims->input['wre'])) {
            $where .= $ims->func->encrypt_decrypt('decrypt', $ims->input['wre'], 'excel', 'export');
        }

        // $sql = "select * from evenet_order_detail " . $where;    
        $event = array();    
        $arr = $ims->db->load_row_arr('event_order_detail', $where);
        $i = 5; $j = 0;
        if ($arr) {
            foreach ($arr as $row) {
                $i++; $j++;
                $rowname++;
                $event = $ims->db->load_row('event','item_id="'.$row['event_id'].'"');
//                $num_thread = $ims->db->do_get_num('thread', " is_show=1 and lang='".$ims->conf['lang_cur']."' and user_id='".$row['user_id']."'");
//                $num_comment = $ims->db->do_get_num('thread_comment', " is_show=1 and user_id='".$row['user_id']."'");               
                
                foreach (array('date_create', 'date_update', 'date_checkin') as $k) {
                    $row[$k] = (isset($row[$k]) && $row[$k]) ? date('H:i:s, d/m/Y', $row[$k]) : '';
                }                
                             
                $colname = 'A';
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colname.$rowname, $j); $colname++;
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colname.$rowname, $row['date_create']); $colname++;
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colname.$rowname, $row['full_name']); $colname++;
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colname.$rowname, $row['email']); $colname++;
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colname.$rowname, $row['phone']); $colname++;
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colname.$rowname, $row['title']); $colname++;
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colname.$rowname, $row['team']); $colname++;
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colname.$rowname, !empty($row['is_send'])?'Đã gửi':''); $colname++;
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colname.$rowname, $row['date_checkin']); $colname++;

                $objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(80);                
            }
        }
        $title_style = array(
            'font' => array(
                'bold' => true,
                'size' => 15,
                'color' => array(
                    'rgb' => 'FE6505',
                ),
            ),
        );
        $info = "Sự kiện: ".$event['title']."\r".$event['address']."\r".date('d/m/Y H:i', $event['date_begin']);
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:'.$colname.'1')->setCellValue('A1', $title)->getStyle('A1')->applyFromArray($title_style);   
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A3:'.$colname.'3')->setCellValue('A3', $info)->getRowDimension('3')->setRowHeight(100);   


		$thead_style = array(
            'font' => array(
                'bold' => true,
                'size' => 13,
                'color' => array(
                    'rgb' => 'FFFFFF',
                ),
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array(
                    'rgb' => 'FE6505',
                )
            ),
        );
        $tc='A';
        while ($tc <= $colname) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($tc)->setAutoSize(true);     
            $objPHPExcel->getActiveSheet()->getStyle($tc."5:".$colname."5")->applyFromArray($thead_style);
            $tc++;
        } 
        // Rename worksheet
        //$objPHPExcel->getActiveSheet()->setTitle(date('d/m/Y'));
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setWrapText(true);


        // Redirect output to a client’s web browser (Excel5)
        // header('Content-Type: application/vnd.ms-excel');
        // header('Content-Disposition: attachment;filename="'.$title.'-' . date('d/m/Y') . '.xls"');
        // header('Cache-Control: max-age=0');
        // // If you're serving to IE 9, then the following may be needed
        // header('Cache-Control: max-age=1');

        // // If you're serving to IE over SSL, then the following may be needed
        // header('Expires: ' . date('r') . ' GMT'); // Date in the past
        // header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        // header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        // header('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        ob_start();
        $objWriter->save('php://output');
        $xlsData = ob_get_contents();
		ob_end_clean();
		$response =  array(
	        'ok' => '1',
	        'filename' => $title.'-' . date('d/m/Y'),
	        'file' => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData)
	    );
        return json_encode($response);
    }

	function do_update_team(){
		global $ims;
		$output = array(
            'ok' => 0,
            'mess' => ''
        );
        $input_tmp = $ims->func->if_isset($ims->post['data'], array());
		foreach($input_tmp as $key) {
			$input[$key['name']] = $key['value'];
		}
		$input['ticket'] = json_decode($input['ticket'],true);		
		if(!empty($input['event_id'])){
			$count_request = count($input['ticket']);
			$count_dup = 0;
			$count_team_exist = 0;			
			$arr_team_exist = $ims->db->load_item_arr('event_order_detail',' product_id="'.$input['event_id'].'" and team="'.$input['team'].'"','detail_id');
			if($arr_team_exist){
				$count_team_exist = count($arr_team_exist);
				foreach ($arr_team_exist as $row) {
					if(in_array($row['detail_id'], $input['ticket'])){
						$count_dup++;
					}
				}
			}
			$count_new = $count_request - $count_dup;
			
			$event = $ims->db->load_row('product','is_show=1 and lang="'.$ims->conf['lang_cur'].'" and item_id="'.$input['event_id'].'"',"item_id, arr_teams");
			$arr_teams = $ims->func->unserialize($event['arr_teams']);
			$team = $arr_teams[$input['team']];

			if(($count_new + $count_team_exist) > $team['quantity']){
				$output['mess'] = $ims->site_func->get_lang('exceed_members', 'user', array(
										'[max]' => '<b>'.$team['quantity'].'</b>',
										'[available]' => '<b>'.$count_team_exist.'</b>',
										'[add]'	=> '<b>'.$count_new.'</b>',
									));
			}
			if(empty($output['mess'])){
				// foreach ($input['ticket'] as $detail_id) {
				// }
				$arr_up = array();
				$arr_up['team'] = $input['team'];
				$ok = $ims->db->do_update('event_order_detail', $arr_up, ' find_in_set(detail_id,"'.implode(',',$input['ticket']).'") ');
				if($ok){
					$output['ok'] = 1;
				}
			}
		}
		return json_encode($output);
	}

	function do_edit_team(){
		global $ims;
		$output = array(
            'ok' => 0,
            'mess' => ''
        );
        $input_tmp = $ims->func->if_isset($ims->post['data'], array());		
		foreach($input_tmp as $key) {
			$input[$key['name']] = $key['value'];
		}		
		if(!empty($input['event_id'])){
			$event = $ims->db->load_row('product','is_show=1 and lang="'.$ims->conf['lang_cur'].'" and item_id="'.$input['event_id'].'"',"item_id, arr_teams");
			$arr_teams = $ims->func->unserialize($event['arr_teams']);
			foreach ($arr_teams as $key => $value) {
				if($key == $input['key']){
					$arr_teams[$input['key']] = array(
						'title' => $input['title'],
						'quantity' => $input['quantity'],
					);
				}
			}
			$arr_up = array();
			switch ($input['submit']) {
				case 'edit':
					
					break;
				case 'remove':
					unset($arr_teams[$input['key']]);
					break;
				default:
					break;
			}
			$arr_up['arr_teams'] = $ims->func->serialize($arr_teams);
			$ok = $ims->db->do_update('product', $arr_up, ' item_id="'.$event['item_id'].'" ');
			if($ok){
				$output['ok'] = 1;
			}
		}
		return json_encode($output);
	}

	function do_create_team(){
		global $ims;
		$output = array(
            'ok' => 0,
            'mess' => ''
        );
        $input = array();
		$input_tmp = $ims->func->if_isset($ims->post['data'], array());		
		foreach($input_tmp as $key) {
			$input[$key['name']] = $key['value'];
		}
		if(!empty($input['event_id'])){
			$event = $ims->db->load_row('product','is_show=1 and lang="'.$ims->conf['lang_cur'].'" and item_id="'.$input['event_id'].'"',"item_id, arr_teams");
			if($event){
				$team = array(
					$ims->func->random_str(6,'ln') => array(
						'title' => $input['title'],
						'quantity' => $input['quantity'],
					)
				);
				$arr_teams = $ims->func->unserialize($event['arr_teams']);				
				$arr_up = array();
				$arr_up['arr_teams'] = array_merge($arr_teams, $team);
				$arr_up['arr_teams'] = $ims->func->serialize($arr_up['arr_teams']);
				// print_arr($arr_up);die;
				$ok = $ims->db->do_update('product', $arr_up, ' item_id="'.$event['item_id'].'" ');
				if($ok){
					$output['ok'] = 1;
				}
			}
		}
		return json_encode($output);
	}

    function do_verify_otp(){
        global $ims;
        $output = array(
            'ok' => 0,
            'mess' => ''
        );
        $input = $ims->func->if_isset($ims->post['data'], array());
        $arr_tmp = array('otp','phone','user_id');
        $arr_in = array();
        foreach($arr_tmp as $key) {
            if(empty($output['mess']) && (!isset($input[$key]) || empty($input[$key]))) {
                $output['mess'] = $ims->lang['user']['verify_otp_false'];
                break;
            }
            $arr_in[$key] = trim($input[$key]);
        }

        $check = $ims->db->load_row('user','is_show=0 and user_id="'.(int)$arr_in['user_id'].'"','date_expire,otp,username,password');
        if($check){
            if(time() > $check['date_expire']){
                $output['mess'] = $ims->lang['user']['verify_otp_false2'];
            }
            if($check['otp'] != $arr_in['otp']){
                $output['mess'] = $ims->lang['user']['verify_otp_false3'];
            }
            if(empty($output['mess'])){
                $col = array();
                $col['phone'] = $arr_in['phone'];
                $col['date_expire'] = 0;
                $col['otp'] = 0;
                $col['is_show'] = 1;
                $ok = $ims->db->do_update('user', $col, " user_id='".$arr_in['user_id']."' ");
                if($ok){
                    $output['ok'] = 1;
                    $output['mess'] = $ims->lang['user']['verify_otp_success'];
                    Session::Set('user_cur', array(
                        'userid' => (int)$arr_in['user_id'],
                        'username' => $check['username'],
                        'password' => $check['password'],
                        'session' => ''
                    ));
                }else{
                    $output['mess'] = $ims->lang['user']['verify_otp_false4'];
                }
            }
        }else{
            $output['mess'] = $ims->lang['user']['verify_otp_false4'];
        }
        return json_encode($output);
    }

    function do_request_otp(){
        global $ims;
        $output = array(
            'ok' => 0,
            'mess' => '',
            'go_link' => '',
        );
        $input = $ims->func->if_isset($ims->post['data'], array());
        $arr_tmp = array('phone','user_id');
        $arr_in = array();
        foreach($arr_tmp as $key) {
            if(empty($output['mess']) && (!isset($input[$key]) || empty($input[$key]))) {
                $output['mess'] = $ims->site_func->get_lang ('err_invalid', $this->modules, array(
                    '[name]' => $ims->site_func->get_lang ($key, $this->modules)
                ));
                break;
            }
            $arr_in[$key] = trim($input[$key]);
        }        
        $check = $ims->db->load_row('user','user_id='.$arr_in['user_id'].'','date_expire,otp');
        if($check){
            if(time() < $check['date_expire'] && !empty($check['otp'])){
                $output['ok'] = 1;
                $output['mess'] = $ims->lang['user']['request_otp_success2'];
            }
        }
        if(empty($output['mess'])){
            // Gửi sms mã otp
            $otp = rand(1000, 9999);
            $data_sms = array(
                'ApiKey'    => $ims->setting['user']['esms_ApiKey'],
                'SecretKey' => $ims->setting['user']['esms_SecretKey'],
                'Brandname' => $ims->setting['user']['esms_Brandname'],
                'Phone'     => $input['phone'],
                'Content'   => str_replace('{otp}', $otp, $ims->setting['user']['esms_Content']),
                'SmsType'   => 2,
                'Sandbox'   => 0,
                // 'RequestId' => $col['order_code'],
            );
            $data_sms = http_build_query ($data_sms);
            $SMS =  $ims->site_func->sendPostData('http://rest.esms.vn/MainService.svc/json/SendMultipleMessage_V4_post/', $data_sms, 'post', 1, '', array());
            if (!empty($SMS)) {
                $SMS = json_decode($SMS);
                if (isset($SMS->CodeResult) && $SMS->CodeResult==100) {
                    $sms = array();
                    $sms['smsid'] = $SMS->SMSID;
                    $output['sms'] = $sms;
                    $col = array();
                    $col['date_expire'] = time()+60*5;
                    $col['otp'] = $otp;
                    if(empty($input['request'])){
                        $col['phone'] = $arr_in['phone'];
                    }
                    $ok = $ims->db->do_update('user', $col, " user_id='".$arr_in['user_id']."' ");
                    if($ok){
                        $output['ok'] = 1;
                        $output['sms'] = $sms;
                        $output['mess'] = $ims->lang['user']['request_otp_success'];
                    }else{
                        $output['mess'] = $ims->lang['user']['request_otp_false'];
                    }
                }
            }else{
                $output['ok'] = 0;
                $output['mess'] = $ims->lang['user']['request_otp_false'];
            }
        }
        return json_encode($output);
    }

	function cancel_order(){
		global $ims;
		$output = array(
			'ok' => 0,
			'mess' => ''
		);
		$ims->func->load_language('user');
		$order_code = isset($ims->post['data'])?$ims->post['data']:'';		
		$input = $ims->func->unserialize_array ($ims->func->if_isset($ims->post['data'], array()));		
		if($ims->site_func->checkuserlogin() != 1) {
			$output['mess'] = $ims->lang['global']['signin_false'];
			return json_encode($output);
		}
		$new = $ims->db->load_row('product_order_status','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and is_first = 1');
		$cancel = $ims->db->load_row('product_order_status','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and is_cancel = 1');
		$order = $ims->db->load_row('product_order', 'order_code="'.$input['order_code'].'" and user_id="'.$ims->data['user_cur']['user_id'].'"','order_id, order_code, is_status, is_status_payment, payment_wcoin, user_id, promotion_id');
//        print_arr($order);die;
		if($order){
			if($order['is_status'] == $new['item_id'] && $order['is_status_payment'] != 3){				
				$arr_up = array();
				$arr_up['is_status'] = $cancel['item_id'];
				$arr_up['is_cancel'] = 1;
				$arr_up['cancel_reason'] = $ims->func->if_isset($input['content']);
				$arr_up['date_cancel'] = time();
				$ok = $ims->db->do_update("product_order",$arr_up," order_id='".$order['order_id']."' ");				
				if($ok){
					$SQL_UPDATE = 'UPDATE user SET wcoin = wcoin + '.$order['payment_wcoin'].' WHERE is_show = 1 AND user_id = "'.$order['user_id'].'"';
					$ims->db->query($SQL_UPDATE);

					if(!empty($order['promotion_id'])){
						$check = $ims->db->load_row('promotion','is_show=1 and promotion_id="'.$order['promotion_id'].'" ');
						if(!empty($check['num_use'])){
							$ims->db->query("UPDATE promotion SET num_use=num_use-1, date_update=".time()." WHERE promotion_id='".$order['promotion_id']."' ");
						}
					}
					//lưu log
					$arr_ins                    = array();
			        $arr_ins['is_show']         = 1;
			        $arr_ins['order_id']        = $order['order_id'];
			        $arr_ins['date_create']     = time();
			        $arr_ins['date_update']     = time();
			        $arr_ins['title'] = 'Khách hàng đã hủy đơn #'.$order['order_code'];
			        $arr_ins['content'] = $cancel['title'];
        			$ims->db->do_insert('product_order_log', $arr_ins);

					$output['ok'] = 1;
					$output['mess'] = $ims->lang['user']['cancelOrder_message_success'];
				}
			}else{
				switch ($order['is_status']) {
					case $cancel['item_id']:
						$output['mess'] = $ims->lang['user']['cancelOrder_message_false1'];
						break;					
					default:
						$output['mess'] = $ims->lang['user']['cancelOrder_message_false'];
						break;
				}
				if($order['is_status_payment'] == 3){
					$output['mess'] = $ims->lang['user']['cancelOrder_message_false2'];
				}
			}
		}
		return json_encode($output);
	}

	function do_popupAddessBook(){
		global $ims;

		$output = array(
			'ok' => 0,
			'mess' => '',
			'html' => '',
		);

		if($ims->site_func->checkUserLogin() != 1) {
			$output['mess'] = $ims->lang['global']['signin_false'];
			return json_encode($output);
		}

		$data = array();
		$id  = $ims->func->if_isset($ims->post['id']);
		$arr_address = $ims->func->unserialize($ims->data['user_cur']['arr_address_book']);
		foreach ($arr_address as $item) {
			if($id!='' && $id == $item['id']){
				$data = $item;
				if($item['is_default'] == 1){
					$data['checked'] = 'checked';
				}
			}
		}

		$cur_ward 	  = $data['ward'];
		$cur_district = $data['district'];
		$cur_province = $data['province'];
        $data['type'] = 'edit';
        $data['form_id'] = $data['id'];

        $data["list_location_province"] = $ims->site_func->selectLocation (
        	"province",
        	"vi",
        	$cur_province,
        	" class='form-control select_location_provinces' data-district='district' data-ward='ward' id='provinces' ",
        	array('title' => $ims->lang["user"]["select_title"]),
        	"province"
        );
		$data["list_location_district"] = $ims->site_func->selectLocation (
			"district", 
			$cur_province, 
			$cur_district,
			" class='form-control select_location_districts' data-ward='ward' id='districts' ",
        	array('title' => $ims->lang["user"]["select_title"]),
			"district"
		);
		$data["list_location_ward"] = $ims->site_func->selectLocation (
			"ward",
			$cur_district, 
			$cur_ward,
			" class='form-control' id='wards' ",
        	array('title' => $ims->lang["user"]["select_title"]),
			"ward"
		);

		$ims->func->load_language("product");

        $ims->temp_box->assign('LANG', $ims->lang);
        $ims->temp_box->assign('data', $data);
        $ims->temp_box->reset('form_address_book');
        $ims->temp_box->parse('form_address_book');
        $output['form_id'] = 'form_address_book'.$data['id'];
        $output['html'] = $ims->temp_box->text('form_address_book');
        $output['ok'] = 1;
		return json_encode($output);
	}

	function do_addAddressBook(){
		global $ims;

		$output = array(
			'ok' => 0,
			'default' => 0,
			'mess' => '',
		);
		$input = $ims->func->unserialize_array ($ims->func->if_isset($ims->post['data'], array()));

		if($ims->site_func->checkUserLogin() != 1) {
			$output['mess'] = $ims->lang['global']['signin_false'];
			return json_encode($output);
		} else{
			$arr_address = $ims->func->unserialize($ims->data['user_cur']['arr_address_book']);
			$arr_k  = array('full_name','email','phone','address','province','district','ward');

			// Edit
			if (isset($input['type']) && $input['type']=='edit' && isset($input['id']) && isset($arr_address[$input['id']])) {
				$arr_in['id'] = $input['id'];
				foreach($arr_k as $k) {				
					$arr_in[$k] = (isset($input[$k])) ? $input[$k] : '';
				}
				$arr_in['is_default'] = (isset($input['is_default'])) ? $input['is_default'] : '0';
				$arr_address[$arr_in['id']] = $arr_in;			
				if($arr_in['is_default'] != 0){
					$arr_temp = array();
					foreach ($arr_address as $row) {					
						if($row['id'] != $arr_in['id']){
							$row['is_default'] = 0;						
						}
						$arr_temp[$row['id']] = $row;
					}
					$arr_address = $arr_temp;
				}
				$arr_address = serialize($arr_address);
			}else{
				// Add
				$arr_insert = array();
				if(count($arr_address)>0){
					$arr_insert['id'] = $ims->data['user_cur']['user_id'].count($arr_address);
				}else{
					$arr_insert['id'] = $ims->data['user_cur']['user_id'].'0';
				}			
				foreach($arr_k as $k) {				
					$arr_insert[$k] = (isset($input[$k])) ? $input[$k] : '';
				}			
				$arr_insert['is_default'] = (isset($input['is_default'])) ? $input['is_default'] : '0';
				$arr_address[$arr_insert['id']] = $arr_insert;
				if($arr_insert['is_default'] != 0){
					$arr_temp = array();
					foreach ($arr_address as $row) {					
						if($row['id'] != $arr_insert['id']){
							$row['is_default'] = 0;						
						}
						$arr_temp[$row['id']] = $row;
					}
					$arr_address = $arr_temp;
					$output['default'] = $arr_insert['is_default'];
				}
				$arr_address = serialize($arr_address);
			}

			$ok = $ims->db->do_update('user', array('arr_address_book' => $arr_address), ' user_id="'.$ims->data['user_cur']['user_id'].'"');
			if($ok){
				$output['ok'] = 1;
			}
		}
        return json_encode($output);
	}

    function do_useWcoin(){
        global $ims;

        $output['ok'] = 0;
        $output['mess'] = '';
        $input = $ims->func->unserialize_array ($ims->func->if_isset($ims->post['data'], array()));

        $wcoin = $ims->func->if_isset($input['wcoin']);
        $temp_total_money = $ims->func->if_isset($input['cart_total']);

        if($ims->site_func->checkUserLogin() != 1) {
            $output['mess'] = $ims->lang['global']['signin_false'];
            return json_encode($output);
        }else{
            $wcoin_use  = (int) $wcoin;
            $max_wcoin_use  = $temp_total_money / $ims->setting['product']['wcoin_to_money'];
            $user_wcoin = $ims->data['user_cur']['wcoin'];

//			$user_wcoin_expires = $ims->data['user_cur']['wcoin_expires'];
//			if($user_wcoin_expires < time()){
//                $output['mess'] = $ims->lang['global']['err_wcoin_expires'];
//			}else
            if($wcoin_use < 0){
                $output['mess'] = $ims->lang['global']['error_wcoin'];
            }elseif($wcoin_use > $user_wcoin){
                $output['mess'] = $ims->lang['global']['wcoin_not_enough'];
            }

            if($output['mess'] == ''){
                if($wcoin_use > $max_wcoin_use){
                    $wcoin_use = $max_wcoin_use;
                }
                $price_wcoin = $wcoin_use * $ims->setting['product']['wcoin_to_money'];
                $output['price_wcoin'] = $ims->func->get_price_format($price_wcoin, 0);
                $output['ok'] = 1;
//                $output['wcoin_use'] = $wcoin_use;
                $output['mess'] = $ims->lang['global']['success_use_wcoin'];
            }
//			$output['user_wcoin'] = $user_wcoin;
            $cart_info = Session::Get ('cart_info', array());
            $cart_info['wcoin_use'] = $wcoin_use;
            Session::Set ('cart_info', $cart_info);
            return json_encode($output);
        }
    }

    function promotion_info($cart_total, &$code) {
	   	global $ims;

	   	$output = array(
	       'promotion_id' => 0,
	       'value_type' => 0,
	       'value' => 0,
	       'price' => 0,
	       'percent' => 0,
	       'mess' => ''
	   	);
	   
	   	$err_promotion = '';
	   	$promotion_percent = 0;
	   	$promotion_code = (isset($code) && $code) ? trim($code) : Session::get('promotion_code');
	      	if(isset($ims->data['user_cur']['user_id'])){
	        	$promotion_log = $ims->db->do_get_num('promotion_log','is_show=1 and promotion_id="'.$promotion_code.'" ');
	      	}
	      // if($cart_total == 0){

	      // }
	      // else 
	      	if($promotion_code) {
	         	$sql = "select * 
	                     from promotion 
	                     where is_show=1
	                        and promotion_id='".$promotion_code."'
	                     limit 0,1";
	          	$result = $ims->db->query($sql);
	          if ($row_promotion = $ims->db->fetch_row($result)) {
	            $row_promotion['num_used'] = (isset($promotion_log) && $promotion_log!=0)?$promotion_log:$row_promotion['num_use'];
	            $output['type_promotion'] = $row_promotion['type_promotion'];
	            if($row_promotion['date_start'] > time() || $row_promotion['date_end'] < time()) {
	               $err_promotion = $ims->lang['product']['err_promotion_timeover'];
	            }            
	            elseif(round($row_promotion['total_min']) > round($cart_total)) {
	              $err_promotion = $ims->site_func->get_lang('err_promotion_min_cart','product',array('{min_cart}' => $ims->func->get_price_format($row_promotion['total_min'], 0)));
	            } 
	            elseif($row_promotion['num_used'] >= $row_promotion['max_use']) {
	              $err_promotion = $ims->lang['product']['err_promotion_numover'];
	            }
	            elseif($row_promotion['type_promotion'] != 'apply_freeship'){
	              $flag = 0;
	              if($row_promotion['type_promotion'] == 'apply_email' || $row_promotion['type_promotion'] == 'apply_user'){
	                $err_promotion = $ims->lang['product']['err_promotion_user'];
	                if(isset($ims->data['user_cur']['email']) && in_array($ims->data['user_cur']['email'], explode(',',$row_promotion['list_email']))){
	                  $flag = 1;
	                  $err_promotion = '';
	                }
	                if(isset($ims->data['user_cur']['user_id']) && in_array($ims->data['user_cur']['user_id'], explode(',',$row_promotion['list_user']))){
	                  $flag = 1;
	                  $err_promotion = '';
	                }
	              }elseif($row_promotion['type_promotion'] == 'apply_product'){
	                $arr_cart_list_pro = Session::get('cart_list_pro');
	                $err_promotion = $ims->lang['product']['err_promotion_product'];
	                foreach($arr_cart_list_pro as $row) {
	                  if(in_array($row,explode(',',$row_promotion['list_product']))){
	                    $flag = 1;
	                    $err_promotion = '';                  
	                  }
	                }
	              }elseif($row_promotion['type_promotion'] == 'apply_all'){
	                $flag = 1;
	              }
	              if($flag == 1){
	                  $tmp_percent = 0;
	                  $tmp_price = 0;
	                  switch ($row_promotion['value_type']){
	                      case 1:
	                          $tmp_percent = $row_promotion['value'];
	                          $tmp_price = round(($tmp_percent * $cart_total) / 100, 2);
	                          if($tmp_price > $row_promotion['value_max']){
	                            $tmp_price = $row_promotion['value_max'];
	                          }
	                          break;
	                      default:
	                          $tmp_price = $row_promotion['value'];
	                          $tmp_percent = round(($tmp_price * 100) / $cart_total, 2);
	                          break;
	                  }
	                  if($tmp_percent < 0 || $tmp_percent > 100) {
	                      $output['mess'] = 'Giá trị không hợp lệ!';
	                      return $output;
	                  }
	                  $output['promotion_id'] = Session::set ('promotion_code', $row_promotion['promotion_id']);
	                  $output['value_type'] = $row_promotion['value_type'];
	                  $output['total_min'] = $row_promotion['total_min'];
	                  $output['value'] = $row_promotion['value'];
	                  $output['price'] = $tmp_price;
	                  $output['percent'] = $tmp_percent;
	              }else{
	                  Session::set ('promotion_code', '');
	                  $err_promotion = $ims->lang['product']['err_promotion_user'];
	              }
	            }elseif($row_promotion['type_promotion'] == 'apply_freeship'){
	                Session::set ('promotion_code', $row_promotion['promotion_id']);
	                $err_promotion = $ims->lang['product']['freeship'];                
	            }
	          } else {
	            $err_promotion = $ims->lang['product']['err_promotion_wrong'];
	            Session::set ('promotion_code', '');
	          }
	        $promotion_code = Session::get('promotion_code');
	      }
	   //    elseif(!empty($promotion_code)) {
	   //    //$err_promotion = str_replace('{min_cart}',$ims->func->get_price_format($ims->setting['voucher']['min_cart_promotion'], 0),$ims->lang['global']['err_promotion_min_cart']);
	   //    $err_promotion = $ims->site_func->get_lang ('err_promotion_min_cart', 'product', array(
	   //              '{min_cart}' => $ims->func->get_price_format($ims->setting['voucher']['min_cart_promotion'], 0)
	   //          ));
	   //    Session::set ('promotion_code', '');
	   // }   
	   $output['mess'] = $err_promotion;

	   return $output;
	}
	
	private function check_user ($arr_data, $type_check = 'username')
	{
		global $ims;
		
		$output = 1;
		$user_id = isset($ims->data['user_cur']['user_id']) ? $ims->data['user_cur']['user_id'] : 0;
		$sql = "select ".$type_check." from user 
						where ".$type_check."='".$arr_data[$type_check]."' ) and user_id != '".$user_id."' 
						limit 0,1 ";
						// print_arr($sql);
						// die;
        $result = $ims->db->query($sql);
        if ($rcheck = $ims->db->fetch_row($result)){
			if($rcheck[$type_check] == $arr_data[$type_check]) {
				$output = 0;
			}
		}
		
		
		return $output;
	}

	function do_post_advisory(){
		global $ims;
		$input = $ims->func->unserialize_array ($ims->func->if_isset($ims->post['data'], array()));	

		if(empty($output['mess']) && Captcha::Check ($input['captcha']) != 1) {
			$output['mess'] = $ims->site_func->get_lang ('err_invalid', $this->modules, array(
                '[name]' => $ims->site_func->get_lang ('Captcha', $this->modules)
            ));
			return json_encode($output);
		}
		$output = array(
			'ok' => 0,
			'mess' => $ims->site_func->get_lang ('error_advisory', 'advisory')
		);
		$max_id = $this->get_maxid_advisory();
		$arr_in["title"] = $ims->func->input_editor($input['txtaComment']);
		$arr_in["owner_nickname"] = $input['txtName'];
		$arr_in["owner_email"] = $input['txtEmail'];
		$arr_in["item_id"] = $max_id + 1;
		$arr_in["is_show"] = 1;
		$arr_in["lang"] = $ims->conf['lang_cur'];
		$arr_in["is_approval"] = 0;
		$arr_in["date_create"] = time();
		$arr_in["date_update"] = time();
		$ok = $ims->db->do_insert("advisory", $arr_in);
		if($ok) {
			Captcha::Set ();
			$output = $arr_in;
			$output['ok'] = 1;
			$output['mess'] = $ims->site_func->get_lang ('success_advisory', 'advisory');
		}
		return json_encode($output);
	}

	function do_get_notification(){
		global $ims;
		$output = array(
			'ok' => 0,
			'mess' => $ims->site_func->get_lang ('error_notification1', 'product')
		);
		$input = $ims->func->unserialize_array ($ims->func->if_isset($ims->post['data'], array()));	
		$query = $ims->db->query("SELECT id FROM product_notification WHERE email = '".$input['email']."' AND item_id = ". $input['item_id'] ."  ");
		$arr = $ims->db->fetch_row($query);
		$check = isset($arr['id']) ? $arr['id'] : '';
		if(isset($check) && $check > 0){
			$output['mess'] = 0;
			$output['mess'] = $ims->site_func->get_lang ('error_notification', 'product');
		}
		else{
			$arr_in["item_id"] = $input['item_id'];
			$arr_in["full_name"] = $input['name'];
			$arr_in["email"] = $input['email'];
			$arr_in["content"] = $input['content'];
			$arr_in["is_show"] = 1;
			$arr_in["date_create"] = time();
			$arr_in["date_update"] = time();
			$ok = $ims->db->do_insert("product_notification", $arr_in);
			if($ok) {
				$output['ok'] = 1;
				$output['mess'] = $ims->site_func->get_lang ('success_notification', 'product');
			}
		}
		return json_encode($output);
	}

	function do_get_order(){
		global $ims;
		$output = array(
			'ok' => 0,
			'mess' => $ims->site_func->get_lang ('error_order', 'product'),
			'data' => array()
		);
		$array = array();
		$input = $ims->func->unserialize_array ($ims->func->if_isset($ims->post['data'], array()));	
		$sql ="SELECT * FROM product_order WHERE (order_code = '".$input['order_code']."' OR d_phone = '".$input['order_code']."' OR o_phone = '".$input['order_code']."')";

		$query = $ims->db->query($sql);
		$fet = $ims->db->fetch_row($query);
		$check = $ims->db->num_rows($query);
		if($check < 1){
			return json_encode($output);
		}
		else{
			$output['ok'] = 1;
			$output['mess'] = $ims->site_func->get_lang ('success_order', 'product');
			if(isset($fet['o_phone']) && $fet['o_phone'] == $input['order_code']){
				$output['ok'] = 2;
			}
			if(isset($fet['d_phone']) && $fet['d_phone'] == $input['order_code']){
				$output['ok'] = 2;
			}
		}
		return json_encode($output);
	}

	function do_signup (){
		global $ims;
		
		$output = array(
			'ok' => 0,
			'mess' => ''
		);
		
		if($ims->site_func->checkUserLogin() == 1) {
			$output['ok'] = 1;
			$output['mess'] = 'Bạn đã đăng nhập rồi';
			return json_encode($output);
		}
		$input = $ims->func->unserialize_array ($ims->func->if_isset($ims->post['data'], array()));

		//$arr_check = array('username','password','full_name','address');
//        $arr_check = array('username','password','full_name');
        $arr_check = array('password', 'full_name');
		$arr_in = array();

		// Check số điện thoại hợp lệ
		if(isset($input['phone']) && !empty($input['phone'])){
            $code_phone = substr($input['phone'],0,3);
            if($code_phone == '+84'){
                $phone = substr($input['phone'],3);
                if(substr($phone,0,1) == 0){
                    $output['mess'] = $ims->lang['user']['invalid_phone'];
                }elseif(strlen($phone) != 9 || !preg_match ("/^[0-9]*$/", $phone)){
                    $output['mess'] = $ims->lang['user']['invalid_phone'];
                }else{
                    $input['phone'] = '0'.$phone;
                }
            }else{
                $first = substr($input['phone'],0,1);
                if($first != 0){
                    $output['mess'] = $ims->lang['user']['invalid_phone'];
                }elseif(strlen($input['phone']) != 10 || !preg_match ("/^[0-9]*$/", $input['phone'])){
                    $output['mess'] = $ims->lang['user']['invalid_phone'];
                }
            }
        }
        if(empty($output['mess'])){
            $arr_in['phone'] = $input['phone'];
            foreach($arr_check as $key) {
                if(empty($output['mess']) && (!isset($input[$key]) || empty($input[$key]))) {
                    $output['mess'] = $ims->site_func->get_lang ('err_invalid', $this->modules, array(
                        '[name]' => $ims->site_func->get_lang ($key, $this->modules)
                    ));
                    break;
                }
                $arr_in[$key] = trim($input[$key]);
            }
        }        
		$arr_in['email'] = (isset($input['username']) && $input['username'] != '') ? strtolower($input['username']) : '';
		$arr_in['username'] = (isset($input['username']) && $input['username'] != '') ? strtolower($input['username']) : '';
		if(!empty($arr_in['phone'])){
			$arr_in['username'] = $arr_in['phone'];
		}
        if(empty($output['mess']) && (isset($arr_in['full_name']) && $arr_in['full_name'] != '')){
            if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $arr_in['full_name'])){
                $output['mess'] = $ims->site_func->get_lang ('err_invalid', $this->modules, array(
                    '[name]' => $ims->site_func->get_lang ('full_name', $this->modules)
                ));
            }
        }
		//$arr_in['full_name'] = trim($arr_in['first_name'].' '.$arr_in['last_name']);
		
		if(empty($output['mess']) && (isset($arr_in['username']) && $arr_in['username'] != '') && $this->check_user ($arr_in,'username') != 1) {
			$output['mess'] = $ims->site_func->get_lang ('err_exited', $this->modules, array(
                '[name]' => $ims->site_func->get_lang ('email', $this->modules)
            ));
		}
		if(empty($output['mess']) && (isset($arr_in['email']) && $arr_in['email'] != '') && $this->check_user ($arr_in,'email') != 1) {
			$output['mess'] = $ims->site_func->get_lang ('err_exited', $this->modules, array(
                '[name]' => $ims->site_func->get_lang ('email', $this->modules)
            ));
		}
		if(empty($output['mess']) && (isset($arr_in['phone']) && $arr_in['phone'] != '') && $this->check_user ($arr_in,'phone') != 1) {
			$output['mess'] = $ims->site_func->get_lang ('err_exited', $this->modules, array(
                '[name]' => $ims->site_func->get_lang ('phone', $this->modules)
            ));
		}
		if(empty($output['mess']) && !empty($input['captcha']) && Captcha::Check ($input['captcha']) != 1) {
			$output['mess'] = $ims->site_func->get_lang ('err_invalid', $this->modules, array(
                '[name]' => $ims->site_func->get_lang ('captcha', $this->modules)
            ));
		}
		if(empty($output['mess'])) {
			$arr_in['password'] = $ims->func->md25($arr_in['password']);
		}

		if(empty($output['mess'])){
			$date_login = time();
            $check_log_cookie = $ims->db->load_item('user_recommend_log', 'is_show = 1 and referred_user_id = 0 and (referred_phone = "'.$input['phone'].'" or referred_email = "'.$arr_in['email'].'")', 'id');
            if(!$check_log_cookie){
                $arr_in["user_contributor"] = isset($_COOKIE['user_contributor']) ? $_COOKIE['user_contributor'] : '';
                $arr_in["type_contributor"] = isset($_COOKIE['type_contributor']) ? $_COOKIE['type_contributor'] : '';
                $user_row = $ims->db->load_row('user', 'is_show = 1 AND user_code = "'.$arr_in["user_contributor"].'"');
                if($user_row){
                    if( $user_row['user_contributor_level'] > 0 &&
                        $user_row['root_id'] > 0 &&
                        $user_row['user_contributor_level'] <= 5 &&
                        $user_row['user_contributor'] != ''
                    ){
                        $arr_in["root_id"] = $user_row["root_id"];
                        $arr_in["user_contributor_level"] = $user_row["user_contributor_level"] + 1;
                    }else{
                        if ($arr_in["user_contributor"] != '') {
                            $arr_in["root_id"] = $user_row["user_id"];
                            $arr_in["user_contributor_level"] = $user_row["user_contributor_level"] + 1;
                        }
                    }
                }
            }
			$arr_in["phone"] = $input["phone"];
			$arr_in["address"] = $input["address"];
			$arr_in["province"] = $input["province"];
			$arr_in["district"] = $input["district"];
			$arr_in["ward"] = $input["ward"];
			$arr_in["birthday"] = 0;
			$arr_in["folder_upload"] = $ims->db->getAutoIncrement ('user').'c'.$ims->func->random_str(4);
			$arr_in["user_code"] = $ims->db->getAutoIncrement ('user').'c'.$ims->func->random_str(10);
			$arr_in["show_order"] = 0;
			$arr_in["is_show"] = ($ims->setting['user']['signup_type'] == 0) ? 1 : 0;			
			$arr_in["date_login"] = $date_login;
			$arr_in["date_create"] = time();
			$arr_in["date_update"] = time();
			//address book ==============================
			$arr_ad["id"] = $ims->db->getAutoIncrement('user')."0";
			$arr_ad["full_name"] = $arr_in["full_name"];
			$full_name = explode(" ",$arr_ad["full_name"]);
			$arr_ad["first_name"] = $full_name[count($full_name) - 1];
			unset($full_name[count($full_name) - 1]);
			$arr_ad["last_name"] = implode(" ", $full_name);
			$arr_ad["phone"] = $arr_in["phone"];
			$arr_ad["email"] = $arr_in["email"];
			$arr_ad["address"] = $arr_in["address"];
			$arr_ad["province"] = $arr_in["province"];
			$arr_ad["district"] = $arr_in["district"];
			$arr_ad["ward"] = $arr_in["ward"];				
			$arr_ad["is_default"] = 1;
			$arr_address_book[$arr_ad["id"]] = $arr_ad;
			$arr_in["arr_address_book"] = serialize($arr_address_book);

			$ok = $ims->db->do_insert("user", $arr_in);
			if($ok) {
				$userid = $ims->db->insertid();

                if($check_log_cookie){
                    $ims->db->do_update('user_recommend_log', array('referred_user_id' => $userid), 'id = '.$check_log_cookie);
                }elseif(isset($arr_in["user_contributor"]) && $arr_in["user_contributor"] != '' && $user_row){
                    $recommend_log = array(
                        'type' => 'contributor',
                        'recommend_user_id' => $user_row['user_id'],
                        'recommend_link' => 'contributor='.$ims->func->base64_encode($arr_in["user_contributor"]).'&type='.$arr_in["type_contributor"],
                        'referred_user_id' => $userid,
                        'referred_full_name' => $arr_in["full_name"],
                        'referred_phone' => $arr_in["phone"],
                        'referred_email' => $arr_in["email"],
                        'is_show' => 1,
                        'date_create' => time(),
                        'date_update' => time(),
                    );
                    $ims->db->do_insert("user_recommend_log", $recommend_log);
                }elseif(isset($_COOKIE["deeplink"])){
                    $deeplink_user = $ims->db->load_row('user_deeplink', 'is_show = 1 and id = '.$_COOKIE["deeplink"], 'user_id, short_code');
                    if($deeplink_user){
                        $recommend_log = array(
                            'type' => 'deeplink',
                            'recommend_user_id' => $deeplink_user['user_id'],
                            'recommend_link' => $deeplink_user['short_code'],
                            'deeplink_id' => $_COOKIE["deeplink"],
                            'referred_user_id' => $userid,
                            'referred_full_name' => $arr_in["full_name"],
                            'referred_phone' => $arr_in["phone"],
                            'referred_email' => $arr_in["email"],
                            'is_show' => 1,
                            'date_create' => time(),
                            'date_update' => time(),
                        );
                        $ims->db->do_insert("user_recommend_log", $recommend_log);
                    }
                }
				$output['ok'] = 1;
				$output['mess'] = $ims->site_func->get_lang ('mess_success', $this->modules, array('[name]' => $ims->site_func->get_lang ('signup', $this->modules)));
			    if($ims->setting['user']['signup_type'] == 1){
				    $output['mess'] = $ims->site_func->get_lang ('mess_success_1', $this->modules, array('[name]' => $ims->site_func->get_lang ('signup', $this->modules)));
                    Captcha::Set ();

                    $mail_arr_key = array(
                        '{full_name}',
                        '{username}',
                        '{password}',
                        '{link_active}',
                        '{domain}',
                    );
                    $mail_arr_value = array(
                        $arr_in["full_name"],
                        $arr_in['phone'],
                        $input['password'],
                        '<a href="'.$ims->site_func->get_link ($this->modules, $ims->setting[$this->modules]["active_link"])."?code=".$arr_in["user_code"].'">'.$ims->site_func->get_link ($this->modules, $ims->setting[$this->modules]["active_link"])."?code=".$arr_in["user_code"].'</a>',
                        $ims->conf['rooturl'],
                    );
                    //send to customer
                    $ims->func->send_mail_temp ('signup-'.$ims->setting['user']['signup_type'], $arr_in["email"], $ims->conf['email'], $mail_arr_key, $mail_arr_value);
                }elseif ($ims->setting['user']['signup_type'] == 3) { //Kích hoạt OTP
                    Session::Set('signup_info', array(
                        'phone' => $input['phone'],
                        'user_id' => $userid,
                    ));
                    $otp = rand(1000, 9999);
                    $data_sms = array(
                        'ApiKey'    => $ims->setting['user']['esms_ApiKey'],
                        'SecretKey' => $ims->setting['user']['esms_SecretKey'],
                        'Brandname' => $ims->setting['user']['esms_Brandname'],
                        'Phone'     => $input['phone'],
                        'Content'   => str_replace('{otp}', $otp, $ims->setting['user']['esms_Content']),
                        'SmsType'   => 2,
                        'Sandbox'   => 1,
                        // 'RequestId' => $col['order_code'],
                    );
                    $data_sms = http_build_query ($data_sms);
                    $SMS = $ims->site_func->sendPostData('http://rest.esms.vn/MainService.svc/json/SendMultipleMessage_V4_post/', $data_sms, 'post', 1, '', array());
                    if (!empty($SMS)) {
                        $SMS = json_decode($SMS);
                        if (isset($SMS->CodeResult) && $SMS->CodeResult==100) {
                            $sms = array();
                            $sms['smsid'] = $SMS->SMSID;
                            $output['sms'] = $sms;
                            $col = array();
                            $col['date_expire'] = time()+60*15;
                            $col['otp'] = $otp;
                            $ok = $ims->db->do_update('user', $col, " user_id='".$userid."' ");
                            if($ok){
                                $output['ok'] = 2;
                                $output['verify_otp'] = $ims->site_func->get_link ($this->modules, $ims->setting[$this->modules]["verify_otp_link"]);
                            }else{
                                $output['ok'] = 2;
                                $output['verify_otp'] = '';
                                $output['mess'] = $ims->lang['user']['request_otp_false'];
                            }
                        }
                    }else{
                        $output['ok'] = 2;
                        $output['verify_otp'] = '';
                        $output['mess'] = $ims->lang['user']['request_otp_false'];
                    }
			    } elseif ($arr_in["is_show"] == 1) {
					Session::Set('user_cur', array(
						'userid' => $userid,
//						'username' => $arr_in["username"],
						'username' => (isset($input['username']) && !empty($input['username'])) ? $input['username'] : '',
						'password' => $arr_in["password"],
						'session' => ''//md5($arr_in["username"].$date_login)
					));
				}
			} else {				
				$output['mess'] = $ims->site_func->get_lang ('mess_false', $this->modules, array(
	                '[name]' => $ims->site_func->get_lang ('signup', $this->modules)
	            ));
			}
		}
		
		
		return json_encode($output);
	}
	
	function do_signin (){
		global $ims;
		
		$output = array(
			'ok' => 0,
			'mess' => ''
		);
		
		if($ims->site_func->checkUserLogin() == 1) {
			$output['ok'] = 1;
			$output['mess'] = $ims->site_func->get_lang ('mess_success', $this->modules, array(
                '[name]' => $ims->site_func->get_lang ('signin', $this->modules)
            ));
			return json_encode($output);
		}
		
		$input_tmp = $ims->post['data'];
		foreach($input_tmp as $key) {
			$input[$key['name']] = $key['value'];
		}
		
		$arr_check = array('username','password');
		$arr_in = array();
		
		foreach($arr_check as $key) {
			if(empty($output['mess']) && (!isset($input[$key]) || empty($input[$key]))) {
				$output['mess'] = $ims->site_func->get_lang ('err_invalid', $this->modules, array(
                '[name]' => $ims->site_func->get_lang ($key, $this->modules)
            ));
				break;
			}
			$arr_in[$key] = $input[$key];
		}
		
		if(empty($output['mess'])) {
			$arr_in['password'] = $ims->func->md25($arr_in['password']);
		}

		if(empty($output['mess'])){
			$user = $ims->db->load_row("user", " (username='".$arr_in['username']."' or phone = '".$arr_in['username']."' or email = '".$arr_in['username']."') AND password='".$arr_in['password']."' ");

			if (!empty($user)) {
				$output['mess'] = $ims->site_func->get_lang ('signin_false', $this->modules);
				switch ($user['is_show']) {
					case 1:
						// Cập nhật giỏ hàng temp (nếu tồn tại giỏ hàng web)
						$arr_cart = Session::Get('cart_pro', array());
						// Cập nhật thêm danh sách sản phẩm mua kèm ưu đãi
                        $bundled_selected = Session::Get('bundled_selected', array());
						if (!empty($arr_cart)) {
							// Xóa giỏ hàng tạm
							$ims->db->query('DELETE FROM `product_order_temp` WHERE user_id="'.$user['user_id'].'" ');
							foreach ($arr_cart as $key => $value) {
								$arr_ins 				= array();
								$arr_ins['item_id'] 	= $value['item_id'];
								$arr_ins['option_id'] 	= $value['option_id'];
								$arr_ins['combo_info'] 	= $value['combo_info'];
								$arr_ins['user_id'] 	= $user['user_id'];
								$arr_ins['quantity'] 	= $value['quantity'];
								$arr_ins['is_show'] 	= 1;
								$arr_ins['date_create'] = time();
								$arr_ins['date_update'] = time();
								$arr_ins['bundled_product'] = ($bundled_selected) ? $ims->func->serialize($bundled_selected) : '';
								$ims->db->do_insert("product_order_temp", $arr_ins);
							}
						}
						$arr_up = array();
						if(empty($user["token_login"])){
							$user["token_login"] = $ims->func->random_str(20, 'ln');
							$arr_up["token_login"] = $user["token_login"];
						}
						//list_watched
						$list = array();
				        if(isset($_COOKIE['list_watched']) && $_COOKIE['list_watched']){
				            parse_str($_COOKIE['list_watched']);
				            $list = explode(',', $list);
				        }
				        $arr_tmp = array();
				        if ($ims->site_func->checkUserLogin() == 1) {
				            $list_watched_user = ($user['list_watched']) ? $ims->func->unserialize($user['list_watched']) : array();
				            if($list_watched_user){
				                foreach ($list_watched_user as $item){
				                    $arr_tmp[] = $item['id'];
				                }
				            }
				        }
				        $list = array_unique(array_merge($list, $arr_tmp));
				        $arr_watched = array();
				        foreach ($list as $key => $value) {
				        	$arr_watched[$value]['id'] = $value;
			                $arr_watched[$value]['date_create'] = time();
				        }
				        $arr_up['list_watched'] = $ims->func->serialize($arr_watched);
				        $ims->db->do_update("user", $arr_up, " user_id='".$user["user_id"]."' ");
				        //

						Session::Set('user_cur', array(
							'userid'   => $user['user_id'],
							'username' => $user['username'],
							'password' => $user['password'],
							'session'  => $user['session']
						));

                        $check_log_cookie = $ims->db->load_item('user_recommend_log', 'is_show = 1 and referred_user_id = 0 and (referred_phone = "'.$user['phone'].'" or referred_email = "'.$user['email'].'")', 'id');
                        if($check_log_cookie){
                            $ims->db->do_update('user_recommend_log', array('referred_user_id' => $user['user_id']), 'id = '.$check_log_cookie);
                        }else{
                            // Nhập log deeplink hoặc contributor
                            $check = $ims->db->load_item('user_recommend_log', 'is_show = 1 and referred_user_id = '.$user['user_id'], 'id');
                            if(!$check){
                                $recommend_log = array();
                                if(isset($_COOKIE['deeplink'])){
                                    $deeplink = $ims->db->load_row('user_deeplink', ' is_show = 1 and id = '.$_COOKIE['deeplink'], 'id, referred_member, user_id, short_code');
                                    if($deeplink){
                                        if($deeplink['user_id'] != $user['user_id']){
                                            if($deeplink['referred_member'] == ''){
                                                $referred_member = $user['user_id'];
                                            }else{
                                                $referred_member = $deeplink['referred_member'].','.$user['user_id'];
                                            }
                                            $ims->db->do_update('user_deeplink', array('referred_member' => $referred_member), ' id = "'.$_COOKIE['deeplink'].'"');
                                            $recommend_log = array(
                                                'type' => 'deeplink',
                                                'recommend_user_id' => $deeplink['user_id'],
                                                'recommend_link' => $deeplink['short_code'],
                                                'deeplink_id' => $deeplink['id'],
                                                'referred_user_id' => $user['user_id'],
                                                'is_show' => 1,
                                                'date_create' => time(),
                                                'date_update' => time(),
                                            );
                                        }else{
                                            setcookie('deeplink', '', time() - 3600, '/');
                                        }
                                    }
                                }elseif(isset($_COOKIE['user_contributor'])){
                                    $contributor = $_COOKIE['user_contributor'];
                                    $type = isset($_COOKIE['type_contributor']) ? $_COOKIE['type_contributor'] : '';
                                    $recommend_user = $ims->db->load_row('user', 'is_show = 1 and user_code = "'.$contributor.'"');
                                    if($recommend_user){
                                        if($recommend_user['user_code'] != $user['user_code']){
                                            $recommend_log = array(
                                                'type' => 'contributor',
                                                'recommend_user_id' => $recommend_user['user_id'],
                                                'recommend_link' => 'contributor='.$ims->func->base64_encode($contributor).'&type='.$type,
                                                'referred_user_id' => $user['user_id'],
                                                'is_show' => 1,
                                                'date_create' => time(),
                                                'date_update' => time(),
                                            );
                                        }else{
                                            setcookie('user_contributor', '', time() - 3600, '/');
                                            setcookie('type_contributor', '', time() - 3600, '/');
                                        }
                                    }
                                }
                                if($recommend_log){
                                    $ims->db->do_insert("user_recommend_log", $recommend_log);
                                }
                            }
                        }
						$output['ok'] = 1;
						$output['mess'] = $ims->site_func->get_lang ('signin_success', $this->modules);
						if(!empty($input['remember'])){
							$user_remember = $ims->func->encrypt_decrypt('encrypt', json_encode($arr_in), 'remember_me', 'rememberMe');
							$user_remember = bin2hex($user_remember);
							$cookie_name = "__rme";
							$cookie_time = time() + 60*60*24*30;
							setcookie($cookie_name, $user_remember, $cookie_time, "/");
						}
						break;
					case 0:
						$output['mess'] = $ims->site_func->get_lang ('signin_false_1', $this->modules);
						break;
					case 2:
						$output['mess'] = $ims->site_func->get_lang ('signin_false_2', $this->modules);
						break;
				}
			} else {
				$output['mess'] = $ims->site_func->get_lang ('mess_false', $this->modules, array(
	                '[name]' => $ims->site_func->get_lang ('signin', $this->modules)
	            ));
			}
		}
		return json_encode($output);
	}
	
	function do_signout ()
	{
		global $ims;
		
		$output = array(
			'ok' => 1
		);
		setcookie('__rme', '', time() - 3600, '/');
		Session::Delete('user_cur');
		Session::Delete('ordering_address');	
		Session::Delete('promotion_code');	
		Session::Delete('gift_voucher');	
		Session::Delete('cart_pro');	
		Session::Delete('cart_list_pro');	
		Session::Delete('arr_info_booked');

		return json_encode($output);
	}
	

	function do_account (){
		global $ims;
		
		$output = array(
			'ok' => 0,
			'mess' => ''
		);
		
		if($ims->site_func->checkUserLogin() != 1) {
			$output['mess'] = $ims->lang['global']['signin_false'];
			return json_encode($output);
		}

		$input_tmp = $ims->post;		
        if(isset($input_tmp['is_request_affiliates']) && $input_tmp['is_request_affiliates'] == 1){
            $check = 0;
        }else{
            $check = 1;
        }
		foreach($input_tmp as $key => $v) {
		    if($check == 0){
                if($key == 'affiliate_picture'){
                    $check = 1;
                    $input['affiliate_picture'] = str_replace($ims->conf['rooturl'].'uploads/', '', $v);
                }else{
                    $input[$key] = $v;
                }
            }else{
                $input[$key] = $v;
            }
		}

        if(isset($input_tmp['is_request_affiliates']) && $input_tmp['is_request_affiliates'] == 1){
            if(!$check){
                $output['mess'] = $ims->lang['user']['no_picture_affiliate'];
            }else{
                if(!empty($input['affiliate_picture']) && is_array($input['affiliate_picture'])){
                    $input['affiliate_picture'] = implode(',', $input['affiliate_picture']);
                }
            }
        }
        
        $arr_check = array('first_name', 'phone', 'email', 'province', 'district', 'ward', 'address');
		if($ims->data['user_cur']['fb_id'] != '' || $ims->data['user_cur']['fb_id'] != ''){
        	$arr_check = array('username');
		}
		$arr_in = array();
		foreach($arr_check as $key) {
			if(empty($output['mess']) && (!isset($input[$key]) || empty($input[$key]))) {
				$output['mess'] = $ims->site_func->get_lang ('err_empty', $this->modules, array(
                    '[name]' => $ims->site_func->get_lang ($key, $this->modules)
                ));
				break;
			}
			$arr_in[$key] = trim($input[$key]);
		}
		if(isset($input["username"])){
			if(empty($output['mess']) && $this->check_user ($arr_in,'username') != 1) {
				$output['mess'] = $ims->site_func->get_lang ('err_exited', $this->modules, array(
	                '[name]' => $ims->site_func->get_lang ('username', $this->modules)
	            ));
			}
		}
		if(isset($input["email"])){
			if(empty($output['mess']) && $this->check_user ($arr_in,'email') != 1) {
				$output['mess'] = $ims->site_func->get_lang ('err_exited', $this->modules, array(
	                '[name]' => $ims->site_func->get_lang ('email', $this->modules)
	            ));
			}
		}
		if(isset($input["phone"])){
			$code_phone = substr($input['phone'],0,3);
	       	if($code_phone == '+84'){
	           	$phone = substr($input['phone'],3);
	           	if(substr($phone,0,1) == 0){	               	
	               	$output['mess'] = $ims->site_func->get_lang ('err_invalid', $this->modules, array(
	                    '[name]' => $ims->site_func->get_lang ('phone', $this->modules)
	                ));
	           	}elseif(strlen($phone) != 9 || !preg_match ("/^[0-9]*$/", $phone)){
	               	$output['mess'] = $ims->site_func->get_lang ('err_invalid', $this->modules, array(
	                    '[name]' => $ims->site_func->get_lang ('phone', $this->modules)
	                ));
	           	}else{
	               	$input['phone'] = '0'.$phone;
	           	}
	       	}else{
	           	$first = substr($input['phone'],0,1);	           	
	           	if($first != 0){
	               	$output['mess'] = $ims->site_func->get_lang ('err_invalid', $this->modules, array(
	                    '[name]' => $ims->site_func->get_lang ('phone', $this->modules)
	                ));
	           	}elseif(strlen($input['phone']) != 10 || !preg_match ('/^(0?)(3[2-9]|5[6|8|9]|7[0|6-9]|8[0-6|8|9]|9[0-4|6-9])[0-9]{7}$/', $input['phone'])){
	               	$output['mess'] = $ims->site_func->get_lang ('err_invalid', $this->modules, array(
	                    '[name]' => $ims->site_func->get_lang ('phone', $this->modules)
	                ));
	           	}
	       	}
			if(empty($output['mess']) && $this->check_user ($arr_in,'phone') != 1) {
				$output['mess'] = $ims->site_func->get_lang ('err_exited', $this->modules, array(
	                '[name]' => $ims->site_func->get_lang ('phone', $this->modules)
	            ));
			}			
		}
		// if(!checkdate($ims->post['month'], $ims->post['date'], $ims->post['year'])){
		// 	$output['mess'] = "Ngày tháng không hợp lệ";
		// }
		// $input["birthday"] = strtotime($ims->post['date'].'-'.$ims->post['month'].'-'.$ims->post['year']);
		
		if(isset($_FILES['picture']) && $_FILES['picture']['error'] == 0){
			$_FILES['picture']['name'] = 'avatar.png';
            $folder_upload = "user/".$ims->data['user_cur']['folder_upload'].'/'.date('Y',time()).'_'.date('m',time());
            $out_pic = array();
            $out_pic = $ims->site_func->upload_image($folder_upload,'picture');
            if($out_pic['ok'] ==1){
                $arr_in['picture'] = $out_pic['url_picture'];
            }else{
                $output['ok'] = 0;
                $output['mess']  = $out_pic['mess'];
                return json_encode($output);
            }
        }
		if(empty($output['mess'])){
			if($ims->data['user_cur']['fb_id'] != '' || $ims->data['user_cur']['gg_id'] != ''){
            	$arr_in["username"] = $ims->func->if_isset($input["username"]);
            	$arr_in["email"] 	= $ims->func->if_isset($input["username"]);
			}else{
                $arr_in["username"] = $ims->func->if_isset($input["email"]);
                $arr_in["email"] 	= $ims->func->if_isset($input["email"]);
            }
            // if(!empty($input["picture"]) && strpos($input["picture"], '/resources/images') === false){
            //     $arr_in["picture"] 	= $ims->func->get_input_pic($input["picture"]);
            // }
            $arr_in["last_name"] = $ims->func->if_isset($input["last_name"], $ims->data['user_cur']['last_name']);
            $arr_in["first_name"] = $ims->func->if_isset($input["first_name"], $ims->data['user_cur']['first_name']);
            $arr_in["full_name"] = $arr_in["last_name"]." ".$arr_in["first_name"];

            $arr_in["phone"] 	= $ims->func->if_isset($input["phone"], $ims->data['user_cur']['phone']);
            $arr_in["landline"] = $ims->func->if_isset($input["landline"], $ims->data['user_cur']['landline']);
            $arr_in["website"] 	= $ims->func->if_isset($input["website"], $ims->data['user_cur']['website']);
            $arr_in["job"] 		= $ims->func->if_isset($input["job"], $ims->data['user_cur']['job']);
            $arr_in["company"] 	= $ims->func->if_isset($input["company"], $ims->data['user_cur']['company']);            
            // $arr_in["birthday"] = $ims->func->if_isset($input["birthday"], $ims->data['user_cur']['birthday']);
            // $arr_in["gender"] = $ims->func->if_isset($input["gender"], $ims->data['user_cur']['gender']);
            $arr_in["province"] = $ims->func->if_isset($input["province"], $ims->data['user_cur']['province']);
            $arr_in["district"] = $ims->func->if_isset($input["district"], $ims->data['user_cur']['district']);
            $arr_in["ward"] 	= $ims->func->if_isset($input["ward"], $ims->data['user_cur']['ward']);
            $arr_in["address"] 	= $ims->func->if_isset($input["address"], $ims->data['user_cur']['address']);

			// Dành cho đăng ký tài khoản affiliates
            if(isset($input_tmp['is_request_affiliates']) && $input_tmp['is_request_affiliates'] == 1){
                $arr_in["is_request_affiliates"] = $ims->func->if_isset($input["is_request_affiliates"], 0);
                $arr_in['affiliate_picture'] = $ims->func->if_isset($input["affiliate_picture"], '');
                $arr_in['bank_account_owner'] = $ims->func->if_isset($input["bank_account_owner"], '');
                $arr_in['bank_account_number'] = $ims->func->if_isset($input["bank_account_number"], '');
                $arr_in['bank_name'] = $ims->func->if_isset($input["bank_name"], '');
                $arr_in['bank_branch'] = $ims->func->if_isset($input["bank_branch"], '');
            }

			$arr_in["date_update"] = time();			
			$ok = $ims->db->do_update("user", $arr_in, " user_id='".$ims->data['user_cur']['user_id']."'");	
			if($ok){			
				$output['ok'] = 1;		
				$output['mess'] = $ims->site_func->get_lang ('edit_success', $this->modules);
				if(!empty($arr_in['username'])){
				    $user_cur = $ims->db->load_row('user', 'user_id = '.$ims->data['user_cur']['user_id'], 'user_id, username, password, session');
                    Session::Set('user_cur', array(
                        'userid'   => $user_cur['user_id'],
                        'username' => $user_cur['username'],
                        'password' => $user_cur['password'],
                        'session'  => $user_cur['session']
                    ));

                }
			}else{
				$output['mess'] = $ims->site_func->get_lang ('edit_false', $this->modules);
			}
		}
		
		return json_encode($output);
	}

	function do_remove_avatar (){
		global $ims;

		$output = array(
			'ok' => 0,
			'mess' => ''
		);
		
		if($ims->site_func->checkUserLogin() != 1) {
			$output['mess'] = $ims->lang['global']['signin_false'];
			return json_encode($output);
		}
		$ok = $ims->db->do_update('user', array('picture' => ''), ' user_id="'.$ims->data['user_cur']['user_id'].'" ');
		if($ok){
			$output['ok'] = 1;
		}
		return json_encode($output);
	}
	

	function do_change_pass ()
	{
		global $ims;
		
		$output = array(
			'ok' => 0,
			'mess' => ''
		);
		
		if($ims->site_func->checkUserLogin() != 1) {
			$output['mess'] = $ims->lang['global']['signin_false'];
			return json_encode($output);
		}
		
		$input_tmp = $ims->post['data'];
		foreach($input_tmp as $key) {
			$input[$key['name']] = $key['value'];
		}
		/*print_arr($input);
		die('adasd');*/
		
		$arr_check = array('password_cur','password','re_password');
		$arr_in = array();
		
		foreach($arr_check as $key) {
			if(empty($output['mess']) && (!isset($input[$key]) || empty($input[$key]))) {
				$output['mess'] = $ims->site_func->get_lang ('err_invalid', $this->modules, array(
                    '[name]' => $ims->site_func->get_lang ($key, $this->modules)
                ));
				break;
			}
			$arr_in[$key] = trim($input[$key]);
		}
		
		$arr_in['password_cur'] = $ims->func->md25($arr_in['password_cur']);
		$arr_in['password'] = $ims->func->md25($arr_in['password']);
		$arr_in['re_password'] = $ims->func->md25($arr_in['re_password']);
		
		if($arr_in['password_cur'] != $ims->data['user_cur']['password']) {
			$output['mess'] = $ims->site_func->get_lang ('err_invalid', $this->modules, array(
                        '[name]' => $ims->site_func->get_lang ('password_cur', $this->modules)
                    ));
		}
		if($arr_in['password'] != $arr_in['re_password']) {
			$output['mess'] = $ims->site_func->get_lang ('err_invalid', $this->modules, array(
                        '[name]' => $ims->site_func->get_lang ('re_password', $this->modules)
                    ));
		}
		
		unset($arr_in['password_cur']);
		unset($arr_in['re_password']);
		
		if(empty($output['mess'])){
			$arr_in["date_update"] = time();
			$ok = $ims->db->do_update("user", $arr_in, " user_id='".$ims->data['user_cur']['user_id']."'");	
			if($ok){				
				$user_cur = Session::Get('user_cur', array());
				if(count($user_cur)) {
					$user_cur['password'] = $arr_in['password'];
					Session::Set('user_cur', $user_cur);
				}												
				$output['ok'] = 1;		
				$output['mess'] = $ims->site_func->get_lang ('edit_success', $this->modules);
			}else{
				$output['mess'] = $ims->site_func->get_lang ('edit_false', $this->modules);
			}
		}
		
		return json_encode($output);
	}

	function do_change_pass_otp ()
	{
		global $ims;
		
		$output = array(
			'ok' => 0,
			'link' => '',
			'mess' => '',
		);
		$ims->site_func->setting('user');
		$input_tmp = $ims->post['data'];
		foreach($input_tmp as $key) {
			$input[$key['name']] = $key['value'];
		}
		/*print_arr($input);
		die('adasd');*/
		
		$arr_check = array('phone','otp','password','re_password');
		$arr_in = array();
		
		foreach($arr_check as $key) {
			if(empty($output['mess']) && (!isset($input[$key]) || empty($input[$key]))) {
				$output['mess'] = $ims->site_func->get_lang ('err_invalid', $this->modules, array(
                    '[name]' => $ims->site_func->get_lang ($key, $this->modules)
                ));
				break;
			}
			$arr_in[$key] = trim($input[$key]);
		}
		$user = $ims->db->load_row('user','is_show=1 and (username="'.$arr_in['phone'].'" or phone="'.$arr_in['phone'].'")');
		if(empty($user)){
			$output['mess'] = $ims->lang['user']['user_not_found'];
		}
		$arr_in['password'] = $ims->func->md25($arr_in['password']);
		$arr_in['re_password'] = $ims->func->md25($arr_in['re_password']);
		
		if($arr_in['otp'] != $user['otp']) {
			$output['mess'] = $ims->lang['user']['verify_otp_false3'];
		}
		if($arr_in['password'] != $arr_in['re_password']) {
			$output['mess'] = $ims->site_func->get_lang ('err_invalid', $this->modules, array(
                        '[name]' => $ims->site_func->get_lang ('re_password', $this->modules)
                    ));
		}
		unset($arr_in['phone']);
		unset($arr_in['password_cur']);
		unset($arr_in['re_password']);
		
		if(empty($output['mess'])){
			$arr_in["otp"] = "";
			$arr_in["date_expire"] = 0;
			$arr_in["date_update"] = time();
			$ok = $ims->db->do_update("user", $arr_in, " user_id='".$user['user_id']."'");
			if($ok){
				Session::Delete('otp_pw_request');
				$output['ok'] = 1;		
				$output['link'] = $ims->site_func->get_link ('user', $ims->setting['user']['signin_link']);
				$output['mess'] = $ims->site_func->get_lang ('edit_success', $this->modules);
			}else{
				$output['mess'] = $ims->site_func->get_lang ('edit_false', $this->modules);
			}
		}
		
		return json_encode($output);
	}
	
	function do_forget_pass ()
	{
		global $ims;
		
		$output = array(
			'ok' => 0,
			'mess' => '',
			'link' => ''
		);
		
		if($ims->site_func->checkUserLogin() == 1) {
			$output['mess'] = $ims->lang['global']['signin_success'];
			return json_encode($output);
		}
		
		$input_tmp = $ims->post['data'];
		foreach($input_tmp as $key) {
			$input[$key['name']] = $key['value'];
		}
		/*print_arr($input);
		die('adasd');*/
		
		$arr_check = array('username');
		$arr_in = array();
		
		foreach($arr_check as $key) {
			if(empty($output['mess']) && (!isset($input[$key]) || empty($input[$key]))) {
				$output['mess'] = $ims->site_func->get_lang ('err_invalid', $this->modules, array(
                    '[name]' => $ims->site_func->get_lang ($key, $this->modules)
                ));
				break;
			}
			$arr_in[$key] = trim($input[$key]);
		}
		
		$sql = "select user_id, username, full_name, phone, email, date_expire, otp from user  
						where is_show=1 
						and (username='".$arr_in['username']."' or phone='".$arr_in['username']."' or email='".$arr_in['username']."')
						limit 0,1";
		// echo $sql;

		$result = $ims->db->query($sql);
		if ($user = $ims->db->fetch_row($result)) {
		} else {
			$output['mess'] = $ims->site_func->get_lang ('err_exit', $this->modules, array(
                    '[name]' => $ims->site_func->get_lang ('username', $this->modules)
                ));
			return json_encode($output);
		}
		
		unset($arr_in['username']);		
		if(empty($output['mess'])){
			if(!empty($user['email'])){
				$arr_in["user_code"] = $user['user_id'].'c'.$ims->func->random_str(10);
				$arr_in["pass_reset"] = $ims->func->random_str (10);
				$arr_in["date_update"] = time();
				$ok = $ims->db->do_update("user", $arr_in, " user_id='".$user['user_id']."'");	
				if($ok){
					$link_forget_pass = $ims->site_func->get_link ('user', $ims->setting['user']['forget_pass_link']).'?code='.$arr_in["user_code"];
					$arr_key = array('{full_name}','{domain}','{new_pass}','{link_forget_pass}');
					$arr_value = array($user['full_name'],$ims->conf['rooturl'],$arr_in["pass_reset"],$link_forget_pass);
					
					$ims->func->send_mail_temp ('forget-pass', $user['email'], $ims->conf['email'], $arr_key, $arr_value);
					$output['ok'] = 1;		
					$output['mess'] = $ims->lang['user']['forget_pass_send'];
				}else{
					$output['mess'] = $ims->site_func->get_lang ('err_exit', $this->modules, array(
	                    '[name]' => $ims->site_func->get_lang ('username', $this->modules)
	                ));
				}
			}else{				
				$ims->site_func->setting('user');				
                if(time() < $user['date_expire'] && !empty($user['otp']) && !empty($user['date_expire'])){
                	$array = array(
						'code' => 400,
		        		'message' => $ims->lang['user']['request_otp_success1'],
		        		'otp' => $check['otp'],
		        	);
					$this->response(400, $array);
                }
            	$otp = rand(1000, 9999);
		        $data_sms = array(
		            'ApiKey'    => $ims->setting['user']['esms_ApiKey'],
		            'SecretKey' => $ims->setting['user']['esms_SecretKey'],
		            'Brandname' => $ims->setting['user']['esms_Brandname'],
		            'Phone'     => $user['phone'],
		            'Content'   => str_replace('{otp}', $otp, $ims->setting['user']['esms_Contentpass']),
		            'SmsType'   => 2,
		            'Sandbox'   => 0,
		        );
		        
		        $data_sms = http_build_query ($data_sms);
		        $curl = curl_init();
		        $header = array("Content-Type:application/x-www-form-urlencoded");
		        curl_setopt_array($curl, array(
                    CURLOPT_RETURNTRANSFER  => 1,
                    CURLOPT_URL             => 'http://rest.esms.vn/MainService.svc/json/SendMultipleMessage_V4_post/',
                    CURLOPT_POST            => 1,
                    CURLOPT_HTTPHEADER      => $header,
                    CURLOPT_SSL_VERIFYPEER  => 0,
                    CURLOPT_POSTFIELDS      => $data_sms
                ));
                $resp = curl_exec($curl);
        		curl_close($curl);
		        $SMS = $resp;			        
		        if (!empty($SMS)) {
		            $SMS = json_decode($SMS);	            
		            if (isset($SMS->CodeResult) && $SMS->CodeResult==100) {
		                $col 				= array();
		                $col['date_expire'] = time()+60*5;
		                $col['otp']		    = $otp;
		                $ok = $ims->db->do_update('user', $col, " user_id='".$user['user_id']."' ");
		                if($ok){
		                	Session::Set ('otp_pw_request', $user['phone']);
		                	$output['ok'] = 1;		                	
							$output['mess'] = $ims->lang['user']['forget_pass_send'];
							$output['link'] = $ims->site_func->get_link('user',$ims->setting['user']['change_pass_otp_link']);
		                }else{	            
		                	$output['mess'] = $ims->lang['user']['forget_pass_otp_false'];    	
		                }
		            }
		        }else{
					$output['mess'] = $ims->lang['user']['forget_pass_otp_false1'];
		        }
			}
		}
		
		return json_encode($output);
	}

	function get_maxid_advisory(){
		global $ims;
		$output= '';
		$sql ="SELECT item_id FROM advisory ORDER BY item_id DESC LIMIT 0,1";
		$query = $ims->db->query($sql);
		$arr = $ims->db->fetch_row($query);
		$output = isset($arr['item_id']) ? $arr['item_id'] : '';
		return $output;
	}	

	function do_check_favorite(){
		global $ims;
        $output = array(
            'ok' => 0,
            'is_favorite' => 0,
            'mess' => ''
        );
        $id   = isset($ims->post['id']) ? $ims->func->base64_decode($ims->post['id']) : '';
        $path = isset($ims->post['path']) ? $ims->post['path'] : '';
        $mod  = isset($ims->post['mod']) ? $ims->post['mod'] : 'product';
        // if(isset($id) && $id != ''){
        //     $id = substr($id, 25);
        // }

        if ($ims->site_func->checkUserLogin() != 1) {        	
        	$url = $ims->func->base64_encode($path);
            $url = (!empty($url)) ? '/?url='.$url : '';
            $link_singin = $ims->site_func->get_link ('product', $ims->setting['user']['signin_link']).$url;
            $output['mess'] = $ims->lang['user']['not_signin_favorite'].' <br/><a style="color: #0056b3;" href="'.$link_singin.'"><b>'.$ims->lang['user']['signin'].'</b></a>';
            return json_encode($output);
        }
        $info_user = $ims->db->load_row('user',' user_id = "'.$ims->data['user_cur']['user_id'].'" ','list_favorite');
        $key_unset = -1;
        if(isset($info_user) && !empty($info_user) && is_array($info_user)){
            $col = array();
            $arr_favorite = $ims->func->unserialize($info_user['list_favorite']);
            if(empty($arr_favorite)){
                $arr_favorite[0]['mod'] = $mod;
                $arr_favorite[0]['id'] = $id;
                $col['list_favorite'] = $ims->func->serialize($arr_favorite);
            }else{
                $arr_search = array();
                foreach ($arr_favorite as $key => $object){
                    if(array_search($mod, $object)){
                        if(array_search($id, $object)){
                            $arr_search = $arr_favorite[$key];
                            $key_unset = $key;
                        }
                    }
                }
                if(empty($arr_search)){
                    $count_default = count($arr_favorite);
                    if($count_default >= $ims->setting['user']['max_favorite']){
                        $output['mess'] = str_replace('[num]', '<b>('.$ims->setting['user']['max_favorite'].')</b>', $ims->lang['global']['error_max_favorite']);
                    }else{
                        $count = max(array_keys($arr_favorite)) + 1;
                        $arr_favorite[$count]['mod'] = $mod;
                        $arr_favorite[$count]['id'] = $id;

                        $col['list_favorite'] = $ims->func->serialize($arr_favorite);
                    }
                }else{
                    unset($arr_favorite[$key_unset]);
                    $col['list_favorite'] = $ims->func->serialize($arr_favorite);
                }
            }
            $ok = $ims->db->do_update('user',$col , "user_id = '".$ims->data['user_cur']['user_id']."'");
            if($ok){
                $output['ok'] = 1;
                $output['is_favorite'] = 1;
                $output['mess'] = $ims->lang['global']['favorite_success'];
                if($key_unset != -1){
                    $output['is_favorite'] = 0;
                    $output['mess'] = $ims->lang['global']['favorite_success_remove'];
                }
            }
        }
        return json_encode($output);
	}

	function do_withdrawWcoin(){
		global $ims;
		$output = array(
            'ok' => 0,
            'mess' => ''
        );
		$input = $ims->func->unserialize_array ($ims->func->if_isset($ims->post['data'], array()));	
        if ($ims->site_func->checkUserLogin() != 1) {
            return json_encode($output);
        }
        if ($input['num_wcoin']  < 0 || $input['num_wcoin'] > $ims->data['user_cur']['wcoin']) {
        	$output['mess'] = $ims->lang['user']['err_wcoin'];
            return json_encode($output);
        }
        $arr_ins = array();
        $arr_ins['num_wcoin']   = $input['num_wcoin'];
        $arr_ins['bankcode']    = $input['bankcode'];
        $arr_ins['bankname']    = $input['bankname'];
        $arr_ins['bankbranch']  = $input['bankbranch'];
        $arr_ins['full_name']   = $input['full_name'];
        $arr_ins['user_id'] 	= $ims->data['user_cur']['user_id'];
        $arr_ins['is_show'] 	= 1;
        $arr_ins['date_create'] = time();
        $arr_ins['date_update'] = time();
        $ok = $ims->db->do_insert("user_withdrawals", $arr_ins);
        if($ok){
        	$output['ok'] = 1;
        	$output['mess'] = $ims->lang['user']['success_wcoin'];
        }
        return json_encode($output);
	}

	function do_swap_commission(){
		global $ims;
		$output = array(
            'ok' => 0,
            'mess' => ''
        );
		$input = $ims->func->unserialize_array ($ims->func->if_isset($ims->post['data'], array()));
        if ($ims->site_func->checkUserLogin() != 1) {
            return json_encode($output);
        }

        if ($input['num_commission']  < 1000) {
        	$output['mess'] = $ims->lang['user']['min_commission'];
            return json_encode($output);
        }elseif($input['num_commission'] > $ims->data['user_cur']['commission']){
            $output['mess'] = $ims->lang['user']['not_enough_commission'];
            return json_encode($output);
        }else{
            $arr_ins['exchange_type'] = 'swap_commission';
            $arr_ins['value_type'] = 1;
            $arr_ins['value'] = $input['num_commission']/$ims->setting['product']['money_to_wcoin'];
            $arr_ins['total_amount'] = $input['num_commission'];
            $arr_ins['wcoin_before'] = $ims->data['user_cur']['wcoin'];
            $arr_ins['wcoin_after'] = $arr_ins['wcoin_before'] + $arr_ins['value'];
            $arr_ins['commission_before'] = $ims->data['user_cur']['commission'];
            $arr_ins['commission_after'] = $ims->data['user_cur']['commission'] - $input['num_commission'];
            $arr_ins['note'] = $ims->lang['user']['note_swap_commission_log'];
            $arr_ins['user_code'] = isset($ims->data['user_cur']['user_code']) ? $ims->data['user_cur']['user_code'] : '';
            $arr_ins['user_id'] 	= $ims->data['user_cur']['user_id'];
            $arr_ins['is_show'] 	= 1;
            $arr_ins['date_create'] = time();
            $ok = $ims->db->do_insert("user_exchange_log", $arr_ins);
            if($ok){
                $update_user = array(
                    'wcoin' => $arr_ins['wcoin_after'],
                    'wcoin_total' => $arr_ins['wcoin_before'],
                    'commission' => $ims->data['user_cur']['commission'] - $input['num_commission']
                );
                $ims->db->do_update("user", $update_user, ' user_id = '.$ims->data['user_cur']['user_id']);
                $output['ok'] = 1;
                $output['mess'] = $ims->lang['user']['success_swap'];
            }
            return json_encode($output);
        }
	}

	function do_save_link(){
		global $ims;		
		$output = array(
			'ok' => 0,
			'mess' => '',
			'link_shorten' => ''
		);
		$input = isset($ims->post['data'])?$this->clean($ims->post['data']):'';
		if(trim($input) == '' ) {
			$output['mess'] = $ims->lang['user']['empty_shorten'];			
		}else{
			$arr_check = array();	
			$arr_friendly_link = $ims->db->load_item_arr('friendly_link','lang="'.$ims->conf['lang_cur'].'"','friendly_link');
			if($arr_friendly_link){
				foreach ($arr_friendly_link as $key) {
					if(!empty($key['friendly_link'])) array_push($arr_check,$key['friendly_link']);
				}
			}
			$arr_shorten = $ims->db->load_item_arr('user','is_show=1','link_shorten');
			if($arr_shorten){
				foreach ($arr_shorten as $key){
					if(!empty($key['link_shorten'])) array_push($arr_check,$key['link_shorten']);
				}
			}			
			if(count($arr_check)>0){
				if(in_array($input,$arr_check)){
					$output['mess'] = $ims->lang['user']['exist_shorten'];
				}else{
					$arr_in["link_shorten"] = trim($input);
					$arr_in["date_update"] = time();
					$ok = $ims->db->do_update("user", $arr_in, " user_id='".$ims->data['user_cur']['user_id']."'");	
					if($ok){
						$output['link_shorten'] = $ims->conf['rooturl'].$arr_in["link_shorten"];
						$output['ok'] = 1;
						$output['mess'] = $ims->lang['user']['save_shorten_success'];
					}
				}
			}
		}		
		return json_encode($output);
	}

	function clean($string) {
	   $string = str_replace(' ', '', $string);
	   return preg_replace('/[^A-Za-z0-9\-]/', '', $string);
	}

	function do_send_inv(){
		global $ims;
		$output['ok'] = 0;
		$output['mess'] = $ims->site_func->get_lang ('mess_send_false', 'user');;
		$input = $ims->func->unserialize_array ($ims->func->if_isset($ims->post['data'], array()));	

        $content = isset($input['content']) ? $input['content'] : '';
		$content_more = isset($input['content_more']) ? $input['content_more'] : '';
		$name = isset($input['name']) ? $input['name'] : '';
		if(empty($content)){
			return json_encode($output);
		}
		if (strpos($content, ',') !== false) {
		    $content = explode(',', $content);
		}			
		$mail_arr_key = array(
			'{link_buy}',
			'{domain}',
            '{content_more}',
		);
		$mail_arr_value = array(
			'<a href="'.$name.'">'.$name.'</a>',
			$_SERVER['HTTP_HOST'],
            $content_more,
		);
		if(!empty($content)){			
			if(is_array($content)){
				foreach ($content as $key => $value) {				
				    $ok = $ims->func->send_mail_temp ('template-send-invitation', $value, $ims->conf['email'], $mail_arr_key, $mail_arr_value);			    
				}
			}else{
				$ok = $ims->func->send_mail_temp ('template-send-invitation', $content, $ims->conf['email'], $mail_arr_key, $mail_arr_value);
			}
			if($ok){
				$output['ok'] = 1;
				$output['mess'] = $ims->site_func->get_lang ('mess_send_success', 'user');
			}
		}
		return json_encode($output);
	}	

	function do_add_deeplink(){
        global $ims;
        $output = array(
            'ok' => 0,
            'mess' => '',
        );

        if($ims->site_func->checkUserLogin() != 1) {
			$output['mess'] = $ims->lang['global']['signin_false'];
			return json_encode($output);
		}
        $input = $ims->func->unserialize_array ($ims->func->if_isset($ims->post['data'], array()));

        //product_group
        $request_uri = $input["link_source"];
	
		
        $friendy_link = ($ims->conf["rooturl"] != '/') ? str_replace($ims->conf["rooturl"],"",$request_uri) : substr($request_uri,1);
        $friendy_link = str_replace('/',"",$friendy_link);
        $friendy_link = str_replace('.html',"",$friendy_link);
        $check = $ims->db->load_row('friendly_link',' friendly_link = "'.$friendy_link.'" and lang ="'.$ims->conf["lang_cur"].'" ','module,action,dbtable_id');
        //print_arr($check);
        if ($check){
            if ($check['module'] =='product' && ($check['action'] =='group' || $check['action'] =='detail')){
                $arr_in['link_source'] = $friendy_link;
                $arr_in['user_id'] = $ims->data['user_cur']['user_id'];
                $arr_in['is_show'] = 1;
                $arr_in['type'] = $check['action'];
                $arr_in['item_id'] = $check['dbtable_id'];
                $arr_in['date_create'] = time();
                $arr_in['date_update'] = time();
                $arr_in['short_code'] = 'c'.$ims->func->random_str(1).$ims->db->getAutoIncrement ('deeplink').$ims->func->random_str(8);
               // print_arr($arr_in);die;
                $ok =  $ims->db->do_insert('user_deeplink',$arr_in);
                if ($ok){
                    $output['ok'] =1;

                }else{
                    $output['mess'] = $ims->lang['user']['deeplink_error1'];
                }
            }else{
                //echo 'adasdas';die;
                $output['mess'] = $ims->lang['user']['deeplink_error'];
            }

        }else{
            $output['mess'] = $ims->lang['user']['deeplink_error'];
        }
        return json_encode($output);
    }

	function do_delete_deeplink(){
        global $ims;
        $output['ok'] = 0;
        $output['mess'] = 'Đã xóa';
        $id_pro = $ims->input['item_id'];
        if($ims->site_func->checkUserLogin() != 1) {
            $output['mess'] = $ims->lang['global']['please_signup'];
            return json_encode($output);
        }else{
            $post['is_show'] = 0;
            $post['date_update'] = time();
            $ok = $ims->db->do_update('deeplink',$post,'id = '.$id_pro.'');
            if($ok){
                $output['ok'] = 1;
            }
        }

        return json_encode($output);
    }

	function do_load_form_address(){
		global $ims;

		$output = '';
		$data = array();
		$row = array(			
			'full_name' => '',
			'email' => '',
			'phone' => '',
			'province' => '',
			'district' => '',
			'ward' => '',
			'address' => '',
			'is_default' => '',
		);

		if($ims->site_func->checkUserLogin() != 1) {
			$output['mess'] = $ims->lang['global']['signin_false'];
			return json_encode($output);
		}
		$id_address = isset($ims->post['data'])?$ims->post['data']:'';
		$arr_address = $ims->func->unserialize($ims->data['user_cur']['arr_address_book']);
		
		foreach ($arr_address as $address) {
			if($id_address!='' && $id_address==$address['id']){
				$row['id'] = $id_address;
				$row['full_name'] = $address['full_name'];
				$row['email'] = $address['email'];
				$row['phone'] = $address['phone'];
				$row['province'] = $address['province'];
				$row['district'] = $address['district'];
				$row['ward'] = $address['ward'];
				$row['address'] = $address['address'];
				if($address['is_default'] == 1){
					$row['is_default'] = 'checked';
				}
			}
		}

		$cur_ward = $row['ward'];
		$cur_district = $row['district'];
		$cur_province = $row['province'];


		$data["list_location_province_d"] = $ims->site_func->selectLocation (
        	"d_province",
        	"vi", 
        	$cur_province,
        	" class='form-control select_location_province_d' data-district='d_district' data-ward='d_ward' id='d_province' ", 
        	array('title' => $ims->lang["user"]["select_title"]),
        	"province"
        );
		$data["list_location_district_d"] = $ims->site_func->selectLocation (
			"d_district", 
			$cur_province, 
			$cur_district,
			" class='form-control select_location_district_d' data-ward='d_ward' id='d_district' ", 
        	array('title' => $ims->lang["user"]["select_title"]),
			"district"
		);
		$data["list_location_ward_d"] = $ims->site_func->selectLocation (
			"d_ward",
			$cur_district, 
			$cur_ward,
			" class='form-control' id='d_ward' ", 
        	array('title' => $ims->lang["user"]["select_title"]),
			"ward"
		);

        $output .= '<form class="user-form" id="form_add_address" name="form_add_address" method="post" action="" >
        	<div class="form-group">
                <label class="title">'.$ims->lang['user']['full_name'].'<span class="required">*</span></label>
                <input placeholder="'.$ims->lang['user']['text_full_name'].'" name="d_full_name" type="text" maxlength="100" class="form-control" value="'.$row['full_name'].'" required/>
            </div>
            <div class="form-group">
                <label class="title">'.$ims->lang['user']['email'].' <span class="required">*</span></label>
                <input placeholder="'.$ims->lang['user']['text_email'].'" name="d_email" type="text" maxlength="100" class="form-control" value="'.$row['email'].'" required/>
            </div>
            <div class="form-group">
                <label class="title">'.$ims->lang['user']['phone'].' <span class="required">*</span></label>
                <input placeholder="'.$ims->lang['user']['text_phone'].'" name="d_phone" type="text" maxlength="100" class="form-control" value="'.$row['phone'].'" required/>
            </div>
            <div class="form-group">
                <label class="title">'.$ims->lang['user']['province'].' <span class="required">*</span></label>
                <div class="form-content">'.$data["list_location_province_d"].'</div>
            </div>
            <div class="form-group">
                <label class="title">'.$ims->lang['user']['district'].'</label>
                <div class="form-content">'.$data["list_location_district_d"].'</div>
            </div>
            <div class="form-group">
                <label class="title">'.$ims->lang['user']['ward'].'</label>
                <div class="form-content">'.$data["list_location_ward_d"].'</div>
            </div>
            <div class="form-group">
                <label class="title">'.$ims->lang['user']['address'].' <span class="required">*</span></label>
                <input placeholder="'.$ims->lang['user']['text_address'].'" name="d_address" type="text" maxlength="100" class="form-control" value="'.$row['address'].'" required/>
            </div>';
	        if($id_address){
	        	if($arr_address[$id_address]['is_default'] != 1){
	            	$output .= '<div class="form-group"><label class="title"></label><div class="row_checkbox">
			            <input type="checkbox" id="default_address" name="is_default" value="1" '.$row['is_default'].'>
			            <label for="default_address">'.$ims->lang['user']['default_address'].'</label>
			        </div></div>';
		        }else{
		        	$output .= '<input type="hidden" name="is_default" value="1" >';
		        }
	            $output .= '
	            <div class="row_btn">
	             	<div class="form-group">
	                <label class="title"></label>
        			<div class="form-content">
		            	<input type="hidden" name="id" value="'.$row['id'].'" />
		                <input type="hidden" name="do_edit" value="1" />
		                <button type="submit" class="btn bg-color text-color btn_custom">'.$ims->lang['user']['confirm'].'</button>
	                </div>
	            </div></div>';
        	}else{
        		$output .= '<div class="row_checkbox">
		            <input type="checkbox" id="default_address" name="is_default" value="1" '.$row['is_default'].'>
		            <label for="default_address">'.$ims->lang['user']['default_address'].'</label>
		        </div>
        		<div class="row_btn">
        		<div class="form-group">
        		    <label class="title"></label>
        			<div class="form-content">
                        <input type="hidden" name="do_submit" value="1" />
                        <button type="submit" class="btn bg-color text-color btn_custom">'.$ims->lang['user']['confirm'].'</button>
                    </div>
	            </div></div>';
        	}
            $output .= '</form>';
		return json_encode($output);
	}

	function do_default_address(){
		global $ims;
		$output = array(
			'ok' => 0,
			'mess' => 'false',
		);
		if($ims->site_func->checkUserLogin() != 1) {
			$output['mess'] = $ims->lang['global']['signin_false'];
			return json_encode($output);
		}
		$id_address = isset($ims->post['id'])?$ims->post['id']:0;		
		if(!empty($id_address)){
			$arr_temp = array();
			$arr_address = $ims->func->unserialize($ims->data['user_cur']['arr_address_book']);			
			foreach ($arr_address as $row) {
				$row['is_default'] = 1;
				if($row['id'] != $id_address){
					$row['is_default'] = 0;
				}
				$arr_temp[$row['id']] = $row;
			}
			$arr_address = $arr_temp;
		}
		$arr_address = serialize($arr_address);
		$ok = $ims->db->do_update('user', array('arr_address_book'=>$arr_address), ' user_id="'.$ims->data['user_cur']['user_id'].'"');
		if($ok){
			$output["ok"] = 1;
			$output["mess"] = "success";
		}
		return json_encode($output);		
	}

	function do_delete_address(){
		global $ims;
		$output = '';
		$id_address = isset($ims->post['data'])?$ims->post['data']:'';
		// print_arr($ims->data);
		if($ims->site_func->checkUserLogin() != 1) {
			$output['mess'] = $ims->lang['global']['signin_false'];
			return json_encode($output);
		}
		$arr_address = $ims->func->unserialize($ims->data['user_cur']['arr_address_book']);
		foreach ($arr_address as $address) {
			if($id_address!='' && $id_address==$address['id']){
				unset($arr_address[$id_address]);
			}
		}
		$arr_address = $ims->func->serialize($arr_address);
		$ok = $ims->db->do_update('user', array('arr_address_book'=>$arr_address), ' user_id="'.$ims->data['user_cur']['user_id'].'"');
		if($ok){
			$ims->func->include_js_content('window.history.replaceState( null, null, window.location.href);$( "#form_ordering_address" ).load(window.location.href + " #form_ordering_address" );');
		}
		return json_encode($arr_address);
	}

    function do_cartSaveLater (){
        global $ims;

        $output = array(
            'ok' => 0,
            'mess' => '',
            'empty_cart' => 0,
            'saved' => 0,
            'delete_promotion' => 0
        );
        $ok = 0;
        $arr_cart 	= Session::Get('cart_pro');
        $cart_item 	= $ims->func->if_isset($ims->post['cart_item']);
        $id 		= $ims->func->if_isset($ims->post['id']);
        $path 		= $ims->func->if_isset($ims->post['path']);
        $col 		= array();

        if($ims->site_func->checkUserLogin() == 1) {
            $arr_cart = $ims->load_data->data_table("product_order_temp", 'id', '*', ' user_id="'.$ims->data['user_cur']['user_id'].'" ');
        }
        if($ims->site_func->checkUserLogin() != 1){
            $url = $ims->func->base64_encode($path);
            $url = (!empty($url)) ? '/?url='.$url : '';
            $link_singin = $ims->site_func->get_link ('product', $ims->setting['user']['signin_link']).$url;
            $output['mess'] = $ims->lang['global']['save_later_false1'].' <br/><b class="custom"><a id="popup_ok_1" href="'.$link_singin.'" class="fancybox_pd_0">'.$ims->lang['global']['login_now'].'</a></b>';
            $output['link'] = $link_singin;
        }

        $info_user = $ims->data['user_cur'];
        if(empty($cart_item) || empty($id) || !isset($arr_cart[$cart_item])){
            $output['mess'] = $ims->lang['global']['save_later_false0'];
        }
        if($output['mess'] == ''){
            if($info_user['list_save'] == ''){
                $arr_save[0]['id'] 	    = $id;
                $arr_save[0]['item_id'] = $arr_cart[$cart_item]['item_id'];
                $col['list_save']       = $ims->func->serialize($arr_save);
            }else{
                $arr_save = $ims->func->unserialize($info_user['list_save']);
                $arr_search = array();
                foreach ($arr_save as $key => $value){
                    $output['value'] = $value;
                    if(array_search($id, $value)){
                        $arr_search = $arr_save[$key];
                    }
                }
                if(empty($arr_search)){
                    $count = max(array_keys($arr_save)) + 1;
                    $arr_save[$count]['id'] = $id;
                    $arr_save[$count]['item_id'] = $arr_cart[$cart_item]['item_id'];
                    $col['list_save'] = serialize($arr_save);
                }else{
                    $output['mess'] = $ims->lang['global']['save_later_false2'];
                    $output['saved'] = 1;
                }
            }
            $ok = $ims->db->do_update('user', $col , "user_id='".$info_user['user_id']."'");
            if($ok){
                $ims->db->query('DELETE FROM `product_order_temp` WHERE id="'.$cart_item.'" AND user_id="'.$info_user['user_id'].'" ');
                unset($arr_cart[$cart_item]);
                Session::Set ('cart_pro', $arr_cart);
                $output['ok'] = 1;
                $output['mess'] = $ims->lang['global']['save_later_success'];
                $check = $ims->db->load_item('product_order_temp', ' user_id = '.$info_user['user_id'], 'id');
                if(!$check){
                    $output['empty_cart'] = 1;
                    Session::Set ('promotion_code', ''); // Xóa luôn mã khuyến mãi
                }else{
                    // Cập nhật lại mã khuyến mãi
                    require_once ($ims->conf["rootpath"]."modules/product/controllers/ordering_func.php");
                    $this->orderiFunc = new OrderingFunc($this);
                    $code = Session::Get('promotion_code', '');

                    if($code){
                        $promotion_info = $this->orderiFunc->promotion_info(0, $code);
                        if($promotion_info['price'] == 0 && $promotion_info['mess'] != $ims->lang['product']['freeship']){
                            $output['delete_promotion'] = 1;
                            $output['promotion_mess'] = $ims->html->html_alert ($promotion_info['mess'], "warning");
                            $output['promotion_price_out'] = '-'.$ims->func->get_price_format($promotion_info['price'], 0);
                        }
                    }
                }
            }
        }
        return json_encode($output);
    }

    function do_removeSaveLater(){
        global $ims;

        $output = array(
            'mess' => '',
            'ok' => 0,
        );

        if($ims->site_func->checkUserLogin() != 1) {
            $output['mess'] = $ims->lang['global']['signin_false'];
            return json_encode($output);
        }
        $id = $ims->func->if_isset($ims->post['id'], 0);
        if($id!='' && $ims->data['user_cur']['list_save']!=''){
            $arr_save = $ims->func->unserialize($ims->data['user_cur']['list_save']);
            $col = array();
            foreach ($arr_save as $key => $save) {
                if($id==$save['id']){
                    unset($arr_save[$key]);
                }
            }
            $arr_save = $ims->func->serialize($arr_save);
            $ok = $ims->db->do_update('user', array('list_save'=>$arr_save), "user_id = '".$ims->data['user_cur']['user_id']."'");
            if ($ok) {
                $output['ok'] = 1;
            }
        }
        return json_encode($output);
    }

	function do_update_api_status(){
		global $ims;
		$output = array(
			'type' => '',
			'mess' => '',
			'ok' => 0,
		);
		$id = $ims->func->if_isset($ims->post['id']);
		$status = $ims->func->if_isset($ims->post['status'],0);
		if($id!=''){
			$info = $ims->db->load_item('api_department','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and name_action="'.$id.'"','arr_connect');
			if(!empty($info)){
				$output['test'] = $info;
				$info = unserialize($info);
				$check = $ims->site_func->api_sendo($info);
				if($check!=''){
					$ok = $ims->db->do_update('api_department', array('is_connect'=>$status), "name_action = '".$id."'");
		            if($ok){
		            	$output['type'] = 'success';
		            	$output['ok'] = 1;
		            	if($status==1){
		            		$output['mess'] = "Kết nối gian hàng thành công!";
		            	}else{
		            		$output['mess'] = "Ngắt kết nối gian hàng thành công!";
		            	}
		            }else{
		            	$output['ok'] = 0;
		            	$output['type'] = 'error';
		            	$output['mess'] = "Có lỗi xảy ra! Vui lòng liên hệ quản trị viên";
		            }
	            }else{
	            	$output['ok'] = 3;
	            	$output['type'] = 'error';
	            	$output['mess'] = "Sai thông tin! Kết nối thất bại";
	            }
            }else{            	
            	$output['ok'] = 2;
            }
		}
		return json_encode($output);
	}

	function do_save_config_sendo(){
		global $ims;
		$output = array(
			'type' => '',
			'mess' => '',
			'ok' => 0,
		);
		$id = $ims->func->if_isset($ims->post['id']);
		$status = $ims->func->if_isset($ims->post['status'],0);
		$input_tmp = $ims->post['data'];
		foreach($input_tmp as $key) { //shopname - username - client key
			$input[$key['name']] = $key['value'];
		}
		$arr_connect = serialize($input);
		$check = $ims->site_func->api_sendo($input);
		if($check!=''){
			$ok = $ims->db->do_update('api_department', array('arr_connect'=>$arr_connect,'is_connect'=>$status), 'lang="'.$ims->conf['lang_cur'].'" and is_show=1 and name_action="'.$id.'"');
			if($ok){
				$output['ok'] = 1;
				$output['type'] = 'success';
				$output['mess'] = "Lưu thông tin thành công!";
			}
		}else{
			$ok = $ims->db->do_update('api_department', array('is_connect'=>0), 'lang="'.$ims->conf['lang_cur'].'" and is_show=1 and name_action="'.$id.'"');
			if($ok){
				$output['ok'] = 0;
				$output['type'] = 'error';
				$output['mess'] = "Client key không hợp lệ! Vui lòng kiểm tra lại!";
			}
		}
		return json_encode($output);
	}
	
  	// End class
    function do_form_store(){
	    global $ims;
        $dir_view = $ims->func->dirModules('user', 'views', 'path');
        $ims->temp_act = new XTemplate($dir_view ."store.tpl");
        $ims->temp_act->assign('LANG', $ims->lang);

	    $out = array(
	        'ok' => 0,
	        'html' => ''
        );
	    $lang_cur = $ims->post['lang_cur'];
	    $store_item = (isset($ims->post['item'])) ? $ims->func->base64_decode($ims->post['item']) : 0;
	    if($store_item){
	        $data = $ims->db->load_row('event_store', 'is_show = 1 and lang = "'.$lang_cur.'" and item_id = '.$store_item);
	        if($data){
	            $data['src_ori'] = $data['picture'];
	            $data['src'] = $ims->func->get_src_mod($data['picture'], 100, 100, 1, 1);
	            $data['item'] = '<input type="hidden" name="item" value="'.$ims->func->base64_encode($data['item_id']).'">';
	            $data['title'] = htmlspecialchars($data['title']);
                $ims->temp_act->assign('data', $data);
                $ims->temp_act->parse('add_edit_store.picture');
            }
        }

        $ims->temp_act->parse('add_edit_store');
        $out['ok'] = 1;
	    $out['html'] = $ims->temp_act->text('add_edit_store');

	    return json_encode($out);
    }
    function do_add_edit_store(){
	    global $ims;
        $out = array(
            'ok' => 0,
            'mess' => $ims->lang['user']['action_false']
        );

        if($ims->site_func->checkUserLogin() == 1) {
            $store_item = isset($ims->post['item']) ? $ims->func->base64_decode($ims->post['item']) : 0;
            $lang_cur = $ims->post['lang_cur'];
            $picture_available = isset($ims->post['picture_available']) ? $ims->post['picture_available'] : '';

            $arr_in = array();
            $arr_in['title'] = $ims->post['title'];
            if(isset($_FILES['picture']) && $_FILES['picture']['error'] == 0 && $picture_available == ''){
                $folder_upload = "user/".$ims->data['user_cur']['folder_upload'].'/'.date('Y',time()).'_'.date('m',time()).'/store';
                $pic_result = $ims->site_func->upload_image($folder_upload, 'picture');
                if($pic_result['ok'] == 1){
                    if(!$store_item){
                        $arr_in['item_id'] = $ims->db->getAutoIncrement('event_store');
                        $arr_in['picture'] = $pic_result['url_picture'];
                        $arr_in['show_order'] = 0;
                        $arr_in['is_show'] = 1;
                        $arr_in['user_id'] = $ims->data['user_cur']['user_id'];
                        $arr_in['date_create'] = time();
                        $arr_in['date_update'] = time();
                        $list_lang = $ims->db->load_item_arr('lang', 'is_show = 1 order by is_default desc, show_order desc', 'name');
                        foreach ($list_lang as $lang){
                            $arr_in['lang'] = $lang['name'];
                            $ok = $ims->db->do_insert("event_store", $arr_in);
                        }
                        if($ok){
                            $out['ok'] = 1;
                            $out['mess'] = $ims->lang['user']['add_store_success'];
                        }
                    }else{
                        $arr_in['picture'] = $pic_result['url_picture'];
                        $arr_in['date_update'] = time();
                        $ok = $ims->db->do_update("event_store", $arr_in, 'item_id = '.$store_item.' and lang = "'.$lang_cur.'" and user_id = '.$ims->data['user_cur']['user_id']);
                        if($ok){
                            $out['ok'] = 1;
                            $out['mess'] = $ims->lang['user']['edit_store_success'];
                        }
                    }
                } else {
                    $out['mess']  = $pic_result['mess'];
                }
            }elseif ($picture_available && $store_item){
                $arr_in['picture'] = $picture_available;
                $arr_in['date_update'] = time();
                $ok = $ims->db->do_update("event_store", $arr_in, 'item_id = '.$store_item.' and lang = "'.$lang_cur.'" and user_id = '.$ims->data['user_cur']['user_id']);
                if($ok){
                    $out['ok'] = 1;
                    $out['mess'] = $ims->lang['user']['edit_store_success'];
                }
            }else{
                $out['mess']  = $ims->lang['user']['upload_picture_require'];
            }
        }
        return json_encode($out);
    }

    function do_delete_store(){
        global $ims;
        $out = array(
            'ok' => '0',
            'mess' => $ims->lang['user']['action_false']
        );

        if ($ims->site_func->checkUserLogin() == 1) {
            $item_id = isset($ims->post['item']) ? $ims->func->base64_decode($ims->post['item']) : 0;
            $check = $ims->db->load_item('event_store', 'is_show = 1 and item_id = '.$item_id.' and user_id = '.$ims->data['user_cur']['user_id'], 'item_id');
            if($check){
                $ok = $ims->db->do_update("event_store", array('is_show' => 0, 'date_update' => time()), 'item_id = '.$item_id.' and user_id = '.$ims->data['user_cur']['user_id']);
                if($ok){
                    $out['ok'] = 1;
                    $out['mess'] = $ims->lang['user']['delete_store_success'];
                }
            }
        }
        return json_encode($out);
    }

    function do_restore_store(){
        global $ims;
        $out = array(
            'ok' => '0',
            'mess' => $ims->lang['user']['action_false']
        );

        if ($ims->site_func->checkUserLogin() == 1) {
            $item_id = isset($ims->post['item']) ? $ims->func->base64_decode($ims->post['item']) : 0;
            $lang_cur = isset($ims->post['lang_cur']) ? $ims->post['lang_cur'] : '';
            $check = $ims->db->load_item('event_store', 'is_show = 0 and item_id = '.$item_id.' and user_id = '.$ims->data['user_cur']['user_id'], 'item_id');
            if($check){
                $ok = $ims->db->do_update("event_store", array('is_show' => 1, 'date_update' => time()), 'item_id = '.$item_id.' and user_id = '.$ims->data['user_cur']['user_id']);
                if($ok){
                    $out['ok'] = 1;
                    $out['mess'] = $ims->lang['user']['restore_store_success'];
                }
            }
        }
        return json_encode($out);
    }

    function do_load_event_to_store(){
        global $ims;
        $dir_view = $ims->func->dirModules('user', 'views', 'path');
        $ims->temp_act = new XTemplate($dir_view ."store.tpl");
        $ims->temp_act->assign('LANG', $ims->lang);
        $ims->temp_act->assign('CONF', $ims->conf);

        $out = array(
            'ok' => 0,
            'html' => '',
            'mess' => ''
        );

        $lang_cur = $ims->post['lang_cur'];
        $sort = $ims->post['sort'];
        $keyword = $ims->post['keyword'];

        if ($ims->site_func->checkUserLogin() == 1) {
            $out['ok'] = 1;
            $where = '';

            if($keyword){
                $arr_key = explode(' ', $keyword);
                $arr_tmp = array();
                foreach ($arr_key as $value) {
                    $value = trim($value);
                    if (!empty($value)) {
                        $arr_tmp['title'][] = "title LIKE '%".$value."%'";
                        $arr_tmp['title1'][] = "title1 LIKE '%".$value."%'";
                    }
                }
                if (count($arr_tmp) > 0) {
                    foreach ($arr_tmp as $k => $v) {
                        if (count($v) > 0) {
                            $arr_tmp[$k] = "(" . implode(" AND ", $v) . ")";
                        } else {
                            unset($arr_tmp[$k]);
                        }
                    }
                }
                if (count($arr_tmp) > 0) {
                    $where .= " AND (".implode(" OR ", $arr_tmp).")";
                }
            }
            if($sort != ''){
                if ($sort == 0){
                    $where .= ' AND date_begin > '.time(); //Sắp diễn ra
                }elseif ($sort == 1){
                    $where .= ' AND date_begin <= '.time().' AND date_end >= '.time(); //Đang diễn ra
                }elseif ($sort == 2){
                    $where .= ' AND date_end < '.time(); //Đã kết thúc
                }
            }

            $list_event = $ims->db->load_item_arr('event', 'is_show = 1 and lang = "'.$lang_cur.'"'.$where.' and user_id = '.$ims->data['user_cur']['user_id'].' order by title1 asc, title asc', 'title1, title, picture, address, organizer, date_begin, date_end, item_id');
            if($list_event){
                $i = 0;
                foreach ($list_event as $row){
                    $title1 = ($row['title1'] != '') ? $row['title1'].':&nbsp' : '';
                    $row['title'] = $title1.$row['title'];
                    $row['picture'] = $ims->func->get_src_mod($row['picture'], 100, 100, 1, 1);
                    $row['address'] = ($row['address'] != '') ? '<p>'.$row['address'].'</p>' : '';
                    $row['checkbox'] = '<input type="checkbox" name="list_event[]" value="'.$row['item_id'].'" id="check'.$i.'"><label for="check'.$i.'"></label>';
                    if($row['date_begin'] > time()){
                        $row['event_status'] = '<span style="color: #04457A">'.$ims->lang['user']['event_upcoming'].'</span>';
                    }elseif ($row['date_begin'] <= time() && $row['date_end'] >= time()){
                        $row['event_status'] = '<span style="color: #25AB0C;">'.$ims->lang['user']['event_ongoing'].'</span>';
                    }elseif ($row['date_end'] < time()){
                        $row['event_status'] = '<span style="color: #D80F00;">'.$ims->lang['user']['event_over'].'</span>';
                        $row['checkbox'] = '<span class="over"></span>';
                    }
                    $row['date_begin'] = '<p>'.$ims->lang['global']['day_'.date('N', $row['date_begin'])].', '.date('d/m h:i A', $row['date_begin']).'</p>';
                    $i++;
                    $ims->temp_act->assign('row', $row);
                    $ims->temp_act->reset('form_add_to_event.item');
                    $ims->temp_act->parse('form_add_to_event.item');
                    $out['html'] .= $ims->temp_act->text('form_add_to_event.item');
                }
            }else{
                $ims->temp_act->parse('form_add_to_event.empty');
                $out['html'] = $ims->temp_act->text('form_add_to_event.empty');
            }
        }else{
            $out['mess'] = $ims->lang['user']['action_false'];
        }

        return json_encode($out);
    }

    function do_add_store_to_event(){
        global $ims;
        $out = array(
            'ok' => 0,
            'mess' => ''
        );

        if ($ims->site_func->checkUserLogin() == 1) {
            $post_data = $ims->post['data'];
            $item_store = 0;
            foreach ($post_data as $item){
                if(substr($item['name'], 0, 10) == 'list_event'){
                    eval('$'.$item['name'].' = "'.$item['value'].'";');
                }
                if($item['name'] == 'it'){
                    $item_store = $ims->func->base64_decode($item['value']);
                }
            }

            if(!empty($list_event)){
                $list_product = $ims->db->load_item_arr('event_product', 'is_show = 1 and find_in_set('.$item_store.', store_id) and user_id = '.$ims->data['user_cur']['user_id'], 'item_id, event_id');
                if(!$list_product){
                    $out['mess'] = $ims->lang['user']['store_not_have_product'];
                }else{
                    $event_owner = array();
                    $event_owner_tmp = $ims->db->load_item_arr('event', 'is_show = 1 and user_id = '.$ims->data['user_cur']['user_id'], 'item_id');
                    if($event_owner_tmp){
                        foreach ($event_owner_tmp as $item){
                            $event_owner[] = $item['item_id'];
                        }
                    }
                    $list_event_valid = array();
                    foreach ($list_event as $item){
                        if(in_array($item, $event_owner)){
                            $list_event_valid[] = $item;
                        }
                    }
                    foreach ($list_product as $product){
                        $event_chosed = array();
                        if($product['event_id']){
                            $event_chosed = explode(',', $product['event_id']);
                        }
                        $event_update = array_unique(array_merge($event_chosed, $list_event_valid));
                        sort($event_update);
                        $ims->db->do_update('event_product', array('event_id' => implode(',', $event_update), 'date_update' => time()), ' item_id = '.$product['item_id'].' and user_id = '.$ims->data['user_cur']['user_id']);
                    }
                    $out['mess'] = $ims->lang['user']['add_store_to_event_success'];
                    $out['ok'] = 1;
                }
            }else{
                $out['mess'] = $ims->lang['user']['not_yet_chose_event'];
            }
        }else{
            $out['mess'] = $ims->lang['user']['action_false'];
        }

        return json_encode($out);
    }

    function do_load_product_to_store(){
	    global $ims;

        $dir_view = $ims->func->dirModules('user', 'views', 'path');
        $ims->temp_act = new XTemplate($dir_view ."store.tpl");
        $ims->temp_act->assign('LANG', $ims->lang);
        $ims->temp_act->assign('CONF', $ims->conf);

        $out = array(
            'ok' => 0,
            'html' => '',
            'mess' => ''
        );

        $store_id = $ims->func->base64_decode($ims->post['it']);
        $lang_cur = $ims->post['lang_cur'];
        $keyword = $ims->post['keyword'];

        if ($ims->site_func->checkUserLogin() == 1) {
            $out['ok'] = 1;
            $where = '';

            if($keyword){
                $arr_key = explode(' ', $keyword);
                $arr_tmp = array();
                foreach ($arr_key as $value) {
                    $value = trim($value);
                    if (!empty($value)) {
                        $arr_tmp['title'][] = "title LIKE '%".$value."%'";
                        $arr_tmp['title1'][] = "title1 LIKE '%".$value."%'";
                    }
                }
                if (count($arr_tmp) > 0) {
                    foreach ($arr_tmp as $k => $v) {
                        if (count($v) > 0) {
                            $arr_tmp[$k] = "(" . implode(" AND ", $v) . ")";
                        } else {
                            unset($arr_tmp[$k]);
                        }
                    }
                }
                if (count($arr_tmp) > 0) {
                    $where .= " AND (".implode(" OR ", $arr_tmp).")";
                }
            }

            $list_product_added = array();
            $store_product_cur = $ims->db->load_item_arr('event_product', 'is_show = 1 and find_in_set('.$store_id.', store_id)', 'item_id');
            if($store_product_cur){
                foreach ($store_product_cur as $item){
                    $list_product_added[] = $item['item_id']; // list sản phẩm đã thêm vào cửa hàng này
                }
            }

            $list_product = $ims->db->load_item_arr('event_product', 'is_show = 1 and lang = "'.$lang_cur.'"'.$where.' and user_id = '.$ims->data['user_cur']['user_id'].' order by title1 asc, title asc', 'title1, title, picture, item_id, num_item, num_sold, price, date_end');
            if($list_product){
                $i = 0;
                foreach ($list_product as $row){
                    $title1 = ($row['title1'] != '') ? $row['title1'].':&nbsp' : '';
                    $row['title'] = $title1.$row['title'];
                    $row['picture'] = $ims->func->get_src_mod($row['picture'], 100, 100, 1, 1);
                    $row['inventory'] = $ims->lang['user']['inventory'].'&nbsp'.($row['num_item'] - $row['num_sold']);
                    $row['price'] = number_format($row['price'],0,',','.');
                    $checked = '';
                    if($list_product_added && in_array($row['item_id'], $list_product_added)){
                        $checked = 'checked';
                    }
                    $row['checkbox'] = '<input type="checkbox" name="list_product['.$row['item_id'].'][\'item\']" value="'.$row['item_id'].'" id="check'.$i.'" '.$checked.'><label for="check'.$i.'"></label>';
                    $row['item_hidden'] = '<input type="hidden" name="list_product['.$row['item_id'].'][\'d\']" value="1">';
                    $i++;
                    $ims->temp_act->assign('row', $row);
                    $ims->temp_act->reset('form_add_to_store.item');
                    $ims->temp_act->parse('form_add_to_store.item');
                    $out['html'] .= $ims->temp_act->text('form_add_to_store.item');
                }
            }else{
                $ims->temp_act->parse('form_add_to_store.empty');
                $out['html'] = $ims->temp_act->text('form_add_to_store.empty');
            }
        }else{
            $out['mess'] = $ims->lang['user']['action_false'];
        }

        return json_encode($out);
    }

    function do_add_product_to_store(){
        global $ims;
        $out = array(
            'ok' => 0,
            'mess' => ''
        );

        if ($ims->site_func->checkUserLogin() == 1) {
            $post_data = $ims->post['data'];

            $item_store = 0;
            foreach ($post_data as $item){
                if(substr($item['name'], 0, 12) == 'list_product'){
                    eval('$'.$item['name'].' = "'.$item['value'].'";');
                }
                if($item['name'] == 'it'){
                    $item_store = $ims->func->base64_decode($item['value']);
                }
            }

            if(!empty($list_product)){
                $arr_store = array();
                $product_owner = array();
                $product_owner_tmp = $ims->db->load_item_arr('event_product', 'is_show = 1 and user_id = '.$ims->data['user_cur']['user_id'], 'item_id, store_id');

                if($product_owner_tmp){
                    foreach ($product_owner_tmp as $item){
                        $product_owner[] = $item['item_id'];
                        $arr_store[$item['item_id']] = ($item['store_id'] != '') ? explode(',', $item['store_id']) : array();
                    }
                }

                foreach ($list_product as $k => $v){
                    if(in_array($k, $product_owner)){
                        if(!empty($v['item'])){
                            if(!in_array($item_store, $arr_store[$k])){
                                $arr_store[$k][] = $item_store;
                            }
                        }else{
                            if(in_array($item_store, $arr_store[$k])){
                                unset($arr_store[$k][array_search($item_store, $arr_store[$k])]);
                            }
                        }
                    }
                    sort($arr_store[$k]);
                    $ims->db->do_update('event_product', array('store_id' => implode(',', $arr_store[$k]), 'date_update' => time()), ' item_id = '.$k.' and user_id = '.$ims->data['user_cur']['user_id']);
                }
                $out['mess'] = $ims->lang['user']['add_product_to_store_success'];
                $out['ok'] = 1;
            }
        }else{
            $out['mess'] = $ims->lang['user']['action_false'];
        }

        return json_encode($out);

    }

    function do_form_product(){
	    global $ims;
        $out = array(
            'ok' => 0,
            'html' => $ims->lang['user']['action_false']
        );

        $dir_view = $ims->func->dirModules('user', 'views', 'path');
        $ims->temp_act = new XTemplate($dir_view ."store.tpl");
        $ims->temp_act->assign('LANG', $ims->lang);
        $ims->temp_act->assign('CONF', $ims->conf);

        if ($ims->site_func->checkUserLogin() == 1) {
            $item = (isset($ims->post['it'])) ? $ims->func->base64_decode($ims->post['it']) : 0;
            $lang_cur = $ims->post['lang_cur'];

            $data = array();
            if($item){
                $data = $ims->db->load_row('event_product', 'lang = "'.$lang_cur.'" and item_id = '.$item.' and user_id = '.$ims->data['user_cur']['user_id']);
                if($data){
                    $picture = array();
                    if($data['picture'] != ''){
                        $picture[] = $data['picture'];
                    }
                    $arr_picture = $ims->func->unserialize($data['arr_picture']);
                    $picture = array_merge($picture, $arr_picture);
                    if($picture){
                        $pic = array();
                        foreach ($picture as $item){
                            $pic['src'] = $ims->func->get_src_mod($item, 100, 100, 1, 1);
                            $pic['src_ori'] = $item;
                            $ims->temp_act->assign('pic', $pic);
                            $ims->temp_act->parse('add_edit_product.arr_picture');
                        }
                    }
                    if($data['date_begin'] != 0 && $data['date_end'] != 0){
                        $data['checked'] = 'checked';
                    }else{
                        $data['show_date'] = 'style="display:none"';
                    }
                    $data['date_begin'] = ($data['date_begin'] != 0) ? date('d/m/Y H:i', $data['date_begin']) : '';
                    $data['date_end'] = ($data['date_end'] != 0) ? date('d/m/Y H:i', $data['date_end']) : '';
                    $data['type'] = 'edit';
                    $data['content'] = $ims->func->input_editor_decode($data['content']);
                    $data['item'] = $ims->func->base64_encode($data['item_id']);
                    $data['title1'] = htmlspecialchars($data['title1']);
                    $data['title'] = htmlspecialchars($data['title']);
                    $data['content'] = htmlspecialchars($data['content']);
                }
            }else{
                $data['type'] = 'add';
                $data['show_date'] = 'style="display:none"';
            }

            $ims->temp_act->assign('data', $data);
            $ims->temp_act->parse('add_edit_product');
            $out['ok'] = 1;
            $out['html'] = $ims->temp_act->text('add_edit_product');
        }
        return json_encode($out);
    }

    function do_add_edit_product(){
	    global $ims;
        $output = array(
            'ok' => 0,
            'mess' => ''
        );
        $type  = $ims->func->if_isset($ims->post['type'], '');
        $lang_cur  = $ims->func->if_isset($ims->post['lang_cur'], '');
        $item_id = isset($ims->post['item']) ? $ims->func->base64_decode($ims->post['item']) : 0;

        $arr_picture_tmp = array();
        if ($ims->site_func->checkUserLogin() == 1) {
            if($item_id){
                $check = $ims->db->load_item('event_product', 'lang = "'.$lang_cur.'" and item_id = '.$item_id.' and user_id = '.$ims->data['user_cur']['user_id'], 'item_id');
                if(!$check){
                    $output['mess'] = $ims->lang['user']['action_false'];
                }
            }
            if($output['mess'] == ''){
                $num_item  = $ims->func->if_isset($ims->post['num_item'], 0);
                $price  = $ims->func->if_isset($ims->post['price'], 0);
                if($num_item < 0){
                    $output['mess'] = $ims->lang['user']['num_item_invalid'];
                }elseif($price < 0){
                    $output['mess'] = $ims->lang['user']['price_invalid'];
                }else{
                    $set_time = $ims->func->if_isset($ims->post['set_time'], 0);
                    if($set_time){
                        $date_begin = $ims->func->if_isset($ims->post['date_begin'], 0);
                        $date_end = $ims->func->if_isset($ims->post['date_end'], 0);
                        if($date_begin == 0){
                            $output['mess'] = $ims->lang['user']['date_begin_require'];
                        }elseif($date_end == 0){
                            $output['mess'] = $ims->lang['user']['date_end_require'];
                        }
                    }
                }
            }
            if($output['mess'] == ''){
                if (isset($ims->post['arr_picture_available']) && !empty($ims->post['arr_picture_available'])) {
                    foreach ($ims->post['arr_picture_available'] as $v) {
                        $arr_picture_tmp[] = $v;
                    }
                }
                $count = count($arr_picture_tmp);

                if (isset($_FILES['arr_picture']['name'][0]) && $_FILES['arr_picture']['name'][0] !='') {
                    foreach ($_FILES['arr_picture'] as $k_file => $v_file) {
                        if (!empty($v_file) && $k_file=='name') {
                            foreach ($v_file as $k => $v) {
                                if (in_array($v, $ims->post['arr_picture_name'])) {
                                    if ($v!='') {
                                        $count++;
                                    }
                                }else{
                                    unset($_FILES['arr_picture']['name'][$k]);
                                    unset($_FILES['arr_picture']['type'][$k]);
                                    unset($_FILES['arr_picture']['tmp_name'][$k]);
                                    unset($_FILES['arr_picture']['error'][$k]);
                                    unset($_FILES['arr_picture']['size'][$k]);
                                }
                            }
                        }
                    }

                    if ($count > 20) {
                        $output['mess'] = $ims->lang['user']['max_num_image'].' 20';
                    }else{
                        $folder_upload = "user/".$ims->data['user_cur']['folder_upload'].'/'.date('Y',time()).'_'.date('m',time()).'/product';
                        $out_pic = $ims->site_func->upload_image_multi($folder_upload, 'arr_picture', 'array');
                        if($out_pic['ok'] == 1){
                            $url_picture = $ims->func->unserialize($out_pic['url_picture']);
                            foreach ($url_picture as $v) {
                                $arr_picture_tmp[] = $v;
                            }
                        } else {
                            $output['mess']  = $out_pic['mess'];
                        }
                    }
                }
                if($count == 0){
                    $output['mess'] = $ims->lang['user']['min_image_require'];
                }
            }
        }else{
            $output['mess'] = $ims->lang['user']['action_false'];
        }

        $arr_in = array();
        if($output['mess'] == ''){
            if($type == 'add'){
                $arr_in['item_id'] = $ims->db->getAutoIncrement('event_product');
                $arr_in['user_id']  = $ims->data['user_cur']['user_id'];
            }
            $arr_in['picture']  = $arr_picture_tmp[0];
            unset($arr_picture_tmp[0]);
            $arr_in['arr_picture']  = $ims->func->serialize($arr_picture_tmp);
            $arr_in['title1']  = $ims->func->if_isset($ims->post['title1'], '');
            $arr_in['title']  = $ims->func->if_isset($ims->post['title'], '');
            $arr_in['content']  = $ims->func->if_isset($ims->post['content'], '');
            $arr_in['price']  = $ims->func->if_isset($ims->post['price'], 0);
            $arr_in['num_item']  = $ims->func->if_isset($ims->post['num_item'], 0);
            $arr_in['date_begin'] = (isset($ims->post['date_begin']) && $ims->post['date_begin']) ? strtotime(str_replace('/','-', $ims->post['date_begin'])) : 0;
            $arr_in['date_end'] = (isset($ims->post['date_end']) && $ims->post['date_end']) ? strtotime(str_replace('/','-', $ims->post['date_end'])) : 0;
            $arr_in['is_show'] = 1;
            if($type == 'add'){
                $list_lang = $ims->db->load_item_arr('lang', 'is_show = 1 order by is_default desc, show_order desc', 'name');
                foreach ($list_lang as $item){
                    $arr_in['lang'] = $item['name'];
                    $arr_in['date_create'] = time();
                    $arr_in['date_update'] = time();
                    $ok = $ims->db->do_insert("event_product", $arr_in);
                }
                if($ok){
                    $output['ok'] = 1;
                    $output['mess'] = $ims->lang['user']['add_product_success'];
                }
            }else{
                $arr_in['date_update'] = time();
                $ok = $ims->db->do_update("event_product", $arr_in, 'item_id = '.$item_id.' and user_id = '.$ims->data['user_cur']['user_id']);
                if($ok){
                    $output['ok'] = 1;
                    $output['mess'] = $ims->lang['user']['edit_product_success'];
                }
            }
        }
        return json_encode($output);
    }

    function do_delete_product(){
	    global $ims;
        $out = array(
            'ok' => '0',
            'mess' => $ims->lang['user']['action_false']
        );

        if ($ims->site_func->checkUserLogin() == 1) {
            $item_id = isset($ims->post['item']) ? $ims->func->base64_decode($ims->post['item']) : 0;
            $lang_cur = isset($ims->post['lang_cur']) ? $ims->post['lang_cur'] : '';
            $check = $ims->db->load_item('event_product', 'is_show = 1 and item_id = '.$item_id.' and user_id = '.$ims->data['user_cur']['user_id'], 'item_id');
            if($check){
                $ok = $ims->db->do_update("event_product", array('is_show' => 0, 'date_update' => time()), 'item_id = '.$item_id.' and user_id = '.$ims->data['user_cur']['user_id']);
                if($ok){
                    $out['ok'] = 1;
                    $out['mess'] = $ims->lang['user']['delete_product_success'];
                }
            }
        }
        return json_encode($out);
    }

    function do_restore_product(){
	    global $ims;
        $out = array(
            'ok' => '0',
            'mess' => $ims->lang['user']['action_false']
        );

        if ($ims->site_func->checkUserLogin() == 1) {
            $item_id = isset($ims->post['item']) ? $ims->func->base64_decode($ims->post['item']) : 0;
            $lang_cur = isset($ims->post['lang_cur']) ? $ims->post['lang_cur'] : '';
            $check = $ims->db->load_item('event_product', 'is_show = 0 and item_id = '.$item_id.' and user_id = '.$ims->data['user_cur']['user_id'], 'item_id');
            if($check){
                $ok = $ims->db->do_update("event_product", array('is_show' => 1, 'date_update' => time()), 'item_id = '.$item_id.' and user_id = '.$ims->data['user_cur']['user_id']);
                if($ok){
                    $out['ok'] = 1;
                    $out['mess'] = $ims->lang['user']['restore_product_success'];
                }
            }
        }
        return json_encode($out);
    }

    function do_load_event_user(){
	    global $ims;
	    $out = array(
	        'ok' => 1,
            'html' => '<option value="">'.$ims->lang['user']['select_user'].'</option>'
        );
	    $event_id = $ims->func->base64_decode($ims->post['item']);
	    $list_order = $ims->db->load_item_arr('event_order', 'event_id = '.$event_id.' and is_status NOT IN(17,29,31)', 'order_id');
	    if($list_order){
	        $list_order_tmp = array();
	        foreach ($list_order as $od){
                $list_order_tmp[] = $od['order_id'];
            }
            $list_order = implode(',', $list_order_tmp);
            $list_register = $ims->db->load_item_arr('event_order_detail', 'event_id = '.$event_id.' and order_id IN('.$list_order.') order by full_name asc', 'DISTINCT email, full_name, detail_id');
            if($list_register){
                foreach ($list_register as $user){
                    $detail_id = $ims->func->base64_encode($user['detail_id']);
                    $out['html'] .= '<option value="'.$detail_id.'">'.$user['full_name'].'</option>';
                }
            }
        }

	    return json_encode($out);
    }

    function do_load_info_user(){
	    global $ims;
        $dir_view = $ims->func->dirModules('user', 'views', 'path');
        $ims->temp_act = new XTemplate($dir_view ."store.tpl");
        $ims->temp_act->assign('LANG', $ims->lang);
        $ims->temp_act->assign('CONF', $ims->conf);

        $out = array(
            'ok' => 0,
            'html' => ''
        );

        $detail_id = $ims->func->base64_decode($ims->post['item']);
        $user = $ims->db->load_row('event_order_detail', 'detail_id = '.$detail_id, 'full_name, email, phone');
        if($user){
            $ims->temp_act->assign('data', $user);
            $ims->temp_act->parse('create_order.info_user');
            $out['ok'] = 1;
            $out['html'] = $ims->temp_act->text('create_order.info_user');
        }

        return json_encode($out);
    }

    function do_load_search_product(){
	    global $ims;
	    $data = array();

	    $keyword = $ims->post['keyword'];
        $lang_cur = $ims->post['lang_cur'];
	    $event = ($ims->post['event']) ? $ims->func->base64_decode($ims->post['event']) : 0;

        if ($ims->site_func->checkUserLogin() == 1) {
            $arr_key = explode(' ', $keyword);
            $where = '';
            $arr_tmp = array();
            foreach ($arr_key as $value) {
                $value = trim($value);
                if (!empty($value)) {
                    $arr_tmp['title'][] = "title LIKE '%".$value."%'";
                    $arr_tmp['title1'][] = "title1 LIKE '%".$value."%'";
                }
            }
            if (count($arr_tmp) > 0) {
                foreach ($arr_tmp as $k => $v) {
                    if (count($v) > 0) {
                        $arr_tmp[$k] = "(" . implode(" AND ", $v) . ")";
                    } else {
                        unset($arr_tmp[$k]);
                    }
                }
            }
            if (count($arr_tmp) > 0) {
                $where .= " AND (".implode(" OR ", $arr_tmp).")";
            }

            if($event){
                $where .= ' AND find_in_set('.$event.', event_id)';
            }
            $result = $ims->db->load_item_arr('event_product', 'is_show = 1 and lang = "'.$lang_cur.'"'.$where.' and user_id = '.$ims->data['user_cur']['user_id'].' order by title1 asc, title asc', 'title1, title, picture, item_id, price, num_item, num_sold');
            if($result){
                foreach ($result as $row){
                    $title1 = ($row['title1'] != '') ? $row['title1'].':&nbsp' : '';
                    $row['title'] = $title1.$ims->func->input_editor_decode($row['title']);
                    $row['picture'] = $ims->func->get_src_mod($row['picture'], 50, 50, 1, 1);
                    $row['item_id'] = $ims->func->base64_encode($row['item_id']);
                    $price = '<p class="price">'.number_format($row['price'], 0, ',','.').$ims->lang['global']['unit'].'</p>';
                    $inventory = '<p class="inventory">'.$ims->lang['user']['inventory'].'&nbsp'.($row['num_item'] - $row['num_sold']).'</p>';
                    $row['info'] = $price.$inventory;
                    if($row['num_item'] - $row['num_sold'] > 0){
                        array_push($data, $row);
                    }
                }
            }else{
                $row = array(
                    'title' => $ims->lang['user']['not_found_product'],
                    'picture' => '',
                    'item_id' => 0,
                    'info' => ''
                );
                array_push($data, $row);
            }
        }
	    return json_encode($data);
    }

    function do_load_cart(){
	    global $ims;
        $dir_view = $ims->func->dirModules('user', 'views', 'path');
        $ims->temp_act = new XTemplate($dir_view ."store.tpl");
        $ims->temp_act->assign('LANG', $ims->lang);
        $ims->temp_act->assign('CONF', $ims->conf);

        $out = array(
            'ok' => 0,
            'html' => '',
            'mess' => ''
        );
        $item_add = ($ims->post['item_add']) ? $ims->func->base64_decode($ims->post['item_add']) : 0;
        $post_data = $ims->post['data'];
        $lang_cur = $ims->post['lang_cur'];

        if ($ims->site_func->checkUserLogin() == 1) {
            $payment_selected = '';
            foreach ($post_data as $item){
                if(substr($item['name'], 0, 12) == 'list_product'){
                    eval('$'.$item['name'].'="'.$item['value'].'";');
                }
                if($item['name'] == 'method'){
                    $payment_selected = $item['value'];
                }
                if($item['name'] == 'event'){
                    $event = $ims->func->base64_decode($item['value']);
                }
            }

            $total = 0;
            $arr_item = array();
            $i = 0;
            if(!empty($list_product)){
                foreach ($list_product as $item){
                    $item_id = $ims->func->base64_decode($item['item']);
                    $row = $ims->db->load_row('event_product', 'is_show = 1 and lang = "'.$lang_cur.'" and item_id = '.$item_id.' and user_id = '.$ims->data['user_cur']['user_id'].' and find_in_set('.$event.', event_id)', 'title1, title, picture, item_id, num_item, num_sold, price');
                    if($row){
                        $arr_item[] = $row['item_id'];
                        $total += $row['price'] * $item['quantity'];
                        $row['title1'] = ($row['title1'] != '') ? $row['title1'].':&nbsp' : '';
                        $row['index'] = $i;
                        $row['picture'] = $ims->func->get_src_mod($row['picture'], 87, 87, 1, 1);
                        $row['quantity'] = $item['quantity'];
                        $row['max_quantity'] = $row['num_item'] - $row['num_sold'];
                        $row['item_id'] = $ims->func->base64_encode($row['item_id']);
                        $row['into_money'] = number_format($row['price']*$item['quantity'],0,',','.');
                        $row['price'] = number_format($row['price'],0,',','.');
                        $i++;
                        $ims->temp_act->assign('row', $row);
                        $ims->temp_act->reset('create_order.item');
                        $ims->temp_act->parse('create_order.item');
                        $out['html'] .= $ims->temp_act->text('create_order.item');
                    }
                }
            }
            if(in_array($item_add, $arr_item)){
                $out['html'] = '';
                $out['mess'] = $ims->lang['user']['product_already_in_cart'];
            }
            if($out['mess'] == ''){
                if(empty($list_product) || ($item_add != 0 && !in_array($item_add, $arr_item))){
                    $product_new = $ims->db->load_row('event_product', 'is_show = 1 and lang = "'.$lang_cur.'" and item_id = '.$item_add.' and user_id = '.$ims->data['user_cur']['user_id'].' and find_in_set('.$event.', event_id)', 'title1, title, picture, item_id, num_item, num_sold, price');
                    if($product_new){
                        $total += $product_new['price'];
                        $product_new['title1'] = ($product_new['title1'] != '') ? $product_new['title1'].':&nbsp' : '';
                        $product_new['index'] = $i;
                        $product_new['picture'] = $ims->func->get_src_mod($product_new['picture'], 87, 87, 1, 1);
                        $product_new['quantity'] = 1;
                        $product_new['max_quantity'] = $product_new['num_item'] - $product_new['num_sold'];
                        $product_new['item_id'] = $ims->func->base64_encode($product_new['item_id']);
                        $product_new['price'] = $product_new['into_money'] = number_format($product_new['price'],0,',','.');
                        $ims->temp_act->assign('row', $product_new);
                        $ims->temp_act->reset('create_order.item');
                        $ims->temp_act->parse('create_order.item');
                        $out['html'] .= $ims->temp_act->text('create_order.item');
                    }
                }

                if((empty($list_product) || $out['html'] == '') && $item_add == 0){
                    $ims->temp_act->parse('create_order.empty'); // Giỏ hàng rỗng
                    $out['html'] = $ims->temp_act->text('create_order.empty');
                }
                $list_payment = $ims->db->load_item('event_setting', 'is_show = 1 and lang = "'.$lang_cur.'" and setting_key = "payment_methods"', 'setting_value');
                if($list_payment){
                    $list_payment = explode(',', $list_payment);
                    foreach ($list_payment as $payment){
                        $payment = trim($payment);
                        if($payment == $payment_selected){
                            $selected = 'selected';
                        }else{
                            $selected = '';
                        }
                        $ims->temp_act->assign('selected', $selected);
                        $ims->temp_act->assign('payment', $payment);
                        $ims->temp_act->parse('create_order.total.payment.item');
                    }
                    $ims->temp_act->parse('create_order.total.payment');
                }
                $ims->temp_act->assign('total', number_format($total,0,',','.').'&nbsp'.$ims->lang['global']['unit']);
                $ims->temp_act->parse('create_order.total');
                $out['html'] .= $ims->temp_act->text('create_order.total');
                $out['ok'] = 1;
            }
        }

        return json_encode($out);
    }

    function do_create_order(){
	    global $ims;
        $out = array(
            'ok' => 0,
            'mess' => $ims->lang['user']['create_order_false']
        );

        if ($ims->site_func->checkUserLogin() == 1) {
            $post_data = $ims->post['data'];
            $lang_cur = $ims->post['lang_cur'];

            $arr_in = array();
            foreach ($post_data as $item){
                if(substr($item['name'], 0, 12) == 'list_product'){
                    eval('$'.$item['name'].'="'.$item['value'].'";');
                }else{
                    if($item['name'] == 'event' || $item['name'] == 'user'){
                        $arr_in[$item['name']] = $ims->func->base64_decode($item['value']);
                    }else{
                        $arr_in[$item['name']] = $item['value'];
                    }
                }
            }
            unset($arr_in['search']);
            if(empty($list_product)){
                $out['mess'] = $ims->lang['user']['no_have_product'];
            }else{
                if($arr_in['method'] == ''){
                    $out['mess'] = $ims->lang['user']['not_yet_payment'];
                }else{
                    $arr_in['user_id'] = $ims->data['user_cur']['user_id'];
                    $arr_in['event_id'] = ($arr_in['event']) ? $arr_in['event'] : 0;
                    $arr_in['detail_id'] = ($arr_in['user']) ? $arr_in['user'] : 0;
                    unset($arr_in['user'], $arr_in['event']);
                    $arr_in['date_create'] = $arr_in['date_update'] = time();
                    $ok = $ims->db->do_insert('event_product_order', $arr_in);
                    if($ok){
                        $order_id = $ims->db->insertid();
                        $arr_update = array(
                            'total_order' => 0
                        );
                        $check = 0;
                        $arr_order_detail = array();
                        foreach ($list_product as $item){
                            $item_id = $ims->func->base64_decode($item['item']);
                            $row = $ims->db->load_row('event_product', 'is_show = 1 and lang = "'.$lang_cur.'" and item_id = '.$item_id.' and find_in_set('.$arr_in['event_id'].', event_id)', 'item_id, price, num_item, num_sold');
                            if($row){
                                if($item['quantity'] <= ($row['num_item'] - $row['num_sold'])){
                                    $check++;
                                    $arr_update['total_order'] += $row['price']*$item['quantity'];
                                    $arr_order_detail[] = array(
                                        'order_id' => $order_id,
                                        'event_product_id' => $row['item_id'],
                                        'price_buy' => $row['price'],
                                        'quantity' => $item['quantity'],
                                        'date_create' => time(),
                                        'date_update' => time(),
                                        'num_sold_update' => $row['num_sold'] + $item['quantity']
                                    );
                                }
                            }
                        }
                        if($check == count($list_product)){
                            foreach ($arr_order_detail as $arr_detail){
                                $num_sold_update = $arr_detail['num_sold_update'];
                                unset($arr_detail['num_sold_update']);
                                $ims->db->do_insert('event_product_order_detail', $arr_detail);
                                $ims->db->do_update('event_product', array('num_sold' => $num_sold_update), 'item_id = '.$arr_detail['event_product_id']);
                            }
                            $arr_update['total_payment'] = $arr_update['total_order'];
                            $arr_update['is_show'] = 1;
                            $ok1 = $ims->db->do_update('event_product_order', $arr_update, 'order_id = '.$order_id);
                            if($ok1 == 1){
                                $out['mess'] = $ims->lang['user']['create_order_success'];
                                $out['ok'] = 1;
                            }
                        }
                    }
                }
            }
        }
	    return json_encode($out);
    }

    function do_load_event(){
	    global $ims;
        $dir_view = $ims->func->dirModules('user', 'views', 'path');
        $ims->temp_act = new XTemplate($dir_view ."store.tpl");
        $ims->temp_act->assign('LANG', $ims->lang);
        $ims->temp_act->assign('CONF', $ims->conf);

	    $out = array(
	        'ok' => 0,
            'html' => '',
            'mess' => ''
        );

	    $product_item = $ims->func->base64_decode($ims->post['it']);
	    $lang_cur = $ims->post['lang_cur'];
	    $sort = $ims->post['sort'];
	    $keyword = $ims->post['keyword'];

        if ($ims->site_func->checkUserLogin() == 1) {
            $out['ok'] = 1;
            $where = '';

            if($keyword){
                $arr_key = explode(' ', $keyword);
                $arr_tmp = array();
                foreach ($arr_key as $value) {
                    $value = trim($value);
                    if (!empty($value)) {
                        $arr_tmp['title'][] = "title LIKE '%".$value."%'";
                        $arr_tmp['title1'][] = "title1 LIKE '%".$value."%'";
                    }
                }
                if (count($arr_tmp) > 0) {
                    foreach ($arr_tmp as $k => $v) {
                        if (count($v) > 0) {
                            $arr_tmp[$k] = "(" . implode(" AND ", $v) . ")";
                        } else {
                            unset($arr_tmp[$k]);
                        }
                    }
                }
                if (count($arr_tmp) > 0) {
                    $where .= " AND (".implode(" OR ", $arr_tmp).")";
                }
            }
            if($sort != ''){
                if ($sort == 0){
                    $where .= ' AND date_begin > '.time(); //Sắp diễn ra
                }elseif ($sort == 1){
                    $where .= ' AND date_begin <= '.time().' AND date_end >= '.time(); //Đang diễn ra
                }elseif ($sort == 2){
                    $where .= ' AND date_end < '.time(); //Đã kết thúc
                }
            }

            $list_event_added = array();
            $event_product_cur = $ims->db->load_item('event_product', 'item_id = '.$product_item.' and user_id = '.$ims->data['user_cur']['user_id'], 'event_id');
            if($event_product_cur){
                $list_event_added = explode(',', $event_product_cur); // list event đã thêm sp này vào
            }
            $list_event = $ims->db->load_item_arr('event', 'is_show = 1 and lang = "'.$lang_cur.'"'.$where.' and user_id = '.$ims->data['user_cur']['user_id'].' order by title1 asc, title asc', 'title1, title, picture, address, organizer, date_begin, date_end, item_id');
            if($list_event){
                $i = 0;
                foreach ($list_event as $row){
                    $title1 = ($row['title1'] != '') ? $row['title1'].':&nbsp' : '';
                    $row['title'] = $title1.$row['title'];
                    $row['picture'] = $ims->func->get_src_mod($row['picture'], 100, 100, 1, 1);
                    $row['address'] = ($row['address'] != '') ? '<p>'.$row['address'].'</p>' : '';
                    $checked = '';
                    if($list_event_added && in_array($row['item_id'], $list_event_added)){
                        $checked = 'checked';
                    }
                    $row['checkbox'] = '<input type="checkbox" name="list_event['.$row['item_id'].'][\'item\']" value="'.$row['item_id'].'" id="check'.$i.'" '.$checked.'><label for="check'.$i.'"></label>';
                    if($row['date_begin'] > time()){
                        $row['event_status'] = '<span style="color: #04457A">'.$ims->lang['user']['event_upcoming'].'</span>';
                    }elseif ($row['date_begin'] <= time() && $row['date_end'] >= time()){
                        $row['event_status'] = '<span style="color: #25AB0C;">'.$ims->lang['user']['event_ongoing'].'</span>';
                    }elseif ($row['date_end'] < time()){
                        $row['event_status'] = '<span style="color: #D80F00;">'.$ims->lang['user']['event_over'].'</span>';
                        $row['checkbox'] = '<span class="over '.$checked.'"></span>';
                    }
                    $row['date_begin'] = '<p>'.$ims->lang['global']['day_'.date('N', $row['date_begin'])].', '.date('d/m h:i A', $row['date_begin']).'</p>';
                    $row['item_hidden'] = '<input type="hidden" name="list_event['.$row['item_id'].'][\'d\']" value="1">';
                    $i++;
                    $ims->temp_act->assign('row', $row);
                    $ims->temp_act->reset('form_add_to_event.item');
                    $ims->temp_act->parse('form_add_to_event.item');
                    $out['html'] .= $ims->temp_act->text('form_add_to_event.item');
                }
            }else{
                $ims->temp_act->parse('form_add_to_event.empty');
                $out['html'] = $ims->temp_act->text('form_add_to_event.empty');
            }
        }else{
            $out['mess'] = $ims->lang['user']['action_false'];
        }

	    return json_encode($out);
    }

    function do_add_product_to_event(){
	    global $ims;
        $out = array(
            'ok' => 0,
            'mess' => ''
        );

        if ($ims->site_func->checkUserLogin() == 1) {
            $post_data = $ims->post['data'];
            $item_product = 0;
            foreach ($post_data as $item){
                if(substr($item['name'], 0, 10) == 'list_event'){
                    eval('$'.$item['name'].' = "'.$item['value'].'";');
                }
                if($item['name'] == 'it'){
                    $item_product = $ims->func->base64_decode($item['value']);
                }
            }

            if(!empty($list_event)){
                $event_owner = array();
                $event_owner_tmp = $ims->db->load_item_arr('event', 'is_show = 1 and user_id = '.$ims->data['user_cur']['user_id'], 'item_id');
                if($event_owner_tmp){
                    foreach ($event_owner_tmp as $item){
                        $event_owner[] = $item['item_id'];
                    }
                }
                $event_chosed = array();
                $event = $ims->db->load_item('event_product', 'item_id = '.$item_product.' and user_id = '.$ims->data['user_cur']['user_id'], 'event_id');
                if($event){
                    $event_chosed = explode(',', $event);
                }

                foreach ($list_event as $k => $v){
                    if(!empty($v['item'])){
                        if(in_array($v['item'], $event_owner) && !in_array($v['item'], $event_chosed)){
                            $event_chosed[] = $v['item'];
                        }
                    }else{
                        if(in_array($k, $event_owner) && in_array($k, $event_chosed)){
                            unset($event_chosed[array_search($k, $event_chosed)]);
                        }
                    }
                }
                sort($event_chosed);
                $ok = $ims->db->do_update('event_product', array('event_id' => implode(',', $event_chosed), 'date_update' => time()), ' item_id = '.$item_product.' and user_id = '.$ims->data['user_cur']['user_id']);
                if($ok){
                    $out['mess'] = $ims->lang['user']['add_event_success'];
                    $out['ok'] = 1;
                }
            }
        }else{
            $out['mess'] = $ims->lang['user']['action_false'];
        }

        return json_encode($out);
    }
}
?>