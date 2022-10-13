<?php
if (!defined('IN_ims')) { die('Access denied'); }

$nts = new sMain();
class sMain{
	var $modules = "home";
	var $action  = "home";
	var $sub	 = "manage";

	function __construct (){
		global $ims;

        $arrLoad = array(
            'modules'        => $this->modules,
            'action'         => $this->action,
            'template'       => $this->modules,
            'css'            => $this->modules,
            'use_func'       => "", // Sử dụng func
            'use_navigation' => 0, // Sử dụng navigation
            'required_login' => 0, // Bắt buộc đăng nhập
            'js'             => 'product'
        );
        $ims->func->loadTemplate($arrLoad);

        require_once ($this->modules . "_func.php");
        $this->modFunc = new home_func($this);

        $ims->func->load_language('product');
		$data = array();
        $data['content'] = $this->do_banner();

        $ims->conf['class_full'] = 'home';
      	$ims->conf['container_layout'] = 'full';
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("main");
		$ims->output .=  $ims->temp_act->text("main");		
	}	

	function do_banner_bo(){
		global $ims;
		
		$data = array();
		$data['list_menu'] = $ims->site->list_menu ('menu_header_top', 'menu');
//		$data['link'] = $ims->site_func->get_link('product');
//		$data['title'] = $ims->lang['home']['view_more'];
//		$data['num_total'] = 0;
//		$data['main_slide'] = $ims->site->get_banner_slide('banner-main', 'main_slide');

        $arr_banner = $ims->data["banner"]["banner-main"];
        if($arr_banner){
//        	$data['num_total'] = count($arr_banner);
//            $i=0;
        	foreach ($arr_banner as $banner) {
        		$banner['link'] = $ims->site_func->get_link_menu($banner['link'], $banner['link_type']);
                $banner['alt'] = ($banner['title']!='') ? $banner['title'] : "img";
                $banner['picture'] = $ims->func->get_src_mod($banner['content'], 964, 371, 1, 1);
//				$banner['content_img'] = '<img src="'.$banner['picture'].'" alt="'.$banner['alt'].'" title="'.$banner['alt'].'"/>';
//				$banner['icon'] = $ims->func->get_src_mod($banner['icon'],60,60,1,0,array());
//				$banner['short'] = $ims->func->input_editor_decode($banner['short']);
//                $i++;
//                if ($i<6) {
//                    $ims->temp_act->assign('row', $banner);
//                    $ims->temp_act->parse("banner.row");
//                    $ims->temp_act->parse("banner.title_more");
//                }
                $ims->temp_act->assign('row', $banner);
                $ims->temp_act->parse("banner.row");
        	}
        }
//        $data['num_total'] = ($data['num_total']>=5) ? 5 : $data['num_total'];

        $ims->temp_act->assign('data',$data);
        $ims->temp_act->parse('banner');
        return $ims->temp_act->text('banner');
	}

    // ------ end class ------
    function do_banner(){
	    global $ims;
        $data = array();
        $check = 0;

        $background = $ims->db->load_item('banner', $ims->conf['qr'].' and group_name = "banner-main"', 'content');
        $data['background'] = !empty($background) ? $ims->func->get_src_mod($background) : $ims->conf['rooturl'].'resources/images/use/banner_main.png';
        $list_text = $ims->db->load_item_arr('banner', $ims->conf['qr'].' and group_name = "slide-on-banner-main" order by show_order desc, date_create asc limit 8', 'content, link, link_type, target');
        if($list_text){
            $check = 1;
            foreach ($list_text as $row){
                $row['link'] = $ims->site_func->get_link_menu($row['link'], $row['link_type']);
                $row['content'] = $ims->func->input_editor_decode($row['content']);
                $ims->temp_act->assign('row', $row);
                if(!$row['link']){
                    $ims->temp_act->parse("banner_main.list_text.text.detail");
                }
                $ims->temp_act->parse("banner_main.list_text.text");
            }
            $ims->temp_act->parse("banner_main.list_text");
            $ims->temp_act->parse("banner_main.list_text_js");
        }
        $list_service = $ims->db->load_item_arr('service_group', $ims->conf['qr'].' order by show_order desc, date_create desc', 'title, friendly_link');
        if($list_service){
            $check = 1;
            foreach ($list_service as $service){
                $service['link'] = $ims->site_func->get_link('service', $service['friendly_link']);
                $ims->temp_act->assign('service', $service);
                $ims->temp_act->parse("banner_main.service.item");
            }
            $ims->temp_act->parse("banner_main.service");
        }
        $event = $ims->site->get_banner('event-on-banner-main', 20);
        if(!empty($event)){
            $check = 1;
            $ims->temp_act->assign('event', $event);
            $ims->temp_act->parse("banner_main.event");
            $ims->temp_act->parse("banner_main.event_js");
        }
        if($check == 1){
            $ims->temp_act->assign('data', $data);
            $ims->temp_act->parse("banner_main");
            return $ims->temp_act->text("banner_main");
        }
    }
}
?>