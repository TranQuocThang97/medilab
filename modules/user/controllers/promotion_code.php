<?php
if (!defined('IN_ims')) { die('Access denied'); }
$nts = new sMain();

class sMain
{
	var $modules = "user";
	var $action  = "promotion_code";
	var $sub 	 = "manage";
	
	/**
		* function __construct ()
		* Khoi tao 
		* Quản lý danh sách mã khuyến mãi
	**/
	function __construct ()
	{
		global $ims;
		$ims->conf['resource'] = $ims->conf['rooturl'].'resources/images/';
        $ims->conf['numshow'] = 8;

		$arrLoad = array(
            'modules'        => $this->modules,
            'action'         => $this->action,
            'template'       => $this->action,
            'js'             => $this->modules,
            'css'            => $this->modules,
            'use_func'       => $this->modules, // Sử dụng func
            'use_navigation' => 0, // Sử dụng navigation
            'required_login' => 1, // Bắt buộc đăng nhập
        );
        $ims->func->loadTemplate($arrLoad);
        $ims->func->include_js ($ims->dir_js.'jquery.copy-to-clipboard.js');
		$data = array();
		$data['content']  = $this->do_manage();
		$data['box_left'] = box_left($this->action);
		$ims->conf["class_full"] = 'user';
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("main");
		$ims->output .=  $ims->temp_act->text("main");
	}
	
	//-----------
	function manage_row($row)
	{
		global $ims;
		
		$output = '';		
		if(!empty($row["picture"])){
			$row["picture"] = '<a class="fancybox-effects-a" title="'.$row["picture"].'" href="'.DIR_UPLOAD.$row["picture"].'">
				'.$ims->func->get_pic_mod($row["picture"], 50, 50, '', 1, 0, array('fix_width'=>1)).'
			</a>';
		}
		switch($row['value_type']){
			case 0:
				$row['code_value'] = '<span class="price_format"><span class="number">'.$row['value'].'</span></span>';
				break;					
			case 1:
				$row['code_value'] = '<span>'.$row['value']."%".'</span>';
				break;					
		}
		if($row['check'] == 0){
			$row['status'] = promotion_status_info (0);			
		}else{
			$row['status'] = promotion_status_info (1);
			$row['class'] = 'disabled';
		}
		if($row['date_end'] < time()){
			$row['status'] = promotion_status_info (1);
			$row['class'] = 'disabled';
		}
		// $row['status'] = ($row['check'] > 0) ? promotion_status_info (1) : promotion_status_info (0);;
		$row['total_min'] = $ims->func->get_price_format($row['total_min']);
		$row['date_create'] = date('d/m/Y',$row['date_create']);
		$row['date_end'] = date('d/m/Y, H:i',$row['date_end']);
		
		$ims->temp_act->assign('row', $row);
		
		$ims->temp_act->parse("manage.row_item");
		$output = $ims->temp_act->text("manage.row_item");
		$ims->temp_act->reset("manage.row_item");
		
		return $output;
	}
	
