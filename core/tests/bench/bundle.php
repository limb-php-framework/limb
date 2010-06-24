<?php

set_include_path(dirname(__FILE__) . '/../../../../');
require_once('limb/core/common.inc.php');
require_once('limb/fs/src/lmbFs.class.php');

/*--------------------------------------*/
lmbFs :: mkDir(dirname(__FILE__) . '/temp/');

generateBundle('cc');

$mark = microtime(true);

require_once(dirname(__FILE__) . '/temp/bundle.inc.php');

for($i=0;$i<300;$i++)
{
  $class_name = 'MyClass'. $i . 'cc';
  $object = new $class_name();
}

echo "require_once absolute: " . (microtime(true) - $mark) . "\n";

/*--------------------------------------*/
generateFiles('aa');

$mark = microtime(true);

$dir = dirname(__FILE__) . '/temp/';
for($i=0;$i<300;$i++)
{
  $class_name = 'MyClass'. $i . 'aa';
  lmb_require($dir. $class_name . '.class.php');
  $object = new $class_name();
}

echo "lmb_require $i files: " . (microtime(true) - $mark) . "\n";

lmbFs :: rm(dirname(__FILE__) . '/temp/');

/*--------------------------------------*/

function generateBundle($sufffix)
{
  $bundle = "";

  for($i = 0; $i < 300; $i++)
  {
    $content = getContent($i . $sufffix);
    $bundle .= $content;
  }

  file_put_contents(dirname(__FILE__) . '/temp/bundle.inc.php', '<?php ' . $bundle . ' ?>');
}

function generateFiles($sufffix)
{
  for($i = 0; $i < 300; $i++)
  {
    $content = getContent($i . $sufffix);
    file_put_contents(dirname(__FILE__) . '/temp/MyClass' . $i . $sufffix .'.class.php', '<?php ' . $content . ' ?>');
  }
}

function getContent($sufffix)
{
  $content = 'class MyClass' . $sufffix;
  $content .= file_get_contents(dirname(__FILE__) . '/class_content.inc');
  return $content;
}

