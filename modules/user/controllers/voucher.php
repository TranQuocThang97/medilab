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
	var $modules = "user";
	var $action = "voucher";
	var $sub = "manage";
	
	/**
	* function __construct ()
	* Khoi tao 
	**/
	function __construct ()
	{
		global $ims;
		
		if($ims->site_func->checkUserLogin() != 1) {
			$url = $ims->func->base64_encode($_SERVER['REQUEST_URI']);
			$url = (!empty($url)) ? '/?url='.$url : '';
			
			$link_go = $ims->site->get_link ($this->modules, $ims->setting[$this->modules]["signin_link"]).$url;
			$ims->html->redirect_rel($link_go);
		}
		
		$ims->func->load_language($this->modules);
		$ims->temp_act = new XTemplate($ims->path_html.$this->modules.DS.$this->modules.".tpl");
		$ims->temp_act->assign('CONF', $ims->conf);
		$ims->temp_act->assign('LANG', $ims->lang);
		$ims->temp_act->assign('DIR_IMAGE', $ims->dir_images);
		
		$ims->func->include_css ($ims->dir_css.$this->modules.'/'.$this->modules.'.css');
		
		$ims->conf['menu_action'] = array($this->modules);
		$ims->data['link_lang'] = (isset($ims->data['link_lang'])) ? $ims->data['link_lang'] : array();
		
		include ($this->modules."_func.php");
		
		$data = array();
		//Make link lang
		foreach($ims->data['lang'] as $row_lang) {
			$ims->data['link_lang'][$row_lang['name']] = $ims->site->get_link_lang ($row_lang['name'], $this->modules);
		}
		//End Make link lang
		
		//SEO
		$ims->site->get_seo (array(
			'meta_title' => (isset($ims->setting[$this->modules][$this->action."_meta_title"])) ? $ims->setting[$this->modules][$this->action."_meta_title"] : '',
			'meta_key' => (isset($ims->setting[$this->modules][$this->action."_meta_key"])) ? $ims->setting[$this->modules][$this->action."_meta_key"] : '',
			'meta_desc' => (isset($ims->setting[$this->modules][$this->action."_meta_desc"])) ? $ims->setting[$this->modules][$this->action."_meta_desc"] : ''
		));
		$ims->conf["cur_group"] = 0;
		
		$data = array();
		$data['content'] = $this->do_manage();
		$data['box_left'] = box_left($this->action);
		//$data['box_column'] = box_column();
	
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("main");
		$ims->output .=  $ims->temp_act->text("main");
	}
	
	//-----------
	function manage_row($row)
	{
		global $ims;
		
		$output = '';
		
		if(!empty($row["picture"])){
			$row["picture"] = '<a class="fancybox-effects-a" title="'.$row["picture"].'" href="'.DIR_UPLOAD.$row["picture"].'">
				'.$ims->func->get_pic_mod($row["picture"], 50, 50, '', 1, 0, array('fix_width'=>1)).'
			</a>';
		}
		
		$row['amount'] = $ims->func->get_price_format($row['amount'], 0);
		$row['amount_use'] = $ims->func->get_price_format($row['amount_use'], 0);
		$row['date_create'] = date('d/m/Y',$row['date_create']);
		$row['date_end'] = date('d/m/Y, H:i',$row['date_end']);
		
		$ims->temp_act->assign('row', $row);
		
		$ims->temp_act->parse("voucher.row_item");
		$output = $ims->temp_act->text("voucher.row_item");
		$ims->temp_act->reset("voucher.row_item");
		
		return $output;
	}
	
	//-----------
	function do_manage($is_show="")
	{
		global $ims;
		
		$err = "";
		
		$p = (isset($ims->input["p"])) ? $ims->input["p"] : 1;
		$search_date_begin = (isset($ims->input["search_date_begin"])) ? $ims->input["search_date_begin"] : "";
		$search_date_end = (isset($ims->input["search_date_end"])) ? $ims->input["search_date_end"] : "";
		$search_title = (isset($ims->input["search_title"])) ? $ims->input["search_title"] : "";
		
		$where = " ";
		$ext = "";
		$is_search = 0;
		
		$where .= " where is_show=1 and user_id='".$ims->data['user_cur']['user_id']."' ";
		
		if($search_date_begin || $search_date_end ){
			$tmp1 = @explode("/", $search_date_begin);
			$time_begin = @mktime(0, 0, 0, $tmp1[1], $tmp1[0], $tmp1[2]);
			
			$tmp2 = @explode("/", $search_date_end);
			$time_end = @mktime(23, 59, 59, $tmp2[1], $tmp2[0], $tmp2[2]);
			
			$where.=" AND (date_create BETWEEN {$time_begin} AND {$time_end} ) ";
			$ext.="&date_begin=".$search_date_begin."&date_end=".$search_date_end;
			$is_search = 1;
		}
		
		if(!empty($search_title)){
			$where .=" and (a.order_id='$search_title' or title like '%$search_title%') ";			
			$ext.="&search_title=".$search_title;
			$is_search = 1;
		}
    
		$num_total = 0;
		$res_num = $ims->db->query("select voucher_id from voucher ".$where." ");
			$num_total = $ims->db->num_rows($res_num);
		$n = 20;//($ims->conf["n_list"]) ? $ims->conf["n_list"] : 20;
		$num_products = ceil($num_total / $n);
		if ($p > $num_products)
		  $p = $num_products;
		if ($p < 1)
		  $p = 1;
		$start = ($p - 1) * $n;
		
		$link_action = $ims->site->get_link ($this->modules,$ims->setting[$this->modules]["ordering_link"]);
		
		$where .= " order by date_create DESC";

    $sql = "select * from voucher ".$where." limit $start,$n";
    //echo $sql;
		
		$nav = $ims->site->paginate ($link_action, $num_total, $n, $ext, $p);
		
		$result = $ims->db->query($sql);
    $i = 0;
		$data['row_item'] = '';
    $html_row = "";
    if ($num = $ims->db->num_rows($result))
		{
			while ($row = $ims->db->fetch_row($result)) 
			{
				$i++;
				$row['stt'] = $start + $i;
				$data['row_item'] .= $this->manage_row($row);
			}
		}
		else
		{
			$ims->temp_act->assign('row', array("mess"=>$ims->lang["user"]["no_have_data"]));
			$ims->temp_act->parse("voucher.row_empty");
		}
		
		$data['html_row'] = $html_row;
		$data['nav'] = $nav;
		$data['err'] = $err;
		
		$data['link_action_search'] = $link_action;
		$data['link_action'] = $link_action."&p=".$p.$ext;
		
		$data['search_date_begin'] = $search_date_begin;
		$data['search_date_end'] = $search_date_end;
		$data['search_title'] = $search_title;
		$data['form_search_class'] = ($is_search == 1) ? ' expand' : '';
		
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("voucher");
		return $ims->temp_act->text("voucher");
	}
	
  // end class
}
?>