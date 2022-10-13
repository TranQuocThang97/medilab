<?php
if (!defined('IN_ims')) { die('Access denied'); }

class Data
{
	
	function __construct(){
		global $ims;

		$this->data_lang();
		$this->data_modules();
		return true;
	}
		
	//------- Load lang website ------ //
	public function data_lang (){
		global $ims;
		
		if(isset($ims->data["lang"])){
			return $ims->data["lang"];
		}
		
		$ims->data["lang"] = array();
		$result = $ims->db->query("SELECT * FROM lang WHERE is_show=1 ORDER BY show_order DESC, id ASC");
		if($num = $ims->db->num_rows($result)){
			while($row = $ims->db->fetch_row($result)){
				$ims->data["lang"][$row["name"]] = $row;
				if($row["is_default"] == 1) {
					$ims->data["lang_default"] = $row;
					$ims->data["lang_default"]["num_lang"] = $num;
				}
			}
		}
		return $ims->data["lang"]; 
	}
	

	//------- Load modules website ------ //
	public function data_modules (){
		global $ims;
		
		if(isset($ims->data["modules"])){
			return $ims->data["modules"];
		}
		$ims->data["modules"] = array();
		$result = $ims->db->query("SELECT * FROM modules WHERE is_show=1");
		if($num = $ims->db->num_rows($result)){
			while($row = $ims->db->fetch_row($result)){
				$ims->data["modules"][$row["name_action"]] = $row;
				$ims->data["modules"][$row["name_action"]]["arr_title"] = unserialize($row["arr_title"]);
				$ims->data["modules"][$row["name_action"]]["arr_friendly_link"] = unserialize($row["arr_friendly_link"]);
			}
		}
		
		return $ims->data["modules"]; 
	}

	
	//------- Load modules URL website ------ //
	public function data_modules_url (){
		global $ims;
		
		if(isset($ims->data["modules_url"])){
			return $ims->data["modules_url"];
		}else{
			$this->data_modules();
		}
		$ims->data["modules_url"] = array();
		foreach($ims->data["modules"] as $row){
			foreach($row["arr_friendly_link"] as $lang => $friendly_link){
				$ims->data["modules_url"][$friendly_link] = array(
					"name_action" => $row["name_action"],
					"lang" => $lang
				);
			}
		}
		return $ims->data["modules_url"]; 
	}

	
	//------- Load widget ------ //
	public function data_widget (){
		global $ims;
		
		if(isset($ims->data["widget"])){
			return $ims->data["widget"];
		}
		
		$ims->data["widget"] = array();
		$result = $ims->db->query("SELECT * FROM widget WHERE is_show=1");
		if($num = $ims->db->num_rows($result)){
			while($row = $ims->db->fetch_row($result)){
				$ims->data["widget"][$row["name_action"]] = $row;
				$ims->data["widget"][$row["name_action"]]["arr_title"] = unserialize($row["arr_title"]);
				
			}
		}
		return $ims->data["widget"]; 
	}

	
	//------- Load banner group ------ //
	public function data_banner_group (){
		global $ims;

		if(isset($ims->data["banner_group"])){
			return $ims->data["banner_group"];
		}
		
		$ims->data["banner_group"] = array();
		$result = $ims->db->query("SELECT group_id, group_name, title, width, height FROM banner_group WHERE is_show=1 AND lang='".$ims->conf['lang_cur']."'");
		if($num = $ims->db->num_rows($result)){
			while($row = $ims->db->fetch_row($result)){				
				$ims->data["banner_group"][$row["group_name"]] = $row;
			}
		}
		return $ims->data["banner_group"]; 
	}
	
