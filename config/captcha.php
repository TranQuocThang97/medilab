<?php

if (! defined('IN_ims')) {
  die('Access denied');
}

class Captcha
{	
  static public function Check($in_captcha)
  {
		$captcha = Captcha::Get();
		if($captcha == $in_captcha) {
			return 1;
		}
		return 0;
  }

  static public function Set()
  {
		$ranStr = md5(microtime()); // Lấy chuỗi rồi mã hóa md5
		$ranStr = substr($ranStr, 0, 6);    // Cắt chuỗi lấy 6 ký tự
		return Session::Set('captcha', $ranStr);
  }

  static public function Get()
  {
		return Session::Get('captcha', 'Error');
  }

  static public function pic()
  {		
		global $ims;
		
		$captcha = Captcha::Get ();
		if($captcha == 'Error') {
			$captcha = Captcha::Set ();
		}
		
		header("Content-Type: image/gif");
		//$im = @imagecreate(100, 40) or die("Cannot Initialize new GD image stream");
		
		$bg_num = rand(1,4);
		$bg = $ims->conf['rootpath_web'].'config'.DS.'captcha_font'.DS.'bg_'.$bg_num.'.png';

		$im = @imagecreatefrompng($bg);
		
		$font_num = rand(1,3);
		$font = $ims->conf['rootpath_web'].'config'.DS.'captcha_font'.DS.'font_'.$font_num.'.ttf';
		$font = $ims->conf['rootpath_web'].'config'.DS.'captcha_font'.DS.'tahoma.ttf';
		
		$background_color = imagecolorallocate($im, 255, 255, 255);
		
		if($bg_num == 2) {
			$text_color = imagecolorallocate($im, 0, 0, 0);
			// $text_color = imagecolorallocate($im, 255, 255, 255);
		} else {
			$text_color = imagecolorallocate($im, 0, 0, 0);
		}
		
		//imagestring($im, 16, 2, 2,  $captcha, $text_color);
		$font_size = 18;
		if($font_num == 2) {
			$font_size = 21;
		} elseif ($font_num == 3) {
			$font_size = 17;
		}
		imagettftext($im, $font_size, 0, 10, 30, $text_color, $font, $captcha);
		imagepng($im);
		imagedestroy($im);
  }
}

?>