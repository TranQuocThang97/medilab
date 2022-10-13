<?php
if (!defined('IN_ims')) { die('Access denied'); }
$nts = new sMain();

class sMain
{
	var $modules = "user";
	var $action  = "image";
	var $sub 	 = "manage";
	var $template = "image";
	
	/**
		* Khởi tạo
		* Quản lý sự kiện
	**/
	function __construct (){
		global $ims;

		$dir_assets  = $ims->func->dirModules($this->modules, 'assets');		
		$ims->func->include_css($dir_assets."css/event.css");
		$ims->func->include_css($dir_assets."css/".$this->modules.'.css');
		$arrLoad = array(
			'modules' 		 => $this->modules,
			'action'  		 => $this->action,
			'template'  	 => $this->modules,
			'js'  	 		 => $this->modules,
			'css'  	 		 => $this->template,
			'use_func'  	 => $this->modules, // Sử dụng func
			'use_navigation' => 0, // Sử dụng navigation
			'required_login' => 1, // Bắt buộc đăng nhập
		);
        $ims->func->loadTemplate($arrLoad);
		//custom tpl
		$dir_view      = $ims->func->dirModules($this->modules, 'views', 'path');
        $ims->temp_out = new XTemplate($dir_view . $this->template . ".tpl");
        $ims->temp_out->assign('CONF', $ims->conf);
        $ims->temp_out->assign('LANG', $ims->lang);
        $ims->temp_out->assign('DIR_IMAGE', $ims->dir_images);

        $ims->func->include_css($ims->dir_js."image-uploader/image-uploader.css");
		$ims->func->include_js($ims->dir_js."image-uploader/image-uploader.js");

		$ims->func->include_css($ims->dir_js."bootstrap-toggle/css/bootstrap4-toggle.min.css");
		$ims->func->include_js($ims->dir_js."bootstrap-toggle/js/bootstrap4-toggle.min.js");

		$data = array();
		$data['content'] = '';
		$param = $ims->func->get_id_page($ims->conf['cur_act_url']);
		if(!empty($param['detail'])){
			$other = $param;
			unset($other['detail']);
			if(count($other) == 0){
				$data['content'] = $this->do_image($param['detail']);
			}
		}else{
			$data['content'] = $this->do_main();
		}		
		$data['box_left'] = box_left($this->action);
	
		$ims->conf["class_full"] = 'user';
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("main");
		$ims->output .=  $ims->temp_act->text("main");
	}