	//------- Load banner  ------ //
	public function data_banner (){
		global $ims;
		
		if(isset($ims->data["banner"])){
			return $ims->data["banner"];
		}
		$ims->data["banner"] = array();
		$where = " AND lang='".$ims->conf['lang_cur']."'";
		
		if(isset($ims->conf['cur_mod'])) {
			$where .= " AND (FIND_IN_SET('".$ims->conf['cur_mod']."',show_mod)>0 || show_mod='')";
		}
		if(isset($ims->conf['cur_act'])) {
			$where .= " AND (FIND_IN_SET('".$ims->conf['cur_act']."',show_act)>0 || show_act='')";
		}		
		
		$query = "SELECT title, banner_id, type, link_type, link, target, icon, content, short, group_name, date_begin, date_end FROM banner WHERE is_show=1 ".$where." ORDER BY show_order DESC, date_create ASC";
		$result = $ims->db->query($query);
		if($num = $ims->db->num_rows($result)){
			while($row = $ims->db->fetch_row($result)){
				$ims->data["banner"][$row["group_name"]][$row["banner_id"]] = $row;
			}
		}
		return $ims->data["banner"]; 
	}

	
	//------- Load MENU website  ------ //
	public function data_menu (){
		global $ims;
		
		if(isset($ims->data["menu"])){
			return $ims->data["menu"];
		}
		
		$ims->data["menu"] = array();
		$ims->data["menu_action"] = array();
		
		$output = "";
		
		$where = " AND (FIND_IN_SET('".$ims->conf['cur_mod']."', show_mod)>0 || show_mod='')";
		if(isset($ims->conf['cur_act'])) {
			$where .= " AND (FIND_IN_SET('".$ims->conf['cur_act']."', show_act)>0 || show_act='')";
		}		
		$query = "SELECT * FROM menu WHERE 1 ".$ims->conf['where_lang']." ".$where." ORDER BY menu_level ASC, show_order DESC, date_create ASC";
		// echo $query;die;
		$result = $ims->db->query($query);
		if($num = $ims->db->num_rows($result)){
			while($row = $ims->db->fetch_row($result)){
				$ims->data["menu_action"][$row["group_id"]][$row["name_action"]] = $row;
				$ims->data["menu"][$row["group_id"]][$row["menu_id"]] = $row;
				
				$arr_menu_nav = explode(',',$row['menu_nav']);
				$str_code_parent = '';
				$str_code = '';
				$f = 0;
				foreach($arr_menu_nav as $tmp){
					$f++;
                    if($f < count($arr_menu_nav)) {
                        $str_code_parent .= ($f == 1) ? '['.$tmp.']' : '["arr_sub"]['.$tmp.']';
                    }					
					$str_code .= ($f == 1) ? '['.$tmp.']' : '["arr_sub"]['.$tmp.']';
				}
				//eval('$ims->data["menu_tree_'.$row['group_id'].'"]'.$str_code.' = $row;');
                eval('if(isset($ims->data["menu_tree_'.$row['group_id'].'"]'.$str_code_parent.') || '.$row['menu_level'].'==1){$ims->data["menu_tree_'.$row['group_id'].'"]'.$str_code.' = $row;}');
			}
		}
		
		return $ims->data["menu"]; 
	}
	
	//-----------------data_menu
	public function data_group ($type='product'){
		global $ims;

		if(!isset($ims->data[$type."_group_tree"])){
//		    if($type == 'product'){
//                $query = "SELECT DISTINCT pdg.* from product_group as pdg, product as pd WHERE pdg.is_show=1 AND pdg.lang='".$ims->conf["lang_cur"]."' and find_in_set(pdg.group_id, pd.group_nav) ORDER BY group_level ASC, show_order DESC, group_id ASC";
//            }else{
//                $query = "SELECT * from ".$type."_group WHERE is_show=1 AND lang='".$ims->conf["lang_cur"]."' ORDER BY group_level ASC, show_order DESC, group_id ASC";
//            }
            $query = "SELECT * from ".$type."_group WHERE is_show=1 AND lang='".$ims->conf["lang_cur"]."' ORDER BY group_level ASC, show_order DESC, group_id ASC";
            //echo $query;
			$result = $ims->db->query($query);
			$ims->data[$type."_group"] = array();
			$ims->data[$type."_group_tree"] = array();
			if($num = $ims->db->num_rows($result)){
				while($row = $ims->db->fetch_row($result)){
					if(isset($ims->data['icon']) && isset($row['icon']) && isset($ims->data['icon'][$row['icon']])){
						if(!empty($ims->data['icon'][$row['icon']]['value'])){
							$row['icon_code'] = $ims->data['icon'][$row['icon']]['value'];
						}elseif(!empty($ims->data['icon'][$row['icon']]['picture'])){
							$row['icon_pic'] = $ims->data['icon'][$row['icon']]['picture'];
						}
					}
					// $row['icon_pic'] = isset($ims->data['icon'][$row['icon']])?$ims->data['icon'][$row['icon']]['picture']:'';
					$ims->data[$type."_group"][$row["group_id"]] = $row;
					$arr_group_nav = explode(',',$row['group_nav']);
					$str_code = '';
                    $str_code_parent = '';
					$f = 0;
					foreach($arr_group_nav as $tmp){
						$f++;
                        if($f < count($arr_group_nav)) {
                            $str_code_parent .= ($f == 1) ? '['.$tmp.']' : '["arr_sub"]['.$tmp.']';
                        }		
						$str_code .= ($f == 1) ? '['.$tmp.']' : '["arr_sub"]['.$tmp.']';
					}
					eval('if(isset($ims->data["'.$type.'_group_tree"]'.$str_code_parent.') || '.$row['group_level'].'==1){$ims->data["'.$type.'_group_tree"]'.$str_code.' = $row;}');
				}
			}
		}
		
		return $ims->data[$type."_group"]; 
	}
	
