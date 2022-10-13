<?php
if (! defined('IN_ims')) {
  die('Access denied');
}
$nts = new sMain();
class sMain{
    var $modules = "product";
    var $action = "promotion";
    var $sub = "manage";

    function __construct (){
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

        require($this->modules . "_func.php");
        $this->modFunc = new productFunc($this);
        
        $data = array();
        if(isset($ims->conf['cur_item']) && isset($ims->data['cur_item']) && $ims->data['cur_item']){
            //Make link lang
            foreach ($ims->data['lang'] as $row_lang) {
                $ims->data['link_lang'][$row_lang['name']] = $ims->site_func->get_link_lang($row_lang['name'], $this->modules);
            }
            if($ims->data['cur_item']['date_begin'] > time() || $ims->data['cur_item']['date_end'] < time()){
                require_once ($ims->conf["rootpath"]."404.php");die;
            }
            //End Make link lang
            //SEO
//            $ims->site->get_seo(array(
//                'meta_title' => (isset($ims->data['cur_item']["meta_title"])) ? $ims->data['cur_item']["meta_title"] : '',
//                'meta_key' => (isset($ims->data['cur_item']["meta_key"])) ? $ims->data['cur_item']["meta_key"] : '',
//                'meta_desc' => (isset($ims->data['cur_item']["meta_desc"])) ? $ims->data['cur_item']["meta_desc"] : ''
//            ));
            $data['content'] = $this->do_list($ims->data['cur_item']);
        }else{
            //Make link lang
            foreach($ims->data['lang'] as $row_lang) {
                $ims->data['link_lang'][$row_lang['name']] = $ims->site_func->get_link_lang ($row_lang['name'], $this->modules);
            }
            //End Make link lang
            //SEO
//            $ims->site->get_seo(array(
//                'meta_title' => (isset($ims->setting['product']["promotion_meta_title"])) ? $ims->setting['product']["promotion_meta_title"] : '',
//                'meta_key' => (isset($ims->setting['product']["promotion_meta_key"])) ? $ims->setting['product']["promotion_meta_key"] : '',
//                'meta_desc' => (isset($ims->setting['product']["promotion_meta_desc"])) ? $ims->setting['product']["promotion_meta_desc"] : ''
//            ));
            $ims->conf["cur_group"] = 0;
            $data['content'] = $this->do_list();
        }
        $ims->conf['page_title'] = '<div class="title">'.$ims->lang['product']['product'].'</div>';
        $ims->conf['container_layout'] = 'full';
        $ims->temp_act->assign('data', $data);
        $ims->temp_act->parse("main");
        $ims->output .=  $ims->temp_act->text("main");
    }

