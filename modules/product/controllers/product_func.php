<?php
if (!defined('IN_ims')) { die('Access denied'); }

class productFunc {

    public $modules     = "product";
    public $parent      = null;
    public $parent_mod  = "product";
    public $parent_act  = "product";
    public $temp_act    = "";

    public function __construct($parent = null) {
        global $ims;
        $this->parent = $parent;
        $this->parent_mod = $this->parent_property('modules');
        $this->parent_act = $this->parent_property('action');
        $this->temp_act   = $this->parent_property('temp_act');
        $ims->func->include_css($ims->func->dirModules($this->modules, 'assets')."css/func.css");
        $ims->call->mfunc_temp($this);
        return true;
    }

    public function parent_property($property) {
        global $ims;
        $output = false;
        if ($this->parent) {
            if (property_exists($this->parent, $property)) {
                $output = $this->parent->$property;
            }
        }
        return $output;
    }

    public function parent_method($method, $param_arr = array()) {
        global $ims;
        $output = false;
        if (method_exists($this->parent, $method)) {
            $output = call_user_func_array(array($this->parent, $method), $param_arr);
        }
        return $output;
    }

    // where_product
    function where_product($type = '') {
        global $ims;

        $type = ($type) ? $type : $this->modules;

        return $ims->site_func->whereLoaded($type);
    }

    // product_loaded
    function product_loaded($id = 0, $type = '') {
        global $ims;

        $type = ($type) ? $type : $this->modules;

        return array();
        return $ims->site_func->addLoaded($type, $id);
    }

    //-----------get_brand_name
    function get_brand_name($brand_id, $type = 'none') {
        global $ims;
        $output = '';
        $brand = $ims->db->load_row($this->modules . "_brand", "brand_id='".$brand_id."'".$ims->conf["where_lang"]);
        if (!empty($brand)) {
            switch ($type) {
                case "link":
                    $brand['friendly_link'] = '?brand='.$brand['brand_id'];
                    $link = $ims->site_func->get_link($this->modules).$brand['friendly_link'];
                    $output = '<a href="' . $link . '">' . $brand['title'] . '</a>';
                    break;
                default:
                    $output = $brand['title'];
                    break;
            }
        }
        return $output;
    }

    //-----------get_status_pic
    function get_status_pic_delete($status_id, $type = 'none') {
        global $ims;

        $arr_status = $ims->load_data->data_table($this->modules . '_status', 'status_id', 'status_id,picture,title', " is_show=1 and lang='" . $ims->conf['lang_cur'] . "'");
        $pic_w = 70;
        $pic_h = 70;

        $output = '';
        if (isset($arr_status[$status_id])) {
            $row = $arr_status[$status_id];
            $row["picture"] = $ims->func->get_src_mod($row["picture"], $pic_w, $pic_h, 1, 1);
            switch ($type) {
                /* case "link":
                  $link = $ims->site_func->get_link ($this->modules,'thuong-hieu',$row['friendly_link']);
                  $output = '<a href="'.$link.'">'.$row['picture'].'</a>';
                  break; */
                default:
                    $output = '<div class="status_pic"><img src="' . $row['picture'] . '" alt="' . $row["title"] . '" /></div>';
                    break;
            }
        }

        return $output;
    }

    //-----------list_color
    function list_color_delete($item_is, $list_color = '', $cur = 0) {
        global $ims;

        $output = '';

        $arrrow = $ims->db->row(array(
            'select' => array('title', 'friendly_link'),
            'from' => array($this->modules . '_color'),
            'where' => array(
                array('=', 'is_show', 1),
                array('=', 'lang', "'" . $ims->conf['lang_cur'] . "'"),
                array('find_in_set', 'color_id', "'" . $list_color . "'")
            ),
            'orderby' => array('show_order' => 'desc', 'date_update' => 'desc')
        ));
        if (is_array($arrrow) && count($arrrow)) {
            $output = '<ul class="list_none list_input_color list_input">';
            $i = 0;
            foreach ($arrrow as $row) {
                $i++;
                if ($cur > 0) {
                    $checked_li = ($row['color_id'] == $cur) ? ' class="checked"' : '';
                    $checked = ($row['color_id'] == $cur) ? ' checked="checked"' : '';
                } else {
                    $checked_li = ($i == 1) ? ' class="checked"' : '';
                    $checked = ($i == 1) ? ' checked="checked"' : '';
                }

                $output .= '<li ' . $checked_li . '><input type="radio" id="color_' . $row['color_id'] . '" name="color" value="' . $row['color_id'] . '" ' . $checked . ' /><label for="color_' . $row['color_id'] . '" class="label_view">' . $row['title'] . '</label></li>';
            }
            $output .= '</ul><div class="clear"></div>';

            $ims->func->include_js_content('list_input_color();');
        }

        return $output;
    }

