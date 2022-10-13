<?php
if (! defined('IN_ims')) {
  	die('Access denied');
}
$nts = new sMain();

class sMain
{
	var $modules = "user";
	var $action = "deeplink_statistics";
    var $template  = "deeplink";
	var $sub = "manage";
	
	/**
	* function __construct ()
	* Khoi tao 
	**/
	function __construct ()
	{
		global $ims;
		
		$arrLoad = array(
			'modules' 		 => $this->modules,
			'action'  		 => $this->action,
			'template'  	 => $this->template,
			'js'  	 		 => $this->modules,
			'css'  	 		 => $this->modules,
			'use_func'  	 => $this->modules, // Sử dụng func
            'use_navigation' => 0, // Sử dụng navigation
			'required_login' => 1, // Bắt buộc đăng nhập
		);
        $ims->func->loadTemplate($arrLoad);
		
		$data = array();
		
		$data['content'] = $this->do_main();
		$data['box_left'] = box_left($this->action);
		//$data['box_column'] = box_column();
	    $ims->conf['container_layout'] = 'm';
	    $ims->conf['user_control'] = 'deeplink_statistics';
	    $ims->conf['class_full'] = ' deeplink_statistics';
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("main");
		$ims->output .=  $ims->temp_act->text("main");
	}
	
	function get_status_order_by_list_string ($string) {
        global $ims;
        
        $status = 0;

        $ims->setting_ordering['product_order_status'] = $ims->load_data->data_table(
            'product_order_status', 
            'item_id', '*', 
            "lang='".$ims->conf['lang_cur']."' ORDER BY show_order DESC, date_create DESC", array()
        );
        $status_access = (isset($ims->setting_ordering['product_order_status'])) ? $ims->setting_ordering['product_order_status'] : array();
        foreach ($status_access as $key => $value) {
            $list_status_string = explode(',', $value['list_status_string']);
            if (in_array($string, $list_status_string)) {
                $status = $value['item_id'];
            }
        }
        return $status;
    }

	//-----------
	function do_main()
	{
		global $ims;

        $data = array();
        $where = '';
        $ext = '';
        $search_date_begin = (isset($ims->input["search_date_begin"])) ? $ims->input["search_date_begin"] : "";
        $search_date_end = (isset($ims->input["search_date_end"])) ? $ims->input["search_date_end"] : "";
        if($search_date_begin || $search_date_end ){
            $tmp1 = @explode("/", $search_date_begin);
            $time_begin = @mktime(0, 0, 0, $tmp1[1], $tmp1[0], $tmp1[2]);

            $tmp2 = @explode("/", $search_date_end);
            $time_end = @mktime(23, 59, 59, $tmp2[1], $tmp2[0], $tmp2[2]);

            $where.=" AND (date_update BETWEEN {$time_begin} AND {$time_end} ) ";
            $ext.="&date_begin=".$search_date_begin."&date_end=".$search_date_end;
            $is_search = 1;
        }else{
        	$firstdate = $ims->func->time_str2int(date('01/m/Y', time()), 'd/m/Y');
			$date = strtotime(date("Y-m-t", time() ));
			$day = date("d/m/Y", $date);
			$lastdate = $ims->func->time_str2int($day, 'd/m/Y');
            $where .= ' AND (o.date_create>'.$firstdate.' AND o.date_create<'.$lastdate.') ';
        }
        $complete = $ims->db->load_item('product_order_status','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and is_complete=1','item_id');

        $arr_deep = $ims->db->load_item_arr('product_order o inner join product_order_detail od',' o.deeplink_user = '.$ims->data["user_cur"]["user_id"].' and o.order_id=od.order_id and o.is_status='.$complete.' '.$where.' GROUP BY o.order_id ORDER BY date_create desc','od.type_id as product_id, o.*');
        //$this->get_status_order_by_list_string(6)
        $data['total_offer_by_month'] = 0;
        $data['month_cur'] = date('m',time());
        // print_r($arr_deep);
        if ($arr_deep) {
            $i = 0;
            foreach ($arr_deep as $row) {
                $i++;

                $row['stt'] = $i;
                $product = $ims->db->load_row('product',' item_id ='.$row["product_id"].' and is_show=1  and lang = "'.$ims->conf["lang_cur"].'" ORDER BY show_order desc, date_create desc','title,friendly_link');
                $row['product_link'] = $ims->site_func->get_link('product',$product['friendly_link']);
                $row['product_name'] = $product['title'];
                $row['product_name_short'] = $ims->func->short($product['title'],50);
                //$row['total_offer'] = number_format($row['total_offer']);
                $deeplink = $ims->db->load_row('user_deeplink',' id = '.$row["deeplink_id"].' ','*');
                $row['deeplink_code'] = $deeplink['short_code'];
                $row['deeplink'] = $ims->conf['rooturl'].$deeplink['short_code'];
                $row['deeplink_total'] = $row['deeplink_total']*$ims->setting['product']['wcoin_to_money'];
                if ($row['is_show'] ==1){
                    $row['status'] = 'Đã nhận';
                    $data['total_offer_by_month'] += $row['deeplink_total'];
                }else{
                    $row['status'] = 'Chưa nhận';
                }                
                $row['link_order'] = $ims->site_func->get_link('user',$ims->setting['user']['ordering_link']).'/'.$row['order_code'];//.'?by_phone='.$row['o_phone']
                $row['date_create'] = date('H:i d/m/Y ',$row['date_create']);
                $ims->temp_act->assign('row', $row);
                $ims->temp_act->parse("deeplink_statistics.row");
            }
        }
        if($search_date_begin || $search_date_end ){
            $data['curdeeplink_statistics'] = 'Tổng hoa hồng từ <b> '.$search_date_begin.'</b> đến <b>'.$search_date_end.'</b>';
        }else{
            $data['curdeeplink_statistics'] = $ims->lang['user']['total_offer_by_month'];
        }
        $data['page_title'] = $ims->setting["user"]["deeplink_statistics_meta_title"];

        $ims->func->include_js_content("imsUser.add_deeplink('form_deeplink')");


		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("deeplink_statistics");
		return  $ims->temp_act->text("deeplink_statistics");
		// return $ims->html->temp_box('box_main',array('content' => $output,'title'=> $ims->setting["user"]["deeplink_statistics_meta_title"]));

	}

  // end class
}
?>