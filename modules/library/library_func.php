<?php

/*================================================================================*\
Name code : function.php
Copyright © 2013 by Tran Thanh Hiep
@version : 1.0
@date upgrade : 03/02/2013 by Tran Thanh Hiep
\*================================================================================*/

if (! defined('IN_ims')) {
  die('Hacking attempt!');
}

//=================list_skin===============
function load_setting ()
{
	global $ims;
	$ims->setting = array();
	$sql = "SELECT userid, username FROM users WHERE status=1";
	$result = $ims->db->query($sql);
	if ($num = $ims->db->num_rows($result))
	{
		while ($row = $ims->db->fetch_row($result))
		{
			$selected = ($row["userid"] == $cur) ? " selected='selected'" : "";
			$text .= "<option value=\"".$row["userid"] ."\" ".$selected."> " . $row["username"] . " </option>";
		}
		
	}
	return $text;
}

?>