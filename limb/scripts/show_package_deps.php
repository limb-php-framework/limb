<?php
set_time_limit(0);

if($argc < 2)
{
  echo "Usage: show_package_deps <dir> [dep]";
  exit(1);
}

$dir = $argv[1];
$dep = isset($argv[2]) ? $argv[2] : '';
$pkg = basename($dir);
$deps = array();
$files = explode("\n", trim(`find $dir -type f -name "*.php"`));
$dep_files = array();

foreach($files as $file)
{
  if(preg_match_all('~(?:\'|")(limb/(\w+)/[^\'"]+)~', file_get_contents($file), $matches))
  {
    foreach(array_unique($matches[2]) as $index => $name)
    {
      if($pkg == $name)
        continue;

      $deps[$name] = 1;

      if($name == $dep)
        $dep_files[$file] = $matches[1][$index];
    }
  }
}

if($dep_files)
{
  foreach($dep_files as $file => $dep)
    echo "$file => $dep\n";
}
else
{
  foreach(array_keys($deps) as $name)
    echo "$name\n";
}



