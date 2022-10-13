<?php
if (!defined('IN_ims')) { die('Access denied'); }
class Thumb {
    /**
        * vn_str_filter
        * @param type $str
        * @return type
    */
    function vn_str_filter($str) {
        $unicode = array(
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
            'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'D' => 'Đ',
            'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
            'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
        );
        foreach ($unicode as $nonUnicode => $uni) {
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }
        return $str;
    }
    /**
        * fix_file_name
        * @global type $ims
        * @param type $str
        * @return type
    */
    function fix_file_name($str) {
        global $ims;
        $str = $this->vn_str_filter($str);
        $str = preg_replace('/[^a-zA-Z0-9\.-_ ]/', '-', $str);
        $str = preg_replace('/[_ ]/', '_', $str);
        //    while (strlen(strstr($str, "--")) > 0) {
        //        $str = str_replace('--', '-', $str);
        //    }
        //    while (strlen(strstr($str, "..")) > 0) {
        //        $str = str_replace('..', '.', $str);
        //    }
        $str = str_replace(array('(.)', '()', '(.', '.)', '(', ')'), '', '(' . $str . ')');


        // $str = strtolower($str);
        return $str;
    }

    /**
        * rmkdir
        * @global type $ims
        * @param type $dir
        * @param type $chmod
        * @param type $path_folder
        * @return boolean
    */
    function rmkdir($dir = "", $chmod = 0777, $path_folder = "uploads") {
        global $ims;
        $chmod = ($chmod == 'auto') ? 0777 : $chmod;
        $arr_allow = array("uploads", "thumbs", "thumbs");
        $path_folder = (in_array($path_folder, $arr_allow)) ? $path_folder : 'uploads';
        $path = $ims->conf["rootpath_web"] . $path_folder;
        $path = rtrim(preg_replace(array("/\\\\/", "/\/{2,}/"), "/", $path), "/");
        if (is_dir($path . '/' . $dir) && file_exists($path . '/' . $dir)) {
            return true;
        }
        $path_thumbs = $path . '/' . $dir;
        $path_thumbs = rtrim(preg_replace(array("/\\\\/", "/\/{2,}/"), "/", $path_thumbs), "/");
        $oldumask = umask(0);
        if ($path && !file_exists($path)) {
            mkdir($path, $chmod, true); // or even 01777 so you get the sticky bit set 
        }
        if ($path_thumbs && !file_exists($path_thumbs)) {
            mkdir($path_thumbs, $chmod, true);
            //mkdir($path_thumbs, $chmod, true) or die("$path_thumbs cannot be found"); // or even 01777 so you get the sticky bit set 
        }
        umask($oldumask);
        return true;
    }
    /**
     * watermark
     * @global type $ims
     * @param type $imgfile
     * @param type $watermark
     * @param type $pos
     * @return boolean
     */
    function watermark($imgfile, $watermark, $pos = 'rb') {
        global $ims;
        $imgcreate = function($imgfile, $fext) {
            $im = '';
            // Load the stamp and the photo to apply the watermark to
            switch ($fext) {
                case 'image/pjpeg':
                case 'image/jpeg':
                case 'image/jpg':
                    if (!function_exists('imagecreatefromjpeg')) {
                        die('No create from JPEG support');
                    } else {
                        $im = @imagecreatefromjpeg($imgfile);
                    }
                    break;
                case 'image/png':
                    if (!function_exists('imagecreatefrompng')) {
                        die("No create from PNG support");
                    } else {
                        $im = @imagecreatefrompng($imgfile);
                    }
                    break;
                case 'image/gif':
                    if (!function_exists('imagecreatefromgif')) {
                        die("No create from GIF support");
                    } else {
                        $im = @imagecreatefromgif($imgfile);
                    }
                    break;
            }
            return $im;
        };
        $imgfile = str_replace($ims->conf['rooturl'], $ims->conf['rootpath'], $imgfile);
        $info = @getimagesize($imgfile);
        $mime = $info[2];
        $fext = ($mime == 1 ? 'image/gif' : ($mime == 2 ? 'image/jpeg' : ($mime == 3 ? 'image/png' : ($mimi == 18 ? 'image/webp' : NULL))));
        $im = $imgcreate($imgfile, $fext);
        $watermark = str_replace($ims->conf['rooturl'], $ims->conf['rootpath'], $watermark);
        $watermark_info = @getimagesize($watermark);
        $watermark_mime = $watermark_info[2];
        $watermark_fext = ($watermark_mime == 1 ? 'image/gif' : ($watermark_mime == 2 ? 'image/jpeg' : ($watermark_mime == 3 ? 'image/png' : ($mimi == 18 ? 'image/webp' : NULL))));
        $stamp = $imgcreate($watermark, $watermark_fext);
        // Set the margins for the stamp and get the height/width of the stamp image
        $margex = 10;
        $margey = 10;
        $sx = imagesx($stamp);
        $sy = imagesy($stamp);
        //--------------
        $imw = imagesx($im);
        $imh = imagesy($im);
        //--------------
        switch ($pos) {
            case 'lt':
                $watermark_posx = $margex;
                $watermark_posy = $margey;
                break;
            case 'lc':
                $watermark_posx = $margex;
                $watermark_posy = floor($imh / 2) - floor($sy / 2);
                break;
            case 'lb':
                $watermark_posx = $margex;
                $watermark_posy = $imh - $sy - $margey;
                break;
            case 'rt':
                $watermark_posx = $imw - $sx - $margex;
                $watermark_posy = $margey;
                break;
            case 'rc':
                $watermark_posx = $imw - $sx - $margex;
                $watermark_posy = floor($imh / 2) - floor($sy / 2);
                break;
            case 'rb':
                $watermark_posx = $imw - $sx - $margex;
                $watermark_posy = $imh - $sy - $margey;
                break;
            case 'ct':
                $watermark_posx = floor($imw / 2) - floor($sx / 2);
                $watermark_posy = $margey;
                break;
            case 'cc':
                $watermark_posx = floor($imw / 2) - floor($sx / 2);
                $watermark_posy = floor($imh / 2) - floor($sy / 2);
                break;
            case 'cb':
                $watermark_posx = floor($imw / 2) - floor($sx / 2);
                $watermark_posy = $imh - $sy - $margey;
                break;
            default :
                $watermark_posx = $imw - $sx - $margex;
                $watermark_posy = $imh - $sy - $margey;
                break;
        }
        if ($sx > ($imw / 2)) {
            $sx = floor($imw / 2);
            $sy = floor($sx * $imh / $imw);
        }
//        if($sy > ($imh / 2)) {
//            $sy = floor($imh / 2);
//            $sx = floor($sy * $imw / $imh);
//        }
        //------------
        // Copy the stamp image onto our photo using the margin offsets and the photo 
        // width to calculate positioning of the stamp. 
        imagecopy($im, $stamp, $watermark_posx, $watermark_posy, 0, 0, $sx, $sy);
        // Output and free memory
        //header('Content-type: image/png');
        //imagepng($im);
        //imagepng($im, 'photo_img.png');
        $thumbfile = str_replace($ims->conf['rooturl'], $ims->conf['rootpath'], $imgfile);
        @touch($thumbfile);
        switch ($fext) {
            case 'image/pjpeg':
            case 'image/jpeg':
            case 'image/jpg':
                @imagejpeg($im, $thumbfile, 100);
                break;
            case 'image/png':
                @imagepng($im, $thumbfile);
                break;
            case 'image/gif':
                @imagegif($im, $thumbfile, 100);
                break;
        }
        // Finally, we destroy the images in memory.
        @imagedestroy($im);
        return true;
    }


