<?php

function profile_start()
{
  global $profile_start;
  $profile_start = microtime(true);
}

function profile_end($label = "time", $class_stat = false)
{
  global $profile_start;
  $time = microtime(true) - $profile_start;

  echo "$label: $time sec.\n";

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
}
