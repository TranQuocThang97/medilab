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
	var $action = "ajax";

	function __construct ()
	{
		global $ims;

        $ims->func->load_language($this->modules);
        $dir_view = $ims->func->dirModules('advisory', 'views', 'path');
        $ims->temp_act = new XTemplate($dir_view."advisory.tpl");
        $ims->temp_act->assign('CONF', $ims->conf);
        $ims->temp_act->assign('LANG', $ims->lang);
        $ims->temp_act->assign('DIR_IMAGE', $ims->dir_images);
        require_once ($this->modules."_func.php");
		
		$fun = (isset($ims->input['f'])) ? $ims->input['f'] : '';

		switch ($fun) {
			case "advisory":
				echo $this->do_advisory ();
				exit;
				break;
			case "load":
				$this->do_pagination ();
				exit;
				break;
            case "load_more":
                echo $this->do_load_more();
                exit;
                break;
			default:
				flush();
				echo '';
				exit;
				break;
		}
		
		exit;
	}
	
	function do_pagination ()
	{

        global $ims;
        //Get page number from Ajax POST

        if(isset($ims->input['page'])){
            $page_number = $ims->input['page'];
            $number = $ims->input['num'];
        }else{
            $page_number = 1; //if there's no page number, set it to 1
        }

        $group_id = Session::get('id_group');
        $arr_in = array(
//            'link_action' => $ims->site_func->get_link ('page'),
            'where' => " and find_in_set('".$group_id."',group_nav)>0",
            'temp' => 'list_item',
            'page' => $page_number,
            'num' => $number
        );
        //print_arr($kq= html_list_item($arr_in)); die();
         echo $kq= html_list_item($arr_in);
        //echo json_encode($kq);
	}
	function do_advisory ()
	{
		global $ims;	
		
		$output = array(
			'ok' => 0,
			'mess' => $ims->lang['advisory']['send_false'],
			'show' => 1
		);
		
		$input_tmp = $ims->post['data'];
		foreach($input_tmp as $key) {
			$input[$key['name']] = $key['value'];
		}
		
		$arr_in = array();		
		$arr_key = array('email','phone','summary');
		
		foreach($arr_key as $key) {
		
			//$arr_in[$key] = (isset($input[$key])) ? $input[$key] : '';
		
		}		
		if(0==0) {
			$arr_in["item_id"] =$ims->db->getAutoIncrement("advisory");
			$arr_in["owner_nickname"] =$input['username'];
			$arr_in["owner_email"] =$input['email'];
			$arr_in["owner_phone"] =$input['phone'];
			$arr_in["title"] =$input['content'];
			$arr_in["is_approval"] =0;
			$arr_in["lang"] ='vi';						
			$arr_in["date_create"] = time();
			$arr_in["date_update"] = time();	
			//$arr_in["friendly_link"] = $ims->func->get_friendly_link_db ($input["content"], ''.$this->modules, 'item_id', $arr_in["item_id"], 'vi');
			$ok = $ims->db->do_insert("advisory", $arr_in);				
			if($ok) {					
				$output['ok'] = 1;
				$output['mess'] = $ims->lang['advisory']['send_success'];
				
				//Send email
				/*				
				$mail_arr_value = $arr_in;
				$mail_arr_value['date_create'] = $ims->func->get_date_format($mail_arr_value["date_create"]);
				$mail_arr_value['domain'] = $_SERVER['HTTP_HOST'];
				$mail_arr_key = array();
				foreach($mail_arr_value as $k => $v) {
					$mail_arr_key[$k] = '{'.$k.'}';
				}				
				//send to admin
				$ims->func->send_mail_temp ('admin-contact', $ims->conf['email'], $ims->conf['email'], $mail_arr_key, $mail_arr_value);
				//send to contact
				$ims->func->send_mail_temp ('contact', $arr_in["email"], $ims->conf['email'], $mail_arr_key, $mail_arr_value);
				//End Send email
				*/
			}
		}
		
		return json_encode($output);
	}
    function do_load_more(){
        global $ims;
        include_once($ims->conf["rootpath"].DS."config/site.php");
        include_once($ims->conf["rootpath"].DS."config".DS."xtemplate.class.php");
        $ims->site = new Site($this);
        $ims->site_func->setting('advisory');

        $output = array(
            'num' => 0,
            'html' => '',
        );

        $where = '';
        $start = isset($ims->post['num_cur']) ? $ims->post['num_cur'] : '';
        $group_id = isset($ims->post['group_id']) ? $ims->post['group_id'] : 0;
        $num_list = $ims->setting['advisory']['num_list'];

        if($group_id != 0){
            $where .= ' and (find_in_set('.$group_id.', group_nav) or find_in_set('.$group_id.', group_related))';
        }

        $total = $ims->db->do_get_num("advisory", 'is_show = 1 and lang = "'.$ims->conf['lang_cur'].'"' . $where);
        $result = $ims->db->load_item_arr("advisory", 'is_show = 1 and lang = "'.$ims->conf['lang_cur'].'"' . $where.' limit '.$start.','.$num_list, 'title, content, date_create');
        foreach ($result as $row){
            $row['date'] = $ims->func->time_to_text($row['date_create']);
            $row['content'] = $ims->func->input_editor_decode($row['content']);
            $row['none'] = 'style="display:none"';
            $ims->temp_act->assign('row', $row);
            $ims->temp_act->reset('list_group.item');
            $ims->temp_act->parse('list_group.item');
            $output['html'] .= $ims->temp_act->text('list_group.item');
        }
        $result_total = count($result);
        if(($start + $result_total) == $total){
            $output['num'] = 0;
        }else{
            $output['num'] = $start + $result_total;
        }
        return json_encode($output);
    }
  // end class
}
?>