<?php
if (!defined('IN_ims')) { die('Access denied'); }
$nts = new sMain();

class sMain {

    var $modules    = "user";
    var $action     = "list_watched";

    /**
        * Khởi tạo
        * Danh sách yêu thích
    **/
    function __construct() {
        global $ims;

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

        $data = array();
        $data['content'] = $this->do_manage();
        $data['box_left'] = box_left($this->action);
        $ims->conf["class_full"] = 'user';
        $ims->conf['container_layout'] = 'm';
        $ims->temp_act->assign('data', $data);
        $ims->temp_act->parse("main");
        $ims->output .= $ims->temp_act->text("main");
    }

    function do_manage() {
        global $ims;

        $ims->func->load_language('product');
        $data = array();
        $user = $ims->data['user_cur'];
        if(!empty($user['list_watched'])){            
            $list_watched = unserialize($user['list_watched']);
            $arr_tmp = array();
            foreach ($list_watched as $key => $value) {
                $arr_tmp[] = $value["id"];
            }
            $where = ' AND FIND_IN_SET(item_id,"'.implode(',', $arr_tmp).'") ORDER BY FIELD(item_id,'.implode(',', $arr_tmp).') DESC';
            $arr_in = array(
                'where' => $where,
                'num_list' => 8,
                'temp_mod' => 'mod_item_user',
                'paginate' => 0,
            );
            $data['content'] = $ims->call->mFunc('product','html_list_item', array($arr_in));
        }else{
            $row['text'] = $ims->lang['user']['empty_favorite'];
            $ims->temp_act->assign('row', $row);
            $ims->temp_act->parse("list_favorite.empty");
        }
        $data["page_title"] = $ims->conf["meta_title"];
        $ims->temp_act->assign('data', $data);
        $ims->temp_act->parse("list_favorite");        
        return $ims->temp_act->text("list_favorite");
    }
    // End class
}
?>