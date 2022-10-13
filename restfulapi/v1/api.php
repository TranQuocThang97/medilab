<?php
require_once 'restful_api.php';
use \Firebase\JWT\JWT;

class api extends restful_api {
	function __construct(){
		parent::__construct();
	}

	function getConfigApp(){
		global $ims;

		if ($ims->method == 'GET'){
			$data = array();
			$arr = $ims->db->load_row_arr('api_configapp', ' option_key!="" ');
			if (!empty($arr)) {
				foreach ($arr as $key => $value) {
					if ($value['type'] == 'array') {
						$value['option_value'] = explode(',', $value['option_value']);
					}
					$data[$value['option_key']] = isset($value['option_value']) ? $value['option_value'] : '';
				}
			}
			$ims->site_func->setting('product');
			$data['vat'] = array(
				'show_vat' => !empty($ims->setting['product']['show_vat'])?true:false,
				'text_vat' => $ims->func->input_editor_decode($ims->setting['product']['text_vat']),
			);	
			$array = array(
	    		"code" => 200,
	    		"message" => $ims->lang['api']['success'],
	    		"data" => $data,	    		
	    	);
			$this->response(200, $array);

		}else{
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}

	// lấy token app 
	function getToken(){
		global $ims;
		
		if ($ims->method == 'POST'){
			$username = $ims->func->if_isset($ims->post['username']);
			$password = $ims->func->if_isset($ims->post['password']);
			$connect = $ims->db->load_row('api_account', ' username="'.$username.'" AND password="'.$password.'" ');
			if (!empty($connect)) {
				if ($connect['is_status'] == 0) {
					$this->response(400, "", 400 , $ims->lang['api']['error_getToken_3_1']);
				}
				if ($connect['is_status'] == 2) {
					$this->response(400, "", 400 , $ims->lang['api']['error_getToken_3_2']);
				}
				$now_seconds = time();
				$payload = array (
			     	"iss" => $connect['item_id'],
			    	"iat" => $now_seconds,
			    	// "exp" => $now_seconds + (60*60*15),  // Thời gian tồn tại 15 phút
			    	"exp" => $now_seconds + (60*60*24*30),  // Thời gian tồn tại 1 tháng
			    );
			    $jwt = JWT::encode($payload, $this->private_key, "HS256");
			    $ims->db->query("DELETE FROM jwt_token WHERE date_expired<'".time()."' ");
			    $arr_ins 				 = array();
			    $arr_ins['account_id'] 	 = $connect['item_id'];
			    $arr_ins['token'] 		 = $jwt;
			    $arr_ins['date_expired'] = $now_seconds+(60*60*24*30);
			    $arr_ins['date_create']  = $now_seconds;
			    $arr_ins['date_update']  = $now_seconds;
			    $ims->db->do_insert("api_token", $arr_ins);
			    $array = array (
		    		"code" => 200,
	    			"message" => $ims->lang['api']['success'],
		    		"exp" => $payload['exp'],
		    		"token" => $jwt,
		    	);
				$this->response(200, $array);
			}
		}else{
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}

	// lấy trang tĩnh theo item_id 
	function getPageByID() {
		global $ims;

		if ($ims->method == 'GET'){
			$item_id = $ims->func->if_isset($ims->get['item_id']);
			$data = $ims->db->load_row('page', 'is_show=1 AND lang="'.$ims->conf['lang_cur'].'" AND item_id="'.$item_id.'"');
			if (!empty($data)) {
				$data_out = array(
					'title' => $data['title'],
					'content' => $ims->func->input_editor_decode($data['content'])
				);
				$array = array(
					"code" => 200,
		    		"message" => $ims->lang['api']['success'],
					'data' => $data_out
	        	);
				$this->response(200, $array);
			}else{
				$array = array(
					"code" => 200,
		    		"message" => $ims->lang['api']['success'],
					'data' => array()
	        	);
				$this->response(200, $array);
			}
		}else{
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}


	// lấy Banner theo item_id 
	function getBannerByID(){
		global $ims;

		if ($ims->method == 'GET'){
			$banner_id = $ims->func->if_isset($ims->get['banner_id']);
			if ($banner_id == '') {
				$this->response(400, "", 400, $ims->lang['api']['error_data']);
			}
			$this->data_banner();
			$this->data_banner_group();
			$banner_arr = array();
			if (strpos($banner_id, ',') !== false) {
				$banner_arr = explode(',', $banner_id);
			}else{
				$banner_arr[] = $banner_id;
			}
			$data_out = array();
			if (!empty($banner_arr)) {
				foreach ($banner_arr as $key => $value) {
					$data_out[] = $this->getBanner($value);
				}
			}
			$array = array(
				"code" => 200,
	    		"message" => $ims->lang['api']['success'],
				'data' => $data_out
        	);
			$this->response(200, $array);
		}else{
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}


	// lấy tất cả sản phẩm
	function getProduct(){
		global $ims;

		if ($ims->method == 'GET'){

			$this->setting('product');
			$token_login    = $ims->func->if_isset($ims->get['user']);
			$p 		        = $ims->func->if_isset($ims->get['p'], 1);
			$numshow  	    = $ims->func->if_isset($ims->get['numshow'], 0); // Số sp hiển thị
			$item_id        = $ims->func->if_isset($ims->get['item_id'], 0); 
			$group_id 	    = $ims->func->if_isset($ims->get['group_id'], 0); // Lấy sp theo nhóm
			$keyword  	    = $ims->func->if_isset($ims->get['keyword']); // Tìm theo từ khóa
			$type  	  	    = $ims->func->if_isset($ims->get['type']); // Lấy theo loại sp
			$sort 		    = $ims->func->if_isset($ims->get['sort']); // Sắp xếp sp theo ?
			$price_min 	    = $ims->func->if_isset($ims->get['price_min'], 0); // giá tối thiểu
			$price_max 	    = $ims->func->if_isset($ims->get['price_max'], 0); // giá tối đa
			$average_rating = $ims->func->if_isset($ims->get['average_rating']); // Lấy theo đánh giá

           	$order_by = '';
			$n = $ims->setting['product']['num_list'];
			if ($numshow>0) {
				$n = $numshow;
			}
			if ($numshow>100) {
				$n = $ims->setting['product']['num_list'];
			}
			$where = '';
	        if ($item_id>0) {
	        	$where = ' item_id="'.$item_id.'" AND ';
	        }

	        // Lọc theo khoảng giá
	        if ($price_min==0 && $price_max>0) {
	        	$where .= ' (price_buy <= '.$price_max.') AND ';
	        }elseif ($price_max==0 && $price_min==0) {

	        }elseif ($price_max==0 && $price_min>0) {
	        	$where .= ' (price_buy >= '.$price_min.') AND ';
	        }elseif ($price_min>0 && $price_max>0) {
	        	$where .= ' (price_buy >= '.$price_min.' AND price_buy <= '.$price_max.') AND ';
	        }

	        if ($keyword !='') {
	        	$arr_tmp = array();
				$arr_key = explode(' ', $keyword);
		        foreach ($arr_key as $value) {
		            $value = trim($value);
		            if (!empty($value)) {
		                $arr_tmp[] = "title LIKE '%" . $value . "%'";
		            }
		        }
		        if (count($arr_tmp) > 0) {         
		            $where .= "  (" . implode(" AND ", $arr_tmp) . ") AND ";
		        }
	        }
	        if ($group_id>0) {
	        	$where .= ' ( find_in_set("'.$group_id.'", group_nav)>0 OR group_id="'.$group_id.'" ) AND ';
	        }
	        if($sort){
	            if($sort == 'price-desc'){
	                $order_by = " price_buy DESC, ";
	            }
	            elseif($sort == 'price-asc'){
	                $order_by = " price_buy ASC, ";
	            }
	            elseif($sort == 'title-asc'){
	                $order_by = " title ASC, ";
	            }            
	            elseif($sort == 'title-desc'){
	                $order_by = " title DESC, ";
	            }
	            elseif($sort == 'new'){
	                $order_by = " date_create DESC, ";
	            }
	            elseif($sort == 'good_review'){
	                $order_by = " average_rating DESC, num_rate DESC, ";
	            }
	            elseif($sort == 'discount'){
	                $order_by = " percent_discount DESC, ";
	            }
	            elseif($sort == 'selling'){
	                $order_by = " quantity_sold DESC, ";
	            }
	        } 

	        // Đánh giá
	        if ($average_rating>0) {
		        $where .= "  (average_rating>=".$average_rating.") AND ";
	        }

	        $infoUser = $ims->db->load_row('user', ' FIND_IN_SET("'.$token_login.'", token_login) ');
	        $arr_favorite = array();
			if (!empty($infoUser)) {
				$product_favorite = $ims->db->load_row_arr('shared_favorite', ' type="product" AND user_id="'.$infoUser['user_id'].'" AND is_show=1 ');
				if (!empty($product_favorite)) {
					foreach ($product_favorite as $k => $v) {
						$arr_favorite[$v['type_id']] = $v;
					}
				}
			}

			$arr_where = array();
			if (strpos($type, ',') !== false) {
	        	$arr_where = explode(',', $type);
	        }

	        if (!empty($arr_where)) {
	        	$arrtmp = array();
	        	foreach ($arr_where as $key_where => $value_where) {
	        		$where_new = $where;
	        		$value_where = trim($value_where);
	        		if ($value_where == 'is_focus') {
			        	// Sản phẩm nổi bật
			        	$where_new .= ' ( is_focus=1 ) AND ';

			        }elseif ($value_where == 'is_topsell') {
			        	// Sản phẩm bán chạy
			        	$where_new .= ' ( is_topsell=1 ) AND ';

			        }elseif ($value_where == 'is_shock_today') {
			        	// Sản phẩm giá sock hôm nay
			        	$where_new .= ' percent_discount>0 AND ';
			        	$order_by = " percent_discount DESC, ";

			        }elseif ($value_where == 'save_for_late') {
			        	// Sản phẩm mua sau
			        	if (!empty($infoUser)) {
							$arr_tmp = $ims->func->unserialize($infoUser['list_save']);
							if (!empty($arr_tmp)) {
								$tmp = array();
								foreach ($arr_tmp as $k => $v) {
									$tmp[] = $v['item_id'];
								}
			        			$where_new .= ' ( FIND_IN_SET(item_id, "'.implode(',', $tmp).'") ) AND ';
							}else{
			        			$where_new .= ' ( FIND_IN_SET(item_id, "EMPTY") ) AND ';
							}
			        	}
			        	// else{
			        	// 	$where_new .= ' ( FIND_IN_SET(item_id, "EMPTY") ) AND ';
			        	// }
			        }elseif ($value_where == 'list_viewed') {
			        	// Sản phẩm đã xem
			        	if (!empty($infoUser)) {
							$arr_tmp = $ims->func->unserialize($infoUser['list_watched']);
							if (!empty($arr_tmp)) {
								$tmp = array();
								foreach ($arr_tmp as $k => $v) {
									$tmp[] = $v['id'];
								}
			        			$where_new .= ' ( FIND_IN_SET(item_id, "'.implode(',', $tmp).'") ) AND ';
							}
			        	}
			        	// else{
			        	// 	$where_new .= ' ( FIND_IN_SET(item_id, "EMPTY") ) AND ';
			        	// }
			        }elseif ($value_where == 'list_for_you') {
			        	// Sản phẩm dành riêng cho bạn
			        	if (!empty($infoUser)) {
			        		$where_new .= ' ( FIND_IN_SET(item_id, "EMPTY") ) AND ';
			        	}else{
			        		$where_new .= ' ( FIND_IN_SET(item_id, "EMPTY") ) AND ';
			        	}
			        }else{
			        	$where_new .= ' ( is_focus=1000 ) AND ';
			        }
					$res_num = $ims->db->query("SELECT item_id from product where ".$where_new." is_show=1 AND lang='".$ims->conf['lang_cur']."'");
			        $num_total = $ims->db->num_rows($res_num);
			        $num_items = ceil($num_total / $n);
			        if ($p > $num_items)
			            $p = $num_items;
			        if ($p < 1)
			            $p = 1;
			        $start = ($p - 1) * $n;

					$arr = $ims->db->load_item_arr(
						'product',
						$where_new .'is_show=1 AND lang="'.$ims->conf['lang_cur'].'" ORDER BY '.$order_by.' show_order DESC, date_update DESC LIMIT '.$start.', '.$n.'', 
						'item_id, group_id, combo_id, picture, title, price, price_buy, price_promotion, percent_discount, num_view, average_rating, arr_item, quantity_sold, field_option, friendly_link'
					);
					if (!empty($arr)) {
						foreach ($arr as $key => $value) {
							$arr[$key]['title'] = $ims->func->input_editor_decode($value['title']);
                        	$arr[$key]['friendly_link'] = $this->get_link_lang ($ims->conf['lang_cur'] ,'product', $value['friendly_link']);
				        	$arr[$key]['rating'] 	  = $value['average_rating'];
							$arr[$key]['is_favorite'] = isset($arr_favorite[$value['item_id']]) ? 1 : 0;
							$arr[$key]['picture'] 	  = $ims->func->get_src_mod($value['picture']);
							$arr[$key]['thumbnail']   = $ims->func->get_src_mod($value['picture'], 40, 40 , 1, 1);
							if ($value['price_promotion']>0) {
								$value['price_buy'] = $value['price_promotion'];
							}
							$arr[$key]['is_combo'] = !empty($value['combo_id'])?true:false;
							unset($arr[$key]['combo_id']);
							unset($arr[$key]['price_promotion']);
							unset($arr[$key]['average_rating']);
							$arrtmp[$value_where][] = $arr[$key];
						}
					}else{
						$arrtmp[$value_where] = array();
					}
	        	}
	        	$array = array(
	        		"code" => 200,
	    			"message" => $ims->lang['api']['success'],
		    		'total' => $num_total,
		    		'total_page' => $num_items,
		    		'numshow' => $n,
		    		'page' => $p,
		    		'data' => $arrtmp,
		    	);
				$this->response(200, $array);
	        }else{
		        if ($type == 'is_focus') {
		        	// Sản phẩm nổi bật
		        	$where .= ' ( is_focus=1 ) AND ';

		        }elseif ($type == 'is_topsell') {
		        	// Sản phẩm bán chạy
		        	$where .= ' ( is_topsell=1 ) AND ';

		        }elseif ($type == 'is_shock_today') {
		        	// Sản phẩm giá sock hôm nay
		        	$where .= ' percent_discount>0 AND ';
		        	$order_by = " percent_discount DESC, ";

		        }elseif ($type == 'save_for_late') {
		        	// Sản phẩm mua sau
		        	if (!empty($infoUser)) {
						$arr_tmp = $ims->func->unserialize($infoUser['list_save']);
						if (!empty($arr_tmp)) {
							$tmp = array();
							foreach ($arr_tmp as $k => $v) {
								$tmp[] = $v['item_id'];
							}
		        			$where .= ' ( FIND_IN_SET(item_id, "'.implode(',', $tmp).'") ) AND ';
						}else{
		        			$where .= ' ( FIND_IN_SET(item_id, "EMPTY") ) AND ';
						}
		        	}
		        	// else{
		        	// 	$where .= ' ( FIND_IN_SET(item_id, "'.implode(',', $tmp).'") ) AND ';
		        	// }
		        }elseif ($type == 'list_viewed') {		        	
		        	// Sản phẩm đã xem
		        	if (!empty($infoUser)) {		        		
						$arr_tmp = $ims->func->unserialize($infoUser['list_watched']);						
						if (!empty($arr_tmp)) {
							$tmp = array();
							foreach ($arr_tmp as $k => $v) {
								$tmp[] = $v['id'];
							}
		        			$where .= ' ( FIND_IN_SET(item_id, "'.implode(',', $tmp).'") ) AND ';
		        			$order_by = ' FIELD(item_id,'.implode(',', $tmp).') DESC, ';
						}else{
		        			$where .= ' ( FIND_IN_SET(item_id, "EMPTY") ) AND ';
						}
		        	}
		        	// else{
		        	// 	$where .= ' ( FIND_IN_SET(item_id, "'.implode(',', $tmp).'") ) AND ';
		        	// }
		        }elseif ($type == 'list_for_you') {
		        	// Sản phẩm dành riêng cho bạn
		        	if (!empty($infoUser)) {
		        		$where .= ' ( FIND_IN_SET(item_id, "EMPTY") ) AND ';
		        	}else{
		        		$where .= ' ( FIND_IN_SET(item_id, "EMPTY") ) AND ';
		        	}
		        }
				$res_num = $ims->db->query("SELECT item_id from product where ".$where." is_show=1 AND lang='".$ims->conf['lang_cur']."'");
		        $num_total = $ims->db->num_rows($res_num);
		        $num_items = ceil($num_total / $n);
		        if ($p > $num_items)
		            $p = $num_items;
		        if ($p < 1)
		            $p = 1;
		        $start = ($p - 1) * $n;
		        	
				$arr = $ims->db->load_item_arr(
					'product',
					$where .'is_show=1 AND lang="'.$ims->conf['lang_cur'].'" ORDER BY '.$order_by.' show_order DESC, date_update DESC LIMIT '.$start.', '.$n.'', 
					'item_id, group_id, combo_id, picture, title, price, price_buy, price_promotion, percent_discount, num_view, average_rating, arr_item, quantity_sold, field_option, friendly_link'
				);

				if (!empty($arr)) {
					foreach ($arr as $key => $value) {
						$arr[$key]['title'] = $ims->func->input_editor_decode($value['title']);
						$arr[$key]['friendly_link'] = $this->get_link_lang ($ims->conf['lang_cur'] ,'product', $value['friendly_link']);
			        	$arr[$key]['rating'] 	  = $value['average_rating'];
						$arr[$key]['is_favorite'] = isset($arr_favorite[$value['item_id']]) ? 1 : 0;
						$arr[$key]['picture'] 	  = $ims->func->get_src_mod($value['picture']);
						$arr[$key]['thumbnail']   = $ims->func->get_src_mod($value['picture'], 40, 40 , 1, 1);
						if ($value['price_promotion']>0) {
							$value['price_buy'] = $value['price_promotion'];
						}
						$arr[$key]['is_combo'] = !empty($value['combo_id'])?true:false;
						// unset($arr[$key]['combo_id']);
						unset($arr[$key]['price_promotion']);
						unset($arr[$key]['average_rating']);
					}
				}
	        }

	        if ($item_id>0) {
	        	// Get detail	      
	        	$arr[0]['title'] = $ims->func->input_editor_decode($arr[0]['title']);  	;
		        $arr[0]['rating'] = $this->getRatingByProduct('product', $arr[0]['item_id'], 'all');
	        	$infoDetail = $ims->db->load_row('product_detail', 'product_id ="'.$arr[0]['item_id'].'"');

	        	if (!empty($infoDetail)) {
	        		$arr[0] = array_merge($arr[0], $infoDetail);
	        		$arr_picture = $arr[0]['arr_picture'];
		            $arr[0]['short'] =  $ims->func->input_editor_decode($arr[0]['short']);
		            $arr[0]['content'] =  $ims->func->input_editor_decode($arr[0]['content']);
	        		if ($arr_picture!='') {
						$arr_picture = unserialize($arr_picture);
						$arr_picture[] = $arr[0]['picture'];
			            foreach ($arr_picture as $k_pic => $v_pic) {
							$arr_picture[$k_pic] = $ims->func->get_src_mod($v_pic);
			            }
						$arr[0]['arr_picture'] = $arr_picture;
					}else{
						$arr[0]['arr_picture'] = array();
						$arr[0]['arr_picture'][] = $arr[0]['picture'];
					}
	        	}

	        	$where_nav = 'item_id!="'.$item_id.'" AND ';
	        	if ($arr[0]['group_id'] > 0) {
		            $where_nav .= " ( find_in_set('" . $arr[0]['group_id'] . "',group_nav)>0 
						OR find_in_set('" . $arr[0]['group_id'] . "',group_related)>0 
					) AND ";
		        }

				$arr_nav = $ims->db->load_item_arr(
					'product',
					$where_nav .'is_show=1 AND lang="'.$ims->conf['lang_cur'].'" ORDER BY show_order DESC, date_update DESC LIMIT 0,'.$ims->setting['product']["num_order_detail"].' ',
					'item_id, group_id, picture, title, price, price_buy, price_promotion, percent_discount, quantity_sold, num_view');
	        	if (!empty($arr_nav)) {
					foreach ($arr_nav as $k_nav => $v_nav) {
						$arr_nav[$k_nav]['title'] = $ims->func->input_editor_decode($v_nav['title']);
						if ($v_nav['price_promotion']>0) {
							$v_nav['price_buy'] = $v_nav['price_promotion'];
						}
						unset($arr_nav[$k_nav]['price_promotion']);
						$arr_nav[$k_nav]['picture']   = $ims->func->get_src_mod($v_nav['picture']);
						$arr_nav[$k_nav]['thumbnail'] = $ims->func->get_src_mod($v_nav['picture'], 40, 40 , 1, 1);
						$arr_nav[$k_nav]['rating']    = $this->getRatingByProduct('product', $v_nav['item_id'], 'average');
					}
				}

	        	// Get Option
				$data = array();
				$arr_item = $arr[0]['arr_item'];
				$ims->func->load_language('global');
				$ims->func->load_language('product');
	        	$option = array();
		        if($arr_item != ''){
		            $data['arr_item'] = $ims->func->unserialize($arr_item);               
		            foreach ($data['arr_item'] as $k => $row) {
		                if($row['SelectName'] == 'Custom'){
		                    $row['title'] = $row['CustomName'];                    
		                }else{
		                    $row['title'] = isset($ims->lang['product']['option_'.strtolower($row['SelectName'])])?$ims->lang['product']['option_'.strtolower($row['SelectName'])]:'';
		                }                
		                $option[$k]['id'] = 'option'.($k+1);
		                $option[$k]['title'] = $row['title'];
		                $option[$k]['group_id'] = strtolower($row['SelectName']);
		                $option[$k]['value'] = array();
		            }
		            $order_by = ' ORDER BY date_create';
		            if($arr[0]['field_option'] != ''){
		                $order_by = ' ORDER BY '.$arr[0]['field_option'].', date_create DESC';
		            }
		            $arr_option = $ims->db->load_row_arr('product_option','lang="'.$ims->conf['lang_cur'].'" AND is_show=1 AND ProductId="'.$arr[0]['item_id'].'" '.$order_by);

		            $arr_color = array();
			        $arr_color_tmp = $ims->db->load_item_arr('product_color','lang="'.$ims->conf['lang_cur'].'" and is_show=1','color_id,color,title');
			        foreach ($arr_color_tmp as $color) {
			            $color['name'] = $ims->func->vn_str_filter($color['title']);
			            $arr_color[strtolower($color['name'])] = $color;
			        }

		            if($arr_option){                   
		                // print_arr($arr_option);
		                $i=0;
		                foreach (($arr_option) as $k => $v) {
		                    $i++;
		                    if(count($arr_option)>0){
		                        if($v['Option1'] != ''){  
		                            $option[0]['value'][$v['Option1']]['title'] = $v['Option1'];                        
		                        	if (!empty($option[2]['group_id']) && $option[2]['group_id'] == 'color') {
		                        		$colorkey = $ims->func->vn_str_filter($v['Option3']);
		                            	$option[2]['value'][$v['Option3']]['colorkey'] = isset($arr_color[strtolower($colorkey)]) ? $arr_color[strtolower($colorkey)]['color'] : '';                        
		                        	}                     
		                            $option[0]['value'][$v['Option1']]['data'][] = $v['id'];                        
		                        }
		                        if($v['Option2'] != ''){
		                            $option[1]['value'][$v['Option2']]['title'] = $v['Option2'];                        
		                        	if (!empty($option[2]['group_id']) && $option[2]['group_id'] == 'color') {
		                        		$colorkey = $ims->func->vn_str_filter($v['Option3']);
		                            	$option[2]['value'][$v['Option3']]['colorkey'] = isset($arr_color[strtolower($colorkey)]) ? $arr_color[strtolower($colorkey)]['color'] : '';                        
		                        	}
		                            $option[1]['value'][$v['Option2']]['data'][] = $v['id'];
		                        }
		                        if($v['Option3'] != ''){
		                            $option[2]['value'][$v['Option3']]['title'] = $v['Option3'];                        
		                        	if (!empty($option[2]['group_id']) && $option[2]['group_id'] == 'color') {
		                        		$colorkey = $ims->func->vn_str_filter($v['Option3']);
		                            	$option[2]['value'][$v['Option3']]['colorkey'] = isset($arr_color[strtolower($colorkey)]) ? $arr_color[strtolower($colorkey)]['color'] : '';                        
		                        	}
		                            $option[2]['value'][$v['Option3']]['data'][] = $v['id'];                            
		                        }
		                    }
		                } 
		            } // End foreach
		        } // End if arr_option 

	        	$arr[0]['arr_option'] = $option;


	        	$arr_option = $ims->db->load_row_arr('product_option', ' ProductId ="'.$arr[0]['item_id'].'" AND is_show=1 ');
	        	if (!empty($arr_option)) {
	        		$arr_tmp = array();
	        		foreach ($arr_option as $k => $option) {
	        			if ($option['PricePromotion']>0) {
							$option['PriceBuy'] = $option['PricePromotion'];
						}
	        			$arr_tmp[$k]['id'] 	     = $option['id'];
	        			$arr_tmp[$k]['Option1']  = $option['Option1'];
	        			$arr_tmp[$k]['Option2']  = $option['Option2'];
	        			$arr_tmp[$k]['Option3']  = $option['Option3'];
	        			$arr_tmp[$k]['Price']    = $option['Price'];
	        			$arr_tmp[$k]['PriceBuy'] = $option['PriceBuy'];
	        			$arr_tmp[$k]['SKU'] 	 = $option['SKU'];
			            $arr_tmp[$k]['Picture'] = $option['Picture']!="" ? $ims->func->get_src_mod($option['Picture']) : "";
	        			// $arr_tmp[$k]['useWarehouse'] = (int)$option['useWarehouse'];
	        			$arr_tmp[$k]['useWarehouse'] = ($option['is_OrderOutStock']==1)?0:(int)$ims->setting['product']['use_ware_house'];
	        			$arr_tmp[$k]['Quantity'] = (int)$option['Quantity'];
	        			// $arr_tmp[$k]['is_OrderOutStock'] = (int)$option['is_OrderOutStock'];
	        			if ($arr_tmp[$k]['PriceBuy'] < $arr_tmp[$k]['Price']) {
							$arr_tmp[$k]['PercentDiscount'] = round((($arr_tmp[$k]['Price']-$arr_tmp[$k]['PriceBuy'])/$arr_tmp[$k]['Price'])*100);
						}
	        		}
	        		$arr[0]['arr_option_tmp'] = $arr_tmp;
	        	}else{
	        		$arr[0]['arr_option_tmp'] = array();
	        	} 
		    	$arr[0]['arr_related'] = $arr_nav;
		    	$arr[0]['promotion_code'] = $this->promotion_code($arr[0]);

		    	//list product combo
				$combo = $ims->db->load_row('combo','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and item_id="'.$arr[0]['combo_id'].'"','item_id,title,type,value,value_type,num_chose,arr_product,arr_gift,arr_include');
				if($combo){							
					switch ($combo['type']) {
						case '0':
							$type = 'Mua 1 tặng 1';
							break;
						case '1':
							$type = 'Giảm giá';
							break;
						case '2':
							$type = 'Giảm giá sản phẩm mua kèm';
							break;
						default:
							break;
					}				 	
					$arr_detail_combo = $ims->db->load_item_arr('product','is_show=1 and lang="'.$ims->conf['lang_cur'].'" and find_in_set(item_id,"'.$combo['arr_product'].'")>0 order by field(item_id,"'.$combo['arr_product'].'")','item_id,group_id,picture,title,price,price_buy,percent_discount,num_view');
					$arr_favorite = array();							
					if(!empty($infoUser['user_id'])){
						$product_favorite = $ims->db->load_row_arr('shared_favorite', ' type="product" AND user_id="'.$infoUser['user_id'].'" AND is_show=1 ');
						if (!empty($product_favorite)) {
							foreach ($product_favorite as $k => $v) {
								$arr_favorite[$v['type_id']] = $v;
							}
						}
					}
		        	if ($arr_detail_combo) {
						foreach ($arr_detail_combo as $k_nav => $v_nav) {									
							$arr_detail_combo[$k_nav]['is_favorite'] = isset($arr_favorite[$v_nav['item_id']]) ? 1 : 0;
							$arr_detail_combo[$k_nav]['picture'] = $ims->func->get_src_mod($v_nav['picture']);
							$arr_detail_combo[$k_nav]['thumbnail'] = $ims->func->get_src_mod($v_nav['picture'],40,40);
							// $arr_detail_combo[$k_nav]['price_buy'] = $v_nav['price'];
							$arr_detail_combo[$k_nav]['combo_type'] = $combo['type'];
							$arr_detail_combo[$k_nav]['combo_name'] = $type;									
						}
					}
		    		$arr[0]['arr_detail_combo'] = $arr_detail_combo;
		    		$arr[0]['combo'] = array(
		    			'title' => $combo['title'],
		    			'combo_id' => $combo['item_id'],
		    			'combo_type' => $combo['type'],
		    			'combo_name' => $type,
		    			'value_type' => $combo['value_type'],
		    			'value' => $combo['value'],
		    			'num_chose' => (int)$combo['num_chose'],
		    		);
		   //  		if(!empty($ims->get['debug_combo'])){
					// 	print_arr($array);
					// 	die;
					// }
					if($combo['type'] == 0){
						$arr_gift = $ims->db->load_item_arr('user_gift','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and find_in_set(item_id,"'.$combo['arr_gift'].'")>0 and quantity_combo>0', 'item_id, title, short, price, picture, quantity_combo, 1 as active');
						if($arr_gift){
							foreach ($arr_gift as $k => $v) {
								$arr_gift[$k]['picture'] = !empty($v['picture'])?$ims->func->get_src_mod($v['picture']):'';
								$arr_gift[$k]['thumbnail'] = !empty($v['picture'])?$ims->func->get_src_mod($v['picture'],40,40):'';
								$arr_gift[$k]['price_buy'] = 0;
							}
						}
						// $arr_gift_disable = $ims->db->load_item_arr('user_gift','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and find_in_set(item_id,"'.$combo['arr_gift'].'")<=0', 'item_id, title, short, price, picture, quantity_combo, 0 as active');
						// if($arr_gift_disable){
						// 	foreach ($arr_gift_disable as $k => $v) {
						// 		$arr_gift_disable[$k]['picture'] = $ims->func->get_src_mod($v['picture']);
						// 		$arr_gift_disable[$k]['thumbnail'] = $ims->func->get_src_mod($v['picture'],40,40);
						// 		$arr_gift_disable[$k]['price_buy'] = 0;
						// 	}
						// }								
						// $arr_gift = array_merge($arr_gift,$arr_gift_disable);
						$arr['combo']['arr_gift'] = $arr_gift;
					}				
					if($combo['type'] == 1){
						if(isset($combo['value_type']) && $combo['value_type'] == 0){
		                    $arr[0]['price_buy'] = $arr[0]['price'] - $combo['value'];
		                    if($arr[0]['price_buy'] <= 0){
		                        $arr[0]['price_buy'] = 0;
		                    }
		                }
		                elseif(isset($combo['value_type']) && $combo['value_type'] == 1){
		                    $arr[0]['price_buy'] = $arr[0]['price'] - ($arr[0]['price']*$combo['value']/100);
		                }
		                $arr[0]['percent_discount'] = 100-(round($arr[0]['price_buy']/$arr[0]['price'],2)*100);		                
		    //             if ($arr_detail_combo) {
						// 	foreach ($arr_detail_combo as $k_nav => $v_nav) {
						// 		if(isset($combo['value_type']) && $combo['value_type'] == 0){
				  //                   $arr_detail_combo[$k_nav]['price_buy'] = $arr_detail_combo[$k_nav]['price'] - $combo['value'];
				  //                   if($arr_detail_combo[$k_nav]['price_buy'] <= 0){
				  //                       $arr_detail_combo[$k_nav]['price_buy'] = 0;
				  //                   }
				  //               }
				  //               elseif(isset($combo['value_type']) && $combo['value_type'] == 1){
				  //                   $arr_detail_combo[$k_nav]['price_buy'] = $arr_detail_combo[$k_nav]['price'] - ($arr_detail_combo[$k_nav]['price']*$combo['value']/100);
				  //               }
				  //               $arr_detail_combo[$k_nav]['percent_discount'] = 100-(round($arr_detail_combo[$k_nav]['price_buy']/$arr_detail_combo[$k_nav]['price'],2)*100);
						// 	}
						// }
					}
					if($combo['type'] == 2){
						$arr_include = $ims->db->load_item_arr('product','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and find_in_set(item_id,"'.$combo['arr_include'].'")>0', 'item_id,group_id,picture,title,price,price_buy,percent_discount,num_view');
						if($arr_include){
							foreach ($arr_include as $k => $v) {
								$arr_include[$k]['is_favorite'] = isset($arr_favorite[$v['item_id']]) ? 1 : 0;
								$arr_include[$k]['picture'] = !empty($v['picture'])?$ims->func->get_src_mod($v['picture']):'';
								$arr_include[$k]['thumbnail'] = !empty($v['picture'])?$ims->func->get_src_mod($v['picture'],40,40):'';
								// $arr_include[$k]['price_buy'] = $v['price'];							
								// unset($arr_include[$k]["price"]);
								if(isset($combo['value_type']) && $combo['value_type'] == 0){
				                    $arr_include[$k]['price_buy'] = $v['price_buy'] - $combo['value'];
				                    if($arr_include[$k]['price_buy'] <= 0){
				                        $arr_include[$k]['price_buy'] = 0;
				                    }
				                }
				                elseif(isset($combo['value_type']) && $combo['value_type'] == 1){
				                    $arr_include[$k]['price_buy'] = $v['price_buy'] - ($v['price_buy']*$combo['value']/100);
				                }
				                $arr_include[$k]['percent_discount'] = 100-(round($arr_include[$k]['price_buy']/$arr_include[$k]['price'],2)*100);
							}
							$arr[0]['combo']['array_product_bonus'] = $arr_include;
						}
					}
				}
				unset($arr[0]['arr_item']);
        		unset($arr[0]['field_option']);
        		unset($arr[0]['detail_id']);
    			unset($arr[0]['meta_title']);
        		unset($arr[0]['meta_key']);
        		unset($arr[0]['meta_desc']);

	        	$array = array(
		    		"code" => 200,
	    			"message" => $ims->lang['api']['success'],
		    		'data' => $arr[0],
		    	);
			}else{
				$array = array(
					"code" => 200,
	    			"message" => $ims->lang['api']['success'],
		    		'total' => $num_total,
		    		'total_page' => $num_items,
		    		'numshow' => $n,
		    		'page' => $p,
		    		'data' => $arr,
		    	);
			}
			$this->response(200, $array);
		}else{
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}


	/*
		* tìm kiếm sản phẩm theo từ khóa
		* Method: GET
		* Content-Type: application/x-www-form-urlencoded
		* Param: keyword : ---		
	*/
	function getKeywordSearch(){
		global $ims;

		if ($ims->method == 'GET'){
			$keyword = $ims->func->if_isset($ims->get['keyword']); // Tìm theo từ khóa

			$where = '';
			if ($keyword !='') {
	        	$arr_tmp = array();
        		$arr_tmp_content = array();
				$arr_key = explode(' ', $keyword);
		        foreach ($arr_key as $value) {
		            $value = trim($value);
		            if (!empty($value)) {
		            	$value_khongdau = $ims->func->vn_str_filter($value);
		            	if (strpos($value, 'd') !== false) {
		                   $arr_tmp[] = " ( title LIKE '%".$value."%' OR title LIKE '%" . str_replace('d','đ',$value)."%') ";
		               	} elseif (strpos($value, 'đ') !== false) {
		                   $arr_tmp[] = " ( friendly_link LIKE '%" . $value_khongdau . "%' OR title LIKE '%".$value."%' OR title LIKE '%".str_replace('đ','d', $value)."%' ) ";
		               	} else{
		                   $arr_tmp[] = "title LIKE '%".$value."%'";
		               	}
		            }
		        }
		        if (count($arr_tmp) > 0) {         
		            $where .= " ( (" . implode(" AND ", $arr_tmp) . ") ) AND ";
		        }
	        }
	        $order_by = ' show_order DESC, date_update DESC, ';
	        $arr = $ims->db->load_item_arr(
				'product',
				$where .'is_show=1 AND lang="'.$ims->conf['lang_cur'].'" ORDER BY '.$order_by.' num_stock DESC  LIMIT 0, 10', 
				'item_id, title'
			);
	        $array = array(
        		"code" => 200,
	    		"message" => $ims->lang['api']['success'],
	    		'data' => $arr,
        	);
			$this->response(200, $array);
		}else{
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}


	/*
		* Kiểm tra số điện thoại đã có trên hệ thống chưa?
		* Method: POST
		* Content-Type: application/x-www-form-urlencoded
		* Body: username: ---
				password: ---
	*/
	function checkPhone(){
		global $ims;

		if ($ims->method == 'GET'){
			$phone = $ims->func->if_isset($ims->get['phone']);
			if (isset($phone)) {
				$check = $ims->db->load_row('user', 'phone="'.$phone.'"');
				if (!empty($check)) {
					$this->response(400, "", 400 , 'Số điện thoại đã tồn tại');
				}else{
					$this->response(200, "", 200 , 'Có thể sử dụng số điện thoại này');
				}
			}
		} else {
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}
	
	/*
		* Cập nhật lượt xem sản phẩm
		* Method: POST
		* Content-Type: application/x-www-form-urlencoded
		* Body: item_id : ---		
	*/
	function updateProductNumView(){
		global $ims;

		if ($ims->method == 'POST'){
			$item_id = $ims->func->if_isset($ims->get['item_id']);
			$product = $ims->db->load_row('product', 'is_show=1 AND lang="'.$ims->conf['lang_cur'].'" AND item_id="'.$item_id.'" ');
			if (!empty($product)) {
				$arr_up = array();
				$arr_up['num_view'] = $product['num_view'] + 1;
				$ok = $ims->db->do_update("product", $arr_up, " item_id='".$product['item_id']."'");	
				if($ok){
					$this->response(200, "", 200 , $ims->lang['api']['success']);
				}
			}else{
				$this->response(200, "", 200 , $ims->lang['api']['error_getRatingProduct_2']);
			}
		} else{
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}

	// Lấy chương trình khuyến mãi
	function getPromotionTime(){
		global $ims;
		if ($ims->method == 'GET'){

			$keyword  = $ims->func->if_isset($ims->get['keyword']); // Tìm theo từ khóa			
			$numshow  = $ims->func->if_isset($ims->get['numshow'],0);
			$p 		  = $ims->func->if_isset($ims->get['p'],1);
			$n = 50;
			if ($numshow>0) {
				$n = $numshow;
			}
			if ($numshow>500) {
				$n = 50;
			}
			$where = ' AND is_focus=1 ';

	        if ($keyword !='') {
	        	$arr_tmp = array();
				$arr_key = explode(' ', $keyword);
		        foreach ($arr_key as $value) {
		            $value = trim($value);
		            if (!empty($value)) {
		                $arr_tmp[] = "title LIKE '%" . $value . "%'";
		            }
		        }
		        if (count($arr_tmp) > 0) {         
		            $where .= " AND (" . implode(" AND ", $arr_tmp) . ")  ";
		        }
	        }
	     
			$res_num =$ims->db->query("SELECT item_id FROM product_promotion_time where is_show=1 AND lang='".$ims->conf['lang_cur']."' ".$where." ");
	        $num_total = $ims->db->num_rows($res_num);
	        $num_items = ceil($num_total / $n);
	        if ($p > $num_items)
	            $p = $num_items;
	        if ($p < 1)
	            $p = 1;
	        $start = ($p - 1) * $n;

			$arr = $ims->db->load_row('product_promotion_time','is_show=1 AND lang="'.$ims->conf['lang_cur'].'" '.$where.' ORDER BY show_order DESC, date_update DESC','item_id,title,picture,picture_banner,short,date_begin,date_end,time_begin,time_end,apply_product,apply_group');
			if (!empty($arr)) {
	            // foreach ($arr as $key => $value) {
					$arr['is_start'] = 0;
	            	if (time()>$arr['date_begin'] && time()<$arr['date_end']) {
						$arr['is_start'] = 1;
	            	}
					// $arr['date_begin'] = $valau['date_begin'];
					// $arr['date_end'] = $value['date_end'];
					$arr['time_begin'] = strtotime($arr['time_begin']) - strtotime('TODAY');
					$arr['time_end'] = strtotime($arr['time_end']) - strtotime('TODAY');
	            	if ($arr['picture']!="") {
						$arr['picture'] = $this->get_src_mod($arr['picture']);
	            	}
	            	if ($arr['picture_banner']!="") {
						$arr['picture_banner'] = $this->get_src_mod($arr['picture_banner']);
	            	}
	                $arr['short'] = $this->input_editor_decode($arr['short']);

	                //list item
					$where_product = $ims->site_func->list_product_bypromotion($arr);
					// echo 'lang="'.$ims->conf['lang_cur'].'" and is_show=1 '.$where_product.' order by show_order desc, date_create desc';
					$arr_product = $ims->db->load_item_arr('product','lang="'.$ims->conf['lang_cur'].'" and is_show=1 '.$where_product.' order by show_order desc, date_create desc','item_id,title,group_id,group_nav,picture,price,price_buy,price_promotion,percent_discount,quantity_sold');
					if(!empty($arr_product)){
						foreach ($arr_product as $k => $v) {
							$arr_product[$k]['picture'] = $ims->func->get_src_mod($v['picture']);
							$arr_product[$k]['price'] = (int)$v['price'];
							$arr_product[$k]['price_buy'] = (int)$v['price_buy'];
							if ($v['price_promotion']>0) {
								$arr_product[$k]['price_buy'] = (int)$v['price_promotion'];
								$arr_product[$k]['percent_discount'] = round((($v['price'] - $arr_product[$k]['price_buy'])/$v['price'])*100,2);
							}
							$arr_product[$k]['quantity_sold'] = (int)$v['quantity_sold'];
							unset($arr_product[$k]['price_promotion']);
						}
						$arr['list_product'] = $arr_product;
	                	$arr['total_products'] = count($arr_product);
					}else{
	                	$arr['total_products'] = 0;
					}
					unset($arr['apply_product']);
					unset($arr['apply_group']);
	            // }
	        }
			$array = array(
        		"code" => 200,
	    		"message" => $ims->lang['api']['success'],
				// 'total' => $num_total,
	   //  		'page' => $p,
	    		'data' => $arr,
        	);
			$this->response(200, $array);
		} else{
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}


	// lấy danh sách chương trình khuyến mãi
	function getPromotionList(){
		global $ims;

		if ($ims->method == 'GET'){

			$where = '';
			$type  = $ims->func->if_isset($ims->get['type']);
//			if ($type == 'banner') {
				$time = time()+(10*60);
				$where .= ' AND (date_begin<'.$time.' AND date_end>'.time().') ';
//			}

			$data = array (
				'modules' => 'product',
				'table' => 'product_promotion',
				'column' => array (
					array(
						'key' => 'item_id',
						'type' => 'number',
					),
					array(
						'key' => 'title',
						'type' => 'title',
					),
					array(
						'key' => 'icon',
						'type' => 'picture',
					),
					array(
						'key' => 'picture_banner',
						'type' => 'picture',
					),
					array(
						'key' => 'short',
						'type' => 'editor',
					),
					array(
						'key' => 'date_begin',
						'type' => 'datetime',
					),
					array(
						'key' => 'date_end',
						'type' => 'datetime',
					),
					array(
						'key' => 'apply_product',
						'type' => 'text',
					),
					array(
						'key' => 'apply_group',
						'type' => 'text',
					)
				),
				'arr_related' => 1,
				'where' => $where
			);
			$this->returnsPaging($data);
		}else{
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}

	// Lấy chương trình khuyến mãi trang chủ
	function getPromotionHome(){
		global $ims;
		if ($ims->method == 'GET'){
			$keyword  = $ims->func->if_isset($ims->get['keyword']);
			$type 	  = $ims->func->if_isset($ims->get['type']);
			$numshow_icon  = $ims->func->if_isset($ims->get['numshow_icon'],0);
			$numshow_text  = $ims->func->if_isset($ims->get['numshow_text'],0);
			$p  	  = $ims->func->if_isset($ims->get['p'],1);
			$n = 50;
			if ($numshow_icon>0) {
				$n = $numshow_icon;
			}
			if ($numshow_icon>500) {
				$n = 50;
			}

			$n2 = 50;
			if ($numshow_text>0) {
				$n2 = $numshow_text;
			}
			if ($numshow_text>500) {
				$n2 = 50;
			}

			$where = '';
				     
			// $res_num =$ims->db->query("SELECT item_id FROM product_promotion where is_show=1 AND lang='".$ims->conf['lang_cur']."' ".$where." ");
	  //       $num_total = $ims->db->num_rows($res_num);
	  //       $num_items = ceil($num_total / $n);
	  //       if ($p > $num_items)
	  //           $p = $num_items;
	  //       if ($p < 1)
	  //           $p = 1;
	  //       $start = ($p - 1) * $n;

			$arr2 = $ims->db->load_item_arr('product_promotion','is_show=1 AND lang="'.$ims->conf['lang_cur'].'" AND is_text=1 '.$where.' ORDER BY show_order DESC, date_update DESC LIMIT 0, '.$n2,'item_id,title,icon,short,apply_product,apply_group');
			if (!empty($arr2)) {
	            foreach ($arr2 as $key => $value) {
	            	if ($value['icon']!="") {
						$arr2[$key]['icon'] = $ims->func->get_src_mod($value['icon']);
	            	}
	                $arr2[$key]['short'] = $ims->func->input_editor_decode($value['short']);
	            }
	        }

			$arr = $ims->db->load_item_arr('product_promotion','is_show=1 AND lang="'.$ims->conf['lang_cur'].'" AND is_icon=1 '.$where.' ORDER BY show_order DESC, date_update DESC LIMIT 0, '.$n,'item_id,title,icon,short,apply_product,apply_group');
			if (!empty($arr)) {
	            foreach ($arr as $key => $value) {
	            	if ($value['icon']!="") {
						$arr[$key]['icon'] = $ims->func->get_src_mod($value['icon']);
	            	}
	                $arr[$key]['short'] = $ims->func->input_editor_decode($value['short']);
	            }
	        }

			$array = array(
        		"code" => 200,
    			"message" => $ims->lang['api']['success'],
	    		// 'total' => $num_total,
		    	// 'page' => $p,
		    	"numshow_text" => $n2,
		    	"numshow_icon" => $n,
	    		'data' => array(
	    			'text' => $arr2,
	    			'icon' => $arr,
	    		),
        	);
			$this->response(200, $array);
		} else{
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}


	// lấy chi tiết chương trình khuyến mãi
	function getPromotionDetail(){
		global $ims;

		if ($ims->method == 'GET'){
			$this->load_language('home');
			$this->load_language('product');
			$this->setting('product');
			
			$sort 		    = $ims->func->if_isset($ims->get['sort']); // Sắp xếp sp theo ?
			$price_min 	    = $ims->func->if_isset($ims->get['price_min'], 0); // giá tối thiểu
			$price_max 	    = $ims->func->if_isset($ims->get['price_max'], 0); // giá tối đa
			$average_rating = $ims->func->if_isset($ims->get['average_rating']); // Lấy theo đánh giá
			$num_total = 0;
			$item_id  = $ims->func->if_isset($ims->get['item_id'], 0);
			$p 		  = $ims->func->if_isset($ims->get['p'], 1);
			$numshow  = $ims->func->if_isset($ims->get['numshow'], 0); // Số sp hiển thị
			$n = $ims->setting['product']['num_list'];
			if ($numshow>0) {
				$n = $numshow;
			}
			if ($numshow>500) {
				$n = 10;
			}

			$promotion = $ims->db->load_row('product_promotion',' lang="'.$ims->conf['lang_cur'].'" AND is_show=1 AND item_id="'.$item_id.'" ','item_id,title,quantity,apply_product,apply_group,content,date_begin,date_end,time_begin,time_end');
			$products = array();
			if(!empty($promotion)){	
				$promotion['content'] = $this->short_no_cut($promotion['content']);
					//list item
					$where = $this->list_product_bypromotion($promotion);
					$order_by = " show_order desc, date_create desc ";
					// // Lọc theo khoảng giá
			        if ($price_min==0 && $price_max>0) {
			        	$where .= ' AND (price_buy <= '.$price_max.') ';
			        }elseif ($price_max==0 && $price_min==0) {

			        }elseif ($price_max==0 && $price_min>0) {
			        	$where .= ' AND (price_buy >= '.$price_min.') ';
			        }elseif ($price_min>0 && $price_max>0) {
			        	$where .= ' AND (price_buy >= '.$price_min.' AND price_buy <= '.$price_max.') ';
			        }
					if($sort){
			            if($sort == 'price-desc'){
			                $order_by = " price_buy DESC ";
			            }
			            elseif($sort == 'price-asc'){
			                $order_by = " price_buy ASC ";
			            }
			            elseif($sort == 'title-asc'){
			                $order_by = " title ASC ";
			            }            
			            elseif($sort == 'title-desc'){
			                $order_by = " title DESC ";
			            }
			            elseif($sort == 'new'){
			                $order_by = " date_create DESC ";
			            }
			            elseif($sort == 'good_review'){
			                $order_by = " average_rating DESC, num_rate DESC ";
			            }
			            elseif($sort == 'discount'){
			                $order_by = " percent_discount DESC ";
			            }
			            elseif($sort == 'selling'){
			                $order_by = " quantity_sold DESC ";
			            }
			        } 
			        // Đánh giá
			        if ($average_rating>0) {
				        $where .= " AND (average_rating>=".$average_rating.") ";
			        }

					$res_num = $ims->db->query("SELECT item_id from product where is_show=1 AND lang='".$ims->conf['lang_cur']."' ".$where);
			        $num_total = $ims->db->num_rows($res_num);
			        $num_items = ceil($num_total / $n);
			        if ($p > $num_items)
			            $p = $num_items;
			        if ($p < 1)
			            $p = 1;
			        $start = ($p - 1) * $n;
			        if(!empty($ims->get['debug'])){
			        	echo 'lang="'.$ims->conf['lang_cur'].'" and is_show=1 '.$where.' order by '.$order_by.' LIMIT '.$start.', '.$n.' ';
			        	die;	
			        }
					$arr_product = $ims->db->load_item_arr('product','lang="'.$ims->conf['lang_cur'].'" and is_show=1 '.$where.' order by '.$order_by.' LIMIT '.$start.', '.$n.' ','item_id, title, group_id, group_nav, picture, price, price_buy, price_promotion, percent_discount, average_rating, num_rate, quantity_sold');	
					if($arr_product){
						foreach ($arr_product as $k => $v) {
							$row = array();	
							if ($v['price_promotion']>0) {
								$v['price_buy'] = $v['price_promotion'];
							}
							$row['title']    = $ims->func->input_editor_decode($v['title']);
							$row['picture']  = $ims->func->get_src_mod($v['picture'],'','',1,0,array());
							$row['item_id']  = $v['item_id'];
					        $row["price"] = $v["price"];
							$row['price_buy'] = $v['price_buy'];							
					        $products[] = $row;
						}
					}
			}
			unset($promotion['quantity']);
			unset($promotion['apply_product']);
			unset($promotion['apply_group']);
			unset($promotion['date_begin']);
			unset($promotion['date_end']);
			unset($promotion['time_begin']);
			unset($promotion['time_end']);
			$array = array(
	    		"code" => 200,
    			"message" => $ims->lang['api']['success'],
	    		'total' => $num_total,
		    	'page' => $p,
	    		'data' => $promotion,
	    		'is_promo' => 1,
	    		'products' => $products,
	    	);
			$this->response(200, $array);
		} else{
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}


	// lấy danh sách thuộc tính sản phẩm
	function getProductOption(){
		global $ims;

		if ($ims->method == 'GET'){
			$productId = isset($ims->get['item_id']) ? $ims->get['item_id'] : 0;
			$option1   = isset($ims->get['option1']) ? $ims->get['option1'] : '';
			$option2   = isset($ims->get['option2']) ? $ims->get['option2'] : '';
			$option3   = isset($ims->get['option3']) ? $ims->get['option3'] : '';

			$productInfo = $ims->db->load_row('product', 'is_show=1 AND lang="'.$ims->conf['lang_cur'].'" AND item_id="'.$productId.'" ');

			$ims->func->load_language('global');
			$ims->func->load_language('product');

			$data = array();
        	$option = array();
			if(!empty($productInfo)){
	            $arr_item = $ims->func->unserialize($productInfo['arr_item']);               
	            foreach ($arr_item as $k => $row) {
	                if($row['SelectName'] == 'Custom'){
	                    $row['title'] = $row['CustomName'];                    
	                }else{
	                    $row['title'] = isset($ims->lang['product']['option_'.strtolower($row['SelectName'])])?$ims->lang['product']['option_'.strtolower($row['SelectName'])]:'';
	                }                
	                $option['option'.($k+1)]['id'] = 'option'.($k+1);
	                $option['option'.($k+1)]['title'] = $row['title'];
	                $option['option'.($k+1)]['group_id'] = strtolower($row['SelectName']);
	                $option['option'.($k+1)]['value'] = array();
	            }
	            $order_by = ' ORDER BY date_create';
	            if($productInfo['field_option'] != ''){
	                $order_by = ' ORDER BY '.$productInfo['field_option'].', date_create DESC';
	            }
	            $arr_option = $ims->db->load_row_arr('product_option','lang="'.$ims->conf['lang_cur'].'" AND is_show=1 AND ProductId="'.$productId.'" '.$order_by);

	            $arr_color = array();
		        $arr_color_tmp = $ims->db->load_item_arr('product_color','lang="'.$ims->conf['lang_cur'].'" and is_show=1','color_id,color,title');
		        foreach ($arr_color_tmp as $color) {
		            $color['name'] = $ims->func->vn_str_filter($color['title']);
		            $arr_color[strtolower($color['name'])] = $color;
		        }

	            if($arr_option){                   
	                $i=0;
	                foreach (($arr_option) as $k => $v) {
	                    $i++;
	                    $key_title = $v['Option1'].'/'.$v['Option2'].'/'.$v['Option3'];
	                    if(count($arr_option)>0){
	                        if($v['Option1'] != ''){  
	                            $option['option1']['value'][$v['Option1']]['title'] = $v['Option1'];                        
	                        	if ($option['option1']['group_id'] == 'color') {
	                        		$colorkey = $ims->func->vn_str_filter($v['Option1']);
	                            	$option['option1']['value'][$v['Option1']]['colorkey'] = isset($arr_color[strtolower($colorkey)]) ? $arr_color[strtolower($colorkey)]['color'] : '';                        
	                        	}                     
	                            $option['option1']['value'][$v['Option1']]['data'][$key_title] = $v['id'];                        
	                        }
	                        if($v['Option2'] != ''){
	                            $option['option2']['value'][$v['Option2']]['title'] = $v['Option2'];                        
	                        	if ($option['option2']['group_id'] == 'color') {
	                        		$colorkey = $ims->func->vn_str_filter($v['Option2']);
	                            	$option['option2']['value'][$v['Option2']]['colorkey'] = isset($arr_color[strtolower($colorkey)]) ? $arr_color[strtolower($colorkey)]['color'] : '';                        
	                        	}
	                            $option['option2']['value'][$v['Option2']]['data'][$key_title] = $v['id'];
	                        }
	                        if($v['Option3'] != ''){
	                            $option['option3']['value'][$v['Option3']]['title'] = $v['Option3'];                        
	                        	if ($option['option3']['group_id'] == 'color') {
	                        		$colorkey = $ims->func->vn_str_filter($v['Option3']);
	                            	$option['option3']['value'][$v['Option3']]['colorkey'] = isset($arr_color[strtolower($colorkey)]) ? $arr_color[strtolower($colorkey)]['color'] : '';                        
	                        	}
	                            $option['option3']['value'][$v['Option3']]['data'][$key_title] = $v['id'];                            
	                        }
	                    }
	                } 
	            } // End foreach
	        } // End if arr_option 

	        if ($option1=='' && $option2=='' && $option3=='') {
	        	$array = array(
		    		"code" => 200,
	    			"message" => $ims->lang['api']['success'],
		    		'data' => $option,
		    	);
				$this->response(200, $array);
	        }

	       

	        $arr_output = array();
	        if (!empty($option)) {
	        	$arr_tmp = array();

	        	if ($option1!="") {
	        		$arr_tmp['option1'] = $this->processProductOption($option1, 'option1', $option);
	        	}
	        	if ($option2!="") {
	        		$arr_tmp['option2'] = $this->processProductOption($option2, 'option2', $option);
	        	}
	        	if ($option3!="") {
	        		$arr_tmp['option3'] = $this->processProductOption($option3, 'option3', $option);
	        	}

	        	// Tìm điểm chung của Option1, Option2, Option3
	        	$arr_all_option = array();
        		$arr_all_option[] = 'option1';
        		$arr_all_option[] = 'option2';
        		$arr_all_option[] = 'option3';
	        	foreach ($arr_all_option as $key => $value) {
	        		$list = array();
	        		foreach (array('option1', 'option2', 'option3') as $k => $v) {
			            if (isset($arr_tmp[$v][$value]['value']) && !empty($arr_tmp[$v][$value]['value'])) {
							$list[] = $arr_tmp[$v][$value]['value'];
			        	}
	        		}
	        		$intersect = array();
	        		if (count($list) == 1) {
		        		$intersect = $list[0];
		        	}else{
		        		if (!empty($list)) {
							$intersect = call_user_func_array('array_intersect_key', $list);
		        		}
		        	}

		        	$arr_output[$value] = isset($option[$value]) ? $option[$value] : array();
		        	if (!empty($intersect)) {
		        		$arr_output[$value]['value'] = $intersect;
		        	}else{
		        		$arr_output[$value]['value'] = array();
		        	}
		        }



	        	$array = array(
		    		"code" => 200,
	    			"message" => $ims->lang['api']['success'],
		    		'data' => $arr_output,
		    	);
				$this->response(200, $array);
	        }
		}else{
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}

	// lấy chi tiết thuộc tính sản phẩm
	function getProductOptionDetail(){

		global $ims;

		if ($ims->method == 'GET'){
			$productId = $ims->func->if_isset($ims->get['item_id'], 0);
			$option1   = $ims->func->if_isset($ims->get['option1']);
			$option2   = $ims->func->if_isset($ims->get['option2']);
			$option3   = $ims->func->if_isset($ims->get['option3']);
			$productInfo =$ims->db->load_row('product','is_show=1 AND lang="'.$ims->conf['lang_cur'].'" AND item_id="'.$productId.'"');
			$ims->func->load_language('global');
			$data = array();
        	$option = array();
			if(!empty($productInfo)){
	            $arr_item = $ims->func->unserialize($productInfo['arr_item']);               
	            foreach ($arr_item as $k => $row) {
	                if($row['SelectName'] == 'Custom'){
	                    $row['title'] = $row['CustomName'];                    
	                }else{
	                    $row['title'] = isset($ims->lang['global']['option_'.strtolower($row['SelectName'])])?$ims->lang['global']['option_'.strtolower($row['SelectName'])]:'';
	                }                
	                $option['option'.($k+1)]['id'] = 'option'.($k+1);
	                $option['option'.($k+1)]['title'] = $row['title'];
	                $option['option'.($k+1)]['group_id'] = strtolower($row['SelectName']);
	                $option['option'.($k+1)]['value'] = array();
	            }
	            $order_by = ' ORDER BY date_create';
	            if($info['field_option'] != ''){
	                $order_by = ' ORDER BY '.$arr[0]['field_option'].', date_create DESC';
	            }
	            $where_option = '';
	            if ($option1!="") {
	            	$where_option .= ' AND Option1="'.$option1.'" ';
	            }
	            if ($option2!="") {
	            	$where_option .= ' AND Option2="'.$option2.'" ';
	            }
	            if ($option3!="") {
	            	$where_option .= ' AND Option3="'.$option3.'" ';
	            }
	            $arr_option = $ims->db->load_row('product_option','lang="'.$ims->conf['lang_cur'].'" AND is_show=1 AND ProductId="'.$productId.'" '.$where_option.' '.$order_by);

	            if($arr_option){   
	            	if ($arr_option['Picture']!="") {
				         $arr_option['Picture'] = $ims->func->get_src_mod($arr_option['Picture']);
	            	}else{
				         $arr_option['Picture'] = $ims->func->get_src_mod($productInfo['picture']);
	            	}
	            } // End foreach
	            $array = array(
		    		"code" => 200,
	    			"message" => $ims->lang['api']['success'],
		    		'data' => $arr_option,
		    	);
				$this->response(200, $array);         
			}
		}else{
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}


	// lấy danh sách màu sắc
	function getColor(){
		global $ims;

		$item_id = $ims->func->if_isset($ims->get['color_id']);
		if ($ims->method == 'GET'){
			$where = '';
	        if ($item_id > 0) {
	        	$where = ' color_id="'.$item_id.'" AND ';
	        }
			$arr = $ims->db->load_item_arr('product_color',$where.'is_show=1 and lang="'.$ims->conf['lang_cur'].'" order by show_order DESC, date_update DESC', 'color_id,title,color');
			$array = array(
        		"code" => 200,
	    		"message" => $ims->lang['api']['success'],
				'data' => $arr
        	);
			$this->response(200, $array);
		}
	}


	// Lấy danh mục sản phẩm
	function getProductGroup(){
		global $ims;

		if ($ims->method == 'GET'){
			$parent_id = $ims->func->if_isset($ims->get['parent_id'], 0);
			$this->data_group('product', $parent_id);
			if ($parent_id >0) {
				$tmp = array();
				foreach ($ims->data["product_group"] as $k => $v) {
					$tmp_sub = array();
					$group_sub = $this->data_table ('product_group', 'group_id', 'group_id, picture_app, title', "lang='".$ims->conf['lang_cur']."' AND parent_id='".$v['group_id']."' ORDER BY show_order DESC, date_create DESC");
					if (!empty($group_sub)) {
						foreach ($group_sub as $k_sub => $v_sub) {							
		                    $group_sub[$k_sub]['picture'] = !empty($v_sub['picture_app'])?$ims->func->get_src_mod($v_sub['picture_app']):'';
							$tmp_sub[] = $group_sub[$k_sub];
						}
						$ims->data["product_group"][$k]['group_sub'] = $tmp_sub;
					}else{
						$ims->data["product_group"][$k]['group_sub'] = array();
					}
					$ims->data["product_group"][$k]['group_sub'] = $tmp_sub;
					$ims->data["product_group"][$k]['friendly_link'] = $this->get_link_lang ($ims->conf['lang_cur'] ,'product', $ims->data["product_group"][$k]['friendly_link']);
					unset($ims->data["product_group"][$k]['group_nav']);
					unset($ims->data["product_group"][$k]['group_level']);
					unset($ims->data["product_group"][$k]['parent_id']);
					unset($ims->data["product_group"][$k]['picture_app']);
					$tmp[] = $ims->data["product_group"][$k];
				}
				$array = array(
		    		"code" => 200,
    				"message" => $ims->lang['api']['error_getProductGroup_0'],
		    		'data' => $tmp,
		    	);
				$this->response(200, $array);
			}
			$arr_tmp = array();
			foreach ($ims->data["product_group_tree"] as $key => $value) {
				$ims->data["product_group_tree"][$key]['friendly_link'] = $this->get_link_lang ($ims->conf['lang_cur'] ,'product', $ims->data["product_group_tree"][$key]['friendly_link']);
				unset($ims->data["product_group_tree"][$key]['arr_sub']);
				unset($ims->data["product_group_tree"][$key]['group_nav']);
				unset($ims->data["product_group_tree"][$key]['group_level']);
				unset($ims->data["product_group_tree"][$key]['parent_id']);
				unset($ims->data["product_group_tree"][$key]['picture_app']);
				$arr_tmp[] = $ims->data["product_group_tree"][$key];
			}
			$array = array(
	    		"code" => 200,
				"message" => $ims->lang['api']['error_getProductGroup_0'],
	    		'data' => $ims->data["product_group_tree"],
	    	);
	    	$array['data'] = $arr_tmp;
			$this->response(200, $array);
		}else{
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}


	/*
		* Quên mật khẩu
		* Method: GET
	*/
	function fogetPassword(){
		global $ims;

		if ($ims->method == 'GET'){
			$username = $ims->func->if_isset($ims->get['username']);
			if (!empty($username)) {
				$this->setting('user');
				$ims->func->load_language('user');
	            $user = $ims->db->load_row('user'," (username='".$username."' OR phone='".$username."' OR email='".$username."') ");
	            if (empty($user)) {
					$this->response(400, "", 400, $ims->lang['api']['error_fogetPassword_2_1']);
	            }
	            if($user['is_show'] == 0){
	            	$this->response(403, "", 403 , $ims->lang['api']['error_signinUser_2']);
	            }
	            if($user['is_show'] == 2){
	            	$this->response(403, "", 403 , $ims->lang['api']['error_signinUser_3']);
	            }
	            if(!empty($user['email'])){
		            $arr_in = array();
		           	$arr_in["user_code"]   = $user['user_id'].'u'.$ims->func->random_str(10);
					$arr_in["pass_reset"]  = $ims->func->random_str (10);
					$arr_in["date_update"] = time();
					$ok = $ims->db->do_update("user", $arr_in, " user_id='".$user['user_id']."'");	
					if($ok){						
						$link_forget_pass = $this->get_link_lang ($ims->conf['lang_cur'] ,'user', $ims->setting['user']["forget_pass_link"]).'/?code='.$arr_in["user_code"];
						$arr_key   = array('{full_name}','{new_pass}','{link_forget_pass}');
						$arr_value = array($user['full_name'], $arr_in["pass_reset"], $link_forget_pass);
						$this->send_mail_temp ('forget-pass', $user['email'], $ims->conf['email'], $arr_key, $arr_value);
						$array = array(
							'code' => 200,
							'message' => $ims->lang['api']['error_fogetPassword_0'],
							'data' => array(
								'type' => 'email'
							),
						);
						$this->response(200, $array);
		            }else{
						$this->response(400, "", 400, $ims->lang['api']['error_fogetPassword_3']);
		            }
	            }else{
	            	$ims->site_func->setting('user');	            	
	                if(time() < $user['date_expire'] && !empty($user['otp'])){
	                	$array = array(
							'code' => 400,
			        		'message' => $ims->lang['user']['request_otp_success1'],
			        		'otp' => $check['otp'],
			        	);
						$this->response(400, $array);
	                }
	            	$otp = rand(1000, 9999);
			        $data_sms = array(
			            'ApiKey'    => $ims->setting['user']['esms_ApiKey'],
			            'SecretKey' => $ims->setting['user']['esms_SecretKey'],
			            'Brandname' => $ims->setting['user']['esms_Brandname'],
			            'Phone'     => $username,
			            'Content'   => str_replace('{otp}', $otp, $ims->setting['user']['esms_Contentpass']),
			            'SmsType'   => 2,
			            'Sandbox'   => 0,
			        );
			        
			        $data_sms = http_build_query ($data_sms);
			        $curl = curl_init();
			        $header = array("Content-Type:application/x-www-form-urlencoded");
			        curl_setopt_array($curl, array(
	                    CURLOPT_RETURNTRANSFER  => 1,
	                    CURLOPT_URL             => 'http://rest.esms.vn/MainService.svc/json/SendMultipleMessage_V4_post/',
	                    CURLOPT_POST            => 1,
	                    CURLOPT_HTTPHEADER      => $header,
	                    CURLOPT_SSL_VERIFYPEER  => 0,
	                    CURLOPT_POSTFIELDS      => $data_sms
	                ));
	                $resp = curl_exec($curl);
	        		curl_close($curl);
			        $SMS = $resp;  			        
			        if (!empty($SMS)) {
			            $SMS = json_decode($SMS);	            
			            if (isset($SMS->CodeResult) && $SMS->CodeResult==100) {
			                $col 				= array();
			                $col['date_expire'] = time()+60*5;
			                $col['otp']		    = $otp;
			                $ok = $ims->db->do_update('user', $col, " user_id='".$user['user_id']."' ");
			                if($ok){
			                	$array = array(
									'code' => 200,
					        		'message' => $ims->lang['user']['request_otp_success'],
									'otp' => $otp,
									'phone' => $user['phone'],
									'data' => array(
										'type' => 'phone'
									),
					        	);
								$this->response(200, $array);
			                }else{	            
			                	$array = array(
									'code' => 400,
					        		'message' => $ims->lang['user']['request_otp_false']
					        	);
								$this->response(400, $array);    	
			                }
			            }
			        }else{
						$array = array(
							'code' => 400,
			        		'message' => $ims->lang['user']['request_otp_false']
			        	);
						$this->response(200, $array);    	
			        }
	            }
			}else{
				$this->response(400, "", 400, $ims->lang['api']['error_fogetPassword_2_1']);
			}
		} else {
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}


	/*
		* Lấy Thành phố/ Quận huyện
		* Method: GET
		* Body: type : (province or district or ward)
		+ Nếu lấy district: &province_code=?&type=district.
		+ Nếu lấy ward: &district_code=?&type=ward.
	*/
	function getLocation(){
		global $ims;

		if ($ims->method == 'GET'){
			$country 	   = 'vi';
			$type 		   = $ims->func->if_isset($ims->get['type'], 0);
			$province_code = $ims->func->if_isset($ims->get['province_code'], 0);
			$district_code = $ims->func->if_isset($ims->get['district_code'], 0);
			$arr = $ims->db->load_item_arr('location_province', 'lang="'.$ims->conf['lang_cur'].'" AND is_show=1 AND country_code="'.$country.'" ', 'code,title');
			if ($type == 'province') {
			}
			if ($type == 'district' && $province_code > 0) {
				$arr = $ims->db->load_item_arr('location_district', 'lang="'.$ims->conf['lang_cur'].'" AND is_show=1 AND province_code="'.$province_code.'" ', 'code,title');
				if (empty($arr)) {
					$this->response(400, "", 400 , $ims->lang['api']['wrongcitycode']);
				}
			}
			if ($type == 'ward' && $district_code > 0) {
				$arr = $ims->db->load_item_arr('location_ward', 'lang="'.$ims->conf['lang_cur'].'" AND is_show=1 AND district_code="'.$district_code.'" ', 'code,title');
				if (empty($arr)) {
					$this->response(400, "", 400 , $ims->lang['api']['wrongdistrictcode']);
				}
			}
			$array = array (
				"code" => 200,
				"message" => $ims->lang['api']['success'],
	    		'data' => $arr,
	    	);
			$this->response(200, $array);
		} else{
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}

	// Active and login
	function verifyOTP(){
		global $ims;
		if ($ims->method == 'GET'){
			$token_login 	= $ims->func->if_isset($ims->get['user'],0);
			$otp 			= $ims->func->if_isset($ims->get['otp']);
			$device_token 	= $ims->func->if_isset($ims->post['device_token']);
			$device_name  	= $ims->func->if_isset($ims->post['device_name']);
			if(empty($otp)){
				$this->response(400, "", 400, 'Mã OTP không hợp lệ');
			}
			if (isset($token_login) && $token_login!='') {
				$this->load_language('user');
				$this->setting('user');
				$infoUser = $ims->db->load_row('user','token_login="'.$token_login.'"');
				$check_isset = isset($infoUser['user_id'])?$infoUser['user_id']:0;
	            if ($check_isset==0) {
	            	$array = array(
						'code' => 400,
		        		'message' => 'Sai token login'
		        	);
					$this->response(400, $array);
	            }
	            if (!empty($infoUser['otp']) && $infoUser['otp']==$otp) {		            
		            $col 				= array();
					$col['date_expire'] = 0;
					$col['otp'] 		= 0;
					$col['is_show'] 	= 1;
	                $ok = $ims->db->do_update('user', $col, " user_id='".$infoUser['user_id']."' ");
	                if ($ok) {
			            $array = array(
			        		'code' => 200,
			        		'message' => $ims->lang['api']['success'],
			        	);
						$this->response(200, $array);
	                }
	            }else{
	            	$array = array(
						'code' => 400,
		        		'message' => 'OTP không hợp lệ'
		        	);
					$this->response(400, $array);
	            }
	        }
	    } else {
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}

	// Gửi OTP
	function sendOTP(){
		global $ims;
		if ($ims->method == 'GET'){			
			$token_login = $ims->func->if_isset($ims->get['user'],0);			
			// $request = $ims->func->if_isset($ims->get['request'],0);
			if (!empty($token_login)) {
				$this->load_language('user');
				$this->setting('user');
				$infoUser = $ims->db->load_row('user','token_login="'.$token_login.'"');
				$check_isset = isset($infoUser['user_id'])?$infoUser['user_id']:0;
	            if ($check_isset==0) {
	            	$array = array(
						'code' => 400,
		        		'message' => 'Sai token login'
		        	);
					$this->response(400, $array);
	            }
	            	           
	            $check = $ims->db->load_row('user','user_id='.$infoUser['user_id'].'','date_expire,otp');
	            if($check){
	                if(time() < $check['date_expire'] && !empty($check['otp'])){
	                	$array = array(
							'code' => 400,
			        		'message' => $ims->lang['user']['request_otp_success1'],
			        		'otp' => $check['otp'],
			        	);
						$this->response(400, $array);
	                }
	            }

				// Gửi sms mã otp 0336565275
				$otp = rand(1000, 9999);
		        $data_sms = array(
		            'ApiKey'    => $ims->setting['user']['esms_ApiKey'],
		            'SecretKey' => $ims->setting['user']['esms_SecretKey'],
		            'Brandname' => $ims->setting['user']['esms_Brandname'],
		            'Phone'     => $infoUser['phone'],
		            'Content'   => str_replace('{otp}', $otp, $ims->setting['user']['esms_Content']),
		            'SmsType'   => 2,
		            'Sandbox'   => 0,
		        );
		        //str_replace('{otp}', $otp, $ims->setting['user']['esms_Content'])
		        //Ma OTP la {otp}. Ma OTP có thoi han 5 phut va chi su dung 1 lan. Quy khach vui long bao mat thong tin.
		        $data_sms = http_build_query ($data_sms);
		        $curl = curl_init();
		        $header = array("Content-Type:application/x-www-form-urlencoded");
		        curl_setopt_array($curl, array(
                    CURLOPT_RETURNTRANSFER  => 1,
                    CURLOPT_URL             => 'http://rest.esms.vn/MainService.svc/json/SendMultipleMessage_V4_post/',
                    CURLOPT_POST            => 1,
                    CURLOPT_HTTPHEADER      => $header,
                    CURLOPT_SSL_VERIFYPEER  => 0,
                    CURLOPT_POSTFIELDS      => $data_sms
                ));
                $resp = curl_exec($curl);
        		curl_close($curl);
		        $SMS = $resp;  
		        if (!empty($SMS)) {
		            $SMS = json_decode($SMS);	            
		            if (isset($SMS->CodeResult) && $SMS->CodeResult==100) {
		                $col 				= array();
		                $col['date_expire'] = time()+60*5;
		                $col['otp']		    = $otp;
		                $ok = $ims->db->do_update('user', $col, " user_id='".$infoUser['user_id']."' ");
		                if($ok){
		                	$array = array(
								'code' => 200,
				        		'message' => $ims->lang['user']['request_otp_success'],
								'otp' => $otp,
								'phone' => $infoUser['phone'],
				        	);
							$this->response(200, $array);
		                }else{	            
		                	$array = array(
								'code' => 400,
				        		'message' => $ims->lang['user']['request_otp_false']
				        	);
							$this->response(400, $array);    	
		                }
		            }
		        }else{
					$array = array(
						'code' => 400,
		        		'message' => $ims->lang['user']['request_otp_false']
		        	);
					$this->response(200, $array);    	
		        }
	        }
	    } else {
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}

	/*
		* Đăng ký tài khoản
		* Method: POST
		* Content-Type: application/x-www-form-urlencoded
		* Body: username: ---
				password: ---
	*/
	function signinUser(){
		global $ims;

		if ($ims->method == 'POST'){
			$username     = $ims->func->if_isset($ims->post['username']);
			$password     = $ims->func->if_isset($ims->post['password']);
			$device_token = $ims->func->if_isset($ims->post['device_token']);
			$device_name  = $ims->func->if_isset($ims->post['device_name']);
			if (isset($username) && isset($password)) {
				$this->setting('user');
	            $password = $ims->func->md25($password);
	            $row = $ims->db->load_row("user", " (email='".$username."' OR phone='".$username."') AND password='" . $password . "' ");
	            if (!empty($row)) {
					switch ($row['is_show']) {
						case 1:
							$token = $this->createTokenLogin($row["user_id"], $row["token_login"], $device_token, $device_name);
							$array = array(
								"code" 	  => 200,
					    		"message" => $ims->lang['api']['success'],
				        		'token'   => $token,
				        	);
							$this->response(200, $array);
							break;
						case 0:
							$this->response(403, "", 403 , $ims->lang['api']['error_signinUser_2']);
							break;
						case 2:
							$this->response(403, "", 403 , $ims->lang['api']['error_signinUser_3']);
							break;
					}
	            }else{
					$this->response(422, "", 422 , $ims->lang['api']['error_signinUser_4']);
	            }
	        }
		} else {
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}


	/*
		* Đăng ký tài khoản
		* Method: POST
		* Content-Type: application/x-www-form-urlencoded
		* Body: username:  ---
				password:  ---
				full_name: ---
				phone: 	   ---
	*/
	function signupUser(){
		global $ims;

		if ($ims->method == 'POST'){
			$email   	  = $ims->func->if_isset($ims->post['email']);
			$password     = $ims->func->if_isset($ims->post['password']);
			$full_name    = $ims->func->if_isset($ims->post['full_name']);
			$phone        = $ims->func->if_isset($ims->post['phone']);
			$device_token = $ims->func->if_isset($ims->post['device_token']);
			$device_name  = $ims->func->if_isset($ims->post['device_name']);
			// $invitation_code  = $ims->func->if_isset($ims->post['invitation_code']);
			$deeplink_code = $contributor_code = '';
			$referral_code = $ims->func->if_isset($ims->post['referral_code']);
			if($referral_code){
				$deeplink_code = $ims->db->load_item('user_deeplink', 'short_code = "'.$referral_code.'" and is_show = 1 ', 'id');
				$contributor_code = $ims->db->load_item('user', 'user_code = "'.$referral_code.'" and is_show = 1 ', 'user_code');
			}			
			if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$check_email = $ims->db->load_item('user', "(email='".$email."' AND email!='')",'user_id');
	            if ($check_email>0) {
					$this->response(409, "", 409 , $ims->lang['api']['error_signupUser_2']);
	            }
			  	$this->response(409, "", 409 , "Email không hợp lệ");
			}
			$ims->site_func->setting('user');
			if($ims->setting['user']['signup_type']==1 && empty($email)){
				$this->response(409, "", 409 , "Vui lòng nhập email để kích hoạt tài khoản");	
			}
			if (!empty($password)) {
				$this->setting('user');				
				// $ims->setting['user']['signup_type'] = 3;
				switch ($ims->setting['user']['signup_type']) {
					case '1':
						$signup = array(
							'name' => 'active_by_email',
							'mess' => $ims->site_func->get_lang ('mess_success_1', 'user', array('[name]' => $ims->site_func->get_lang ('signup', 'user'))),
						);
						break;
					case '2':
						$signup = array(
							'name' => 'active_by_admin',
							'mess' => $ims->site_func->get_lang ('mess_success_2', 'user', array('[name]' => $ims->site_func->get_lang ('signup', 'user'))),
						);
						break;
					case '3':						
						$signup = array(
							'name' => 'active_by_otp',
							'mess' => $ims->site_func->get_lang ('mess_success_3', 'user', array('[name]' => $ims->site_func->get_lang ('signup', 'user'))),
						);
						break;
					default:						
						$signup = array(
							'name' => 'auto_active',
							'mess' => $ims->site_func->get_lang ('mess_success', 'user', array('[name]' => $ims->site_func->get_lang ('signup', 'user'))),
						);
						break;
				}
	            $password = $ims->func->md25($password);	            
	            $check_phone = $ims->db->load_item('user', "(phone='".$phone."' AND phone!='')",'user_id');
	            if ($check_phone>0) {
					$this->response(409, "", 409 , $ims->lang['api']['error_signupUser_4']);
	            }	            
	            $arr_in = array();

	    //         if ($invitation_code > 0) {
					// $arr_in["type_contributor"] = 'app';
					// $arr_in["user_contributor"] = $invitation_code;
		   //          $userInv = $ims->db->load_row('user', ' is_show=1 AND user_id="'.$invitation_code.'" ');
					// if( $userInv['user_contributor_level'] > 0 && 
		   //              $userInv['root_id'] > 0 && 
		   //              $userInv['user_contributor_level'] <= 5 &&
		   //              $userInv['user_contributor'] != ''
		   //          ){
					// 	$arr_in["root_id"] = $userInv["root_id"];
					// 	$arr_in["user_contributor_level"] = $userInv["user_contributor_level"] + 1;
					// }else{
		   //              if ($arr_in["user_contributor"] != '') {
		   //                  $arr_in["root_id"] = $userInv["user_id"];
		   //                  $arr_in["user_contributor_level"] = $userInv["user_contributor_level"] + 1;
		   //              }
		   //          }
	    //         }
	            $check_log = $ims->db->load_item('user_recommend_log', 'is_show = 1 and referred_user_id = 0 and (referred_phone = "'.$phone.'" or referred_email = "'.$email.'")', 'id');
	            if(!$check_log){
	                $arr_in["user_contributor"] = $contributor_code;
	                $arr_in["type_contributor"] = 'app';
	                $user_row = $ims->db->load_row('user', 'is_show = 1 AND user_code = "'.$arr_in["user_contributor"].'"');
	                if($user_row){
	                    if( $user_row['user_contributor_level'] > 0 &&
	                        $user_row['root_id'] > 0 &&
	                        $user_row['user_contributor_level'] <= 5 &&
	                        $user_row['user_contributor'] != ''
	                    ){
	                        $arr_in["root_id"] = $user_row["root_id"];
	                        $arr_in["user_contributor_level"] = $user_row["user_contributor_level"] + 1;
	                    }else{
	                        if ($arr_in["user_contributor"] != '') {
	                            $arr_in["root_id"] = $user_row["user_id"];
	                            $arr_in["user_contributor_level"] = $user_row["user_contributor_level"] + 1;
	                        }
	                    }
	                }
	            }

	            $arr_in["user_id"] 		 = $ims->db->getAutoIncrement('user');
	            $arr_in["folder_upload"] = $arr_in["user_id"].'u'.$ims->func->random_str(4, 'ul');
				$arr_in["user_code"] 	 = $arr_in["user_id"].'u'.$ims->func->random_str(10, 'ul');
	            // $arr_in["is_show"] 		 = ($ims->setting['user']['signup_type'] == 0) ? 1 : 0;
	            $arr_in["is_show"] = 0;
				$arr_in["email"]         = $email;
				$arr_in["username"]  	 = $phone;
				$arr_in["password"]  	 = $password;
				$arr_in["full_name"]  	 = $full_name;
				$arr_in["phone"]     	 = $phone;
				$arr_in["date_login"]    = time();
	            $arr_in["date_update"]   = time();
	            $arr_in["date_update"]   = time();
	            if(!empty($ims->post['debug'])){
	            	$array = array(
						"code" => 200,
	    				"message" => 'debug',
		        		"data" => $arr_in,
		        		"type" => $signup,
		        	);
					$this->response(200, $array);
	            }
	            $ok = $ims->db->do_insert("user", $arr_in);
				if($ok) {					
					if($check_log){
	                    $ims->db->do_update('user_recommend_log', array('referred_user_id' => $arr_in['user_id']), 'id = '.$check_log);
	                }elseif(!empty($arr_in["user_contributor"]) && !empty($user_row)){
			            //contributor_code
	                    $recommend_log = array(
	                        'type' => 'contributor',
	                        'recommend_user_id' => $user_row['user_id'],
	                        'recommend_link' => 'contributor='.$ims->func->base64_encode($arr_in["user_contributor"]).'&type='.$arr_in["type_contributor"],
	                        'referred_user_id' => $arr_in['user_id'],
	                        'referred_full_name' => $arr_in["full_name"],
	                        'referred_phone' => $arr_in["phone"],
	                        'referred_email' => $arr_in["email"],
	                        'is_show' => 1,
	                        'date_create' => time(),
	                        'date_update' => time(),
	                    );
	                    $ims->db->do_insert("user_recommend_log", $recommend_log);
	                }elseif(!empty($deeplink_code)){
	                	//deeplink_code
	                    $deeplink_user = $ims->db->load_row('user_deeplink', 'is_show = 1 and id = '.$deeplink_code, 'user_id, short_code');
	                    if($deeplink_user){
	                        $recommend_log = array(
	                            'type' => 'deeplink',
	                            'recommend_user_id' => $deeplink_user['user_id'],
	                            'recommend_link' => $deeplink_user['short_code'],
	                            'deeplink_id' => $deeplink_code,
	                            'referred_user_id' => $arr_in['user_id'],
	                            'referred_full_name' => $arr_in["full_name"],
	                            'referred_phone' => $arr_in["phone"],
	                            'referred_email' => $arr_in["email"],
	                            'is_show' => 1,
	                            'date_create' => time(),
	                            'date_update' => time(),
	                        );
	                        $ims->db->do_insert("user_recommend_log", $recommend_log);
	                    }
	                }
	                if($ims->setting['user']['signup_type'] == 1){
	                	require_once '../../library/phpmailer/class.phpmailer.php';
	                	$mail_arr_key = array(
							'{full_name}',
							'{username}',
							'{password}',
							'{link_active}',
							'{domain}',
						);
						$mail_arr_value = array(
							$arr_in["full_name"],
							$arr_in['username'],
							$arr_in['password'],
							'<a href="'.$ims->site_func->get_link ('user', $ims->setting['user']["active_link"])."?code=".$arr_in["user_code"].'">'.$ims->site_func->get_link ('user', $ims->setting['user']["active_link"])."?code=".$arr_in["user_code"].'</a>',
							$ims->conf['rooturl'],
						);
						//send to customer
						$ok = $ims->func->send_mail_temp ('signup-'.$ims->setting['user']['signup_type'], $arr_in["email"], $ims->conf['email'], $mail_arr_key, $mail_arr_value);						
	                }	                
					$token = $this->createTokenLogin($arr_in["user_id"], "", $device_token, $device_name);
					if($ims->setting['user']['signup_type'] == 2){
	                	$token = '';
	                }
					$array = array(
						"code" => 200,
	    				"message" => $ims->lang['api']['error_signupUser_0'],
		        		"token" => $token,
		        		"type" => $signup,
		        	);
					$this->response(200, $array);
	            }else{
					$this->response(422, "", 422 , $ims->lang['api']['error_signupUser_3']);
	            }
			}else{
				$this->response(422, "", 422 , $ims->lang['api']['error_signupUser_3']);
			}
		} else {
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}


	/*
		* Đăng nhập facebook
		* Method: POST
		* Content-Type: application/x-www-form-urlencoded
		* Body: fbID:---
				info:{JSON}
	*/
	function loginFb(){
		global $ims;

		if ($ims->method == 'POST'){
			$this->loginWithSocial("facebook");
		} else {
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}


	/*
		* Đăng nhập google
		* Method: POST
		* Content-Type: application/x-www-form-urlencoded
		* Body: ggID:---
				info:{JSON}
	*/
	function loginGg(){
		global $ims;

		if ($ims->method == 'POST'){
			$this->loginWithSocial("google");
		}else {
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}


	/*
		* Đăng nhập apple
		* Method: POST
		* Content-Type: application/x-www-form-urlencoded
		* Body: apID:---
				info:{JSON}
	*/
	function loginAp(){
		global $ims;

		if ($ims->method == 'POST'){
			$this->loginWithSocial("apple");
		}else {
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}


	/*
		* Cập nhật tài khoản
		* Method: POST
		* Content-Type: application/x-www-form-urlencoded
		* Body: full_name: ---
				phone: --- 
				address: --- 
				province: --- 
				district: --- 
				ward: --- 
	*/
	function updateUser(){
		global $ims;

		if ($ims->method == 'POST'){
			$infoUser = $this->check_token_user();

            $input = $ims->post;
            $arr_in = array();
            if(isset($_FILES['picture']) && $_FILES['picture']['error'] == 0){
	            $folder_upload = "user/".$infoUser['folder_upload'].'/'.date('Y',time()).'_'.date('m',time());
	            $out_pic = array();
	            $out_pic = $this->upload_image($folder_upload,'picture');
	            if($out_pic['ok'] ==1){
	                $arr_in['picture'] = $out_pic['url_picture'];
	            }
	        }else{
				$arr_in["full_name"]    = $ims->func->if_isset($input['full_name']);
				$arr_in["phone"]    	= $ims->func->if_isset($input['phone']);
				$arr_in["address"]    	= $ims->func->if_isset($input['address']);
				$arr_in["province"]    	= $ims->func->if_isset($input['province']);
				$arr_in["district"]    	= $ims->func->if_isset($input['district']);
				$arr_in["ward"]    		= $ims->func->if_isset($input['ward']);
				$arr_in["birthday"]     = isset($input['birthday']) ? $this->time_str2int($input['birthday'], 'd/m/Y') : '';
				$arr_in["gender"]    	= $ims->func->if_isset($input['gender']);
	        }
            $arr_in["date_update"]  = time();
            $ok = $ims->db->do_update("user", $arr_in, ' user_id="'.$infoUser['user_id'].'" ');
			if($ok) {
				if (isset($arr_in['picture'])) {
                    $arr_in['picture'] = $ims->func->get_src_mod($arr_in['picture']);
				}
				$array = array(
					"code" => 200,
				    "message" => $ims->lang['api']['error_updateUser_0'],
	        		"data" => $arr_in
	        	);
				$this->response(200, $array);
            }else{
            	$this->response(400, "", 400 , $ims->lang['api']['error_updateUser_2']);
            }
		} else {
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}


	/*
		* Tạo sổ địa chỉ
		* Method: POST
		* Content-Type: application/x-www-form-urlencoded
		* Body: full_name: ---
				phone: --- 
				email: --- 
				address: --- 
				province: --- 
				district: --- 
				ward: --- 
				is_default: --- 
	*/
	function addAddressBook(){
		global $ims;

		if ($ims->method == 'POST'){
			$infoUser = $this->check_token_user();
			$max = 1;
			$arr_address = $ims->func->unserialize($infoUser['arr_address_book']);
			if (!empty($arr_address)) {
				$i = 1;
				$arr_in = array();
				$arr_address_book = array();
				foreach ($arr_address as $address) {
					$arr_ad = array();
					$arr_ad["id"] 		  = $i;
					$arr_ad["full_name"]  = $ims->func->if_isset($address['full_name']);
					$arr_ad["phone"] 	  = $address["phone"];
					$arr_ad["email"] 	  = $address["email"];
					$arr_ad["address"]    = $address["address"];
					$arr_ad["province"]   = $address["province"];
					$arr_ad["district"]   = $address["district"];
					$arr_ad["ward"]		  = $address["ward"];	
					if (isset($ims->post['is_default']) && $ims->post['is_default'] == 1) {
						$arr_ad["is_default"] = 0;
					} else{
						$arr_ad["is_default"] = $address["is_default"];
					}	
					$arr_address_book[] = $arr_ad;
					$i++;
				}
				$max = $i;
				$arr_ad["id"] 		  = $max;
				$arr_ad["full_name"]  = $ims->func->if_isset($ims->post['full_name']);
				$arr_ad["phone"] 	  = $ims->func->if_isset($ims->post['phone']);
				$arr_ad["email"] 	  = $ims->func->if_isset($ims->post['email']);
				$arr_ad["address"]    = $ims->func->if_isset($ims->post['address']);
				$arr_ad["province"]   = $ims->func->if_isset($ims->post['province']);
				$arr_ad["district"]   = $ims->func->if_isset($ims->post['district']);
				$arr_ad["ward"]		  = $ims->func->if_isset($ims->post['ward']);
				$arr_ad["is_default"] = $ims->func->if_isset($ims->post['is_default'], 0);
				$arr_address_book[]   = $arr_ad;
				$arr_in = array();
				$arr_in["arr_address_book"] = serialize($arr_address_book);
				$ok = $ims->db->do_update('user', $arr_in, ' user_id="'.$infoUser['user_id'].'" ');
				if ($ok) {
					// cập nhật0 sổ địa chỉ thành công.
					$array = array(
						"code" => 200,
					    "message" => $ims->lang['api']['success']
		        	);
		        	$this->response(200, $array);
				}
			} else{
				$arr_ad["id"] 		  = $max;
				$arr_ad["full_name"]  = $ims->func->if_isset($ims->post['full_name']);
				$arr_ad["phone"] 	  = $ims->func->if_isset($ims->post['phone']);
				$arr_ad["email"] 	  = $ims->func->if_isset($ims->post['email']);
				$arr_ad["address"]    = $ims->func->if_isset($ims->post['address']);
				$arr_ad["province"]   = $ims->func->if_isset($ims->post['province']);
				$arr_ad["district"]   = $ims->func->if_isset($ims->post['district']);
				$arr_ad["ward"]		  = $ims->func->if_isset($ims->post['ward']);
				$arr_ad["is_default"] = $ims->func->if_isset($ims->post['is_default'], 0);
				$arr_address_book[$arr_ad["id"]] = $arr_ad;
				$arr_in = array();
				$arr_in["arr_address_book"] = serialize($arr_address_book);
				$ok = $ims->db->do_update('user', $arr_in, ' user_id="'.$infoUser['user_id'].'" ');
				if ($ok) {
					// Khởi tạo sổ địa chỉ thành công.
					$array = array(
						"code" => 200,
					    "message" => $ims->lang['api']['success'],
		        	);
		        	$this->response(200, $array);
				}
			}
		} else {
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}


	/*
		* Cập nhật sổ địa chỉ
		* Method: POST
		* Content-Type: application/x-www-form-urlencoded
		* Body: full_name: ---
				phone: --- 
				email: --- 
				address: --- 
				province: --- 
				district: --- 
				ward: --- 
				is_default: --- 
	*/
	function updateAddressBook(){
		global $ims;

		if ($ims->method == 'POST'){
			$infoUser = $this->check_token_user();
			$max = 1;
			$arr_address = $ims->func->unserialize($infoUser['arr_address_book']);
			$id = $ims->func->if_isset($ims->get['id'], 0);
			if (!empty($arr_address) && $id > 0) {
				$i = 1;
				$arr_in = array();
				$arr_address_book = array();
				foreach ($arr_address as $address) {
					if ($address['id'] == $id) {
						$arr_ad["id"] 		  = $address['id'];
						$arr_ad["full_name"]  = $ims->func->if_isset($ims->post['full_name']);
						$arr_ad["phone"] 	  = $ims->func->if_isset($ims->post['phone']);
						$arr_ad["email"] 	  = $ims->func->if_isset($ims->post['email']);
						$arr_ad["address"]    = $ims->func->if_isset($ims->post['address']);
						$arr_ad["province"]   = $ims->func->if_isset($ims->post['province']);
						$arr_ad["district"]   = $ims->func->if_isset($ims->post['district']);
						$arr_ad["ward"]		  = $ims->func->if_isset($ims->post['ward']);
						$arr_ad["is_default"] = $ims->func->if_isset($ims->post['is_default'], 0);
						$arr_address_book[] = $arr_ad;
					}else{
						if (isset($ims->post['is_default']) && $ims->post['is_default'] == 1) {
							$address["is_default"] = 0;
						}
						$arr_address_book[] = $address;
					}
				}
				$arr_in["arr_address_book"] = serialize($arr_address_book);
				$ok = $ims->db->do_update('user', $arr_in, ' user_id="'.$infoUser['user_id'].'" ');
				if ($ok) {
					// Khởi tạo sổ địa chỉ thành công.
					$array = array(
						"code" => 200,
					    "message" => $ims->lang['api']['success']
		        	);
		        	$this->response(200, $array);
				}
			} else{
				$this->response(400, "", 400 , $ims->lang['api']['error_getPriceShipping_4_4']);
			}
		} else {
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}


	/*
		* Xóa sổ địa chỉ
		* Method: POST
		* Content-Type: application/x-www-form-urlencoded
		* Body: full_name: ---
				phone: --- 
				email: --- 
				address: --- 
				province: --- 
				district: --- 
				ward: --- 
				is_default: --- 
	*/
	function deleteAddressBook(){
		global $ims;

		if ($ims->method == 'GET'){
			$infoUser = $this->check_token_user();
			$max = 1;
			$id = $ims->func->if_isset($ims->get['id'], 0);
			$arr_address = $ims->func->unserialize($infoUser['arr_address_book']);
			if (!empty($arr_address)) {
				$arr_in = array();
				$arr_address_book = array();
				foreach ($arr_address as $address) {
					$arr_ad = array();
					$arr_ad["id"] 		  = $address['id'];
					$arr_ad["full_name"]  = $address['full_name'];
					$arr_ad["phone"] 	  = $address["phone"];
					$arr_ad["email"] 	  = $address["email"];
					$arr_ad["address"]    = $address["address"];
					$arr_ad["province"]   = $address["province"];
					$arr_ad["district"]   = $address["district"];
					$arr_ad["ward"]		  = $address["ward"];	
					if (isset($ims->post['is_default']) && $ims->post['is_default'] == 1) {
						$arr_ad["is_default"] = 0;
					} else{
						$arr_ad["is_default"] = $address["is_default"];
					}	
					if ($address['id'] == $id) { }else {
						$arr_address_book[] = $arr_ad;
					}
				}
				$arr_new = array();
				foreach ($arr_address_book as $key => $value) {
					$arr_new[] = $value;
				}
				$arr_in["arr_address_book"] = serialize($arr_new);
				$ok = $ims->db->do_update('user', $arr_in, ' user_id="'.$infoUser['user_id'].'" ');
				if ($ok) {
					// Khởi tạo sổ địa chỉ thành công.
					$array = array(
						"code" => 200,
					    "message" => $ims->lang['api']['success']
		        	);
		        	$this->response(200, $array);
				}
			} else{
				$this->response(400, "", 400 , $ims->lang['api']['error_getPriceShipping_4_4']);
			}
		} else {
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}


	/*
		* Lấy danh sách sổ địa chỉ
		* Method: GET
		* Content-Type: application/x-www-form-urlencoded
		* Body: 
	*/
	function getAddressBook(){
		global $ims;

		if ($ims->method == 'GET'){
			$infoUser = $this->check_token_user();
			$max = 1;
			$address_book = $ims->func->unserialize($infoUser['arr_address_book']);
			$arr = array();
			foreach ($address_book as $key => $value) {
				$arr[$key] = $value;
				$arr[$key]['ward_text'] 	= $this->location_name('ward', $value['ward']);
				$arr[$key]['province_text'] = $this->location_name('province', $value['province']);
				$arr[$key]['district_text'] = $this->location_name('district', $value['district']);
			}
			$output = array();
			if (!empty($arr)) {
				foreach ($arr as $key => $value) {
					$output[] = $value;
				}
			}
			$array = array(
				"code" => 200,
			    "message" => $ims->lang['api']['success'],
				"data" => $output
        	);
        	$this->response(200, $array);
		} else {
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}


	/*
		* Thay đổi mật khẩu
		* Method: POST
		* Content-Type: application/x-www-form-urlencoded
		* Body: password_cur: ---
				password: --- 
				re_password: --- 
	*/
	function updatePassword(){
		global $ims;

		if ($ims->method == 'POST'){
			$infoUser = $this->check_token_user();

            $password_cur = $ims->func->if_isset($ims->post['password_cur']);
            $password 	  = $ims->func->if_isset($ims->post['password']);
            $re_password  = $ims->func->if_isset($ims->post['re_password']);

            $arr_check = array();
            $arr_check['password_cur'] = $ims->func->md25($password_cur);
			$arr_check['password'] 	   = $ims->func->md25($password);
			$arr_check['re_password']  = $ims->func->md25($re_password);
			if($arr_check['password_cur'] != $infoUser['password']) {
				$this->response(400, "", 400 , $ims->lang['api']['error_updatePassword_4']);
			}
			if($arr_check['password'] != $arr_check['re_password']) {
				$this->response(400, "", 400 , $ims->lang['api']['error_updatePassword_5']);
			}
			if($arr_check['password']=='') {
				$this->response(400, "", 400 , $ims->lang['api']['error_updatePassword_6']);
			}

            $arr_in = array();
			$arr_in["password"] = $arr_check['password'];
            $arr_in["date_update"] = time();
            $ok = $ims->db->do_update("user", $arr_in, ' user_id="'.$infoUser['user_id'].'" ');
			if($ok) {
				$this->response(200, "", 200 , $ims->lang['api']['error_updatePassword_0']);
            }else{
				$this->response(400, "", 400 , $ims->lang['api']['error_updatePassword_2']);
            }
		} else {
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}

	/*
		* Thay đổi mật khẩu
		* Method: POST
		* Content-Type: application/x-www-form-urlencoded
		* Body: password_cur: ---
				password: --- 
				re_password: --- 
	*/
	function updatePasswordOTP(){
		global $ims;

		if ($ims->method == 'POST'){
			$phone 		  = $ims->func->if_isset($ims->post['phone']);
            $otp 		  = $ims->func->if_isset($ims->post['otp']);
            $password 	  = $ims->func->if_isset($ims->post['password']);
            $re_password  = $ims->func->if_isset($ims->post['re_password']);
            if(empty($otp)){
				$this->response(400, "", 400, 'Mã OTP không hợp lệ');
			}
			$infoUser = $ims->db->load_row('user','is_show=1 and phone="'.$phone.'"');			
			if(empty($infoUser)){
				$this->response(400, "", 400, $ims->lang['api']['error_fogetPassword_2_1']);
			}
            $arr_check = array();
            $arr_check['otp'] 			= $otp;
			$arr_check['password'] 	   	= $ims->func->md25($password);
			$arr_check['re_password']  	= $ims->func->md25($re_password);
			if($arr_check['otp'] != $infoUser['otp']) {				
				$this->response(400, "", 400, 'OTP không hợp lệ');
			}
			if($arr_check['password'] != $arr_check['re_password']) {
				$this->response(400, "", 400 , $ims->lang['api']['error_updatePassword_5']);
			}
			if($arr_check['password'] == '') {
				$this->response(400, "", 400 , $ims->lang['api']['error_updatePassword_6']);
			}

            $arr_in = array();
			$arr_in["password"] = $arr_check['password'];
            $arr_in["date_update"] = time();
            $ok = $ims->db->do_update("user", $arr_in, ' user_id="'.$infoUser['user_id'].'" ');
			if($ok) {
				$this->response(200, "", 200 , $ims->lang['api']['error_updatePassword_0']);
            }else{
				$this->response(400, "", 400 , $ims->lang['api']['error_updatePassword_2']);
            }
		} else {
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}

	/*
		* Đăng xuất
		* Method: GET
		* Content-Type: application/x-www-form-urlencoded
		* Body: 
	*/
	function logoutUser(){
		global $ims;

		if ($ims->method == 'GET'){
			$infoUser = $this->check_token_user();
            $token = $ims->func->if_isset($ims->post['token'], 0);
			$list_token = explode(',', $infoUser["token_login"]);
			if (!empty($list_token)) {
				foreach ($list_token as $key => $value) {
					if ($token == $value) {
						unset($list_token[$key]);
					}
				}
			}
			$token_new = implode(',', $list_token);
			$ims->db->query('UPDATE `user` SET token_login="'.$token_new.'" WHERE user_id="'.$infoUser['user_id'].'" ');
			$ims->db->query("UPDATE `user_login_log` SET is_show=0 WHERE token_login='".$token."'");
			$array = array(
        		"code" => 200,
	    		"message" => $ims->lang['api']['success']
        	);
		}  else {
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}


	/*
		* Đánh giá sản phẩm
		* Method: POST
		* Content-Type: application/x-www-form-urlencoded
		* Body: type: product
				item_id: --- 
	*/
	function ratingProduct(){
		global $ims;

		if ($ims->method == 'POST'){
	        $arr = array();

	        $user   = $ims->func->if_isset($ims->get['user']);
	        $mod    = $ims->func->if_isset($ims->post['type'], 'product');
	        $name   = $ims->func->if_isset($ims->post['full_name']);
	        $phone  = $ims->func->if_isset($ims->post['phone']);
	        $content= $ims->func->if_isset($ims->post['content']);
	        $email  = $ims->func->if_isset($ims->post['email']);
	        $rate   = $ims->func->if_isset($ims->post['rate'], 0);
			$id     = $ims->func->if_isset($ims->post['item_id'], 0);

			$infoUser = $this->check_token_user();
			// $arr_rate = $ims->db->load_row('shared_rate', ' type="'.$mod.'" AND type_id="'.$id.'" AND user_id="'.$infoUser['user_id'].'" AND is_show=1 ');
			$arr_rate = $ims->db->load_row('shared_comment', ' type="'.$mod.'" AND type_id="'.$id.'" AND user_id="'.$infoUser['user_id'].'" AND is_show=1 ');

			// check mua sản phẩm này chưa
			$check = 0;
			$order = $ims->db->load_row_arr('product_order', ' is_show=1 AND is_status="'.$ims->site_func->getStatusOrder(6).'" AND user_id="'.$infoUser['user_id'].'" ');
			if (!empty($order)) {
				foreach ($order as $key => $value) {
					$detail = $ims->db->load_row_arr('product_order_detail', ' order_id="'.$value['order_id'].'" ');
					foreach ($detail as $k => $v) {
						if ($v['type_id'] == $id) {
							$check = 1;
						}
					}
				}
			}
			if($check == 0){
	        	// Báo lỗi chưa mua sản phẩm này
	        	$this->response(400, "", 400 , $ims->lang['api']['error_ratingProduct_3']);
			}

	        if (!empty($arr_rate)) {
	        	// Báo lỗi đã đánh giá sản phẩm này
	        	$this->response(400, "", 400 , $ims->lang['api']['error_ratingProduct_2']);
	        }else{
	        	// Tạo đánh giá mới
	        	$arr_ins = array();
	        	$arr_ins['picture'] = '';
			    // print_arr($arr_ins);
	        	if(isset($_FILES['picture'])) {
			        $num_files = count($_FILES['picture']);
			        $arr_tmp = array();
		            $folder_upload = 'product/rating/'.date('Y',time()).'_'.date('m',time());
			        for($i=0; $i < $num_files; $i++) {
	        			$out_pic = array();
			        	$out_pic = $this->upload_image_multi($folder_upload,'picture', $i);
					    if($out_pic['ok'] == 1){
			                $arr_tmp[] = $out_pic['url_picture'];
			            }else{
	        				$this->response(400, "", 400 , $out_pic['mess']);
			            }
				    }
				    if (!empty($arr_tmp)) {
        				$arr_ins['picture'] = $ims->func->serialize($arr_tmp);
				    }
				}
				$arr_ins['item_id'] 	= $ims->db->getAutoIncrement('shared_comment');
        		$arr_ins['type_id'] 	= $id;
        		$arr_ins['type'] 		= $mod;
        		$arr_ins['full_name'] 	= $name;
        		$arr_ins['phone'] 		= $phone;
        		$arr_ins['email'] 		= $email;
        		$arr_ins['content'] 	= $content;
        		$arr_ins['rate'] 		= $rate;
        		$arr_ins['user_id'] 	= $infoUser['user_id'];
        		$arr_ins['is_show'] 	= 0;
        		$arr_ins['date_create'] = time();
        		$arr_ins['date_update'] = time();
        		// $ok = $ims->db->do_insert('shared_rate', $arr_ins);
        		$ok = $ims->db->do_insert('shared_comment', $arr_ins);
        		if ($ok) {
        			// Cập nhật lại cho sản phẩm
        			// $list_rate = $ims->db->load_item_arr('shared_rate', ' type="'.$mod.'" AND type_id="'.$id.'" AND is_show=1 ', 'rate');
        			$list_rate = $ims->db->load_item_arr('shared_comment', ' type="'.$mod.'" AND type_id="'.$id.'" AND is_show=1 ', 'rate');
        			$num = 0;
        			$arr_up = array();
        			$total_rate = 0;
        			if (!empty($list_rate)) {
        				$num = count($list_rate);
        				foreach ($list_rate as $key => $value) {
        					$arr_up['num_rate']++;
        					$total_rate += $value['rate'];
        				}
        			}
        			if($total_rate != 0){
			            $average_rating = round($total_rate/$num, 1);
			            $arr_up['num_rate'] = $average_rating;
			        }
        			// if ($arr_up['num_rate'] > 0) {
	        		// 	$arr_up['average_rating'] = round(2*($total_rate/$arr_up['num_rate']))/2;
        			// }

        			$ims->db->do_update('product', $arr_up, ' item_id="'.$id.'" ');

        			$array = array(
						"code" => 200,
					    "message" => $ims->lang['api']['error_checkFavorite_0'],
						"status" => 'add',
						"data" => $arr_ins
		        	);
					$this->response(200, $array);
        		}
	        }
		}else {
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}


	/*
		* Lấy đánh giá theo sản phẩm
		* Method: GET
		* Content-Type: application/x-www-form-urlencoded
	*/
	function getRatingProduct(){
		global $ims;

		if ($ims->method == 'GET'){

			$user      	  = isset($ims->get['user']) ? $ims->get['user']: '';
			$item_id      = isset($ims->get['item_id']) ? $ims->get['item_id'] : 0;
			$p 		      = isset($ims->get['p']) ? $ims->get['p'] : 1;			
			$numshow  	  = isset($ims->get['numshow']) ? $ims->get['numshow']:0;
			$n = 10;
			if ($numshow>0) {
				$n = $numshow;
			}
			if ($numshow>500) {
				$n = 10;
			}
			$where = '';

			if ($user != "") {
				$infoUser = $this->check_token_user();
		        $where = ' user_id="'.$infoUser['user_id'].'" AND type="product" AND ';
			}else{
		        if ($item_id>0) {
		        	$where = ' type_id="'.$item_id.'" AND type="product" AND ';
		        }else{
		        	$this->response(400, "", 400 , $ims->lang['api']['error_getRatingProduct_2']);
		        }
			}

			// $res_num = $ims->db->query("SELECT id from shared_rate where ".$where." is_show=1 AND lang='".$ims->conf['lang_cur']."'");
			$res_num = $ims->db->query("SELECT id from shared_comment where ".$where." is_show=1 AND lang='".$ims->conf['lang_cur']."'");
	        $num_total = $ims->db->num_rows($res_num);
	        $num_items = ceil($num_total / $n);
	        if ($p > $num_items)
	            $p = $num_items;
	        if ($p < 1)
	            $p = 1;
	        $start = ($p - 1) * $n;

			// $arr = $ims->db->load_item_arr(
			// 	'shared_rate',
			// 	$where .'is_show=1 AND lang="'.$ims->conf['lang_cur'].'" ORDER BY show_order DESC, date_update DESC LIMIT '.$start.', '.$n.'', 
			// 	'*'
			// );
			$arr = $ims->db->load_item_arr(
				'shared_comment',
				$where .'is_show=1 AND lang="'.$ims->conf['lang_cur'].'" ORDER BY show_order DESC, date_update DESC LIMIT '.$start.', '.$n.'', 
				'*'
			);
			if (!empty($arr)) {
				foreach ($arr as $key => $value) {
					// $picture = $value['picture'];
					// if ($picture!='') {
					// 	$picture = unserialize($picture);
			  //           foreach ($picture as $k_pic => $v_pic) {
					// 		$picture[$k_pic] = $ims->func->get_src_mod($v_pic, 400, 400, 1, 0, array('fix_width' => 1));
			  //           }
					// 	$arr[$key]['picture'] = $picture;
					// }else{
					// 	$arr[$key]['picture'] = array();
					// }
					$arr[$key]['product'] = $ims->db->load_row('product', $ims->conf['where_lang'].' AND item_id="'.$value['type_id'].'" ', 'item_id, title, picture, combo_id');
					$arr[$key]['is_combo'] = !empty($arr[$key]['product']['combo_id'])?true:false;
					$arr[$key]['product']['picture'] = $ims->func->get_src_mod($arr[$key]['product']['picture']);

					$arr[$key]['avatar'] = $this->get_avatar($value['full_name']);
					$arr[$key]['avatar'] = substr($arr[$key]['avatar'], -2);
					unset($arr[$key]['id']);
					unset($arr[$key]['type']);
					unset($arr[$key]['type_id']);
					unset($arr[$key]['title']);
					unset($arr[$key]['user_id']);
					unset($arr[$key]['show_order']);
					unset($arr[$key]['is_show']);
					unset($arr[$key]['date_update']);
					unset($arr[$key]['lang']);
				}
			}
			$array = array(
				"code" => 200,
			    "message" => $ims->lang['api']['success'],
	    		"total" => $num_total,
	    		"total_page" => $num_items,
	    		"page" => $p,
	    		"data" => $arr,
	    	);
			$this->response(200, $array);


		} else {
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}



	/*
		* Lấy danh sách yêu thích
		* Method: GET
	*/
	function getFavorite(){
		global $ims;

		if ($ims->method == 'GET'){
			$infoUser = $this->check_token_user();
	        $arr = array();
	        if(isset($infoUser['list_favorite']) && $infoUser['list_favorite'] != ''){
	            $list_favorite = unserialize($infoUser['list_favorite']);
	            $arr_tmp = array();
	            foreach ($list_favorite as $key => $value) {
	                $arr_tmp[] = $value["id"];
	            }
	            $where = ' AND FIND_IN_SET(item_id,"'.implode(',', $arr_tmp).'") ORDER BY FIELD(item_id,'.implode(',', $arr_tmp).') DESC';		        
		        $arr_favorite = $ims->db->load_item_arr('product', 'is_show=1 AND lang="'.$ims->conf['lang_cur'].'"'.$where,'item_id, title, price, price_buy, price_promotion, percent_discount, picture, combo_id');		        
		        if (!empty($arr_favorite)) {
		            foreach ($arr_favorite as $k => $v) {
                    	$arr_favorite[$k]['is_combo'] = !empty($v['combo_id'])?true:false;
                    	if ($v['price_promotion']>0) {
							$arr_favorite[$k]['price_buy'] = $v['price_promotion'];
						}
						unset($arr_favorite[$k]['price_promotion']);
						unset($arr_favorite[$k]['combo_id']);
						$arr_favorite[$k]['rating'] = $this->getRatingByProduct('product', $v['item_id'], 'average');
                    	$arr_favorite[$k]['picture'] = $ims->func->get_src_mod($v['picture']);
		            }
		            $arr = $arr_favorite;
		        }
		    }
        	$array = array(
				"code" => 200,
			    "message" => $ims->lang['api']['success'],
	        	'data' => $arr
        	);
			$this->response(200, $array);
		} else {
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}


	/*
		* Yêu thích sản phẩm
		* Method: POST
		* Content-Type: application/x-www-form-urlencoded
		* Body: type: product
				item_id: --- 
	*/
	// function checkFavorite(){
	// 	global $ims;

	// 	if ($ims->method == 'POST'){
	// 		$this->load_language('global');
	// 		$this->setting('user');

	// 		$infoUser = $this->check_token_user();
 //            $mod  = isset($ims->post['type']) ? $ims->post['type'] : '';
	// 		$id   = isset($ims->post['item_id']) ? $ims->post['item_id'] : 0;

 //        	$col = array();
 //        	$arr_favorite = $ims->db->load_row_arr('shared_favorite', ' type="'.$mod.'" AND user_id="'.$infoUser['user_id'].'" AND is_show=1 ');
 //        	if (!empty($arr_favorite)) {
 //        		if(count($arr_favorite) >= $ims->setting['user']['max_favorite']){
 //        			$this->response(200, "", 200, $ims->lang['api']['error_checkFavorite_2_1']);
 //                }else{
 //                	$check_isset = 0;
 //                	foreach ($arr_favorite as $key => $value) {
 //                		if ($value['type_id'] == $id) {
 //                			$check_isset = $value['id'];
 //                		}
 //                	}
 //                	if ($check_isset > 0) {
 //                		// Xóa yêu thích
 //                		$ok = $ims->db->query('DELETE FROM `shared_favorite` WHERE id="'.$check_isset.'" ');
 //                		if ($ok) {
	// 	        			$array = array(
	// 							"code" => 200,
	// 						    "message" => $ims->lang['api']['success'],
	// 							"status" => 'remove'
	// 			        	);
	// 						$this->response(200, $array);
	// 	        		}
 //                	}else{
 //                		// Thêm yêu thích mới
 //                		$arr_ins 				= array();
	// 	        		$arr_ins['type_id'] 	= $id;
	// 	        		$arr_ins['type'] 		= $mod;
	// 	        		$arr_ins['user_id'] 	= $infoUser['user_id'];
	// 	        		$arr_ins['date_create'] = time();
	// 	        		$arr_ins['date_update'] = time();
	// 	        		$ok = $ims->db->do_insert('shared_favorite', $arr_ins);
	// 	        		if ($ok) {
	// 	        			$array = array(
	// 							"code" => 200,
	// 						    "message" => $ims->lang['api']['success'],
	// 							"status" => 'add',
	// 							"data" => $arr_ins
	// 			        	);
	// 						$this->response(200, $array);
	// 	        		}
 //                	}
 //                }
 //        	}else{
 //        		// Thêm yêu thích mới
 //        		$arr_ins 				= array();
 //        		$arr_ins['type_id'] 	= $id;
 //        		$arr_ins['type'] 		= $mod;
 //        		$arr_ins['user_id'] 	= $infoUser['user_id'];
 //        		$arr_ins['date_create'] = time();
 //        		$arr_ins['date_update'] = time();
 //        		$ok = $ims->db->do_insert('shared_favorite', $arr_ins);
 //        		if ($ok) {
 //        			$array = array(
	// 					"code" => 200,
	// 				    "message" => $ims->lang['api']['success'],
	// 					"status" => 'add',
	// 					"data" => $arr_ins
	// 	        	);
	// 				$this->response(200, $array);
 //        		}
 //        	}
	// 	} else {
	// 		$this->response(405, "", 405, $ims->lang['api']['error_method']);
	// 	}
	// }
	function checkFavorite(){
		global $ims;

		if ($ims->method == 'POST'){
			$this->load_language('global');
			$this->setting('user');

			$infoUser = $this->check_token_user();
            $mod  = isset($ims->post['type']) ? $ims->post['type'] : '';
			$id   = isset($ims->post['item_id']) ? $ims->post['item_id'] : 0;
			$key_unset = -1;

        	$col = array();
        	$arr_favorite = $ims->func->unserialize($infoUser['list_favorite']);
        	if (!empty($arr_favorite)) {
        		if(count($arr_favorite) >= $ims->setting['user']['max_favorite']){
        			$this->response(200, "", 200, $ims->lang['api']['error_checkFavorite_2_1']);
                }else{                	
                	$arr_search = array();
	                foreach ($arr_favorite as $key => $object){
	                    if(array_search($mod, $object)){
	                        if(array_search($id, $object)){
	                            $arr_search = $arr_favorite[$key];
	                            $key_unset = $key;
	                        }
	                    }
	                }
                	if (count($arr_search)>0) {                		
                		// Xóa yêu thích
                		unset($arr_favorite[$key_unset]);
                		$col = array();
                    	$col['list_favorite'] = $ims->func->serialize($arr_favorite);
                    	$ok = $ims->db->do_update('user',$col , "user_id = '".$infoUser['user_id']."'");
                    	if ($ok) {
		        			$array = array(
								"code" => 200,
							    "message" => $ims->lang['api']['success'],
								"status" => 'remove',
								"data" => $arr_favorite
				        	);
							$this->response(200, $array);
		        		}
                	}else{
                		// Thêm yêu thích mới
                		$count = max(array_keys($arr_favorite)) + 1;
                        $arr_favorite[$count]['mod'] = $mod;
                        $arr_favorite[$count]['id'] = $id;
                        $col['list_favorite'] = $ims->func->serialize($arr_favorite);
                        $ok = $ims->db->do_update('user',$col , "user_id = '".$infoUser['user_id']."'");
                        if ($ok) {
		        			$array = array(
								"code" => 200,
							    "message" => $ims->lang['api']['success'],
								"status" => 'add',
								"data" => $arr_favorite
				        	);
							$this->response(200, $array);
		        		}
                	}
                }
        	}else{
        		// Thêm yêu thích mới
        		$arr_favorite[0]['mod'] = $mod;
                $arr_favorite[0]['id'] = $id;
                $col['list_favorite'] = $ims->func->serialize($arr_favorite);
        		$ok = $ims->db->do_update('user',$col , "user_id = '".$infoUser['user_id']."'");
        		if ($ok) {
        			$array = array(
						"code" => 200,
					    "message" => $ims->lang['api']['success'],
						"status" => 'add',
						"data" => $arr_favorite
		        	);
					$this->response(200, $array);
        		}
        	}
		} else {
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}

	/*
		* Sản phẩm mua sau
		* Method: POST
		* Content-Type: application/x-www-form-urlencoded
		* Body: type: product
				item_id: --- 
	*/ 
	function productForLater(){
		global $ims;

		if ($ims->method == 'POST'){
			$this->load_language('global');
			$this->setting('user');

			$infoUser  = $this->check_token_user();
			$act       = isset($ims->post['act']) ? $ims->post['act'] : 'add';
			$item_id   = isset($ims->post['item_id']) ? $ims->post['item_id'] : 0;
            $option_id = isset($ims->post['option_id']) ? $ims->post['option_id'] : '';

        	$col = array();
        	$arr_save = array();
        	if ($act == 'add') {
				$arr_tmp = $ims->func->unserialize($infoUser['list_save']);
				if (empty($arr_tmp)) {
					$arr_save[0]['item_id'] = $item_id;
					$arr_save[0]['id'] = $option_id;
				}else{
					$j = 0;
					$exist = 0;
					foreach ($arr_tmp as $key => $value) {
	                	if ($option_id != $value['id']) {
		                }else{
		                	$exist = 1;
		                }
						$arr_save[$j]['item_id'] = $value['item_id'];
	                    $arr_save[$j]['id'] = $value['id'];
	                    $j++;
					}
					if ($exist==0) {
						$arr_save[$j]['item_id'] = $item_id;
	                    $arr_save[$j]['id'] = $option_id;
					}
				}
	            $col['list_save'] = $ims->func->serialize($arr_save);  
        	}elseif ($act == 'del') {
        		$arr_tmp = $ims->func->unserialize($infoUser['list_save']);
				if (empty($arr_tmp)) {
					$array = array(
						"code" => 200,
					    "message" => "Danh sách rỗng"
		        	);
					$this->response(200, $array);
				}else{
					$j = 0;
					$exist = 0;
					foreach ($arr_tmp as $key => $value) {
	                	if ($item_id != $value['item_id']) {
							$arr_save[$j]['item_id'] = $value['item_id'];
		                    $arr_save[$j]['id'] = $value['id'];
		                    $j++;
		                }
					}
				}
	            $col['list_save'] = $ims->func->serialize($arr_save);
        	}
			$col['date_update'] = time();
			$ok = $ims->db->do_update('user', $col ,"user_id = '".$infoUser['user_id']."'");
			if($ok){
    			$array = array(
					"code" => 200,
				    "message" => $ims->lang['api']['success']
	        	);
				$this->response(200, $array);
			}
		} else {
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}


	/*
		* Sản phẩm đã xem
		* Method: POST
		* Content-Type: application/x-www-form-urlencoded
		* Body: type: product
				item_id: --- 
	*/ 
	function viewedProduct(){
		global $ims;

		if ($ims->method == 'POST'){
			$this->load_language('global');
			$this->setting('user');

			$infoUser  = $this->check_token_user();
			$item_id   = isset($ims->post['item_id']) ? $ims->post['item_id'] : 0;
			if ($item_id > 0) {
	        	$col = array();
	        	$arr_watched = array();
				$arr_tmp = $ims->func->unserialize($infoUser['list_watched']);
				if (empty($arr_tmp)) {
					$arr_watched[0]['id'] = $item_id;
					$arr_watched[0]['date_create'] = time();
				}else{
					$j = 0;
					$exist = 0;
					foreach ($arr_tmp as $key => $value) {
	                	if ($item_id != $value['id']) {
		                }else{
		                	$exist = 1;
		                }
						$arr_watched[$j]['id'] = $value['id'];
						$arr_watched[$j]['date_create'] = time();
	                    $j++;
					}
					if ($exist==0) {
						$arr_watched[$j]['id'] = $item_id;
						$arr_watched[$j]['date_create'] = time();
					}
				}
	            $col['list_watched'] = $ims->func->serialize($arr_watched);  
				$col['date_update'] = time();
				$ok = $ims->db->do_update('user', $col ,"user_id = '".$infoUser['user_id']."'");
				if($ok){
	    			$array = array(
						"code" => 200,
					    "message" => $ims->lang['api']['success']
		        	);
					$this->response(200, $array);
				}
			}else{
				$this->response(400, "", 400, $ims->lang['api']['error_data']);
			}
		} else {
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}


	/*
		* Lấy danh sách thông báo
		* Method: GET
		* Content-Type: application/x-www-form-urlencoded
		* Body: 
	*/
	function getNotification(){
		global $ims;

		if ($ims->method == 'GET'){
			$infoUser = $this->check_token_user();

			$p 		 = isset($ims->get['p']) ? $ims->get['p'] : 1;
			$numshow = isset($ims->get['numshow']) ? $ims->get['numshow']:0;
			$type 	 = isset($ims->get['type']) ? $ims->get['type']:'';
			$type_of = isset($ims->get['type_of']) ? $ims->get['type_of'] : '';
			$item_id = isset($ims->get['item_id']) ? $ims->get['item_id'] : '';

			$n = 20;
			if ($numshow>0) {
				$n = $numshow;
			}
			if ($numshow>500) {
				$n = 20;
			}

            $where = '';
            if ($type_of != '') {
            	if ($type_of == 'promotion') {
            		$where .= ' AND type_of="'.$type_of.'" ';
            	} elseif ($type_of == 'personal') {
            		$where .= " AND (FIND_IN_SET('".$infoUser['user_id']."', user_id)) AND type_of='normal' ";
            	} elseif ($type_of == 'system') {
            		$where .= " AND (type = 0) AND type_of='normal' ";
            	} elseif ($type_of == 'normal') {
            		$where .= " AND (type = 0 OR FIND_IN_SET('".$infoUser['user_id']."', user_id)) AND type_of='normal' ";
            	}
            }
            if ($item_id>0) {
            	$where .= ' AND item_id="'.$item_id.'" ';
            }

			$res_num = $ims->db->query("SELECT item_id FROM user_notification WHERE is_show=1 AND lang ='".$ims->conf['lang_cur']."' and (type=0 OR find_in_set('".$infoUser['user_id']."', user_id)) and find_in_set('".$infoUser['user_id']."',user_delete)<=0 and date_create >= '".$infoUser['date_create']."' ".$where." ");
	        $num_total = $ims->db->num_rows($res_num);
	        $num_items = ceil($num_total / $n);
	        if ($p > $num_items)
	            $p = $num_items;
	        if ($p < 1)
	            $p = 1;
	        $start = ($p - 1) * $n;

	        $readed = 0;
	        $reading = 0;
	       	// arr 
			$arr = $ims->db->load_item_arr('user_notification'," is_show = 1 AND lang ='".$ims->conf['lang_cur']."' and (type=0 OR find_in_set('".$infoUser['user_id']."', user_id)) and find_in_set('".$infoUser['user_id']."',user_delete)<=0  ".$where." and date_create >= '".$infoUser['date_create']."' ORDER BY find_in_set('".$infoUser['user_id']."',is_view) asc, date_create DESC LIMIT ".$start.",".$n." ", 'item_id,type_of,title,short,picture,content,is_view,type,user_id,date_create');
	        if (!empty($arr)) {
	           	foreach ($arr as $key => $row) {
	                $row['date_create_text'] = date('d/m/Y', $row['date_create']);
	                $row['user_id'] = explode(",", $row['user_id']);
	                if (!empty($row['is_view'])) {
	                    $row['is_view'] = explode(",", $row['is_view']);
	                    if (in_array($infoUser['user_id'], $row['is_view'])) {
	                        $row['status'] = "readed";
	                        $readed++;
	                    } else {
	                        $row['status'] = "reading";
	                        $reading++;
	                    }
	                } else {
	                    $row['status'] = "reading";
	                    $reading++;
	                }
	            	$row['content']  =  $ims->func->input_editor_decode($row['content']);
	            	$row['short']    =  $ims->func->short($row['short'], 500);
	            	if ($row["picture"] != "") {
						$row["picture"]  =  $ims->func->get_src_mod($row["picture"]);
	            	}
	            	$row['date_create'] = date('d/m/Y', $row['date_create']);
	                if ($row['type'] == 0 || in_array($infoUser['user_id'], $row['user_id'])) {
	                	unset($row['is_view']);
	                	unset($row['type']);
	                	unset($row['user_id']);
			            $arr[$key] = $row;
			        }			        
	            }
	        }
	        if ($item_id>0) {
            	$arr = $arr[0];
            }
	        if ($type != "") {
	        	$total_type = 0;
	        	if ($type == 'reading') {
	        		$total_type = $reading;
	        	}elseif ($type == 'readed') {
	        		$total_type = $readed;
	        	}
	        	$array = array(
					"code" => 200,
				    "message" => $ims->lang['api']['success'],
					"total" => $total_type,
	        	);
				$this->response(200, $array);
	        }
	        $array = array(
				"code" => 200,
			    "message" => $ims->lang['api']['success'],
				"total" => $num_total,
				'total_page' => $num_items,
	    		"page" => $p,
				"data" => $arr
        	);
			$this->response(200, $array);
		} else {
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}


	/*
		* Cập nhật thông báo
		* Method: POST
		* Content-Type: application/x-www-form-urlencoded
		* Body: 
	*/
	function updateNotification(){
		global $ims;

		if ($ims->method == 'POST'){
			$act = $ims->func->if_isset($ims->get['act']);
			$item_id = $ims->func->if_isset($ims->post["item_id"],0);
			$where = '';
			if(!empty($ims->post["item_id"])){
				$where .= " and find_in_set(item_id,'".$item_id."') ";
			}
			$infoUser = $this->check_token_user();
			$arr = $ims->db->load_item_arr('user_notification'," is_show = 1 AND lang ='".$ims->conf['lang_cur']."' and (type=0 OR find_in_set('".$infoUser['user_id']."', user_id)) ".$where." ORDER BY date_create DESC ",'item_id,title,is_view,type,user_id,date_create');
			if (!empty($arr)) {
				$ok = 0;
	           	foreach ($arr as $key => $row) {	
	           		$is_view = '';
	           		if ($act == 'readed') { // cập nhật user đã đọc cho id
	           			if ($row['item_id'] == $item_id) {
			           		$arr_user_view = array();
					        if ($row['is_view'] == '') {
					            $is_view = $infoUser['user_id'];
					            $ok = $ims->db->do_update("user_notification", array("is_view" => $is_view), "item_id='".$row['item_id']."'");
					        } else {
					            $arr_user_view = explode(',', $row['is_view']);
					            if (in_array($infoUser['user_id'], $arr_user_view) == false) {
					                $is_view = $row['is_view'].','.$infoUser['user_id'];
					                $ok = $ims->db->do_update("user_notification", array("is_view" => $is_view), "item_id='".$row['item_id']."'");
					            }
					        }
	           			}
			    	}elseif($act == 'readed_all'){ // cập nhật user đã đọc tất cả (item_id rỗng)
			    		if ($row['is_view'] == '') {
				            $is_view = $infoUser['user_id'];
				            $ok = $ims->db->do_update("user_notification", array("is_view" => $is_view), "item_id='".$row['item_id']."'");
				        } else {
				            $arr_user_view = explode(',', $row['is_view']);
				            if (in_array($infoUser['user_id'], $arr_user_view) == false) {
				                $is_view = $row['is_view'].','.$infoUser['user_id'];
				                $ok = $ims->db->do_update("user_notification", array("is_view" => $is_view), "item_id='".$row['item_id']."'");
				            }
				        }
			    	}else{ // cập nhật user chưa đọc cho id
		                if ($row['type'] == 0 || in_array($infoUser['user_id'], $row['user_id'])) {
			           		if (isset($row['is_view']) && $row['is_view']!='') {
			                    $arr_is_view = explode(',', $row['is_view']);
			                    $arr_tmp = array();			                    
			                    foreach ($arr_is_view as $k => $v) {
			                        if ($v!=$infoUser['user_id']) {			                        	
			                           	$arr_tmp[] = $v;
			                        }
			                    }			                    
			                    $is_view = implode(',', $arr_tmp);			                    
			                    $ok = $ims->db->do_update("user_notification", array("is_view" => $is_view), "item_id='".$row['item_id']."'");
			                }
				        }
			        }		        	
	            }
	            if ($ok) {
	            	$this->response(200, "", 200 , $ims->lang['api']['error_updateNotification_0']);
	            }else{
	            	$array = array(
						"code" => 200,
				    	"message" => $ims->lang['api']['error_updateNotification_2'],
						"data" => $arr
		        	);
					$this->response(200, $array);
	            }
	        }else{
		        $array = array(
					"code" => 200,
			    	"message" => $ims->lang['api']['error_updateNotification_2'],
					'data' => $arr
	        	);
				$this->response(200, $array);
	        }
		} else {
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}

	/*
		* Xóa thông báo
		* Method: POST
		* Content-Type: application/x-www-form-urlencoded
		* Body: 
	*/
	function deleteNotification(){
		global $ims;
		if ($ims->method == 'POST'){ // DELETE
			$infoUser = $this->check_token_user();
			$item_id = $ims->func->if_isset($ims->post["item_id"]);

			$where = " and item_id='".$item_id."' ";
			if(empty($item_id)){
				$where = "";
			}
			$noti = $ims->db->load_row_arr("user_notification","is_show=1 AND lang='".$ims->conf['lang_cur']."' and (type=0 OR find_in_set('".$infoUser['user_id']."', user_id)) ".$where);
			if(!$noti){
				$this->response(400, "", 400 , "Không tìm thấy thông báo này");
			}
			$arr_up = array();
			foreach ($noti as $key => $value) {
				if(empty($value["user_delete"])){
					$arr_up["user_delete"] = $infoUser["user_id"];
					$ok = $ims->db->do_update("user_notification",$arr_up," item_id='".$value['item_id']."' ");
				}else{
					$arr_tmp = explode(",",$value["user_delete"]);
					if (in_array($infoUser["user_id"], $arr_tmp) == false) {
						$arr_tmp[] = $infoUser["user_id"];
					}
					$arr_up["user_delete"] = implode(",",$arr_tmp);
					$ok = $ims->db->do_update("user_notification",$arr_up," item_id='".$value['item_id']."' ");
				}
			}
			if($ok){
				$array = array(
		    		"code" => 200,
					"message" => $ims->lang['api']['success'],				
		    		'data' => $ok,
		    	);
				$this->response(200, $array);
			}
		} else {
			$this->response(405, "", 405 , $ims->lang['api']['error_method']);
		}
	}

	/*
		* gửi yêu cầu rút điểm tích lũy
		* Method: POST
		* Content-Type: application/x-www-form-urlencoded
		* Body: 
	*/
	function withdrawWcoin(){
		global $ims;

		if ($ims->method == 'POST'){
			$infoUser = $this->check_token_user();
			$num_wcoin = $ims->func->if_isset($ims->post['num_wcoin'], 0);
			if ($num_wcoin  < 0 || $num_wcoin > $infoUser['wcoin']) {
				$this->response(400, "", 400, $ims->lang['api']['error_wcoin'].', chỉ còn: '.$infoUser['wcoin']);
	        }
	        $arr_ins = array();
	        $arr_ins['num_wcoin'] 	   = $ims->func->if_isset($ims->post['num_wcoin'], 0);
	        $arr_ins['bankcode'] 	   = $ims->func->if_isset($ims->post['bankcode']);
	        $arr_ins['bankname'] 	   = $ims->func->if_isset($ims->post['bankname']);
	        $arr_ins['bankbranch'] 	   = $ims->func->if_isset($ims->post['bankbranch']);
	        $arr_ins['full_name'] 	   = $ims->func->if_isset($ims->post['full_name']);
	        $arr_ins['user_id'] 	   = $infoUser['user_id'];
	        $arr_ins['is_show'] 	   = 1;
	        $arr_ins['date_create']    = time();
	        $arr_ins['date_update']    = time();
	        $ok = $ims->db->do_insert("user_withdrawals", $arr_ins);
	        if($ok){
		        $array = array(
					"code" => 200,
			    	"message" => $ims->lang['api']['success']
	        	);
				$this->response(200, $array);
			}
		} else {
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}		

	/*
		* Lấy danh sách người được giới thiệu
		* Method: GET
		* Content-Type: application/x-www-form-urlencoded
		* Body: 
	*/
	function getUserContributor(){
		global $ims;
		if ($ims->method == 'GET'){			
			$this->load_language('user');
			$infoUser = $this->check_token_user();
			$numshow  = (isset($ims->get['numshow'])) ? $ims->get['numshow']:0;
			$p 		  = (isset($ims->get['p'])) ? $ims->get['p'] : 1;
			$n = 20;
			if ($numshow>0) {
				$n = $numshow;
			}
			if ($numshow>500) {
				$n = 20;
			}
			$where = '';
			$res_num = $ims->db->query("SELECT user_id FROM user WHERE is_show=1 AND user_contributor='".$infoUser['phone']."' ".$where."  ");
			$num_total = $ims->db->num_rows($res_num);
	        $num_items = ceil($num_total / $n);
	        if ($p > $num_items)
	            $p = $num_items;
	        if ($p < 1)
	            $p = 1;
	        $start = ($p - 1) * $n;

	        $complete = $ims->db->load_item('product_order_status','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and is_complete=1','item_id');

	        $arr_user = $ims->db->load_item_arr('user',"is_show=1 AND user_contributor='".$infoUser['phone']."' ".$where." ORDER BY date_create DESC LIMIT ".$start.",".$n." ","user_id,full_name,picture,date_create");
			if($arr_user){
				foreach ($arr_user as $key => $value) {
					if(!empty($value['picture'])){
						$arr_user[$key]['picture'] = $ims->func->get_src_mod($value['picture']);
						$arr_user[$key]['thumbnail'] = $ims->func->get_src_mod($value['picture'],120,120);
					}else{
						$arr_user[$key]['picture'] = NULL;
						$arr_user[$key]['thumbnail'] = NULL;
					}
					$arr_user[$key]['date_create'] = date('d/m/Y',$value['date_create']);
					$user_orders = $ims->db->load_row('product_order','order_code!="" and is_show=1 and user_id="'.$value['user_id'].'" and is_status="'.$complete.'" group by user_id','COUNT(order_id) as num, sum(total_payment) as sum');					
					$arr_user[$key]['total_order'] = !empty($user_orders['num'])?$user_orders['num']:0;
					$arr_user[$key]['total_order_payment'] = !empty($user_orders['sum'])?$user_orders['sum']:0;
				}
				$array = array(
					'error' => array(
		        		'error_code' => 0,
			        	'error_description' => 'Thành công'
					),
					'total' => $num_total,
		    		'total_page' => $num_items,
		    		'numshow' => $n,
		    		'page' => $p,
					'data' => $arr_user
	        	);
				$this->response(200, $array);
			}else{
				$array = array(
					'error' => array(
		        		'error_code' => 0,
			        	'error_description' => 'Thành công'
					),
					'data' => array(),
				);
				$this->response(200, $array);
			}
		} else {
			$this->response(200, "", -1 , $ims->lang['api']['error_method']);
		}
	}

	/*
		* Lấy danh sách đơn hàng của người được giới thiệu
		* Method: GET
		* Content-Type: application/x-www-form-urlencoded
		* Body: 
	*/
	function getOrderContributor(){
		global $ims;
		if ($ims->method == 'GET'){
			$this->load_language('user');
			$infoUser = $this->check_token_user();
			if(empty($ims->get["presentee"])){
	        	$this->response(200, "", 2, "ID thành viên không hợp lệ");
	        }
			$search_date_begin 	= (isset($ims->get["date_begin"])) ? $ims->get["date_begin"] : "";
			$search_date_end 	= (isset($ims->get["date_end"])) ? $ims->get["date_end"] : "";
			$month 				= (isset($ims->get["m"])) ? $ims->get["m"] : "";
			$year 				= (isset($ims->get["y"])) ? $ims->get["y"] : "";
			$presentee			= $ims->get["presentee"];
			$numshow  	  		= (isset($ims->get['numshow'])) ? $ims->get['numshow']:0;
			$p 		      		= (isset($ims->get['p'])) ? $ims->get['p'] : 1;
			$n = 20;
			if ($numshow>0) {
				$n = $numshow;
			}
			if ($numshow>500) {
				$n = 20;
			}
			$where = '';
            if($search_date_begin || $search_date_end ){
				$tmp1 = @explode("-", $search_date_begin);
				$time_begin = @mktime(0, 0, 0, $tmp1[1], $tmp1[0], $tmp1[2]);
				$tmp2 = @explode("-", $search_date_end);
				$time_end = @mktime(23, 59, 59, $tmp2[1], $tmp2[0], $tmp2[2]);
				$where.=" AND (date_create BETWEEN {$time_begin} AND {$time_end} ) ";
			}
			if(!empty($month) && !empty($year)){
	            $where .=" and MONTH(FROM_UNIXTIME(`date_create`)) = ".$month." and YEAR(FROM_UNIXTIME(`date_create`)) =".$year."  ";
	        }	        
	        $res_num = $ims->db->query("SELECT id FROM user_exchange_log WHERE exchange_type='ouser_wcoin' AND is_show=1 AND user_id='".$presentee."' ".$where."  ");
			$num_total = $ims->db->num_rows($res_num);
	        $num_items = ceil($num_total / $n);
	        if ($p > $num_items)
	            $p = $num_items;
	        if ($p < 1)
	            $p = 1;
	        $start = ($p - 1) * $n;

	        $total_commission = 0;
	        $arr_tmp = array();
			$complete = $ims->db->load_item('product_order_status','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and is_complete=1','item_id');
			$order_status = $this->data_table(
				    'product_order_status', 
				    'item_id', '*', 
				    "lang='".$ims->conf['lang_cur']."' and is_show=1 ORDER BY show_order DESC, date_create DESC", array()
				);
			$infoOrder = $ims->db->load_item_arr('product_order','is_show=1 and user_id="'.$presentee.'" '.$where." ORDER BY date_create DESC LIMIT ".$start.",".$n." ",'order_id,order_code,total_payment,d_full_name,d_phone,d_province,d_district,d_ward,d_address,date_create,is_status,vat_price,cancel_reason');	        
			if($infoOrder){
				foreach ($infoOrder as $key => $value) {					
					$arr_tmp[$key]['title'] 		= $value['order_code'];
					$arr_tmp[$key]['order_code'] 	= $value['order_code'];
					$arr_tmp[$key]['total_payment'] = $value['total_payment'];
					$picture = $ims->db->load_item('product_order_detail','order_id="'.$value['order_id'].'" order by detail_id asc','picture');
					if($picture){
						$arr_tmp[$key]['picture'] 	= $ims->func->get_src_mod($picture);
						$arr_tmp[$key]['thumbnail'] = $ims->func->get_src_mod($picture,120,120);
					}
					$arr_tmp[$key]['full_name']		= $value['d_full_name'];
					$arr_tmp[$key]['phone']			= $value['d_phone'];
					$arr_tmp[$key]['province'] 		= $this->location_name('province', $value['d_province']);
        			$arr_tmp[$key]['district'] 		= $this->location_name('district', $value['d_district']); 
        			$arr_tmp[$key]['ward'] 			= $this->location_name('ward', $value['d_ward']);
        			$arr_tmp[$key]['address'] 		= $value['d_address'];
        			$arr_tmp[$key]['address_full'] 	= $arr_tmp[$key]['address'].', '.$arr_tmp[$key]['ward'].', '.$arr_tmp[$key]['district'].', '.$arr_tmp[$key]['province'];        
        			$status = isset($order_status[$value['is_status']])?$order_status[$value['is_status']]:'';			
        			$arr_tmp[$key]["status_order"] 	= $status['title'];
					$arr_tmp[$key]['value']			= 0;
					$arr_tmp[$key]['date_create'] 	= date("d/m/Y", $value['date_create']);
					$arr_tmp[$key]['time_create'] 	= date("H:i", $value['date_create']);
					if($value['is_status'] == $complete){
	        			$exchange = $ims->db->load_row('user_exchange_log',"exchange_type='ouser_wcoin' AND is_show=1 AND user_id='".$presentee."'",'*');
	        			$arr_tmp[$key]['wcoin_before'] 	= $exchange['wcoin_before'];
						$arr_tmp[$key]['wcoin_after'] 	= $exchange['wcoin_after'];
						$arr_tmp[$key]['value'] 		= $exchange['value']*$exchange['value_type'];
        			}
					$total_commission += $arr_tmp[$key]['value'];

					$list_detail = $ims->db->load_item_arr('product_order_detail', " order_id='".$value['order_id']."' ORDER BY detail_id ASC", "type_id, picture, title, price_buy, quantity, size_id, list_nature, note");
	        		if (!empty($list_detail)) {
	        			foreach ($list_detail as $k => $v) {
	        				$price_size = $price_nature = 0;
	        				$list_detail[$k]['item_id'] = $list_detail[$k]['type_id'];
	        				$list_detail[$k]['price_buy'] = $v['price_buy'];
	        				$list_detail[$k]['picture'] = $ims->func->get_src_mod($v['picture']);
	        				$list_detail[$k]['thumbnail'] = $ims->func->get_src_mod($v['picture'],120,120);
	        				$list_detail[$k]['note'] = $ims->func->input_editor_decode($list_detail[$k]['note']);
	        				$list_detail[$k]['size'] = $ims->db->load_row('product_size','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and product_id="'.$list_detail[$k]['item_id'].'" and item_id="'.$list_detail[$k]['size_id'].'"','item_id,title,upsize');
	        				if(count($list_detail[$k]['size']) == 0){
	        					unset($list_detail[$k]['size']);
	        				}else{
	        					$price_size = $list_detail[$k]['size']['upsize'];
	        				}
	        				$list_detail[$k]['group_id'] = $ims->db->load_item('product','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and item_id="'.$list_detail[$k]['item_id'].'"','group_id');
	        				$lna = array();
	        				$arr_nature = $ims->func->unserialize($list_detail[$k]['list_nature']);
	        				foreach ($arr_nature as $kn => $vn) {
	        					$lna[] = $vn['item_id'];
	        					$arr_nature[$vn['item_id']] = $vn;
	        					$price_nature += ($nature[$vn['item_id']]['price_nature'] * $vn['quantity']);
	        				}	        				
	        				$arr_nat = $ims->db->load_item_arr('product_nature','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and find_in_set(item_id,"'.implode(',',$lna).'")','group_id,item_id,price_nature');
	                        $list_detail[$k]['list_nature'] = array();
	                        foreach ($arr_nat as $kn => $vn) {
	                            $list_detail[$k]['list_nature'][$vn['group_id']]['title'] = $nature_group[$vn['group_id']]['title'];
	                            $list_detail[$k]['list_nature'][$vn['group_id']]['data'][] = array(
	                                'item_id' => $vn['item_id'],
	                                'quantity' => $arr_nature[$vn['item_id']]['quantity'],
	                                'title' => $nature[$vn['item_id']]['title'],          
	                                'price_nature' => $vn['price_nature'],
	                            );
	                        }  
	                        if(count($list_detail[$k]['list_nature']) == 0){
	                            unset($list_detail[$k]['list_nature']);
	                        }else{
	                            $list_detail[$k]['list_nature'] = array_values($list_detail[$k]['list_nature']);
	                        }   
	                        $list_detail[$k]['item_payment'] = ($v['price_buy'] + $price_size + $price_nature);
			                unset($list_detail[$k]['group_id']);
	        				unset($list_detail[$k]['size_id']);
			                unset($list_detail[$k]['type_id']);
	        			}
	        			$arr_tmp[$key]['list_detail'] = $list_detail;
	        		}
				}
				$array = array(
					'error' => array(
		        		'error_code' => 0,
			        	'error_description' => 'Thành công'
					),
					'total' => $num_total,
		    		'total_page' => $num_items,
		    		'numshow' => $n,
		    		'page' => $p,
					'data' => $arr_tmp,
					'total_commission' => $total_commission,
	        	);
				$this->response(200, $array);
			}else{
				$array = array(
					'error' => array(
		        		'error_code' => 0,
			        	'error_description' => 'Thành công'
					),
					'data' => array(),
				);
				$this->response(200, $array);
			}
		} else {
			$this->response(200, "", -1 , $ims->lang['api']['error_method']);
		}
	}

	/*
		* Lấy danh sách đơn hàng
		* Method: GET
		* Content-Type: application/x-www-form-urlencoded
		* Body: 
	*/
	function getOrder(){
		global $ims;
		
		if ($ims->method == 'GET'){
			$this->load_language('user');
			$infoUser = $this->check_token_user();
			$p 		      		= $ims->func->if_isset($ims->get['p'], 1);
			$numshow  	  		= $ims->func->if_isset($ims->get['numshow'], 0);
			$order_id 			= $ims->func->if_isset($ims->get["order_id"]);
			$is_status 			= $ims->func->if_isset($ims->get["is_status"], 0);
			$search_date_begin  = $ims->func->if_isset($ims->get["search_date_begin"]);
			$search_date_end 	= $ims->func->if_isset($ims->get["search_date_end"]);
			$search_title 		= $ims->func->if_isset($ims->get["search_title"]);

			$n = 20;
			if ($numshow>0) {
				$n = $numshow;
			}
			if ($numshow>500) {
				$n = 20;
			}

            $where = '';
            if($search_date_begin || $search_date_end ){
				$tmp1 = @explode("/", $search_date_begin);
				$time_begin = @mktime(0, 0, 0, $tmp1[1], $tmp1[0], $tmp1[2]);
				$tmp2 = @explode("/", $search_date_end);
				$time_end = @mktime(23, 59, 59, $tmp2[1], $tmp2[0], $tmp2[2]);
				$where.=" AND (date_create BETWEEN {$time_begin} AND {$time_end} ) ";
			}
			if(!empty($search_title)){
				$where .=" AND (order_code='$search_title' 
					OR order_id='$search_title' 
					OR o_full_name LIKE '%$search_title%') ";
			}
			if ($order_id!="") {
				$where.=" AND (order_id='".$order_id."') ";
			}
			if ($is_status>0) {
				$where.=" AND (is_status='".$is_status."') ";
			}

			$res_num = $ims->db->query("SELECT order_id FROM product_order WHERE order_code!='' AND is_show=1 AND user_id='".$infoUser['user_id']."' ".$where."  ");
	        $num_total = $ims->db->num_rows($res_num);
	        $num_items = ceil($num_total / $n);
	        if ($p > $num_items)
	            $p = $num_items;
	        if ($p < 1)
	            $p = 1;
	        $start = ($p - 1) * $n;

	       	// arr 
			$arr_tmp = $ims->db->load_item_arr('product_order'," order_code !='' AND is_show=1 AND user_id='".$infoUser['user_id']."' ".$where." ORDER BY date_create DESC LIMIT ".$start.",".$n." ",'*');

			$status_order = $this->data_table(
			    'product_order_status', 
			    'item_id', '*', 
			    "lang='".$ims->conf['lang_cur']."' ORDER BY show_order DESC, date_create DESC", array()
			);

			$arr = array();
	        if (!empty($arr_tmp)) {
	        	foreach ($arr_tmp as $key => $value) {
	        		
	        		if($order_id!=''){
	        			$arr[$key] = $value;
	        			unset($arr[$key]['department_id']);
	        			unset($arr[$key]['o_area']);
	        			unset($arr[$key]['o_country']);
	        			unset($arr[$key]['d_area']);
	        			unset($arr[$key]['d_country']);
	        			unset($arr[$key]['message_send']);
	        			unset($arr[$key]['message_title']);
	        			unset($arr[$key]['message_content']);
	        			unset($arr[$key]['user_contributor']);
	        			unset($arr[$key]['amount_contributor']);
	        			unset($arr[$key]['amount_contributor_root']);
	        			unset($arr[$key]['user_contributor_department']);
	        			unset($arr[$key]['amount_contributor_department']);
	        			unset($arr[$key]['voucher_id']);
	        			unset($arr[$key]['voucher_amount']);
	        			unset($arr[$key]['user_id']);
	        			unset($arr[$key]['show_order']);
	        			unset($arr[$key]['is_collectedmoney']);
	        			unset($arr[$key]['is_senddepartment']);
	        			unset($arr[$key]['is_fee20']);
	        			unset($arr[$key]['collectedmoney_message']);
	        			unset($arr[$key]['collectedmoney_adminid']);
	        			unset($arr[$key]['collectedmoney_adminname']);
	        			// unset($arr[$key]['payment_wcoin2money']);
	        			unset($arr[$key]['wcoin_accumulation']);
	        			unset($arr[$key]['invoice_company']);
	        			unset($arr[$key]['invoice_tax_code']);
	        			unset($arr[$key]['invoice_address']);
	        			unset($arr[$key]['invoice_email']);
	        			unset($arr[$key]['promotion_id']);
	        			unset($arr[$key]['promotion_percent']);
	        	

	        			$arr[$key]['shipping']		= $ims->db->load_item('order_shipping', 'shipping_id="'.$value['shipping'].'" AND lang="'.$ims->conf['lang_cur'].'" AND is_show=1' ,'title');
	        			$arr[$key]['method']		= $ims->db->load_item('order_method', 'method_id="'.$value['method'].'" AND lang="'.$ims->conf['lang_cur'].'" AND is_show=1' ,'title');
	        			$arr[$key]['method_text']	= $ims->func->input_editor_decode($ims->db->load_item('order_method', 'method_id="'.$value['method'].'" AND lang="'.$ims->conf['lang_cur'].'" AND is_show=1' ,'content'));
	        			// $arr[$key]['o_phone']		= $value['o_phone'];
	        			$arr[$key]['o_province'] 	= $this->location_name('province', $value['o_province']);
	        			$arr[$key]['o_district'] 	= $this->location_name('district', $value['o_district']); 
	        			$arr[$key]['o_ward'] 		= $this->location_name('ward', $value['o_ward']); 
	        			// $arr[$key]['d_phone']		= $value['d_phone'];
	        			$arr[$key]['d_province'] 	= $this->location_name('province', $value['d_province']);
	        			$arr[$key]['d_district'] 	= $this->location_name('district', $value['d_district']); 
	        			$arr[$key]['d_ward'] 		= $this->location_name('ward', $value['d_ward']); 
	        		}else{

		        		$arr[$key]['order_id'] 			= $value['order_id'];
		        		$arr[$key]['order_code'] 		= $value['order_code'];
		        		$arr[$key]['total_payment'] 	= $value['total_payment'];
		        		$arr[$key]['date_create'] 		= $value['date_create'];
		        		// $arr[$key]['date_update'] 		= $value['date_update'];
		        		$arr[$key]['is_status'] 		= $value['is_status'];
		        		$arr[$key]['o_full_name'] 		= $value['o_full_name'];
		        		$arr[$key]['o_phone']			= $value['o_phone'];
	        		}	        		

	        		$list_detail = $ims->db->load_item_arr('product_order_detail', " order_id='".$value['order_id']."' ORDER BY detail_id ASC", "picture, title, price_buy, quantity, option1, option2, option3, combo_id,arr_gift_include");
	        		if (!empty($arr)) {
	        			foreach ($list_detail as $k => $v) {
	        				$list_detail[$k]['price_buy'] = $v['price_buy'];
	        				$list_detail[$k]['picture'] = $ims->func->get_src_mod($v['picture']);
	        				$list_detail[$k]['option_name'] = '';
			                if ($v['option1']!='') {
			                    $list_detail[$k]['option_name'] .= $v['option1'];
			                }
			                if ($v['option2']!='') {
			                    $list_detail[$k]['option_name'] .= ' / '.$v['option2'];
			                }
			                if ($v['option3']!='') {
			                    $list_detail[$k]['option_name'] .= ' / '.$v['option3'];
			                }
			                unset($list_detail[$k]['option1']);
			                unset($list_detail[$k]['option2']);
			                unset($list_detail[$k]['option3']);
			              
							if ($v['combo_id']>0 && $v['arr_gift_include']!='') {					
			                    $arr_gift_include = $ims->func->unserialize($v['arr_gift_include']);
			                    if (!empty($arr_gift_include)) {
			                        if (isset($arr_gift_include['include'])) {
			                        	$v['class_type'] = "combo";
			                            foreach ($arr_gift_include['include'] as $kc => $vc) {
			                                $arr_gift_include['include'][$kc]["picture"] = $ims->func->get_src_mod($vc["picture"]);
			                                $arr_gift_include['include'][$kc]['price_buy'] = $vc['price_buy_combo'];
			                                unset($arr_gift_include['include'][$kc]['price']);
			                                unset($arr_gift_include['include'][$kc]['price_buy_combo']);
			                                unset($arr_gift_include['include'][$kc]['group_id']);
			                                unset($arr_gift_include['include'][$kc]['group_nav']);
			                                // $v["link_combo"] = $ims->admin->get_link_admin($v['type'], $v['type'], 'edit', array("id" => $v['item_id']));
			                            }
			                            $list_detail[$k]['include_info'] = $arr_gift_include['include'];
			                        }
			                        if (isset($arr_gift_include['gift'])) {
			                        	$v['class_type'] = "combo";
			                            foreach ($arr_gift_include['gift'] as $kc => $vc) {                                
			                                $arr_gift_include['gift'][$kc]["picture"] = $ims->func->get_src_mod($vc["picture"]);
			                                $arr_gift_include['gift'][$kc]['price_buy'] = (int)$vc['price'];
			                                unset($arr_gift_include['gift'][$kc]['price']);
			                                unset($arr_gift_include['gift'][$kc]['price_buy_combo']);
			                                // $v["link_combo"] = $ims->admin->get_link_admin($v['type'], $v['type'], 'edit', array("id" => $v['item_id']));
			                            }
			                            $list_detail[$k]['gift_info'] = $arr_gift_include['gift'];
			                        }
			                        unset($list_detail[$k]['arr_gift_include']);
			                    }
			                }
	        			}
	        			$arr[$key]['list_detail'] = $list_detail;
	        		}


	        		$arr[$key]['is_status_title'] 	= isset($status_order[$value['is_status']]['title'])?$status_order[$value['is_status']]['title']:'';
	        		$arr[$key]['is_status_color'] 	= isset($status_order[$value['is_status']]['color_title'])?$status_order[$value['is_status']]['color_title']:'';
	        		$arr[$key]['is_status_bgcolor'] = isset($status_order[$value['is_status']]['color_bg'])?$status_order[$value['is_status']]['color_bg']:'';
	        	}
	        }
	        $array = array(
				"code" => 200,
		    	"message" => $ims->lang['api']['success'],
				"total" => $num_total,
	    		"page" => $p,
				"data" => $arr
        	);
			$this->response(200, $array);
		} else {
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}


	/*
		* Lấy danh sách trạng thái đơn hàng
		* Method: GET
		* Content-Type: application/x-www-form-urlencoded
		* Body: 
	*/
	function getOrderStatus(){
		global $ims;

		if ($ims->method == 'GET'){
			$arr = $ims->db->load_item_arr('product_order_status','is_show=1 AND lang="'.$ims->conf['lang_cur'].'" AND is_show_app=1 ORDER BY show_order DESC, date_update DESC','item_id, title, picture');
			if (!empty($arr)) {
				foreach ($arr as $key => $value) {
					if ($value['picture']!='') {
						$arr[$key]['picture'] = $ims->func->get_src_mod($value['picture']);
					}
				}
			}
			$array = array(
        		"code" => 200,
	    		"message" => $ims->lang['api']['success'],
				'data' => $arr
        	);
			$this->response(200, $array);
		} else {
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}

	function orderCancel(){
		global $ims;
		if($ims->method == 'POST'){
			$this->load_language('user');
			$infoUser = $this->check_token_user();
			$order_id = $ims->func->if_isset($ims->post['order_id']);
			if(empty($order_id)){
				$this->response(400, "", 400, "Đơn hàng không hợp lệ!");
			}
			$order = $ims->db->load_row('product_order', 'order_id="'.$order_id.'" and user_id="'.$infoUser['user_id'].'"','order_id, order_code, is_status, is_status_payment, payment_wcoin, user_id, promotion_id');
			if(empty($order)){
				$this->response(400, "", 400, "Đơn hàng không hợp lệ!");
			}

			$new = $this->get_status_order_by_list_string('1');
			$cancel = $this->get_status_order_by_list_string('cancel');
			
			if($order['is_status'] == $new['item_id'] && $order['is_status_payment'] != 3){				
				$arr_up = array();
				$arr_up['is_status'] = $cancel['item_id'];
				$arr_up['is_cancel'] = 1;
				$arr_up['cancel_reason'] = $ims->func->if_isset($ims->post['cancel_reason']);
				$arr_up['date_cancel'] = time();
				$ok = $ims->db->do_update("product_order",$arr_up," order_id='".$order['order_id']."' ");
				if($ok){
					$SQL_UPDATE = 'UPDATE user SET wcoin = wcoin + '.$order['payment_wcoin'].' WHERE is_show = 1 AND user_id = "'.$order['user_id'].'"';
					$ims->db->query($SQL_UPDATE);

					if(!empty($order['promotion_id'])){
						$check = $ims->db->load_row('promotion','is_show=1 and promotion_id="'.$order['promotion_id'].'" ');
						if(!empty($check['num_use'])){
							$ims->db->query("UPDATE promotion SET num_use=num_use-1 WHERE promotion_id='".$order['promotion_id']."' ");
						}
					}
					
					//lưu log
					$arr_ins                    = array();
			        $arr_ins['is_show']         = 1;
			        $arr_ins['order_id']        = $order['order_id'];
			        $arr_ins['date_create']     = time();
			        $arr_ins['date_update']     = time();
			        $arr_ins['title'] = 'Khách hàng đã hủy đơn #'.$order['order_code'];
			        $arr_ins['content'] = $cancel['title'];
        			$ok = $ims->db->do_insert('product_order_log', $arr_ins);

					if($ok){
						$array = array(
			        		"code" => 200,
				    		"message" => $ims->lang['user']['cancelOrder_message_success'],
				    		'data' => $arr_up,
			        	);
						$this->response(200, $array);
					}else{
						$this->response(400, "", 400, "Có lỗi xảy ra!");
					}
				}
			}else{
				switch ($order['is_status']) {
					case $cancel['item_id']:						
						$this->response(400, "", 400, $ims->lang['user']['cancelOrder_message_false1']);
						break;					
					default:						
						$this->response(400, "", 400, $ims->lang['user']['cancelOrder_message_false']);
						break;
				}
				if($order['is_status_payment'] == 3){					
					$this->response(400, "", 400, $ims->lang['user']['cancelOrder_message_false2']);
				}
			}
		}else{
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}

	
	// Lấy giỏ hàng
	function getCart(){
		global $ims;

		if ($ims->method == 'GET'){
			$infoUser = $this->check_token_user();
			$ims->site_func->setting('product');
			$cart = $ims->db->load_item_arr('product_order_temp','user_id="'.$infoUser['user_id'].'" ORDER BY id DESC','item_id, quantity, option_id, combo_info, bundled_product');
			$total_cart = 0;
			$arr_tmp = array();
			if (!empty($cart)) {
				$i = 0;
				$pic_w = 310;
				$pic_h = 300;				
				foreach ($cart as $key => $value) {
					$product = $ims->db->load_row('product', $ims->conf['where_lang'].' AND item_id="'.$value['item_id'].'" ');
					$option = $ims->db->load_row('product_option', $ims->conf['where_lang'].' AND id="'.$value['option_id'].'" ');
					if (!empty($option)) {
						$arr_tmp[$i]['thumbnail'] 		 = $ims->func->get_src_mod($product['picture'], 40, 40, 1, 1);
						$arr_tmp[$i]['picture']   		 = $ims->func->get_src_mod($product['picture'], $pic_w, $pic_h, 1, 0);
	                    $arr_tmp[$i]['price'] 	  		 = $option['Price'];
	                    $arr_tmp[$i]['price_buy'] 		 = $option['PriceBuy'];
	                    $arr_tmp[$i]['percent_discount'] = $option['PercentDiscount'];
					}
					$arr_tmp[$i]['title'] = $product['title'];
					$arr_tmp[$i]['item_id'] = $value['item_id'];
					$arr_tmp[$i]['option_id'] = $value['option_id'];
					$arr_tmp[$i]['option_text'] = '';
					if ($option['Option1'] != "" && $option['Option1'] != "Default Title") {
						$arr_tmp[$i]['option_text'] .= $option['Option1'];
					}
					if ($option['Option2'] != "") {
						$arr_tmp[$i]['option_text'] .= ' / '.$option['Option2'];
					}
					if ($option['Option3'] != "") {
						$arr_tmp[$i]['option_text'] .= ' / '.$option['Option3'];
					}
					$arr_tmp[$i]['quantity'] = $value['quantity'];
					$total_cart += ($arr_tmp[$i]['price_buy'] * $value['quantity']);
					//combo
					$arr_tmp[$i]['is_combo'] = 0;					
					$arr_tmp[$i]['combo_id'] = 0;
					$arr_tmp[$i]['combo_info'] = "";
					$combo = $ims->db->load_row('combo','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and item_id="'.$product['combo_id'].'"','item_id,arr_gift,arr_include,value_type,value,num_chose');
					if($combo){
						$arr_tmp[$i]['is_combo'] = 1;
						$arr_tmp[$i]['num_chose'] = (int)$combo['num_chose'];
						$arr_tmp[$i]['combo_id'] = $combo['item_id'];
						$combo_info = $ims->func->unserialize($value['combo_info']);
						$arr_tmp[$i]['combo_info'] = $combo_info;
						//gift: quà
						if(isset($combo_info['gift_id'])){
							$arr_tmp[$i]['gift_id'] = 0;
							$arr_tmp[$i]['gift_info'] = NULL;
							$arr_gift = $ims->db->load_item_arr('user_gift','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and find_in_set(item_id,"'.$combo_info['gift_id'].'")>0', 'item_id, title, short, price, picture, quantity_combo');
							if($arr_gift){
								$arr_tmp[$i]['gift_id'] = $combo_info['gift_id'];
								foreach ($arr_gift as $key => $gift) {									
									$arr_tmp[$i]['gift_info'][$key] = $gift;									
									$arr_tmp[$i]['gift_info'][$key]['title'] = $ims->func->input_editor_decode($gift['title']);
									$arr_tmp[$i]['gift_info'][$key]['short'] = $ims->func->input_editor_decode($gift['short']);
									$arr_tmp[$i]['gift_info'][$key]['picture'] = $ims->func->get_src_mod($gift['picture']);
									$arr_tmp[$i]['gift_info'][$key]['thumbnail'] = $ims->func->get_src_mod($gift['picture'],40,40);
									$arr_tmp[$i]['gift_info'][$key]['price_buy'] = 0;
								}
							}
							$arr_gift = $ims->db->load_item_arr('user_gift','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and find_in_set(item_id,"'.$combo['arr_gift'].'")>0 and quantity_combo>0', 'item_id, title, short, price, picture, quantity_combo, 1 as active');
							if($arr_gift){
								foreach ($arr_gift as $k => $v) {
									$arr_gift[$k]['picture'] = $ims->func->get_src_mod($v['picture']);
									$arr_gift[$k]['thumbnail'] = $ims->func->get_src_mod($v['picture'],40,40);
									$arr_gift[$k]['price_buy'] = 0;
								}
							}
							// $arr_gift_disable = $ims->db->load_item_arr('user_gift','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and find_in_set(item_id,"'.$combo['arr_gift'].'")<=0', 'item_id, title, short, price, picture, quantity_combo, 0 as active');
							// if($arr_gift_disable){
							// 	foreach ($arr_gift_disable as $k => $v) {
							// 		$arr_gift_disable[$k]['picture'] = $ims->func->get_src_mod($v['picture']);
							// 		$arr_gift_disable[$k]['thumbnail'] = $ims->func->get_src_mod($v['picture'],40,40);
							// 		$arr_gift_disable[$k]['price_buy'] = 0;
							// 	}
							// }								
							// $arr_gift = array_merge($arr_gift,$arr_gift_disable);
							$arr_tmp[$i]['arr_gift'] = $arr_gift;
						}

						//include: sản phẩm mua kèm
						if(isset($combo_info['include_id'])){
							$arr_tmp[$i]['include_id'] = 0;
							$arr_tmp[$i]['include_info'] = NULL;
							$arr_include = $ims->db->load_item_arr('product','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and find_in_set(item_id,"'.$combo_info['include_id'].'")>0', 'item_id, combo_id, group_id, picture, title, price, price_buy, percent_discount, arr_item, field_option');							
							if($arr_include){								
								$arr_tmp[$i]['include_id'] = $combo_info['include_id'];
								foreach ($arr_include as $k => $v) {
									// $arr_tmp[$i]['include_info'][$key] = array(
									// 	'title' => $ims->func->input_editor_decode($include['title']),									
									// 	'picture' => $ims->func->get_src_mod($include['picture']),
									// 	// 'price_buy' => (int)$include['price_buy'],
									// );								
									// if(isset($combo['value_type']) && $combo['value_type'] == 0){
					    //                 $arr_tmp[$i]['include_info'][$key]['price_buy'] = $include['price_buy'] - $combo['value'];
					    //                 if($arr_tmp[$i]['include_info'][$key]['price_buy'] <= 0){
					    //                     $arr_tmp[$i]['include_info'][$key]['price_buy'] = 0;
					    //                 }
					    //             }
					    //             elseif(isset($combo['value_type']) && $combo['value_type'] == 1){				                 	
					    //                 $arr_tmp[$i]['include_info'][$key]['price_buy'] = $include['price_buy'] - ($include['price_buy']*$combo['value']/100);				                    
					    //             }
									$arr_include[$k]['picture'] = $ims->func->get_src_mod($v['picture']);
									$arr_include[$k]['thumbnail'] = $ims->func->get_src_mod($v['picture'],40,40);
									// $arr_include[$k]['price_buy'] = (int)$v['price_buy'];
									if(isset($combo['value_type']) && $combo['value_type'] == 0){
				                    	$arr_include[$k]['price_buy'] = $v['price_buy'] - $combo['value'];
					                    if($arr_include[$k]['price_buy'] <= 0){
					                        $arr_include[$k]['price_buy'] = 0;
					                    }
					                }
					                elseif(isset($combo['value_type']) && $combo['value_type'] == 1){
					                    $arr_include[$k]['price_buy'] = $v['price_buy'] - ($v['price_buy']*$combo['value']/100);
					                }
									$order_by = ' ORDER BY date_create';
									if(!empty($v['field_option'])){
										$order_by = ' order by '.$v['field_option'].',date_create';
									}
						            $option = $ims->db->load_row('product_option','lang="'.$ims->conf['lang_cur'].'" AND is_show=1 AND ProductId="'.$v['item_id'].'" '.$order_by,'id');
									$arr_include[$k]['option_id'] = $option['id'];
						        	unset($arr_include[$k]['arr_item']);
						        	unset($arr_include[$k]['field_option']);
					                $total_cart += $arr_include[$k]['price_buy'];
				                }
				                $arr_tmp[$i]['include_info'] = $arr_include;
							}
							$arr_include_all = $ims->db->load_item_arr('product','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and find_in_set(item_id,"'.$combo['arr_include'].'")>0', 'item_id, combo_id, group_id, picture, title, price, price_buy, percent_discount, arr_item, field_option');
							if($arr_include_all){
								foreach ($arr_include_all as $k => $v) {
									$arr_include_all[$k]['picture'] = $ims->func->get_src_mod($v['picture']);
									$arr_include_all[$k]['thumbnail'] = $ims->func->get_src_mod($v['picture'],40,40);
									// $arr_include[$k]['price_buy'] = (int)$v['price_buy'];
									if(isset($combo['value_type']) && $combo['value_type'] == 0){
				                    	$arr_include_all[$k]['price_buy'] = $v['price_buy'] - $combo['value'];
					                    if($arr_include_all[$k]['price_buy'] <= 0){
					                        $arr_include_all[$k]['price_buy'] = 0;
					                    }
					                }
					                elseif(isset($combo['value_type']) && $combo['value_type'] == 1){
					                    $arr_include_all[$k]['price_buy'] = $v['price_buy'] - ($v['price_buy']*$combo['value']/100);
					                }
									$order_by = ' ORDER BY date_create';
									if(!empty($v['field_option'])){
										$order_by = ' order by '.$v['field_option'].',date_create';
									}
						            $option = $ims->db->load_row('product_option','lang="'.$ims->conf['lang_cur'].'" AND is_show=1 AND ProductId="'.$v['item_id'].'" '.$order_by,'id');
									$arr_include_all[$k]['option_id'] = $option['id'];
						        	unset($arr_include_all[$k]['arr_item']);
						        	unset($arr_include_all[$k]['field_option']);
								}
								$arr_tmp[$i]['arr_include'] = $arr_include_all;
							}							
						}
						
					}
					$i++;
				}
				$bundled = array();
				if(!empty($value['bundled_product']) && !empty($ims->setting['product']['is_order_bundled'])){
					$bundled = $ims->func->unserialize($value['bundled_product']);
					foreach ($bundled as $k => $v) {
						$product = $ims->db->load_row('product', $ims->conf['where_lang'].' AND item_id="'.$v['item_id'].'" ','title,picture');
						$bundled[$k]['title'] = $ims->func->input_editor_decode($product['title']);
						$bundled[$k]['quantity'] = 1;
						$bundled[$k]['picture'] = $ims->func->get_src_mod($product['picture']);
						$bundled[$k]['thumb'] = $ims->func->get_src_mod($product['picture'],40,40);
					}
					$bundled = array_values($bundled);

				}
				$wcoin_accumulation = round($total_cart * ($ims->setting['product']['percentforwcoin']/ 100) / $ims->setting['product']['money_to_wcoin']);
			}

			$array = array(
        		"code" => 200,
	    		"message" => $ims->lang['api']['success'],
				'data' => $arr_tmp,
				'bundled' => $bundled,
				'total_payment' => $total_cart,
				'wcoin_accumulation' => $wcoin_accumulation,
        	);
			$this->response(200, $array);
		} else {
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}


	// Thêm vào giỏ hàng
	function updateCart(){
		global $ims;

		if ($ims->method == 'POST'){			
			$infoUser = $this->check_token_user();
			$item_id   = $ims->func->if_isset($ims->post['item_id'], 0);
			$quantity  = $ims->func->if_isset($ims->post['quantity'], 0);
			$option_id = $ims->func->if_isset($ims->post['option_id'], 0);
			$combo_info = $ims->func->if_isset($ims->post['combo_info'], "");
			$combo_info = json_decode($combo_info, true);
			$combo_info = $ims->func->serialize($combo_info);
			$bundled = $ims->func->if_isset($ims->post['bundled'], "");
			$bundled = json_decode($bundled, true);
			$bundled = $ims->func->serialize($bundled);
						
			$checkCart = $ims->db->load_row('product_order_temp',' item_id="'.$item_id.'" AND option_id="'.$option_id.'" AND user_id="'.$infoUser['user_id'].'"');
			if (!empty($checkCart)) {
				// Đã tồn tại trong giỏ hàng
				$checkDel = $quantity;
				if ($checkDel<=0 || $quantity==0) {
					$ims->db->query('DELETE FROM product_order_temp WHERE id="'.$checkCart['id'].'" ');
					
					$array = array(
						"code" => 200,
    					"message" => 'Xóa sản phẩm thành công'
					);
					$this->response(200, $array);
				}else{
					$ok = $ims->db->do_update('product_order_temp', array(
						"quantity" => $checkDel,
						"combo_info" => $combo_info,
						"date_update" => time(),
					), ' id="'.$checkCart['id'].'" ');
					// $ims->db->query('UPDATE product_order_temp SET quantity="'.$checkDel.'", combo_info="'.$combo_info.'", date_update="'.time().'" WHERE id="'.$checkCart['id'].'" ');
				}
				//bundled
				$ims->db->do_update('product_order_temp',array('bundled_product'=>''),' user_id="'.$infoUser['user_id'].'" ');
				$ims->db->do_update('product_order_temp',array('bundled_product'=>$bundled),' user_id="'.$infoUser['user_id'].'" order by date_update limit 1');

                $cart = $ims->db->load_item_arr('product_order_temp',' user_id="'.$infoUser['user_id'].'" ORDER BY id DESC','item_id, quantity, option_id, combo_info');
				$array = array(
					"code" => 200,
					"message" => 'Thêm vào giỏ hàng thành công',
					'data' => $cart
				);
				$this->response(200, $array);
			}else{
				// Thêm mới sản phẩm vào giỏ hàng
				if ($quantity<=0) {
					$array = array(
						"code" => 400,
    					"message" => 'Số lượng phải >= 1'
					);
					$this->response(400, $array);
				}
				$arr_ins = array();
				$arr_in['item_id']   = $item_id;
				$arr_in['option_id']    = $option_id;
				$arr_in['combo_info']   = $combo_info;
				$arr_in['user_id']      = $infoUser['user_id'];
				$arr_in['quantity']     = $quantity;
				$arr_in['date_create']  = time();
				$arr_in['date_update']  = time();
				$arr_in['bundled']   	= $bundled;
				$ok = $ims->db->do_insert("product_order_temp", $arr_in);
                if ($ok) {
                	$cart = $ims->db->load_item_arr('product_order_temp','user_id="'.$infoUser['user_id'].'" ORDER BY id DESC','item_id, quantity, option_id, combo_info, bundled_product');
                	$array = array(
						"code" => 200,
    					"message" => 'Thêm vào giỏ hàng thành công',
						'data' => $cart
					);
					$this->response(200, $array);
                }
			}
		} else {
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}


	// Sử dụng mã giảm giá
	function usePromotionCode() {
		global $ims;

		if ($ims->method == 'POST'){

			// $infoUser = $this->check_token_user();
			$token_login = $ims->func->if_isset($ims->get['user'], 0);
			$infoUser = array();
			$access = 0;
			if(!empty($token_login)){				
				$infoUser = $ims->db->load_row('user',' FIND_IN_SET("'.$token_login.'", token_login) ');
			}
			$this->setting('user');
			$this->setting('product');
			$this->load_language('product');

			$promotion_code = $ims->func->if_isset($ims->post['code']);
			$arr_cart       = $ims->func->if_isset($ims->post['cart']);
			$arr_cart 		= json_decode($arr_cart, true);

			if(empty($arr_cart)){
				$this->response(400, "", 400 , $ims->lang['api']['error_usePromotionCode_2']);
            }
            // Giỏ hàng không rỗng
            $arr_cart_list_pro = array();            
            foreach ($arr_cart as $key => $value) {            	
            	$arr_cart_list_pro[] = $value['item_id'];
            }
            $output = $this->promotion_info($arr_cart, $promotion_code, $arr_cart_list_pro);
            if(!empty($output['ok'])){
	            $array = array(
					"code" => 200,
					"message" => 'Thành công',
					'data' => $output
				);
			}else{
				$array = array(
					"code" => 400,
					"message" => $output['mess'],
					'data' => $output
				);
			}
            $this->response(200, $array);
           
		} else {
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}

	// Sử dụng điểm tích lũy
	function useWcoin(){
		global $ims;

		if ($ims->method == 'POST'){
			$infoUser = $this->check_token_user();
			$this->setting('user');
			$this->setting('product');
			$this->load_language('global');

			$wcoin_use 	= $ims->func->if_isset($ims->post['wcoin_use']);
			$arr_cart   = $ims->func->if_isset($ims->post['cart']);
			$arr_cart   = json_decode($arr_cart);
			if($wcoin_use <= 0){
				$this->response(400, "", 400 , $ims->lang['api']['error_useWcoin_4_1']);
            }
            if(empty($arr_cart)){
            	$this->response(400, "", 400 , $ims->lang['api']['error_useWcoin_2']);
            }

            // Giỏ hàng không rỗng
            $arr_cart_list_pro = array();
            $cart_total = 0;
            foreach ($arr_cart as $key => $value) {
            	$cart_total += $value->quantity * $value->price_buy;
            	$arr_cart_list_pro[] = $value->item_id;
            }

            $wcoin_use  = (int)$wcoin_use;
			// $max_wcoin  = $ims->post['max_wcoin'];
			$max_wcoin  = round($cart_total / $ims->setting['product']['wcoin_to_money']);
			$user_wcoin = $infoUser['wcoin'];
			// $user_wcoin_expires = $infoUser['wcoin_expires'];
			if($user_wcoin < $wcoin_use){
				$this->response(400, "", 400 , $ims->lang['api']['error_useWcoin_4_2']);
			}
			// if($user_wcoin_expires < time()){
			// 	$output['mess'] = $ims->lang['global']['err_wcoin_expires'];
			// }
			if($wcoin_use > $max_wcoin){
				$wcoin_use = $max_wcoin;
			}
			$price_wcoin = $wcoin_use * $ims->setting['product']['wcoin_to_money'];
			if($wcoin_use >= 0 && $wcoin_use <= $user_wcoin){
				$total_after = $cart_total - $price_wcoin;
				$array = array(
					"code" => 200,
			    	"message" => $ims->lang['api']['success'],
					"data" => (float)$price_wcoin,
					"total_before" => $cart_total,
					"total_after" => $total_after,
	        	);
				$this->response(200, $array);
			}
		} else {
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}


	// Tính phí vận chuyển
	function getPriceShipping(){
		global $ims;
		if ($ims->method == 'POST'){
			$arr_cart = $ims->func->if_isset($ims->post['cart']);
			$province = $ims->func->if_isset($ims->post['province'], 0);
			$district = $ims->func->if_isset($ims->post['district'], 0);
			$ward 	  = $ims->func->if_isset($ims->post['ward'], 0);
			$address  = $ims->func->if_isset($ims->post['address']);
			$input['shipping'] = $ims->func->if_isset($ims->post['shipping'], 0);
			$ok = 0;
			if (isset($input['shipping'])) {
				$infoShipping = $ims->db->load_row("order_shipping", " shipping_id='".$input['shipping']."' AND is_show=1 AND lang='".$ims->conf['lang_cur']."' ");   				
				$arr_cart = json_decode($arr_cart);				
				if(empty($arr_cart)){
					$this->response(400, "", 400 , $ims->lang['api']['error_getPriceShipping_2']);
	            }
	            if ($province == 0) {
	            	$this->response(400, "", 400, $ims->lang['api']['error_getPriceShipping_4_1']);
	            }
	            if ($district == 0) {
	            	$this->response(400, "", 400, $ims->lang['api']['error_getPriceShipping_4_2']);
	            }
	            if ($ward == 0) {
	            	$this->response(400, "", 400, $ims->lang['api']['error_getPriceShipping_4_3']);
	            }
	            if ($address == '') {
	            	$this->response(400, "", 400, $ims->lang['api']['error_getPriceShipping_4_4']);
	            }	   

	            // Giỏ hàng không rỗng
	            $arr_cart_list_pro = array();
	            $input['totalProvisional'] = 0;
	            foreach ($arr_cart as $key => $value) {
	            	$input['totalProvisional'] += $value->quantity * $value->price_buy;
	            	$arr_cart_list_pro[] = $value->item_id;
	            	$arr_cart_list_op[] = $value->option_id;
	            }
	            
	            // die;
				if (!empty($infoShipping) && $input['totalProvisional']>0) {
					$totalProvisional = isset($input['totalProvisional']) ? $input['totalProvisional'] : 0;
					// Miễn ship nếu vượt quá tiền

		            if($totalProvisional >= $infoShipping['ototal_freeship']){
						$array = array(
							"code" => 200,
					    	"message" => $ims->lang['api']['success'],
							"data" => 0,
			        	);
						$ok = 1;
					}
					// GHTK 
					$products = $this->data_table ('product_option', 'ProductId', '*', " is_show=1 AND lang='".$ims->conf['lang_cur']."' AND find_in_set(ProductId,'".@implode(',', $arr_cart_list_pro)."')>0 AND find_in_set(id,'".@implode(',', $arr_cart_list_op)."')>0 ORDER BY show_order DESC, date_create ASC");
					$totalweight = 0;
					$length = 20;
					$width  = 20;
					$height = 20;
					$multiplicationMax = 0;
					if (!empty($arr_cart)) {
						foreach ($arr_cart as $key => $value) {
							$product = $products[$value->item_id];
							if (!empty($product)) {
								$totalweight += $product["Weight"] * $value->quantity;
								$multiplication = $product["Length"] * $product['Width'] * $product['Height'];
								if ($multiplicationMax>0) {
									if ($multiplication > $multiplicationMax) {
										$multiplicationMax = $multiplication;
										$length = $product["Length"];
										$width  = $product['Width'];
										$height = $product['Height'];
									}
								}else{
									$multiplicationMax = $multiplication;
									$length = $product["Length"];
									$width  = $product['Width'];
									$height = $product['Height'];
								}
							}
						}
					}   

					if ($totalweight == 0) {
						$totalweight = 1000;
					}

		            if ($infoShipping['shipping_type']=="GHTK") {
	                    $arr_connect = $ims->func->unserialize ($infoShipping['arr_connect']);
	            		$arr_option  = $ims->func->unserialize ($infoShipping['arr_option']);
			            $warehouse = $ims->db->load_row("product_order_address", "is_default=1 AND is_show=1 AND lang='".$ims->conf['lang_cur']."' ");
			            $warehouse_id = 0;
			            if (!empty($warehouse)) {
			            	$warehouse_id = $arr_connect[$warehouse['item_id']];
			            }
		            	$province    = $ims->func->if_isset($province, 0);
				        $district    = $ims->func->if_isset($district, 0);
				        $ward        = $ims->func->if_isset($ward, 0);
				        $address     = $ims->func->if_isset($address, "");
		                $data = array(
		                    "address"       	=> $address,
		                    "province"     	 	=> $this->location_name('province', $province),
		                    "district"     	 	=> $this->location_name('district', $district),
		                    "ward"          	=> $this->location_name('ward', $ward),
		                    "pick_address_id" 	=> $warehouse_id,
		                    "pick_district" 	=> $this->location_name('district', $warehouse['district']),
		                    "pick_province" 	=> $this->location_name('province', $warehouse['province']),
		                    "weight"        	=> $totalweight,
		                    "deliver_option"	=> "xfast",
		                    "transport"     	=> "fly",
		                    "value"         	=> $totalProvisional,
		                );
		                $url = $ims->conf['URL_API_GHTK'].'services/shipment/fee?'.http_build_query($data);
		                $resp = $this->sendPostDataGHTK($url, array(), 'get', $arr_option['Token']);
		                $resp = json_decode($resp);
		                // print_r($resp);
		                if (isset($resp->fee->fee)) {
		                    $array = array(
								"code" => 200,
					    		"message" => $ims->lang['api']['success'],
								"data" => $resp->fee->fee,
				        	);
							$ok = 1;
		                }else{
		                	$array = array(
								"code" => 400,
			    				"message" => "Vui lòng nhập đầy đủ thông tin nhận hàng ở bước trước!",
								"data" => 0
				        	);
		                }
		            } elseif ($infoShipping['shipping_type'] == "GHN") {
	                    $arr_connect = $ims->func->unserialize ($infoShipping['arr_connect']);
		            	$arr_option  = $ims->func->unserialize ($infoShipping['arr_option']);
			            $warehouse = $ims->db->load_row("product_order_address", "is_default=1 AND is_show=1 AND lang='".$ims->conf['lang_cur']."' ");
			            $warehouse_id = 0;
			            if (!empty($warehouse)) {
			            	$warehouse_id = $arr_connect[$warehouse['item_id']];
			            }
			            $arr_input = array(
			               "offset" => 0,
			               "limit" => 50,
			               "client_phone" => ""
			            );

			            $resp = $this->apiGHN("GetShop", $arr_input, $arr_option['Token']);
			            $from_district_id = 0;
			            foreach ($resp->data->shops as $k => $v) {
			                if ($v->_id == $warehouse_id) {
			                    $from_district_id = $v->district_id;
			                }
			            }
		            	$arr_input = array(
							"from_district_id" 	=> $from_district_id,
							"service_type_id"  	=> 2,
							"to_district_id"   	=> (int)$district,
							"to_ward_code" 	   	=> $ward,
							"weight" 			=> $totalweight,
							"height" 			=> (int)$height,
							"length" 			=> (int)$length,
							"width"  			=> (int)$width,
							"insurance_fee" 	=> 0,
							"coupon" 			=>  null
						);
		            	$resp = $this->apiGHN("Getfee", $arr_input, $arr_option['Token']);		            	
		            	if (isset($resp->data->service_fee)) {
		                    $array = array(
								"code" => 200,
					    		"message" => $ims->lang['api']['success'],
								"data" => $resp->data->service_fee,
				        	);
							$ok = 1;
		                }else{
		                    $array = array(
								"code" => 400,
					    		"message" => "Vui lòng nhập đầy đủ thông tin nhận hàng ở bước trước!",
								"data" => 0
				        	);							
		                }
		            } else{
	                    $province    = $ims->func->if_isset($province, 0);
	                    $district    = $ims->func->if_isset($district, 0);
						$check = 0;
						$output['shipping_price'] = 0;                    
						$arr_price = $ims->func->unserialize ($infoShipping['arr_price']);	                                        
						if(isset($arr_price) && is_array($arr_price) && !empty($arr_price)){
							foreach($arr_price as $value){                            
								$get_price = 0;                            
								if($value['province'] == $province && ($value['district'] == $district || $value['district'] == '')){
									$check = 1;
									$get_price = $value['value'] + $value['value1'];
									$output['shipping_price'] = $get_price;
								}
							}
						}
						if($check == 0){
							$output['shipping_price'] = $infoShipping['price'];
						}
						$array = array(
							"code" => 200,
				    		"message" => $ims->lang['api']['success'],
							"data" => (float)$output['shipping_price']
			        	);
			        	$ok = 1;						
		            }
		            $array['shipping_price0'] = $shipping_price = $array['data'];
		            //Chương trình freeship
		            $ims->site_func->setting('product');		            
		            if(!empty($ims->setting['product']['is_freeship'])){
		                $from = '';
		                $to = $address.', '.$ims->func->location_name('ward', $ward).', '.$ims->func->location_name('district', $district).', '.$ims->func->location_name('province', $province);
		                $arr_price = $ims->func->unserialize ($ims->setting['product']['arr_price']);		                
		                if(!empty($arr_price) && is_array($arr_price)){
		                    $radius = 0;
		                    $arr_check = array();
		                    foreach($arr_price as $key => $value){
		                        $radius = $value['radius'];
		                        if($value['province']==$province){	                        	
		                        	$from = $ims->db->load_item('product_order_address','is_show=1 and lang="'.$ims->conf['lang_cur'].'" and item_id="'.$value['warehouse'].'"','address');		                        	
		                            $arr_check[] = $key;
		                        }
		                    }
		                    if($input['totalProvisional']>=$ims->setting['product']['ototal_freeship']){
			                    $tmp = $ims->func->getMultiDistance($from,$to,$arr_check);
			                    if(!empty($tmp) && min($tmp)<=$radius){
			                        $array = array(
										"code" => 200,
							    		"message" => $ims->lang['api']['success'],
							    		"shipping_price0" => $shipping_price,
										"data" => 0
						        	);
						        	$ok = 1;
			                    }
		                    }
		                }
		            }
		            if($ok){
		            	$this->response(200, $array);
		            }else{
		            	$this->response(400, $array);
		            }
				}else{
					$array = array(
						"code" => 400,
					    "message" => "Tổng giá trị thanh toán quá nhỏ",
						"data" => 0
		        	);
					$this->response(400, $array);
				}
			}
		} else {
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}	


	// Api hoàn tất đặt hàng
	function orderComplete(){
		global $ims;

		$key = "2uG2mDWTQ6pWRAsOjfyZhjU0JtwhYXQ1m";
		if ($ims->method == 'POST'){
			$this->setting('product');
			$this->setting('user');
			$ims->func->load_language('global');
			$ims->func->load_language('product');

			$is_test = $ims->func->if_isset($ims->get['debug'],0);
			$errors_variable_prices = 0;
			$combo_payment = 0;

			// $infoUser = $this->check_token_user();
			$token_login = $ims->func->if_isset($ims->get['user'], 0);
			$infoUser = $ims->db->load_row('user', ' FIND_IN_SET("'.$token_login.'", token_login) ');
			if (empty($infoUser)) {				
				$infoUser['user_id'] = 0;
				$infoUser['email'] = $ims->func->if_isset($ims->post['o_email']);
				$infoUser['phone'] = $ims->func->if_isset($ims->post['o_phone']);
				$infoUser['full_name'] = $ims->func->if_isset($ims->post['o_full_name']);
			}
			$deeplink_code = $contributor_code = '';
			$referral_code = $ims->func->if_isset($ims->post['referral_code']);
			if($referral_code){
				$deeplink_code = $ims->db->load_item('user_deeplink', 'short_code = "'.$referral_code.'" and is_show = 1 ', 'short_code');
				$contributor_code = $ims->db->load_item('user', 'user_code = "'.$referral_code.'" and is_show = 1 ', 'user_code');

				if(!empty($ims->post['o_phone']) && !empty($ims->post['o_email'])){ // lưu mã giới thiệu/tiếp thị liên kết nếu chưa có
					$user_row = array();
					$check_log = $ims->db->load_row('user_recommend_log', 'is_show = 1 and (referred_phone = "'.$ims->post['o_phone'].'" or referred_email = "'.$ims->post['o_email'].'")');				
					if(!empty($check_log)){
						$user_row = $ims->db->load_row('user', 'is_show = 1 AND user_code = "'.$contributor_code.'"');
						if($check_log['referred_user_id'] == 0){							
							$ims->db->do_update('user_recommend_log', array('referred_user_id' => $infoUser['user_id']), 'id = '.$check_log);
						}
		            }elseif(!empty($deeplink_code)){
		            	$deeplink_user = $ims->db->load_row('user_deeplink', 'is_show = 1 and short_code = "'.$deeplink_code.'"', 'id, user_id, short_code');					
		                if($deeplink_user){	                	
		                    $recommend_log = array(
		                        'type' => 'deeplink',
		                        'recommend_user_id' => $deeplink_user['user_id'],
		                        'recommend_link' => $deeplink_user['short_code'],
		                        'deeplink_id' => $deeplink_user['id'],
		                        'referred_user_id' => $infoUser['user_id'],
		                        'referred_full_name' => $ims->post['o_full_name'],
		                        'referred_phone' => $ims->post['o_phone'],
		                        'referred_email' => $ims->post['o_email'],
		                        'is_show' => 1,
		                        'date_create' => time(),
		                        'date_update' => time(),
		                    );
		                    $ims->db->do_insert("user_recommend_log", $recommend_log);
		                }
		            }elseif(!empty($contributor_code) && !empty($user_row)){
		            	//contributor_code
	                    $recommend_log = array(
	                        'type' => 'contributor',
	                        'recommend_user_id' => $user_row['user_id'],
	                        'recommend_link' => 'contributor='.$ims->func->base64_encode($contributor_code).'&type=app',
	                        'referred_user_id' => $infoUser['user_id'],
	                        'referred_full_name' => $ims->post['o_full_name'],
	                        'referred_phone' => $ims->post['o_phone'],
	                        'referred_email' => $ims->post['o_email'],
	                        'is_show' => 1,
	                        'date_create' => time(),
	                        'date_update' => time(),
	                    );
	                    $ims->db->do_insert("user_recommend_log", $recommend_log);
		            }
				}		
			}

			$arr_cart 	   = $ims->func->if_isset($ims->post['cart']);
			$shipping_id   = $ims->func->if_isset($ims->post['shipping_id']);
			$method_id     = $ims->func->if_isset($ims->post['method_id']);
			$bundled 	   = $ims->func->if_isset($ims->post['bundled']);
			$promotion_code = $ims->func->if_isset($ims->post['promotion_code']);
			$arr_cart      = json_decode($arr_cart, true);
			$bundled 	   = json_decode($bundled, true);
			if ($shipping_id == "") {
				$array = array(
					"code" => 400,
				    "message" => "Phương thức vận chuyển không được rỗng"
	        	);
				$this->response(400, $array);
			}
			if ($method_id == "") {
				$array = array(
					"code" => 400,
				    "message" => "Phương thức thanh toán không được rỗng"
	        	);
				$this->response(400, $array);
			}
			if(empty($arr_cart)){
                $array = array(
                	"code" => 400,
				    "message" => "Giỏ hàng rỗng"
	        	);
				$this->response(400, $array);
            }
            $arr_invalid = array();
            $arr_cart_list_pro = array();
            foreach ($arr_cart as $k => $v) {
            	$check = $ims->db->load_row('product','is_show=1 and item_id="'.$v['item_id'].'"','id');
            	if($check){
                	$arr_cart_list_pro[] = $v['item_id'];
                }else{
                	$arr_invalid[] = $v;
                }
            }
            if(count($arr_invalid) > 0){
            	$array = array(
                	"code" => 443,
				    "message" => "Sản phẩm có sự thay đổi",
				    "data" => $arr_invalid,
	        	);
				$this->response(443, $array);
            }

            $cartProduct = $this->data_table (
	            'product',
	            'item_id', '*',
	            ' lang="'.$ims->conf['lang_cur'].'" and is_show=1 and FIND_IN_SET(item_id, "'.@implode(',', $arr_cart_list_pro).'")>0'
	        );
	        if(empty($cartProduct)){
	        	$array = array(
                	"code" => 400,
				    "message" => "Sản phẩm có sự thay đổi",
	        	);
				$this->response(400, $array);
	        }
	        $cartOption = $this->data_table(
	            'product_option',
	            'id', '*',
	            ' lang="'.$ims->conf['lang_cur'].'" and is_show=1 and FIND_IN_SET(ProductId, "'.@implode(',',$arr_cart_list_pro).'")>0 '
	        );
	        if(empty($cartProduct)){
	        	$array = array(
                	"code" => 400,
				    "message" => "Sản phẩm có sự thay đổi",
	        	);
				$this->response(400, $array);
	        }
	        $orderShipping = $this->data_table (
	            'order_shipping',
	            'shipping_id',
	            '*',
	            'lang="'.$ims->conf['lang_cur'].'" and is_show=1'
	        );
	        $orderMethod = $this->data_table (
	            'order_method',
	            'method_id',
	            '*',
	            'lang="'.$ims->conf['lang_cur'].'" and is_show=1'
	        );

			// Đặt hàng
			$arr_in = array();
			$arr_k = array('full_name','email','phone','address','province','district','ward');
			foreach($arr_k as $k) {
				$arr_in['o_'.$k] = $arr_in['d_'.$k] = (isset($ims->post['d_'.$k])) ? $ims->post['d_'.$k] : '';
			}
			
	        $recommend_type = '';
	        if(!empty($infoUser['user_id'])){
	            $info_deeplink = $ims->db->load_row('user_recommend_log', 'is_show = 1 and referred_user_id = '.$infoUser['user_id'], 'type, recommend_user_id, deeplink_id');
	            if($info_deeplink){
	                $recommend_type = $info_deeplink['type'];
	            }elseif (!empty($deeplink_code)){
	                $recommend_type = 'deeplink';
	            }elseif (!empty($contributor_code)){
	                $recommend_type = 'contributor';
	            }
            }else{
            	$recommend_type_log = $ims->db->load_row('user_recommend_log', 'is_show = 1 and referred_phone = "'.$arr_in['o_phone'].'" or referred_email = "'.$arr_in['o_email'].'"', 'type, recommend_user_id, recommend_link, deeplink_id');
	            if($recommend_type_log){
	                $recommend_type = $recommend_type_log['type'];	                
	            }elseif (!empty($deeplink_code)){
	                $recommend_type = 'deeplink';
	            }elseif (!empty($contributor_code)){
	                $recommend_type = 'contributor';
	            }
            }

            $arr_in["sales_channel"]    = 'app';
            $arr_in["order_id"]         = $ims->db->getAutoIncrement ('product_order');
            $arr_in["order_code"]       = $arr_in["order_id"] == 1 ? 100000 : ($arr_in["order_id"]+99999);
            $arr_in["shipping"]         = $shipping_id;
            $arr_in["method"]           = $method_id;
            $arr_in["request_more"]     = $ims->func->if_isset($ims->post['request_more']);
            $arr_in["user_id"]          = $ims->func->if_isset($infoUser['user_id']);
			$arr_in["show_order"]       = 0;
			$arr_in["is_show"]          = 1;
			$arr_in["date_create"]      = time();
			$arr_in["date_update"]      = time();
			$arr_in["invoice_company"]  = $ims->func->if_isset($ims->post['invoice_company']);
			$arr_in["invoice_tax_code"] = $ims->func->if_isset($ims->post['invoice_tax_code']);
			$arr_in["invoice_address"]  = $ims->func->if_isset($ims->post['invoice_address']);	
			$arr_in["invoice_email"] 	= $ims->func->if_isset($ims->post['invoice_email']);	
			$phone 						= $ims->func->if_isset($infoUser["phone"], '');

			$arr_in['is_status']        = $ims->site_func->getStatusOrder(1);
			$arr_in['is_status_payment']= 1;
			$arr_in['deeplink_id'] = 0;			
            $deeplink_user_id = 0;
            $contributor_user_id = 0;
			// Tính hoa hồng tiếp thị liên kết trên từng sản phẩm
			if($recommend_type == 'deeplink'){
				if(!empty($infoUser['user_id'])){
	                if(isset($info_deeplink) && $info_deeplink){
	                    $deeplink_user_id = $info_deeplink['recommend_user_id'];
	                    $arr_in['deeplink_id'] = $info_deeplink['deeplink_id'];
	                }
                }elseif(!empty($deeplink_code)){
                	$deeplink = $ims->db->load_row('user_deeplink', 'is_show = 1 and short_code = "'.$deeplink_code.'"', 'id,user_id');
                	$deeplink_user_id = $deeplink['user_id'];
                    $arr_in['deeplink_id'] = $deeplink["id"];
                }
                if($deeplink_user_id > 0){
                    $info_recommend_user = $ims->db->load_row('user', 'user_id = '.$deeplink_user_id.' AND is_show = 1', 'full_name, user_id');
                    $arr_in['request_more']   = 'DL_'.$info_recommend_user["full_name"].'_id_'.$info_recommend_user["user_id"].'--'.$ims->lang['product']['text_note'].' ('.date('d-m-Y H:i:s A',time()).')'.$arr_in['request_more'];
                    $arr_in['deeplink_valid'] = 1;
                    $arr_in['deeplink_user']  = $deeplink_user_id;
                }else{ // Không có deeplink
                    $arr_in['request_more'] = $phone.' '.$ims->lang['product']['text_note'].' ('.date('d-m-Y H:i:s A',time()).')'.$arr_in['request_more'];
                }
            }elseif($recommend_type == 'contributor'){                
                // $contributor_user_id = $ims->db->load_item('user_recommend_log', 'is_show = 1 and type = "contributor" and referred_user_id = '.$infoUser['user_id'], 'recommend_user_id');
                if(!empty($infoUser['user_id'])){
                    $contributor_user_id = $ims->db->load_item('user_recommend_log', 'is_show = 1 and type = "contributor" and referred_user_id = '.$infoUser['user_id'], 'recommend_user_id');
                }elseif(!empty($contributor_code)){
                    $contributor_user_id = $ims->db->load_item('user', 'is_show = 1 and user_code = "'.$contributor_code.'"', 'user_id');
                }
                if($contributor_user_id > 0){
                    $info_recommend_user = $ims->db->load_row('user', 'user_id = '.$contributor_user_id.' AND is_show = 1', 'full_name, user_id');
                    $arr_in['request_more']   = 'CT_'.$info_recommend_user["full_name"].'_id_'.$info_recommend_user["user_id"].'--'.$ims->lang['product']['text_note'].' ('.date('d-m-Y H:i:s A',time()).')'.$arr_in['request_more'];
                }else{
                    $arr_in['request_more'] = $phone.' '.$ims->lang['product']['text_note'].' ('.date('d-m-Y H:i:s A',time()).')'.$arr_in['request_more'];
                }
            }else{
                $arr_in['request_more'] = $phone.' '.$ims->lang['product']['text_note'].' ('.date('d-m-Y H:i:s A',time()).')'.$arr_in['request_more'];
            }
			if ($is_test == 1) {
				$ok = 1;
			}else{
				$check_in_stock       = $this->do_check_in_stock($arr_cart, $cartProduct, $cartOption);
            	if(in_array(0,$check_in_stock['ok'])){
					$array = array(
						'code' => 444,
						'message' => 'Sản phẩm không đủ số lượng',
						'data' => $check_in_stock['out_stock'],
					);
					$this->response(444, $array);
				}else{
					$ok = $ims->db->do_insert("product_order", $arr_in);						
				}
			}
			if ($ok) {
				$info_api = $arr_in;
				$info_gift_include = array();
				$total_order = 0;
				$totalweight = 0;
				$length 	 = 20;
				$width   	 = 20;
				$height 	 = 20;
				$multiplicationMax = 0;
				$combo_payment = 0;
	            $deeplink_total = 0; // Hoa hồng tiếp thị liên kết
	            $deeplink_total_old_temp = 0; // Hoa hồng tiếp thị liên kết tạm dành cho người mua cũ
	            $is_use_deeplink_old = 0; // Dùng deeplink cho người mua cũ
				// $arr_cart_list_pro = array();
				// Kiểm tra KH đã có đơn hàng thành công hay chưa (dành cho deeplink)
                $completed_order_status = $ims->db->load_item('product_order_status', 'is_show = 1 and lang = "'.$ims->conf['lang_cur'].'" and is_complete = 1', 'item_id');
				$check_old_order = $ims->db->load_item('product_order', 'is_show = 1 and ((user_id = '.$infoUser['user_id'].') or (user_id = 0 and (o_email = "'.$infoUser['email'].'" or o_phone = "'.$infoUser['phone'].'"))) and is_status = '.$completed_order_status, 'order_id');
				if(is_array($arr_cart) && count($arr_cart) > 0){
					foreach($arr_cart as $cart_id => $row) {
						// $arr_cart_list_pro[] = $row['item_id'];
						$row_pro = $ims->db->load_row('product','is_show=1 AND lang="'.$ims->conf['lang_cur'].'" AND item_id="'.$row['item_id'].'"');
						$row_op = $ims->db->load_row('product_option','is_show=1 AND id="'.$row['option_id'].'"');
						$totalweight += $row_pro["weight"] * $row['quantity'];
						$multiplication = $row_pro["length"] * $row_pro['width'] * $row_pro['height'];
						if ($multiplicationMax>0) {
							if ($multiplication > $multiplicationMax) {
								$multiplicationMax = $multiplication;
								$length = $row_pro["length"];
								$width  = $row_pro['width'];
								$height = $row_pro['height'];
							}
						}else{
							$multiplicationMax = $multiplication;
							$length = $row_pro["length"];
							$width  = $row_pro['width'];
							$height = $row_pro['height'];
						}	
						$col 			   = array();
						$col['order_id']   = $arr_in["order_id"];
						$col['type']       = 'product';
						$col['type_id']    = $ims->func->if_isset($row_pro['item_id'], '');
						$col['title']      = $ims->func->if_isset($row_pro['title'], '');
						$col['price_buy']  = $row_op['PriceBuy'];
						if ($row_op['PricePromotion']>0) {
							$col['price_buy'] = $row_op['PricePromotion'];
						}
						$col['quantity']   = $ims->func->if_isset($row['quantity'], 0); 
						$col['combo_id']   = $ims->func->if_isset($row['combo_id'], 0);
						$combo_info = array();
						if(!empty($row['combo_id'])){
							$combo_info = $ims->db->load_row('combo','is_show=1 and lang="'.$ims->conf['lang_cur'].'" and item_id="'.$row['combo_id'].'"');
						}
						$col['combo_info'] = $ims->func->serialize($combo_info);
						$col['option_SKU'] = $ims->func->if_isset($row_op['SKU'], '--');
						$col['option_id']  = $ims->func->if_isset($row_op['id'], ''); 
						$col['option1']    = $ims->func->if_isset($row_op['Option1'], ''); 
						$col['option2']    = $ims->func->if_isset($row_op['Option2'], ''); 
						$col['option3']    = $ims->func->if_isset($row_op['Option3'], ''); 
						$col['picture']    = $ims->func->if_isset($row_pro['picture'], '');
						//Danh sách quà hoặc sp mua kèm combo
                        $arr_gift_include = $this->do_arr_gift_include($row, $combo_info, $check_old_order, $recommend_type, $deeplink_user_id);                        
                        $info_gift_include[$col['type_id']]['include'] = !empty($arr_gift_include['arr_deeplink_include'])?$arr_gift_include['arr_deeplink_include']:array();
                        $info_gift_include[$col['type_id']]['gift'] = !empty($arr_gift_include['arr']['gift'])?$arr_gift_include['arr']['gift']:array();
                        $col['arr_gift_include'] = $ims->func->serialize($arr_gift_include['arr']);
                        $combo_payment += $arr_gift_include['add_payment']; //Cộng thêm tiền mua sp kèm combo

						if($row_op['Picture'] != ''){
							$col['picture'] = $row_op['Picture'];
						}
						$total_order += $col['price_buy'] * $col['quantity'];
						// $arr_cart_list_pro .= $col['type_id'].',';
						if ($is_test==0) {
							$ok = $ims->db->do_insert("product_order_detail", $col);
							if($ok){
                                if(isset($list_save[$row['item_id']])){
                                    unset($list_save[$row['item_id']]);
                                }
                            }
						}	
						if ($row['price_buy'] != $col['price_buy']) {
							$errors_variable_prices = 1;
						}
						if($errors_variable_prices == 0){
							$orderDetails = array();
							// Kiểm tra sản phẩm tồn tại trên kiotviet
	                        // if (empty($row_op['api_id'])) { // chưa có thì đồng bộ lên
	                        //     $api_id = $ims->site_func->do_switch_toapp(1, $row_pro['item_id'],$row_op['id']);
	                        //     if ($api_id>0) {
	                        //         $orderDetails[] = array(
	                        //             'productId'     => $api_id,
	                        //             'productCode'   => $row_op['SKU'],
	                        //             'productName'   => $row_pro['title'],
	                        //             'quantity'      => $col['quantity'],
	                        //             'price'         => $col['price_buy'],
	                        //             'discount'      => 0,
	                        //             'discountRatio' => 0,
	                        //         );
	                         //        if(!empty($info_gift_include[$col['type_id']]['gift'])){
	                         //        	foreach ($info_gift_include[$col['type_id']]['gift'] as $ki => $vi) {
		                        //     		$gift = $ims->db->load_row('user_gift','is_show=1 and lang="'.$ims->conf['lang_cur'].'" and item_id="'.$vi['item_id'].'"');
	                    					// $orderDetails[] = array(
				                      //           'productId'     => $gift['api_id'],
				                      //           'productCode'   => 'GIFT'.$gift['item_id'],
				                      //           'productName'   => $gift['title'],
				                      //           'quantity'      => 1,
				                      //           'price'         => 0,
				                      //           'discount'      => 0,
				                      //           'discountRatio' => 0,
				                      //       );	
		                        //     	}
	                         //        }
	                        //         if(!empty($info_gift_include[$col['type_id']]['include'])){
	                        //         	foreach ($info_gift_include[$col['type_id']]['include'] as $ki => $vi) {	                                		
		                       //      		$prdct = $ims->db->load_row('product','is_show=1 and lang="'.$ims->conf['lang_cur'].'" and item_id="'.$vi['item_id'].'"');
		                       //      		$order_by = ' ORDER BY date_create';
		                       //      		if(!empty($prdct['field_option'])){
		                       //      			$order_by = ' ORDER BY '.$prdct['field_option'].', date_create DESC';
		                       //      		}
		                       //      		$opt = $ims->db->load_row('product_option','is_show=1 and lang="'.$ims->conf['lang_cur'].'" and ProductId="'.$vi['item_id'].'" '.$order_by);
	                    				// 	$orderDetails[] = array(
				                     //            'productId'     => $prdct['api_id'],
				                     //            'productCode'   => $opt['SKU'],
				                     //            'productName'   => $prdct['title'],
				                     //            'quantity'      => 1,
				                     //            'price'         => 0,
				                     //            'discount'      => 0,
				                     //            'discountRatio' => 0,
				                     //        );	
		                       //      	}
	                        //         }
	                        //     }                
	                        // }else{
							if(!empty($row_op['api_id'])){
	                            $orderDetails[] = array(
	                                'productId'     => $row_op['api_id'],
	                                'productCode'   => $row_op['SKU'],
	                                'productName'   => $row_pro['title'],
	                                'quantity'      => $col['quantity'],
	                                'price'         => $col['price_buy'],
	                                'discount'      => 0,
	                                'discountRatio' => 0,
	                            );
	                        //     if(!empty($info_gift_include[$col['type_id']]['gift'])){
                         //        	foreach ($info_gift_include[$col['type_id']]['gift'] as $ki => $vi) {
	                        //     		$gift = $ims->db->load_row('user_gift','is_show=1 and lang="'.$ims->conf['lang_cur'].'" and item_id="'.$vi['item_id'].'"');
                    					// $orderDetails[] = array(
			                      //           'productId'     => $gift['api_id'],
			                      //           'productCode'   => 'GIFT'.$gift['item_id'],
			                      //           'productName'   => $gift['title'],
			                      //           'quantity'      => 1,
			                      //           'price'         => 0,
			                      //           'discount'      => 0,
			                      //           'discountRatio' => 0,
			                      //       );	
	                        //     	}
                         //        }
	                            if(!empty($info_gift_include[$col['type_id']]['include'])){	                            	
	                            	foreach ($info_gift_include[$col['type_id']]['include'] as $ki => $vi) {	                            		
	                            		$prdct = $ims->db->load_row('product','is_show=1 and lang="'.$ims->conf['lang_cur'].'" and item_id="'.$vi['item_id'].'"');
	                            		$order_by = ' ORDER BY date_create';
	                            		if(!empty($prdct['field_option'])){
	                            			$order_by = ' ORDER BY '.$prdct['field_option'].', date_create DESC';
	                            		}
	                            		$opt = $ims->db->load_row('product_option','is_show=1 and lang="'.$ims->conf['lang_cur'].'" and ProductId="'.$vi['item_id'].'" '.$order_by);
                    					$orderDetails[] = array(
			                                'productId'     => $prdct['api_id'],
			                                'productCode'   => $opt['SKU'],
			                                'productName'   => $prdct['title'],
			                                'quantity'      => 1,
			                                'price'         => $vi['price_buy_discounted'],
			                                'discount'      => 0,
			                                'discountRatio' => 0,
			                            );	
	                            	}
                                }	
	                        }
	                        
	                        // Tính hoa hồng tiếp thị liên kết trên từng sản phẩm
                            if ($recommend_type == 'deeplink' && $deeplink_user_id > 0){
                                $price_use_commisson = (float)$col['price_buy'] * (int)$col['quantity']; // Giá sản phẩm dành cho tính hoa hồng
                                
                            	if($promotion_code != ''){
                                    $promotion = $this->promotion_discount_per_item($arr_cart, $row_pro['item_id'], $price_use_commisson, $promotion_code, $arr_cart_list_pro);
                                    $price_use_commisson -= (float)$promotion['price_minus']; // Trừ tiền khuyến mãi
                                }
                                if((int)$row_pro['group_id'] == 0){
                                    $percent_deeplink_old = (float)$ims->setting['product']['percent_deeplink_default_old'];
                                    $percent_deeplink_new = (float)$ims->setting['product']['percent_deeplink_default_new'];
                                }else{
                                    $group_nav = explode(',', $row_pro['group_nav']);
                                    $group_id = $group_nav[0];
                                    $percent_deeplink_group = $ims->db->load_row('product_group', 'is_show = 1 and lang = "'.$ims->conf["lang_cur"].'" and group_id = '.$group_id, 'percent_deeplink_old, percent_deeplink_new');
                                    $percent_deeplink_old = ((float)$percent_deeplink_group['percent_deeplink_old'] > 0) ? (float)$percent_deeplink_group['percent_deeplink_old'] : (float)$ims->setting['product']['percent_deeplink_default_old'];
                                    $percent_deeplink_new = ((float)$percent_deeplink_group['percent_deeplink_new'] > 0) ? (float)$percent_deeplink_group['percent_deeplink_new'] : (float)$ims->setting['product']['percent_deeplink_default_new'];
                                }

                                if($check_old_order){
                                    $deeplink_item_tmp = ($price_use_commisson * $percent_deeplink_old/100);
                                    $deeplink_total += ($deeplink_item_tmp > (float)$ims->setting['product']['amount_deeplink_default']) ? (float)$ims->setting['product']['amount_deeplink_default'] : $deeplink_item_tmp;
                                    $is_use_deeplink_old = 1;
                                }else{
                                    $deeplink_item_new_tmp = ($price_use_commisson * $percent_deeplink_new/100);
                                    $deeplink_item_old_tmp = ($price_use_commisson * $percent_deeplink_old/100);

                                    $deeplink_total += ($deeplink_item_new_tmp > (float)$ims->setting['product']['amount_deeplink_default']) ? (float)$ims->setting['product']['amount_deeplink_default'] : $deeplink_item_new_tmp;
                                    $deeplink_total_old_temp += ($deeplink_item_old_tmp > (float)$ims->setting['product']['amount_deeplink_default']) ? (float)$ims->setting['product']['amount_deeplink_default'] : $deeplink_item_old_tmp;
                                }
                                $deeplink_total += (isset($arr_gift_include['deeplink_total_include'])) ? (float)$arr_gift_include['deeplink_total_include'] : 0;
                                $deeplink_total_old_temp += (isset($arr_gift_include['deeplink_total_include_old'])) ? (float)$arr_gift_include['deeplink_total_include_old'] : 0;

                                $deeplink_detail[] = array(
                                    'item_id' => $col['type_id'],
                                    'picture' => $col['picture'],
                                    'option_id' => $col['option_id'],
                                    'price_buy' => $col['price_buy'],
                                    'quantity' => $col['quantity'],
                                    'price_use_commisson' => $price_use_commisson,
                                    'root_group' => ($row_pro['group_id'] > 0) ? $group_id : 0,
                                    'percent_deeplink_group_old' => ((int)$row_pro['group_id'] > 0 && isset($percent_deeplink_group['percent_deeplink_old'])) ? $percent_deeplink_group['percent_deeplink_old'] : 0,
                                    'percent_deeplink_group_new' => ((int)$row_pro['group_id'] > 0 && isset($percent_deeplink_group['percent_deeplink_new'])) ? $percent_deeplink_group['percent_deeplink_new'] : 0,
                                    'percent_deeplink_default_old' => $ims->setting['product']['percent_deeplink_default_old'],
                                    'percent_deeplink_default_new' => $ims->setting['product']['percent_deeplink_default_new'],
                                    'max_deeplink_default_per_item' => $ims->setting['product']['amount_deeplink_default'],
                                    'arr_deeplink_include' => (isset($arr_gift_include['arr_deeplink_include'])) ? $arr_gift_include['arr_deeplink_include'] : ''
                                );
                            }
                        }
					}
				}
				if ($errors_variable_prices==1) {
					// Có lỗi xảy ra, giá sp đã thay đổi
					// Xóa đơn hàng + chi tiết đơn hàng
					$ims->db->query('DELETE FROM product_order WHERE order_id="'.$arr_in["order_id"].'" ');
					$ims->db->query('DELETE FROM product_order_detail WHERE order_id="'.$arr_in["order_id"].'" ');
					$array = array(
					    "code" => 400,
				    	"message" => "Đã có sản phẩm thay đổi giá, vui lòng kiểm tra lại giỏ hàng"
		        	);
					$this->response(400, $array);
				}

				// Nhập lịch sử hoa hồng theo từng sản phẩm
                if($recommend_type == 'deeplink' && $deeplink_user_id > 0){
                    $deeplink_log = array(
                        'order_id' => $arr_in["order_id"],
                        'deeplink_id' => $arr_in['deeplink_id'],
                        'order_user' => (isset($infoUser['user_id'])) ? $infoUser['user_id'] : 0,
                        'deeplink_detail' => $ims->func->serialize($deeplink_detail),
                        'commission_add' => $deeplink_total,
                        'commission_add_old_temp' => $deeplink_total_old_temp,
                        'is_show' => 1,
                        'is_added' => 0,
                        'date_create' => time(),
                        'date_update' => time(),
                    );
                    $ims->db->do_insert("user_deeplink_log", $deeplink_log);
                }

				$total_payment = $total_order;

				// Kiểm tra có mã giảm giá không
		        $promotion_code    = isset($ims->post['promotion_code']) ? $ims->post['promotion_code'] : '';
                $promotion_info    = $this->promotion_info($arr_cart, $promotion_code, $arr_cart_list_pro);
                $promotion_code    = $promotion_info['promotion_id'];
                $promotion_percent = $promotion_info['percent'];
                $promotion_price   = $promotion_info['price'];
                $err_promotion 	   = $promotion_info['mess'];
				if($promotion_price > 0) {
             		$total_payment -= $promotion_price;
				}
         		$event_promotion = array();
         		// ---------------- discount event         		
                if(!empty($promotion_info['type']) && $promotion_info['type'] == 'event'){
                    if($ims->setting['product']['is_order_discount'] == 1){
                        $event_promotion['order_discount'] = array(
                            'percent_discount' => $ims->setting['product']['percent_discount'],
                            'min_cart_item_discount' => $ims->setting['product']['min_cart_item_discount'],
                            'promotion_price' => $promotion_price
                        );
                    }
                }
                // ---------------- freeship event
                $shipping_price0 = isset($ims->post['shipping_price0']) ? $ims->post['shipping_price0'] : '';
                if($ims->setting['product']['is_freeship'] == 1){
                	$event_promotion['freeship'] = array(
                        'shipping_price' => $shipping_price0,
                        'ototal_freeship' => $ims->setting['product']['ototal_freeship'],
                        'arr_price' => $ims->setting['product']['arr_price'],
                    );
                }
				// Cộng phí vận chuyển
		        $shipping_price = isset($ims->post['shipping_price']) ? $ims->post['shipping_price'] : '';
				if($shipping_price > 0) {
					$total_payment += $shipping_price;
				}
				// ---------------- bundled event
                if(!empty($bundled) && !empty($ims->setting['product']['is_order_bundled'])){
                    foreach ($bundled as $prd){
                        $total_payment += $prd['endow_price'];
                        $total_order += $prd['endow_price'];
                    }
                    $event_promotion['bundled_product'] = $bundled;
                }

				$col_up = array();

				// Giảm giá khi sử dụng điểm
		        $wcoin_use = isset($ims->post['wcoin_use']) ? $ims->post['wcoin_use'] : '';
				if(!empty($infoUser) && $wcoin_use>0){
					$max_wcoin = $total_payment / $ims->setting['product']['wcoin_to_money'];
					if($wcoin_use > $max_wcoin){
						$wcoin_use = $max_wcoin;
					}
					$money_use_wcoin = $wcoin_use * $ims->setting['product']['wcoin_to_money'];
					$total_payment -= $money_use_wcoin;
		        	$wcoin_after = $infoUser['wcoin']-$wcoin_use;
					if($wcoin_after >=0){
						// Cập nhật lại điểm + ghi log sử dụng điểm
						$user_log['wcoin_before'] = $infoUser['wcoin'];
						$user_log['wcoin_after'] = $wcoin_after;
						$user_log['exchange_type'] = 'buy';
						$user_log['dbtable'] = 'product_order';
						$user_log['dbtable_id'] = $arr_in["order_id"];
						$user_log['value_type'] = -1;
						$user_log['note'] = $ims->lang['global']['note_use_wcoin'].$arr_in["order_code"];
						$user_log['is_show'] = 1;
						$user_log['date_create'] = time();
						$user_log['user_code'] = $infoUser['user_code'];
						$user_log['user_id'] = $infoUser['user_id'];
						$user_log['value'] = $wcoin_use;
						$ok_log = $ims->db->do_insert("user_exchange_log", $user_log);
						if($ok_log){
							$ims->db->query("UPDATE user SET wcoin=wcoin-".$wcoin_use." WHERE is_show=1 AND user_id=".$infoUser['user_id']." ");
							$col_up['payment_wcoin'] = $wcoin_use;
							$col_up['payment_wcoin2money'] = $wcoin_use*$ims->setting['product']['wcoin_to_money'];
						}
					}
				}

				$col_up['delivery_weight']  	  = $totalweight;
				$col_up['delivery_packagelength'] = $length;
				$col_up['delivery_packagewidth']  = $width;
				$col_up['delivery_packageheight'] = $height;
				$col_up["total_payment"] 		  = $total_payment + $combo_payment;
				$col_up["total_order"] 			  = $total_order + $combo_payment;
				$col_up["promotion_id"] 		  = ($promotion_info['type']=='voucher')?$promotion_code:"";
				$col_up["promotion_percent"]	  = ($promotion_info['type']=='voucher')?$promotion_percent:0;
				$col_up["promotion_price"] 		  = ($promotion_info['type']=='voucher')?$promotion_price:0;
				$col_up['event_promotion']   	  = $ims->func->serialize($event_promotion);
				$col_up['shipping_price']   	  = $shipping_price;
				$arr_in['payment'] = array();        
				if(isset($orderMethod[$arr_in['method']]) && $orderMethod[$arr_in['method']]['name_action']!='') {
                    //  Thanh toán qua ONLINE  
                    $arr_in["total_payment"] 	= $col_up["total_payment"];
					$arr_in["total_order"] 		= $col_up["total_order"];					          
                    $resultp = $this->paymentCustom($orderMethod[$arr_in['method']], $arr_in);                    
	                $col_up['is_show'] = $resultp['is_show'];
	                unset($resultp['is_show']);
	                $arr_in['payment'] = $resultp;
                }

				if($recommend_type == 'deeplink' && $deeplink_user_id != 0){ // Update hoa hồng tiếp thị liên kết vào product_order
                    $col_up['deeplink_total'] = $deeplink_total;
                    $col_up['deeplink_total_old_temp'] = $deeplink_total_old_temp;
                    $col_up['is_use_deeplink_old'] = $is_use_deeplink_old;
                }

                // Tính điểm tích lũy
                // $arr_in['wcoin_expected'] = 
				$col_up['wcoin_accumulation'] = round($total_payment * ($ims->setting['product']['percentforwcoin']/ 100) / $ims->setting['product']['money_to_wcoin']);
				// Cập nhật hoa hồng cho người giới thiệu link thường
                if($recommend_type == 'contributor' && $contributor_user_id != 0){
                    if(!empty($infoUser['user_contributor'])){
                        $col_up["user_contributor"] = $infoUser['user_contributor'];
                    }elseif(!empty($contributor_code)){
                        $col_up["user_contributor"] = $contributor_code;
                    }
                    $col_up["wcoin_contributor"] = $col_up['wcoin_accumulation'] * $ims->setting['product']['percentforcontributor']/100;
                }

                // up đơn hàng lên kiotviet
                $customer = array();
                $customer['id'] = 0;
                $token_api = $ims->site_func->getTokenKiotviet();
                if (!empty($token_api)) {
                    $url_customer = 'https://public.kiotapi.com/customers/?contactNumber='.rawurlencode($info_api['o_phone']);
                    $header = array(
                        "Retailer: " .$ims->setting['kiotviet']['retailer_kiotviet'],
	                        "Authorization: Bearer ".$token_api,
	                        "Content-Type: application/json",
                    );                    
                    $Response_customer = $ims->site_func->sendPostData($url_customer, array(), 'get', 10, '', $header);                    
                    if (!empty($Response_customer)) {                       	
                        $Response_customer = json_decode($Response_customer);
                        if (isset($Response_customer->data) && !empty($Response_customer->data)) {
                            foreach ($Response_customer->data as $k => $v) {
                                if ($info_api['o_phone'] != '') {
                                    if ($v->contactNumber == $info_api['o_phone']) {
                                        $customer['id'] = $v->id;
                                        $customer['code'] = $v->code;
                                        $customer['name'] = $v->name;
                                        $customer['contactNumber'] = $v->contactNumber;
                                        $customer['address'] = $v->address;
                                        // $customer['email'] = $v->email;
                                    }
                                }
                            }
                        }
                    }
                    if ($customer['id'] == 0) {
                        $data_customer = array(
                            "name" => $info_api['o_full_name'],
                            "contactNumber" => $info_api['o_phone'],
                            "address" => $info_api['o_address'],
                            "branchId" => $ims->setting['kiotviet']['branch_id_kiotviet'],
                            "email" => $info_api['o_email'],
                        );
                        $data_customer = json_encode($data_customer);                        
                        $Response_customer = $ims->site_func->sendPostData("https://public.kiotapi.com/customers", $data_customer, 'post', 10, '', $header);                        
                        if (!empty($Response_customer)) {
                            $Response_customer = json_decode($Response_customer);
                            if (isset($Response_customer->data) && !empty($Response_customer->data)) {
                                $tmp = $Response_customer->data;
                                $customer['id'] = $tmp->id;
                                $customer['code'] = $tmp->code;
                                $customer['name'] = $tmp->name;
                                $customer['contactNumber'] = $tmp->contactNumber;
                                $customer['address'] = $tmp->address;
                                $customer['email'] = $tmp->email;
                            }
                        }
                    }
                    if (!empty($customer['id'])) {
                        $id_surcharges = '';
                        $code_surcharges = '';
                        if (isset($col_up['shipping_price']) && $col_up['shipping_price']>0) {
                            $surcharges = $ims->site_func->sendPostData('https://public.kiotapi.com/surchages', array(), 'get', 10, '', $header);
                            if (!empty($surcharges)) {
                                $surcharges = json_decode($surcharges);
                                if (isset($surcharges->data)) {
                                    foreach ($surcharges->data as $k => $v) {
                                        if ($col_up['shipping_price'] == $v->value) {
                                            $id_surcharges = $v->id;
                                            $code_surcharges = $v->surchargeCode;
                                        }
                                    }
                                }
                            }
                        }                        
                        $data_api = array(
                            "purchaseDate" => time(),
                            "branchId" => $ims->setting['kiotviet']['branch_id_kiotviet'],
                            "discount" => $promotion_price,
                            "method" => $ims->db->load_item('order_method', ' method_id="'.$info_api['method'].'" and lang="'.$ims->conf['lang_cur'].'" ','title'),
                            "totalPayment" => 0,
                            "makeInvoice" => isset($ims->post['invoice']) ? true : false,
                            "orderDetails" => $orderDetails,
                            "description" => $info_api['request_more'],
                            "customer" => array(
                                "id" => $customer['id'],
                                "name" => $customer['name'],
                                "contactNumber" => $customer['contactNumber'],
                                "address" => $customer['address'],
                                // "email" => $customer['email'],
                            ),
                            "usingCod" => true,
                            "orderDelivery" => array(
                                'price' => $col_up['shipping_price'],
                                'receiver' => $info_api['o_full_name'],
                                "address" => $ims->func->full_address($info_api, 'o_'),
                                "contactNumber" => $info_api['o_phone'],
                                'partnerDeliveryId' => 0,
                                'partnerDelivery' => array(),
                            ),
                            "surchages" => array(
                                'id' => $id_surcharges,
                                'code' => $code_surcharges,
                            )
                        );
                        $data_api = json_encode($data_api);
                        $url_send = "https://public.kiotapi.com/orders/";

                        $Response = $ims->site_func->sendPostData($url_send, $data_api, 'post', 10, '', $header);
                        if (!empty($Response)) {                        	
                            $Response = json_decode($Response);
                            if (!empty($Response->id)) {
                                $col_up['api_id'] = $Response->id;
                                $col_up['api_code'] = $Response->code;
                                $col_up['order_code'] = $col_up['api_code'];
                                $col_up['api_branchId'] = $Response->branchId;
                                $col_up['api_branchName'] = $Response->branchName;
                                $col_up['api_retailerId'] = $Response->retailerId;
                            }else{
                                if (isset($Response->responseStatus)) {
                                    $responseStatus = $Response->responseStatus;
                                    if (strpos($responseStatus->message, 'Không đủ số lượng tồn kho cho sản phẩm') !== false) {
                                        $col_up["error_kiotviet"] = $responseStatus->message;
                                    }
                                }
                            }
                        }
                    }
                }
                if(!empty($arr_in["invoice_tax_code"])){
                	$arr_user = array();
                	$arr_user['invoice_company'] = $arr_in['invoice_company'];
                	$arr_user['invoice_tax_code'] = $arr_in['invoice_tax_code'];
                	$arr_user['invoice_address'] = $arr_in['invoice_address'];
                	$arr_user['invoice_email'] = $arr_in['invoice_email'];
                	$ims->db->do_update('user',$arr_user,' user_id="'.$infoUser['user_id'].'" ');

                    $col_up['vat_price'] = round($col_up["total_order"]*10/100,0);
                    $col_up["total_payment"] += $col_up['vat_price'];
                }
                // die;
				if ($is_test==0) {
					$ok = $ims->db->do_update("product_order", $col_up, " order_id='".$arr_in["order_id"]."'");
					if($ok){
						// Xóa giỏ hàng tạm
						$ims->db->query('DELETE FROM `product_order_temp` WHERE user_id="'.$infoUser['user_id'].'" ');
					}
					// ---------------- product_order_log
					$order_full_name 		= $arr_in['o_full_name'];
					$arr_log                = array();
			        $arr_log['is_show']     = 1;
			        $arr_log['order_id']    = $arr_in["order_id"];
			        $arr_log['date_create'] = time();
			        $arr_log['date_update'] = time();				        
			 		$arr_ins['title'] 		= $ims->site_func->get_lang('create_new_order','global',array('{order_id}' => '#'.$arr_in["order_id"], '{order_name}' => $order_full_name));
			 		// $arr_log['title'] 		= str_replace('{order_id}', '#'.$arr_in["order_id"], $arr_log['title']);
			 		// $arr_log['title'] 		= str_replace('{order_name}', $order_full_name, $arr_log['title']);
				    $ims->db->do_insert('product_order_log', $arr_log);
					// ---------------- End product_order_log

					// ---------------- promotion log
					if ($col_up["promotion_id"]!="" && $promotion_info['type']!='event') {
						$promo_log 				   = array();
						$promo_log['promotion_id'] = $col_up["promotion_id"];
						$promo_log['order_id'] 	   = $arr_in["order_id"];
						$promo_log['is_show'] 	   = 1;
						$promo_log['date_create']  = time();
						$ims->db->do_insert("promotion_log", $promo_log);
						// Update promotion 
						$ims->db->query("UPDATE promotion SET num_use=num_use+1, date_update=".time()." WHERE promotion_id='".$col_up["promotion_id"]."' ");
					}
					
					// ---------------- END promotion log
				}
				
				$invoice = "";
				if ($arr_in["invoice_company"]!='') {
					$invoice = $arr_in["invoice_company"].", ".$arr_in["invoice_tax_code"].", ".$arr_in["invoice_address"];
				}
				$infoOrder = array_merge($arr_in, $col_up);

				$mail_arr_key = array(
					'{list_cart}',
					'{o_full_name}',
					'{o_email}',
					'{o_phone}',
					'{o_address}',
					'{o_full_address}',
					'{d_full_name}',
					'{d_email}',
					'{d_phone}',
					'{d_address}',
					'{d_full_address}',
					'{shipping}',
					'{method}',
					'{request_more}',
					'{order_code}',
					'{date_create}',
					'{invoice}',
				);
				$mail_arr_value = array(
					$this->do_cart ($infoOrder, $cartProduct, $cartOption),
					$arr_in["o_full_name"],
					$arr_in["o_email"],
					$arr_in["o_phone"],
					$arr_in["o_address"],
					$this->full_address($arr_in, 'o_'),
					$arr_in["d_full_name"],
					$arr_in["d_email"],
					$arr_in["d_phone"],
					$arr_in["d_address"],
					$this->full_address($arr_in, 'd_'),
					$ims->db->load_item('order_shipping', 'shipping_id="'.$arr_in['shipping'].'" AND lang="'.$ims->conf['lang_cur'].'" AND is_show=1' ,'title'),
					$ims->db->load_item('order_method', 'method_id="'.$arr_in['method'].'" AND lang="'.$ims->conf['lang_cur'].'" AND is_show=1' ,'title'),
					$arr_in["request_more"],
					$arr_in["order_code"],
					$this->get_date_format($arr_in["date_create"]),
					$invoice
				);
				if ($is_test==0) {
					// GUI EMAIL THONG TIN DON HANG CHO ADMIN
					$this->send_mail_temp ('admin-ordering-complete', $ims->conf['email'], $ims->conf['email'], $mail_arr_key, $mail_arr_value);
					// GUI EMAIL CHO NGUOI MUA HANG
					if(isset($arr_in['o_email']) && $arr_in['o_email'] != ''){
						$this->send_mail_temp ('ordering-complete', $arr_in['o_email'], $ims->conf['email'], $mail_arr_key, $mail_arr_value);
					}else{ //Gửi tin nhắn sms cho khách hàng vãng lai không nhập email
						if (empty($infoUser['user_id'])) {
							$ims->site_func->setting('user');
							$sms_content = str_replace('{total}', $col_up['total_payment'], $ims->setting['user']['esms_Contentorder']);
							$sms_content = str_replace('{datetime}', date('H:i d/m/Y',time()), $sms_content);
							$sms_content = str_replace('{order}', !empty($col_up['order_code'])?$col_up['order_code']:$arr_in["order_code"], $sms_content);							
					        $data_sms = array(
					            'ApiKey'    => $ims->setting['user']['esms_ApiKey'],
					            'SecretKey' => $ims->setting['user']['esms_SecretKey'],
					            'Brandname' => $ims->setting['user']['esms_Brandname'],
					            'Phone'     => $arr_in["o_phone"],
					            'Content'   => $sms_content,
					            'SmsType'   => 2,
					            'Sandbox'   => 0,
					        );
					        
					        $data_sms = http_build_query ($data_sms);
					        $curl = curl_init();
					        $header = array("Content-Type:application/x-www-form-urlencoded");
					        curl_setopt_array($curl, array(
			                    CURLOPT_RETURNTRANSFER  => 1,
			                    CURLOPT_URL             => 'http://rest.esms.vn/MainService.svc/json/SendMultipleMessage_V4_post/',
			                    CURLOPT_POST            => 1,
			                    CURLOPT_HTTPHEADER      => $header,
			                    CURLOPT_SSL_VERIFYPEER  => 0,
			                    CURLOPT_POSTFIELDS      => $data_sms
			                ));
			                $resp = curl_exec($curl);
			        		curl_close($curl);
					        $SMS = $resp;  			  					        
					        if (!empty($SMS)) {
					            $SMS = json_decode($SMS);	            
					            if (isset($SMS->CodeResult) && $SMS->CodeResult==100) {
					            	$infoOrder['sms'] = $ims->lang['api']['success'];
					            }else{
					            	$infoOrder['sms'] = "Có lỗi xảy ra";
					            }
					        }else{
					        	$infoOrder['sms'] = "Có lỗi xảy ra";
					        }
						}
					}
				}
				$array = array(
					"code" => 200,
				    "message" => $ims->lang['api']['success'],
					"data" => $infoOrder,
	        	);
				$this->response(200, $array);
			}else{
				$array = array(
					"code" => 400,
				    "message" => "Có lỗi xảy ra"
	        	);
				$this->response(400, $array);
			}
		} else {
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}

	
	/*
		* Lấy hình thức giao hàng
		* Method: GET
		* Content-Type: application/x-www-form-urlencoded
		* Body: 
	*/
	function getOrderShipping(){
		global $ims;

		if ($ims->method == 'GET'){
			$arr = $ims->db->load_item_arr('order_shipping','is_show=1 AND lang="'.$ims->conf['lang_cur'].'" ORDER BY show_order DESC, date_update DESC', 'shipping_id,shipping_type,is_connect,title,content');
			if (!empty($arr)) {
	            foreach ($arr as $key => $value) {
	                $arr[$key]['content'] = $this->short_no_cut($value['content']);
	            	if ($value['shipping_type'] != "") {
	            		if ($value['is_connect'] == 0) {
	            			unset($arr[$key]);
	            		}
	            	}
	            	unset($arr[$key]['shipping_type']);
	            	unset($arr[$key]['is_connect']);
	            }
	        }
			$array = array(
				"code" => 200,
			    "message" => $ims->lang['api']['success'],
				"data" => $arr
        	);
			$this->response(200, $array);
		} else {
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}


	/*
		* Lấy hình thức thanh toán
		* Method: GET
		* Content-Type: application/x-www-form-urlencoded
		* Body: 
	*/
	function getOrderMethod(){
		global $ims;

		if ($ims->method == 'GET'){
			$arr = $ims->db->load_item_arr('order_method','is_show=1 AND lang="'.$ims->conf['lang_cur'].'" ORDER BY show_order DESC, date_update DESC','method_id,name_action,arr_option,is_connect,title,content');
			if (!empty($arr)) {
	            foreach ($arr as $key => $value) {
	            	$option = $ims->func->unserialize($value['arr_option']);
	            	if (!empty($option)) {
	            		$arr[$key]['option'] = $option;
	            	}
	                $arr[$key]['content'] = $this->short_no_cut($value['content']);
	                if ($value['name_action'] != "") {
	            		if ($value['is_connect'] == 0) {
	            			unset($arr[$key]);
	            		}
	            	}
	            	unset($arr[$key]['arr_option']);
	            	unset($arr[$key]['is_connect']);
	            }
	        }
			$array = array(
				"code" => 200,
			    "message" => $ims->lang['api']['success'],
				"data" => $arr
        	);
			$this->response(200, $array);
		} else {
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}


	/*
		* Lấy danh sách tin tức
		* Method: GET
		* Content-Type: application/x-www-form-urlencoded
		* Body: 
	*/
	function getNews(){
		global $ims;

		if ($ims->method == 'GET') {
			$data = array (
				'modules' => 'news',
				'table' => 'news',
				'column' => array (
					array(
						'key' => 'item_id',
						'type' => 'number',
					),
					array(
						'key' => 'group_id',
						'type' => 'number',
					),
					array(
						'key' => 'picture',
						'type' => 'picture',
					),
					array(
						'key' => 'date_update',
						'type' => 'datetime',
					),
					array(
						'key' => 'title',
						'type' => 'title',
					),
					array(
						'key' => 'short',
						'type' => 'editor',
					),
					array(
						'key' => 'content',
						'type' => 'editor',
					)
				),
				'arr_related' => 1,
			);
			$this->returnsPaging($data);
		} else {
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}


	/*
		* Lấy danh sách tuyển dụng
		* Method: GET
		* Content-Type: application/x-www-form-urlencoded
		* Body: 
	*/
	function getRecruitment(){
		global $ims;

		if ($ims->method == 'GET') {
			$data = array (
				'modules' => 'recruitment',
				'table' => 'recruitment',
				'column' => array (
					array(
						'key' => 'item_id',
						'type' => 'number',
					),
					array(
						'key' => 'group_id',
						'type' => 'number',
					),
					array(
						'key' => 'picture',
						'type' => 'picture',
					),
					array(
						'key' => 'date_update',
						'type' => 'datetime',
					),
					array(
						'key' => 'title',
						'type' => 'title',
					),
					array(
						'key' => 'salary',
						'type' => 'number',
					),
					array(
						'key' => 'quantity',
						'type' => 'number',
					),
					array(
						'key' => 'province',
						'type' => 'province',
					),
					array(
						'key' => 'email',
						'type' => 'email',
					),
					array(
						'key' => 'short',
						'type' => 'editor',
					),
					array(
						'key' => 'content',
						'type' => 'editor',
					)
				),
				'arr_related' => 1,
			);
			$this->returnsPaging($data);
		} else {
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}


	/*
		* Lấy danh sách giới thiệu
		* Method: GET
		* Content-Type: application/x-www-form-urlencoded
		* Body: 
	*/
	function getAbout(){
		global $ims;

		if ($ims->method == 'GET'){
			$arr_return = array();
			$row = $ims->db->load_row('about','is_show=1 AND lang="'.$ims->conf['lang_cur'].'" ORDER BY show_order DESC, date_update DESC');
			if (!empty($row)) {
	            $arr_return['title'] = $row['title'];
	            $arr_return['content'] = $ims->func->input_editor_decode($row['content']);
	        }
			$array = array(
				"code" => 200,
			    "message" => $ims->lang['api']['success'],
				"data" => $arr_return
        	);
			$this->response(200, $array);
		} else {
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}

	/*
		* Lấy nội dung trang chủ
		* URL : api.php/getHome
		* Method: GET			
	*/
	function getHomeFocus(){
		global $ims;
		if ($ims->method == 'GET'){			
			if(!empty($ims->get['user'])){
				$infoUser = $ims->db->load_row('user','find_in_set("'.$ims->get['user'].'",token_login)>0','*');
	            $arr_favorite = array();
	            if (!empty($infoUser)) {
	                $favorite = $ims->db->load_row_arr('shared_favorite', ' type="product" AND user_id="'.$infoUser['user_id'].'" AND is_show=1 ');
	                if (!empty($favorite)) {
	                    foreach ($favorite as $k => $v) {
	                        $arr_favorite[$v['type_id']] = $v;
	                    }
	                }
	            }
            }
            $arr_home = array();
			
			$numshow = isset($ims->get['numshow']) ? $ims->get['numshow'] : 0;
			$n = 6;
			if ($numshow>0) {
				$n = $numshow;
			}
			if ($numshow>100) {
				$n = 100;
			}

			$arr_focus = $ims->db->load_item_arr('product','is_show=1 and lang="'.$ims->conf['lang_cur'].'" and is_focus = 1 order by show_order desc, date_create desc','group_id');
			if($arr_focus){
				foreach ($arr_focus as $gr) {
					$group[] = $gr['group_id'];
				}
				$group = (array_unique($group));
            	$group = implode(',', $group);	
            	$list_group = $ims->db->load_item_arr('product_group', 'is_show=1 and lang="'.$ims->conf['lang_cur'].'" and group_id IN('.$group.') order by show_order desc, date_create desc limit 5', 'title, group_id');
            	if($list_group){
            		foreach ($list_group as $key => $value) {
            			$arr_product = $ims->db->load_item_arr('product','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and find_in_set("'.$value["group_id"].'",group_nav)>0 ORDER BY show_order desc, date_create desc limit 0,'.$n,'item_id, group_id, picture, title, price, price_buy, price_promotion, percent_discount, num_view, average_rating');
						if($arr_product){
							foreach ($arr_product as $k => $v) {
								$arr_product[$k]['title'] = $ims->func->input_editor_decode($v['title']);
					        	$arr_product[$k]['rating'] 	  = $v['average_rating'];
								$arr_product[$k]['is_favorite'] = isset($arr_favorite[$v['item_id']]) ? 1 : 0;
								$arr_product[$k]['picture'] 	  = $ims->func->get_src_mod($v['picture']);
								$arr_product[$k]['thumbnail']   = $ims->func->get_src_mod($v['picture'], 40, 40 , 1, 1);
								if ($v['price_promotion']>0) {
									$v['price_buy'] = $v['price_promotion'];
								}
								unset($arr_product[$k]['price_promotion']);
								unset($arr_product[$k]['average_rating']);
							}							
						}
						$arr_home[$key] = array(
        					'title' => $ims->func->input_editor_decode($value['title']),
        					'group_id' => $value['group_id'],
        					'data' => $arr_product,
        				);
            		}
            	}
			}
            $array = array(
    			'code' => 200,
    			'message' => "Thành công",
    			'data' => $arr_home,
	    	);
			$this->response(200, $array);
		}else{
			$this->response(405, "", 405 , $ims->lang['api']['error_method']);
		}
	}

	function getHomeProduct(){
		global $ims;
		if ($ims->method == 'GET'){			
			if(!empty($ims->get['user'])){
				$infoUser = $ims->db->load_row('user','find_in_set("'.$ims->get['user'].'",token_login)>0','*');
	            $arr_favorite = array();
	            if (!empty($infoUser)) {
	                $favorite = $ims->db->load_row_arr('shared_favorite', ' type="product" AND user_id="'.$infoUser['user_id'].'" AND is_show=1 ');
	                if (!empty($favorite)) {
	                    foreach ($favorite as $k => $v) {
	                        $arr_favorite[$v['type_id']] = $v;
	                    }
	                }
	            }
            }

			$arr_home = array();
			
			$numshow = isset($ims->get['numshow']) ? $ims->get['numshow'] : 0;
			$n = 6;
			if ($numshow>0) {
				$n = $numshow;
			}
			if ($numshow>100) {
				$n = 100;
			}

	        $arr_group = $ims->db->load_row_arr("product_group","lang='".$ims->conf['lang_cur']."' and is_show=1 and is_app=1 and group_level=1 order by show_order desc, date_create asc");
	        if($arr_group){
	        	foreach ($arr_group as $key => $value) {
	        		$arr_home[$key]['title'] = $value["title"];
			    	$arr_home[$key]['group_id'] = $value["group_id"];			    						
	        		$group_child = $ims->db->load_row_arr("product_group","lang='".$ims->conf['lang_cur']."' and is_show=1 and parent_id='".$value['group_id']."' order by show_order desc, date_create asc");
	        		if($group_child){	        			
	        			foreach ($group_child as $child) {	        				
	        				$arr_product = $ims->db->load_item_arr('product','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and find_in_set("'.$child["group_id"].'",group_nav)>0 ORDER BY show_order desc, date_create desc limit 0,'.$n,'item_id, group_id, picture, title, price, price_buy, price_promotion, percent_discount, num_view, average_rating');
							if($arr_product){
								foreach ($arr_product as $k => $v) {
									$arr_product[$k]['title'] = $ims->func->input_editor_decode($v['title']);
						        	$arr_product[$k]['rating'] 	  = $v['average_rating'];
									$arr_product[$k]['is_favorite'] = isset($arr_favorite[$v['item_id']]) ? 1 : 0;
									$arr_product[$k]['picture'] 	  = $ims->func->get_src_mod($v['picture']);
									$arr_product[$k]['thumbnail']   = $ims->func->get_src_mod($v['picture'], 40, 40 , 1, 1);
									if ($v['price_promotion']>0) {
										$v['price_buy'] = $v['price_promotion'];
									}
									unset($arr_product[$k]['price_promotion']);
									unset($arr_product[$k]['average_rating']);
								}
								$arr_home[$key]['group_child'][] = array(
		        					'title' => $ims->func->input_editor_decode($child['title']),
		        					'group_id' => $child['group_id'],
		        					'data' => $arr_product,
		        				);
							}else{								
							}
	        			}	        			
	        		}
	        	}
	        }
			

			$array = array(
    			'code' => 200,
    			'message' => "Thành công",
    			'data' => $arr_home,
	    	);
			$this->response(200, $array);
		}else{
			$this->response(405, "", 405 , $ims->lang['api']['error_method']);
		}
	}


	/*
		* Lấy danh sách liên hệ
		* Method: GET
		* Content-Type: application/x-www-form-urlencoded
		* Body: 
	*/
	function getContact(){
		global $ims;

		if ($ims->method == 'GET'){
			$this->setting('contact');
			$content = '';
			$row = $ims->db->load_row('contact_map','is_show=1 AND lang="'.$ims->conf['lang_cur'].'" ORDER BY show_order DESC, date_update DESC');
			if (!empty($row)) {
	            $content = $ims->func->input_editor_decode($row['content']);
	        }
			$array = array(
				"code" => 200,
			    "message" => $ims->lang['api']['success'],
				"data" => array(
					'title' => $ims->setting['contact']['contact_meta_title'],
					'content' => $content
				)
        	);
			$this->response(200, $array);
		} else {
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}

	/*
		* Gửi liên hệ
		* Method: POST
		* Content-Type: application/x-www-form-urlencoded
		* Body: 
		*     full_name = HO_VA_TEN
		*     email = EMAIL
		*     address = DIA_CHI
		*     phone = SO_DIEN_THOAI
		*     title = TIEU_DE
		*     content = NOI_DUNG
	*/
	function sendContact(){
		global $ims;

		if ($ims->method == 'POST'){
			$arr_in = array();
			$arr_key = array('full_name','email','address','phone','title','content');
			foreach($arr_key as $key) {
				$arr_in[$key] = (isset($ims->post[$key])) ? $ims->post[$key] : '';
			}
			if(count($arr_in) > 0) {
				$arr_in["is_status"] = 0;
				$arr_in["date_create"] = time();
				$arr_in["date_update"] = time();
                $ok = $ims->db->do_insert("contact", $arr_in);
				if($ok) {				
					$mail_arr_value = $arr_in;
					$mail_arr_value['date_create'] = $ims->func->get_date_format($mail_arr_value["date_create"]);
					$mail_arr_value['domain'] = $_SERVER['HTTP_HOST'];
					$mail_arr_key = array();
					foreach($mail_arr_value as $k => $v) {
						$mail_arr_key[$k] = '{'.$k.'}';
					}
					// Send to admin
					$this->send_mail_temp ('admin-contact', $ims->conf['email'], $ims->conf['email'], $mail_arr_key, $mail_arr_value);
					// Send to contact
					$this->send_mail_temp ('contact', $arr_in["email"], $ims->conf['email'], $mail_arr_key, $mail_arr_value);
					$array = array(
						"code" => 200,
					    "message" => $ims->lang['api']['success']
		        	);
					$this->response(200, $array);
				}
			}
		} else {
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}

	/*
		* Lấy thông tin tài khoản theo TOKEN_LOGIN
		* URL : api.php/getUser?user=TOKEN_LOGIN
		* Method: GET
		* Content-Type: application/x-www-form-urlencoded
		* Body: 
		* Error: -1 => Sai phương thức
		* Error: 0 => Thành công
		* Error: 1 => Sai token hoặc hết hạn
		* Error: 2 => Không tìm thấy tài khoản
	*/
	function getUser(){
		global $ims;

		if ($ims->method == 'GET'){
			$infoUser = $this->check_token_user();
			unset($infoUser['password']);
			unset($infoUser['list_cart']);
			unset($infoUser['list_save']);
			unset($infoUser['list_favorite']);
			unset($infoUser['list_watched']);
			unset($infoUser['link_shorten']);
			unset($infoUser['token_login']);
			unset($infoUser['admin_id']);
			unset($infoUser['admin_full_name']);
			unset($infoUser['user_contributor']);
			unset($infoUser['user_contributor_level']);
			unset($infoUser['type_contributor']);
			unset($infoUser['arr_address_book']);			
			unset($infoUser['folder_upload']);
			unset($infoUser['show_order']);
			unset($infoUser['wcoin_expires']);
			unset($infoUser['code_authentic']);
			unset($infoUser['pass_reset']);
			unset($infoUser['email_change']);
			unset($infoUser['fb_id']);
			unset($infoUser['ap_id']);
			unset($infoUser['gg_id']);
			unset($infoUser['root_id']);
			$infoUser['picture'] = $ims->func->get_src_mod($infoUser['picture']);
			$infoUser['link_invitaion'] = $ims->conf['rooturl_web'].'redirect/profile/'.$ims->func->base64_encode($infoUser['user_id']);
			$infoUser['code_invitaion'] = $infoUser['user_code'];
			unset($infoUser['user_code']);
			$array = array(
				"code" => 200,
		    	"message" => $ims->lang['api']['error_getUser_0'],
        		'data' => $infoUser
        	);
			$this->response(200, $array);
		} else {
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}

	function paymentConfirm(){
		global $ims;

		if ($ims->method == 'POST') {
			$payment_type 	= $ims->func->if_isset($ims->post['payment_type']);
			switch ($payment_type) {
				case 'momo':
					

					$method = $ims->db->load_row('order_method',' is_show=1 AND lang="'.$ims->conf['lang_cur'].'" AND name_action="'. $payment_type .'" ');
            		$config = $ims->func->unserialize($method['arr_option']);
            		$secretKey = $ims->func->if_isset($config['SecretKey']);
            		$PublicKey = $ims->func->if_isset($config['PublicKey']);


					$partnerCode 	= $ims->func->if_isset($ims->post['partnerCode']);
					$partnerRefId 	= $ims->func->if_isset($ims->post['partnerRefId']);
					$customerNumber = $ims->func->if_isset($ims->post['customerNumber']);
					$appData 		= $ims->func->if_isset($ims->post['appData']);
					$version 		= $ims->func->if_isset($ims->post['version']);
					$payType 		= $ims->func->if_isset($ims->post['payType']);
					$description 	= $ims->func->if_isset($ims->post['description']);
					$extra_data 	= $ims->func->if_isset($ims->post['extra_data']);
					$amount 	    = $ims->func->if_isset($ims->post['amount']);

					$curl = curl_init();
					$header = array(
						 "Content-Type: application/json",
		            );

					$publicKey = "-----BEGIN PUBLIC KEY-----
								".$PublicKey."
								-----END PUBLIC KEY-----";

					$plaintext = '{"partnerCode": "'.$partnerCode.'","partnerRefId": "'.$partnerRefId.'","amount": '.$amount.'}';
					$url = 'https://test-payment.momo.vn/pay/app';
					$hash = $ims->func->createRSA($publicKey, $plaintext);
					$post = array(
						"partnerCode" => $partnerCode,
						"customerNumber" => $customerNumber,
						"partnerRefId" => $partnerRefId,
						"appData" => $appData,
						"hash" => $hash,
						"description" => $description,
						"version" => (int)$version,
						"payType" => (int)$payType,
						"extra_data" => $extra_data,
					);
		            $post = json_encode($post);
		            curl_setopt_array($curl, array(
		                CURLOPT_RETURNTRANSFER  => 1,
		                CURLOPT_URL             => $url,
		                CURLOPT_POST            => 1,
		                CURLOPT_HTTPHEADER      => $header,
		                CURLOPT_SSL_VERIFYPEER  => 0,
		                CURLOPT_POSTFIELDS      => $post
		            ));
		            $resp = curl_exec($curl);
		        	curl_close($curl);
		        	if (!empty($resp)) {
		        		$resp = json_decode($resp);
		        		if (isset($resp->status) && $resp->status==0) {
		        			// Call confirm
		        			$url = 'https://test-payment.momo.vn/pay/confirm';
		        			$curl = curl_init();
							$header = array(
								 "Content-Type: application/json",
				            );

				            $post = array(
								"partnerCode" => $partnerCode,
								"partnerRefId" => $partnerRefId,
								"requestType" =>  "capture",
								"requestId" => time()."000",
								"momoTransId" => $resp->transid,
								"customerNumber" => $customerNumber,
							);
				            $rawHash = "partnerCode=".$post['partnerCode']."&partnerRefId=".$post['partnerRefId']."&requestType=capture&requestId=".$post['requestId']."&momoTransId=".$post['momoTransId']."";
				            $signature = hash_hmac("sha256", $rawHash, $secretKey);
				            $post['signature'] = $signature;
				            $post = json_encode($post);
				            curl_setopt_array($curl, array(
				                CURLOPT_RETURNTRANSFER  => 1,
				                CURLOPT_URL             => $url,
				                CURLOPT_POST            => 1,
				                CURLOPT_HTTPHEADER      => $header,
				                CURLOPT_SSL_VERIFYPEER  => 0,
				                CURLOPT_POSTFIELDS      => $post
				            ));
				            $resp = curl_exec($curl);
				        	curl_close($curl);
				        	if (!empty($resp)) {
				        		$resp = json_decode($resp);
				        		if (isset($resp->status) && $resp->status==0) {
				        			// Cập nhật lại cho đơn hàng thanh toán thành công
				        			$Order = $ims->db->load_row("product_order", "order_code='".$partnerRefId."'");
				                    if (!empty($Order)) {
				                        $arr_payment = array(
				                            'is_status_payment' => $ims->db->load_item('product_order_status_payment', 'is_show=1 AND is_complete=1 AND lang="'.$ims->conf['lang_cur'].'" ', 'item_id'),
				                            'is_ConfirmOrder' => 1,
				                            'is_ConfirmPayment' => 1,
				                            'transaction_id' => $ims->func->if_isset($resp->data->momoTransId),
				                        );
				                        $ims->db->do_update('product_order', $arr_payment,' order_code ="'.$Order['order_code'].'" ');
				                    }
				        			$array = array(
						  	    		"code" => 200,
	    								"message" => $ims->lang['api']['success'],
						        		"data" => $resp
						        	);
									$this->response(200, $array);
				        		}
				        	}
		        		}else{
							$this->response(200, "", 200 , $resp->message);
		        		}
		        	}else{
						$this->response(200, "", 200 , 'Có lỗi xảy ra');
		        	}
					break;
				default:
					break;
			}
		}else{
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}


	// Lấy mã khuyến mãi theo thành viên
	function getPromotionUser(){
		global $ims;

		if ($ims->method == 'GET'){
			$user    = $ims->func->if_isset($ims->get['user']);
			$numshow = $ims->func->if_isset($ims->get['numshow'], 0);
			$p 		 = $ims->func->if_isset($ims->get['p'], 1);
			$f 		 = $ims->func->if_isset($ims->get['filter']);
			$n = 50;
			if ($numshow>0) {
				$n = $numshow;
			}
			if ($numshow>500) {
				$n = 50;
			}
			$where = '';
			if(!empty($user)){
				$infoUser = $this->check_token_user();
				$where .= "AND (type_promotion = 'apply_all'
							OR (type_promotion = 'apply_user' AND FIND_IN_SET('".$infoUser['user_id']."', list_user))
							OR (type_promotion = 'apply_email' AND FIND_IN_SET('".$infoUser['email']."', list_email))
							OR type_promotion = 'apply_product'
							OR type_promotion = 'apply_freeship')";
			}else{
				$where .= "AND (type_promotion = 'apply_all'
							OR type_promotion = 'apply_product'
							OR type_promotion = 'apply_freeship')";
			}

			if(!empty($f)){
				switch ($f) {
					case 'new':
						$where .= " AND num_use < max_use_total AND date_end > '".time()."' ";
						break;
					case 'old':						
						$where .= " AND (num_use >= max_use_total OR date_end < '".time()."') ";
					default:
						// code...
						break;
				}
			}
	     	
			$res_num =$ims->db->query("SELECT promotion_id FROM promotion where is_show=1 ".$where." ");
	        $num_total = $ims->db->num_rows($res_num);
	        $num_items = ceil($num_total / $n);
	        if ($p > $num_items)
	            $p = $num_items;
	        if ($p < 1)
	            $p = 1;
	        $start = ($p - 1) * $n;
	        // echo 'is_show=1 '.$where.' LIMIT '.$start.', '.$n;
			$arr = $ims->db->load_item_arr('promotion','is_show=1 '.$where.' ORDER BY order_index asc, date_end asc LIMIT '.$start.', '.$n, 'promotion_id,type_promotion,max_use,date_start,date_end,value_type,value,short,picture,num_use,total_min,value_max,date_create, 
				CASE WHEN date_end > "'.time().'" AND num_use < max_use THEN 1 ELSE 2 END as order_index');
			if (!empty($arr)) {
	            foreach ($arr as $key => $value) {
	            	if ($value['picture'] != '') {
						$arr[$key]['picture'] 	  = $ims->func->get_src_mod($value['picture']);
	            	}
	                $arr[$key]['short'] = $ims->func->input_editor_decode($value['short']);	                
	            }
	        }
			$array = array(
        		"code" => 200,
	    		"message" => $ims->lang['api']['success'],
				"total_page" => $num_items,
				'total' => $num_total,
				'numshow' => $n,
	    		'page' => $p,
	    		'data' => $arr,
        	);
			$this->response(200, $array);
		}else{
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}

	// Lấy lịch sử dùng điểm theo thành viên user_exchange_log
	function getUserExchangeLog(){
		global $ims;

		if ($ims->method == 'GET'){
			$user    = $ims->func->if_isset($ims->get['user']);
			$numshow = $ims->func->if_isset($ims->get['numshow'], 0);
			$p 		 = $ims->func->if_isset($ims->get['p'], 1);
			$n = 50;
			if ($numshow>0) {
				$n = $numshow;
			}
			if ($numshow>500) {
				$n = 50;
			}
			$where = '';
			if(!empty($user)){
				$infoUser = $this->check_token_user();
				$where .= "AND FIND_IN_SET('" . $infoUser['user_id'] . "', user_id)";
			}
	     	
			$res_num =$ims->db->query("SELECT id FROM user_exchange_log where is_show=1 ".$where." ");
	        $num_total = $ims->db->num_rows($res_num);
	        $num_items = ceil($num_total / $n);
	        if ($p > $num_items)
	            $p = $num_items;
	        if ($p < 1)
	            $p = 1;
	        $start = ($p - 1) * $n;

			$arr = $ims->db->load_item_arr('user_exchange_log','is_show=1 '.$where.' LIMIT '.$start.', '.$n, 'value_type,value,note,date_create');
			if (!empty($arr)) {
	            foreach ($arr as $key => $value) {
					
	            }
	        }
			$array = array(
        		"code" => 200,
	    		"message" => $ims->lang['api']['success'],
				"total_page" => $num_items,
				'total' => $num_total,
				'numshow' => $n,
	    		'page' => $p,
	    		'data' => $arr,
        	);
			$this->response(200, $array);
		}else{
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}


	// Quản lý điểm tích lũy
	function getUserWcoinLog(){
		global $ims;
		if ($ims->method == 'GET'){
			$infoUser = $this->check_token_user();

			$numshow = $ims->func->if_isset($ims->get['numshow'],0);
            $p = $ims->func->if_isset($ims->get['p'], 1);
			$search_date_end = $ims->func->if_isset($ims->get["search_date_end"]);		
			$search_date_begin = $ims->func->if_isset($ims->get["search_date_begin"]);
						
			$where = '';			
			$where .= " is_show = 1 AND user_id='".$infoUser['user_id']."' ";

			$where_date = '';
			if($search_date_begin || $search_date_end ){
				$tmp1 = @explode("/", $search_date_begin);
				$time_begin = @mktime(0, 0, 0, $tmp1[1], $tmp1[0], $tmp1[2]);
				
				$tmp2 = @explode("/", $search_date_end);
				$time_end = @mktime(23, 59, 59, $tmp2[1], $tmp2[0], $tmp2[2]);
				
				$where_date .=" AND (date_create BETWEEN {$time_begin} AND {$time_end} ) ";
			}
			// Những đơn hàng chưa hoàn tất và chưa hủy
	        $list_order_not_complete = $ims->db->load_item_arr('product_order', 'is_show = 1 and is_status NOT IN(17,27,29,31) and user_id = '.$infoUser['user_id'], 'order_id');
	        if($list_order_not_complete){
	            foreach ($list_order_not_complete as $itm){
	                $where_order_not_complete[] = ' dbtable_id = "'.$itm['order_id'].'"';
	            }
	            $where_order_not_complete = implode(' OR ', $where_order_not_complete);
	            $where_not_complete = ' and user_id = '.$infoUser['user_id'].' and dbtable = "product_order" and ('.$where_order_not_complete.')';
	        }
	    	// Chỉ lấy những đơn hàng đã hoàn tất
	        $complete_status = $ims->db->load_item('product_order_status', 'is_show=1 and lang="'.$ims->conf['lang_cur'].'" and is_complete = 1', 'item_id');
	        $list_order_id_tmp = $ims->db->load_item_arr('product_order', 'is_show = 1 and is_status = '.$complete_status.' and user_id = '.$infoUser['user_id'], 'order_code');
	        if($list_order_id_tmp){
	            foreach ($list_order_id_tmp as $it){
	                $where_order[] = ' dbtable_id = "'.$it['order_code'].'"';
	            }
	            $where_order = implode(' OR ', $where_order);
	            $where .= ' and (dbtable != "product_order" or (dbtable = "product_order" and ('.$where_order.')))';
	        }

			$num_total = 0;
			$res_num = $ims->db->query("SELECT id FROM user_exchange_log WHERE ".$where.$where_date."  ");
				$num_total = $ims->db->num_rows($res_num);
			$n = 20;
			if ($numshow>0) {
				$n = $numshow;
			}
			if ($numshow>50) {
				$n = 50;
			}
			$num_items = ceil($num_total / $n);
			if ($p > $num_items)
			  $p = $num_items;
			if ($p < 1)
			  $p = 1;
			$start = ($p - 1) * $n;

			
			$order = " ORDER BY date_create DESC ";

	        $arrLog = $ims->db->load_row_arr('user_exchange_log', $where.$where_date.$order. " LIMIT ". $start.",".$n);

	 		$exchange_type = array(
				'swap_commission' => array(
					'title' => 'Đổi hoa hồng sang điểm',
					'color' => '#fff',
					'background_color' => '#fb8a04'
					),
				'buy' => array(
					'title' => 'Mua hàng',
					'color' => '#fff',
					'background_color' => '#0051ca'
					),
				'up_contributor' => array(
					'title' => 'Giới thiệu',
					'color' => '#fff',
					'background_color' => '#337ac2'
					),
				'ouser_wcoin' => array(
					'title' => 'Tích điểm',
					'color' => '#fff',
					'background_color' => '#28a745'
					),
				);
			$exchange_value_type = array(
				-1 => array(
					'title' => '-',
					'color' => '#f44336'
				),
				1 => array(
					'title' => '+',
					'color' => '#28a745'
				)
			);

			$info = array();
		    $arr = array();  
		    if (!empty($arrLog)) {
		    	$i=0;
		    	$info['user_wcoin_by_search'] = (int)$ims->db->load_item_sum_where('user_exchange_log', $where.$where_date, 'value', 'value_type');
            	$info['total_payment_by_search'] = (int)$ims->db->load_item_sum('user_exchange_log', $where.$where_date, 'total_amount');
		    	foreach ($arrLog as $key => $row) {
		    		$i++;
		    		$arr[$key]['stt'] = $start + $i;
		    		$arr[$key]['order_code'] = !empty($row['dbtable_id'])?$row['dbtable_id']:'---';
		    		$arr[$key]['total_amount'] = $row['total_amount'];
		    		$arr[$key]['exchange_type'] = isset($exchange_type[$row['exchange_type']]) ? $exchange_type[$row['exchange_type']]['title'] : '';
		    		$arr[$key]['value_type'] = $row['value_type'];
		    		$arr[$key]['value'] = $row['value'];
		    		$arr[$key]['value_change'] = $row['value']*$row['value_type'];
		    		$arr[$key]['wcoin_after'] = $row['wcoin_after'];
		    		$arr[$key]['date_create'] = $row['date_create'];
		    		$arr[$key]['note'] = $row['note'];
				}
		    }
		    
			$info['total_wcoin_buy'] = (int)$ims->db->load_item_sum('user_exchange_log', "is_show = 1 and exchange_type = 'buy' AND ".$where, 'value') * -1;
	        $info['user_wcoin'] = (float)$infoUser['wcoin'];
	        if(isset($where_not_complete)){
	            $info['total_wcoin_buy_not_complete'] = (int)$ims->db->load_item_sum('user_exchange_log', "is_show = 1 and exchange_type = 'buy' ".$where_not_complete, 'value');
	        }else{
	            $info['total_wcoin_buy_not_complete'] = 0;
	        }
			$array = array(
        		"code" => 200,
	    		"message" => $ims->lang['api']['success'],
				"total_page" => $num_items,
				'total' => $num_total,
				'numshow' => $n,
	    		'page' => $p,
	    		'info' => $info,
	    		'data' => $arr,
        	);
			$this->response(200, $array);
		}else{
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}

	
	// Thành viên gửi yêu cầu Affiliates
	function requestUserAffiliates(){
		global $ims;

		if ($ims->method == 'POST'){
			$infoUser = $this->check_token_user();
			$arr_in = array();
			$arr_in['email']   		= $ims->func->if_isset($ims->post['email'], $infoUser['email']);
			$arr_in['full_name']   	= $ims->func->if_isset($ims->post['full_name'], $infoUser['full_name']);
			$arr_in['birthday']   	= $ims->func->if_isset($ims->post['birthday'], $infoUser['birthday']);
			$arr_in['phone']   		= $ims->func->if_isset($ims->post['phone'], $infoUser['phone']);
			$arr_in['province']   	= $ims->func->if_isset($ims->post['province'], $infoUser['province']);
			$arr_in['district']   	= $ims->func->if_isset($ims->post['district'], $infoUser['district']);
			$arr_in['address']   	= $ims->func->if_isset($ims->post['address'], $infoUser['address']);
            $arr_in['bank_account_owner'] 	= $ims->func->if_isset($ims->post["bank_account_owner"], '');
            $arr_in['bank_account_number'] 	= $ims->func->if_isset($ims->post["bank_account_number"], '');
            $arr_in['bank_name'] 			= $ims->func->if_isset($ims->post["bank_name"], '');
            $arr_in['bank_branch'] 			= $ims->func->if_isset($ims->post["bank_branch"], '');
            if(isset($_FILES['affiliate_picture'])) {
	 			$_FILES['affiliate_picture'] = array_map('array_values', $_FILES['affiliate_picture']);
		        $num_files = count($_FILES['affiliate_picture']['name']);
		        $arr_tmp = array();
	            $folder_upload = "user/".$infoUser['folder_upload'].'/'.date('Y',time()).'_'.date('m',time());
		        for($i=0; $i < $num_files; $i++) {
        			$out_pic = array();
		        	$out_pic = $this->upload_image_multi($folder_upload,'affiliate_picture', $i);
				    if($out_pic['ok'] == 1){
		                $arr_tmp[] = $out_pic['url_picture'];
		            }else{
        				$this->response(400, "", 400 , $out_pic['mess']);
		            }
			    }
			    if (!empty($arr_tmp)) {
			    	$arr_in['affiliate_picture'] = implode(',',$arr_tmp);
			    }
			}
			$arr_in["is_request_affiliates"] = 1;
			$ok = $ims->db->do_update("user", $arr_in, " user_id='". $infoUser['user_id'] ."'");			
			if($ok){
				$array = array(
					"code" => 200,
					"message" => $ims->lang['api']['success']
				);
				$this->response(200, $array);
			}
		} else {
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}

    // Thành viên lấy danh sách link tiếp thị
	function getDeeplink(){
		global $ims;

		if ($ims->method == 'GET'){
            $infoUser = $this->check_token_user();

            $where = '';
			$type  = $ims->func->if_isset($ims->get['type']);
			if ($type == 'banner') {
				$time = time()+(10*60);
				$where .= ' AND (user_id ="'.$infoUser['user_id'].'" ) ';
			}
			$where .= ' is_show=1 AND user_id="'.$infoUser['user_id'].'" ';

			$numshow  = isset($ims->get['numshow']) ? $ims->get['numshow']:0;
			$p 		  = $ims->func->if_isset($ims->get['p'], 1);

			$search_date_end = $ims->func->if_isset($ims->get["search_date_end"]);		
			$search_date_begin = $ims->func->if_isset($ims->get["search_date_begin"]);
				if($search_date_begin || $search_date_end ){
				$tmp1 = @explode("/", $search_date_begin);
				$time_begin = @mktime(0, 0, 0, $tmp1[1], $tmp1[0], $tmp1[2]);
				
				$tmp2 = @explode("/", $search_date_end);
				$time_end = @mktime(23, 59, 59, $tmp2[1], $tmp2[0], $tmp2[2]);
				
				$where .= " AND (date_create BETWEEN {$time_begin} AND {$time_end} ) ";
			}


			$n = 50;
			if ($numshow>0) {
				$n = $numshow;
			}
			if ($numshow>500) {
				$n = 50;
			}

			$num_total = 0;
			$res_num = $ims->db->query("SELECT id FROM user_deeplink WHERE ".$where."  ");
				$num_total = $ims->db->num_rows($res_num);
			$n = 20;
			$num_items = ceil($num_total / $n);
			if ($p > $num_items)
			  $p = $num_items;
			if ($p < 1)
			  $p = 1;
			$start = ($p - 1) * $n;

	        $arr = $ims->db->load_item_arr('user_deeplink', $where.' ORDER BY date_create DESC LIMIT '.$start.', '.$n ,'id, item_id, link_source, num_view, date_create, short_code, type');
           
			if (!empty($arr)) {
	            foreach ($arr as $key => $value) {
	                $arr[$key]['link_source'] = $ims->conf['rooturl_web'].'redirect/product/'.$value['item_id'].'/'.$value['short_code'];

	                if ($value['type'] == 'group') {
	                	$detailDeeplink = $ims->db->load_item('product_group' , 'group_id="'.$value['item_id'].'"', 'title');
	                	$detailDeeplink = base64_encode($detailDeeplink);
	                	$detailDeeplink_source = str_replace('/', '+', $detailDeeplink);
	                	$arr[$key]['link_source'] = $ims->conf['rooturl_web'].'redirect/allproduct/'.$value['item_id'].'/'.$detailDeeplink_source.'/'.$value['short_code'];
	                }elseif($value['type'] == 'detail') {
	                	$detailDeeplink = $ims->db->load_item('product' , 'item_id="'.$value['item_id'].'"', 'title');
	                }
                    $arr[$key]['title'] = $detailDeeplink;
                    $arr[$key]['deeplink_code'] = $value['short_code'];
                    // unset($arr[$key]['item_id']);
	            }
	        }
			$array = array(
        		"code" => 200,
	    		"message" => $ims->lang['api']['success'],
				"total_page" => $num_items,
				'total' => $num_total,
				'numshow' => $n,
	    		'page' => $p,
	    		'data' => $arr,
        	);
			$this->response(200, $array);
		}else{
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}

	// Thành viên gửi deeplink
	function addDeeplink(){
		global $ims;

		$infoUser = $this->check_token_user();
		if ($ims->method == 'POST') {
            $link_source = isset($ims->post['link_source']) ? $ims->post['link_source'] : "";
            $friendy_link = ($ims->conf["rooturl"] != '/') ? str_replace($ims->conf["rooturl"], "", $link_source) : substr($link_source, 1);
            $friendy_link = str_replace('/',"",$friendy_link);
            $friendy_link = str_replace('.html',"",$friendy_link);
            $check = $ims->db->load_row('friendly_link',' friendly_link = "'.$friendy_link.'" AND lang ="'.$ims->conf["lang_cur"].'" ','module, action, dbtable_id');
            if ($check){
                if ($check['module'] == 'product' && ($check['action'] =='group' || $check['action'] =='detail')){
                	$check_exist = $ims->db->load_row('user_deeplink', 'user_id="'.$infoUser['user_id'].'" AND link_source="'.$friendy_link.'" ');
                	if (!empty($check_exist)) {
                		$array = array(
                            "code" => 400,
                            "message" => 'Link đã tồn tại'
                        );
                        $this->response(400, $array);
                	}
                    $arr_in['type'] 	   = $check['action'];
                    $arr_in['user_id'] 	   = $infoUser['user_id'];
                    $arr_in['item_id'] 	   = $check['dbtable_id'];
                    $arr_in['link_source'] = $friendy_link;
                    $arr_in['is_show'] 	   = 1;
                    $arr_in['date_create'] = time();
                    $arr_in['date_update'] = time();
                    $arr_in['short_code']  = 'c'.$ims->func->random_str(1).$ims->db->getAutoIncrement('user_deeplink').$ims->func->random_str(8);
                    $ok =  $ims->db->do_insert('user_deeplink', $arr_in);                    
                    if ($ok){
                        $array = array(
                            "code" => 200,
                            "message" => $ims->lang['api']['success']
                        );
                        $this->response(200, $array);
                    }else{
                        $array = array(
                            "code" => 400,
                            "message" => $ims->lang['api']['error']
                        );
                        $this->response(400, $array);
                    }
                }else{
                    $array = array(
                        "code" => 400,
                        "message" => $ims->lang['api']['error']
                    );
                    $this->response(400, $array);
                }
            }else{
                $array = array(
                    "code" => 400,
                    "message" => $ims->lang['api']['error_data']
                );
                $this->response(400, $array);
            }
		}elseif($ims->method == 'DELETE'){
			$id = $ims->func->if_isset($ims->get['id']);
            $check_exist = $ims->db->load_row('user_deeplink', 'user_id="'.$infoUser['user_id'].'" AND id="'.$id.'" ');
            if (!empty($check_exist)) {
			    $ok = $ims->db->query("DELETE FROM user_deeplink WHERE id='".$id."' ");
			    if ($ok) {
	            	$array = array(
	                    "code" => 200,
	                    "message" => $ims->lang['api']['success']
	                );
	                $this->response(200, $array);
			    }
            }else{
            	$array = array(
                    "code" => 400,
                    "message" => 'Link không tồn tại'
                );
                $this->response(400, $array);
            }

		} else {
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}

	// Lưu giới thiệu tiếp thị liên kết
	function addUserRecommend(){
		global $ims;

		if ($ims->method == 'POST'){
			$infoUser = $this->check_token_user();
			$short_code = $ims->func->if_isset($ims->post['deeplink_code']);
			if(empty($short_code)){
				$array = array(
                    "code" => 400,
                    "message" => 'Link không tồn tại'
                );
                $this->response(400, $array);
			}
			$check = $ims->db->load_row('user_deeplink', 'short_code = "'.$short_code.'" and is_show = 1 ', 'id, user_id, type, item_id, num_view, referred_member, short_code, link_source');
            if($check){
                $check_referred = $ims->db->load_item('user_recommend_log', 'is_show = 1 and referred_user_id = '.$infoUser['user_id'], 'id'); //Đã được người khác gthiệu
                if(!$check_referred && $check['user_id'] != $infoUser['user_id']){
                    if(!$check['referred_member']){
                        $referred_member = $infoUser['user_id'];
                    }else{
                        $referred_member = $check['referred_member'].','.$infoUser['user_id'];
                    }
                    $ims->db->do_update('user_deeplink', array('referred_member' => $referred_member),' short_code="'.$short_code.'"');
                    $check['num_view'] += 1;
                    $ok = $ims->db->do_update('user_deeplink', array('num_view' => $check['num_view']),' short_code="'.$short_code.'"');
                    // Thêm data vào bảng user_recommend_log
                    if(!empty($infoUser['phone']) && !empty($infoUser['email'])){
	                    $recommend_log = array(
	                        'type' => 'deeplink',
	                        'recommend_user_id' => $check['user_id'],
	                        'recommend_link' => $check['short_code'],
	                        'deeplink_id' => $check['id'],
	                        'referred_user_id' => $infoUser['user_id'],
	                        'referred_full_name' => $infoUser["full_name"],
	                        'referred_phone' => $infoUser["phone"],
	                        'referred_email' => $infoUser["email"],
	                        'is_show' => 1,
	                        'date_create' => time(),
	                        'date_update' => time(),
	                    );
	                    $ok = $ims->db->do_insert("user_recommend_log", $recommend_log);
	                    if($ok){
	                    	$array = array(
			                    "code" => 200,
			                    "message" => 'Thành công'
			                );
			                $this->response(200, $array);
	                    }
                    }else{
                    	$array = array(
		                    "code" => 200,
		                    "message" => 'Email hoặc phone trống',
		                );
		                $this->response(200, $array);
                    }
                }else{
                	$array = array(
	                    "code" => 200,
	                    "message" => 'Đã có người giới thiệu'
	                );
	                $this->response(200, $array);
                }
            }else{
            	$array = array(
                    "code" => 400,
                    "message" => 'Link không tồn tại'
                );
                $this->response(400, $array);
            }
		}else{
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}

    // Thành viên lấy danh sách hoa hồng
	function getCommission(){
		global $ims;

		if ($ims->method == 'GET'){
            $infoUser = $this->check_token_user();
            $ims->func->load_language('user');
            $ims->site_func->setting('product');
            $numshow = $ims->func->if_isset($ims->get['numshow'],0);
            $p = $ims->func->if_isset($ims->get['p'], 1);
			$search_date_end = $ims->func->if_isset($ims->get["search_date_end"]);		
			$search_date_begin = $ims->func->if_isset($ims->get["search_date_begin"]);
			
			$where = '';

			$list_deeplink = $ims->db->load_item_arr('user_deeplink', 'is_show=1 and lang="'.$ims->conf['lang_cur'].'" and user_id = '.$infoUser['user_id'], 'id');
			if($list_deeplink){
			    $list_tmp = array();
			    foreach ($list_deeplink as $item){
			        $list_tmp[] = $item['id'];
	            }
			    $list_deeplink = implode(',', $list_tmp);
			    $where .= ' and log.deeplink_id IN ('.$list_deeplink.')';
	            $where_deeplink_id = ' and deeplink_id IN ('.$list_deeplink.')';
	        }else{
	            $where .= ' and log.deeplink_id = -1'; // Không có dữ liệu
	            $where_deeplink_id = ' and deeplink_id = -1'; // Không có dữ liệu
	        }

			if($search_date_begin || $search_date_end ){
				$tmp1 = @explode("/", $search_date_begin);
				$time_begin = @mktime(0, 0, 0, $tmp1[1], $tmp1[0], $tmp1[2]);
				
				$tmp2 = @explode("/", $search_date_end);
				$time_end = @mktime(23, 59, 59, $tmp2[1], $tmp2[0], $tmp2[2]);
				
				$where.=" AND (log.date_create BETWEEN {$time_begin} AND {$time_end} ) ";
			}
	    
			$num_total = 0;
			$res_num = $ims->db->query("select log.id from user_deeplink_log as log where log.is_show = 1 and log.is_added = 1 ".$where);
				$num_total = $ims->db->num_rows($res_num);			
			$n = 20;
			if ($numshow>0) {
				$n = $numshow;
			}
			if ($numshow>50) {
				$n = 50;
			}
			$num_items = ceil($num_total / $n);
			if ($p > $num_items)
			  $p = $num_items;
			if ($p < 1)
			  $p = 1;
			$start = ($p - 1) * $n;

			
			$where .= " ORDER BY log.date_create DESC";	        
			if(!empty($ims->get['debug'])){
				print_r('log.*, od.o_full_name, od.order_code, od.total_order'."\n");
				print_r('user_deeplink_log as log, product_order as od'."\n");
				print_r('log.is_show = 1 and log.is_added = 1 and log.order_id = od.order_id '.$where.' LIMIT '. $start.','.$n);
				die;
			}
	        $arrLog = $ims->db->load_row_arr('user_deeplink_log as log, product_order as od', 'log.is_show = 1 and log.is_added = 1 and log.order_id = od.order_id '.$where.' LIMIT '. $start.','.$n,'log.*, od.o_full_name, od.order_code, od.total_order');

	 		$total = array();
	        $total['total_order'] = 0;
	        $total['total_commissions'] = $ims->db->load_item_sum('user_deeplink_log', 'is_added = 1 '.$where_deeplink_id, 'commission_add');

		    $arr = array();  
		    if (!empty($arrLog)){ 		    	
		    	$i = 0;
		    	foreach ($arrLog as $key => $row) {		    		
		    		$i++;
		    		$arr[$key]['stt'] = $start + $i;
		    		$full_name = $ims->db->load_item('user','is_show=1 and (user_id="'.$row['order_user'].'" or email="'.$row['o_email'].'")','full_name');		    		
		    		$arr[$key]['o_full_name'] = (!empty($full_name))?$full_name:$row['o_full_name'];
		    		$arr[$key]['total_order'] = $row['total_order'];
		    		$arr[$key]['commission_add'] = $row['commission_add'];
                	$total['total_order'] += $row['total_order'];
                	$arr[$key]['date_create'] = $row['date_create'];
					$arr[$key]['recommend_link'] = $ims->conf['rooturl'].$ims->db->load_item('user_deeplink', 'id = '.$row['deeplink_id'], 'short_code');
				}
		    }
		    $lang = array(
		    	'note_swap_1' => $ims->lang['user']['note_swap_commission'],
		    	'note_swap_2' => $ims->lang['user']['wcoin_proportion'],
		    	'note_swap_3' => $ims->lang['user']['note_swap_commission_form'],
		    );

			$info = array();
		    $info['total_commissions'] = $total['total_commissions'];
	        $info['swap_commmission'] = $ims->db->load_item_sum('user_exchange_log', 'exchange_type = "swap_commission" and user_id = '.$infoUser['user_id'], 'total_amount');
	        $info['user_commission'] = $infoUser['commission'];
			$info['wcoin2money'] = $ims->setting['product']['wcoin_to_money'];

			$array = array(
        		"code" => 200,
	    		"message" => $ims->lang['api']['success'],
				"total_page" => $num_items,
				'total' => $num_total,
				'numshow' => $n,
	    		'page' => $p,
	    		'lang' => $lang,
	    		'info' => $info,
	    		'data' => $arr,
        	);
			$this->response(200, $array);
		}else{
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}

	function swapCommission(){
		global $ims;
		if ($ims->method == 'POST'){
			$ims->site_func->setting('product');
			$ims->func->load_language('user');
			$num_commission = $ims->func->if_isset($ims->post['num_commission'],0);
	        $infoUser = $this->check_token_user();

	        if ($num_commission  < 1000) {	        	
	            $array = array(
                    "code" => 400,
                    "message" => $ims->lang['user']['min_commission'],
                );
                $this->response(400, $array);
	        }elseif($num_commission > $infoUser['commission']){	            
	            $array = array(
                    "code" => 400,
                    "message" => $ims->lang['user']['not_enough_commission'],
                );
                $this->response(400, $array);
	        }else{
	            $arr_ins['exchange_type'] 	= 'swap_commission';
	            $arr_ins['value_type'] 		= 1;
	            $arr_ins['value'] 			= $num_commission/$ims->setting['product']['money_to_wcoin'];
	            $arr_ins['total_amount'] 	= $num_commission;
	            $arr_ins['wcoin_before'] 	= $infoUser['wcoin'];
	            $arr_ins['wcoin_after'] 	= $arr_ins['wcoin_before'] + $arr_ins['value'];
	            $arr_ins['commission_before'] = $infoUser['commission'];
	            $arr_ins['commission_after'] = $infoUser['commission'] - $num_commission;
	            $arr_ins['note'] 			= $ims->lang['user']['note_swap_commission_log'];
	            $arr_ins['user_code'] 		= isset($infoUser['user_code']) ? $infoUser['user_code'] : '';
	            $arr_ins['user_id'] 		= $infoUser['user_id'];
	            $arr_ins['is_show'] 		= 1;
	            $arr_ins['date_create'] 	= time();
	            $ok = $ims->db->do_insert("user_exchange_log", $arr_ins);
	            if($ok){
	                $update_user = array(
	                    'wcoin' => $arr_ins['wcoin_after'],
	                    'wcoin_total' => $arr_ins['wcoin_before'],
	                    'commission' => $infoUser['commission'] - $num_commission,
	                );
	                $ims->db->do_update("user", $update_user, ' user_id = '.$infoUser['user_id']);
	                $array = array(
	                    "code" => 200,
	                    "message" => $ims->lang['user']['success_swap'],
	                );
	                $this->response(200, $array);
	            }	            
	        }
	    }else{
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}

	// Lịch sử đổi điểm
	function swapCommissionLog(){
		global $ims;
		if ($ims->method == 'GET'){
			$infoUser = $this->check_token_user();
			$ims->func->load_language('user');
            $ims->site_func->setting('product');

            $numshow = $ims->func->if_isset($ims->get['numshow'],0);
            $p = $ims->func->if_isset($ims->get['p'], 1);
            $search_date_end = $ims->func->if_isset($ims->get["search_date_end"]);		
			$search_date_begin = $ims->func->if_isset($ims->get["search_date_begin"]);

			$where_deeplink_id = '';
	        $list_deeplink = $ims->db->load_item_arr('user_deeplink', 'is_show=1 and lang="'.$ims->conf['lang_cur'].'" and user_id = '.$infoUser['user_id'], 'id');
	        if($list_deeplink){
	            $list_tmp = array();
	            foreach ($list_deeplink as $item){
	                $list_tmp[] = $item['id'];
	            }
	            $list_deeplink = implode(',', $list_tmp);
	            $where_deeplink_id = ' and deeplink_id IN ('.$list_deeplink.')';
	        }else{
	            $where_deeplink_id = ' and deeplink_id = -1'; // Không có dữ liệu
	        }

			$where = " is_show = 1 AND (user_code='".$infoUser['user_code']."' OR user_id='".$infoUser['user_id']."') AND exchange_type = 'swap_commission' ";

			if($search_date_begin || $search_date_end ){
				$tmp1 = @explode("/", $search_date_begin);
				$time_begin = @mktime(0, 0, 0, $tmp1[1], $tmp1[0], $tmp1[2]);
				
				$tmp2 = @explode("/", $search_date_end);
				$time_end = @mktime(23, 59, 59, $tmp2[1], $tmp2[0], $tmp2[2]);
				
				$where.=" AND (date_create BETWEEN {$time_begin} AND {$time_end} ) ";
			}
			$num_total = 0;
			$res_num = $ims->db->query("SELECT id FROM user_exchange_log WHERE ".$where."  ");
				$num_total = $ims->db->num_rows($res_num);
			$n = 20;
			if ($numshow>0) {
				$n = $numshow;
			}
			if ($numshow>50) {
				$n = 50;
			}
			$num_items = ceil($num_total / $n);
			if ($p > $num_items)
			  $p = $num_items;
			if ($p < 1)
			  $p = 1;
			$start = ($p - 1) * $n;
			$where .= " ORDER BY date_create DESC ";

			$total = array();
	        $total['total_commissions_swap'] = 0;
	        $total['total_point_receive'] = 0;
	        $total['total_commissions'] = $ims->db->load_item_sum('user_deeplink_log', 'is_added = 1 '.$where_deeplink_id, 'commission_add');

			$arr = array();  
			$arrLog = $ims->db->load_row_arr('user_exchange_log', $where. " LIMIT ". $start.",".$n);
		    if (!empty($arrLog)) {
		    	$i = 0;
		    	foreach ($arrLog as $key => $row) {
		    		$i++;
		    		$arr[$key]['stt'] = $start + $i;
		    		$arr[$key]['commission_before'] = $row['commission_before'];
		    		$arr[$key]['total_amount'] = $row['total_amount'];
		    		$arr[$key]['value'] = '+'.$row['value'];
		    		$arr[$key]['commission_after'] = $row['commission_after'];
		    		$arr[$key]['date_create'] = $row['date_create'];
	                $total['total_commissions_swap'] += $row['total_amount'];
	                $total['total_point_receive'] += $row['value'];
				}
		    }

		    $info = array();
		    $info['total_commissions'] = $total['total_commissions'];
		    $info['swap_commmission'] = $ims->db->load_item_sum('user_exchange_log', 'is_show = 1 and exchange_type = "swap_commission" and user_id = '.$infoUser['user_id'], 'total_amount');
		    $info['user_commission'] = $infoUser['commission'];

			$array = array(
        		"code" => 200,
	    		"message" => $ims->lang['api']['success'],
				"total_page" => $num_items,
				'total' => $num_total,
				'numshow' => $n,
	    		'page' => $p,
	    		'info' => $info,
	    		'data' => $arr,
        	);
			$this->response(200, $array);
		}else{
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}

	// Danh sách người giới thiệu
	function getRecommendUser(){
		global $ims;
		if ($ims->method == 'GET'){
            $infoUser = $this->check_token_user();
          	$ims->func->load_language('user');

          	$numshow = $ims->func->if_isset($ims->get['numshow'],0);
            $p = $ims->func->if_isset($ims->get['p'], 1);
            $where = 'recommend_user_id = '.$infoUser['user_id'].' ';

            $num_total = 0;
			$res_num = $ims->db->query("SELECT id FROM user_recommend_log WHERE ".$where."  ");
			$num_total = $ims->db->num_rows($res_num);
			$n = 20;
			if ($numshow>0) {
				$n = $numshow;
			}
			if ($numshow>50) {
				$n = 50;
			}
			$num_items = ceil($num_total / $n);
			if ($p > $num_items)
			  $p = $num_items;
			if ($p < 1)
			  $p = 1;
			$start = ($p - 1) * $n;
			$where .= " ORDER BY date_create DESC ";

			$arr = array();  
			$arrUser = $ims->db->load_row_arr('user_recommend_log', $where. " LIMIT ". $start.",".$n);
		    if (!empty($arrUser)) {
		    	$i = 0;
		    	$ims->dir_images  = $ims->conf['rooturl']."resources/images/";
		    	foreach ($arrUser as $key => $row) {
		    		$i++;		    		
			        $arr[$key]['user_id'] = $row['referred_user_id'];
			        $arr[$key]['full_name'] = $row['referred_full_name'];
			        $arr[$key]['email'] = $row['referred_email'];
			        $arr[$key]['phone'] = $row['referred_phone'];
			        if($row['referred_user_id'] != 0){
			            $info_user = $ims->db->load_row('user', 'is_show = 1 and user_id = '.$row['referred_user_id'], 'user_id, picture, full_name, email, phone');
			            $row['picture'] = $info_user['picture'];
			            $arr[$key]['user_id'] = $info_user['user_id'];
			        	$arr[$key]['full_name'] = $info_user['full_name'];
				        $arr[$key]['email'] = $info_user['email'];
				        $arr[$key]['phone'] = $info_user['phone'];
			        }
			        $arr[$key]['date_create'] = $row['date_create'];
					if(empty($row["picture"])){			
						$arr[$key]["picture"] = $ims->dir_images.'user.png';
					}else{
						$arr[$key]["picture"] = $ims->func->get_src_mod($row["picture"], 40, 40, 1, 0, array('fix_width'=>1));
					}
					if($row['type'] == 'deeplink'){
					    // $arr[$key]['recommend_link'] = $ims->conf['rooturl'].$row['recommend_link'];
					    $deeplink = $ims->db->load_row('user_deeplink','is_show=1 and lang="'.$ims->conf['lang_cur'].'" and short_code="'.$row['recommend_link'].'"');
					    if ($deeplink['type'] == 'group') {
		                	$detailDeeplink = $ims->db->load_item('product_group' , 'group_id="'.$deeplink['item_id'].'"', 'title');
		                	$detailDeeplink = base64_encode($detailDeeplink);
		                	$detailDeeplink_source = str_replace('/', '+', $detailDeeplink);
		                	$arr[$key]['recommend_link'] = $ims->conf['rooturl_web'].'redirect/allproduct/'.$deeplink['item_id'].'/'.$detailDeeplink_source.'/'.$row['recommend_link'];
		                }elseif($deeplink['type'] == 'detail') {
		                	$detailDeeplink = $ims->db->load_item('product' , 'item_id="'.$deeplink['item_id'].'"', 'title');
		                	$arr[$key]['recommend_link'] = $ims->conf['rooturl_web'].'redirect/product/'.$deeplink['item_id'].'/'.$row['recommend_link'];
		                }
			        }else{
			            $arr[$key]['recommend_link'] = $ims->conf['rooturl'].'?'.$row['recommend_link'];
			        }
				}
		    }


      		$array = array(
        		"code" => 200,
	    		"message" => $ims->lang['api']['success'],
				"total_page" => $num_items,
				'total' => $num_total,
				'numshow' => $n,
	    		'page' => $p,
	    		'data' => $arr,
        	);
			$this->response(200, $array);
		}else{
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}

	// Xem đơn hàng người được giới thiệu
	function getOrderRecommendUser(){
		global $ims;

		if ($ims->method == 'GET'){
			$infoUser = $this->check_token_user();
			$ims->func->load_language('user');
			$p 		      		= $ims->func->if_isset($ims->get['p'], 1);
			$numshow  	  		= $ims->func->if_isset($ims->get['numshow'], 0);
			$phone 				= $ims->func->if_isset($ims->get['phone']);
			$email 				= $ims->func->if_isset($ims->get['email']);
			$user_id 			= $ims->func->if_isset($ims->get['user_id']);
			$order_id 			= $ims->func->if_isset($ims->get['order_id'],0);

			$n = 20;
			if ($numshow>0) {
				$n = $numshow;
			}
			if ($numshow>500) {
				$n = 20;
			}
			// if ($user_id=''){
			// 	$this->response(400, "", 400, "ID người được giới thiệu không hợp lệ");
			// }			
			if ($phone != '' || $email != ''){
				$recommend_user_id = $ims->db->load_item('user_recommend_log', 'is_show = 1 and referred_phone = "'.$phone.'" or referred_email = "'.$email.'"', 'recommend_user_id');
				if(!$recommend_user_id || $recommend_user_id != $infoUser['user_id']){
					$this->response(400, "", 400, "Bạn không phải là người giới thiệu của thành viên này");	
				}
			}else{
				if (empty($phone)){
					$this->response(400, "", 400, "Số điện thoại người được giới thiệu không hợp lệ");
				}
				if (empty($email)){
					$this->response(400, "", 400, "Email người được giới thiệu không hợp lệ");
				}
			}
			
            $where = ' AND ((o_phone = "'.$phone.'" or o_email = "'.$email.'") and user_id != '.$infoUser['user_id'].') ';
            
            $num_total = 0;
			$res_num = $ims->db->query("SELECT order_id FROM product_order WHERE is_show=1 AND order_code!='' ".$where."  ");
			$num_total = $ims->db->num_rows($res_num);
			$n = 20;
			if ($numshow>0) {
				$n = $numshow;
			}
			if ($numshow>50) {
				$n = 50;
			}
			$num_items = ceil($num_total / $n);
			if ($p > $num_items)
			  $p = $num_items;
			if ($p < 1)
			  $p = 1;
			$start = ($p - 1) * $n;
			$where .= " ORDER BY date_create DESC ";

			$status_order = $ims->load_data->data_table('product_order_status','item_id','*','is_show=1 and lang="'.$ims->conf['lang_cur'].'"');
			$complete = $ims->db->load_row('product_order_status','is_show=1 and lang="'.$ims->conf['lang_cur'].'" and is_complete=1');			
			$info = array();
			$arr = array();  
			if(empty($order_id)){ //Lấy danh sách
				$arr_tmp = $ims->db->load_row_arr('product_order',"is_show=1 AND order_code!='' ".$where." LIMIT ". $start.",".$n);
				if($arr_tmp){
					$i = 0;
					foreach ($arr_tmp as $key => $value) {
						$i++;					
						$arr[$key]['stt'] = $start+$i;
						$arr[$key]['order_id'] = $value['order_id'];
						$arr[$key]['order_code'] = $value['order_code'];
						$arr[$key]['status_order'] = !empty($status_order[$value['is_status']]['title'])?$status_order[$value['is_status']]['title']:'';
						$arr[$key]['total_order'] = (int)$value['total_order'];
						$arr[$key]['total_order_after_promotion'] = $value['total_order'] - $value['promotion_price'];
						$arr[$key]['deeplink_total'] = (int)$value['deeplink_total'];
						$status = !empty($status_order[$value['is_status']]['statusclass'])?$status_order[$value['is_status']]['statusclass']:'';
						if($status == 'danger'){
		                    $arr[$key]['commission_status'] = $ims->lang['user']['not_added'];
		                }else{
		                    $arr[$key]['commission_status'] = ($value['is_status'] == $complete['item_id']) ? $ims->lang['user']['added'] : $ims->lang['user']['not_yet_added'];
		                }
						$arr[$key]['date_create'] = $value['date_create'];
						$arr[$key]['view_order'] = !empty($infoUser['show_cart_detail_other_user'])?true:false;
					}
					$info['total_commission_received'] = (int)$ims->db->load_item_sum('product_order', 'is_show = 1 and is_status = '.$complete['item_id'].$where, 'deeplink_total');
				}
			}else{ // Lấy chi tiết
				$order = $ims->db->load_row('product_order as pod, user_deeplink_log as udl', 'pod.order_id = '.$order_id.' and udl.order_id = '.$order_id, 'pod.order_id,pod.order_code, pod.total_order, pod.promotion_price, pod.deeplink_total, pod.is_use_deeplink_old, pod.is_status, udl.deeplink_detail');
				if($order){
					$is_use_deeplink_old = $order['is_use_deeplink_old'];
			        $deeplink_detail = $ims->func->unserialize($order['deeplink_detail']);
		            foreach ($deeplink_detail as $key => $value){
		                $info['max_commission_per_item'] = $amount_deeplink_default = (float)$value['max_deeplink_default_per_item'];
		                $product = $ims->db->load_row('product', 'is_show=1 and lang="'.$ims->conf['lang_cur'].'" and item_id = '.$value['item_id'], 'title, picture');
		                $arr[$key]['order_id'] = $order['order_id'];
		                $arr[$key]['order_code'] = $order['order_code'];
		                $arr[$key]['title'] = $product['title'];
		                $arr[$key]['picture'] = $ims->func->get_src_mod($value['picture']);
		                $arr[$key]['price_buy'] = (int)$value['price_buy'];
		                $arr[$key]['quantity'] = (int)$value['quantity'];
		                $arr[$key]['into_money'] = $value['into_money'] = $value['price_buy'] * $value['quantity'];
		                $arr[$key]['promotion_price_minus'] = $value['into_money'] - $value['price_use_commisson'];
		                $percent_deeplink_old = ((float)$value['percent_deeplink_group_old'] > 0) ? (float)$value['percent_deeplink_group_old'] : (float)$value['percent_deeplink_default_old'];
		                $percent_deeplink_new = ((float)$value['percent_deeplink_group_new'] > 0) ? (float)$value['percent_deeplink_group_new'] : (float)$value['percent_deeplink_default_new'];
		                $arr[$key]['percent_deeplink'] = $value['percent_deeplink'] = ($is_use_deeplink_old == 1) ? $percent_deeplink_old : $percent_deeplink_new;
		                $arr[$key]['commission'] = round((float)$value['price_use_commisson'] * $value['percent_deeplink'] / 100, 2);
		                if($arr[$key]['commission'] > $amount_deeplink_default){
		                    $arr[$key]['commission'] = $amount_deeplink_default;
		                }
		                if(!empty($value['arr_deeplink_include'])){
		                    foreach ($value['arr_deeplink_include'] as $include){
		                        $include_info = $ims->db->load_row('product','is_show=1 and item_id = '.$include['item_id'], 'title, picture');
		                        $include['title'] = $include_info['title'];
		                        $include['picture'] = $ims->func->get_src_mod($include_info['picture']);
		                        $include['quantity'] = 1;
		                        $include['price_buy'] = $include['price_buy_discounted'];
		                        $include['into_money'] = $include['price_buy_discounted'];
		                        $include['price_minus'] = 0;
		                        $include_percent_deeplink_old = ((float)$include['percent_deeplink_group_old'] > 0) ? (float)$include['percent_deeplink_group_old'] : (float)$row['percent_deeplink_default_old'];
		                        $include_percent_deeplink_new = ((float)$include['percent_deeplink_group_new'] > 0) ? (float)$include['percent_deeplink_group_new'] : (float)$row['percent_deeplink_default_new'];
		                        $include['percent_deeplink'] = ($is_use_deeplink_old == 1) ? $include_percent_deeplink_old : $include_percent_deeplink_new;
		                        $include['commission'] = round((float)$include['price_buy_discounted'] * $include['percent_deeplink'] / 100, 2);
		                        if($include['commission'] > $amount_deeplink_default){
		                            $include['commission'] = $amount_deeplink_default;
		                        }
		                        $include['include'] = $ims->lang['user']['include'];
		                    }
		                    $arr[$key]['include'] = $include;
		                }
		                $info['total_order'] = (int)$order['total_order'];
		                $info['total_order_after_promotion'] = $order['total_order'] - $order['promotion_price'];
		                $info['total_expected_commission'] = (int)$order['deeplink_total'];
		                $info['order_status'] = !empty($status_order[$order['is_status']]['title'])?$status_order[$order['is_status']]['title']:'';
		                $status = !empty($status_order[$order['is_status']]['statusclass'])?$status_order[$order['is_status']]['statusclass']:'';
						if($status == 'danger'){
		                    $info['commission_status'] = $ims->lang['user']['not_added'];
		                }else{
		                    $info['commission_status'] = ($order['is_status'] == $complete['item_id']) ? $ims->lang['user']['added'] : $ims->lang['user']['not_yet_added'];
		                }
		            }
	            }
			}
			
			$array = array(
        		"code" => 200,
	    		"message" => $ims->lang['api']['success'],
				"total_page" => $num_items,
				'total' => $num_total,
				'numshow' => $n,
	    		'page' => $p,
	    		'info' => $info,
	    		'data' => $arr,
        	);
			$this->response(200, $array);
		}else{
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}

    // Thống kê tiếp thị liên kết
	function getDeeplinkStatistics(){
		global $ims;

		if ($ims->method == 'GET'){
            $infoUser = $this->check_token_user();
          	$ims->func->load_language('user');
            $data = array();
	        $where = '';
	        $ext = '';
	        $search_date_begin = (isset($ims->input["search_date_begin"])) ? $ims->input["search_date_begin"] : "";
	        $search_date_end   = (isset($ims->input["search_date_end"])) ? $ims->input["search_date_end"] : "";
	        if($search_date_begin || $search_date_end ){
	            $tmp1 = @explode("/", $search_date_begin);
	            $time_begin = @mktime(0, 0, 0, $tmp1[1], $tmp1[0], $tmp1[2]);

	            $tmp2 = @explode("/", $search_date_end);
	            $time_end = @mktime(23, 59, 59, $tmp2[1], $tmp2[0], $tmp2[2]);

	            $where.=" AND (date_update BETWEEN {$time_begin} AND {$time_end} ) ";
	            $ext.="&date_begin=".$search_date_begin."&date_end=".$search_date_end;
	            $is_search = 1;
	        }else{
	        	$firstdate = $ims->func->time_str2int(date('01/m/Y', time()), 'd/m/Y');
				$date = strtotime(date("Y-m-t", time() ));
				$day = date("d/m/Y", $date);
				$lastdate = $ims->func->time_str2int($day, 'd/m/Y');
	            $where .= ' AND (o.date_create>'.$firstdate.' AND o.date_create<'.$lastdate.') ';
	        }


	        $numshow  = isset($ims->get['numshow']) ? $ims->get['numshow']:0;
			$p 		  = $ims->func->if_isset($ims->get['p'], 1);
			$n = 50;
			if ($numshow>0) {
				$n = $numshow;
			}
			if ($numshow>500) {
				$n = 50;
			}
			$where = ' AND o.is_show=1 AND o.deeplink_user="'.$infoUser["user_id"].'" ';

			$num_total = 0;
			$res_num = $ims->db->query("SELECT o.order_id FROM product_order o WHERE 1 ".$where."  ");
				$num_total = $ims->db->num_rows($res_num);
			$n = 20;
			$num_items = ceil($num_total / $n);
			if ($p > $num_items)
			  $p = $num_items;
			if ($p < 1)
			  $p = 1;
			$start = ($p - 1) * $n;

	        $arr_deep = $ims->db->load_item_arr('product_order o inner join product_order_detail od',' o.order_id=od.order_id AND o.is_status='.$ims->site_func->getStatusOrder(1).' '.$where.' GROUP BY o.order_id ORDER BY o.date_create DESC' . ' LIMIT '.$start.', '.$n , ' od.type_id AS product_id, o.* ');

	        $data['total_offer_by_month'] = 0;
	        $data['month_cur'] = date('m',time());

	        $arr = array();
	        if ($arr_deep) {
	            $i = 0;
	            foreach ($arr_deep as $row) {
	                $i++;

	                $row['stt'] = $i;
	                $product = $ims->db->load_row('product',' item_id ='.$row["product_id"].' AND is_show=1 AND lang="'.$ims->conf["lang_cur"].'" ', 'title, friendly_link');
	                $row['product_link'] = $ims->site_func->get_link('product',$product['friendly_link']);
	                $row['product_name'] = $product['title'];
	                $deeplink = $ims->db->load_row('user_deeplink',' id = '.$row["deeplink_id"].' ','*');
	                $row['deeplink_code'] = $deeplink['short_code'];
	                $row['deeplink'] = $ims->conf['rooturl'].$deeplink['short_code'];
	                $row['deeplink_total'] = $row['deeplink_total']*$ims->setting['product']['wcoin_to_money'];
	                if ($row['is_show'] == 1){
	                    $row['status'] = 'Đã nhận';
	                }else{
	                    $row['status'] = 'Chưa nhận';
	                }                
	                $row['link_order'] = $ims->site_func->get_link('user',$ims->setting['user']['ordering_link']).$row['order_code'].'.html';
	                $row['date_create'] = date('H:i d/m/Y ',$row['date_create']);

	                $add = array();
	                $add['order_code']     = $row['order_code'];
	                $add['customer_name']   = $row['o_full_name'];
	                $add['customer_phone']  = $row['o_phone'];
	        		$User = $ims->db->load_row('user', ' user_id="'.$row['user_id'].'" ');
	        		if (!empty($User)) {
	        			$add['customer_picture'] = $ims->func->get_src_mod($User['picture']);
	        		}
	                $add['deeplink_code']  = $row['deeplink_code'];
	                $add['deeplink_code']  = $row['deeplink_code'];
	                $add['product_name']   = $row['product_name'];
	                $add['deeplink']       = $row['deeplink'];
	                $add['status'] 		   = $row['status'];
	                $add['date_create']    = $row['date_create'];
	                $add['deeplink_total'] = $row['deeplink_total'];
	                $data['total_offer_by_month'] += $row['deeplink_total'];
	                $arr[] = $add;
	            }
	        }
	        if($search_date_begin || $search_date_end ){
	            $data['curdeeplink_statistics'] = 'Tổng hoa hồng từ <b> '.$search_date_begin.'</b> đến <b>'.$search_date_end.'</b>';
	        }else{
	            $data['curdeeplink_statistics'] = $ims->lang['user']['total_offer_by_month'];
	        }

	        $array = array(
        		"code" => 200,
	    		"message" => $ims->lang['api']['success'],
				"total_page" => $num_items,
				'total' => $num_total,
				'numshow' => $n,
	    		'page' => $p,
	    		'total_commission' => $data['total_offer_by_month'],
	    		'data' => $arr,
        	);
			$this->response(200, $array);

		} else{
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}

	// Lấy danh sách quà tặng
	function getUserGift(){
		global $ims;
		if ($ims->method == 'GET'){
			$item_id = isset($ims->get['item_id'])?$ims->get['item_id']:0;			
			$act = isset($ims->get['act'])?$ims->get['act']:0;
			$infoUser = $this->check_token_user();
			
       	 	$where = ' AND time_begin<"'.date('H:i:s').'" AND time_end>"'.date('H:i:s').'" AND date_begin<"'.time().'" AND date_end > "'.time().'"';
       	 	// and find_in_set('.$infoUser['level_id'].',apply_group)>0 
       	 	if ($item_id>0) {
       	 		$where .= ' AND item_id="'.$item_id.'" ';
       	 	}
       	 	if ($act == 'clear') {
       	 		// echo "Run Clear";die;
       	 		// $ims->db->query('DELETE FROM `user_gift_log` WHERE user_id="'.$infoUser['user_id'].'" ');
       	 	}
			$arr = $ims->db->load_item_arr('user_promotion','is_show=1 and lang="'.$ims->conf['lang_cur'].'" '.$where.' order by show_order DESC, date_update DESC','item_id,title,picture,short,value,apply_gift,date_begin,date_end');
			if (!empty($arr)) {
	            foreach ($arr as $key => $value) {
	                $arr[$key]['picture'] = $ims->func->get_src_mod($value['picture']);
	                $arr[$key]['thumbnail'] = $ims->func->get_src_mod($value['picture'],120,120);
	                $arr[$key]['short'] = $ims->func->input_editor_decode($value['short']);
	                $arr[$key]['short'] = strip_tags($arr[$key]['short']);
	                $arr[$key]['date_begin'] = date("Y/m/d H:i:s",$value['date_begin']);
	                $arr[$key]['date_end'] = date("Y/m/d H:i:s",$value['date_end']);
	            }
	        }
			$array = array(
				"code" => 200,
				"message" => $ims->lang['api']['success'],
				'data' => $arr
        	);
       	 	if ($item_id>0) {
       	 		$array['list_gift'] = array();
       	 		//list gift        
       	 		$where_gift = '';
    			$array_item = array();
       	 		$arr[0]['apply_gift'] = explode(",", $arr[0]['apply_gift']);
		        if(is_array($arr[0]['apply_gift'])){
		            foreach ($arr[0]['apply_gift'] as $key => $value) {
		                array_push($array_item, ' item_id = "'.$value.'" ');
		            }
		            if(isset($array_item) && !empty($array_item) && is_array($array_item)){
		                $array_item = implode(' or ', $array_item);
		                $where_gift .= ' and ('. $array_item. ')';
		            }
		        }
		        else{
		            $where_gift .= ' and item_id = "'.$arr[0]['apply_gift'][0].'" ';
		        }
		        // and pet_id="'.$infoUser['pet_id'].'" and level_id="'.$infoUser['level_id'].'"
    			$arr_gift = $ims->db->load_item_arr('user_gift','lang="'.$ims->conf['lang_cur'].'" and is_show=1 '.$where_gift, 'item_id, title, short, picture');
    			if (!empty($arr_gift)) {
    				foreach ($arr_gift as $k => $v) {
    					$arr_gift[$k]['picture'] = $ims->func->get_src_mod($v['picture']);
    					$arr_gift[$k]['thumbnail'] = $ims->func->get_src_mod($v['picture'],120,120);
						$arr_gift[$k]['short'] = $ims->func->input_editor_decode($v['short']);
						$arr_gift[$k]['short'] = strip_tags($arr_gift[$k]['short']);
    				}
    				$array['list_gift'] = $arr_gift;
    			}

        	}
			$this->response(200, $array);
		}else{
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}

	function get_gift_name($item_id=0,$arr_gift=array()){
		global $ims;
		$output = '';
		if(!empty($arr_gift)){
			foreach ($arr_gift as $key => $value) {
				if($item_id == $value['item_id']){
					$output = $value['title'];
				}
			}
		}
		return $output;
	}

	// Xác nhận quà đã chọn
	function confirmUserGift(){
		global $ims;
		if ($ims->method == 'POST'){
			$infoUser = $this->check_token_user();
			
			$ims->func->load_language('user');
			$content = '';

			$promotion_id = isset($ims->post['promotion_id']) ? $ims->post['promotion_id'] : '';
			$gift_id      = isset($ims->post['gift_id']) ? $ims->post['gift_id'] : '';
			$gift_id = explode(',', $gift_id);

			$promotion = $ims->db->load_row('user_promotion','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and item_id='.$promotion_id.'');
			$arr_gift_name = $ims->db->load_item_arr('user_gift','lang="'.$ims->conf['lang_cur'].'" and is_show=1','item_id,title');

			$check = $ims->db->load_item('user_gift_log','user_id='.$infoUser['user_id'].' and promotion_id='.$promotion_id.' and '.time().'<=date_end','id');
			if($check){
				$array = array(
            		'error' => array(
		        		'error_code' => 401,
		        		'error_description' => $ims->lang['user']['gift_message_false1']
					)
            	);
				$this->response(401, $array);	
			}else{
				$arr_in = array();					
				if(count($gift_id) > $promotion['value']){					
					$array = array(
	            		'error' => array(
			        		'error_code' => 400,
			        		'error_description' => $ims->lang['user']['gift_message_false2']
						)
	            	);
					$this->response(400, $array);	
				}else{
					$check_ok = 0;
					foreach ($gift_id as $k => $row) {
						if (in_array($row, explode(',', $promotion['apply_gift']))) {
							$check_ok = 1;
							$arr_in['gift_id'] = $row;				
							$arr_in['promotion_id'] = $promotion_id;
							$arr_in['cur_total_wcoin'] = $infoUser['total_wcoin'];
							$arr_in['code'] = 'G'.$ims->db->getAutoIncrement('user').$ims->func->random_str(5, 'un');
							$arr_in['is_show'] = 1;
							$arr_in['user_id'] = $infoUser['user_id'];
							$arr_in['date_end'] = $promotion['date_end'];
							$arr_in['date_create'] = time();		
							$ok = $ims->db->do_insert("user_gift_log", $arr_in);
					        if($ok){
					        	$content .= '<div class="item"><b>'.$arr_in['code'].'</b> - '.$this->get_gift_name($arr_in['gift_id'], $arr_gift_name).'</div>'."\n";
					        }
						}
					}
					if ($check_ok == 1) {
						$mail_arr_key = array(	
							'{title}',
				            '{content}',
						);
						$mail_arr_value = array(
							$ims->func->input_editor_decode($promotion['title']),
				            $ims->func->input_editor_decode($content.'</br>'.$promotion['content']),
						);	
						
						if(!empty($content)){
				        	$mess = '<div class="mess-success"><p>'.$ims->lang['user']['gift_message_success']."</p><b>".$ims->lang['user']['confirm_code']."</b>\n</br>".$content."\n".$promotion['content'].'</div>';
				        	$mess = $ims->func->input_editor_decode($mess);
				        	$this->send_mail_temp ('template-confirm-gift',$infoUser['email'],$ims->conf['email'], $mail_arr_key, $mail_arr_value);
				        	$mess = strip_tags($mess);
							$array = array(			            		
								"code" => 200,
	    						"message" => $ims->lang['api']['success'],
								'data' => array(
									'note' => strip_tags($ims->lang['user']['gift_message_success']),
									'code' => strip_tags($content),
									'content' => strip_tags($ims->func->input_editor_decode($promotion['content']))
								),
			            	);
							$this->response(200, $array);
						}else{
							$array = array(
								"code" => 400,
	    						"message" => $ims->lang['user']['gift_message_false'],
			            		"data" => ''
			            	);
							$this->response(400, $array);
						}
					}
				}			
			}
		}else{
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}

	// Kiểm tra đã chọn quà chương trình này chưa
	function checkUserGift(){
		global $ims;
		if ($ims->method == 'GET'){
			$infoUser = $this->check_token_user();

			$ims->func->load_language('user');
			$content = '';

			$promotion_id = isset($ims->get['promotion_id'])?$ims->get['promotion_id']:0;
			$arr_gift_name = $ims->db->load_item_arr('user_gift','lang="'.$ims->conf['lang_cur'].'" and is_show=1','item_id,title');
			$promotion = $ims->db->load_row('user_promotion','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and item_id='.$promotion_id.'');
			$arr = $ims->db->load_row_arr('user_gift_log','user_id='.$infoUser['user_id'].' and promotion_id='.$promotion_id.' and '.time().'<=date_end');		
			if(!empty($arr) && !empty($promotion)){
				$content = '';

				foreach ($arr as $k => $row) {
			        $content .= '<div class="item"><b>'.$row['code'].'</b> - '.$this->get_gift_name($row['gift_id'], $arr_gift_name).'</div>'."\n";
				}

				$mess = '<div class="mess-success"><p>'.$ims->lang['user']['gift_message_success']."</p><b>".$ims->lang['user']['confirm_code']."</b>\n</br>".$content."\n".$promotion['content'].'</div>';
				$mess = $ims->func->input_editor_decode($mess);
				$mess = strip_tags($mess);
				$array = array(            		
					"code" => 200,
	    			"message" => 'Bạn đã chọn quà chương trình này',
					'data' => array(
						'note' => "Thông tin quà",
						'code' => strip_tags($content),
						'content' => strip_tags($ims->func->input_editor_decode($promotion['content']))
					),
            	);
				$this->response(200, $array);	
			}else{
				$array = array(
					"code" => 400,
	    			"message" => 'Bạn chưa chọn quà chương trình này',
					'data' => ''
            	);
				$this->response(400, $array);		
			}
		}else{
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}


	function getCombo(){
		global $ims;
		if ($ims->method == 'GET'){
			// $infoUser = $this->check_token_user();			
			if(!empty($ims->get['user'])){
				$infoUser = $ims->db->load_row('user',' FIND_IN_SET("'.$ims->get['user'].'", token_login) ');
			}
			$ims->site_func->setting('product');
			$item_id  = isset($ims->get['item_id'])?$ims->get['item_id']:0;
			$type_combo = isset($ims->get['type_combo'])?$ims->get['type_combo']:'';
			$p 		        = $ims->func->if_isset($ims->get['p'], 1);
			$numshow  	    = $ims->func->if_isset($ims->get['numshow'], 0); // Số sp hiển thị
			$n = $ims->setting['product']['num_list'];
			if ($numshow>0) {
				$n = $numshow;
			}
			if ($numshow>100) {
				$n = $ims->setting['product']['num_list'];
			}
			$where = '';
	        if ($item_id>0) {
	        	$where = ' item_id="'.$item_id.'" AND ';
	        }
			$where_combo = ' apply_for=0 AND ';
			if(!empty($infoUser['level_id'])) {
				$where_combo = ' (apply_for=0 OR apply_for="'.$infoUser['level_id'].'") AND ';
			}
			if(!empty($type_combo)){
				$where_combo .= ' type='.$type_combo.' AND ';
			}
			$list_pcombo = ''; $tmp = array();					
			$combo = $ims->db->load_item_arr('combo',$where_combo.' is_show=1 and lang="'.$ims->conf['lang_cur'].'"','item_id,title,arr_product,type,value,value_type,arr_include,arr_gift');			
			if($combo){
				foreach ($combo as $key => $value) {
					$tmp[] = $value['item_id'];
				}
			}

			$list_pcombo = implode(',', $tmp);		
			if(!empty($list_pcombo)){
				$where .= ' find_in_set(combo_id,"'.$list_pcombo.'")>0 AND ';
				// quantity_combo>0 AND
			}else{
				$array = array(
		    		"code" => 200,
	    			"message" => 'Hiện không có combo nào',
		    		'data' => array(),
		    	);
		    	$this->response(200, $array);
			}
			$res_num = $ims->db->query("select item_id from product where ".$where." is_show=1 and lang='".$ims->conf['lang_cur']."'");
	        $num_total = $ims->db->num_rows($res_num);
	        $num_items = ceil($num_total / $n);
	        if ($p > $num_items)
	            $p = $num_items;
	        if ($p < 1)
	            $p = 1;
	        $start = ($p - 1) * $n;
	        if(!empty($ims->get['debug'])){
	        	echo $where .'is_show=1 AND lang="'.$ims->conf['lang_cur'].'" ORDER BY show_order DESC, date_update DESC LIMIT '.$start.', '.$n.''; die;
	        }
	        $arr = $ims->db->load_item_arr(
						'product',
						$where .'is_show=1 AND lang="'.$ims->conf['lang_cur'].'" ORDER BY show_order DESC, date_update DESC LIMIT '.$start.', '.$n.'', 
						'item_id, combo_id, group_id, picture, title, price, price_buy, percent_discount, arr_item, num_view, quantity_sold, quantity_combo'
					);	        
	        if (!empty($arr)) {
				foreach ($arr as $key => $value) {					
					$arr[$key]['picture'] 	  = $ims->func->get_src_mod($value['picture']);
					$arr[$key]['thumbnail']   = $ims->func->get_src_mod($value['picture'], 40, 40 , 1, 1);										
				}	

				if ($item_id>0) {
					// Get detail	        	;
		        	$arr[0]['rating'] = $this->getRatingByProduct('product', $arr[0]['item_id'], 'all');	        	
					$infoDetail = $ims->db->load_row('product_detail', 'product_id ="'.$arr[0]['item_id'].'"');
		        	if (!empty($infoDetail)) {
		        		// $arr[0] = array_merge($arr[0], $infoDetail);
		        		$arr_picture = !empty($arr[0]['arr_picture'])?$arr[0]['arr_picture']:'';
			            $arr[0]['short'] =  $ims->func->input_editor_decode($infoDetail['short']);
			            $arr[0]['short'] = strip_tags($arr[0]['short']);
			            $arr[0]['content'] =  $ims->func->input_editor_decode($infoDetail['content']);
			            $arr[0]['content'] = strip_tags($arr[0]['content']);
		        		if ($arr_picture!='') {
							$arr_picture = unserialize($arr_picture);
							$arr_picture[] = $arr[0]['picture'];
				            foreach ($arr_picture as $k_pic => $v_pic) {
								$arr_picture[$k_pic] = $ims->func->get_src_mod($v_pic);
				            }
							$arr[0]['arr_picture'] = $arr_picture;
						}else{
							$arr[0]['arr_picture'] = array();
							$arr[0]['arr_picture'][] = $arr[0]['picture'];
						}
						$arr_item = $arr[0]['arr_item'];
						$ims->func->load_language('global');
						$ims->func->load_language('product');
			        	$option = array();
				        if($arr_item != ''){
				            $data['arr_item'] = $ims->func->unserialize($arr_item);               
				            foreach ($data['arr_item'] as $k => $row) {
				                if($row['SelectName'] == 'Custom'){
				                    $row['title'] = $row['CustomName'];                    
				                }else{
				                    $row['title'] = isset($ims->lang['product']['option_'.strtolower($row['SelectName'])])?$ims->lang['product']['option_'.strtolower($row['SelectName'])]:'';
				                }                
				                $option[$k]['id'] = 'option'.($k+1);
				                $option[$k]['title'] = $row['title'];
				                $option[$k]['group_id'] = strtolower($row['SelectName']);
				                $option[$k]['value'] = array();
				            }
				            $order_by = ' ORDER BY date_create';				           
				            $arr_option = $ims->db->load_row_arr('product_option','lang="'.$ims->conf['lang_cur'].'" AND is_show=1 AND ProductId="'.$arr[0]['item_id'].'" '.$order_by);	
				            if($arr_option){                   
				                // print_arr($arr_option);
				                $i=0;
				                foreach (($arr_option) as $k => $v) {
				                    $i++;
				                    if(count($arr_option)>0){
				                        if($v['Option1'] != ''){  
				                            $option[0]['value'][$v['Option1']]['title'] = $v['Option1']; 
				                            $option[0]['value'][$v['Option1']]['data'][] = $v['id'];
				                        }
				                        if($v['Option2'] != ''){
				                            $option[1]['value'][$v['Option2']]['title'] = $v['Option2']; 
				                            $option[1]['value'][$v['Option2']]['data'][] = $v['id'];
				                        }
				                        if($v['Option3'] != ''){
				                            $option[2]['value'][$v['Option3']]['title'] = $v['Option3'];
				                            $option[2]['value'][$v['Option3']]['data'][] = $v['id'];
				                        }
				                    }
				                } 
				            } // End foreach
				        } // End if arr_option 

			        	$arr[0]['arr_option'] = $option;


			        	$arr_option = $ims->db->load_row_arr('product_option', ' ProductId ="'.$arr[0]['item_id'].'" AND is_show=1 ');
			        	if (!empty($arr_option)) {
			        		$arr_tmp = array();
			        		foreach ($arr_option as $k => $option) {
			        			if ($option['PricePromotion']>0) {
									$option['PriceBuy'] = $option['PricePromotion'];
								}
			        			$arr_tmp[$k]['id'] 	     = $option['id'];
			        			$arr_tmp[$k]['Option1']  = $option['Option1'];
			        			$arr_tmp[$k]['Option2']  = $option['Option2'];
			        			$arr_tmp[$k]['Option3']  = $option['Option3'];
			        			$arr_tmp[$k]['Price']    = $option['Price'];
			        			$arr_tmp[$k]['PriceBuy'] = $option['PriceBuy'];
			        			$arr_tmp[$k]['SKU'] 	 = $option['SKU'];
					            $arr_tmp[$k]['Picture'] = $option['Picture']!="" ? $ims->func->get_src_mod($option['Picture']) : "";
			        			// $arr_tmp[$k]['useWarehouse'] = (int)$option['useWarehouse'];
			        			$arr_tmp[$k]['useWarehouse'] = ($option['is_OrderOutStock']==1)?0:(int)$ims->setting['product']['use_ware_house'];
			        			$arr_tmp[$k]['Quantity'] = (int)$option['Quantity'];
			        			// $arr_tmp[$k]['is_OrderOutStock'] = (int)$option['is_OrderOutStock'];
			        			if ($arr_tmp[$k]['PriceBuy'] < $arr_tmp[$k]['Price']) {
									$arr_tmp[$k]['PercentDiscount'] = round((($arr_tmp[$k]['Price']-$arr_tmp[$k]['PriceBuy'])/$arr_tmp[$k]['Price'])*100);
								}
			        		}
			        		$arr[0]['arr_option_tmp'] = $arr_tmp;
			        	}else{
			        		$arr[0]['arr_option_tmp'] = array();
			        	} 
			        	unset($arr[0]['arr_item']);
				    	$arr[0]['arr_related'] = array();
			        	$array = array(
				    		"code" => 200,
			    			"message" => $ims->lang['api']['success'],
				    		'data' => $arr[0],
				    	);

						//list product combo
						$combo = $ims->db->load_row('combo','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and item_id="'.$arr[0]['combo_id'].'"','item_id,title,type,value,value_type,num_chose,arr_product,arr_gift,arr_include');
						if($combo){							
							switch ($combo['type']) {
								case '0':
									$type = 'Mua 1 tặng 1';
									break;
								case '1':
									$type = 'Giảm giá';
									break;
								case '2':
									$type = 'Giảm giá sản phẩm mua kèm';
									break;
								default:
									break;
							}				 	
							$arr_detail_combo = $ims->db->load_item_arr('product','is_show=1 and lang="'.$ims->conf['lang_cur'].'" and find_in_set(item_id,"'.$combo['arr_product'].'")>0 order by field(item_id,"'.$combo['arr_product'].'")','item_id,group_id,picture,title,price,price_buy,percent_discount,num_view');
							$arr_favorite = array();							
							if(!empty($infoUser['user_id'])){
								$product_favorite = $ims->db->load_row_arr('shared_favorite', ' type="product" AND user_id="'.$infoUser['user_id'].'" AND is_show=1 ');
								if (!empty($product_favorite)) {
									foreach ($product_favorite as $k => $v) {
										$arr_favorite[$v['type_id']] = $v;
									}
								}
							}
				        	if ($arr_detail_combo) {
								foreach ($arr_detail_combo as $k_nav => $v_nav) {									
									$arr_detail_combo[$k_nav]['is_favorite'] = isset($arr_favorite[$v_nav['item_id']]) ? 1 : 0;
									$arr_detail_combo[$k_nav]['picture'] = $ims->func->get_src_mod($v_nav['picture']);
									$arr_detail_combo[$k_nav]['thumbnail'] = $ims->func->get_src_mod($v_nav['picture'],40,40);
									// $arr_detail_combo[$k_nav]['price_buy'] = $v_nav['price'];
									$arr_detail_combo[$k_nav]['combo_type'] = $combo['type'];
									$arr_detail_combo[$k_nav]['combo_name'] = $type;									
								}
							}
				    		$array['data']['arr_detail_combo'] = $arr_detail_combo;
				    		$array['data']['combo'] = array(
				    			'title' => $combo['title'],
				    			'combo_id' => $combo['item_id'],
				    			'combo_type' => $combo['type'],
				    			'combo_name' => $type,
				    			'value_type' => $combo['value_type'],
				    			'value' => $combo['value'],
				    			'num_chose' => (int)$combo['num_chose'],
				    		);
				   //  		if(!empty($ims->get['debug_combo'])){
							// 	print_arr($array);
							// 	die;
							// }
							if($combo['type'] == 0){
								$arr_gift = $ims->db->load_item_arr('user_gift','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and find_in_set(item_id,"'.$combo['arr_gift'].'")>0 and quantity_combo>0', 'item_id, title, short, price, picture, quantity_combo, 1 as active');
								if($arr_gift){
									foreach ($arr_gift as $k => $v) {
										$arr_gift[$k]['picture'] = !empty($v['picture'])?$ims->func->get_src_mod($v['picture']):'';
										$arr_gift[$k]['thumbnail'] = !empty($v['picture'])?$ims->func->get_src_mod($v['picture'],40,40):'';
										$arr_gift[$k]['price_buy'] = 0;
									}
								}
								// $arr_gift_disable = $ims->db->load_item_arr('user_gift','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and find_in_set(item_id,"'.$combo['arr_gift'].'")<=0', 'item_id, title, short, price, picture, quantity_combo, 0 as active');
								// if($arr_gift_disable){
								// 	foreach ($arr_gift_disable as $k => $v) {
								// 		$arr_gift_disable[$k]['picture'] = $ims->func->get_src_mod($v['picture']);
								// 		$arr_gift_disable[$k]['thumbnail'] = $ims->func->get_src_mod($v['picture'],40,40);
								// 		$arr_gift_disable[$k]['price_buy'] = 0;
								// 	}
								// }								
								// $arr_gift = array_merge($arr_gift,$arr_gift_disable);
								$array['data']['combo']['arr_gift'] = $arr_gift;
							}				
							if($combo['type'] == 1){
								if(isset($combo['value_type']) && $combo['value_type'] == 0){
				                    $arr[0]['price_buy'] = $arr[0]['price'] - $combo['value'];
				                    if($arr[0]['price_buy'] <= 0){
				                        $arr[0]['price_buy'] = 0;
				                    }
				                }
				                elseif(isset($combo['value_type']) && $combo['value_type'] == 1){
				                    $arr[0]['price_buy'] = $arr[0]['price'] - ($arr[0]['price']*$combo['value']/100);
				                }
				                $arr[0]['percent_discount'] = 100-(round($arr[0]['price_buy']/$arr[0]['price'],2)*100);
				                $array['data'] += $arr[0];
				    //             if ($arr_detail_combo) {
								// 	foreach ($arr_detail_combo as $k_nav => $v_nav) {
								// 		if(isset($combo['value_type']) && $combo['value_type'] == 0){
						  //                   $arr_detail_combo[$k_nav]['price_buy'] = $arr_detail_combo[$k_nav]['price'] - $combo['value'];
						  //                   if($arr_detail_combo[$k_nav]['price_buy'] <= 0){
						  //                       $arr_detail_combo[$k_nav]['price_buy'] = 0;
						  //                   }
						  //               }
						  //               elseif(isset($combo['value_type']) && $combo['value_type'] == 1){
						  //                   $arr_detail_combo[$k_nav]['price_buy'] = $arr_detail_combo[$k_nav]['price'] - ($arr_detail_combo[$k_nav]['price']*$combo['value']/100);
						  //               }
						  //               $arr_detail_combo[$k_nav]['percent_discount'] = 100-(round($arr_detail_combo[$k_nav]['price_buy']/$arr_detail_combo[$k_nav]['price'],2)*100);
								// 	}
								// }
							}
							if($combo['type'] == 2){
								$arr_include = $ims->db->load_item_arr('product','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and find_in_set(item_id,"'.$combo['arr_include'].'")>0', 'item_id,group_id,picture,title,price,price_buy,percent_discount,num_view');
								if($arr_include){
									foreach ($arr_include as $k => $v) {
										$arr_include[$k]['is_favorite'] = isset($arr_favorite[$v['item_id']]) ? 1 : 0;
										$arr_include[$k]['picture'] = !empty($v['picture'])?$ims->func->get_src_mod($v['picture']):'';
										$arr_include[$k]['thumbnail'] = !empty($v['picture'])?$ims->func->get_src_mod($v['picture'],40,40):'';
										// $arr_include[$k]['price_buy'] = $v['price'];							
										// unset($arr_include[$k]["price"]);
										if(isset($combo['value_type']) && $combo['value_type'] == 0){
						                    $arr_include[$k]['price_buy'] = $v['price_buy'] - $combo['value'];
						                    if($arr_include[$k]['price_buy'] <= 0){
						                        $arr_include[$k]['price_buy'] = 0;
						                    }
						                }
						                elseif(isset($combo['value_type']) && $combo['value_type'] == 1){
						                    $arr_include[$k]['price_buy'] = $v['price_buy'] - ($v['price_buy']*$combo['value']/100);
						                }
						                $arr_include[$k]['percent_discount'] = 100-(round($arr_include[$k]['price_buy']/$arr_include[$k]['price'],2)*100);
									}
									$array['data']['combo']['array_product_bonus'] = $arr_include;
								}
							}
						}
		        	}		        	
		        	$this->response(200, $array);
				}

				$array = array(
		    		"code" => 200,
					"message" => $ims->lang['api']['success'],
		    		'total' => $num_total,
		    		'total_page' => $num_items,
		    		'numshow' => $n,
		    		'page' => $p,
		    		'data' => $arr,
		    	);
				$this->response(200, $array);
			}else{
				$array = array(
					"code" => 404,
    				"message" => "Không có sản phẩm combo",
					"data" => array()
	        	);
				$this->response(404, $array);
			}			
		}else{
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}

	//
	function eventOrderDiscount(){
		global $ims;
		if ($ims->method == 'GET'){
			$arr = array();
			$ims->site_func->setting('product');
			$code = $ims->db->load_row('promotion','is_show=2');
			if($code){
				$arr['promotion_id'] = $ims->func->input_editor_decode($code['promotion_id']);
				$arr['type_promotion'] = $code['type_promotion'];
				$arr['min_cart_item'] = !empty($ims->setting['product']['min_cart_item_discount'])?(int)$ims->setting['product']['min_cart_item_discount']:1;
			}
			$array = array(
        		"code" => 200,
	    		"message" => $ims->lang['api']['success'],
	    		"allow" => !empty($ims->setting['product']['is_order_discount'])?true:false,
	    		"data" => $arr,
        	);
			$this->response(200, $array);
		}else{
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}

	function eventOrderBundled(){
		global $ims;
		if ($ims->method == 'GET'){
			$ims->site_func->setting('product');
			$arr_product = $ims->func->unserialize($ims->setting['product']['arr_product_bundled']);
			if($arr_product){
				foreach ($arr_product as $key => $value) {
					$product = $ims->db->load_row('product','is_show=1 and lang="'.$ims->conf['lang_cur'].'" and item_id="'.$value['item_id'].'"','title,picture');					
					$arr_product[$key]['title'] = $ims->func->input_editor_decode($product['title']);
					$arr_product[$key]['picture'] = $ims->func->get_src_mod($product['picture']);
					$arr_product[$key]['thumb'] = $ims->func->get_src_mod($product['picture'],80,80);
				}
			}
			$arr = array(
				'min_cart_item' => !empty($ims->setting['product']['min_cart_item_bundled'])?(int)$ims->setting['product']['min_cart_item_bundled']:1,
				'arr_product' => $arr_product
			);
			$array = array(
        		"code" => 200,
	    		"message" => $ims->lang['api']['success'],
	    		"allow" => !empty($ims->setting['product']['is_order_bundled'])?true:false,
	    		"data" => $arr,
        	);
			$this->response(200, $array);
		}else{
			$this->response(405, "", 405, $ims->lang['api']['error_method']);
		}
	}
}
$user_api = new api();
?>