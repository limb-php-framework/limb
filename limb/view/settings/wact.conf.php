<?php
$conf = array(
  'cache_dir' => LIMB_VAR_DIR . '/compiled/',
  'forcescan' => false,  #Force to scan directories for tags, filters and properties (very slow)
  'forcecompile' => true, #Force every template to be re-compiled on every request. Option is used
                       #for debugging templates when developing template generation code
  'tpl_scan_dirs' => 'template;limb/*/template',
  'tags_dirs' => 'src/wact','limb/*/src/wact','limb/wact/src/tags','src/template/tags','limb/*/src/template/tags','limb/wact/src/tags'
);

