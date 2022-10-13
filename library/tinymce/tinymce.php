<?php
class Editor
{
	function __construct(){
		global $ims;
        $ims->conf['lang_cur']     = (isset($ims->conf['lang_cur'])) ? $ims->conf['lang_cur'] : $ims->data["lang_default"]["name"];

		$plugins = '"advlist autolink link image lists charmap preview hr anchor pagebreak",
                "visualblocks visualchars code fullscreen media nonbreaking",
                "table contextmenu directionality emoticons colorpicker textcolor paste "';
		$option = array(
			'image_class_list' => '{title: "None", value: ""},
				{title: "Full size", value: "img_full_size"},
				{title: "Max width", value: "img_max_width"},
				{title: "Float left", value: "img_fleft"},
				{title: "Float right", value: "img_fright"}',
			'table_class_list' => '{title: "None", value: ""},
				{title: "Kiểu 1", value: "csstable1"},
				{title: "Kiểu 2", value: "csstable2"},
				{title: "Kiểu 3", value: "csstable3"},
				{title: "Kiểu 4", value: "csstable4"},
				{title: "Kiểu 5", value: "csstable5"}'
		);
		
		$ims->func->include_js_content('
			var g_folder_up = "";
			var g_fldr = "";
			function load_editor (type, html_id, folder_up, fldr) {
				g_folder_up = folder_up;
				g_fldr = fldr;
				var height = 250;
				var toolbar = "fontselect fontsizeselect | removeformat bold italic underline strikethrough | forecolor backcolor | link unlink anchor | hr | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent| superscript subscript | image media";
                var is_inline = 0;
                var menubar = true;
				
				if(type == "template_email") {
					height = 500;
				} else if(type == "full") {
					height = 500;                
                    toolbar += " ";
				} else if(type == "map") {
					height = 250;
				} else if(type == "mini") {
					height = 250;
                    menubar = false;
				} else if(type == "inline_short") {
					height = 250;
                    is_inline = 1;
				}

                if(is_inline == 1) {
                    toolbar = "superscript subscript | bold italic underline strikethrough | removeformat | hr";
                    menubar = false;
                }

                console.log("hEYYY");
                tinymce.remove("#" + html_id);

				tinymce.init({
					fontsize_formats: "10px 11px 12px 13px 14px 15px 16px 18px 20px 22px 24px 26px 28px",
					content_style: " body {font-size: 15px;font-family: \'Arial\';} " +
    " p {margin: 0px; padding: 3px 0px} ",
                    language : "vi",
					selector: "#"+html_id,
					theme: "silver",
					skin: "custom",
    				icons: "oxide-dark",
                    inline: (is_inline == 1) ? true : false,
                    forced_root_block: (is_inline == 1) ? false : "p",
                    body_class: (is_inline == 1) ? "mce-container-body-inline" : "",
					width: "100%",
					height: height,
					link_list: [
						{title: "Trang chủ", value: "'.$ims->conf['rooturl_web'].'"}
					],
					plugins: ['.$plugins.'],
					paste_image_data: false,
					paste_as_text: true,
					image_class_list: ['.$option["image_class_list"].'],
                    image_caption: true,
                    //image_prepend_url: "'.$ims->conf['rooturl_web'].'",
					table_class_list: ['.$option["table_class_list"].'],
					relative_urls: false,
					//image_dimensions: (type == "template_email" || type == "map") ? true : false,
                    image_dimensions: true,
					convert_urls : (type == "template_email") ? false : true,
					browser_spellcheck : true ,
					entity_encoding : "raw",
					// filemanager_title:"",
					// external_filemanager_path:"'.$ims->conf["rooturl"].'admin/?mod=library&act=library&sub=popup_library&lang='.$ims->conf["lang_cur"].'&folder_up="+folder_up+"&fldr="+fldr,
					// external_plugins: { "filemanager" : "'.$ims->conf["rooturl"].'admin/modules/library/plugin.min.js"},
					codemirror: {
					indentOnInit: true, // Whether or not to indent code on init. 
					path: "CodeMirror",
					setup: function (editor) {						
                        editor.on("change", function (e) {                        	
                            tinymce.triggerSave();
                        });
                    }
				},
				
				image_advtab: true,
				
				toolbar: toolbar,
				extended_valid_elements : "i[*]",
				
				menubar: menubar,
				// toolbar_items_size: "small"

			 });
			}
		');
	}
	//=================get_menu===============
	function load_editor ($html_name, $html_id, $value="", $more_atl="", $type="full", $more_conf = array())
	{
		global $ims;
		
		$folder_up = (isset($more_conf["folder_up"])) ? $more_conf["folder_up"] : "";
		$fldr = (isset($more_conf["fldr"])) ? $more_conf["fldr"] : "";
		$is_inline = (in_array($type, array('inline_short'))) ? true : false;
        if($is_inline == true) {
            $value = $ims->func->input_editor_decode($value);
        }
        $html_etype = ($is_inline == true) ? 'div' : 'textarea';
        $html_id = $html_id.'_'.$ims->func->random_str(10);
        
		$output = '<'.$html_etype.' id="'.$html_id.'" name="'.$html_name.'" '.$more_atl.'>'.$value.'</'.$html_etype.'><script language="javascript">load_editor ("'.$type.'", "'.$html_id.'", "'.$folder_up.'", "'.$fldr.'")</script>';
		
		return $output;
	}
}
?>