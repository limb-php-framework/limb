<?php

set_include_path(dirname(__FILE__) . '/../../../../;.');
require_once('limb/core/common.inc.php');

//require_once(dirname(__FILE__) . '/bundle.inc.php');

$path = dirname(__FILE__) . '/MyClass.class.php';

$mark = microtime(true);

for($i=0;$i<1000;$i++)
{
  lmb_require(dirname(__FILE__) . '/MyClass' . $i . '.class.php');
}

echo "lmb_require not included file: " . (microtime(true) - $mark) . "\n";

$mark = microtime(true);

for($i=0;$i<1000;$i++)
{
  lmb_require($path);
  $object = new MyClass();
}

echo "lmb_require absolute: " . (microtime(true) - $mark) . "\n";

$mark = microtime(true);

for($i=0;$i<1000;$i++)
{
  lmb_require('MyClass.class.php');
  $object = new MyClass();
}

echo "lmb_require relative: " . (microtime(true) - $mark) . "\n";


$mark = microtime(true);

for($i=0;$i<1000;$i++)
{
  require_once($path);
  $object = new MyClass();
}

echo "require once absolute: " . (microtime(true) - $mark) . "\n";


$mark = microtime(true);

for($i=0;$i<1000;$i++)
{
  require_once('MyClass.class.php');
  $object = new MyClass();
}

echo "require once relative: " . (microtime(true) - $mark) . "\n";


