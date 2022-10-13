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

class widget_box_product
{
	var $widget = "box_product";
	var $output = '';
	var $temp = '';
	
	/**
	* function __construct ()
	* Khoi tao 
	**/
	function __construct ()
	{
		global $ims;
		
		$ims->func->load_language_widget($this->widget);
		
		$file_tbl = $ims->path_html.'widget'.DS.$this->widget.".tpl";
		if(!file_exists($file_tbl)) {
			$file_tbl = $ims->conf['rootpath'].DS."widget".DS.$this->widget.DS.$this->widget.".tpl";
		}
		$file_css = $ims->path_css."widget".DS.$this->widget.DS.$this->widget.".css";
		if(file_exists($file_css)) {
			$ims->func->include_css ($ims->dir_css.'widget/'.$this->widget.'/'.$this->widget.".css");
		}

		$this->temp = new XTemplate($file_tbl);
		$this->temp->assign('CONF', $ims->conf);
		$this->temp->assign('LANG', $ims->lang);
		$this->temp->assign('DIR_IMAGE', $ims->dir_images);
	}
	
	//=================main===============
	function do_main ()
	{
		global $ims;
		
		if($this->output) {
			return $this->output;
		}

		$data = array();		
		$data['content'] = $this->box_main();
	
		$this->temp->reset("main");
		$this->temp->assign('data', $data);
		$this->temp->parse("main");
		$this->output = $this->temp->text("main");
		return $this->output;
	}
	
	//=================box_main===============
	function box_main () {
		global $ims;		
		
		$output = '';
      
      $pic_w = 84;
      $pic_h = 69;

      $sql = "select picture,price,price_buy,title,friendly_link 
						from product 
						where is_show=1 
						and is_focus=1 
						and lang='" . $ims->conf['lang_cur'] . "'
						order by show_order desc, date_update desc 
						limit 0," . $num_show;
      //echo $sql;
      $result = $ims->db->query($sql);
      if ($num = $ims->db->num_rows($result)) {
         $output .= '<ul class="list_none">';
         $i = 0;
         while ($row = $ims->db->fetch_row($result)) {
            $i++;
            $row['link'] = $ims->site->get_link('product', '', $row['friendly_link']);
            $row['picture'] = $ims->func->get_src_mod('product/' . $row['picture'], $pic_w, $pic_h, 1, 0, array('fix_min' => 1));
            $class = ($i == 1) ? ' class="first"' : '';
            $output .= '<li ' . $class . '>
					<div class="img">
						<div class="limit bo_css" style="width:' . $pic_w . 'px; height:' . $pic_h . 'px;"><a href="' . $row['link'] . '"><img src="' . $row['picture'] . '" alt="' . $row['title'] . '" title="' . $row['title'] . '" /></a></div>
					</div>
					<h3><a href="' . $row['link'] . '">' . $row['title'] . '</a></h3>
					<div class="price_out">';
            if ($row['price'] > $row['price_buy'] && $row['price_buy'] > 0) {
               $output .= '<div class="price">' . $ims->func->get_price_format($row['price'], '', '<u>đ</u>') . '</div>';
            }
            $output .= '<div class="price_buy">' . $ims->func->get_price_format($row['price_buy'], '', '<u>đ</u>') . '</div>
					</div>
					<div class="clear"></div>
				</li>';
         }
         $output .= '</ul>';
         //$output .= '<div class="view_more"><a href="'.$ims->site->get_link('product').'"><img src="'.$ims->dir_images.'view_more.gif" alt="Xem thêm" /></a></div>';

         $nd = array(
             'class_box' => 'box_product_focus',
             'title' => $ims->lang['global']['product_focus'],
             'content' => $output
         );

         $output = $ims->html->temp_box("box_notitle", $nd);
      }
      
      
		if(($num = count($ims->data[$ims->conf['cur_mod']."_group_tree"])) > 0){
			$data = array(
				'title' => (isset($ims->lang['widget_box_product']['menu_'.$ims->conf['cur_mod']]) ? $ims->lang['widget_box_product']['menu_'.$ims->conf['cur_mod']] : $ims->lang['widget_box_product']['widget_title']),
				'content' => ''
			);
			
			foreach($ims->data[$ims->conf['cur_mod']."_group_tree"] as $row)
			{
				$i++;
				//if($row['is_focus']==1){
            //print_arr($row);die();
            $row['link'] = $ims->site->get_link ($ims->conf['cur_mod'],$row['friendly_link']);
            $class_li = array();
            $class_li[] = 'menu_li';
            if($i == 1) {
               $class_li[] = 'first';
            }
            if($i == $num) {
               $class_li[] = 'last';
            }
            $row['class_li'] = (count($class_li) > 0) ? ' class="'.implode(' ',$class_li).'"' : '';

            $row['class'] = (in_array($row["group_id"],$arr_cur)) ? 'current' : '';
            $row['class'] = ' class="menu_link '.$row['class'].'"';
            $row['menu_sub'] = '';
            $ims->temp_box->assign('row', $row);
            $ims->temp_box->parse("box_menu.menu_sub.row");
            $menu_sub .= $ims->temp_box->text("box_menu.menu_sub.row");
            $ims->temp_box->reset("box_menu.menu_sub.row");
				//}
			}		
			
			$ims->temp_box->reset("box");
			$ims->temp_box->assign('data', $data);
			$ims->temp_box->parse("box");
			$output = $ims->temp_box->text("box");
		}
		
		return $output;
	}
	
  // end class
}
?>