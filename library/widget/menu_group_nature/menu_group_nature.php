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



class widget_menu_group_nature

{

	var $widget = "menu_group_nature";

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

		$data['content'] = $this->box_menu();

	

		$this->temp->reset("main");

		$this->temp->assign('data', $data);

		$this->temp->parse("main");

		$this->output = $this->temp->text("main");

		return $this->output;

	}

	

	//=================box_menu_sub===============

	function box_menu_sub ($array=array())

	{

		global $ims;



		$output = '';

		$arr_cur = ($ims->conf['cur_group'] > 0 && isset($ims->conf["cur_group_nav"])) ? explode(',',$ims->conf["cur_group_nav"]) : array();

		

		$menu_sub = '';

		$num = count($array);

		$i = 0;

		foreach($array as $row)

		{

			$i++;

			$row['count'] = "<span>(".$row['count'].")<span>";

			// $row['link'] = $ims->site->get_link ($ims->conf['cur_mod'],$row['friendly_link']);

			$class_li = array();

			if($i == 1) {

				$class_li[] = 'first';

			}

			if($i == $num) {

				$class_li[] = 'last';

			}

			$row['class_li'] = (count($class_li) > 0) ? ' class="'.implode(' ',$class_li).'"' : '';

			$row['class'] = (in_array($row["group_id"],$arr_cur)) ? ' class="current"' : '';

			$row['input_choose'] = '<input id="check_box_'. $row['item_id'].time() .'" type="checkbox" name="product_group[]" class="product_group_view" value = "'.$row['item_id'].'"/>';

			$row['menu_sub'] = '';

			$row['id_check'] = $row['item_id'].time();

			if(isset($row['arr_sub'])){

				$row['menu_sub'] = $this->box_menu_sub ($row['arr_sub']);

			}

			$ims->temp_box->assign('row', $row);

			$ims->temp_box->parse("box_menu.menu_sub.row");

			$menu_sub .= $ims->temp_box->text("box_menu.menu_sub.row");

			$ims->temp_box->reset("box_menu.menu_sub.row");

		}

		

		$ims->temp_box->reset("box_menu.menu_sub");

		$ims->temp_box->assign('data', array('content' => $menu_sub));

		$ims->temp_box->parse("box_menu.menu_sub");

		return $ims->temp_box->text("box_menu.menu_sub");

	}

	

	//=================box_menu===============

	function box_menu () {

		global $ims;

		$temp = 'box_menu';

		$output = '';

		$arr_cur = (isset($ims->conf['cur_group']) && $ims->conf['cur_group'] > 0 && isset($ims->conf["cur_group_nav"])) ? explode(',',$ims->conf["cur_group_nav"]) : array();

		$where = '';

		if($ims->conf['cur_mod'] == 'product' && $ims->conf['cur_group'] != ''){

			$where = 'and FIND_IN_SET('.$ims->conf['cur_group'].', group_show)';

		}

		$query = "select * from product_nature_group

							where is_show=1 

							" .$where. "

							and lang='".$ims->conf["lang_cur"]."' 

							order by show_order desc, group_id asc";

		$result = $ims->db->query($query);

		

		if($num = $ims->db->num_rows($result)){

			$data = array(

				'title' => '',

				'content' => ''

			);

			$menu_sub = '';

			$i = 0;

			while($row = $ims->db->fetch_row($result)){

			$i++;

				//if($row['is_focus']==1){

            //print_arr($row);die();

            // $row['link'] = $ims->site->get_link ($ims->conf['cur_mod'],$row['friendly_link']);

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

            $row['menu_sub'] = '';

            $row['arr_sub'] = $this->get_product_nature($row['group_id']);

            if(isset($row['arr_sub'])){

                $row['menu_sub'] = $this->box_menu_sub ($row['arr_sub']);

                $row['open_sub'] = '<i class="ficon-angle-down"></i>';

           	    $row['class'] = ' class="has-sub menu_link '.$row['class'].'"';

            }

            else{

           	   $row['class'] = ' class="menu_link '.$row['class'].'"';

            }

            $ims->temp_box->assign('row', $row);

            $ims->temp_box->parse("box_menu.menu_sub.row");

            $menu_sub .= $ims->temp_box->text("box_menu.menu_sub.row");

            $ims->temp_box->reset("box_menu.menu_sub.row");

				//}

			}		

			$ims->temp_box->reset("box_menu.menu_sub");

			$ims->temp_box->assign('data', array('content' => $menu_sub));

			$ims->temp_box->parse("box_menu.menu_sub");

			

			$ims->temp_box->reset("box_menu");

			$ims->temp_box->assign('data', $data);

			$ims->temp_box->parse("box_menu");

			$output = $ims->temp_box->text("box_menu");

		}

		return $output;

	}

	function get_product_nature($id = 0){

		global $ims;

		$arr = array();

		$sql = "SELECT * FROM product_nature WHERE is_show = 1 AND lang = '".$ims->conf['lang_cur']."' AND group_id = ".$id." ORDER BY show_order DESC, date_update DESC ";

		$query = $ims->db->query($sql);

		$i = 0;

		while ($row = $ims->db->fetch_row($query)) {

			$sql_count = "SELECT id FROM product WHERE FIND_IN_SET(".$ims->conf['cur_group'].", group_nav) AND is_show = 1 AND lang = '".$ims->conf['lang_cur']."' AND FIND_IN_SET(".$row['item_id'].", arr_nature)";

			$query_count = $ims->db->query($sql_count);

			$count = $ims->db->num_rows($query_count);

			if($count > 0){

				$row['count'] = $count;

				$arr[$i] = $row;

				$i++;

			}

		}

		return $arr;

	}

  // end class

}

?>