	//-----------
	function do_manage($is_show=""){
		global $ims;
		
		$err = "";
		
		$p = (isset($ims->input["p"])) ? $ims->input["p"] : 1;
		$search_date_begin = (isset($ims->get["date_begin"])) ? trim($ims->get["date_begin"]) : "";
		$search_date_end = (isset($ims->get["date_end"])) ? trim($ims->get["date_end"]) : "";
		$search_title = (isset($ims->get["search_title"])) ? trim($ims->get["search_title"]) : "";
		
		$where = " ";
		$ext = "";
		$is_search = 0;
		$data = $ims->data['user_cur'];
		$where .= ' where is_show=1 and (type_promotion=0 or find_in_set("'.$data['email'].'",list_email)>0 or find_in_set("'.$data['user_id'].'",list_user)>0)';
		
		if($search_date_begin || $search_date_end ){
			$tmp1 = @explode("/", $search_date_begin);
			$time_begin = @mktime(0, 0, 0, $tmp1[1], $tmp1[0], $tmp1[2]);
			
			$tmp2 = @explode("/", $search_date_end);
			$time_end = @mktime(23, 59, 59, $tmp2[1], $tmp2[0], $tmp2[2]);
			if($search_date_begin && $search_date_end==''){
				$where .= " and date_start >= {$time_begin} ";
			}elseif($search_date_end && $search_date_begin==''){
				$where .= " and date_end <= {$time_end} ";
			}elseif($search_date_begin && $search_date_end){
				$where.=" AND date_start <= {$time_begin} AND {$time_end} <= date_end ";
			}
			$ext.="&date_begin=".$search_date_begin."&date_end=".$search_date_end;
		}
		
		if(!empty($search_title)){
			$where .=" and (promotion_id='$search_title') ";			
			$ext.="&search_title=".$search_title;
		}
    
		$num_total = 0;
		// $res_num = $ims->db->query("select promotion_id from promotion ".$where." ");
			// $num_total = $ims->db->num_rows($res_num);
		$n = $ims->conf["numshow"];//($ims->conf["n_list"]) ? $ims->conf["n_list"] : 20;
		// $num_products = ceil($num_total / $n);
		// if ($p > $num_products)
		//   $p = $num_products;
		// if ($p < 1)
		//   $p = 1;
		// $start = ($p - 1) * $n;
		
		$link_action = $ims->site_func->get_link($this->modules,$ims->setting[$this->modules]["promotion_code_link"]);
		
		// $where .= " order by date_create DESC";	

   		// $sql = "select * from promotion ".$where." limit $start,$n";    	
    	// echo $sql;
		// print_arr($ext);
		// $nav = $ims->site->paginate ($link_action, $num_total, $n, $ext, $p);
		
		// $result = $ims->db->query($sql);
    	// $i = 0;

		$arr_filter = array(
			'all' => $ims->lang['user']['promo_code_all'],
            'new' => $ims->lang['user']['promo_code_new'],
            'old' => $ims->lang['user']['promo_code_old'],
        );
        
		$filter = !empty($ims->get['filter'])?trim($ims->get['filter']):'';
		$f = !empty($filter)?"&filter=".$filter:'';

		foreach ($arr_filter as $k => $v) {
            $row_f = array();
            $row_f['title'] = $v;
            $row_f['link'] = $link_action.'?filter='.$k.$ext;
            if($k == 'all'){
            	$row_f['link'] = $link_action;

            }elseif($k == 'new'){
            	$text = (isset($ims->input["search_title"])) ? '&search_title='.trim($ims->input["search_title"]) : "";
            	$row_f['link'] = $link_action.'?filter='.$k.$text;
            }
            if(isset($ims->get['filter']) && $k == trim($ims->get['filter'])){                
                $row_f['active'] = "active";
            }elseif(!isset($ims->get['filter']) && $k == "all"){
            	$row_f['active'] = "active";
            }

            $ims->temp_act->assign("row", $row_f);
            $ims->temp_act->parse('manage.row_filter');
        }   
    	
    	$token_login = explode(",",$ims->data["user_cur"]["token_login"]);        
        $url_api = $ims->conf['rooturl'].'restfulapi/v1/staging/api.php/getPromotionUser?user='.$token_login[0].'&numshow='.$n.'&p='.$p.'&filter='.$filter;
        $token = $ims->site_func->getRestfulToken();       
        $result = $ims->site_func->sendPostData($url_api, array(), 'get', 0, $token);                
        $result = json_decode($result, true);        
        if (!empty($result) && $result["code"]==200) {            
            // print_arr($result);
            // $p = $result["page"];
            $ext = $f;
            $num_total = $result["total"];
            $num_items = ceil($num_total / $n);
            if ($p > $num_items)
                $p = $num_items;
            if ($p < 1)
                $p = 1;
            $start = ($p - 1) * $n;            
            $data['nav'] = $ims->site->paginate($link_action, $num_total, $n, $ext, $p);

            $arr_promotion = $result["data"];
            if(count($arr_promotion)>0){
	            foreach ($arr_promotion as $row) {	            	
					$row['check']=0;
					$row['short'] = $ims->func->input_editor_decode($row['short']);
					$row['bg'] = $ims->conf['resource'].'user/promotion-bg.png';
					$row['type'] = $ims->lang['user']['promo_'.$row['type_promotion']];
					switch($row['value_type']){
						case 0:
							$row['code_value'] = $ims->lang["user"]["decreate"].' <span class="price_format"><span class="number">'.$row['value'].'</span></span>';
							break;					
						case 1:
							$row['code_value'] = $ims->lang["user"]["decreate"].' <span>'.$row['value']."%".'</span>';
							break;					
					}
					if($row['type_promotion'] == 'apply_freeship'){
						$row['code_value'] = 'FREESHIP';
					}
					if(!empty($row['total_min'])){
						$row['promotion_condition'] = $ims->site_func->get_lang('promotion_condition','user',array(
							'[total_min]' => '<span class="price_format"><span class="number">'.$row['total_min'].'</span></span>',
						));
					}
					if(!empty($row['value_max'])){
						$row['promotion_condition2'] = $ims->site_func->get_lang('promotion_condition2','user',array(
							'[value_max]' => '<span class="price_format"><span class="number">'.$row['value_max'].'</span></span>',
						));
					}
					$row['count_num_use'] = $ims->site_func->get_lang('promotion_num_use','user',array(
						'[num_use]' => '<quote>'.$row['num_use'],
						'[max_use]' => $row['max_use'].'</quote>',
					));
					$row['promotion_expire'] = $ims->site_func->get_lang('promotion_expire','user',array(
						'[date_begin]' => date('d/m/Y H:i',$row['date_start']),
						'[date_end]' => date('d/m/Y H:i',$row['date_end']),
					));
					$row['date_expire'] = date('d/m/Y  H:i',$row['date_end']);
					// if($row['type_promotion'] == 'apply_user' || $row['type_promotion'] == 'apply_email'){
					// 	$row['check'] = $ims->db->do_get_num('promotion_log','is_show=1 and promotion_id="'.$row['promotion_id'].'" and user_id="'.$ims->data['user_cur']['user_id'].'"');
					// }else{
						// $row['check'] = $row['num_use']<$row['max_use']?0:1;
					// }
					$row['class_num_use'] = '';
					$row['class_date'] = '';
					$row['valid'] = 'bg-success';
					if($row['num_use']>=$row['max_use'] || $row['date_end']<time()){
						$row['class_date'] = 'text-dark';
						$row["class"] = "disabled";
						$row['valid'] = 'bg-danger';
						if($row['num_use']>=$row['max_use']){
							$row['note'] = $ims->lang['user']['promotion_error'];
							$row['class_num_use'] = 'invalid';
						}
						if($row['date_end']<time()){
							$row['note'] = $ims->lang['user']['promotion_error1'];
						}
					}

					$ims->temp_act->assign("row",$row);
					$ims->temp_act->parse("manage.row_item");					
				}
			}else{				
				$ims->temp_act->assign('data', array("mess"=>$ims->lang["user"]["no_promotion_code"]));
				$ims->temp_act->parse("manage.row_empty");
			}
		}
		
		// $data['nav'] = $nav;
		$data['err'] = $err;
		
		$data['link_action_search'] = $link_action;
		$data['link_action'] = $link_action."&p=".$p.$ext;
		
		$data['search_date_begin'] = $search_date_begin;
		$data['search_date_end'] = $search_date_end;
		$data['search_title'] = $search_title;
		$data['form_search_class'] = ($is_search == 1) ? ' expand' : '';
		
		$data['page_title'] = $ims->conf["meta_title"];
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("manage");
		return $ims->temp_act->text("manage");
	}
  	// End class
}
?>