	function do_image($id = 0){
		global $ims;

		$data = array();
		$info = $ims->db->load_row('event','is_show=1 and lang="'.$ims->conf['lang_cur'].'" and item_id="'.$id.'"');
		if(!$info){
			$ims->html->redirect_rel($ims->site_func->get_link('user', $ims->setting['user']['event_link']));
		}

		$data['arr_logo'] = $ims->func->unserialize($info['arr_logo']);		
		if($data['arr_logo']){
			foreach ($data['arr_logo'] as $pic) {
				$row = array();
				$row['picture'] = $ims->func->get_src_mod($pic);
				$ims->temp_out->assign('row', $row);
				$ims->temp_out->parse("image.logo.row");
			}
			$ims->temp_out->parse("image.logo");
		}
		$data['title'] = $ims->func->input_editor_decode($info['title']);
		switch ($ims->conf['lang_cur']) {
			case 'vi':
				$data['date_begin'] = $ims->func->rebuild_date('l, d/m, h:i A', $info['date_begin']);
				break;
			case 'en':
				$data['date_begin'] = date('l, d/m, h:i A', $info['date_begin']);
			default:
				break;
		}
		$data['event_id'] = $info['item_id'];
		$data['organizer'] = $ims->func->input_editor_decode($info['organizer']);

		$arr_image = $ims->db->load_row_arr('event_image','is_show=1 and lang="'.$ims->conf['lang_cur'].'" and event_id="'.$info['item_id'].'"');
		if($arr_image){
			foreach ($arr_image as $row) {
				$row['title'] = htmlspecialchars($row['title']);
				$row['thumb'] = $ims->func->get_src_mod($row['picture'], 190, 170, 1, 1);
				$row['picture'] = $ims->func->get_src_mod($row['picture']);
				$ims->temp_out->assign('row', $row);
				switch ($row['type']) {
					case 'event':
						$ims->temp_out->parse("image.event");
						break;
					case 'personal':
						$ims->temp_out->parse("image.personal");
						break;
					default:
						break;
				}
				
			}
		}
		// if(file_exists($ims->conf['rootpath']."library/aws/aws-autoloader.php")){
	 //        require_once ($ims->conf['rootpath']."library/aws/aws-autoloader.php");
	 //        // 'key' => 'AKIAWWYO6NIOLC2JMOPW',
	 //        // 'secret' => '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',
	 //        $configAWS = array(
	 //        	'credentials' => array(
	 //        		'key' => $ims->conf['aws_key'],
  //                   'secret' => $ims->conf['aws_secret'],
	 //        	),
	 //        	'region' => 'ap-southeast-1',
	 //        	'version' => 'latest',
	 //        ); 
	 //        $client = new Aws\Rekognition\RekognitionClient($configAWS);
	 //        $photo = $ims->conf['rootpath'].'uploads/event/2022_06/sk3.jpg';
	 //        $infoUser = $ims->data['user_cur'];
	 //        // print_arr($ims->conf['rooturl'].'uploads/event/2022_06/sk3.jpg');
	 //        if(file_exists($photo)){
	 //        	$imageSrc = imagecreatefromstring(file_get_contents($photo));
	 //        	$imgSize = getimagesize($photo);
		// 	    $fp_image = fopen($photo, 'r');
		// 	    $image = fread($fp_image, filesize($photo));
		// 	    fclose($fp_image);
		//         $result = $client->detectFaces(
		//         	array(
		//         		'Image' => array(
		// 		          	'Bytes' => $image,
		// 		      	),
		// 		       	'Attributes' => array('ALL'),
		//         	)
		//         );	
		//         print_arr($result);
		//         $title = 'net-chm-ph-cua-neo';
		// 		foreach ($result['FaceDetails'] as $k => $pic) {
		// 			if($pic['Confidence'] >= 80){
		// 				$detail = array();
  //                       $detail['width'] = $pic['BoundingBox']['Width'] * $imgSize['0'];
  //                       $detail['height'] = $pic['BoundingBox']['Height'] * $imgSize['1'];
  //                       $detail['startX'] = $pic['BoundingBox']['Left'] * $imgSize['0'];
  //                       $detail['startY'] = $pic['BoundingBox']['Top'] * $imgSize['1'];
  //                       $face = imagecrop($imageSrc, array('x' => $detail['startX'], 'y' => $detail['startY'], 'width' => $detail['width'], 'height' => $detail['height']));
  //                       $folder_upload = "event/".$infoUser['folder_upload'].'/'.$ims->func->get_friendly_link($title).'/'.date('Y',time()).'_'.date('m',time()).'/faces/';
  //                       $name = time().'-'.$k.'.jpg';
  //                       $ims->func->rmkdir($folder_upload);
  //                       imagejpeg($face, $ims->conf['rootpath'].'uploads/'.$folder_upload.$name, 100);
	 //            	}
		// 		}
	 //        }
	 //    }

		$ims->temp_out->assign('data', $data);
		$ims->temp_out->parse("image");
		return $ims->temp_out->text("image");
	}
	
