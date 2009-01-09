<?php
set_time_limit(0);
$dir = $argv[1];
$files = explode("\n", trim(`find $dir -type f -name "*.class.php" | grep src`));

foreach($files as $file)
{
  $src = file_get_contents($file);
  if(preg_match('~(?<=\*\/)(?:\n|\r|\r\n)class\s+(\w+)~', $src, $m))
    echo $m[1] . "\n";
}


