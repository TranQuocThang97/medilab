<?php
	$conf = array();
	// $conf['URL_API_GHN'] = 'https://dev-online-gateway.ghn.vn/';
	// $conf['URL_API_GHTK'] = 'https://dev.ghtk.vn/';
	$conf['URL_API_GHN']   = 'https://online-gateway.ghn.vn/';
	$conf['URL_API_GHTK']  = 'https://services.giaohangtietkiem.vn/';
	$conf['host'] ='localhost';
	$conf['dbuser'] = 'coder18';
	$conf['dbpass'] = 'BlyoRWSs2l';
	$conf['dbname'] = 'coder18_medilab';
	$conf['rooturl'] = (isset($_SERVER["HTTPS"])?'https':'http').'://'.$_SERVER['HTTP_HOST'].'/coder18/medilab/';
	$conf['rootpath'] = $_SERVER['DOCUMENT_ROOT'].'/coder18/medilab/';
	$conf['rooturl_web'] = $conf['rooturl'];
	$conf['rootpath_web'] = $conf['rootpath'];
    $conf['construction'] = 1;
    $conf['refresh'] = 0;
?>