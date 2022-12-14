<?php
if (! defined('IN_ims')) {
  die('Access denied');
}
/*include('config/config.php');
if($_SESSION["verify"] != "RESPONSIVEfilemanager") die('forbiden');
include('include/utils.php');*/

$thumb_pos=strpos($ims->post['path_thumb'],$thumbs_base_path);
if($thumb_pos !=0
    || strpos($ims->post['path_thumb'],'../',strlen($thumbs_base_path)+$thumb_pos)!==FALSE
    || strpos($ims->post['path'],'/')===0
    || strpos($ims->post['path'],'../')!==FALSE
    || strpos($ims->post['path'],'./')===0)
    die('wrong path');

$language_file = 'lang/vi.php'; 
if (isset($ims->get['lang']) && $ims->get['lang'] != 'undefined' && $ims->get['lang']!='') {
    $path_parts = pathinfo($ims->get['lang']);
    if(is_readable('lang/' .$path_parts['basename']. '.php')){ 
        $language_file = 'lang/' .$path_parts['basename']. '.php';
    }
}
require_once $language_file;

$base=$current_path;
$path=$current_path.$ims->post['path'];
$cycle=true;
$max_cycles=50;
$i=0;
while($cycle && $i<$max_cycles){
    $i++;
    if($path==$base)  $cycle=false;
    
    if(file_exists($path."config.php")){
	require_once($path."config.php");
	$cycle=false;
    }
    $path=fix_dirname($path)."/";
    $cycle=false;
}

$path=$current_path.$ims->post['path'];
$path_thumb=$ims->post['path_thumb'];
$path_thumbs_size=$current_thumbs_size.$ims->post['path'];
if(isset($ims->post['name'])){
    $name=$ims->post['name'];
    if(strpos($name,'../')!==FALSE) die('wrong name');
}

$info=pathinfo($path);
if(isset($info['extension']) && !(isset($ims->get['action']) && $ims->get['action']=='delete_folder') && !in_array(strtolower($info['extension']), $ext)){
    die('wrong extension');
}
    
