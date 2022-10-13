<?php
if (!defined('IN_ims')) { die('Access denied'); }
$nts = new sMain();

class sMain {

    var $modules = "product";
    var $action  = "combo";

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

        $ims->conf['container_layout'] = 'm';
        $ims->conf["class_full"] = 'combo';
        $ims->temp_act->assign('data', $data);
        $ims->temp_act->parse("main");
        $ims->output .= $ims->temp_act->text("main");
    }

    function do_list() {
        global $ims;
        $data = array();

        $time = time();
        $result = $ims->db->load_item_arr('product as pd, combo as cb', ' pd.is_show = 1 and pd.lang = "'.$ims->conf['lang_cur'].'" and pd.combo_id = cb.item_id and (cb.date_begin < '.$time.' and cb.date_end > '.$time.') and cb.quantity_product > 0 order by pd.show_order desc, pd.date_create desc', 'pd.title, pd.friendly_link, pd.picture, pd.price, pd.price_buy, cb.date_begin, cb.date_end');
        if($result){
            $banner = $ims->site->get_banner('banner-forproduct-combo', 10);
            if($banner){
                $data['banner'] = '<div class="slide_banner_combo">'.$banner.'</div>';
                $ims->func->include_js_content('
                $(".slide_banner_combo").slick({
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
            $data['condition'] = $this->do_condition();
            foreach ($result as $row){
                $row['picture'] = $ims->func->get_src_mod($row['picture'], 595, 310, 1, 1);
                $row['loading'] = $ims->dir_images."spin.svg";
                $row['link'] = $ims->func->get_link($row['friendly_link'], '');
                $row['time_application'] = $ims->site_func->get_lang('time_apply_combo', 'product', array('[begin]' => date('d/m', $row['date_begin']), '[end]' => date('d/m/Y', $row['date_end'])));
                if($row['price_buy'] < $row['price']){
                    $row['price'] = '<div class="price">'.number_format($row['price'], 0,',','.').'đ</div>';
                }else{
                    $row['price'] = '';
                }
                $row['price_buy'] = number_format($row['price_buy'], 0,',','.').'đ';
                $ims->temp_act->assign('row', $row);
                $ims->temp_act->parse("combo.item");
            }
        }else{
            $ims->temp_act->parse("combo.empty");
        }

        $ims->temp_act->assign('data', $data);
        $ims->temp_act->parse("combo");
        return $ims->temp_act->text("combo");
    }

    function do_condition(){
        global $ims;

        $result = $ims->db->load_item_arr('banner', $ims->conf['qr'].' and group_name = "condition-combo" order by show_order desc, date_create desc', 'title, content');
        if($result){
            $i = 0;
            $j = -1;
            foreach ($result as $row){
                $i++;
                $j++;
                if($i == 1){
                    $row['content'] = strip_tags($row['content']);
                    $ims->temp_act->assign('row', $row);
                    $ims->temp_act->parse("combo_condition.title");
                }else{
                    $row['stt'] = $j;
                    $row['content'] = $ims->func->input_editor_decode($row['content']);
                    $ims->temp_act->assign('row', $row);
                    $ims->temp_act->parse("combo_condition.item");
                }
            }
            $ims->temp_act->parse("combo_condition");
            return $ims->temp_act->text("combo_condition");
        }
    }
    // End class
}

?>