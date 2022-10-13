<?php
if (!defined('IN_ims')) { die('Access denied'); }
function load_setting ()
{
    global $ims;

    $ims->site_func->setting('gallery');
    return true;
}
load_setting ();
$nts = new sMain();

use \Firebase\JWT\JWT;


class sMain{

    var $modules = "gallery";
    var $action  = "ajax";

    function __construct (){
        global $ims;

        $ims->func->load_language($this->modules);
        $fun = (isset($ims->post['f'])) ? $ims->post['f'] : '';

        switch ($fun) {
            case "load_more":
                echo $this->do_load_more();
                exit;
                break;
            case "load_detail_image":
                echo $this->do_load_detail_image();
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

    function do_load_more(){
        global $ims;

        include_once($ims->conf["rootpath"].DS."config/site.php");
        include_once($ims->conf["rootpath"].DS."config".DS."xtemplate.class.php");
        $ims->site = new Site($this);
        if(!isset($ims->setting['gallery'])){
            $ims->site_func->setting ('gallery');
        }

        $output = array(
            'num' => 0,
            'html' => '',
            'filter_group' => ''
        );

        $where = '';
        $num_list = $ims->setting['gallery']['num_list'];
        $start = isset($ims->post['num_cur']) ? $ims->post['num_cur'] : 0;
        $keyword = isset($ims->post['keyword']) ? $ims->post['keyword'] : '';
        $group_id = isset($ims->post['group_id']) ? $ims->post['group_id'] : 0;
        $product_id = isset($ims->post['product_id']) ? $ims->post['product_id'] : 0;
        $design_id = isset($ims->post['design_id']) ? $ims->post['design_id'] : 0;

        $clear_all = '<span class="clear_all">'.$ims->lang['gallery']['clear_all'].'</span>';
        if($group_id){
            $where .= ' and group_id IN('.$group_id.')';
            $list_group = $ims->db->load_item_arr('gallery_group', 'is_show = 1 and lang = "'.$ims->conf['lang_cur'].'" and group_id IN ('.$group_id.')', 'group_id, title');
            if($list_group){
                foreach ($list_group as $item){
                    $output['filter_group'] .= '<span data-filter="'.$item['group_id'].'" class="image_type">'.$item['title'].'</span>';
                }
            }
        }
        if($product_id){
            $where .= ' and find_in_set('.$product_id.', product_id)';
        }

        if($design_id){
            $list_brand = $ims->db->load_item_arr('product_brand', 'is_show = 1 and lang = "'.$ims->conf['lang_cur'].'" and brand_id IN('.$design_id.') order by show_order desc, date_create asc', 'title, brand_id');
            foreach ($list_brand as $item){
                $output['filter_group'] .= '<span data-filter="'.$item['brand_id'].'" class="image_designer">'.$item['title'].'</span>';
            }
            $list_product = $ims->db->load_item_arr('product', 'is_show = 1 and lang = "'.$ims->conf['lang_cur'].'" and brand_id IN('.$design_id.')', 'item_id');
            if($list_product){
                $arr_product = array();
                foreach ($list_product as $item){
                    $arr_product[] = 'find_in_set('.$item['item_id'].',product_id)';
                }
                $arr_product = implode(' OR ', $arr_product);
                $where .= ' and ('.$arr_product.')';
            }
        }
        if($output['filter_group']){
            $output['filter_group'] = $clear_all.$output['filter_group'];
        }
        if($keyword != ''){
            $arr_key = explode(' ', $keyword);
            $arr_tmp = array();
            foreach ($arr_key as $value) {
                $value = trim($value);
                if (!empty($value)) {
                    $arr_tmp['title'][] = "title LIKE '%".$value."%'";
                    $arr_tmp['tag_list'][] = "tag_list LIKE '%".$value."%'";
                }
            }
            if (count($arr_tmp) > 0) {
                foreach ($arr_tmp as $k => $v) {
                    if (count($v) > 0) {
                        $arr_tmp[$k] = "(" . implode(" AND ", $v) . ")";
                    } else {
                        unset($arr_tmp[$k]);
                    }
                }
                $where .= " AND (".implode(" OR ", $arr_tmp).")";
            }
        }

        $arr_in = array(
            'where' => $where.' order by show_order desc, date_create asc limit '.$start.','.$num_list,
            'paginate' => 0,
            'temp' => 'list_item_ajax',
            'ajax' => 1
        );

        $output['total'] = $ims->db->do_get_num("gallery", 'is_show = 1 and lang = "'.$ims->conf['lang_cur'].'"' . $where);
        $result_total = $ims->db->do_get_num("gallery", 'is_show = 1 and lang = "'.$ims->conf['lang_cur'].'"' . $arr_in['where']);
        if(($start + $result_total) == $output['total']){
            $output['num'] = 0;
        }else{
            $output['num'] = $start + $result_total;
        }
        $output['html'] = $ims->call->mFunc('gallery', 'html_list_item', array($arr_in));

        return json_encode($output);
    }
    // End class
    function do_load_detail_image(){
        global $ims;
        $output = array();

        include_once($ims->conf["rootpath"].DS."config/site.php");
        $dir_view = $ims->func->dirModules('gallery', 'views', 'path');
        include_once($ims->conf["rootpath"].DS."config".DS."xtemplate.class.php");
        $ims->temp_func = new XTemplate($dir_view."func.tpl");
        $ims->temp_func->assign('LANG', $ims->lang);
        $ims->site = new Site($this);

        $where = '';
        $post = $ims->func->if_isset($ims->post['info']);
        $input = $ims->func->unserialize_array($post);

        if($input['group']){
            $where .= ' and group_id IN('.$input['group'].')';
        }
        if($input['product_item']){
            $where .= ' and find_in_set('.$input['product_item'].', product_id)';
        }
        if($input['design']){
            $list_product = $ims->db->load_item_arr('product', 'is_show = 1 and lang = "'.$ims->conf['lang_cur'].'" and brand_id IN('.$input['design'].')', 'item_id');
            if($list_product){
                $arr_product = array();
                foreach ($list_product as $item){
                    $arr_product[] = 'find_in_set('.$item['item_id'].',product_id)';
                }
                $arr_product = implode(' OR ', $arr_product);
                $where .= ' and ('.$arr_product.')';
            }
        }
        if($input['keyword']){
            $arr_key = explode(' ', $input['keyword']);
            $arr_tmp = array();
            foreach ($arr_key as $value) {
                $value = trim($value);
                if (!empty($value)) {
                    $arr_tmp['title'][] = "title LIKE '%".$value."%'";
                    $arr_tmp['tag_list'][] = "tag_list LIKE '%".$value."%'";
                }
            }
            if (count($arr_tmp) > 0) {
                foreach ($arr_tmp as $k => $v) {
                    if (count($v) > 0) {
                        $arr_tmp[$k] = "(" . implode(" AND ", $v) . ")";
                    } else {
                        unset($arr_tmp[$k]);
                    }
                }
                $where .= " AND (".implode(" OR ", $arr_tmp).")";
            }
        }
        $detail = $ims->db->load_row('gallery', 'is_show = 1 and lang = "'.$ims->conf['lang_cur'].'" and item_id = '.$input['item']);
        $detail['picture_view'] = $ims->func->get_src_mod($detail['picture'], 992, 992, 1, 0);
        $detail['content_download'] = ($detail['picture_low'] != '') ? '<li><a href="'.$ims->func->get_src_mod($detail['picture_low']).'" download><p>'.$ims->lang['gallery']['low_resolution'].'</p>'.$ims->site->do_info_pic($detail['picture_low']).'</a></li>' : '';
        $detail['content_download'] .= ($detail['picture_medium'] != '') ? '<li><a href="'.$ims->func->get_src_mod($detail['picture_medium']).'" download><p>'.$ims->lang['gallery']['medium_resolution'].'</p>'.$ims->site->do_info_pic($detail['picture_medium']).'</a></li>' : '';
        $detail['content_download'] .= ($detail['picture'] != '') ? '<li><a href="'.$ims->func->get_src_mod($detail['picture']).'" download><p>'.$ims->lang['gallery']['high_resolution'].'</p>'.$ims->site->do_info_pic($detail['picture']).'</a></li>' : '';
        $detail['file_name'] = ($detail['file_name'] != '') ? '<div class="name_file">'.$detail['file_name'].'</div>' : '';
        if($detail['tag_list'] != ''){
            $tag_list = explode(',', $detail['tag_list']);
            foreach ($tag_list as $item){
                $tag = array(
                    'title' => $item,
                    'link' => $ims->site_func->get_link('gallery').'/?keyword='.$item
                );
                $ims->temp_func->assign('tag', $tag);
                $ims->temp_func->parse('detail_image.tag.item');
                $ims->temp_func->parse('detail_image.tag_mobile.item');
            }
            $ims->temp_func->parse('detail_image.tag');
            $ims->temp_func->parse('detail_image.tag_mobile');
        }
        $list_product = $ims->db->load_item_arr('product', ' is_show = 1 and lang = "'.$ims->conf['lang_cur'].'" and item_id IN('.$detail['product_id'].') order by show_order desc, date_create asc', 'title, picture, picture1, brand_id, friendly_link');
        if($list_product){
            foreach ($list_product as $product){
                $product['picture1'] = ($product['picture1'] != '') ? $product['picture1'] : $product['picture'];
                $product['picture'] = $ims->func->get_src_mod($product['picture'], 600, 800, 1, 0);
                $product['picture1'] = $ims->func->get_src_mod($product['picture1'], 600, 800, 1, 0);
                $product['designer'] = ($product['brand_id'] != '') ? '<div class="designer">'.$ims->lang['gallery']['design_by'].' '.$ims->db->load_item('product_brand', 'is_show = 1 and lang = "'.$ims->conf['lang_cur'].'" and brand_id = '.$product['brand_id'], 'title').'</div>' : '';
                $product['link'] = $ims->func->get_link($product['friendly_link'],'');
                $ims->temp_func->assign('product', $product);
                $ims->temp_func->parse('detail_image.product.item');
            }
            $ims->temp_func->parse('detail_image.product');
        }
        $prev = $ims->db->load_item('gallery', 'is_show = 1 and lang = "'.$ims->conf['lang_cur'].'" and item_id < '.$input['item'].$where.' order by item_id desc', 'item_id');
        if($prev){
            $detail['item_prev'] = $prev;
            $detail['show_prev'] = 'show';
        }else{
            $detail['show_prev'] = 'hide';
        }
        $next = $ims->db->load_item('gallery', 'is_show = 1 and lang = "'.$ims->conf['lang_cur'].'" and item_id > '.$input['item'].$where.' order by item_id asc', 'item_id');
        if($next){
            $detail['item_next'] = $next;
            $detail['show_next'] = 'show';
        }else{
            $detail['show_next'] = 'hide';
        }
        $ims->temp_func->assign('data', $detail);
        $ims->temp_func->parse('detail_image');
        $output['html'] = $ims->temp_func->text('detail_image');
        return json_encode($output);
    }
}
?>