    //-----------list_size
    function list_size_delete($item_is, $list_size = '', $cur = 0) {
        global $ims;

        $output = '';

        $arrrow = $ims->db->row(array(
            'select' => array('title', 'friendly_link'),
            'from' => array($this->modules . '_size'),
            'where' => array(
                array('=', 'is_show', 1),
                array('=', 'lang', "'" . $ims->conf['lang_cur'] . "'"),
                array('find_in_set', 'size_id', "'" . $list_size . "'")
            ),
            'orderby' => array('show_order' => 'desc', 'date_update' => 'desc')
        ));
        if (is_array($arrrow) && count($arrrow)) {
            $output = '<ul class="list_none list_input_size list_input">';
            $i = 0;
            foreach ($arrrow as $row) {
                $i++;
                if ($cur > 0) {
                    $checked_li = ($row['size_id'] == $cur) ? ' class="checked"' : '';
                    $checked = ($row['size_id'] == $cur) ? ' checked="checked"' : '';
                } else {
                    $checked_li = ($i == 1) ? ' class="checked"' : '';
                    $checked = ($i == 1) ? ' checked="checked"' : '';
                }

                $output .= '<li ' . $checked_li . '><input type="radio" id="size_' . $row['size_id'] . '" name="size" value="' . $row['size_id'] . '" ' . $checked . ' /><label for="size_' . $row['size_id'] . '" class="label_view">' . $row['title'] . '</label></li>';
            }
            $output .= '</ul><div class="clear"></div>';

            $ims->func->include_js_content('list_input_size();');
        }

        return $output;
    }

    //-----------list_combine
    function list_combine_delete($item_id, $cur = 0) {
        global $ims;

        $output = '';

        $arr_color = $ims->load_data->data_table($this->modules . '_color', 'color_id', 'color_id,color,title', " is_show=1 and lang='" . $ims->conf['lang_cur'] . "' order by show_order desc, date_create desc");
        $arr_size = $ims->load_data->data_table($this->modules . '_size', 'size_id', 'size_id,title', " is_show=1 and lang='" . $ims->conf['lang_cur'] . "' order by show_order desc, date_create desc");
        $arr_combine = $ims->load_data->data_table($this->modules . '_combine', 'id', 'id,color_id,size_id,in_stock,out_stock', " type= '".$this->modules."' and type_id='" . $item_id . "' order by date_create asc");

        $arr_option = array();
        if (($num = count($arr_combine)) > 0) {

            $i = 0;
            foreach ($arr_combine as $row) {
                $i++;

                if (!isset($arr_color[$row['color_id']]) || !isset($arr_color[$row['size_id']])) {
                    continue;
                }

                // $option_id = $row['id'];
                $option_id = $row['color_id'];
                $check_quantity = $row['in_stock'] - $row['out_stock'];

                $checked_li = '';
                $checked = '';
                /* if($cur > 0) {               
                  $checked_li = ($option_id == $cur) ? ' class="checked"' : '';
                  $checked = ($option_id == $cur) ? ' checked="checked"' : '';
                  } else {
                  $checked_li = ($i == 1) ? ' class="checked"' : '';
                  $checked = ($i == 1) ? ' checked="checked"' : '';
                  } */

                $arr_option[$row['color_id']] = (isset($arr_option[$row['color_id']])) ? $arr_option[$row['color_id']] : '';
                $arr_option[$row['color_id']] .= '<li' . $checked_li . '><label  style="background:' . $arr_color[$row['color_id']]['color'] . '; " ><input type="radio"  for="combine_' . $option_id . '_'. $check_quantity .' " id="combine_' . $option_id . '" name="color" data-title="' . $arr_size[$row['size_id']]['title'] . '" value="'.$option_id.'"/></label></li>';
            }

            if (count($arr_option) > 0) {
                $output = '<div class="list_combine_detail" data-count="'.count($arr_option).'">
                <div class="list_combine-title_c"><span>' . $ims->lang[$this->modules]['color'] . '</span></div>
                <ul class="list_none">';
                foreach ($arr_option as $color_id => $html) {
                    //$text_color = ($arr_color[$color_id]['color'] > '#555555') ? '#ffffff' : '#000000';

                    $tmp = array();
                    foreach ($ims->func->hex2rgb($arr_color[$color_id]['color']) as $tmp1v) {
                        $tmp[] = 255 - $tmp1v;
                        //$tmp[] = ($tmp1v > 127) ? 0 : 255;
                    }
                    $option_id = $arr_color[$color_id]['color_id'];


                    $text_color = '#' . $ims->func->rgb2hex($tmp);

                    // $output .= '<li ' . $checked_li . ' class="optgroup" id="combine_' . $option_id . '" name="combine" value="' . $option_id . '" ' . $checked . ' style="background:' . $arr_color[$color_id]['color'] . '; color:' . $text_color . ';"></li>';
                    $output .= $html;
                }
                $output .= '<div class="clear"></div></ul></div>';
            }
        }

        return $output;
    }

