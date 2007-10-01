<?php

set_include_path(dirname(__FILE__) . '/../../../../');
require_once('limb/core/common.inc.php');
require_once('limb/macro/src/lmbMacroTemplate.class.php');

$config = new lmbMacroConfig('/tmp/macro', true, true, array(dirname(__FILE__) . '/tpl'));

for($i=0;$i<1000;$i++)
{
  $tpl = new lmbMacroTemplate('simple.phtml', $config);
  $tpl->set('name', 'Bob');
  $tpl->render();
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
