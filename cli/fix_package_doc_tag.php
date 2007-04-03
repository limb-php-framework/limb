<?php
set_time_limit(0);
$dir = $argv[1];
$pkg = $argv[2];
$files = explode("\n", trim(`find $dir -type f -name "*.php"`));

foreach($files as $file)
{
  echo "Processing $file...\n";
  $src = file_get_contents($file);
  $src = preg_replace('~@package\s+(\?\?\?|\n)~', "@package    " . $pkg, $src);
  file_put_contents($file, $src);
}

?>