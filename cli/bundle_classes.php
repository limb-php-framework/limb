<?php
set_time_limit(0);

function make_bundle($classes)
{
  $filter_regex = '~lmb_require.+(' . implode('|', array_keys($classes)) . ')~';

  $bundle = '';
  foreach($classes as $class => $payload)
  {
    $bundle .= bundle_with_deps($classes, $class, $payload, $filter_regex);
  }
  return $bundle;
}

function bundle_with_deps($classes, $class, $payload, $filter_regex)
{
  static $bundled = array();

  if(isset($bundled[$class]))
    return '';
  $bundled[$class] = true;

  if(!$payload['deps'])
  {
    return process_file($payload['file'], $filter_regex);
  }
  else
  {
    $bundle = '';
    foreach($payload['deps'] as $new_class)
    {
      //ignoring built in classes
      if(isset($classes[$new_class]))
        $bundle .= bundle_with_deps($classes, $new_class, $classes[$new_class], $filter_regex);
    }
    $bundle .= process_file($payload['file'], $filter_regex);
    return $bundle;
  }
}

function process_file($file, $filter_regex)
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
  return implode("", $lines); 
}

$files = array();
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
  $deps = array();
  if(preg_match("~class\s+$class\s+.*(extends|implements)~", $content = file_get_contents($file)))
  {
    if(preg_match("~extends\s+(\w+)~", $content, $m))
      $deps[] = $m[1];
    if(preg_match("~implements\s+(\w+)~", $content, $m))
      $deps[] = $m[1];
  }

  $classes[$class] = array('file' => $file, 'deps' => $deps);
}

$bundle = make_bundle($classes);

echo "<?php\n" . $bundle;
