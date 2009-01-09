<?php
set_time_limit(0);
$dir = $argv[1];
$pkg = $argv[2];
$files = explode("\n", trim(`find $dir -type f -name "*.php" | grep -v /lib/ | grep -v /settings/ | grep -v /shared/ | grep -v /www/ | grep -v /tests/`));
$header = str_replace('$package$', $pkg, trim(file_get_contents(dirname(__FILE__) . '/header.current')));
$regex = make_regex(trim(file_get_contents(dirname(__FILE__) . '/header.current.pattern')));

foreach($files as $file)
{
  $src = file_get_contents($file);
  if(!preg_match("~$regex~", $src))
  {
    echo "Processing $file...\n";
    $src = preg_replace("~<\?php~", "<?php\n$header\n", $src);
    file_put_contents($file, $src);
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

