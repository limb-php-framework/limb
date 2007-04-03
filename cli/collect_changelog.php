<?php
set_time_limit(0);

if($argc < 2)
{
  echo "Usage: collect_changelog <dir>";
  exit(1);
}

$root = $argv[1];
$dirs = explode("\n", trim(`find $root -maxdepth 1 -mindepth 1 -type d`));

foreach($dirs as $dir)
{
  $pkg = basename($dir);
  if(is_file("$dir/CHANGELOG"))
  {
    echo "\n################### Package '$pkg' ###################\n\n";
    echo trim(file_get_contents("$dir/CHANGELOG")) . "\n";
  }
}


?>