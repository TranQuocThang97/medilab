<?php
if (!defined('IN_ims')) { die('Access denied'); }

class Func {
    function fileGetContent($url=''){
        global $ims;

        $response = '';
        $arrContextOptions = array(
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            )
        );
        $response = file_get_contents($url, false, stream_context_create($arrContextOptions));
        return $response;
    }
    // load the shared template_email
    function loadTemplate($arrLoad=array()){
        global $ims;

        // required login
        if (isset($arrLoad['required_login']) && $arrLoad['required_login'] == 1) {
            $ims->site_func->setting("user");
            if($ims->site_func->checkUserLogin() != 1) {
                $url = $ims->func->base64_encode($_SERVER['REQUEST_URI']);
                $url = (!empty($url)) ? '/?url='.$url : '';
                $link_go = $ims->site_func->get_link ("user", $ims->setting["user"]["signin_link"]).$url;
                $ims->html->redirect_rel($link_go);
            }
        }
        if(isset($arrLoad['required_store']) && $arrLoad['required_store'] == 1){
            if($ims->site_func->checkUserLogin() == 1) {
                $is_store = $ims->db->load_item('store', $ims->conf['qr'].' and user_id = '.$ims->data['user_cur']['user_id'], 'item_id');
                if(!$is_store){
                    $ims->html->redirect_rel($ims->site_func->get_link('store'));
                }
            }
        }
        $this->load_language($arrLoad['modules']);
        $dir_assets  = $this->dirModules($arrLoad['modules'], 'assets');
        $dir_view    = $this->dirModules($arrLoad['modules'], 'views', 'path');
        $dir_control = $this->dirModules($arrLoad['modules'], 'controllers', 'path_control');

        $ims->temp_act = new XTemplate($dir_view . $arrLoad['template'] . ".tpl");
        $ims->temp_act->assign('CONF', $ims->conf);
        $ims->temp_act->assign('LANG', $ims->lang);
        $ims->temp_act->assign('DIR_IMAGE', $ims->dir_images);

        // Include css 
        if (isset($arrLoad['css']) && $arrLoad['css']!="") {
            $ims->func->include_css($dir_assets."css/".$arrLoad['css'].'.css');
        }
        // Include js 
        if (isset($arrLoad['js']) && $arrLoad['js']!="") {
            if($ims->conf['cur_mod'] == 'home'){
                $dir_assets  = $this->dirModules('product', 'assets');
                $dir_assets_user  = $this->dirModules('user', 'assets');
                $ims->func->include_js($dir_assets_user.'js/user.js');
            }
            $ims->func->include_js($dir_assets."js/".$arrLoad['js'].'.js');
        }
        // Include func 
        if (isset($arrLoad['use_func']) && $arrLoad['use_func'] !="" ) {
            include ($dir_control . $arrLoad['use_func']."_func.php");
        }
        // Include func css
        if (isset($arrLoad['use_func_css']) && $arrLoad['use_func_css'] == 1) {
            $ims->func->include_css($dir_assets."css/func.css");
        }

        $ims->conf['menu_action'] = array($arrLoad['modules'], $arrLoad['action']);

        $ims->data['link_lang']   = $this->if_isset($ims->data['link_lang'], array());
        //Make link lang

        if ($arrLoad['action'] != "detail" && $arrLoad['action'] != "promotion") {
            foreach($ims->data['lang'] as $v) {
                $ims->data['link_lang'][$v['name']] = $ims->site_func->get_link_lang ($v['name'], $arrLoad['modules'], $ims->setting[$arrLoad['modules'].'_'.$v['name']][$arrLoad['action'].'_link']);
            }
        }
        //End Make link lang

        //SEO
        if(!isset($ims->data['cur_item'])){
            $ims->setting[$arrLoad['modules']][$arrLoad['action']."_meta_title"] = strip_tags($ims->setting[$arrLoad['modules']][$arrLoad['action']."_meta_title"]);
            $ims->setting[$arrLoad['modules']][$arrLoad['action']."_meta_key"] = strip_tags($ims->setting[$arrLoad['modules']][$arrLoad['action']."_meta_key"]);
            $ims->setting[$arrLoad['modules']][$arrLoad['action']."_meta_desc"] = strip_tags($ims->setting[$arrLoad['modules']][$arrLoad['action']."_meta_desc"]);
            $ims->site->get_seo(array(
                'meta_title' => $ims->func->if_isset($ims->setting[$arrLoad['modules']][$arrLoad['action']."_meta_title"]),
                'meta_key'   => $ims->func->if_isset($ims->setting[$arrLoad['modules']][$arrLoad['action']."_meta_key"]),
                'meta_desc'  => $ims->func->if_isset($ims->setting[$arrLoad['modules']][$arrLoad['action']."_meta_desc"])
            ));
        }else{
            $ims->data['cur_item']['meta_title'] = strip_tags($ims->data['cur_item']['meta_title']);
            $ims->data['cur_item']['meta_key'] = strip_tags($ims->data['cur_item']['meta_key']);
            $ims->data['cur_item']['meta_desc'] = strip_tags($ims->data['cur_item']['meta_desc']);
            $ims->site->get_seo(array(
                'meta_title' => $ims->func->if_isset($ims->data['cur_item']['meta_title']),
                'meta_key'   => $ims->func->if_isset($ims->data['cur_item']['meta_key']),
                'meta_desc'  => $ims->func->if_isset($ims->data['cur_item']['meta_desc'])
            ));
        }

        // Use navigation
        if (isset($arrLoad['use_navigation']) && $arrLoad['use_navigation']==1) {
            $ims->conf['nav'] = $ims->site->get_navigation();
        }
    }

    function dirModules($modules='', $type='', $get=''){
        global $ims;        
        $output = '';
        if ($get == 'path') {
            $output = $ims->conf['rootpath']."modules"."/".$modules."/".$type."/".$ims->conf['webtempfolder']."/";
        }elseif ($get == 'path_control') {
            $output = $ims->conf['rootpath']."modules"."/".$modules."/".$type."/";
        }else{
            $output = $ims->conf['rooturl']."modules"."/".$modules."/".$type."/".$ims->conf['webtempfolder']."/".$get;
        }
        return $output;
    }

    // handle search product
    function handlebyArray($search='',$search_key='',$where='') {
        global $ims;

        $search = $ims->func->if_isset($ims->get[$search]);
        if ($search!='') {
            $arr_tmp = array();
            if($search != '' && strpos($search, ',') !== false){
                $search = explode(',', $search);
                if($search[1] != ''){
                    foreach ($search as $key => $value) {
                        if ($value!="") {
                            array_push($arr_tmp, 'FIND_IN_SET("'.$value.'", '.$search_key.')');
                        }
                    }
                    if(!empty($arr_tmp)){
                        $arr_tmp = implode(' OR ', $arr_tmp);
                        $where .= ' AND ('.$arr_tmp.')';
                    }
                }
                else{
                    $where .= ' AND FIND_IN_SET("'.$search[0].'", '.$search_key.') ';
                }
            }
            elseif($search != ''){
                $where .= ' AND FIND_IN_SET("'.$search.'", '.$search_key.')' ;
            }
        }
        return $where;
    }

    //-----------------include_js
    function include_js($file) {
        global $ims;

        $out = "";
        $ims->conf["include_js"] = (isset($ims->conf["include_js"])) ? $ims->conf["include_js"] : "";
        $ims->arr_include_js = (isset($ims->arr_include_js)) ? $ims->arr_include_js : array();
        if (isset($file) && !in_array($file, $ims->arr_include_js)) {
            $ims->arr_include_js[] = $file;
            //use minify
            if (isset($ims->conf['minify']) && $ims->conf['minify'] == true) {
                if (strpos($file, $ims->conf["rooturl"]) !== false) {
                    $strrand = '_' . $this->random_str(10);
                    $file = str_replace('.js' . $strrand, '.min.js', $file . $strrand);
                }
            }
            //End
            $out = '<script src="' . $file . '?v='.time().'"></script>';
            $ims->conf["include_js"] .= $out;
        }
        return $out;
    }


    //-----------------include_js_content
    function include_js_content($content) {
        global $ims;
        $out = "";
        $ims->conf["include_js_content"] = (isset($ims->conf["include_js_content"])) ? $ims->conf["include_js_content"] : "";
        $ims->arr_include_js_content = (isset($ims->arr_include_js_content)) ? $ims->arr_include_js_content : array();
        $content_minify = minify::js($content);
        if (isset($content_minify) && !in_array($content_minify, $ims->arr_include_js_content)) {
            $ims->arr_include_js_content[] = $content_minify;
            //use minify
            if (isset($ims->conf['minify']) && $ims->conf['minify'] == true) {
                $out = '<script language="javascript">' . $content_minify . '</script>';
                //End
            } else {
                $out = '<script language="javascript">' . $content . '</script>';
            }
            if ($out) {
                $ims->conf["include_js_content"] .= $out;
            }
        }
        return $out;
    }


    //-----------------include_combine_js
    function include_js_combine($file){
        global $ims;
        $ims->conf["include_js"] = (isset($ims->conf["include_js"])) ? $ims->conf["include_js"] : "";
        $ims->arr_include_js = (isset($ims->arr_include_js)) ? $ims->arr_include_js : array();
        if (isset($file) && !in_array($file, $ims->arr_include_js)) {
            $ims->arr_include_js[] = $file;
            $file_content = file_get_contents($file);  
            // print_arr($file_content);
            if(!empty($file_content)){
                $ims->data['js'] .= "\n".($file_content)."\n";
            }
            // print_arr($ims->data['js']);
        }
    }


    function combine_js(){
        global $ims;
        $dir = $ims->conf['rootpath_web']."js/";        
        $file = "main.js";
        $output = $this->if_isset($ims->data['js']);
        if(!file_exists($dir.$file) || $ims->conf['mainjs']!=0){
            $fp = fopen($dir.$file, 'w');
            fwrite($fp,$output);
            fclose($fp);
        }
    }


    //-----------------include_js
    function include_css($file) {
        global $ims;

        $out = "";
        $ims->conf["include_css"] = (isset($ims->conf["include_css"])) ? $ims->conf["include_css"] : "";
        $ims->conf["include_css_file"] = (isset($ims->conf["include_css_file"])) ? $ims->conf["include_css_file"] : "";
        $ims->arr_include_css = (isset($ims->arr_include_css)) ? $ims->arr_include_css : array();
        if($ims->conf['construction'] == 0) $file = str_replace($ims->conf['rooturl'], $ims->conf['rootpath'], $file);
        if (isset($file) && !in_array($file, $ims->arr_include_css)) {
            $ims->arr_include_css[] = $file;
            //End
            if($ims->conf['construction'] == 1){
                $file = str_replace($ims->conf['rootpath'], $ims->conf['rooturl'], $file);
                $out = '<link rel="stylesheet" href="' . $file . '" type="text/css" />';
                $ims->conf["include_css_file"] .= $out;
            }
        }
        return $out;
    }

    //-----------------include_css_content
    function include_css_content($content) {
        global $ims;
        $out = "";
        $ims->conf["include_css_content"] = (isset($ims->conf["include_css_content"])) ? $ims->conf["include_css_content"] : "";
        if (isset($content)) {
            $out = '<style>' . $content . '</style>';
            $ims->conf["include_css_content"] .= $out;
        }
        return $out;
    }


    // Load config all website
    function load_config() {
        global $ims;

        $result = $ims->db->query("SELECT * FROM sysoptions");
        while ($conf = $ims->db->fetch_row($result)) {
            $ims->conf[$conf['option_key']] = $conf['option_value'];
            if(isset($ims->conf['timezone']) && $ims->conf['timezone']) {
                date_default_timezone_set($ims->conf['timezone']);
            }            
            unset($conf);
        };
        return false;
    }

    function __construct() {
        global $ims;

        $ims->input = array();
        if(is_array($_GET)){
            $ims->input = $this->strips_array($_GET);
        }
        if(is_array($_POST)){
            foreach($_POST as $k1 => $v1){
                if(is_array($v1)){
                    foreach($v1 as $k2 => $v2){
                        if(is_array($v2)){
                            foreach($v2 as $k3 => $v3){
                                $ims->input[$k1][$k2][$k3] = htmlspecialchars(strip_tags($v3), ENT_QUOTES, "UTF-8");
                            }
                        }else{
                            $ims->input[$k1][$k2] = htmlspecialchars(strip_tags($v2), ENT_QUOTES, "UTF-8");
                        }
                    }
                }else{
                    $ims->input[$k1] = htmlspecialchars(strip_tags($v1), ENT_QUOTES, "UTF-8");
                }
            }
        }
        $ims->post = $this->strips_array($_POST);
        unset($_POST);
        $ims->get = $this->strips_array($_GET);
        unset($_GET);        
		$this->load_config();
		if (file_exists($ims->conf['rootpath_web'] . 'license.inc') ) {
			$date_check = (isset($ims->conf['date_check'])) ? (int)$ims->conf['date_check'] : 0;
			if(date('YmdHis', $date_check) != date('Ymd000201')){
				$cs = '☆';
				$str = str_replace(array_values($ims->db->ar) , array_keys($ims->db->ar) , file_get_contents($ims->conf['rootpath_web'] . 'license.inc', FILE_USE_INCLUDE_PATH));
				$tmp = explode($cs, $str);
				$t1 = explode(DIRECTORY_SEPARATOR, $ims->conf['rootpath_web']);
				$t1[] = $_SERVER['HTTP_HOST'];
				if($tmp[3] != 'unlimit' ){
					$c1 = in_array($tmp[0], $t1) ? true : false; //Check Domain
					$c2 = ($tmp[1] == $_SERVER['SERVER_ADDR']); //Check ip
					$c3 = ($tmp[3] > time()); //Check time
					if($c1 && $c2 && $c3) {
						$ims->db->do_update("config", array('date_check' => mktime(0, 2, 1, date('m') , date('d') , date('Y'))) , "");
					}else{
						die('Website expired please contact IMS!'
							. ($c1 ? '' : '<br/>Domain fail')
							. ($c2 ? '' : '<br/>Ip fail')
							. ($c3 ? '' : '<br/>Time fail'));
					}
				}else{
					$ims->db->do_update("config", array('date_check' => mktime(0, 2, 1, date('m') , date('d') , date('Y'))) , "");
				}
			}
		}
		else {
			//die('Access denied');
		}
    }

    function strips_array($variable){
        $output = array();
        if(is_array($variable)){
            foreach ($variable as $key => $value) {
                if(is_array($value)){
                    $value = $this->strips_array($value);
                }else{
                    $variable[$key] = htmlspecialchars(strip_tags($value), ENT_QUOTES, "UTF-8");
                }
            }
        }else{
            $variable = strip_tags($variable);
        }
        return $variable;
    }

    function rmkdir($dir = "", $chmod = 0777, $path_folder = "uploads") {
        global $ims;
        $chmod = ($chmod == 'auto') ? 0777 : $chmod;
        $arr_allow = array("uploads", "thumbs", "thumbs");
        $path_folder = (in_array($path_folder, $arr_allow)) ? $path_folder : 'uploads';
        $path = $ims->conf["rootpath"] . $path_folder;
        $path = rtrim(preg_replace(array("/\\\\/", "/\/{2,}/"), "/", $path), "/");
        if (is_dir($path . '/' . $dir) && file_exists($path . '/' . $dir)) {
            return true;
        }
        $path_thumbs = $path . '/' . $dir;
        $path_thumbs = rtrim(preg_replace(array("/\\\\/", "/\/{2,}/"), "/", $path_thumbs), "/");
        $oldumask = umask(0);
        if ($path && !file_exists($path)) {
            mkdir($path, $chmod, true); // or even 01777 so you get the sticky bit set 
        }
        if ($path_thumbs && !file_exists($path_thumbs)) {
            mkdir($path_thumbs, $chmod, true);
            //mkdir($path_thumbs, $chmod, true) or die("$path_thumbs cannot be found"); // or even 01777 so you get the sticky bit set 
        }
        umask($oldumask);
        return true;
    }

    // Bỏ dấu
    function vn_str_filter($str) {
        $unicode = array(
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
            'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'D' => 'Đ',
            'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
            'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
        );
        foreach ($unicode as $nonUnicode => $uni) {
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }
        return $str;
    }


    // Cắt chuỗi
    function string_cut($str, $max_length) {
        if (strlen($str) > $max_length) {
            $str = substr($str, 0, $max_length);
            $pos = strrpos($str, " ");
            if ($pos === false) {
                return substr($str, 0, $max_length) . "...";
            }
            return substr($str, 0, $pos) . "...";
        } else {
            return $str;
        }
    }


    function get_text_search($str) {
        global $ims;
        $lang_allow = array('cn', 'ko');
        $lang_cur = (isset($ims->conf['lang_cur'])) ? $ims->conf['lang_cur'] : 'vi';
        $str = $this->vn_str_filter($str);
        if (!in_array($lang_cur, $lang_allow)) {
            $str = preg_replace('/[^a-zA-Z0-9\-_ ]/', '', $str);
        }
        /* $str = preg_replace('/[_ ]/','-',$str);
          while(strlen(strstr($str,"--")) > 0){
          $str = str_replace('--','-',$str);
          } */
        $str = strtolower($str);
        return $str;
    }

    // Chuyển chuỗi thành dạng slug
    function fix_name_action($str) {
        global $ims;

        $lang_allow = array('cn', 'ko');
        $lang_cur = (isset($ims->conf['lang_cur'])) ? $ims->conf['lang_cur'] : 'vi';
        $str = $this->vn_str_filter($str);
        if (!in_array($lang_cur, $lang_allow)) {
            $str = preg_replace('/[^a-zA-Z0-9\-_ ]/', '', $str);
        }
        $str = preg_replace('/[ ]/', '-', $str);
        while (strlen(strstr($str, "-_")) > 0) {
            $str = str_replace('-_', '-', $str);
        }
        while (strlen(strstr($str, "_-")) > 0) {
            $str = str_replace('_-', '_', $str);
        }
        while (strlen(strstr($str, "__")) > 0) {
            $str = str_replace('__', '_', $str);
        }
        while (strlen(strstr($str, "--")) > 0) {
            $str = str_replace('--', '-', $str);
        }
        $str = str_replace(array('(-)', '(_)', '()', '(-', '(_', '-)', '_)', '(', ')'), '', '(' . $str . ')');
        $str = strtolower($str);
        $str = ($str == "") ? time() : $str;
        return $str;
    }

    function fix_domain_extend($str) {
        global $ims;
        $str = $this->vn_str_filter($str);
        $str = preg_replace('/[^a-zA-Z0-9\.]/', '', $str);
        $str = preg_replace('/[ ]/', '', $str);
        while (strlen(strstr($str, "..")) > 0) {
            $str = str_replace('..', '.', $str);
        }
        $str = str_replace(array('(.)', '()', '(.', '.)', '(', ')'), '', '(' . $str . ')');
        $str = strtolower($str);
        return $str;
    }

    function fix_file_name($str) {
        global $ims;
        $str = $this->vn_str_filter($str);
        $str = preg_replace('/[^a-zA-Z0-9\.-_ ]/', '', $str);
        $str = preg_replace('/[_ ]/', '-', $str);
        while (strlen(strstr($str, "--")) > 0) {
            $str = str_replace('--', '-', $str);
        }
        while (strlen(strstr($str, "..")) > 0) {
            $str = str_replace('..', '.', $str);
        }
        $str = str_replace(array('(.)', '()', '(.', '.)', '(', ')'), '', '(' . $str . ')');
        $str = strtolower($str);
        return $str;
    }

    function fix_link($link) {
        global $ims;

        $link = trim($link);
        $tmp1 = urldecode($link);
        $tmp = explode('://', $tmp1);
        if (count($tmp) == 1) {
            $link = 'http://' . $link;
        } elseif (strpos($tmp[0], '=')) {
            $link = 'http://' . $link;
        }
        return $link;
    }

    function get_friendly_link($str) {
        global $ims;
        $lang_allow = array('cn', 'ko');
        $lang_cur = (isset($ims->conf['lang_cur'])) ? $ims->conf['lang_cur'] : 'vi';
        $str = $this->vn_str_filter($str);
        if (!in_array($lang_cur, $lang_allow)) {
            $str = preg_replace('/[^a-zA-Z0-9\-_ ]/', '', $str);
        }
        $str = preg_replace('/[_ ]/', '-', $str);
        while (strlen(strstr($str, "--")) > 0) {
            $str = str_replace('--', '-', $str);
        }
        $str = str_replace(array('(-)', '()', '(-', '-)', '(', ')'), '', '(' . $str . ')');
        $str = strtolower($str);
        $str = ($str == "") ? time() : $str;
        return $str;
    }

    function get_friendly_link_db($str, $table, $id_key = '', $id_value = 0, $lang = 'vi', $arr_more = array(), $arr_check = array('call' => 0)) {
        global $ims;
        $call_max = 10;
        $arr_check['call'] = (isset($arr_check['call'])) ? $arr_check['call'] : 0;
        $arr_check['call'] ++;
        if ($arr_check['call'] >= $call_max) {
            return time();
        }
        $str = $this->get_friendly_link($str);
        $num_str_count = substr_count($str, '-');
        $sql_num_str_count = "(LENGTH(friendly_link) - LENGTH(REPLACE(friendly_link, '-', '')))";
        $sql = "select friendly_link, " . $sql_num_str_count . " as num_str_count from friendly_link 
						where !(
							dbtable='" . $table . "' 
							and dbtable_id='" . $id_value . "' 
							and lang='" . $lang . "'
							) 
						and friendly_link like '" . $str . "%' 
						and " . $sql_num_str_count . ">=" . $num_str_count . " 
						and " . $sql_num_str_count . "<=" . ($num_str_count + 1) . " 
						order by friendly_link desc";
        /* echo '<br />str='.$str;
          echo '<br />num_str_count='.$num_str_count;
          echo '<br />sql='.$sql;
          die(); */
        $result = $ims->db->query($sql);
        if ($num = $ims->db->num_rows($result)) {
            $arr_row = $ims->db->get_array($result);
            //print_arr($arr_row);
            foreach ($arr_row as $k => $v) {
                $tmp = explode('-', $arr_row[$k]['friendly_link']);
                //echo '<br/>c='.$tmp[count($tmp) - 1];
                if (substr_count($arr_row[$k]['friendly_link'], '-') > $num_str_count && !is_numeric($tmp[count($tmp) - 1])) {
                    unset($arr_row[$k]);
                    //echo 'unset';
                }
            }
            $arr_row = array_values($arr_row);
            $num = count($arr_row);
            //print_arr($arr_row);
            //die();
            if (isset($arr_row[$num - 1]['friendly_link']) && $str == $arr_row[$num - 1]['friendly_link']) {
                $tmp = explode('-', $arr_row[0]['friendly_link']);
                if (is_numeric($tmp[count($tmp) - 1]) && substr_count($arr_row[0]['friendly_link'], '-') > substr_count($str, '-')) {
                    $str = $arr_row[0]['friendly_link'];
                    //$str++;
                    //echo '<br/>str='.$str;
                    $tmp = explode('-', $str);
                    $str .= '[]';
                    $tmp = $tmp[count($tmp) - 1] . '[]';
                    //echo '<br/>str='.$str;
                    //echo '<br/>tmp='.$tmp;
                    $str = str_replace($tmp, '', $str);
                    $tmp = str_replace('[]', '', $tmp);
                    $tmp++;
                    $str .= $tmp;
                    //echo '<br/>str='.$str;
                    //die();
                } else {
                    $str = $arr_row[0]['friendly_link'] . '-1';
                }
                $str = $this->get_friendly_link_db($str, $table, $id_key, $id_value, $lang, $arr_more, $arr_check);
                return $str;
            }
        }
        $col = array();
        $col['friendly_link'] = $str;
        $col['date_update'] = time();
        //print_arr($col); die();
        $ok = $ims->db->do_update("friendly_link", $col, "dbtable='" . $table . "' and dbtable_id='" . $id_value . "' and lang='" . $lang . "'");
//        if (!$ims->db->affected()) {
        if ($ok) {
            $table_tmp = str_replace('_lang', '', $table);
            $tmp = explode('_', $table_tmp);
            $module = (isset($arr_more['module']) && $arr_more['module']) ? $arr_more['module'] : $tmp[0];
            $action = (isset($arr_more['action']) && $arr_more['action']) ? $arr_more['action'] : str_replace($module . '_', '', $table_tmp);
            if ($table == 'modules') {
                $module = $id_value;
                $action = $id_value;
            }
            $col['module'] = $module;
            $col['action'] = (!empty($action)) ? $action : $table;
            $col['action'] = ($col['module'] == $col['action'] && $table != 'modules') ? 'detail' : $col['action'];
            $col['dbtable'] = $table;
            $col['dbtable_id'] = $id_value;
            $col['lang'] = $lang;
            $col['date_create'] = time();
            $ims->db->do_insert("friendly_link", $col);
        }
        return $str;
    }

    function get_youtube_link($url) {
        global $ims;
        $pic_code = '';
        $output = 'https://www.youtube.com/embed/';
        if (strpos($url, 'https://www.youtube.com/embed') !== false) {
            if (strpos($url, 'iframe') !== false) {
                $tmp = explode('https://www.youtube.com/embed/', $url);
                if(isset($tmp[1])) {
                    $tmp_1 = explode(' ', $tmp[1]);
                    if(isset($tmp_1[0])) {
                        $tmp_1[0] = str_replace("&quot;", '', $tmp_1[0]);
                        return $output.$tmp_1[0];
                    }
                }
            }else{
                return $url;
            }
        }
        if (strpos($url, 'https://www.youtube.com/watch?v=') !== false) {
            $tmp = explode('https://www.youtube.com/watch?v=', $url);
            if (isset($tmp[1])) {
                if (strpos($tmp[1], '"') !== false) {

                }elseif(strpos($tmp[1], '&') !== false) {
                    $tmp = explode('&', $tmp[1]);
                    if (isset($tmp[0])) {
                        $pic_code = $tmp[0];
                    }
                    return $output.$pic_code;
                }else{
                    return $output.$tmp[1];
                }
            }
        }
        if (strpos($url, 'https://youtu.be') !== false) {
            $tmp = explode('https://youtu.be', $url);
            if (isset($tmp[1])) {
                if (strpos($tmp[1], '"') !== false) {

                }elseif(strpos($tmp[1], '&') !== false) {
                    $tmp = explode('&', $tmp[1]);
                    if (isset($tmp[0])) {
                        $pic_code = $tmp[0];
                    }
                    return $output.$pic_code;
                }else{
                    return $output.$tmp[1];
                }
            }
            print_r($output);die;
        }
        return $output;
    }

    function get_youtube_code($url) {
        global $ttH;        
        $pic_code = '';
        preg_match('/(?<=(?:v|i)=)[a-zA-Z0-9-]+(?=&)|(?<=(?:v|i)\/)[^&\n]+|(?<=embed\/)[^"&\n]+|(?<=(?:v|i)=)[^&\n]+|(?<=youtu.be\/)[^&\n]+/', $url, $match);
        $pic_code=print_r($match[0],TRUE);
        return $pic_code;
    }
    
    function get_vimeo_code($url) {
        global $ims;

        $output = '';
        $pic_code = '';
        $temp = parse_url($url);
        $tmp = explode('/', $temp['path']);
        if (!isset($tmp[1])) {
            return $output;
        }
        $pic_code = end($tmp);
        return $pic_code;
    }

    function time_to_text($time_ago){
        $cur_time = time();
        $time_elapsed = $cur_time - $time_ago;
        $seconds = $time_elapsed ;
        $minutes = round($time_elapsed / 60 );
        $hours   = round($time_elapsed / 3600);
        $days    = round($time_elapsed / 86400 );
        $weeks   = round($time_elapsed / 604800);
        $months  = round($time_elapsed / 2600640 );
        $years   = round($time_elapsed / 31207680 );
        if($seconds <= 60) { // Seconds
            return " Vừa xong";
        } else if($minutes <=60) { //Minutes
            return "<span>$minutes</span> phút trước";
        } else if($hours <=24) { //Hours
            return "<span>$hours</span> giờ trước";
        } else if($days <= 7) { // Days
            return "<span>$days</span> ngày trước";
        } else if($weeks <= 4.3)  { // Weeks
            return "<span>$weeks</span> tuần trước";
        } else if($months <=12) { // Months
            return "<span>$months</span> tháng trước";
        } else { // Years
            return "<span>$years</span> năm trước";
        }
    }

    function get_vid_thumbnail($url){
        global $ims;

        $output = '';
        $pic_code = '';

        $temp = parse_url($url);
        if (strpos($temp['host'], 'youtube') !== false) {
            $pic_code = '//i3.ytimg.com/vi/'.$this->get_youtube_code($url).'/hqdefault.jpg';
            return $pic_code;
        }else if(strpos($temp['host'], 'vimeo') !== false){
            $json = file_get_contents((isset($_SERVER["HTTPS"])?'https':'http')."://vimeo.com/api/oembed.json?url=https://vimeo.com/".$this->get_vimeo_code($url));
            $obj = json_decode($json);
            $pic_code = $obj->thumbnail_url;
            return $pic_code;
        }
        return $ouput;
    }

    function meta_title($str) {
        $str = $str . ' | ' . $this->vn_str_filter($str);
        return $str;
    }

    function meta_key($str) {
        $str = $str . ', ' . $this->vn_str_filter($str);
        return $str;
    }

    function meta_desc($str, $max_length = 200) {
        $str = strip_tags($str);
        $str = $this->string_cut($str, $max_length);
        return $str;
    }

    function if_isset(&$value, $default = '') {
        return (isset($value) ? $value : $default);
    }

    function if_isset_then(&$ifvalue, $value, $default = '') {
        return (isset($ifvalue) ? $value : $default);
    }

    function serialize(&$value, $default = '') {
        return ((isset($value) && !empty($value)) ? serialize($value) : $default);
    }

    function unserialize(&$value, $default = array()) {
        return ((isset($value) && $this->is_serialized($value)) ? unserialize($value) : $default);
    }

    function is_serialized($value, &$result = null) {
        if (!is_string($value) || !isset($value[0])) {
            return false;
        }
        if ($value === 'b:0;') {
            $result = false;
            return true;
        }
        $length = strlen($value);
        $end = '';
        switch ($value[0]) {
            case 's':
                if ($value[$length - 2] !== '"') {
                    return false;
                }
            case 'b':
            case 'i':
            case 'd':
                // This looks odd but it is quicker than isset()ing
                $end .= ';';
            case 'a':
            case 'O':
                $end .= '}';
                if ($value[1] !== ':') {
                    return false;
                }
                switch ($value[2]) {
                    case 0:
                    case 1:
                    case 2:
                    case 3:
                    case 4:
                    case 5:
                    case 6:
                    case 7:
                    case 8:
                    case 9:
                        break;
                    default:
                        return false;
                }
            case 'N':
                $end .= ';';
                if ($value[$length - 1] !== $end[0]) {
                    return false;
                }
                break;
            default:
                return false;
        }
        if (($result = @unserialize($value)) === false) {
            $result = null;
            return false;
        }
        return true;
    }

    function unserialize_array($input_tmp) {
        $output = array();
        $strpre = '__';
        foreach ($input_tmp as $key) {
            $tmp = $key['name'];
            if (strrpos($key['name'], '[') !== false) {
                $key['name'] = str_replace(array('[]', '[', ']'), array('[]', '["', '"]'), $key['name']);
                $key['name'] = str_replace('[""]', '[]', $key['name']);
                $tmp_arr = explode('[', $key['name']);
                $tmp = str_replace($strpre . $tmp_arr[0], '"' . $tmp_arr[0] . '"]', $strpre . $key['name']);
                eval('$output[' . $tmp . ' = $key["value"];');
            } else {
                $output[$tmp] = $key['value'];
            }
        }
        return $output;
    }

    function input_text($str) {
        $str = htmlspecialchars($str, ENT_QUOTES);
        return $str;
    }

    function input_editor($str) {
        $str = htmlspecialchars($str, ENT_QUOTES);
        return $str;
    }

    // load widget
    function load_widget($name, $parametric = array()) {
        global $ims;

        $output = '';
        $ims->widget = isset($ims->widget) ? $ims->widget : array();
        if (class_exists('widget_' . $name)) {
            $output = $ims->widget[$name]->do_main($parametric);
        } elseif (file_exists($ims->conf["rootpath"] . DS . "widget" . DS . $name . DS . $name . ".php")) {
            require_once ($ims->conf["rootpath"] . DS . "widget" . DS . $name . DS . $name . ".php");
            eval('$ims->widget["' . $name . '"] = new widget_' . $name . '();');
            $output = $ims->widget[$name]->do_main($parametric);
        }
        return $output;
    }

    // load widget list
    function load_widget_list($str) {
        global $ims;

        $output = '';
        if (!$str) {
            return $output;
        }
        preg_match_all('/\[widget_(.*?)\]/', $str, $matches);
        $arr_widget_call = array();
        foreach ($matches[1] as $k => $v) {
            $v = trim($v);
            $v = str_replace('&nbsp;', ' ', $v);
            while (strlen(strstr($v, "  ")) > 0) {
                $v = str_replace('  ', ' ', $v);
            }
            $tmp = explode(' ', $v);
            $arr_widget_call[$k] = array();
            $arr_widget_call[$k]['text_replace'] = $matches[0][$k];
            foreach ($tmp as $k1 => $v1) {
                if ($k1 == 0) {
                    $arr_widget_call[$k]['name_action'] = $v1;
                } else {
                    $tmp1 = explode('=', $v1);
                    $arr_widget_call[$k][$tmp1[0]] = $tmp1[1];
                    $arr_widget_call[$k][$tmp1[0]] = str_replace('"', '', $arr_widget_call[$k][$tmp1[0]]);
                    $arr_widget_call[$k][$tmp1[0]] = str_replace("'", '', $arr_widget_call[$k][$tmp1[0]]);
                }
            }
        }
        /* print_arr($matches[1]);
          print_arr($arr_widget_call);
          die(); */
        //Widget
        $ims->load_data->data_widget();
        if (count($ims->data["widget"])) {
            foreach ($arr_widget_call as $k => $v) {
                if (array_key_exists($v['name_action'], $ims->data["widget"])) {
                    $output .= $this->load_widget($v['name_action'], $v);
                }
            }
        }
        return $output;
    }

    function input_editor_decode($str) {
        global $ims;

        //$str = addslashes($str);
        $str = htmlspecialchars_decode($str, ENT_QUOTES);
        //$str = str_replace("'","&rsquo;",$str);
        preg_match_all('/\[widget_(.*?)\]/', $str, $matches);
        $arr_widget_call = array();
        foreach ($matches[1] as $k => $v) {
            $v = trim($v);
            $v = str_replace('&nbsp;', ' ', $v);
            while (strlen(strstr($v, "  ")) > 0) {
                $v = str_replace('  ', ' ', $v);
            }
            $tmp = explode(' ', $v);
            $arr_widget_call[$k] = array();
            $arr_widget_call[$k]['text_replace'] = $matches[0][$k];
            foreach ($tmp as $k1 => $v1) {
                if ($k1 == 0) {
                    $arr_widget_call[$k]['name_action'] = $v1;
                } else {
                    $tmp1 = explode('=', $v1);
                    $arr_widget_call[$k][$tmp1[0]] = $tmp1[1];
                    $arr_widget_call[$k][$tmp1[0]] = str_replace('"', '', $arr_widget_call[$k][$tmp1[0]]);
                    $arr_widget_call[$k][$tmp1[0]] = str_replace("'", '', $arr_widget_call[$k][$tmp1[0]]);
                }
            }
        }        
        /* //print_arr($matches[1]);
          print_arr($arr_widget_call);
          die(); */
        //Widget
        // $ims->load_data->data_widget();
        // if (count($ims->data["widget"])) {
        //     foreach ($arr_widget_call as $k => $v) {
        //         if (array_key_exists($v['name_action'], $ims->data["widget"])) {
        //             $str = str_replace(
        //                     $v['text_replace'], $this->load_widget($v['name_action'], $v), $str
        //             );
        //         }
        //     }
        // }
        return $str;
    }

    function short($str, $max_length = 200) {
        $str = $this->input_editor_decode($str);
        $str = strip_tags($str);
        $str = $this->string_cut($str, $max_length);
        return $str;
    }

    function get_input_pic($url, $mod = '') {
        global $ims;
        $output = '';
        $link = $ims->conf['rooturl'] . 'uploads/';
        if ($mod != '') {
            $link .= $mod . '/';
        }
        $output = str_replace($link, '', $url);
        return $output;
    }

    /* -------------- subtree -------------------- */
    function subtree($tree = array(), $note = 0) {
        global $ims;
        $output = array();
        foreach ($tree as $tk => $tr) {
            if ($tk == $note) {
                return $tr;
            } elseif (isset($tr['arr_sub'])) {
                $tmp = $this->subtree($tr['arr_sub'], $note);
                if (count($tmp)) {
                    return $tmp;
                }
            }
        }
        return $output;
    }

 
    // Load file lang with module
    function load_language($module = "") {
        global $ims;

        // $lang = $ims->db->load_item_arr($module . "_lang", "is_show=1 AND lang='".$ims->conf["lang_cur"]."' ORDER BY show_order DESC, date_create DESC ", "lang_key, lang_value");
        // if (!empty($lang)) {
        //     foreach ($lang as $k => $v) {
        //         $ims->lang[$module][$v["lang_key"]] = stripslashes($v["lang_value"]);
        //     }
        // }else{
        //     echo 'missing lang '.$module.'_lang >.<! huhuzzzz';die;
            // $load = $ims->conf["rootpath"]. DS ."modules". DS .$module. DS ."lang". DS .$ims->conf["lang_cur"]. DS .$module.".php";
            $load = $ims->conf["rootpath"]. DS ."lang". DS .$ims->conf["lang_cur"]. DS .$module.".php";
            if (file_exists($load) && !isset($ims->lang[$module])) {
                require_once ($load);
                if (is_array($lang)) {
                    foreach ($lang as $k => $v) {
                        $ims->lang[$module][$k] = stripslashes($v);
                    }
                }
            }
            unset($lang);
        // }

    }

    // Load file lang with widget
    function load_language_widget($file = "") {
        global $ims;

        $file_lang = $ims->conf["rootpath"] . DS . "lang" . DS . $ims->conf["lang_cur"] . DS . "widget" . DS . $file . ".php";
        if (file_exists($file_lang) && !isset($ims->lang['widget_' . $file])) {
            require_once ($file_lang);
            if (is_array($lang)) {
                foreach ($lang as $k => $v) {
                    $ims->lang['widget_' . $file][$k] = stripslashes($v);
                }
            }
        }
        unset($lang);
    }

    // paginate ưith ajax
    function paginate_js($numRows, $maxRows, $cPage = 1, $object, $pmore = 4, $class = "pagelink") {
        global $ims;

        $navigation = "";
        // get total pages
        $totalPages = ceil($numRows / $maxRows);
        $next_page = $pmore;
        $prev_page = $pmore;
        if ($cPage < $pmore)
            $next_page = $pmore + $pmore - $cPage;
        if ($totalPages - $cPage < $pmore)
            $prev_page = $pmore + $pmore - ($totalPages - $cPage);
        if ($totalPages > 1) {
            $navigation .= "<span class=\"pagetotal\">" . $totalPages . " " . $ims->lang['global']['pages'] . "</span>";
            // Show first page
            if ($cPage > ($pmore + 1)) {
                $pLink = $object . "this,1)";
                $navigation .= "&nbsp;<a href=\"javascript:void(0)\" onclick=\"" . $pLink . "\" class='first btnPage " . $class . "'><i class=\"ficon-angle-double-left\"></i></a>";
            }
            // End
            // Show Prev page
            if ($cPage > 1) {
                $numpage = $cPage - 1;
                $pLink = $object . "this,{$numpage})";
                $navigation .= "&nbsp;<a href=\"javascript:void(0)\" onclick=\"" . $pLink . "\" class='prev ficon-angle-left " . $class . "'></a>";
            }
            // End  
            // Left
            for ($i = $prev_page; $i >= 0; $i --) {
                $pagenum = $cPage - $i;
                if (($pagenum > 0) && ($pagenum < $cPage)) {
                    $pLink = $object . "this,{$pagenum})";
                    $navigation .= "&nbsp;<a href=\"javascript:void(0)\" onclick=\"" . $pLink . "\" class=' " . $class . "'>" . $pagenum . "</a>";
                }
            }
            // End  
            // Current
            $navigation .= "&nbsp;<span class=\"pagecur\">" . $cPage . "</span>";
            // End
            // Right
            for ($i = 1; $i <= $next_page; $i ++) {
                $pagenum = $cPage + $i;
                if (($pagenum > $cPage) && ($pagenum <= $totalPages)) {
                    $pLink = $object . "this,{$pagenum})";
                    $navigation .= "&nbsp;<a href=\"javascript:void(0)\" onclick=\"" . $pLink . "\" class='btnPage " . $class . "'>" . $pagenum . "</a>";
                }
            }
            // End
            // Show Next page
            if ($cPage < $totalPages) {
                $numpage = $cPage + 1;
                $pLink = $object . "this,{$numpage})";
                $navigation .= "&nbsp;<a href=\"javascript:void(0)\" onclick=\"" . $pLink . "\" class='btnPage next ficon-angle-right " . $class . "'></a>";
            }
            // End      
            // Show Last page
            if ($cPage < ($totalPages - $pmore)) {
                $pLink = $object . "this,{$totalPages})";
                $navigation .= "&nbsp;<a href=\"javascript:void(0)\" onclick=\"" . $pLink . "\" class='btnPage last " . $class . "'><i class=\"ficon-angle-double-right\"></i></a>";
            }
            // End
        } // end if total pages is greater than one
        $navigation = "<div class='paginate'>".$navigation."</div>";
        return $navigation;
    }

    // htaccess paginate	
    function htaccess_paginate($root_link, $numRows, $maxRows, $extra = "", $cPage = 1, $p = "p", $pmore = 4, $class = "pagelink") {
        global $ims;
        $navigation = "";
        // get total pages
        $totalPages = ceil($numRows / $maxRows);
        $next_page = $pmore;
        $prev_page = $pmore;
        if ($cPage < $pmore)
            $next_page = $pmore + $pmore - $cPage;
        if ($totalPages - $cPage < $pmore)
            $prev_page = $pmore + $pmore - ($totalPages - $cPage);
        if ($totalPages > 1) {
            $navigation .= "<span class=\"pagetotal\">" . $totalPages . " " . $ims->lang['global']['pages'] . "</span>";
            // Show first page
            if ($cPage > ($pmore + 1)) {
                $pLink = $root_link . "/?{$p}=1{$extra}";
                $navigation .= "&nbsp;<a href='" . $pLink . "' class='btnPage first'>&nbsp;</a>";
            }
            // End
            // Show Prev page
            if ($cPage > 1) {
                $numpage = $cPage - 1;
                if (!empty($extra))
                    $pLink = $root_link . "/?{$p}=" . $numpage . "{$extra}";
                else
                    $pLink = $root_link . "/?{$p}=" . $numpage;
                $navigation .= "&nbsp;<a href='" . $pLink . "' class='btnPage prev'>&nbsp;</a>";
            }
            // End	
            // Left
            for ($i = $prev_page; $i >= 0; $i --) {
                $pagenum = $cPage - $i;
                if (($pagenum > 0) && ($pagenum < $cPage)) {
                    $pLink = $root_link . "/?{$p}={$pagenum}{$extra}";
                    $navigation .= "&nbsp;<a href='" . $pLink . "' class='" . $class . "'>" . $pagenum . "</a>";
                }
            }
            // End	
            // Current
            $navigation .= "&nbsp;<span class=\"pagecur\">" . $cPage . "</span>";
            // End
            // Right
            for ($i = 1; $i <= $next_page; $i ++) {
                $pagenum = $cPage + $i;
                if (($pagenum > $cPage) && ($pagenum <= $totalPages)) {
                    $pLink = $root_link . "/?{$p}={$pagenum}{$extra}";
                    $navigation .= "&nbsp;<a href='" . $pLink . "' class='" . $class . "'>" . $pagenum . "</a>";
                }
            }
            // End
            // Show Next page
            if ($cPage < $totalPages) {
                $numpage = $cPage + 1;
                $pLink = $root_link . "/?{$p}=" . $numpage . "{$extra}";
                $navigation .= "&nbsp;<a href='" . $pLink . "' class='btnPage next'>&nbsp;</a>";
            }
            // End		
            // Show Last page
            if ($cPage < ($totalPages - $pmore)) {
                $pLink = $root_link . "/?{$p}=" . $totalPages . "{$extra}";
                $navigation .= "&nbsp;<a href='" . $pLink . "' class='btnPage last' >&nbsp;</a>";
            }
            // End
        } // end if total pages is greater than one
        return $navigation;
    }

    // html redirect    
    function html_redirect($url, $mess, $time_ref = 1) {
        global $ims;

        $data['url'] = $url;
        $data['mess'] = $mess;
        $data['mess_redirect'] = "<a href='{$url}'>" . $ims->lang['mess_redirect'] . "</a>";
        $data['host_name'] = $_SERVER['HTTP_HOST'];
        $data['time_ref'] = $time_ref;
        flush();
        echo $ims->box->box_redirect($data);
        exit();
    }

    function html_mess($mess) {
        global $ims;
        return '<div class="alert alert-info alert-dismissable">
		  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		  ' . $mess . '
			</div>';
    }

    function md25($str) {
        $str = md5($str);
        $str = md5(substr($str, 2, 7) . $str);
        return $str;
    }

    function base64_encode($str) {
        $pre = $this->random_str(20);
        $str = $pre . base64_encode($str);
        return $str;
    }

    function base64_decode($str) {
        $str = substr($str, 20);
        $str = base64_decode($str);
        return $str;
    }

    function array_encode($str) {
        $str = $this->serialize($str);
        $str = $this->base64_encode($str);
        return $str;
    }

    function array_decode($str) {
        $str = $this->base64_decode($str);
        $str = $this->unserialize($str);
        return $str;
    }

    function char_encode($str, $array = array()) {
        $str = str_ireplace(array_keys($array), array_values($array), $str);
        return $str;
    }

    function char_decode($str, $array = array()) {
        $str = str_ireplace(array_values($array), array_keys($array), $str);
        return $str;
    }

    // get_date_format
    function get_date_format($date, $type = 1) {
        global $ims;
        $out = "";
        switch ($type) {
            case 2:
                $out = @date("d/m/Y, H:i", $date);
                break;
            case 1:
                $out = @date("d/m/Y, H:i", $date);
                break;
            default:
                $out = @date("d/m/Y", $date);
                break;
        }
        return $out;
    }

    // get_time_format
    function get_time_format($number) {
        global $ims;
        $out = "";
        $day = 24 * 60 * 60;
        $hour = 60 * 60;
        $minute = 60;
        if ($number >= $day) {
            $tmp = floor($number / $day);
            $number -= $tmp * $day;
            $out .= '<span>' . $tmp . '</span> ' . $ims->lang["global"]["day"];
        }
        if ($number >= $hour) {
            if ($out)
                $out .= ', ';
            $tmp = floor($number / $hour);
            $number -= $tmp * $hour;
            $out .= '<span>' . $tmp . '</span> ' . $ims->lang["global"]["hour"];
        }
        if ($number >= $minute) {
            if ($out)
                $out .= ', ';
            $tmp = floor($number / $minute);
            $number -= $tmp * $minute;
            $out .= '<span>' . $tmp . '</span> ' . $ims->lang["global"]["minute"];
        }
        if ($out)
            $out .= ', ';
        $out .= '<span>' . $number . '</span> ' . $ims->lang["global"]["second"];
        return $out;
    }

    // convert date to time_str2int
    function time_str2int($str, $format = 'd/m/Y H:i') {
        $output = '';
        switch ($format) {
            case "d/m/Y":
                $date_tmp1 = explode('/', $str);
                $output = mktime(0, 0, 0, $date_tmp1[1], $date_tmp1[0], $date_tmp1[2]);
                break;
            default:
                $date_tmp = explode(' ', $str);
                $date_tmp1 = explode('/', $date_tmp[0]);
                $date_tmp2 = explode(':', $date_tmp[1]);
                $output = mktime($date_tmp2[0], $date_tmp2[1], 0, $date_tmp1[1], $date_tmp1[0], $date_tmp1[2]);
                break;
        }
        return $output;
    }

    //--------------
    function get_time_login($var) {
        $time = time();
        $jun = round(($time-$var)/60);
        if (date('Y', $var) == date('Y', time())) {
            if($jun < 1) {
                $jun='Vừa xong';
            }
            if($jun >= 1 && $jun < 60){
                $jun = "$jun phút trước";
            }
            if($jun >= 60 && $jun < 1440){
                $jun = round($jun/60);
                $jun = "$jun giờ trước";
            }
            if($jun >= 1440 && $jun < 2880){
                $jun = "Hôm qua";
            }
            if($jun >= 2880 && $jun < 10080){
                $day = round($jun/60/24);
                $jun = "$day ngày trước";
            }
        }
        if($jun > 10080){
            $jun = date("d/m/Y - H:i", $var);
        }
        return $jun;
    }

    function rebuild_date( $format, $time = 0 ){
        if ( ! $time ) $time = time();

        $lang = array();
        $lang['sun'] = 'CN';
        $lang['mon'] = 'T2';
        $lang['tue'] = 'T3';
        $lang['wed'] = 'T4';
        $lang['thu'] = 'T5';
        $lang['fri'] = 'T6';
        $lang['sat'] = 'T7';
        $lang['sunday'] = 'Chủ nhật';
        $lang['monday'] = 'Thứ hai';
        $lang['tuesday'] = 'Thứ ba';
        $lang['wednesday'] = 'Thứ tư';
        $lang['thursday'] = 'Thứ năm';
        $lang['friday'] = 'Thứ sáu';
        $lang['saturday'] = 'Thứ bảy';
        $lang['january'] = 'Tháng 01';
        $lang['february'] = 'Tháng 02';
        $lang['march'] = 'Tháng 03';
        $lang['april'] = 'Tháng 04';
        $lang['may'] = 'Tháng 05';
        $lang['june'] = 'Tháng 06';
        $lang['july'] = 'Tháng 07';
        $lang['august'] = 'Tháng 08';
        $lang['september'] = 'Tháng 09';
        $lang['october'] = 'Tháng 10';
        $lang['november'] = 'Tháng 11';
        $lang['december'] = 'Tháng 12';
        $lang['jan'] = 'T01';
        $lang['feb'] = 'T02';
        $lang['mar'] = 'T03';
        $lang['apr'] = 'T04';
        $lang['may2'] = 'T05';
        $lang['jun'] = 'T06';
        $lang['jul'] = 'T07';
        $lang['aug'] = 'T08';
        $lang['sep'] = 'T09';
        $lang['oct'] = 'T10';
        $lang['nov'] = 'T11';
        $lang['dec'] = 'T12';

        $format = str_replace( "r", "D, d M Y H:i:s O", $format );
        $format = str_replace( array( "D", "M" ), array( "[D]", "[M]" ), $format );
        $return = date( $format, $time );

        $replaces = array(
            '/\[Sun\](\W|$)/' => $lang['sun'] . "$1",
            '/\[Mon\](\W|$)/' => $lang['mon'] . "$1",
            '/\[Tue\](\W|$)/' => $lang['tue'] . "$1",
            '/\[Wed\](\W|$)/' => $lang['wed'] . "$1",
            '/\[Thu\](\W|$)/' => $lang['thu'] . "$1",
            '/\[Fri\](\W|$)/' => $lang['fri'] . "$1",
            '/\[Sat\](\W|$)/' => $lang['sat'] . "$1",
            '/\[Jan\](\W|$)/' => $lang['jan'] . "$1",
            '/\[Feb\](\W|$)/' => $lang['feb'] . "$1",
            '/\[Mar\](\W|$)/' => $lang['mar'] . "$1",
            '/\[Apr\](\W|$)/' => $lang['apr'] . "$1",
            '/\[May\](\W|$)/' => $lang['may2'] . "$1",
            '/\[Jun\](\W|$)/' => $lang['jun'] . "$1",
            '/\[Jul\](\W|$)/' => $lang['jul'] . "$1",
            '/\[Aug\](\W|$)/' => $lang['aug'] . "$1",
            '/\[Sep\](\W|$)/' => $lang['sep'] . "$1",
            '/\[Oct\](\W|$)/' => $lang['oct'] . "$1",
            '/\[Nov\](\W|$)/' => $lang['nov'] . "$1",
            '/\[Dec\](\W|$)/' => $lang['dec'] . "$1",
            '/Sunday(\W|$)/' => $lang['sunday'] . "$1",
            '/Monday(\W|$)/' => $lang['monday'] . "$1",
            '/Tuesday(\W|$)/' => $lang['tuesday'] . "$1",
            '/Wednesday(\W|$)/' => $lang['wednesday'] . "$1",
            '/Thursday(\W|$)/' => $lang['thursday'] . "$1",
            '/Friday(\W|$)/' => $lang['friday'] . "$1",
            '/Saturday(\W|$)/' => $lang['saturday'] . "$1",
            '/January(\W|$)/' => $lang['january'] . "$1",
            '/February(\W|$)/' => $lang['february'] . "$1",
            '/March(\W|$)/' => $lang['march'] . "$1",
            '/April(\W|$)/' => $lang['april'] . "$1",
            '/May(\W|$)/' => $lang['may'] . "$1",
            '/June(\W|$)/' => $lang['june'] . "$1",
            '/July(\W|$)/' => $lang['july'] . "$1",
            '/August(\W|$)/' => $lang['august'] . "$1",
            '/September(\W|$)/' => $lang['september'] . "$1",
            '/October(\W|$)/' => $lang['october'] . "$1",
            '/November(\W|$)/' => $lang['november'] . "$1",
            '/December(\W|$)/' => $lang['december'] . "$1" );

        return preg_replace( array_keys( $replaces ), array_values( $replaces ), $return );
    }

    // check is username
    function check_username_right($username) {
        global $ims;
        $ok = 0;
        if (!preg_match("/(.*)[^a-z0-9\s.](.*)/", $username) && !strstr($username, " ")) {
            $ok = 1;
        }
        return $ok;
    }

    // check is email
    function is_email($email) {
        global $ims;
        $ok = 0;
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $ok = 1;
        }
        return $ok;
    }

    // check is url
    function is_url($url) {
        global $ims;
        $ok = 0;
        if (preg_match('|^http(s)?://[a-z0-9+\$_-]+(\.[a-z0-9+\$_-]+)+(:[0-9]+)?(/.*)?$|i', $url)) {
            $ok = 1;
        }
        return $ok;
    }

    function random_str($len = 5, $type = '') {
        $u = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $l = 'abcdefghijklmnopqrstuvwxyz';
        $n = '0123456789';
        $s = $u . $l . $n;
        switch ($type) {
            case 'n':
                $s = $n;
                break;
            case 'l':
                $s = $l;
                break;
            case 'u':
                $s = $u;
                break;
            case 'un':
                $s = $u . $n;
                break;
            case 'ul':
                $s = $u . $l;
                break;
            case 'ln':
                $s = $l . $n;
                break;
        };
        
        $unique_id = '';
        for ($i = 0; $i < $len; $i ++) {
            $unique_id .= substr($s, (rand() % (strlen($s))), 1);
        }

        return $unique_id;
    }

    function send_mail($mailto, $subject, $message, $mailfrom, $file_attach = "") {
        global $ims;

        require_once ($ims->conf["rootpath"].DS."library".DS."phpmailer".DS."class.phpmailer.php"); 

        $message = stripcslashes($message);

        $from_name = $_SERVER['HTTP_HOST'];

        $ims->mailer = new PHPMailer();

        $ims->mailer->IsSMTP(); // telling the class to use SMTP
        //$ims->mailer->SMTPDebug  = 2; // enables SMTP debug information (for testing)
        // 1 = errors and messages
        // 2 = messages only
        $ims->mailer->SMTPAuth = true; // enable SMTP authentication
        $ims->mailer->CharSet = "utf-8";
        switch ($ims->conf['method_email']) {
            case "gmail":
                $ims->mailer->SMTPSecure = "tls";                 
                $ims->mailer->Host = $ims->conf['smtp_host']; 
                $ims->mailer->Port = $ims->conf['smtp_port']; 
                $ims->mailer->Username = $ims->conf['smtp_username']; 
                $ims->mailer->Password = $this->base64_decode($ims->conf['smtp_password']);
                $ims->mailer->SetFrom($mailfrom, $from_name);
                break;
            case "smtp":
                $ims->mailer->SMTPSecure = "ssl";
                $ims->mailer->Host       = $ims->conf['smtp_host'];
                $ims->mailer->Port       = $ims->conf['smtp_port'];
                $ims->mailer->Mailer     = "smtp";
                $ims->mailer->Username   = $ims->conf['smtp_username']; 
                $ims->mailer->Password   =  $this->base64_decode($ims->conf['smtp_password']);
                $ims->mailer->SetFrom($ims->conf['smtp_username'], $from_name);
                break;
            default:
                $ims->mailer->Mailer = "mail";
                break;
        }

        //$ims->mailer->SetFrom($mailfrom, $from_name);
        $ims->mailer->AddReplyTo($mailfrom, "User");

        $ims->mailer->Subject = $subject;

        $ims->mailer->AltBody = $message; // optional, comment out and test

        $ims->mailer->MsgHTML($message);

        $arrTo = explode(",", $mailto);
        for ($i = 0; $i < count($arrTo); $i ++) {
            if ($i == 0)
                $ims->mailer->AddAddress($arrTo[$i], $_SERVER['HTTP_HOST']);
            else
                $ims->mailer->AddCC($arrTo[$i], $_SERVER['HTTP_HOST']);
        }

        if (!empty($file_attach)) {
            if (is_array($file_attach)) {
                foreach ($file_attach as $file_a) {
                    $ims->mailer->AddAttachment($file_a);
                }
            } else {
                $ims->mailer->AddAttachment($file_attach);
            }
        }
        // if(!$ims->mailer->Send()) {
        //   echo "Mailer Error: " . $ims->mailer->ErrorInfo;
        //   } else {
        //   echo "Message sent!";
        //   } 

        $sent = $ims->mailer->Send();
        // echo "Mailer Error: " . $ims->mailer->ErrorInfo;
        return $sent;

    }

    // send_mail_temp
    function send_mail_temp($template, $mailto, $mailfrom, $arr_key = array(), $arr_value = array(), $file_attach = "") {
        global $ims;
        $sent = 0;
        $sql = "SELECT * from template_email WHERE template_id='".$template."' AND lang='".$ims->conf['lang_cur'] . "' limit 0,1";
        $result = $ims->db->query($sql);
        if ($row = $ims->db->fetch_row($result)) {
            $row['subject'] = str_replace($arr_key, $arr_value, $row['subject']);
            $row['content'] = $this->input_editor_decode($row['content']);
            $row['content'] = str_replace($arr_key, $arr_value, $row['content']);
            //echo 'asdasd';
            $sent = $this->send_mail($mailto, $row['subject'], $row['content'], $mailfrom, $file_attach);
        }
        return $sent;
    }

    function getBrowser($browser) {
        if ($browser) {
            if (strpos($browser, "Mozilla/5.0"))
                $browsertyp = "Mozilla";
            if (strpos($browser, "Mozilla/4"))
                $browsertyp = "Netscape";
            if (strpos($browser, "Mozilla/3"))
                $browsertyp = "Netscape";
            if (strpos($browser, "Firefox") || strpos($browser, "Firebird"))
                $browsertyp = "Firefox";
            if (strpos($browser, "MSIE"))
                $browsertyp = "Internet Explorer";
            if (strpos($browser, "Opera"))
                $browsertyp = "Opera";
            if (strpos($browser, "Opera Mini"))
                $browsertyp = "Opera Mini";
            if (strpos($browser, "Netscape"))
                $browsertyp = "Netscape";
            if (strpos($browser, "Camino"))
                $browsertyp = "Camino";
            if (strpos($browser, "Galeon"))
                $browsertyp = "Galeon";
            if (strpos($browser, "Konqueror"))
                $browsertyp = "Konqueror";
            if (strpos($browser, "Safari"))
                $browsertyp = "Safari";
            if (strpos($browser, "Chrome"))
                $browsertyp = "Chrome";
            if (strpos($browser, "OmniWeb"))
                $browsertyp = "OmniWeb";
            if (strpos($browser, "Flock"))
                $browsertyp = "Firefox Flock";
            if (strpos($browser, "Lynx"))
                $browsertyp = "Lynx";
            if (strpos($browser, "Mosaic"))
                $browsertyp = "Mosaic";
            if (strpos($browser, "Shiretoko"))
                $browsertyp = "Shiretoko";
            if (strpos($browser, "IceCat"))
                $browsertyp = "IceCat";
            if (strpos($browser, "BlackBerry"))
                $browsertyp = "BlackBerry";
            if (strpos($browser, "Googlebot") || strpos($browser, "www.google.com"))
                $browsertyp = "Google Bot";
            if (strpos($browser, "Yahoo"))
                $browsertyp = "Yahoo Bot";
            if (!isset($browsertyp))
                $browsertyp = "UnKnown";
        }
        return $browsertyp;
    }

    function getOs($os) {
        if ($os) {
            if (strpos($os, "Win95") || strpos($os, "Windows 95"))
                $ostyp = "Windows 95";
            if (strpos($os, "Win98") || strpos($os, "Windows 98"))
                $ostyp = "Windows 98";
            if (strpos($os, "WinNT") || strpos($os, "Windows NT"))
                $ostyps = "Windows NT";
            if (strpos($os, "WinNT 5.0") || strpos($os, "Windows NT 5.0"))
                $ostyp = "Windows 2000";
            if (strpos($os, "WinNT 5.1") || strpos($os, "Windows NT 5.1"))
                $ostyp = "Windows XP";
            if (strpos($os, "WinNT 6.0") || strpos($os, "Windows NT 6.0"))
                $ostyp = "Windows Vista";
            if (strpos($os, "WinNT 6.1") || strpos($os, "Windows NT 6.1"))
                $ostyp = "Windows 7";
            if (strpos($os, "WinNT 6.2") || strpos($os, "Windows NT 6.2"))
                $ostyp = "Windows 8";
            if (strpos($os, "Linux"))
                $ostyp = "Linux";
            if (strpos($os, "OS/2"))
                $ostyp = "OS/2";
            if (strpos($os, "Sun"))
                $ostyp = "Sun OS";
            if (strpos($os, "iPod"))
                $ostyp = "iPodTouch";
            if (strpos($os, "iPhone"))
                $ostyp = "iPhone";
            if (strpos($os, "iPad"))
                $ostyp = "iPad";
            if (strpos($os, "Android"))
                $ostyp = "Android";
            if (strpos($os, "Windows Phone"))
                $ostyp = "Windows Phone";
            if (strpos($os, "Macintosh") || strpos($os, "Mac_PowerPC"))
                $ostyp = "Mac OS";
            if (strpos($os, "Googlebot") || strpos($os, "www.google.com"))
                $ostyp = "Google Bot";
            if (!isset($ostyp))
                $ostyp = "UnKnown";
        }
        return $ostyp;
    }

    function get_link($link, $link_type) {
        global $ims;
        $output = '';
        switch ($link_type) {
            case "web":
                $output = $link;
                break;
            case "mail":
                $output = 'mailto:' . $link;
                break;
            case "neo":
                $output = '#' . $link;
                break;
            default:
                $output = $ims->conf["rooturl"] . $link;
                break;
        }
        return $output;
    }

    function hex2rgb($hexStr, $returnAsString = false, $seperator = ',') {
        $hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr); // Gets a proper hex string
        $rgbArray = array();
        if (strlen($hexStr) == 6) { //If a proper hex code, convert using bitwise operation. No overhead... faster
            $colorVal = hexdec($hexStr);
            $rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
            $rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
            $rgbArray['blue'] = 0xFF & $colorVal;
        } elseif (strlen($hexStr) == 3) { //if shorthand notation, need some string manipulations
            $rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
            $rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
            $rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
        } else {
            return false; //Invalid hex color code
        }
        return $returnAsString ? implode($seperator, $rgbArray) : $rgbArray; // returns the rgb string or the associative array
    }
   
    function rgb2hex($rgb) {
        if (!is_array($rgb) || count($rgb) != 3) {
            echo "Argument must be an array with 3 integer elements";
            return false;
        }
        for ($i = 0; $i < count($rgb); $i++) {
            if (strlen($hex[$i] = dechex($rgb[$i])) == 1) {
                $hex[$i] = "0" . $hex[$i];
            }
        }
        return implode('', $hex);
    }

    function location_name($type = 'area', $code = '') {
        global $ims;

        $data = $ims->load_data->data_table(
            'location_'.$type, 
            'code', 
            'code, title', 
            'is_show=1 AND lang="'.$ims->conf['lang_cur'].'" AND code="'.$code.'" LIMIT 0,1'
        );
        return $this->if_isset($data[$code]['title'], $code);
    }

    function full_address($info = array(), $pre = '') {
        $arr_tmp = array();
        if (isset($info[$pre . 'address'])) {
            $arr_tmp[] = $info[$pre . 'address'];
        }
        $arr_k = array('ward', 'district', 'province', 'country', 'area');
        foreach ($arr_k as $k) {
            if (isset($info[$pre . $k]) && !empty($info[$pre . $k])) {
                $arr_tmp[] = $this->location_name($k, $info[$pre . $k]);
            }
        }
        return (count($arr_tmp) > 0) ? implode(', ', $arr_tmp) : '';
    }

    /**
        * thumbs
        *
        * @params  string  
        * @params  string   
        *
        * @return
    */
    function thumbs($imgfile = "", $path, $maxWidth, $maxHeight = "", $crop = 0, $arr_more = array()) {
        if ($maxHeight == "") {
            $maxHeight = $maxWidth;
        }
        $info = @getimagesize($imgfile);
        $mime = $info[2];
        $fext = ($mime == 1 ? 'image/gif' : ($mime == 2 ? 'image/jpeg' : ($mime == 3 ? 'image/png' : NULL)));
        switch ($fext) {
            case 'image/pjpeg':
            case 'image/jpeg':
            case 'image/jpg':
                if (!function_exists('imagecreatefromjpeg')) {
                    die('No create from JPEG support');
                } else {
                    $img['src'] = @imagecreatefromjpeg($imgfile);
                }
                break;
            case 'image/png':
                if (!function_exists('imagecreatefrompng')) {
                    die("No create from PNG support");
                } else {
                    $img['src'] = @imagecreatefrompng($imgfile);
                }
                break;
            case 'image/gif':
                if (!function_exists('imagecreatefromgif')) {
                    die("No create from GIF support");
                } else {
                    $img['src'] = @imagecreatefromgif($imgfile);
                }
                break;
        }
        $img['old_w'] = @imagesx($img['src']);
        $img['old_h'] = @imagesy($img['src']);
        if ($crop == 1) {
            // Ratio cropping
            $offsetX = 0;
            $offsetY = 0;
            $new_w = $maxWidth;
            $new_h = $maxHeight;
            $cropRatio = array($maxWidth, $maxHeight);
            if (count($cropRatio) == 2) {
                $ratioComputed = $img['old_w'] / $img['old_h'];
                $cropRatioComputed = (float) $cropRatio[0] / (float) $cropRatio[1];
                $ratio = max($maxWidth / $img['old_w'], $maxHeight / $img['old_h']);
                $img_tmp = $img;
                if ($ratioComputed < $cropRatioComputed) { // Image is too tall so we will crop the top and bottom
                    //$img['old_w'] = $img['old_w'];
                    $img['old_h'] = $img['old_w'] / $cropRatioComputed;
                    $offsetY = ($img_tmp['old_h'] - $maxHeight / $ratio) / 2;
                } else if ($ratioComputed > $cropRatioComputed) { // Image is too wide so we will crop off the left and right sides		
                    //$img['old_h'] = $img['old_h'];
                    $img['old_w'] = $img['old_h'] * $cropRatioComputed;
                    $offsetX = ($img_tmp['old_w'] - $maxWidth / $ratio) / 2;
                }
            }
        } else {
            $new_h = $img['old_h'];
            $new_w = $img['old_w'];
            $offsetX = 0;
            $offsetY = 0;
            $tl_old = $img['old_w'] / $img['old_h'];
            $tl_new = 1;
            if ($maxHeight != 'auto') {
                $tl_new = $maxWidth / $maxHeight;
            }
            if (isset($arr_more["fix_width"])) {
                $new_w = $maxWidth;
                $new_h = ($maxWidth / $img['old_w']) * $img['old_h'];
            } elseif (isset($arr_more["fix_height"])) {
                $new_h = $maxHeight;
                $new_w = ($maxHeight / $img['old_h']) * $img['old_w'];
            } elseif (isset($arr_more["fix_min"])) {
                if ($img['old_w'] > $img['old_h']) {
                    $new_h = $maxHeight;
                    $new_w = ($maxHeight / $img['old_h']) * $img['old_w'];
                    if ($new_w < $maxWidth) {
                        $new_w = $maxWidth;
                        $new_h = ($maxWidth / $img['old_w']) * $img['old_h'];
                    }
                } else {
                    $new_w = $maxWidth;
                    $new_h = ($maxWidth / $img['old_w']) * $img['old_h'];
                    if ($new_h < $maxHeight) {
                        $new_h = $maxHeight;
                        $new_w = ($maxHeight / $img['old_h']) * $img['old_w'];
                    }
                }
            } elseif (isset($arr_more["zoom_max"])) {
                if ($tl_new > $tl_old) {
                    $new_h = $maxHeight;
                    $new_w = ($maxHeight / $img['old_h']) * $img['old_w'];
                } else {
                    $new_w = $maxWidth;
                    $new_h = ($maxWidth / $img['old_w']) * $img['old_h'];
                }
            } else {
                if ($img['old_w'] > $maxWidth) {
                    $new_w = $maxWidth;
                    $new_h = ($maxWidth / $img['old_w']) * $img['old_h'];
                }
                if ($new_h > $maxHeight && $maxHeight != "auto") {
                    $new_h = $maxHeight;
                    $new_w = ($new_h / $img['old_h']) * $img['old_w'];
                }
            }
        }
        $img['des'] = @imagecreatetruecolor($new_w, $new_h);
        if ($fext == "image/png") {
            @imagealphablending($img['des'], false);
            @imagesavealpha($img['des'], true);
        } else {
            $white = @imagecolorallocate($img['des'], 255, 255, 255);
            @imagefill($img['des'], 1, 1, $white);
        }
        @imagecopyresampled($img['des'], $img['src'], 0, 0, $offsetX, $offsetY, $new_w, $new_h, $img['old_w'], $img['old_h']);
        //	print "path = ".$path."<br>";	
        @touch($path);
        switch ($fext) {
            case 'image/pjpeg':
            case 'image/jpeg':
            case 'image/jpg':
                //@imagejpeg($img['des'], $path, 90);
                @imagejpeg($img['des'], $path, 100);
                break;
            case 'image/png':
                @imagepng($img['des'], $path);
                break;
            case 'image/gif':
                //@imagegif($img['des'], $path, 90);
                @imagegif($img['des'], $path, 100);
                break;
        }
        // Finally, we destroy the images in memory.
        @imagedestroy($img['des']);
    }

    /* -------------- watermark -------------------- */
    function watermark($imgfile, $watermark, $pos = 'rb') {
        global $ims;

        $imgcreate = function($imgfile, $fext) {
            $im = '';
            // Load the stamp and the photo to apply the watermark to
            switch ($fext) {
                case 'image/pjpeg':
                case 'image/jpeg':
                case 'image/jpg':
                    if (!function_exists('imagecreatefromjpeg')) {
                        die('No create from JPEG support');
                    } else {
                        $im = @imagecreatefromjpeg($imgfile);
                    }
                    break;
                case 'image/png':
                    if (!function_exists('imagecreatefrompng')) {
                        die("No create from PNG support");
                    } else {
                        $im = @imagecreatefrompng($imgfile);
                    }
                    break;
                case 'image/gif':
                    if (!function_exists('imagecreatefromgif')) {
                        die("No create from GIF support");
                    } else {
                        $im = @imagecreatefromgif($imgfile);
                    }
                    break;
            }
            return $im;
        };
        $imgfile = str_replace($ims->conf['rooturl'], $ims->conf['rootpath'], $imgfile);
        $info = @getimagesize($imgfile);
        $mime = $info[2];
        $fext = ($mime == 1 ? 'image/gif' : ($mime == 2 ? 'image/jpeg' : ($mime == 3 ? 'image/png' : NULL)));
        $im = $imgcreate($imgfile, $fext);
        $watermark = str_replace($ims->conf['rooturl'], $ims->conf['rootpath'], $watermark);
        $watermark_info = @getimagesize($watermark);
        $watermark_mime = $watermark_info[2];
        $watermark_fext = ($watermark_mime == 1 ? 'image/gif' : ($watermark_mime == 2 ? 'image/jpeg' : ($watermark_mime == 3 ? 'image/png' : NULL)));
        $stamp = $imgcreate($watermark, $watermark_fext);
        // Set the margins for the stamp and get the height/width of the stamp image
        $margex = 10;
        $margey = 10;
        $sx = imagesx($stamp);
        $sy = imagesy($stamp);
        //--------------
        $imw = imagesx($im);
        $imh = imagesy($im);
        //--------------
        switch ($pos) {
            case 'lt':
                $watermark_posx = $margex;
                $watermark_posy = $margey;
                break;
            case 'lc':
                $watermark_posx = $margex;
                $watermark_posy = floor($imh / 2) - floor($sy / 2);
                break;
            case 'lb':
                $watermark_posx = $margex;
                $watermark_posy = $imh - $sy - $margey;
                break;
            case 'rt':
                $watermark_posx = $imw - $sx - $margex;
                $watermark_posy = $margey;
                break;
            case 'rc':
                $watermark_posx = $imw - $sx - $margex;
                $watermark_posy = floor($imh / 2) - floor($sy / 2);
                break;
            case 'rb':
                $watermark_posx = $imw - $sx - $margex;
                $watermark_posy = $imh - $sy - $margey;
                break;
            case 'ct':
                $watermark_posx = floor($imw / 2) - floor($sx / 2);
                $watermark_posy = $margey;
                break;
            case 'cc':
                $watermark_posx = floor($imw / 2) - floor($sx / 2);
                $watermark_posy = floor($imh / 2) - floor($sy / 2);
                break;
            case 'cb':
                $watermark_posx = floor($imw / 2) - floor($sx / 2);
                $watermark_posy = $imh - $sy - $margey;
                break;
            default :
                $watermark_posx = $imw - $sx - $margex;
                $watermark_posy = $imh - $sy - $margey;
                break;
        }
        if ($sx > ($imw / 2)) {
            $sx = floor($imw / 2);
            $sy = floor($sx * $imh / $imw);
        }
        imagecopy($im, $stamp, $watermark_posx, $watermark_posy, 0, 0, $sx, $sy);
        // Output and free memory
        //header('Content-type: image/png');
        //imagepng($im);
        //imagepng($im, 'photo_img.png');
        $path = str_replace($ims->conf['rooturl'], $ims->conf['rootpath'], $imgfile);
        @touch($path);
        switch ($fext) {
            case 'image/pjpeg':
            case 'image/jpeg':
            case 'image/jpg':
                @imagejpeg($im, $path, 100);
                break;
            case 'image/png':
                @imagepng($im, $path);
                break;
            case 'image/gif':
                @imagegif($im, $path, 100);
                break;
        }
        // Finally, we destroy the images in memory.
        @imagedestroy($im);
        return true;
    }


    function get_src_mod($picture, $w = "", $h = "", $thumb = 1, $crop = 0, $arr_more = array()) {
        global $ims;

        $arr_extension = array('gif', 'png', 'jpg', 'jpeg', 'pjpeg', 'webp');
        $arr_jpg  = array('jpg', 'jpeg', 'pjpeg');
        $arr_png  = array('png');
        $arr_svg  = array('svg');
        $extension = strtolower(substr($picture, strrpos($picture, ".") + 1));
        if (in_array($extension, $arr_svg)) {
            return $ims->conf['rooturl']."uploads/".$picture;
        }
        if (!in_array($extension, $arr_extension)) {
            $picture = 'nophoto/nophoto.jpg';
        }
        $arr_not_thumb = array('gif');
        if(empty($w) && empty($h) && !in_array($extension,$arr_not_thumb)){            
            if(file_exists($ims->conf['rootpath']."uploads/".str_replace("//", "/",$picture))){
                $imgSize = getimagesize($ims->conf['rootpath']."uploads/".str_replace("//", "/",$picture));
                $w = !empty($imgSize[0])?$imgSize[0]:'';
                $h = !empty($imgSize[1])?$imgSize[1]:'';
            }
        }
        $out = "";
        $pre = $w;
        if ($h) {
            $pre = $w . "x" . $h;
        } else {
            $h = $w;
        }
        
        if ($crop != 0) {
            $pre .= "-cr";
        } elseif (isset($arr_more['fix_min'])) {
            $pre .= "-fmi";
        } elseif (isset($arr_more['fix_max'])) {
            $pre .= "-fma";
        } elseif (isset($arr_more['fix_width'])) {
            $pre .= "-fw";
        } elseif (isset($arr_more['fix_height'])) {
            $pre .= "-fh";
        } elseif (isset($arr_more['zoom_max'])) {
            $pre .= "-zma";
        }
        $pre = "[".$pre."]";
        $linkhinh = $picture;
        $linkhinh = str_replace("//", "/", $linkhinh);
        if (!file_exists($ims->conf['rootpath_web']."uploads/".$linkhinh)) {
            $linkhinh = 'nophoto/nophoto.jpg';
        }
        $dir = substr($linkhinh, 0, strrpos($linkhinh, "/"));
        $pic_name = substr($linkhinh, strrpos($linkhinh, "/") + 1);
        $linkhinh = "uploads/".$linkhinh;
        if ($extension == 'gif') {
            $w = '';
        }
        if ($w) {
            if ($thumb) {
                $folder_thumbs  = $dir;
                $file_thumbs    = $folder_thumbs . "/{$pre}" . substr($linkhinh, strrpos($linkhinh, "/") + 1);
                $linkhinhthumbs = $ims->conf['rootpath'] . "thumbs/" . $file_thumbs;
                $src = $ims->conf['rooturl'] . 'thumbs/' . $file_thumbs;                
            } else {
                $src = $ims->conf['rooturl'] . $folder_thumbs . "/" . $pic_name;
            }
        } else {
            $src = $ims->conf['rooturl'] . 'uploads/' . $picture;
        }

        if (in_array($extension, $arr_jpg)) {
            // Image is jpg
            if (strpos($src, 'thumbs') !== false) {
                $src = str_replace($extension, $extension.'__cv.webp', $src);
            }
        }
        if (in_array($extension, $arr_png)) {
            if (strpos($src, 'thumbs') !== false) {
                $size = getimagesize($ims->conf['rootpath_web'].$linkhinh);
                // Image is png
                if (isset($size['mime']) && $size['mime'] == 'image/png') {
                    $im     = imagecreatefrompng($ims->conf['rootpath_web'].$linkhinh);
                    $rgba   = imagecolorat($im, 1, 1);
                    $alpha  = ($rgba & 0x7F000000) >> 24;
                    // if ($alpha == 0) {
                        $src = str_replace($extension, $extension.'__cv.webp', $src);
                    // }
                }
            }
        }

        return $src;
    }


    function get_pic_mod($picture, $w = "", $h = "", $ext = "", $thumb = 1, $crop = 0, $arr_more = array()) {
        global $ims;

        $src = $this->get_src_mod($picture, $w, $h, $thumb, $crop, $arr_more);
        $out = "<img  src=\"{$src}\"  {$ext} >";
        return $out;
    }

    function format_size($rawSize) {
        if ($rawSize / 1048576 > 1) {
            return round($rawSize / 1048576, 1) . ' MB';
        } else {
            if ($rawSize / 1024 > 1) {
                return round($rawSize / 1024, 1) . ' KB';
            } else {
                return round($rawSize, 1) . ' Bytes';
            }
        }
    }


    /**
        * @function : format_number  
        * @param 	: $num -> Chuỗi số
        * 			  $seperator-> Dấu phân cách	 
        * @return	: Chuỗi số 
    */
    function format_number($num, $seperator = ",") {
        $string = strrev(substr(chunk_split(strrev($num), 3, $seperator), 0, - 1));
        return $string;
    }

    /**
        * @function : get_price_format  
    */
    function get_price_format($price, $default = "", $rate = 0) {
        global $ims;
        $output = '';
        if (strlen($default) == 0) {
            $default = $ims->lang["global"]["price_empty"];
        } elseif ($default == 0) {            
            $default = "<span class=\"price_format\"><span class=\"number\" data-value=\"0\">" . $default . "</span></span>";
        }
        if ($price) {
            if ($rate != 0) {
                $price = $price / $rate;
            }
            $output = "<span class=\"price_format\"><span class=\"number autoUpdate auto_price\" data-value=\"" . $price . "\">" . $price . "</span></span>";
            // $nguyen = (int) $price;
            // $dot = strpos($price, ".");
            // if ($dot) {
            //     $du = substr($price, strpos($price, "."), 3);
            // } else {
            //     $du = "";
            // }
            // $price = "<span class=\"price_format\"><span class=\"unit\">" . $unit . "</span><span class=\"number autoUpdate\">" . $this->format_number($nguyen) . $du . "</span></span>";
        } else {
            $output = $default;
        }
        return $output;
    }

    function get_price_format_email($price, $default = "", $unit = "đ", $rate = 0) {
        global $ims;
        if (strlen($default) == 0) {
            $default = $ims->lang["global"]["price_empty"];
        } elseif ($default == 0) {
            $default = "<span class=\"price_format\"><span class=\"number\">" . $default . "</span> <span class=\"unit\">" . $unit . "</span></span>";
        }
        if ($price) {
            if ($rate) {
                $price = $price / $rate;
            }
            $nguyen = (int) $price;
            $dot = strpos($price, ".");
            if ($dot) {
                $du = substr($price, strpos($price, "."), 3);
            } else {
                $du = "";
            }
            $price = "<span class=\"price_format\"><span class=\"number autoUpdate\">" . $this->format_number($nguyen) . $du . "</span> <span class=\"unit\">" . $unit . "</span></span>";
        } else {
            $price = $default;
        }
        return $price;
    }

    function get_price_text($price, $default="", $display="", $unit="vnđ" ,$rate =0){
        global $ims;

        $price_text ='';
        if(strlen($default) == 0) {
            $default = $ims->lang["global"]["price_empty"];
        } elseif($default == 0) {
            $default = "<span class=\"auto_price\" data-value=\"".$price."\">".$price."</span>";
        }
        if ($price) {
            $price_str = $this->format_number($price);
            $arr = explode(',', $price_str);
            $n = count($arr);

            if (isset($arr[$n - 4])) {
                if (intval($arr[$n - 4]) > 0) {
                    $price_text .= " <span class=\"intval\">".intval($arr[$n - 4])."</span><span class=\"unit ".$display."\"> ".$ims->lang['global']['billion']."</span>";
                }
            }
            if (isset($arr[$n - 3])) {
                if (intval($arr[$n - 3]) > 0) {
                    $price_text .= " <span class=\"intval\">".intval($arr[$n - 3])."</span><span class=\"unit ".$display."\"> ".$ims->lang['global']['million']."</span>";
                }
            }
            if (isset($arr[$n - 2])) {
                if (intval($arr[$n - 2]) > 0) {
                    $price_text .= " <span class=\"intval\">".intval($arr[$n - 2])."</span><span class=\"unit ".$display."\"> ".$ims->lang['global']['thousand']."</span>";
                }
            }
            if (isset($arr[$n - 1])) {
                if (intval($arr[$n - 1]) > 0) {
                    $price_text .= " <span class=\"intval\">".intval($arr[$n - 1])." ".$ims->lang['global']['currency']."</span>";
                }
            }
            $price_text = '<span class="price_css">'.trim($price_text).'</span>';
        }else{
            $price_text = '<span class="price_css">'.$ims->lang["global"]["price_empty"].'</span>';
        }
        return $price_text;
    }
    // getHost_URL
    function getHost_URL($Address) {
        $parseUrl = parse_url(trim($Address));

        $domain = (isset($parseUrl['host'])) ? $parseUrl['host'] : $parseUrl['path']; //array_shift(explode('/', $parseUrl['path'], 2));

        $tmp = explode(".", $domain);
        $domain = ($tmp[0] == "www" && count($tmp) > 2) ? substr($domain, 4) : $domain;

        if ($domain == "localhost") {
            $tmp = explode("/", $parseUrl['path']);
            if (isset($tmp[1]))
                $domain = $domain . "_" . $tmp[1];
        }

        return $domain;
    }

    function getMultiDistance($from, $to, $arr_id = array()){
        global $ims;
        $out = array();
        $key_google = $ims->conf["key_google_map"];
        $curl = curl_init();
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&mode=driving&origins=".urlencode($from)."&destinations=".urlencode($to)."&key=".$key_google;
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));
        // echo "https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&mode=driving&origins=".urlencode($from)."&destinations=".urlencode($to)."&key=".$key_google;
        $response = curl_exec($curl);        
        $err = curl_error($curl);        
        curl_close($curl);        
        if (!empty($response)) {
            $response = json_decode($response, true);
            // $out = $response;            
            if(!empty($response['rows'][0]['elements'])){
                foreach ($response['rows'][0]['elements'] as $key => $value) {
                    $out[$arr_id[$key]] = $value['distance']['value'];
                }
            }
        }
        return $out;
    }
    


    function link2hex($str = '', $len = 4){
        global $ims;
        $output = '';
        if(!empty($str)){
            $str = $ims->func->base64_encode($str);
            $str = bin2hex($str);
            $str = str_split($str,$len);
            $str = implode('-', $str);
            $output = $str;
        }
        return $output;
    }

    function get_id_page($url = ''){
        global $ims;
        $output = array();
        if(!empty($url)){
            $code = str_replace('-', '', $url);
            if (ctype_xdigit($code) && strlen($code)%2==0) {
                $query = hex2bin($code);
                $query = $ims->func->base64_decode($query);
                parse_str(str_replace('?', '', $query),$param);
                foreach ($param as $key => $value) {
                    $output[$key] = $value;
                }
            }
        }
        return $output;
    }

    function encrypt_decrypt($action, $string, $secret_key, $secret_iv) {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        // $secret_key = 'This is my secret key';
        // $secret_iv = 'This is my secret iv';
        // hash
        $key = hash('sha256', $secret_key);

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        if ( $action == 'encrypt' ) {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } else if( $action == 'decrypt' ) {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }
        return $output;
    }
    // End class
}
?>