    //-----------list_code_pic
    function list_code_pic_delete($item_is, $list_code_pic = '', $cur = 0) {
        global $ims;

        $output = '';

        $arrrow = $ims->db->row(array(
            'select' => array('title', 'friendly_link'),
            'from' => array($this->modules . '_code_pic'),
            'where' => array(
                array('=', 'is_show', 1),
                array('=', 'lang', "'" . $ims->conf['lang_cur'] . "'"),
                array('find_in_set', 'code_pic_id', "'" . $list_code_pic . "'")
            ),
            'orderby' => array('show_order' => 'desc', 'date_update' => 'desc')
        ));
        if (is_array($arrrow) && count($arrrow)) {
            $output = '<ul class="list_none list_input_code_pic">';
            $i = 0;
            foreach ($arrrow as $row) {
                $i++;
                if ($cur > 0) {
                    $checked_li = ($row['code_pic_id'] == $cur) ? ' class="checked"' : '';
                    $checked = ($row['code_pic_id'] == $cur) ? ' checked="checked"' : '';
                } else {
                    $checked_li = ($i == 1) ? ' class="checked"' : '';
                    $checked = ($i == 1) ? ' checked="checked"' : '';
                }

                $output .= '<li ' . $checked_li . '><input type="radio" id="code_pic_' . $row['code_pic_id'] . '" name="code_pic" value="' . $row['code_pic_id'] . '" ' . $checked . ' /><label for="code_pic_' . $row['code_pic_id'] . '">' . $row['title'] . '</label></li>';
            }
            $output .= '</ul><div class="clear"></div>';

            $ims->func->include_js_content('list_input_code_pic();');
        }

        return $output;
    }

    /** mod_item
        * @global type $ims
        * @param type $group_id
        * @param type $type
        * @return type
    */
    function convert_number($n){
        $n = (0+str_replace(",","",$n));

        // is this a number?
        if(!is_numeric($n)) return false;

        // now filter it;
        if($n>1000000000000) return round(($n/1000000000000),1).' tr'; //trillion
        else if($n>1000000000) return round(($n/1000000000),1).' b'; //billion
        else if($n>1000000) return round(($n/1000000),1).' m'; //million
        else if($n>1000) return round(($n/1000),1).' k'; //thousand

        return number_format($n);
    }

