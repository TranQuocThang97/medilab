<?php
if (!defined('IN_ims')) { die('Access denied'); }
function load_setting (){
    global $ims;

    $ims->site_func->setting('user');
    $ims->site_func->setting('shared');
    return true;
}
load_setting ();

$nts = new sMain();

class sMain
{
    var $modules = "shared_comment";
    var $action = "ajax";

    function __construct () {
        global $ims;

        $ims->func->load_language($this->modules);

        $fun = (isset($ims->post['f'])) ? $ims->post['f'] : '';

        switch ($fun) {
            case "post_comment":
                echo $this->do_post_comment ();
                exit;
                break;
            case "load_comment":
                echo $this->do_load_comment ();
                exit;
                break;
            case "postFavorite":
                echo $this->do_postFavorite ();
                exit;
                break;
            case "postRate":
                echo $this->do_postRate ();
                exit;
                break;
            default:
                echo '';
                exit;
                break;
        }
        flush();
        exit;
    }
    function do_postRate(){
        global $ims;
        $value 		= isset($ims->input['value']) ? $ims->input['value'] : '';
        $type_id 	= isset($ims->input['type_id']) ? $ims->input['type_id'] : '';
        $type 		= isset($ims->input['type']) ? $ims->input['type'] : '';
        $output = array(
            'ok' => 0,
            'mess' => $ims->lang['global']['rate_false']
        );
        if($ims->site_func->checkUserLogin() != 1) {
            $output['mess'] = $ims->lang['global']['signin_false'];
            return json_encode($output);
        }
        if ($value >= 1 && $value <= 5) {
            $array = array();
            $array['type'] = $type;
            $array['type_id'] = $type_id;
            $array['rate'] = $value;
            $array['lang'] = $ims->conf['lang_cur'];
            $array['user_id'] = $ims->data['user_cur']['user_id'];
            $array['date_create'] = time();
            $array['date_update'] = time();

            $check = $ims->db->load_item('shared_rate', 'type_id="'.$type_id.'" and user_id="'.$ims->data['user_cur']['user_id'].'" and type="'.$type.'" ','id');
            if ($check > 0) {
                $output['mess'] = $ims->lang['global']['rate_exist'];
                return json_encode($output);
            }

            $ok = $ims->db->do_insert('shared_rate',$array);
            if ($ok) {
                if( $type == 'product'){
                    $sql = "SELECT * FROM shared_comment WHERE type='product' and type_id = '".$type_id."' AND is_show = 1 and lang='".$ims->conf['lang_cur']."' and rate!=0";
                    $query = $ims->db->query($sql);
                    $output['num'] = $ims->db->num_rows($query);
                    while ($row = $ims->db->fetch_row($query)) {
                        $total_rate += $row['rate'];
                    }
                    $col['num_rate'] = round($total_rate/$output['num'],1);
                    $ims->db->do_update("product", $col, " item_id='".$type_id."'");
                }
                $output = array(
                    'ok' => 1,
                    'mess' => $ims->lang['global']['rate_success']
                );
            }
        }
        return json_encode($output);
    }
    function do_postFavorite() {
        global $ims;

        $type_id 	= isset($ims->input['like']) ? $ims->input['like'] : '';
        $type 		= isset($ims->input['type']) ? $ims->input['type'] : '';
        $output = array(
            'ok' => 2,
            'mess' => ''
        );
        if($ims->site_func->checkUserLogin() != 1) {
            $output['ok'] = 0;
            $output['mess'] = $ims->lang['global']['signin_false'];
            return json_encode($output);
        }

        $type_id = $ims->func->base64_decode($type_id);
        $check = $ims->db->load_item('shared_favorite', 'type_id="'.$type_id.'" and user_id="'.$ims->data['user_cur']['user_id'].'" and type="'.$type.'" ','id');
        if ($check > 0) {
            $ims->db->query('DELETE FROM  shared_favorite WHERE id="'.$check.'" ');
            // Cập nhật lượt thích
            $output['type_id'] = $type_id;
            $ims->db->query('UPDATE shared_comment SET num_like=num_like-1 WHERE lang="'.$ims->conf['lang_cur'].'" AND item_id="'.$type_id.'"');
            $output['num_like'] = $ims->db->load_item('shared_comment',' lang="'.$ims->conf['lang_cur'].'" AND item_id="'.$type_id.'" ','num_like');
            $output['ok'] = 2;
            return json_encode($output);
        }
        $array = array();
        $array['id'] 		  = $ims->db->getAutoIncrement('shared_favorite');
        $array['type'] 		  = $type;
        $array['type_id']     = $type_id;
        $array['lang']        = $ims->conf['lang_cur'];
        $array['user_id']     = $ims->data['user_cur']['user_id'];
        $array['date_create'] = time();
        $array['date_update'] = time();
        $ok = $ims->db->do_insert('shared_favorite',$array);
        if ($ok) {
            $output = array(
                'ok' => 1,
                // 'mess' => $ims->lang['global']['rate_success']
            );
            $output['type_id'] = $array['type_id'];
            // Cập nhật lượt thích
            $ims->db->query('UPDATE shared_comment SET num_like=num_like+1 WHERE lang="'.$ims->conf['lang_cur'].'" AND item_id="'.$array['type_id'].'"');
            $output['num_like'] = $ims->db->load_item('shared_comment',' lang="'.$ims->conf['lang_cur'].'" AND item_id="'.$array['type_id'].'" ','num_like');
        }
        return json_encode($output);
    }
    function do_post_comment_bo() {
        global $ims;

        include_once($ims->conf["rootpath"].DS."inc".DS."xtemplate.class.php");
        $ims->temp_box = new XTemplate($ims->path_html."box.tpl");
        $arr_in = array();

        $input = $ims->func->if_isset($ims->post, array());

        if($ims->site_func->checkUserLogin() != 1) {
            $product_link = $ims->db->load_item($input['type'], ' is_show=1 and lang="'.$ims->conf['lang_cur'].'" and item_id="'.$input['type_id'].'" ','friendly_link');
            $product_link = $ims->site_func->get_link ('product', $product_link);
            $url = $ims->func->base64_encode($product_link);
            $url = (!empty($url)) ? '/?url='.$url : '';
            $link_login = $ims->site_func->get_link ('user', $ims->setting['user']['signin_link']).$url;

            $output['mess'] = $ims->lang['global']['signin_false'].' <a href="'.$link_login.'"> Click vào đây</a>';
            return json_encode($output);
        }
        $value = isset($ims->post['rate'])?$ims->post['rate']:0;
        $output = array(
            'ok' => 0,
            'mess' => $ims->site_func->get_lang ('error_comment', 'global')
        );
        $count=0;

        if (isset($_FILES['files'])) {
            foreach ($_FILES['files'] as $k_file => $v_file) {
                if (!empty($v_file) && $k_file=='name') {
                    foreach ($v_file as $k => $v) {
                        if ($v!='') {
                            $count++;
                        }
                    }
                }
            }
            if ($count>3) {
                $output['mess'] = $ims->lang['global']['max_num_file'].' 3';
                return json_encode($output);
            }
            $folder_upload = "user/".$ims->data['user_cur']['folder_upload'].'/'.date('Y',time()).'_'.date('m',time());
            $out_pic = array();
            $out_pic = $ims->site_func->upload_image_multi($folder_upload,'files');
            if($out_pic['ok'] ==1){
                $arr_in['picture'] = $out_pic['url_picture'];
            }
        }
        if($ims->site_func->checkUserLogin() == 1) {
            // Kiểm tra Spam
            $sql = "SELECT date_create FROM shared_comment WHERE 
						user_id = '".$ims->data['user_cur']['user_id']."' AND 
						is_show = 1 AND 
						lang ='". $ims->conf['lang_cur'] ."' AND 
						type_id = '".$input['type_id']."' AND
						type = '".$input['type']."' 
						ORDER BY date_create DESC LIMIT 0,5";
            $query = $ims->db->query($sql);
            $num_spam = $ims->db->num_rows($query);
            if($num_spam == 5){
                $i_spam = 0;
                while ($row = $ims->db->fetch_row($query)) {
                    if($i_spam == 4){
                        $check_spam = time() - $row['date_create'];
                        if($check_spam < 600){
                            $output['mess'] = $ims->site_func->get_lang ('spam_comment', 'global');
                            return json_encode($output);
                        }
                    }
                    $i_spam++;
                }
            }
        }
        $arr_in['id'] 		     = $ims->db->getAutoIncrement('shared_comment');
        $arr_in['item_id'] 	     = $arr_in['id'];
        $arr_in["content"]       = $ims->func->input_editor($input['txtaComment']);
        $arr_in["video"]       	 = $ims->func->if_isset($input['txtVideo']);
        if($ims->site_func->checkUserLogin() == 1) {
            $arr_in["full_name"] = $ims->data['user_cur']['full_name'];
            $arr_in["email"]     = $ims->data['user_cur']['email'];
            $arr_in["phone"]     = $ims->data['user_cur']['phone'];
            $arr_in["user_id"]   = $ims->data['user_cur']['user_id'];
        }else{
            $arr_in["full_name"] = $ims->func->if_isset($input['txtName']);
            $arr_in["email"] 	 = $ims->func->if_isset($input['txtEmail']);
            $arr_in["phone"]     = $ims->func->if_isset($input['txtPhone']);
            $arr_in["user_id"]   = 0;
        }
        $arr_in["type"]          = $ims->func->if_isset($input['type']);
        $arr_in["type_id"]       = $ims->func->if_isset($input['type_id']);
        $arr_in["parent_id"]     = $ims->func->if_isset($input['parent_id']);
        $arr_in["lang"]          = $ims->conf['lang_cur'];
        $arr_in["is_show"]       = 0;
        $arr_in["num_comment"]   = 0;
        $arr_in["num_like"]      = 0;
        if ($value >= 1 && $value <= 5) {
            $arr_in['rate'] = $value;
        }
        $arr_in["date_create"]   = time();
        $arr_in["date_update"]   = time();
        $ok = $ims->db->do_insert("shared_comment", $arr_in);
        if($ok) {
            $output = $arr_in;
            if ($arr_in["parent_id"] > 0) {
                // Cập nhật số nhận xét
                $ims->db->query('UPDATE shared_comment SET num_comment=num_comment+1 WHERE lang="'.$ims->conf['lang_cur'].'" AND item_id="'.$arr_in["parent_id"].'"');
                $output['num_comment_parent'] = $ims->db->load_item('shared_comment',' lang="'.$ims->conf['lang_cur'].'" AND item_id="'.$arr_in["parent_id"].'" ','num_comment');
            }
            $output['ok'] = 1;
            $data = array();
            $data['item_id'] = $arr_in["type_id"];
            $data['type'] = $arr_in["type"];
            $output['html'] = $ims->site_func->item_comment($arr_in, $data);
            $output['html'] .='
                <script type="text/javascript"> 
				    SharedComment.post_comment("sub_'.$arr_in['item_id'].'form"); 
				</script>';
            $output['mess'] = $ims->site_func->get_lang ('success_comment', 'global');
        }
        return json_encode($output);
    }
    function do_load_comment_bo() {
        global $ims;
        include_once($ims->conf["rootpath"].DS."inc".DS."xtemplate.class.php");
        $ims->temp_box = new XTemplate($ims->path_html."box.tpl");
        $output = array(
            'ok' => 0,
            'mess' => '',
            'html' => ''
        );
        $load_sub = 0;
        $data 			   = array();
        $num_show 		   = $ims->setting['shared']['numshow_comment'];
        $start 			   = $ims->post['start'];
        $data['item_id']   = $ims->post['type_id'];
        $data['parent_id'] = $ims->func->if_isset($ims->post['parent_id']);
        $data['type']      = $ims->post['type'];
        // LIST_COMMENT
        $where = '';
        if ($data['parent_id']) {
            $load_sub = 1;
            $where = 'parent_id='.$data['parent_id'].' AND ';
        }else{
            $where = 'parent_id=0 AND ';
        }
        $num_rows_count = $ims->db->do_get_num("shared_comment", "".$where." type_id=".$data['item_id']." and lang='".$ims->conf['lang_cur']."' and type='".$data['type']."'  ");
        if($num_rows_count - $start < $num_show){
            $num_show = $num_rows_count - $start;
        }
        $arr_comment = $ims->db->load_row_arr("shared_comment","".$where." is_show=1 AND type_id=".$data['item_id']." AND type='".$data['type']."' AND lang='".$ims->conf['lang_cur']."' ORDER BY date_create DESC LIMIT ".$start." , ". $num_show ."");
        if (!empty($arr_comment)) {
            foreach ($arr_comment as $key => $row) {
                if ($load_sub == 1) {
                    $row['load_sub'] = 1;
                }
                $output['html'] .= $ims->site_func->item_comment($row, $data);
                $output['html'] .='
                <script type="text/javascript"> 
				    SharedComment.post_comment("sub_'.$row['item_id'].'form"); 
				</script>';
            }
            $output['start'] = $start + $num_show;
            $output['max'] = $num_rows_count;
        }
        return json_encode($output);
    }

