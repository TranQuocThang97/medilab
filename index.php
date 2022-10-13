<?php
	session_cache_limiter('nocache');

	session_start();
	define('IN_ims', 1);
	define('PATH_ROOT', dirname(__FILE__));
	define('DS', DIRECTORY_SEPARATOR);
	$imsdebug_start=microtime();

	class ims { }
	$ims = new ims;
	require_once ("dbcon.php");

	// $cachefile = './cache_template/'.md5($_SERVER['REQUEST_URI']) . '.cache';
	// clearstatcache();
	// if (file_exists($cachefile) && filemtime($cachefile) > time() - 300) { // good to serve!
	//     include($cachefile);
	//     exit;
	// }
	// ob_start();
	// ini_set('display_errors', 0);

	$ims->conf = $conf;
	require_once __DIR__ . "/config/inc.php";

	$ims->navigation = '';
	$ims->output = '';
	$ims->data['js'] = $ims->data['css'] = '';
	$ims->conf['qr'] = ' is_show = 1 and lang = "'.$ims->conf['lang_cur'].'" ';

	if($ims->conf['construction'] == 1){
		$ims->site->loadGlobalCssFile();
		$ims->site->loadGlobalJSFile();
	}else{
		$ims->func->include_js ($ims->resources."minify/minify.jquery.min.js");
	}
	$ims->site->remember_me();
	$ims->func->include_js ($ims->func->dirModules('product', 'assets', 'js').'/ordering.js');
	$ims->func->include_js ($ims->func->dirModules('user', 'assets', 'js').'/user.js');
	$ims->func->include_js_content ("imsUser.check_order('check_order');");

	$ims->deviceType = ($ims->detect->isMobile() ? ($ims->detect->isTablet() ? 'tablet' : 'phone') : 'computer');
	// main	 
	$ims->conf['cur_mod'] = (isset($ims->conf['cur_mod'])) ? $ims->conf['cur_mod'] : "home";
	$ims->conf['cur_act'] = (isset($ims->conf['cur_act'])) ? $ims->conf['cur_act'] : "home";
	//echo $fileactname ;

	if (!file_exists($ims->conf['rootpath']."modules/".$ims->conf['cur_mod']."/controllers/".$ims->conf['cur_act'].".php")) {   
		$fileactname = "modules/".$ims->conf['cur_mod']."/".$ims->conf['cur_act'].".php";
	}else{
		$fileactname = "modules/".$ims->conf['cur_mod']."/controllers/".$ims->conf['cur_act'].".php";
	}

	if (file_exists($fileactname)){
		require_once $fileactname;
	} else {
		flush();
		require_once ($conf["rootpath"].DS."404.php");
		exit();
	}
	// end main
	// $box_lang = $ims->site->box_lang ($ims->conf["lang_cur"], 1);
	$box_lang = '';
	//Lang JS
	$lang_js = "";
	$lang_js .= "var lang_js_mod = new Array();";
	foreach($ims->lang as $k => $v) {
		if($k != 'global') {
			$lang_js .= "lang_js_mod['".$k."'] = new Array();";
		}
		foreach($v as $k1 => $v1) {
			$v1 = str_replace("'",'&acute;',$v1);
			if($k == 'global') {
				$lang_js .= "lang_js['".$k1."']='".$v1."';";
			} else {
				$lang_js .= "lang_js_mod['".$k."']['".$k1."']='".$v1."';";
			}
		}
	}

	//End Lang JS
	$ims->page_css = (isset($ims->page_css)) ? $ims->page_css : "";
	$ims->page_js = (isset($ims->page_js)) ? $ims->page_js : "";

	$data = array();
	$data['header'] = $ims->site_func->header();
	$data['menu_footer']   = $ims->site->list_menu('menu_footer', 'menu_footer');
	$data['menu_footer1']  = $ims->site->list_menu('menu_footer1', 'menu_footer');
	$data['menu_footer2']  = $ims->site->list_menu('menu_footer2', 'menu_footer');
	$data['menu_footer3']  = $ims->site->list_menu('menu_footer3', 'menu_footer1');
	$data['menu_aside']    = $ims->site->list_menu ('menu_header', 'menu_aside');

