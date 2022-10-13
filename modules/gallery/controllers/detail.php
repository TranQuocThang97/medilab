<?php
if (!defined('IN_ims')) { die('Access denied'); }
$nts = new sMain();

class sMain {

    var $modules = "gallery";
    var $action  = "detail";
    var $sub     = "manage";

    /**
        * function __construct ()
        * Khoi tao 
    **/
    function __construct() {
        global $ims;

        $arrLoad = array(
            'modules'        => $this->modules,
            'action'         => $this->action,
            'template'       => $this->modules,
            'css'            => $this->modules,
            'use_func'       => $this->modules, // Sử dụng func
            'use_navigation' => 1, // Sử dụng navigation
            'required_login' => 0, // Bắt buộc đăng nhập
        );
        $ims->func->loadTemplate($arrLoad); 

        require_once ($this->modules . "_func.php");
        $this->modFunc = new galleryFunc($this);

        $data = array();
        if (isset($ims->conf['cur_item']) && isset($ims->data['cur_item']) && $ims->data['cur_item']) {

            $ims->db->query("UPDATE gallery set num_view=num_view+1 WHERE item_id='".$ims->data['cur_item']['item_id']."'");

            $row = $ims->data['cur_item'];
            //Make link lang
            $result = $ims->db->query("SELECT friendly_link,lang FROM gallery WHERE item_id='".$ims->conf['cur_item']."' ");
            while ($row_lang = $ims->db->fetch_row($result)) {
                $ims->data['link_lang'][$row_lang['lang']] = $ims->site_func->get_link_lang($row_lang['lang'], $this->modules, '', $row_lang['friendly_link']);
            }
            //End Make link lang

            //SEO
            $ims->site->get_seo($ims->data['cur_item']);

            $ims->conf["cur_group"]     = $row["group_id"];
            $ims->conf["cur_group_nav"] = $row["group_nav"];
            $ims->conf["meta_image"]    = $ims->func->get_src_mod($row["picture"], 630, 420, 1, 1);

            //Current menu
            $arr_group_nav = (!empty($ims->conf["cur_group_nav"])) ? explode(',', $ims->conf["cur_group_nav"]) : array();
            foreach ($arr_group_nav as $v) {
                $ims->conf['menu_action'][] = $this->modules . '-group-' . $v;
            }
            $ims->conf['menu_action'][] = $this->modules . '-item-' . $ims->conf['cur_item'];
            //End current menu
            
            $data['content'] = $this->do_detail($row);
        } else {
            $ims->html->redirect_rel($ims->site_func->get_link($this->modules));
        }
        $ims->conf['container_layout'] = 'm';        
        $ims->temp_act->assign('data', $data);
        $ims->temp_act->parse("main");
        $ims->output .= $ims->temp_act->text("main");
    }

    function do_detail($info = array()) {
        global $ims;

        $data = $info;
        $data['picture']     = $ims->func->get_src_mod($data['picture']);
        $info['arr_picture'] = $ims->func->unserialize($info['arr_picture']);
        $list_pic = '';    
        if (count($info['arr_picture'])) {
            foreach ($info['arr_picture'] as $picture) {
                $row = array();
                // $row['color_id'] = 'data-color="'.$row['color_id'].'"';                
                $row['title'] = $ims->func->input_editor_decode($info['title']);                
                $pic_w = '';
                $pic_h = '';
                $thum_w = 210;
                $thum_h = 100;
                $row['picture'] = $picture;
                $row['src_zoom'] = $ims->func->get_src_mod($row["picture"]);
                $row['src'] = $ims->func->get_src_mod($row["picture"], $pic_w, $pic_h, 1, 0, array('fix_max' => '1'));
                $row['src_thumb'] = $ims->func->get_src_mod($row["picture"], $thum_w, $thum_h, 1, 0, array('fix_max' => '1'));

                $ims->temp_act->assign('row', $row);
                $ims->temp_act->parse("detail.gallery.pic");
                $ims->temp_act->parse("detail.gallery.pic_thumb");
            }
            $ims->temp_act->parse("detail.gallery");
        }
        // $data['date_update'] = date('d', $data['date_create']);
        // $data['my_update']   = $ims->func->rebuild_date('F, Y', $data['date_create']);
        $data['short']       = $ims->func->input_editor_decode($data['short']);
        $data['content']     = $ims->func->input_editor_decode($data['content']);
        $data["link_share"]  = $ims->site_func->get_link('gallery', '', $info['friendly_link']);
        $data['other']       = $this->do_other($info);
        $ims->conf["class_full"] = 'gallery';
        $ims->temp_act->assign('data', $data);
        $ims->temp_act->parse("detail");
        return $ims->temp_act->text("detail");
    }

    function do_other($info) {
        global $ims;

        $arr_in = array(
            'link_action' => $ims->site_func->get_link('gallery'),
            'where' => " and item_id!='" . $info['item_id'] . "' ",
            'temp' => 'list_item_other',
            'num_list' => $ims->setting['gallery']["num_order_detail"],
            'pic_w' => '350',
            'pic_h' => '182',
            'paginate' => 0,
        );
        if ($info['group_id'] > 0) {
            $arr_in['where'] .= "and ( 
                find_in_set('" . $info['group_id'] . "',group_nav)>0 
                or find_in_set('" . $info['group_id'] . "',group_related)>0 
            )";
        }
        $num = $ims->db->do_get_num("gallery"," item_id!='" . $info['item_id'] . "' and ( 
                find_in_set('" . $info['group_id'] . "',group_nav)>0 
                or find_in_set('" . $info['group_id'] . "',group_related)>0 
            )");
        $rows = $num>2?2:1;
        $ims->func->include_js_content('
            var o = $(".list_other .row_item");
            o.slick({
                slidesToShow: 3,
                dots: false,
                arrows: false,
                swipeToSlide: true,
                responsive: [
                    {
                        breakpoint: 992,
                        settings: {
                            slidesToShow: 2,
                        }
                    },
                    {
                        breakpoint: 576,
                        settings: {
                            slidesToShow: 1,
                            dots: true,
                        }
                    }
                ]
            });
            $(".list_other .btn-prev").on("click",function(){o.slick("slickPrev");})
            $(".list_other .btn-next").on("click",function(){o.slick("slickNext");})
        ');
        return $this->modFunc->html_list_item($arr_in);
    }
    // end class
}

?>