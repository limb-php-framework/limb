<?php

set_include_path(dirname(__FILE__) . '/../../../../' . PATH_SEPARATOR. '.');
require_once('limb/core/common.inc.php');

$path = dirname(__FILE__) . '/MyClass.class.php';

$mark = microtime(true);

for($i=0;$i<1000;$i++)
{
  lmb_require('Boo.class.php');
}

echo "lmb_require same class, no autoload: " . (microtime(true) - $mark) . "\n";

$mark = microtime(true);

for($i=0;$i<1000;$i++)
{
  lmb_require('Boo' . $i . '.class.php');
}

echo "lmb_require unique class, no autoload: " . (microtime(true) - $mark) . "\n";

$mark = microtime(true);

for($i=0;$i<1000;$i++)
{
  lmb_require($path);
}
$object = new MyClass();

echo "lmb_require absolute, same class, autoload: " . (microtime(true) - $mark) . "\n";

$mark = microtime(true);

for($i=0;$i<1000;$i++)
{
  lmb_require('MyClass.class.php');
}
$object = new MyClass();

echo "lmb_require relative, same class, autoload: " . (microtime(true) - $mark) . "\n";

for($i=0;$i<1000;$i++)
{
  make_class('UniqueClass' . $i);
}

$mark = microtime(true);

for($i=0;$i<1000;$i++)
{
  $class = 'UniqueClass' . $i;
  lmb_require(dirname(__FILE__) . '/tmp/'. $class . '.class.php');
  $object = new $class;
}

echo "lmb_require absolute, unique class, autoload: " . (microtime(true) - $mark) . "\n";

$mark = microtime(true);

for($i=0;$i<1000;$i++)
{
  $class = 'UniqueClass' . $i;
  lmb_require(dirname(__FILE__) . '/tmp/'. $class . '.class.php');
  $object = new $class;
}

echo "lmb_require absolute, again: " . (microtime(true) - $mark) . "\n";

$mark = microtime(true);

for($i=0;$i<1000;$i++)
{
  require_once($path);
}
$object = new MyClass();

echo "require_once absolute, same class: " . (microtime(true) - $mark) . "\n";

$mark = microtime(true);

for($i=0;$i<1000;$i++)
{
  require_once('MyClass.class.php');
}
$object = new MyClass();

echo "require_once relative, same class: " . (microtime(true) - $mark) . "\n";

for($i=0;$i<1000;$i++)
{
  make_class('UniqueClazz' . $i);
}

$mark = microtime(true);

for($i=0;$i<1000;$i++)
{
  $class = 'UniqueClazz' . $i;
  require_once(dirname(__FILE__) . '/tmp/' . $class . '.class.php');
  $object = new $class();
}

echo "require_once absolute, unique class: " . (microtime(true) - $mark) . "\n";

$mark = microtime(true);

for($i=0;$i<1000;$i++)
{
  $class = 'UniqueClazz' . $i;
  require_once(dirname(__FILE__) . '/tmp/' . $class . '.class.php');
  $object = new $class();
}

echo "require_once absolute, again: " . (microtime(true) - $mark) . "\n";

`rm -rf ./tmp`;

function make_class($name)
{
  if(!is_dir('./tmp'))
    mkdir('./tmp');
  file_put_contents('./tmp/' . $name . '.class.php', 
                    '<?php class ' . $name . ' {}; ?>');
}
