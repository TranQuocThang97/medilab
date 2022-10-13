<?php
if (!defined('IN_ims')) { die('Access denied'); }
$nts = new sMain();

class sMain {

    var $modules = "product";
    var $action  = "product";

    function __construct() {
        global $ims;
        
        $arrLoad = array(
            'modules'        => $this->modules,
            'action'         => $this->action,
            'template'       => $this->modules,
            'js'             => $this->modules,
            'css'            => $this->modules,
            'use_func'       => "", // Sử dụng func
            'use_navigation' => 0, // Sử dụng navigation
            'required_login' => 1, // Bắt buộc đăng nhập
        );
        $ims->func->loadTemplate($arrLoad);

        require($this->modules . "_func.php");
        $this->modFunc = new productFunc($this);

        $data = array();        
        if (isset($ims->conf['cur_group'])) {
            // Current menu
            $arr_group_nav = (!empty($row["group_nav"])) ? explode(',', $row["group_nav"]) : array();
            foreach ($arr_group_nav as $v) {
                $ims->conf['menu_action'][] = $this->modules.'-group-'.$v;
            }
            // End current menu
            //Make link lang
            $load_lang = $ims->db->load_item_arr("product_group", " group_id='".$ims->conf['cur_group']."' ", "friendly_link, lang");
            foreach ($load_lang as $key => $row_lang) {
                $ims->data['link_lang'][$row_lang['lang']] = $ims->site_func->get_link_lang($row_lang['lang'], $this->modules, $row_lang['friendly_link']);
            }
            //End Make link lang

            $ims->site->get_seo($ims->data['cur_group']);
            $ims->conf["cur_group_nav"] = $ims->data['cur_group']["group_nav"];

            $data['content']    = $this->do_list($ims->data['cur_group'], $ims->func->get_link($ims->data['cur_group']['friendly_link'], ''));
        } else {
            foreach ($ims->data['lang'] as $row_lang) {
                $ims->data['link_lang'][$row_lang['name']] = $ims->site_func->get_link_lang($row_lang['name'], $this->modules);
            }
            $ims->site->get_seo (array(
                'meta_title' => $ims->func->if_isset($ims->setting[$this->modules][$this->action."_meta_title"]),
                'meta_key'   => $ims->func->if_isset($ims->setting[$this->modules][$this->action."_meta_key"]),
                'meta_desc'  => $ims->func->if_isset($ims->setting[$this->modules][$this->action."_meta_desc"])
            ));
            $ims->conf["cur_group"] = 0;
            if(!empty($ims->get['act']) && $ims->get['act'] == 'add'){
                $data['content'] = $this->do_add_product();
            }else{
                $data['content'] = $this->do_list();
            }
        }

        $ims->conf['container_layout'] = 'm';
        $ims->conf["class_full"] = 'product';
        $ims->temp_act->assign('data', $data);
        $ims->temp_act->parse("main");
        $ims->output .= $ims->temp_act->text("main");
    }

