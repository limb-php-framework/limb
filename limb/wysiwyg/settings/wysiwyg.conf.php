<?php

$conf = array(

  'default_profile' => 'default_fckeditor',

  'default_fckeditor' => array(
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
      'mode' => "textareas",
      'theme' => "advanced",
      'plugins' => "spellchecker,style,layer,table,save,advhr,advimage,ibrowser,advlink,emotions,iespell,insertdatetime,preview,zoom,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
      'theme_advanced_buttons1_add_before' => "save,newdocument,separator",
      'theme_advanced_buttons1_add' => "fontselect,fontsizeselect",
      'theme_advanced_buttons2_add' => "separator,insertdate,inserttime,preview,separator,forecolor,backcolor",
      'theme_advanced_buttons2_add_before' => "cut,copy,paste,pastetext,pasteword,separator,search,replace,separator",
      'theme_advanced_buttons3_add_before' => "tablecontrols,separator",
      'theme_advanced_buttons3_add' => "ibrowser, emotions,iespell,media,advhr,separator,print,separator,ltr,rtl,separator,fullscreen",
      'theme_advanced_buttons4' => "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,spellchecker,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template",
      'theme_advanced_toolbar_location' => "top",
      'theme_advanced_toolbar_align' => "left",
      'theme_advanced_path_location' => "bottom",
      'plugin_insertdate_dateFormat' => "%Y-%m-%d",
      'plugin_insertdate_timeFormat' => "%H:%M:%S",
      'extended_valid_elements' => "hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style],p[lang]",
      'external_link_list_url' => "example_data/example_link_list.js",
      'external_image_list_url' => "example_data/example_image_list.js",
      'flash_external_list_url' => "example_data/example_flash_list.js",
      'template_external_list_url' => "example_data/example_template_list.js",
      'theme_advanced_resize_horizontal' => false,
      'theme_advanced_resizing' => true,
      'apply_source_formatting' => true,
      'spellchecker_languages' => "+English=>en,Danish=>da,Dutch=>nl,Finnish=>fi,French=>fr,German=>de,Italian=>it,Polish=>pl,Portuguese=>pt,Spanish=>es,Swedish=>sv",
    )
  )
);