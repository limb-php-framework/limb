<?php
set_time_limit(0);

$files = array();
$root_classes = array();
$nonroot_classes = array();

function make_bundle($files, $filter_regex)
{
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
  return $bundle;
}

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

$classes = array();
foreach($files as $file)
{
  $class = reset(explode('.', basename($file))); 
  if(preg_match("~class\s+$class\s+.*(extends|implements)~", file_get_contents($file)))
    $nonroot_classes[] = $file;
  else
    $root_classes[] = $file;

  $classes[] = $class;
}

$filter_regex = '~lmb_require.+(' . implode('|', $classes) . ')~';

$bundle = '';

$bundle .= make_bundle($root_classes, $filter_regex);
$bundle .= make_bundle($nonroot_classes, $filter_regex);

echo "<?php\n" . $bundle;
