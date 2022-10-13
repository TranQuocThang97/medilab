<?php
if (!defined('IN_ims')) { die('Access denied'); }
$nts = new sMain();

class sMain{
	var $modules = "news";
	var $action  = "news";
	var $sub 	 = "manage";

	function __construct (){
		global $ims;

		$arrLoad = array(
			'modules' 		 => $this->modules,
			'action'  		 => $this->action,
			'template'  	 => $this->modules,
			'css'  	 		 => $this->modules,
			'use_func'  	 => $this->modules, // Sử dụng func
			'use_navigation' => 0, // Sử dụng navigation
			'required_login' => 0, // Bắt buộc đăng nhập
		);
        $ims->func->loadTemplate($arrLoad);	
		
		require_once ($this->modules."_func.php");
        $this->modFunc = new newsFunc($this);

		$data = array();
		if(isset($ims->conf['cur_group']) && isset($ims->data['cur_group']) && $ims->data['cur_group']){
			$row = $ims->data['cur_group'];
			//Current menu
			$arr_group_nav = (!empty($row["group_nav"])) ? explode(',',$row["group_nav"]) : array();
			foreach($arr_group_nav as $v) {
				$ims->conf['menu_action'][] = $this->modules.'-group-'.$v;
			}
			//End current menu
			
			//Make link lang
			$result = $ims->db->query("select friendly_link,lang   
										from news_group 
										where group_id='".$ims->conf['cur_group']."' ");
			while($row_lang = $ims->db->fetch_row($result)){
				$ims->data['link_lang'][$row_lang['lang']] = $ims->site_func->get_link_lang($row_lang['lang'], $this->modules, $row_lang['friendly_link']);
			}
			//End Make link lang
			//SEO
			$ims->site->get_seo ($ims->data['cur_group']);
			$ims->conf["cur_group_nav"] = $row["group_nav"];

			$data['content'] = $this->do_list_group($row);
			$ims->conf['nav'] = $ims->site->get_navigation();
			$ims->conf['class_full'] = 'detail';
		}else{
			if((!isset($ims->input['keyword']) || !$ims->input['keyword']) && (!isset($ims->input['tag']) || !$ims->input['tag'])) {
				//$ims->html->redirect_rel($ims->site_func->get_link('home'));
			}
			
			//Make link lang
			foreach($ims->data['lang'] as $row_lang) {
				$ims->data['link_lang'][$row_lang['name']] = $ims->site_func->get_link_lang($row_lang['name'], $this->modules);
			}
			//End Make link lang

			$ims->conf["cur_group"] = 0;
			$data = array(
				"content" => $this->do_list(),
			);
		}
		$data['link_action'] = $ims->conf['rooturl'].$ims->conf['cur_mod_url'];
		$_SESSION['location'] = '';
		Session::DELETE('locatiom');
		if(isset($ims->input['location'])){
			 Session::SET('locatiom',$ims->input['location']);			
		}
		$data['location'] = Session::GET('locatiom','');		
//        $ims->func->send_mail_temp ('admin-ordering-complete', 'quoctuan122@gmail.com', $ims->conf['email']);
		$ims->conf['page_title'] = '<div class="title">'.$ims->lang['news']['mod_title'].'</div>';
		// $data['box_menu'] = $this->modFunc->box_menu();
		$ims->conf['container_layout'] = 'm';
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("main");
		$ims->output .=  $ims->temp_act->text("main");
	}
		
	function do_list (){
		global $ims;
      
//      if(isset($ims->post['keyword'])) {
//         $ims->html->redirect_rel($ims->site_func->get_link('news', '', '', array('keyword'=>$ims->post['keyword'])));
//         die;
//      }

		$keyword = (isset($ims->input['keyword'])) ? trim($ims->input['keyword']) : '';
		$tag = (isset($ims->input['tag'])) ? trim($ims->input['tag']) : '';
		$ext = '';
		$where = '';
		if($keyword) {
			$ext = '&keyword='.$keyword;
			//$text_search = $ims->func->get_text_search ($str);
			$arr_key = explode(' ',$keyword);
			$arr_tmp = array();
			foreach($arr_key as $value) {
				$value = trim($value);
				if(!empty($value)) {
					$arr_tmp[] = "title like '%".$value."%'";
					//$arr_tmp[] = "content like '%".$value."%'";
				}	
			}
			if(count($arr_tmp) > 0) {
				//$where .= " and (".implode(" or ",$arr_tmp).")";
				$where .= " and (".implode(" and ",$arr_tmp).")";
			}
		} elseif($tag) {
			$ext = '&tag='.$tag;
			$where .= " and find_in_set('".$tag."', tag_list)";
		}
		
		
		$arr_in = array(
			'where' => $where.' order by show_order desc, date_create desc',
			'ext' => $ext,
		);
		$list_item = $this->modFunc->html_list_item($arr_in);
		$data = array(
			'content' => $list_item,
            'tab_news' => $this->tab_news(),
            'group1' => $this->do_group1(),
            'group2' => $this->do_group2(),
            'video_group3' => $this->do_video_group3(),
            'most_read' => $this->do_most_read()
		);
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("main_news");
		return $ims->temp_act->text("main_news");
	}
	
	function do_list_group ($info = array()) {
		global $ims;	
		
		$arr_in = array(
			'link_action' => $ims->site_func->get_link('news',$info['friendly_link']),
			'where' => " and find_in_set('".$info['group_id']."',group_nav)>0",
			'temp' => 'list_item',
		);

		$list_item = $this->modFunc->html_list_item($arr_in);
		$data = array(
		    'group_title' => '<div class="group_title">'.$info['title'].'</div>',
			'content' => $list_item,
            'most_read' => $this->do_most_read()
		);
		
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("main_news");
		return $ims->temp_act->text("main_news");
	}
	
  // end class
    function tab_news(){
	    global $ims;
        $data = array();

	    $lasted = $ims->db->load_item('news', $ims->conf['qr'].' order by show_order desc, date_create desc', 'item_id');
	    if($lasted){
	        $data[] = array(
                            'title' => $ims->lang['news']['lasted'],
                            'content' => $this->do_lasted_focus1(''),
                            'group_id' => 'lasted'
                        );
        }
        $focus1 = $ims->db->load_item('news', $ims->conf['qr'].' and is_focus1 = 1 order by show_order desc, date_create desc', 'item_id');
        if($focus1){
            $data[] = array(
                'title' => $ims->lang['news']['focus_week'],
                'content' => $this->do_lasted_focus1(' and is_focus1 = 1'),
                'group_id' => 'focus1'
            );
        }
        $i = 0;
        foreach ($data as $row){
            $i++;
            if($i == 1){
                $row['active'] = 'active';
            }else{
                $row['active'] = '';
            }
            $ims->temp_act->assign('row', $row);
            $ims->temp_act->parse("tab_news.li");
            $ims->temp_act->parse("tab_news.content");
        }
        $ims->temp_act->parse("tab_news");
        return $ims->temp_act->text("tab_news");
    }
    function do_lasted_focus1($where){
	    global $ims;
        $ims->temp_act->reset("lasted_focus1");

        $result = $ims->db->load_item_arr('news', $ims->conf['qr'].$where.' order by show_order desc, date_create desc limit 4', 'title, picture, friendly_link, date_create, group_id');
	    $i = 0;
	    foreach ($result as $row){
	        $i++;
	        $row['link'] = $ims->func->get_link($row['friendly_link'], '');
	        $row['group_name'] = $this->modFunc->get_group_name($row['group_id'], 'link');
	        $row['date_create'] = date('d/m/Y', $row['date_create']);
	        if($i == 1){
                $row['picture'] = $ims->func->get_src_mod($row['picture'], 756, 362, 1, 0);
                $ims->temp_act->assign('row', $row);
                $ims->temp_act->parse("lasted_focus1.left");
            }else{
                $row['picture'] = $ims->func->get_src_mod($row['picture'], 144, 104, 1, 0);
                $ims->temp_act->assign('row', $row);
                $ims->temp_act->parse("lasted_focus1.item");
            }
        }
        $ims->temp_act->parse("lasted_focus1");
        return $ims->temp_act->text("lasted_focus1");
    }
    function do_group1(){
	    global $ims;
	    $group = $ims->db->load_row('news_group', $ims->conf['qr'].' and is_focus = 1 order by show_order desc, date_create desc limit 1', 'group_id, title, friendly_link');
	    if($group){
            $group['link'] = $ims->func->get_link($group['friendly_link'], '');
	        $result = $ims->db->load_item_arr('news', $ims->conf['qr'].' and find_in_set('.$group['group_id'].', group_nav) order by show_order desc, date_create desc limit 10', 'title, friendly_link, picture, date_create');
            if($result){
                foreach ($result as $row){
                    $row['picture'] = $ims->func->get_src_mod($row['picture'], 364, 317, 1, 1);
                    $row['link'] = $ims->func->get_link($row['friendly_link'], '');
                    $row['group_name'] = '<a href="'.$group['link'].'">'.$group['title'].'</a>';
                    $row['date_create'] = date('d/m/Y', $row['date_create']);
                    $ims->temp_act->assign('row', $row);
                    $ims->temp_act->parse("group1.item");
                }
                $ims->temp_act->assign('group', $group);
                $ims->temp_act->parse("group1");
                return $ims->temp_act->text("group1");
            }
	    }
    }
    function do_group2(){
	    global $ims;
        $group = $ims->db->load_row('news_group', $ims->conf['qr'].' and is_focus = 1 order by show_order desc, date_create desc limit 1, 1', 'group_id, title, friendly_link');
        if($group){
            $group['link'] = $ims->func->get_link($group['friendly_link'], '');
            $result = $ims->db->load_item_arr('news', $ims->conf['qr'].' and find_in_set('.$group['group_id'].', group_nav) order by show_order desc, date_create desc limit 5', 'title, friendly_link, picture, date_create');
            if($result){
                $i = 0;
                foreach ($result as $row){
                    $i++;
                    $row['link'] = $ims->func->get_link($row['friendly_link'], '');
                    $row['group_name'] = '<a href="'.$group['link'].'">'.$group['title'].'</a>';
                    $row['date_create'] = date('d/m/Y', $row['date_create']);
                    if($i == 1){
                        $row['picture'] = $ims->func->get_src_mod($row['picture'], 624, 501, 1, 1);
                        $ims->temp_act->assign('row', $row);
                        $ims->temp_act->parse("group2.left");
                    }else{
                        $row['picture'] = $ims->func->get_src_mod($row['picture'], 283, 190, 1, 1);
                        $ims->temp_act->assign('row', $row);
                        $ims->temp_act->parse("group2.item");
                    }
                }
                $ims->temp_act->assign('group', $group);
                $ims->temp_act->parse("group2");
                return $ims->temp_act->text("group2");
            }
        }
    }
    function do_video_group3(){
	    global $ims;
	    $data = array(
	        'class_full' => '',
	        'video_none' => '',
	        'group3_none' => '',
        );

	    $video = $ims->db->load_item_arr('video', $ims->conf['qr'].' order by show_order desc, date_create desc', 'title, video, video_file, picture');
	    if($video){
	        $data['video_link'] = $ims->site_func->get_link('video');
	        foreach ($video as $vd){
                $vd['item'] = $ims->site->do_video($vd);
                $ims->temp_act->assign('vd', $vd);
                $ims->temp_act->parse("video_group3.video_item");
            }
        }else{
            $data['class_full'] = 'full_view';
            $data['video_none'] = 'd-none';
        }
	    $group_news = $ims->db->load_row('news_group', $ims->conf['qr'].' and is_focus = 1 order by show_order desc, date_create desc limit 2, 1', 'title, friendly_link, group_id');
	    if($group_news){
            $data['group_link'] = $ims->func->get_link($group_news['friendly_link'], '');
            $data['group_title'] = $group_news['title'];
            $result = $ims->db->load_item_arr('news', $ims->conf['qr'].' and find_in_set('.$group_news['group_id'].', group_nav) order by show_order desc, date_create desc limit 9', 'title, friendly_link');
            if($result){
                foreach ($result as $row){
                    $row['link'] = $ims->func->get_link($row['friendly_link'],'');
                    $ims->temp_act->assign('row', $row);
                    $ims->temp_act->parse("video_group3.item_news");
                }
            }else{
                $data['class_full'] = 'full_view';
                $data['group3_none'] = 'd-none';
            }
        }
        if((isset($video) && $video) || (isset($result) && $result)){
            $ims->temp_act->assign('data', $data);
            $ims->temp_act->parse("video_group3");
            return $ims->temp_act->text("video_group3");
        }
    }
    function do_most_read(){
	    global $ims;

	    $result = $ims->db->load_item_arr('news', $ims->conf['qr'].' order by num_view desc, date_create desc limit 8', 'title, picture, friendly_link');
	    if($result){
	        foreach ($result as $row){
	            $row['link'] = $ims->func->get_link($row['friendly_link'], '');
	            $row['picture'] = $ims->func->get_src_mod($row['picture'], 104, 55, 1, 1);
                $ims->temp_act->assign('row', $row);
                $ims->temp_act->parse("most_read.item");
            }
            $ims->temp_act->parse("most_read");
            return $ims->temp_act->text("most_read");
        }
    }
}
?>