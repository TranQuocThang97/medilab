<?php
if (!defined('IN_ims')) { die('Access denied'); }
function load_setting ()
{
    global $ims;

    $ims->site_func->setting('user');
    $ims->site_func->setting('event');
    return true;
}
load_setting ();
$nts = new sMain();

use \Firebase\JWT\JWT;


class sMain{

    var $modules = "event";
    var $action  = "ajax";

    function __construct (){
        global $ims;

        $ims->func->load_language($this->modules);
        $fun = (isset($ims->post['f'])) ? $ims->post['f'] : '';
        $ims->conf['lang'] = !empty($ims->post['lang'])?$ims->post['lang']:'vi';
        switch ($fun) {
            case "load_event_location_main":
                echo $this->do_load_event_location_main();
                exit;
                break;
            case "load_event_main":
                echo $this->do_load_event_main();
                exit;
                break;
            case "load_events_ajax":
                echo $this->do_load_events_ajax();
                exit;
                break;
            case "load_event_location":
                echo $this->do_load_event_location();
                exit;
                break;
            case "load_event":
                echo $this->do_load_event();
                exit;
                break;
            case "load_event_product":
                echo $this->do_load_event_product();
                exit;
                break;
            case "load_detail_event_product":
                echo $this->do_load_detail_event_product();
                exit;
                break;
            case "follow":
                echo $this->do_follow();
                exit;
                break;
            case "load_form_register":
                echo $this->do_load_form_register();
                exit;
                break;
            case "load_cart_info":
                echo $this->do_load_cart_info();
                exit;
                break;
            case "register_step1":
                echo $this->do_register_step1();
                exit;
                break;
            case "register_step2":
                echo $this->do_register_step2();
                exit;
                break;
            case "upload_ticket_render":
                echo $this->do_upload_ticket_render();
                exit;
                break;
            case "send_mail_ticket":
                echo $this->do_send_mail_ticket();
                exit;
                break;
            case "send_other_mail":
                echo $this->do_send_other_mail();
                exit;
                break;
            case "load_complete_order_event":
                echo $this->do_load_complete_order_event();
                exit;
                break;
            case "edit_ticket":
                echo $this->do_edit_ticket();
                exit;
                break;
            case "cancel_ticket_booked":
                echo $this->do_cancel_ticket_booked();
                exit;
                break;
            case "contact":
                echo $this->do_contact();
                exit;
                break;
            case "upload_image":
                echo $this->do_upload_image();
                exit;
                break;
            case "update_image":
                echo $this->do_update_image();
                exit;
                break;
            case "detect_face":
                echo $this->do_detect_face();
                exit;
                break;
            case "picture_search":
                echo $this->do_picture_search();
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

    function do_picture_search(){
        global $ims;

        $output = array(
            'ok' => 0,
            'mess' => '',
            'html' => '',
            'index' => 0,
            'end' => 1,
            'item_id' => array(),
        );

        if($ims->site_func->checkUserLogin() != 1) {
            $output['mess'] = $ims->lang['global']['signin_false'];
            return json_encode($output);
        }

        $dir_view      = $ims->func->dirModules('gallery', 'views', 'path');
        $ims->temp_out = new XTemplate($dir_view . "gallery.tpl");
        $ims->temp_out->assign('CONF', $ims->conf);
        $ims->temp_out->assign('LANG', $ims->lang);
        $ims->temp_out->assign('DIR_IMAGE', $ims->dir_images);

        $event_id = $ims->func->if_isset($ims->post['id']);
        $founded = $ims->func->if_isset($ims->post['founded']);
        $output['index'] = $ims->func->if_isset($ims->post['index'], $output['index']);        
        // $list_founded = !empty($ims->post['founded'])?explode(',', $ims->post['founded']):array();
        // if(!empty($ims->post['founded'])){
        //     $list_founded = explode(',', $ims->post['founded']);
        // }
        $face = '';
        $where = '';
        $arr_found = array();
        if(isset($_FILES['picture']) && $_FILES['picture']['error'] == 0){
            $_FILES['picture']['name'] = 'facelookup.png';
            $folder_upload = "user/".$ims->data['user_cur']['folder_upload'].'/'.date('Y',time()).'_'.date('m',time());
            $out_pic = array();
            $out_pic = $ims->site_func->upload_image($folder_upload,'picture');
            if($out_pic['ok'] == 1){
                $face = $out_pic['url_picture'];
                $face = $ims->conf['rooturl'].'uploads/'.$face;
            }else{
                $output['ok'] = 0;
                $output['mess']  = $out_pic['mess'];
                return json_encode($output);
            }
            if(!empty($face)){
                if(!empty($founded)){
                    $where .= 'find_in_set(item_id,"'.$founded.'") <= 0 AND ';
                }
                $arr_picture = $ims->db->load_item_arr('event_image_detail', $where.'event_id="'.$event_id.'" order by id asc limit '.$output['index'].',10', 'id, item_id, parent_id, event_id, picture');
                $output['where'] =  $where.'event_id="'.$event_id.'" order by id asc limit '.$output['index'].',10';
                if($arr_picture){                    
                    // print_arr($arr_picture);
                    $output['index'] += count($arr_picture);
                    $output['end'] = 0;
                    $aws = $ims->conf['rootpath']."library/aws/aws-autoloader.php";
                    if(file_exists($aws)){
                        require_once ($aws);
                        $config = array(
                            'credentials' => array(
                                'key' => $ims->conf['aws_key'],
                                'secret' => $ims->conf['aws_secret'],
                            ),
                            'region' => 'ap-southeast-1',
                            'version' => 'latest',
                        );                 
                        $client = new Aws\Rekognition\RekognitionClient($config);                        
                        foreach ($arr_picture as $row) {
                            $row['picture'] = $ims->conf['rooturl'].'uploads/'.$row['picture'];
                            $result = $client->compareFaces(
                                array(
                                    'SimilarityThreshold' => 80,
                                    'SourceImage' => array(
                                        'Bytes' => file_get_contents($face)
                                    ),
                                    'TargetImage' => array(
                                        'Bytes' => file_get_contents($row['picture'])
                                    ),
                                )
                            );                            
                            if(count($result['FaceMatches'])>0){
                                $arr_found[] = $row['parent_id'];
                                $output['item_id'][] = $row['item_id'];
                                $output['result'][] = $result['FaceMatches'];
                            }                  
                        }
                    }
                    $file = $ims->conf['rootpath'].'uploads/'.$out_pic['url_picture'];
                    if(file_exists($file)){
                        unlink($file);
                    }
                }
                if(count($arr_found)>0){
                    $output['ok'] = 1;
                    $arr_result = $ims->db->load_item_arr('event_image', ' find_in_set(item_id,"'.implode(',', $arr_found).'") ', 'item_id, picture, title');
                    if($arr_result){
                        foreach ($arr_result as $v) {
                            $v['title'] = htmlspecialchars($v['title']);
                            $v['thumb'] = $ims->func->get_src_mod($v['picture'], 190, 170, 1, 1);
                            $v['picture'] = $ims->func->get_src_mod($v['picture']);                            
                            $ims->temp_out->assign('row', $v);
                            $ims->temp_out->parse('list_image.row');
                        }
                        $ims->temp_out->parse('list_image');
                        $output['html'] .= $ims->temp_out->text('list_image');                        
                    }
                }
            }
        }
        $output['item_id'] = implode(',', $output['item_id']);
        return json_encode($output);
    }

    function do_detect_face(){
        global $ims;
        $output = array(
            'ok' => 0,
            'mess' => '',
        );  
        if($ims->site_func->checkuserlogin() != 1) {
            $output['mess'] = $ims->lang['global']['signin_false'];
            return json_encode($output);
        }
        $infoUser = $ims->data['user_cur'];
        $input = $ims->func->if_isset($ims->post['data']);
        $title = $ims->func->if_isset($ims->post['title']);
        $arr_image = $ims->db->load_item_arr('event_image', ' find_in_set(item_id,'.$input.')>0 ', 'item_id, event_id, picture');        
        if($arr_image){
            $aws = $ims->conf['rootpath']."library/aws/aws-autoloader.php";
            if(file_exists($aws)){
                require_once ($aws);
                $config = array(
                    'credentials' => array(
                        'key' => $ims->conf['aws_key'],
                        'secret' => $ims->conf['aws_secret'],
                    ),
                    'region' => 'ap-southeast-1',
                    'version' => 'latest',
                );                 
                $client = new Aws\Rekognition\RekognitionClient($config);
                foreach ($arr_image as $row) {
                    $photo = $ims->conf['rootpath'].'uploads/'.$row['picture'];
                    if(file_exists($photo)){
                        $imageSrc = imagecreatefromstring(file_get_contents($photo));
                        $imgSize = getimagesize($photo);
                        $fp_image = fopen($photo, 'r');
                        $image = fread($fp_image, filesize($photo));
                        fclose($fp_image);
                        $result = $client->detectFaces(
                            array(
                                'Image' => array(
                                    'Bytes' => $image,
                                ),
                                'Attributes' => array('ALL'),
                            )
                        );
                        foreach ($result['FaceDetails'] as $k => $pic) {
                            if($pic['Confidence'] >= 80){
                                $detail = array();
                                $detail['width'] = $pic['BoundingBox']['Width'] * $imgSize['0'];
                                $detail['height'] = $pic['BoundingBox']['Height'] * $imgSize['1'];
                                $detail['startX'] = $pic['BoundingBox']['Left'] * $imgSize['0'];
                                $detail['startY'] = $pic['BoundingBox']['Top'] * $imgSize['1'];
                                if($detail['width'] >= 30 || $detail['height'] >= 30){
                                    $face = imagecrop($imageSrc, array('x' => $detail['startX'], 'y' => $detail['startY'], 'width' => $detail['width'], 'height' => $detail['height']));
                                    $folder_upload = "event/".$infoUser['folder_upload'].'/'.$ims->func->get_friendly_link($title).'/'.date('Y',time()).'_'.date('m',time()).'/faces/';
                                    $name = time().'-'.$k.'.jpg';
                                    $ims->func->rmkdir($folder_upload);
                                    imagejpeg($face, $ims->conf['rootpath'].'uploads/'.$folder_upload.$name, 100);

                                    $arr_in = array();
                                    $arr_in['id'] = $ims->db->getAutoIncrement('event_image_detail');
                                    $arr_in['item_id'] = $arr_in['id'];
                                    $arr_in['parent_id'] = $row['item_id'];
                                    $arr_in['event_id'] = $row['event_id'];
                                    $arr_in['picture'] = $folder_upload.$name;
                                    $arr_in['title'] = $name;
                                    $arr_in['is_show'] = 1;
                                    $arr_in['date_create'] = time();
                                    $arr_in['date_update'] = time();
                                    $arr_in['lang'] = $ims->conf['lang_cur'];
                                    // $check = $ims->db->load_item('event_image_detail', 'title = "'.$name.'"', 'item_id');
                                    // if(!$check){
                                        $ok = $ims->db->do_insert('event_image_detail', $arr_in);
                                    // }
                                }
                            }
                        }
                    }
                }
                if($ok){
                    $output['ok'] = 1;
                }
            }
        }
        return json_encode($output);
    }

    function do_update_image(){
        global $ims;
        $output = array(
            'ok' => 0,
            'mess' => '',
        );  
        if($ims->site_func->checkuserlogin() != 1) {
            $output['mess'] = $ims->lang['global']['signin_false'];
            return json_encode($output);
        }
        
        $dup = array();
        $type = $ims->func->if_isset($ims->post['type']);
        $event_id = $ims->func->if_isset($ims->post['event']);
        $input = !empty($ims->post['data'])?$ims->func->unserialize_array($ims->post['data']):array();        
        $infoUser = $ims->data['user_cur'];
        
        if(!empty($input['selected_id'])){
            $arr_image = $ims->db->load_item_arr('event_image', ' find_in_set(item_id, "'.implode(',',$input['selected_id']).'") ','item_id, title, type, picture');
            if($arr_image){
                foreach ($arr_image as $row) {
                    if($type == "remove"){    
                        $file = $ims->conf['rootpath'].'uploads/'.$row['picture'];
                        if(file_exists($file)){
                            unlink($file);
                        }        
                        $arr_del = array(
                            'from' => 'event_image',
                            'where' => ' item_id = '.$row['item_id'],
                        );
                        $ok = $ims->db->delete($arr_del);
                        if($ok){
                            //xóa ảnh mặt
                            $arr_image_detail = $ims->db->load_item_arr('event_image_detail', ' parent_id = '.$row['item_id'] ,'item_id, picture');
                            if($arr_image_detail){
                                foreach ($arr_image_detail as $d) {
                                    $file_d = $ims->conf['rootpath'].'uploads/'.$d['picture'];
                                    if(file_exists($file_d)){
                                        unlink($file_d);
                                    }
                                }
                                $arr_del_detail = array(
                                    'from' => 'event_image_detail',
                                    'where' => ' parent_id = '.$row['item_id'],
                                );
                                $ims->db->delete($arr_del_detail);
                            }
                        }
                    }                
                    if($type == "update"){
                        $arr_up = array();
                        $arr_up['type'] = isset($input['image'][$row['item_id']]['type'])?"event":"personal";
                        $arr_up['title'] = $row['title'];
                        if((isset($input['image'][$row['item_id']]['title']) && $input['image'][$row['item_id']]['title']!="")){
                            $check = $ims->db->load_row('event_image','is_show=1 and event_id="'.$event_id.'" and (title="'.$input['image'][$row['item_id']]['title'].'")');
                            if(!$check){
                                $arr_up['title'] = $input['image'][$row['item_id']]['title'];
                            }else{
                                if($arr_up['type'] == $row['type']){
                                    $dup[] = $input['image'][$row['item_id']]['title'];
                                }
                            }
                        }
                        $ok = $ims->db->do_update('event_image', $arr_up, ' item_id="'.$row['item_id'].'" ' );
                    }
                }
            }
            if($ok){
                $output['ok'] = 1;
            }
        }
        if(count($dup) > 0){
            $output['ok'] = 2;
            $output['mess'] = '<div class="text-left">'.$ims->site_func->get_lang('image_duplicate', 'user', array( '[img]' => '<b>'.implode(', ',$dup).'<b><br>')).'</div>';
        }
        return json_encode($output);
    }

    function do_upload_image(){
        global $ims;
        $output = array(
            'ok' => 0,
            'mess' => '',
            'list_image' => array(),
        );        
        if($ims->site_func->checkuserlogin() != 1) {
            $output['mess'] = $ims->lang['global']['signin_false'];
            return json_encode($output);
        }
        $input = $ims->post;
        $infoUser = $ims->data['user_cur'];        
        if(isset($_FILES['images']) && $_FILES['images']['error'][0] == 0) {
            $_FILES['images'] = array_map('array_values', $_FILES['images']);
            $arr_tmp = array();
            $folder_upload = "event/".$infoUser['folder_upload'].'/'.$ims->func->get_friendly_link($input['title']).'/'.date('Y',time()).'_'.date('m',time());
            $out_pic = $ims->site_func->upload_image_multi($folder_upload,'images',1,0,0);
            if($out_pic['ok'] == 1){
                $arr_tmp[] = $ims->func->unserialize($out_pic['url_picture']);
            }
            if(count($arr_tmp[0]) > 0){
                $dup = array();
                foreach ($arr_tmp[0] as $k => $pic) {                   
                    $arr_in = array();
                    $arr_in['item_id'] = $ims->db->getAutoIncrement('event_image');
                    $file = explode("/",$pic);
                    $title = strtolower(substr($file[count($file)-1], 0, strrpos($file[count($file)-1], ".")));                    
                    $arr_in['title'] = !empty($input['caption'][$k])?$input['caption'][$k]:$title;
                    $arr_in['user_id'] = $infoUser['user_id'];
                    $arr_in['event_id'] = $input['event'];
                    $arr_in['type'] = $input['type'];
                    $arr_in['picture'] = $pic;
                    $arr_in['is_show'] = 1;
                    $arr_in['is_free'] = 0;
                    $arr_in['date_create'] = time();
                    $arr_in['date_update'] = time();
                    $arr_in['lang'] = $ims->conf['lang_cur'];

                    $check = $ims->db->load_row('event_image','is_show=1 and event_id="'.$input['event'].'" and (picture="'.$pic.'" or title="'.$title.'")');
                    if(!$check){
                        $ok = $ims->db->do_insert('event_image', $arr_in);
                        $output['list_image'][] = $arr_in['item_id'];
                    }else{
                        $dup[] = $title;
                    }
                }
                $output['list_image'] = implode(',', $output['list_image']);
                if($ok){
                    $output['ok'] = 1;
                }
                if(count($dup) > 0){
                    $output['ok'] = 2;
                    $output['mess'] = '<div class="text-left">'.$ims->site_func->get_lang('image_duplicate', 'user', array( '[img]' => '<b>'.implode(', ',$dup).'<b><br>')).'</div>';
                }
            }
        }
        return json_encode($output);
    }

    function do_load_event_location_main(){
        global $ims;
        include_once($ims->conf["rootpath"].DS."config/site.php");
        include_once($ims->conf["rootpath"].DS."config".DS."xtemplate.class.php");
        $ims->site = new Site($this);
        $dir_view = $ims->func->dirModules('event', 'views', 'path');
        $ims->temp_act = new XTemplate($dir_view ."event.tpl");

        if(!isset($ims->lang['event'])){
            $ims->func->load_language('event');
        }
        $ims->temp_act->assign('LANG', $ims->lang);
        $output = array(
            'html' => '',
        );

        $lang_cur = $ims->post['lang_cur'];
        $group_cur = $ims->post['group_cur'];
        $province = isset($ims->post['province']) ? $ims->post['province'] : 0;

        $where_root = ($group_cur) ? ' and find_in_set('.$group_cur.', group_nav) ' : '';
        $where_province = ($group_cur) ? ' and find_in_set('.$group_cur.', ev.group_nav) ' : '';

        $keyword = isset($ims->post['keyword']) ? $ims->post['keyword'] : '';
        if ($keyword) {
            $arr_key = explode(' ', $keyword);
            $arr_tmp = array();
            $arr_tmp_prv = array();
            foreach ($arr_key as $value) {
                $value = trim($value);
                if (!empty($value)) {
                    $arr_tmp[] = "title LIKE '%".$value."%'";
                    $arr_tmp_prv[] = "ev.title LIKE '%".$value."%'";
                }
            }
            if (count($arr_tmp) > 0) {
                $where_root .= " AND (".implode(" AND ", $arr_tmp).")";
                $where_province .= " AND (".implode(" AND ", $arr_tmp_prv).")";
            }
        }

        $where_location = ($province) ? ' and province = '.$province : '';

        $list_province = $ims->db->load_item_arr('location_province as prv, event as ev', ' ev.province = prv.code and ev.is_show = 1 and prv.is_show = 1 '.$where_province.' order by prv.title asc', 'DISTINCT (prv.code), prv.title');
        $event_at = '';
        if($list_province){
            $select_location = $ims->lang['event']['all'];
            $i = 0;
            foreach ($list_province as $prv){
                $i++;
                if(($province) && $province == $prv['code']){
                    $event_at = $select_location = $prv['title'];
                }
                $ims->temp_act->assign('prv', $prv);
                if($i == 1){
                    $ims->temp_act->parse("list_event.main.select.all");
                }
                $ims->temp_act->parse("list_event.main.select.item");
            }
            $ims->temp_act->assign('province_cur', $province);
            $ims->temp_act->assign('select_location', $select_location);
            $ims->temp_act->parse("list_event.main.select");
        }

        $day = date('N', time()); // Thứ hiện tại
        $date_cur = date('m/d/Y', time()); // Ngày hiện tại

        $today_min = strtotime($date_cur.' 0:0:0');
        $today_max = strtotime($date_cur.' 23:59:59');

        $weekend_min = ($day < 5) ? $today_min + (5 - $day) * 24 * 60 * 60 + (5 * 60 * 60) : $today_min + 24 * 60 * 60 + (5 * 60 * 60);
        $weekend_max = ($day < 7) ? $today_min + (8 - $day) * 24 * 60 * 60 : $today_min + 24 * 60 * 60;

        $arr_nav = array(
            array(
                'title' => $ims->lang['event']['all'],
                'group_id' => 'a'
            ),
            array(
                'title' => $ims->lang['event']['today'],
                'where' => ' and (date_create >= '.$today_min.' and date_create <= '.$today_max.') ',
                'group_id' => 'b',
                'type_show' => 'today'
            ),
            array(
                'title' => $ims->lang['event']['weekend'],
                'where' => ' and (date_begin >= '.$weekend_min.' and date_end <= '.$weekend_max.') ',
                'group_id' => 'c',
                'type_show' => 'weekend'
            ),
        );

        $list_data = $arr_nav;
        $i = 0;
        foreach ($list_data as $row){
            $i++;
            $row['active'] = ($i == 1) ? 'active' : '';
            $row['active_content'] = ($i == 1) ? '' : 'd-none';
            $where = $where_root;
            if(isset($row['where'])){
                $where .= $row['where'];
            }

            $arr_in = array(
                'group_id' => ($group_cur) ? $group_cur : 0,
                'where' => $where.$where_location.' order by show_order desc, date_create desc',
                'viewmore_ajax' => 1,
                'province' => $province,
                'type_show' => isset($row['type_show']) ? $row['type_show'] : '',
                'ajax' => 2,
                'lang_cur' => $lang_cur,
                'keyword' => $keyword
            );

            $row['content'] = $ims->call->mFunc('event', 'html_list_item', array($arr_in));
            $ims->temp_act->assign('row', $row);
            $ims->temp_act->parse("list_event.main.li");
            $ims->temp_act->parse("list_event.main.content");
        }

        if($province){
            $ims->temp_act->assign('event_at', $ims->lang['event']['event_at'].' '.$event_at);
        }else{
            $ims->temp_act->assign('none_event_at', 'd-none');
        }
        $ims->temp_act->parse("list_event.main");
        $output['html'] = $ims->temp_act->text("list_event.main");

        return json_encode($output);
    }

    function do_load_event_main(){
        global $ims;
        include_once($ims->conf["rootpath"].DS."config/site.php");
        include_once($ims->conf["rootpath"].DS."config".DS."xtemplate.class.php");
        $ims->site = new Site($this);

        $output = array(
            'num' => 0,
            'html' => '',
        );

        $where = '';
        $start = isset($ims->post['num_cur']) ? $ims->post['num_cur'] : 0;
        $group_id = isset($ims->post['group_id']) ? $ims->post['group_id'] : 0;
        $province = isset($ims->post['province']) ? $ims->post['province'] : 0;
        $typeshow = isset($ims->post['typeshow']) ? $ims->post['typeshow'] : '';
        $keyword = isset($ims->post['keyword']) ? $ims->post['keyword'] : '';
        $lang_cur = isset($ims->post['lang_cur']) ? $ims->post['lang_cur'] : '';
        $num_list = $ims->setting['event']['num_list'];

        $day = date('N', time()); // Thứ hiện tại
        $date_cur = date('m/d/Y', time()); // Ngày hiện tại

        $today_min = strtotime($date_cur.' 0:0:0');
        $today_max = strtotime($date_cur.' 23:59:59');

        $weekend_min = ($day < 5) ? $today_min + (5 - $day) * 24 * 60 * 60 + (5 * 60 * 60) : $today_min + 24 * 60 * 60 + (5 * 60 * 60);
        $weekend_max = ($day < 7) ? $today_min + (8 - $day) * 24 * 60 * 60 : $today_min + 24 * 60 * 60;

        if($group_id != 0){
            $where .= ' and (find_in_set('.$group_id.', group_nav) or find_in_set('.$group_id.', group_related))';
        }
        if($province){
            $where .= ' and province = '.$province;
        }
        if($typeshow == 'for_you'){
            $user = array();
            if($ims->site_func->checkUserLogin() != 1) {
                $user = $ims->data['user_cur'];
            }
            if($user && $user['list_favorite'] != ''){
                $where .= ' and item_id IN ('.$user['list_favorite'].')';
            }else{
                $where .= ' and item_id = -10 ';
            }
        }elseif($typeshow == 'today'){
            $where .= ' and (date_create >= '.$today_min.' and date_create <= '.$today_max.') ';
        }elseif($typeshow == 'weekend'){
            $where .= ' and (date_begin >= '.$weekend_min.' and date_end <= '.$weekend_max.') ';
        }

        $arr_key = explode(' ', $keyword);
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

        $arr_in = array(
            'where' => $where.' order by show_order desc, date_create desc limit '.$start.','.$num_list,
            'paginate' => 0,
            'temp' => 'list_item_ajax',
            'ajax' => 1,
            'lang_cur' => $lang_cur,
            'keyword' => $keyword
        );

        $output['total'] = $ims->db->do_get_num("event", 'is_show = 1 and lang = "'.$lang_cur.'"'.$where);
        $result_total = $ims->db->do_get_num("event", 'is_show = 1 and lang = "'.$lang_cur.'"'.$arr_in['where']);
        if(($start + $result_total) == $output['total']){
            $output['num'] = 0;
        }else{
            $output['num'] = $start + $result_total;
        }
        $output['html'] = $ims->call->mFunc('event', 'html_list_item', array($arr_in));

        return json_encode($output);
    }

    function do_load_events_ajax(){
        global $ims;
        include_once($ims->conf["rootpath"].DS."config/site.php");
        include_once($ims->conf["rootpath"].DS."config".DS."xtemplate.class.php");
        $ims->site = new Site($this);

        $output = array(
            'num' => 0,
            'html' => '',
            'filter_event' => ''
        );

        $where = '';
        $start = isset($ims->post['num_cur']) ? $ims->post['num_cur'] : '';
        $group_id = isset($ims->post['group_id']) ? $ims->post['group_id'] : 0;
        $order_by = isset($ims->post['order_by']) ? $ims->post['order_by'] : '';
        $sort = isset($ims->post['sort']) ? $ims->post['sort'] : '';
        $keyword = isset($ims->post['keyword']) ? $ims->post['keyword'] : '';
        $focus = isset($ims->post['focus']) ? $ims->post['focus'] : '';
        $num_list = $ims->setting['event']['num_list'];

        if($group_id != 0){
            $where .= ' and (find_in_set('.$group_id.', group_nav) or find_in_set('.$group_id.', group_related))';
        }

        $arr_key = explode(' ', $keyword);
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
        if($focus){
            $where .= ' and is_'.$focus.' = 1';
        }

        if($sort){
            parse_str($sort, $arr_sort);
            $output['filter_event'] = $this->filter_event($arr_sort);
            if(isset($arr_sort['brand']) && $arr_sort['brand']){
                $where .= ' and brand_id IN('.$arr_sort['brand'].') ';
            }
            if(isset($arr_sort['nature']) && $arr_sort['nature']){
                $nature = explode(',', $arr_sort['nature']);
                $where_nt = array();
                foreach ($nature as $nt){
                    $where_nt[] = 'find_in_set('.$nt.', arr_nature)';
                }
                $where .= ' and ('.implode(' OR ', $where_nt).') ';
            }
            if(isset($arr_sort['price']) && $arr_sort['price']){
                $price = explode('-', $arr_sort['price']);
                if ($price[1] == 0) {
                    $where .= ' AND price_buy >= '.$price[0];
                }else{
                    $where .= ' and (price_buy BETWEEN '.$price[0].' and '.$price[1].')';
                }
            }
            if(isset($arr_sort['origin']) && $arr_sort['origin']){
                $where .= ' and origin_id IN('.$arr_sort['origin'].') ';
            }
        }
        if($order_by != ''){
            $order_by = ' order by '.str_replace('-', ' ', $order_by);
        }else{
            $order_by = ' order by show_order desc, date_create desc';
        }

        $arr_in = array(
            'where' => $where.$order_by.' limit '.$start.','.$num_list,
            'paginate' => 0,
            'temp' => 'list_item_ajax',
            'ajax' => 1,
        );

        $output['total'] = $ims->db->do_get_num("event", 'is_show = 1 and combo_id = 0 and lang = "'.$ims->conf['lang_cur'].'"' . $where);
        $result_total = $ims->db->do_get_num("event", 'is_show = 1 and combo_id = 0 and lang = "'.$ims->conf['lang_cur'].'"' . $arr_in['where']);
        if(($start + $result_total) == $output['total']){
            $output['num'] = 0;
        }else{
            $output['num'] = $start + $result_total;
            $output['more'] = (($output['total'] - $output['num']) > $num_list) ? $num_list : $output['total'] - $output['num'];
        }
        $output['html'] = $ims->call->mFunc('event','html_list_item', array($arr_in));
        return json_encode($output);
    }

    function do_load_event_location(){
        global $ims;
        include_once($ims->conf["rootpath"].DS."config/site.php");
        include_once($ims->conf["rootpath"].DS."config".DS."xtemplate.class.php");
        $ims->site = new Site($this);
        $dir_view = $ims->func->dirModules('home', 'views', 'path');
        $ims->temp_act = new XTemplate($dir_view ."home.tpl");

        if(!isset($ims->lang['home'])){
            $ims->func->load_language('home');
        }
        $ims->temp_act->assign('LANG', $ims->lang);
        $output = array(
            'html' => '',
        );

        $where_location = '';
        $lang_cur = $ims->post['lang_cur'];
        $province = isset($ims->post['province']) ? $ims->post['province'] : 0;

        if($province){
            $where_location = ' and province = '.$province;
        }

        $user = array();
        $list_favorite = '';
        if($ims->site_func->checkUserLogin() == 1) {
            $user = $ims->data['user_cur'];
            $list_favorite = $ims->func->unserialize($user['list_favorite']);
            if($list_favorite){
                $list_favorite_tmp = array();
                foreach ($list_favorite as $item){
                    if($item['mod'] == 'event'){
                        $list_favorite_tmp[] = $item['id'];
                    }
                }
                $list_favorite = implode(',', $list_favorite_tmp);
            }
        }

        $list_province = $ims->db->load_item_arr('location_province as prv, event as ev', ' ev.province = prv.code and ev.is_show = 1 and prv.is_show = 1 order by prv.title asc', 'DISTINCT (prv.code), prv.title');
        $event_at = '';
        if($list_province){
            $select_location = $ims->lang['home']['all'];
            $i = 0;
            foreach ($list_province as $prv){
                $i++;
                if(($province) && $province == $prv['code']){
                    $event_at = $select_location = $prv['title'];
                }
                $ims->temp_act->assign('prv', $prv);
                if($i == 1){
                    $ims->temp_act->parse("list_event.main.select.all");
                }
                $ims->temp_act->parse("list_event.main.select.item");
            }
            $ims->temp_act->assign('province_cur', $province);
            $ims->temp_act->assign('select_location', $select_location);
            $ims->temp_act->parse("list_event.main.select");
        }

        $day = date('N', time()); // Thứ hiện tại
        $date_cur = date('m/d/Y', time()); // Ngày hiện tại

        $today_min = strtotime($date_cur.' 0:0:0');
        $today_max = strtotime($date_cur.' 23:59:59');

        $weekend_min = ($day < 5) ? $today_min + (5 - $day) * 24 * 60 * 60 + (5 * 60 * 60) : $today_min + 24 * 60 * 60 + (5 * 60 * 60);
        $weekend_max = ($day < 7) ? $today_min + (8 - $day) * 24 * 60 * 60 : $today_min + 24 * 60 * 60;

        $arr_nav = array(
            array(
                'title' => $ims->lang['home']['all'],
                'group_id' => 'a'
            ),
            array(
                'title' => $ims->lang['home']['for_you'],
                'where' => ($list_favorite) ? ' and item_id IN ('.$list_favorite.') ' : ' and item_id = -10 ',
                'empty' => $ims->lang['home']['empty_favorite'],
                'group_id' => 'b',
                'type_show' => 'for_you'
            ),
            array(
                'title' => $ims->lang['home']['today'],
                'where' => ' and (date_create >= '.$today_min.' and date_create <= '.$today_max.') ',
                'group_id' => 'c',
                'type_show' => 'today'
            ),
            array(
                'title' => $ims->lang['home']['weekend'],
                'where' => ' and (date_begin >= '.$weekend_min.' and date_end <= '.$weekend_max.') ',
                'group_id' => 'd',
                'type_show' => 'weekend'
            ),
        );

        $list_group = $ims->db->load_item_arr('event_group as evg, event as ev', 'ev.is_focus = 0 and ev.is_show = 1 and ev.group_id = evg.group_id and evg.is_show = 1 and evg.lang = "'.$lang_cur.'" order by evg.title asc', 'evg.group_id, evg.title');
        $list_data = ($list_group) ? array_merge($arr_nav, $list_group) : $arr_nav;
        $i = 0;
        foreach ($list_data as $row){
            $i++;
            $row['active'] = ($i == 1) ? 'active' : '';
            $row['active_content'] = ($i == 1) ? '' : 'd-none';
            $where = (!empty($row['type_show']) && $row['type_show'] != 'for_you') ? '' : ' and is_focus = 0';
            if(isset($row['group_id']) && !in_array($row['group_id'], array('a', 'b', 'c', 'd'))){
                $where .= ' and find_in_set ('.$row['group_id'].', group_nav)';
            }
            if(isset($row['where'])){
                $where .= $row['where'];
            }

            $arr_in = array(
                'group_id' => (isset($row['group_id']) && !in_array($row['group_id'], array('a', 'b', 'c', 'd'))) ? $row['group_id'] : 0,
                'where' => $where.$where_location.' order by show_order desc, date_create desc',
                'viewmore_ajax' => 1,
                'empty' => isset($row['empty']) ? $row['empty'] : '',
                'province' => $province,
                'type_show' => isset($row['type_show']) ? $row['type_show'] : '',
                'ajax' => 2,
                'lang_cur' => $lang_cur
            );

            $row['content'] = $ims->call->mFunc('event', 'html_list_item', array($arr_in));
            $ims->temp_act->assign('row', $row);
            $ims->temp_act->parse("list_event.main.li");
            $ims->temp_act->parse("list_event.main.content");
        }

        if($province){
            $ims->temp_act->assign('event_at', $ims->lang['home']['event_at'].' '.$event_at);
        }else{
            $ims->temp_act->assign('none_event_at', 'd-none');
        }
        $ims->temp_act->parse("list_event.main");
        $output['html'] = $ims->temp_act->text("list_event.main");

        return json_encode($output);
    }
    function do_load_event(){
        global $ims;
        include_once($ims->conf["rootpath"].DS."config/site.php");
        include_once($ims->conf["rootpath"].DS."config".DS."xtemplate.class.php");
        $ims->site = new Site($this);

        $output = array(
            'num' => 0,
            'html' => '',
        );

        $where = '';
        $start = isset($ims->post['num_cur']) ? $ims->post['num_cur'] : 0;
        $group_id = isset($ims->post['group_id']) ? $ims->post['group_id'] : 0;
        $province = isset($ims->post['province']) ? $ims->post['province'] : 0;
        $typeshow = isset($ims->post['typeshow']) ? $ims->post['typeshow'] : '';
        $focus = isset($ims->post['focus']) ? $ims->post['focus'] : '';
        $lang_cur = isset($ims->post['lang_cur']) ? $ims->post['lang_cur'] : '';
        $num_list = $ims->setting['event']['num_list'];

        $day = date('N', time()); // Thứ hiện tại
        $date_cur = date('m/d/Y', time()); // Ngày hiện tại

        $today_min = strtotime($date_cur.' 0:0:0');
        $today_max = strtotime($date_cur.' 23:59:59');

        $weekend_min = ($day < 5) ? $today_min + (5 - $day) * 24 * 60 * 60 + (5 * 60 * 60) : $today_min + 24 * 60 * 60 + (5 * 60 * 60);
        $weekend_max = ($day < 7) ? $today_min + (8 - $day) * 24 * 60 * 60 : $today_min + 24 * 60 * 60;

        if($group_id > 0){
            $where .= ' and (find_in_set('.$group_id.', group_nav) or find_in_set('.$group_id.', group_related)) ';
        }
        if($province){
            $where .= ' and province = '.$province;
        }
        if($typeshow == 'for_you'){
            $user = array();
            if($ims->site_func->checkUserLogin() != 1) {
                $user = $ims->data['user_cur'];
            }
            if($user && $user['list_favorite'] != ''){
                $where .= ' and item_id IN ('.$user['list_favorite'].')';
            }else{
                $where .= ' and item_id = -10 ';
            }
        }elseif($typeshow == 'today'){
            $where .= ' and (date_create >= '.$today_min.' and date_create <= '.$today_max.') ';
        }elseif($typeshow == 'weekend'){
            $where .= ' and (date_begin >= '.$weekend_min.' and date_end <= '.$weekend_max.') ';
        }

        if($focus){
            $where .= ' and is_focus = 1';
        }else{
            $where .= ' and is_focus = 0';
        }

        $arr_in = array(
            'where' => $where.' order by show_order desc, date_create desc limit '.$start.','.$num_list,
            'paginate' => 0,
            'temp' => 'list_item_ajax',
            'ajax' => 1,
            'lang_cur' => $lang_cur
        );

        $output['total'] = $ims->db->do_get_num("event", 'is_show = 1 and lang = "'.$lang_cur.'"'.$where);
        $result_total = $ims->db->do_get_num("event", 'is_show = 1 and lang = "'.$lang_cur.'"'.$arr_in['where']);
        if(($start + $result_total) == $output['total']){
            $output['num'] = 0;
        }else{
            $output['num'] = $start + $result_total;
        }
        $output['html'] = $ims->call->mFunc('event', 'html_list_item', array($arr_in));

        return json_encode($output);
    }

    function do_load_event_product(){
        global $ims;

        include_once($ims->conf["rootpath"].DS."config/site.php");
        include_once($ims->conf["rootpath"].DS."config".DS."xtemplate.class.php");
        $ims->site = new Site($this);
        $dir_view = $ims->func->dirModules('event', 'views', 'path');
        $ims->temp_act = new XTemplate($dir_view ."event.tpl");

        $output = array(
            'html' => '',
        );

        $lang_cur = $ims->post['lang_cur'];
        $start = $ims->post['num_cur'];
        $event_item = $ims->post['event_item'];
        $num_list = $ims->setting['event']['num_list_store'];

        $where = ' and find_in_set ('.$event_item.', event_id) order by show_order desc, date_create desc';
        $result = $ims->db->load_item_arr('event_product', 'is_show = 1 and lang = "'.$lang_cur.'" '.$where.' limit '.$start.','.$num_list, 'item_id, title1, title, price, picture');
        if($result){
            foreach ($result as $row){
                $row['picture'] = $ims->func->get_src_mod($row['picture'], 210, 165, 1, 1);
                $row['price'] = $ims->lang['event']['price_buy'].': '.number_format($row['price'], 0, ',', '.').' vnđ';
                $row['title1'] = ($row['title1'] != '') ? $row['title1'].': ' : '';
                $row['item_id'] = $ims->func->base64_encode($row['item_id']);
                $ims->temp_act->assign('row', $row);
                $ims->temp_act->reset("event_product.item_product");
                $ims->temp_act->parse("event_product.item_product");
                $output['html'] .= $ims->temp_act->text("event_product.item_product");
            }
        }

        $output['total'] = $ims->db->do_count('event_product', 'is_show = 1 and lang = "'.$lang_cur.'" '.$where, 'item_id');
        $result_total = count($result);
        if(($start + $result_total) == $output['total']){
            $output['num'] = 0;
        }else{
            $output['num'] = $start + $result_total;
        }

        return json_encode($output);
    }

    function do_load_detail_event_product(){
        global $ims;
        $dir_view = $ims->func->dirModules('event', 'views', 'path');
        $ims->temp_act = new XTemplate($dir_view ."event.tpl");

        $out = array(
            'ok' => 0,
            'html' => ''
        );

        $lang_cur   = $ims->post['lang_cur'];
        $store_item   = isset($ims->post['item']) ? $ims->func->base64_decode($ims->post['item']) : '';
        $row = $ims->db->load_row('event_product', 'is_show = 1 and lang = "'.$lang_cur.'" and item_id = '.$store_item, 'title1, title, price, picture, arr_picture, content, num_item');
        if($row){
            $arr_pic = ($row['picture']) ? array($row['picture']) : array();
            if($row['arr_picture']){
                $arr_pic = array_merge($arr_pic, $ims->func->unserialize($row['arr_picture']));
            }

            foreach ($arr_pic as $pic){
                $pic_thumb = $ims->func->get_src_mod($pic, 100, 78, 1, 1);
                $pic = $ims->func->get_src_mod($pic, 456, 358, 1, 1);
                $ims->temp_act->assign('pic_thumb', $pic_thumb);
                $ims->temp_act->assign('pic', $pic);
                $ims->temp_act->parse('event_product.detail_product.thumb');
                $ims->temp_act->parse('event_product.detail_product.item');
            }
            $row['price'] = $ims->lang['event']['price_buy'].': '.number_format($row['price'], 0, ',', '.').' vnđ';
            $row['title1'] = ($row['title1'] != '') ? $row['title1'].': ' : '';
            $row['content'] = ($row['content']) ? '<div class="store_content">'.$ims->func->input_editor_decode($row['content']).'</div>' : '';
            $row['remaining_store_item'] = str_replace('{num}', $row['num_item'], $ims->lang['event']['remaining_store_item']);

            $ims->temp_act->assign('row', $row);
            $ims->temp_act->parse('event_product.detail_product');
            $out['html'] = $ims->temp_act->text('event_product.detail_product');
            $out['ok'] = 1;
            return json_encode($out);
        }
    }

    function do_follow(){
        global $ims;
        include_once($ims->conf["rootpath"].DS."config/site.php");
        $ims->site = new Site();
        $out = array(
            'text_follow' => $ims->lang['event']['btn_follow'],
            'ok' => 0,
            'mess' => ''
        );

        $event_item   = isset($ims->post['event_item']) ? $ims->func->base64_decode($ims->post['event_item']) : '';
        $check_follow = $ims->site->check_follow($event_item);
        $user_cur = ($ims->data['user_cur']) ? $ims->data['user_cur'] : array();
        if($user_cur){
            $owner_event = $check_follow['owner_event'];
            if($owner_event != 0){
                $num_follow_owner = $ims->db->load_item('user', 'user_id = '.$owner_event, 'num_follow');
                if($check_follow['ok'] == 0 && $check_follow['none_follow'] != 'none'){
                    $list_follow = ($user_cur['list_follow']) ? $user_cur['list_follow'].','.$owner_event : $owner_event;
                    $ok = $ims->db->do_update("user", array('list_follow' => $list_follow), ' user_id='.$user_cur['user_id']);
                    if($ok){
                        $num_follow_owner += 1;
                        $ok1 = $ims->db->do_update("user", array('num_follow' => $num_follow_owner), ' user_id='.$owner_event);
                        if($ok1){
                            $out['text_follow'] = $ims->lang['event']['cancel_follow'];
                            $out['ok'] = 1;
                        }
                    }
                }else{
                    $list_follow = explode(',', $user_cur['list_follow']);
                    unset($list_follow[array_search($owner_event, $list_follow)]);
                    $list_follow = implode(',', $list_follow);
                    $ok = $ims->db->do_update("user", array('list_follow' => $list_follow), ' user_id='.$user_cur['user_id']);
                    if($ok){
                        $num_follow_owner -= 1;
                        $ok1 = $ims->db->do_update("user", array('num_follow' => $num_follow_owner), ' user_id='.$owner_event);
                        if($ok1){
                            $out['ok'] = 2;
                        }
                    }
                }
                $out['num_follow'] = number_format($num_follow_owner,0,',','.');
            }
        }else{
            $out['mess'] = $ims->lang['event']['need_login'];
        }
        return json_encode($out);
    }

    function do_load_form_register(){
        global $ims;
        $dir_view = $ims->func->dirModules('event', 'views', 'path');
        $ims->temp_act = new XTemplate($dir_view ."event.tpl");
        $ims->temp_act->assign('LANG', $ims->lang);
        $out = array(
            'ok' => 0,
            'html_form' => '',
            'event_info' => '',
            'mess' => ''
        );

        $lang_cur = $ims->post['lang_cur'];
        if($ims->site_func->checkUserLogin() == 1) {
            $event_item = isset($ims->post['event_item']) ? $ims->func->base64_decode($ims->post['event_item']) : 0;
            if($event_item){
                $data = $ims->db->load_row('event', 'is_show = 1 and lang = "'.$lang_cur.'" and item_id = '.$event_item);
                if($data['date_end'] < time()){
                    $out['mess'] = $ims->lang['event']['ended_event'];
                    return json_encode($out);
                }elseif($data['date_end_ticket'] < time()){
                    $out['mess'] = $ims->site_func->get_lang('ticket_expires', 'event', array('{date_end}' => date('d/m/Y H:i:s', $data['date_end_ticket'])));
                    return json_encode($out);
                }
                if($data && $data['user_id'] != $ims->data['user_cur']['user_id']){
                    // Event info
                    $data['title'] = $ims->func->input_editor_decode($data['title']);
                    $data['title1'] = ($data['title1'] != '') ? $data['title1'].': ' : '';
                    if($data['organizer'] != ''){
                        $data['organizational'] = '<div class="organizational">'.$ims->lang['event']['organizational'].': <span>'.$data['organizer'].'</span></div>';
                    }
                    $data['date_begin'] = $ims->lang['global']['day_'.date('N',$data['date_begin'])].date(', d/m, h:i A', $data['date_begin']);
                    if($data['arr_logo'] != ''){
                        $arr_logo = $ims->func->unserialize($data['arr_logo']);
                        foreach ($arr_logo as $logo){
                            $logo = $ims->func->get_src_mod($logo, 115, 59, 1, 0);
                            $ims->temp_act->assign('logo', $logo);
                            $ims->temp_act->parse('form_register1.event_info.list_logo.logo');
                        }
                        $ims->temp_act->parse('form_register1.event_info.list_logo');
                        $ims->temp_act->assign('data', $data);
                        $ims->temp_act->parse('form_register1.event_info');
                        $out['event_info'] = $ims->temp_act->text('form_register1.event_info');
                    }
                    if($data['arr_price'] != ''){
                        $arr_price = $ims->func->unserialize($data['arr_price']);

                        foreach ($arr_price as $k => $ticket){
                            if($ticket['type_ticket'] == 'donate'){
                                $ticket['input_price_donate'] = '<div class="input_price" style="display: none">
                                                                    <p>'.str_replace('{min_price}', '<b>'.number_format($ticket['price'],0,',','.').' '.$ims->lang['global']['unit'].'</b>', $ims->lang['event']['input_price_donate']).'</p>
                                                                    <p><input type="number" min="'.$ticket['price'].'" value="'.$ticket['price'].'" name="book[\''.$k.'\'][\'donate_price\']"></p>
                                                                </div>';
                            }
                            $ticket['price'] = number_format($ticket['price'],0,',','.').' '.$ims->lang['global']['unit'];
                            $ticket['short'] = ($ticket['short']) ? '<p>'.$ims->func->input_editor_decode($ticket['short']).'</p>' : '';
                            $remain = (isset($ticket['num_ticket_remain']) && $ticket['num_ticket_remain'] >= 0) ? $ticket['num_ticket_remain'] : $ticket['num_ticket'];
                            $max = ($remain > 100) ? 100 : $remain;
                            $ticket['num_ticket'] = $ims->site_func->get_lang('ticket_info', 'event', array('{num_ticket_remain}' => $ticket['num_ticket_remain']));

                            $option = array();
                            if($max > 0 && $data['date_end_ticket'] > time()){
                                for($i = 0; $i <= $max; $i++){
                                    $option['title'] = ($i < 10) ? '0'.$i : $i;
                                    $option['val'] = $i;
                                    $ims->temp_act->assign('option', $option);
                                    $ims->temp_act->parse('form_register1.form.ticket.select.option');
                                }
                                $ims->temp_act->assign('type_ticket', $ticket['type_ticket']);
                                $ims->temp_act->assign('ticket_id', $k);
                                $ims->temp_act->parse('form_register1.form.ticket.select');
                            }
                            $ims->temp_act->assign('ticket', $ticket);
                            $ims->temp_act->parse('form_register1.form.ticket');
                        }
                        $ims->temp_act->assign('expiry', str_replace('{date_end_ticket}', date('d/m/Y H:i:s', $data['date_end_ticket']), $ims->lang['event']['ticket_expiry']));
                        $ims->temp_act->parse('form_register1.form');
                        $out['html_form'] = $ims->temp_act->text('form_register1.form');
                    }
                    $out['ok'] = 1;
                }
            }else{
                $out['mess'] = $ims->lang['event']['event_not_exist'];
            }
        }else{
            $event_item = isset($ims->post['event_item']) ? $ims->func->base64_decode($ims->post['event_item']) : 0;
            if($event_item) {
                $link = $ims->db->load_item('event', 'is_show = 1 and lang = "' . $lang_cur . '" and item_id = ' . $event_item, 'friendly_link');
                $login_link = $ims->db->load_item('user_setting', 'setting_key = "signin_link"', 'setting_value');
                $out['link_go'] = $ims->site_func->get_link('event', '', $login_link).'/?url='.$ims->func->base64_encode($ims->site_func->get_link('event', '', $link));
            }
            $out['ok'] = 2;
//            $out['mess'] = $ims->lang['event']['need_login'];
        }
        return json_encode($out);
    }

    function do_load_cart_info(){
        global $ims;
        include_once($ims->conf["rootpath"].DS."config/site.php");
        $ims->site = new Site();
        $dir_view = $ims->func->dirModules('event', 'views', 'path');
        $ims->temp_act = new XTemplate($dir_view ."event.tpl");
        $ims->temp_act->assign('LANG', $ims->lang);

        $out = array(
            'ok' => 0,
            'html' => '',
            'mess' => ''
        );

        $event_item = isset($ims->post['event_item']) ? $ims->func->base64_decode($ims->post['event_item']) : 0;
        $post_data = ($ims->post['data']) ? $ims->post['data'] : array();
        $step = $ims->post['step'];
        $lang_cur = $ims->post['lang_cur'];

        $data = Session::Get('ticket_selected', array());
        $promotion_code = Session::Get('promotion_code', '');
        $vat = Session::Get('vat', 0);

        $event_info = $ims->db->load_row('event', 'is_show = 1 and lang = "'.$lang_cur.'" and item_id = '.$event_item, 'arr_price, date_end_ticket, user_id, organizer');
        $list_price = ($event_info['arr_price']) ? $ims->func->unserialize($event_info['arr_price']) : array();

        if($event_info['date_end_ticket'] < time()){
            $out['mess'] = $ims->site_func->get_lang('ticket_expires', 'event', array('{date_end}' => date('d/m/Y H:i:s', $event_info['date_end_ticket'])));
            return json_encode($out);
        }

        if($step == 0){
            Session::Delete('ticket_selected');
            Session::Delete('promotion_code');
            Session::Delete('vat');
            Session::Delete('cart_info');
            Session::Delete('arr_info_booked');
        }elseif($step == 1 && !empty($post_data)){
            foreach ($post_data as $v){
                if($v['value'] == ''){
                    $v['value'] = 0;
                }
                if($v['name'] != 'promotion_code' && $v['name'] != 'vat'){
                    eval('$'.$v['name'].' = '.$v['value'].';');
                }
            }
            $data = array();
            if(isset($book) && $book){
                foreach ($book as $k1 => $v1){
                    if($v1['num'] > 0){
                        $data[$k1] = $v1;
                    }
                }
            }

            if($list_price){
                $check = 0; // Kiểm tra chọn vé hợp lệ chưa
                foreach ($data as $k => $v){
                    if(!isset($list_price[$k])){
                        unset($data[$k]);
                    }else{
                        if($v['num'] > $list_price[$k]['num_ticket_remain']){
                            $out['mess'] = $ims->site_func->get_lang('ticket_not_enough', 'event', array('{ticket_name}' => $list_price[$k]['title'], '{num_ticket}' => $list_price[$k]['num_ticket_remain']));
                            return json_encode($out);
                        }elseif($list_price[$k]['type_ticket'] == 'donate' && $v['donate_price'] < $list_price[$k]['price']){
                            $out['mess'] = $ims->site_func->get_lang('min_price_donate', 'event', array('{donate_ticket}' => $list_price[$k]['title'], '{donate_price}' => number_format($list_price[$k]['price'],0,',','.').' '.$ims->lang['global']['unit']));
                            return json_encode($out);
                        }else{
                            $check = 1;
                        }
                    }
                }

                if($check == 1 && !empty($data)){
                    Session::Set('ticket_selected', $data);
                }else{
                    Session::Delete('ticket_selected');
                }
            }
        }elseif ($step == 2){
            $data_tmp = array();
            foreach ($post_data as $v){
                if(($v['name'] == 'promotion_code' || $v['name'] == 'vat') && $v['value']){
                    $data_tmp[$v['name']] = $v['value'];
                }
            }
            if(isset($data_tmp['promotion_code'])){
                $promotion_code = trim($data_tmp['promotion_code']);
                Session::Set('promotion_code', $promotion_code);
            }else{
                $promotion_code = '';
                Session::Delete('promotion_code');
            }
            if(isset($data_tmp['vat'])){
                $vat = $data_tmp['vat'];
                Session::Set('vat', 1);
            }else{
                $vat = 0;
                Session::Delete('vat');
            }
        }

        $cart_info = array();
        if(($step == 1 || $step == 2)){
            if(!empty($data) && !empty($list_price)){
                // Load thông tin đặt vé
                $data_info = array();
                $row = array(); // item ticket

                $discount_price = 0; // Trừ tiền dùng mã giảm giá
                $total_money_tmp = 0; // Tổng tiền tạm tính
                $total_money = 0; // Tổng tiền cuối cùng
                foreach ($data as $k => $v){
                    $row['num'] = $v['num'];
                    $row['title'] = $list_price[$k]['title'];
                    if($list_price[$k]['type_ticket'] == 'donate'){
                        $price = $v['donate_price'] * $v['num'];
                    }else{
                        $price = $list_price[$k]['price'] * $v['num'];
                    }
                    $total_money_tmp += $price;
                    $total_money += $price;
                    $row['price'] = number_format($price,0,',','.').' '.$ims->lang['global']['unit'];
                    $ims->temp_act->assign('row', $row);
                    $ims->temp_act->parse('cart_info.item');
                }
                $cart_info['total_money_tmp'] = $total_money_tmp;
                if($step == 2){
                    if($promotion_code != ''){
                        $data_info['promotion_code'] = $promotion_code;
                        $promotion_check = $this->do_discount_promotion_code($post_data, $promotion_code, $total_money, $event_item, $lang_cur);
                        if($promotion_check['mess'] == ''){
                            $discount_price = $promotion_check['discount_price'];
                            $total_money -= $discount_price;
                            $cart_info['discount_price'] = $discount_price;
                            $data_info['button_code'] = '<button class="cancel" type="button">'.$ims->lang['event']['cancel'].'</button>';
                        }else{
                            $promotion_code = '';
                            Session::Delete('promotion_code');
                            $out['mess'] = $promotion_check['mess'];
                            $data_info['button_code'] = '<button class="approve" type="button">'.$ims->lang['event']['approve_code'].'</button>';
                        }
                    }else{
                        $data_info['button_code'] = '<button class="approve" type="button">'.$ims->lang['event']['approve_code'].'</button>';
                    }
                    if($vat == 1){
                        $surcharge_fee = $ims->setting['event']['vat_surcharge'] / 100 * $total_money; // Tiền phụ thu VAT
                        $total_money += $surcharge_fee;
                        $cart_info['surcharge_fee'] = $surcharge_fee;
                        $data_info['surcharge_fee'] = number_format($surcharge_fee,0,',','.').' '.$ims->lang['global']['unit'];
                        $data_info['vat_checked'] = 'checked';
                        $ims->temp_act->assign('data', $data_info);
                        $ims->temp_act->parse('cart_info.step2.vat_fee');
                    }
                    if($vat == 1 || $promotion_code != ''){
                        $data_info['total_money_tmp'] = number_format($total_money_tmp,0,',','.').' '.$ims->lang['global']['unit'];
                        $ims->temp_act->assign('data', $data_info);
                        $ims->temp_act->parse('cart_info.step2.total_money_tmp');
                    }
                    $cart_info['total_money'] = $total_money;
                    $data_info['discount_price'] = number_format($discount_price,0,',','.').' '.$ims->lang['global']['unit'];
                    $ims->temp_act->assign('data', $data_info);
                    $ims->temp_act->parse('cart_info.step2');
                }
                $data_info['total_money'] = number_format($total_money,0,',','.').' '.$ims->lang['global']['unit'];
                $ims->temp_act->assign('data', $data_info);
                $ims->temp_act->parse('cart_info');
                $out['html'] = $ims->temp_act->text('cart_info');
                $out['ok'] = 1;
                Session::Set('cart_info', $cart_info);
            }
        }
        if ($step == 3){
            $follow_html = '';
            $user_info = $ims->db->load_row('user', 'user_id = '.$event_info['user_id'], 'num_follow');
            if($user_info){
                $follow = $ims->site->check_follow($event_item);
                $num_follow = number_format($user_info['num_follow'],0,',','.');

                $btn_follow = '';
                if(!isset($follow['none_follow']) || (isset($follow['none_follow']) && $follow['none_follow'] != 'none')){
                    $btn_follow = '<p class="btn_follow '.$follow['none_follow'].'"><button type="button" data-item="'.$ims->func->base64_encode($event_item).'" class="'.$follow['class_follow'].'">'.$follow['text_follow'].'</button></p>';
                }
                $follow_html = '<div class="follow">
                                    <p class="num"><span>'.$num_follow.'</span> '.$ims->lang['event']['num_follow'].'</p>
                                    '.$btn_follow.'
                                </div>';

            }

            $out['html'] = '<div class="event_info"><img src="'.$ims->conf['rooturl'].'resources/images/use/company.png" alt="organizer"><div class="content_info"><div class="organizer">'.$event_info['organizer'].'</div>'.$follow_html.'</div></div>';
            $out['ok'] = 1;
        }
        return json_encode($out);
    }

    function do_discount_promotion_code($post_data, $promotion_code, $total_money, $event_item, $lang_cur){
        global $ims;
        $out = array(
            'mess' => '',
            'discount_price' => 0
        );

        $email = '';
        foreach ($post_data as $v){
            if($v['name'] == 'o_email'){
                $email = trim($v['value']);
                break;
            }
        }

        $promotion_info = $ims->db->load_row('promotion', 'is_show = 1 and promotion_id = "'.$promotion_code.'"');
        if (!empty($promotion_info)) {
            $cancel_order = $ims->db->load_item('event_order_status', 'is_show = 1 and lang = "'.$lang_cur.'" and is_cancel = 1', 'item_id');
            if ($promotion_info['type_promotion'] == 'apply_email'){
                if($email == ''){
                    $out['mess'] = $ims->lang['event']['input_email_promotion'];
                }else{
                    $list_email = explode(',', $promotion_info['list_email']);
                    $check = 0;
                    foreach ($list_email as $item){
                        if($email == trim($item)){
                            $check = 1;
                            break;
                        }
                    }
                    if($check == 0){
                        $out['mess'] = $ims->lang['event']['err_promotion_user'];
                    }else{
                        $total_use = $ims->db->do_count('event_order', 'is_status != '.$cancel_order.' and promotion_id = "'.$promotion_code.'" and o_email = "'.$email.'"', 'order_id');
                        if($total_use >= 1){
                            $out['mess'] = $ims->lang['event']['err_promotion_numover'];
                        }
                    }
                }
            }elseif ($promotion_info['type_promotion'] == 'apply_event'){
                $list_event = explode(',', $promotion_info['list_event']);
                if(!in_array($event_item, $list_event)){
                    $out['mess'] = $ims->lang['event']['event_promotion_incorrect'];
                }else{
                    $total_use = $ims->db->do_count('event_order', 'is_status != '.$cancel_order.' and promotion_id = "'.$promotion_code.'" and event_id = '.$event_item, 'order_id');
                    if($total_use >= $promotion_info['max_use']){
                        $out['mess'] = $ims->lang['event']['err_promotion_numover'];
                    }
                }
            }

            if($out['mess'] == ''){
                if($promotion_info['date_start'] > time()) {
                    $out['mess'] = $ims->lang['event']['err_promotion_notyet_timetouse']; // chưa tới ngày sử dụng
                }elseif($promotion_info['date_end'] < time()) {
                    $out['mess'] = $ims->lang['event']['err_promotion_date_end']; // mã hết hạn
                }else{
                    if($promotion_info['value_type'] == 1){
                        $tmp_percent = $promotion_info['value'];
                        $tmp_price = round(($tmp_percent * $total_money) / 100, 2);
                        if($promotion_info['value_max'] > 0 && $tmp_price > $promotion_info['value_max']){
                            $tmp_price = $promotion_info['value_max'];
                        }
                    }else{
                        $tmp_price = $promotion_info['value'];
                        if($tmp_price >= $total_money){
                            $tmp_price = $total_money;
                        }
                    }
                    $out['discount_price'] = $tmp_price;
                }
            }
        }else{
            $out['mess'] = $ims->lang['event']['err_promotion_wrong'];
        }

        return $out;
    }

    function do_register_step1(){
        global $ims;
        $dir_view = $ims->func->dirModules('event', 'views', 'path');
        $ims->temp_act = new XTemplate($dir_view ."event.tpl");
        $ims->temp_act->assign('LANG', $ims->lang);

        $out = array(
            'ok' => 0,
            'html' => '',
            'mess' => ''
        );
        $event_item = isset($ims->post['event_item']) ? $ims->func->base64_decode($ims->post['event_item']) : 0;
        $lang_cur = $ims->post['lang_cur'];

        $data = Session::Get('ticket_selected', array());

        if($ims->site_func->checkUserLogin() == 1) {
            $user = $ims->data['user_cur'];
            $event_info = $ims->db->load_row('event', 'is_show = 1 and lang = "'.$lang_cur.'" and item_id = '.$event_item, 'arr_price, date_end_ticket, min_ticket, max_ticket');
            $list_price = ($event_info['arr_price']) ? $ims->func->unserialize($event_info['arr_price']) : array();

            if(empty($data)){
                $out['mess'] = str_replace('{num_ticket}', $event_info['min_ticket'], $ims->lang['event']['min_ticket_require']);
            }else{
                $total = 0;
                foreach ($data as $k => $v){
                    $total += $v['num'];
                    for($i = 1; $i <= $v['num']; $i++){
                        $ticket = array();
                        $ticket['index'] = $i - 1;
                        $ticket['id'] = $k;
                        $ticket['ticket_name'] = $ims->lang['event']['ticket'].' '.$i.' - '.$list_price[$k]['title'];
                        $ims->temp_act->assign('ticket', $ticket);
                        $ims->temp_act->parse('form_register2.item_ticket');
                    }
                }
                if($total < $event_info['min_ticket']){
                    $out['mess'] = str_replace('{num_ticket}', $event_info['min_ticket'], $ims->lang['event']['min_ticket_require']);
                }elseif ($total > $event_info['max_ticket']){
                    $out['mess'] = str_replace('{num_ticket}', $event_info['max_ticket'], $ims->lang['event']['max_ticket_require']);
                }else{
                    $list_payment = $ims->db->load_item_arr('order_method', 'is_show = 1 and lang = "'.$lang_cur.'" order by show_order desc, date_create desc', '*');
                    if($list_payment){
                        $i = 0;
                        foreach ($list_payment as $payment){
                            $i++;
                            $payment['content'] = $ims->func->input_editor_decode($payment['content']);
//                            $payment['none'] = ($i == 1) ? '' : 'style="display:none;"';
//                            $payment['checked'] = ($i == 1) ? 'checked' : '';
                            $payment['picture'] = $ims->func->get_src_mod($payment['picture'], 30, 30, 1, 0);
                            if($payment['name_action'] == 'paypal'){
                                $payment['content'] .= '<p><b>'.str_replace('{num}', number_format($ims->setting['event']['exchange_rate'],0,',','.'), $ims->lang['event']['exchange_rate']).'</b></p>';
                            }
                            $ims->temp_act->assign('payment', $payment);
                            $ims->temp_act->parse('form_register2.item_payment');
                        }
                    }
                    $ims->temp_act->assign('user', $user);
                    $ims->temp_act->parse('form_register2');
                    $out['html'] = $ims->temp_act->text('form_register2');
                    $out['ok'] = 1;
                }
            }
        }else{
            $out['mess'] = $ims->lang['event']['need_login'];
        }

        return json_encode($out);
    }

    function do_register_step2(){
        global $ims;
        include_once($ims->conf["rootpath"].DS."config/site.php");
        $ims->site = new Site();

        $out = array(
            'ok' => 0,
            'html' => '',
            'mess' => '',
            'link' => ''
        );
        $data_post = $ims->post['data'];
        $event_item = isset($ims->post['event_item']) ? $ims->func->base64_decode($ims->post['event_item']) : 0;
        $lang_cur = $ims->post['lang_cur'];

        $ticket_selected = Session::Get('ticket_selected', array());
        $promotion_code = Session::Get('promotion_code', '');
        $cart_info = Session::Get('cart_info', array());

        if($ims->site_func->checkUserLogin() == 1) {
            $data = array();
            foreach ($data_post as $v){
                if(substr($v['name'], 0, 11) == 'ticket_info'){
                    eval('$'.$v['name'].' = "'.$v['value'].'";');
                }else{
                    $data[$v['name']] = $v['value'];
                }
            }

            if($cart_info['total_money'] > 0){
                if(!isset($data['method'])){
                    $out['mess'] = $ims->lang['event']['choose_ordering_method'];
                }
            }

            if($out['mess'] == ''){
                $event_info = $ims->db->load_row('event', 'is_show = 1 and lang = "'.$lang_cur.'" and item_id = '.$event_item);
                if($event_info['arr_price']){
                    $arr_price = $ims->func->unserialize($event_info['arr_price']);
                }
                $recommend_type = '';
                if($ims->site_func->checkUserLogin() == 1){
                    $info_deeplink = $ims->db->load_row('user_recommend_log', 'is_show = 1 and referred_user_id = '.$ims->data['user_cur']['user_id'], 'type, recommend_user_id, deeplink_id');
                    if($info_deeplink){
                        $recommend_type = $info_deeplink['type'];
                    }elseif (isset($_COOKIE['deeplink'])){
                        $recommend_type = 'deeplink';
                    }elseif (isset($_COOKIE['user_contributor'])){
                        $recommend_type = 'contributor';
                    }
                }else{
                    $recommend_type_log = $ims->db->load_row('user_recommend_log', 'is_show = 1 and referred_phone = "'.$data['o_phone'].'" or referred_email = "'.$data['o_email'].'"', 'type, recommend_user_id, recommend_link, deeplink_id');
                    if($recommend_type_log){
                        $recommend_type = $recommend_type_log['type'];
                        if($recommend_type == 'deeplink'){
                            $_COOKIE['deeplink'] = $recommend_type_log['deeplink_id'];
                            unset($_COOKIE['user_contributor']);
                        }elseif ($recommend_type == 'contributor'){
                            $_COOKIE['user_contributor'] = $ims->db->load_item('user', 'is_show = 1 and user_id = '.$recommend_type_log['recommend_user_id'], 'user_code');
                            parse_str($recommend_type_log['recommend_link'], $type);
                            $_COOKIE['type_contributor'] = $type['type'];
                            unset($_COOKIE['deeplink']);
                        }
                    }elseif(isset($_COOKIE['deeplink'])){
                        $recommend_type = 'deeplink';
                    }elseif (isset($_COOKIE['user_contributor'])){
                        $recommend_type = 'contributor';
                    }
                }

                $order['o_full_name']      = $data['o_full_name'];
                $order['o_email']          = $data['o_email'];
                $order['o_phone']          = $data['o_phone'];
                $order['method'] 		   = $ims->func->if_isset($data['method'], 0);
                $order["user_id"] 	       = $ims->func->if_isset($ims->data['user_cur']["user_id"], 0);
                $order["is_show"]          = (isset($data['method'])) ? (($data['method'] == 3) ? 1 : 0) : 1;
                $order["is_status_payment"]= (!isset($data['method']) || (isset($data['method']) && $data['method'] == 3)) ? 0 : 1;
                $order['is_status']        = $ims->site_func->getStatusOrder(1);
                $order["total_order"] 	   = $cart_info['total_money_tmp'];
                $order["promotion_id"] 	   = $promotion_code;
                if($promotion_code != ''){
                    $promotion_info = $ims->db->load_row('promotion', 'promotion_id = "'.$promotion_code.'"');
                    if($promotion_info['value_type'] == 1){
                        $tmp_percent = $promotion_info['value'];
                    }else{
                        $tmp_percent = round(($promotion_info['value'] * 100) / $cart_info['total_money_tmp'], 2);
                        if($promotion_info['value'] > $cart_info['total_money_tmp']){
                            $tmp_percent = 100;
                        }
                    }
                    $order["promotion_price"]  = $ims->func->if_isset($cart_info['discount_price'], 0);
                    $order["promotion_percent"]= $tmp_percent;
                }
                $order["vat_price"]  = $ims->func->if_isset($cart_info['surcharge_fee'], 0);
                $order["show_order"] 	   = 0;
                $order["event_id"] 	       = $event_item;
                $order["sales_channel"]    = 'web';
                $order["date_create"]      = time();
                $order["date_update"]      = time();

                $deeplink_user_id = 0;
                $contributor_user_id = 0;
                if($recommend_type == 'deeplink'){
                    if($ims->site_func->checkUserLogin() == 1){ // check deeplink theo database
                        if(isset($info_deeplink) && $info_deeplink){
                            $deeplink_user_id = $info_deeplink['recommend_user_id'];
                            $order['deeplink_id'] = $info_deeplink['deeplink_id'];
                        }
                    }elseif(isset($_COOKIE["deeplink"])){ // check deeplink theo cookie
                        $deeplink_user_id = $ims->db->load_item('user_deeplink', 'is_show = 1 and id = '.$_COOKIE["deeplink"], 'user_id');
                        $order['deeplink_id'] = $_COOKIE["deeplink"];
                    }
                    if($deeplink_user_id > 0){
                        $order['deeplink_valid'] = 1;
                        $order['deeplink_user']  = $deeplink_user_id;
                    }
                }elseif($recommend_type == 'contributor'){
                    if($ims->site_func->checkUserLogin() == 1){
                        $contributor_user_id = $ims->db->load_item('user_recommend_log', 'is_show = 1 and type = "contributor" and referred_user_id = '.$ims->data['user_cur']['user_id'], 'recommend_user_id');
                    }elseif(isset($_COOKIE["user_contributor"])){
                        $contributor_user_id = $ims->db->load_item('user', 'is_show = 1 and user_code = "'.$_COOKIE["user_contributor"].'"', 'user_id');
                    }
                }
                $ok = $ims->db->do_insert("event_order", $order);
                $deeplink_total = 0; // Hoa hồng tiếp thị liên kết
                $deeplink_total_old_temp = 0; // Hoa hồng tiếp thị liên kết tạm dành cho người mua cũ
                $is_use_deeplink_old = 0; // Dùng deeplink cho người mua cũ
                if($ok){
                    $order_info 	 = $order;
                    $order_id 		 = $ims->db->insertid();
                    $order_id_random = 'TK'.($order_id ? ($order_id+99999) : 100000);

                    // Kiểm tra KH đã có đơn hàng thành công hay chưa (dành cho deeplink)
                    $completed_order_status = $ims->db->load_item('product_order_status', 'is_show = 1 and lang = "'.$ims->conf['lang_cur'].'" and is_complete = 1', 'item_id');
                    if($ims->site_func->checkUserLogin() == 1){
                        $check_old_order = $ims->db->load_item('event_order', 'is_show = 1 and ((user_id = '.$ims->data['user_cur']['user_id'].') or (user_id = 0 and (o_email = "'.$ims->data['user_cur']['email'].'" or o_phone = "'.$ims->data['user_cur']['phone'].'"))) and is_status = '.$completed_order_status, 'order_id');
                    }else{
                        $check_old_order = $ims->db->load_item('event_order', 'is_show = 1 and (o_email = "'.$order['o_email'].'" or o_phone = "'.$order['o_phone'].'") and is_status = '.$completed_order_status, 'order_id');
                    }

                    if(isset($ticket_info) && !empty($ticket_selected)){
                        foreach($ticket_info as $k1 => $item) {
                            foreach ($item as $row){
                                $row['order_id'] = $order_id;
                                $row['ticket_code'] = $k1;
                                $row['event_id'] = $event_item;
                                $row['ticket_info'] = ($event_info['arr_price'] != '') ? $event_info['arr_price'] : '';
                                $row['title'] = $arr_price[$k1]['title'];
                                $row['price_buy'] = ($arr_price[$k1]['type_ticket'] == 'donate') ? $ticket_selected[$k1]['donate_price'] : $arr_price[$k1]['price'];
                                $row['quantity'] = 1;
                                $row['date_create'] = time();
                                $row['date_update'] = time();
                                $ims->db->do_insert("event_order_detail", $row);
                            }

                            // Tính hoa hồng tiếp thị liên kết trên từng sản phẩm
//                            if ($recommend_type == 'deeplink' && $deeplink_user_id > 0){
//                                $price_use_commisson = (float)$col['price_buy'] * (int)$col['quantity']; // Giá sản phẩm dành cho tính hoa hồng
//
//                                $promotion_code = Session::Get('promotion_code', '');
//                                if($promotion_code != ''){
//                                    $promotion = $this->orderiFunc->promotion_discount_per_item($event['item_id'], $price_use_commisson, $promotion_code);
//                                    $price_use_commisson -= (float)$promotion['price_minus']; // Trừ tiền khuyến mãi
//                                }
//                                if((int)$event['group_id'] == 0){
//                                    $percent_deeplink_old = (float)$ims->setting['event']['percent_deeplink_default_old'];
//                                    $percent_deeplink_new = (float)$ims->setting['event']['percent_deeplink_default_new'];
//                                }else{
//                                    $group_nav = explode(',', $event['group_nav']);
//                                    $group_id = $group_nav[0];
//                                    $percent_deeplink_group = $ims->db->load_row('event_group', 'is_show = 1 and lang = "'.$ims->conf["lang_cur"].'" and group_id = '.$group_id, 'percent_deeplink_old, percent_deeplink_new');
//                                    $percent_deeplink_old = ((float)$percent_deeplink_group['percent_deeplink_old'] > 0) ? (float)$percent_deeplink_group['percent_deeplink_old'] : (float)$ims->setting['event']['percent_deeplink_default_old'];
//                                    $percent_deeplink_new = ((float)$percent_deeplink_group['percent_deeplink_new'] > 0) ? (float)$percent_deeplink_group['percent_deeplink_new'] : (float)$ims->setting['event']['percent_deeplink_default_new'];
//                                }
//
//                                if($check_old_order){
//                                    $deeplink_item_tmp = ($price_use_commisson * $percent_deeplink_old/100);
//                                    $deeplink_total += ($deeplink_item_tmp > (float)$ims->setting['event']['amount_deeplink_default']) ? (float)$ims->setting['event']['amount_deeplink_default'] : $deeplink_item_tmp;
//                                    $is_use_deeplink_old = 1;
//                                }else{
//                                    $deeplink_item_new_tmp = ($price_use_commisson * $percent_deeplink_new/100);
//                                    $deeplink_item_old_tmp = ($price_use_commisson * $percent_deeplink_old/100);
//
//                                    $deeplink_total += ($deeplink_item_new_tmp > (float)$ims->setting['event']['amount_deeplink_default']) ? (float)$ims->setting['event']['amount_deeplink_default'] : $deeplink_item_new_tmp;
//                                    $deeplink_total_old_temp += ($deeplink_item_old_tmp > (float)$ims->setting['event']['amount_deeplink_default']) ? (float)$ims->setting['event']['amount_deeplink_default'] : $deeplink_item_old_tmp;
//                                }
//                                $deeplink_total += (isset($arr_gift_include['deeplink_total_include'])) ? (float)$arr_gift_include['deeplink_total_include'] : 0;
//                                $deeplink_total_old_temp += (isset($arr_gift_include['deeplink_total_include_old'])) ? (float)$arr_gift_include['deeplink_total_include_old'] : 0;
//
//                                $deeplink_detail[] = array(
//                                    'item_id' => $col['type_id'],
//                                    'picture' => $col['picture'],
//                                    'option_id' => $col['option_id'],
//                                    'price_buy' => $col['price_buy'],
//                                    'quantity' => $col['quantity'],
//                                    'price_use_commisson' => $price_use_commisson,
//                                    'root_group' => ($event['group_id'] > 0) ? $group_id : 0,
//                                    'percent_deeplink_group_old' => ((int)$event['group_id'] > 0 && isset($percent_deeplink_group['percent_deeplink_old'])) ? $percent_deeplink_group['percent_deeplink_old'] : 0,
//                                    'percent_deeplink_group_new' => ((int)$event['group_id'] > 0 && isset($percent_deeplink_group['percent_deeplink_new'])) ? $percent_deeplink_group['percent_deeplink_new'] : 0,
//                                    'percent_deeplink_default_old' => $ims->setting['event']['percent_deeplink_default_old'],
//                                    'percent_deeplink_default_new' => $ims->setting['event']['percent_deeplink_default_new'],
//                                    'max_deeplink_default_per_item' => $ims->setting['event']['amount_deeplink_default'],
//                                    'arr_deeplink_include' => (isset($arr_gift_include['arr_deeplink_include'])) ? $arr_gift_include['arr_deeplink_include'] : ''
//                                );
//                            }
                        }
                    }

                    // Nhập lịch sử hoa hồng theo từng sản phẩm
//                    if($recommend_type == 'deeplink' && $deeplink_user_id > 0){
//                        $deeplink_log = array(
//                            'order_id' => $order_id,
//                            'deeplink_id' => $order['deeplink_id'],
//                            'order_user' => (isset($ims->data['user_cur']['user_id'])) ? $ims->data['user_cur']['user_id'] : 0,
//                            'deeplink_detail' => $ims->func->serialize($deeplink_detail),
//                            'commission_add' => $deeplink_total,
//                            'commission_add_old_temp' => $deeplink_total_old_temp,
//                            'is_show' => 1,
//                            'is_added' => 0,
//                            'date_create' => time(),
//                            'date_update' => time(),
//                        );
//                        $ims->db->do_insert("user_deeplink_log", $deeplink_log);
//                    }

                    // Cập nhật lại danh sách sản phẩm đã lưu
//                    $list_save = array_values($list_save);
//                    $list_save = $ims->func->serialize($list_save);
//                    $ims->db->do_update('user', array('list_save' => $list_save), 'user_id = '.$ims->data['user_cur']['user_id']);

                    // ---------------- promotion log
                    if($promotion_code != ''){
                        $promo_log = array();
                        $promo_log['promotion_id'] = $promotion_code;
                        $promo_log['user_id'] = $ims->func->if_isset($ims->data['user_cur']["user_id"], 0);
                        $promo_log['order_id'] = $order_id;
                        $promo_log['is_show'] = 1;
                        $promo_log['date_create'] = time();
                        $ok_promotion = $ims->db->do_insert("promotion_log", $promo_log);
                        if($ok_promotion){
                            $ims->db->query("UPDATE promotion SET num_use=num_use+1, date_update=".time()." WHERE promotion_id='".$promotion_code."'");
                        }
                    }
                    // ---------------- END promotion log

                    $col_up = array();
                    $col_up["order_code"] = $order_id_random;
                    $col_up['total_payment'] = $cart_info['total_money'];

                    // Cập nhật hoa hồng cho người giới thiệu link thường
                    if($recommend_type == 'contributor' && $contributor_user_id != 0){
                        if($ims->site_func->checkUserLogin() == 1){
                            $col_up["user_contributor"] = $ims->data['user_cur']['user_contributor'];
                        }elseif (isset($_COOKIE['user_contributor'])){
                            $col_up["user_contributor"] = $_COOKIE['user_contributor'];
                        }
//                        $col_up["wcoin_contributor"] = $col_up['wcoin_accumulation'] * $ims->setting['event']['percentforcontributor']/100;
                        $col_up["money_contributor"] = $order['total_payment'] * $ims->setting['event']['percentforcontributor']/100;
                    }

                    // Cập nhật log giới thiệu khi không đăng nhập
                    if($ims->site_func->checkUserLogin() != 1){
                        $check_inserted = $ims->db->load_item('user_recommend_log', 'is_show = 1 and referred_email = "'.$order['o_email'].'" or referred_phone = "'.$order['o_phone'].'"', 'id');
                        if(!$check_inserted){
                            $recommend_log = array();
                            if($recommend_type == 'contributor' && $contributor_user_id != 0){
                                $recommend_log = array(
                                    'type' => 'contributor',
                                    'recommend_user_id' => $contributor_user_id,
                                    'recommend_link' => 'contributor='.$ims->func->base64_encode($_COOKIE['user_contributor']).'&type='.$_COOKIE['type_contributor'],
                                    'referred_user_id' => 0,
                                    'referred_full_name' => $order['o_full_name'],
                                    'referred_email' => $order['o_email'],
                                    'referred_phone' => $order['o_phone'],
                                    'is_show' => 1,
                                    'date_create' => time(),
                                    'date_update' => time(),
                                );
                            }elseif ($recommend_type == 'deeplink' && $deeplink_user_id != 0){
                                $recommend_log = array(
                                    'type' => 'deeplink',
                                    'recommend_user_id' => $deeplink_user_id,
                                    'recommend_link' => $ims->db->load_item('user_deeplink', 'is_show = 1 and id = '.$_COOKIE["deeplink"], 'short_code'),
                                    'deeplink_id' => $_COOKIE["deeplink"],
                                    'referred_user_id' => 0,
                                    'referred_full_name' => $order['o_full_name'],
                                    'referred_email' => $order['o_email'],
                                    'referred_phone' => $order['o_phone'],
                                    'is_show' => 1,
                                    'date_create' => time(),
                                    'date_update' => time(),
                                );
                            }
                            if($recommend_log){
                                $ims->db->do_insert("user_recommend_log", $recommend_log);
                            }
                        }
                    }

                    if($recommend_type == 'deeplink' && $deeplink_user_id != 0){ // Update hoa hồng tiếp thị liên kết vào event_order
                        $col_up['deeplink_total'] = $deeplink_total;
                        $col_up['deeplink_total_old_temp'] = $deeplink_total_old_temp;
                        $col_up['is_use_deeplink_old'] = $is_use_deeplink_old;
                    }
                    $ims->db->do_update("event_order", $col_up, " order_id='".$order_id."'");

                    $order_info = array_merge($order_info, $col_up);
                    // -------------------- End Update order
                    $arr_info_booked = array(
                        'event_item' => $event_item,
                        'order_id' => $order_id
                    );
                    Session::Set('arr_info_booked', $arr_info_booked);
                    // ------------------------ *************** Thanh toán ONLINE
                    $orderMethod = $ims->load_data->data_table (
                        'order_method',
                        'method_id',
                        '*',
                        'is_show = 1 and lang = "'.$lang_cur.'"'
                    );

                    if(isset($orderMethod[$order_info['method']]) && $orderMethod[$order_info['method']]['name_action']!='') { //  Thanh toán qua ONLINE
//                        $input['address']['bankcode'] = $ims->func->if_isset($input['bankcode'], 0);
//                        $resurl = $ims->site_func->paymentCustom($orderMethod[$order_info['method']], $order_info, $col_up , $input['address']);
                        $link = $ims->db->load_item('event', 'is_show = 1 and lang = "'.$lang_cur.'" and item_id = '.$event_item, 'friendly_link');
                        $link_go = $ims->site_func->get_link('event', $link);
                        $resurl = $ims->site_func->paymentCustom($orderMethod[$order_info['method']], $order_info, $col_up , array(), 'event_order', $link_go);
                        if (isset($resurl['ok']) && $resurl['ok'] == 1) {
                            $out['link'] = $resurl['link'];
                            $out['ok'] = 1;
                        }
                    }else{
                        $ims->site->update_arr_price_event($arr_info_booked);
                        $event_ticket = $ims->site->create_image(); // Tạo vé sự kiện
                        $out['event_ticket'] = $event_ticket['content'];
                        $out['arr_name'] = $event_ticket['arr_name'];

                        Session::Delete('ticket_selected');
                        Session::Delete('promotion_code');
                        Session::Delete('vat');
                        Session::Delete('cart_info');
                        $out['ok'] = 1;
                    }
                }
            }
        }else{
            $out['mess'] = $ims->lang['event']['need_login'];
        }
        return json_encode($out);
    }

    function do_upload_ticket_render(){
        global $ims;
        $out = array('ok' => 0);

        $index = $ims->post['index'];

        if ($ims->site_func->checkUserLogin() == 1) {
            $folder_upload = "user/".$ims->data['user_cur']['folder_upload'].'/'.date('Y',time()).'_'.date('m',time()).'/ticket';
        }else{
            $folder_upload = "event/".date('Y',time()).'_'.date('m',time()).'/ticket';
        }

        $ims->func->rmkdir($folder_upload);
        $img = $ims->post['imgBase64'];
        $img = str_replace('data:image/png;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);
        $file = $ims->conf['rootpath'].'uploads/'.$folder_upload. '/'.$index.'.png';
        $ok = file_put_contents ($file, $data);
        if($ok){
            $detail_id = explode('_', $index)[1];
            $ims->db->do_update('event_order_detail', array('ticket' => $folder_upload. '/'.$index.'.png'), 'detail_id = '.$detail_id);
            $out['ok'] = 1;
            return json_encode($out);
        }
    }
    function do_send_mail_ticket(){
        global $ims;

        $arr_info_booked = Session::Get('arr_info_booked', array());
        if($arr_info_booked){
            $order_email = $ims->db->load_item('event_order', 'order_id = '.$arr_info_booked['order_id'], 'o_email');
            $list_pic = $ims->db->load_item_arr('event_order_detail', 'order_id = '.$arr_info_booked['order_id'], 'ticket');

            $attach = array();
            foreach ($list_pic as $item){
                if($item['ticket'] != ''){
                    $attach[] = $ims->conf['rootpath'].'uploads/'.$item['ticket'];
                }
            }
            if($attach){
                $ok = $ims->func->send_mail_temp ('register-event', $order_email, $ims->conf['email'], array(), array(), $attach);
                $ok = $ims->func->send_mail_temp ('register-event', 'quoctuan122@gmail.com', $ims->conf['email'], array(), array(), $attach);
                if($ok){
                    $ims->db->do_update('event_order', array('is_sent_mail' => 1), 'order_id = '.$arr_info_booked['order_id']);
                }
            }
        }
    }

    function do_send_other_mail(){
        global $ims;
        $out = array('ok' => 0);

        $email = $ims->post['mail'];
        if($email == ''){
            $out['mess'] = $ims->lang['event']['empty_mail'];
        }else{
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $out['mess'] = $ims->lang['event']['invalid_mail'];
            }else{
                $arr_info_booked = Session::Get('arr_info_booked', array());
                if($arr_info_booked){
                    $list_pic = $ims->db->load_item_arr('event_order_detail', 'order_id = '.$arr_info_booked['order_id'], 'ticket');

                    $attach = array();
                    foreach ($list_pic as $item){
                        if($item['ticket'] != ''){
                            $attach[] = $ims->conf['rootpath'].'uploads/'.$item['ticket'];
                        }
                    }
                    if($attach){
                        $ok = $ims->func->send_mail_temp ('register-event', $email, $ims->conf['email'], array(), array(), $attach);
                        if($ok){
                            $out['ok'] = 1;
                            $out['mess'] = $ims->lang['event']['send_mail_other_success'];
                        }
                    }
                }
            }
        }
        return json_encode($out);
    }

    function do_load_complete_order_event(){
        global $ims;
        $dir_view = $ims->func->dirModules('event', 'views', 'path');
        $ims->temp_act = new XTemplate($dir_view ."event.tpl");
        $ims->temp_act->assign('LANG', $ims->lang);
        $ims->temp_act->assign('CONF', $ims->conf);

        $out = array(
            'html' => '',
            'complete_bottom' => ''
        );

        $lang_cur = $ims->post['lang_cur'];
        $arr_info_booked = Session::Get('arr_info_booked', array());
        if($ims->site_func->checkUserLogin() == 1 && !empty($arr_info_booked)) {
            $data = $ims->db->load_row('event', 'is_show = 1 and lang = "'.$lang_cur.'" and item_id = '.$arr_info_booked['event_item']);
            $data['title'] = $ims->func->input_editor_decode($data['title']);
            $data['title1'] = ($data['title1'] != '') ? $data['title1'].': ' : '';
            if($data['organizer'] != ''){
                $data['organizational'] = '<div class="organizational">'.$ims->lang['event']['organizational'].': <span>'.$data['organizer'].'</span></div>';
            }
            $data['date_begin'] = $ims->lang['global']['day_'.date('N', $data['date_begin'])].date(', d/m, h:i A', $data['date_begin']);
            $data['link'] = $ims->site_func->get_link('event', $data['friendly_link']);
            $data['content'] = $ims->func->short($data['content'], 196);

            $order = $ims->db->load_row('event_order', 'order_id = '.$arr_info_booked['order_id']);
            $data = array_merge($data, $order);
            $total_order = $ims->db->do_count('event_order_detail', 'order_id = '.$arr_info_booked['order_id'], 'detail_id');
            $data['mail_sent'] = str_replace('{num}', $total_order, $ims->lang['event']['mail_sent']);
            $data['button_support'] = '';
            if($data['zalo'] != ''){
                $data['button_support'] = '<li><a href="'.$data['zalo'].'" target="_blank" style="background: #0091FF">'.$ims->lang['event']['zalo_group'].'</a></li>';
            }
            if($data['facebook'] != ''){
                $data['button_support'] .= '<li><a href="'.$data['facebook'].'" target="_blank" style="background: #4267B2">'.$ims->lang['event']['facebook_group'].'</a></li>';
            }
            $ims->temp_act->assign('data', $data);
            if($data['button_support'] != ''){
                $ims->temp_act->parse('order_complete.support');
            }
            $out['complete_bottom'] = '<div class="list_item">
                                        <div class="btn_view_ticket"><a href="'.$data['link'].'/?view_ticket=1">'.$ims->lang['event']['view_ticket'].'</a></div>
                                            <div class="list_share">
                                                <ul class="list_none">
                                                    <li><a href="https://twitter.com/intent/tweet?url='.$data['link'].'" target="_blank"><img src="'.$ims->conf['rooturl'].'resources/images/use/twitter.svg" alt="twitter"></a></li>
                                                    <li><a href="https://www.instagram.com/?url='.$data['link'].'" target="_blank"><img src="'.$ims->conf['rooturl'].'resources/images/use/instagram.svg" alt="instagram"></a></li>
                                                    <li><a href="https://www.linkedin.com/shareArticle?mini=true&url=&title=&summary=&source='.$data['link'].'" target="_blank"><img src="'.$ims->conf['rooturl'].'resources/images/use/linkedin.svg" alt="linkedin"></a></li>
                                                    <li><a href="https://facebook.com/sharer/sharer.php?u='.$data['link'].'" target="_blank"><img src="'.$ims->conf['rooturl'].'resources/images/use/facebook.svg" alt="facebook"></a></li>
                                                </ul>
                                            </div>
                                        </div>';
            $ims->temp_act->parse('order_complete');
            $out['html'] = $ims->temp_act->text('order_complete');
        }
        return json_encode($out);
    }
    function do_edit_ticket(){
        global $ims;
        $out = array(
            'ok' => 0,
            'mess' => $ims->lang['event']['edit_ticket_false'],
        );
        $arr_info_booked = Session::Get('arr_info_booked', array());
        if($ims->site_func->checkUserLogin() == 1 && !empty($arr_info_booked)) {
            $user_order = $ims->db->load_item('event_order', 'order_id = '.$arr_info_booked['order_id'], 'user_id');
            if($user_order == $ims->data['user_cur']['user_id']){
                $data = $ims->post['data'];
                $lang_cur = $ims->post['lang_cur'];
                foreach ($data as $v){
                    eval('$'.$v['name'].' = "'.$v['value'].'";');
                }
                if(isset($ticket) && !empty($ticket)){
                    foreach ($ticket as $k => $v){
                        $item = $ims->func->base64_decode($k);
                        $v['date_update'] = time();
                        $ok = $ims->db->do_update('event_order_detail', $v, 'detail_id="'.$item.'"');
                    }
                    if($ok){
                        $link = $ims->db->load_item('event', 'is_show = 1 and lang = "'.$lang_cur.'" and item_id = '.$arr_info_booked['event_item'], 'friendly_link');
                        $out['link'] = $ims->site_func->get_link('event', $link).'/?view_ticket=1';
                        $out['ok'] = 1;
                        $out['mess'] = $ims->lang['event']['edit_ticket_success'];
                    }
                }
            }
        }

        return json_encode($out);
    }
    function do_cancel_ticket_booked(){
        global $ims;
        $out = array(
            'ok' => 0,
            'mess' => $ims->lang['event']['cancel_ticket_false']
        );

        $arr_info_booked = Session::Get('arr_info_booked', array());
        if($ims->site_func->checkUserLogin() == 1 && !empty($arr_info_booked)) {
            $user_order = $ims->db->load_item('event_order', 'order_id = '.$arr_info_booked['order_id'], 'user_id');
            if($user_order == $ims->data['user_cur']['user_id']){
                $check_payment = $ims->db->load_row('event_order', 'order_id = '.$arr_info_booked['order_id'], 'method, is_status_payment');
                if($check_payment['method'] == 3 || ($check_payment['method'] != 3 && !in_array($check_payment['is_status_payment'], array(3,5)))){
                    $cancel_status = $ims->site_func->getStatusOrder(-1);
                    $ok = $ims->db->do_update('event_order', array('is_status' => $cancel_status, 'is_cancel' => 1, 'date_cancel' => time()), 'order_id='.$arr_info_booked['order_id']);
                    if($ok){
                        // BEGIN: Cập nhật arr_price event: Số lượng vé còn lại
                        $arr_price = $ims->db->load_item('event', 'item_id = '.$arr_info_booked['event_item'], 'arr_price');
                        $arr_price = $ims->func->unserialize($arr_price);
                        foreach ($arr_price as $k => $v){
                            $count = $ims->db->do_count('event_order_detail', 'order_id = '.$arr_info_booked['order_id'].' and ticket_code = "'.$k.'"', 'detail_id');
                            $arr_price[$k]['num_ticket_remain'] += $count;
                        }
                        $arr_price = $ims->func->serialize($arr_price);
                        $ims->db->do_update("event", array('arr_price' => $arr_price), " item_id = '".$arr_info_booked['event_item']."'");
                        // END: Cập nhật arr_price event: Số lượng vé còn lại
                        $out['ok'] = 1;
                        $out['mess'] = $ims->lang['event']['cancel_ticket_success'];
                        Session::Delete('arr_info_booked');
                    }
                }
            }
        }
        return json_encode($out);
    }
    function do_contact(){
        global $ims;

        $out = array(
            'ok' => 0,
            'mess' => $ims->lang['event']['contact_false']
        );

        $data = $ims->post['data'];
        if(!empty($data)){
            $arr_in = array();
            foreach ($data as $item){
                $arr_in[$item['name']] = $item['value'];
            }
            $arr_in['is_show'] = 1;
            $arr_in['date_create'] = time();
            $arr_in['date_update'] = time();
            $ok = $ims->db->do_insert("contact", $arr_in);
            if($ok){
                $out['ok'] = 1;
                $out['mess'] = $ims->lang['event']['contact_success'];

                //Send email
                $mail_arr_value = $arr_in;
                $mail_arr_value['date_create'] = $ims->func->get_date_format($mail_arr_value["date_create"]);
                $mail_arr_value['domain'] = $_SERVER['HTTP_HOST'];
                $mail_arr_key = array();
                foreach($mail_arr_value as $k => $v) {
                    $mail_arr_key[$k] = '{'.$k.'}';
                }

                // send to admin
                $ims->func->send_mail_temp ('admin-contact', $ims->conf['email'], $ims->conf['email'], $mail_arr_key, $mail_arr_value);

                //send to contact
                $ims->func->send_mail_temp ('contact', $arr_in["email"], $ims->conf['email'], $mail_arr_key, $mail_arr_value);
                //End Send email
            }
        }
        return json_encode($out);
    }
}
?>