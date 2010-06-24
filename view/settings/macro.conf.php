<?php
$conf = array(
  'cache_dir' => lmb_env_get('LIMB_VAR_DIR') . '/compiled/',
  'forcescan' => false,  #Force to scan directories for tags, filters and properties (very slow)
  'forcecompile' => true, #Force every template to be re-compiled on every request. Option is used
                       #for debugging templates when developing template generation code
  'tpl_scan_dirs' => lmb_env_get('LIMB_TEMPLATES_INCLUDE_PATH', array('template', 'limb/*/template')),
  'tags_scan_dirs' => lmb_env_get('LIMB_MACRO_TAGS_INCLUDE_PATH', array('src/macro', 'limb/*/src/macro','limb/macro/src/tags')),
  'filters_scan_dirs' => lmb_env_get('LIMB_MACRO_FILTERS_INCLUDE_PATH', array('src/macro','limb/*/src/macro','limb/macro/src/filters')),
);