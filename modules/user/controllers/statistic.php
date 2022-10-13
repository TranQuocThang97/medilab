<?php
if (!defined('IN_ims')) { die('Access denied'); }
$nts = new sMain();

class sMain
{
	var $modules = "user";
	var $action  = "statistic";
	var $sub 	 = "manage";
	var $template = "statistic";
	
	/**
		* Khởi tạo
		* Quản lý sự kiện
	**/
	function __construct (){
		global $ims;

		$dir_assets  = $ims->func->dirModules($this->modules, 'assets');
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
        $ims->func->include_css($ims->dir_js."jquery_ui/jquery-ui-timepicker-addon.css");
        $ims->func->include_js($ims->dir_js."jquery_ui/jquery-ui-timepicker-addon.min.js");
        $ims->func->include_js($ims->dir_js.'amcharts/core.js');
		$ims->func->include_js($ims->dir_js.'amcharts/charts.js');
		$ims->func->include_js($ims->dir_js.'amcharts/animated.js');
        //custom tpl
		$dir_view      = $ims->func->dirModules($this->modules, 'views', 'path');
        $ims->temp_out = new XTemplate($dir_view . $this->template . ".tpl");
        $ims->temp_out->assign('CONF', $ims->conf);
        $ims->temp_out->assign('LANG', $ims->lang);
        $ims->temp_out->assign('DIR_IMAGE', $ims->dir_images);

		$data = array();
		$data['content'] = '';		
		$param = $ims->func->get_id_page($ims->conf['cur_act_url']);
		if(!empty($param['detail'])){
			$other = $param;
			unset($other['detail']);
			if(count($other) == 0){
				$data['content'] = $this->do_main($param['detail']);
			}
			if(!empty($param['list'])){
				$data['content'] = $this->do_table($param['detail']);
			}
			if(!empty($param['team'])){
				$data['content'] = $this->do_team($param['detail']);
			}
		}else{
			$ims->html->redirect_rel($ims->site_func->get_link('user', $ims->setting['user']['event_link']));
		}
		$data['box_left'] = box_left($this->action);
		
		$ims->conf["class_full"] = 'user';
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("main");
		$ims->output .=  $ims->temp_act->text("main");
	}
		
	function do_main($id=0){
		global $ims;
		$data = array();
		
		$info = $ims->db->load_row('event','is_show=1 and lang="'.$ims->conf['lang_cur'].'" and item_id="'.$id.'"');
		if(!$info){
			$ims->html->redirect_rel($ims->site_func->get_link('user', $ims->setting['user']['event_link']));
		}

		$data['content'] = '';
		$data['content'] .= $this->do_info($info);
		$data['content'] .= $this->do_general($info);
		$data['content'] .= $this->do_ticket($info);
		$data['content'] .= $this->do_picture($info);
		$data['content'] .= $this->do_chart($info);
		$data['content'] .= $this->do_percent($info);

		$ims->temp_out->assign('data', $data);
		$ims->temp_out->parse("statistic");
		return $ims->temp_out->text("statistic");
	}

	function do_info($info = array()){
		global $ims;
		$ims->func->include_css($ims->dir_js."qrcode-reader/css/qrcode-reader.min.css");
        $ims->func->include_js($ims->dir_js."qrcode-reader/js/qrcode-reader.min.js");

		$data = array();		
		$data['page_title'] = $ims->setting['user']['statistic_meta_title'];
		$data['title'] = $ims->func->input_editor_decode($info['title']);
		$data['event_id'] = $info['item_id'];
		if(!empty($info['picture'])){
			$data['picture_src'] = $ims->func->get_src_mod($info['picture']);
			$data['picture'] = $ims->func->get_src_mod($info['picture'],102,102,1,0,array('fix_min' => 1));
		}
		$data['address'] = $ims->func->input_editor_decode($info['address']);
		switch ($ims->conf['lang_cur']) {
			case 'vi':
				$data['date_begin'] = $ims->func->rebuild_date('l, d/m, h:i A', $info['date_begin']);
				break;
			case 'en':
				$data['date_begin'] = date('l, d/m, h:i A', $info['date_begin']);
			default:
				break;
		}
		$data['link_checkin'] = $ims->site_func->get_link('user', $ims->setting['user']['checkin_link']);
		$data['link_jsQR'] = $ims->dir_js."qrcode-reader/js/jsQR/jsQR.min.js";
        $data['link_audio'] = $ims->dir_js."qrcode-reader/audio/beep.mp3";
		$data['src'] = $ims->conf['rooturl'].'resources/images/user/';
		$data['icon_qr'] = $data['src'].'scan.svg';
		$ims->temp_out->assign('data', $data);
		$ims->temp_out->reset("info");
		$ims->temp_out->parse("info");
		return $ims->temp_out->text("info");
	}