	//-----------------data_table
	public function data_table ($table_name, $table_id, $sql_select='*', $sql_where='', $arr_is_array=array(), $arr_more = array()){
		global $ims;

		if(is_array($sql_where) && count($sql_where) > 0) {
			$sql_where = explode(' AND ',$sql_where);
		}
		
		if(!empty($sql_where)) {
			$sql_where = " WHERE ".$sql_where;
		}
		
		$data_name = $table_name.md5($sql_select.$sql_where);
		
		if(isset($ims->data[$data_name])){
			return $ims->data[$data_name];
		}
		
		$ims->data[$data_name] = array();
		
		$output = "";
		
		$query = "SELECT ".$sql_select." FROM ".$table_name.$sql_where;
		//echo $query;die;
		$result = $ims->db->query($query);
		if($num = $ims->db->num_rows($result)){
			while($row = $ims->db->fetch_row($result)){
				foreach($arr_is_array as $key) {
					$row[$key] = unserialize($row[$key]);
				}
				if(isset($arr_more['editor'])) {
					$arr_tmp = explode(',',$arr_more['editor']);
					foreach($arr_tmp as $key) {
						$row[$key] = $ims->func->input_editor_decode($row[$key]);
					}
				}
				$ims->data[$data_name][$row[$table_id]] = $row;
			}
		}
		
		return $ims->data[$data_name]; 
	}
	  
	public function data_icon(){
		global $ims;
		if(!isset($ims->data['icon'])){
			$ims->data['icon'] = $this->data_table ('shared_icon', 'item_id', 'item_id,title,value,picture', " is_show=1 and lang='".$ims->conf['lang_cur']."' and is_show=1 order by show_order desc, date_create desc");
		}else{
			return $ims->data['icon'];	
		}
		return $ims->data['icon'];
	}
	
	public function data_brand(){
		global $ims;
		if(!isset($ims->data['brand'])){
			$ims->data['brand'] = $this->data_table ('product_brand', 'brand_id', 'brand_id,title,picture', " is_show=1 and lang='".$ims->conf['lang_cur']."' order by show_order desc, date_create desc");
		}else{
			return $ims->data['brand'];	
		}
		return $ims->data['brand'];
	}

	public function data_rate(){
		global $ims;
		if(!isset($ims->data['rate'])){
			$ims->data['rate'] = $this->data_table ('shared_comment', 'type_id', 'FORMAT(AVG(rate),1) AS average, type_id, COUNT( item_id ) AS num', " is_show=1 and lang='".$ims->conf['lang_cur']."' and rate!=0 GROUP BY type_id");
		}else{
			return $ims->data['rate'];	
		}
		return $ims->data['rate'];
	}

	public function data_color(){		
		global $ims;
		if(!isset($ims->data['color'])){
			$ims->data['color'] = $this->data_table ('product_color', 'color_id', 'color_id,title,color', " is_show=1 and lang='".$ims->conf['lang_cur']."'");
		}else{
			return $ims->data['color'];	
		}
		return $ims->data['color'];
	}
	// End class
}
?>