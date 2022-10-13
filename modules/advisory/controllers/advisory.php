<?php

/*================================================================================*\
Name code : view.php
Copyright © 2013 by Tran Thanh Hiep
@version : 1.0
@date upgrade : 03/02/2013 by Tran Thanh Hiep
\*================================================================================*/

if (! defined('IN_ims')) {
  die('Access denied');
}
$nts = new sMain();

class sMain
{
	var $modules = "advisory";
	var $action = "advisory";
	var $sub = "manage";

	function __construct (){
		global $ims;

        $arrLoad = array(
            'modules' 		 => $this->modules,
            'action'  		 => $this->action,
            'template'  	 => $this->modules,
            'js'             => $this->modules,
            'css'  	 		 => $this->modules,
            'use_func'  	 => $this->modules, // Sử dụng func
            'use_navigation' => 0, // Sử dụng navigation
            'required_login' => 0, // Bắt buộc đăng nhập
        );
        $ims->func->loadTemplate($arrLoad);
        require_once ($this->modules."_func.php");

        $data = array();
		if(isset($ims->conf['cur_group'])){
		    $row = $ims->data['cur_group'];
            //Current menu
            $arr_group_nav = (!empty($row["group_nav"])) ? explode(',',$row["group_nav"]) : array();
            foreach($arr_group_nav as $v) {
                $ims->conf['menu_action'][] = $this->modules.'-group-'.$v;
            }
            //End current menu

            //Make link lang
            $result = $ims->db->query("select friendly_link,lang
											from advisory_group
											where group_id='".$ims->conf['cur_group']."' ");
            while($row_lang = $ims->db->fetch_row($result)){
                $ims->data['link_lang'][$row_lang['lang']] = $ims->site_func->get_link_lang ($row_lang['lang'], $this->modules, $row_lang['friendly_link']);
            }
            //End Make link lang
            //SEO
            $ims->site->get_seo ($ims->data['cur_group']);
            $ims->conf["cur_group_nav"] = $row["group_nav"];

            $data['content'] = $this->do_list_group($row);
		}else{
			//Make link lang
			foreach($ims->data['lang'] as $row_lang) {
				$ims->data['link_lang'][$row_lang['name']] = $ims->site_func->get_link_lang ($row_lang['name'], $this->modules);
			}
			//End Make link lang

			if(isset($ims->conf['cur_item'])){	
				$data['content'] = $this->do_item();
			}else{
                $data['content'] = $this->do_list();
            }
		}

		$ims->conf['container_layout'] = 'c-m';
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("main");
		$ims->output .=  $ims->temp_act->text("main");
	}
	function do_list (){
		global $ims;
			
		$arr_in = array(
			'link_action' => $ims->site_func->get_link('advisory'),
			'where' => "and is_approval = 1",
		);

		$data = array(
            'text' => $ims->lang['advisory']['text_advisory'],
			'content' => html_list_item($arr_in),
			'title' => $ims->conf['meta_title'],
		);
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("box_advisory");
		return $ims->temp_act->text("box_advisory");
	}
	function do_item (){		
		global $ims;	
		$output = '';
		$sql = "select *
					from advisory
					where is_show=1
					and lang='".$ims->conf["lang_cur"]."'
					and item_id = '".$ims->conf['cur_item']."'
		";	
    	$result = $ims->db->query($sql);    
        $row = $ims->db->fetch_row($result);
		
		      
		$row['title'] = strip_tags($ims->func->input_editor_decode($row['title']));  	
		$row['content'] = strip_tags($ims->func->input_editor_decode($row['content']));
		$row['date_update'] = date('d/m/Y',$row['date_update']);		
		$row['num'] = 999;
		$row['num_comment'] = 0;			
		$row['owner_email'] = str_replace(substr($row['owner_email'],0,3),'***',$row['owner_email']);
			
		$output .= '
		<div class="box_advisory">
		<div class="group_advisory">
			<div class="title_advisory">
                <span class="ico ico_thunho_'.$row['num'].'"></span>
                '.$row['title'].'
                <div class="no_show_info info_advisory_'.$row['num'].'">
                	<div class="info_nickname">'.$row['owner_nickname'].'</div>
                    <div class="info_email">'.$row['owner_email'].'</div>                    
                    <div class="info_date">'.$row['date_update'].'</div>
                </div>                
            </div>            
            <div class="none content_advisory_'.$row['num'].'">
                	'.$row['content'].'
            </div>
			</div></div>
			<script>
                $(".ico_thunho_'.$row['num'].'").click(function(){
                    $(this).toggleClass("ico_minus");
                    $(".info_advisory_'.$row['num'].'").toggleClass("show");
					$(".content_advisory_'.$row['num'].'").toggleClass("show");
                });
            </script>
		';
		return $output;
	}
	function do_list_group_bo ($info = array(), $info_lang = array()){
        global $ims;

        $data = array(
            'title' => $info_lang['title']
        );
        $color = $ims->load_data->data_table ('advisory_group', 'group_id', 'group_id, group_nav, title, home_color', "group_id = ".$info['group_id']." and is_show=1");
        foreach($color as $v){
            if($v['home_color'] == ''){
                $id_root = explode(",", $v['group_nav']);
                $data['id'] = $id_root[0];
            }
            else{
                $data['id'] = $info['group_id'];
            }
        }
        $arr_in = array(
            'link_action' => $ims->site_func->get_link('advisory',$info_lang['friendly_link']),
            'where' => " and find_in_set('".$info['group_id']."',group_nav)>0",
            'temp' => 'list_item',
            'id' => $data['id']
        );
        $data['content'] = html_list_item($arr_in);
        $ims->temp_box->assign('data', $data);
        $ims->temp_box->parse("box_page");
        return $ims->temp_box->text("box_page");
	}
  // end class
    function do_list_group ($info = array()){
        global $ims;

        $total = $ims->db->do_get_num("advisory", $ims->conf['qr'].' and find_in_set('.$info['group_id'].', group_nav)');
        $result = $ims->db->load_item_arr('advisory', $ims->conf['qr'].' and find_in_set ('.$info['group_id'].', group_nav) order by show_order desc, date_create desc limit '.$ims->setting['advisory']['num_list'], 'title, content, date_create');
        if($result){
            $info['start'] = count($result);
            if($total > $info['start']){
                $info['view_more'] = '<div class="view_more"><span onclick="imsAdvisory.load_more();">'.$ims->lang['advisory']['view_more'].'</span></div>';
            }
            $i = 0;
            foreach ($result as $row){
                $i++;
                if($i == 1){
                    $row['active'] = 'active';
                    $row['class_i'] = 'close';
                    $row['none'] = '';
                }else{
                    $row['active'] = '';
                    $row['none'] = 'style="display:none;"';
                }
                $row['date'] = $ims->func->time_to_text($row['date_create']);
                $row['content'] = $ims->func->input_editor_decode($row['content']);
                $ims->temp_act->assign('row', $row);
                $ims->temp_act->parse('list_group.item');
            }
        }else{
            $ims->temp_act->parse('list_group.empty');
        }
        $ims->temp_act->assign('data', $info);
        $ims->temp_act->parse('list_group');
        return $ims->temp_act->text('list_group');
    }
}
?>