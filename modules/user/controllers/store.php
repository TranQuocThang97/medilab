<?php
if (!defined('IN_ims')) { die('Access denied'); }
$nts = new sMain();

class sMain
{
	var $modules = "user";
	var $action  = "store";
	var $sub 	 = "manage";
	var $template = "store";
	
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
			'template'  	 => $this->template,
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

        $user_type = explode(',', $ims->data['user_cur']['user_type']);
        if(!in_array('organizer', $user_type)){
            require_once ($ims->conf["rootpath"]."404.php");die;
        }

		$data = array();
        $cur_url = $ims->func->get_id_page($ims->conf['cur_act_url']);
        if(!empty($cur_url['create_order']) && $cur_url['create_order'] == $ims->data['user_cur']['user_id']){
            $data['content'] = $this->do_create_order();
        }else{
            $data['content'] = $this->do_main();
        }
		$data['box_left'] = box_left($this->action);
		
		$ims->conf["class_full"] = 'user';
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("main");
		$ims->output .=  $ims->temp_act->text("main");
	}
	
	function do_main (){
		global $ims;
        $where_search = '';
        $content = '';

		$link = $ims->site_func->get_link('user', $ims->setting['user']['store_link']);
        $top_page = array(
            'box_search' => $this->box_search(),
            'right' => $this->right_top_page(),
        );
        if(isset($ims->get['store']) && $ims->get['store'] != ''){
            $store_item = $ims->func->base64_decode($ims->get['store']);
            $where_search .= ' and find_in_set('.$store_item.', store_id) ';
            $top_page['title'] = $ims->lang['user']['product_of_store'];
        }else{
            $cur_product = $cur_store = '';
            if(isset($ims->get['act']) && $ims->get['act'] == 'product'){
                $cur_product = 'class="current"';
            }else{
                $cur_store = 'class="current"';
            }
            $top_page['title'] = '<a href="'.$link.'" '.$cur_store.'>'.$ims->lang['user']['store'].'</a><a href="'.$link.'/?act=product" '.$cur_product.'>'.$ims->lang['user']['product'].'</a>';
        }

        $trash = '/?trash=1';
        if(isset($ims->get['store']) && $ims->get['store'] != ''){
            $link .= '/?store='.$ims->get['store'];
            $trash = '&trash=1';
        }elseif (isset($ims->get['act']) && $ims->get['act'] == 'product'){
            $link .= '/?act=product';
            $trash = '&trash=1';
        }
        $top_page['list_link'] = $link;
        $top_page['trash_link'] = $link.$trash;

        if(isset($ims->get['trash']) && $ims->get['trash'] == 1){
            $top_page['trash_cur'] = 'class="current"';
        }else{
            $top_page['list_cur'] = 'class="current"';
        }

        $ims->temp_act->assign("top_page", $top_page);
        $ims->temp_act->parse("top_page");
        $content .= $ims->temp_act->text("top_page");

        $keyword = (isset($ims->input['keyword'])) ? trim($ims->input['keyword']) : '';
        if($keyword) {
            $arr_key = explode(' ', $keyword);
            $arr_tmp = array();
            foreach($arr_key as $value) {
                $value = trim($value);
                if(!empty($value)) {
                    $arr_tmp[] = "title like '%".$value."%'";
                }
            }
            if(count($arr_tmp) > 0) {
                $where_search .= " and (".implode(" and ",$arr_tmp).")";
            }
        }

        parse_str($_SERVER['QUERY_STRING'], $pr);
        $pr_tmp = array();
        foreach ($pr as $k => $v){
            if($v == ''){
                unset($pr[$k]);
            }else{
                $pr_tmp[] = $k.'='.$v;
            }
        }
        $param = implode('&', $pr_tmp);
        $ext = ($param != '') ? '&'.$param : '';
        if(isset($ims->get['p'])){
            $pos_p =  strpos($ext, 'p=');
            $exclude_p = ($pos_p == 0) ? 'p='.$ims->get['p'] : '&p='.$ims->get['p'];
            $ext = str_replace($exclude_p, '', $ext);
        }
        if((isset($ims->get['act']) && $ims->get['act'] == 'product') || (isset($ims->get['store']) && $ims->get['store'] != '')){
            $content .= $this->do_product($where_search, $ext);
        }else{
            $content .= $this->do_store($where_search, $ext);
        }
        return $content;
	}
	function box_search(){
	    global $ims;
        $action = '';
        if(isset($ims->get['act']) && $ims->get['act'] == 'product'){
            $action = '<input type="hidden" name="act" value="product">';
        }elseif(isset($ims->get['store']) && $ims->get['store'] != ''){
            $action = '<input type="hidden" name="store" value="'.$ims->get['store'].'">';
        }

        if((isset($ims->get['act']) && $ims->get['act'] == 'product') || (isset($ims->get['store']) && $ims->get['store'] != '')){
            $list_option = array(
                array(
                    'value' => '',
                    'title' => $ims->lang['user']['product_price']
                ),
                array(
                    'value' => 'asc',
                    'title' => $ims->lang['user']['asc']
                ),
                array(
                    'value' => 'desc',
                    'title' => $ims->lang['user']['desc']
                ),
            );
            foreach ($list_option as $option){
                if(isset($ims->get['price']) && $ims->get['price'] == $option['value']){
                    $option['selected'] = 'selected';
                }
                $ims->temp_act->assign('option', $option);
                $ims->temp_act->parse("box_search_top_page.price.option");
            }
            $ims->temp_act->parse("box_search_top_page.price");
        }
        $keyword = (isset($ims->input['keyword'])) ? trim($ims->input['keyword']) : '';

        $ims->temp_act->assign('action', $action);
        $ims->temp_act->assign('keyword', $keyword);
        $ims->temp_act->parse("box_search_top_page");
        return $ims->temp_act->text("box_search_top_page");
    }
    function right_top_page(){
	    global $ims;
	    $data = array(
	        'scan_link' => '',
        );
	    $data['create_order_link'] = $ims->site_func->get_link('user', $ims->setting['user']['store_link']).'/'.$ims->func->link2hex('create_order='.$ims->data['user_cur']['user_id'], 4);
        $ims->temp_act->assign("data", $data);
        $ims->temp_act->parse("right_top_page.create_order");

        if((isset($ims->get['act']) && $ims->get['act'] == 'product') || (isset($ims->get['store']) && $ims->get['store'] != '')){
            $data['button2'] = '<div class="item add add_product"><a><img src="'.$ims->conf['rooturl'].'resources/images/use/add1.svg" alt="">'.$ims->lang['user']['add_product'].'</a></div>';
        }else{
            $data['button2'] = '<div class="item add add_store"><a><img src="'.$ims->conf['rooturl'].'resources/images/use/add1.svg" alt="">'.$ims->lang['user']['add_store'].'</a></div>';
        }

        $ims->temp_act->assign("data", $data);
        $ims->temp_act->parse("right_top_page");
        return $ims->temp_act->text("right_top_page");
    }

    function do_store($where_search, $ext){
	    global $ims;
        if(isset($ims->get['trash']) && $ims->get['trash'] == 1){
            $ims->conf['qr'] = ' is_show = 0 and lang = "'.$ims->conf['lang_cur'].'"';
        }

	    $where = ' and user_id = '.$ims->data['user_cur']['user_id'].$where_search;
        $result = $ims->db->load_item('event_store', $ims->conf['qr'].$where, 'item_id');
        $nav = '';
        if($result){
            $n = 10;
            $p = $ims->func->if_isset($ims->input["p"], 1);
            $num_total = $ims->db->do_get_num('event_store', $ims->conf['qr'].$where);
            $num_items = ceil($num_total / $n);
            if ($p > $num_items)
                $p = $num_items;
            if ($p < 1)
                $p = 1;
            $start = ($p - 1) * $n;
            $link_action = $ims->site_func->get_link('user', $ims->setting['user']['store_link']);
            $nav = $ims->site->paginate($link_action, $num_total, $n, $ext, $p);

            $result = $ims->db->load_item_arr('event_store', $ims->conf['qr'].$where.' order by show_order desc, date_create desc LIMIT '.$start.','.$n, 'title, picture, item_id');
            $i = 1;
            foreach ($result as $row){
                $row['picture'] = $ims->func->get_src_mod($row['picture'], 87, 87, 1, 1);
                $row['num_product'] = $ims->db->do_count('event_product', 'is_show = 1 and lang = "'.$ims->conf['lang_cur'].'" and find_in_set('.$row['item_id'].', store_id)', 'item_id');
                $row['item_id'] = $ims->func->base64_encode($row['item_id']);
                $row['link'] = $ims->site_func->get_link('user', $ims->setting['user']['store_link']).'/?store='.$row['item_id'];
                $row['index'] = $i;
                if(isset($ims->get['trash']) && $ims->get['trash'] == 1){
                    $row['action'] = '<li class="restore">'.$ims->lang['user']['restore'].'</li>';
                }else{
                    $row['action'] = '<li class="edit">'.$ims->lang['user']['edit'].'</li><li class="add_product">'.$ims->lang['user']['add_product_to_store'].'</li><li class="delete">'.$ims->lang['user']['delete'].'</li>';
                }
                $i++;
                $ims->temp_act->assign('row', $row);
                $ims->temp_act->parse("store.item");
            }
        }else{
            $ims->temp_act->parse("store.empty");
        }
        $ims->temp_act->assign('nav', $nav);
        $ims->temp_act->parse("store");
        return $ims->temp_act->text("store");
    }
    function do_product($where_search, $ext){
	    global $ims;
        $where = ' and user_id = '.$ims->data['user_cur']['user_id'].$where_search;

        if(isset($ims->get['trash']) && $ims->get['trash'] == 1){
            $ims->conf['qr'] = ' is_show = 0 and lang = "'.$ims->conf['lang_cur'].'"';
        }

        $result = $ims->db->load_item('event_product', $ims->conf['qr'].$where, 'item_id');
        $nav = '';
        if($result){
            $order_by = ' order by show_order desc, date_create desc';
            if(isset($ims->get['price']) && $ims->get['price'] != ''){
                if($ims->get['price'] == 'asc'){
                    $order_by = ' order by price asc';
                }elseif ($ims->get['price'] == 'desc'){
                    $order_by = ' order by price desc';
                }
            }
            $where .= $order_by;

            $n = 10;
            $p = $ims->func->if_isset($ims->input["p"], 1);
            $num_total = $ims->db->do_get_num('event_product', $ims->conf['qr'].$where);
            $num_items = ceil($num_total / $n);
            if ($p > $num_items)
                $p = $num_items;
            if ($p < 1)
                $p = 1;
            $start = ($p - 1) * $n;
            $link_action = $ims->site_func->get_link('user', $ims->setting['user']['store_link']);
            $nav = $ims->site->paginate($link_action, $num_total, $n, $ext, $p);

            $result = $ims->db->load_item_arr('event_product', $ims->conf['qr'].$where.' LIMIT '.$start.','.$n, 'title, title1, picture, item_id, num_item, num_sold, price');
            $i = 1;
            foreach ($result as $row){
                $row['picture'] = $ims->func->get_src_mod($row['picture'], 87, 87, 1, 1);
                $row['num_product'] = $row['num_item'] - $row['num_sold'];
                $row['price'] = number_format($row['price'], 0, ',', '.');
                $row['item_id'] = $ims->func->base64_encode($row['item_id']);
                $row['index'] = $i;
                $row['title1'] = ($row['title1'] != '') ? $row['title1'].':&nbsp' : '';
                if(isset($ims->get['trash']) && $ims->get['trash'] == 1){
                    $row['action'] = '<li class="restore">'.$ims->lang['user']['restore'].'</li>';
                }else{
                    $row['action'] = '<li class="edit">'.$ims->lang['user']['edit'].'</li><li class="delete">'.$ims->lang['user']['delete'].'</li>';
                }
                $i++;
                $ims->temp_act->assign('row', $row);
                $ims->temp_act->parse("product.item");
            }
        }else{
            $ims->temp_act->parse("product.empty");
        }

        $ims->temp_act->assign('nav', $nav);
        $ims->temp_act->parse("product");
        return $ims->temp_act->text("product");
    }

    function do_create_order(){
	    global $ims;

	    $list_event = $ims->db->load_item_arr('event', $ims->conf['qr'].' and user_id = '.$ims->data['user_cur']['user_id'].' order by date_create desc', 'title, title1, item_id');
	    $event_first = 0;
	    if($list_event){
	        $i = 0;
	        foreach ($list_event as $event){
	            $i++;
	            $event['title'] = ($event['title1'] != '') ? $event['title1'].':&nbsp'.$event['title'] : $event['title'];
	            if($i == 1){
                    $event_first = $event['item_id'];
                }
                $event['item_id'] = $ims->func->base64_encode($event['item_id']);
                $ims->temp_act->assign('event', $event);
                $ims->temp_act->parse('create_order.event');
            }
        }else{
            $event = array(
                'title' => $ims->lang['user']['empty_event_select']
            );
            $ims->temp_act->assign('event', $event);
            $ims->temp_act->parse('create_order.event');
        }
	    if($event_first){
            $list_order = $ims->db->load_item_arr('event_order', 'event_id = '.$event_first.' and is_status NOT IN(17,29,31)', 'order_id');
            if($list_order){
                $list_order_tmp = array();
                foreach ($list_order as $od){
                    $list_order_tmp[] = $od['order_id'];
                }
                $list_order = implode(',', $list_order_tmp);
                $list_register = $ims->db->load_item_arr('event_order_detail', 'event_id = '.$event_first.' and order_id IN('.$list_order.') order by full_name asc', 'DISTINCT email, full_name, detail_id');
                if($list_register){
                    foreach ($list_register as $user){
                        $user['detail_id'] = $ims->func->base64_encode($user['detail_id']);
                        $ims->temp_act->assign('user', $user);
                        $ims->temp_act->parse('create_order.user');
                    }
                }
            }
        }
	    $list_payment = $ims->db->load_item('event_setting', $ims->conf['qr'].'and setting_key = "payment_methods"', 'setting_value');
	    if($list_payment){
            $list_payment = explode(',', $list_payment);
            foreach ($list_payment as $payment){
                $payment = trim($payment);
                $ims->temp_act->assign('payment', $payment);
                $ims->temp_act->parse('create_order.total.payment.item');
            }
            $ims->temp_act->assign('total', '0 '.$ims->lang['global']['unit']);
            $ims->temp_act->parse('create_order.empty');
            $ims->temp_act->parse('create_order.total.payment');
            $ims->temp_act->parse('create_order.total');
        }
        $ims->temp_act->parse('create_order.info_user');
        $ims->temp_act->parse('create_order');
        return $ims->temp_act->text('create_order');
    }
  	// End class
}
?>