    function mod_item($row, $temp = 'mod_item', $type = 0) {
        global $ims;

        $check_promotion = 0;

        $title1 = ($row['title1'] != '') ? $ims->func->input_editor_decode($row['title1']).': ' : '';
        $row['title'] = $title1.$ims->func->input_editor_decode($row['title']);
        $arr = $ims->site->check_favorite($row['item_id']);
        $row['class_favorite'] = isset($arr['class']) ? $arr['class'] : '';
        $row['added'] = isset($arr['added']) ? $arr['added'] : '';
        $row['date_begin'] = $ims->lang['global']['day_'.date('N', $row['date_begin'])].', '.date('d/m h:i A');
        $event_owner = $ims->db->load_row('user', 'user_id = '.$row['user_id'], 'full_name, num_follow');
        $row['event_owner'] = $event_owner['full_name'];
        $row['num_follow'] = $this->convert_number($event_owner['num_follow']);
//        if($row['percent_discount'] > 0){
//            $row['discount'] = '<div class="discount">-'.number_format($row['percent_discount'], 0).'%</div>';
//        }
//        $row['price_buy'] = ($row['price_promotion'] > 0) ? $row['price_promotion'] : $row['price_buy'];
//        if($row['price'] > 0 && $row['price'] > $row['price_buy']){
//            $row['discount'] = '<div class="discount">-'.number_format(($row['price'] - $row['price_buy'])/$row['price']*100, 0).'%</div>';
//        }
        
        // check promotion  
//        if($row['price_promotion']!=0){
//            $row['price'] = $row['price_buy'];
//            $row['price_buy'] = $row['price_promotion'];
//            $this->temp_func->assign('row', $row);
//            $this->temp_func->parse($temp.".promo");
//        }

//        if($row['price_buy'] < $row['price'] && $row['price']!=0){
//            $row['price'] = number_format($row['price'], 0,',','.').'đ';
//            $this->temp_func->assign('price', $row['price']);
//            $this->temp_func->parse($temp.'.price');
//        }
//        $row['price_buy'] = ($row['price_buy'] != 0) ? number_format($row['price_buy'],0,',','.').'đ' : $ims->lang['product']['no_price'];
//
        $row['link'] = $ims->func->get_link($row['friendly_link'], '');
        $row["picture"] = $ims->func->get_src_mod($row["picture"], $row['pic_w'], $row['pic_h'], 1, 1);

        // ------------------- Đánh giá -------------------
//        $rate = $row['rate'];
//        if($row['num_rate'] != $rate['average']){
//            $ims->db->do_update("product", array('num_rate'=>$rate['average']), " item_id='".$row['item_id']."'");
//        }

//        $rate = $ims->site->rate_average($row['item_id']);
//        if(!empty($rate)){
//            if($rate['num'] > 0){
//                if($rate['average'] > 0){
//                    $star = $rate['average'];
//                    $int = (int) $star;
//                    $decimal = $star - $int;
//                    for ($i=0; $i < 5; $i++) {
//                        if($star >= 1){
//                            $row['average'] = '<i class="fas fa-star" title ="'.$rate['average'].' sao"></i>';
//                            $star--;
//                        }else{
//                            if($decimal>=0.5 && $star>=0.5){
//                                $row['average'] = '<i class="fas fa-star-half-alt" title ="'.$rate['average'].' sao"></i>';
//                                $star -= 0.5;
//                            }else{
//                                $row['average'] = '<i class="fal fa-star" title ="'.$rate['average'].' sao"></i>';
//                            }
//                        }
//                        $this->temp_func->assign('row', $row);
//                        // $this->temp_func->parse($temp.".rate.star");
//                        $this->temp_func->parse($temp.".rate_view.star");
//                    }
//                }
////                $row['num_rate'] = "<span style='line-height: 100%;'>(".$ims->site_func->get_lang('num_rate','global',array("{num_rate}"=>$rate['num'])).")</span>";
//                $row['num_rate'] = "<span>(".$rate['num'].")</span>";
//            }
//            else{
//                for ($i=0; $i < 5; $i++) {
//                    $row['average'] = "<i class='fal fa-star' title ='".$rate['average']." sao'></i>";
//                    $this->temp_func->assign('row', $row);
//                    // $this->temp_func->parse($temp.".rate.star");
//                    $this->temp_func->parse($temp.".rate_view.star");
//                }
//                $row['num_rate'] = "<span>(0)</span>";
//            }
//            $this->temp_func->assign('row', $row);
//            // $this->temp_func->parse($temp.".rate");
//            $this->temp_func->parse($temp.".rate_view");
//        }
        // ------------------- Đánh giá -------------------

        $this->temp_func->reset($temp);
//        $row["link_cart"] = "";
//        $row["type_btn"]  = "submit";
//        $check_stock = 0;
//        if($check_stock == 1){
//            $row["link_cart"] = '';
//            $row["type_btn"] = "button";
//            $row['id_disable'] = '_dis';
//            $row['btn_add_cart'] = $row['item_status'] = $ims->lang['product']['status_stock0'];
//            $row['btn_order'] = $ims->lang['global']['price_empty'];
//        }else{
//            $row['btn_order'] = $ims->lang['product']['btn_add_cart'];
//            $row['btn_add_cart'] = $ims->lang['product']['btn_add_cart_now'];
//            $row['item_status'] = $ims->lang['product']['status_stock1'];
//        }
        //The item loaded and no load again
        $favorite = $ims->site->check_favorite($row['item_id']);
        if (!empty($favorite)) {
            $row['i_favorite'] = $ims->func->if_isset($favorite["class"]);
            $row['added'] = $ims->func->if_isset($favorite["added"]);
        }
        $this->product_loaded($row['item_id'], $this->modules);
        $row['item_id'] = $ims->func->base64_encode($row['item_id']);
        $row['loading'] = $ims->dir_images."spin.svg";
        $row['rooturl'] = $ims->conf['rooturl'];
//        $row['brand'] = $this->get_brand_name($row['brand_id'], 'link');

        if($check_promotion != 1){
            $this->temp_func->reset($temp);
            $this->temp_func->assign('row', $row);
            $this->temp_func->parse($temp);
            return $this->temp_func->text($temp);
        }
        return false;
    }