	function do_general($info = array()){
		global $ims;
		$data = array();		
		$data['total_revenue'] = 0;
		$data['revenue'] = array(
			'ticket' => 0,
			'product' => 0,
			'picture' => 0,
		);		
		
		$data['total_revenue'] = $ims->func->format_number(115000000).' '.$ims->lang['global']['unit'];
		$data['revenue']['ticket'] = $ims->func->format_number(75000000).' '.$ims->lang['global']['unit'];
		$data['revenue']['product'] = $ims->func->format_number(20000000).' '.$ims->lang['global']['unit'];
		$data['revenue']['picture'] = $ims->func->format_number(20000000).' '.$ims->lang['global']['unit'];

		$ims->temp_out->assign('data', $data);
		$ims->temp_out->parse("general");
		return $ims->temp_out->text("general");
	}

	function do_ticket($info = array()){
		global $ims;
		$data = array();

		$arr_order_detail = $ims->db->load_row_arr('event_order_detail', 'event_id="'.$info['item_id'].'"');
		$data['ticket_total'] = 0;
		$data['ticket_sold'] = 0;
		$arr_ticket_revenue = array(
			'total' => $ims->lang['user']['ticket_total'], 
			'sold' => $ims->lang['user']['ticket_sold'], 
			'revenue' => $ims->lang['user']['revenue']
		);		
		$arr_price = $ims->func->unserialize($info['arr_price']);
		if($arr_price){
			foreach ($arr_price as $col) {
				$col['title'] = $ims->func->input_editor_decode($col['title']);
				$data['ticket_total'] += $col['num_ticket'];
				$ims->temp_out->assign('col', $col);
				$ims->temp_out->parse("ticket.col_type_head");
			}
			foreach ($arr_ticket_revenue as $key => $type) {
				$row = array();
				$row['name_row'] = $ims->func->input_editor_decode($type);
				foreach ($arr_price as $col) {
					switch ($key) {
						case 'total':
							$col['num'] = $col['num_ticket'];
							break;
						case 'sold':
							$col['num'] = $col['num_ticket'] - $col['num_ticket_remain'];
							break;
						case 'revenue':
							$col['num'] = ($col['num_ticket'] - $col['num_ticket_remain']) * $col['price'];
							$col['num'] = $ims->func->get_price_format($col['num'],0);
							break;
						default:
							break;
					}
					$ims->temp_out->assign('col', $col);
					$ims->temp_out->parse("ticket.row.col_type_body");
				}
				$ims->temp_out->assign('row', $row);
				$ims->temp_out->parse("ticket.row");
			}
			$ims->temp_out->assign('data', $data);
			$ims->temp_out->parse("ticket");
			return $ims->temp_out->text("ticket");
		}
	}

	function do_picture($info = array()){
		global $ims;
		$data = array();
		$arr_picture_revenue = array(
			'revenue' => $ims->lang['user']['revenue']
		);
		$arr_picture_type = array(
			'each' => $ims->lang['user']['picture_each'],
			'album' => $ims->lang['user']['picture_album'],
		);
		foreach ($arr_picture_type as $type) {
			$col = array();
			$col['title'] = $ims->func->input_editor_decode($type);			
			$ims->temp_out->assign('col', $col);
			$ims->temp_out->parse("picture.col_type_head");
		}	
		foreach ($arr_picture_revenue as $key => $type) {
			$row = array();
			$row['name_row'] = $ims->func->input_editor_decode($type);
			foreach ($arr_picture_type as $type) {
				$col = array();
				switch ($key) {
					case 'revenue':
						$col['num'] = 0;
						$col['num'] = $ims->func->get_price_format($col['num'],0);
						break;
					default:
						break;
				}
				$ims->temp_out->assign('col', $col);
				$ims->temp_out->parse("picture.row.col_type_body");
			}
			$ims->temp_out->assign('row', $row);
			$ims->temp_out->parse("picture.row");
		}
		$ims->temp_out->assign('data', $data);
		$ims->temp_out->parse("picture");
		return $ims->temp_out->text("picture");
	}

