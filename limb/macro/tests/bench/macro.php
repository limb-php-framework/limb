<?php

if(!isset($argv[1]))
  die("\nTemplate argument is required\n");
$file = $argv[1];

$forcecompile = false;
if(isset($argv[2]))
  $forcecompile = (bool)$argv[2];

$forcescan = false;
if(isset($argv[3]))
  $forcescan = (bool)$argv[3];

set_include_path(dirname(__FILE__) . '/../../../../');
require_once('limb/core/common.inc.php');
require_once('limb/core/src/lmbSet.class.php');
require_once('limb/macro/src/lmbMacroTemplate.class.php');
require_once(dirname(__FILE__) . '/profile.inc.php');

$tpl = new lmbMacroTemplate($file, array('cache_dir' => '/tmp/macro', 
                                         'forcecompile' => $forcecompile, 
                                         'forcescan' => $forcescan, 
                                         'tpl_scan_dirs' => array(dirname(__FILE__))));
/*$tpl = new lmbMacroTemplate($file, new lmbSet(array('cache_dir' => '/tmp/macro', 
                                         'forcecompile' => $forcecompile, 
                                         'forcescan' => $forcescan, 
                                         'tpl_scan_dirs' => array(dirname(__FILE__)))));*/

for($i=2;$i<$argc;$i++)
{
  if(strpos($argv[$i], '=') === false)
    continue;

  list($key, $value) = explode('=', $argv[$i]);
  $tpl->set($key, $value);
}

profile_start();

for($i=0;$i<1000;$i++)
{
  $tpl->render();
}

profile_end("running $i iterations of render");