    //-----------
    function html_list_item($arr_in = array(), $type = 0) {
        global $ims;

        $temp        = $ims->func->if_isset($arr_in['temp'], 'list_item');
        $this->temp_func->reset($temp);

        $link_action = $ims->func->if_isset($arr_in['link_action'], $ims->site_func->get_link($this->modules));
        $temp_mod    = $ims->func->if_isset($arr_in['temp_mod'], 'mod_item');
        $paginate    = $ims->func->if_isset($arr_in['paginate'], 1);
        $p           = $ims->func->if_isset($ims->input["p"], 1);
        $n           = $ims->func->if_isset($ims->setting[$this->modules]["num_list"], 30);
        $n           = $ims->func->if_isset($arr_in["num_list"], $n);
        $pic_w       = $ims->func->if_isset($arr_in["pic_w"], 285);
        $pic_h       = $ims->func->if_isset($arr_in["pic_h"], 162);
        $where       = $ims->func->if_isset($arr_in["where"], '');
        $ajax        = $ims->func->if_isset($arr_in["ajax"], 0);
        $viewmore_ajax = $ims->func->if_isset($arr_in["viewmore_ajax"], 0);
        $empty_lang = $ims->func->if_isset($arr_in["empty"], '');

        // --------------- Not default ---------------
        $show_pic       = $ims->func->if_isset($arr_in["show_pic"], 0);
        $pic_group      = $ims->func->if_isset($arr_in["pic_group"], '');
        $link_group     = $ims->func->if_isset($arr_in["link_group"], '');
        $group_title    = $ims->func->if_isset($arr_in["group_title"], '');
        $group_id       = $ims->func->if_isset($arr_in["group_id"], 0);
        $province       = $ims->func->if_isset($arr_in["province"], 0);
        $type_show      = $ims->func->if_isset($arr_in["type_show"], '');
        $focus          = $ims->func->if_isset($arr_in["focus"], '');

        $ext = ($_SERVER['QUERY_STRING'] != '') ? '&'.$_SERVER['QUERY_STRING'] : '';
        if(isset($ims->get['p'])){
            $pos_p =  strpos($ext, 'p=');
            $exclude_p = ($pos_p == 0) ? 'p='.$ims->get['p'] : '&p='.$ims->get['p'];
            $ext = str_replace($exclude_p, '', $ext);
        }

        $nav = '';
        $num_total = 0;
        $start = 0;
        if ($paginate == 1) {
            $num_total = $ims->db->do_get_num("product", "1 ". $ims->conf['where_lang'] . $where);
            $num_items = ceil($num_total / $n);
            if ($p > $num_items)
                $p = $num_items;
            if ($p < 1)
                $p = 1;
            $start = ($p - 1) * $n;
             if($type == 1){
                 $link_action =  $ims->conf['rooturl'].$ims->setting[$this->modules]['promotion_link'];
                 if(isset($ims->data['cur_item'])){
                    $link_action =  $ims->site_func->get_link($this->modules, $ims->data['cur_item']['friendly_link']);
                 }
             }
            $nav = $ims->site->paginate($link_action, $num_total, $n, $ext, $p);
        }
        if($where == ''){
            $where .= " ORDER BY show_order DESC, date_create DESC";
        }

        if($ajax == 0){
            $arr_product = $ims->db->load_row_arr('product', $ims->conf['qr'].$where.' LIMIT '.$start.','.$n);
        }elseif($ajax == 2){ // ajax load sp khi đổi province
            $arr_product = $ims->db->load_row_arr('product', 'is_show = 1 and lang = "'.$ims->conf['lang_cur'].'" '.$where.' LIMIT '.$start.','.$n);
        }else{ // ajax load sp bình thường
            $arr_product = $ims->db->load_row_arr('product', 'is_show = 1 and lang = "'.$ims->conf['lang_cur'].'" '.$where);
        }
        if($arr_product){
            $i=0;
            foreach ($arr_product as $k => $row) {
                $i++;
                $row['stt'] = $i;
                $row['pic_w'] = $pic_w;
                $row['pic_h'] = $pic_h;   
                $row['rate'] = array(
                    'num' => 0,
                    'average' => 0
                );
                if(isset($ims->data['rate'][$row['item_id']])){                    
                    $row['rate'] = array(
                        'num' => $ims->data['rate'][$row['item_id']]['num'],
                        'average' => $ims->data['rate'][$row['item_id']]['average'],
                    );
                }
                if($show_pic == 1 && $i == 1){
                    $pic_group = '<div class="group_picture"><a href="'.$ims->func->get_link($link_group, '').'"><img src="'.$ims->func->get_src_mod($pic_group).'" alt="'.$group_title.'"></a></div>';
                    $this->temp_func->assign('row', array('mod_item' => $pic_group));
                    $this->temp_func->parse($temp . ".row_item");
                }
                $row['mod_item'] = $this->mod_item($row, $temp_mod , $type);
                $this->temp_func->assign('row', $row);
                $this->temp_func->parse($temp . ".row_item");
            }
        }else{
            if (isset($arr_in["empty"])) {
                $data['empty'] = "d-none";
            }
            $mess = ($empty_lang == '') ? $ims->lang[$this->modules]["no_have_item"] : $empty_lang;
            $this->temp_func->assign('row', array("mess" => $mess));
            $this->temp_func->parse($temp . ".row_empty");
        }

        if($type == 1 && isset($ims->data['cur_item'])){
            $data['cur_item'] = $ims->data['cur_item'];
            $data['cur_item']['picture'] = $data['cur_item']['picture']!=''?'<img src="'.$ims->func->get_src_mod($data['cur_item']['picture']).'" alt="'.$data['cur_item']['title'].'" width="100%" height="auto">':'';
            if($data['cur_item']['time_end'] < date('H:i:s') && $data['cur_item']['date_end'] > time()){            
                $data['cur_item']['promotime'] = $ims->lang['product']['promotion_end'];
            }elseif($data['cur_item']['date_end'] < time()){
                $data['cur_item']['promotime'] = $ims->lang['product']['completed_event'];
            }
            $this->temp_func->assign('data', $data);
            $this->temp_func->parse($temp.'.promotion');
        }

        if($viewmore_ajax == 1){
            $num_list_setting = $ims->setting[$this->modules]["num_list"];
            if($num_total > $num_list_setting){
//                $num = (($num_total - $num_list_setting) > $num_list_setting) ? $num_list_setting : $num_total - $num_list_setting;
//                $data['view_more'] = $ims->site_func->get_lang('view_more', 'product', array('[num]' => '<span>'.$num.'</span>'));
                $data['view_more'] = $ims->site_func->get_lang('view_more', 'product');
                $data['start'] = count($arr_product);
            }else{
                $data['hide_view_more'] = 'style="display:none"';
            }
//            $data['keyword'] = (isset($ims->get['keyword']) && $ims->get['keyword'] != '') ? $ims->get['keyword'] : '';

//            if(isset($ims->get['fc']) && $ims->get['fc'] == 1){
//                $data['focus'] = 'focus';
//            }elseif(isset($ims->get['fc1']) && $ims->get['fc1'] == 1){
//                $data['focus'] = 'focus1';
//            }

            if($province){
                $data['province'] = $province;
            }
            if($focus){
                $data['focus'] = $focus;
            }
            $data['type_show'] = ($type_show) ? $type_show : '';

            $this->temp_func->assign('data', $data);
            $this->temp_func->parse($temp.'.viewmore_ajax');
        }else{
            $data['nav'] = (!empty($nav)) ? $nav : '';
        }

        if($group_id != 0){
            $data['cur_group'] = $group_id;
        }else{
            $data['cur_group'] = isset($ims->conf['cur_group']) ? $ims->conf['cur_group'] : 0;
        }

        $data['link_action'] = $link_action . "&p=" . $p;

        $this->temp_func->assign('data', $data);
        $this->temp_func->parse($temp);
        return $this->temp_func->text($temp);
    }

