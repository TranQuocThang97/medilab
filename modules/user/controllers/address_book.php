<?php
if (!defined('IN_ims')) { die('Access denied'); }
$nts = new sMain();

class sMain
{
	var $modules = "user";
	var $action  = "address_book";
	
	/**
		* Khởi tạo
		* Sổ địa chỉ của tài khoản
	**/
	function __construct ()
	{
		global $ims;
		
		$arrLoad = array(
			'modules' 		 => $this->modules,
			'action'  		 => $this->action,
			'template'  	 => $this->modules,
			'js'  	 		 => $this->modules,
			'css'  	 		 => $this->modules,
			'use_func'  	 => $this->modules, // Sử dụng func
            'use_navigation' => 0, // Sử dụng navigation
			'required_login' => 1, // Bắt buộc đăng nhập
		);
        $ims->func->loadTemplate($arrLoad);

		$data 			  = array();
		$data['box_left'] = box_left($this->action);
		$data['content']  = $this->do_main();
		$ims->conf["class_full"] = 'user';
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("main");
		$ims->output .=  $ims->temp_act->text("main");
	}
	
	function do_main ()
	{
		global $ims;
		
		$data = array();
		$link_action = $ims->site_func->get_link($this->modules, $ims->setting[$this->modules][$this->action."_link"]);
		$err = '';
		
		if(!empty($ims->data['user_cur']['arr_address_book'])){
			$arr_address = $ims->func->unserialize($ims->data['user_cur']['arr_address_book']);			
			foreach ($arr_address as $row) {				
				$row['address_full'] = $ims->func->full_address($row);
				$row['color'] = "#fff";
				$row['bg'] = "#787878";
				if($row['is_default'] == 1){					
					$row['default'] = 'is-default';
					$ims->temp_act->assign('row', $row);					
					$ims->temp_act->parse("address_book.row.default");
					$ims->temp_act->parse("address_book.row.unremove");
				}else{
					$ims->temp_act->assign('row', $row);
					$ims->temp_act->parse("address_book.row.remove");
				}
				$ims->temp_act->assign('row', $row);
				$ims->temp_act->parse("address_book.row");			
			}
		}	
		$arr_address = $ims->func->unserialize($ims->data['user_cur']['arr_address_book']);
		$arr_k = array('full_name','email','phone','address','province','district','ward');
		if (isset($ims->post['do_submit'])) {	
			$arr_in = array();
			if(count($arr_address)>0){
				$arr_in['id'] = $ims->data['user_cur']['user_id'].count($arr_address);
			}else{
				$arr_in['id'] = $ims->data['user_cur']['user_id'].'0';
			}			
			foreach($arr_k as $k) {				
				$col['d_'.$k] = $arr_in[$k] = (isset($ims->post['d_'.$k])) ? $ims->post['d_'.$k] : '';
			}			
			$arr_in['is_default'] = (isset($ims->post['is_default'])) ? $ims->post['is_default'] : '0';
			$arr_address[$arr_in['id']] = $arr_in;
			if($arr_in['is_default'] != 0){
				$arr_temp = array();
				foreach ($arr_address as $row) {					
					if($row['id'] != $arr_in['id']){
						$row['is_default'] = 0;						
					}
					$arr_temp[$row['id']] = $row;
				}
				$arr_address = $arr_temp;
			}
			$arr_address = serialize($arr_address);
			$ok = $ims->db->do_update('user', array('arr_address_book'=>$arr_address), ' user_id="'.$ims->data['user_cur']['user_id'].'"');
			if($ok){
				$ims->func->include_js_content('window.history.replaceState( null, null, window.location.href);$( "#form_ordering_address" ).load(window.location.href + " #form_ordering_address" );');
			}
		}
		//edit
		if (isset($ims->post['do_edit'])) {
			$arr_in['id'] = $ims->post['id'];
			foreach($arr_k as $k) {
				$arr_in[$k] = (isset($ims->post['d_'.$k])) ? $ims->post['d_'.$k] : '';
			}
			$arr_in['is_default'] = (isset($ims->post['is_default'])) ? $ims->post['is_default'] : '0';
			$arr_address[$arr_in['id']] = $arr_in;
			if($arr_in['is_default'] != 0){
				$arr_temp = array();
				foreach ($arr_address as $row) {
					if($row['id'] != $arr_in['id']){
						$row['is_default'] = 0;
					}
					$arr_temp[$row['id']] = $row;
				}
				$arr_address = $arr_temp;
			}
			$arr_address = serialize($arr_address);
			$ok = $ims->db->do_update('user', array('arr_address_book'=>$arr_address), ' user_id="'.$ims->data['user_cur']['user_id'].'"');
			if($ok){
				$ims->func->include_js_content('window.history.replaceState( null, null, window.location.href);$( "#form_ordering_address" ).load(window.location.href + " #form_ordering_address" );');
			}
		}
		
		$data['page_title'] = $ims->lang["user"]["address_book"];
		$ims->temp_act->assign('data', $data);
		$ims->temp_act->parse("address_book");
		return $ims->temp_act->text("address_book");
	}
  	// End class
}
?>