    function do_post_comment() {
        global $ims;

        $ims->site_func->setting('shared');

        include_once($ims->conf["rootpath"].DS."config".DS."xtemplate.class.php");
        $ims->temp_box = new XTemplate($ims->path_html."box.tpl");
        $arr_in = array();

        $input = $ims->func->if_isset($ims->post, array());

        if($ims->setting['shared']['is_requiredlogin'] == 1 && $ims->site_func->checkUserLogin() != 1) {
            $product_link = $ims->db->load_item($input['type'], ' is_show=1 and lang="'.$ims->conf['lang_cur'].'" and item_id="'.$input['type_id'].'" ','friendly_link');
            $product_link = $ims->site_func->get_link ('product', $product_link);
            $url = $ims->func->base64_encode($product_link);
            $url = (!empty($url)) ? '/?url='.$url : '';
            $link_login = $ims->site_func->get_link ('user', $ims->setting['user']['signin_link']).$url;

            $output['mess'] = $ims->lang['global']['signin_false'].' <a href="'.$link_login.'"> Click vào đây</a>';
            return json_encode($output);
        }

        // Chỉ cho comment khi mua sản phẩm đó
        $comment_when_bought = $ims->db->load_item('product_setting',' is_show = 1 and lang = "'.$ims->conf['lang_cur'].'" and setting_key = "comment_when_bought"', 'setting_value');
        if($comment_when_bought == 1){
            $complete = $ims->db->load_item('product_order_status', 'is_show = 1 and lang = "'.$ims->conf['lang_cur'].'" and is_complete = 1', 'item_id');
            $check_order = $ims->db->load_row('product_order as po, product_order_detail as pod', 'po.is_show = 1 and pod.order_id = po.order_id and pod.type_id = '.$input['type_id'].' and po.user_id = '.$ims->data['user_cur']['user_id'].' and po.is_status != 0', 'po.is_status');
            if($check_order){
                if($check_order['is_status'] != $complete){
                    $output['mess'] = $ims->lang['global']['cant_comment_without_complete_order'];
                    return json_encode($output);
                }
            }else{
                $output['mess'] = $ims->lang['global']['cant_comment_without_order'];
                return json_encode($output);
            }
        }
        // Chỉ cho comment khi mua sản phẩm đó

        $value = isset($ims->post['rate'])?$ims->post['rate']:0;
        $output = array(
            'ok' => 0,
            'mess' => $ims->site_func->get_lang ('error_comment', 'global')
        );
        $count=0;

        if (isset($_FILES['files'])) {
            foreach ($_FILES['files'] as $k_file => $v_file) {
                if (!empty($v_file) && $k_file=='name') {
                    foreach ($v_file as $k => $v) {
                        if ($v!='') {
                            $count++;
                        }
                    }
                }
            }
            if ($count>3) {
                $output['mess'] = $ims->lang['global']['max_num_file'].' 3';
                return json_encode($output);
            }
            $folder_upload = "user/".$ims->data['user_cur']['folder_upload'].'/'.date('Y',time()).'_'.date('m',time());
            $out_pic = array();
            $out_pic = $ims->site_func->upload_image_multi($folder_upload,'files');
            if($out_pic['ok'] ==1){
                $arr_in['picture'] = $out_pic['url_picture'];
            }
        }
        if($ims->site_func->checkUserLogin() == 1) {
            // Kiểm tra Spam
            $sql = "SELECT date_create FROM shared_comment WHERE 
						user_id = '".$ims->data['user_cur']['user_id']."' AND 
						is_show = 1 AND 
						lang ='". $ims->conf['lang_cur'] ."' AND 
						type_id = '".$input['type_id']."' 
						type = '".$input['type']."' 
						ORDER BY date_create DESC LIMIT 0,5";
            $query = $ims->db->query($sql);
            $num_spam = $ims->db->num_rows($query);
            if($num_spam == 5){
                $i_spam = 0;
                while ($row = $ims->db->fetch_row($query)) {
                    if($i_spam == 4){
                        $check_spam = time() - $row['date_create'];
                        if($check_spam < 600){
                            $output['mess'] = $ims->site_func->get_lang ('spam_comment', 'global');
                            return json_encode($output);
                        }
                    }
                    $i_spam++;
                }
            }
        }
        $arr_in['id'] 		     = $ims->db->getAutoIncrement('shared_comment');
        $arr_in['item_id'] 	     = $arr_in['id'];
        $arr_in["content"]       = $ims->func->input_editor($input['txtaComment']);
        $arr_in["video"]       	 = $ims->func->if_isset($input['txtVideo']);
        if($ims->site_func->checkUserLogin() == 1) {
            $arr_in["full_name"] = $ims->data['user_cur']['full_name'];
            $arr_in["email"]     = $ims->data['user_cur']['email'];
            $arr_in["phone"]     = $ims->data['user_cur']['phone'];
            $arr_in["user_id"]   = $ims->data['user_cur']['user_id'];
        }else{
            $arr_in["full_name"] = $ims->func->if_isset($input['txtName']);
            $arr_in["email"] 	 = $ims->func->if_isset($input['txtEmail']);
            $arr_in["phone"]     = $ims->func->if_isset($input['txtPhone']);
            $arr_in["user_id"]   = 0;
        }
        $arr_in["type"]          = $ims->func->if_isset($input['type']);
        $arr_in["type_id"]       = $ims->func->if_isset($input['type_id']);
        $arr_in["parent_id"]     = $ims->func->if_isset($input['parent_id']);
        $arr_in["lang"]          = $ims->conf['lang_cur'];
        $arr_in["is_show"]       = 0;
        $arr_in["num_comment"]   = 0;
        $arr_in["num_like"]      = 0;
        if ($value >= 1 && $value <= 5) {
            $arr_in['rate'] = $value;
        }
        $arr_in["date_create"]   = time();
        $arr_in["date_update"]   = time();

        $ok = $ims->db->do_insert("shared_comment", $arr_in);
        if($ok) {
            $output = $arr_in;
            if ($arr_in["parent_id"] > 0) {
                // Cập nhật số nhận xét
                $ims->db->query('UPDATE shared_comment SET num_comment=num_comment+1 WHERE lang="'.$ims->conf['lang_cur'].'" AND item_id="'.$arr_in["parent_id"].'"');
                $output['num_comment_parent'] = $ims->db->load_item('shared_comment',' lang="'.$ims->conf['lang_cur'].'" AND item_id="'.$arr_in["parent_id"].'" ','num_comment');
            }
            $output['ok'] = 1;
            $data = array();
            $data['item_id'] = $arr_in["type_id"];
            $data['type'] = $arr_in["type"];
            $output['html'] = $ims->site_func->item_comment($arr_in, $data);
            $output['html'] .='
                <script type="text/javascript"> 
				    SharedComment.post_comment("sub_'.$arr_in['item_id'].'form"); 
				</script>';
            $output['mess'] = $ims->site_func->get_lang ('success_comment', 'global');
        }
        return json_encode($output);
    }
    function do_load_comment() {
        global $ims;
        include_once($ims->conf["rootpath"].DS."config".DS."xtemplate.class.php");
        $ims->temp_box = new XTemplate($ims->path_html."box.tpl");
        $output = array(
            'ok' => 0,
            'mess' => '',
            'html' => ''
        );
        $load_sub = 0;
        $data 			   = array();
        $num_show 		   = $ims->setting['shared']['numshow_comment'];
        $start 			   = $ims->post['start'];
        $data['item_id']   = $ims->post['type_id'];
        $data['parent_id'] = $ims->func->if_isset($ims->post['parent_id']);
        $data['type']      = $ims->post['type'];
        // LIST_COMMENT
        $where = '';
        if ($data['parent_id']) {
            $load_sub = 1;
            $where = 'parent_id='.$data['parent_id'].' AND ';
        }else{
            $where = 'parent_id=0 AND ';
        }
        $num_rows_count = $ims->db->do_get_num("shared_comment", "".$where." type_id=".$data['item_id']." and lang='".$ims->conf['lang_cur']."' and type='".$data['type']."'  ");
        if($num_rows_count - $start < $num_show){
            $num_show = $num_rows_count - $start;
        }
        $arr_comment = $ims->db->load_row_arr("shared_comment","".$where." is_show=1 AND type_id=".$data['item_id']." AND type='".$data['type']."' AND lang='".$ims->conf['lang_cur']."' ORDER BY date_create DESC LIMIT ".$start." , ". $num_show ."");
        if (!empty($arr_comment)) {
            foreach ($arr_comment as $key => $row) {
                if ($load_sub == 1) {
                    $row['load_sub'] = 1;
                }
                $output['html'] .= $ims->site_func->item_comment($row, $data);
                $output['html'] .='
                <script type="text/javascript"> 
				    SharedComment.post_comment("sub_'.$row['item_id'].'form"); 
				</script>';
            }
            $output['start'] = $start + $num_show;
            $output['max'] = $num_rows_count;
        }
        return json_encode($output);
    }
}