    /**
        * thumbs
        * @param type $imgfile
        * @param type $thumbfile
        * @param type $maxWidth
        * @param type $maxHeight
        * @param type $crop
        * @param type $arr_more
    */
    function thumbs($arr_in = array()) {
        global $ims;

        $imgfile   = (isset($arr_in['src'])) ? $arr_in['src'] : '';
        $thumbfile = (isset($arr_in['srcthumb'])) ? $arr_in['srcthumb'] : '';
        $maxWidth  = (isset($arr_in['w'])) ? $arr_in['w'] : '';
        $maxHeight = (isset($arr_in['h'])) ? $arr_in['h'] : '';
        $crop      = (isset($arr_in['zc']) && $arr_in['zc'] == 'c') ? $arr_in['zc'] : '';
        $zt        = (isset($arr_in['zt'])) ? $arr_in['zt'] : 'fma';
        if ($maxHeight == "") {
            $maxHeight = $maxWidth;
        }
        $folder_thumbs = substr($thumbfile, 0, strrpos($thumbfile, "/"));
        $folder_thumbs = str_replace($ims->conf['rootpath_web'].'thumbs/', '', $folder_thumbs);
        if ($folder_thumbs) {
            $this->rmkdir($folder_thumbs, 0777, "thumbs");
        }
        // echo $folder_thumbs; die;
        $info = @getimagesize($imgfile);
        $mime = $info[2];
        $fext = ($mime == 1 ? 'image/gif' : ($mime == 2 ? 'image/jpeg' : ($mime == 3 ? 'image/png' : ($mimi == 18 ? 'image/webp' : NULL))));

        switch ($fext) {
            case 'image/pjpeg':
            case 'image/jpeg':
            case 'image/jpg':
                if (!function_exists('imagecreatefromjpeg')) {
                    die('No create from JPEG support');
                } else {
                    $img['src'] = @imagecreatefromjpeg($imgfile);
                }
                break;
            case 'image/png':
                if (!function_exists('imagecreatefrompng')) {
                    die("No create from PNG support");
                } else {
                    $img['src'] = @imagecreatefrompng($imgfile);
                }
                break;
            case 'image/webp':
                if (!function_exists('imagecreatefromwebp')) {
                    die("No create from WEBP support");
                } else {
                    $img['src'] = @imagecreatefromwebp($imgfile);
                }
                break;
            case 'image/gif':
                if (!function_exists('imagecreatefromgif')) {
                    die("No create from GIF support");
                } else {
                    $img['src'] = @imagecreatefromgif($imgfile);
                }
                break;
        }

        // echo '<pre>';
        // print_r($img);
        // echo '</pre>';
        // die;

        $img['old_w'] = @imagesx($img['src']);
        $img['old_h'] = @imagesy($img['src']);
        if ($crop == 'c') {
            // Ratio cropping
            $offsetX = 0;
            $offsetY = 0;
            $new_w = $maxWidth;
            $new_h = $maxHeight;
            $cropRatio = array($maxWidth, $maxHeight);
            if (count($cropRatio) == 2) {
                $ratioComputed = $img['old_w'] / $img['old_h'];
                $cropRatioComputed = (float) $cropRatio[0] / (float) $cropRatio[1];
                $ratio = max($maxWidth / $img['old_w'], $maxHeight / $img['old_h']);
                $img_tmp = $img;
                if ($ratioComputed < $cropRatioComputed) { // Image is too tall so we will crop the top and bottom
                    //$img['old_w'] = $img['old_w'];
                    $img['old_h'] = $img['old_w'] / $cropRatioComputed;
                    $offsetY = ($img_tmp['old_h'] - $maxHeight / $ratio) / 2;
                } else if ($ratioComputed > $cropRatioComputed) { // Image is too wide so we will crop off the left and right sides		
                    //$img['old_h'] = $img['old_h'];
                    $img['old_w'] = $img['old_h'] * $cropRatioComputed;
                    $offsetX = ($img_tmp['old_w'] - $maxWidth / $ratio) / 2;
                }
            }
        } else {
            $new_h = $img['old_h'];
            $new_w = $img['old_w'];
            $offsetX = 0;
            $offsetY = 0;
            $tl_old = $img['old_w'] / $img['old_h'];
            $tl_new = 1;
            if ($maxHeight != 'auto') {
                $tl_new = $maxWidth / $maxHeight;
            }
            if ($zt === 'fw') {
                $new_w = $maxWidth;
                $new_h = ($maxWidth / $img['old_w']) * $img['old_h'];
            } elseif ($zt === 'fh') {
                $new_h = $maxHeight;
                $new_w = ($maxHeight / $img['old_h']) * $img['old_w'];
            } elseif ($zt === 'fmi') {
                if ($img['old_w'] > $img['old_h']) {
                    $new_h = $maxHeight;
                    $new_w = ($maxHeight / $img['old_h']) * $img['old_w'];
                    if ($new_w < $maxWidth) {
                        $new_w = $maxWidth;
                        $new_h = ($maxWidth / $img['old_w']) * $img['old_h'];
                    }
                } else {
                    $new_w = $maxWidth;
                    $new_h = ($maxWidth / $img['old_w']) * $img['old_h'];
                    if ($new_h < $maxHeight) {
                        $new_h = $maxHeight;
                        $new_w = ($maxHeight / $img['old_h']) * $img['old_w'];
                    }
                }
            } elseif ($zt === 'zma') {
                if ($tl_new > $tl_old) {
                    $new_h = $maxHeight;
                    $new_w = ($maxHeight / $img['old_h']) * $img['old_w'];
                } else {
                    $new_w = $maxWidth;
                    $new_h = ($maxWidth / $img['old_w']) * $img['old_h'];
                }
            } else {
                if ($img['old_w'] > $maxWidth) {
                    $new_w = $maxWidth;
                    $new_h = ($maxWidth / $img['old_w']) * $img['old_h'];
                }
                if ($new_h > $maxHeight && $maxHeight != "auto") {
                    $new_h = $maxHeight;
                    $new_w = ($new_h / $img['old_h']) * $img['old_w'];
                }
            }
        }


        $img['des'] = @imagecreatetruecolor($new_w, $new_h);
        if ($fext == "image/png") {
            @imagealphablending($img['des'], false);
            @imagesavealpha($img['des'], true);
            $transparent = imagecolorallocatealpha($img['des'], 255, 255, 255, 127);
            imagefilledrectangle($img['des'], 0, 0, $new_w, $new_h, $transparent);
        } else {
            $white = @imagecolorallocate($img['des'], 255, 255, 255);
            @imagefill($img['des'], 1, 1, $white);
        }
        

        @imagecopyresampled($img['des'], $img['src'], 0, 0, $offsetX, $offsetY, $new_w, $new_h, $img['old_w'], $img['old_h']);
        //print "path = ".$thumbfile."<br>";	
        @touch($thumbfile);
        switch ($fext) {
            case 'image/pjpeg':
            case 'image/jpeg':
            case 'image/jpg':
                imagewebp($img['des'], $arr_in['srcthumb'], 80);
                @imagedestroy($img['des']);
                return "webp";

                @imagejpeg($img['des'], $thumbfile, 95);
                break;
            case 'image/png':
                imagewebp($img['des'], $arr_in['srcthumb']);
                @imagedestroy($img['des']);
                return "webp";

                @imagepng($img['des'], $thumbfile);
                break;
            case 'image/webp':                
                return "webp";
            case 'image/gif':
                //@imagegif($img['des'], $thumbfile, 90);
                @imagegif($img['des'], $thumbfile, 95);
                break;
        }
        // Finally, we destroy the images in memory.
        @imagedestroy($img['des']);
    }


