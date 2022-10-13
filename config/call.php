<?php
    if (!defined('IN_ims')) { die('Access denied'); }

    class Call {

        /** load module function
            * @global type $ims
            * @param type $info
            * @param type $typeout
            * @return string
        */
        public function loadclass($mod = '') {		
            global $ims;

            $output = true;
            $class_name = $mod.'Func';
            if(!class_exists ($class_name )) {
                $path = $ims->conf["rootpath"].'modules'. DS .$mod. DS ."controllers". DS .$mod.'_func.php';
                if ($path && file_exists($path)) {
                    require_once ($path); 
                } else {
                    $output = false;
                }
            }
            return $output;
        }

        /** get module function
            * @global type $ims
            * @param type $info
            * @param type $typeout
            * @return string
        */
        public function mfunc($mod = '', $method = '', $param_arr = array()) {		
            global $ims;

            $output = false;
            $mfunc = false;
            $class_name = $mod.'Func';
            $path = $ims->conf["rootpath"].'modules'. DS .$mod. DS ."controllers". DS .$mod.'_func.php';
            if ($path && file_exists($path)) {
                require_once ($path); 
                $mfunc = new $class_name;
            }
            if(method_exists ($mfunc, $method )) {
                $output = call_user_func_array(array($mfunc, $method), $param_arr);
            }
            return $output;
        }

        /** get module function
            * @global type $ims
            * @param type $info
            * @param type $typeout
            * @return string
        */
        public function mfunc_temp(&$pthis, $arr_more = array()) {		
            global $ims;

            if(!isset($pthis->temp_func) || !$pthis->temp_func) {
                $dir_view  = $ims->func->dirModules($pthis->modules, 'views', 'path');
                $pthis->temp_func = new XTemplate($dir_view.DS."func.tpl");
                $pthis->temp_func->assign('LANG', $ims->lang);
                $pthis->temp_func->assign('DIR_IMAGE', $ims->dir_images);      
            }
            if(isset($pthis->temp_func) && $pthis->temp_func) {
                $assign = isset($arr_more['assign']) ? $arr_more['assign'] : array();      
                if(count($assign)) {
                    foreach($assign as $k => $v) {
                        $pthis->temp_func->assign($k, $v);
                    }
                }
            }
            return $pthis;
        }
    }
?>