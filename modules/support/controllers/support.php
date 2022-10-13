<?php
if (!defined('IN_ims')) { die('Access denied'); }
$nts = new sMain();

class sMain{
	var $modules = "support";
	var $action  = "support";
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
        $this->modFunc = new supportFunc($this);

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
										from support_group 
										where group_id='".$ims->conf['cur_group']."' ");
			while($row_lang = $ims->db->fetch_row($result)){
				$ims->data['link_lang'][$row_lang['lang']] = $ims->site_func->get_link_lang($row_lang['lang'], $this->modules, $row_lang['friendly_link']);
			}
			//End Make link lang
			//SEO
			$ims->site->get_seo ($ims->data['cur_group']);
			$ims->conf["cur_group_nav"] = $row["group_nav"];

//			$data['content'] = $this->do_list_group($row);
            require_once ($ims->conf["rootpath"]."404.php");die;
		}else{
			//Make link lang
			foreach($ims->data['lang'] as $row_lang) {
				$ims->data['link_lang'][$row_lang['name']] = $ims->site_func->get_link_lang($row_lang['name'], $this->modules);
			}
			//End Make link lang

			$ims->conf["cur_group"] = 0;
			$data['content'] = $this->do_support();
		}

		$ims->conf['container_layout'] = 'm';
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("main");
		$ims->output .=  $ims->temp_act->text("main");
	}
		
	function do_list (){
		global $ims;
      
//      if(isset($ims->post['keyword'])) {
//         $ims->html->redirect_rel($ims->site_func->get_link('support', '', '', array('keyword'=>$ims->post['keyword'])));
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
		);
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("main_support");
		return $ims->temp_act->text("main_support");
	}
	
	function do_list_group ($info = array()) {
		global $ims;	
		
		$arr_in = array(
			'link_action' => $ims->site_func->get_link('support',$info['friendly_link']),
			'where' => " and find_in_set('".$info['group_id']."',group_nav)>0",
			'temp' => 'list_item',
		);

		$list_item = $this->modFunc->html_list_item($arr_in);
		$data = array(
		    'group_title' => '<div class="group_title">'.$info['title'].'</div>',
			'content' => $list_item,
		);
		
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("main_support");
		return $ims->temp_act->text("main_support");
	}

	function do_support(){
	    global $ims;
	    $data = array();

	    $data['form_img'] = (!empty($ims->setting['support']['form_picture'])) ? $ims->func->get_src_mod($ims->setting['support']['form_picture']) : $ims->conf['rooturl'].'resources/images/use/register_form.jpg';
	    $data['other_content'] = $this->do_other_content();
	    $data['focus_news'] = $this->do_focus_news();
	    $data['home_link'] = $ims->site_func->get_link('');
        $data['complete_picture'] = !empty($ims->setting['support']['complete_form']) ? $ims->func->get_src_mod($ims->setting['support']['complete_form']) : $ims->conf['rooturl'].'resources/images/use/complete_form.png';

        $ims->temp_act->assign('data', $data);
        $ims->temp_act->parse('main_content');
        return $ims->temp_act->text('main_content');
    }

    function do_other_content(){
        global $ims;
        $out = '';
        $list_group = $ims->db->load_item_arr('support_group', $ims->conf['qr'].' order by show_order desc, date_create desc', 'group_id, title, short, content, picture, arr_picture, type_show');
        if($list_group){
            $index_review = (count($list_group) > 1) ? count($list_group) - 1 : 1;
            $i = 1;
            foreach ($list_group as $item){
                switch ($item['type_show']){
                    case "partner":
                        $out .= $this->partner($item);
                        break;
                    case "content":
                        $out .= $this->content($item);
                        break;
                    case "about":
                        $out .= $this->about($item);
                        break;
                    case "support":
                        $out .= $this->support($item);
                        break;
                }
                if($i == $index_review){
                    $out .= $this->customer_review();
                }
                $i++;
            }
        }else{
            $out .= $this->customer_review();
        }
        return $out;
    }

    function partner($data){
        global $ims;

        if($data['arr_picture'] != ''){
            $arr_picture = $ims->func->unserialize($data['arr_picture']);
            foreach ($arr_picture as $picture){
                $picture = $ims->func->get_src_mod($picture);
                $ims->temp_act->assign('picture', $picture);
                $ims->temp_act->parse('partner.item');
            }
            $ims->temp_act->reset('partner');
            $ims->temp_act->assign('data', $data);
            $ims->temp_act->parse('partner');
            return $ims->temp_act->text('partner');
        }
    }
    function content($data){
        global $ims;

        $result = $ims->db->load_item_arr('support', $ims->conf['qr'].' and group_id = '.$data['group_id'].' order by show_order desc, date_create asc', 'title, picture, content');
        if($result){
            foreach ($result as $row){
                $row['picture'] = $ims->func->get_src_mod($row['picture']);
                $row['content'] = $ims->func->input_editor_decode($row['content']);
                $ims->temp_act->assign('row', $row);
                $ims->temp_act->parse('content.item');
            }
            $ims->temp_act->assign('data', $data);
            $ims->temp_act->reset('content');
            $ims->temp_act->parse('content');
            return $ims->temp_act->text('content');
        }
    }
    function about($data){
        global $ims;

        $result = $ims->db->load_item_arr('support', $ims->conf['qr'].' and group_id = '.$data['group_id'].' order by show_order desc, date_create asc', 'title, picture, content');
        if($result){
            foreach ($result as $row){
                $row['picture'] = $ims->func->get_src_mod($row['picture'], 320, 255, 1, 0);
                $row['content'] = $ims->func->input_editor_decode($row['content']);
                $ims->temp_act->assign('row', $row);
                $ims->temp_act->parse('about.item');
            }
            $ims->temp_act->assign('data', $data);
            $ims->temp_act->reset('about');
            $ims->temp_act->parse('about');
            return $ims->temp_act->text('about');
        }
    }
    function support($data){
        global $ims;

        $data['short'] = ($data['short'] != '') ? '<div class="short">'.$ims->func->input_editor_decode($data['short']).'</div>': '';
        $data['content'] = $ims->func->input_editor_decode($data['content']);
        $data['picture'] = $ims->func->get_src_mod($data['picture']);

        $ims->temp_act->assign('data', $data);
        $ims->temp_act->reset('support');
        $ims->temp_act->parse('support');
        return $ims->temp_act->text('support');
    }

    function customer_review(){
	    global $ims;

	    $result = $ims->db->load_item_arr('customer_reviews', $ims->conf['qr'].' order by show_order desc, date_create desc', 'title, name, job, content, picture');
	    if($result){
	        foreach ($result as $row){
	            $row['picture_zoom'] = $ims->func->get_src_mod($row['picture']);
	            $row['picture'] = $ims->func->get_src_mod($row['picture'], 150, 150, 1, 1);
	            $row['content'] = $ims->func->input_editor_decode($row['content']);
                $ims->temp_act->assign('row', $row);
                $ims->temp_act->parse('customer_review.item');
            }
            $ims->temp_act->parse('customer_review');
            return $ims->temp_act->text('customer_review');
        }
    }

    function do_focus_news(){
        global $ims;

        $result = $ims->db->load_item_arr('news', $ims->conf['qr'].' and is_focus = 1 order by show_order desc, date_create desc', 'title, short, content, picture, friendly_link');
        if($result){
            foreach ($result as $row){
                $row['link'] = $ims->site_func->get_link('news', $row['friendly_link']);
                $row['picture'] = $ims->func->get_src_mod($row['picture'], 320, 237, 1, 1);
                $row['short'] = ($row['short'] != '') ? $ims->func->short($row['short'], 60) : $ims->func->short($row['content'], 60);
                $ims->temp_act->assign('row', $row);
                $ims->temp_act->parse('news.item');
            }
            $ims->temp_act->parse('news');
            return $ims->temp_act->text('news');
        }
    }
}
?>