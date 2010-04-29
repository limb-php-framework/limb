<?php

require_once('limb/core/common.inc.php');

class lmbBundler
{
  static public $regexp_for_requires = '/^\s*((lmb_)?require(_once)?)\s*\([\'|\"]([a-zA-z0-9\-_.\/]+).*$/m';
  public $include_paths;
  protected $_includes = array();
  protected $_verbose;

  function __construct($include_path, $verbose = false)
  {
    $this->include_paths = explode(PATH_SEPARATOR, $include_path);
    foreach($this->include_paths as $key => $path)
    {
      if(!strlen($path))
      unset($this->include_paths[$key]);
    }

    $this->_verbose = $verbose;
  }

  static function getDependenciesFromFile($file)
  {
    $file = trim($file);
    $matches = array();
    preg_match_all(self::$regexp_for_requires, file_get_contents($file), $matches);
    if(!isset($matches[4]))
      return array();

    $matches = array_unique($matches[4]);
    //array_walk($matches,'array_trim');
    return $matches;
  }

  function resolvePath($file)
  {
    if($this->isPathAbsolute($file))
    return $file;

    $file_path = false;
    foreach($this->include_paths as $include_path)
    {
      $full_path = $include_path . '/' . $file;

      if(file_exists($full_path)) {
        return $full_path;
      }
    }

    return $file_path;
  }

  function isPathAbsolute($path)
  {
    //return ('/' === $path{0});
    return lmb_is_path_absolute($path);
  }

  function add($file)
  {
    $file = realpath(trim($file));
    if(in_array($file, $this->_includes))
    {
      if($this->_verbose)
        echo 'exist: '.$file.PHP_EOL;
      return;
    }

    if($this->_verbose)
      echo 'add: '.$file.PHP_EOL;

    $deps = self::getDependenciesFromFile($file);

    foreach($deps as $dependency)
    {
      $dependency_path = $this->resolvePath($dependency);

      if(in_array($dependency_path, $this->_includes))
        continue;

      if($this->_verbose)
        echo 'dependency: '.$dependency_path.PHP_EOL;

      $this->add($dependency_path);
    }

    if($this->_verbose)
      echo 'pushed: '.$file.PHP_EOL;

    if(!in_array($file, $this->_includes))
      array_push($this->_includes, $file);
  }

  function getIncludes()
  {
    return $this->_includes;
  }

  static function cleanUpFile($file)
  {
    $lines = file(trim($file));

     if(!is_array($lines))
        return '';
    //removing <?php stuff
    array_shift($lines);

    if(false !== strpos($lines[count($lines)-1], '?>'))
      array_pop($lines);

    $lines_count = count($lines);

    //filter unneccessary require's
    for($i = 0;$i<$lines_count;$i++)
    {
      if(preg_match(self::$regexp_for_requires, $lines[$i]))
        unset($lines[$i]);
    }

    return implode("", $lines);
  }

  function makeBundle($without_tags = false)
  {
    echo $without_tags ? '' : "<?php\n";
    foreach(array_unique($this->_includes) as $file)
    {
//      echo "//-----------".$file."------------".PHP_EOL;
      echo self::cleanUpFile($file);
    }
  }
}
