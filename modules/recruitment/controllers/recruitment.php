<?php
if (!defined('IN_ims')) { die('Access denied'); }
$nts = new sMain();

class sMain
{
	var $modules = "recruitment";
	var $action  = "recruitment";
	var $sub 	 = "manage";
	
	/**
		* function __construct ()
		* Khoi tao 
	**/
	function __construct ()
	{
		global $ims;

		$arrLoad = array(
			'modules' 		 => $this->modules,
			'action'  		 => $this->action,
			'template'  	 => $this->modules,
			'css'  	 		 => $this->modules,
			'use_func'  	 => $this->modules, // Sử dụng func
			'use_navigation' => 1, // Sử dụng navigation
			'required_login' => 0, // Bắt buộc đăng nhập
		);
        $ims->func->loadTemplate($arrLoad);

		$data = array();
		if(isset($ims->conf['cur_group']) && isset($ims->data['cur_group']) && $ims->data['cur_group']){			
			$row = $ims->data['cur_group'];
			//Current menu
            $arr_group_nav = (!empty($row["group_nav"])) ? explode(',',$row["group_nav"]) : array();
            foreach($arr_group_nav as $v) {
                $ims->conf['menu_action'][] = $this->modules.'-group-'.$v;
            }
            //End current menu
            $ims->conf["cur_group_nav"] = $row["group_nav"];	

            //Make link lang
            $result = $ims->db->query("select friendly_link,lang   
                                        from recruitment_group 
                                        where group_id='".$ims->conf['cur_group']."' ");
            while($row_lang = $ims->db->fetch_row($result)){
                $ims->data['link_lang'][$row_lang['lang']] = $ims->site_func->get_link_lang ($row_lang['lang'], $this->modules, $row_lang['friendly_link']);
            }
            //End Make link lang
            //SEO
            $ims->site->get_seo ($ims->data['cur_group']);

            $ims->navigation = $ims->site->html_arr_navigation (array(
                array(
                    'title' => '<i class="fas fa-home"></i> '.$ims->lang['global']['homepage'],
                    'link' => $ims->site_func->get_link ('home')
                ),
                array(
                    'title' => (isset($ims->setting['recruitment']["recruitment_meta_title"])) ? $ims->setting['recruitment']["recruitment_meta_title"] : '',
                    'link' => $ims->site_func->get_link ('recruitment',$ims->data['recruitment_group'][$ims->conf['cur_group']]['friendly_link'])
                )
            ));
            $ims->conf['nav'] = $ims->navigation;

            $data = array();            
            $data['content'] = $this->do_recruitment($ims->data['cur_group']);
            $ims->conf['container_layout'] = 'c-m';
		}elseif (isset($ims->conf['cur_item']) && isset($ims->data['cur_item']) && $ims->data['cur_item']) {
            $row = $ims->data['cur_item'];			
			$row['content'] = $ims->func->input_editor_decode($row['content']);
            $ims->conf['cur_group'] = $row['group_id'];
            $ims->conf["cur_group_nav"] = $row["group_nav"];
            $ims->conf["meta_image"] = $ims->func->get_src_mod($row["picture"], 600, 315, 1, 1);
            $ims->conf['cur_item'] = $row['item_id'];
            $ims->data['cur_item'] = $row;
            //Make link lang
            $result = $ims->db->query("select friendly_link,lang 
                                        from recruitment 
                                        where item_id='".$ims->conf['cur_item']."' ");
            while($row_lang = $ims->db->fetch_row($result)){
                $ims->data['link_lang'][$row_lang['lang']] = $ims->site_func->get_link_lang ($row_lang['lang'], $this->modules, '', $row_lang['friendly_link']);
            }
            //End Make link lang
            //SEO
            $ims->site->get_seo ($ims->data['cur_item']);
            $ims->navigation = $ims->site->html_arr_navigation (array(
                array(
                    'title' => '<i class="fas fa-home"></i> '.$ims->lang['global']['homepage'],
                    'link' => $ims->site_func->get_link ('home')
                ),                
                array(
                    'title' => $ims->data['cur_item']['title'],
                    'link' => $ims->site_func->get_link('recruitment',$ims->data["cur_item"]['friendly_link'])
                )
            ));            

            $ims->conf['menu_action'][] = $this->modules.'-group-'.$ims->conf['cur_group'];
            $ims->conf['menu_action'][] = $this->modules.'-item-'.$ims->conf['cur_item'];
            $ims->conf['nav'] = $ims->navigation;

            $data = array();
            $data['content'] = $this->do_detail ($ims->data['cur_item']);
            $ims->conf['container_layout'] = 'c-m';
		}else{
			$data = array();            
            $data['content'] = $this->do_recruitment();
            $ims->conf['container_layout'] = 'c-m';
		}		

		$ims->conf['class_full'] = 'recruitment';
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("main");
		$ims->output .=  $ims->temp_act->text("main");
	}
	
	function do_list_group ($info = array(), $info_lang = array())
	{
		global $ims;	
		
		$data = array(
			'title' => $info_lang['title']
		);
		
		$arr_in = array(
			'link_action' => $ims->site_func->get_link ('recruitment',$info_lang['friendly_link']),
			'where' => " and find_in_set('".$info['group_id']."',group_nav)>0",
			'temp' => 'list_item',
		);
		
		switch ($info['type_show']) {
			case "list_item":
				$arr_in['temp'] = "list_item";
				$data['content'] = html_list_item($arr_in);
				break;
			case "grid_item":
				$arr_in['temp'] = "grid_item";
				$arr_in['pic_w'] = 200;
				$arr_in['pic_h'] = 200;
				$arr_in['num_row'] = 4;
				if($info['num_show'] > 0) {
					$arr_in['num_show'] = $info['num_show'];
				}
				$arr_in['short_len'] = 200;
				$data['content'] = html_list_item($arr_in);
				break;
			case "content_only":
				$data['content'] = $info_lang['content'];
				break;
			default:
				$arr_in['where'] .= " and group_id!='".$info['group_id']."'";
				$data['content'] = html_list_group($arr_in);
				
				if($data['content']) {
				} else {
					$arr_in['where'] = " and find_in_set('".$info['group_id']."',group_nav)>0";
					$arr_in['temp'] = "list_item";
					$data['content'] = html_list_item($arr_in);
				}
				
				break;
		}
		
		$ims->temp_box->assign('data', $data);
		$ims->temp_box->parse("box_main");
		return $ims->temp_box->text("box_main");
	}
	
	function do_recruitment($info = array()){
		global $ims;
		$data = array();
		$where = '';
		if(count($info)>0){
			$where = ' and group_id="'.$info['group_id'].'"';	
		}		
		//keyword
		$text_search = isset($ims->input['keyword'])?$ims->input['keyword']:'';		        
        $arr_key = explode(' ', $text_search);        
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
        //type
        $type_search = isset($ims->input['type'])?$ims->input['type']:'';       
       	if($type_search != ''){
            $where .= ' and find_in_set('.$type_search.' , list_type)' ;
        }
        //province
        $province_search = isset($ims->input['province'])?$ims->input['province']:'';
       	if($province_search != ''){
            $where .= ' and find_in_set('.$province_search.' , province)' ;
        }
		$arr_in = array(
			'where' => $where,
			'link_action' => $ims->site_func->get_link($this->modules,$ims->conf['cur_mod_url']),
		);
		$res_num = $ims->db->query("select item_id 
                            from recruitment 
                            where is_show=1 
                            and lang='" . $ims->conf["lang_cur"] . "' 
                            " . $where . " ");
        $num_total = $ims->db->num_rows($res_num);
        // $data['text_search'] = '<div class="text_search">'.$ims->lang['recruitment']['list_search'].' <b>'.$num_total.'</b></div>';
        
		// $data['box_search'] = $this->do_search();
		$data['content'] = html_list_item($arr_in);
		$ims->temp_act->assign('data',$data);
		$ims->temp_act->parse('recruitment');
		return $ims->temp_act->text('recruitment');

		// $boxdata = array(
		// 	'title' => $ims->lang['recruitment']['mod_title'],
		// 	'content' => $ims->temp_act->text('recruitment'),
		// );
		// return $ims->html->temp_box('box',$boxdata);
	}

	function do_search(){
		global $ims;
		$data = array(
			'link_search' => $ims->site_func->get_link($this->modules,$ims->conf['cur_mod_url']),
			'keyword' => (isset($ims->input['keyword'])) ? $ims->input['keyword'] : '',
		);
		$arr_type = $ims->db->load_row_arr('recruitment_type','lang="'.$ims->conf['lang_cur'].'" and is_show=1');
		if($arr_type){
			foreach($arr_type as $row) {
				$type = isset($ims->input['type'])?$ims->input['type']:'';
				if($type == $row['id']){
					$row['selected'] = 'selected';
				}
				$ims->temp_act->assign('row',$row);
				$ims->temp_act->parse('box_search.type');
			}
		}
		$arr_province = $ims->db->load_row_arr('location_province','is_show=1');
		if($arr_province){
			foreach($arr_province as $row) {
				$province = isset($ims->input['province'])?$ims->input['province']:'';
				if($province == $row['code']){
					$row['selected'] = 'selected';
				}
				$ims->temp_act->assign('row',$row);
				$ims->temp_act->parse('box_search.province');
			}
		}
		$ims->temp_act->assign('data',$data);
		$ims->temp_act->parse('box_search');
		return $ims->temp_act->text('box_search');
	}

	function do_detail ($info = array()){
		global $ims;	
		$ims->func->include_js ($ims->dir_js.'/rrssb.min.js');
		$data = array();
		
		$province = '';
		$data['title'] = $ims->func->input_editor_decode($info['title']);
		$data['quantity'] = $info['quantity']?$ims->lang['recruitment']['quantity'].': <b>'.$ims->func->input_editor_decode($info['quantity']).'</b>':'';
		$data['salary'] = $info['salary']?$ims->lang['recruitment']['salary'].': <b>'.$ims->func->input_editor_decode($info['salary']).'</b>':'';
		$data['content'] = $ims->func->input_editor_decode($info['content']);
		// $data['link_share'] = $ims->data['link_lang'][$ims->conf['lang_cur']];	
		$data['link_share'] = $ims->site_func->get_link('recruitment','',$info['friendly_link']);
		$province = $ims->db->load_item('location_province','is_show=1 and code="'.$info['province'].'"','title');
		$data['ititle'] = $info['title'].' - '.$province;		
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("item_detail");
		return $ims->temp_act->text("item_detail");		
	}
	
  // end class
}
?>