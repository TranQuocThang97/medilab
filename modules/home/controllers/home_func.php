<?php
if (!defined('IN_ims')) { die('Access denied'); }

class home_func {

    public $modules    = "home";
    public $parent     = null;
    public $parent_mod = "home";
    public $parent_act = "home";

    public function __construct($parent = null) {
        global $ims;

        $this->parent = $parent;
        $this->parent_mod = $this->parent_property('modules');
        $this->parent_act = $this->parent_property('action');
        return true;
    }

    public function parent_property($property) {
        global $ims;

        $output = false;
        if ($this->parent) {
            if (property_exists($this->parent, $property)) {
                $output = $this->parent->$property;
            }
        }
        return $output;
    }

    public function parent_method($method, $param_arr = array()) {
        global $ims;
        
        $output = false;
        if (method_exists($this->parent, $method)) {
            $output = call_user_func_array(array($this->parent, $method), $param_arr);
        }
        return $output;
    }
}

?>