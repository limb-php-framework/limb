<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
require_once(dirname(__FILE__) . '/lmbTestRunner.class.php');

/**
 * class lmbTestFileRunner.
 *
 * @package tests_runner
 * @version $Id$
 */
class lmbTestFileRunner extends lmbTestRunner
{
  protected $tests_found = false;

  function runForFiles($file_path)
  {
    if(!is_array($file_path))
      $test_paths[] = $file_path;
    else
      $test_paths = $file_path;

    require_once(dirname(__FILE__) . '/../simpletest.inc.php');

    $this->_startTimer();
    $this->_startCoverage();

    try
    {
      $res = $this->_doRunForFiles($test_paths);
    }
    catch(Exception $e)
    {
      $this->_showException($e);
      return false;
    }

    $this->_endCoverage();
    $this->_stopTimer();
    return $res;
  }

  protected function _doRunForFiles($test_paths)
  {
    $this->tests_found = false;
    $res = true;
    foreach($test_paths as $test_path)
    {
      foreach(glob($this->_normalizePath($test_path)) as $file)
      {
        $this->tests_found = true;
        $root_dir = $this->_getRootDir($file);
        $path = $this->_mapFileToNode($root_dir, $file);
        $node = $this->_initDirNode($root_dir);
        $res = $res & $this->_doRun($node, $path);
      }
    }
    return $res;
  }

  function testsFound()
  {
    return $this->tests_found;
  }

  protected function _normalizePath($path)
  {
    if($this->_isAbsolutePath($path))
      return rtrim($path, '\\/');
    else
      return rtrim($this->_getcwd() . DIRECTORY_SEPARATOR . $path, '\\/');
  }

  /**
   * Due to require_once error in PHP before 5.2 version this method 'strtolowers' paths under windows
   */
  protected function _getcwd()
  {
    $wd = getcwd();
    //win32 check
    if(DIRECTORY_SEPARATOR == '\\')
      $wd = strtolower($wd);
    return $wd;
  }

  protected function _isAbsolutePath($path)
  {
    return $path{0} == '/' || preg_match('~^[a-z]:~i', $path);
  }

  protected function _initDirNode($dir)
  {
    require_once(dirname(__FILE__) . '/lmbTestTreeDirNode.class.php');
    return new lmbTestTreeDirNode($dir);
  }

  protected function _mapFileToNode($root_dir, $file)
  {
    require_once(dirname(__FILE__) . '/lmbFile2TestNodeMapper.class.php');
    $mapper = new lmbFile2TestNodeMapper();
    return $mapper->map($root_dir, $file);
  }

  protected function _getRootDir($file)
  {
    $path_items = explode(DIRECTORY_SEPARATOR, $file);
    //windows/linux filesystem paths style check
    return empty($path_items[0]) ?
              DIRECTORY_SEPARATOR . $path_items[1] :  //unix
              $path_items[0] . DIRECTORY_SEPARATOR;   //windows
  }
}

?>