    function do_list_bo($info = array(), $link_action='') {
        global $ims;

//        $where = ' and combo_id = 0 ';
        $where = '';
        if (!empty($info)) {
            $where .= " AND (FIND_IN_SET('".$info['group_id']."', group_nav) or FIND_IN_SET('".$info['group_id']."', group_related))";
        }
        $shock = '';
        if(isset($ims->get['fc']) && $ims->get['fc'] == 1){
            $where .= ' and is_focus = 1 ';
            $shock = ' '.$ims->lang['product']['shock_discount'];
        }
        if(isset($ims->get['fc1']) && $ims->get['fc1'] == 1){
            $where .= ' and is_focus1 = 1 ';
            $shock = ' '.$ims->lang['product']['discount_day'];
        }
        $text_search = $ims->func->if_isset($ims->get['keyword']);
        $data['text_search'] = '';
        // Search by title
        $order = '';
        $arr_key = explode(' ', $text_search);
        if (!empty($arr_key)) {
            $arr_tmp = array();
            foreach ($arr_key as $value) {
                $value = trim($value);
                if (!empty($value)) {
                    $arr_tmp[] = "title LIKE '%".$value."%'";
                    $order .= "(title = '".$value."') DESC, ";
                }
            }
            if (count($arr_tmp) > 0) {         
                $where .= " AND (".implode(" AND ", $arr_tmp).")";
            }
        }

        $where = $ims->func->handlebyArray('view_group', 'group_id', $where); // Search by group_id
        $where = $ims->func->handlebyArray('brand', 'brand_id', $where); // Search by brand_id
        $where = $ims->func->handlebyArray('nature', 'arr_nature', $where); // Search by nature
        $where = $ims->func->handlebyArray('tag', 'tag_list', $where); // Search by TAG
        // Search by Rate
        $rate = $ims->func->if_isset($ims->get['rate'], 0);
        if($rate>0) {
            $where .= ' AND num_rate>="'.$rate.'" ';
        }

        // Search by price
        $price = $ims->func->if_isset($ims->input['price']);
        $price_min = $ims->func->if_isset($ims->input['price_min'], 0);     
        $price_max = $ims->func->if_isset($ims->input['price_max'], 0);
        if ($price_min >= 0 && $price_max > 0) {
            $where .= " AND (price_buy BETWEEN ".$price_min." AND ".$price_max.") ";
        }
        if ($price != '') {
            $arr_price = explode('-', $price);
            if (is_array($arr_price)) {
                $where .= " AND ( price_buy BETWEEN ".$arr_price[0]." AND ".$arr_price[1]." )";
            }
        }

        if ($order!="") {
            $order = "CASE WHEN instr(title, '".$text_search."')=0 THEN 1 ELSE 0 END, ".$order."length(title) ,";
        }
        $sort = $ims->func->if_isset($ims->get['sort']);
        if($sort){
            if($sort == 'price-desc'){
                $where .= " ORDER BY price_buy DESC, price DESC";
            }
            elseif($sort == 'price-asc'){
                $where .= " ORDER BY price_buy ASC, price ASC";
            }
            elseif($sort == 'title-asc'){
                $where .= " ORDER BY title ASC";
            }            
            elseif($sort == 'title-desc'){
                $where .= " ORDER BY title DESC";
            }
            elseif($sort == 'stock-desc'){
                $where .= "ORDER BY quantity_sold DESC";
            }
            elseif($sort == 'new'){
                $where .= " ORDER BY date_create DESC";
            }
        } else{
            $where .= " ORDER BY ".$order." show_order DESC, date_create DESC";
        }

        $arr_in = array(
            'link_action' => $link_action,
            'where'       => $where,
            'viewmore_ajax' => 1,
        );

        $total = $ims->db->do_get_num("product", "1 ". $ims->conf['where_lang'] . $where);
        $data = array(
            'title'          => (isset($ims->get['keyword']) && $ims->get['keyword'] != '') ? $ims->lang['global']['search_result'] : (($info) ? $info['title'].$shock : $ims->lang['product']['mod_title'].$shock),
//            'filter_title'   => $ims->lang['product']['filter_title_sm'],
//            'sort'           => $ims->site->box_sort_product_top("sort_product_radio_top"),
//            'box_nature'     => $ims->site->box_sort_nature_left(),
//            'box_origin'    => $ims->site->box_origin(),
//            'sort_mobile'    => $ims->site->box_sort_product_top_ajax(),
//            'filter_product' => $ims->site->box_filter_product(),
            'link_product'   => $ims->site_func->get_link('product'),
            'content'        => $this->modFunc->html_list_item($arr_in),
//            'sort_price'     => $ims->site->box_sort_price_top(),
//            'total'          => $ims->site_func->get_lang('total_product', 'product', array('[num]' => '<span>'.$total.'</span>')),
//            'sort_brand'     => $ims->site->box_sort_trademark_left(),
//            'list_group'     => $this->do_list_group($info),
//            'watched'         => $ims->site->do_watched(),
//            'is_new'         => $ims->site->do_is_focus1_product(),
        );
//        if($data['sort_price'] != '' || $data['sort_brand'] != '' || $data['box_origin'] != ''){
//            $ims->temp_act->parse("box_product.sort_title");
//        }
        if($text_search != ''){
            $data['text_search'] = '<div class="text_search pt-3 pb-3">'.$ims->site_func->get_lang('text_search', 'search', array(
                    '[keyword]' => "<b>".$text_search."</b>"
                )).'</div>';
        }
//        if(!$info){
//            $data['hide_sort'] = 'd-none';
//        }

        $ims->temp_act->assign('CONF', $ims->conf);
        $ims->temp_act->assign('data', $data);
        $ims->temp_act->parse("box_product");
        return $ims->temp_act->text("box_product");
    }
    // End class
    function do_list_group($info){
        global $ims;
        $data = array();

        if($info){
            if($info['group_level'] == 1){
                $result = $ims->db->load_item_arr('product_group', $ims->conf['qr'].' and parent_id = '.$info['group_id'].' order by show_order desc, date_create desc', 'title, friendly_link, picture, is_hot');
                if($result){
                    $data = $result;
                }
            }elseif ($info['group_level'] == 2){
                $result = $ims->db->load_item_arr('product_group', $ims->conf['qr'].' and parent_id = '.$info['parent_id'].' and group_id != '.$info['group_id'].' order by show_order desc, date_create desc', 'title, friendly_link, picture, is_hot');
                if($result){
                    $data = $result;
                }
            }
        }else{
            $result = $ims->db->load_item_arr('product_group', $ims->conf['qr'].' order by show_order desc, date_create desc', 'title, friendly_link, picture, is_hot');
            if($result){
                $data = $result;
            }
        }
        if($data){
            foreach ($data as $row){
                $row['link'] = $ims->func->get_link($row['friendly_link'], '');
//                $row['picture'] = $ims->func->get_src_mod($row['picture'], 70, 70, 1, 0);
                $row['hot'] = ($row['is_hot'] == 1) ? '<div class="hot"><span>HOT</span></div>' : '';
                $ims->temp_act->assign('row', $row);
                $ims->temp_act->parse("list_group.item");
            }
            $ims->temp_act->parse("list_group");
            return $ims->temp_act->text("list_group");
        }
    }

