<?php

/*================================================================================*\
Name code : view.php
Copyright © 2013 by Tran Thanh Hiep
@version : 1.0
@date upgrade : 03/02/2013 by Tran Thanh Hiep
\*================================================================================*/

if (! defined('IN_ims')) {
  die('Access denied');
}
$nts = new sMain();
use MatthiasMullie\Minify;
class sMain
{
	var $modules = "product";
	var $action = "embed";
	var $sub = "manage";
	
	/**
	* function __construct ()
	* Khoi tao 
	**/
	function __construct ()
	{
		global $ims;
		
	 	$arrLoad = array(
        'modules'        => $this->modules,
        'action'         => $this->action,
        'template'       => $this->action,
        // 'js'             => $this->modules,
        // 'css'            => $this->modules,
        'use_func'       => "", // Sử dụng func
        'use_navigation' => 0, // Sử dụng navigation
        'required_login' => 0, // Bắt buộc đăng nhập
    );
    $ims->func->loadTemplate($arrLoad);

    require($this->modules . "_func.php");
    $this->modFunc = new productFunc($this);
		
    require ($ims->conf["rootpath"]."library".DS."minify/autoload.php"); 

    $dir_assets  = $ims->func->dirModules($arrLoad['modules'], 'assets');
    $arrCss = array(
    	$ims->func->fileGetContent($dir_assets."css/".$this->action.'.css'),
    );    
    $dir_fonts = $ims->func->dirModules("global", "assets", "fonts");		
    $name_file  = $ims->resources_path."minify/".md5(serialize($arrCss))."_include_css.min.css";    
    if (file_exists($name_file) && $ims->conf['refresh'] == 0) {
        $style = str_replace('../images/use', $ims->conf['rooturl'].'resources/images/use', $ims->func->fileGetContent($name_file));
        $style = str_replace(array("../fonts"), $dir_fonts, $style);
        $ims->conf["embed_style"] = $style;
    }else{
    	require ($ims->conf["rootpath"]."library".DS."minify/autoload.php"); 
		  $sourcePath = $arrCss[0];
		  $minifier = new Minify\CSS($sourcePath);
		  $i=0;
		  foreach ($arrCss as $key => $file) {
		      if ($i>0) {
		          $minifier->add($file);
		      }
		      $i++;
		  }
		  $minifier->minify($name_file);		  
		  $style = str_replace('../../../../../resources/', $ims->conf['rooturl'].'resources/', $minifier->minify());
		  $style = str_replace(array("../fonts"), $dir_fonts, $style);
		  $ims->conf["embed_style"] = $style;
		}

		$data = array();
		$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";		
		$temp = parse_url($url);
		$link = $friendly_link = '';
		$product = array();
		// foreach ($temp['path'] as $key => $value) {
		$link = explode('/embed/', $temp['path']);				
		if(isset($link[1]) && trim($link[1])!=''){
			$link[1] = explode('/', $link[1]);								
			if(count($link[1])>1){				
				$data['content'] = $this->load_info($link[1]);				
			}else{												
				$check = $ims->db->load_row('user_deeplink',' short_code="'.$link[1][0].'" and is_show=1 ','type,link_source,item_id');				
				if($check){
					$data['content'] = $this->load_info(array_values($check),$link[1][0]);
				}
			}
			// $friendly_link = (str_replace('.html', '', $link[1]));
		}
		// if($friendly_link != ''){
		// 	$product = $ims->db->load_row('product','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and friendly_link="'.$friendly_link.'"');
		// 	if($product){
		// 		$arr_option = $ims->db->load_row('product_option','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and ProductId="'.$product['item_id'].'" group by ProductId order by show_order desc, date_create asc');
		// 		$product['price'] = $ims->func->get_price_format(isset($arr_option['Price'])?$arr_option['Price']:0);
		// 		$product['price_buy'] = $ims->func->get_price_format(isset($arr_option['PriceBuy'])?$arr_option['PriceBuy']:0);
		// 		$product['price_promotion'] = $ims->func->get_price_format(isset($arr_option['PricePromotion'])?$arr_option['PricePromotion']:0);
		// 		$product['link'] = $ims->site->get_link('product',$product['friendly_link']);				
		// 		$product['title'] = $ims->func->input_editor_decode($product['title']);
		// 		$product['picture'] = isset($arr_option['Picture'])?'<img src="'.$ims->func->get_src_mod($arr_option['Picture']).'" alt="'.$product['title'].'">':'';
		// 		$product['item_code'] = isset($arr_option['SKU'])?$arr_option['SKU']:'';
		// 		$ims->temp_act->assign('LANG', $ims->lang);
		// 		$ims->temp_act->assign('data', $product);
		// 		$ims->temp_act->parse("main");
		// 	}
		// }
		// print_arr($data);
		$ims->conf['cache_version'] = '1';
		$ims->conf['container_layout'] = 'full';
		$ims->temp_act->assign('LANG', $ims->lang);
		$ims->temp_act->assign('data', $data);		
		$ims->temp_act->parse("main");
		$ims->output .=  $ims->temp_act->text("main");
	}