	function do_chart($info = array()){
		global $ims;
		
		// $search_type = $ims->func->if_isset($ims->input['type']);
		$search_date_begin = $ims->func->if_isset($ims->input['date_begin']);
		$search_date_end = $ims->func->if_isset($ims->input['date_end']);

		$data = array();

		// if(!empty($search_type)){
		// 	switch ($search_type) {
		// 		case 'ticket':
					
		// 			break;				
		// 		default:
		// 			// code...
		// 			break;
		// 	}
		// }

		$data['search_date_begin'] = date('01/m/Y', $info['date_begin']);
		$data['search_date_end'] = date('d/m/Y', $info['date_end']);
		if(!empty($search_date_begin) && !empty($search_date_end)){
			$tmp1 = @explode("/", $search_date_begin);
			$date_begin = @mktime(0, 0, 0, $tmp1[1], $tmp1[0], $tmp1[2]);
			
			$tmp2 = @explode("/", $search_date_end);
			$date_end = @mktime(23, 59, 59, $tmp2[1], $tmp2[0], $tmp2[2]);
			if($search_date_begin && $search_date_end==''){
				$where .= " and date_create >= {$date_begin} ";
			}elseif($search_date_end && $search_date_begin==''){
				$where .= " and date_create <= {$date_end} ";
			}elseif($search_date_begin && $search_date_end){
				$where.=" AND (date_create BETWEEN {$date_begin} AND {$date_end} ) ";
			}
		}		
		//ticket
		$arr_date_ticket = array(
			'01/04' => '5000000',
			'07/04' => '10000000',
			'14/04' => '15000000',
			'21/04' => '10000000',
			'28/04' => '15000000',
			'06/05' => '22000000',
			'12/05' => '25000000',
			'19/05' => '35000000',
		);
		$data['arr_date_ticket'] = array();
		foreach ($arr_date_ticket as $key => $value) {
			$data['arr_date_ticket'][] = array(
				'date' => $key,
				'value' => $ims->func->format_number($value).' '.$ims->lang['global']['unit'],
			);
		}		
		$data['arr_date_ticket'] = base64_encode(json_encode($data['arr_date_ticket']));
		
		//event
		$arr_date_product = array(
			'15/04' => '5000000',
			'20/04' => '18000000',
			'21/04' => '15000000',
			'22/04' => '20000000',
			'28/04' => '12000000',
		);
		$data['arr_date_product'] = array();
		foreach ($arr_date_product as $key => $value) {
			$data['arr_date_product'][] = array(
				'date' => $key,
				'value' => $ims->func->format_number($value).' '.$ims->lang['global']['unit'],
			);
		}				
		$data['arr_date_product'] = base64_encode(json_encode($data['arr_date_product']));

		//picture
		$arr_date_picture = array(
			'08/05' => '2000000',
			'12/05' => '2500000',
			'16/05' => '3500000',
			'20/05' => '5000000',
		);
		$data['arr_date_picture'] = array();
		foreach ($arr_date_picture as $key => $value) {
			$data['arr_date_picture'][] = array(
				'date' => $key,
				'value' => $ims->func->format_number($value).' '.$ims->lang['global']['unit'],
			);
		}		
		$data['arr_date_picture'] = base64_encode(json_encode($data['arr_date_picture']));

		$ims->temp_out->assign('data', $data);
		$ims->temp_out->parse("chart");
		return $ims->temp_out->text("chart");
	}

