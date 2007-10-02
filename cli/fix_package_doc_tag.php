<?php
set_time_limit(0);

if($argc < 2)
{
  echo "Usage: fix_package_doc_tag <dir> [<pkg>]";
  exit(1);
}

$dir = $argv[1];
$pkg = isset($argv[2]) ? $argv[2] : basename($dir);
$files = explode("\n", trim(`find $dir -type f -name "*.php"`));

foreach($files as $file)
{
  echo "Processing $file...\n";
  $src = file_get_contents($file);
  $src = preg_replace('~@package\s+(\w+)~', "@package " . $pkg, $src);
  file_put_contents($file, $src);
}