    function do_list(){
        global $ims;
        if(isset($ims->get['trash']) && $ims->get['trash'] == 1){
            $ims->conf['qr'] = ' is_show = 0 and lang = "'.$ims->conf['lang_cur'].'"';
        }

        $where = '';
        $keyword = (isset($ims->input['keyword'])) ? trim($ims->input['keyword']) : '';
        if($keyword) {
            $arr_key = explode(' ', $keyword);
            $arr_title = array();
            $arr_sku = array();
            foreach($arr_key as $value) {
                $value = trim($value);
                if(!empty($value)) {
                    $arr_title[] = "title like '%".$value."%'";
                    $arr_sku[] = "sku like '%".$value."%'";
                }
            }
            if(count($arr_title) > 0 && count($arr_sku) > 0) {
                $where .= ' and (('.implode(' and ', $arr_title).') or ('.implode(' and ', $arr_sku).'))';
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

        $where = ' and user_id = '.$ims->data['user_cur']['user_id'].$where;
        $result = $ims->db->load_item('product', $ims->conf['qr'].$where, 'item_id');
        $nav = '';
        $link_action = $ims->site_func->get_link('product');
        if(!$result){
            $n = $ims->setting['product']['num_list'];
            $p = $ims->func->if_isset($ims->input["p"], 1);
            $num_total = $ims->db->do_get_num('product', $ims->conf['qr'].$where);
            $num_items = ceil($num_total / $n);
            if ($p > $num_items)
                $p = $num_items;
            if ($p < 1)
                $p = 1;
            $start = ($p - 1) * $n;
            $nav = $ims->site->paginate($link_action, $num_total, $n, $ext, $p);

            $result = $ims->db->load_item_arr('product', $ims->conf['qr'].$where.' order by show_order desc, date_update desc LIMIT '.$start.','.$n, 'title, picture, item_id, date_update, item_code');
            $result = array(
                array(
                    'picture' => $ims->conf['rooturl'].'resources/images/tmp/sp1.jpg',
                    'title' => 'Nước mắm hương cá hồi hảo hạng Chinsu 12 độ đạm',
                    'item_code' => 'SS345678',
                    'date_update' => '20:00, 20/08/2022'
                ),array(
                    'picture' => $ims->conf['rooturl'].'resources/images/tmp/sp1.jpg',
                    'title' => 'Nước mắm hương cá hồi hảo hạng Chinsu 12 độ đạm',
                    'item_code' => 'SS345678',
                    'date_update' => '20:00, 20/08/2022'
                ),array(
                    'picture' => $ims->conf['rooturl'].'resources/images/tmp/sp1.jpg',
                    'title' => 'Nước mắm hương cá hồi hảo hạng Chinsu 12 độ đạm',
                    'item_code' => 'SS345678',
                    'date_update' => '20:00, 20/08/2022'
                ),array(
                    'picture' => $ims->conf['rooturl'].'resources/images/tmp/sp1.jpg',
                    'title' => 'Nước mắm hương cá hồi hảo hạng Chinsu 12 độ đạm',
                    'item_code' => 'SS345678',
                    'date_update' => '20:00, 20/08/2022'
                ),
            );
            foreach ($result as $row){
//                $row['picture'] = $ims->func->get_src_mod($row['picture'], 80, 80, 1, 1);
//                $row['item_id'] = $ims->func->base64_encode($row['item_id']);
                $ims->temp_act->assign('row', $row);
                $ims->temp_act->parse("product.item");
            }
        }else{
            $ims->temp_act->parse("product.empty");
        }
        $ims->temp_act->assign('add_link', $link_action.'/?act=add');
        $ims->temp_act->assign('nav', $nav);
        $ims->temp_act->parse("product");
        return $ims->temp_act->text("product");
    }

    function do_add_product(){
        global $ims;
        $list_concern = $ims->db->load_item_arr('product_concern', $ims->conf['qr'].' order by title asc', 'title, item_id');
        if(!empty($list_concern)){
            foreach ($list_concern as $concern){
                $ims->temp_act->assign('concern', $concern);
                $ims->temp_act->parse("add_product.producer");
                $ims->temp_act->parse("add_product.distributor");
            }
        }

        $ims->temp_act->parse("add_product");
        return $ims->temp_act->text("add_product");
    }
}

?>