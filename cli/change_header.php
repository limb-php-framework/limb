<?php
set_time_limit(0);

if($argc < 3)
{
  echo "Usage: change_header <dir> <pattern_file> [<replacement_file>]";
  exit(1);
}

$dir = $argv[1];
$pattern_file = $argv[2];
$replace_file = isset($argv[3]) ? $argv[3] : null;
$files = explode("\n", trim(`find $dir -type f -name "*.php"`));

$old = trim(file_get_contents($pattern_file));
$new = $replace_file ? trim(file_get_contents($replace_file)) : '';

$regex = make_regex($old);

foreach($files as $file)
{
  $src = file_get_contents($file);
  $changed = preg_replace("~$regex~", $new, $src);
  if($src != $changed)
  {
    echo "changing $file...\n";
    file_put_contents($file, $changed);
  }
}

function make_regex($src)
{
  $regex = '';
  $items = preg_split('~<REGEX>(.*?)</REGEX>~', $src, -1, PREG_SPLIT_DELIM_CAPTURE);
  $c = 0;
  foreach($items as $item)
  {
    if($c % 2 == 0)
    {
      $regex .= preg_quote($item, '\\');
      $regex = preg_replace("~\n~", '(?:\n|\r|\r\n)', $regex);
    }
    else
      $regex .= $item;
    $c++;
  }
  return $regex;
}

