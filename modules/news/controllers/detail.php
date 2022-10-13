<?php
if (!defined('IN_ims')) { die('Access denied'); }
$nts = new sMain();

class sMain {

    var $modules = "news";
    var $action  = "detail";
    var $sub     = "manage";

    function __construct() {
        global $ims;

        $arrLoad = array(
            'modules'        => $this->modules,
            'action'         => $this->action,
            'template'       => $this->modules,
            'css'            => $this->modules,
            'use_func'       => $this->modules, // Sử dụng func
            'use_navigation' => 1, // Sử dụng navigation
            'required_login' => 0, // Bắt buộc đăng nhập
        );
        $ims->func->loadTemplate($arrLoad); 

        require_once ($this->modules . "_func.php");
        $this->modFunc = new newsFunc($this);

        $data = array();
        if (isset($ims->conf['cur_item']) && isset($ims->data['cur_item']) && $ims->data['cur_item']) {

            $ims->db->query("UPDATE news set num_view=num_view+1 WHERE item_id='".$ims->data['cur_item']['item_id']."'");

            $row = $ims->data['cur_item'];
            //Make link lang
            $result = $ims->db->query("SELECT friendly_link,lang FROM news WHERE item_id='".$ims->conf['cur_item']."' ");
            while ($row_lang = $ims->db->fetch_row($result)) {
                $ims->data['link_lang'][$row_lang['lang']] = $ims->site_func->get_link_lang($row_lang['lang'], $this->modules, '', $row_lang['friendly_link']);
            }
            //End Make link lang

            //SEO
            $ims->site->get_seo($ims->data['cur_item']);

            $ims->conf["cur_group"]     = $row["group_id"];
            $ims->conf["cur_group_nav"] = $row["group_nav"];
            $ims->conf["meta_image"]    = $ims->func->get_src_mod($row["picture"], 630, 420, 1, 1);

            //Current menu
            $arr_group_nav = (!empty($ims->conf["cur_group_nav"])) ? explode(',', $ims->conf["cur_group_nav"]) : array();
            foreach ($arr_group_nav as $v) {
                $ims->conf['menu_action'][] = $this->modules . '-group-' . $v;
            }
            $ims->conf['menu_action'][] = $this->modules . '-item-' . $ims->conf['cur_item'];
            //End current menu

            $data['content'] = $this->do_detail($row);
        } else {
            $ims->html->redirect_rel($ims->site_func->get_link($this->modules));
        }
        $ims->conf['container_layout'] = 'm';
        $ims->conf['class_full'] = 'detail';
        $ims->temp_act->assign('data', $data);
        $ims->temp_act->parse("main");
        $ims->output .= $ims->temp_act->text("main");
    }

    function do_detail($info = array()) {
        global $ims;

        $data = $info;
        $data['picture']     = $ims->func->get_src_mod($data['picture']);
        $data['date_create'] = date('d/m/Y', $data['date_create']);
        $data['group_name'] = $this->modFunc->get_group_name($info['group_id'], 'link');
//        $data['my_update']   = $ims->func->rebuild_date('F, Y', $data['date_create']);
        $data['content']     = $ims->func->input_editor_decode($data['content']);
//        $data["link_share"]  = $ims->site_func->get_link('news', '', $info['friendly_link']);
        $data['other']       = $this->do_other($info);

        $ims->temp_act->assign('data', $data);
        $ims->temp_act->parse("detail");
        $content = $ims->temp_act->text("detail");
        $data = array(
            'content' => $content,
            'most_read' => $this->do_most_read()
        );
        $ims->temp_act->assign('data', $data);
        $ims->temp_act->parse("main_news");
        return $ims->temp_act->text("main_news");
    }

    function do_other($info) {
        global $ims;

        $arr_in = array(
            'where' => " and item_id != '" . $info['item_id'] . "' ",
            'num_list' => $ims->setting['news']["num_order_detail"],
            'pic_w' => 291,
            'pic_h' => 123,
            'paginate' => 0,
        );
        if ($info['group_id'] > 0) {
            $arr_in['where'] .= "and ( 
                find_in_set('" . $info['group_id'] . "',group_nav)>0 
                or find_in_set('" . $info['group_id'] . "',group_related)>0 
            )";
        }
        $check = $ims->db->load_item('news', $ims->conf['qr'].$arr_in['where'], 'item_id');
        if($check){
            $ims->temp_act->assign('content', $this->modFunc->html_list_item($arr_in));
            $ims->temp_act->parse("list_other");
            return $ims->temp_act->text("list_other");
        }
    }
    // end class
    function do_most_read(){
        global $ims;
        $limit = ($ims->setting['news']['num_order_detail']) ? $ims->setting['news']['num_order_detail'] : 8;
        $result = $ims->db->load_item_arr('news', $ims->conf['qr'].' order by num_view desc, date_create desc limit '.$limit, 'title, picture, friendly_link');
        if($result){
            foreach ($result as $row){
                $row['link'] = $ims->func->get_link($row['friendly_link'], '');
                $row['picture'] = $ims->func->get_src_mod($row['picture'], 104, 55, 1, 1);
                $ims->temp_act->assign('row', $row);
                $ims->temp_act->parse("most_read.item");
            }
            $ims->temp_act->parse("most_read");
            return $ims->temp_act->text("most_read");
        }
    }
}

?>