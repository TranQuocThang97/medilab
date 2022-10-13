<?php
if (!defined('IN_ims')) { die('Access denied'); }
$nts = new sMain();

class sMain
{

    var $modules = "user";
    var $action  = "notifications";

    /**
        * Khởi tạo
        * Thông báo
    **/
    function __construct()
    {
        global $ims;
        $ims->conf['resource'] = $ims->conf['rooturl'].'resources/images/';
        $ims->conf['numshow'] = 4;
        //ajax api
        if(isset($ims->post["f"])){
            switch ($ims->post["f"]) {
                case 'reload':
                    echo $this->do_reload();
                    break;
                case 'update':
                    echo $this->do_update();
                    break;
                case 'delete':
                    echo $this->do_delete();
                    break;
                default:
                    // code...
                    break;
            }
            die;
        }

        $arrLoad = array(
            'modules'        => $this->modules,
            'action'         => $this->action,
            'template'       => $this->action,
            'js'             => $this->modules,
            'css'            => $this->modules,
            'use_func'       => $this->modules, // Sử dụng func
            'use_navigation' => 0, // Sử dụng navigation
            'required_login' => 1, // Bắt buộc đăng nhập
        );
        $ims->func->loadTemplate($arrLoad);        

        if (isset($ims->get['id'])) {
            $where = " AND item_id='".$ims->get['id']."' AND (type=0 OR FIND_IN_SET('".$ims->data['user_cur']['user_id']."', user_id)) ";
            $noti = $ims->db->load_row("user_notification", " is_show=1 ".$where);
            if (!empty($noti)) {
                $noti['content'] = $ims->func->input_editor_decode($noti['content']);
                $ims->conf['cur_item'] = $noti['item_id'];
                $ims->data['cur_item'] = $noti;
                //Make link lang
                $result = $ims->db->query("SELECT friendly_link,lang FROM user_notification WHERE ".$where);
                while ($row_lang = $ims->db->fetch_row($result)) {
                    $ims->data['link_lang'][$row_lang['lang']] = $ims->site_func->get_link_lang($row_lang['lang'], $this->modules, '', $row_lang['friendly_link']);
                }
                //End Make link lang
                //SEO
                $ims->site->get_seo($ims->data['cur_item']);
                $data = array();
                $data['content'] = $this->do_detail($ims->data['cur_item']);
            }
        } else {
            $data['content'] = $this->do_manage();
        }
        $data['box_left'] = box_left($this->action);        
        $ims->conf["class_full"] = 'user';
        $ims->temp_act->assign('data', $data);
        $ims->temp_act->parse("main");
        $ims->output .= $ims->temp_act->text("main");
    }

    function do_update_id(){
        global $ims;
        //update is_view by id
        if (isset($ims->input['selectid']) && !empty($ims->input['selectid'])) {             
            foreach ($ims->input['selectid'] as $key => $value) {           
                if(isset($ims->input['unread'])){
                    $no = $ims->db->load_row('user_notification','item_id="'.$value.'" and lang="'.$ims->conf['lang_cur'].'"');
                    if (isset($no['is_view']) && $no['is_view']!='') {
                        $arr_is_view = explode(',', $no['is_view']);
                        $arr_tmp = array();
                        $list_tmp = '';
                        foreach ($arr_is_view as $k => $v) {                        
                            if ($v!=$ims->data['user_cur']['user_id']) {
                               $arr_tmp[$v] = $v;
                            }
                            $list_tmp = implode(',', $arr_tmp);
                            $ok = $ims->db->query('UPDATE user_notification SET is_view="'.$list_tmp.'" WHERE item_id="'.$value.'" and lang="'.$ims->conf['lang_cur'].'"');
                        }
                    }
                }elseif(isset($ims->input['read'])){                    
                    $no = $ims->db->load_row('user_notification','item_id="'.$value.'" and lang="'.$ims->conf['lang_cur'].'"');
                    if (isset($no['is_view']) && $no['is_view']!='') {
                        $arr_is_view = explode(',', $no['is_view']);
                        $arr_tmp = array();
                        foreach ($arr_is_view as $k => $v) {
                            $arr_tmp[$v] = $v;
                            if (!isset($arr_tmp[$ims->data['user_cur']['user_id']])) {
                                $arr_tmp[$ims->data['user_cur']['user_id']] = $ims->data['user_cur']['user_id'];
                            }
                            $arr_tmp = implode(',', $arr_tmp);
                            $ims->db->query('UPDATE user_notification SET is_view="'.$arr_tmp.'" WHERE item_id="'.$value.'" and lang="'.$ims->conf['lang_cur'].'"');
                        }
                    }else{
                        $ok = $ims->db->query('UPDATE user_notification SET is_view="'.$ims->data['user_cur']['user_id'].'" WHERE item_id="'.$value.'" and lang="'.$ims->conf['lang_cur'].'"');
                    }
                }
            }
            if($ok){
                $link_go = $ims->site_func->get_link ("user", $ims->setting["user"]["notifications_link"]);
                $ims->html->redirect_rel($link_go);
            }
        }
    }

