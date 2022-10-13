<?php
if (!defined('IN_ims')) { die('Access denied'); }
$nts = new sMain();

class sMain {

    var $modules = "user";
    var $action  = "list_save";

    /**
        * Khởi tạo
        * Danh sách mua sau
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
    //-----------
    function do_manage() {
        global $ims;

        $data = array(
        	'class' => 'save_later',
            'id' => 'id="save_for_later"',
        );
        $info_user = $ims->data['user_cur'];
        if(isset($info_user['list_save']) && $info_user['list_save'] != ''){
        	$list_save = $ims->func->unserialize($info_user['list_save']);

            $arr_option = array();
            $arr_product = array();
            if (!empty($list_save)) {
                foreach ($list_save as $key => $value) {
                    $arr_option[] = $value["id"];
                    $arr_product[] = $value["item_id"];
                }
            }
            $cartProduct = $ims->load_data->data_table (
                'product', 
                'item_id', '*', 
                ' FIND_IN_SET(item_id, "'.@implode(',', $arr_product).'")>0 '.$ims->conf['where_lang']
            );
            $cartOption = $ims->load_data->data_table(
                'product_option',
                'id', '*',
                ' FIND_IN_SET(id, "'.@implode(',',$arr_option).'")>0 '.$ims->conf['where_lang']
            );

            foreach ($list_save as $key => $value) {
                $product = isset($cartProduct[$value["item_id"]]) ? $cartProduct[$value["item_id"]] : array();
                $option = isset($cartOption[$value["id"]]) ? $cartOption[$value["id"]] : array();
                $row['content'] = $this->mod_item($product, $option);
                $ims->temp_act->assign('row', $row);
                $ims->temp_act->parse("manage_product.row");
            }
        }else{
        	$row['text'] = $ims->lang['user']['empty_favorite'];
            $ims->temp_act->assign('row', $row);
            $ims->temp_act->parse("manage_product.empty");
        }
        $data["page_title"] = $ims->conf["meta_title"];
        $ims->temp_act->assign('data', $data);
        $ims->temp_act->parse("manage_product");
        return $ims->temp_act->text("manage_product");
    }

    function mod_item($row=array(), $option=array()){
        global $ims;

        $ims->temp_act->reset("mod_item_cart");
        $ims->func->load_language('product');
        if($row && $option){
            $row_op = $option;
            $row['link'] = $ims->site_func->get_link('product',$row['friendly_link']);
            $row['link_cart'] = $ims->site_func->get_link('product',$ims->setting['product']['ordering_cart_link']);
            $row['pic_w']     = 400;
            $row['pic_h']     = 460;
            $row['picture']   = $row_op['Picture']!=''?$row_op['Picture']:$row['picture'];
            $row['picture']   = $ims->func->get_src_mod($row["picture"],$row['pic_w'],$row['pic_h'],1,0,array());
            $row['price']     = $row_op['Price'];
            $row['price_buy'] = $row_op['PriceBuy'];
            $row['price_text'] = $ims->func->get_price_format($row['price']);
            $row['price_buy_text'] = $ims->func->get_price_format($row['price_buy']);
            if($row['price_buy'] < $row['price'] && $row['price']!=0){
                $row['percent_discount'] = number_format(100-($row['price_buy']/$row['price']*100),1,'.',''); 
                $ims->temp_act->assign('row', $row);
                $ims->temp_act->parse("mod_item_cart.discount");   
            }
            $row['arr_item'] = $ims->func->unserialize($row['arr_item']);
            foreach ($row['arr_item'] as $key => $value) {
                if($value['SelectName'] == 'Custom'){
                    $value['option_name'] = $value['CustomName'];
                }else{
                    $value['option_name'] = $ims->lang['product']['option_'.strtolower($value['SelectName'])];
                }
                if(isset($row_op['Option'.($key+1)])){
                    $value['option_value'] = $value['value'] = $row_op['Option'.($key+1)];
                    $value['name'] = 'option'.($key+1);
                    $ims->temp_act->assign('row', $value);
                    $ims->temp_act->parse("mod_item_cart.option");
                    $ims->temp_act->parse("mod_item_cart.ver");
                }
            }
            $row["item_id"] = $ims->func->base64_encode($row['item_id']);
            $ims->temp_act->assign('LANG', $ims->lang);
            $ims->temp_act->assign('row', $row);
            $ims->temp_act->parse("mod_item_cart");
        }        
        return $ims->temp_act->text("mod_item_cart");
    }   
    // End class
}
?>