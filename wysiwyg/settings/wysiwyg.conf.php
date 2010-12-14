<?php

$conf = array (

  'default_profile' => 'default_fckeditor',

  'default_ckeditor' => array(
    'type' => 'ckeditor',		    
    'basePath' => '/shared/wysiwyg/ckeditor/',
    'Config' => array(
      'toolbar' => 'Full',
      'uiColor' => '#9AB8F3',
      'customConfig' => '/shared/wysiwyg/ckeditor/ckeditor_config.js',
	    'filebrowserBrowseUrl' => '/shared/wysiwyg/kcfinder/browse.php?type=files&opener=ckeditor',
	    'filebrowserImageBrowseUrl' => '/shared/wysiwyg/kcfinder/browse.php?type=images&opener=ckeditor',
      'filebrowserFlashBrowseUrl' => '/shared/wysiwyg/kcfinder/browse.php?type=flash&opener=ckeditor',
      'filebrowserUploadUrl' => '/shared/wysiwyg/kcfinder/upload.php?type=files&opener=ckeditor',
      'filebrowserImageUploadUrl' => '/shared/wysiwyg/kcfinder/upload.php?type=images&opener=ckeditor',
      'filebrowserFlashUploadUrl' => '/shared/wysiwyg/kcfinder/upload.php?type=flash&opener=ckeditor'
    ),
  ),

  'default_fckeditor' => array (
    'type' => 'fckeditor',
    'width' => '600px',
    'height' => '400px',
    'cols' => '100',
    'rows' => '15',
    'Config' => array('CustomConfigurationsPath' => '/shared/wysiwyg/fckeditor/fckconfig.js'),
    'ToolbarSet' => 'Default'
  ),

  'default_tinymce' => array(
    'type' => 'tinymce',
    'width' => '600px',
    'height' => '400px',
    'cols' => 100,
    'rows' => 15,
    'base_path' => '/shared/wysiwyg/tiny_mce/',
    'editor' => array(
      'language' => 'en',
      'mode' => "textareas",
      'theme' => "advanced",

      'plugins' => "safari,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

      'theme_advanced_buttons1' => "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
      'theme_advanced_buttons2' => "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
      'theme_advanced_buttons3' => "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
      'theme_advanced_buttons4' => "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",

      'theme_advanced_toolbar_location' => "top",
      'theme_advanced_toolbar_align' => "left",
      'theme_advanced_path_location' => "bottom",

      'plugin_insertdate_dateFormat' => "%Y-%m-%d",
      'plugin_insertdate_timeFormat' => "%H:%M:%S",

      'theme_advanced_resize_horizontal' => false,
      'theme_advanced_resizing' => true,
      'apply_source_formatting' => true,

      'spellchecker_languages' => "+English=>en,Danish=>da,Dutch=>nl,Finnish=>fi,French=>fr,German=>de,Italian=>it,Polish=>pl,Portuguese=>pt,Russian=>ru,Spanish=>es,Swedish=>sv,Ukrainian=>uk",

      // Example content CSS (should be your site CSS)
      'content_css' > "css/example.css",

      // Drop lists for link/image/media/template dialogs
      'template_external_list_url' => "js/template_list.js",
      'external_link_list_url' => "js/link_list.js",
      'external_image_list_url' => "js/image_list.js",
      'media_external_list_url' => "js/media_list.js",
    )
  )
);