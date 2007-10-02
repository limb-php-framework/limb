<?php

set_include_path(dirname(__FILE__) . '/../../../../');
require_once('limb/core/common.inc.php');
require_once('limb/macro/src/lmbMacroTemplate.class.php');

include('start.inc.php');

for($i=0;$i<1000;$i++)
{
  $tpl = new lmbMacroTemplate('macro.phtml', new lmbMacroConfig('/tmp/macro', false, false, array(dirname(__FILE__) . '/tpl')));
  $tpl->set('name', 'Bob');
  $tpl->render();
}

include('end.inc.php');
