<?php
define('IN_ims', 1);
define('PATH_ROOT', dirname(__FILE__));
define('DS', DIRECTORY_SEPARATOR);

function print_arr($array){
    echo "<div style=\"background:#ffffff; color:#000000\">";
    echo "<pre>";
    print_r($array);
    echo "</pre>";
    echo "</div>";
}

class ims{}
$ims = new ims;
$ims->data = array();
require_once("../../dbcon.php");
$ims->conf = $conf;

require_once ($ims->conf['rootpath']."library/firebase/BeforeValidException.php");
require_once ($ims->conf['rootpath']."library/firebase/ExpiredException.php");
require_once ($ims->conf['rootpath']."library/firebase/SignatureInvalidException.php");
require_once ($ims->conf['rootpath']."library/firebase/JWT.php");
include_once ($ims->conf['rootpath']."config/APIxtemplate.class.php");

require_once($ims->conf['rootpath']."config/db.php");
$ims->db = new DB($conf);

require_once($ims->conf['rootpath']."config/data.php"); 
$ims->load_data = new Data;

require_once($ims->conf['rootpath']."config/func.php"); 
$ims->func = new Func;

require_once($ims->conf['rootpath']."config/sitefunc.php"); 
$ims->site_func = new siteFunc;

$ims->lang = array();

use \Firebase\JWT\JWT;

class restful_api {
    /**
        * Property: $method
        * Method được gọi, GET POST PUT hoặc DELETE
    */

    protected $key_user = "2uG2mDWTQ6pWRAsOjfyZhjU0JtwhYXQ1m";

    protected $private_key = "RnvuER4W8oDH3+3iuIYW4VQAzyqFpwuzjkDI+17t5t0tyazyZ8JXw+KgXTxldMPEL95";

    protected $data = array();

    protected $conf = '';

    protected $rooturl = '';

    protected $rootpath = '';
    
    protected $ims_DB = '';

    protected $method = '';
    /**
        * Property: $endpoint
        * Endpoint của api
    */
    protected $endpoint = '';
    /**
        * Property: $params
        * Các tham số khác sau endpoint, ví dụ /<endpoint>/<param1>/<param2>
    */
    protected $params = array();
    /**
        * Property: $file
        * Lưu trữ file của PUT request
    */
    protected $file = null;
    /**
        * Function: __construct
        * Just a constructor
    */

    public function __construct(){
        global $ims;

        $ims->data['api_list'] = $this->data_table('api_list', 'name_api', 'is_status,title,name_api', 'is_show=1');
        $result = $ims->db->query("SELECT * FROM sysoptions");
        while ($conf = $ims->db->fetch_row($result)) {
            $ims->conf[$conf['option_key']] = isset($conf['option_value']) ? $conf['option_value'] : '';
            if(isset($ims->conf['timezone']) && $ims->conf['timezone']) {
                date_default_timezone_set($ims->conf['timezone']);
            }
        }
        $ims->conf['lang_cur'] = 'vi';
        $ims->conf['where_lang'] = ' lang="'.$ims->conf['lang_cur'].'" AND is_show=1 ';
        $this->load_language('api');
        $this->_input();        
        $this->_process_api();
        // $this->_checkToken();


        // Login with facebook
        define('app_id_facebook', "536427229896019");
        define('app_secret_facebook', "46305d44833597d07dd79386fd8be2ea");
        define('redirect_uri_facebook', urlencode($this->rooturl.'thong-tin-tai-khoan/'));

        // Login with google
        define('client_id_google', "16472166892-1smimkplqgir742ngel3t22041nid0ek.apps.googleusercontent.com");
        define('client_secret_google', "Pf9tJJFeBBB_iZmHYvyBqPRu");
        define('redirect_uri_google', $this->rooturl.'doi-mat-khau/');
    }

    /**
        * Allow CORS
        * Thực hiện lấy các thông tin của request: endpoint, params và method
    */
    private function _input(){
        global $ims;

        header("Access-Control-Allow-Orgin: *");
        header("Access-Control-Allow-Methods: *");
        if (!isset($_SERVER['REQUEST_URI'])) {
            $array = array(
                'status' => 'error',
                'message' => 'Invalid Method'
            );
            $this->response(500, $array);
        }

        $ims->params   = explode('/', trim($_SERVER['REQUEST_URI'],'/'));
        $ims->endpoint = end($ims->params);
        if (strpos($ims->endpoint, '?') !== false) {
            $explode = explode('?', $ims->endpoint);
            $ims->endpoint = $explode[0];
        }

        // Lấy method của request
        $method         = $_SERVER['REQUEST_METHOD'];
        $allow_method   = array('GET', 'POST', 'PUT', 'DELETE');
        if (in_array($method, $allow_method)){
            $ims->method = $method;
        }
        // Nhân thêm dữ liệu tương ứng theo từng loại method
        switch ($ims->method) {
            case 'POST':
                $ims->post = $ims->post;
            break;
            case 'GET':
                // Không cần nhận, bởi params đã được lấy từ url
            break;
            case 'PUT':
                $ims->file = file_get_contents("php://input");
            break;
            case 'DELETE':
                // Không cần nhận, bởi params đã được lấy từ url
            break;
            default:
                $array = array(
                    'status' => 'error',
                    'message' => 'Invalid Method'
                );
                $this->response(500, $array);
            break;
        }
    }
    /**
       * Check token
    */