	function load_info($info=array(),$code=''){
		global $ims;
		$output = '';		
		$arr_item = $item = array();		
		foreach ($info as $key => $value) {
			$row = array();
			if($info[0] == "group"){				
				if(count($info)>2){
					$arr_item = $ims->db->load_row_arr('product','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and group_id="'.$info[2].'" order by show_order desc, date_create asc limit 0,4');
				}else{
					$arr_item = $ims->db->load_row_arr('product','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and group_id=(SELECT group_id FROM product_group WHERE friendly_link="'.$info[1].'") order by show_order desc, date_create asc limit 0,4');
				}
				
			}elseif($info[0] == "detail"){
				if(count($info)>2){
					$item = $ims->db->load_row('product','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and item_id="'.$info[2].'"');
				}else{
					$item = $ims->db->load_row('product','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and friendly_link="'.$info[1].'"');
				}
			}
						
		}
		if(!empty($arr_item)){			
			$output = $this->html_list_item($arr_item,$code);
		}
		if(!empty($item)){
			$output = $this->mod_item($item,$code);
		}
		return $output;
	}

	function html_list_item($arr_item=array(),$code=''){
		global $ims;
		// print_arr($arr_item);
		if($arr_item){      
        $i=0;      
        foreach ($arr_item as $row) {
            $i++;
            
            // $row_op = isset($arr_product_option[$k])?$arr_product_option[$k]:array();
            // $order_by = ' order by date_create';
            // if($row['field_option'] != ''){
            //     $order_by = ' order by '.$row['field_option'].',date_create';
            // }
            $row['friendly_link'] = $code!=''?$row['friendly_link'].'?dl='.$code:$row['friendly_link'];
            // $row['price'] = $row['price_buy'] = 1;
            // $row_op = $ims->db->load_row('product_option','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and ProductId="'.$row['item_id'].'" '.$order_by,'Price,PriceBuy');
            // if($row_op){
            //     $row['price'] = $row_op['Price'];
            //     $row['price_buy'] = $row_op['PriceBuy'];                    
            // }
            
            $row['stt'] = $i;
            $row['pic_w'] = 300;
            $row['pic_h'] = 300;                
            $row['mod_item'] = $this->mod_item($row);
            // print_arr($row['mod_item']);
            $ims->temp_act->assign('row', $row);
            $ims->temp_act->parse("list_item.row");
        }
        $ims->temp_act->parse("list_item");
    }
    return $ims->temp_act->text("list_item");
	}

