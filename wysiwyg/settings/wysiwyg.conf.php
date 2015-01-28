<?php

$conf = array (
  'default_profile' => 'full',

  'full' => array(
    'type' => 'ckeditor',
    'basePath' => '/shared/wysiwyg/ckeditor/',
    'Config' => array(
      'customConfig' => '/shared/wysiwyg/ckeditor/config.js',
      'toolbar' => 'Full',
    ),
  ),

  'basic' => array(
    'type' => 'ckeditor',
    'basePath' => '/shared/wysiwyg/ckeditor/',
    'Config' => array(
      'customConfig' => '/shared/wysiwyg/ckeditor/config.js',
      'toolbar' => 'Basic',
      'format_tags' => 'p;div',
    ),
  ),
);
