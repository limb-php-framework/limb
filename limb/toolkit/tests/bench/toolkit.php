<?php

set_include_path(dirname(__FILE__) . '/../../../../');
require_once('limb/core/common.inc.php');
require_once('limb/toolkit/src/lmbToolkit.class.php');
require_once('limb/toolkit/src/lmbAbstractTools.class.php');

class BenchTools extends lmbAbstractTools
{
  function getFoo()
  {
    return 'foo';
  }
}

/*---------------------------*/
$toolkit = lmbToolkit :: setup(new BenchTools());

$mark = microtime(true);

for($i=0;$i<1000;$i++)
  $toolkit->getFoo();

echo "tools method access: " . (microtime(true) - $mark) . "\n";

/*---------------------------*/
$tools = new BenchTools();

$mark = microtime(true);

for($i=0;$i<1000;$i++)
  $tools->getFoo();

echo "regular method access: " . (microtime(true) - $mark) . "\n";

/*---------------------------*/
$mark = microtime(true);

for($i=0;$i<1000;$i++)
  lmbToolkit :: instance()->getFoo();

echo "tools method access with lmbToolkit :: instance() : " . (microtime(true) - $mark) . "\n";

