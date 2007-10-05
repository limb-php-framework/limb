<?php

if(!isset($argv[1]))
  die("\nTemplate argument is required\n");
$file = $argv[1];

set_include_path(dirname(__FILE__) . '/../../../../');
define('WACT_CACHE_DIR', '/tmp/wact');
define('WACT_TPLS', dirname(__FILE__) . '/tpl');
require_once('limb/wact/common.inc.php');
require_once('limb/wact/src/WactTemplate.class.php');
require_once('limb/wact/src/WactDefaultTemplateConfig.class.php');

$tpl = new WactTemplate($file, new WactDefaultTemplateConfig(dirname(__FILE__) . '/settings/wact.ini'));

for($i=2;$i<$argc;$i++)
{
  list($key, $value) = explode('=', $argv[$i]);
  $tpl->set($key, $value);
}

include('start.inc.php');

for($i=0;$i<1000;$i++)
{
  $tpl->capture();
}

include('end.inc.php');