if(isset($ims->get['action'])){
    
    switch($ims->get['action']){
        case 'delete_file':
						$path_thumbs_size = substr($path_thumbs_size, 0, strrpos($path_thumbs_size, "."));
						$path_thumbs_size .= '_'.substr($ims->post['path'], strrpos($ims->post['path'], ".") + 1);
            if($delete_files){
                unlink($path);
                if(file_exists($path_thumb))
                    unlink($path_thumb);
								if(file_exists($path_thumbs_size) && is_dir($path_thumbs_size))
									deleteDir($path_thumbs_size);
		    
		$info=pathinfo($path);
		if($relative_image_creation){
		    foreach($relative_path_from_current_pos as $k=>$path){
			if($path!="" && $path[strlen($path)-1]!="/") $path.="/";
			if(file_exists($info['dirname']."/".$path.$relative_image_creation_name_to_prepend[$k].$info['filename'].$relative_image_creation_name_to_append[$k].".".$info['extension'])){
			    unlink($info['dirname']."/".$path.$relative_image_creation_name_to_prepend[$k].$info['filename'].$relative_image_creation_name_to_append[$k].".".$info['extension']);
			}
		    }
		}
		
		if($fixed_image_creation){
		    foreach($fixed_path_from_filemanager as $k=>$path){
			if($path!="" && $path[strlen($path)-1]!="/") $path.="/";
			$base_dir=$path.substr_replace($info['dirname']."/", '', 0, strlen($current_path));
			if(file_exists($base_dir.$fixed_image_creation_name_to_prepend[$k].$info['filename'].$fixed_image_creation_to_append[$k].".".$info['extension'])){
			    unlink($base_dir.$fixed_image_creation_name_to_prepend[$k].$info['filename'].$fixed_image_creation_to_append[$k].".".$info['extension']);
			}
		    }
		}
            }
            break;
        case 'delete_folder':
            if($delete_folders){
							if(is_dir($path_thumb))
									deleteDir($path_thumb);
							if(is_dir($path)){
								deleteDir($path);	
								if($fixed_image_creation){
									foreach($fixed_path_from_filemanager as $k=>$paths){
											if($paths!="" && $paths[strlen($paths)-1]!="/") $paths.="/";
											$base_dir=$paths.substr_replace($path, '', 0, strlen($current_path));
											if(is_dir($base_dir))
										deleteDir($base_dir);
									}
								}
							}
							if(is_dir($path_thumbs_size))
								deleteDir($path_thumbs_size);
            }
            break;
        case 'create_folder':
            if($create_folders){
                create_folder(fix_path($path,$transliteration),fix_path($path_thumb,$transliteration));
            }
            break;
        case 'rename_folder':
            if($rename_folders){
                $name=fix_filename($name,$transliteration);
		$name=str_replace('.','',$name);
		
                if(!empty($name)){
                    if(!rename_folder($path,$name,$transliteration))
                        die(lang_Rename_existing_folder);
                    rename_folder($path_thumb,$name,$transliteration);
		    if($fixed_image_creation){
			foreach($fixed_path_from_filemanager as $k=>$paths){
			    if($paths!="" && $paths[strlen($paths)-1]!="/") $paths.="/";
			    $base_dir=$paths.substr_replace($path, '', 0, strlen($current_path));
			    rename_folder($base_dir,$name,$transliteration);
			}
		    }
                }else{
                    die(lang_Empty_name);
                }
            }
            break;
        case 'rename_file':
            if($rename_files){
                $name=fix_filename($name,$transliteration);
                if(!empty($name)){
                    if(!rename_file($path,$name,$transliteration))
                        die(lang_Rename_existing_file);
                    rename_file($path_thumb,$name,$transliteration);
		    if($fixed_image_creation){
			$info=pathinfo($path);
			foreach($fixed_path_from_filemanager as $k=>$paths){
			    if($paths!="" && $paths[strlen($paths)-1]!="/") $paths.="/";
			    $base_dir=$paths.substr_replace($info['dirname']."/", '', 0, strlen($current_path));
			    if(file_exists($base_dir.$fixed_image_creation_name_to_prepend[$k].$info['filename'].$fixed_image_creation_to_append[$k].".".$info['extension'])){
				rename_file($base_dir.$fixed_image_creation_name_to_prepend[$k].$info['filename'].$fixed_image_creation_to_append[$k].".".$info['extension'],$fixed_image_creation_name_to_prepend[$k].$name.$fixed_image_creation_to_append[$k],$transliteration);
			    }
			}
		    }
                }else{
                    die(lang_Empty_name);
                }
            }
            break;
	case 'duplicate_file':
            if($duplicate_files){
                $name=fix_filename($name,$transliteration);
                if(!empty($name)){
                    if(!duplicate_file($path,$name))
                        die(lang_Rename_existing_file);
                    duplicate_file($path_thumb,$name);
		    if($fixed_image_creation){
			$info=pathinfo($path);
			foreach($fixed_path_from_filemanager as $k=>$paths){
			    if($paths!="" && $paths[strlen($paths)-1]!="/") $paths.="/";
			    $base_dir=$paths.substr_replace($info['dirname']."/", '', 0, strlen($current_path));
			    if(file_exists($base_dir.$fixed_image_creation_name_to_prepend[$k].$info['filename'].$fixed_image_creation_to_append[$k].".".$info['extension'])){
				duplicate_file($base_dir.$fixed_image_creation_name_to_prepend[$k].$info['filename'].$fixed_image_creation_to_append[$k].".".$info['extension'],$fixed_image_creation_name_to_prepend[$k].$name.$fixed_image_creation_to_append[$k]);
			    }
			}
		    }
                }else{
                    die(lang_Empty_name);
                }
            }
            break;
        default:
            die('wrong action');
            break;
    }
    
}



?>
