<?php
if (!defined('IN_ims')) { die('Access denied'); }
$nts = new sMain();

class sMain {

    var $modules = "search";
    var $action  = "search";
    var $sub     = "manage";
    var $dbtable_search = array(
        // 'about' => array(
        //     'module' => 'about',
        //     'link_type' => 'detail',
        //     'dbtable_id' => 'item_id'
        // ),
        // 'page' => array(
        //     'module' => 'page',
        //     'link_type' => 'detail',
        //     'dbtable_id' => 'item_id'
        // ),
        // 'news' => array(
        //     'module' => 'news',
        //     'link_type' => 'detail',
        //     'dbtable_id' => 'item_id'
        // ),
        // 'news_group' => array(
        //     'module' => 'news',
        //     'link_type' => 'group',
        //     'dbtable_id' => 'group_id'
        // ),
        // 'service' => array(
        //     'module' => 'service',
        //     'link_type' => 'detail',
        //     'dbtable_id' => 'item_id'
        // ),
        // 'project' => array(
        //     'module' => 'project',
        //     'link_type' => 'detail',
        //     'dbtable_id' => 'item_id'
        // ),
//        'product' => array(
//            'module' => 'product',
//            'link_type' => 'detail',
//            'dbtable_id' => 'item_id'
//        ),
        'event' => array(
            'module' => 'event',
            'link_type' => 'detail',
            'dbtable_id' => 'item_id'
        )
    );

    function __construct() {
        global $ims;

        $arrLoad = array(
            'modules'        => $this->modules,
            'action'         => $this->action,
            'template'       => $this->modules,
            'css'  	 		 => $this->modules,
            'use_navigation' => 0, // Sử dụng navigation
            'required_login' => 0, // Bắt buộc đăng nhập
        );
        $ims->func->loadTemplate($arrLoad);


        $data = array();
        $data['content'] = $this->list_search();
        $ims->conf['container_layout'] = 'full';
        $ims->temp_act->assign('data', $data);
        $ims->temp_act->parse("main");
        $ims->output .= $ims->temp_act->text("main");
    }

    //-----------list_search
    function list_search() {
        global $ims;

        $output = '';

        $ext = '';
        $keyword = (isset($ims->input['keyword'])) ? trim($ims->input['keyword']) : '';
        $ext = '&keyword=' . $keyword;
        //$text_search = $ims->func->get_text_search ($str);
        $arr_key = explode(' ', $keyword);
        $where = $order = '';
        $arr_tmp = array();
        foreach ($arr_key as $value) {
            $value = trim($value);
            if (!empty($value)) {
                // $arr_tmp['title_search'][] = "title_search like '%" . $value . "%'";
                // $arr_tmp['friendly_link'][] = "friendly_link like '%" . $value . "%'";
                // $arr_tmp['content'][] = "content like '%" . $value . "%'";
                $arr_tmp['title'][] = "title LIKE '%".$value."%'";
                $order .= "(title='".$value."') DESC, ";
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
        }
        if (count($arr_tmp) > 0) {
            $where .= " AND (".implode(" OR ", $arr_tmp).")";
        }

        $arr_kq = array();
        // print_arr($order);
        if($where != ''){
            foreach ($this->dbtable_search as $dbtable => $v) {
                $sql = "SELECT ".$v['dbtable_id'].", picture, title, price, price_buy, price_promotion, friendly_link, date_update  from ".$dbtable."  WHERE lang='".$ims->conf['lang_cur']."' AND is_show=1 and combo_id = 0 ".$where." ORDER BY CASE WHEN instr(title, '".$keyword."')=0 THEN 1 ELSE 0 END , ".$order." length(title), show_order DESC, date_create DESC limit 0,10";
                // echo $sql;die;
                $result = $ims->db->query($sql);
                while ($row = $ims->db->fetch_row($result)) {
                    $link = '#';
                    if ($v['link_type'] == 'detail') {
                       $link = $ims->site_func->get_link($v['module'], '', $row["friendly_link"]);
                    } elseif ($v['link_type'] == 'group') {
                       $link = $ims->site_func->get_link($v['module'], $row["friendly_link"]);
                    }
                    $arr_kq[$row['item_id']] = array(
                        // 'is_mod' => $v['module'],
                        'link' => $link,
                        'title' => $row['title'],
                        'picture' => $row['picture'],
                        'price' => $row['price'],
                        'price_buy' => $row['price_buy'],
                        'price_promotion' => $row['price_promotion'],
                        // 'short' => $ims->func->short($row['content'], 500),
                        // 'date_update' => date('d/m/Y', $row['date_update'])
                    );
                }
            }
        }
        
        // print_arr($arr_kq);
        //die();
        $data = array();
        if(count($arr_kq)>0){            
            foreach ($arr_kq as $k => $v) {
                $v['title'] = $ims->func->input_editor_decode($v['title']);                     
                $v['picture'] = $ims->func->get_src_mod($v['picture'],50,50,1,1,array('fix_max' => '1'));
                if($v['price_buy']!=0 && $v['price_buy'] <= $v['price']){
                    $v['price'] = $v['price_buy'];
                }
                if($v['price_promotion']!=0 && $v['price_promotion'] <= $v['price_buy']){
                    $v['price'] = $v['price_promotion'];
                }
                $v['price'] = $ims->func->get_price_format($v['price']);
                array_push($data,$v);
                // $ims->temp_act->assign('row', $v);
                // $ims->temp_act->parse("list_item.row_item");
            }            
        }
        $output = json_encode($data);
        return $output;        
    }
    // End class
}

?>