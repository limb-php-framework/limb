<?php

$time = microtime(true) - $start;

echo "\ntime: $time sec.\n";

$class_stat = false;
foreach($argv as $arg)
{
  if($arg == '--classes')
    $class_stat = true;
}

if($class_stat)
{
  $classes = array();
  foreach(get_declared_classes() as $class)
  {
      $refl = new ReflectionClass($class);
        if(strpos($refl->getFileName(), 'src/') !== false)
              $classes[] = $class;
  }
  sort($classes);
  var_dump($classes);
}