	function do_main (){
		global $ims;	
		$data = array();
		$data['src'] = $ims->conf['rooturl'].'resources/images/user/';				
		$data['link_action'] = $ims->site_func->get_link('user', $ims->setting['user']['event_link']);

		$search_keyword = $ims->func->if_isset($ims->input['search_keyword']);
		$search_status = $ims->func->if_isset($ims->input['search_status']);
		$search_organizer = $ims->func->if_isset($ims->input['search_organizer']);		
		$is_search = 0;

		$p = $ims->func->if_isset($ims->input["p"], 1);
		$ext = '';
		$where = '';		

		$arr_status = array(
        	0 => $ims->lang['user']['event_upcoming'],
        	1 => $ims->lang['user']['event_ongoing'],
        	2 => $ims->lang['user']['event_over'],
        );        
        $data['status'] = $ims->html->select("search_status", $arr_status, $search_status, " class=\"\"", array("title" => $ims->lang['user']['event_status']));

        $arr_organizer = $ims->load_data->data_table('event', 'organizer', 'organizer, organizer as title', 'is_show=1 and user_id="'.$ims->data['user_cur']['user_id'].'" and organizer!="" ');        
        $data['organizer'] = $ims->html->select("search_organizer", $arr_organizer, $search_organizer, " class=\"\"", array("title" => $ims->lang['user']['organizer']));

		if(!empty($search_keyword)){
			$data['search_keyword'] = $search_keyword;
			$arr_tmp = array();
			$arr_key = explode(" ", $search_keyword);
	        foreach ($arr_key as $value) {
	            $value = trim($value);
	            if (!empty($value)) {
	                $value = str_replace(chr(39), chr(34),$value);
	                $arr_tmp['title'][] = "LOWER(title) like CONCAT('%', CONVERT('" . mb_strtolower($value,'UTF-8') . "', BINARY), '%')";	                
	                $order .= "(title = '".$value."') desc, ";
	            }
	        }
	        if (count($arr_tmp) > 0) {
	            foreach ($arr_tmp as $k => $v) {
	                if (count($v) > 0) {
	                    $arr_tmp[$k] = "(" . implode(" and ", $v) . ")";
	                } else {
	                    unset($arr_tmp[$k]);
	                }
	            }
	        }
	        if (count($arr_tmp) > 0) {
	            $where .= " and (" . implode(" or ", $arr_tmp) . ") ";
	        }
			$ext .= "&search_keyword=".$search_keyword;
			$is_search = 1;
		}
		if(!empty($search_status)){
			switch ($search_status) {
				case '1':
					$where .= " AND date_begin <= ".time()." and date_end >= ".time()." ";					
					break;
				case '2':
					$where .= " AND date_end < ".time()." ";
					break;
				default: //0
					$where .= " AND date_begin > ".time()." ";
					break;
			}
			$ext .= "&search_status=".$search_status;
			$is_search = 1;
		}
		if(!empty($search_organizer)){
			$where .= " AND LOWER(organizer) = CONVERT('" . mb_strtolower($search_organizer,'UTF-8') . "', BINARY) ";
			$ext .= "&search_organizer=".$search_organizer;
			$is_search = 1;
		}

		$num_total = $ims->db->do_get_num("event", 'lang="'.$ims->conf['lang_cur'].'" and is_show=1 and user_id="'.$ims->data['user_cur']['user_id'].'" '.$where);
		$n = !empty($ims->setting['user']['num_list'])?$ims->setting['user']['num_list']:10;
        $num_items = ceil($num_total / $n);
        if ($p > $num_items)
            $p = $num_items;
        if ($p < 1)
            $p = 1;
        $start = ($p - 1) * $n;
     	$link_action =  $ims->site_func->get_link('user', $ims->setting['user']['event_link']);       	
        $nav = $ims->site->paginate($link_action, $num_total, $n, $ext, $p);
        $data['nav'] = $nav;

		$arr_event = $ims->db->load_item_arr('event', 'lang="'.$ims->conf['lang_cur'].'" and is_show=1 and user_id="'.$ims->data['user_cur']['user_id'].'" '.$where.' order by date_create desc limit '.$start.','.$n, 'item_id, title, picture, address, date_begin, date_end, arr_price');		
		if($arr_event){
			foreach ($arr_event as $row) {
				$row['title'] = $ims->func->input_editor_decode($row['title']);
				$row['picture'] = $ims->func->get_src_mod($row['picture'],102,102,1,0,array('fix_min' => 1));
				$row['address'] = $ims->func->input_editor_decode($row['address']);
				$row['status'] = $ims->lang['user']['event_upcoming'];
				if(time() >= $row['date_begin'] && time() <= $row['date_end']){
					$row['status'] = $ims->lang['user']['event_ongoing'];
				}
				if(time() > $row['date_end']){
					$row['status'] = $ims->lang['user']['event_over'];
				}
				switch ($ims->conf['lang_cur']) {
					case 'vi':
						$row['date_begin'] = $ims->func->rebuild_date('l, d/m, h:i A', $row['date_begin']);	
						break;
					case 'en':
						$row['date_begin'] = date('l, d/m, h:i A', $row['date_begin']);	
					default:
						break;
				}
				$row['ticket_remain'] = 0;
				$row['ticket_total'] = 0;
				$arr_price = $ims->func->unserialize($row['arr_price']);
				if($arr_price){
					foreach ($arr_price as $k_p => $v_p) {						
						$row['ticket_remain'] += $v_p['num_ticket_remain'];
						$row['ticket_total'] += $v_p['num_ticket'];
					}
				}
				$row['revenue'] = 0;
				$event_order = $ims->db->load_row('event_order',' event_id="'.$row['item_id'].'" ',' SUM(total_payment) as revenue');
				if(!empty($event_order['revenue'])){
					$row['revenue'] += $event_order['revenue'];
				}
				$row['revenue'] = $ims->func->format_number($row['revenue']).' '.$ims->lang['global']['unit'];
				$row['participations'] = $ims->db->do_get_num('event_order_detail',' event_id="'.$row['item_id'].'" and is_checkin=1');
				$row['link_image'] = $ims->site_func->get_link('user', $ims->setting['user']['image_link']).'/'.$ims->func->link2hex('?detail='.$row['item_id'],6);
				
				$ims->temp_out->assign('row', $row);
				if(time() <= $row['date_end']){
					$ims->temp_out->parse("event.row.checkin");			
				}
				$ims->temp_out->parse("event.row");
			}
		}else{
			$ims->temp_out->assign('row', array('title' => $ims->lang['global']['no_have_data']));
			$ims->temp_out->parse("event.row_empty");
		}
		
		$ims->temp_out->assign('data', $data);
		$ims->temp_out->parse("event");
		return $ims->temp_out->text("event");
	}
  	// End class
}
?>