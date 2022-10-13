<?php
if (!defined('IN_ims')) { die('Access denied'); }
$nts = new sMain();

class sMain {

    var $modules = "product";
    var $action  = "header";

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

        require($this->modules . "_func.php");
        $this->modFunc = new productFunc($this);

        $data = array();
//        foreach ($ims->data['lang'] as $row_lang) {
//            $ims->data['link_lang'][$row_lang['name']] = $ims->site_func->get_link_lang($row_lang['name'], $this->modules);
//        }

        $ims->conf["cur_group"] = 0;
        $data['content'] = $this->do_list();

        $ims->conf['container_layout'] = 'full';
        $ims->conf["class_full"] = 'trogia';
        $ims->temp_act->assign('data', $data);
        $ims->temp_act->parse("main");
        $ims->output .= $ims->temp_act->text("main");
    }

    function do_list() {
        global $ims;
        $data = array();
        $ims->temp_act->assign('CONF', $ims->conf);
        $banner = $ims->site->get_banner('banner-forproduct-'.$ims->conf['cur_act_distinct'], 10, 0);
        if($banner){
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
        $data['list_product'] = $this->do_list_product();
        $ims->temp_act->assign('data', $data);
        $ims->temp_act->parse("header_page");
        return $ims->temp_act->text("header_page");
    }
    function do_list_product(){
        global $ims;

        $list_group_nav = $ims->db->load_item_arr('product', $ims->conf['qr'].' and is_'.$ims->conf['cur_act_distinct'].' = 1 order by show_order desc, date_create desc', 'distinct group_nav');
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
                $list_gr_child = $ims->db->load_item_arr('product_group', $ims->conf['qr'].' and group_id  IN('.$child.') order by show_order desc, date_create desc', 'title, group_id, friendly_link');
                $i = 0;
                foreach ($list_gr_child as $row){
                    $i++;
                    $row['active'] = ($i == 1) ? 'active' : '';
                    $arr_in = array(
                        'where' => ' and find_in_set('.$row['group_id'].', group_nav) and is_'.$ims->conf['cur_act_distinct'].' = 1 order by show_order desc, date_create desc',
                        'num_list' => $num,
                        'paginate' => 0
                    );
                    $count = $ims->db->do_get_num("product", $ims->conf['qr'].$arr_in['where']);
                    if($count > $num){
                        $row['more'] = '<div class="see_more_header"><a data-group="'.$row['group_id'].'" data-start="'.$num.'" data-limit="'.($count-$num).'" data-header="'.$ims->conf['cur_act_distinct'].'">'.$ims->site_func->get_lang('view_more_trogia', 'product', array('[num]' => $count-$num)).'</a></div>';
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
                    'where' => ' and find_in_set('.$group.', group_nav) and is_'.$ims->conf['cur_act_distinct'].' = 1 order by show_order desc, date_create desc',
                    'num_list' => $num,
                    'paginate' => 0
                );
                $count = $ims->db->do_get_num("product", $ims->conf['qr'].$arr_in['where']);
                if($count > $num){
                    $row['more'] = '<div class="see_more_header"><a data-group="'.$group.'" data-start="'.$num.'" data-limit="'.($count-$num).'" data-header="'.$ims->conf['cur_act_distinct'].'">'.$ims->site_func->get_lang('view_more_trogia', 'product', array('[num]' => $count-$num)).'</a></div>';
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
    // End class
}

?>