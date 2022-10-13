<?php

/*================================================================================*\
Name code : view.php
Copyright © 2013 by Tran Thanh Hiep
@version : 1.0
@date upgrade : 03/02/2013 by Tran Thanh Hiep
\*================================================================================*/

if (! defined('IN_ims')) {
	echo "string";die;
  die('Access denied');
}
$nts = new sMain();

class sMain
{
	var $modules = "library";
	var $action = "ajax";
	
	/**
	* function __construct ()
	* Khoi tao 
	**/
	function __construct ()
	{
		global $ims;
		$ims->dir_mod = "modules/".$this->modules."/";
		$this->link_act = "ajax.php?m=".$this->modules;
		if($ims->site_func->checkUserLogin() != 1) {
			die('Access denied');
		}
		$token_key = isset($ims->get['token']) ? $ims->get['token'] : 'token_'.$ims->func->random_str (10, 'ln');

		$root_mod = isset($_SESSION[$token_key]['root_mod']) ? $_SESSION[$token_key]['root_mod'] : '';
		$root_mod = isset($ims->conf['root_mod']) ? $ims->conf['root_mod'] : $root_mod;
		
		if(!isset($ims->get['token']) && isset($ims->conf['root_mod'])) {
			$_SESSION[$token_key] = array('root_mod' => $ims->conf['root_mod']);
		}
		$this->link_act .= '&token='.$token_key;
		$ims->conf['root_mod'] = $root_mod;
		
		//$ims->func->load_language($this->modules);
		
		$fun = (isset($ims->get['sub'])) ? $ims->get['sub'] : '';

		switch ($fun) {
			case "upload":
				$this->do_upload();
				break;
			case "uploader":
				$this->do_uploader();
				break;
			case "execute":
				$this->do_execute();
				break;
			case "force_download":
				$this->do_force_download();
				break;
			case "ajax_calls":
				$this->do_ajax_calls();
				break;
			case "popup_library":
				$this->do_popup_library();
				break;
			default:
				die('Access denied');
				$this->do_manage_library();
				break;
		}
		flush();
		exit;
	}
	
	private function do_upload()
	{
		global $ims;
		
		flush();
		$ims->conf["folder_up"] = (isset($ims->get["folder_up"])) ? $ims->get["folder_up"] : "";
		require_once($ims->conf['rootpath'].'modules'.DS.'library'.DS.'config/config.php');
		if($_SESSION["verify"] != "RESPONSIVEfilemanager") die('forbiden');
		require_once($ims->conf['rootpath'].'modules'.DS.'library'.DS.'include/utils.php');
		
		require_once($ims->conf['rootpath'].'modules'.DS.'library'.DS.'upload.php');
		
		exit;
	}
	
	private function do_uploader()
	{
		global $ims;
		
		flush();
		$ims->conf["folder_up"] = (isset($ims->get["folder_up"])) ? $ims->get["folder_up"] : "";
		require_once($ims->conf['rootpath'].'modules'.DS.'library'.DS.'uploader/jupload.php');
		
		exit;
	}
	
	private function do_execute()
	{
		global $ims;
		
		flush();
		$ims->conf["folder_up"] = (isset($ims->get["folder_up"])) ? $ims->get["folder_up"] : "";
		require_once($ims->conf['rootpath'].'modules'.DS.'library'.DS.'config/config.php');
		if($_SESSION["verify"] != "RESPONSIVEfilemanager") die('forbiden');
		require_once($ims->conf['rootpath'].'modules'.DS.'library'.DS.'include/utils.php');
		
		require_once($ims->conf['rootpath'].'modules'.DS.'library'.DS.'execute.php');
		
		exit;
	}
	
	private function do_force_download()
	{
		global $ims;
		
		flush();
		//$ims->conf["folder_up"] = (isset($ims->get["folder_up"])) ? $ims->get["folder_up"] : "";
		require_once($ims->conf['rootpath'].'modules'.DS.'library'.DS.'config/config.php');
		if($_SESSION["verify"] != "RESPONSIVEfilemanager") die('forbiden');
		require_once($ims->conf['rootpath'].'modules'.DS.'library'.DS.'include/utils.php');
		require_once($ims->conf['rootpath'].'modules'.DS.'library'.DS.'force_download.php');
		exit;
	}
	
	private function do_ajax_calls()
	{
		global $ims;
		
		flush();
		$ims->conf["folder_up"] = (isset($ims->get["folder_up"])) ? $ims->get["folder_up"] : "";
		require_once($ims->conf['rootpath'].'modules'.DS.'library'.DS.'config/config.php');
		if($_SESSION["verify"] != "RESPONSIVEfilemanager") die('forbiden');
		require_once($ims->conf['rootpath'].'modules'.DS.'library'.DS.'include/utils.php');
		
		require_once($ims->conf['rootpath'].'modules'.DS.'library'.DS.'ajax_calls.php');
		
		exit;
	}
	
	//-----------
	private function do_popup_library()
	{
		global $ims;
		
		flush();
		
		$ims->conf["folder_up"] = (isset($ims->get["folder_up"])) ? $ims->get["folder_up"] : "";

		require_once($ims->conf['rootpath'].'modules'.DS.'library'.DS.'config/config.php');
		
		require_once($ims->conf['rootpath'].'modules'.DS.'library'.DS.'popup_library.php');
		
		exit;
	}
	
	//-----------
	private function do_manage_library()
	{
		global $ims;
		
		flush();
		
		$ims->conf["folder_up"] = (isset($ims->get["folder_up"])) ? $ims->get["folder_up"] : "";
		require_once($ims->conf['rootpath'].'modules'.DS.'library'.DS.'config/config.php');
		
		require_once($ims->conf['rootpath'].'modules'.DS.'library'.DS.'manage_library.php');
		
		exit;
	}
		
  // end class
}
?>