<?php

$conf = array(
  'default_profile' => 'cms_document',

  'cms_document' => array(
    'type' => 'fckeditor',    
    'Config' => array('CustomConfigurationsPath' => '/shared/cms/js/fckconfig.js'),
    'ToolbarSet' => 'cms_document'
  ),
  
  'simple' => array(
    'type' => 'fckeditor',
    'Config' => array('CustomConfigurationsPath' => '/shared/cms/js/fckconfig.js'),
    'ToolbarSet' => 'Basic'
  ),
  
);