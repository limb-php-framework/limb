<?php
set_time_limit(0);

$files = array();
$classes = array();

array_shift($argv);
foreach($argv as $dir)
{
  $not = false;
  if($dir{0} == '!')
  {
    $not = true;
    $dir = substr($dir, 1);
  }

  $found = explode("\n", trim(`find $dir -type f -name "*.class.php" -o -name "*.interface.php"`));
  if($not)
    $files = array_diff($files, $found);
  else
    $files = array_merge($files, $found);
}

$files = array_filter($files);
$files = array_unique($files);

foreach($files as $file)
  $classes[] = reset(explode('.', basename($file))); 

$filter_regex = '~lmb_require.+(' . implode('|', $classes) . ')~';

$bundle = '';
foreach($files as $file)
{
  $lines = file($file);
  //removing <?php stuff
  array_shift($lines); 
  if(strpos($lines[count($lines)-1], '?>'))
    array_pop($lines);
  
  //filter unneccessary lmb_require's
  for($i=0;$i<count($lines);$i++)
  {
    if(preg_match($filter_regex, $lines[$i]))
      unset($lines[$i]);
  }

  $bundle .= implode("", $lines);
}

echo "<?php\n" . $bundle;
