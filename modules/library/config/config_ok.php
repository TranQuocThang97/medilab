<?php
//session_start();
mb_internal_encoding('UTF-8');

//include('../../dbcon.php');

//------------------------------------------------------------------------------
// DON'T COPY THIS VARIABLES IN FOLDERS config.php FILES
//------------------------------------------------------------------------------

//**********************
//Path configuration
//**********************
// In this configuration the folder tree is
// root
//    |- source <- upload folder
//    |- thumbs <- thumbnail folder [must have write permission (755)]
//    |- filemanager
//    |- js
//    |   |- tinymce
//    |   |   |- plugins
//    |   |   |   |- responsivefilemanager
//    |   |   |   |   |- plugin.min.js

//print_arr($_SERVER);
//print_arr($ims->conf);

if($ims->site_func->checkUserLogin() != 1) {
  die('Access denied');
}
if(!isset($ims->data['user_cur']["username"]) || $ims->data['user_cur']["username"] == '') {
	die('Access denied');
}

if(!isset($ims->data['user_cur']["folder_upload"]) || $ims->data['user_cur']["folder_upload"] == '') {
	die('Access denied');
} else {
	$folder_conf = 'user/'.$ims->func->fix_name_action($ims->data['user_cur']["folder_upload"]);
}

/*if(isset($ims->conf['root_mod']) && $ims->conf['root_mod'] == 'shopad') {
		if(isset($ims->data['shopad_cur']) && $ims->data['shopad_cur']["folder_upload"]) {
		$folder_conf = 'shop/'.$ims->func->fix_name_action($ims->data['shopad_cur']["folder_upload"]);
	}
}*/

//$folder_conf = 'user/'.md5('z1d5gj96kg'.$ims->data['user_cur']["username"]);
//$ims->conf["folder_up"] = date('Y_m');
if(isset($ims->conf["folder_up"]) && !empty($ims->get['folder_up'])){
	$folder_conf .= '/'.$ims->conf["folder_up"];
}
$folder_conf .= '/';

if(isset($ims->get['fldr']) && !empty($ims->get['fldr'])) {
	if($ims->func->rmkdir($folder_conf.$ims->get['fldr'].'/')){
	}else{
		die('Không thể tạo thư mục');
	}
} else {
	if($ims->func->rmkdir($folder_conf)){
	}else{
		die('Không thể tạo thư mục');
	}
}
if($ims->func->rmkdir($folder_conf, 'auto', 'thumbs')){
}else{
	die('Không thể tạo thư mục thumbs');
}

$base_url=$ims->conf["rooturl"];  // DON'T TOUCH (base url (only domain) of site (without final /)).
$upload_dir = 'uploads/'.$folder_conf; // path from base_url to base of upload folder (with start and final /)
$current_path = $ims->conf['rootpath'].'uploads/'.$folder_conf; // relative path from filemanager folder to upload folder (with final /)
$current_thumbs_size = $ims->conf['rootpath'].'thumbs_size/'.$folder_conf;
$current_src = $ims->conf['rooturl'].'uploads/'.$folder_conf;
//thumbs folder can't put inside upload folder
$thumbs_base_path = 'thumbs/'.$folder_conf; // relative path from filemanager folder to thumbs folder (with final /)
$thumbs_base_src = $ims->conf['rooturl'].'thumbs/'.$folder_conf;

//--------------------------------------------------------------------------------------------------------
// YOU CAN COPY AND CHANGE THESE VARIABLES INTO FOLDERS config.php FILES TO CUSTOMIZE EACH FOLDER OPTIONS
//--------------------------------------------------------------------------------------------------------

$MaxSizeUpload=10; //Mb

$default_language="vi"; //default language file name
$icon_theme="ico"; //ico or ico_dark you can cusatomize just putting a folder inside filemanager/img
$show_folder_size=true; //Show or not show folder size in list view feature in filemanager (is possible, if there is a large folder, to greatly increase the calculations)
$show_sorting_bar=true; //Show or not show sorting feature in filemanager
$loading_bar=true; //Show or not show loading bar
$transliteration=false; //active or deactive the transliteration (mean convert all strange characters in A..Za..z0..9 characters)

//*******************************************
//Images limit and resizing configuration
//*******************************************

// set maximum pixel width and/or maximum pixel height for all images
// If you set a maximum width or height, oversized images are converted to those limits. Images smaller than the limit(s) are unaffected
// if you don't need a limit set both to 0
$image_max_width=800;
$image_max_height=0;