	function mod_item($row=array(),$code=''){
		global $ims;
		$temp = "mod_item";
		$ims->temp_act->reset("mod_item");
		$row['pic_w'] = 300;
    $row['pic_h'] = 300;
		$check_promotion = 0;

    $row['title'] = $ims->func->input_editor_decode($row['title']);
    $arr = $ims->site->check_favorite($row['item_id']);
    $row['class_favorite'] = isset($arr['class']) ? $arr['class'] : '';
    $row['added'] = isset($arr['added']) ? $arr['added'] : '';
//        if($row['percent_discount'] > 0){
//            $row['discount'] = '<div class="discount">-'.number_format($row['percent_discount'], 0).'%</div>';
//        }
    if($row['price'] > 0 && $row['price'] > $row['price_buy']){
        $row['discount'] = '<div class="discount">-'.number_format(($row['price'] - $row['price_buy'])/$row['price']*100, 0).'%</div>';
    }
    
    // check promotion  
//        if($row['price_promotion']!=0){
//            $row['price'] = $row['price_buy'];
//            $row['price_buy'] = $row['price_promotion'];
//            $ims->temp_act->assign('row', $row);
//            $ims->temp_act->parse($temp.".promo");
//        }

    if($row['price_buy'] < $row['price'] && $row['price']!=0){
        $row['price'] = number_format($row['price'], 0,',','.').'đ';
        $ims->temp_act->assign('price', $row['price']);
        $ims->temp_act->parse($temp.'.price');
    }    
    $row['price_buy'] = ($row['price_buy'] != 0) ? number_format($row['price_buy'],0,',','.').'đ' : $ims->lang['product']['no_price'];

    $row['link'] = $ims->func->get_link($row['friendly_link'], '');
    $row["picture"] = $ims->func->get_src_mod($row["picture"], $row['pic_w'], $row['pic_h'], 1, 0);

    // ------------------- Đánh giá -------------------
//        $rate = $row['rate'];
//        if($row['num_rate'] != $rate['average']){
//            $ims->db->do_update("product", array('num_rate'=>$rate['average']), " item_id='".$row['item_id']."'");
//        }
    $rate = $ims->site->rate_average($row['item_id']);
    if(!empty($rate)){
        if($rate['num'] > 0){
            if($rate['average'] > 0){
                $star = $rate['average'];
                $int = (int) $star;
                $decimal = $star - $int;
                for ($i=0; $i < 5; $i++) {
                    if($star >= 1){
                        $row['average'] = '<i class="fas fa-star" title ="'.$rate['average'].' sao"></i>';
                        $star--;
                    }else{
                        if($decimal>=0.5 && $star>=0.5){
                            $row['average'] = '<i class="fas fa-star-half-alt" title ="'.$rate['average'].' sao"></i>';
                            $star -= 0.5;
                        }else{
                            $row['average'] = '<i class="fal fa-star" title ="'.$rate['average'].' sao"></i>';
                        }
                    }
                    $ims->temp_act->assign('row', $row);
                    // $ims->temp_act->parse($temp.".rate.star");
                    $ims->temp_act->parse($temp.".rate_view.star");
                }
            }
//                $row['num_rate'] = "<span style='line-height: 100%;'>(".$ims->site_func->get_lang('num_rate','global',array("{num_rate}"=>$rate['num'])).")</span>";
            $row['num_rate'] = "<span>(".$rate['num'].")</span>";
        }
        else{
            for ($i=0; $i < 5; $i++) {
                $row['average'] = "<i class='fal fa-star' title ='".$rate['average']." sao'></i>";
                $ims->temp_act->assign('row', $row);
                // $ims->temp_act->parse($temp.".rate.star");
                $ims->temp_act->parse($temp.".rate_view.star");
            }
            $row['num_rate'] = "<span>(0)</span>";
        }
        $ims->temp_act->assign('row', $row);
        // $ims->temp_act->parse($temp.".rate");
        $ims->temp_act->parse($temp.".rate_view");
    }
    // ------------------- Đánh giá -------------------

    $ims->temp_act->reset($temp);
    $row["link_cart"] = "";
    $row["type_btn"]  = "submit";
    $check_stock = 0;
    if($check_stock == 1){
        $row["link_cart"] = '';
        $row["type_btn"] = "button";
        $row['id_disable'] = '_dis';
        $row['btn_add_cart'] = $row['item_status'] = $ims->lang['product']['status_stock0'];
        $row['btn_order'] = $ims->lang['global']['price_empty'];
    }else{
        $row['btn_order'] = $ims->lang['product']['btn_add_cart'];
        $row['btn_add_cart'] = $ims->lang['product']['btn_add_cart_now'];            
        $row['item_status'] = $ims->lang['product']['status_stock1'];
    }
    //The item loaded and no load again    
    $row['item_id'] = $ims->func->base64_encode($row['item_id']);
    $row['loading'] = $ims->dir_images."spin.svg";
    $row['brand'] = $this->get_brand_name($row['brand_id'], 'link');
//        $row['trogia'] = ($row['is_new'] == 1) ? '<div class="trogia">'.$ims->func->input_editor_decode($ims->lang['product']['trogia']).'</div>' : '';
    $icon_trogia = ($row['icon_price_sponsorship'] != '') ? $ims->func->get_src_mod($row['icon_price_sponsorship']) : $ims->conf['rooturl'].'resources/images/use/trogia.png';
    $row['trogia'] = ($row['price_sponsorship'] != '') ? '<div class="trogia"><div class="img"><img src="'.$icon_trogia.'" alt=""></div>'.$ims->func->input_editor_decode($row['price_sponsorship']).'</div>' : '';

		$ims->temp_act->assign('row', $row);
    $ims->temp_act->parse("mod_item");
    return $ims->temp_act->text("mod_item");
	}

	//-----------get_brand_name
  function get_brand_name($brand_id, $type = 'none') {
      global $ims;
      $output = '';
      $brand = $ims->db->load_row($this->modules . "_brand", "brand_id='".$brand_id."'".$ims->conf["where_lang"]);
      if (!empty($brand)) {
          switch ($type) {
              case "link":
                  $brand['friendly_link'] = '?brand='.$brand['brand_id'];
                  $link = $ims->site_func->get_link($this->modules).$brand['friendly_link'];
                  $output = '<a href="' . $link . '">' . $brand['title'] . '</a>';
                  break;
              default:
                  $output = $brand['title'];
                  break;
          }
      }
      return $output;
  }
  // end class
}
?>