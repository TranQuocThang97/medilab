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
	var $modules = "recruitment";
	var $action = "ajax";
	
	/**
	* function __construct ()
	* Khoi tao 
	**/
	function __construct ()
	{
		global $ims;
		
		$ims->func->load_language($this->modules);
		
		$fun = (isset($ims->post['f'])) ? $ims->post['f'] : '';

		flush();
		switch ($fun) {
			case "load_item":
				echo $this->do_load_item ();
				exit;
				break;
			case "recruitment":
				echo $this->do_recruitment ();
				exit;
				break;			
			default:
				echo '';
				exit;
				break;
		}
		
		exit;
	}

	
	function upload_file($folder_upload, $file_upload){
        //exp:  $folder_upload ==> "user/".$ims->data['user_cur']['folder_upload'].'/2018_02';
        // $name_input ==> name input file upload (picture).
        global $ims;
        $output = array(
            'ok'    => 1,
            'mess'    => '',
        );
        $target_dir =  $ims->conf['rooturl_web'].'uploads/'.$folder_upload.'/';
        $target_file = $target_dir . basename($file_upload["name"]);
        $FileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
		// Check if file already exists
        if (file_exists($target_file)) {
            $output['mess'] = $ims->lang['recruitment']['file_exist'];
            $output['ok'] = 0;
            return $output;
        }
		// Check file size
        if ($file_upload["size"] == 0){
            $output['mess'] = $ims->lang['recruitment']['file_error'];
            $output['ok'] = 0;
            return $output;
        }
        if ($file_upload["size"] > 4000000) {
            $output['mess'] = $ims->lang['recruitment']['file_size'];
            $output['ok'] = 0;
            return $output;
        }
		// Allow certain file formats
		//,"xls","xlsx","xlsm"'
		$arr_format = ["pdf","doc","docx"];
        if(!in_array($FileType, $arr_format)) {
            $output['mess'] = $ims->lang['recruitment']['file_type'];
            $output['ok'] = 0;
            return $output;
        }
		// Check if $upload Ok is set to 0 by an error
		// if everything is ok, try to upload file
        $ims->func->rmkdir($folder_upload);
        // Save file
        $file_upload["name"] = str_replace(' ', '_', $file_upload["name"]);
        move_uploaded_file($file_upload["tmp_name"], $ims->conf['rootpath'].'uploads/'.$folder_upload.'/'.time().'_'.$file_upload["name"]);
        $output['link']  = $folder_upload.'/'.time().'_'.$file_upload["name"];
        return $output;
    }

	function do_recruitment ()
	{
		global $ims;	
		$ims->func->load_language('recruitment');
		$output = array(
			'ok' => 0,
			'mess' => $ims->lang['recruitment']['send_false'],
			'show' => 1
		);
		$cv_attachment = isset($_FILES['file'])?$_FILES['file']:'';
		$folder_upload = '';		
		if($cv_attachment!=''){
			if( $cv_attachment['error'] === 0){
				$folder_upload = "recruitment/".date('Y',time()).'_'.date('m',time()).'/CV';	
			}
        }

        $input_tmp = $ims->post['info'];
		$input_tmp = json_decode($input_tmp,true);
		foreach($input_tmp as $key => $value) {			
			$input[$key] = $value;
		}
		
		$arr_in = array();
		$arr_key = array('full_name','email','address','phone','title','content','file');
		foreach($arr_key as $key) {
			$arr_in[$key] = (isset($input[$key])) ? $input[$key] : '';
		}		
		if(count($arr_in) > 0) {
			$arr_in["is_status"] = 0;
			$arr_in["date_create"] = time();
			$arr_in["date_update"] = time();			
			$arr_in["title"] .= " - ".$arr_in["full_name"];
			if($folder_upload!=''){
				$upload_file = $this->upload_file($folder_upload,$cv_attachment);
				if($upload_file['ok']==1){
					$arr_in['file'] = $upload_file['link'];
					$arr_in['content'] .= $ims->lang['recruitment']['download_attachment'].' <a href="'.$ims->conf['rooturl_web'].'uploads/'.$arr_in['file'].'">'.$ims->lang['recruitment']['here'].'</a>';
					$ok = $ims->db->do_insert("contact", $arr_in);
					if($ok){				
						$output['ok'] = 1;
						$output['mess'] = $ims->lang['recruitment']['send_success'];					
						//Send email
						$mail_arr_value = $arr_in;						
						$mail_arr_value['date_create'] = $ims->func->get_date_format($mail_arr_value["date_create"]);
						$mail_arr_value['domain'] = $_SERVER['HTTP_HOST'];
						$mail_arr_key = array();
						foreach($mail_arr_value as $k => $v) {
							$mail_arr_key[$k] = '{'.$k.'}';
						}
						
						// send to admin
						$ims->func->send_mail_temp ('apply-job-admin', $ims->conf['email'], $ims->conf['email'], $mail_arr_key, $mail_arr_value);
						
						// send to contact
						$ims->func->send_mail_temp ('apply-job', $arr_in["email"], $ims->conf['email'], $mail_arr_key, $mail_arr_value);
						// End Send email
					}
				}else{
					$output['ok'] = 0;
	                $output['mess']  = $upload_file['mess'];
				}	
			}					
		}
		
		return json_encode($output);
	}
		
	function do_load_item(){
		global $ims;		
		$output = array();
		$item_id = isset($ims->post['item_id'])?$ims->post['item_id']:'';
		$lang_cur = isset($ims->post['lang_cur'])?$ims->post['lang_cur']:'';
		$item = $ims->db->load_row('recruitment','lang="'.$lang_cur.'" and is_show=1 and item_id="'.$item_id.'"');
		if($item){
			$output['title'] = $item['title'];
			$output['picture'] = '<img src="'.$ims->func->get_src_mod($item['picture'],'','',1,0,array()).'" alt="'.$item['title'].'" title="'.$item['title'].'">';
			$output['content'] = $ims->func->input_editor_decode($item['content']);
		}
		return json_encode($output);
	}
	
  // end class
}
?>