    function do_list_product_bo(){
        global $ims;

        $data = array();
        if(isset($ims->data['cur_item'])){
            if($ims->data['cur_item']['date_end'] < time() || time() < $ims->data['cur_item']['date_begin']){
                // $ims->html->redirect_rel($ims->site_func->get_link('home'));
            }
        }
        $text_search = (isset($ims->input['keyword'])) ? $ims->input['keyword'] : '';
        $data['text_search'] = '';
        $ext = '&keyword=' . $text_search;
        $arr_key = explode(' ', $text_search);
        $where = '';
        $arr_tmp = array();
        foreach ($arr_key as $value) {
            $value = trim($value);
            if (!empty($value)) {
                $arr_tmp[] = "title like '%" . $value . "%'";
            }
        }
        if (count($arr_tmp) > 0) {         
            $where .= " and (" . implode(" and ", $arr_tmp) . ")";
        }
        $array_view_group = array();
        $view_group = isset($ims->get['view_group']) ? $ims->get['view_group'] : '';
        if($view_group != '' && strpos($view_group, ',') !== false){
            $view_group = explode(',', $view_group);
            if($view_group[1] != ''){
                foreach ($view_group as $key => $value) {
                    array_push($array_view_group, 'group_id= '. $value);
                }
                if(isset($array_view_group) && !empty($array_view_group) && is_array($array_view_group)){
                    $array_view_group = implode(' or ', $array_view_group);
                    $where .= 'and ('. $array_view_group. ')';
                }
            }
            else{
                $where .= 'and group_id= '. $view_group[0];
            }
        }
        elseif($view_group != ''){
            $where .= 'and group_id= '. $view_group;
        }
        // print_arr($ims->data['cur_item']);
        $where .= $ims->site_func->list_product_bypromotion($ims->data['cur_item']);
        $sort = isset($ims->get['sort']) ? $ims->get['sort'] : '';
        if($sort){
            if($sort == 'price-desc'){
                $where .= " ORDER BY price_buy DESC";
            }
            else if($sort == 'price-asc'){
                $where .= " ORDER BY price_buy ASC";
            }
            else if($sort == 'title-asc'){
                $where .= " ORDER BY title ASC";
            }
            else if($sort == 'stock-desc'){
                $where .= "ORDER BY out_stock DESC";
            }
            else if($sort == 'title-desc'){
                $where .= " ORDER BY title DESC";
            }
            else if($sort == 'new'){
                $where .= " and is_new = 1";
            }
        }
        $arr_in = array(
            'link_action' => $ims->site_func->get_link('product'),
            'where' => $where,
            'temp' => 'list_item',
        );
        $res_num = $ims->db->query("select item_id 
                            from product 
                            where is_show=1 
                            and lang='" . $ims->conf["lang_cur"] . "' 
                            " . $where . " ");
        $num_total = $ims->db->num_rows($res_num);
        $data = array(
            'num_total' => $num_total,
            'content' => $this->modFunc->html_list_item($arr_in, 1),
            'title' => $ims->lang['product']['product'],
            'link_sort' => $ims->conf["rooturl"].$ims->conf["cur_mod_url"].'/',
            'data_lang' => array(
                'product' => $ims->site_func->get_lang('product', 'product'),
                'sort_by' => $ims->site_func->get_lang('sort_by', 'product'),
                'stock_desc' => $ims->site_func->get_lang('stock_desc', 'product'),
                'new_product' => $ims->site_func->get_lang('new_product', 'product'),
                'price_asc' => $ims->site_func->get_lang('price_asc', 'product'),
                'price_desc' => $ims->site_func->get_lang('price_desc', 'product'),
                'title_asc' => $ims->site_func->get_lang('title_asc', 'product'),
                'title_desc' => $ims->site_func->get_lang('title_desc', 'product'),
                'select' => $ims->site_func->get_lang('select', 'product'),
                'list' => $ims->site_func->get_lang('list', 'product'),
                'grid' => $ims->site_func->get_lang('grid', 'product')
            )
        );
        if($text_search != ''){
            $data['text_search'] = $ims->site_func->get_lang('text_search', 'search', array(
                    '[num_total]' => "<b>".$num_total."</b>",
                    '[keyword]' => "<b>".$text_search."</b>"
                ));
        }        
        $data['link_product'] = $ims->site_func->get_link('product');
        $ims->func->include_js_content('
            matchHeight($(".promo-product .list_item_product .info .info-title"));matchHeight($(".promo-product .list_item_product .info .info-price"));
        ');
        $this->temp_act->assign('data', $data);
        $this->temp_act->parse("list_item");
        return $this->temp_act->text("list_item");
    }
    function do_list_bo (){
        global $ims;
        $keyword = (isset($ims->input['keyword'])) ? trim($ims->input['keyword']) : '';
        $tag = (isset($ims->input['tag'])) ? trim($ims->input['tag']) : '';
        $ext = '';
        $where = '';
        if($keyword) {
            $ext = '&keyword='.$keyword;
            $arr_key = explode(' ',$keyword);
            $arr_tmp = array();
            foreach($arr_key as $value) {
                $value = trim($value);
                if(!empty($value)) {
                    $arr_tmp[] = "title like '%".$value."%'";
                }   
            }
            if(count($arr_tmp) > 0) {
                $where .= " and (".implode(" and ",$arr_tmp).")";
            }
        } elseif($tag) {
            $ext = '&tag='.$tag;
            $where .= " and find_in_set('".$tag."', tag_list)";
        }
        // $where .= " and date_begin <= '".time()."' and '".time()."' <= date_end ";
        // $where .= " and '".time()."' <= date_end ";
        $where .= " and date_begin <= '".time()."' ";
        $arr_in = array(
            'link_action' => $ims->site_func->get_link ('news'),
            'where' => $where,
            'ext' => $ext,
            'temp' => 'list_item_promotion',
        );
        $list_item = $this->modFunc->html_list_promotion($arr_in,1);
        $data = array(
            'content' => $list_item,
            'class' => 'news_content',
        );
        $this->temp_act->assign('data', $data);
        $this->temp_act->parse("promotion");
        return $this->temp_act->text("promotion");
    }
    // end class
    function do_list($info) {
        global $ims;
        $data = array();

        $ims->temp_act->assign('CONF', $ims->conf);
        if($info['arr_picture'] != ''){
            $arr_picture = $ims->func->unserialize($info['arr_picture']);
            $banner = '';
            foreach ($arr_picture as $item){
                $picture = $ims->func->get_src_mod($item);
                $banner .= '<div class="banner_item"><a href=""><img src="'.$picture.'" alt="'.$info['title'].'"></a></div>';
            }
            $data['banner'] = '<div class="banner_forproduct banner_full">'.$banner.'</div>';
            $ims->func->include_js_content('
                $(".banner_forproduct").slick({
                    autoplay: !0,
                    autoplaySpeed: 5000,
                    speed: 2000,
                    swipe: !1,
                    dots: !1,
                    infinite: !0,
                    slidesToShow: 1,
                    arrows: !1,
                });
            ');
        }

        $check = $ims->db->load_item('product', $ims->conf['qr'].' and is_focus = 1', 'item_id');
        if($check){
            $arr_shock = array(
                'where' => ' and is_focus = 1 ORDER BY show_order DESC, date_create DESC',
                'paginate' => 0,
                'num_list' => 50,
            );
            $ims->func->include_js_content('
                $(".header_page .product_is_focus .list_item_product .row_item").slick({
                    arrows: !0,
                    dots: !1,
                    infinite: !0,
                    autoplay: !1,
                    autoplaySpeed: 3500,
                    speed: 500,
                    slidesToShow: 5,
                    swipeToSlide: !0,
                    lazyload:"ondemand",
                    responsive: [{
                        breakpoint: 1101,
                        settings: {
                            slidesToShow: 4,
                            slidesToScroll: 3,
                            infinite: !0
                        }
                    }, {
                        breakpoint: 769,
                        settings: {
                            slidesToShow: 3,
                            slidesToScroll: 3,
                            infinite: !0
                        }
                    }, {
                        breakpoint: 601,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 3,
                            infinite: !0
                        }
                    }, {
                        breakpoint: 365,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 2,
                            infinite: !0
                        }
                    }]
                });
            ');
            $content = $this->modFunc->html_list_item($arr_shock);
            $ims->temp_act->assign('content', $content);
            $ims->temp_act->parse("header_page.is_focus");
        }
        $data['list_product'] = $this->do_list_product($info);
        $ims->temp_act->assign('data', $data);
        $ims->temp_act->parse("header_page");
        return $ims->temp_act->text("header_page");
    }
    function do_list_product($info){
        global $ims;
        $where = '';

        if($info['apply_product'] != ''){
            $list_group_nav = $ims->db->load_item_arr('product', $ims->conf['qr'].' and item_id IN ('.$info['apply_product'].') order by show_order desc, date_create desc', 'distinct group_nav');
            $where = ' and item_id IN ('.$info['apply_product'].')';
        }elseif ($info['apply_group'] != ''){
            $list_group_nav = $ims->db->load_item_arr('product_group', $ims->conf['qr'].' and group_id IN ('.$info['apply_group'].') order by show_order desc, date_create desc', 'distinct group_nav');
        }

        $arr_root = array();
        $arr_child = array();

        foreach ($list_group_nav as $gr){
            $group = explode(',', $gr['group_nav']);
            $arr_root[] = $group[0];
            if(count($group) > 1){
                $arr_child[$group[0]][] = $group[count($group)-1];
            }
        }
        $arr_root = array_unique($arr_root);
        foreach ($arr_child as $key => $child){
            $arr_child[$key] = implode(',', $child);
        }

        $num = 10;
        foreach ($arr_root as $group){
            $child = (isset($arr_child[$group])) ? $arr_child[$group] : '';
            $gr_result = $ims->db->load_row('product_group', $ims->conf['qr'].' and group_id = '.$group, 'arr_picture, title, friendly_link');
            if($gr_result['arr_picture']){
                $gr_result['slide'] = $this->slide_gr_picture($gr_result);
            }
            if($child){
                $list_gr_child = $ims->db->load_item_arr('product_group', $ims->conf['qr'].' and group_id IN('.$child.') order by show_order desc, date_create desc', 'title, group_id, friendly_link');
                $i = 0;
                foreach ($list_gr_child as $row){
                    $i++;
                    $row['active'] = ($i == 1) ? 'active' : '';
                    $arr_in = array(
                        'where' => ' and find_in_set('.$row['group_id'].', group_nav) '.$where.' order by show_order desc, date_create desc',
                        'num_list' => $num,
                        'paginate' => 0
                    );
                    $count = $ims->db->do_get_num("product", $ims->conf['qr'].$arr_in['where']);
                    if($count > $num){
                        $row['more'] = '<div class="see_more_header"><a data-group="'.$row['group_id'].'" data-where="'.$where.'" data-start="'.$num.'" data-limit="'.($count-$num).'">'.$ims->site_func->get_lang('view_more_trogia', 'product', array('[num]' => $count-$num)).'</a></div>';
                    }
                    $row['content'] = $this->modFunc->html_list_item($arr_in);
                    $ims->temp_act->assign('row', $row);
                    $ims->temp_act->parse("list_product.item_group.content_tab.li");
                    $ims->temp_act->parse("list_product.item_group.content_tab.content");
                }
                $ims->temp_act->parse("list_product.item_group.content_tab");
                $ims->temp_act->assign('data', $gr_result);
                $ims->temp_act->parse("list_product.item_group");
            }else{
                $row = array();
                $arr_in = array(
                    'where' => ' and find_in_set('.$group.', group_nav) '.$where.' order by show_order desc, date_create desc',
                    'num_list' => $num,
                    'paginate' => 0
                );
                $count = $ims->db->do_get_num("product", $ims->conf['qr'].$arr_in['where']);
                if($count > $num){
                    $row['more'] = '<div class="see_more_header"><a data-group="'.$group.'" data-where="'.$where.'" data-start="'.$num.'" data-limit="'.($count-$num).'">'.$ims->site_func->get_lang('view_more_trogia', 'product', array('[num]' => $count-$num)).'</a></div>';
                }
                $row['group_id'] = $group;
                $row['active'] = 'active';
                $row['title'] = $gr_result['title'];
                $row['content'] = $this->modFunc->html_list_item($arr_in);
                $ims->temp_act->assign('row', $row);
                $ims->temp_act->parse("list_product.item_group.content_tab.li");
                $ims->temp_act->parse("list_product.item_group.content_tab.content");
                $ims->temp_act->parse("list_product.item_group.content_tab");
                $ims->temp_act->assign('data', $gr_result);
                $ims->temp_act->parse("list_product.item_group");
            }
        }
        $ims->temp_act->parse("list_product");
        return $ims->temp_act->text("list_product");
    }
    function slide_gr_picture($data){
        global $ims;
        $out = '<div class="slide_pic_group">';
        $arr_pic = $ims->func->unserialize($data['arr_picture']);
        foreach ($arr_pic as $pic){
            $pic = $ims->func->get_src_mod($pic);
            $out .= '<div class="item_pic"><a href="'.$ims->func->get_link($data['friendly_link'], '').'"><img src="'.$pic.'" alt="'.$data['title'].'"></a></div>';
        }
        $ims->func->include_js_content('
            $(".slide_pic_group").slick({
                autoplay: !0,
                autoplaySpeed: 5000,
                speed: 2000,
                swipe: !1,
                dots: !1,
                infinite: !0,
                slidesToShow: 1,
                arrows: !1,
                fade: true,
            });
        ');
        return $out .= '</div>';
    }
}
?>