//Automatic resizing //
// If you set $image_resizing to true the script converts all uploaded images exactly to image_resizing_width x image_resizing_height dimension
// If you set width or height to 0 the script automatically calculates the other dimension
// Is possible that if you upload very big images the script not work to overcome this increase the php configuration of memory and time limit
$image_resizing=true;
$image_resizing_width=0;
$image_resizing_height=0;

//******************
// Default layout setting
//
// 0 => boxes
// 1 => detailed list (1 column)
// 2 => columns list (multiple columns depending on the width of the page)
// YOU CAN ALSO PASS THIS PARAMETERS USING SESSION VAR => $_SESSION["VIEW"]=
//
//******************
$default_view=0;

//set if the filename is truncated when overflow first row 
$ellipsis_title_after_first_row=true;

//*************************
//Permissions configuration
//******************
$delete_files=true;
$create_folders=true;
$delete_folders=true;
$upload_files=true;
$rename_files=true;
$rename_folders=true;
$duplicate_files=true;

//**********************
//Allowed extensions (lowercase insert)
//**********************
$ext_img = array('jpg', 'png', 'gif'); //Images
$ext_file = array(); //Files
$ext_video = array(); //Video 
$ext_music = array(); //Audio
$ext_misc = array(); //Archives

$ext=array_merge($ext_img, $ext_file, $ext_misc, $ext_video,$ext_music); //allowed extensions


/******************
 * AVIARY config
*******************/
$aviary_active=true;
$aviary_key="dvh8qudbp6yx2bnp";
$aviary_secret="m6xaym5q42rpw433";
$aviary_version=3;
$aviary_language='en';


//The filter and sorter are managed through both javascript and php scripts because if you have a lot of
//file in a folder the javascript script can't sort all or filter all, so the filemanager switch to php script.
//The plugin automatic swich javascript to php when the current folder exceeds the below limit of files number
$file_number_limit_js=500;

//**********************
// Hidden files and folders
//**********************
// set the names of any folders you want hidden (eg "hidden_folder1", "hidden_folder2" ) Remember all folders with these names will be hidden (you can set any exceptions in config.php files on folders)
$hidden_folders = array();
// set the names of any files you want hidden. Remember these names will be hidden in all folders (eg "this_document.pdf", "that_image.jpg" )
$hidden_files = array('config.php');

/*******************
 * JAVA upload 
 *******************/
$java_upload=true;
$JAVAMaxSizeUpload=200; //Gb


//************************************
//Thumbnail for external use creation
//************************************


// New image resized creation with fixed path from filemanager folder after uploading (thumbnails in fixed mode)
// If you want create images resized out of upload folder for use with external script you can choose this method, 
// You can create also more than one image at a time just simply add a value in the array
// Remember than the image creation respect the folder hierarchy so if you are inside source/test/test1/ the new image will create at
// path_from_filemanager/test/test1/
// PS if there isn't write permission in your destination folder you must set it
$fixed_image_creation                   = false; //activate or not the creation of one or more image resized with fixed path from filemanager folder
$fixed_path_from_filemanager            = array('../test/','../test1/'); //fixed path of the image folder from the current position on upload folder
$fixed_image_creation_name_to_prepend   = array('','test_'); //name to prepend on filename
$fixed_image_creation_to_append         = array('_test',''); //name to appendon filename
$fixed_image_creation_width             = array(300,400); //width of image (you can leave empty if you set height)
$fixed_image_creation_height            = array(200,''); //height of image (you can leave empty if you set width)


// New image resized creation with relative path inside to upload folder after uploading (thumbnails in relative mode)
// With Responsive filemanager you can create automatically resized image inside the upload folder, also more than one at a time
// just simply add a value in the array
// The image creation path is always relative so if i'm inside source/test/test1 and I upload an image, the path start from here
$relative_image_creation                = false; //activate or not the creation of one or more image resized with relative path from upload folder
$relative_path_from_current_pos         = array('thumb/','thumb/'); //relative path of the image folder from the current position on upload folder
$relative_image_creation_name_to_prepend= array('','test_'); //name to prepend on filename
$relative_image_creation_name_to_append = array('_test',''); //name to append on filename
$relative_image_creation_width          = array(300,400); //width of image (you can leave empty if you set height)
$relative_image_creation_height         = array(200,''); //height of image (you can leave empty if you set width)

?>
