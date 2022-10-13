<?php
if (!defined('IN_ims')) { die('Access denied'); }
use MatthiasMullie\Minify;


class Site {
    function compressingJS(){
        global $ims;

        $name_file = $ims->resources_path."minify/minify.jquery.min.js";
        if (file_exists($name_file) && $ims->conf['refresh'] == 0) {
            return 1;
        }

        require ($ims->conf["rootpath"]."library".DS."minify/autoload.php"); 
        $dir_global_path = $ims->func->dirModules("global", "assets", "path");
        $jsFiles = array(
            $ims->dir_js_path.'select2/select2.js', // Jquery select2
            $ims->dir_js_path.'slick/slick.min.js', // jquery slick slider
            $ims->dir_js_path.'jquery.validate.js', // jquery kiểm tra lỗi form
            // $ims->dir_js_path.'jquery.smooth-scroll.js', // jquery mượt scroll trình duyệt
            $ims->dir_js_path.'lazyload.min.js', // jquery lazy load
            $ims->dir_js_path.'jquery_ui/jquery-ui.min.js', // jquery ui
            $ims->dir_js_path.'fancybox/jquery.fancybox.min.js', // jquery popup
            $ims->dir_js_path.'sweetalert2/sweetalert2.all.min.js', // jquery thông báo
            // $ims->dir_js_path.'smartmenus/jquery.smartmenus.js', // jquery Menu
//            $ims->dir_js_path.'smartmenus/addons/bootstrap-4/jquery.smartmenus.bootstrap-4.min.js', // jquery Menu bt4
            // $ims->dir_js_path.'auto_numeric/autoNumeric.js', // jquery định dạng tiền
            $ims->dir_js_path.'jquery.sticky.js',
            $ims->dir_js_path.'javascript.js', // Javascript custom
            $ims->dir_js_path.'setting.js', // Javascript setting custom
            $ims->dir_js_path.'bootstrap/js/popper.min.js',
            $ims->dir_js_path.'bootstrap/js/bootstrap.min.js',
            // $ims->dir_js_path.'bootstrap/js/bootstrap-tagsinput.min.js',
//            $ims->dir_js_path.'statistic.js', // statistic
             $dir_global_path.'js/location.js', // Địa chỉ
        );
        $sourcePath = $ims->dir_js_path.'jquery-3.6.0.min.js';
        $minifier = new Minify\JS($sourcePath);

        foreach ($jsFiles as $key => $file) {
            $minifier->add($file);
        }

        $minifier->minify($name_file);
    }
    function compressingCSS(){
        global $ims;

        $name_file  = $ims->resources_path."minify/minify.style.min.css";
        /* Add your CSS files to this array (THESE ARE ONLY EXAMPLES) */
        if (file_exists($name_file) && $ims->conf['refresh'] == 0) {
            return 1;
        }

        $dir_css_image = $ims->dir_css.'images/';
        $dir_fonts = $ims->func->dirModules("global", "assets", "fonts");
        $dir_fonts_fontello = $ims->dir_css.'fontello/font/ficon';
        $dir_global      = $ims->func->dirModules("global", "assets", "css");
        $dir_global_path = $ims->func->dirModules("global", "assets", "path");
        $dir_global_icon = $dir_global."/fontawesome/webfonts/";
        $dir_slick       = $ims->dir_js.'slick/assets';

        $cssFiles = array(
//            $ims->dir_js."jquery_ui2/jquery-ui.css", // jquery_ui_search như đttl
            // $ims->dir_js."smartmenus/css/sm-core-css.css",
//            $ims->dir_js."smartmenus/css/sm-simple/sm-simple.css",
            // $ims->dir_js."smartmenus/addons/bootstrap-4/jquery.smartmenus.bootstrap-4.css",
            $ims->dir_js."bootstrap/css/bootstrap.min.css",
            // $ims->dir_js."bootstrap/css/bootstrap-tagsinput.css",
            $ims->dir_js.'jquery_ui/jquery-ui.min.css',

            $ims->dir_js."select2/select2.css",
            $ims->dir_js."slick/assets/slick.css",
            $ims->dir_js."slick/assets/slick-theme.css",
            $ims->dir_js."fancybox/jquery.fancybox.min.css",

            $dir_global."/fontawesome/css/all.min.css",
            $dir_global."/layout.css",
        );

        $time_create = "/* ".date("h:i:s d/m/Y")." */*";
        $buffer = "";
        $arrContextOptions = array(
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            )
        );
        foreach ($cssFiles as $cssFile) {
            $buffer .= file_get_contents($cssFile, false, stream_context_create($arrContextOptions));
        }

