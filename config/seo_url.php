<?php
    if (!defined('IN_ims')) { die('Access denied'); }

if (isset($_SERVER['REDIRECT_QUERY_STRING'])){
    $request_uri = $_SERVER["REDIRECT_URL"];
}else{
    $request_uri = $_SERVER["REQUEST_URI"];
}

$request_uri = ($ims->conf["rooturi"] != '/') ? str_replace($ims->conf["rooturi"],"",$request_uri) : substr($request_uri,1);
if (strpos($request_uri, 'html?') !== false) {
    $request_uri = str_replace('html?', 'html/?', $request_uri);die;
    $ims->html->redirect_rel($request_uri);
}


$arr_pos = explode("/", $request_uri);
foreach ($arr_pos as $k => $v) {
    $arr_pos[$k] = urldecode($v);
}
$pos_start = 0;

$ims->conf['lang_cur']     = (isset($ims->conf['lang_cur'])) ? $ims->conf['lang_cur'] : $ims->data["lang_default"]["name"];
$ims->conf['cur_mod_url']  = (isset($arr_pos[$pos_start]) && !empty($arr_pos[$pos_start])) ? $arr_pos[$pos_start] : "";
$ims->conf['cur_act_url']  = "";
$ims->conf['cur_item_url'] = "";
if(isset($arr_pos[$pos_start+2])){
    $ims->conf['cur_act_url'] = (isset($arr_pos[$pos_start+1]) && !empty($arr_pos[$pos_start+1])) ? $arr_pos[$pos_start+1] : "";
    $ims->conf['cur_item_url'] = (isset($arr_pos[$pos_start+2]) && !empty($arr_pos[$pos_start+2])) ? $arr_pos[$pos_start+2] : "";
    if(substr($ims->conf['cur_item_url'],-5) == ".html"){
        $ims->conf['cur_item_url'] = substr($ims->conf['cur_item_url'],0,-5);
    }
}elseif(isset($arr_pos[$pos_start+1])){
    if(substr($arr_pos[$pos_start+1],-5) == ".html"){
        $ims->conf['cur_item_url'] = substr($arr_pos[$pos_start+1],0,-5);
    }else{
        $ims->conf['cur_act_url'] = $arr_pos[$pos_start+1];
    }
}

$ims->conf['where_lang'] = " AND is_show=1 AND lang='".$ims->conf['lang_cur']."'";

$ims->site_func->checkContributor();
// $ims->site_func->check_product_not_promo();

if(empty($ims->conf['cur_mod_url'])) {
	$ims->conf['cur_mod'] = 'home';
} else {
	$tmp = (substr($ims->conf['cur_mod_url'],-5) == ".html") ? substr($ims->conf['cur_mod_url'],0,-5) : $ims->conf['cur_mod_url'];
    $tmp2 = explode('?',$tmp);
    $sql = "SELECT * FROM friendly_link WHERE friendly_link='".$tmp2[0]."' limit 0,1";
    $result = $ims->db->query($sql);
    if ($info = $ims->db->fetch_row($result)){
        $ims->conf['lang_cur'] = $info['lang'];
        $ims->conf['cur_mod'] = $info['module'];
        $ims->conf['cur_act'] = $info['action'];
        $ims->conf['cur_act_id'] = $info['dbtable_id'];
        $ims->conf['cur_friendly_link'] = $info['friendly_link'];
    } else {
        if($ims->conf['is_under_construction'] == 1) {
            if(Session::Get('is_admin')=='admin' || (isset($ims->input['is_admin']))) {
                Session::Set('is_admin', 'admin');
            }
        }
        $arr_ext = explode(".",$request_uri);
        $duoi = strtolower(trim($arr_ext[count($arr_ext) - 1]));
        if(in_array($duoi, array('jpg', 'jpeg', 'gif', 'png', 'bmp'))) {
            $link_go = $ims->func->get_src_mod('');
            $ims->html->redirect_rel($link_go);
        } elseif(in_array($duoi, array('mp4', 'wav', 'mp3', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'))) {
            die('Not found: 404');
        } else {
            if ($request_uri != ''){
                $flag = false;

                $check = $ims->db->load_row('user_deeplink', 'short_code = "'.$request_uri.'" and is_show = 1 ', 'id, user_id, type, item_id, num_view, referred_member, short_code, link_source');
                if($check){
                    if(!isset($_COOKIE['user_contributor']) && !isset($_COOKIE['deeplink'])){
                        if($ims->site_func->checkUserLogin() == 1) {
                            $check_referred = $ims->db->load_item('user_recommend_log', 'is_show = 1 and referred_user_id = '.$ims->data['user_cur']['user_id'], 'id'); //Đã được người khác gthiệu
                            if(!$check_referred && $check['user_id'] != $ims->data['user_cur']['user_id']){
                                if(!$check['referred_member']){
                                    $referred_member = $ims->data['user_cur']['user_id'];
                                }else{
                                    $referred_member = $check['referred_member'].','.$ims->data['user_cur']['user_id'];
                                }
                                $ims->db->do_update('user_deeplink', array('referred_member' => $referred_member),' short_code="'.$request_uri.'"');
                                $check['num_view'] += 1;

                                // Thêm data vào bảng user_recommend_log
                                $recommend_log = array(
                                    'type' => 'deeplink',
                                    'recommend_user_id' => $check['user_id'],
                                    'recommend_link' => $check['short_code'],
                                    'deeplink_id' => $check['id'],
                                    'referred_user_id' => $ims->data['user_cur']['user_id'],
                                    'referred_full_name' => $ims->data['user_cur']["full_name"],
                                    'referred_phone' => $ims->data['user_cur']["phone"],
                                    'referred_email' => $ims->data['user_cur']["email"],
                                    'is_show' => 1,
                                    'date_create' => time(),
                                    'date_update' => time(),
                                );
                                $ims->db->do_insert("user_recommend_log", $recommend_log);
                            }
                        }else{
                            $check['num_view'] += 1;
                        }
                        setcookie('deeplink', $check['id'], time()+(86400 * 30));

                        $ims->db->do_update('user_deeplink', array('num_view' => $check['num_view']),' short_code="'.$request_uri.'"');
                    }
                    $flag = true;
                    $ims->html->redirect_rel($ims->conf['rooturl'].$check['link_source']);
                }

                $info = $ims->db->load_row('user','is_show=1 AND link_shorten="'.$request_uri.'"','user_code');
                if($info){
                    $flag = true;
                    $request_uri = $ims->conf['rooturl'].'?contributor='.$ims->func->base64_encode($info['user_code']);
                    $ims->html->redirect_rel($request_uri);
                }
                if($flag == false){
                    // require_once ($ims->conf["rootpath"]."404.php");die;
                    $ims->html->redirect_rel($ims->conf["rooturl"]);
                }
            }
        }
    }
}

// print_arr($ims->conf);
// die();
?>