//	$ims->site->box_statistic();

	if($ims->conf['cur_mod'] == 'home'){
//		$data['menu_category'] = $ims->site->list_menu('menu_header_top', 'menu_bootstrap');
//		$data['main_slide'] = $ims->site->get_banner_slide('banner-main', 'main_slide');
//		$data['main_slide'] = $ims->site->get_banner('banner-main', 1);
	}else{
		$data['main_slide'] = $ims->site->get_banner_slide('banner-in','slide_in');
	}
//	if($ims->conf['cur_mod'] == 'product' && in_array($ims->conf['cur_act'], array('detail', 'combo', 'header', 'ordering_cart', 'ordering_method', 'ordering_complete', 'promotion'))){
//	    $data['none_slider'] = 'd-none';
//    }

	$layout = (isset($ims->conf['container_layout']) ? $ims->conf['container_layout'] : 'c-m');
	$layout = str_replace('-','_',$layout);
	if(strpos('  '.$layout, ' c_')) {
		// $column_left = $ims->site->block_left ();
		$column_left = (isset($ims->conf['column_left']) ? $ims->conf['column_left'] : $ims->site->block_left ());
	}
	if(strpos($layout.' ', '_c ')) {
		// $column_right = $ims->site->block_column ();
		$column_right = (isset($ims->conf['column_right']) ? $ims->conf['column_right'] : $ims->site->block_column ());
	}
	
	//----------Not default--------------
	$data['social'] = $ims->site->do_social();
	$data['contact_footer'] = $ims->site->get_banner('contact-footer');
	$data['top_footer'] = $ims->site->top_footer();
//	$data['contact_footer_title'] = $ims->db->load_item('banner', $ims->conf['qr'].' and group_name = "contact-footer"', 'title');
//	$data['copyright'] = $ims->site_func->get_lang('copyright', 'global', array('{copyright}' => $ims->conf['copyright']));
//	$data['maps'] = $ims->func->input_editor_decode($ims->conf['maps_iframe']);
//    if($ims->conf['cur_mod'] != 'user'){
//        if ($ims->site_func->checkUserLogin() == 1) {
//            require_once ("modules/user/controllers/user_func.php");
//            $data['menu_user'] = box_left('');
//        }
//    }
	//----------Not default--------------

	// đóng kết nối db
	$ims->db->close();

//	$data['register_mail'] = $ims->site->register_mail ();
	$data['logo'] 		   = $ims->site->get_logo ('logo');
	$data['footer_logo']   = $ims->site->get_banner ('logo-footer');
//	$data['scroll_left']   = $ims->site->get_banner ('scroll-left');
//	$data['scroll_right']  = $ims->site->get_banner ('scroll-right');
	$data['deviceType']    = $ims->deviceType;
//	$data['brand_scroll']  = $ims->site->get_banner_slide ('brand', 'brand_scroll', 1);

	if($ims->conf['construction'] == 0){
		$ims->conf['include_css'] = $ims->site->compressingCssOutput($ims->arr_include_css);
		$data['box_style'] = $ims->func->fileGetContent($ims->resources."minify/minify.style.min.css");
	}
	$ims->conf['tag_footer'] = $ims->func->input_editor_decode($ims->conf['tag_footer']);

	if(!isset($ims->conf["meta_image"])){
		// $pic = $ims->db->load_item('banner', 'is_show = 1 and lang = "'.$ims->conf['lang_cur'].'" and group_id = "logo"', 'content');
		$pic = $ims->data['banner']['logo'];
		$pic = array_values($pic);		
		$ims->conf["meta_image"] = (isset($ims->conf['picture_share']) && $ims->conf['picture_share'] != '') ? $ims->func->get_src_mod($ims->conf['picture_share']) : $ims->func->get_src_mod($pic[0]['content']);
	}
	$ims->conf['meta_more'] = '<meta property="og:image" content="'.$ims->conf["meta_image"].'" />';
	$ims->conf['meta_more'] .= '<meta property="og:image:width" content="630" /><meta property="og:image:height" content="420" />';

	$ims->conf['content_footer'] = $ims->func->input_editor_decode($ims->conf['content_footer']);

	$ims->conf['embedcode_head_begin'] = $ims->func->input_editor_decode($ims->conf['embedcode_head_begin']);
	$ims->conf['embedcode_head'] 	   = $ims->func->input_editor_decode($ims->conf['embedcode_head']);
	$ims->conf['embedcode_body_begin'] = $ims->func->input_editor_decode($ims->conf['embedcode_body_begin']);
	$ims->conf['embedcode_body'] 	   = $ims->func->input_editor_decode($ims->conf['embedcode_body']);

	$ims->temp_html->assign("CONF", $ims->conf);
