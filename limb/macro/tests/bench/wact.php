<?php

set_include_path(dirname(__FILE__) . '/../../../../');
define('WACT_CACHE_DIR', '/tmp/wact');
define('WACT_TPLS', dirname(__FILE__) . '/tpl');
require_once('limb/wact/common.inc.php');
require_once('limb/wact/src/WactTemplate.class.php');
require_once('limb/wact/src/WactDefaultTemplateConfig.class.php');

include('start.inc.php');

for($i=0;$i<1000;$i++)
{
  $tpl = new WactTemplate('wact.html', new WactDefaultTemplateConfig(dirname(__FILE__) . '/settings/wact.ini'));
  $tpl->set('name', 'Bob');
  $tpl->capture();
}

include('end.inc.php');
