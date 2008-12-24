<?php
$conf = array(
  'cache_dir' => LIMB_VAR_DIR . '/compiled/',
  'forcescan' => false,  #Force to scan directories for tags, filters and properties (very slow)
  'forcecompile' => true, #Force every template to be re-compiled on every request. Option is used
                       #for debugging templates when developing template generation code
  'tpl_scan_dirs' => array('template', 'limb/*/template'),
  'tags_scan_dirs' => array('src/macro', 'limb/*/src/macro','limb/macro/src/tags'),
  'filters_scan_dirs' => array('src/macro','limb/*/src/macro','limb/macro/src/filters'),
);

if(defined('LIMB_TEMPLATES_INCLUDE_PATH'))
  $conf['tpl_scan_dirs'] = constant('LIMB_TEMPLATES_INCLUDE_PATH');

if(defined('LIMB_MACRO_TAG_INCLUDE_PATH'))
  $conf['tags_dirs'] = constant('LIMB_MACRO_TAG_INCLUDE_PATH');

if(defined('LIMB_MACRO_FILTER_INCLUDE_PATH'))
  $conf['filters_dirs'] = constant('LIMB_MACRO_FILTER_INCLUDE_PATH');