    //-----------
    function do_manage($is_show = "")
    {
        global $ims;        

        $err = $order = "";
        $data = array();
        $this->do_update_id();

        $data["icon_general"] = $ims->conf['resource'].'user/icon-general.png';
        $data["icon_promo"] = $ims->conf['resource'].'user/icon-promo.png';
        $data["icon_normal"] = $ims->conf['resource'].'user/icon-normal.png';
        
        $where = "lang='".$ims->conf['lang_cur']."' and is_show=1 and (type=0 OR find_in_set('".$ims->data["user_cur"]['user_id']."', user_id)) and find_in_set('".$ims->data["user_cur"]["user_id"]."',user_delete)<=0 and find_in_set('".$ims->data["user_cur"]["user_id"]."',is_view)<=0 and date_create >= '".$ims->data['user_cur']['date_create']."'";

        $promo = $ims->db->do_get_num("user_notification",$where." and type_of='promotion'");        
        $normal = $ims->db->do_get_num("user_notification",$where." and type_of='normal'");
        $general = $promo + $normal;
        // $viewed_promo = $ims->db->do_get_num

        $info_user = $ims->data['user_cur'];
        //paginate
        
        $data['nav'] = '';
        $num_total = 0;
        $start = 0;
        $link_action = $ims->conf['rooturl'].$ims->conf['cur_mod_url'];
        $ext = '';
        $n = $ims->conf['numshow'];
        
        $token_login = explode(",",$ims->data["user_cur"]["token_login"]);        
        $url_api = $ims->conf['rooturl'].'restfulapi/v1/staging/api.php/getNotification?user='.$token_login[0].'&numshow='.$n;
        $token = $ims->site_func->getRestfulToken();
        $result = $ims->site_func->sendPostData($url_api, array(), 'get', 0, $token);        
        $result = json_decode($result, true);

        if (!empty($result) && $result["code"]==200) {

            $p = $result["page"];
            $num_total = $result["total"];
            $num_items = ceil($num_total / $n);
            if ($p > $num_items)
                $p = $num_items;
            if ($p < 1)
                $p = 1;
            $start = ($p - 1) * $n;
            $data['nav'] = $ims->site->paginate_api($link_action, $num_total, $n, $ext, $p);

            $arr_noti = $result["data"];
            if(count($arr_noti)>0){
                $data['row_item'] = '';
                foreach ($arr_noti as $row) {                    
                    $data['row_item'] .= $this->manage_row($row);
                }                
            }else{
                $data['mess'] = $ims->lang['user']['empty_notifications'];
                $ims->temp_act->assign('data', $data);
                $ims->temp_act->parse("manage.empty");
            }
        }else{
            $data['mess'] = $ims->lang['user']['empty_notifications'];
            $ims->temp_act->assign('data', $data);
            $ims->temp_act->parse("manage.empty");
        }
        if($promo>0 || $normal>0){
            $general = 1;
        }
        $data["general"] = ($general!=0)?"unread":'';
        $data["promo"] = ($promo!=0)?"unread":'';
        $data["normal"] = ($normal!=0)?"unread":'';

        $data["link_action"] = $link_action;
        $data['page_title'] = $ims->conf["meta_title"];
        $ims->temp_act->assign('data', $data);
        $ims->temp_act->parse("manage");
        return $ims->temp_act->text("manage");
    }

    function manage_row($row = array()){
        global $ims;
        $output = '';        
        switch ($row['type_of']) {
            case 'normal':
                $row['icon'] = $ims->conf['resource'].'user/noti-normal.png';                
                break;
            case 'promotion':
                $row['icon'] = $ims->conf['resource'].'user/noti-promo.png';
                break;
            default:                
                break;
        }
        $row['link'] = $ims->conf['rooturl'].$ims->conf['cur_mod_url']."/?id=".$row['item_id'];
        $row['short'] = $ims->func->input_editor_decode($row['short']);
        $row['time'] = date('d/m/Y', $row['date_create']);        
        $row['class'] = ($row['status'] == "reading")?'reading':'';
        $ims->temp_act->assign('LANG', $ims->lang);
        $ims->temp_act->assign('row', $row);
        if($row['status'] == "reading"){
            $ims->temp_act->parse("manage.row.reading");
        }
        $ims->temp_act->parse("manage.row");
        $output = $ims->temp_act->text("manage.row");        
        $ims->temp_act->reset("manage.row");
        
        return $output;
    }

