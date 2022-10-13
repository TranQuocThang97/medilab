<?php

/* ================================================================================*\
  Name code : view.php
  Copyright © 2013 by Tran Thanh Hiep
  @version : 1.0
  @date upgrade : 03/02/2013 by Tran Thanh Hiep
  \*================================================================================ */

if (!defined('IN_ims')) {
    die('Access denied');
}
$nts = new sMain();

class sMain
{

    var $modules = "user";
    var $action = "user_gifts";
    var $sub = "manage";
    var $dbtable = 'user';
    var $dbtable_id = 'user_id';
    var $check_get = '';

    /**
     * function __construct ()
     * */
    function __construct()
    {
        global $ims;
        if ($ims->site_func->checkUserLogin() != 1) {
            $url = $ims->func->base64_encode($_SERVER['REQUEST_URI']);
            $url = (!empty($url)) ? '/?url=' . $url : '';

            $link_go = $ims->site->get_link($this->modules, $ims->setting[$this->modules]["signin_link"]) . $url;
            $ims->html->redirect_rel($link_go);
        }

        $ims->func->load_language($this->modules);
        $ims->temp_act = new XTemplate($ims->path_html . $this->modules . DS . $this->action . ".tpl");
        $ims->temp_act->assign('CONF', $ims->conf);
        $ims->temp_act->assign('LANG', $ims->lang);
        $ims->temp_act->assign('DIR_IMAGE', $ims->dir_images);

        $ims->func->include_css($ims->dir_css . $this->modules . '/' . $this->modules . '.css');

        $ims->conf['menu_action'] = array($this->modules);
        $ims->data['link_lang'] = (isset($ims->data['link_lang'])) ? $ims->data['link_lang'] : array();

        require_once($this->modules . "_func.php");


        $data = array();

        $breadcrumb = array(
            'manage' => array(
                'link' => $ims->site->get_link($this->modules, $ims->setting[$this->modules]["user_notifications_link"]),
                'class' => 'icon-list',
                'title' => $ims->setting[$this->modules]["user_notifications_meta_title"]
            )
        );
        if (isset($ims->get['id'])) {
            $where = " and item_id='" . $ims->get['id'] . "' and find_in_set(".$ims->data['user_cur']['level_id'].",apply_group)>0 ";

            $result = $ims->db->query("select *
										from user_promotion
										where is_show=1
										" . $where . "
										limit 0,1");
            if ($row = $ims->db->fetch_row($result)) {
                $row['content'] = $ims->func->input_editor_decode($row['content']);
                $ims->conf['cur_item'] = $row['item_id'];
                $ims->data['cur_item'] = $row;
                //Make link lang
                $result = $ims->db->query("select friendly_link,lang
											from user_promotion
											where item_id='" .$ims->get['id'] . "' ");
                while ($row_lang = $ims->db->fetch_row($result)) {
                    $ims->data['link_lang'][$row_lang['lang']] = $ims->site->get_link_lang($row_lang['lang'], $this->modules, '', $row_lang['friendly_link']);
                }
                //End Make link lang
                //SEO
                $ims->site->get_seo($ims->data['cur_item']);
                $data = array();
                $data['content'] = $this->do_detail($ims->data['cur_item']);
            }
        } else {
            //Make link lang
            foreach ($ims->data['lang'] as $row_lang) {
                $ims->data['link_lang'][$row_lang['name']] = $ims->site->get_link_lang($row_lang['name'], $this->modules);
            }
            //End Make link lang
            //SEO
            $ims->site->get_seo(array(
                'meta_title' => (isset($ims->setting[$this->modules][$this->action . "_meta_title"])) ? $ims->setting[$this->modules][$this->action . "_meta_title"] : '',
                'meta_key' => (isset($ims->setting[$this->modules][$this->action . "_meta_key"])) ? $ims->setting[$this->modules][$this->action . "_meta_key"] : '',
                'meta_desc' => (isset($ims->setting[$this->modules][$this->action . "_meta_desc"])) ? $ims->setting[$this->modules][$this->action . "_meta_desc"] : ''
            ));

            // $ims->conf["page_title"] = $ims->conf['meta_title'];

            $ims->conf["cur_group"] = 0;
            $data['content'] = $this->do_manage();
        }

        $data['title'] = (isset($ims->setting[$this->modules][$this->action . "_meta_title"])) ? $ims->setting[$this->modules][$this->action . "_meta_title"] : '';

        $data['box_left'] = box_left($this->action);
        $ims->conf["class_full"] = 'user';
        $ims->temp_act->assign('data', $data);
        $ims->temp_act->parse("main");
        $ims->output .= $ims->temp_act->text("main");
    }

    //-----------
    function do_manage($is_show = ""){
        global $ims;
        $data = array();
        $where = ' AND time_begin<"'.date('H:i:s').'" AND time_end>"'.date('H:i:s').'" AND date_begin<"'.time().'" AND date_end > "'.time().'"  and find_in_set('.$ims->data['user_cur']['level_id'].',apply_group)>0 ';
        //paginate
        $data['nav'] = '';
        $num_total = 0;
        $start = 0;
        $link_action = $ims->conf['rooturl'].$ims->conf['cur_mod_url'];
        $ext = '';
        $p = (isset($ims->input["p"])) ? $ims->input["p"] : 1;
        $n = 3;        
        $res_num = $ims->db->query("select item_id 
                        from user_promotion 
                        where is_show=1 
                        and lang='" . $ims->conf["lang_cur"] . "' 
                        " . $where . " ");
        $num_total = $ims->db->num_rows($res_num);
        $num_items = ceil($num_total / $n);
        if ($p > $num_items)
            $p = $num_items;
        if ($p < 1)
            $p = 1;
        $start = ($p - 1) * $n;                
        $data['nav'] = $ims->site->paginate($link_action, $num_total, $n, $ext, $p);        
        // 
        $arr_promotion = $ims->db->load_item_arr('user_promotion','lang="'.$ims->conf['lang_cur'].'" AND is_show=1'.$where.'
                        limit '.$start.','.$n,'item_id,title,picture,short,date_begin,date_end,time_begin,time_end');       
        if($arr_promotion){            
            foreach ($arr_promotion as $row){                 
                $row['link'] = $ims->conf['rooturl'].$ims->conf['cur_mod_url']."/?id=".$row['item_id'];
                $row['title'] = $ims->func->input_editor_decode($row['title']);
                $row['short'] = $ims->func->input_editor_decode($row['short']);
                $row['picture'] = $ims->func->get_src_mod($row['picture'],275,180);
                $row['date_begin'] = date('d-m-Y',$row['date_begin']);
                $row['date_end'] = date('d-m-Y',$row['date_end']);
                $ims->temp_act->assign('row', $row);
                $ims->temp_act->parse("manage.row.col");
            }
            $ims->temp_act->assign('data', $data);
            $ims->temp_act->parse("manage.row");
        }else{
            $ims->temp_act->assign('data', array('text' => $ims->lang['user']['empty_gift']));
            $ims->temp_act->parse("manage.empty");
        }
        $ims->temp_act->parse("manage");
        return $ims->temp_act->text("manage");
    }

    function do_detail($info = array()){
        global $ims;
        $temp = array();
        $data = $info;
        $data['picture'] = $ims->func->get_src_mod($data['picture'],275,180);
        $data['title'] = $ims->func->input_editor_decode($data['title']);
        $data['short'] = $ims->func->input_editor_decode($data['short']);        
        $data['level'] = get_level_user($ims->data['user_cur']['total_wcoin']);
        $data['level_gift'] = $ims->lang['user']['list_gift'].' '.$data['level'];
        $data['type'] = 'submit';
        $data['class'] = $data['mess'] = '';
        //selected
        $arr_log = $ims->db->load_item_arr('user_gift_log','user_id="'.$ims->data['user_cur']['user_id'].'" and promotion_id="'.$data['item_id'].'"','gift_id,code');
        if($arr_log){
            $data['type'] = 'button';
            $data['class'] = 'selected';            
            foreach ($arr_log as $log) {
                array_push($temp,$log['gift_id']);
                $log['mess'] = '<div class="item"><b>'.$log['code'].'</b> - '.$this->get_gift_name($log['gift_id']).'</div>';
                $ims->temp_act->assign('log', $log);
                $ims->temp_act->parse("item_detail.mess.log");
            }
            $ims->temp_act->assign('data', $data);
            $ims->temp_act->parse("item_detail.mess");
        }
        //list gift
        $arr_gift = $ims->db->load_row_arr('user_gift','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and pet_id="'.$ims->data['user_cur']['pet_id'].'" and level_id="'.$ims->data['user_cur']['level_id'].'"');
        $data['value'] = count($arr_gift)<=(int)$data['value']?count($arr_gift):$data['value'];
        if ($arr_gift) {
            $pic_w = 400;
            $pic_h = 460;
            foreach ($arr_gift as $row) {
                $row['picture'] = $ims->func->get_src_mod($row['picture'],$pic_w,$pic_h,1,0,array());
                $row['title'] = $ims->func->input_editor_decode($row['title']);
                if(in_array($row['item_id'],$temp)){
                    $row['class'] = 'selected';
                }
                $ims->temp_act->assign('row', $row);
                $ims->temp_act->parse("item_detail.row.col");
            }
            $data['ongoing_event'] = $ims->lang['user']['ongoing_event'];
            // $temp = strtotime($promo['time_end']) - time();
            $temp = $data['date_end'] - time();
            $ims->func->include_js_content('
                var deadline = new Date(Date.parse(new Date()) + '.$temp.'*1000);
                initializeClock(".clocke", deadline);                       
            ');
            $ims->temp_act->assign('data', $data);
            $ims->temp_act->parse("item_detail.row");
        } else {            
            $ims->temp_act->assign('data', array('text' => $ims->lang['user']['empty_gift']));
            $ims->temp_act->parse("item_detail.empty");
        }
        $ims->temp_act->assign('data', $data);
        $ims->temp_act->parse("item_detail");
        return $ims->temp_act->text("item_detail");        
    }
    function get_gift_name($item_id=0){
        global $ims;
        $output = '';
        $arr_gift = $ims->db->load_row_arr('user_gift','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and pet_id="'.$ims->data['user_cur']['pet_id'].'" and level_id="'.$ims->data['user_cur']['level_id'].'"');
        if(!empty($arr_gift)){
            foreach ($arr_gift as $key => $value) {
                if($item_id == $value['item_id']){
                    $output = $value['title'];
                }
            }
        }
        return $output;
    }
    // end class
}

?>