<?php
if (!defined('IN_ims')) { die('Access denied'); }
$nts = new sMain();

class sMain {

    var $modules = "product";
    var $action  = "detail";

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
            'required_login' => 0, // Bắt buộc đăng nhập
        );
        $ims->func->loadTemplate($arrLoad);

        require_once($this->modules . "_func.php");
        $this->modFunc = new productFunc($this);

        if(isset($ims->get['dl']) && $ims->get['dl']!=''){
            $code  = preg_replace('/[^A-Za-z0-9\. -]/','', $ims->get['dl']);
            $check = $ims->db->load_row("user_deeplink", "short_code='".$code."' AND is_show=1");
            if ($check['id'] > 0){
                setcookie('deeplinkv2', $check['id'], time()+(86400 * 30));
                $num_view = $check['num_view'] + 1;
                $ims->db->do_update('user_deeplink', array('num_view' => $num_view), " short_code='".$code."' ");
            }
        }

        if (isset($ims->conf['cur_item'])) {
            //Make link lang
            $load_lang = $ims->db->load_item_arr("product", " item_id='".$ims->conf['cur_item']."' ", "friendly_link, lang");
            foreach ($load_lang as $key => $row_lang) {
                $ims->data['link_lang'][$row_lang['lang']] = $ims->site_func->get_link_lang($row_lang['lang'], $this->modules, '', $row_lang['friendly_link']);
            }
            //End Make link lang

            //SEO
//            $ims->site->get_seo($ims->data['cur_item']);
            $ims->conf["cur_group"]     = $ims->data['cur_item']["group_id"];
            $ims->conf["cur_group_nav"] = $ims->data['cur_item']["group_nav"];
            $ims->conf["meta_image"]    = $ims->func->get_src_mod($ims->data['cur_item']["picture"], 630, 420, 1, 1);

            //Current menu
            $arr_group_nav = (!empty($ims->conf["cur_group_nav"])) ? explode(',', $ims->conf["cur_group_nav"]) : array();
            foreach ($arr_group_nav as $v) {
                $ims->conf['menu_action'][] = $this->modules.'-group-'.$v;
            }
            $ims->conf['menu_action'][] = $this->modules.'-item-'.$ims->conf['cur_item'];
            //End current menu

//            $data['content'] = $this->do_detail($ims->data['cur_item']);

            if(isset($ims->get['view_ticket']) && $ims->get['view_ticket'] == 1){
                $data['content'] = $this->do_view_ticket();
            }else{
                $data['content'] = $this->do_detail1($ims->data['cur_item']);
            }
//            $data['other']   = $this->do_related($ims->data['cur_item']);
        } else {
            $ims->html->redirect_rel($ims->site_func->get_link($this->modules));
        }
        $temp = 'main';
        $ims->conf['class_full'] = 'detail';
        $ims->conf['container_layout'] = 'm';
        $ims->temp_act->assign('data', $data);
        $ims->temp_act->parse($temp);
        $ims->output .= $ims->temp_act->text($temp);
    }

    function do_box_hot($info){
        global $ims;
        $data = array();
        $data['list_product_hot'] = $ims->site->product_vertical($ims->lang['product']['hot_product'], 'focus', 3);
        $data['list_news_related'] = $this->do_news_related($info);
        $ims->temp_act->assign('data', $data);
        $ims->temp_act->parse("block_column");
        return $ims->temp_act->text("block_column");
    }

    function do_detail($info = array()) {
        global $ims;

        $data = $info;
        $ims->func->include_js($ims->dir_js.'jquery.elevateZoom.min.js');

        if($data['combo_id'] > 0){
            $check = $ims->db->load_item('combo', $ims->conf['qr'].' and quantity_product > 0 and item_id = '.$data['combo_id'].' and date_begin < '.time().' and date_end > '.time(), 'item_id');
            if(!$check){
                require_once ($ims->conf["rootpath"]."404.php");die;
            }
        }
        // Cập nhật lượt xem
        $ims->db->query("UPDATE product SET num_view=num_view+1 WHERE item_id=".$data['item_id']. $ims->conf['where_lang']);
        $ims->load_data->data_color();

        $data["link_action"] = $ims->site_func->get_link('product', '', $info['friendly_link']);
        $data['short'] = ($data['short'] != '') ? '<div class="short">'.$ims->func->input_editor_decode($data['short']).'</div>' : ''; // Mô tả ngắn
        $data['short1'] = ($data['short1'] != '') ? '<div class="short1">'.$ims->func->input_editor_decode($data['short1']).'</div>' : ''; // Trên mô tả ngắn
        $data['short2'] = ($data['short2'] != '') ? '<div class="short2"><div class="short2_title">'.$ims->lang['product']['endow'].'</div><div class="content">'.$ims->func->input_editor_decode($data['short2']).'</div></div>' : ''; // Ưu đãi thêm
        $data['content'] = $ims->func->input_editor_decode($data['content']);
        $data["brand_name"] = ($info["brand_id"] > 0) ? '<div class="info_brand">'.$ims->lang['product']['brand'].': '.$this->modFunc->get_brand_name($info["brand_id"], "link").'</div>' : '';

        $favorite = $ims->site->check_favorite($data['item_id']);
        if (!empty($favorite)) {
            $data['i_favorite'] = $ims->func->if_isset($favorite["class"]);
            $data['added'] = $ims->func->if_isset($favorite["added"]);
        }

        // ------------------- Đánh giá -------------------
        $rate = $ims->site->rate_average($info['item_id']);
        if($rate['average'] == 0){
            $rate['average'] = $info['num_rate'];
        }
        if($rate['average'] > 0){
            $star = $rate['average'];
            $int = (int) $star;
            $decimal = $star - $int;
            for ($i=0; $i < 5; $i++) {
                if($star >= 1){
                    $info['average'] = '<i class="fas fa-star" title ="'.$rate['average'].' sao"></i>';
                    $star--;
                }else{
                    if($decimal>=0.5 && $star>=0.5){
                        $info['average'] = '<i class="fas fa-star-half-alt" title ="'.$rate['average'].' sao"></i>';
                        $star -= 0.5;
                    }else{
                        $info['average'] = '<i class="fal fa-star" title ="'.$rate['average'].' sao"></i>';
                    }
                }
                $ims->temp_act->assign('info', $info);
                $ims->temp_act->parse("detail.rate.star");
            }
        } else{
            for ($i=0; $i < 5; $i++) {
                $info['average'] = "<i class='fal fa-star' title ='".$data['num_rate']." sao'></i>";
                $ims->temp_act->assign('info', $info);
                $ims->temp_act->parse("detail.rate.star");
            }
        }
//        $info['num'] = "<span>(".$rate['num'].' '.$ims->lang['product']['review'].")</span>";
        if($rate['num'] > 0){
            $info['num'] = "<span>".$rate['num'].' '.$ims->lang['product']['rate']."</span>";
        }

        $ims->temp_act->assign('info', $info);
        $ims->temp_act->parse("detail.rate");
        // ------------------- Đánh giá -------------------

        // phiên bản sản phẩm
        $version = $ims->site_func->versionProduct($info);
        if (!empty($version)) {
            $data['version'] = $version['html'];
            $data['count_version'] = $version['count'];
            $data['option'] = $version['option'];            
            $data['pic_color'] = $version['pic_color'];
            if ($data['count_version']>1) {
                $data['option_id'] = 0;
            }
        }
        // Lấy giá sp theo promotion
        $data['price_buy'] = ($data['price_promotion'] > 0) ? $data['price_promotion'] : $data['price_buy'];
        // hình sản phẩm
        $data["img_detail"] = $this->box_image($data, $data['option']);
        $data['price_buy_text'] = ($data['price_buy'] > 0) ? number_format($data['price_buy'], 0, ',', '.').'đ' : $ims->lang['product']['no_price'];
        if($data['price'] > 0 && $data['price_buy'] < $data['price']){
            $data['percent_discount'] = number_format(100 - ($data['price_buy'] / $data['price'] * 100), 1, '.', '');
        }

        // có giảm giá
        if($data['price_buy'] < $data['price'] && $data['price'] != 0){
            $data['price_text'] = number_format($data['price'], 0, ',', '.').'đ';
//            $ims->temp_act->assign('price', $data);
//            $ims->temp_act->parse("detail.info_row_price");
        }else{
            $data['none'] = 'style="display:none;"';
        }

//        if(isset($data['tag_list']) && $data['tag_list']!= ''){
//            $tag_list = ($info['tag_list']) ? explode(",", $info['tag_list']) : array();
//            if (empty($tag_list)){
//                $tag_list[] = $info['title'];
//            }
//            if (count($tag_list)){
//                foreach ($tag_list as $key => $value){
//                    $row['tag'] = $value;
//                    $row['tag_link'] = $ims->site_func->get_link('product').'?tag='.$tag_list[$key];
//                    $ims->temp_act->assign('row', $row);
//                    $ims->temp_act->parse("detail.tag_list.row");
//                }
//            }
//            $ims->temp_act->assign('data', $data);
//            $ims->temp_act->parse("detail.tag_list");
//        }

        // form đặt hàng
        $data["type_btn"] = "submit";
        $data["link_cart"] = $ims->site_func->get_link('product',$ims->setting['product']['ordering_cart_link']);
        $data['btn_order'] = $ims->lang['product']['btn_add_cart'];
        $data['btn_add_cart'] = $ims->lang['product']['btn_add_cart_now'];
        $data['item_status'] = $ims->lang['product']['status_stock1'];

        // -------------- Kiểm tra sp hết hàng -------------
        $op = $ims->db->load_item_arr('product_option', $ims->conf['qr'].' and ProductId = '.$data['item_id'], 'Quantity, is_OrderOutStock');
        $useWarehouse = ($op[0]['is_OrderOutStock'] == 1) ? 0 : (int)$ims->setting['product']['use_ware_house'];
        if($useWarehouse == 1){
            $num_stock = (count($op) > 1) ? $data['num_stock'] : $op[0]['Quantity'];
            $data['max_quantity'] = $num_stock;
            if($num_stock <= 0){
                $data["type_btn"] = "button";
                $data['btn_add_cart'] = $data['item_status'] = $ims->lang['product']['status_stock0'];
                $data['btn_order'] = $ims->lang['global']['price_empty'];
            }
        }else{
            $data['max_quantity'] = 1000;
        }

        $ims->temp_act->assign('data', $data);
        $ims->temp_act->parse("detail.btn_add_cart");
        $ims->temp_act->parse("detail.quantity");
          
        
        // tab nội dung
//        $content_tab = $ims->db->load_row_arr("product_contenttab", '1 '.$ims->conf["where_lang"]);
//        foreach ($content_tab as $key => $value) {
//            $row = array();
//            if($key==0){
//                // $row['active'] = 'active';
//            }
//            $row['id_tab'] = 't-'.$key;
//            $row['title_tab'] = $value['title'];
//            $row['content_tab'] = $ims->func->input_editor_decode($data['contenttab_'.$value['item_id']]);
//            $ims->temp_act->assign('row',$row);
//            $ims->temp_act->parse('detail.tab_title');
//            $ims->temp_act->parse('detail.tab_content');
//        }

        // natures
        // $arr_nature = $ims->db->load_item_arr('product_nature AS n,  product_nature_group AS g', 'n.lang = "'.$ims->conf['lang_cur'].'"AND g.lang =  "'.$ims->conf['lang_cur'].'" AND n.group_id = g.group_id AND FIND_IN_SET( n.item_id, "'.$info['arr_nature'].'" ) >0 ORDER BY g.show_order DESC, g.date_create DESC', 'n.title , g.title AS group_title');
        // if(count($arr_nature)){
        //     $i=0;
        //     foreach ($arr_nature as $row) {
        //         $i++;
        //         $row['title'] = $ims->func->input_editor_decode($row['title']);
        //         $row['group_title'] = $ims->func->input_editor_decode($row['group_title']);
        //         $ims->temp_act->assign('row',$row);
        //         $ims->temp_act->parse("detail.natures.row_popup");
        //         if($i<=9){
        //             $ims->temp_act->parse("detail.natures.row");
        //         }
        //     }
        //     $ims->temp_act->parse("detail.natures");
        // }


        // quà tặng kèm khi mua sản phẩm
        // $gift_program = $ims->db->load_row_arr('product_gift_program','lang="'.$ims->conf['lang_cur'].'" AND is_show=1 AND apply_product="'.$info['item_id'].'" AND time_begin < "'.date('H:i:s').'" AND time_end > "'.date('H:i:s').'" AND date_begin < "'.time().'" AND date_end > "'.time().'"');
        // if($gift_program){
        //     foreach ($gift_program as $gp) {
        //         $list_gift = $ims->db->load_row_arr('product_gift','lang="'.$ims->conf['lang_cur'].'" AND is_show=1 AND find_in_set(item_id,"'.$gp['list_gift'].'")>0');
        //         if($list_gift){
        //             foreach ($list_gift as $gift) {
        //                 $gift['program_id'] = $gp['item_id'];
        //                 $gift['picture'] = $ims->func->get_src_mod($gift['picture'],120,120,1,0,array('fix_max'=>1));
        //                 $gift['title'] = $ims->func->input_editor_decode($gift['title']);
        //                 $gift['short'] = $ims->func->input_editor_decode($gift['short']);
        //                 $gift['desc'] = $gift['title'].'</br>'.$gift['short'];
        //                 if($gp['program_type'] == 1){
        //                     $gift['input_type'] = '';
        //                 }else{
        //                     if($gp['max_gift'] > 1){                                
        //                         $gift['input_type'] = '<input type="checkbox" name="gift_program'.$gift['program_id'].'" id="gift'.$gift['item_id'].'"">';
        //                     }else{
        //                         $gift['input_type'] = '<input type="radio" name="gift_program'.$gift['program_id'].'" id="gift'.$gift['item_id'].'"">';
        //                     }
        //                 }
        //                 $ims->temp_act->assign('row',$gift);
        //                 $ims->temp_act->parse('detail.gift.row');
        //             }
        //         }
        //         $gp['max_gift'] = ($gp['program_type']!=1 && $gp['max_gift']>1)?'data-max="'.$gp['max_gift'].'"':'';
        //         $gp['list_gift'] = ($gp['program_type']==1)?$gp['list_gift']:'';
        //         $ims->temp_act->assign('data',$gp);
        //         $ims->temp_act->parse('detail.gift');
        //         $ims->temp_act->parse('detail.gift_info');
        //     }
        // }

        // ----- Thông số sản phẩm -----
        if($info['arr_nature']){
            $list_nature = $ims->db->load_item_arr('product_nature as pn, product_nature_group as png', 'pn.is_show = 1 and pn.lang = "'.$ims->conf['lang_cur'].'" and pn.item_id IN ('.$info['arr_nature'].') and pn.group_id = png.group_id order by png.show_order desc, png.date_create asc', 'pn.group_id, pn.title as item_title, png.title as group_title');
            $list_nature_tmp = array();
            foreach ($list_nature as $v){
                $list_nature_tmp[$v['group_id']]['title'] = $v['group_title'];
                $list_nature_tmp[$v['group_id']]['content'][] = $v['item_title'];
            }
            foreach ($list_nature_tmp as $nature){
                $nature['content'] = implode(', ', $nature['content']);
                $ims->temp_act->assign('nature', $nature);
                if($i < 10){
                    $ims->temp_act->parse("detail.specifications.spec_item");
                }
                $ims->temp_act->parse("detail.specifications.spec_item_fcb");
            }
            if($i > 9){
                $ims->temp_act->parse("detail.specifications.detail_specifications");
            }
            $ims->temp_act->parse("detail.specifications");
        }
        // ----- Thông số sản phẩm -----

        // thêm sản phẩm đã xem

        if($ims->site_func->checkUserLogin() == 1) {
            $array_watched = $ims->site_func->addListWatched($data['item_id']);
        }else{
            $array_watched = $ims->site_func->addListWatchedWithCookie($data['item_id']);
        }

        // Sản phẩm cùng loại        
        $data['other'] = $this->do_related($info);

        // form bình luận
        $data['form_comment'] = $ims->site_func->form_comment('product', $info['item_id'], 0, 'form_comment_rate', $info['num_rate']);

//        $data['box_hot'] = $this->do_box_hot($info);

        if($info['combo_id'] > 0){
            $data['box_combo'] = $this->do_combo($info['combo_id']);
        }else{
            $data['promotion_code'] = $this->promotion_code($info);
        }
        $data['focus1_product'] = $ims->site->do_is_focus1_product();
        $data['item_id'] = $ims->func->base64_encode($data['item_id']);
        $ims->temp_act->assign('data', $data);
        $ims->temp_act->parse("detail");
        return $ims->temp_act->text("detail");
    }

    function box_image($info=array(), $option=array()) {
        global $ims;

        $output = '';
        $data = $temp = array();
        $pic_w = 745;
        $pic_h = 465;
        $thum_w = 106;
        $thum_h = 85;
        $is_show = 0;
        $count = -1;
        $info["link_action"] = $ims->func->get_link($info['friendly_link'], '');

        if($info['arr_picture'] == ''){
            if(!empty($info["picture"])){
                $row = array();
                $is_show = 1;
                $row['title'] = $ims->func->input_editor_decode($info['title']);
                $row['pic_w'] = $pic_w;
                $row['pic_h'] = $pic_h;
                $row['thum_w'] = $thum_w;
                $row['thum_h'] = $thum_h;
                $row['picture'] = $info["picture"];

                $row['src_zoom'] = $ims->func->get_src_mod($row["picture"]);
                $row['src'] = $ims->func->get_src_mod($row["picture"], $pic_w, $pic_h, 1, 0, array('fix_max' => '1'));
                $row['src_thumb'] = $ims->func->get_src_mod($row["picture"], $thum_w, $thum_h, 1, 0, array('fix_max' => '1'));

                array_push($temp,$row);
            }
        }

        if($info['arr_item'] != '' ){
            $info['arr_item'] = $ims->func->unserialize($info['arr_item']);
            // print_arr($info['arr_item']);
            foreach ($info['arr_item'] as $row) {
                if(!empty($row['picture'])){
                    $is_show = 1;
                    $row['color_id'] = 'data-color="'.$row['color_id'].'"';
                    $row['title'] = $ims->func->input_editor_decode($info['title']);
                    $row['pic_w'] = $pic_w;
                    $row['pic_h'] = $pic_h;
                    $row['thum_w'] = $thum_w;
                    $row['thum_h'] = $thum_h;
                    $row['picture'] = ($row["picture"]==$info["picture"])?$info["picture"]:$row["picture"];

                    $row['src_zoom'] = $ims->func->get_src_mod($row["picture"]);
                    $row['src'] = $ims->func->get_src_mod($row["picture"], $pic_w, $pic_h, 1, 0, array('fix_max' => '1'));
                    $row['src_thumb'] = $ims->func->get_src_mod($row["picture"], $thum_w, $thum_h, 1, 0, array('fix_max' => '1'));                    
                    array_push($temp,$row);
                    // $ims->temp_act->assign('row', $row);
                    // $ims->temp_act->parse("img_detail.pic");
                    // $ims->temp_act->parse("img_detail.pic_thumb");
                }
            }
        }        
        
        $arr_color_picture = isset($info['pic_color'])?$info['pic_color']:'';
        if($arr_color_picture){            
            // print_arr($arr_color_picture);
            foreach ($arr_color_picture as $row) {
                if(!empty($row['picture'])){
                    $is_show = 1;
                    if(isset($row['option']) && count($row['option'])>0){                        
                        foreach ($row['option'] as $kop => $vop) {       
                            $color = isset($ims->data['color'][$vop])?$ims->data['color'][$vop]:array();
                            $vop = isset($color['title'])?str_replace(' ','-',strtolower($ims->func->vn_str_filter(trim($color['title'])))):$vop;
                            $row_c = isset($option[$kop])?$option[$kop]:array();                            
                            if(isset($row_c['group_id']) && $row_c['group_id']=='color'){                                
                                $row['color_id'] = 'data-color="'.$vop.'"';
                            }
                        }
                    }                    
                    $row['title'] = $ims->func->input_editor_decode($info['title']);
                    $row['pic_w'] = $pic_w;
                    $row['pic_h'] = $pic_h;
                    $row['thum_w'] = $thum_w;
                    $row['thum_h'] = $thum_h;
                    $row['picture'] = ($row["picture"]==$info["picture"])?$info["picture"]:$row["picture"];

                    $row['src_zoom'] = $ims->func->get_src_mod($row["picture"]);
                    $row['src'] = $ims->func->get_src_mod($row["picture"], $pic_w, $pic_h, 1, 0, array('fix_max' => '1'));
                    $row['src_thumb'] = $ims->func->get_src_mod($row["picture"], $thum_w, $thum_h, 1, 0, array('fix_max' => '1'));
                    array_push($temp,$row);
                    // $ims->temp_act->assign('row', $row);
                    // $ims->temp_act->parse("img_detail.pic");
                    // $ims->temp_act->parse("img_detail.pic_thumb");
                }
            }
        }        
        if(count($temp)){
            foreach ($temp as $row) {
                $data[$row['picture']] = $row;
            }
        }
        $data = array_values($data);
        if(count($data)){
            $i=-1;
            foreach($data as $row){
                $i++;
                $row['pid'] = $i;                
                $ims->temp_act->assign('row', $row);
                $ims->temp_act->parse("img_detail.pic");
                $ims->temp_act->parse("img_detail.pic_thumb");
            }
            $count = $i;
        }
        $info['arr_picture'] = $ims->func->unserialize($info['arr_picture']);
        $list_pic = '';    
        if ($num = count($info['arr_picture'])) {
            $is_show = 1;            
            $j = $count;
            foreach ($info['arr_picture'] as $picture) {                
                $j++;
                $row = array();
                // $row['color_id'] = 'data-color="'.$row['color_id'].'"';
                $row['pid'] = $j;
                $row['title'] = $ims->func->input_editor_decode($info['title']);
                $row['pic_w'] = $pic_w;
                $row['pic_h'] = $pic_h;
                $row['thum_w'] = $thum_w;
                $row['thum_h'] = $thum_h;
                $row['picture'] = $picture;

                $row['src_zoom'] = $ims->func->get_src_mod($row["picture"]);
                $row['src'] = $ims->func->get_src_mod($row["picture"], $pic_w, $pic_h, 1, 0, array('fix_max' => '1'));
                $row['src_thumb'] = $ims->func->get_src_mod($row["picture"], $thum_w, $thum_h, 1, 0, array('fix_max' => '1'));

                $ims->temp_act->assign('row', $row);
                $ims->temp_act->parse("img_detail.pic");
                $ims->temp_act->parse("img_detail.pic_thumb");
            }
        }
        $favorite = $ims->site->check_favorite($info['item_id']);
        if (!empty($favorite)) {
            $info['i_favorite'] = $ims->func->if_isset($favorite["class"]);
            $info['added'] = $ims->func->if_isset($favorite["added"]);
        }
        if(!empty($info['arr_video'])){
            $arr_video = $ims->func->unserialize($info['arr_video']);
            if($arr_video){                
                foreach ($arr_video as $row) {
                    $row['title'] = $ims->func->input_editor_decode($info['title']);
                    $row['vid'] = 'video';
                    $row['plugin'] = 'data-fancybox';
                    if($row['select'] == 'link'){
                        $row['code1'] = $ims->func->get_youtube_code($row['youtube1']);
                        $row['code2'] = $ims->func->get_youtube_code($row['youtube2']);                        
                        $row['src_zoom'] = $ims->func->get_src_mod($row["picture"]);
                        $row['src'] = $ims->func->get_src_mod($row["picture"], $pic_w, $pic_h, 1, 0, array('fix_max' => '1'));
                        $row['src_thumb'] = $ims->func->get_src_mod($row["picture"], $thum_w, $thum_h, 1, 0, array('fix_max' => '1'));
                        $ims->temp_act->assign('row', $row);
                        if(!empty($row['youtube1'])){
                            $ims->temp_act->parse("img_detail.vid1.youtube");
                            $ims->temp_act->parse("img_detail.vid_thumb1.youtube");
                        }
                        if(!empty($row['youtube2'])){
                            $ims->temp_act->parse("img_detail.vid2.youtube");                            
                            $ims->temp_act->parse("img_detail.vid_thumb2.youtube");
                        }
                    }
                    if($row['select'] == 'mp4'){
                        $ims->temp_act->assign('row', $row);
                        if(!empty($row['mp41'])){
                            $ims->temp_act->parse("img_detail.vid1.mp4");
                            $ims->temp_act->parse("img_detail.vid_thumb1.mp4");
                        }
                        if(!empty($row['mp42'])){
                            $ims->temp_act->parse("img_detail.vid2.mp4");                            
                            $ims->temp_act->parse("img_detail.vid_thumb2.mp4");
                        }
                    }
                    $ims->temp_act->parse("img_detail.vid1");
                    $ims->temp_act->parse("img_detail.vid2");
                    $ims->temp_act->parse("img_detail.vid_thumb1");
                    $ims->temp_act->parse("img_detail.vid_thumb2");
                }
            }
            // $row = array();
            // $row['plugin'] = 'data-fancybox';
            // $row['vid'] = 'video';
            // $row['src_zoom'] = $info['video'];
            // $row['src'] = $row['src_thumb'] = '//i3.ytimg.com/vi/'.$ims->func->get_youtube_code($info['video']).'/hqdefault.jpg';
            
        }
        if ($is_show == 1) {            
            $ims->temp_act->reset("img_detail");
            $ims->temp_act->assign('info', $info);
            $ims->temp_act->parse("img_detail");
            $output = $ims->temp_act->text("img_detail");
        }
        return $output;
    }

    function promotion_code($info){
        global $ims;
        require_once ("ordering_func.php");
        $this->orderiFunc = new OrderingFunc($this);

        $where = ' and ((type_promotion = "apply_all" OR type_promotion = "apply_freeship") and num_use < max_use) OR (type_promotion = "apply_product" and find_in_set('.$info['item_id'].', list_product))';
        if($ims->site_func->checkUserLogin() == 1) {
            $where .= ' OR (type_promotion = "apply_user" and find_in_set('.$ims->data['user_cur']['user_id'].', list_user))';
        }

        $result = $ims->db->load_item_arr('promotion', 'is_show = 1 and date_start < '.time().' and date_end > '.time().$where, 'promotion_id, type_promotion, picture, short, value_type, value, num_use, max_use, list_user, list_product, date_end');
        if($result){
            $i = 0;
            foreach ($result as $row){
                if(in_array($row['type_promotion'], array('apply_product','apply_user'))){
                    $check = $this->orderiFunc->check_promotion($row, $info['item_id']);
                    if($check == 0){
                        continue;
                    }
                }
                $i++;
                $row['short'] = $ims->func->input_editor_decode($row['short']);
                if($row['type_promotion'] == 'apply_freeship'){
                    $row['title'] = $ims->lang['product']['free_ship'];
                }else{
                    $row['title'] = $ims->lang['product']['decrease'].' '.(($row['value_type'] == 1) ? $row['value'].'%' : number_format($row['value'],0,',','.').'đ');
                }
                $row['pic'] = $ims->conf['rooturl'].'resources/images/promotion.jpg';
                if(!empty($row['picture'])){
                    $row['pic'] = $ims->func->get_src_mod($row['picture'],60,60);
                }
                $row['promotion_code'] = $ims->func->get_friendly_link($row['promotion_id']);
                $row['date_end'] = date('d/m/Y', $row['date_end']);
                $ims->temp_act->assign('row', $row);
                $ims->temp_act->parse('promotion_code.title');
                $ims->temp_act->parse('promotion_code.item');
                $ims->temp_act->parse('promotion_code.items');
            }
            if($i > 0){
                $ims->temp_act->parse('promotion_code');
                return $ims->temp_act->text('promotion_code');
            }
        }
    }

    function do_combo($combo_id){
        global $ims;
        $data = array();
        $where = '';

        $combo = $ims->db->load_row('combo', $ims->conf['qr'].$where.' and item_id = '.$combo_id, 'item_id, title, type, value, value_type, arr_product, arr_gift, arr_include');
        if($combo){
            $data['combo_id'] = $combo['item_id'];
            $data['combo_title'] = $ims->lang['product']['combo_type_'.$combo['type']];
            $data['type_select'] = ($combo['type']==0)?mb_strtolower($ims->lang['product']['gift']):mb_strtolower($ims->lang['product']['include']);
            $data['select'] = $ims->lang['product']['choose'].' '.$data['type_select'];
            if($combo['type'] == 0){
                $data['type'] = 'gift';
            }
            if($combo['type'] == 2){
                $data['type'] = 'include';
            }

            $arr_in = array(
                'where' => ' and item_id IN ('.$combo['arr_product'].') order by show_order DESC, date_create DESC',
                'paginate' => 0
            );
            $data['product_in_combo'] = $this->modFunc->html_list_item($arr_in);

            if($combo['type'] == 0){
                $arr_gift = $ims->db->load_item_arr('user_gift', $ims->conf['qr'].' and item_id IN('.$combo['arr_gift'].') and quantity_combo > 0 order by FIELD(item_id,"'.$combo['arr_gift'].'") desc', 'title, picture, product_id');
                if($arr_gift){
                    $data['select_button'] = '<a class="btn-combo" data-combo="'.$data['combo_id'].'">'.$data['select'].'</a>';
                    foreach ($arr_gift as $gift){
                        $gift['picture'] = $ims->func->get_src_mod($gift['picture']);
                        if($gift['product_id'] > 0){
                            $link = $ims->db->load_item('product', $ims->conf['qr'].' and item_id = '.$gift['product_id'], 'friendly_link');
                            $gift['link'] = 'href="'.$ims->func->get_link($link, '').'"';
                        }else{
                            $gift['link'] = '';
                        }
                        $ims->temp_act->assign('gift', $gift);
                        $ims->temp_act->parse("box_combo.gift_include.list_gift.gift");
                    }
                    $ims->temp_act->assign('data', $data);
                    $ims->temp_act->parse("box_combo.gift_include.list_gift");
                    $ims->temp_act->parse("box_combo.gift_include");
                    $ims->func->include_js_content('
                        $(".box_combo .box_gift_include .row_item_gift").slick({
                            arrows: !0,
                            dots: !1,
                            infinite: !0,
                            autoplay: !0,
                            autoplaySpeed: 3500,
                            speed: 500,
                            slidesToShow: 3,
                            swipeToSlide: !0,
                            lazyload:"ondemand",
                            responsive: [{
                                breakpoint: 901,
                                settings: {
                                    slidesToShow: 4,
                                }
                            }, {
                                breakpoint: 769,
                                settings: {
                                    slidesToShow: 3,
                                }
                            }, {
                                breakpoint: 601,
                                settings: {
                                    slidesToShow: 2,
                                }
                            }, {
                                breakpoint: 365,
                                settings: {
                                    slidesToShow: 1,
                                }
                            }]
                        });
                    ');
                }//else{
//                    $ims->temp_act->assign('empty', $ims->lang['product']['out_of_gift']);
//                    $ims->temp_act->parse("box_combo.empty");
                //}
            }
            if($combo['type'] == 2){
                $result = $ims->db->load_row_arr('product', $ims->conf['qr'].' and item_id IN('.$combo['arr_include'].') and quantity_include > 0 order by FIELD(item_id,"'.$combo['arr_include'].'") desc');
                if($result){
                    $data['select_button'] = '<a class="btn-combo" data-combo="'.$data['combo_id'].'">'.$data['select'].'</a>';
                    foreach ($result as $row){
                        $row['price'] = $row['price_buy'];
                        $row['price_buy'] = ($combo['value_type'] == 1) ? $row['price_buy']*((100 - $combo['value'])/100) : ($row['price_buy'] - $combo['value']);
                        if($row['price_buy'] < 0){
                            $row['price_buy'] = 0;
                        }
                        $row['percent_discount'] = $combo['value'];
                        $row['pic_w'] = 400;
                        $row['pic_h'] = 460;
                        $row['item_include'] = $this->modFunc->mod_item($row);
                        $ims->temp_act->assign('row', $row);
                        $ims->temp_act->parse("box_combo.gift_include.list_include.include");
                    }
                    $ims->temp_act->assign('data', $data);
                    $ims->temp_act->parse("box_combo.gift_include.list_include");
                    $ims->temp_act->parse("box_combo.gift_include");
                    $ims->func->include_js_content('
                        $(".box_combo .box_gift_include .list_item_product .row_item").slick({
                            arrows: !0,
                            dots: !1,
                            infinite: !0,
                            autoplay: !0,
                            autoplaySpeed: 3500,
                            speed: 500,
                            slidesToShow: 3,
                            swipeToSlide: !0,
                            lazyload:"ondemand",
                            responsive: [{
                                breakpoint: 901,
                                settings: {
                                    slidesToShow: 4,
                                }
                            }, {
                                breakpoint: 769,
                                settings: {
                                    slidesToShow: 3,
                                }
                            }, {
                                breakpoint: 601,
                                settings: {
                                    slidesToShow: 2,
                                }
                            }, {
                                breakpoint: 365,
                                settings: {
                                    slidesToShow: 1,
                                }
                            }]
                        });
                    ');
                }//else{
//                    $ims->temp_act->assign('empty', $ims->lang['product']['out_of_include']);
//                    $ims->temp_act->parse("box_combo.empty");
                //}
            }
            $ims->temp_act->assign('data', $data);
            $ims->temp_act->parse("box_combo");
            return $ims->temp_act->text("box_combo");
        }
    }

    function do_related($info) {
        global $ims;

        $arr_in = array(
            'where' => " AND item_id!='" . $info['item_id'] . "' ",
            'num_list' => $ims->setting['product']["num_order_detail"],
            'paginate' => 0,
        );
        if ($info['group_id'] > 0) {
            $arr_in['where'] .= " AND FIND_IN_SET('" . $info['group_id'] . "', group_nav)";
        }
        $check = $ims->db->load_item('product', $ims->conf['qr'].$arr_in['where'],'item_id');
        if($check){
            $content = $this->modFunc->html_list_item($arr_in);
            $ims->temp_act->assign('content', $content);
            $ims->temp_act->parse("list_other");
            return $ims->temp_act->text("list_other");
        }
    }

    function do_news_related($info){
        global $ims;

        $ims->func->load_language('news');
        $output = '';
        $title = $ims->lang['product']['news'];
        $arr_in = array(
            // 'where' => ' AND find_in_set("'.$info['item_id'].'",item_related)>0 ORDER BY num_view DESC, show_order DESC, date_create DESC',
            'where' => ' ORDER BY num_view desc, show_order DESC, date_create DESC',
            'num_list' => 10,
            'paginate' => 0,
        );
        $output .= "<div class='box_news'><div class='title'><span>".$title."</span></div>";
        $output .= $ims->call->mFunc('news','html_list_item',array($arr_in));
        $output .= "</div>";
        return $output;
    }
    // -------- END ------------
    function do_detail1($data){
        global $ims;

        $favorite = $ims->site->check_favorite($data['item_id']);
        if (!empty($favorite)) {
            $data['i_favorite'] = $ims->func->if_isset($favorite["class"]);
            $data['added'] = $ims->func->if_isset($favorite["added"]);
        }
        $data['background'] = ($data['picture'] != '') ? $ims->func->get_src_mod($data['picture'], 1366, 768, 1, 1) : $ims->conf['rooturl'].'resources/images/bg_detail.png';
        $data['pic_zoom'] = $ims->func->get_src_mod($data['picture']);
        $data['picture_form'] = $ims->func->get_src_mod($data['picture'], 540, 250, 1, 1);
        $data['picture'] = $ims->func->get_src_mod($data['picture'], 720, 360);
        $data['title'] = $ims->func->input_editor_decode($data['title']);
        $data['title1'] = ($data['title1'] != '') ? $data['title1'].': ' : '';
        $data['e_title'] = strip_tags($data['title']);
        $data['content'] = $ims->func->input_editor_decode($data['content']);
        $data['event_same_organization'] = $this->do_same_organization($data);
        $data['event_other'] = $this->do_event_other($data);
        if($data['event_other'] != '' || $data['event_same_organization'] != ''){
            $data['border'] = 'borders';
        }
        $data['store'] = $this->do_store($data['list_prd_store'], $data['item_id']);
        $data['link_share'] = $ims->site_func->get_link('product', $data['friendly_link']);
        if($data['organizer'] != ''){
            $data['organizational'] = '<div class="organizational">'.$ims->lang['product']['organizational'].': <span>'.$data['organizer'].'</span></div>';
        }

        $user_info = $ims->db->load_row('user', 'user_id = '.$data['user_id'], 'num_follow');
        if($user_info){
            $follow = $ims->site->check_follow($data['item_id']);
            $num_follow = number_format($user_info['num_follow'],0,',','.');

            $btn_follow = '';
            if(!isset($follow['none_follow']) || (isset($follow['none_follow']) && $follow['none_follow'] != 'none')){
                $btn_follow = '<p class="btn_follow '.$follow['none_follow'].'"><button data-item="'.$ims->func->base64_encode($data['item_id']).'" class="'.$follow['class_follow'].'">'.$follow['text_follow'].'</button></p>';
            }
            $data['follow'] = '<div class="follow">
                                    <p class="num">'.$ims->lang['product']['num_follow'].': <span>'.$num_follow.'</span></p>
                                    '.$btn_follow.'
                                </div>';
        }

        $date_begin = $data['date_begin'];
        $data['date_begin'] = $ims->lang['global']['day_'.date('N', $data['date_begin'])].date(', d/m, h:i A', $data['date_begin']);
        if(date('d', $date_begin) == date('d',$data['date_end']) && date('m', $date_begin) == date('m',$data['date_end'])){
            $data['time'] = $data['date_begin'].' - '.date('h:i A', $data['date_end']);
        }else{
            $data['time'] = $data['date_begin'].' - '.$ims->lang['global']['day_'.date('N', $data['date_end'])].date(', d/m, h:i A', $data['date_end']);
        }

        $arr_price = $ims->func->unserialize($data['arr_price']);
        $price = array();
        foreach ($arr_price as $row){
            $price[] = $row['price'];
        }
        if($price){
            sort($price);
            $data['price'] = number_format($price[0],0,',','.').' '.$ims->lang['global']['unit'];
            if(count($price) > 1){
                $data['price'] .= ' - '.number_format($price[count($price) - 1],0,',','.').' '.$ims->lang['global']['unit'];
            }
        }
        if($data['type_event'] == 'offline'){
            $data['maps'] = '<div class="maps">'.$ims->func->input_editor_decode($data['frame_maps']).'</div>';
            $data['link_event_maps'] = '<div class="see_maps"><a href="#address" class="goto">'.$ims->lang['product']['see_maps'].'</a></div>';
            $data['location'] = $ims->lang['product']['location'];
        }else{
            $data['address'] = '';
            $data['link_event_maps'] = '<div class="see_maps"><a href="'.$data['link_event'].'" target="_blank">'.$data['link_event'].'</a></div>';
            $data['link_event_text'] = '<div class="see_maps"><span>'.$ims->lang['product']['link_event'].':</span> <a href="'.$data['link_event'].'" target="_blank">'.$data['link_event'].'</a></div>';
            $data['location'] = $ims->lang['product']['link_event'];
        }
        $data['item_id'] = $ims->func->base64_encode($data['item_id']);
        if (isset($ims->get['vnp_TxnRef']) && isset($ims->get['vnp_SecureHash'])) {
            $output_mess = $ims->site_func->paymentCustomComplete('vnpay', $data['link_share'], 'event_order');
            if($output_mess['status_payment'] == 'success'){
                $arr_info_booked = Session::Get('arr_info_booked', array());
                if(!empty($arr_info_booked)){
                    $ims->func->include_js_content('
                        $("#register").modal("show");
                        imsProduct.load_complete_order_event();
                        imsProduct.load_cart_info(3);
                    ');
                }else{
                    $ims->html->redirect_rel($data['link_share']);
                }
            }else{
                Session::Delete('arr_info_booked');
                $ims->func->include_js_content("
                    Swal.fire({
                        icon: 'error',
                        title: lang_js['aleft_title'],
                        text: '".$output_mess['notification_payment']."',
                    }).then((result) => {
                        go_link('".$data['link_share']."');
                    });
                ");
            }
        }

        $ims->temp_act->assign('data', $data);
        $ims->temp_act->parse("detail1");
        return $ims->temp_act->text("detail1");
    }

    function do_same_organization($data){
        global $ims;

        $arr_in = array(
            'where' => ' and item_id != '.$data['item_id'].' and user_id = '.$data['user_id'],
            'paginate' => 0,
            'num_list' => $ims->setting['product']['num_order_detail']
        );
        $check = $ims->db->load_item('product', $ims->conf['qr'].$arr_in['where'], 'item_id');
        if ($check){
            $content = $this->modFunc->html_list_item($arr_in);
            return '<div class="other same_organization"><div class="other_title">'.$ims->lang['product']['same_organization'].'</div>'.$content.'</div>';
        }else{
            return '';
        }
    }

    function do_event_other($data){
        global $ims;

        $arr_in = array(
            'where' => ' and item_id != '.$data['item_id'].' and (find_in_set('.$data['group_id'].', group_nav) or find_in_set('.$data['group_id'].', group_related))',
            'paginate' => 0,
            'num_list' => $ims->setting['product']['num_order_detail']
        );
        $check = $ims->db->load_item('product', $ims->conf['qr'].$arr_in['where'], 'item_id');
        if ($check){
            $content = $this->modFunc->html_list_item($arr_in);
            return '<div class="other event_other"><div class="other_title">'.$ims->lang['product']['other_event'].'</div>'.$content.'</div>';
        }else{
            return '';
        }
    }
    function do_store($list_prd_store, $item_id){
        global $ims;

        if($list_prd_store){
            $where = ' and item_id IN ('.$list_prd_store.') order by show_order desc, date_create desc';
            $result = $ims->db->load_item_arr('product_store', $ims->conf['qr'].$where.' limit '.$ims->setting['product']['num_list_store'], 'item_id, title1, title, price, picture');
            $total = $ims->db->do_count('product_store', $ims->conf['qr'].$where, 'item_id');
            $show_more = '';
            if($total > count($result)){
                $show_more = '<div class="show_more"><input type="hidden" name="start" value="'.count($result).'" data-it="'.$item_id.'"><button>'.$ims->lang['product']['load_more'].'<i></i></button></div>';
            }
            if($result){
                foreach ($result as $row){
                    $row['picture'] = $ims->func->get_src_mod($row['picture'], 210, 165, 1, 1);
                    $row['price'] = $ims->lang['product']['price_buy'].': '.number_format($row['price'], 0, ',', '.').' vnđ';
                    $row['title1'] = ($row['title1'] != '') ? $row['title1'].': ' : '';
                    $row['item_id'] = $ims->func->base64_encode($row['item_id']);
                    $ims->temp_act->assign('row', $row);
                    $ims->temp_act->parse("store.item");
                }
                $ims->temp_act->assign('show_more', $show_more);
                $ims->temp_act->parse("store");
                return $ims->temp_act->text("store");
            }
        }
    }

    function do_view_ticket(){
        global $ims;
        $arr_info_booked = Session::Get('arr_info_booked', array());

//        $arr_info_booked = array(
//            'event_item' => 17,
//            'order_id' => 2
//        );
        if($ims->site_func->checkUserLogin() == 1) {
            $user_order = $ims->db->load_item('event_order', 'order_id = '.$arr_info_booked['order_id'], 'user_id');
            if($user_order != $ims->data['user_cur']['user_id']){
                Session::Delete('arr_info_booked');
                $arr_info_booked = array();
            }
        }else{
            Session::Delete('arr_info_booked');
            $arr_info_booked = array();
        }

        if($arr_info_booked){
            $data = array();
            $event = $ims->db->load_row('product', $ims->conf['qr'].' and item_id = '.$arr_info_booked['event_item']);
            $event['title1'] = ($event['title1'] != '') ? $event['title1'].': ' : '';
            $event['link'] = $ims->site_func->get_link('product', $event['friendly_link']);

            $province = $ims->db->load_item('location_province', 'lang = "'.$ims->conf['lang_cur'].'" and code = "'.$event['province'].'"', 'title');
            $date_begin = $event['date_begin'];
            $event['date_begin'] = $ims->lang['global']['day_'.date('N', $event['date_begin'])].date(', d/m, h:i A', $event['date_begin']);
            if(date('d', $date_begin) == date('d',$event['date_end']) && date('m', $date_begin) == date('m',$event['date_end'])){
                $event['event_info'] = $event['date_begin'].' - '.date('h:i A', $event['date_end']).', '.$province;
            }else{
                $event['event_info'] = $event['date_begin'].' - '.$ims->lang['global']['day_'.date('N', $event['date_end'])].date(', d/m, h:i A', $event['date_end']).', '.$province;
            }
            if(isset($ims->get['edit']) && $ims->get['edit'] == 1){
                $data['elm'] = 'form';
                $data['form_info'] = 'action="" method="post" id="edit_ticket"';
            }else{
                $data['elm'] = 'div';
            }
            $list_ticket = $ims->db->load_item_arr('event_order_detail', 'order_id = '.$arr_info_booked['order_id'].' order by detail_id asc', 'detail_id, full_name, phone, email, age, title');
            $i = 0;
            foreach ($list_ticket as $ticket){
                $i++;
                $ticket['disable'] = (isset($ims->get['edit']) && $ims->get['edit'] == 1) ? '' : 'disabled';
                $ticket['title'] = $ims->lang['product']['ticket'].' '.$i.' - '.$ticket['title'];
                $ticket['detail_id'] = $ims->func->base64_encode($ticket['detail_id']);
                if($i == 1){
                    $ticket['button_edit'] = '<a href="'.$event['link'].'/?view_ticket=1&edit=1"><img src="'.$ims->conf['rooturl'].'resources/images/use/edit.svg" />'.$ims->lang['product']['edit'].'</a>';
                }
                $ims->temp_act->assign('ticket', $ticket);
                $ims->temp_act->parse("view_ticket.item_ticket");
            }
            $ims->temp_act->assign('data', $data);
            $ims->temp_act->assign('event', $event);
            if(isset($ims->get['edit']) && $ims->get['edit'] == 1){
                $ims->temp_act->parse("view_ticket.list_button");
            }
            $list_advisory = $ims->db->load_item_arr('advisory', $ims->conf['qr'].' order by show_order desc, date_create desc', 'title, friendly_link');
            if($list_advisory){
                foreach ($list_advisory as $row){
                    $row['link'] = $ims->site_func->get_link('advisory', $row['friendly_link']);
                    $ims->temp_act->assign('row', $row);
                    $ims->temp_act->parse("view_ticket.list_advisory.advisory");
                }
                $ims->temp_act->parse("view_ticket.list_advisory");
            }
            $ims->temp_act->parse("view_ticket");
            return $ims->temp_act->text("view_ticket");
        }else{
            return $this->do_detail1($ims->data['cur_item']);
        }
    }
}
?>