	function do_percent($info = array()){
		global $ims;

		$data = array();
		$data['total_registed'] = 0;
		$data['participants'] = 0;
		$data['supporters'] = 0;
		$data['link_participants'] = $ims->site_func->get_link('user', $ims->setting['user']['statistic_link']).'/'.$ims->func->link2hex('?detail='.$info['item_id'].'&list=1',6).'/?type=checkin';
		$data['link_supporters'] = $ims->site_func->get_link('user', $ims->setting['user']['statistic_link']).'/'.$ims->func->link2hex('?detail='.$info['item_id'].'&list=1',6).'/?type=team';
		$infoOrder = $ims->load_data->data_table('event_order_detail', 'ticket_code', '*', 'event_id="'.$info['item_id'].'"');
		if($infoOrder){
			$data['total_registed'] = count($infoOrder);
			foreach ($infoOrder as $key => $value) {
				if(!empty($value['is_checkin'])){
					$data['participants']++;
				}
				if(!empty($value['team'])){
					$data['supporters']++;
				}	
			}
		}

		$arr_price = $ims->func->unserialize($info['arr_price']);
		if($arr_price){
            $data['total_ticket'] = 0;
			foreach ($arr_price as $key => $col) {
				$data['total_ticket'] += $col['num_ticket'];
				$col['title'] = $ims->func->input_editor_decode($col['title']);
				$col['participants'] = 0;
				if(!empty($infoOrder[$key]['is_checkin'])){
					$col['participants']++;
				}
				$col['title_participants'] = $col['title'].': <b>'.$col['participants'].'/'.$col['num_ticket'].'</b>';
				$col['percent_participants'] = round($col['participants'] / $col['num_ticket'])*100;
				
				$col['supporters'] = 0;
				if(!empty($infoOrder[$key]['team'])){
					$col['supporters']++;
				}
				$col['title_supporters'] = $col['title'].': <b>'.$col['supporters'].'/'.$col['participants'].'</b>';
				$col['percent_supporters'] = 0;
				if($col['participants'] > 0){
					$col['percent_supporters'] = round($col['supporters'] / $col['participants'])*100;
				}
				$ims->temp_out->assign('col', $col);
				$ims->temp_out->parse("percent.participants");	
				$ims->temp_out->parse("percent.supporters");			
			}
		}
		
		$data['title_participants'] = $ims->lang['user']['ticket_total'].': <b>'.$data['total_registed'].'/'.$data['total_ticket'].'</b>';
		$data['percent_participants'] = round($data['total_registed'] / $data['total_ticket'])*100;

		$data['title_supporters'] = $ims->lang['user']['ticket_total'].': <b>'.$data['supporters'].'/'.$data['total_registed'].'</b>';
		$data['percent_supporters'] = !empty($data['total_registed'])?round($data['supporters'] / $data['total_registed'])*100:0;

		$ims->temp_out->assign('data', $data);
		$ims->temp_out->parse("percent");
		return $ims->temp_out->text("percent");
	}