    /**
        * detach_src_mod
        * @global type $ims
        * @param type $picture
        * @return string
    */
    function detach_src_mod($arr_in = array()) {
        global $ims;

        $strrand = '_VEHdbsdgewrtgv35tgrd56_';
        $arr_type = array('gif', 'png', 'jpg', 'jpeg', 'pjpeg', 'svg', 'webp');
        $filename = isset($arr_in['name']) ? trim($arr_in['name']) : '';
        $filename = $this->fix_file_name($filename);
        $type = isset($arr_in['type']) ? trim($arr_in['type']) : 'jpg';
        $type = strtolower($type);
        $picture = $filename . '.' . $type;
        if (!in_array($type, $arr_type) || !$filename) {
            $picture = 'thumbs/nophoto/nophoto.jpg';
        }
        $tmp = explode('/', $picture);
        $fdroot = $tmp[0];
        $fdmod = (isset($tmp[1]) ? $this->fix_file_name($tmp[1]) : '');
        if ($fdroot === 'thumbs') {
            $pictureup = str_replace($strrand . $fdroot, 'uploads', $strrand . $picture);
            $is_crop = false;
            $is_w = 0;
            $is_h = 0;
            $dir = substr($pictureup, 0, strrpos($pictureup, "/"));
            $pic_name = substr($pictureup, strrpos($pictureup, "/") + 1);
            $thumbGet = array();
            if (substr($pic_name, 0, 1) === '[') {
                $tmp = explode(']', $pic_name);
                if ($tmp > 1) {
                    $pic_name = substr($pic_name, strlen($tmp[0]) + 1);
                    $tmp = substr($tmp[0], 1);
                    $tmp = explode('-', $tmp);
                    $tmp1 = explode('x', $tmp[0]);
                    if (isset($tmp1[0]) && $tmp1[0] > 0) {
                        $is_w = (int) $tmp1[0];
                    }
                    if (isset($tmp1[1]) && $tmp1[1] > 0) {
                        $is_h = (int) $tmp1[1];
                    }
                    if (in_array('cr', $tmp)) {
                        $is_crop = true;
                    } elseif (in_array('fw', $tmp)) {
                        $is_h = 0;
                        $thumbGet['zt'] = 'fw';
                    } elseif (in_array('fh', $tmp)) {
                        $is_w = 0;
                        $thumbGet['zt'] = 'fh';
                    } elseif (in_array('fmi', $tmp)) {
                        $thumbGet['zt'] = 'fmi';
                    } elseif (in_array('fma', $tmp)) {
                        $thumbGet['zt'] = 'fma';
                    } elseif (in_array('zma', $tmp)) {
                        $thumbGet['zt'] = 'zma';
                    }
                }
            }
           
            if (strpos($pic_name, '__cv') !== false) {
                $pic_name = str_replace('__cv.'.$type, '', $pic_name);
            }

            $thumbGet['src'] = $ims->conf['rootpath_web'] . $dir . '/' . $pic_name;
            $thumbGet['srcthumb'] = $ims->conf['rootpath_web'] . $picture;
            $thumbGet['f'] = (in_array($type, array('png', 'gif')) ? $type : 'jpg');

            if (!file_exists($thumbGet['src'])) {
                $thumbGet['src'] = $ims->conf['rootpath_web'] . 'uploads/nophoto/nophoto.jpg';
                $thumbGet['f'] = 'jpg';
            }
            $thumbGet['q'] = 95;
            $thumbGet['sx'] = 0;
            $thumbGet['sy'] = 0;
            $thumbGet['sw'] = 1;
            $thumbGet['sh'] = 1;
            $thumbGet['bg'] = 'FFFFFF';
            $thumbGet['bc'] = '000000';
            if ($is_w > 0) {
                $thumbGet['w'] = $is_w;
            }
            if ($is_h > 0) {
                $thumbGet['h'] = $is_h;
            }
            if ($is_crop === true) {
                $thumbGet['zc'] = 'c';
            }
            //$arr_fdmod_watermark = array('product');
            //if($fdmod && in_array($fdmod, $arr_fdmod_watermark)) {
            if ($fdmod) {
                $sessionkey = md5($ims->conf['rooturl_web']);
                if (!isset($_SESSION[$sessionkey . 'thumbsconfig'])) {
                    require_once("db.php");
                    $ims->db = new DB($ims->conf);
                    $config = array();
                    $result = $ims->db->query("select * from sysoptions");
                    while ($conf = $ims->db->fetch_row($result)) {
                        $config[$conf['option_key']] = $conf['option_value'];
                    };
                    $_SESSION[$sessionkey . 'thumbsconfig'] = $config;
                }
                $_SESSION[$sessionkey . 'thumbsconfig'] = (isset($_SESSION[$sessionkey . 'thumbsconfig'])) ? $_SESSION[$sessionkey . 'thumbsconfig'] : array();
                $watermark_for = isset($_SESSION[$sessionkey . 'thumbsconfig']['watermark_for']) ? $_SESSION[$sessionkey . 'thumbsconfig']['watermark_for'] : '';
                $arr_fdmod_watermark = ($watermark_for) ? explode(',', $watermark_for) : array();
                if (in_array($fdmod, $arr_fdmod_watermark) && isset($_SESSION[$sessionkey . 'thumbsconfig']['watermark_picture'])) {
                    $watermark = $_SESSION[$sessionkey . 'thumbsconfig']['watermark_picture'];
                    if ($is_w < 500 && isset($_SESSION[$sessionkey . 'thumbsconfig']['watermark_small'])) {
                        $watermark = $_SESSION[$sessionkey . 'thumbsconfig']['watermark_small'];
                    }
                    $watermark_pos = isset($_SESSION[$sessionkey . 'thumbsconfig']['watermark_pos']) ? $_SESSION[$sessionkey . 'thumbsconfig']['watermark_pos'] : '*';
                    $watermark_pos = str_replace('_c', '', '_' . $watermark_pos);
                    $watermark_pos = strtoupper($watermark_pos);
                    $watermark = $ims->conf['rootpath_web'] . 'uploads/' . $watermark;
                    $watermark_check_skip_key = md5($watermark);
                    if (isset($_SESSION[$sessionkey . 'thumbsconfig'][$watermark_check_skip_key]) || file_exists($watermark)) {
                        $_SESSION[$sessionkey . 'thumbsconfig'][$watermark_check_skip_key] = true;
                    }
                    if (isset($_SESSION[$sessionkey . 'thumbsconfig'][$watermark_check_skip_key]) && $_SESSION[$sessionkey . 'thumbsconfig'][$watermark_check_skip_key] = true) {
                        $thumbGet['fltr'][] = 'wmi|' . $watermark . '|' . $watermark_pos . '|100';
                    }
                }
            }


            $this->thumbs($thumbGet);
            header("Content-Type: image/".$type);
            if(file_exists($thumbGet['srcthumb'])){
                echo file_get_contents($thumbGet['srcthumb']); 
                exit();
            }
        }
        return true;
    }
    // End class
}
?>