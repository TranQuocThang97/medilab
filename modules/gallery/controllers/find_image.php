<?php
if (!defined('IN_ims')) { die('Access denied'); }
$nts = new sMain();

class sMain
{
    var $modules = "gallery";
    var $action  = "find_image";
    var $sub     = "manage";
    var $template = "gallery";
    
    /**
        * Khởi tạo
        * Quản lý sự kiện
    **/
    function __construct (){
        global $ims;

        $dir_assets  = $ims->func->dirModules($this->modules, 'assets');        
        $arrLoad = array(
            'modules'        => $this->modules,
            'action'         => $this->action,
            'template'       => $this->modules,
            'js'             => $this->modules,
            'css'            => $this->modules,
            'use_func'       => $this->modules, // Sử dụng func
            'use_navigation' => 0, // Sử dụng navigation
            'required_login' => 1, // Bắt buộc đăng nhập
        );
        $ims->func->loadTemplate($arrLoad);
        
        $ims->func->include_js ($ims->dir_js.'croppie/croppie.min.js');
        $ims->func->include_css ($ims->dir_js.'croppie/croppie.min.css');

        $data = array();
        $data['content'] = '';
        $param = $ims->func->get_id_page($ims->conf['cur_act_url']);
        if(!empty($param['detail'])){            
            $data['content'] = $this->do_main($param['detail']);
        }else{
            $ims->html->redirect_rel($ims->site_func->get_link('gallery'));
        }
        
        $ims->conf["class_full"] = 'gallery';
        $ims->conf['container_layout'] = 'm';
        $ims->temp_act->assign('data', $data);
        $ims->temp_act->parse("main");
        $ims->output .=  $ims->temp_act->text("main");
    }

    function do_main ($id = 0){
        global $ims;    
        $data = array();
        
        $id = $ims->func->base64_decode($id);
        $info = $ims->db->load_row('event','is_show=1 and lang="'.$ims->conf['lang_cur'].'" and item_id="'.$id.'"');
        if(!$info){
            $ims->html->redirect_rel($ims->site_func->get_link('gallery'));
        }
        $data['event_id'] = $info['item_id'];
        $data['arr_logo'] = $ims->func->unserialize($info['arr_logo']);     
        if($data['arr_logo']){
            foreach ($data['arr_logo'] as $pic) {
                $row = array();
                $row['picture'] = $ims->func->get_src_mod($pic);
                $ims->temp_act->assign('row', $row);
                $ims->temp_act->parse("find.logo.row");
            }
            $ims->temp_act->parse("find.logo");
        }
        $data['title'] = $ims->func->input_editor_decode($info['title']);
        switch ($ims->conf['lang_cur']) {
            case 'vi':
                $data['date_begin'] = $ims->func->rebuild_date('l, d/m, h:i A', $info['date_begin']);
                break;
            case 'en':
                $data['date_begin'] = date('l, d/m, h:i A', $info['date_begin']);
            default:
                break;
        }
        $data['event_id'] = $info['item_id'];
        $data['organizer'] = $ims->func->input_editor_decode($info['organizer']);
        $data['address'] = $info['address'];        
        
        $src = $ims->conf['rooturl'].'resources/images/';
        $data['upload_pic'] = $src.'upload_pic.svg';

        $ims->temp_act->assign('data', $data);
        $ims->temp_act->parse("find");
        return $ims->temp_act->text("find");
    }
    // End class
}
?>