<?php
if (!defined('IN_ims')) { die('Access denied'); }
$nts = new sMain();

class sMain
{
	var $modules = "user";
	var $action  = "deeplink";
	var $sub 	 = "manage";

	function __construct (){
		global $ims;
		
		$arrLoad = array(
			'modules' 		 => $this->modules,
			'action'  		 => $this->action,
			'template'  	 => $this->action,
			'js'  	 		 => $this->modules,
			'css'  	 		 => $this->modules,
			'use_func'  	 => $this->modules, // Sử dụng func
			'use_navigation' => 0, // Sử dụng navigation
			'required_login' => 1, // Bắt buộc đăng nhập
		);
        $ims->func->loadTemplate($arrLoad);

		$ims->func->include_js ($ims->dir_js.'jquery.copy-to-clipboard.js');
		
		$data = array();
		
		$data['content'] = $this->do_main();
		$data['box_left'] = box_left($this->action);
	    $ims->conf['container_layout'] = 'm';
	    $ims->conf['class_full'] = ' deeplink';
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("main");
		$ims->output .=  $ims->temp_act->text("main");
	}
	

	//-----------
	function do_main()
	{
		global $ims;

        $data = array();
        $ims->site_func->setting("product");        
        $arr_deep = $ims->db->load_row_arr('user_deeplink',' user_id= '.$ims->data["user_cur"]["user_id"].' and is_show=1 ORDER BY date_create desc','*');
        //print_arr($_COOKIE);
        if ($arr_deep) {
            $i = 0;
            foreach ($arr_deep as $row) {
                $i++;
                $row['stt'] = $i;                
                $row['link_embed'] = $ims->conf['rooturl'].$ims->setting["product"]["embed_link"].'/'.$row['short_code'];
                if ($row['type'] == 'group'){
                    $group = $ims->db->load_row('product_group',' group_id  = '.$row["item_id"].' and is_show=1 and lang = "'.$ims->conf["lang_cur"].'" ','title,friendly_link');
                    $tmp = base64_encode($group['title']);
                    $tmp = str_replace('/', '+', $tmp);
                    $row['link_short'] = $ims->conf['rooturl'].'redirect/allproduct/'.$row["item_id"].'/'.$tmp.'/'.$row['short_code'];
                }

                if ($row['type'] == 'detail'){
                    $group = $ims->db->load_row('product',' item_id  = '.$row["item_id"].' and is_show=1 and lang = "'.$ims->conf["lang_cur"].'" ','title,friendly_link');
                    $row['link_short'] = $ims->conf['rooturl'].'redirect/product/'.$row["item_id"].'/'.$row['short_code'];
                }

                $row['group_name'] = $group['title'];
                $row['group_link'] = $row['link'] = $ims->site_func->get_link('product',$group['friendly_link']);
                $row['date_create'] = date('H:i d/m/Y ',$row['date_create']);
                $ims->temp_act->assign('row', $row);
                $ims->temp_act->parse("deeplink.row");
            }
        }

        $ims->func->include_js_content("imsUser.add_deeplink('form_deeplink')");

        $data['page_title'] = $ims->setting["user"]["deeplink_meta_title"];
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("deeplink");
		return $ims->temp_act->text("deeplink");
		// return $ims->html->temp_box('box_main',array('content' => $output,'title'=> $ims->setting["user"]["deeplink_meta_title"]));

	}

  // end class
}
?>