    function do_reload($p=1,$type_of=''){
        global $ims;
        include_once($ims->conf["rootpath"].DS."config".DS."xtemplate.class.php");
        $dir_view    = $ims->func->dirModules($this->modules, 'views', 'path');
        $ims->temp_act = new XTemplate($dir_view . $this->action . ".tpl");
        $ims->func->load_language("user");
        $output = array(
            'ok' => 0,
            'mess' => '',
            'html' => '',
        );
        if($ims->site_func->checkUserLogin() != 1) {
            $output['mess'] = $ims->lang['global']['signin_false'];
            return json_encode($output);
        }
        $n = $ims->conf['numshow'];
        $p = $ims->func->if_isset($ims->post["p"],$p);
        $type_of = $ims->func->if_isset($ims->post["type_of"],$type_of);
        $lang = $ims->func->if_isset($ims->post["lang"],"vi");

        $token_login = explode(",",$ims->data["user_cur"]["token_login"]);
        $url_api = $ims->conf['rooturl'].'restfulapi/v1/staging/api.php/getNotification?user='.$token_login[0].'&type_of='.$type_of.'&numshow='.$n.'&p='.$p;
        
        $token = $ims->site_func->getRestfulToken();
        $result = $ims->site_func->sendPostData($url_api, array(), 'get', 0, $token);        
        $result = json_decode($result, true);        
        if (!empty($result) && $result["code"]==200) {
            $ext = '';
            $link_action = $ims->conf['rooturl'].$ims->conf['cur_mod_url'];
            $p = $result["page"];
            $num_total = $result["total"];
            $num_items = ceil($num_total / $n);
            if ($p > $num_items)
                $p = $num_items;
            if ($p < 1)
                $p = 1;
            $start = ($p - 1) * $n;
            $data['nav'] = $ims->site->paginate_api($link_action, $num_total, $n, $ext, $p);

            $arr_noti = $result["data"];
            if(count($arr_noti)>0){
                $data['row_item'] = '';
                foreach ($arr_noti as $row) {                    
                    $data['row_item'] .= $this->manage_row($row);
                }
            }else{
                $data['mess'] = $ims->lang['user']['empty_notifications'];
                $ims->temp_act->assign('data', $data);
                $ims->temp_act->parse("list_content.empty");
            }            
            $ims->temp_act->assign('LANG', $ims->lang);
            $ims->temp_act->assign('data', $data);
            $ims->temp_act->parse("list_content");
            $output["html"] = $ims->temp_act->text("list_content");
            $output["ok"] = 1;
        }        
        return json_encode($output);        
    }

    function do_update(){
        global $ims;
        $output = array(
            'ok' => 0,
            'html' => '',
        );
        if($ims->site_func->checkUserLogin() != 1) {
            $output['mess'] = $ims->lang['global']['signin_false'];
            return json_encode($output);
        }
        $n = $ims->conf['numshow'];
        $p = $ims->func->if_isset($ims->post["p"],1);
        $type_of = $ims->func->if_isset($ims->post["type_of"]);
        $lang = $ims->func->if_isset($ims->post["lang"],"vi");

        $id = $ims->func->if_isset($ims->post["id"],0);
        $act = $ims->func->if_isset($ims->post["act"]);

        $token_login = explode(",",$ims->data["user_cur"]["token_login"]);
        $url_api = $ims->conf['rooturl'].'restfulapi/v1/staging/api.php/updateNotification?user='.$token_login[0].'&act='.$act;
        // print_r($url_api);
        $data = array(
            "item_id" => $id,
        );
        $token = $ims->site_func->getRestfulToken();
        $result = $ims->site_func->sendPostData($url_api, $data, 'post', 0, $token);        
        $result = json_decode($result, true);        
        if($result["code"] == 200){            
            $output = $this->do_reload($p,$type_of);            
        }else{
            $output = json_encode($output);
        }
        return $output;
    }

