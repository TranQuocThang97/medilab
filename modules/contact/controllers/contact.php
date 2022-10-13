<?php
if (!defined('IN_ims')) { die('Access denied'); }
$nts = new sMain();

class sMain {

    var $modules = "contact";
    var $action  = "contact";
    var $sub     = "manage";

    /**
         * function __construct ()
         * Khoi tao 
    **/
    function __construct() {
        global $ims;

        $arrLoad = array(
            'modules'        => $this->modules,
            'action'         => $this->action,
            'template'       => $this->modules,
            'js'             => $this->modules,
            'css'            => $this->modules,
            'use_navigation' => 1, // Sử dụng navigation
            'required_login' => 0, // Bắt buộc đăng nhập
        );
        $ims->func->loadTemplate($arrLoad);

     
        $data = array();
        $data['content'] = $this->do_contact();
        $ims->conf['container_layout'] = 'm';
        $ims->conf['class_full'] = 'contact';
        $ims->temp_act->assign('data', $data);
        $ims->temp_act->parse("main");
        $ims->output .= $ims->temp_act->text("main");
    }

    function do_contact() {
        global $ims;

        $ims->func->include_js("https://maps.google.com/maps/api/js?key=AIzaSyB4LPiGAQI-J2dr_G0VSqFP0B7YUvQr00M&sensor=false");
        $ims->func->include_js($ims->dir_js . "gmap/infobubble-compiled.js");

        $data = array();

        $map_information = str_replace(PHP_EOL, '', $ims->func->input_editor_decode($ims->conf['map_information']));
        $map_information = str_replace("'", '`', $map_information);
        $map_information = str_replace('"', '\"', $map_information);     
        $map_information = minify::html($map_information);           
        $arr_markers = array();
        $arr_markers['cm_1']['map_id'] = 1;
        $arr_markers['cm_1']['map_latitude'] = $ims->conf["map_latitude"];
        $arr_markers['cm_1']['map_longitude'] = $ims->conf["map_longitude"];
        $arr_markers['cm_1']['map_information'] = $map_information;
        $data['centerMaplat'] = $ims->conf["map_latitude"];
        $data['centerMaplng'] = $ims->conf["map_longitude"];
        $data['arr_markers'] = json_encode($arr_markers);

        $ims->func->include_js_content('
            imsContact.contact("form_contact");'
            .'var arr_markers = JSON.parse(\''.$data['arr_markers'].'\'); '
            .'imsContact.initialize(arr_markers);'
        );

        $data['link_action'] = $ims->site_func->get_link($this->modules);

        $data['title'] = isset($ims->input['title']) ? $ims->input['title'] : $ims->lang['contact']['contact'];
        $data['contact_info'] = $ims->func->input_editor_decode($ims->conf["contact_info"]);

        $ims->temp_act->assign("data", $data);
        $ims->temp_act->assign("setting", $ims->setting['contact']);
        $ims->temp_act->parse("html_contact");
        return $ims->temp_act->text("html_contact");
    }
    // End class
}

?>