<?php

set_include_path(dirname(__FILE__) . '/../../../../');
define('WACT_CACHE_DIR', '/tmp/wact');
define('WACT_TPLS', dirname(__FILE__) . '/tpl');
require_once('limb/core/common.inc.php');
require_once('limb/macro/src/lmbMacroTemplate.class.php');
require_once('limb/wact/common.inc.php');
require_once('limb/wact/src/WactTemplate.class.php');
require_once('limb/wact/src/WactDefaultTemplateConfig.class.php');

for($i=0;$i<1000;$i++)
 $j = $i;

$name = 'Bob';

$tpl = new lmbMacroTemplate('macro.phtml', 
                           array('cache_dir' => '/tmp/macro', 
                                 'forcecompile' => false, 
                                 'forcescan' => false, 
                                 'tpl_scan_dirs' => array(dirname(__FILE__) . '/tpl')));
$tpl->set('name', $name);

$mark = microtime(true);

$tpl->render();

echo "MACRO 1xrender: " . (microtime(true) - $mark) . "\n";

$mark = microtime(true);

for($i=0;$i<1000;$i++)
{
  $tpl->render();
}

echo "MACRO 1000xrender: " . (microtime(true) - $mark) . "\n";

$tpl = new WactTemplate('wact.html', new WactDefaultTemplateConfig(dirname(__FILE__) . '/settings/wact.ini'));
$tpl->set('name', $name);

$mark = microtime(true);

$tpl->capture();

echo "WACT 1xrender: " . (microtime(true) - $mark) . "\n";

$mark = microtime(true);

for($i=0;$i<1000;$i++)
{
  $tpl->capture();
}

echo "WACT 1000xrender: " . (microtime(true) - $mark) . "\n";

$mark = microtime(true);

ob_start();
include(dirname(__FILE__) . '/tpl/native.phtml');
ob_get_contents();
ob_end_clean();

echo "PHP 1xrender: " . (microtime(true) - $mark) . "\n";

$mark = microtime(true);

for($i=0;$i<1000;$i++)
{
  ob_start();
  include(dirname(__FILE__) . '/tpl/native.phtml');
  ob_get_contents();
  ob_end_clean();
}

echo "PHP 1000xrender: " . (microtime(true) - $mark) . "\n";