    function get_tab(){
        global $ims;
        $output = array();
        $sql = "SELECT * FROM product_contenttab WHERE is_show = 1 AND lang = '".$ims->conf['lang_cur']."' ";
        $query = $ims->db->query($sql);
        while ($row = $ims->db->fetch_row($query)) {
            array_push($output, $row);
        }
        return $output;
    }

    function list_other($where = '', $type= "list_other") {
        global $ims;
      
        $output = '';

        $pic_w = 300;
        $pic_h = 300;
        $temp = 'list_other';
        if($type != 'list_other'){
            $temp = 'list_watched';
        }
        $sql = "select *
                from product 
                where is_show=1 
                and lang='" . $ims->conf["lang_cur"] . "' 
                " . $where . "
                order by show_order desc, date_update desc";
        // print_r($sql);die;
        $result = $ims->db->query($sql);
        $html_row = '';
        if ($num = $ims->db->num_rows($result)) {
            $i = 0;
            while ($row = $ims->db->fetch_row($result)) {
                $i++;
                $row['pic_w'] = $pic_w;
                $row['pic_h'] = $pic_h;
                $row['link'] = $ims->site_func->get_link($this->modules, '', $row['friendly_link']);
                $row['date_update'] = date('d/m/Y', $row['date_update']);
                $row["picture_zoom"] = $ims->func->get_src_mod($row["picture"]);
                $row['picture'] = $ims->func->get_src_mod($row["picture"], $pic_w, $pic_h, 1, 0, array('fix_max' => '1'));
                $rate = $ims->site->rate_average($row['item_id']);
                if(!empty($rate)){
                    if($rate['num'] > 0){
                        if($rate['average'] > 0){
                            for ($i=0; $i < $rate['average']; $i++) { 
                                $row['average'] = "<img class='star_img' src= '".$ims->dir_images ."star.png' alt='".$rate['average']." sao' title ='".$rate['average']." sao'/>";
                                $this->temp_act->assign('row', $row);
                                $this->temp_act->parse($temp.".row.rate.star");
                            }
                        }
                        $row['num_rate'] = "<span>(".  $rate['num'] .")</span>";
                    }
                    else{
                        for ($i=0; $i < 5; $i++) { 
                            $row['average'] = "<img class='star_img' src= '".$ims->dir_images ."no_star.png' alt='".$rate['average']." sao' title ='".$rate['average']." sao'/>";
                            $this->temp_act->assign('row', $row);
                            $this->temp_act->parse($temp.".row.rate.star");
                        }
                        $row['num_rate'] = "";
                    }
                    $this->temp_act->assign('row', $row);
                    $this->temp_act->parse($temp.".row.rate");
                }                
                $value_price_buy = $ims->site_func->get_price_promotion($row);
                $row['price_buy'] = $ims->func->get_price_format($value_price_buy['price_buy']);
                if($value_price_buy['price_buy'] != $row['price_sale']){
                    $row['sale'] =  'sale_now';
                    $row['class_price_buy'] = 'none';
                    $row['price_sale'] = $ims->func->get_price_format($row['price_sale']);
                    $this->temp_act->assign('row', $row);
                    $this->temp_act->parse($temp.'.row.price_promotion');
                }
                if($value_price_buy['price_buy'] != $row['price']){
                    $row['price'] = $ims->func->get_price_format($row['price']);
                    $row['class_price'] =  'right_b';
                    $row['ribbon'] =  'sale';
                    $row['percent_discount'] =  $row['percent_discount'];
                    $this->temp_act->assign('row', $row);
                    $this->temp_act->parse($temp.'.row.price');
                }
                if(isset($value_price_buy['short']) && $value_price_buy['short'] != ''){
                    $row['short_promotion'] = $value_price_buy['short'];
                }
                $row["link_cart"] = $ims->site_func->get_link_popup('product', 'cart', array('item_id' => $row['item_id']));
                // $check_stock = $ims->site_func->check_in_stock (array('type_id' => $row['item_id']));
                // $check_combine = $ims->site_func->check_product_combine($row);
                // if($check_combine != 0 || $check_stock < 1){
                //     $row["link_cart"] = $ims->site_func->get_link('product', '', $row['friendly_link']);
                //     $row["link_go"] = "data-go = ".$ims->site_func->get_link('product', '', $row['friendly_link'])." ";
                // }
                $this->temp_act->assign('row', $row);
                $this->temp_act->parse($temp.".row");
            }
            $this->temp_act->parse($temp);
            if($type == 'list_watched' && $num > 3){
                $ims->func->include_css($ims->dir_js . 'mCustomScrollbar/jquery.mCustomScrollbar.css');
                $ims->func->include_js($ims->dir_js . 'mCustomScrollbar/jquery.mCustomScrollbar.concat.min.js');
                $ims->func->include_js_content("
                    $('.list_watched .list_none').mCustomScrollbar({
                        scrollButtons:{enable:true},
                        theme:'minimal-dark',
                    });
                ");
            }
            if($type == 'list_other'){
            $ims->func->include_css($ims->dir_js . 'owl.carousel.2/assets/owl.theme.default.css');
            $ims->func->include_css($ims->dir_js . 'owl.carousel.2/assets/owl.carousel.css');
            $ims->func->include_css($ims->dir_js . 'owl.carousel.2/assets/owl.animate.css');
            $ims->func->include_js($ims->dir_js . 'owl.carousel.2/owl.carousel.js');

            $ims->func->include_js_content("
                $('.product_scroll_orther').owlCarousel({
                    autoplay: true,
                    smartSpeed: 800,
                    autoplayTimeout: 3000,
                    autoplayHoverPause: true,
                    loop: true,
                    margin: 18,
                    dots: false,
                    nav: true,
                    responsive: {
                        0: {
                            items: 2
                        },
                        400: {
                            items: 2
                        },
                        600: {
                            items: 3
                        },
                        750: {
                            items: 4
                        },
                        1000: {
                            items: 5
                        },
                        1300: {
                            items: 5
                        }
                    }
            })");
            }
            return $this->temp_act->text($temp);
        }
    }

    //=================box_column===============
    function box_left() {
        global $ims;

        $output = $ims->site->block_left();

        return $output;
    }

    //=================box_column===============
    function box_column_promotion() {
        global $ims;

        $output .= $ims->site->box_news_hot(10);
        $output .= $ims->site->box_news(10);

        return $output;
    }

    //=================box_column===============
    function box_column() {
        global $ims;

        $output = $ims->site->block_column();

        return $output;
    }

    /* ==============================SHOPPING============================== */

    function list_quantity($select_name, $cur = "", $ext = "", $arr_more = array()) {
        global $ims;

        return $ims->site->list_number($select_name, 1, 100, $cur, $ext, $arr_more);
    }
   

}

?>