    /** 
        * Get header Authorization
    **/
    function getAuthorizationHeader(){
        $headers = null;

        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        }
        else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            //print_r($requestHeaders);
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }

    /**
        *get access token from header
    **/
    function getBearerToken() {
        $headers = $this->getAuthorizationHeader();

        // HEADER: Get the access token from the header
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }

    public function _getAccessToken(){  
        global $ims;

        // Xóa những token hết hạn
        $ims->db->query('DELETE FROM `api_token` WHERE date_expired<"'.time().'"');
        $bearerToken = $this->getBearerToken();
        $decoded = JWT::decode($bearerToken, $this->private_key, array('HS256'));
        if (isset($decoded->iss)) {
            $token_db = $ims->db->load_item('api_token',' account_id="'.$decoded->iss.'" AND date_expired>"'.time().'" AND token="'.$bearerToken.'" ', 'id');
            if ($token_db>0) {
                
            }else{
                $array = array(
                    "code" => 401,
                    "message" => 'Lỗi sai token hoặc token hết hạn',
                );
                $this->response(401, $array);
            }
        }else{
            $array = array(
                "code" => 401,
                "message" => $decoded['mess']
            );
            $this->response(401, $array);
        }
    }
    /**
       * Thực hiện xử lý request
    */
    private function _process_api(){   
        global $ims;

        
        if ($ims->endpoint != 'getToken' && $_SERVER['SERVER_NAME']!="localhost") {
            $this->_getAccessToken();
        }

        if (method_exists($this, $ims->endpoint)){
            // Check api này có tồn tại trong list api hay không??
            if (isset($ims->data['api_list'][$ims->endpoint])) {
                if ($ims->data['api_list'][$ims->endpoint]['is_status'] == 1) {
                    $this->{$ims->endpoint}();
                }elseif ($ims->data['api_list'][$ims->endpoint]['is_status'] == 2) {
                    $this->response(403, "", 403, $ims->lang['api']['api_lock']);
                }elseif ($ims->data['api_list'][$ims->endpoint]['is_status'] == 0) {
                    $this->response(403, "", 403, $ims->lang['api']['api_notactive']);
                }
            }else{
                $this->response(404, "", 404, $ims->lang['api']['apinotfound']);
            }
        } else {
            $this->response(404, "", 404, $ims->lang['api']['apinotfound']);
        }
    }
    /**
        * Trả dữ liệu về client
        * @param: $status_code: mã http trả về
        * @param: $data: dữ liệu trả về
    */
    protected function response($status_code, $data = NULL, $error_code =-10, $error_description= ''){
        if ($error_code>=-1) {
            if (empty($data)) {
                $data = array(
                    "code" => $error_code,
                    "message" => $error_description,
                );
            }
        }
        header($this->_build_http_header_string($status_code));
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header("Content-Type: application/json");
        echo json_encode($data);
        die();
    }
    /**
        * Tạo chuỗi http header
        * @param: $status_code: mã http
        * @return: Chuỗi http header, ví dụ: HTTP/1.1 404 Not Found
    */
    private function _build_http_header_string($status_code){
        $status = array(
            100 => 'Continue',
            101 => 'Switching Protocols',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => '(Unused)',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            422 => 'Wrong Usernam or Password',
            443 => 'Invalid item',
            444 => 'Out of stock',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported'
        );
        return "HTTP/1.1 " . $status_code . " " . $status[$status_code];
    }    

    //-----------------load_language
    function load_language($file = "") {
        global $ims;

        $ims->func->load_language($file);
    }
    
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
        
        $query = "SELECT title, banner_id, type, link_type, link, target, icon, content, short, group_name FROM banner WHERE is_show=1 ".$where." ORDER BY show_order DESC, date_create ASC";
        $result = $ims->db->query($query);
        if($num = $ims->db->num_rows($result)){
            while($row = $ims->db->fetch_row($result)){
                $ims->data["banner"][$row["group_name"]][$row["banner_id"]] = $row;
            }
        }
        return $ims->data["banner"]; 
    }

    public function get_link_lang($lang, $modules, $action = "", $item = "", $arr_ext = array()) {
        global $ims;

        $link_out = $ims->conf['rooturl'];
        $arr_full_link = array();
        $this->setting($modules);
        if (in_array($modules, $arr_full_link)) {
            $link_out .= (!empty($modules)) ? $ims->setting[$modules.'_'.$lang][$modules.'_link'] . '/' : '';
            if (!empty($action)) {
                $link_out .= (!empty($action)) ? $action . '/' : '';
            }
            if (!empty($item)) {
                $link_out .= (!empty($item)) ? $item . '.html' : '';
            }
        } else {
            if (!empty($action)) {
                $link_out .= (!empty($action)) ? $action . '/' : '';
                if (!empty($item)) {
                    $link_out .= (!empty($item)) ? $item . '.html' : '';
                }
            } elseif (!empty($item)) {
                $link_out .= (!empty($item)) ? $item . '.html' : '';
            } else {
                $link_out .= (!empty($modules) && isset($ims->setting[$modules.'_'.$lang][$modules.'_link'])) ? $ims->setting[$modules.'_'.$lang][$modules.'_link'] . '/' : '';
            }
        }
        $i = 0;
        foreach ($arr_ext as $k => $v) {
            $i++;
            $link_out .= ($i == 1) ? '/?' : '&';
            $link_out .= $k . "=" . $v;
        }
        return $link_out;
    }

    public function get_link_menu($link, $link_type = 'site') {
        global $ims;

        $arr_data = array(
            'site' => 'Nội bộ trang',
            'web' => 'Liên kết web khác',
            'mail' => 'Thư điện tử',
            'neo' => 'Neo trong trang',
        );

        switch ($link_type) {
            case "app":
                $link = $link;
                break;
            case "site":
                $link = $this->rooturl . $link;
                break;
            case "web":
                $link = $link;
                break;
            case "mail":
                $link = 'mailto:' . $link;
                break;
            case "neo":
                $link = '#' . $link;
                break;
        }

        return $link;
    }
  
    function get_input_pic($url, $mod = '') {
        $output = '';
        $link = $this->rooturl . 'uploads/';
        if ($mod != '') {
            $link .= $mod . '/';
        }
        $output = str_replace($link, '', $url);
        return $output;
    }
    function get_src_mod($picture, $w = "", $h = "", $thumb = 1, $crop = 0, $arr_more = array()) {
        global $ims;

        $arr_duoi = array('gif', 'png', 'jpg', 'jpeg', 'pjpeg');

        $duoi = strtolower(substr($picture, strrpos($picture, ".") + 1));
        if (!in_array($duoi, $arr_duoi)) {
            $picture = 'nophoto/nophoto.jpg';
        }

        $out = "";
        $pre = $w;
        if ($h) {
            $pre = $w . "x" . $h;
        } else {
            $h = $w;
        }
        
        if ($crop != 0) {
            $pre .= "-cr";
        } elseif (isset($arr_more['fix_min'])) {
            $pre .= "-fmi";
        } elseif (isset($arr_more['fix_max'])) {
            $pre .= "-fma";
        } elseif (isset($arr_more['fix_width'])) {
            $pre .= "-fw";
        } elseif (isset($arr_more['fix_height'])) {
            $pre .= "-fh";
        } elseif (isset($arr_more['zoom_max'])) {
            $pre .= "-zma";
        }
        $pre = "[".$pre."]";

        $linkhinh = $picture;
        $linkhinh = str_replace("//", "/", $linkhinh);
        if (!file_exists($this->rooturl . "uploads/" . $linkhinh)) {
            $linkhinh = 'nophoto/nophoto.jpg';
        }
        $dir = substr($linkhinh, 0, strrpos($linkhinh, "/"));
        $pic_name = substr($linkhinh, strrpos($linkhinh, "/") + 1);
        $linkhinh = "uploads/" . $linkhinh;

        if ($duoi == 'gif') {
            $w = '';
        }
        if ($w) {
            if ($thumb) {
                $folder_thumbs = $dir;
                $file_thumbs = $folder_thumbs . "/{$pre}" . substr($linkhinh, strrpos($linkhinh, "/") + 1);
                $linkhinhthumbs = $this->rootpath . "thumbs_size/" . $file_thumbs;
                $src = $this->rooturl . 'thumbs_size/' . $file_thumbs;
            } else {
                $src = $this->rooturl . $folder_thumbs . "/" . $pic_name;
            }
        } else {
            $src = $this->rooturl . 'uploads/' . $picture;
        }

        return $src;
    }

    function getBanner($group_id = ''){
        global $ims;

        $output = array();
        $i =0;

        if (isset($ims->data["banner"][$group_id]) && $group_id != '') {
            foreach ($ims->data["banner"][$group_id] as $banner) {
                $w = $ims->data["banner_group"][$group_id]['width'];
                $h = $ims->data["banner_group"][$group_id]['height'];
                $style_pic = '';
                if ($ims->data["banner_group"][$group_id]['height'] == 'fixed') {
                } elseif ($ims->data["banner_group"][$group_id]['height'] == 'full') {
                    $style_pic = "width:100%;";
                }
                $banner['link'] = $this->get_link_menu($banner['link'], $banner['link_type']);
                if ($banner['type'] == 'image') {
                    $banner['thumbnail'] = $ims->func->get_src_mod($banner['content'], 40, 40 , 1, 1);
                    $banner['content'] = $ims->func->get_src_mod($banner['content'], $w, $h, " alt=\"" . $banner['title'] . "\" style=\"" . $style_pic . "\"", 1, 0, array('fix_width' => 1));

                } elseif ($banner['type'] == 'flash' && $banner['content']) {
                    $w = ($w) ? $w : '100%';
                    $h = ($h) ? $h : $w;
                    $tl = ((int) $h / (int) $w) * 100;
                    $tmp = 'flash_file_' . $ttH->func->random_str('6');
                    $banner['content'] = '<div style="position:relative; padding-bottom:' . $tl . '%;"><div id="' . $tmp . '"></div></div>
                    <script type="text/javascript">
                        swfobject.embedSWF("' . $ims->conf['rooturl_web'] . 'uploads/' . $banner['content'] . '", "' . $tmp . '", "' . $w . '", "' . $h . '", "9.0.0", "' . $ttH->dir_js . 'swfobject/expressInstall.swf");
                    </script>';
                }
                if ($group_id == 'banner-main') {
                    $output[$i]['thumbnail'] = isset($banner['thumbnail'])?$banner['thumbnail']:'';
                    $output[$i]['img_link'] = $banner['content'];
                    $output[$i]['web_link'] = $banner['link'];
                }elseif($group_id == 'brand' || $group_id == 'outstanding-feature'){
                    $output[$i]['thumbnail'] = isset($banner['thumbnail'])?$banner['thumbnail']:'';
                    $output[$i]['img_link'] = $banner['content'];
                    $output[$i]['title'] = $banner['title'];
                    $output[$i]['web_link'] = $banner['link'];
                }elseif($group_id == 'footer'){
                    $output = $banner['content'];
                } else{
                    $output[$i]['thumbnail'] = isset($banner['thumbnail'])?$banner['thumbnail']:'';
                    $output[$i]['img_link'] = $banner['content'];
                    $output[$i]['web_link'] = $banner['link'];
                }
                $output[$i]['type_link'] = $banner['link_type'];
                $i++;
            }
        }
        return $output;
    }

    function getGroupProduct($type = ''){
        $output = array();
        $output['id_list_san_pham'] = $type;
        $output['title'] = '';
        $output['sub_title'] = '';
        $where = '  ';
        if ($type == 'group_product_1') {
            $output['title'] = $ims->lang['home']['product_focus'];
            $output['sub_title'] = $ims->lang['home']['product_focus_sub'];
            $where = ' is_show=1 and is_focus=1 and lang="'.$this->conf['lang_cur'].'" order by show_order DESC, date_update DESC LIMIT 0,12 ';
        }
        if ($type == 'group_product_2') {
            $output['title'] = $ims->lang['home']['best_sellers'];
            $output['sub_title'] = $ims->lang['home']['best_sellers_sub'];
            $where = ' is_show=1 and is_hot=1 and lang="'.$this->conf['lang_cur'].'" order by show_order DESC, date_update DESC LIMIT 0,12 ';
        }
        if ($type == 'group_product_3') {
            $output['title'] = $ims->lang['home']['product_sale'];
            $output['sub_title'] = $ims->lang['home']['product_sale_sub'];
            $where = ' is_show=1 and is_sale=1 and lang="'.$this->conf['lang_cur'].'" order by show_order DESC, date_update DESC LIMIT 0,18 ';
        }
        $output['sub_list_san_pham'] = array();
        $arr = $ims->db->load_item_arr('product', $where, 'item_id,picture,title,price,price_buy,percent_discount');
        if (!empty($arr)) {
            foreach ($arr as $key => $value) {
                $arr[$key]['picture'] = $this->get_src_mod($value['picture']);
            }
        }
        $output['sub_list_san_pham'] = $arr;
        return $output;
    }

    // setting
    function setting($module, $arr_more = array()) {
        global $ims;
        
        $ims->setting = (isset($ims->setting)) ? $ims->setting : array();
        if (!isset($ims->setting[$module])) {
            $all = $ims->db->load_row_arr($module . "_setting" ,"is_show=1");
            if (!empty($all)) {
                foreach ($all as $k => $row) {
                    if(isset($arr_more['editor'])) {
                        $arr_tmp = explode(',',$arr_more['editor']);
                        foreach($arr_tmp as $key) {
                            $row[$key] = isset($row[$key]) ? $ims->func->input_editor_decode($row[$key]) : '';
                        }
                    }
                    $ims->setting[$module . '_'. $row['lang']][$row['setting_key']] = $row['setting_value'];
                    if ($ims->conf['lang_cur'] == $row['lang']) {
                        $ims->setting[$module][$row['setting_key']] = $row['setting_value'];
                    }
                }
            }
        }
    }

    //----------------- data_menu
    function data_group ($type='product', $group_parent = 0 ,$where = ''){
        global $ims;

        $check = 0;
        $check_parent = 0;
        if($group_parent != 0){
            $ims->data[$type."_group_tree"] = array();
            $where .= " AND parent_id='".$group_parent."' ";
            $check_parent = 1;
        }
        $query = "SELECT friendly_link, group_id, group_nav, group_level, parent_id, title, picture_app, arr_search_price
                FROM ".$type."_group 
                WHERE is_show=1 ".$where."
                AND lang='vi' ORDER BY group_level ASC, show_order DESC, group_id ASC";
        $result = $ims->db->query($query);
        $ims->data[$type."_group"] = array();
        $ims->data[$type."_group_tree"] = array();
        if($num = $ims->db->num_rows($result)){
            while($row = $ims->db->fetch_row($result)){                
                $row['title'] = $ims->func->input_editor_decode($row['title']);
                $row['picture'] = !empty($row['picture_app'])?$ims->func->get_src_mod($row['picture_app']):'';
                $row['arr_search_price'] = $ims->func->unserialize($row['arr_search_price']);
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
                eval('if(isset($ims->data["'.$type.'_group_tree"]'.$str_code_parent.') || '.$row['group_level'].'==1 || '.$row['group_level'].'==2){ $ims->data["'.$type.'_group_tree"]'.$str_code.' = $row;}');
            }
        }
        return $ims->data[$type."_group"]; 
    }

    function input_editor_decode($str) {
        global $ims;
        $str = htmlspecialchars_decode($str, ENT_QUOTES);
        preg_match_all('/\[widget_(.*?)\]/', $str, $matches);
        $arr_widget_call = array();
        foreach ($matches[1] as $k => $v) {
            $v = trim($v);
            $v = str_replace('&nbsp;', ' ', $v);
            while (strlen(strstr($v, "  ")) > 0) {
                $v = str_replace('  ', ' ', $v);
            }

            $tmp = explode(' ', $v);
            $arr_widget_call[$k] = array();
            $arr_widget_call[$k]['text_replace'] = $matches[0][$k];
            foreach ($tmp as $k1 => $v1) {
                if ($k1 == 0) {
                    $arr_widget_call[$k]['name_action'] = $v1;
                } else {
                    $tmp1 = explode('=', $v1);
                    $arr_widget_call[$k][$tmp1[0]] = $tmp1[1];
                    $arr_widget_call[$k][$tmp1[0]] = str_replace('"', '', $arr_widget_call[$k][$tmp1[0]]);
                    $arr_widget_call[$k][$tmp1[0]] = str_replace("'", '', $arr_widget_call[$k][$tmp1[0]]);
                }
            }
        }
        return $str;
    }

    // send_mail_temp
    function send_mail_temp($template, $mailto, $mailfrom, $arr_key = array(), $arr_value = array(), $file_attach = "") {
        global $ims;

        $sent = 0;

        $sql = "select * from template_email where template_id='" . $template . "' and lang='" . $ims->conf['lang_cur'] . "' limit 0,1";
        $result = $ims->db->query($sql);
        if ($row = $ims->db->fetch_row($result)) {
            $row['subject'] = str_replace($arr_key, $arr_value, $row['subject']);
            $row['content'] = $this->input_editor_decode($row['content']);
            $row['content'] = str_replace($arr_key, $arr_value, $row['content']);
            $sent = $this->send_mail($mailto, $row['subject'], $row['content'], $mailfrom, $file_attach);
        }
        return $sent;
    }

    /* --------------- send_mail`  ----------- */

    function send_mail($mailto, $subject, $message, $mailfrom, $file_attach = "") {
        global $ims;
        require_once ($ims->conf['rootpath']."library/phpmailer/class.phpmailer.php");

        $message = stripcslashes($message);
        $from_name = $_SERVER['HTTP_HOST'];
        $mailer = new PHPMailer();
        $mailer->IsSMTP(); 
        //$mailer->SMTPDebug  = 2;                    
        // 1 = errors and messages
        // 2 = messages only
        $mailer->SMTPAuth = true;                 
        $mailer->CharSet = "utf-8";
        switch ($ims->conf['method_email']) {
            case "gmail":
                $mailer->SMTPSecure = "tls";                 // sets the prefix to the servier
                $mailer->Host = $ims->conf['smtp_host'];      // sets GMAIL as the SMTP server
                $mailer->Port = $ims->conf['smtp_port'];                   // set the SMTP port for the GMAIL server
                $mailer->Username = $ims->conf['smtp_username'];  // GMAIL username
                $mailer->Password = $ims->conf['smtp_password'];            // GMAIL password
                $mailer->SetFrom($mailfrom, $from_name);
                break;
            case "smtp":
                $mailer->Host = $ims->conf['smtp_host'];
                $mailer->Port = $ims->conf['smtp_port'];
                $mailer->Mailer = "smtp";
                $mailer->Username = $ims->conf['smtp_username'];
                $mailer->Password = $ims->conf['smtp_password']; // Password E-mail
                $mailer->SetFrom($ims->conf['smtp_username'], $from_name);
                break;
            default:
                $mailer->Mailer = "mail";
                break;
        }
        $mailer->AddReplyTo($mailto, $mailto);
        $mailer->Subject = $subject;
        $mailer->AltBody = $message; 
        $mailer->MsgHTML($message);
        $arrTo = explode(",", $mailto);
        for ($i = 0; $i < count($arrTo); $i ++) {
            if ($i == 0)
                $mailer->AddAddress($arrTo[$i], $_SERVER['HTTP_HOST']);
            else
                $mailer->AddCC($arrTo[$i], $_SERVER['HTTP_HOST']);
        }

        if (!empty($file_attach)) {
            if (is_array($file_attach)) {
                foreach ($file_attach as $file_a) {
                    $mailer->AddAttachment($file_a);
                }
            } else {
                $mailer->AddAttachment($file_attach);
            }
        }
        //  if(!$mailer->Send()) {
        //      echo "Mailer Error: " . $mailer->ErrorInfo;
        //  } else {
        //      echo "Message sent!";
        //  } 
        $sent = $mailer->Send();
        return $sent;
    }
    function full_address($info = array(), $pre = '') {
        $arr_tmp = array();
        if (isset($info[$pre . 'address'])) {
            $arr_tmp[] = $info[$pre . 'address'];
        }
        $arr_k = array('ward', 'district', 'province', 'country', 'area');
        foreach ($arr_k as $k) {
            if (isset($info[$pre . $k]) && !empty($info[$pre . $k])) {
                $arr_tmp[] = $this->location_name($k, $info[$pre . $k]);
            }
        }
        return (count($arr_tmp) > 0) ? implode(', ', $arr_tmp) : '';
    }
    function location_name($type = 'area', $code = '') {
        global $ims;
        
        $data = $ims->db->load_item('location_' . $type,"code='".$code."' and is_show=1 and lang='".$ims->conf['lang_cur']."'",'title');
        return $data;
    }
    // get_date_format
    function get_date_format($date, $type = 1) {
        global $ims;
        $out = "";
        switch ($type) {
            case 2:
                $out = @date("d/m/Y, H:i", $date);
                break;
            case 1:
                $out = @date("d/m/Y, H:i", $date);
                break;
            default:
                $out = @date("d/m/Y", $date);
                break;
        }
        return $out;
    }
  
    function get_friendly_link($str) {
        $lang_allow = array('cn', 'ko');
        $lang_cur = (isset($this->conf['lang_cur'])) ? $this->conf['lang_cur'] : 'vi';
        $str = $this->vn_str_filter($str);
        if (!in_array($lang_cur, $lang_allow)) {
            $str = preg_replace('/[^a-zA-Z0-9\-_ ]/', '', $str);
        }
        $str = preg_replace('/[_ ]/', '-', $str);
        while (strlen(strstr($str, "--")) > 0) {
            $str = str_replace('--', '-', $str);
        }
        $str = str_replace(array('(-)', '()', '(-', '-)', '(', ')'), '', '(' . $str . ')');
        $str = strtolower($str);
        $str = ($str == "") ? time() : $str;
        return $str;
    }
    function get_friendly_link_db($str, $table, $id_key = '', $id_value = 0, $lang = 'vi', $arr_more = array(), $arr_check = array('call' => 0)) {
        global $ims;

        $call_max = 10;
        $arr_check['call'] = (isset($arr_check['call'])) ? $arr_check['call'] : 0;
        $arr_check['call'] ++;
        if ($arr_check['call'] >= $call_max) {
            return time();
        }

        $str = $this->get_friendly_link($str);
        $num_str_count = substr_count($str, '-');
        $sql_num_str_count = "(LENGTH(friendly_link) - LENGTH(REPLACE(friendly_link, '-', '')))";

        $sql = "select friendly_link, " . $sql_num_str_count . " as num_str_count from friendly_link 
                        where !(
                            dbtable='" . $table . "' 
                            and dbtable_id='" . $id_value . "' 
                            and lang='" . $lang . "'
                            ) 
                        and friendly_link like '" . $str . "%' 
                        and " . $sql_num_str_count . ">=" . $num_str_count . " 
                        and " . $sql_num_str_count . "<=" . ($num_str_count + 1) . " 
                        order by friendly_link desc";
        $result = $ims->db->query($sql);
        if ($num = $ims->db->num_rows($result)) {
            $arr_row = $ims->db->get_array($result);
            foreach ($arr_row as $k => $v) {
                $tmp = explode('-', $arr_row[$k]['friendly_link']);
                if (substr_count($arr_row[$k]['friendly_link'], '-') > $num_str_count && !is_numeric($tmp[count($tmp) - 1])) {
                    unset($arr_row[$k]);
                }
            }
            $arr_row = array_values($arr_row);
            $num = count($arr_row);
            if (isset($arr_row[$num - 1]['friendly_link']) && $str == $arr_row[$num - 1]['friendly_link']) {
                $tmp = explode('-', $arr_row[0]['friendly_link']);
                if (is_numeric($tmp[count($tmp) - 1]) && substr_count($arr_row[0]['friendly_link'], '-') > substr_count($str, '-')) {
                    $str = $arr_row[0]['friendly_link'];
                    $tmp = explode('-', $str);
                    $str .= '[]';
                    $tmp = $tmp[count($tmp) - 1] . '[]';
                    $str = str_replace($tmp, '', $str);
                    $tmp = str_replace('[]', '', $tmp);
                    $tmp++;
                    $str .= $tmp;
                } else {
                    $str = $arr_row[0]['friendly_link'] . '-1';
                }
                $str = $this->get_friendly_link_db($str, $table, $id_key, $id_value, $lang, $arr_more, $arr_check);
                return $str;
            }
        }

        $col = array();
        $col['friendly_link'] = $str;
        $col['date_update'] = time();
        $ok = $ims->db->do_update("friendly_link", $col, "dbtable='" . $table . "' and dbtable_id='" . $id_value . "' and lang='" . $lang . "'");
        if (!$ims->db->affected()) {
            $table_tmp = str_replace('_lang', '', $table);
            $tmp = explode('_', $table_tmp);
            $module = (isset($arr_more['module']) && $arr_more['module']) ? $arr_more['module'] : $tmp[0];
            $action = (isset($arr_more['action']) && $arr_more['action']) ? $arr_more['action'] : str_replace($module . '_', '', $table_tmp);
            if ($table == 'modules') {
                $module = $id_value;
                $action = $id_value;
            }
            $col['module'] = $module;
            $col['action'] = (!empty($action)) ? $action : $table;
            $col['action'] = ($col['module'] == $col['action'] && $table != 'modules') ? 'detail' : $col['action'];
            $col['dbtable'] = $table;
            $col['dbtable_id'] = $id_value;
            $col['lang'] = $lang;
            $col['date_create'] = time();
            $ims->db->do_insert("friendly_link", $col);
        }
        return $str;
    }
    function get_price_format_email($price, $default = "", $unit = "đ", $rate = 0) {
        global $ims;

        if (strlen($default) == 0) {
            $default = 'Rỗng';
        } elseif ($default == 0) {
            $default = "<span class=\"price_format\"><span class=\"number\">" . $default . "</span> <span class=\"unit\">" . $unit . "</span></span>";
        }
        if ($price) {

            if ($rate) {
                $price = $price / $rate;
            }

            $nguyen = (int) $price;
            $dot = strpos($price, ".");
            if ($dot) {
                $du = substr($price, strpos($price, "."), 3);
            } else {
                $du = "";
            }
            $price = "<span class=\"price_format\"><span class=\"number autoUpdate\">" . $this->format_number($nguyen) . $du . "</span> <span class=\"unit\">" . $unit . "</span></span>";
        } else {
            $price = $default;
        }
        return $price;
    }
    function format_number($num, $seperator = ",") {
        $string = strrev(substr(chunk_split(strrev($num), 3, $seperator), 0, - 1));
        return $string;
    }
    //-----------------data_menu
    public function data_menu (){
        global $ims;
        
        if(isset($ims->data["menu"])){
            return $ims->data["menu"];
        }
        $ims->data["menu"] = array();
        $ims->data["menu_action"] = array();
        $output = "";
        $where = " and (find_in_set('".$this->conf['cur_mod']."',show_mod)>0 || show_mod='')";
        if(isset($this->conf['cur_act'])) {
            $where .= " and (find_in_set('".$this->conf['cur_act']."',show_act)>0 || show_act='')";
        }       
        $query = "select *  from menu where is_show=1  and lang='".$this->conf["lang_cur"]."' ".$where." order by menu_level asc, show_order desc, date_create asc";
        //echo $query;
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
                eval('if(isset($ims->data["menu_tree_'.$row['group_id'].'"]'.$str_code_parent.') || '.$row['menu_level'].'==1){$ims->data["menu_tree_'.$row['group_id'].'"]'.$str_code.' = $row;}');
            }
        }
        
        return $ims->data["menu"]; 
    }
    public function input_editor($str) {
        $str = htmlspecialchars($str, ENT_QUOTES);
        return $str;
    }
    public function item_code($item_code_key = '', $item_code = '', $item_id = 0, $dbtable = 'product', $dbtable_id ='item_id') {
        
        $output = $this->random_str(6, 'u');
        if ($item_code && $item_id > 0) {
            $sql_check = "select ".$dbtable_id.", ".$item_code_key." from ".$dbtable." where ".$item_code_key."='".$item_code."' and ".$dbtable_id."!='".$item_id."'";
            $result_check = $ims->db->query($sql_check);
            if ($ims->db->num_rows($result_check)) {
                $item_code = $item_id . $this->random_str(5, 'u');
                return $this->item_code($item_code_key, $item_code, $item_id);
            } else {
                return $item_code;
            }
        }
        $query = "select ".$dbtable_id." from ".$dbtable." order by ".$dbtable_id." desc limit 0,1";
        //echo $query;
        $result = $ims->db->query($query);
        if ($row = $ims->db->fetch_row($result)) {
            $item_code = ($row[$dbtable_id] + 1) . $this->random_str(5, 'u');
            return $this->item_code($item_code_key, $item_code, $row[$dbtable_id]);
        }
        return $output;
    }
    public function get_group_nav ($parent_id, $group_id=0, $type='group') {
        $output = '';
        if($group_id <= 0 && $type == 'group'){
            return '';
        }
        $query = "select group_id, group_nav from product_group where group_id='".$parent_id."' limit 0,1";
        //echo $query;
        $result = $ims->db->query($query);
        if($row = $ims->db->fetch_row($result)){
            $output = $row['group_nav'];
            if($type == 'group'){
                $output .= ','.$group_id;
            }
        }else{
            if($type == 'group'){
                $output = $group_id;
            }
        }
        return $output;
    }
    public function list_menu($group_id = 'menu_header', $temp_name = 'menu') {
        // global $ims;
        $ims->data_menu();

        $arr_cur = array();
        $str_cur = '';

        $menu_aciton = (isset($this->conf['menu_action'])) ? $this->conf['menu_action'] : '';
        if (is_array($menu_aciton)) {
            foreach ($menu_aciton as $value) {
                $arr_menu_action = (isset($ims->data['menu_action'][$group_id][$value])) ? $ims->data['menu_action'][$group_id][$value] : array();
                $str_cur .= (!empty($str_cur)) ? ',' : '';
                $str_cur .= (isset($arr_menu_action["menu_nav"])) ? $arr_menu_action["menu_nav"] : '';
            }
            $arr_cur = (!empty($str_cur)) ? explode(',', $str_cur) : array();
        } else {
            $arr_menu_action = (isset($ims->data['menu_action'][$group_id][$menu_aciton])) ? $ims->data['menu_action'][$group_id][$menu_aciton] : array();
            $arr_cur = (isset($arr_menu_action["menu_nav"])) ? explode(',', $arr_menu_action["menu_nav"]) : array();
        }

        $arr_cur = array_unique($arr_cur);

        $output = array();

        if (isset($ims->data["menu_tree_" . $group_id]) && count($ims->data["menu_tree_" . $group_id]) > 0) {
            $menu_sub = '';
            $menu_more_tree = array();

            $num = count($ims->data["menu_tree_" . $group_id]);
            $i = 0;
            foreach ($ims->data["menu_tree_" . $group_id] as $row) {
                $i++;
                $row['link'] = $this->get_link_menu($row['link'], $row['link_type']);
                $row['class'] = (isset($row['class'])) ? $row['class'] : '';
                $row['class'] = (in_array($row["menu_id"], $arr_cur)) ? 'current' : $row['class'];
                $arr_class_li = array();
                if ($i == 1) {
                    $arr_class_li[] = 'first';
                }
                if ($i == $num) {
                    $arr_class_li[] = 'last';
                }
                $row['class_li'] = (count($arr_class_li) > 0) ? implode(' ', $arr_class_li) : '';
                $row['menu_sub'] = '';
                if ($row['auto_sub'] == 'group') {
                    // $row['menu_sub'] .= $this->list_menu_sub_group($temp_name, $row['name_action']);
                }
                if ($row['auto_sub'] == 'item') {
                    // $row['menu_sub'] .= $this->list_menu_sub_item($temp_name, $row['name_action']);
                }
                if (isset($row['arr_sub'])) {
                    // $row['menu_sub'] .= $this->list_menu_sub($temp_name, $row['arr_sub'], $arr_cur);
                }
                if ($row['menu_sub']) {
                    // $ims->temp_html->reset($temp_name . ".item.menu_sub");
                    // $ims->temp_html->assign('row', array('content' => $row['menu_sub']));
                    // $ims->temp_html->parse($temp_name . ".item.menu_sub");
                    // $row['menu_sub'] = $ims->temp_html->text($temp_name . ".item.menu_sub");
                    // $ims->temp_html->reset($temp_name . ".item.menu_sub");
                }
                $row_new = array();
                $row_new['title'] = $row['title'];
                $row_new['link'] = $row['link'];
                $output[] = $row_new;
            }
        }
        return $output;
    }
    public function do_product_by_group (){
        $pic_w = 428;
        $pic_h = 350;   
        $temp = 'group_product';
        $data = array();
        $output = array();
        $sql = "SELECT *
                FROM product_group
                WHERE is_show = 1 
                AND is_focus = 1 
                AND lang='".$this->conf['lang_cur']."' 
                ORDER BY show_order DESC, date_create DESC";
        $query = $ims->db->query($sql);
        $arr_group = array();
        $k = 0;
        while($group = $ims->db->fetch_row($query)){
            $arr_product = $ims->db->load_row_arr("product", "is_show = 1 AND is_approve AND is_focus = 1 AND lang = '".$this->conf['lang_cur']."' AND find_in_set('".$group['group_id']."', group_nav) ORDER BY show_order DESC, date_create DESC LIMIT 0,20");
            if(!empty($arr_product)){
                $output[$k]['group_id'] = $group['group_id'];
                $output[$k]['title'] = $group['title'];
                $output[$k]['menu_sub'] = array();
                $output[$k]['list_product'] = array();
                $arr_group = $ims->db->load_row_arr("product_group", "is_show = 1 AND parent_id='".$group['group_id']."' AND  lang = '".$this->conf['lang_cur']."' ORDER BY show_order DESC, date_create DESC LIMIT 0,20");
                if(!empty($arr_group)){
                    $j = 0;
                    foreach ($arr_group as $k_sub => $v) {
                        $v['link'] = $this->get_link_lang ($this->conf['lang_cur'] ,'product', $v['friendly_link']);
                        $output[$k]['menu_sub'][$j]['group_id'] = $v['group_id'];
                        $output[$k]['menu_sub'][$j]['title'] = $v['title'];
                        $j++;
                    }
                }
                $i = 1;
                foreach ($arr_product as $key => $row) {
                    //$row['link'] = $this->get_link_lang ($this->conf['lang_cur'] ,'product', $row['friendly_link']);
                    //$row["picture_zoom"] = $this->get_src_mod($row["picture"]);
                    //$row['picture'] = $this->get_src_mod($row['picture'], $pic_w, $pic_h, 1, 0, array('fix_max' => '1'));
                    // $value_price_buy = $ims->site_func->get_price_promotion($row);
                    // $row['price_buy'] = $ims->func->get_price_format($value_price_buy['price_buy']);
                    // $row['class_promotion'] = 'none';
                    // if($row['price_sale'] == $row['price']){
                    //     $row['class_price'] = 'none';
                    // }
                    // if($value_price_buy['price_buy'] != $row['price_sale']){
                    //     $row['info_price'] = 'none';
                    //     $row['sale'] =  'sale_now';
                    //     $row['promotion'] = $value_price_buy;
                    //     $row['price'] = $ims->func->get_price_format($row['price']);
                    //     $row['price_sale'] = $ims->func->get_price_format($row['price_sale']);
                    //     $ims->temp_box->assign('row', $row);
                    //     $ims->temp_box->parse("mod_item.price_promotion");
                    // }
                    // if($value_price_buy['price_buy'] != $row['price']){
                    //     $row['ribbon'] =  'sale';
                    //     $row['class_price'] =  'show';
                    //     $row['price'] = $ims->func->get_price_format($row['price']);
                    // }
                    // else{
                    //     $row['price'] = "";
                    //     $row['class_price'] =  'none';
                    // }
                    // $ims->temp_box->assign('row', $row);
                    // $ims->temp_box->reset("mod_item");
                    // $ims->temp_box->parse("mod_item");
                    $row_new = array();
                    $row_new['item_id'] = $row['item_id'];
                    $row_new['title'] = $row['title'];
                    $row_new['price'] = $row['price'];
                    $row_new['price_sale'] = $row['price_sale'];
                    $row_new['picture'] = $this->get_src_mod($row['picture']);
                    $output[$k]['list_product'][] = $row_new;
                    $i++;
                }
            }
            $k++;
        }
        return $output;
    }
    public function product_focus(){
        $pic_w = 428;
        $pic_h = 350;   
        $output = array();
        $arr_product = $ims->db->load_row_arr("product", "is_show = 1 AND is_approve AND is_focus = 1 AND lang = '".$this->conf['lang_cur']."' ORDER BY date_update DESC LIMIT 0, 50");
        $i = 1;
        foreach ($arr_product as $key => $row) {
            $row_new = array();
            $row_new['item_id'] = $row['item_id'];
            $row_new['title'] = $row['title'];
            $row_new['price'] = $row['price'];
            $row_new['price_sale'] = $row['price_sale'];
            $row_new['picture'] = $this->get_src_mod($row['picture']);
            $output[] = $row_new;
            $i++;
        }
        return $output;
    }
    public function product_nature_group($id = '', $type = 0){
        $output = array();
        $row = array();
        if($type == 1){
            $arr = array();
            foreach ($id as $k_res => $v_res) {
                $arr[$k_res] = $v_res['title'];
            }
            $arr = implode(', ', $arr);
            return $arr;
        }elseif($type == 2){
            $sql = "SELECT title, show_order FROM product_nature_group WHERE is_show = 1 AND is_focus = 1 AND group_id = ".$id." AND lang = '".$this->conf['lang_cur']."' LIMIT 0,1";
            $query = $ims->db->query($sql);
            $row = $ims->db->fetch_row($query);
        }
        else{
            $sql = "SELECT title, show_order FROM product_nature_group WHERE is_show = 1 AND group_id = ".$id." AND lang = '".$this->conf['lang_cur']."' LIMIT 0,1";
            $query = $ims->db->query($sql);
            $row = $ims->db->fetch_row($query);
        }
        return isset($row) ? $row : '';
    }
    function get_product_nature($id = ''){
        global $ims;
        $output = array();
        $sql = "SELECT * FROM product_nature WHERE is_show = 1 AND item_id = ".$id." AND lang = '".$this->conf['lang_cur']."' LIMIT 0,1";
        $query = $ims->db->query($sql);
        $row = $ims->db->fetch_row($query);
        return $row;
    }
    function time_str2int($str, $format = 'd/m/Y H:i') {
        $output = '';
        switch ($format) {
            case "d/m/Y":
                $date_tmp1 = explode('/', $str);
                $output = mktime(0, 0, 0, $date_tmp1[1], $date_tmp1[0], $date_tmp1[2]);
                break;
            default:
                $date_tmp = explode(' ', $str);
                $date_tmp1 = explode('/', $date_tmp[0]);
                $date_tmp2 = explode(':', $date_tmp[1]);
                $output = mktime($date_tmp2[0], $date_tmp2[1], 0, $date_tmp1[1], $date_tmp1[0], $date_tmp1[2]);
                break;
        }
        return $output;
    }

    function table_cart($data = array()){
        global $ims;

        $output = array();
        $arr = $ims->db->load_row_arr('product_order_detail', " order_id='".$data['order_id']."' order by detail_id asc ");
                     
        if (!empty($arr)) {
            $data['shipping_price_total'] = 0;
            foreach ($arr as $key => $row) {
                // if ($row['is_status'] == 0) {
                //     $row['is_status'] = 'Chưa hoàn tất';
                // }else{
                //     $row['is_status'] = 'Đã hoàn tất';
                // }
                // if ($row['is_cancel'] == 1) {
                //     $row['is_cancel_title'] = 'Đơn hàng đã bị hủy';
                // }else{
                //     $row['is_cancel_title'] = '';
                // }
                $row['pic_w'] = 100;
                $row['pic_h'] = 75;
                $row["picture"] =  $ims->func->get_src_mod($row["picture"], $row['pic_w'], $row['pic_h'], 1, 0, array('fix_max' => 1));
                $row['quantity'] = (isset($row['quantity'])) ? $row['quantity'] : 0;
                $row['total'] = $row['quantity'] * $row['price_buy'];
                $row['price_buy'] = $row['price_buy'];
                $row['total'] = $row['total'];
                $row['group_id'] = $ims->db->load_item('product', ' item_id = "'.$row['type_id'].'" and lang = "'.$ims->conf['lang_cur'].'" ', 'group_id');
                $row['group_title'] = $ims->db->load_item('product_group', ' group_id = "'.$row['group_id'].'" and lang = "'.$ims->conf['lang_cur'].'" ', 'title');
                $data['shipping_price_total'] += $row['freetransfer'];
                // if ($row['freetransfer'] >0) {
                //     $row['freetransfer'] = $row['freetransfer'];
                // }else{
                //     $row['freetransfer'] = 'Miễn phí';
                // }
                unset($row['order_id']);
                unset($row['type']);
                // unset($row['type_id']);
                unset($row['fee_type']);
                unset($row['out_stock']);
                unset($row['color_id']);
                unset($row['size_id']);
                unset($row['code_pic']);
                unset($row['is_cancel']);
                unset($row['is_cancel_title']);
                unset($row['pic_w']);
                unset($row['pic_h']);
                $output[] = $row;
            }
        }
        return $output;
    }

    function data_table ($table_name, $table_id, $sql_select='*', $sql_where='', $arr_is_array=array(), $arr_more = array()){
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
    function get_http_response_code($url) {
        $headers = get_headers($url);
        return substr($headers[0], 9, 3);
    }

    function check_token_user(){
        global $ims;

        $token_login = $ims->func->if_isset($ims->get['user'], 0);
        if (isset($token_login) && $token_login!='') {
            $this->setting('user');
            $userInfo = $ims->db->load_row('user',' FIND_IN_SET("'.$token_login.'", token_login) ');
            if (!empty($userInfo)) {
                return $userInfo;
            }else{
                $this->response(403, "", 403 , $ims->lang['api']['error_token_user']);
            }
        }else{
            $this->response(403, "", 403 , $ims->lang['api']['error_token_user']);
        }
    }

    function loginWithSocial ($type=''){
        global $ims;

        $device_token = $ims->func->if_isset($ims->post['device_token']);
        $device_name  = $ims->func->if_isset($ims->post['device_name']);

        $where = '';
        if ($type == "facebook") {
            // Tạo mới bằng tài khoản facebook
            $fb_id = $ims->func->if_isset($ims->post['fbID']);
            $info  = $ims->func->if_isset($ims->post['info']);
            $info = json_decode($info);
            if (empty($info)) {
                $this->response(404, "", 404 , $ims->lang['api']['error_loginFb_4']);
            }
            if ($fb_id == '') {
                $this->response(404, "", 404 , $ims->lang['api']['error_loginFb_4']);
            }
            $email = isset($info->email) ? $info->email : '';     
            $where .= " (fb_id='".$fb_id."') ";

        } elseif ($type == "google") {
            // Tạo mới bằng tài khoản goole
            $gg_id = $ims->func->if_isset($ims->post['ggID']);
            $info  = $ims->func->if_isset($ims->post['info']);
            $info = json_decode($info);            
            if (empty($info)) {
                $this->response(200, "", 200 , $ims->lang['api']['error_loginGg_4']);
            }
            if ($gg_id == '') {
                $this->response(200, "", 200 , $ims->lang['api']['error_loginGg_4']);
            }
            $email = isset($info->user->email) ? $info->user->email : '';     
            $where .= " (gg_id='".$gg_id."') ";

        } elseif ($type == "apple") {
            // Tạo mới bằng tài khoản apple
            $apID = isset($ims->post['apID']) ? $ims->post['apID'] : '';
            $info = isset($ims->post['info']) ? $ims->post['info'] : '';
            $info = json_decode($info);
            if (empty($info)) {
                $this->response(200, "", 200 , $ims->lang['api']['error_loginAp_4_1']);
            }
            if ($apID == '') {
                $this->response(200, "", 200 , $ims->lang['api']['error_loginAp_4']);
            }
            $email = isset($info->email) ? $info->email : '';     
            $where .= " (ap_id='".$apID."') ";

        }

        // Kiểm tra đã đăng ký chưa ??         
        $row = $ims->db->load_row("user", $where);        
        if (!empty($row)) {                        
            switch ($row['is_show']) {
                case 1:
                    $token = $this->createTokenLogin($row["user_id"], $row["token_login"], $device_token, $device_name);
                    $array = array(
                        "code"    => 200,
                        "message" => $ims->lang['api']['error_loginFb_0_1'],
                        "token"   => $token
                    );
                    $this->response(200, $array);
                    break;
                case 0:
                    $this->response(200, "", 200 , $ims->lang['api']['error_loginFb_2']);
                    break;
                case 2:
                    $this->response(200, "", 200 , $ims->lang['api']['error_loginFb_3']);
                    break;
            }
        }else{
            $check_isset = $ims->db->load_row('user'," (email!='' and email='".$email."') ",'user_id,picture,email,phone');            
            if ($check_isset>0) {                   
                $arr_in                  = array();
                if ($type == "facebook") {
                    if(empty($check_isset["fb_id"])){
                        $arr_in["fb_id"] = $fb_id;
                    }
                }
                if ($type == "google") {
                    if(empty($check_isset["gg_id"])){
                        $arr_in["gg_id"] = $gg_id;
                    }   
                }
                if ($type == "apple") {
                    if(empty($check_isset["ap_id"])){
                        $arr_in["ap_id"] = $apID;
                    }
                }
                $arr_in["date_update"]   = time();
                $ok = $ims->db->do_update("user", $arr_in, ' user_id="'.$check_isset['user_id'].'" ');
                $arr_in["user_id"]       = $check_isset["user_id"];
                $arr_in["picture"]       = $check_isset["picture"];
            }else{
                // Chưa đăng ký => Đăng ký mới
                $arr_in                  = array();
                $arr_in["user_id"]       = $ims->db->getAutoIncrement('user');
                $arr_in["password"]      = $ims->func->md25("123456");
                $arr_in["folder_upload"] = $arr_in['user_id'].'c'.$ims->func->random_str(4, 'ln');
                $arr_in["user_code"]     = $arr_in['user_id'].'c'.$ims->func->random_str(10, 'ln');
                $folder_conf             = 'user/' . $ims->func->fix_name_action($arr_in["folder_upload"]);
                $folder_conf            .= '/';        
                $folder_conf            .= date('Y_m').'/';
                $ims->func->rmkdir($folder_conf);

                if ($type == "facebook") {
                    $arr_in["first_name"]    = $ims->func->if_isset($info->first_name);
                    $arr_in["last_name"]     = $ims->func->if_isset($info->last_name);
                    $arr_in["full_name"]     = $ims->func->if_isset($info->name);
                    $arr_in["username"]      = $ims->func->if_isset($info->email,$info->phone);
                    $arr_in["email"]         = $ims->func->if_isset($info->email);
                    $arr_in["phone"]         = $ims->func->if_isset($info->phone);
                    $ext    = 'jpg';
                    $width  = "200";
                    $height = "200";
                    $fb_url = "https://graph.facebook.com/".$fb_id."/picture?width=$width&height=$height";
                    $fb_url = isset($info->picture->data->url) ? $info->picture->data->url : $fb_url;
                    $img_save_location =  $folder_conf . $arr_in["user_code"] . '.' . $ext;
                    if($this->get_http_response_code($fb_url) != "200") {

                    }else{
                        file_put_contents($ims->conf['rootpath_web']."uploads/" . $img_save_location, file_get_contents($fb_url));
                    }
                    $arr_in['picture'] = $img_save_location;
                    $arr_in["fb_id"]   = $fb_id;

                }elseif ($type == "google") {
                    $arr_in["first_name"]    = $ims->func->if_isset($info->user->givenName);
                    $arr_in["last_name"]     = $ims->func->if_isset($info->user->givenName);
                    $arr_in["full_name"]     = $ims->func->if_isset($info->user->name);
                    $arr_in["username"]      = $ims->func->if_isset($info->user->email);
                    $arr_in["email"]         = $ims->func->if_isset($info->user->email);
                    if (isset($info->user->photo)) {
                        $ext = 'jpg';
                        $img_save_location = 'uploads/'.$folder_conf.$arr_in["user_code"].'.'.$ext;
                        if($this->get_http_response_code($info->user->photo) != "200"){
                        }else{
                            file_put_contents($ims->conf['rootpath_web'].$img_save_location, file_get_contents($info->user->photo));
                        }
                        $arr_in['picture'] = $folder_conf.$arr_in["user_code"].'.'.$ext;
                    }
                    $arr_in["gg_id"]   = $gg_id;
                }elseif ($type == "apple") {
                    $arr_in["first_name"]    = $ims->func->if_isset($info->givenName);
                    $arr_in["last_name"]     = $ims->func->if_isset($info->givenName);
                    $arr_in["full_name"]     = $ims->func->if_isset($info->name);
                    $arr_in["username"]      = $ims->func->if_isset($info->email);
                    $arr_in["email"]         = $ims->func->if_isset($info->email);
                    $arr_in["ap_id"]         = $apID;
                }
                
                $arr_in["show_order"]    = 0;
                $arr_in["is_show"]       = 1;
                $arr_in["date_login"]    = time();
                $arr_in["date_create"]   = time();
                $arr_in["date_update"]   = time();
                $ok = $ims->db->do_insert("user", $arr_in);
            }
            if ($ok) {
                $token = $this->createTokenLogin($arr_in["user_id"], '', $device_token, $device_name);

                $arr_in['picture'] = !empty($arr_in['picture'])?$ims->conf['rooturl'].'uploads/'.$arr_in['picture']:NULL;
                $array = array(
                    "code"    => 200,
                    "message" => $ims->lang['api']['success'],
                    "token"   => $token,
                    "is_signup" => 1
                );
                $this->response(200, $array);
            }
        }
    }

    function createTokenLogin($user_id='', $token_login='', $device_token='', $device_name=''){
        global $ims;

        $output = "";
        $arr_log                 = array();
        $arr_log["user_id"]      = $user_id;
        $arr_log["type"]         = "Mobile";
        $arr_log["device_token"] = $device_token;
        $arr_log["device_name"]  = $device_name;
        $arr_log["token_login"]  = $ims->func->random_str(20, 'ln');
        $arr_log["is_show"]      = 1;
        $arr_log["show_order"]   = 0;
        $arr_log["date_create"]  = time();
        $arr_log["date_update"]  = time();
        $ok = $ims->db->do_insert("user_login_log", $arr_log);
        if ($ok) {
            $up = array();
            if ($token_login == "") {
                $up["token_login"] = $arr_log["token_login"];
            }else{
                $up["token_login"] = $token_login.','.$arr_log["token_login"];
            }
            $up["date_login"] = time();
            $ims->db->do_update("user", $up, " user_id='".$user_id."' ");
            $output = $arr_log["token_login"];
        }
        return $output;
    }

    // Upload hình ảnh
    public function upload_image($folder_upload, $name_input = 'picture'){
        global $ims;

        $output = array(
            'ok'    => 0,
            'mess'    => '',
        );

        $target_dir =  $ims->conf['rooturl_web'].$folder_upload;
        $target_file = $target_dir . basename($_FILES[$name_input]["name"]);
        if (isset($_FILES[$name_input]['type'])) {
            $imageFileType = $_FILES[$name_input]['type'];
            $imageFileType = explode('/', $imageFileType);
            $imageFileType = isset($imageFileType[1]) ? $imageFileType[1] : '';
        }else{
            $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        }

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES[$name_input]["tmp_name"]);
        if($check !== false) {
            //$output['mess'] =  "File is an image - " . $check["mime"] . ".";
            $output['ok'] = 1;
        } else {
            $output['mess'] =  "Chỉ được upload file hình ảnh!";
            $output['ok'] = 0;
        }


        // Check if file already exists
        if (file_exists($target_file)) {
            $output['mess'] =  "Sorry, file already exists.";
            $output['ok'] = 0;
            return $output;
        }

        // Check file size
        if ($_FILES[$name_input]["size"] > 5000000) {
            $output['mess'] =  "Xin lỗi, file upload phải nhỏ hơn 5mb.";
            $output['ok'] = 0;
            return $output;
        }

        // Allow certain file formats
        // if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        //     && $imageFileType != "gif" ) {
        //     $output['mess'] =  "Xin lỗi, chỉ được upload file .JPG, .JPEG, .PNG & .GIF";
        //     $output['ok'] = 0;
        //     return $output;
        // }

        // Check if $uploadOk is set to 0 by an error
        if ($output['ok'] == 0) {
            $output['mess'] =  "Quá trình upload không thành công!";
            return $output;
            // if everything is ok, try to upload file
        } else {

            $ims->func->rmkdir($folder_upload);
            // Save file
            $_FILES[$name_input]["name"] = strtolower(str_replace(' ', '_', $_FILES[$name_input]["name"]));
            move_uploaded_file($_FILES[$name_input]["tmp_name"], $ims->conf['rootpath'].'uploads/'.$folder_upload.'/'.time().'_'.$_FILES[$name_input]["name"]);
            $output['url_picture']  = $folder_upload.'/'.time().'_'.$_FILES[$name_input]["name"];
        }
        return $output;
    }

    // Upload hình ảnh
    public function upload_image_multi($folder_upload, $name_input = 'picture', $stt=0){
        global $ims;

        $output = array(
            'ok'    => 0,
            'mess'    => '',
        );


        $target_dir =  $ims->conf['rooturl_web'].$folder_upload;
        $target_file = $target_dir . basename($_FILES[$name_input]["name"][$stt]);
        if (isset($_FILES[$name_input]['type'][$stt])) {
            $imageFileType = $_FILES[$name_input]['type'][$stt];
            $imageFileType = explode('/', $imageFileType);
            $imageFileType = isset($imageFileType[1]) ? $imageFileType[1] : '';
        }else{
            $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        }

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES[$name_input]["tmp_name"][$stt]);
        if($check !== false) {
            //$output['mess'] =  "File is an image - " . $check["mime"] . ".";
            $output['ok'] = 1;
        } else {
            // $output['mess'] =  "Chỉ được upload file hình ảnh!";
            // $output['ok'] = 0;
            // return $output;
            $output['ok'] = 1;
        }


        // Check if file already exists
        if (file_exists($target_file)) {
            $output['mess'] =  "Sorry, file already exists.";
            $output['ok'] = 0;
            return $output;
        }

        // Check file size
        if ($_FILES[$name_input]["size"][$stt] > 52428800) {
            $output['mess'] =  "Xin lỗi, file upload phải nhỏ hơn 5mb. ".$_FILES[$name_input]["size"][$stt];
            $output['ok'] = 0;
            return $output;
        }

        // Allow certain file formats
        // if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        //     && $imageFileType != "gif" ) {
        //     $output['mess'] =  "Xin lỗi, chỉ được upload file .JPG, .JPEG, .PNG & .GIF";
        //     $output['ok'] = 0;
        //     return $output;
        // }

        // Check if $uploadOk is set to 0 by an error
        if ($output['ok'] == 0) {
            $output['mess'] =  "Quá trình upload không thành công!";
            return $output;
            // if everything is ok, try to upload file
        } else {

            $ims->func->rmkdir($folder_upload);
            // Save file
            $_FILES[$name_input]["name"][$stt] = strtolower(str_replace(' ', '_', $_FILES[$name_input]["name"][$stt]));
            move_uploaded_file($_FILES[$name_input]["tmp_name"][$stt], $ims->conf['rootpath'].'uploads/'.$folder_upload.'/'.time().'_'.$_FILES[$name_input]["name"][$stt]);
            $output['url_picture']  = $folder_upload.'/'.time().'_'.$_FILES[$name_input]["name"][$stt];
        }
        return $output;
    }

    // Xử lý option của sản phẩm
    public function processProductOption($value_option='',$option='', $arr_option = array()){
        global $ims;

        $output = array();

        foreach (array('option1', 'option2', 'option3') as $key => $value) {
            $output[$value]['value'] = $ims->func->if_isset($arr_option[$value]['value']);
        }
        if ($option == 'option1') {
            // Option1 => Không xử lý
            // Option2 => Theo Option1
            // Option3 => Theo Option1
            $output['option2']['value'] = $this->filterOption($arr_option, 'option2', $value_option);
            $output['option3']['value'] = $this->filterOption($arr_option, 'option3', $value_option);

        }elseif ($option == 'option2') {
            // Option2 => Không xử lý
            // Option1 => Theo Option2
            // Option3 => Theo Option2
            $output['option1']['value'] = $this->filterOption($arr_option, 'option1', $value_option);
            $output['option3']['value'] = $this->filterOption($arr_option, 'option3', $value_option);

        }elseif ($option == 'option3') {
            // Option3 => Không xử lý
            // Option1 => Theo Option3
            // Option2 => Theo Option3
            $output['option1']['value'] = $this->filterOption($arr_option, 'option1', $value_option);
            $output['option2']['value'] = $this->filterOption($arr_option, 'option2', $value_option);

        }
        return $output;
    }

    public function filterOption($arr_option, $key, $value_option){
        global $ims;

        $result = array();
        if (isset($arr_option[$key]['value']) && !empty($arr_option[$key]['value'])) {
            foreach ($arr_option[$key]['value'] as $k => $v) {
                if (!empty($v['data'])) {
                    foreach ($v['data'] as $k_d => $v_d) {
                        $k_d = explode('/', $k_d);
                        if (in_array($value_option, $k_d)) {
                            $result[$k]['title'] = $v['title'];
                            $result[$k]['data'] = $v['data'];
                        }
                    }
                }
            }
        }
        return $result;
    }

    // Lấy đánh giá theo modules
    public function form_rate($type='', $id=''){
        global $ims;

        $data = array();
        if ($type == 'product') {
            $data['product'] = $ims->db->load_row('product', 'item_id="'.$id.'" and is_show=1 and lang="'.$ims->conf['lang_cur'].'" ');            
        }
        $total_rate = 0;
        $data['average'] = 0;
        $data['count_1star'] = 0;
        $data['count_2star'] = 0;
        $data['count_3star'] = 0;
        $data['count_4star'] = 0;
        $data['count_5star'] = 0;
        $data['count_1percent'] = 0;
        $data['count_2percent'] = 0;
        $data['count_3percent'] = 0;
        $data['count_4percent'] = 0;
        $data['count_5percent'] = 0;
        $arr = $ims->db->load_item_arr("shared_rate", " type_id = '".$id."' AND type='".$type."' AND is_show = 1 ", 'rate');
        $data['num'] = count($arr);
        if (!empty($arr)) {
            foreach ($arr as $key => $value) {
                if ($value['rate']==1) {
                    $data['count_1star']++;
                }
                if ($value['rate']==2) {
                    $data['count_2star']++;
                }
                if ($value['rate']==3) {
                    $data['count_3star']++;
                }
                if ($value['rate']==4) {
                    $data['count_4star']++;
                }
                if ($value['rate']==5) {
                    $data['count_5star']++;
                }
                $total_rate += $value['rate'];
            }

        }
        if ($data['num']>0) {
            $data['count_1percent'] = ($data['count_1star']/$data['num'])*100;
            $data['count_2percent'] = ($data['count_2star']/$data['num'])*100;
            $data['count_3percent'] = ($data['count_3star']/$data['num'])*100;
            $data['count_4percent'] = ($data['count_4star']/$data['num'])*100;
            $data['count_5percent'] = ($data['count_5star']/$data['num'])*100;
        }
        if($total_rate != 0){
            $data['average'] = round($total_rate/$data['num'],1);
        }
        $data['total_rate'] = $total_rate;
        print_arr($data);die;
    }

    public function getRatingByProduct($type, $type_id, $get = "average"){
        global $ims;

        $output = array();
        $average = 0;
        $output['count'] = array();
        // $arr_rate = $ims->db->load_item_arr('shared_rate', ' type="'.$type.'" AND type_id="'.$type_id.'" AND is_show=1 ', 'type, type_id, rate');
        $arr_rate = $ims->db->load_item_arr('shared_comment', ' type="'.$type.'" AND type_id="'.$type_id.'" AND is_show=1 ', 'type, type_id, rate');
        if (!empty($arr_rate)) {
            $total = count($arr_rate);
            $total_rate = 0;
            foreach ($arr_rate as $key => $value) {
                $total_rate += $value['rate'];
                for ($i=1; $i <= 5; $i++) { 
                    $output['count'][$i.'star'] += ($i == $value['rate']) ? 1 : 0;
                }
            }
            $average = round(2*($total_rate/$total))/2;
        }else{
            for ($i=1; $i <= 5; $i++) { 
                $output['count'][$i.'star'] = 0;
            }
        }
        if ($get == 'average') {
            return $average;
        }elseif ($get == 'all') {
            $output['average'] = $average;
            return $output;
        }
    }

    public function promotion_code($info = array()){
        global $ims;
        $output = array();
        $token_login = $ims->func->if_isset($ims->get['user'], 0);

        $where = ' and ((type_promotion = "apply_all" OR type_promotion = "apply_freeship") and num_use < max_use) OR (type_promotion = "apply_product" and find_in_set('.$info['item_id'].', list_product))';
        
        if (isset($token_login) && $token_login!='') {
            $this->setting('user');
            $infoUser = $ims->db->load_row('user',' FIND_IN_SET("'.$token_login.'", token_login) ');
            if (!empty($infoUser)) {
                $where .= ' OR (type_promotion = "apply_user" and find_in_set('.$infoUser['user_id'].', list_user))';
            }
        }
        $result = $ims->db->load_item_arr('promotion', 'is_show = 1 and date_start < '.time().' and date_end > '.time().$where, 'promotion_id, type_promotion, short, picture, value_type, value, num_use, max_use, list_user, list_product, date_end');
        if($result){
            $i = 0;
            foreach ($result as $key => $row){
                if(in_array($row['type_promotion'], array('apply_product','apply_user'))){
                    $check = $this->check_promotion($row, $info['item_id']);
                    if($check == 0){
                        continue;
                    }
                }
                $i++;
                $result[$key]['short'] = $ims->func->input_editor_decode($row['short']);
                if($row['type_promotion'] == 'apply_freeship'){
                    $result[$key]['title'] = $ims->lang['product']['free_ship'];
                }else{
                    $result[$key]['title'] = $ims->lang['product']['decrease'].' '.(($row['value_type'] == 1) ? $row['value'].'%' : number_format($row['value'],0,',','.').'đ');
                }
                $result[$key]['picture'] = $ims->func->get_src_mod($row['picture']);
                // $result[$key]['date_end'] = date('d/m/Y', $row['date_end']);                
            }
            if($i > 0){
                $output = $result;
            }
        }
        return $output;
    }

    function check_promotion ($code, $item_id) {
        global $ims;

        $output = 1;

        $cancel_order = $ims->db->load_item('product_order_status', 'is_show = 1 and lang = "' . $ims->conf['lang_cur'] . '" and is_cancel = 1', 'item_id');

        // áp dụng cho email hoặc thành viên
        if ($code['type_promotion'] == 'apply_email' || $code['type_promotion'] == 'apply_user') {
            // áp dụng cho email
//                    if($code['type_promotion'] == 'apply_email' && isset($infoUser['email'])){
//                        if (in_array($infoUser['email'], explode(',',$code['list_email']))){
//                            $err_promotion = '';
//                        }
//                        $check_use_tmp = $ims->db->load_item_arr('product_order as po, product_order_detail as pod', 'po.lang = "'.$ims->conf['lang_cur'].'" and po.is_status != '.$cancel_order.' and po.promotion_id = "'.$promotion_code.'" and po.order_id = pod.order_id and pod.type_id IN('.$code['list_product'].')', 'pod.type_id');
//                        if($check_use_tmp){
//                            foreach ($check_use_tmp as $item){
//                                $check_use[] = $item['type_id'];
//                            }
//                            $check_use = array_count_values($check_use); // Mảng kiểm tra số lần sử dụng mã cho từng user
//                        }
//
//                    }else
            // áp dụng cho thành viên
            if ($code['type_promotion'] == 'apply_user' && isset($infoUser['user_id'])) {
                $total_use = $ims->db->load_item_arr('product_order', 'lang = "' . $ims->conf['lang_cur'] . '" and is_status != ' . $cancel_order . ' and promotion_id = "' . $code['promotion_id'] . '" and user_id = ' . $infoUser['user_id'], 'order_id');
                if (count($total_use) >= $code['max_use']) {
                    $output = 0;
                }
            }
        } elseif ($code['type_promotion'] == 'apply_product') {
            $check_use_tmp = $ims->db->load_item_arr('product_order as po, product_order_detail as pod', 'po.lang = "' . $ims->conf['lang_cur'] . '" and po.is_status != ' . $cancel_order . ' and po.promotion_id = "' . $code['promotion_id'] . '" and po.order_id = pod.order_id and pod.type_id = ' . $item_id, 'pod.type_id');
            if (count($check_use_tmp) >= $code['max_use']) {
                $output = 0;
            }
        }

        return $output;
    }

    public function get_avatar($str){
        $acronym;
        $word;
        $words = preg_split("/(\s|\-|\.)/", $str);
        foreach($words as $w) {
            $acronym .= substr($w,0,1);
        }
        $word = $word . $acronym ;
        return $word;
    }


    function sendPostDataGHTK($url, $post, $method = 'post', $token = 0, $type = 0){
        $curl = curl_init();
        switch ($method) {
            case 'put':
                $header = array(
                    "Content-Type:multipart/form-data",
                    "Retailer:vinafecom", 
                    "Authorization:Bearer ".$token."");
                curl_setopt_array($curl, array(
                    CURLOPT_RETURNTRANSFER  => 1,
                    CURLOPT_URL             => $url,
                    CURLOPT_CUSTOMREQUEST   => "PUT",
                    CURLOPT_HTTPHEADER      => $header,
                    CURLOPT_SSL_VERIFYPEER  => 0,
                    CURLOPT_POSTFIELDS      => $post
                ));
                break;
            case 'post':
                $header = array(
                    "Content-Type:application/json",
                    "Token: ".$token."");
                if ($type == 1) {
                    $header = array("Content-Type:application/x-www-form-urlencoded");
                }
                curl_setopt_array($curl, array(
                    CURLOPT_RETURNTRANSFER  => 1,
                    CURLOPT_URL             => $url,
                    CURLOPT_POST            => 1,
                    CURLOPT_HTTPHEADER      => $header,
                    CURLOPT_SSL_VERIFYPEER  => 0,
                    CURLOPT_POSTFIELDS      => $post
                ));
                break;
            case 'get':
                $header = array(
                    "Token: ".$token."
                ");
                curl_setopt_array($curl, array(
                    CURLOPT_URL             => $url,
                    CURLOPT_RETURNTRANSFER  => 1,
                    CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST   => "GET",
                    CURLOPT_HTTPHEADER      => $header,
                ));
                break;
            case 'delete':
                curl_setopt_array($curl, array(
                    CURLOPT_RETURNTRANSFER  => 1,
                    CURLOPT_URL             => $url,
                    CURLOPT_CUSTOMREQUEST   => "DELETE",
                    CURLOPT_HTTPHEADER      => $header,
                    CURLOPT_SSL_VERIFYPEER  => 0,
                    CURLOPT_POSTFIELDS      => $post
                ));
            default:
                break;
        }
        $resp = curl_exec($curl);
        curl_close($curl);
        return $resp;
    }

    public function apiGHN($name='', $dataAPI = array(), $token=''){
        global $ims;

        $output = array();
        $urlRootGHN = $ims->conf['URL_API_GHN'];
        switch ($name) {
            case 'Getfee':
                // Gethubs GHN 
                $urlAPI = $urlRootGHN."shiip/public-api/v2/shipping-order/fee";
                $dataAPI = json_encode($dataAPI);
                $result  = $this->sendPostDataGHN($urlAPI, $dataAPI, 'post', $token, 0, 2);
                $result  = json_decode($result);
                $output  = $result;
                break;
            case 'GetShop':
                // Lấy tất cả cửa hàng
                $urlAPI = $urlRootGHN."shiip/public-api/v2/shop/all";
                $dataAPI = json_encode($dataAPI);
                $result  = $this->sendPostDataGHN($urlAPI, $dataAPI, 'post', $token, 0, 2);
                $result  = json_decode($result);
                $output  = $result;
                break;
            case 'SignIn':
                // Signin GHN 
                $urlAPI  = $urlRootGHN.'SignIn';
                $dataAPI = array(
                    "token"    => "TokenStaging",
                    "Email"    => $username,
                    "Password" => $password
                );
                $dataAPI = json_encode($dataAPI);
                $result  = $this->sendPostData($urlAPI, $dataAPI, 'post', 2);
                $result  = json_decode($result);
                $output  = $result;
                break;
            case 'GetDistricts':
                // Gethubs GHN 
                $urlAPI = $urlRootGHN."GetDistricts";
                $dataAPI = array(
                    "token"    => $token,
                );
                $dataAPI = json_encode($dataAPI);
                $result  = $this->sendPostData($urlAPI, $dataAPI, 'post', 2);
                $result  = json_decode($result);
                $output  = $result;
                break;
            case 'FindAvailableServices':
                // FindAvailableServices GHN 
                $urlAPI  = $urlRootGHN."FindAvailableServices";
                $dataAPI = json_encode($dataAPI);
                $result  = $this->sendPostData($urlAPI, $dataAPI, 'post', 2);
                $result  = json_decode($result);
                $output  = $result;
                break;
            case 'CreateOrder':
                // CreateOrder GHN 
                $urlAPI  = $urlRootGHN."shipping-order/create";
                $dataAPI = json_encode($dataAPI);
                $result  = $this->sendPostDataGHN($urlAPI, $dataAPI, 'post', $token, 1257, 2);
                $result  = json_decode($result);
                $output  = $result;
                break;
            case 'GetWards':
                // GetWards GHN 
                $urlAPI  = $urlRootGHN."GetWards";
                $dataAPI = json_encode($dataAPI);
                $result  = $this->sendPostData($urlAPI, $dataAPI, 'post', 2);
                $result  = json_decode($result);
                $output  = $result;
                break;
            default:
                break;
        }
        return $output;
    }

    function sendPostDataGHN($url, $post, $method = 'post', $token = 0, $ShopId = 0, $type = 0){
        $curl = curl_init();
        switch ($method) {
            case 'put':
                $header = array(
                    "Content-Type:multipart/form-data",
                    "Retailer:vinafecom", 
                    "Authorization:Bearer ".$token."");
                curl_setopt_array($curl, array(
                    CURLOPT_RETURNTRANSFER  => 1,
                    CURLOPT_URL             => $url,
                    CURLOPT_CUSTOMREQUEST   => "PUT",
                    CURLOPT_HTTPHEADER      => $header,
                    CURLOPT_SSL_VERIFYPEER  => 0,
                    CURLOPT_POSTFIELDS      => $post
                ));
                break;
            case 'post':
                $header = array(
                    "token: ".$token."",
                    "Content-Type:application/json",
                    "ShopId:".$ShopId.""
                );
                curl_setopt_array($curl, array(
                    CURLOPT_RETURNTRANSFER  => 1,
                    CURLOPT_URL             => $url,
                    CURLOPT_POST            => 1,
                    CURLOPT_HTTPHEADER      => $header,
                    CURLOPT_SSL_VERIFYPEER  => 0,
                    CURLOPT_POSTFIELDS      => $post
                ));
                break;
            case 'get':
                $header = array(
                    "Token: ".$token."
                ");
                curl_setopt_array($curl, array(
                    CURLOPT_URL             => $url,
                    CURLOPT_RETURNTRANSFER  => 1,
                    CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST   => "GET",
                    CURLOPT_HTTPHEADER      => $header,
                ));
                break;
            case 'delete':
                curl_setopt_array($curl, array(
                    CURLOPT_RETURNTRANSFER  => 1,
                    CURLOPT_URL             => $url,
                    CURLOPT_CUSTOMREQUEST   => "DELETE",
                    CURLOPT_HTTPHEADER      => $header,
                    CURLOPT_SSL_VERIFYPEER  => 0,
                    CURLOPT_POSTFIELDS      => $post
                ));
            default:
                break;
        }
        $resp = curl_exec($curl);
        curl_close($curl);
        return $resp;
    }



    function list_product_bypromotion($row = ''){

        $where = ''; 
        $array_where = array();
        $list_product = $row['apply_product'];
        $list_group = $row['apply_group'];
        if($list_group == '' && $list_product == ''){
            return ' and find_in_set(item_id, "") ';
        } 
        if (strpos($list_group, ',') !== false) {
            $arr_group_nav = explode(',',$list_group);
            $i = 0;
            foreach($arr_group_nav as $value){
                $i++;
                array_push($array_where, "find_in_set('" . $value . "', group_nav)");
            }
            $where = implode(' or ', $array_where);
            if($list_product != ''){
                $where .= " or find_in_set(item_id, '".$list_product."') ";
            }
            $where = ' and ('.$where.') ';
        }
        else{
            if($list_group != ''){
                $where = " find_in_set('" . $list_group . "', group_nav) ";
            }
            if($list_product != ''){
                if($where != ''){
                    $where .= 'or';
                }
                $where .= " find_in_set(item_id, '".$list_product."') ";
            }
            $where = ' and ('.$where.') ';
        }
        return $where;
    }

    function get_price_promotion($row = array(), $type = 0){
        global $ims;

        $data = array();
        $output = array();
        $output['price_buy'] = isset($row['price_sale']) ? $row['price_sale'] : 0;
        $array_where = array();
        $where = "";
        if (strpos($row['group_nav'], ',') !== false) {
            $arr_group_nav = explode(',',$row['group_nav']);
            $i = 0;
            foreach($arr_group_nav as $value){
                $i++;
                array_push($array_where, "find_in_set('" . $value . "', apply_group)");
            }
            $where = implode(' or ', $array_where);
            if($row['item_id'] != ''){
                $where .= " or find_in_set('".$row['item_id']."', apply_product) ";
            }
            $where = ' ('.$where.') ';
        } else{
            if($row['group_nav'] != ''){
                $where = " find_in_set('" . $row['group_nav'] . "', apply_group) ";
            }
            if($row['item_id'] != ''){
                if($where != ''){
                    $where .= 'or';
                }
                $where .= " find_in_set('".$row['item_id']."', apply_product) ";
            }
            $where = ' ('.$where.') ';
        }        
        $sql = "SELECT * FROM product_promotion WHERE time_begin < '".date('H:i:s')."' AND time_end > '".date('H:i:s')."' AND date_begin < ".time()." AND date_end > ".time()." AND is_show = 1 AND ". $where ." ORDER BY show_order DESC , date_update DESC LIMIT 0,1";
        $result = $ims->db->query($sql);
        if($result){
            $row_promotion = $ims->db->fetch_row($result);            
            if(isset($row_promotion['date_begin']) && time() > $row_promotion['date_begin'] && time() < $row_promotion['date_end']){
                $output['content'] = $row_promotion['content'];
                $output['short'] = $row_promotion['short'];
                $output['title'] = $row_promotion['title'];                
                $output['quantity'] = isset($row_promotion['quantity'])?$row_promotion['quantity']:0;
                $output['date_begin'] = $row_promotion['date_begin'];
                $output['date_end'] = $row_promotion['date_end'];
                $output['value_type'] = $row_promotion['value_type'];
                $output['value'] = $row_promotion['value'];
                if(isset($row_promotion['value_type']) && $row_promotion['value_type'] == 0){
                    $output['price_buy'] = $row['price_sale'] - $row_promotion['value'];
                    if($output['price_buy'] <= 0){
                        $output['price_buy'] = $row['price_sale'];
                    }
                }
                else if(isset($row_promotion['value_type']) && $row_promotion['value_type'] == 1){
                    $output['price_buy'] = $row['price_sale'] - ($row['price_sale']*$row_promotion['value']/100);
                }                
                //------------------------------------------------- quantity of product in promotion                
                if(isset($row_promotion['quantity']) && isset($row['in_promo'])){
                    if($row['in_promo']==0){                        
                        $SQL_UPDATE = "UPDATE product SET in_stock =  ". $row_promotion['quantity'] .", in_promo = 1 , date_update = ". time() ." WHERE is_show = 1 AND item_id = ".$row['item_id']." AND lang = '".$ims->conf['lang_cur']."' ";
                        $ims->db->query($SQL_UPDATE);
                    }
                    if($row_promotion['quantity']>0 && $row_promotion['quantity']<$row['in_stock']){
                        $SQL_UPDATE = "UPDATE product SET in_stock =  ". $row_promotion['quantity'] .", date_update = ". time() ." WHERE is_show = 1 AND item_id = ".$row['item_id']." AND lang = '".$ims->conf['lang_cur']."' ";                        
                        $ims->db->query($SQL_UPDATE);
                    }                    
                }
                //-------------------------------------------------
                if($row['price_buy'] != $output['price_buy']){
                    $SQL_UPDATE = "UPDATE product SET price_buy =  ". $output['price_buy'] ." , date_update = ". time() ." WHERE is_show = 1 AND item_id = ".$row['item_id']." AND lang = '".$ims->conf['lang_cur']."' ";
                    $ims->db->query($SQL_UPDATE);
                }
            }            
        }
        return $output;
    }

    function short_no_cut($str) {
        global $ims;
        $str = $ims->func->input_editor_decode($str);
        $str = strip_tags($str);
        return $str;
    }

    function promotion_info ($arr_cart = array(), &$code, $arr_cart_list_pro = array()) {
        global $ims;
        $ims->func->load_language('product');
        // $infoUser = $this->check_token_user();
        $token_login = $ims->func->if_isset($ims->get['user'], 0);
        $infoUser = $ims->db->load_row('user', ' FIND_IN_SET("'.$token_login.'", token_login) ');
        if (empty($infoUser)) {             
            $infoUser['user_id'] = 0;
            // $infoUser['email'] = $ims->func->if_isset($ims->post['o_email']);
            // $infoUser['phone'] = $ims->func->if_isset($ims->post['o_phone']);
            // $infoUser['full_name'] = $ims->func->if_isset($ims->post['o_full_name']);
        }
        $output = array(
           'price'        => 0,
           'promotion_id' => 0,
           'value'        => 0,
           'value_type'   => 0,
           'value_max'    => 0,
           'percent'      => 0,
           'mess'         => $ims->lang['product']['err_promotion_wrong'],
           'ok'           => 0,
           'type'         => 'voucher',
        );
       
        $err_promotion = '';
        
        if (empty($arr_cart_list_pro)) {
            $output['mess'] = $ims->lang['product']['cart_empty'];
            return $output;
        }
        $cartProduct = $this->data_table (
            'product', 
            'item_id', '*', 
            ' FIND_IN_SET(item_id, "'.@implode(',', $arr_cart_list_pro).'")>0 and is_show=1 and lang="'.$ims->conf['lang_cur'].'"'
        );
        $cartOption = $this->data_table(
            'product_option',
            'id', '*',
            ' FIND_IN_SET(ProductId, "'.@implode(',', $arr_cart_list_pro).'")>0  and is_show=1 and lang="'.$ims->conf['lang_cur'].'"'
        );
        $cart_total = 0;
        $cart_total_include_combo = 0;        
        foreach ($arr_cart as $k => $v) {
            $option  = $ims->func->if_isset($cartOption[$v['option_id']], array());
            $v['price_buy'] = $option['PriceBuy'];
            $v['total'] = $v['quantity'] * $v['price_buy'];
            if($cartProduct[$v['item_id']]['is_use_promotion_code'] != 2){
                $cart_total += $v['total'];
                // Tính thêm tiền cho các sp mua kèm theo combo
                $gift_include = $this->combo_gift_include($v);
                $cart_total_include_combo += $v['total'];
                $cart_total_include_combo += $gift_include['add_payment'];
            }
        }
        if(empty($cart_total_include_combo)){
            $err_promotion = $ims->lang['product']['err_promotion_total_price'];
        }
        $promotion_code = (isset($code) && $code) ? trim($code) : '';
        $code = $ims->db->load_row("promotion", " (is_show=1 or is_show=2) AND promotion_id='".$promotion_code."' ");        
        if (!empty($code)) {
            $check_use = array();
            $cancel_order = $ims->db->load_item('product_order_status', 'is_show = 1 and lang = "'.$ims->conf['lang_cur'].'" and is_cancel = 1', 'item_id');
            $check_product_apply = 0;
            $list_product = array();
            if($code['is_show'] == 2){
                $total_item_cart = 0;
                foreach ($arr_cart as $v){
                    $total_item_cart += $v['quantity'];
                }
                if($total_item_cart < $ims->setting['product']['min_cart_item_discount']){
                    $err_promotion = $ims->site_func->get_lang('not_enough_num_product', 'product', array('[num]' => $ims->setting['product']['min_cart_item_discount']));
                }
                $output['type'] = 'event';
            }else{
                // áp dụng cho email hoặc thành viên
                if ($code['type_promotion'] == 'apply_email' || $code['type_promotion'] == 'apply_user'){
                    if(empty($infoUser)) {
                        $err_promotion = $ims->lang['product']['err_promotion_login'];
                    }else{
                        $err_promotion = $ims->lang['product']['err_promotion_user'];
                        // áp dụng cho email
    //                    if($code['type_promotion'] == 'apply_email' && isset($infoUser['email'])){
    //                        if (in_array($infoUser['email'], explode(',',$code['list_email']))){
    //                            $err_promotion = '';
    //                        }
    //                        $check_use_tmp = $ims->db->load_item_arr('product_order as po, product_order_detail as pod', 'po.lang = "'.$ims->conf['lang_cur'].'" and po.is_status != '.$cancel_order.' and po.promotion_id = "'.$promotion_code.'" and po.order_id = pod.order_id and pod.type_id IN('.$code['list_product'].')', 'pod.type_id');
    //                        if($check_use_tmp){
    //                            foreach ($check_use_tmp as $item){
    //                                $check_use[] = $item['type_id'];
    //                            }
    //                            $check_use = array_count_values($check_use); // Mảng kiểm tra số lần sử dụng mã cho từng user
    //                        }
    //
    //                    }else
                        // áp dụng cho thành viên
                        if($code['type_promotion'] == 'apply_user' && isset($infoUser['user_id'])){
                            if(in_array($infoUser['user_id'], explode(',',$code['list_user']))){
                                $err_promotion = '';
                            }
                            $total_use = $ims->db->load_item_arr('product_order', 'lang = "'.$ims->conf['lang_cur'].'" and is_status != '.$cancel_order.' and promotion_id = "'.$promotion_code.'" and user_id = '.$infoUser['user_id'], 'order_id');
                            if(count($total_use) >= $code['max_use']){
                                $err_promotion = $ims->lang['product']['err_promotion_numover'];
                            }
                        }
                    }
                }elseif ($code['type_promotion'] == 'apply_product'){
                    $check_use_tmp = $ims->db->load_item_arr('product_order as po, product_order_detail as pod', 'po.lang = "'.$ims->conf['lang_cur'].'" and po.is_status != '.$cancel_order.' and po.promotion_id = "'.$promotion_code.'" and po.order_id = pod.order_id and pod.type_id IN('.$code['list_product'].')', 'pod.type_id');
                    if($check_use_tmp){
                        foreach ($check_use_tmp as $item){
                            $check_use[] = $item['type_id'];
                        }
                        $check_use = array_count_values($check_use); // Mảng kiểm tra số lần sử dụng mã cho từng sp
                    }
                    $err_promotion = $ims->lang['product']['err_promotion_product'];
                    foreach($arr_cart_list_pro as $product) {
                        // sản phẩm được áp dụng có trong giỏ hàng
                        if(in_array($product, explode(',', $code['list_product']))){
                            $err_promotion = '';
                            if(isset($check_use[$product])){
                                if($check_use[$product] < $code['max_use']){
                                    $list_product[] = $product;
                                }
                            }else{
                                $list_product[] = $product;
                            }
                            if(empty($list_product)){
                                $err_promotion = $ims->lang['product']['err_promotion_numover'];
                            }else{
                                $check_product_apply = 1;
                            }
                        }
                    }
                }
            }

            if($err_promotion == ''){
                // chưa tới ngày sử dụng
                if($code['date_start'] > time()) {
                    $err_promotion = $ims->lang['product']['err_promotion_notyet_timetouse'];

                    // mã hết hạn
                }elseif($code['date_end'] < time()) {
                    $err_promotion = $ims->lang['product']['err_promotion_date_end'];

                    // mã này đã hết lượt sử dụng, đối với loại dùng chung tất cả, hoặc freeship
                }elseif (!in_array($code['type_promotion'], array('apply_product', 'apply_user')) && $code['num_use'] >= $code['max_use']) {
                    $err_promotion = $ims->lang['product']['err_promotion_numover'];

                    // giá trị đơn hàng tối thiểu chưa đủ
                }elseif($code['total_min'] > 0 && round($code['total_min']) > round($cart_total_include_combo)) {
                    $err_promotion = str_replace('{min_cart}', $ims->func->get_price_format($code['total_min'], 0), $ims->lang['product']['err_promotion_min_cart']);

                    // mã thường
                }elseif ($code['type_promotion'] != 'apply_freeship' && $err_promotion == ''){

                    // áp dụng thành công + không có sản phẩm áp dụng trong giỏ hàng
                    if($check_product_apply == 0){
                        $tmp_price = 0;
                        switch ($code['value_type']){
                            case 1:
                                $tmp_percent = $code['value'];
                                $tmp_price = round(($tmp_percent * $cart_total_include_combo) / 100, 2);
                                if($code['value_max'] > 0 && $tmp_price > $code['value_max']){
                                    $tmp_price = $code['value_max'];
                                }
                                break;
                            default:
                                $tmp_price = $code['value'];
                                $tmp_percent = round(($tmp_price * 100) / $cart_total_include_combo, 2);
                                if($tmp_price > $cart_total_include_combo){
                                    $tmp_price = $cart_total_include_combo;
                                    $tmp_percent = 100;
                                }
                                break;
                        }

                        $output['value_type']   = $code['value_type'];
                        $output['value']        = $code['value'];
                        $output['value_max']    = $code['value_max'];
                        $output['total_min']    = $code['total_min'];
                        $output['price']        = $tmp_price;
                        $output['percent']      = $tmp_percent;
                        $output['promotion_id'] = $code['promotion_id'];
                        $output['ok']           = 1;

                        // áp dụng thành công + có sản phẩm áp dụng trong giỏ hàng
                    }elseif($check_product_apply == 1){
                        $tmp_price = 0;
                        if(!empty($arr_cart)){
                            foreach($arr_cart as $row) {
                                if (in_array($row['item_id'], $list_product)) {
//                                    $row_product = $ims->func->if_isset($cartProduct[$row['item_id']], array());
                                    $row_option  = $ims->func->if_isset($cartOption[$row['option_id']], array());
                                    switch ($code['value_type']){
                                        case 1:
                                            $tmp_percent = $code['value'];
                                            $price = round(($tmp_percent * $row_option['PriceBuy']*$row['quantity']) / 100, 2);
                                            if($code['value_max'] > 0 && $price > $code['value_max']){
                                                $price = $code['value_max'];
                                            }
                                            $tmp_price += $price;

                                            break;
                                        default:
                                            $tmp_price += $code['value'];
                                            break;
                                    }
                                }
                            }
                        }
                        if($tmp_price > $cart_total){
                            $tmp_price = $cart_total;
                            $tmp_percent = 100;
                        }else{
                            $tmp_percent = round(($tmp_price * 100) / $cart_total, 2);
                        }

                        $output['value_type']   = $code['value_type'];
                        $output['value']        = $code['value'];
                        $output['value_max']    = $code['value_max'];
                        $output['total_min']    = $code['total_min'];
                        $output['price']        = $tmp_price;
                        $output['percent']      = $tmp_percent;
                        $output['promotion_id'] = $code['promotion_id'];
                        $output['ok']           = 1;
                    }else{
                        
                    }
                    // mã freeship
                }elseif($code['type_promotion'] == 'apply_freeship'){
                    $output['freeship'] = 1;
                    $err_promotion = $ims->lang['product']['freeship'];
                    $output['promotion_id'] = $code['promotion_id'];
                }
            }
            $output['type_promotion'] = $code['type_promotion'];
        }else {
            $err_promotion = $ims->lang['product']['err_promotion_wrong'];            
        }        
        $output['mess'] = $err_promotion;
        return $output;
    }

    function promotion_discount_per_item ($arr_cart=array(),$item_id=0, $price=0, $promotion_code='',$arr_cart_list_pro=array()) {
        global $ims;
        if(!isset($ims->lang['product'])){
            $ims->func->load_language('product');
        }
        $infoUser = $this->check_token_user();
        $output = array(
            'price_minus' => 0,
        );

        $err_promotion = '';

        $cartProduct = $ims->load_data->data_table (
            'product',
            'item_id', '*',
            ' FIND_IN_SET(item_id, "'.@implode(',', $arr_cart_list_pro).'")>0 '.$ims->conf['where_lang']
        );
        $cartOption = $ims->load_data->data_table(
            'product_option',
            'id', '*',
            ' FIND_IN_SET(ProductId, "'.@implode(',', $arr_cart_list_pro).'")>0 '.$ims->conf['where_lang']
        );

        $cart_total = 0;
        $cart_total_include_combo = 0;
        foreach ($arr_cart as $k => $v) {
            $option  = $ims->func->if_isset($cartOption[$v['option_id']], array());
            $v['price_buy'] = $option['PriceBuy'];
            $v['total'] = $v['quantity'] * $v['price_buy'];
            if($cartProduct[$v['item_id']]['is_use_promotion_code'] != 2){
                $cart_total += $v['total'];

                // Tính thêm tiền cho các sp kèm theo combo
                $gift_include = $this->combo_gift_include($v);
                $cart_total_include_combo += $v['total'];
                $cart_total_include_combo += $gift_include['add_payment'];
            }
        }

        $code = $ims->db->load_row("promotion", " is_show=1 AND promotion_id='".$promotion_code."' ");

        if (!empty($code)) {
            $check_use = array();
            $cancel_order = $ims->db->load_item('product_order_status', 'is_show = 1 and lang = "'.$ims->conf['lang_cur'].'" and is_cancel = 1', 'item_id');
            $check_product_apply = 0;
            $list_product = array();

            // áp dụng cho email hoặc thành viên
            if ($code['type_promotion'] == 'apply_email' || $code['type_promotion'] == 'apply_user'){                
                $err_promotion = $ims->lang['product']['err_promotion_user'];
                // áp dụng cho email
//                    if($code['type_promotion'] == 'apply_email' && isset($infoUser['email'])){
//                        if (in_array($infoUser['email'], explode(',',$code['list_email']))){
//                            $err_promotion = '';
//                        }
//                        $check_use_tmp = $ims->db->load_item_arr('product_order as po, product_order_detail as pod', 'po.lang = "'.$ims->conf['lang_cur'].'" and po.is_status != '.$cancel_order.' and po.promotion_id = "'.$promotion_code.'" and po.order_id = pod.order_id and pod.type_id IN('.$code['list_product'].')', 'pod.type_id');
//                        if($check_use_tmp){
//                            foreach ($check_use_tmp as $item){
//                                $check_use[] = $item['type_id'];
//                            }
//                            $check_use = array_count_values($check_use); // Mảng kiểm tra số lần sử dụng mã cho từng user
//                        }
//
//                    }else
                // áp dụng cho thành viên
                if($code['type_promotion'] == 'apply_user' && isset($infoUser['user_id'])){
                    if(in_array($infoUser['user_id'], explode(',',$code['list_user']))){
                        $err_promotion = '';
                    }
                    $total_use = $ims->db->load_item_arr('product_order', 'lang = "'.$ims->conf['lang_cur'].'" and is_status != '.$cancel_order.' and promotion_id = "'.$promotion_code.'" and user_id = '.$infoUser['user_id'], 'order_id');
                    if(count($total_use) >= $code['max_use']){
                        $err_promotion = $ims->lang['product']['err_promotion_numover'];
                    }
                }
            }elseif ($code['type_promotion'] == 'apply_product'){
                $check_use_tmp = $ims->db->load_item_arr('product_order as po, product_order_detail as pod', 'po.lang = "'.$ims->conf['lang_cur'].'" and po.is_status != '.$cancel_order.' and po.promotion_id = "'.$promotion_code.'" and po.order_id = pod.order_id and pod.type_id IN('.$code['list_product'].')', 'pod.type_id');
                if($check_use_tmp){
                    foreach ($check_use_tmp as $item){
                        $check_use[] = $item['type_id'];
                    }
                    $check_use = array_count_values($check_use); // Mảng kiểm tra số lần sử dụng mã cho từng sp
                }
                $err_promotion = $ims->lang['product']['err_promotion_product'];

                // sản phẩm được áp dụng có trong giỏ hàng
                if(in_array($item_id, explode(',', $code['list_product']))){
                    $err_promotion = '';
                    if(isset($check_use[$item_id])){
                        if($check_use[$item_id] < $code['max_use']){
                            $list_product[] = $item_id;
                        }
                    }else{
                        $list_product[] = $item_id;
                    }
                    if(empty($list_product)){
                        $err_promotion = $ims->lang['product']['err_promotion_numover'];
                    }else{
                        $check_product_apply = 1;
                    }
                }
            }

            if($err_promotion == ''){
                // chưa tới ngày sử dụng
                if($code['date_start'] > time()) {
                    $err_promotion = $ims->lang['product']['err_promotion_notyet_timetouse'];

                    // mã hết hạn
                }elseif($code['date_end'] < time()) {
                    $err_promotion = $ims->lang['product']['err_promotion_date_end'];

                    // mã này đã hết lượt sử dụng, đối với loại dùng chung tất cả, hoặc freeship
                }elseif (!in_array($code['type_promotion'], array('apply_product', 'apply_user')) && $code['num_use'] >= $code['max_use']) {
                    $err_promotion = $ims->lang['product']['err_promotion_numover'];

                    // giá trị đơn hàng tối thiểu chưa đủ
                }elseif($code['total_min'] > 0 && round($code['total_min']) > round($cart_total_include_combo)) {
                    $err_promotion = str_replace('{min_cart}', $ims->func->get_price_format($code['total_min'], 0), $ims->lang['product']['err_promotion_min_cart']);

                    // mã thường
                }elseif ($code['type_promotion'] != 'apply_freeship' && $err_promotion == ''){

                    // áp dụng thành công + áp dụng cho tất cả sp
                    if($check_product_apply == 0){
                        switch ($code['value_type']){
                            case 1:
                                $tmp_percent = $code['value'];
                                $tmp_price = round($tmp_percent * $cart_total / 100, 2);
                                if($code['value_max'] > 0 && $tmp_price > $code['value_max']){
                                    $tmp_price = $code['value_max'];
                                }
                                break;
                            default:
                                $tmp_price = $code['value'];
                                if($tmp_price > $cart_total){
                                    $tmp_price = $cart_total;
                                }
                                break;
                        }
                        $percent = round($tmp_price/$cart_total*100, 2);
                        $output['price_minus'] = $price * $percent / 100;

                    // áp dụng thành công + có sản phẩm áp dụng trong giỏ hàng
                    }elseif($check_product_apply == 1){
                        $tmp_price = 0;
                        if(!empty($arr_cart)){
                            if (in_array($item_id, $list_product)) {
                                switch ($code['value_type']){
                                    case 1:
                                        $tmp_percent = $code['value'];
                                        $tmp_price = round($tmp_percent * $price / 100, 2);
                                        if($code['value_max'] > 0 && $tmp_price > $code['value_max']){
                                            $tmp_price = $code['value_max'];
                                        }
                                        break;
                                    default:
                                        $tmp_price = $code['value'];
                                        break;
                                }
                            }
                        }
                        if($tmp_price > $price){
                            $tmp_price = $price;
                        }
                        $output['price_minus'] = $tmp_price;
                    }
                }
            }
        }
        return $output;
    }

    function combo_gift_include($row){
        global $ims;
        $out = array(
            'add_payment' => 0
        );
        if(!empty($row['combo_info'])){           
            // $combo_info =  $ims->func->unserialize($row['combo_info']);
            $combo_info =  $row['combo_info'];
            $k_combo = array_keys($combo_info);            
            $k_combo = str_replace('_id', '', $k_combo[0]);
            $v_combo = array_values($combo_info);
            $row[$k_combo] = $v_combo[0];
        }

        $combo = $ims->db->load_row('product as pd, combo as cb', 'pd.is_show = 1 and pd.lang = "'.$ims->conf['lang_cur'].'" and pd.item_id = '.$row['item_id'].' and pd.combo_id = cb.item_id', 'cb.item_id, cb.title, cb.type, cb.value, cb.value_type');
        if($combo){
            if($combo['type'] != 1){
                if(isset($row['include']) && $row['include'] != ''){
                    $arr_include = $ims->db->load_item_arr('product', 'is_show = 1 and lang = "'.$ims->conf['lang_cur'].'" and item_id IN('.$row['include'].') order by FIELD(item_id,"'.$row['include'].'") desc', 'title, picture, price_buy, friendly_link');
                    if($arr_include){
                        foreach ($arr_include as $include){
                            $include['price_buy'] = ($combo['value_type'] == 1) ? $include['price_buy']*((100 - $combo['value'])/100) : ($include['price_buy'] - $combo['value']);
                            if($include['price_buy'] < 0){
                                $include['price_buy'] = 0;
                            }
                            $out['add_payment'] += $include['price_buy'];
                        }
                    }
                }
            }
        }
        return $out;
    }

    function get_status_order_by_list_string ($string) {
        global $ims;
        
        $status = 0;

        $ims->setting_ordering['product_order_status'] = $this->data_table(
            'product_order_status', 
            'item_id', '*', 
            "lang='".$ims->conf['lang_cur']."' ORDER BY show_order DESC, date_create DESC", array()
        );
        $status_access = (isset($ims->setting_ordering['product_order_status'])) ? $ims->setting_ordering['product_order_status'] : array();
        foreach ($status_access as $key => $value) {
            $list_status_string = explode(',', $value['list_status_string']);
            if (in_array($string, $list_status_string)) {
                $status = $value;
            }
        }
        return $status;
    }

    function returnsPaging($data = array()){
        global $ims;

        $ims->site_func->setting($data['modules']);
        $p = $ims->func->if_isset($ims->get['p'], 1);
        $numshow = $ims->func->if_isset($ims->get['numshow'], 0);
        $item_id = $ims->func->if_isset($ims->get['item_id'], 0);
        $keyword = $ims->func->if_isset($ims->get['keyword'], '');

        $where = " is_show=1 AND lang='".$ims->conf['lang_cur']."' ";
        $order_by = " ORDER BY show_order DESC, date_update DESC ";
        if (isset($data['where']) && $data['where'] != '') {
            $where .= $data['where'];
        }
        if ($item_id>0) {
            $where .= ' AND item_id="'.$item_id.'" ';
        }
        $n = 10;
        if ($numshow > 0) {
            $n = $numshow;
            if ($numshow > 500) {
                $n = 10;
            }
        }

        if ($keyword != '') {
            $arr_tmp = array();
            $arr_key = explode(' ', $keyword);
            foreach ($arr_key as $value) {
                $value = trim($value);
                if (!empty($value)) {
                    $arr_tmp[] = "title LIKE '%" . $value . "%'";
                }
            }
            if (count($arr_tmp) > 0) {         
                $where .= " AND (" . implode(" AND ", $arr_tmp) . ") ";
            }
        }

        // handle list
        $list_get = '';
        $arr_tmp = array();
        foreach ($data['column'] as $k => $v) {
            $arr_tmp[] = $v['key'];
        }
        if (!empty($arr_tmp)) {
            $list_get = implode(',', $arr_tmp);
        }

        $num_total = $ims->db->do_get_num($data['table'], $where."  ");
        $num_items = ceil($num_total / $n);
        if ($p > $num_items)
            $p = $num_items;
        if ($p < 1)
            $p = 1;
        $start = ($p - 1) * $n;

        $arr = $ims->db->load_item_arr($data['table'], $where.''.$order_by.' LIMIT '.$start.', '.$n, $list_get);
        $arr = $this->handleArr($arr, $data['column'], $item_id);

        if ($item_id > 0 
            && isset($data['arr_related']) && $data['arr_related'] == 1 
            && isset($arr[0]) && !empty($arr[0])) {
            $detail = $arr[0];
            $where_nav = '';
            if (!empty($arr[0]['group_id'])) {
                $where_nav.= " FIND_IN_SET('".$detail['group_id']."',group_nav) AND item_id!='".$detail['item_id']."' AND ";
            }
            $arr_nav = $ims->db->load_item_arr($data['table'], $where_nav .'is_show=1 AND lang="'.$ims->conf['lang_cur'].'" '.$order_by.' LIMIT 0,'.$ims->setting[$data['modules']]["num_order_detail"].' ', $list_get);


            $arr_nav = $this->handleArr($arr_nav, $data['column'], $item_id);

            $array = array(
                "code" => 200,
                "message" => $ims->lang['api']['success'],
                "data" => isset($detail) ? $detail : array(),
                "arr_related" => $arr_nav
            );
            $this->response(200, $array);
        }
        $array = array(
            "code" => 200,
            "message" => $ims->lang['api']['success'],
            "total_page" => $num_items,
            "total" => $num_total,
            "page" => $p,
            "data" => $arr
        );
        $this->response(200, $array);
    }

    function handleArr($arr = array(), $column = array(), $item_id = 0){
        global $ims;

        if (!empty($arr)) {
            foreach ($arr as $key => $value) {
                foreach ($column as $k => $v) {
                    if ($v['type'] == 'picture') {
                        $arr[$key][$v['key']] = $ims->func->get_src_mod($value[$v['key']]);
                        if ($v['key'] == 'picture') {
                            $arr[$key]['thumbnail'] = $ims->func->get_src_mod($v['key'], 40, 40 , 1, 1);
                        }
                    }elseif ($v['type'] == 'date') {
                        $arr[$key][$v['key']] = $ims->func->get_date_format($value[$v['key']], 0);

                    }elseif ($v['type'] == 'datetime') {
                        $arr[$key][$v['key']] = $ims->func->get_date_format($value[$v['key']], 1);

                    }elseif ($v['type'] == 'editor') {
                        $arr[$key][$v['key']] = $ims->func->input_editor_decode($value[$v['key']]);
                        if ($v['key'] == 'short') {
                            $arr[$key][$v['key']] = $ims->func->short($value[$v['key']], 400);
                        }
                    }elseif ($v['type'] == 'number') {

                    }elseif ($v['type'] == 'title') {

                    }
                }
                if ($item_id == 0 && isset($arr[$key]['content'])) {
                    unset($arr[$key]['content']);
                }
            }
        }
        return $arr; 
    }

    function do_check_in_stock($arr_cart=array(), $arr_pro=array(), $arr_op=array()){
        global $ims;

        $output = array(
            'ok' => array(1),
            'out_stock' => array(),
        );
        $out_stock = array();
        if(is_array($arr_cart) && count($arr_cart) > 0){
            foreach ($arr_cart as $row) {
                $row_pro = $arr_pro[$row['item_id']];
                $row_op = $arr_op[$row['option_id']];
                // Có quản lý tồn kho                
                // if($row_op['useWarehouse']==1){
                if((int)$ims->setting['product']['use_ware_house'] == 1 && $row_op['is_OrderOutStock']==0){
                    // Nếu số lượng tồn > số lượng đặt
                    if($row_op['Quantity'] >= $row['quantity']){
                        // $row_op['Quantity'] = $row_op['Quantity'] - $row['quantity'];
                        // $SQL_UPDATE = "UPDATE product_option SET Quantity = ".$row_op['Quantity']." WHERE lang = '".$ims->conf['lang_cur']."' AND is_show = 1 AND ProductId = ".$row['item_id']." AND id=".$row['option_id']." ";
                        // $ims->db->query($SQL_UPDATE);
                        $output['ok'][] = 1;
                    }else{
                        // Nếu số lượng tồn < số lượng đặt
                        // if($row_op['is_OrderOutStock']==0){
                        $title = $row_pro['title'];
                        if(!empty($row_op['Option1']) && $row_op['Option1'] != "Default Title"){
                            $title .= ' / '.$row_op['Option1'];
                        }
                        if(!empty($row_op['option2'])){
                            $title .= ' / '.$row_op['option2'];
                        }
                        if(!empty($row_op['option3'])){
                            $title .= ' / '.$row_op['option3'];
                        }
                        $out_stock[$row['item_id'].'o'.$row['option_id']] = array(
                            'item_id' => $row_pro['item_id'],
                            'option_id' => $row_op['id'],
                            'title' => $title,
                            'picture' => !empty($row_op['Picture'])?$ims->func->get_src_mod($row_op['Picture'],80,80):(!empty($row_pro['picture'])?$ims->func->get_src_mod($row_pro['picture'],80,80):''),
                            'quantity' => $row_op['Quantity'],
                        );                        
                        $output['ok'][] = 0;
                        // }else{
                        //     // Cho phép đặt khi hết hàng
                        //     $output['ok'] = 1;
                        // }
                    }
                }
            }
            $out_stock = array_values($out_stock);
            $output['out_stock'] = $out_stock;
        }
        return $output;
    }

    function do_arr_gift_include($row, $combo, $check_old_order, $recommend_type, $deeplink_user_id){
        global $ims;
        $out = array(
            'arr' => '',
            'add_payment' => 0,
            'deeplink_total_include' => 0,
            'deeplink_total_include_old' => 0
        );        
        if(!empty($row['combo_info'])){ //combo_info trong session (chứa id sp hoặc gift mua kèm)            
            // $cb_info =  $ims->func->unserialize($row['combo_info']);
            $cb_info =  $row['combo_info'];
            $k_combo = array_keys($cb_info);
            $k_combo = str_replace('_id', '', $k_combo[0]);
            $v_combo = array_values($cb_info);
            $row[$k_combo] = $v_combo[0];
        }        
        if($combo){
            if($combo['type'] != 1){
                if(isset($row['gift']) && $row['gift'] != ''){
                    $arr_gift = $ims->db->load_item_arr('user_gift', 'is_show = 1 and lang = "'.$ims->conf['lang_cur'].'" and item_id IN ('.$row['gift'].') order by FIELD(item_id,"'.$row['gift'].'") desc', 'item_id, title, picture, product_id, price');
                    if($arr_gift){
                        $out['arr'] = array('gift' => $arr_gift);
                    }
                }
                if(isset($row['include']) && $row['include'] != ''){
                    $out['arr'] = array();
                    $arr_include = $ims->db->load_item_arr('product', 'is_show = 1 and lang = "'.$ims->conf['lang_cur'].'" and item_id IN('.$row['include'].') order by FIELD(item_id,"'.$row['include'].'") desc', 'item_id, title, picture, price, price_buy, group_id, group_nav');
                    if($arr_include){
                        foreach ($arr_include as $include){
                            $include['price_buy_combo'] = ($combo['value_type'] == 1) ? $include['price_buy']*((100 - $combo['value'])/100) : ($include['price_buy'] - $combo['value']);
                            if($include['price_buy_combo'] < 0){
                                $include['price_buy_combo'] = 0;
                            }
                            $out['arr']['include'][] = $include;
                            $out['add_payment'] += $include['price_buy_combo'];

                            // Tính hoa hồng tiếp thị liên kết trên từng sản phẩm
                            if ($recommend_type == 'deeplink' && $deeplink_user_id > 0){
                                if($include['group_id'] == 0){
                                    $percent_deeplink_old = $ims->setting['product']['percent_deeplink_default_old'];
                                    $percent_deeplink_new = $ims->setting['product']['percent_deeplink_default_new'];
                                }else{
                                    $group_nav = explode(',', $include['group_nav']);
                                    $group_id = $group_nav[0];
                                    $percent_deeplink_group = $ims->db->load_row('product_group', 'is_show = 1 and lang = "'.$ims->conf["lang_cur"].'" and group_id = '.$group_id, 'percent_deeplink_old, percent_deeplink_new');
                                    $percent_deeplink_old = ($percent_deeplink_group['percent_deeplink_old'] > 0) ? $percent_deeplink_group['percent_deeplink_old'] : $ims->setting['product']['percent_deeplink_default_old'];
                                    $percent_deeplink_new = ($percent_deeplink_group['percent_deeplink_new'] > 0) ? $percent_deeplink_group['percent_deeplink_new'] : $ims->setting['product']['percent_deeplink_default_new'];
                                }
                                if($check_old_order){
                                    $deeplink_item_tmp = ($include['price_buy_combo'] * $percent_deeplink_old/100);
                                    $out['deeplink_total_include'] += ($deeplink_item_tmp > $ims->setting['product']['amount_deeplink_default']) ? $ims->setting['product']['amount_deeplink_default'] : $deeplink_item_tmp;
                                }else{
                                    $deeplink_item_new_tmp = ($include['price_buy_combo'] * $percent_deeplink_new/100);
                                    $deeplink_item_old_tmp = ($include['price_buy_combo'] * $percent_deeplink_old/100);
                                    $out['deeplink_total_include'] += ($deeplink_item_new_tmp > $ims->setting['product']['amount_deeplink_default']) ? $ims->setting['product']['amount_deeplink_default'] : $deeplink_item_new_tmp;
                                    $out['deeplink_total_include_old'] += ($deeplink_item_old_tmp > $ims->setting['product']['amount_deeplink_default']) ? $ims->setting['product']['amount_deeplink_default'] : $deeplink_item_old_tmp;
                                }

                                $arr_deeplink_include[] = array(
                                    'item_id' => $include['item_id'],
                                    'picture' => $include['picture'],
                                    'price_buy' => $include['price_buy'],
                                    'price_buy_discounted' => $include['price_buy_combo'],
                                    'root_group' => ($include['group_id'] > 0) ? $group_id : 0,
                                    'percent_deeplink_group_old' => ($include['group_id'] > 0 && isset($percent_deeplink_group['percent_deeplink_old'])) ? $percent_deeplink_group['percent_deeplink_old'] : 0,
                                    'percent_deeplink_group_new' => ($include['group_id'] > 0 && isset($percent_deeplink_group['percent_deeplink_new'])) ? $percent_deeplink_group['percent_deeplink_new'] : 0
                                );
                            }
                        }
                    }
                }
            }
        }
        $out['arr_deeplink_include'] = (isset($arr_deeplink_include) && $arr_deeplink_include) ? $arr_deeplink_include : '';

        return $out;
    }


    function do_cart ($infoOrder = array(), $arr_pro = array(), $arr_op = array()){
        global $ims;

        $ims->temp_act = new XTemplate($ims->conf['rootpath']."temp/default/html/product/ordering.tpl");
        $ims->temp_act->assign('CONF', $ims->conf);
        $ims->temp_act->assign('LANG', $ims->lang);

        $order = $ims->db->load_row('product_order', 'order_code="'.$infoOrder['order_code'].'"');
        $data = $order;

        $arr_detail = $ims->db->load_row_arr('product_order_detail', 'order_id="'.$data['order_id'].'"');
        $arr_cart = array();
        foreach ($arr_detail as $key => $value) {
            $arr_cart[$value['detail_id']] = $value;                
        }

        $num_product = 0;
        if(is_array($arr_cart) && count($arr_cart) > 0){
            foreach($arr_cart as $cart_id => $row) {                
                $row_pro = $ims->func->if_isset($arr_pro[$row['type_id']], array());
                $row_op  = $ims->func->if_isset($arr_op[$row['option_id']], array());       
                $row['cart_id'] = $cart_id;
                $row['pic_w'] = 50;
                $row['pic_h'] = 50;
                $row['picture'] = (isset($row['picture'])) ? $row['picture'] : '';
                $row["picture"] = $ims->func->get_src_mod($row["picture"], $row['pic_w'], $row['pic_h'], 1, 0, array('fix_max' => 1));
                $row['price_buy'] = (isset($row['price_buy'])) ? $row['price_buy'] : 0;
                if(!empty($row['option1']) && $row['option1'] != "Default Title"){
                    $row['title'] .= ' / '.$row['option1'];
                }
                if(!empty($row['option2'])){
                    $row['title'] .= ' / '.$row['option2'];
                }
                if(!empty($row['option3'])){
                    $row['title'] .= ' / '.$row['option3'];
                }
                $row['quantity'] = (isset($row['quantity'])) ? $row['quantity'] : 0;
                // Danh sách quà tặng hoặc sp mua kèm combo
                $gift_include = $this->combo_gift_include_mail($row['arr_gift_include']);
                $row['gift_include'] = $gift_include['html'];
                $num_product += $gift_include['num_product'];
                $row['total'] = $row['quantity']*$row['price_buy'];

                /*$row['code_pic'] = (isset($row['code_pic']) && array_key_exists($row['code_pic'], $arr_code_pic)) ? $row['code_pic'] : 0;
                $code_pic = (isset($arr_code_pic[$row['code_pic']]['code_pic'])) ? '<div><span class="code_pic" style="background:'.$arr_code_pic[$row['code_pic']]['code_pic'].';">&nbsp;</span></div>' : '';
                $row['code_pic'] = (isset($arr_code_pic[$row['code_pic']]['title'])) ? $code_pic.$arr_code_pic[$row['code_pic']]['title'] : '';*/
                
                $row['price_buy'] = $ims->func->get_price_format_email($row['price_buy']);
                $row['total'] = $ims->func->get_price_format_email($row['total']);              
                $row['cart_td_attr'] = ' style="background:#ffffff;"';
                $ims->temp_act->assign('row', $row);
                $ims->temp_act->parse("table_cart_ordering_method_mail.row_item");
            }
        } else {
            $ims->temp_act->assign('row', array('mess' => $ims->lang['product']['no_have_item']));
            $ims->temp_act->parse("table_cart_ordering_method_mail.row_empty");
        }

        if($order['shipping_price'] == 0){
            $data['shipping_price_out'] = 'Miễn phí';
        }else{
            $data['shipping_price_out'] = $ims->func->get_price_format_email($order['shipping_price'], 0);
        }
        $ims->temp_act->assign('data', $data);
        $ims->temp_act->parse("table_cart_ordering_method_mail.shipping_price");

        if($order['method_price'] > 0 || $order['method_price'] < 0) {
            if($order['method_price'] > 0){
                $data['save_method'] = ' +'. $ims->func->get_price_format_email($order['method_price']);
            }elseif($order['method_price'] < 0){
                $data['save_method'] = $ims->func->get_price_format_email($order['method_price']);
            }
            $ims->temp_act->assign('data', $data);
            $ims->temp_act->parse("table_cart_ordering_method_mail.save_method");
        }

        if(isset($order['promotion_price']) && $order['promotion_price'] != 0 && $order['promotion_price'] > 0){
            $data['promotion_percent'] = $order['promotion_percent'].'%';
            $data['promotion_price']  = $order['promotion_price'];
            $data['promotion_price_out'] = '-'.$ims->func->get_price_format_email($order['promotion_price'], 0);
            $ims->temp_act->assign('data', $data);
            $ims->temp_act->parse("table_cart_ordering_method_mail.promotional_box_show");
        }

        // sử dụng điểm tích lũy
        if ($data['payment_wcoin'] > 0) {
            $data['wcoin_price_out'] = $ims->func->get_price_format_email($data['payment_wcoin2money'], 0);
            $ims->temp_act->assign('data', $data);
            $ims->temp_act->parse("table_cart_ordering_method_mail.wcoin_box_show");
        }

        $data['cart_total'] = $ims->func->get_price_format_email($data['total_order'], 0);
        $data['cart_payment'] = $ims->func->get_price_format_email($data['total_payment'], 0);
        $data['num_product'] = count($arr_cart) + $num_product;

        $ims->temp_act->assign('data', $data);
        $ims->temp_act->parse("table_cart_ordering_method_mail");
        return $ims->temp_act->text("table_cart_ordering_method_mail");
    }
    function combo_gift_include_mail($gift_include){
        global $ims;
        $out = array(
            'html' => '',
            'add_payment' => 0,
            'num_product' => 0
        );
        if($gift_include){
            $gift_include = $ims->func->unserialize($gift_include);
            foreach ($gift_include as $key => $value){
                $title = $ims->lang['product'][$key];
                foreach ($value as $row){
                    $row['price'] = (isset($row['price_buy_combo'])) ? $ims->func->get_price_format_email($row['price_buy_combo']) : '';
                    $out['add_payment'] += (isset($row['price_buy_combo'])) ? $row['price_buy_combo'] : 0;
                    $out['num_product'] += ($key == 'include') ? 1 : 0;
                    $ims->temp_act->assign('row', $row);
                    $ims->temp_act->parse("combo_gift_include.item");
                }
                $ims->temp_act->assign('title', $title);
                $ims->temp_act->reset("combo_gift_include");
                $ims->temp_act->parse("combo_gift_include");
                $out['html'] = $ims->temp_act->text("combo_gift_include");
            }            
        }
        return $out;
    }

    public function paymentCustom($method = array(), $order = array()){
        global $ims;      
        $this->setting('product');  
        $output = array();
        if (!empty($method['name_action'])) {
            // $method = $ims->db->load_item('order_method','lang="'.$ims->conf['lang_cur'].'" and is_show=1 and name_action="'.$method['name_action'].'"','arr_option');
            if($method['name_action'] == 'vnpay'){
                $vnpay = $ims->func->unserialize($method['arr_option']);
                
                $vnp_TmnCode    = $vnpay['TerminalId']; //Mã website tại VNPAY                 
                $vnp_HashSecret = $vnpay['SecretKey']; //Chuỗi bí mật
                $vnp_Url        = (!empty($method['is_prod']))?$method['link_api']:$method['link_sanbox'];
                $vnp_Returnurl  = $ims->site_func->get_link('product', $ims->setting['product']['vnpayipn_link']);
                if(!empty($method['is_prod'])){
                    $vnp_Returnurl  = $ims->site_func->get_link('product', $ims->setting['product']['ordering_complete_link']);
                }
                // $vnp_Url        = "http://vnpayment.vn/paymentv2/vpcpay.html";
                // $vnp_Returnurl  = $ims->conf['rooturl'].$ims->setting['product']['ordering_complete_link'];
                
                $vnp_TxnRef     = $order['order_code']; // Mã đơn hàng.
                $vnp_OrderInfo  = "Thanh toán cho đơn hàng #".$vnp_TxnRef;
                $vnp_OrderType  = "billpayment";
                $vnp_Amount     = (int)$order['total_payment'] * 100;
                $vnp_Locale     = "vn";
                $vnp_BankCode   = "";
                $vnp_IpAddr     = $_SERVER['REMOTE_ADDR'];
                $vnp_Bill_Address = !empty($ims->conf['address'])?$ims->conf['address']:'VN';
                $vnp_Bill_City = "Ho Chi Minh";
                $vnp_Bill_Country = "VN";
                $vnp_Inv_Phone = !empty($ims->conf['hotline'])?str_replace(' ','',$ims->conf['hotline']):0;
                $vnp_Inv_Email = !empty($ims->conf['email'])?$ims->conf['email']:"mail@gmail.com";
                $vnp_Inv_Customer = !empty($ims->conf['company'])?$ims->conf['company']:"Công ty";
                $vnp_Inv_Address = !empty($ims->conf['address'])?$ims->conf['address']:'VN';;
                $vnp_Inv_Company = !empty($ims->conf['company'])?$ims->conf['company']:"Công ty";
                $vnp_Inv_Taxcode = !empty($ims->conf['mst'])?str_replace(' ','',$ims->conf['mst']):0;
                $vnp_Inv_Type = "I";
                $startTime = date("YmdHis");
                
                $vnp_Bill_FirstName = "V";
                $vnp_Bill_LastName = "N";
                if (isset($order['o_full_name']) && trim($order['o_full_name']) != '') {
                    $name = explode(' ', $order['o_full_name']);
                    $vnp_Bill_FirstName = array_shift($name);
                    $vnp_Bill_LastName = array_pop($name);
                    $vnp_Bill_LastName = !empty($vnp_Bill_LastName)?$vnp_Bill_LastName:$vnp_Bill_FirstName;
                }
                
                $inputData = array(
                    "vnp_Version" => "2.1.0",
                    "vnp_TmnCode" => $vnp_TmnCode,
                    "vnp_Amount" => $vnp_Amount,
                    "vnp_Command" => "pay",
                    "vnp_CreateDate" => $startTime,
                    "vnp_CurrCode" => "VND",
                    "vnp_IpAddr" => $vnp_IpAddr,
                    "vnp_Locale" => $vnp_Locale,
                    "vnp_OrderInfo" => $vnp_OrderInfo,
                    "vnp_OrderType" => $vnp_OrderType,
                    "vnp_ReturnUrl" => $vnp_Returnurl,
                    "vnp_TxnRef" => $vnp_TxnRef,
                    "vnp_ExpireDate"=> date('YmdHis',strtotime('+15 minutes',strtotime($startTime))),
                    "vnp_Bill_Mobile"=> $order['o_phone'],
                    "vnp_Bill_Email"=> $ims->conf['email'],
                    "vnp_Bill_FirstName"=>$vnp_Bill_FirstName,
                    "vnp_Bill_LastName"=>$vnp_Bill_LastName,
                    "vnp_Bill_Address"=>$vnp_Bill_Address,
                    "vnp_Bill_City"=>$vnp_Bill_City,
                    "vnp_Bill_Country"=>$vnp_Bill_Country,
                    "vnp_Inv_Phone"=>$vnp_Inv_Phone,
                    "vnp_Inv_Email"=>$vnp_Inv_Email,
                    "vnp_Inv_Customer"=>$vnp_Inv_Customer,
                    "vnp_Inv_Address"=>$vnp_Inv_Address,
                    "vnp_Inv_Company"=>$vnp_Inv_Company,
                    "vnp_Inv_Taxcode"=>$vnp_Inv_Taxcode,
                    "vnp_Inv_Type"=>$vnp_Inv_Type
                );
                if (isset($vnp_BankCode) && $vnp_BankCode != "") {
                    $inputData['vnp_BankCode'] = $vnp_BankCode;
                }
                ksort($inputData);
                $query = "";
                $i = 0;
                $hashdata = "";
                foreach ($inputData as $key => $value) {
                    if ($i == 1) {
                        $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
                    } else {
                        $hashdata .= urlencode($key) . "=" . urlencode($value);
                        $i = 1;
                    }
                    $query .= urlencode($key) . "=" . urlencode($value) . '&';
                }
                $vnp_Url = $vnp_Url."?".$query;
                if (isset($vnp_HashSecret)) {
                    // $vnpSecureHash = hash('sha512', $vnp_HashSecret . $hashdata);
                    $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
                    $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
                }
                // Cập nhât order với token  $nl_result->token để sử dụng check hoàn thành sau này
                $ims->db->query("UPDATE product_order SET token='".$vnpSecureHash."' WHERE order_code='".$order['order_code']."' ");
                $output['type'] = "vnpay";
                $output['url'] = $vnp_Url;
                $output['order_code'] = $order['order_code'];
                $output['is_show'] = 0;
            }elseif($method['name_action'] == 'momo'){                
                $momo = $ims->func->unserialize($method['arr_option']);
                // $endpoint    = "https://payment.momo.vn/gw_payment/transactionProcessor";                
                $endpoint    = ((!empty($method['is_prod']))?$method['link_api']:$method['link_sanbox'])."/gw_payment/transactionProcessor";

                $partnerCode = $momo['PartnerCode'];
                $accessKey   = $momo['AccessKey'];
                $secretKey   = $momo['SecretKey'];
                $order_code  = $order['order_code'];
                $orderInfo   = "Thanh toán qua MoMo cho đơn hàng ".$order_code;
                $amount      = "".$order['total_payment']."";
                $orderId     = $order_code."";
                $returnUrl   = $this->rooturl.$ims->setting['product']['ordering_complete_link']; // Result
                $notifyurl   = $this->rooturl.$ims->setting['product']['momoipn_link']; //ipn_momo.php";

                $output['type'] = "momo";      
                $output['partnerCode'] = $partnerCode;
                $output['ios_scheme_id'] = strtolower($momo['PartnerCode']);
                $output['amount'] = (string)$order['total_payment'];
                $output['order_code'] = $order_code;
                // $output['endpoint'] = $endpoint;
                $output['is_show'] = 0;
                $ims->db->query("UPDATE product_order SET is_payment=1 WHERE order_code='".$order_code."' ");
            }
        }
        return $output;
    }
}   
?>