    function do_delete(){
        global $ims;
        $output = array(
            'ok' => 0,
            'mess' => '',
        );
        if($ims->site_func->checkUserLogin() != 1) {
            $output['mess'] = $ims->lang['global']['signin_false'];
            return json_encode($output);
        }
        $n = $ims->conf['numshow'];
        $p = $ims->func->if_isset($ims->post["p"],1);
        $type_of = $ims->func->if_isset($ims->post["type_of"]);
        $lang = $ims->func->if_isset($ims->post["lang"],"vi");

        $id = $ims->func->if_isset($ims->post["id"],0);
        $act = $ims->func->if_isset($ims->post["act"]);

        $token_login = explode(",",$ims->data["user_cur"]["token_login"]);
        $url_api = $ims->conf['rooturl'].'restfulapi/v1/staging/api.php/deleteNotification?user='.$token_login[0];
        // print_r($url_api);
        $data = array(
            "item_id" => $id,
        );
        if($act == 'delete_all'){
            $data["item_id"] = 0;
        }        
        $token = $ims->site_func->getRestfulToken();
        $result = $ims->site_func->sendPostData($url_api, $data, 'post', 0, $token);                
        $result = json_decode($result, true);
        if($result["code"] == 200){            
            $output = $this->do_reload($p,$type_of);            
        }else{
            $output = json_encode($output);
        }
        return $output;
    }


    function do_detail($info = array())
    {
        global $ims;
        $this->do_update_id();
        $info["icon_general"] = $ims->conf['resource'].'user/icon-general.png';
        $info["icon_promo"] = $ims->conf['resource'].'user/icon-promo.png';
        $info["icon_normal"] = $ims->conf['resource'].'user/icon-normal.png';
        
        $where = "lang='".$ims->conf['lang_cur']."' and is_show=1 and (type=0 OR find_in_set('".$ims->data["user_cur"]['user_id']."', user_id)) and find_in_set('".$ims->data["user_cur"]["user_id"]."',user_delete)<=0 and find_in_set('".$ims->data["user_cur"]["user_id"]."',is_view)<=0 ";

        $promo = $ims->db->do_get_num("user_notification",$where." and type_of='promotion'");
        $normal = $ims->db->do_get_num("user_notification",$where." and type_of='normal'");;
        $general = $promo + $normal;

        $info["general"] = ($general!=0)?"unread":'';
        $info["promo"] = ($promo!=0)?"unread":'';
        $info["normal"] = ($normal!=0)?"unread":'';

        $info_user = $ims->data['user_cur'];

        $info['active_promo'] = $info['active_normal'] = '';
        switch ($info['type_of']) {
            case 'promotion':
                $info['active_promo'] = 'active';
                break;            
            default:
                $info['active_normal'] = 'active';
                break;
        }

        $info['short'] = $ims->func->input_editor_decode($info['short']);
        $info['content'] = $ims->func->input_editor_decode($info['content']);
        $info['time'] = date('d/m/Y', $info['date_create']);
        $info['day'] = date('d', $info['date_create']);
        $info['month'] = date('m', $info['date_create']);
        $info['link'] = $ims->site_func->get_link('user',$ims->setting['user']['notifications_link']);
        $arr_user_view = array();
        if ($info['is_view'] == '') {
            $info['is_view'] = $info_user['user_id'];
            $view = array('is_view' => $info['is_view']);
            $ims->db->do_update('user_notification', $view, "item_id = '" . $info["item_id"] . "' and lang = '" .$ims->conf['lang_cur'] . "'");
        } else {
            $arr_user_view = explode(',', $info['is_view']);
            if (in_array($info_user['user_id'], $arr_user_view) == false) {
                $info['is_view'] = $info['is_view'] . ',' . $info_user['user_id'];
                $view = array('is_view' => $info['is_view']);
                $ims->db->do_update('user_notification', $view, "item_id = '" . $info["item_id"] . "' and lang = '" .$ims->conf['lang_cur'] . "'");
            }
        }
        $info['page_title'] = $ims->setting["user"]["notifications_meta_title"];

        $ims->temp_act->assign('data', $info);
        $ims->temp_act->parse("item_detail");
        return $ims->temp_act->text("item_detail");        
    }

    public function get_title_product_group($group_id = '')
    {
        global $ims;
        $sql = "SELECT title FROM product_group WHERE group_id = " . $group_id . " AND is_show = 1 AND lang='" . $ims->conf['lang_cur'] . "'  ";
        $query = $ims->db->query($sql);
        $arr = $ims->db->fetch_row($query);
        return isset($arr['title']) ? $arr['title'] : '';
    }
    // end class
}

?>