	function do_table($id = 0){
		global $ims;

		$data = array();
		$data['src'] = $ims->conf['rooturl'].'resources/images/user/';

		$info = $ims->db->load_row('event','is_show=1 and lang="'.$ims->conf['lang_cur'].'" and item_id="'.$id.'"');
		if(!$info){
			$ims->html->redirect_rel($ims->site_func->get_link('user', $ims->setting['user']['event_link']));
		}

		$search_keyword = $ims->func->if_isset($ims->input['search_keyword']);
		$search_team = $ims->func->if_isset($ims->input['search_team']);
		$search_ticket = $ims->func->if_isset($ims->input['search_ticket']);
		$search_checkin = $ims->func->if_isset($ims->input['search_checkin']);
		$is_search = 0;

		$p = $ims->func->if_isset($ims->input["p"], 1);
		$ext = '';
		$where = '';

		$arr_team = $ims->func->unserialize($info['arr_teams']);
		$data['link_editteam'] = $ims->site_func->get_link('user', $ims->setting['user']['statistic_link']).'/'.$ims->func->link2hex('?detail='.$info['item_id'].'&team=1',6);
		if(count($arr_team)){
			$data['link_editteam'] = "#";
			foreach ($arr_team as $key => $row) {
				$row['value'] = $key;
				$ims->temp_out->assign('row', $row);
				$ims->temp_out->parse("table.team");
			}
		}		
		$data['search_team'] = $ims->html->select("search_team", $arr_team, $search_team, " class=\"\"", array("title" => $ims->lang['user']['choose_team']));
		
		$arr_price = $ims->func->unserialize($info['arr_price']);
		$arr_ticket = array();
		if($arr_price){
			foreach ($arr_price as $key => $value) {
				$arr_ticket[$value['title']] = $value;
			}
		}
		$data['search_ticket'] = $ims->html->select("search_ticket", $arr_ticket, $search_ticket, " class=\"\"", array("title" => $ims->lang['user']['choose_ticket']));

		$arr_checkin = array(
			0 => $ims->lang['user']['not_check_in'],
			1 => $ims->lang['user']['checked_in'],
		);
		$data['search_checkin'] = $ims->html->select("search_checkin", $arr_checkin, $search_checkin, " class=\"\"", array("title" => $ims->lang['user']['choose_status_checkin']));

		if(!empty($ims->input['edit'])){
			$data['class'] = "show_edit";
			$ext .= "&edit=1";
		}
		if(!empty($ims->input['type'])){
			switch ($ims->input['type']) {
				case 'checkin':
					$data['active_checkin'] = 'active';
					$where .= " AND is_checkin = 1 ";
					$ext .= "&type=checkin";
					break;
				case 'notcheckin':
					$data['active_notcheckin'] = 'active';
					$where .= " AND is_checkin = 0 ";
					$ext .= "&type=notcheckin";
					break;
				case 'supporters':
					$data['active_supporters'] = 'active';
					$where .= " AND (team != '' OR team != 0) ";
					$ext .= "&type=supporters";
					break;
				case 'buyers':
					$data['active_buyers'] = 'active';
					$ext .= "&type=buyers";
					break;
				default: //registed					
					$data['active_registed'] = 'active';
					$ext .= "&type=registed";
					break;
			}
		}else{
			$data['active_registed'] = 'active';
		}

		if(!empty($search_keyword)){
			$data['search_keyword'] = $search_keyword;
			$arr_tmp = array();
			$arr_key = explode(" ", $search_keyword);
	        foreach ($arr_key as $value) {
	            $value = trim($value);
	            if (!empty($value)) {
	                $value = str_replace(chr(39), chr(34),$value);
	                $arr_tmp[] = "title_search like '%" . $value . "%'";
	                // $arr_tmp['full_name'][] = "LOWER(full_name) like CONCAT('%', CONVERT('" . mb_strtolower($value,'UTF-8') . "', BINARY), '%')";
	                // $arr_tmp['email'][] = "LOWER(email) like CONCAT('%', CONVERT('" . mb_strtolower($value,'UTF-8') . "', BINARY), '%')";
	                // $arr_tmp['phone'][] = "LOWER(phone) like CONCAT('%', CONVERT('" . mb_strtolower($value,'UTF-8') . "', BINARY), '%')";
	                $order .= "(full_name = '".$value."') desc, ";
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
		if(!empty($search_team)){
			$where .= " AND LOWER(team) = '" . mb_strtolower($search_team,'UTF-8') . "' ";
			$ext .= "&search_team=".$search_team;
			$is_search = 1;
		}
		if(!empty($search_ticket)){
			// $where .= " AND LOWER(title) like '%" . mb_strtolower($search_ticket,'UTF-8') . "%' ";
			$where .= " AND LOWER(title) = '" . mb_strtolower($search_ticket,'UTF-8') . "' ";
			$ext .= "&search_ticket=".$search_ticket;
			$is_search = 1;
		}
		if(!empty($search_checkin)){
			$where .= " AND LOWER(is_checkin) = '" . mb_strtolower($search_checkin,'UTF-8') . "' ";
			$ext .= "&search_checkin=".$search_checkin;
			$is_search = 1;
		}
		$data['event_id'] = $info['item_id'];
		$data['info'] = $this->do_info($info);

		$num_total = $ims->db->do_get_num("event_order_detail", 'event_id="'.$info['item_id'].'" '.$where);
		$n = !empty($ims->setting['user']['num_register_list'])?$ims->setting['user']['num_register_list']:10;
        $num_items = ceil($num_total / $n);
        if ($p > $num_items)
            $p = $num_items;
        if ($p < 1)
            $p = 1;
        $start = ($p - 1) * $n;
     	$link_action =  $ims->site_func->get_link('user', $ims->setting['user']['statistic_link']).'/'.$ims->func->link2hex('?detail='.$info['item_id'].'&list=1',6);
        $nav = $ims->site->paginate($link_action, $num_total, $n, $ext, $p);

		$data['total_registed'] = 0;
		$tmp_ticket = array();
		$infoOrder = $ims->load_data->data_table('event_order_detail', 'detail_id', '*', 'event_id="'.$info['item_id'].'" '.$where.' order by date_create desc limit '.$start.','.$n);		

		if($infoOrder){
			$data['total_registed'] = $num_total;
			$i = 0;
			foreach ($infoOrder as $row) {				
				$i++;
				$row['index'] = $i;
				$row['date_create'] = date('d/m/Y', $row['date_create']);
				$row['team'] = !empty($arr_team[$row['team']])?$arr_team[$row['team']]['title']:'';
				$row['date_checkin'] = !empty($row['date_checkin'])?date('d/m/Y H:i', $row['date_checkin']):'';
				$tmp_ticket[] = $row['detail_id'];
				$ims->temp_out->assign('row', $row);
				$ims->temp_out->parse("table.row");
			}
		}
		$data['nav'] = $nav;
		$data['link_createteam'] = $ims->site_func->get_link('user', $ims->setting['user']['statistic_link']).'/'.$ims->func->link2hex('?detail='.$info['item_id'].'&team=1',6);
		$data['link_action'] =  $link_action . "/?p=" . $p . $ext;		
		if(!empty($is_search)){
			$data['link_back'] = '<div class="no_filter"><a href="'.$link_action.'">'.$ims->lang['user']['no_filter'].'</a></div>';
		}
		$wre = 'event_id="'.$info['item_id'].'" and find_in_set(detail_id, "'.implode(',', $tmp_ticket).'")';
		$data['wre'] = $ims->func->encrypt_decrypt('encrypt', $wre, 'excel', 'export');
		$data['excel_title'] = $ims->func->encrypt_decrypt('encrypt', $ims->input['type'], 'excel', 'export');
		$data['link_import_excel'] = $ims->conf['rooturl'].'resources/fileimport.xlsx';
		$ims->temp_out->assign('data', $data);
		$ims->temp_out->parse("table");
		return $ims->temp_out->text("table");
	}

	function do_team($id = 0){
		global $ims;

		$data = array();
		$info = $ims->db->load_row('event','is_show=1 and lang="'.$ims->conf['lang_cur'].'" and item_id="'.$id.'"');
		if(!$info){
			$ims->html->redirect_rel($ims->site_func->get_link('user', $ims->setting['user']['event_link']));
		}
		$data['info'] = $this->do_info($info);
		$data['item_id'] = $id;
		$data['src'] = $ims->conf['rooturl'].'resources/images/user/';
		$data['icon_create'] = $data['src'].'plus.svg';

		$info = $ims->db->load_row('event','is_show=1 and lang="'.$ims->conf['lang_cur'].'" and item_id="'.$id.'"');
		if(!$info){
			$ims->html->redirect_rel($ims->site_func->get_link('user', $ims->setting['user']['event_link']));
		}		
		$step = $ims->func->if_isset($ims->input['step']);
		//step 1
		if(!empty($info['arr_teams'])){
			$data['title_list'] = $ims->lang['user']['list_created'];
			$arr_teams = $ims->func->unserialize($info['arr_teams']);
			$i=0;
			foreach ($arr_teams as $key => $row) {
				$i++;
				$row['index'] = $i;
				$row['key'] = $key;
				$row['item_id'] = $data['item_id'];
				$ims->temp_out->assign('row', $row);
				$ims->temp_out->parse("team.row");
			}
		}

		$data['link_cancel'] = $ims->site_func->get_link('user', $ims->setting['user']['statistic_link']).'/'.$ims->func->link2hex('?detail='.$info['item_id'].'&list=1',6);
		$data['link_save'] = $ims->site_func->get_link('user', $ims->setting['user']['statistic_link']).'/'.$ims->func->link2hex('?detail='.$info['item_id'].'&list=1',6).'/?edit=1';
		$ims->temp_out->assign('data', $data);
		// switch ($step) {
		// 	case '1':
				// $ims->temp_out->parse("team.step1");
		// 		break;
		// 	case '2':
		// 		$ims->temp_out->parse("team.step2");
		// 		break;
		// 	default:
		// 		break;
		// }		
		$ims->temp_out->parse("team");
		return $ims->temp_out->text("team");
	}
  	// End class
}
?>