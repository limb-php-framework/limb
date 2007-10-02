<?php

set_include_path(dirname(__FILE__) . '/../../../../');
require_once('limb/core/common.inc.php');
require_once('limb/macro/src/lmbMacroTemplate.class.php');

include('start.inc.php');

$config = new lmbMacroConfig('/tmp/macro', false, false, array(dirname(__FILE__) . '/tpl'));

for($i=0;$i<1000;$i++)
{
  $tpl = new lmbMacroTemplate('simple.phtml', $config);
  $tpl->set('name', 'Bob');
  $tpl->render();
}

include('end.inc.php');
