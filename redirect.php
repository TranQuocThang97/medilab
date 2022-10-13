<?php
define('IN_ims', 1);
define('PATH_ROOT', dirname(__FILE__));
define('DS', DIRECTORY_SEPARATOR);
class ims {
}
$ims = new ims;
require_once("dbcon.php");
$ims->conf = $conf;
require_once("config/db.php");
$DB = new DB($conf);
$ims->db = $DB;
require_once("config/func.php");
$ims->func = new Func;
$ims->conf["meta_image"] = $ims->func->get_src_mod($ims->conf["picture_share"]);
// $ims->load_data = new Data;
// $ims->load_data->data_lang();
// $ims->conf['lang_cur'] = $ims->data['lang_default'];

$ims->setting['home'] = array();
$result = $ims->db->query("select * from home_setting ");
while ($row = $ims->db->fetch_row($result)) {
    $ims->setting['home_' . $row['lang']] = $row;
    if ($row['lang'] == 'vi') {
        $ims->setting['home'][$row['setting_key']] = $row['setting_value'];
    }
}
$ims->conf['canonical'] = $ims->conf['rooturl'];
$ims->conf['meta_title'] = $ims->setting['home']['home_meta_title'];
$ims->conf['meta_desc'] = $ims->setting['home']['home_meta_desc'];
$ims->conf['meta_key'] = $ims->setting['home']['home_meta_key'];
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{CONF.lang_cur}">
<head>    
    <title><?php echo $ims->conf['meta_title'];?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="content-language" content="vi" />
    <meta name="robots" content="noodp,index,follow" />
    <meta name="revisit-after" content="1 days" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <link href="<?php echo $ims->conf['rooturl']; ?>favicon.ico" rel="shortcut icon" type="image/x-icon" />
    <link href="<?php echo $ims->conf['rooturl']; ?>favicon.ico" rel="apple-touch-icon" />
    <link href="<?php echo $ims->conf['rooturl']; ?>favicon.ico" rel="apple-touch-icon-precomposed" />

    <meta name="description" content="<?php echo $ims->conf['meta_desc']; ?>" />
    <meta name="keywords" itemprop="keywords" content="<?php echo $ims->conf['meta_key']; ?>" />
    <link rel="canonical" href="<?php echo $ims->conf['canonical']; ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="<?php echo $ims->conf['meta_title']; ?>" />
    <meta property="og:description" content="<?php echo $ims->conf['meta_desc']; ?>" />
    <meta property="og:url" content="<?php echo $ims->conf['canonical']; ?>" />
    <meta property="og:image" itemprop="thumbnailUrl" content="<?php echo $ims->conf['meta_image']; ?>" />    
    <meta property="og:image:alt" itemprop="thumbnailUrl" content="<?php echo $ims->conf['meta_image']; ?>" />    
</head>
<body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js" integrity="sha512-DUC8yqWf7ez3JD1jszxCWSVB0DMP78eOyBpMa5aJki1bIRARykviOuImIczkxlj1KhVSyS16w2FSQetkD4UU2w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script type="text/javascript">
	function getUrlParameter(sParam) {
	    var sPageURL = window.location.search.substring(1),
	        sURLVariables = sPageURL.split('&'),
	        sParameterName,
	        i;

	    for (i = 0; i < sURLVariables.length; i++) {
	        sParameterName = sURLVariables[i].split('=');

	        if (sParameterName[0] === sParam) {
	            return typeof sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
	        }
	    }
	    return false;
	};
	function base64_decode(str) {
        str = str.substring(20);
        str = window.atob(str);
        return str;
    }
	function getMobileOperatingSystem() {
	  	var userAgent = navigator.userAgent || navigator.vendor || window.opera;

	      // Windows Phone must come first because its UA also contains "Android"
	    if (/windows phone/i.test(userAgent)) {
	        return "Windows Phone";
	    }

	    if (/android/i.test(userAgent)) {
	        return "Android";
	    }

	    // iOS detection from: http://stackoverflow.com/a/9039885/177710
	    if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {
	        return "iOS";
	    }

	    return "unknown";
	}
	var deeplink_code = '';
	var pathname = window.location.pathname; // Returns path only (/path/example.html)
	var pathname = pathname.replace("/redirect/", "");
	var pathname = pathname.split("/");
		deeplink_code = pathname.at(-1);
	if (pathname[0] == 'signup') {
		pathname[1] = base64_decode(pathname[1]);
	}
	var url = 'totvatot://' + pathname[0] + '/' + pathname[1]	

	if (typeof pathname[2] !== 'undefined'){
		url = url + '/' + pathname[2];
	}
	if (typeof pathname[3] !== 'undefined'){
		url = url + '/' + pathname[3];
	}
	window.location = url;

	var platform = getMobileOperatingSystem();	
	setTimeout(function () {
		if (platform == 'Android') {
	    	window.location.href = 'https://play.google.com/store/apps/details?id=com.imsvietnamese.android.tanthanhson.v4';
		} else if (platform == 'iOS') {
	    	window.location.href = 'https://apps.apple.com/vn/app/totvatot/id1581959116';
		} else{
	    	window.location.href = 'https://totvatot.com/'+deeplink_code;
		}
	}, 500);

	</script>
</body>
</html>