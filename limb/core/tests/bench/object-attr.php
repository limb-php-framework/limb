<?php

set_include_path(dirname(__FILE__) . '/../../../../');
require_once('limb/core/common.inc.php');
require_once('limb/core/src/lmbObject.class.php');

class Foo extends lmbObject
{
  function getBar()
  {
    return 'bar';
  }
}
$object = new Foo(array('foo' => 'foo'));

for($i=0;$i<1000;$i++)
  $object->get('heatingUp');

$mark = microtime(true);

for($i=0;$i<1000;$i++)
  $object->get('foo');

echo "raw getter access: " . (microtime(true) - $mark) . "\n";

$mark = microtime(true);

for($i=0;$i<1000;$i++)
  $object->get('bar');

echo "raw getter access mapped to method: " . (microtime(true) - $mark) . "\n";

$mark = microtime(true);

for($i=0;$i<1000;$i++)
  $object->getBar();

echo "static getter access: " . (microtime(true) - $mark) . "\n";

$mark = microtime(true);

for($i=0;$i<1000;$i++)
  $object->getFoo();

echo "dynamic getter access: " . (microtime(true) - $mark) . "\n";

$mark = microtime(true);

for($i=0;$i<1000;$i++)
  $object->foo;

echo "raw attribute access: " . (microtime(true) - $mark) . "\n";
