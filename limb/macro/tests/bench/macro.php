<?php

if(!isset($argv[1]))
  die("\nTemplate argument is required\n");
$file = $argv[1];

set_include_path(dirname(__FILE__) . '/../../../../');
require_once('limb/core/common.inc.php');
require_once('limb/macro/src/lmbMacroTemplate.class.php');

$tpl = new lmbMacroTemplate($file, new lmbMacroConfig('/tmp/macro', false, false, array(dirname(__FILE__) . '/tpl')));

for($i=2;$i<$argc;$i++)
{
  list($key, $value) = explode('=', $argv[$i]);
  $tpl->set($key, $value);
}

include('start.inc.php');

for($i=0;$i<1000;$i++)
{
  $tpl->render();
}

include('end.inc.php');