//	$ims->temp_html->parse("body.style_custom");

	$ims->temp_html->assign("BOX_LANG",  $box_lang);
	$ims->temp_html->assign("DIR_IMAGE", $ims->dir_images);
	$ims->temp_html->assign("DIR_CSS", $ims->dir_css);
	$ims->temp_html->assign("DIR_JS", $ims->dir_js);
	$ims->temp_html->assign("LANG", $ims->lang);
	$ims->temp_html->assign("LANG_JS", $lang_js);
	$ims->temp_html->assign("CONF", $ims->conf);
	$ims->temp_html->assign("data", $data);

	if($ims->conf['cur_mod'] == 'product' && $ims->conf['cur_act'] == 'embed'){
		$layout = (isset($ims->conf['container_layout']) ? $ims->conf['container_layout'] : 'c-m');
		$layout = str_replace('-','_',$layout);
		$ims->temp_html->assign("CONF", $ims->conf);
		$ims->temp_html->assign("PAGE_CONTENT", $ims->output);
		$ims->temp_html->parse("embed.container_".$layout);
		$ims->temp_html->parse("embed");
		$ims->temp_html->out("embed");
	}elseif($ims->conf['cur_mod'] == 'search'){
		$ims->temp_html->assign("CONF", $ims->conf);
		$ims->temp_html->assign("PAGE_CONTENT", $ims->output);
		$ims->temp_html->parse("search");
		$ims->temp_html->out("search");
	}else{
		if(strpos('  '.$layout, ' c_')) {
			$ims->temp_html->assign("PAGE_COLUMN_LEFT", $column_left);
		}
		if(strpos($layout.' ', '_c ')) {
			$ims->temp_html->assign("PAGE_COLUMN", $column_right);
		}
		$ims->temp_html->assign("PAGE_CONTENT", $ims->output);
		$ims->temp_html->assign("CONF", $ims->conf);
		$ims->temp_html->parse("body.container_".$layout);
		$ims->temp_html->parse("body");

		// $ims->conf['minify'] = true;
		if(isset($ims->conf['minify']) && $ims->conf['minify'] == true){
		    echo minify::html($ims->temp_html->text("body"));
		} else {
	    	$ims->temp_html->out("body");
			// $contents = $ims->temp_html->text("body");
			// // Cache the contents to a cache file
			// $cached = fopen($cachefile, 'w');
			// fwrite($cached, $contents);
			// fclose($cached);
			// ob_end_flush(); // Send the output to the browser
	  //   	include($cachefile);
		}
	}
	
	// $imsdebug_end = microtime();
	// // text Debug
	// $time_start = $ims->db->micro_time($imsdebug_start);
	// $time_stop  = $ims->db->micro_time($imsdebug_end);
	// echo '<div style="background:#ffffff; position:absolute; top:0px; left:0px;z-index:9999;">';
	// echo $ims->db->debug_log();
	// echo "<br>";
	// echo "Exec time: ".bcsub($time_stop, $time_start, 6)." s";
	// echo '</div>';
	// exit();
?>
