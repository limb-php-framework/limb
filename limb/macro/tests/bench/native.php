<?php

set_include_path(dirname(__FILE__) . '/../../../../');
require_once('limb/core/common.inc.php');

for($i=0;$i<100;$i++)
{
  $name = 'Bob';
  ob_start();
  include(dirname(__FILE__) . '/tpl/native.phtml');
  $content = ob_get_contents();
  ob_end_clean();
}

$classes = array();
foreach(get_declared_classes() as $class)
{
    $refl = new ReflectionClass($class);
      if(strpos($refl->getFileName(), 'src/') !== false)
            $classes[] = $class;
}
sort($classes);
var_dump($classes);