        // Remove comments
        $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);

        // Remove space after colons
        $buffer = str_replace(': ', ':', $buffer);

        // Remove whitespace
        $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);
        //$buffer = str_replace(array("\r\n", "\r", "\n", "\t"), '', $buffer);
        //$buffer = ereg_replace(" {2,}", ' ',$buffer);

        // Remove spaces that might still be left where we know they aren't needed
        $buffer = str_replace(array('} '), '}', $buffer);
        $buffer = str_replace(array('{ '), '{', $buffer);
        $buffer = str_replace(array('; '), ';', $buffer);
        $buffer = str_replace(array(', '), ',', $buffer);
        $buffer = str_replace(array(' }'), '}', $buffer);
        $buffer = str_replace(array(' {'), '{', $buffer);
        $buffer = str_replace(array(' ;'), ';', $buffer);
        $buffer = str_replace(array(' ,'), ',', $buffer);

        // Edit image
        // $buffer = str_replace(array("../../images/", "../images/"), $dir_css_image, $buffer);
        // $buffer = str_replace(array('("images/', '(images/'), '('.$dir_css_image, $buffer);
        $buffer = str_replace(array("../fonts"), $dir_fonts, $buffer);
        $buffer = str_replace(array("../../../../../resources/"), $ims->conf['rooturl'].'resources/', $buffer);
        // $buffer = str_replace(array("../font/ficon"), $dir_fonts_fontello, $buffer);
        $buffer = str_replace(array("chosen-sprite.png"), $dir_css_image.'chosen-sprite.png', $buffer);
        $buffer = str_replace(array("fancybox_sprite.png"), $dir_css_image.'fancybox_sprite.png', $buffer);
        // $buffer = str_replace(array("fancybox_overlay.png"), $dir_css_image.'fancybox_overlay.png', $buffer);
        $buffer = str_replace(array("../webfonts/"), $dir_global_icon, $buffer);

        // replace images
        $buffer = str_replace(array("[DIR_SLICK]"), $dir_slick, $buffer);
        $buffer = str_replace(array("[DIR_IMAGES]"), $ims->dir_images, $buffer);

        $buffer = $time_create.$buffer;

        // Write everything out
        $a = fopen($name_file, 'w');
        fwrite($a, $buffer);
        fclose($a);
        chmod($name_file, 0644);
    }

    function compressingCssOutput($arrCss = array()){
        global $ims;

        if (!empty($arrCss)) {
            $name_file  = $ims->resources_path."minify/".md5(serialize($arrCss))."_include_css.min.css";            
            if (file_exists($name_file) && $ims->conf['refresh'] == 0) {
//                return $ims->func->fileGetContent($name_file);
                return str_replace('../images/use', $ims->conf['rooturl'].'resources/images/use', $ims->func->fileGetContent($name_file));
            }
            require ($ims->conf["rootpath"]."library".DS."minify/autoload.php"); 
            $sourcePath = $arrCss[0];
            $minifier = new Minify\CSS($sourcePath);
            $i=0;
            foreach ($arrCss as $key => $file) {
                if ($i>0) {
                    $minifier->add($file);
                }
                $i++;
            }
            $minifier->minify($name_file);

            return str_replace('../../../../../resources/', $ims->conf['rooturl'].'resources/', $minifier->minify());
        }
    }

    function loadGlobalCssFile(){
        global $ims;
        $ims->conf["include_css_file"] = (isset($ims->conf["include_css_file"])) ? $ims->conf["include_css_file"] : "";
        $ims->arr_include_css = (isset($ims->arr_include_css)) ? $ims->arr_include_css : array();
        $dir_global = $ims->func->dirModules("global", "assets", "css");
        $cssFiles = array(
            // $ims->dir_js."smartmenus/css/sm-core-css.css",
//            $ims->dir_js."smartmenus/css/sm-simple/sm-simple.css",
            // $ims->dir_js."smartmenus/addons/bootstrap-4/jquery.smartmenus.bootstrap-4.css",

            $ims->dir_js."bootstrap/css/bootstrap.min.css",
            // $ims->dir_js."bootstrap/css/bootstrap-tagsinput.css",
            $ims->dir_js.'jquery_ui/jquery-ui.min.css',

            $ims->dir_js."select2/select2.css",
            $ims->dir_js."slick/assets/slick.css",
            $ims->dir_js."slick/assets/slick-theme.css",
            $ims->dir_js."fancybox/jquery.fancybox.min.css",

            $dir_global."/fontawesome/css/all.min.css",
            $dir_global."/layout.css",
        );

        foreach ($cssFiles as $key => $value) {
            if (isset($value) && !in_array($value, $ims->arr_include_css)) {
                $ims->conf["include_css_file"] .= '<link rel="stylesheet" href="' . $value . '" type="text/css" />';
            }
        }
    }

    function loadGlobalJSFile(){
        global $ims;
        $ims->conf["include_js"] = (isset($ims->conf["include_js"])) ? $ims->conf["include_js"] : "";
        $ims->arr_include_js = (isset($ims->arr_include_js)) ? $ims->arr_include_js : array();
        $dir_global_path = $ims->func->dirModules("global", "assets", "path");
        $jsFiles = array(
            $ims->dir_js_path.'jquery-3.6.0.min.js',
            $ims->dir_js_path.'select2/select2.js', // Jquery select2
            $ims->dir_js_path.'slick/slick.min.js', // jquery slick slider
            $ims->dir_js_path.'jquery.validate.js', // jquery kiểm tra lỗi form
            // $ims->dir_js_path.'jquery.smooth-scroll.js', // jquery mượt scroll trình duyệt
            $ims->dir_js_path.'lazyload.min.js', // jquery lazy load
            $ims->dir_js_path.'jquery_ui/jquery-ui.min.js', // jquery ui
            $ims->dir_js_path.'fancybox/jquery.fancybox.min.js', // jquery popup
            $ims->dir_js_path.'sweetalert2/sweetalert2.all.min.js', // jquery thông báo
            // $ims->dir_js_path.'smartmenus/jquery.smartmenus.js', // jquery Menu
            // $ims->dir_js_path.'smartmenus/addons/bootstrap-4/jquery.smartmenus.bootstrap-4.min.js', // jquery Menu bt4
            // $ims->dir_js_path.'auto_numeric/autoNumeric.js', // jquery định dạng tiền
            $ims->dir_js_path.'jquery.sticky.js',
            $ims->dir_js_path.'javascript.js', // Javascript custom
            $ims->dir_js_path.'setting.js', // Javascript setting custom
            $ims->dir_js_path.'bootstrap/js/popper.min.js',
            $ims->dir_js_path.'bootstrap/js/bootstrap.min.js',
            // $ims->dir_js_path.'bootstrap/js/bootstrap-tagsinput.min.js',
//            $ims->dir_js_path.'statistic.js',
             $dir_global_path.'js/location.js', // Địa chỉ
        );

        foreach ($jsFiles as $key => $value) {
            if (isset($value) && !in_array($value, $ims->arr_include_js)) {
                $value = str_replace($ims->conf['rootpath'], $ims->conf['rooturl'], $value);
                $ims->conf["include_js"] .= '<script src="' . $value . '"></script>';
            }
        }
    }

    //-----------------box_lang
    public function box_lang($cur, $type = 0) {
        global $ims;

        //$ims->data["lang"] = array();
        $output = "";
        if (!isset($ims->data["lang"])) {
            $result = $ims->db->query("select * from lang order by show_order desc, id asc");
            while ($row = $ims->db->fetch_row($result)) {
                $ims->data["lang"][$row["name"]] = $row;
                if ($row["is_default"] == 1) {
                    $ims->data["lang_default"] = $row;
                }
            }
        }
        //$link_ext = (!empty($_SERVER['QUERY_STRING'])) ? explode('&',$_SERVER['QUERY_STRING']) : '';
        if (!array_key_exists($cur, $ims->data['lang'])) {
            $ims->conf['lang_cur'] = $cur = $ims->data["lang_default"]['name'];
            $url = $ims->conf["rooturl"];
            $ims->html->redirect_rel($url);
        }
        foreach ($ims->data['lang'] as $row) {
            if($type == 1){
                if($row['name'] != $cur){
                    $row['link'] = (isset($ims->data['link_lang'][$row['name']])) ? $ims->data['link_lang'][$row['name']] : '';
                    $row['current'] = ($cur == $row['name']) ? ' current' : '';
                    $ims->temp_html->assign("row", $row);
                    $ims->temp_html->parse("box_lang.row");
                }
            }
            else{
                $row['link'] = (isset($ims->data['link_lang'][$row['name']])) ? $ims->data['link_lang'][$row['name']] : '';
                $row['current'] = ($cur == $row['name']) ? ' current' : '';
            $ims->temp_html->assign("row", $row);
            $ims->temp_html->parse("box_lang.row");
            }
        }
        $data = array();
        $data = $ims->data['lang'][$cur];
        $ims->temp_html->assign("LANG", $ims->lang);
        $ims->temp_html->assign("data", $data);
        $ims->temp_html->parse("box_lang");
        return $ims->temp_html->text("box_lang");
    }

    //-----------------get_link
    public function get_seo($data = array()) {
        global $ims;
        if (!is_array($data) && $data) {
            $data = array(
                'meta_title' => (isset($ims->setting[$ims->conf['cur_mod']][$ims->conf['cur_mod'] . "_meta_title"])) ? $ims->setting[$ims->conf['cur_mod']][$ims->conf['cur_mod'] . "_meta_title"] : '',
                'meta_key' => (isset($ims->setting[$ims->conf['cur_mod']][$ims->conf['cur_mod'] . "_meta_key"])) ? $ims->setting[$ims->conf['cur_mod']][$ims->conf['cur_mod'] . "_meta_key"] : '',
                'meta_desc' => (isset($ims->setting[$ims->conf['cur_mod']][$ims->conf['cur_mod'] . "_meta_desc"])) ? $ims->setting[$ims->conf['cur_mod']][$ims->conf['cur_mod'] . "_meta_desc"] : ''
            );
        }
        $ims->conf['meta_title'] = (isset($data['meta_title'])) ? $data['meta_title'] : $ims->conf['meta_title'];
        $ims->conf['meta_key'] = (isset($data['meta_key'])) ? $data['meta_key'] : $ims->conf['meta_key'];
        $ims->conf['meta_desc'] = (isset($data['meta_desc'])) ? $data['meta_desc'] : $ims->conf['meta_desc'];
        $ims->conf['canonical'] = $ims->conf['rooturl'];
        if (isset($data['item_id'])) {
            $ims->conf['canonical'] = $ims->func->get_link($data['friendly_link'], '');
        } elseif (isset($data['group_id'])) {
            $ims->conf['canonical'] = $ims->func->get_link($data['friendly_link'], '');
        }
        return true;
    }
 
    //-----------------get_link_menu
    public function get_link_menu($link, $link_type = 'site') {
        global $ims;
        return $ims->site_func->get_link_menu($link, $link_type);
    }

    //-----------------list_number
    function list_number($select_name = "id", $min = 0, $max = 10, $cur = "", $ext = "", $arr_more = array()) {
        global $ims;
        $min = (int) $min;
        $max = (int) $max;
        $max = ($max >= $min) ? $max : $min;
        $array = array();
        for ($i = $min; $i <= $max; $i++) {
            $array[$i] = $i;
        }
        return $ims->html->select($select_name, $array, $cur, $ext, $arr_more);
    }
    //-----------------get_logo
    public function get_logo($group_id = 'logo') {
        global $ims;
        $output = '';

        if (isset($ims->data["banner_group"][$group_id]) && isset($ims->data["banner"][$group_id])) {
            foreach ($ims->data["banner"][$group_id] as $banner) {
                $w = $ims->data["banner_group"][$group_id]['width'];
                $h = $ims->data["banner_group"][$group_id]['height'];
                $style = "width:" . $w . "px;";
                $style .= ($h > 0) ? "height:" . $h . "px;overflow:hidden;" : "";
//                $banner['link'] = $ims->site_func->get_link_menu($banner['link'], $banner['link_type']);
                $banner['link'] = $ims->site_func->get_link('');
                if ($banner['type'] == 'image') {
                    $banner['content'] = '<a href="'.$banner['link'].'" target="'.$banner['target'].'">'.$ims->func->get_pic_mod($banner['content'], $w, $h," alt=\"" . $banner['title'] . "\"", 1, 0, array('fix_width' => 1)) . '</a>';
                }
                $output = $banner['content'];
                return $output;
            }
        }
        return $output;
    }
    //-----------------get_background_img
    public function get_bg_img($group_id){
        global $ims;
        $output = '';
        $bg = $ims->db->load_item('banner','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and group_id="'.$group_id.'" order by show_order desc, date_create desc limit 0,1','content');
        if($bg){
            // $output = 'style="background-image:url('.$ims->func->get_src_mod($bg).')"';
            $output = 'data-bg="url('.$ims->func->get_src_mod($bg).')"';
        }
        return $output;
    }
    //-----------------get_banner
    public function get_banner($group_id = 'logo', $limit = 1, $lazy = 1) {
        global $ims;
        $output = '';
        if (isset($ims->data["banner_group"][$group_id]) && isset($ims->data["banner"][$group_id])) {
            $i = 0;
            $loading = $ims->dir_images."spin.svg";
            foreach ($ims->data["banner"][$group_id] as $banner) {
                $i++;
                $w = $ims->data["banner_group"][$group_id]['width'];
                $h = $ims->data["banner_group"][$group_id]['height'];
                $style_pic = '';
                $style_frame = '';
                if ($ims->data["banner_group"][$group_id]['height'] == 'fixed') {
                    $style_frame = "width:" . $w . "px;";
                    $style_frame .= ($h > 0) ? "height:" . $h . "px;overflow:hidden;" : "";
                    $style_frame = ($w > 0 || $h > 0) ? $style : '';
                } elseif ($ims->data["banner_group"][$group_id]['height'] == 'full') {
                    $style_pic = "width:100%;";
                }
                $banner['link'] = $ims->site_func->get_link_menu($banner['link'], $banner['link_type']);
                if ($banner['type'] == 'image') {
                    //$banner['content'] = '<img src="'.$ims->conf["rooturl"].'uploads/banner/'.$banner['content'].'" alt="'.$banner['title'].'" />';
                    // $banner['content'] = '<a href="' . $banner['link'] . '" target="' . $banner['target'] . '">' . $ims->func->get_pic_mod($banner['content'], $w, $h, " alt=\"" . $banner['title'] . "\" style=\"" . $style_pic . "\"", 1, 0, array('fix_width' => 1)) . '</a>';
                    if ($lazy == 0) {
                        $banner['content'] = '<a href="' . $banner['link'] . '" target="' . $banner['target'] . '"><img src="'.$ims->func->get_src_mod($banner['content'], $w, $h, 1, 0, array('fix_width' => 1)).'" alt="'.$banner['title'].'"></a>';
                    } else {
                        $banner['content'] = '<a href="' . $banner['link'] . '" target="' . $banner['target'] . '"><img class="lazyload" src="'.$loading.'" data-src="'.$ims->func->get_src_mod($banner['content'],$w,$h,1,0,array('fix_width'=>1)).'" alt="'.$banner['title'].'"></a>';
                    }
                }elseif ($banner['type'] == 'video' && $banner['content']) {
                    $banner['code_video'] = isset($banner['content']) ? $ims->func->get_youtube_code($banner['content']) : '';
                    $ims->temp_box->assign("row", $banner);
                    $ims->temp_box->parse("banner_video");
                    return $output .= $ims->temp_box->text("banner_video");
                }elseif ($banner['type'] == 'file' && $banner['content']){
                    return $output .= '<video src ='.$banner['content']. ' autoplay loop muted playsinline style="width:100%;"></video>';
                }
                $output .= '<div class="banner_item" style="' . $style_frame . '">' . $banner['content'] . '</div>';
                if ($i >= $limit && $limit > 0) {
                    return $output;
                }
            }
        }
        return $output;
    }
    function form_contact($id="", $title="", $link_go=""){
        global $ims;
        $ims->func->load_language('contact');
        $output = '';
        $data = array(
            'id' => $id,
            'title' => $title,
            'link_go' => $link_go
        );
        $ims->temp_box->reset('form_contact');
        $ims->temp_box->assign('data',$data);
        $ims->temp_box->assign('LANG',$ims->lang);
        $ims->temp_box->parse('form_contact');
        $output .= $ims->temp_box->text('form_contact');
        return $output;
    }
    //=================list_menu_sub_group===============
    function list_menu_sub_group($temp_name, $name_action = '', $array=null, $arr_cur = array(), $is_recursive = 0) {
        global $ims;
        $tmp = explode('-', $name_action);
        $mod = (isset($tmp[0])) ? $tmp[0] : '';
        $act = (isset($tmp[1])) ? $tmp[1] : '';
        $act_id = (isset($tmp[2])) ? $tmp[2] : 0;

        if (!$mod) {
            return '';
        }
        if($array == null){
            $ims->load_data->data_group($mod);
            $array = $ims->data[$mod . "_group_tree"];
            $group_act = ($act_id > 0 && isset($ims->data[$mod . "_group"][$act_id])) ? $ims->data[$mod . "_group"][$act_id] : 0;
            if ($group_act) {
                $arr_menu_nav = explode(',', $group_act['group_nav']);
                $str_code = '';
                $f = 0;
                foreach ($arr_menu_nav as $tmp) {
                    $f++;
                    $str_code .= ($f == 1) ? '[' . $tmp . ']' : '["arr_sub"][' . $tmp . ']';
                    if ($tmp == $group_act['group_id']) {
                        break;
                    }
                }
                eval('$array = $array' . $str_code . ';');
                if (isset($array['arr_sub'])) {
                    $array = $array['arr_sub'];
                } else {
                    return '';
                }
            }
        }
        $arr_cur = (isset($ims->conf['cur_group']) && $ims->conf['cur_group'] > 0 && isset($ims->conf["cur_group_nav"])) ? explode(',', $ims->conf["cur_group_nav"]) : array();
        $output = '';
        $menu_sub = '';
        $num = count($array);
        if ($num) {
            $i = 0;
            foreach ($array as $row) {
                $i++;
                if(isset($row['icon_code']) && $row['icon_code']!=''){
                    $row['icon_pic'] = '<div class="icon"><i class="ficon-'.$row['icon_code'].'"></i></div>';
                }elseif(isset($row['picture_icon']) && $row['picture_icon']!='') {
                    $row['icon_pic'] = '<div class="icon mr-3"><img width="20" height="20" src="' . $ims->func->get_src_mod($row['picture_icon']) . '" alt="' . $row['title'] . '"></div>';
                }else{
                    $row['icon_pic'] = '<div class="icon"><i class="fas fa-clipboard-list-check"></i></div>';
                }
                $row['link'] = $ims->func->get_link($row['friendly_link'], '');
                $row['class'] = ($mod == $ims->conf['cur_mod'] && in_array($row["group_id"], $arr_cur)) ? 'current' : '';
                $arr_class_li = array();
                if ($i == 1) {
                    $arr_class_li[] = 'first';
                }
                if ($i == $num) {
                    $arr_class_li[] = 'last';
                }
                $row['class_li'] = (count($arr_class_li) > 0) ? implode(' ', $arr_class_li) : '';
                $row['menu_sub'] = '';
                if (isset($row['arr_sub'])) {
                    $name_action_sub = $mod.'-'.$act.'-'.$act_id;                    
                    $row['menu_sub'] = $this->list_menu_sub_group($temp_name, $name_action_sub, $row['arr_sub'], $arr_cur, 1);
                    $row['class_li'] .= ' dropdown';
                }
                $ims->temp_html->assign('row', $row);
                $ims->temp_html->parse($temp_name . ".item.menu_sub.row");
                $menu_sub .= $ims->temp_html->text($temp_name . ".item.menu_sub.row");
                $ims->temp_html->reset($temp_name . ".item.menu_sub.row");
            }
            if ($is_recursive == 1) {
                $ims->temp_html->reset($temp_name.".item.menu_sub");
                $ims->temp_html->assign('row', array('content' => $menu_sub));
                $ims->temp_html->parse($temp_name.".item.menu_sub");
                $menu_sub = $ims->temp_html->text($temp_name.".item.menu_sub");
                $ims->temp_html->reset($temp_name.".item.menu_sub");
            }
        }
        return $menu_sub;
    }
    //=================list_menu_sub_item===============
    function list_menu_sub_item($temp_name, $name_action = '', $arr_cur = array()) {
        global $ims;
        if (is_array($name_action)) {
            $array = $name_action;
        } else {
            $tmp = explode('-', $name_action);
            $mod = (isset($tmp[0])) ? $tmp[0] : '';
            $act = (isset($tmp[1])) ? $tmp[1] : '';
            $act_id = (isset($tmp[2])) ? $tmp[2] : 0;
            if (!$mod) {
                return '';
            }
            $group_act = ($act_id > 0 && isset($ims->data[$mod . "_group"][$act_id])) ? $ims->data[$mod . "_group"][$act_id] : 0;
            $where = "";
            if ($act_id > 0) {
                $where .= " and find_in_set('" . $act_id . "', group_nav)";
            }
            $array = $ims->load_data->data_table(
                    $mod, 'item_id', 'item_id, title, friendly_link', "lang='" . $ims->conf['lang_cur'] . "' and is_show=1 " . $where . "  order by show_order desc, date_create asc"
            );
            if (count($array) <= 0) {
                return '';
            }
        }
        $arr_cur = (isset($ims->conf['cur_item']) && $ims->conf['cur_item'] > 0) ? array($ims->conf['cur_item']) : array();
        $output = '';
        $menu_sub = '';
        $num = count($array);
        if ($num) {
            $i = 0;
            foreach ($array as $row) {
                if ($i > 10) {
                    break;
                }
                $i++;
                $row['link'] = $ims->func->get_link($row['friendly_link'], '');
                $row['class'] = ($mod == $ims->conf['cur_mod'] && in_array($row["item_id"], $arr_cur)) ? ' class="current"' : '';
                $arr_class_li = array();
                if ($i == 1) {
                    $arr_class_li[] = 'first';
                }
                if ($i == $num) {
                    $arr_class_li[] = 'last';
                }
                $row['class_li'] = (count($arr_class_li) > 0) ? implode(' ', $arr_class_li) : '';
                $ims->temp_html->assign('row', $row);
                $ims->temp_html->parse($temp_name . ".item.menu_sub.row");
                $menu_sub .= $ims->temp_html->text($temp_name . ".item.menu_sub.row");
                $ims->temp_html->reset($temp_name . ".item.menu_sub.row");
            }
        }
        return $menu_sub;
    }
    //=================select===============
    function list_menu_sub($temp_name, $array = array(), $arr_cur = array(), $is_recursive = 0) {
        global $ims;
        $output = '';
        $menu_sub = '';
        $num = count($array);
        if ($num) {
            $i = 0;
            foreach ($array as $row) {
                $i++;
                $row['link'] = $ims->site_func->get_link_menu($row['link'], $row['link_type']);
                $row['class'] = (in_array($row["menu_id"], $arr_cur)) ? ' class="current"' : '';
                $arr_class_li = array();
                if ($i == 1) {
                    $arr_class_li[] = 'first';
                }
                if ($i == $num) {
                    $arr_class_li[] = 'last';
                }
                $row['class_li'] = (count($arr_class_li) > 0) ? implode(' ', $arr_class_li) : '';
                $row['menu_sub'] = '';
                if ($row['auto_sub'] == 'group') {
                    $row['menu_sub'] .= $this->list_menu_sub_group($temp_name, $row['name_action']);
                }
                if ($row['auto_sub'] == 'item') {
                    $row['menu_sub'] .= $this->list_menu_sub_item($temp_name, $row['name_action']);
                }
                
                if ($row['menu_sub']) {
                    $ims->temp_html->reset($temp_name . ".item.menu_sub");
                    $ims->temp_html->assign('row', array('content' => $row['menu_sub']));
                    $ims->temp_html->parse($temp_name . ".item.menu_sub");
                    $row['menu_sub'] = $ims->temp_html->text("menu.item.menu_sub");
                    $ims->temp_html->reset($temp_name . ".item.menu_sub");
                }
                
                if (isset($row['arr_sub'])) {
                    $row['menu_sub'] .= $this->list_menu_sub($temp_name, $row['arr_sub'], $arr_cur, 1);
                }                
                $ims->temp_html->assign('row', $row);
                $ims->temp_html->parse($temp_name . ".item.menu_sub.row");
                $menu_sub .= $ims->temp_html->text($temp_name . ".item.menu_sub.row");
                $ims->temp_html->reset($temp_name . ".item.menu_sub.row");
            }
            if ($is_recursive == 1) {
                $ims->temp_html->reset($temp_name.".item.menu_sub");
                $ims->temp_html->assign('row', array('content' => $menu_sub));
                $ims->temp_html->parse($temp_name.".item.menu_sub");
                $menu_sub = $ims->temp_html->text($temp_name.".item.menu_sub");
                $ims->temp_html->reset($temp_name.".item.menu_sub");
            }
        }
        /* $ims->temp_html->reset("menu.item.menu_sub");
          $ims->temp_html->assign('row', array('content' => $menu_sub));
          $ims->temp_html->parse("menu.item.menu_sub");
          $output = $ims->temp_html->text("menu.item.menu_sub");
          $ims->temp_html->reset("menu.item.menu_sub"); */
        return $menu_sub;
    }
    //=================list_menu===============
    function list_menu($group_id = 'menu_header', $temp_name = 'menu') {
        global $ims;
        $ims->load_data->data_menu();
        $arr_cur = array();
        $str_cur = '';
        $menu_action = (isset($ims->conf['menu_action'])) ? $ims->conf['menu_action'] : '';
        if (is_array($menu_action)) {
            foreach ($menu_action as $value) {
                $arr_menu_action = (isset($ims->data['menu_action'][$group_id][$value])) ? $ims->data['menu_action'][$group_id][$value] : array();
                $str_cur .= (!empty($str_cur)) ? ',' : '';
                $str_cur .= (isset($arr_menu_action["menu_nav"])) ? $arr_menu_action["menu_nav"] : '';
            }
            $arr_cur = (!empty($str_cur)) ? explode(',', $str_cur) : array();
        } else {
            $arr_menu_action = (isset($ims->data['menu_action'][$group_id][$menu_action])) ? $ims->data['menu_action'][$group_id][$menu_action] : array();
            $arr_cur = (isset($arr_menu_action["menu_nav"])) ? explode(',', $arr_menu_action["menu_nav"]) : array();
        }
        $arr_cur = array_unique($arr_cur);
        $output = '';
        if (isset($ims->data["menu_tree_" . $group_id]) && count($ims->data["menu_tree_" . $group_id]) > 0) {
            $menu_sub = '';
            $menu_more_tree = array();
            $num = count($ims->data["menu_tree_" . $group_id]);
            $i = 0;
            // print_arr($ims->data["menu_tree_" . $group_id]);
            foreach ($ims->data["menu_tree_" . $group_id] as $row) {
                $i++;
                $row['icon'] = ($row['picture']) != '' ? '<div class="icon"><img src="'.$ims->func->get_src_mod($row['picture']).'"></div>' : '';
                $row['link'] = ($row['link'] != '') ? $ims->site_func->get_link_menu($row['link'], $row['link_type']) : $ims->site_func->get_link('');
                $row['class'] = (isset($row['class'])) ? $row['class'] : '';
                $row['class'] = (in_array($row["menu_id"], $arr_cur)) ? 'current' : $row['class'];
                $row['target'] = ($row['target']=="_self")?'':$row['target'];
                $row['gtitle'] = '<a href="'.$row['link'].'">'.$row['title'].'</a>';
                $arr_class_li = array();
                if ($i == 1) {
                    $arr_class_li[] = 'first';
                }
                if ($i == $num) {
                    $arr_class_li[] = 'last';
                }
                //$row['attr_menu_li'] = ' style="width:'.(100/$num).'%;"';
                $row['menu_sub'] = '';
                if ($row['auto_sub'] == 'group') {
                    $row['menu_sub'] .= $this->list_menu_sub_group($temp_name, $row['name_action']);
                }
                if ($row['auto_sub'] == 'item') {
                    $row['menu_sub'] .= $this->list_menu_sub_item($temp_name, $row['name_action']);
                }
                if (isset($row['arr_sub'])) {
                    $row['menu_sub'] .= $this->list_menu_sub($temp_name, $row['arr_sub'], $arr_cur);
                }
                if ($row['menu_sub']) {
                    $row['class'] .= ' dropdown-toggle';
                    $arr_class_li[] = 'dropdown';
                    $ims->temp_html->reset($temp_name . ".item.menu_sub");
                    $ims->temp_html->assign('row', array('content' => $row['menu_sub'],'title' => $row['gtitle']));
                    $ims->temp_html->parse($temp_name . ".item.menu_sub");
                    $row['menu_sub'] = $ims->temp_html->text($temp_name . ".item.menu_sub");
                    $ims->temp_html->reset($temp_name . ".item.menu_sub");
                }
                $row['class_li'] = (count($arr_class_li) > 0) ? implode(' ', $arr_class_li) : '';

                $ims->temp_html->assign('row', $row);
                $ims->temp_html->parse($temp_name . ".item");
            }
            $ims->temp_html->reset($temp_name);
            $ims->temp_html->assign('CONF', $ims->conf);
            $ims->temp_html->parse($temp_name);
            $output = $ims->temp_html->text($temp_name);
        }
        return $output;
    }
    function menu_single($group_id = 'menu_header', $temp_name = 'menu') {
        global $ims;
        $ims->load_data->data_menu();
        if (!isset($ims->data["menu"][$group_id])) {
            return '';
        }
        $arr_cur = array();
        $menu_aciton = (isset($ims->conf['menu_action'])) ? $ims->conf['menu_action'] : '';
        if (is_array($menu_aciton)) {
            foreach ($menu_aciton as $value) {
                $arr_menu_action = (isset($ims->data['menu_action'][$group_id][$value])) ? $ims->data['menu_action'][$group_id][$value] : array();
                $arr_tmp = (isset($arr_menu_action["menu_nav"])) ? explode(',', $arr_menu_action["menu_nav"]) : array();
                //$arr_cur = array_combine($arr_cur,$arr_cur);
                $arr_tmp = array_combine($arr_tmp, $arr_tmp);
                $arr_cur = array_merge($arr_cur, $arr_tmp);
            }
        } else {
            $arr_menu_action = (isset($ims->data['menu_action'][$group_id][$menu_aciton])) ? $ims->data['menu_action'][$group_id][$menu_aciton] : array();
            $arr_cur = (isset($arr_menu_action["menu_nav"])) ? explode(',', $arr_menu_action["menu_nav"]) : array();
        }
        $arr_cur = array_unique($arr_cur);
        $output = '';
        if (count($ims->data["menu_tree_" . $group_id]) > 0) {
            $menu_sub = '';
            $num = count($ims->data["menu_tree_" . $group_id]);
            $i = 0;
            foreach ($ims->data["menu_tree_" . $group_id] as $row) {
                $i++;
                $row['link'] = $ims->site_func->get_link_menu($row['link'], $row['link_type']);
                $row['class'] = '';
                $row['class'] = (in_array($row["menu_id"], $arr_cur)) ? 'current' : '';
                $arr_class_li = array();
                if ($i == 1) {
                    $arr_class_li[] = 'first';
                }
                if ($i == $num) {
                    $arr_class_li[] = 'last';
                }
                $row['class_li'] = (count($arr_class_li) > 0) ? implode(' ', $arr_class_li) : '';
                $ims->temp_html->assign('row', $row);
                $ims->temp_html->parse($temp_name . ".item");
            }
            $ims->temp_html->reset($temp_name);
            $ims->temp_html->parse($temp_name);
            $output = $ims->temp_html->text($temp_name);
        }
        return $output;
    }
    //=================menu_footer_sub===============
    function menu_footer_sub($array = array(), $arr_cur = array()) {
        global $ims;
        $output = '';
        $menu_sub = '';
        foreach ($array as $row) {
            $row['link'] = $ims->site_func->get_link_menu($row['link'], $row['link_type']);
            $row['class'] = (in_array($row["menu_id"], $arr_cur)) ? 'current' : '';
            $row['menu_sub'] = '';
            /* if(isset($row['arr_sub'])){
              $row['menu_sub'] = $this->list_menu_sub ($row['arr_sub'], $arr_cur);
              } */
            $ims->temp_html->assign('row', $row);
            $ims->temp_html->parse("menu_footer.item.menu_sub.row");
            $menu_sub .= $ims->temp_html->text("menu_footer.item.menu_sub.row");
            $ims->temp_html->reset("menu_footer.item.menu_sub.row");
        }
        $ims->temp_html->reset("menu_footer.item.menu_sub");
        $ims->temp_html->assign('row', array('content' => $menu_sub));
        $ims->temp_html->parse("menu_footer.item.menu_sub");
        $output = $ims->temp_html->text("menu_footer.item.menu_sub");
        $ims->temp_html->reset("menu_footer.item.menu_sub");
        return $output;
    }
    function menu_footer($group_id = 'menu_header') {
        global $ims;
        $ims->load_data->data_menu();
        $arr_cur = array();
        $menu_aciton = (isset($ims->conf['menu_action'])) ? $ims->conf['menu_action'] : '';
        if (is_array($menu_aciton)) {
            foreach ($menu_aciton as $value) {
                $arr_menu_action = (isset($ims->data['menu_action'][$group_id][$value])) ? $ims->data['menu_action'][$group_id][$value] : array();
                $arr_tmp = (isset($arr_menu_action["menu_nav"])) ? explode(',', $arr_menu_action["menu_nav"]) : array();
                //$arr_cur = array_combine($arr_cur,$arr_cur);
                $arr_tmp = array_combine($arr_tmp, $arr_tmp);
                $arr_cur = array_merge($arr_cur, $arr_tmp);
            }
        } else {
            $arr_menu_action = (isset($ims->data['menu_action'][$group_id][$menu_aciton])) ? $ims->data['menu_action'][$group_id][$menu_aciton] : array();
            $arr_cur = (isset($arr_menu_action["menu_nav"])) ? explode(',', $arr_menu_action["menu_nav"]) : array();
        }
        $arr_cur = array_unique($arr_cur);
        if (isset($ims->data["menu"][$group_id])) {
            foreach ($ims->data["menu"][$group_id] as $row) {
                $arr_group_nav = explode(',', $row['menu_nav']);
                $str_code = '';
                $f = 0;
                foreach ($arr_group_nav as $tmp) {
                    $f++;
                    $str_code .= ($f == 1) ? '[' . $tmp . ']' : '["arr_sub"][' . $tmp . ']';
                }
                eval('$ims->data["menu_tree_' . $group_id . '"]' . $str_code . '["menu_id"] = $row["menu_id"];
                $ims->data["menu_tree_' . $group_id . '"]' . $str_code . '["name_action"] = $row["name_action"];
                $ims->data["menu_tree_' . $group_id . '"]' . $str_code . '["target"] = $row["target"];
                $ims->data["menu_tree_' . $group_id . '"]' . $str_code . '["title"] = $row["title"];
                $ims->data["menu_tree_' . $group_id . '"]' . $str_code . '["link_type"] = $row["link_type"];
                $ims->data["menu_tree_' . $group_id . '"]' . $str_code . '["link"] = $row["link"];');
            }
        }
        $output = '';
        if (count($ims->data["menu_tree_" . $group_id]) > 0) {
            $menu_sub = '';
            $num = count($ims->data["menu_tree_" . $group_id]);
            $i = 0;
            foreach ($ims->data["menu_tree_" . $group_id] as $row) {
                $i++;
                $row['link'] = $ims->site_func->get_link_menu($row['link'], $row['link_type']);
                $row['class'] = '';
                $row['class'] = (in_array($row["menu_id"], $arr_cur)) ? 'current' : '';
                $arr_class_li = array();
                if ($i == 1) {
                    $arr_class_li[] = 'first';
                }
                if ($i == $num) {
                    $arr_class_li[] = 'last';
                }
                $row['class_li'] = (count($arr_class_li) > 0) ? implode(' ', $arr_class_li) : '';
                $row['menu_sub'] = '';
                if (isset($row['arr_sub'])) {
                    $row['menu_sub'] = $this->list_menu_sub($row['arr_sub'], $arr_cur);
                }
                $ims->temp_html->assign('row', $row);
                $ims->temp_html->parse("menu_footer.item");
            }
            $ims->temp_html->reset("menu_footer");
            $ims->temp_html->parse("menu_footer");
            $output = $ims->temp_html->text("menu_footer");
        }
        return $output;
    }   
    //======paginate    
    function paginate($root_link, $numRows, $maxRows, $extra = "", $cPage = 1, $p = "p", $pmore = 4, $class = "pagelink") {
        global $ims;
        $root_link = (substr($root_link, -1, 1) == '/') ? substr($root_link, 0, strlen($root_link) - 1) : $root_link;
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
            //$navigation .= "<span class=\"pagetotal\">" . $totalPages . " " . $ims->lang['global']['pages'] . "</span>";
            // Show first page
            if ($cPage > ($pmore + 1)) {
                $pLink = $root_link . "/?{$p}=1{$extra}";
                $navigation .= "<a href='" . $pLink . "' class='btnPage first'><i class=\"far fa-angle-double-left\"></i></a>";
            }
            // End
            // Show Prev page
            if ($cPage > 1) {
                $numpage = $cPage - 1;
                if (!empty($extra))
                    $pLink = $root_link . "/?{$p}=" . $numpage . "{$extra}";
                else
                    $pLink = $root_link . "/?{$p}=" . $numpage;
                $navigation .= "<a href='" . $pLink . "' class='btnPage prev'><i class=\"far fa-angle-left\"></i></a>";
            }
            // End  
            // Left
            for ($i = $prev_page; $i >= 0; $i --) {
                $pagenum = $cPage - $i;
                if (($pagenum > 0) && ($pagenum < $cPage)) {
                    $pLink = $root_link . "/?{$p}={$pagenum}{$extra}";
                    $navigation .= "<a href='" . $pLink . "' class='" . $class . "'>" . $pagenum . "</a>";
                }
            }
            // End  
            // Current
            $navigation .= "<span class=\"pagecur\">" . $cPage . "</span>";
            // End
            // Right
            for ($i = 1; $i <= $next_page; $i ++) {
                $pagenum = $cPage + $i;
                if (($pagenum > $cPage) && ($pagenum <= $totalPages)) {
                    $pLink = $root_link . "/?{$p}={$pagenum}{$extra}";
                    $navigation .= "<a href='" . $pLink . "' class='" . $class . "'>" . $pagenum . "</a>";
                }
            }
            // End
            // Show Next page
            if ($cPage < $totalPages) {
                $numpage = $cPage + 1;
                $pLink = $root_link . "/?{$p}=" . $numpage . "{$extra}";
                $navigation .= "<a href='" . $pLink . "' class='btnPage next'><i class=\"far fa-angle-right\"></i></a>";
            }
            // End      
            // Show Last page
            if ($cPage < ($totalPages - $pmore)) {
                $pLink = $root_link . "/?{$p}=" . $totalPages . "{$extra}";
                $navigation .= "<a href='" . $pLink . "' class='btnPage last' ><i class=\"far fa-angle-double-right\"></i></a>";
            }
            // End
            $navigation = '<div class="paginate">' . $navigation . '</div>';
        } // end if total pages is greater than one        
        return $navigation;
    }

    //======paginate_api
    function paginate_api($root_link, $numRows, $maxRows, $extra = "", $cPage = 1, $p = "p", $pmore = 4, $class = "pagelink") {
        global $ims;
        $root_link = (substr($root_link, -1, 1) == '/') ? substr($root_link, 0, strlen($root_link) - 1) : $root_link;
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
            //$navigation .= "<span class=\"pagetotal\">" . $totalPages . " " . $ims->lang['global']['pages'] . "</span>";
            // Show first page
            if ($cPage > ($pmore + 1)) {
                $pLink = "1{$extra}";
                $navigation .= "<button data-page='" . $pLink . "' class='btnPage first'><i class=\"far fa-angle-double-left\"></i></button>";
            }
            // End
            // Show Prev page
            if ($cPage > 1) {
                $numpage = $cPage - 1;
                if (!empty($extra))
                    $pLink = $numpage . "{$extra}";
                else
                    $pLink = $numpage;
                $navigation .= "<button data-page='" . $pLink . "' class='btnPage prev'><i class=\"far fa-angle-left\"></i></button>";
            }
            // End  
            // Left
            for ($i = $prev_page; $i >= 0; $i --) {
                $pagenum = $cPage - $i;
                if (($pagenum > 0) && ($pagenum < $cPage)) {
                    $pLink = "{$pagenum}{$extra}";
                    $navigation .= "<button data-page='" . $pLink . "' class='" . $class . "'>" . $pagenum . "</button>";
                }
            }
            // End  
            // Current
            $navigation .= "<button class=\"pagecur\">" . $cPage . "</button>";
            // End
            // Right
            for ($i = 1; $i <= $next_page; $i ++) {
                $pagenum = $cPage + $i;
                if (($pagenum > $cPage) && ($pagenum <= $totalPages)) {
                    $pLink = "{$pagenum}{$extra}";
                    $navigation .= "<button data-page='" . $pLink . "' class='" . $class . "'>" . $pagenum . "</button>";
                }
            }
            // End
            // Show Next page
            if ($cPage < $totalPages) {
                $numpage = $cPage + 1;
                $pLink = $numpage . "{$extra}";
                $navigation .= "<button data-page='" . $pLink . "' class='btnPage next'><i class=\"far fa-angle-right\"></i></button>";
            }
            // End      
            // Show Last page
            if ($cPage < ($totalPages - $pmore)) {
                $pLink = $totalPages . "{$extra}";
                $navigation .= "<button data-page='" . $pLink . "' class='btnPage last' ><i class=\"far fa-angle-double-right\"></i></button>";
            }
            // End
            $navigation = '<div class="paginate">' . $navigation . '</div>';
        } // end if total pages is greater than one        
        return $navigation;
    }

    // create navigation by array
    function html_arr_navigation($arr = array()) {
        global $ims;

        $output = '';
        $i = 0;
        $num = count($arr);
        if ($num > 0) {
            foreach ($arr as $row) {
                $i++;
                $row['class'] = ($i == $num) ? ' class="current"' : '';
                $row['position'] = $i;
                $ims->temp_box->assign('row', $row);
                $ims->temp_box->parse("html_navigation.row");
            }
            $ims->temp_box->parse("html_navigation");
            $output = $ims->temp_box->text("html_navigation");
        }
        return $output;
    }

    //=================get_navigation===============
    function get_navigation($show_mod = 1, $show_group = 1, $show_item = 1, $detail_title = '') {
        global $ims;
        $ims->func->load_language('home');
        $arr_nav = array(
            array(
                'title' => $ims->lang['home']['mod_title'],
                'link' => $ims->site_func->get_link('')
            )
        );
        $arr_mod = array(
            'title' => $ims->lang[$ims->conf['cur_mod']]['mod_title'],
            'link' => $ims->site_func->get_link($ims->conf['cur_mod'])
        );

        if($show_mod == 1){
            $arr_nav = array_merge($arr_nav, array($arr_mod));
        }

        if($show_group == 1){
            $arr_group = (isset($ims->conf['cur_group']) && ($ims->conf['cur_group'] > 0) && isset($ims->conf["cur_group_nav"])) ? explode(',', $ims->conf["cur_group_nav"]) : array();
            foreach ($arr_group as $group_id) {
                if (isset($ims->data[$ims->conf['cur_mod'] . "_group"][$group_id])) {
                    $arr_nav[] = array(
                        'title' => $ims->data[$ims->conf['cur_mod'] . "_group"][$group_id]['title'],
                        'link' => $ims->site_func->get_link($ims->conf['cur_mod'], $ims->data[$ims->conf['cur_mod'] . "_group"][$group_id]['friendly_link'])
                    );
                }
            }
        }

        if($show_item == 1){
            if (isset($ims->conf['cur_item']) && $ims->conf['cur_item'] > 0) {
                $arr_nav[] = array(
                    'title' => ($detail_title != '') ? $detail_title : $ims->func->input_editor_decode($ims->data["cur_item"]['title']),
                    'link' => $ims->site_func->get_link($ims->conf['cur_mod'], '', $ims->data["cur_item"]['friendly_link']),
                    'class_li' => 'class="detail"'
                );
            }
        }
        return $this->html_arr_navigation($arr_nav);
    }
     //=================box_home===============    
    function box_banner($info = array(), $temp = 'box_banner_top'){
        global $ims;
        $output = '';
        $data = $info;
        $banner_group = $ims->db->load_item('product_group','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and group_id="'.$info['group_id'].'"','arr_picture');        
        if($banner_group){
            $banner_group = $ims->func->unserialize($banner_group);
            foreach ($banner_group as $group) {
                $row = array();
                $row['picture'] = $ims->func->get_src_mod($group);
                $ims->temp_box->assign('row',$row);
                $ims->temp_box->parse($temp.'.row');
            }
            $ims->func->include_js_content('
                $(".box_banner_product").slick({
                    slidesToShow: 1,
                    swipeToSlide: !0,
                    dots: !1,
                    arrows: !0,    
                })
            ');
            $ims->temp_box->assign('data',$data);
            $ims->temp_box->parse($temp);
            $output = $ims->temp_box->text($temp);
        }        
        return $output;
    }

    function get_banner_ads($group_id='banner-ads',$order=0){
        global $ims;
        $output = '';
        $w = $ims->data["banner_group"][$group_id]['width'];
        $h = $ims->data["banner_group"][$group_id]['height'];
        $banner_ads = $ims->db->load_row('banner','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and group_id="'.$group_id.'" and show_order="'.$order.'"','*');
        $banner_ads['title'] = $banner_ads['title']!=''?$ims->func->input_editor_decode($banner_ads['title']):'ads';
        if($banner_ads){            
            $output = '<div class="banner_ads_item"><img class="lazy" data-src="'.$ims->func->get_src_mod($banner_ads['content'],$w,$h,1,0,array('fix_width'=>1)).'" alt="'.$banner_ads['title'].'"></div>';
        }
        return $output;
    }
    //=================get_banner_slide===============
    function get_banner_slide($group_name = 'logo', $temp = 'main_slide') {
        global $ims;
        $output = '';
        $ims->temp_html->reset($temp);
        $data = array();        
        if (isset($ims->data["banner_group"][$group_name]) && isset($ims->data["banner"][$group_name])) {            
            $i=0;
            $loading = $ims->dir_images."spin.svg"; 
            foreach ($ims->data["banner"][$group_name] as $banner) {
                $i++;
                $banner['date_begin'] = $banner['date_begin'] != 0 ? $banner['date_begin'] : time();
                $banner['date_end'] = $banner['date_end'] != 0 ? $banner['date_end'] : time();                
                if($banner['date_begin'] <= time() && $banner['date_end'] >= time()){
                    $w = $ims->data["banner_group"][$group_name]['width'];
                    $h = $ims->data["banner_group"][$group_name]['height'];
                    $style = "width:" . $w . "px;";
                    $style .= ($h > 0) ? "height:" . $h . "px;overflow:hidden;" : "";
                    if ($banner['type'] == 'image') {
                        $data['banner'] = $group_name;
                        $banner['link'] = $ims->site_func->get_link_menu($banner['link'], $banner['link_type']);
                        $banner['alt'] = ($banner['title']!='')?$banner['title']:"img";
                        $banner['content_popup'] = $ims->func->get_src_mod($banner['content']);
                        $banner['picture'] = $ims->func->get_src_mod($banner['content'],$w,$h,1,0,array());
                        $banner['content_img'] = '<img src="'.$banner['picture'].'" alt="'.$banner['alt'].'" title="'.$banner['alt'].'"/>';
                        if($banner['title'] != ''){
                            $ims->temp_html->assign('row', $banner);
                            $ims->temp_html->parse($temp . ".title_more");
                        }
                        $ims->temp_html->assign('row', $banner);
                        $ims->temp_html->parse($temp . ".row");
                    }
                }
            }

            $ims->temp_html->assign('data',$data);
            $ims->temp_html->assign('CONF',$ims->conf);            
            $ims->temp_html->parse($temp);
            $output = $ims->temp_html->text($temp);
        }
        return $output;
    }
    //=================get_banner_scroll===============
    function get_banner_scroll($group_id = 'logo') {
        global $ims;
        $output = '';
        if (isset($ims->data["banner_group"][$group_id]) && isset($ims->data["banner"][$group_id])) {
            $html = '';
            $i = 0;
            foreach ($ims->data["banner"][$group_id] as $banner) {
                $i++;
                $w = (isset($ims->data["banner_group"][$group_id]['width'])) ? $ims->data["banner_group"][$group_id]['width'] : 100;
                $h = (isset($ims->data["banner_group"][$group_id]['height'])) ? $ims->data["banner_group"][$group_id]['height'] : 100;
                $banner['link'] = $ims->site_func->get_link_menu($banner['link'], $banner['link_type']);
                if ($banner['type'] == 'image') {
                    $banner['content'] = $ims->func->get_pic_mod($banner['content'], $w, $h, " alt=\"" . $banner['title'] . "\"", 1, 0, array('fix_height' => 1));
                    $html .= '<a href="' . $banner['link'] . '" target="' . $banner['target'] . '">' . $banner['content'] . '</a>';
                }
            }
            if ($i <= 7) {
                $html .= $html;
            }
            $output .= '<div class="banner_scroll"><div class="smooth_img">' . $html . '</div><div class="clear"></div></div>';
            $ims->func->include_css($ims->dir_js . 'smooth/css/smoothDivScroll.css');
            $ims->func->include_js($ims->dir_js . 'jquery_ui/jquery-ui-1.10.3.custom.min.js');
            $ims->func->include_js($ims->dir_js . 'jquery.mousewheel.min.js');
            $ims->func->include_js($ims->dir_js . 'smooth/js/jquery.smoothdivscroll-1.3-min.js');
            $ims->func->include_js_content('
                $(document).ready(function () {
                    $(".smooth_img").smoothDivScroll({
                        mousewheelScrolling: "allDirections",
                        manualContinuousScrolling: true
                    });
        
        
                    $(".smooth_img").bind("mouseover", function () {
                        $(this).smoothDivScroll("stopAutoScrolling");
                    });
        
                    $(".smooth_img").bind("mouseout", function () {
                        $(this).smoothDivScroll("startAutoScrolling");
                    });
        
                });
            ');
        }
        return $output;
    }
    //=================news_slide===============
    function news_slide($group_id = 'logo') {
        global $ims;
        $output = '';
        if (isset($ims->data["banner_group"][$group_id]) && isset($ims->data["banner"][$group_id])) {
            foreach ($ims->data["banner"][$group_id] as $banner) {
                $w = $ims->data["banner_group"][$group_id]['width'];
                $h = $ims->data["banner_group"][$group_id]['height'];
                $style = "width:" . $w . "px;";
                $style .= ($h > 0) ? "height:" . $h . "px;overflow:hidden;" : "";
                if ($banner['type'] == 'image') {
                    $banner['content'] = $ims->func->get_pic_mod($banner['content'], $w, $h, " alt=\"" . $banner['title'] . "\"", 1, 0, array('fix_width' => 1));
                }
                $banner['link'] = $ims->site_func->get_link_menu($banner['link'], $banner['link_type']);
                $banner['style'] = $style;
                $ims->temp_html->assign('row', $banner);
                $ims->temp_html->parse("news_slide.row");
            }
            $ims->func->include_css($ims->dir_skin . 'js/jquery.bxslider/jquery.bxslider.css');
            $ims->func->include_js($ims->dir_skin . 'js/jquery.bxslider/jquery.bxslider.min.js');
            $ims->temp_html->parse("news_slide");
            $output = $ims->temp_html->text("news_slide");
        }
        return $output;
    }
    //=================menu_pro_sub===============
    function menu_pro_sub($array = array()) {
        global $ims;
        $output = '';
        $arr_cur = array();
        if ($ims->conf['cur_mod'] == 'product') {
            $arr_cur = ($ims->conf['cur_group'] > 0 && isset($ims->conf["cur_group_nav"])) ? explode(',', $ims->conf["cur_group_nav"]) : array();
        }
        $menu_sub = '';
        $num = count($array);
        $i = 0;
        foreach ($array as $row) {
            $i++;
            $row['link'] = $ims->site_func->get_link('product', $row['friendly_link']);
            $class_li = array();
            if ($i == 1) {
                $class_li[] = 'first';
            }
            if ($i == $num) {
                $class_li[] = 'last';
            }
            $row['class_li'] = (count($class_li) > 0) ? ' class="' . implode(' ', $class_li) . '"' : '';
            $row['class'] = (in_array($row["group_id"], $arr_cur)) ? ' class="current"' : '';
            $row['menu_sub'] = '';
            if (isset($row['arr_sub'])) {
                $row['menu_sub'] = $this->menu_pro_sub($row['arr_sub']);
            }
            $ims->temp_box->assign('row', $row);
            $ims->temp_box->parse("box_menu.menu_sub.row");
            $menu_sub .= $ims->temp_box->text("box_menu.menu_sub.row");
            $ims->temp_box->reset("box_menu.menu_sub.row");
        }
        $ims->temp_box->reset("box_menu.menu_sub");
        $ims->temp_box->assign('data', array('content' => $menu_sub));
        $ims->temp_box->parse("box_menu.menu_sub");
        return $ims->temp_box->text("box_menu.menu_sub");
    }
    function menu_pro() {
        global $ims;
        $arr_cur = array();
        if ($ims->conf['cur_mod'] == 'product') {
            $arr_cur = ($ims->conf['cur_group'] > 0 && isset($ims->conf["cur_group_nav"])) ? explode(',', $ims->conf["cur_group_nav"]) : array();
        }
        $ims->load_data->data_group('product');
        $output = '';
        if ($num = count($ims->data["product_group_tree"]) > 0) {
            $data = array(
                'title' => $ims->lang['global']['menu_product'],
                'content' => ''
            );
            $menu_sub = '';
            $i = 0;
            foreach ($ims->data["product_group_tree"] as $row) {
                $i++;
                $row['link'] = $ims->site_func->get_link('product', $row['friendly_link']);
                $class_li = array();
                $class_li[] = 'menu_li';
                if ($i == 1) {
                    $class_li[] = 'first';
                }
                if ($i == $num) {
                    $class_li[] = 'last';
                }
                $row['class_li'] = (count($class_li) > 0) ? ' class="' . implode(' ', $class_li) . '"' : '';
                $row['class'] = (in_array($row["group_id"], $arr_cur)) ? 'current' : '';
                $row['class'] = ' class="menu_link ' . $row['class'] . '"';
                $row['menu_sub'] = '';
                if (isset($row['arr_sub'])) {
                    $row['menu_sub'] = $this->menu_pro_sub($row['arr_sub']);
                }
                $ims->temp_box->assign('row', $row);
                $ims->temp_box->parse("box_menu.menu_sub.row");
                $menu_sub .= $ims->temp_box->text("box_menu.menu_sub.row");
                $ims->temp_box->reset("box_menu.menu_sub.row");
            }
            $ims->temp_box->reset("box_menu.menu_sub");
            $ims->temp_box->assign('data', array('content' => $menu_sub));
            $ims->temp_box->parse("box_menu.menu_sub");
            $ims->temp_box->assign('data', $data);
            $ims->temp_box->parse("box_menu");
            $output = $ims->temp_box->text("box_menu");
        }
        return $output;
    }
    function box_product_focus($num_show = 1) {
        global $ims;
        $output = '';
        $temp = 'product_focus';
        $pic_w = 128;
        $pic_h = 128;
        $sql = "select picture,price,price_buy,title,friendly_link 
                        from product 
                        where is_show=1 
                        and is_focus1=1 
                        and lang='" . $ims->conf['lang_cur'] . "'
                        order by show_order desc, date_update desc 
                        limit 0," . $num_show;
        //echo $sql;
        $result = $ims->db->query($sql);
        if ($num = $ims->db->num_rows($result)) {
            $i = 0;
            while ($row = $ims->db->fetch_row($result)) {
                $i++;
                $row['link'] = $ims->site_func->get_link('product', '', $row['friendly_link']);
                $row['picture'] = $ims->func->get_src_mod($row['picture'], $pic_w, $pic_h, 1, 1);
                if ($row['price'] > $row['price_buy'] && $row['price_buy'] > 0) {
                    $row['price'] = $ims->func->get_price_format($row['price']);
                    $ims->temp_box->assign('row', $row);
                    $ims->temp_box->parse($temp . '.row.price');
                }
                $row['price_buy'] = $ims->func->get_price_format($row['price_buy']);
                $ims->temp_box->assign('row', $row);
                $ims->temp_box->parse($temp . '.row');
            }
            $ims->temp_box->reset($temp);
            //$ims->temp_box->assign('data', $data);
            $ims->temp_box->parse($temp);
            $output = $ims->temp_box->text($temp);
            $nd = array(
                'class_box' => 'box_product_focus',
                'title' => $ims->lang['global']['product_focus'],
                'content' => $output
            );
            $output = $ims->html->temp_box("box", $nd);
        }
        return $output;
    }
    /**
     * @global type $ims
     * @param type $num_show
     * @return type
     */
    function box_news_hot($num_show = 1){
         global $ims;
        $output = '';
        $temp = 'news_hot_first';
        $pic_w = 280;
        $pic_h = 172;
        $where = "";
        if ($ims->conf['cur_mod'] == 'news' && isset($ims->data['cur_group']['group_nav'])) {
            $where .= " and find_in_set(group_id, '" . $ims->data['cur_group']['group_nav'] . "')";
        }
        $sql = "select picture,title,friendly_link 
                        from news 
                        where is_show=1 
                        and lang='" . $ims->conf['lang_cur'] . "' 
                  and is_focus = 1 
                  " . $where . "
                        order by date_update desc 
                        limit 0," . $num_show;
        $result = $ims->db->query($sql);
        if ($num = $ims->db->num_rows($result)) {
            $i = 0;
            while ($row = $ims->db->fetch_row($result)) {
                $i++;
                $row['link'] = $ims->site_func->get_link('news', '', $row['friendly_link']);
                if($i == 1){
                    $row["picture"] = $ims->func->get_src_mod($row["picture"], $pic_w, $pic_h, 1, 0, array('fix_max' => '1'));
                    $ims->temp_box->assign('row', $row);
                    $ims->temp_box->parse($temp . '.row_first');
                }
                else{
                    $ims->temp_box->assign('row', $row);
                    $ims->temp_box->parse($temp . '.row');
                }
            }
            $ims->temp_box->reset($temp);
            //$ims->temp_box->assign('data', $data);
            $ims->temp_box->parse($temp);
            $output = $ims->temp_box->text($temp);
            $nd = array(
                'class_box' => 'box_news_focus hot',
                'title' => $ims->lang['global']['news_focus_hot'],
                'content' => $output
            );
            $output = $ims->html->temp_box("box", $nd);
        }
        return $output;
    }
    function box_news($num_show = 1) {
        global $ims;
        $output = '';
        $temp = 'news_view_desc';
        $pic_w = 130;
        $pic_h = 80;
        $where = "";
        if ($ims->conf['cur_mod'] == 'news' && isset($ims->data['cur_group']['group_nav'])) {
            $where .= " and find_in_set(group_id, '" . $ims->data['cur_group']['group_nav'] . "')";
        }
        $sql = "select picture,title,friendly_link 
                        from news 
                        where is_show=1 
                        and lang='" . $ims->conf['lang_cur'] . "' 
                  and num_view>0 
                  " . $where . "
                        order by num_view desc, date_update desc 
                        limit 0," . $num_show;
        // echo $sql;
        $result = $ims->db->query($sql);
        if ($num = $ims->db->num_rows($result)) {
            $i = 0;
            while ($row = $ims->db->fetch_row($result)) {
                $i++;
                $row['link'] = $ims->site_func->get_link('news', '', $row['friendly_link']);
                $row["picture"] = $ims->func->get_src_mod($row["picture"], $pic_w, $pic_h, 1, 0, array('fix_max' => '1'));
                $ims->temp_box->assign('row', $row);
                $ims->temp_box->parse($temp . '.row');
            }
            $ims->temp_box->reset($temp);
            $ims->temp_box->parse($temp);
            $output = $ims->temp_box->text($temp);
            $nd = array(
                'class_box' => 'news_view_desc',
                'title' => $ims->lang['global']['news_focus'],
                'content' => $output
            );
            $output = $ims->html->temp_box("box", $nd);
        }
        return $output;
    }
    function do_promo_event(){
        global $ims;
        $output = '';
        $arr_promo = $ims->db->load_item_arr('product_promotion','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and time_begin < "'.date('H:i:s').'" AND time_end > "'.date('H:i:s').'" AND date_begin < "'.time().'" AND date_end > "'.time().'"','title,friendly_link');
        if($arr_promo){
            foreach ($arr_promo as $row) {
                $row['title'] = $ims->func->input_editor_decode($row['title']);
                $row['link'] = $ims->site_func->get_link('product',$row['friendly_link']);
                $ims->temp_box->assign('row',$row);
                $ims->temp_box->parse('list_promo_slider.row');
            }
            $ims->temp_box->parse('list_promo_slider');
            $output .= $ims->temp_box->text('list_promo_slider');
        }
        return $output;
    }
    function product_focus($num_show = 1) {
        global $ims;
        $output = '';
        $temp = 'footer_product';
        $pic_w = 128;
        $pic_h = 128;
        $sql = "select picture,price,price_buy,title,friendly_link 
                        from product 
                        where is_show=1 
                        and is_focus1=1 
                        and lang='" . $ims->conf['lang_cur'] . "'
                        order by show_order desc, date_update desc 
                        limit 0," . $num_show;
        //echo $sql;
        $result = $ims->db->query($sql);
        if ($num = $ims->db->num_rows($result)) {
            $i = 0;
            while ($row = $ims->db->fetch_row($result)) {
                $i++;
                $row['link'] = $ims->site_func->get_link('product', '', $row['friendly_link']);
                //$row['picture'] = $ims->func->get_src_mod($row['picture'], $pic_w, $pic_h, 1, 1);
                if ($row['price'] > $row['price_buy'] && $row['price_buy'] > 0) {
                    $row['price'] = $ims->func->get_price_format($row['price']);
                    $ims->temp_box->assign('row', $row);
                    $ims->temp_box->parse($temp . '.row.price');
                }
                $row['price_buy'] = $ims->func->get_price_format($row['price_buy']);
                $ims->temp_box->assign('row', $row);
                $ims->temp_box->parse($temp . '.row');
            }
            $ims->temp_box->reset($temp);
            //$ims->temp_box->assign('data', $data);
            $ims->temp_box->parse($temp);
            $output = $ims->temp_box->text($temp);
        }
        return $output;
    }
    function footer_news($num_show = 1) {
        global $ims;
        $output = '';
        $temp = 'footer_news';
        $pic_w = 128;
        $pic_h = 128;
        $sql = "select picture,title,friendly_link,short,date_update 
                        from news 
                        where is_show=1 
                        and lang='" . $ims->conf['lang_cur'] . "'
                        order by date_update desc 
                        limit 0," . $num_show;
        //echo $sql; die;
        $result = $ims->db->query($sql);
        if ($num = $ims->db->num_rows($result)) {
            $i = 0;
            while ($row = $ims->db->fetch_row($result)) {
                $i++;
                $row['link'] = $ims->site_func->get_link('news', '', $row['friendly_link']);
                //$row['picture'] = $ims->func->get_src_mod($row['picture'], $pic_w, $pic_h, 1, 1);
                $row['day'] = date('d', $row['date_update']);
                $row['month'] = $ims->lang['global']['month_' . date('m', $row['date_update'])];
                $row['year'] = date('Y', $row['date_update']);
                $row['short'] = $ims->func->short($row['short'], 100);
                $ims->temp_box->assign('row', $row);
                $ims->temp_box->parse($temp . '.row');
            }
            $ims->temp_box->reset($temp);
            //$ims->temp_box->assign('data', $data);
            $ims->temp_box->parse($temp);
            $output = $ims->temp_box->text($temp);
        }
        return $output;
    }
    function scroll_new_promotion($num_show = 1, $type = 'promotion', $title = '') {
        global $ims;
        $output = '';
        $temp = 'news_scroll';
        $pic_w = 360;
        $pic_h = 360;
        $has_price = 0;
        $where = "";
        $order_by = "";
        $data = array();
        switch ($type) {
            case 'other':
                $order_by = "order by date_update desc";
                break;
            case 'promotion':
                $group_info_focus = $ims->db->load_row('news_group', " is_focus = 1 ");
                $group_id = isset($group_info_focus['group_id']) ? $group_info_focus['group_id'] : 0;
                $data['title'] =  isset($group_info_focus['title']) ? $group_info_focus['title'] : $ims->lang['global']['title_deal'];
                $order_by = " and '".time()."' < date_end order by date_begin asc, date_update desc";
                break;
            case 'is_focus':
                $order_by = "and is_focus = 1 order by date_update desc";
                break;
            default:
                break;
        }
        $sql = "select *
                        from product_promotion
                        where is_show=1 
                        and lang='" . $ims->conf['lang_cur'] . "' 
                  " . $where . " 
                        " . $order_by . " 
                        limit 0," . $num_show;
        // echo $sql;die;
        $result = $ims->db->query($sql);
        if ($num = $ims->db->num_rows($result)) {
            $i = 0;
            while ($row = $ims->db->fetch_row($result)) {
                $i++;
                $ims->site_func->addLoaded('news', $row['item_id']);
                $row['link'] = $ims->site_func->get_link('news', '', $row['friendly_link']);
                $pic_w = 288;
                $pic_h = 195;
                $row['picture'] = $ims->func->get_src_mod($row['picture'], $pic_w, $pic_h, 1, 0, array('fix_width' => '1'));
                $row['short'] = $ims->func->short($row['content'], 170);
                $ims->temp_box->assign('row', $row);
                $ims->temp_box->parse($temp . ".row");
            }
            $ims->func->include_css($ims->dir_js . 'owl.carousel.2/assets/owl.theme.default.css');
            $ims->func->include_css($ims->dir_js . 'owl.carousel.2/assets/owl.carousel.css');
            $ims->func->include_css($ims->dir_js . 'owl.carousel.2/assets/owl.animate.css');
            $ims->func->include_js($ims->dir_js . 'owl.carousel.2/owl.carousel.js');
           
            $ims->func->include_js_content("
                $('.news_scroll_content').owlCarousel({
                    items: 1,
                    autoplay: true,
                    smartSpeed: 800,
                    autoplayTimeout: 3000,
                    autoplayHoverPause: true,
                    loop: false,
                    animateOut: 'fadeOut',
                    animateIn: 'fadeIn',
                    margin: 18,
                    dots: false,
                    nav: false,
                })");
        
        $ims->temp_box->assign('data', $data);
        $ims->temp_box->parse($temp);
        $output = $ims->temp_box->text($temp);
        }
        else{
           return $ims->site->get_banner ('banner-promotion');
        }  
        return $output;
    }
    function product_scroll($num_show = 1, $type = 'new', $title = '') {
        global $ims;
        $output = '';
        $temp = 'product_scroll';
        $pic_w = 360;
        $pic_h = 360;
        $has_price = 0;
        $where = "";
        $order_by = "";
        switch ($type) {
            case 'other':
                $order_by = "order by date_update desc";
                break;
            case 'new':
                $order_by = "and is_new = 1 order by date_update desc";
                break;
            case 'is_focus':
                $order_by = "and is_focus = 1 order by date_update desc";
                break;
            default:
                break;
        }
        $sql = "select *
                        from product 
                        where is_show=1 
                        and lang='" . $ims->conf['lang_cur'] . "' 
                  " . $where . " 
                        " . $order_by . " 
                        limit 0," . $num_show;
        // echo $sql;die;
        $result = $ims->db->query($sql);
        if ($num = $ims->db->num_rows($result)) {
            $i = 0;
            while ($row = $ims->db->fetch_row($result)) {
                $i++;
                $row['link'] = $ims->site_func->get_link('product', '', $row['friendly_link']);
                $row["picture_zoom"] = $ims->func->get_src_mod($row["picture"]);
                $row['picture'] = $ims->func->get_src_mod($row['picture'], $pic_w, $pic_h, 1, 0, array('fix_max' => '1'));
                $rate = $this->rate_average($row['item_id']);
                if(!empty($rate)){
                    if($rate['num'] > 0){
                        if($rate['average'] > 0){
                            for ($i=0; $i < $rate['average']; $i++) { 
                                $row['average'] = "<img class='star_img' src= '".$ims->dir_images ."star.png' alt='".$rate['average']." sao' title ='".$rate['average']." sao'/>";
                                $ims->temp_box->assign('row', $row);
                                $ims->temp_box->parse("mod_item.rate.star");
                            }
                        }
                        $row['num_rate'] = "<span>(".  $rate['num'] .")</span>";
                    }
                    else{
                        for ($i=0; $i < 5; $i++) { 
                            $row['average'] = "<img class='star_img' src= '".$ims->dir_images ."no_star.png' alt='".$rate['average']." sao' title ='".$rate['average']." sao'/>";
                            $ims->temp_box->assign('row', $row);
                            $ims->temp_box->parse("mod_item.rate.star");
                        }
                        $row['num_rate'] = "";
                    }
                    $ims->temp_box->assign('row', $row);
                    $ims->temp_box->parse("mod_item.rate");
                }
                $ims->temp_box->reset("mod_item");
                // CHECK PROMOTION
                
                $value_price_buy = $ims->site_func->get_price_promotion($row);
                $row['price_buy'] = $ims->func->get_price_format($value_price_buy['price_buy']);
                if($value_price_buy['price_buy'] != $row['price_sale']){
                    $row['sale'] =  'sale_now';
                    $row['class_price_buy'] = 'none';
                    $row['price_sale'] = $ims->func->get_price_format($row['price_sale']);
                    $ims->temp_box->assign('row', $row);
                    $ims->temp_box->parse("mod_item.price_promotion");
                }
                if($value_price_buy['price_buy'] != $row['price']){
                    $row['price'] = $ims->func->get_price_format($row['price']);
                    $row['class_price'] =  'right_b';
                    $row['ribbon'] =  'sale';
                    $ims->temp_box->assign('row', $row);
                    $ims->temp_box->parse('mod_item.price');
                    $has_price++;
                }else{
                    $row['ribbon'] =  'none';
                }
                if(isset($value_price_buy['short']) && $value_price_buy['short'] != ''){
                    $row['short_promotion'] = $value_price_buy['short'];
                }
                if(isset($value_price_buy['content']) && $value_price_buy['content'] != ''){
                    $row['short'] = $ims->func->short($value_price_buy['content'], 400);
                }
                $row["link_cart"] = $ims->site_func->get_link_popup('product', 'cart', array('item_id' => $row['item_id']));
                $check_cart = $ims->site_func->check_product_combine($row);
                if($check_cart != 0){
                    $row["link_cart"] = $ims->site_func->get_link('product', '', $row['friendly_link']);
                    $row["link_go"] = "data-go = ".$ims->site_func->get_link('product', '', $row['friendly_link'])." ";
                    
                }
                $ims->temp_box->assign('LANG', $ims->lang);
                $ims->temp_box->assign('row', $row);
                $ims->temp_box->parse("mod_item");
                $row['content'] = $ims->temp_box->text("mod_item");
                $ims->temp_box->assign('row', $row);
                $ims->temp_box->parse($temp . '.row');
            }
            $ims->func->include_css($ims->dir_js . 'owl.carousel.2/assets/owl.theme.default.css');
            $ims->func->include_css($ims->dir_js . 'owl.carousel.2/assets/owl.carousel.css');
            $ims->func->include_css($ims->dir_js . 'owl.carousel.2/assets/owl.animate.css');
            $ims->func->include_js($ims->dir_js . 'owl.carousel.2/owl.carousel.js');
            $data = array();
            if ($has_price > 0) {
                $data['class'] = 'has_price';
            }
            $ims->temp_box->reset($temp);
            $ims->temp_box->assign('data', $data);
            $ims->temp_box->parse($temp);
            $output = $ims->temp_box->text($temp);
            
            $ims->func->include_js_content("
                $('.product_scroll').owlCarousel({
                    autoplay: true,
                    smartSpeed: 800,
                    autoplayTimeout: 3000,
                    autoplayHoverPause: true,
                    loop: true,
                    margin: 18,
                    dots: false,
                    nav: true,
                    responsive: {
                        0: {
                            items: 2
                        },
                        400: {
                            items: 2
                        },
                        600: {
                            items: 3
                        },
                        750: {
                            items: 3
                        },
                        1000: {
                            items: 4
                        },
                        1300: {
                            items: 5
                        }
                    }
                })");
            $nd = array(
                'title' => $title,
                'content' => $output
            );
            $output = $ims->html->temp_box("box_mid", $nd);
        }
        return $output;
    }
    function rate_average ($item_id = '') {
        global $ims;
        
        $output = array(
            'num' => 0,
            'average' => 0
        );
        $col = array();
        $total_rate = 0;
        // $sql = "SELECT * FROM shared_rate WHERE type='product' and type_id = '".$item_id."' AND is_show = 1 and lang='".$ims->conf['lang_cur']."' ";
        $sql = "SELECT * FROM shared_comment WHERE type='product' and type_id = '".$item_id."' AND is_show = 1 and lang='".$ims->conf['lang_cur']."' and rate!=0";
        $query = $ims->db->query($sql);
        $output['num'] = $ims->db->num_rows($query);
        while ($row = $ims->db->fetch_row($query)) {
            $total_rate += $row['rate'];
        }
        if($total_rate != 0){
            $output['average'] = round($total_rate/$output['num'], 1);
            $col['num_rate'] = $output['average'];
            $ims->db->do_update("product", $col, " item_id='".$item_id."'");
        }

        return $output;
    }
    function box_page_focus($num_show = 1) {
        global $ims;
        $output = '';
        $pic_w = 84;
        $pic_h = 69;
        $sql = "select picture,title,friendly_link 
                        from page 
                        where is_show=1 
                        and lang='" . $ims->conf['lang_cur'] . "'
                        and is_focus=1 
                        order by show_order desc, date_update desc 
                        limit 0," . $num_show;
        //echo $sql;
        $result = $ims->db->query($sql);
        if ($num = $ims->db->num_rows($result)) {
            $output .= '<ul class="list_none">';
            $i = 0;
            while ($row = $ims->db->fetch_row($result)) {
                $i++;
                $row['link'] = $ims->site_func->get_link('page', '', $row['friendly_link']);
                $row['picture'] = $ims->func->get_src_mod($row['picture'], $pic_w, $pic_h, 1, 0, array('fix_min' => 1));
                $class = ($i == 1) ? ' class="first"' : '';
                $output .= '<li ' . $class . '><a href="' . $row['link'] . '">' . $row['title'] . '</a>     </li>';
            }
            $output .= '</ul>';
            //$output .= '<div class="view_more"><a href="'.$ims->site_func->get_link('page').'"><img src="'.$ims->dir_images.'view_more.gif" alt="Xem thêm" /></a></div>';
            $nd = array(
                'class_box' => 'box_page_focus',
                'title' => $ims->lang['global']['page_focus'],
                'content' => $output
            );
            $output = $ims->html->temp_box("box", $nd);
        }
        return $output;
    }
    function box_page_footer($num_show = 1) {
        global $ims;
        $output = '';
        $pic_w = 275;
        $pic_h = 275;
        $sql = "select picture,title,content,friendly_link 
                        from page 
                        where is_show=1 
                        and lang='" . $ims->conf['lang_cur'] . "'
                        and is_focus1=1 
                        order by show_order desc, date_update desc 
                        limit 0," . $num_show;
        //echo $sql;
        $result = $ims->db->query($sql);
        if ($num = $ims->db->num_rows($result)) {
            $output .= '<div id="footer_page_focus"><ul class="list_none">';
            $i = 0;
            while ($row = $ims->db->fetch_row($result)) {
                $i++;
                $row['link'] = $ims->site_func->get_link('page', '', $row['friendly_link']);
                $row['picture'] = $ims->func->get_src_mod($row['picture'], $pic_w, $pic_h, 1, 1);
                $row['title'] = $ims->func->short($row['title'], 40);
                $row['short'] = $ims->func->short($row['content'], 250);
                $class = ($i == 1) ? ' class="first"' : '';
                $output .= '<li ' . $class . '>
                    <a href="' . $row['link'] . '">
                        <div class="img">
                            <img src="' . $row['picture'] . '" alt="' . $row['title'] . '" title="' . $row['title'] . '"/>
                            <h3><div class="limit"><table width="100%" height="100%" cellpadding="0" cellspacing="0" border="0"><tr><td width="100%" height="100%">' . $row['title'] . '</td></tr></table></div></h3>
                        </div>
                        <div class="short">' . $row['short'] . '</div>
                        <span class="view_detail">' . $ims->lang['global']['view_detail'] . '</span>
                    </a>
                </li>';
            }
            $output .= '</ul><div class="clear"></div></div>';
        }
        return $output;
    }
    // box_support
    function box_support_old() {
        global $ims;
        $data = array(
            'title' => $ims->lang['global']['support'],
            'link_support' => $ims->conf['rooturl'] . 'support.php/lang=' . $ims->conf['lang_cur']
        );
        $output = $ims->html->temp_box('box_support', $data);
        return $output;
    }
    //=================box_support===============
    function box_support() {
        global $ims;
        $output = '';
        $ims->temp_box->reset("box_support");
        $arr_support = $ims->db->load_item_arr('support','lang="'.$ims->conf['lang_cur'].'" and is_show=1 order by show_order desc, date_update asc','item_id,title,phone,picture');
        if($arr_support){
            foreach ($arr_support as $row) {
                $row['title'] = $ims->func->input_editor_decode($row['title']);
                $row['picture'] = $ims->func->get_src_mod($row['picture']);
                $ims->temp_box->assign('row', $row);                    
                $ims->temp_box->parse("box_support.row.phone");
            }
            $ims->temp_box->assign('row', $row);
            $ims->temp_box->parse("box_support.row");
        }        
        $output .= $ims->html->temp_box("box_support", array(
                                            'title' => $ims->lang['global']['online_support'],
                                        ));        
        return $output;
    }
    // box_support
    function box_support2(){
        global $ims;
        $output = '';
        $data = array(
            'title' => $ims->lang['global']['box_support_title'],
        );
        $data['support'] = $this->get_banner('support');
        $data['social_title'] = $ims->lang['global']['social_box_title'];
        foreach(array('facebook', 'skype') as $v) {
            if(isset($ims->conf['social_'.$v.'_link']) && $ims->conf['social_'.$v.'_link']) {
                $data['social_'.$v.'_link'] = $ims->conf['social_'.$v.'_link'];
            }
        }
        foreach(array('facebook', 'skype') as $t) {
            if(isset($ims->conf['social_'.$t.'_title']) && $ims->conf['social_'.$t.'_title']) {
                $data['social_'.$t.'_title'] = $ims->conf['social_'.$t.'_title'];
            }
        }
        foreach(array('zalo', 'viber') as $n) {
            if(isset($ims->conf['social_'.$n.'_tel']) && $ims->conf['social_'.$n.'_tel']) {
                $data['social_'.$n.'_tel'] = $ims->conf['social_'.$n.'_tel'];
            }
        }
        $ims->temp_box->assign('data', $data);
        $ims->temp_box->parse("box_support2");
        $output .= $ims->html->temp_box("box_support2", $data);
        return $output;
    }
    // box_statistic
    function box_statistic() {
        global $ims;
        $output = $ims->html->temp_box('box_statistic', array());
        return $output;
    }
    // load_widget
    function load_widget($name) {
        global $ims;
        return $ims->func->load_widget($name);
    }
    // auto_sidebar
    function auto_sidebar($pos = 'left') {
        global $ims;
        $output = '';
        $data_setting = (isset($ims->setting[$ims->conf['cur_mod']])) ? $ims->setting[$ims->conf['cur_mod']] : array();
        $cur_group = (isset($ims->conf['cur_group'])) ? $ims->conf['cur_group'] : 0;
        $data_group = (isset($ims->data['cur_group'])) ? $ims->data['cur_group'] : array();
        $data_item = (isset($ims->data['cur_item'])) ? $ims->data['cur_item'] : array();
        if (count($data_item)) {
            $arr_group = $ims->load_data->data_group($ims->conf['cur_mod']);
            if (isset($data_item['sidebar_' . $pos]) && $data_item['sidebar_' . $pos]) {
                $output .= $ims->site_func->load_sidebar($data_item['sidebar_' . $pos]);
            } elseif (isset($arr_group[$cur_group]['sidebar_' . $pos]) && $arr_group[$cur_group]['sidebar_' . $pos]) {
                $output .= $ims->site_func->load_sidebar($arr_group[$cur_group]['sidebar_' . $pos]);
            } elseif (isset($data_setting['sidebar_item_' . $pos]) && $data_setting['sidebar_item_' . $pos]) {
                $output .= $ims->site_func->load_sidebar($data_setting['sidebar_item_' . $pos]);
            }
        } elseif (count($data_group)) {
            if (isset($data_group['sidebar_' . $pos]) && $data_group['sidebar_' . $pos]) {
                $output .= $ims->site_func->load_sidebar($data_group['sidebar_' . $pos]);
            } elseif (isset($data_setting['sidebar_group_' . $pos]) && $data_setting['sidebar_group_' . $pos]) {
                $output .= $ims->site_func->load_sidebar($data_setting['sidebar_group_' . $pos]);
            }
        } elseif (isset($data_setting['sidebar_' . $pos]) && $data_setting['sidebar_' . $pos]) {
            $output .= $ims->site_func->load_sidebar($data_setting['sidebar_' . $pos]);
        }
        return $output;
    }
    // block_left
    function block_left() {
        global $ims;
        $output = '';
//        if($ims->conf['cur_mod'] == 'home'){
//            $output .= '<div class="box_product_group">'.$this->list_menu('menu_header_product','menu_bootstrap').'</div>';
//            $output .= $this->box_support();
//            $output .= $this->product_vertical($ims->lang['product']['hot_product'],'focus',2);
//        }
        
        if ($ims->conf['cur_mod'] == 'page') {
            $output .= $this->box_right_page();
        }
        if($ims->conf['cur_mod'] == 'advisory'){
            $output .= $this->box_advisory();
        }
        if($ims->conf['cur_mod'] == 'product'){
            $output .= '<div id="box_filter_left">'.$this->box_left_product().'</div>';
        }
        if($ims->conf['cur_mod'] == 'store'){
            $output .= $this->do_column_store();
        }
        return $output;
    }
    function list_about_section($info){
        global $ims;
        $output = '';
        $data = array();
        $info = (isset($info) && $info!='')?$info:$ims->lang['about']['mod_title'];
        $data['title'] = array(
            'content' => $ims->func->input_editor_decode($info),
            'vision' => $ims->lang['about']['vision'],
            'mission' => $ims->lang['about']['mission'],
            'philosophy' => $ims->lang['about']['title_philosophy'],
        );
        $data['link'] = array(
            'content' => 'content',
            'vision' => 'vision',
            'mission' => 'mission',
            'philosophy' => 'philosophy',
        );
        $ims->temp_box->assign('data',$data);
        $ims->temp_box->parse('list_about_section');
        $output .= $ims->temp_box->text('list_about_section');
        return $output;
    }    
    function box_menu_left($tbl,$title='',$group_id='',$arr_in = array(),$temp='box_menu_page'){
        global $ims;
        $output = $where = '';        
        $data = array(
            'title' => $title,
        );
        $ims->temp_box->reset($temp);
        if($group_id!=''){
            $where .= ' and group_id="'.$group_id.'"';
        }
        $arr_item = $ims->db->load_item_arr($tbl,'lang="'.$ims->conf['lang_cur'].'" and is_show=1 '.$where.' order by show_order desc, date_create desc','title,friendly_link,item_id');
        if($arr_item){            
            foreach ($arr_item as $row) {
                $row['title'] = $ims->func->input_editor_decode($row['title']);                    
                $row['link'] = $ims->site_func->get_link('about',$row['friendly_link']);                     
                $row['class_li'] = ($ims->conf['cur_mod']==$tbl && $ims->conf['cur_item']==$row['item_id'])?'class="active"':'';                   
                $ims->temp_box->assign('row',$row);
                $ims->temp_box->parse($temp.'.row');                
            }
            if(count($arr_in)>0){
                foreach ($arr_in as $col){                    
                    // $col['class_li'] = ($ims->site_func->get_link($col['mod'],$ims->conf['cur_mod_url'])==$col['link'])?'class="active"':'';
                    if($ims->conf['cur_mod'] == $col['mod'] ){
                        $col['class_li'] = 'class="active"';
                    }
                    $ims->temp_box->assign('row',$col);
                    $ims->temp_box->parse($temp.'.row_expand'); 
                }               
            }
            $ims->temp_box->assign('CONF',$ims->conf);
            $ims->temp_box->assign('data',$data);
            $ims->temp_box->parse($temp);
            $output .= $ims->temp_box->text($temp);
        }
        return $output;
    }

    function box_left_product(){
        global $ims;
        $data = array();
        $output = '<h2 class="title">'.$ims->lang['product']['filter_title'].'</h2><div class="list_filter">';

        // ------ product group        
        $output .= $this->box_sort_product_group();        
        // ------ Nature
        $output .= $this->box_sort_nature_left();
        // ------ box_search_rate
        $output .= $this->box_sort_rating_left();
        // ------ product price        
        $output .= $this->box_sort_price_left();   
        // ------ product_brand
        $output .= $this->box_sort_trademark_left();
        // ------ box_product
        // $output .= $this->product_vertical($ims->lang['product']['hot_product'],'focus',3);
        // ------ tag
        // $output .= $this->box_sort_tag_left();
        // ------ product_color
        // $output .= $this->box_sort_color_left();
        $output .= '</div>';
        return $output;
    }    

    function box_sort_product_group($collap=1){
        global $ims;
        $output = '';
        $data = array();
        if (isset($ims->data['cur_group'])) {
            $cur_group = $ims->data['cur_group']['group_id'];        
            $nav_id = explode(',', $ims->data['cur_group']['group_nav']);
            $data['cur_id'] = "false"; 
            $arr_product_group = $ims->db->load_item_arr('product_group','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and parent_id="'.$cur_group.'"','group_id,group_nav,title,friendly_link');

            $sum_group = count($arr_product_group);        
            if($arr_product_group){            
                foreach ($arr_product_group as $key => $group) {                
                    $group['link'] = $ims->site_func->get_link('product',$group['friendly_link']);
                    $group['action'] = '<a href="'.$group['link'].'">'.$group['title'].'</a>';                    
                    if(in_array($group['group_id'],$nav_id)){

                        $data['cur_id'] = $key;
                    }
                    $ims->temp_box->assign("item", $group);
                    $ims->temp_box->parse("product_group_left.item");                
                }
                $ims->temp_box->assign('data',$data);     
                $ims->temp_box->assign("LANG", $ims->lang);
                $ims->temp_box->parse("product_group_left");
                $output .= $ims->temp_box->text("product_group_left");
            }
        }
        return $output;
    }

    function box_sort_trademark_left($info = array()){
        global $ims;
        $output = $where = '';
        $data = array();
        // if(count($info)){
        //     $where .= " and (find_in_set('".$info['group_id']."',group_nav)>0 or find_in_set(group_id,'".$info['group_nav']."'))";
        // }
        $list_brand_group = $ims->db->load_item("product_group",'lang="'.$ims->conf['lang_cur'].'" and is_show=1 and group_id="'.$ims->conf['cur_group'].'"','list_brand');
        if($list_brand_group){
            $where .= "and find_in_set(brand_id,'".$list_brand_group."')";
            $arr_brand = $ims->db->load_row_arr("product_brand", "is_show = 1 AND lang = '".$ims->conf['lang_cur']."' ".$where." ORDER BY show_order ASC, date_create ASC");
            if($arr_brand){
                $where = '';
                if (isset($ims->conf['cur_group']) && $ims->conf['cur_group'] > 0) {
                    $where .= ' and find_in_set('.$ims->conf['cur_group'].', group_nav) ';
                }
                $check = 0;
                foreach ($arr_brand as $value) {
                    $value['num_product'] = $ims->db->do_get_num('product', " is_show = 1 AND brand_id = '".$value['brand_id']."' AND lang = '".$ims->conf['lang_cur']."' ".$where." ");                    
                    if ($value['num_product'] != 0) {
                        $check++;
                        $ims->temp_box->assign("item", $value);
                        $ims->temp_box->parse("product_trademark_left.item");
                    }
                }
                if($check > 0){
                    $ims->temp_box->assign("LANG", $ims->lang);
                    $ims->temp_box->assign("data", $data);
                    $ims->temp_box->parse("product_trademark_left");
                    $output .= $ims->temp_box->text("product_trademark_left");
                }
            }
        }
        
        return $output;
    }

    function box_sort_price_left(){
        global $ims;
        $output = '';        
        $data = array(
            'display' => 'style="display:none"',
        );
        if (isset($ims->conf['cur_group']) && $ims->conf['cur_group'] > 0) {
            $info = json_decode($this->get_arr_search_price($ims->conf['cur_group']),true);            
            $arr_search_price = $ims->func->unserialize($info['arr_search_price'], array());
            $num_row = count($arr_search_price);            
            if ($num_row) {                
                $i=0; $row=array();
                foreach ($arr_search_price as $k => $v) {
                    $i++; 
                    $row['id'] = $i;
                    $row['title'] = $ims->func->get_price_text($v);                       
                    if ($i==1) {
                        $row['value'] = '0-'.$arr_search_price[$i];
                        $row['title'] = $ims->lang['global']['below'].' '.$ims->func->get_price_text($arr_search_price[$i]);
                    } elseif (!isset($arr_search_price[$k + 1])) {
                        $row['value'] = $v.'-0';
                        $row['title'] = $ims->lang['global']['over'].' '.$ims->func->get_price_text($v);                    
                    } else {
                        $row['value'] = $v . '-' . $arr_search_price[$k + 1];
                        $row['title'] = $ims->lang['global']['from'].' '.$ims->func->get_price_text($v,'','d-none') . ' - ' . $ims->func->get_price_text($arr_search_price[$k + 1]);
                    }
                    
                    $ims->temp_box->assign("item", $row);
                    $ims->temp_box->parse("search_price_left.item");
                }
                $data['display'] = '';
            }
        }        
        //range-slider
        $data['title'] = $ims->lang['global']['choose_price'];
        $data['price_min_search'] = (isset($ims->input['price_min_search'])) ? $ims->input['price_min_search'] : (is_numeric($ims->lang['global']['min_price_slide'])?$ims->lang['global']['min_price_slide']:0);
        $data['price_max_search'] = (isset($ims->input['price_max_search'])) ? $ims->input['price_max_search'] : (is_numeric($ims->lang['global']['max_price_slide'])?$ims->lang['global']['max_price_slide']:1000000);
        $data['percent_good'] = (isset($ims->input['percent_good'])) ? $ims->input['percent_good'] : $data['price_min_search'].','.$data['price_max_search'];
        $data['range_good'] = $data['percent_good'];
        $ims->temp_box->assign("LANG", $ims->lang);
        $ims->temp_box->assign("data", $data);
        $ims->temp_box->parse("search_price_left");
        $output .= $ims->temp_box->text("search_price_left");
        return $output;
    }

    function box_sort_nature_left(){
        global $ims;
        $output = $where = '';
        $data = array();
        if (isset($ims->conf['cur_group']) && $ims->conf['cur_group'] > 0) {            
            //array nature of grouped product
            $nature_group = $ims->db->load_item('product_group','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and group_id="'.$ims->conf['cur_group'].'"','list_nature');
            if($nature_group){
                //array data group nature
                $arr_group_nature = $ims->load_data->data_table("product_nature_group", 'group_id', 'group_id, title', 'lang="'.$ims->conf['lang_cur'].'" AND is_show=1 and is_focus=1 and parent_id="'.$nature_group.'" ORDER BY show_order DESC, date_create DESC');
                //array data item nature            
                $arr_nature = $ims->db->load_item_arr("product_nature", 'lang="'.$ims->conf['lang_cur'].'" AND is_show=1 ORDER BY show_order DESC, date_create DESC','group_id,item_id,title');   
                foreach ($arr_group_nature as $row) {
                    $row['link'] = $ims->conf["rooturl"].$ims->conf["cur_mod_url"].'/?nature='.$row['group_id'];
                    if($arr_nature) {                            
                        $where_nature = ' and find_in_set('.$ims->conf['cur_group'].', group_nav) ';
                        $check = 0;
                        foreach ($arr_nature as $k_nature => $v_nature) {
                            if($v_nature['group_id'] == $row['group_id']){
                            $v_nature['num_product'] = $ims->db->do_get_num('product', " is_show = 1 AND find_in_set(".$v_nature['item_id'].", arr_nature) AND lang = '".$ims->conf['lang_cur']."' ".$where_nature." ");
                                 if ($v_nature['num_product'] > 0) {
                                     $check++;
                                     $ims->temp_box->assign("item", $v_nature);
                                     $ims->temp_box->parse("arr_group_nature_left.group.item");
                                 }
                            }
                        }
                    }
                    $ims->temp_box->assign("group", $row);
                    $ims->temp_box->parse("arr_group_nature_left.group");
                }
                if($check){
                    $ims->temp_box->assign("LANG", $ims->lang);
                    $ims->temp_box->assign("data", $data);
                    $ims->temp_box->parse("arr_group_nature_left");
                    $output .= $ims->temp_box->text("arr_group_nature_left");
                }
            }         
        }
        return $output;
    }

    function box_sort_rating_left(){
        global $ims;
        $output = '';
        $data = array();
        $arr_rate = array(
            array(
                'title' => ' (5 sao)',
                'value' => '5',
                'link' => '',
            ),
            array(
                'title' => ' (ít nhất 4 sao)',
                'value' => '4',
                'link' => '',
            ),
            array(
                'title' => ' (ít nhất 3 sao)',
                'value' => '3',
                'link' => '',
            ),
            array(
                'title' => ' (ít nhất 2 sao)',
                'value' => '2',
                'link' => '',
            ),
            array(
                'title' => ' (ít nhất 1 sao)',
                'value' => '1',
                'link' => '',
            ),
        );
        foreach ($arr_rate as $k_rate => $v_rate) {
            $text = '';
            for ($i=0; $i < $v_rate['value']; $i++) { 
                // $text .= "<img class='star_img' src= '".$ims->dir_images ."star.png' alt='".$v_rate['title']."' title ='".$v_rate['title']."'/>";
                $text .= '<i class="fas fa-star" style="color:#ffc120"></i>';
            }
            for ($i=0; $i < 5-$v_rate['value']; $i++) { 
                // $text .= "<img class='star_img' src= '".$ims->dir_images ."no_star.png' alt='".$v_rate['title']."' title ='".$v_rate['title']."'/>";
                $text .= '<i class="fas fa-star" style="color:#b8b8b8"></i>';
            }
            $v_rate['title'] = $text.$v_rate['title'];
            $v_rate['link'] = $ims->site_func->get_link('product', '', $ims->setting['product']['product_link'], array('rate' => $v_rate['value']));
            $where = '';
            if (isset($ims->conf['cur_group']) && $ims->conf['cur_group'] > 0) {
                $where .= ' and find_in_set('.$ims->conf['cur_group'].', group_id) ';
                $v_rate['link'] = $ims->site_func->get_link('product', '', $ims->data['cur_group']['friendly_link'], array('rate' => $v_rate['value']));
            }
            $ims->temp_box->assign("item", $v_rate);
            $ims->temp_box->parse("box_search_rate_left.item");
        }
        $ims->temp_box->assign("LANG", $ims->lang);
        $ims->temp_box->assign("data", $data);
        $ims->temp_box->parse("box_search_rate_left");
        $output .= $ims->temp_box->text("box_search_rate_left");
        return $output;
    }

    function box_sort_tag_left($tbl='product',$temp='box_tags_checkbox'){
        global $ims;
        $output = '';
        $arr_tag = $ims->db->load_item_arr($tbl,'lang="'.$ims->conf['lang_cur'].'" and is_show=1','tag_list');
        $data = $tag = array();
        if($arr_tag){            
            $tmp = $this->get_tag($arr_tag);
            if(!empty($tmp)){
                foreach ($tmp as $key => $row) {
                    $tag['id'] = $key;
                    $tag['title'] = $row;
                    $tag['tag_link'] = $ims->site_func->get_link($tbl).'?tag='.$tmp[$key];
                    $ims->temp_box->assign("item",$tag);
                    $ims->temp_box->parse($temp.".row");
                }
                $data['title'] = $ims->lang[$tbl]['tags'];
                $ims->temp_box->assign('data',$data);
                $ims->temp_box->parse($temp);
                $output .= $ims->temp_box->text($temp);
            }            
        }
        return $output;
    }

    function box_sort_color_left(){
        global $ims;
        $output = '';
        $row = array();        
        $arr_color = $ims->db->load_item_arr('product','lang="'.$ims->conf['lang_cur'].'" and is_show=1','list_color');
        if($arr_color){
            $temp = $this->get_color($arr_color);            
            foreach ($temp as $k => $v) {
                $row = $this->get_title_color($v,2);
                $row['id'] = $k;
                $row['color_id'] = $v;                
                $ims->temp_box->assign("item",$row);
                $ims->temp_box->parse("box_product_color_left.row");
            }
            $ims->temp_box->parse("box_product_color_left");
            $output .= $ims->temp_box->text("box_product_color_left");
        }
        return $output;
    }

    function box_origin(){
        global $ims;
        $out = '';
        $where = '';

        if(isset($ims->get['keyword']) || $ims->get['keyword'] != ''){
            $arr_key = explode(' ', $ims->get['keyword']);
            $where = '';
            if (!empty($arr_key)) {
                $arr_tmp = array();
                foreach ($arr_key as $value) {
                    $value = trim($value);
                    if (!empty($value)) {
                        $arr_tmp[] = "title LIKE '%".$value."%'";
                    }
                }
                if (count($arr_tmp) > 0) {
                    $where .= " AND (".implode(" AND ", $arr_tmp).")";
                }
            }
        }
        if(isset($ims->conf['cur_group']) && $ims->conf['cur_group'] > 0){
            $where .= ' and find_in_set('.$ims->conf['cur_group'].', group_nav)';
        }
        if(isset($ims->get['fc']) && $ims->get['fc']){
            $where .= ' and is_focus = 1 ';
        }
        if (isset($ims->get['fc1']) && $ims->get['fc1'] == 1){
            $where .= ' and is_focus1 = 1';
        }

        $list_prd_tmp = $ims->db->load_item_arr('product', $ims->conf['qr'].$where.' and origin_id > 0', 'DISTINCT origin_id');
        if($list_prd_tmp){
            $list_origin = array();
            foreach ($list_prd_tmp as $item){
                $list_origin[] = $item['origin_id'];
            }
            $list_origin = implode(',', $list_origin);
            $result = $ims->db->load_item_arr('product_origin', $ims->conf['qr'].' and item_id IN ('.$list_origin.') order by show_order desc, title asc', 'title, item_id');
            foreach ($result as $row){
                $ims->temp_box->assign("row", $row);
                $ims->temp_box->parse("box_origin.item");
            }
            $ims->temp_box->assign("title", $ims->lang['product']['origin_title']);
            $ims->temp_box->parse("box_origin");
            $out = $ims->temp_box->text("box_origin");
        }
        return $out;
    }

    // block_column
    function get_title_page_group($id=''){
        global $ims;
        $sql = "SELECT title FROM page_group WHERE is_show = 1 AND group_id = ".$id." AND lang ='".$ims->conf['lang_cur']."' ";
        $result = $ims->db->query($sql);
        $arr = $ims->db->fetch_row($result);
        return isset($arr['title']) ? $arr['title'] : '';
    }
    function get_group_id_menu($name_action=''){
        $name_action = 'page-item-'.$name_action;
        global $ims;
        $sql = "SELECT group_id FROM menu WHERE is_show = 1 AND name_action = '".$name_action."' AND lang = '".$ims->conf['lang_cur']."' ";
        $result = $ims->db->query($sql);
        $arr = $ims->db->fetch_row($result);
        return isset($arr['group_id']) ? $arr['group_id'] : '';
    }
    //-----------get_group_name
    function get_group_name($mod, $group_id, $type = 'none') {
        global $ims;

        $output = '';

        $row = $ims->db->row(array(
            'select' => array('title', 'friendly_link'),
            'from' => array($mod . '_group'),
            'where' => array(
                array('=', 'is_show', 1),
                array('=', 'lang', "'" . $ims->conf['lang_cur'] . "'"),
                array('=', 'group_id', "'" . $group_id . "'")
            ),
            'limit' => array(1)
        ));
        if (is_array($row) && count($row)) {
            switch ($type) {
                case "link":
                    $link = $ims->site_func->get_link($mod, $row['friendly_link']);
                    $output = '<a href="' . $link . '">' . $row['title'] . '</a>';
                    break;
                default:
                    $output = $row['title'];
                    break;
            }
        }

        return $output;
    }
    function block_column() {
        global $ims;
        $output = '';
        if ($ims->conf['cur_mod'] == 'about') {
            $ims->func->load_language('product');
            $output .= $this->product_vertical($ims->lang['product']['hot_product'],'focus',4);
        }
        if ($ims->conf['cur_mod'] == 'news') {
            //search
            $arr_search_news = array(
                'form_id' => 'form_search_news',
                'input' => 'news',
            );
            $output .= '<div class="box_r"><label class="title">'.$ims->lang['global']['search'].'</label>'.$this->box_search('news',$arr_search_news).'</div>';
            //list group
            $list_group = $ims->db->load_row_arr('news_group','lang="'.$ims->conf['lang_cur'].'" and is_show=1 order by show_order desc, date_create desc');
            if($list_group){
                $output .= '<div class="box_r"><label class="title">'.$ims->lang['global']['news_category'].'</label>';
                $output .= '<div class="box_news_gr" style="color:'.$ims->conf['bgheader'].'">';                
                foreach ($list_group as $row){
                    $row['link'] = $ims->site_func->get_link('news',$row['friendly_link']);
                    $output .= '<a href="'.$row['link'].'">'.$row['title'].'</a>';
                }             
                $output .= '</div>';
                // $output .= $ims->temp_box->text('news_focus');
                $output .= "</div>";
            }
            //list news
            $list_news = $ims->db->do_get_num('news','lang="'.$ims->conf['lang_cur'].'" and is_show=1 order by show_order desc, date_create desc');
            if($list_news){
                $arr_in = array(
                    'where' => ' order by show_order desc, date_create desc',
                    'num_list' => 4,
                    'paginate' => 0,
                    'temp_mod' => 'mod_item_title',
                );
                $output .= '<div class="box_r"><label class="title">'.$ims->lang['global']['latest_news'].'</label>';
                $output .= '<div class="box_news_ot" style="color:'.$ims->conf['bgheader'].'">'.$ims->call->mFunc('news','html_list_item',array($arr_in)).'</div>';
                // $output .= $ims->temp_box->text('news_focus');
                $output .= "</div>";
            }
            //fanpage
            $output .= '<div class="box_r"><label class="title">'.$ims->lang['global']['fanpage'].'</label>';
            $output .= '<div class="fb-page" 
                              data-href="'.$ims->conf['fanpage_facebook'].'"
                              data-adapt-container-width="true"
                              data-hide-cover="false"
                              data-show-facepile="true"></div>';
            $output .= "</div>";
            //tags            
            $output .= $this->box_sort_tag_left('news','box_tags_link');
            //banner
            $output .= '<div class="box_r"><div class="banner-ads">'.$this->get_banner('banner-ads-news-right',1).'</div></div>';
        }
        return $output;
    }
    function product_vertical($title='',$type='',$num_list=6){
        global $ims;
        $output = $where ='';
        $data = array(
            'title' => $title,
        );
        switch($type){
            case 'new':
                $where .= ' and is_new=1 order by show_order desc, date_create desc';
                break;
            case 'new_order':
                $where .= ' order by show_order desc, date_create desc';
                break;
            case 'focus':
                $where .= ' AND is_focus=1 order by show_order desc, date_create desc';
                break;
            default:
                $where .= '';
        }
        $arr_in = array(
            'where' => $where,
            'num_list' => 8,
            'paginate' => 0,
            'empty' => 1,
        );
        $data['content'] = $ims->call->mFunc('product','html_list_item', array($arr_in));
        if($data['content']){
            $ims->temp_box->assign('data',$data);
            $ims->temp_box->parse('box_product_column');
            $output = $ims->temp_box->text('box_product_column');
            $ims->func->include_js_content('
                var v=$(".box_product_column .row_item");v.slick({slidesToShow:'.$num_list.',swipeToSlide:!0,vertical:!0,swipe:!1,dots:!1,arrows:!1}),$(".box_product_column .btn-next").on("click",function(){v.slick("slickNext")});
            ');
        }        
        return $output;
    }
    // header_user
    function header_user() {
        global $ims;
        $ims->site_func->setting('user');
        $ims->func->load_language('user');        
        $data = array();
        $data['link_user'] = $ims->site_func->get_link('user');
        $data['link_signin'] = $ims->site_func->get_link('user', $ims->setting["user"]["signin_link"]);
        $data['link_signup'] = $ims->site_func->get_link('user', $ims->setting["user"]["signup_link"]);
//        $data['url_fb'] = "https://www.facebook.com/dialog/oauth?client_id=".app_id_facebook."&redirect_uri=".redirect_uri_facebook;
//        $data['url_gg'] = "https://accounts.google.com/o/oauth2/auth?response_type=code&redirect_uri=".redirect_uri_google."&client_id=".client_id_google."&scope=email+profile&access_type=online&approval_prompt=auto";
//        $cookie_watched = isset($_COOKIE['list_watched'])?$_COOKIE['list_watched']:'';
//        if($cookie_watched != ''){
//            $array_watched = str_replace('list=', '', $cookie_watched);
//        }
//        $data['product_watched'] = $ims->lang['global']['account'];
        $ims->temp_html->assign('LANG', $ims->lang);
        $ims->temp_html->assign('data', $data);
        if ($ims->site_func->checkUserLogin() == 1) {
            $row = (!empty($ims->data['user_cur'])) ? $ims->data['user_cur'] : array();
            $row['picture'] = $row['picture'] != '' ? '<p class="avatar"><img src="'.$ims->func->get_src_mod($row['picture'], 50, 50, 1, 1).'"></p>' : '<i class="fas fa-user-circle"></i>';
//            $full_name = explode(" ", $ims->data['user_cur']['full_name']);
//            $row['first_name'] = $full_name[count($full_name) - 1];
//            $row['notifications_link'] = $ims->site_func->get_link ("user",$ims->setting["user"]["notifications_link"]);
//            $row['num_no'] = 0;
//            if ($ims->site_func->checkUserLogin() == 1) {
//                // $sql = "SELECT * FROM user_notification WHERE is_show = 1 AND lang ='" . $ims->conf['lang_cur'] . "' and (type=0 OR find_in_set('".$ims->data['user_cur']['user_id']."', user_id)) ";
//                // $query = $ims->db->query($sql);
//                // $num_spam = $ims->db->num_rows($query);
//                $arr_noti = $ims->db->load_item_arr('user_notification', 'is_show = 1 AND lang ="' . $ims->conf['lang_cur'] . '" and (type=0 OR find_in_set("'.$ims->data['user_cur']['user_id'].'", user_id))','is_view');
//                if ($arr_noti) {
//                    // while ($row = $ims->db->fetch_row($query)) {
//                    foreach ($arr_noti as $noti) {
//                        if (!empty($noti['is_view'])) {
//                            $noti['is_view'] = explode(",", $noti['is_view']);
//                            if (!in_array($ims->data['user_cur']['user_id'], $noti['is_view'])) {
//                                $row['num_no']++;
//                            }
//                        } else {
//                            $row['num_no']++;
//                        }
//                    }
//                }
//            }else{
//                $row['num_no'] = $ims->db->do_get_num('user_notification','lang="'.$ims->conf['lang_cur'].'" AND is_show=1 AND type="0" AND "'.time().'" < date_create+(3600*24*30)');
//            }

            $arr_is_login = array(
                'user' => array(
                    'title' => $ims->setting["user"]["user_meta_title"],
                    'link' => $ims->site_func->get_link("user")
                ),
                'change_pass' => array(
                    'title' => $ims->setting["user"]["change_pass_meta_title"],
                    'link' => $ims->site_func->get_link("user", $ims->setting["user"]["change_pass_link"])
                ),
                /* 'ordering_link' => array(
                  'title' => $ims->setting["user"]["ordering_meta_title"],
                  'link' => $ims->site_func->get_link ("user",$ims->setting["user"]["ordering_link"])
                  ), */
                'signout' => array(
                    'title' => $ims->site_func->get_lang('signout', 'user'),
                    'link' => "javascript:void(0)",
                    'attr_link' => "onclick=\"imsUser.signout('')\""
                ),
                // 'product_watched' => $data['product_watched'],
            );
            $row = array_merge($row, $arr_is_login);
            $ims->temp_html->assign('row', $row);
            $ims->temp_html->parse("header_user.is_login");
        } else {
            //Captcha::Set();
//            $ims->temp_html->assign('row', array(
//                'header_signup' => $ims->html->temp_box("form_signup", array(
//                    'link_root' => $ims->conf['rooturl'],
//                    'form_id_pre' => 'header_'
//                )),
//                'header_signin' => $ims->html->temp_box("form_signin", array(
//                    'form_id_pre' => 'header_'
//                )),
//                'product_watched' => $data['product_watched'],
//            ));


            $ims->temp_html->parse("header_user.not_login");
        }
        $ims->temp_html->parse("header_user");
        return $ims->temp_html->text("header_user");
    }
    // header_cart
    function header_cart() {
        global $ims;
        $ims->setting = (isset($ims->setting)) ? $ims->setting : array();
        if (!isset($ims->setting['product'])) {
            $ims->setting['product'] = array();
            $result = $ims->db->query("select * from product_setting ");
            if ($row = $ims->db->fetch_row($result)) {
                $ims->setting['product_' . $row['lang']] = $row;
                if ($ims->conf['lang_cur'] == $row['lang']) {
                    $ims->setting['product'] = $row;
                }
            }
        }
        $link_cart = $ims->site_func->get_link('product', '', $ims->setting['product']['ordering_cart_link']);
        $data = array(
            'link_cart' => $link_cart,            
        );
        $ims->func->include_js_content("var link_cart='" . $link_cart . "'");
        $ims->temp_html->assign('data', $data);
        $ims->temp_html->parse("header_cart");
        return $ims->temp_html->text("header_cart");
    }

    // form đăng ký nhận bản tin
    function register_mail() {
        global $ims;

        $data = array();
        $output = $ims->html->temp_box('box_register_mail', $data);
        return $output;
    }
    
    // box_search
    function box_search($module='search', $arr_in=array()) {
        global $ims;
        $ims->site_func->setting('search');
        $form_id = isset($arr_in['form_id'])?$arr_in['form_id']:'form_search_product';
        $input = isset($arr_in['input'])?$arr_in['input']:'product';
        $auto = isset($arr_in['auto'])?$arr_in['auto']:'1';
        $data = array(
            'link_search' => $ims->site_func->get_link($module),
            'form_id' => $form_id,
            'input' => $input,
            'keyword' => (isset($ims->input['keyword'])) ? $ims->input['keyword'] : '',
        );
        if($ims->conf['cur_act'] != 'event'){
            $data['keyword'] = '';
        }
        if($input=="product" && $auto==1){                            
            // var data_title_'.$input.' = data_title_'.$input.';
            $ims->func->include_js_content('                
                imsGlobal.autocomplete_search("product", "'.$ims->setting['search']['search_link'].'");
            ');
        }
        $output = $ims->html->temp_box('box_search', $data);
        return $output;
    }    
    function box_sort_product_top($temp="sort_product_top"){
        global $ims;
        $output = '';
        $data = array(
            'link_sort' => $ims->conf["rooturl"].$ims->conf["cur_mod_url"].'/',
            'data_lang' => array(
                'product' => $ims->site_func->get_lang('product', 'product'),
                'view_by' => $ims->site_func->get_lang('view_by', 'product'),
                'sort_by' => $ims->site_func->get_lang('sort_by', 'product'),
                // 'stock_desc' => $ims->site_func->get_lang('stock_desc', 'product'),
                // 'new_product' => $ims->site_func->get_lang('new_product', 'product'),
                // 'price_asc' => $ims->site_func->get_lang('price_asc', 'product'),
                // 'price_desc' => $ims->site_func->get_lang('price_desc', 'product'),
                // 'title_asc' => $ims->site_func->get_lang('title_asc', 'product'),
                // 'title_desc' => $ims->site_func->get_lang('title_desc', 'product'),                
                'select' => $ims->site_func->get_lang('select', 'product'),
                'list' => $ims->site_func->get_lang('list', 'product'),
                'grid' => $ims->site_func->get_lang('grid', 'product')
            )
        );
        $arr_sort = array(
            'new' => $ims->site_func->get_lang('new_product', 'product'),
            'price-asc' => $ims->site_func->get_lang('price_asc', 'product'),
//            'stock-desc' => $ims->site_func->get_lang('stock_desc', 'product'),
            'price-desc' => $ims->site_func->get_lang('price_desc', 'product'),
            'title-asc' => $ims->site_func->get_lang('title_asc', 'product'),
            'title-desc' => $ims->site_func->get_lang('title_desc', 'product'),
        );
        $ext = '';
        $sort = isset($ims->get['sort']) ? $ims->get['sort'] : '';
        $arr_param =  isset($ims->get)?$ims->get:array();
        // if(array_search($sort, $arr_param)){
        if(count($arr_param)>0){
            foreach ($arr_param as $k => $v) {
                if($k!='sort'){
                    $ext .= '&'.$k.'='.$v;
                }
            }
        }
        // print_arr($ext);
        foreach ($arr_sort as $k => $v) {
            $row = array();
            $row['title'] = $v;
            $row['link'] = $data['link_sort'].'?sort='.$k.$ext;
            if($sort == $k){
                $row['selected'] = "selected";
                $row['data'] = "data-selected";
            }
            $ims->temp_box->assign("row", $row);
            $ims->temp_box->parse($temp.'.row');
        }        
        $ims->temp_box->assign("LANG", $ims->lang);
        $ims->temp_box->assign("data", $data);
        $ims->temp_box->parse($temp);
        $output .= $ims->temp_box->text($temp);
        return $output;
    }
    function box_sort_product_top_ajax($temp="sort_product_top_ajax"){
        global $ims;
        $output = '';
        $data = array(
            'link_sort' => $ims->conf["rooturl"].$ims->conf["cur_mod_url"].'/',
            'data_lang' => array(
                'product' => $ims->site_func->get_lang('product', 'product'),
                'view_by' => $ims->site_func->get_lang('view_by', 'product'),
                'sort_by' => $ims->site_func->get_lang('sort_by', 'product'),
                // 'stock_desc' => $ims->site_func->get_lang('stock_desc', 'product'),
                // 'new_product' => $ims->site_func->get_lang('new_product', 'product'),
                // 'price_asc' => $ims->site_func->get_lang('price_asc', 'product'),
                // 'price_desc' => $ims->site_func->get_lang('price_desc', 'product'),
                // 'title_asc' => $ims->site_func->get_lang('title_asc', 'product'),
                // 'title_desc' => $ims->site_func->get_lang('title_desc', 'product'),
                'select' => $ims->site_func->get_lang('select', 'product'),
                'list' => $ims->site_func->get_lang('list', 'product'),
                'grid' => $ims->site_func->get_lang('grid', 'product')
            )
        );
        $arr_sort = array(
            '' => $ims->site_func->get_lang('new_product', 'product'),
            'price_buy-asc' => $ims->site_func->get_lang('price_asc', 'product'),
//            'stock-desc' => $ims->site_func->get_lang('stock_desc', 'product'),
            'price_buy-desc' => $ims->site_func->get_lang('price_desc', 'product'),
            'title-asc' => $ims->site_func->get_lang('title_asc', 'product'),
            'title-desc' => $ims->site_func->get_lang('title_desc', 'product'),
        );

        foreach ($arr_sort as $k => $v) {
            $row = array();
            $row['title'] = $v;
            $row['link'] = $k;

            $ims->temp_box->assign("row", $row);
            $ims->temp_box->parse($temp.'.row');
        }
        $ims->temp_box->assign("LANG", $ims->lang);
        $ims->temp_box->assign("data", $data);
        $ims->temp_box->parse($temp);
        $output .= $ims->temp_box->text($temp);
        return $output;
    }
    function box_sort_price_top(){
        global $ims;
        $output = '';
        $data = array();        
        if (isset($ims->conf['cur_group']) && $ims->conf['cur_group'] > 0) {
            $data = json_decode($this->get_arr_search_price($ims->conf['cur_group']),true);
            $arr_search_price = $ims->func->unserialize($data['arr_search_price'], array());
            $num_row = count($arr_search_price);
            if ($num_row) {
                $i=0;
                $row=array();
                foreach ($arr_search_price as $k => $v) {
                    $i++; 
                    $row['id'] = $i;
                    $row['title'] = $ims->func->get_price_text($v);                       
                    if ($i==1) {
                        $row['value'] = $v . '-' . $arr_search_price[$i];
                        $row['title'] = $ims->lang['global']['below'].' '.$ims->func->get_price_text($arr_search_price[$i]);
                    } elseif (!isset($arr_search_price[$k + 1])) {
                        $row['value'] = $v.'-0';
                        $row['title'] = $ims->lang['global']['over'].' '.$ims->func->get_price_text($v);                    
                    } else {
                        $row['value'] = $v . '-' . $arr_search_price[$k + 1];
                        $row['title'] = $ims->lang['global']['from'].' '.$ims->func->get_price_text($v) . ' - ' . $ims->func->get_price_text($arr_search_price[$k + 1]);
                    }
                    
                    $ims->temp_box->assign("row", $row);
                    $ims->temp_box->parse("sort_price_top.row");
                }
                $data['title'] = $ims->lang['global']['choose_price'];
                $ims->temp_box->assign("LANG", $ims->lang);
                $ims->temp_box->assign("data", $data);
                $ims->temp_box->parse("sort_price_top");
                $output .= $ims->temp_box->text("sort_price_top");
            }
        }
        return $output;
    }

    function get_arr_search_price($group_id){
        global $ims;
        $output = '';
        $group_id = (int)$group_id;
        $group = $ims->db->load_row('product_group','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and group_id="'.$group_id.'"','arr_search_price,parent_id,group_id,group_level');
        if($group){
            $group['sql'] = 'lang="'.$ims->conf['lang_cur'].'" and is_show=1 and group_id="'.$group_id.'"';
            if($group['arr_search_price'] == ''){
                if($group['parent_id']>0){                     
                    return $this->get_arr_search_price($group['parent_id']);
                }
            }else{                
                $output = json_encode($group);
            }
        }        
        return $output;
    }

    function box_sort_nature_top(){
        global $ims;
        $output = '';
        $data = array();
        if (isset($ims->conf['cur_group']) && $ims->conf['cur_group'] > 0) {
            $arr_group = explode(',', $ims->conf['cur_group_nav']);
            $arr_group_nature = $ims->db->load_row_arr("product_nature_group",'lang="'.$ims->conf['lang_cur'].'" AND is_show=1 AND find_in_set("'.$arr_group[0].'",group_show)>0 ORDER BY show_order DESC, date_create ASC');
            if ($arr_group_nature && !empty($arr_group_nature)) {                
                $i=0;
                foreach ($arr_group_nature as $k_n => $v_n) {
                    $i++;
                    $arr_nature = $ims->db->load_row_arr("product_nature", 'lang="'.$ims->conf['lang_cur'].'" AND is_show=1 AND group_id="'.$v_n['group_id'].'" ORDER BY show_order DESC, date_create ASC');                    
                     if (!empty($arr_nature)) {
                        $where_nature = '';
                        if (isset($ims->conf['cur_group']) && $ims->conf['cur_group'] > 0) {
                            $where_nature .= ' and find_in_set('.$ims->conf['cur_group'].', group_id) ';
                        }
                        foreach ($arr_nature as $k_nature => $v_nature) {
                            $v_nature['num_product'] = $ims->db->do_get_num('product', " is_show = 1 AND find_in_set(".$v_nature['item_id'].", arr_nature) AND lang = '".$ims->conf['lang_cur']."' ".$where_nature." ");
                            // if ($v_nature['num_product'] > 0) {
                                $ims->temp_box->assign("item", $v_nature);
                                $ims->temp_box->parse("sort_nature_top.group.item");
                            // }
                        }
                    }
                    if($i>4){
                        $v_n['hidden'] = 'd-none';
                        $data['show'] = 'show';
                    }
                    $ims->temp_box->assign("group", $v_n);
                    $ims->temp_box->parse("sort_nature_top.group");
                }
                $ims->temp_box->assign("LANG", $ims->lang);
                $ims->temp_box->assign("data", $data);
                $ims->temp_box->parse("sort_nature_top");
                $output .= $ims->temp_box->text("sort_nature_top");
            }            
        }        
        return $output;
    }

    function box_product_group_top(){
        global $ims;
        $output = '';
        //find_in_set("'.$ims->conf['cur_group'].'",group_nav)
        $arr_group = $ims->db->load_item_arr('product_group','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and parent_id="'.$ims->conf['cur_group'].'"','group_id,title,friendly_link');
        if($arr_group){
            foreach ($arr_group as $row) {
                $row['num_product'] = $ims->db->do_get_num('product','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and group_id="'.$row['group_id'].'"');
                if($row['num_product']){
                    $row['title'] = $ims->func->input_editor_decode($row['title']);
                    $row['link'] = $ims->site_func->get_link('product',$row['friendly_link']);
                    $ims->temp_box->assign('row',$row);
                    $ims->temp_box->parse('product_group_top.row');
                }                
            }
            $ims->temp_box->parse('product_group_top');
            $output = $ims->temp_box->text('product_group_top');
        }
        return $output;
    }

    function box_filter_product(){
        global $ims;
        $output = '';
        $data = $filter_item = array();
        //price
        $view_price = isset($ims->get['price']) ? $ims->get['price'] : '';
        if($view_price != '' && strpos($view_price, ',') !== false){
            $filter_item['type'] = 'price';
            $view_price = explode(',', $view_price);
            foreach ($view_price as $price) {
                $arr_price = explode('-', $price);
                if (is_array($arr_price)) {
                    $filter_item['value'] = $price;
                    $filter_item['title'] = $ims->lang['global']['from'].' '.$ims->func->get_price_text($arr_price[0]) . ' - ' . $ims->func->get_price_text($arr_price[1]);
                    if($arr_price[0] == 0){
                        $filter_item['title'] = $ims->lang['global']['below'].' '.$ims->func->get_price_text($arr_price[1]);
                    }elseif($arr_price[1] == 0){
                        $filter_item['title'] = $ims->lang['global']['over'].' '.$ims->func->get_price_text($arr_price[0]);
                    }
                    $ims->temp_box->assign("item", $filter_item);
                    $ims->temp_box->parse("filter_product_top.filter_item");
                }
            }
        }
        else if($view_price != '' && strpos($view_price, ',') == false){
            $filter_item['type'] = 'price';
            $arr_price = explode('-', $view_price);
            if (is_array($arr_price)) {              
                $filter_item['value'] = $view_price;
                $filter_item['title'] = $ims->lang['global']['from'].' '.$ims->func->get_price_text($arr_price[0],'','d-none') . ' - ' . $ims->func->get_price_text($arr_price[1]);
                if($arr_price[0] == 0){
                    $filter_item['title'] = $ims->lang['global']['below'].' '.$ims->func->get_price_text($arr_price[1]);
                }elseif($arr_price[1] == 0){
                    $filter_item['title'] = $ims->lang['global']['over'].' '.$ims->func->get_price_text($arr_price[0]);
                }
                $ims->temp_box->assign("item", $filter_item);
                $ims->temp_box->parse("filter_product_top.filter_item");
            }
        }
        //brand
        $view_brand = isset($ims->get['brand']) ? $ims->get['brand'] : '';        
        if($view_brand != '' && strpos($view_brand, ',') !== false){
            $filter_item['type'] = 'brand';
            $view_brand = explode(',', $view_brand);                
            if($view_brand[1] != ''){
                foreach ($view_brand as $key => $value) {
                    $filter_item['title'] = $this->get_title_brand($value);
                    $filter_item['value'] = $value;                    
                    $ims->temp_box->assign("item", $filter_item);
                    $ims->temp_box->parse("filter_product_top.filter_item");
                }
            }
            else if($view_brand[0] != ''){
                $filter_item['title'] = $this->get_title_brand($view_brand[0]);
                $filter_item['value'] = $view_brand[0];                
                $ims->temp_box->assign("item", $filter_item);
                $ims->temp_box->parse("filter_product_top.filter_item");
            }
        }
        else if($view_brand != '' && strpos($view_brand, ',') == false){
            $filter_item['type'] = 'brand';
            $filter_item['title'] = $this->get_title_brand($view_brand);
            $filter_item['value'] = $view_brand;
            $ims->temp_box->assign("item", $filter_item);
            $ims->temp_box->parse("filter_product_top.filter_item");
        }
        //nature
        $view_nature = isset($ims->get['nature']) ? $ims->get['nature'] : '';           
        if($view_nature != '' && strpos($view_nature, ',') !== false){
            $filter_item['type'] = 'nature';
            $view_nature = explode(',', $view_nature);
            if($view_nature[1] != ''){
                foreach ($view_nature as $key => $value) {                    
                    $filter_item['title'] = $this->get_title_nature($value);
                    $filter_item['value'] = $value;
                    $ims->temp_box->assign("item", $filter_item);
                    $ims->temp_box->parse("filter_product_top.filter_item");
                }
            }
            else if($view_nature[0] != ''){
                $filter_item['title'] = $this->get_title_nature($view_nature[0]);
                $filter_item['value'] = $view_nature[0];
                $ims->temp_box->assign("item", $filter_item);
                $ims->temp_box->parse("filter_product_top.filter_item");
            }
        }
        else if($view_nature != '' && strpos($view_nature, ',') == false){
            $filter_item['type'] = 'nature';
            $filter_item['title'] = $this->get_title_nature($view_nature);
            $filter_item['value'] = $view_nature;
            $ims->temp_box->assign("item", $filter_item);
            $ims->temp_box->parse("filter_product_top.filter_item");
        }        
        //color
        $view_color = isset($ims->get['color']) ? $ims->get['color'] : '';
        if($view_color != '' && strpos($view_color, ',') !== false){
            $filter_item['type'] = 'color';
            $view_color = explode(',', $view_color);
            if($view_color[1] != ''){
                foreach ($view_color as $key => $value) {                    
                    $filter_item['title'] = $this->get_title_color($value);
                    $filter_item['value'] = $value;
                    $ims->temp_box->assign("item", $filter_item);
                    $ims->temp_box->parse("filter_product_top.filter_item");
                }
            }
            else if($view_color[0] != ''){
                $filter_item['title'] = $this->get_title_color($view_color[0]);
                $filter_item['value'] = $view_nature[0];
                $ims->temp_box->assign("item", $filter_item);
                $ims->temp_box->parse("filter_product_top.filter_item");
            }
        }
        else if($view_color != '' && strpos($view_color, ',') == false){
            $filter_item['type'] = 'color';
            $filter_item['title'] = $this->get_title_color($view_color);
            $filter_item['value'] = $view_color;
            $ims->temp_box->assign("item", $filter_item);
            $ims->temp_box->parse("filter_product_top.filter_item");
        }
        //tag
        $view_tag = isset($ims->get['tag']) ? $ims->get['tag'] : '';

        if($view_tag != '' && strpos($view_tag, ',') !== false){
            $filter_item['type'] = 'tag';
            $view_tag = explode(',', $view_tag);
            if($view_tag[1] != ''){
                foreach ($view_tag as $key => $value) {    
                    $filter_item['type'] = 'tag';
                    $filter_item['title'] = $value;
                    $filter_item['value'] = $value;
                    $ims->temp_box->assign("item", $filter_item);
                    $ims->temp_box->parse("filter_product_top.filter_item");
                }
            }
            else if($view_tag[0] != ''){
                $filter_item['type'] = 'tag';
                $filter_item['title'] = $view_tag[0];
                $filter_item['value'] = $view_nature[0];
                $ims->temp_box->assign("item", $filter_item);
                $ims->temp_box->parse("filter_product_top.filter_item");
            }
        }
        else if($view_tag != '' && strpos($view_tag, ',') == false){      
            $filter_item['type'] = 'tag';      
            $filter_item['title'] = $view_tag;
            $filter_item['value'] = $view_tag;
            $ims->temp_box->assign("item", $filter_item);
            $ims->temp_box->parse("filter_product_top.filter_item");
        }
        if($view_price || $view_brand || $view_nature || $view_color || $view_tag){
            $ims->temp_box->assign("LANG", $ims->lang);
            $ims->temp_box->assign("data", $data);
            $ims->temp_box->parse("filter_product_top");
            $output .= $ims->temp_box->text("filter_product_top");
        }        
        return $output;
    }

    function get_title_nature($item_id = ''){
        global $ims;
        $output = '';
        $item_id = (int)$item_id;        
        $nature = $ims->db->load_item('product_nature','lang = "'. $ims->conf['lang_cur'] .'" AND is_show = 1 AND item_id = "'.$item_id.'"','title');
        $output = isset($nature) ? $nature : '';        
        return $output;
    }

    function get_title_brand($group_id = ''){
        global $ims;
        $output = '';
        $brand = $ims->db->load_item('product_brand','lang = "'. $ims->conf['lang_cur'] .'" AND is_show = 1 AND brand_id = "'.$group_id.'"','title');        
        $output = isset($brand) ? $brand : '';        
        return $output;        
    }

    function get_title_color($item_id = '',$type=1){
        global $ims;
        $output = '';
        $item_id = (int)$item_id;        
        $color_row = $ims->db->load_row('product_color','lang = "'. $ims->conf['lang_cur'] .'" AND is_show = 1 AND color_id = "'.$item_id.'"','title,color');
        if($color_row){
            if($type==1){
                $output = $color_row['title'];
            }else{
                $output = array();
                $output['color'] = $color_row['color'];
                $output['title'] = $color_row['title'];
            }            
        }
        return $output;        
    }

    function news_focus($type = '') {
        global $ims;
        $temp = 'news_focus';
        $where = $ims->site_func->whereLoaded('news');
        $order_by = " order by order_focus_hot desc, date_update desc ";
        if ($ims->conf['cur_mod'] == 'news') {
            if (isset($ims->conf['cur_group']) && $ims->conf['cur_group']) {
                $where .= " and find_in_set('" . $ims->conf['cur_group'] . "', group_nav)";
            }
            //Add first row new post 
            $sql_first = "select item_id 
                from news 
                where is_show=1 
                and lang='" . $ims->conf["lang_cur"] . "' 
                " . $where . "
                order by order_focus_hot desc, date_update desc 
                limit 0,1";
            $result_first = $ims->db->query($sql_first);
            if ($row_first = $ims->db->fetch_row($result_first)) {
                $where .= " and (item_id='" . $row_first['item_id'] . "' or is_focus_hot=1) ";
                $order_by = " order by FIELD(item_id, '" . $row_first['item_id'] . "') DESC, order_focus_hot desc, date_update desc ";
            } else {
                $where .= " and is_focus_hot=1 ";
            }
            //End    
        } else {
            $where .= " and is_focus_home=1 ";
            $order_by = " order by order_focus_home desc, date_update desc ";
        }
        $sql = "select item_id,picture,title,short,friendly_link,date_update  
                from news 
                where is_show=1 
                and lang='" . $ims->conf["lang_cur"] . "' 
                " . $where . "
                " . $order_by . "
                limit 0,8";
        //echo $sql; die;
//        if(isset($ims->get['test'])){
//            echo $sql; die;
//        }
        if($type == 'home'){
        }
        $result = $ims->db->query($sql);
        $html_row = '';
        if ($num = $ims->db->num_rows($result)) {
            $i = 0;
            while ($row = $ims->db->fetch_row($result)) {
                $i++;
                //The item loaded and no load again
                $ims->site_func->loaded_datatype('news', $row['item_id']);
                $row['link'] = $ims->site_func->get_link('news', '', $row['friendly_link']);
                if ($i == 1 || $i == 2) {
                    $pic_w = 370;
                    $pic_h = 250;
                    $row["picture"] = $ims->func->get_src_mod($row["picture"], $pic_w, $pic_h, 1, 1);
                    $row['short'] = $ims->func->short($row['short'], 250);
                    $ims->temp_box->assign('row', $row);
                    $ims->temp_box->parse($temp . ".row_first");
                    continue;
                } else {
                    $ims->temp_box->assign('row', $row);
                    $ims->temp_box->parse($temp . ".row");
                }
                if ($i == $num && $i > 1) {
                    $ims->temp_box->parse($temp . ".focus");
                }
            }
            $data = array();
            if ($num == 1) {
                $data['class'] = 'full_row';
            }
            $ims->temp_box->assign('data', $data);
            $ims->temp_box->parse($temp);
            return $ims->temp_box->text($temp);
        }
    }
    function get_tag($arr_tag = array()){
        global $ims;
        $output = $tag_list = array();
        if(count($arr_tag)>0){
            foreach($arr_tag as $tag){                
                $tag_list = array_merge_recursive($tag_list, $tag);
            }
            if(count($tag_list)>0){                
                foreach ($tag_list as $row) {                     
                    for($i=0; $i<count($row); $i++){
                        $temp = explode(",", $row[$i]);                      
                        foreach($temp as $key => $value){
                            if($value!='') $output[] = $value;
                        }
                   }
                }
            }
        }
        return array_values(array_unique($output));
    }
    function get_color($arr_color = array()){
        global $ims;
        $output = '';
        $color_list = array();
        foreach($arr_color as $color){                
            $color_list = array_merge_recursive($color_list, $color);
        }
        if(count($color_list)>0){                
            foreach ($color_list as $row) {                     
                for($i=0; $i<count($row); $i++){
                    $temp = explode(",", $row[$i]);                      
                    foreach($temp as $key => $value){
                        if($value!='') $output[] = $value;
                    }
               }
            }
        }
        return array_values(array_unique($output));
    }
    function check_favorite($item_id = 0, $table='product'){
        global $ims;

        $data = array();
        $data['class'] = "fal fa-heart";
        $data['mod'] = $table;
        $data['id'] = $item_id;
        $data['added'] = '';
        if ($ims->site_func->checkUserLogin() == 1) {
            $list_favorite = $ims->data['user_cur']['list_favorite'];
            if($list_favorite != ''){
                $arr_favorite = $ims->func->unserialize($list_favorite);
                $arr_search = array();
                $mod = $data['mod'];
                $id = $data['id'];
                foreach ($arr_favorite as $key => $object){
                    if(array_search($mod, $object)){
                        if(array_search($id, $object)){
                            $arr_search = $arr_favorite[$key];
                        }
                    }
                }
                if(!empty($arr_search)){
                    $data['added'] = 'added';
                    $data['class'] = "fas fa-heart";
                }
            }
        }
        return $data;
    }
    function footer_contact() {
        global $ims;
        $ims->func->include_js('http://maps.google.com/maps/api/js?sensor=false');
        $ims->func->include_js($ims->dir_js . 'gomap/js/jquery.gomap-1.3.1.min.js');
        $ims->func->include_js($ims->dir_js . 'gomap/js/jquery.dump.js');
        $ims->func->include_js($ims->dir_js . 'gomap/js/jquery.chili-2.2.js');
        $ims->func->include_js($ims->dir_js . 'gomap/js/recipes.js');
        $contact_info = '';
        $result = $ims->db->query("select c.map_id, map_type, map_latitude, map_longitude, title, short, map_information, map_picture 
                                                                from contact_map c, contact_map_lang cl   
                                                                where c.map_id=cl.map_id 
                                                                and is_show=1 
                                                                and lang='" . $ims->conf["lang_cur"] . "' 
                                                                order by show_order desc , date_create asc 
                                                                limit 0,2");
        if ($num = $ims->db->num_rows($result)) {
            $i = 0;
            $list_markers = '';
            $list_pic = '';
            while ($row = $ims->db->fetch_row($result)) {
                $i++;
                $list_markers = '';
                switch ($row['map_type']) {
                    case 'google_map' :
                        $list_markers .= (!empty($list_markers)) ? ',' : '';
                        $list_markers .= '{ 
                            latitude: "' . $row['map_latitude'] . '",
                            longitude: "' . $row['map_longitude'] . '",
                            id: "map_id_' . $row['map_id'] . '", 
                            html: {
                                content: "' . $row['map_information'] . '",
                                popup: true
                            }
                        }';
                        break;
                }
                if (!empty($list_markers)) {
                    $row['contact_map'] = '<script language="javascript">
                        $(function() {
                            $("#footer_map_view_' . $row['map_id'] . '").goMap({
                                markers: [' . $list_markers . '],
                                icon: "' . $ims->dir_images . 'icon_markers.png",
                                maptype: "ROADMAP",
                                zoom: 15
                            });
                        });
                    </script>';
                    $ims->temp_html->assign("row", $row);
                    $ims->temp_html->parse("footer_contact.row");
                }
            }
        }
        $ims->temp_html->parse("footer_contact");
        $nd['content'] = $ims->temp_html->text("footer_contact");
        return $ims->temp_html->text("footer_contact");
    }
    // popup_banner
    function popup_banner() {
        global $ims;
        $output = '';
        $popup_showed = Session::Get('popup_showed', 0);
        if($ims->conf['popupwebload'] > 0) {
            $is_show = 0;
            switch ($ims->conf['popupwebload']) {
                case 1: //Chỉ hiện 1 lần trong 1 phiên làm việc
                    if($popup_showed == 0){
                        $is_show = 1;
                    }                        
                    break;
                case 2: //Luôn hiện khi tải lại trang
                    $is_show = 1;
                    break;
                case 3: //Luôn hiện ở trang chủ
                    if($ims->conf['cur_mod'] == 'home'){
                        $is_show = 1;
                    }                        
                    break;
            }
            if($is_show == 1) {
                $popup_showed++;
                Session::Set('popup_showed', $popup_showed);
                $limit = 1;
                $group_id = (isset($ims->input['banner_pos']) && $ims->input['banner_pos']) ? trim($ims->input['banner_pos']) : 'popup';
                if($group_id) {
                    $ims->load_data->data_banner_group();
                    $ims->load_data->data_banner();
                }
                if (isset($ims->data["banner_group"][$group_id]) && isset($ims->data["banner"][$group_id])) {
                    $i = 0;
                    foreach ($ims->data["banner"][$group_id] as $banner) {
                        $i++;
                        $w = $ims->data["banner_group"][$group_id]['width'];
                        $h = $ims->data["banner_group"][$group_id]['height'];
                        $style_pic = '';
                        $style_frame = '';
                        if ($ims->data["banner_group"][$group_id]['type_show'] == 'fixed') {
                            $style_frame = "width:" . $w . "px;";
                            $style_frame .= ($h > 0) ? "height:" . $h . "px;overflow:hidden;" : "";
                            $style_frame = ($w > 0 || $h > 0) ? $style : '';
                        } elseif ($ims->data["banner_group"][$group_id]['type_show'] == 'full') {
                            $style_pic = "max-width:100%;max-height:100%;";
                        }
                        $banner['link'] = $ims->site_func->get_link_menu($banner['link'], $banner['link_type']);
                        if ($banner['type'] == 'image') {
                            //$banner['content'] = '<img src="'.$ims->conf["rooturl"].'uploads/banner/'.$banner['content'].'" alt="'.$banner['title'].'" />';
                            $banner['content'] = '<div class="item"><button class="close" style="color: #f00; box-shadow: none; opacity: 1;">x</button>' .'<a href="' . $banner['link'] . '" target="' . $banner['target'] . '">'. $ims->func->get_pic_mod($banner['content'], $w, $h, " alt=\"" . $banner['title'] . "\" style=\"" . $style_pic . "\"", 1, 0, array('fix_width' => 1)) . '</a></div>';
                        } elseif ($banner['type'] == 'flash' && $banner['content']) {
                            //$ims->func->include_js ($ims->dir_js.'swfobject/swfobject.js');
                            $w = ($w) ? $w : '100%';
                            $h = ($h) ? $h : $w;
                            $tl = ((int) $h / (int) $w) * 100;
                            $tmp = 'flash_file_' . $ims->func->random_str('6');
                            $banner['content'] = '<div style="position:relative; padding-bottom:' . $tl . '%;"><div id="' . $tmp . '"></div></div>
                            <script type="text/javascript">
                                swfobject.embedSWF("' . $ims->conf['rooturl_web'] . 'uploads/' . $banner['content'] . '", "' . $tmp . '", "' . $w . '", "' . $h . '", "9.0.0", "' . $ims->dir_js . 'swfobject/expressInstall.swf");
                            </script>';
                        }
                        $output .= '<div class="banner_item" style="' . $style_frame . '">' . $banner['content'] . '</div>';
                        if ($i >= $limit && $limit > 0) {
                            break;
                        }
                    }
                    $output='<a id="click_popup" class="fancybox_popup" href="#popup_banner_a"></a><div id="popup_banner"><div class="" id="popup_banner_a">'.$output.'</div></div>';
                }
                $ims->func->include_js_content('$(document).ready(function() { 
                    $("#click_popup").click(); 
                    $("#popup_banner_a button.close").on("click",()=>$.fancybox.close());
            });');
                return $output;
                // $ims->func->include_js_content('$(function() {popupUrl("'.$ims->site_func->get_link_ajax('global', "popup", "", array('banner_pos' => 'popup')).'");})');
            }                
        }   
        return ;
    }
    function list_uploaded($arr_file = array(), $type = 'picture') {
        global $ims;
        $output = '';
        if (!is_array($arr_file)) {
            $arr_file = ($arr_file) ? unserialize($arr_file) : array();
        }
        $uploaded = '';
        if (count($arr_file)) {
            foreach ($arr_file as $picture) {
                if ($type == 'picture') {
                    $uploaded .= '<div class="pic-item"><a href="' . $ims->func->get_src_mod($picture, 700, 0, 1, 0, array('fix_width' => 1)) . '" class="fancybox-effects-a">' . $ims->func->get_pic_mod($picture, 100, 100, '', 1, 0, array('fix_width' => 1)) . '</a></div>';
                } else {
                    $uploaded .= '<div class="file-item"><a href="' . $picture . '" target="_blank">' . $picture . '</a></div>';
                }
            }
        }
        $output .= '<div class="fileupload"><div class="list_uploaded list_pic">' . $uploaded . '</div></div>';
        return $output;
    }
    function get_form_upload_muti($html_name = 'picture', $arr_file = array(), $type = 'picture') {
        global $ims;
        $ims->func->include_css($ims->dir_js . 'file-upload/css/jquery.fileupload.css');
        $ims->func->include_js($ims->dir_js . 'file-upload/js/vendor/jquery.ui.widget.js');
        $ims->func->include_js($ims->dir_js . 'file-upload/js/jquery.iframe-transport.js');
        $ims->func->include_js($ims->dir_js . 'file-upload/js/jquery.fileupload.js');
        $output = '';
        if (!is_array($arr_file)) {
            $arr_file = ($arr_file) ? unserialize($arr_file) : array();
        }
        $uploaded = '';
        if (count($arr_file)) {
            foreach ($arr_file as $picture) {
                if ($type == 'picture') {
                    $uploaded .= '<div class="pic-item"><a href="' . $ims->func->get_src_mod($picture, 700, 0, 1, 0, array('fix_width' => 1)) . '" class="fancybox-effects-a">' . $ims->func->get_pic_mod($picture, 100, 100, '', 1, 0, array('fix_width' => 1)) . '</a><a class="btn-remove" href="javascript:;"><i class="far fa-times"></i></a><input type="hidden" value="' . $picture . '" name="' . $html_name . '[]"></div>';
                } else {
                    $uploaded .= '<div class="file-item">' . $picture . '<a class="btn-remove" href="javascript:;"><i class="far fa-times"></i></a><input type="hidden" value="' . $picture . '" name="' . $html_name . '[]"></div>';
                }
            }
        }
        $html_id = str_replace(array('[', ']'), array('_', ''), $html_name);
        $output .= '<div id="' . $html_id . '" class="fileupload">
                <div class="list_uploaded list_pic">' . $uploaded . '</div>
                <!-- The global progress bar -->
                <div class="upload-progress progress">
                    <div class="progress-bar progress-bar-success"></div>
                </div>
                <div class="btn-upload">
                    <span><i class="far fa-plus"></i></span>
                    <input type="file" name="files[]" multiple="multiple" disabled="disabled" />
                </div>
                <script>
                    imsGlobal.uploadMuti("' . $html_name . '", "' . $html_id . '");
                </script>
            </div>';
        return $output;
    }
    // copyright
    function copyright() {
        global $ims;
        $output = "";
        $arr_data = array(
            'vi' => '<a href="http://thietkewebsite.info.vn" target="_blank">Thiết kế web</a>
            <a href="http://imsvietnamese.com" target="_blank">IMS</a>',
            'en' => '<a href="http://thietkewebsite.info.vn" target="_blank" class="title">Designed by</a>
            <a href="http://imsvietnamese.com" target="_blank">IMS</a>'
        );
        $output = (isset($arr_data[$ims->conf['lang_cur']])) ? $arr_data[$ims->conf['lang_cur']] : $arr_data['vi'];
        return $output;
    }
    function do_video($row, $option = array()){
        global $ims;
        $out = '';

        $w = isset($option['pic_w']) ? $option['pic_w'] : 429;
        $h = isset($option['pic_h']) ? $option['pic_h'] : 241;
        $show_title = isset($option['title']) ? $option['title'] : 1;
        if($show_title == 1){
            $row['titles'] = '<div>'.$row['title'].'</div>';
        }
        if($row) {
            if ($row['video_file'] != '') {
                $row['video_file'] = $ims->conf['rooturl'] . 'uploads/' . $row['video_file'];
                $row['picture'] = $ims->func->get_src_mod($row['picture'], $w, $h, 1, 1);
                $ims->temp_box->assign('row', $row);
                $ims->temp_box->parse('view_video.file');
                $ims->temp_box->reset("view_video");
                $ims->temp_box->parse('view_video');
                $out = $ims->temp_box->text('view_video');
            } elseif ($row['video'] != '') {
                $row['code_video'] = $ims->func->get_youtube_code($row['video']);
                $row['picture'] = ($row['picture'] != '') ? $ims->func->get_src_mod($row['picture'], $w, $h, 1, 1) : 'http://img.youtube.com/vi/' . $row['code_video'] . '/hqdefault.jpg';
                $ims->temp_box->assign('row', $row);
                $ims->temp_box->parse("view_video.youtube");
                $ims->temp_box->reset("view_video");
                $ims->temp_box->parse("view_video");
                $out = $ims->temp_box->text("view_video");
            }
        }
        return $out;
    }
    function do_watched(){
        global $ims;
        $out = '';
        if(!isset($ims->lang['product'])){
            $ims->func->load_language('product');
        }

        $list = array();
        if(isset($_COOKIE['list_watched']) && $_COOKIE['list_watched']){
            parse_str($_COOKIE['list_watched'], $tmp);
            $list = $tmp['list'];
            $list = explode(',', $list);
        }
        $arr_tmp = array();
        if ($ims->site_func->checkUserLogin() == 1) {
            $list_watched_user = ($ims->data['user_cur']['list_watched']) ? $ims->func->unserialize($ims->data['user_cur']['list_watched']) : array();
            if($list_watched_user){
                foreach ($list_watched_user as $item){
                    $arr_tmp[] = $item['id'];
                }
            }
        }
        $list = array_unique(array_merge($list, $arr_tmp));
        if($list){
//            $result = $ims->db->load_item_arr('product', $ims->conf['qr'].' and item_id IN('.implode(',', $list).') order by FIELD(item_id, '.implode(',', $list).') limit 8', 'item_id, title, friendly_link, picture');
            $arr_in = array(
                'where' => ' and item_id IN('.implode(',', $list).') order by FIELD(item_id, '.implode(',', $list).')',
                'paginate' => 0
            );
            $check = $ims->db->do_get_num("product", "1 ". $ims->conf['where_lang'] . $arr_in['where']);
            if($check){
                $content = $ims->call->mfunc('product', 'html_list_item', array($arr_in));
                $ims->temp_box->assign('content', $content);
                $ims->temp_box->parse('watched_product');
                return $ims->temp_box->text('watched_product');
            }
            return $out;
        }
    }

    function do_column_store(){
        global $ims;
        $setting = $ims->setting['store'];
        $out = '<ul class="column_store list_none">';

        $data = array(
            array(
                'link' => $setting['store_link'],
                'title' => $setting['store_meta_title'],
                'action' => 'store'
            ),
            array(
                'link' => $setting['product_manage_link'],
                'title' => $setting['product_manage_meta_title'],
                'action' => 'product_manage'
            ),
            array(
                'link' => $setting['post_product_link'],
                'title' => $setting['post_product_meta_title'],
                'action' => 'post_product'
            ),
        );
        foreach ($data as $row){
            $link = $ims->func->get_link($row['link'], '');
            $current = (isset($ims->conf['cur_act']) && $ims->conf['cur_act'] == $row['action']) ? 'class="current"' : '';
            $out .= '<li><a href="'.$link.'" '.$current.'>'.$row['title'].'</a></li>';
        }
        return $out .= '</ul>';
    }

    function do_social(){
        global $ims;
        $out = '';

        $result = $ims->db->load_item_arr('config_social', $ims->conf['qr'].' order by show_order desc, date_create asc', 'title, link, icon');
        if($result){
            $out = '<ul class="list_none list_social">';
            foreach ($result as $row){
                if($row['icon'] != ''){
                    $out .= '<li><a href="'.$row['link'].'" target="_blank"><img src="'.$ims->func->get_src_mod($row['icon']).'" alt="'.$row['title'].'"></a></li>';
                }
//                else{
//                    $out .= '<li><a href="'.$row['link'].'" target="_blank">'.$ims->func->input_editor_decode($row['icon']).'</a></li>';
//                }
            }
            $out .= '</ul>';
        }
        return $out;
    }

    function check_follow($event_id){
        global $ims;

        $out = array(
            'class_follow' => '',
            'none_follow' => '',
            'text_follow' => $ims->lang['event']['btn_follow'],
            'ok' => 0
        );

        if($ims->site_func->checkUserLogin() == 1) {
            $user = $ims->data['user_cur'];
            $owner_event = $ims->db->load_item('event', 'is_show = 1 and item_id = '.$event_id, 'user_id');
            if(($owner_event) && $user['user_id'] == $owner_event){
                $out['none_follow'] = 'none';
            }else{
                $out['owner_event'] = ($owner_event) ? $owner_event : 0;
                if($user['list_follow']){
                    $list_followed = explode(',', $user['list_follow']);
                    if(in_array($owner_event, $list_followed)){
                        $out['class_follow'] = 'followed';
                        $out['text_follow'] = $ims->lang['event']['cancel_follow'];
                        $out['ok'] = 1;
                    }
                }
            }
        }

        return $out;
    }

    function remember_me(){
        global $ims;
        
        if(!empty($_COOKIE['__rme']) && $ims->site_func->checkUserLogin() != 1){
            $decode = json_decode($ims->func->encrypt_decrypt('decrypt', hex2bin($_COOKIE['__rme']), 'remember_me', 'rememberMe'),true);
            $user = $ims->db->load_row("user", " (username='".$decode['username']."' or phone = '".$decode['username']."' or email = '".$decode['username']."') AND password='".$decode['password']."' ");
            if (!empty($user)) {
                if($user['is_show'] == 1){                    
                    Session::Set('user_cur', array(
                        'userid'   => $user['user_id'],
                        'username' => $user['username'],
                        'password' => $user['password'],
                        'session'  => $user['session']
                    ));
                }
            }
        }
    }
// end class

    function top_footer(){
        global $ims;

        $event = $this->get_banner('event-on-footer', 20);
        if(!empty($event)){
            $ims->temp_box->assign('event', $event);
            $ims->temp_box->parse("top_footer.event");
            $ims->temp_box->parse("top_footer.event_js");
        }
        $country = $ims->db->load_item_arr('location_country', 'is_show = 1 order by title asc', 'title, code');
        $ims->temp_box->assign('LANG', $ims->lang);
        $ims->temp_box->parse("top_footer");
        return $ims->temp_box->text("top_footer");
    }
}
?>