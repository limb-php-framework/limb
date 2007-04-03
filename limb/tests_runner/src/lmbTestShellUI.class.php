<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbTestShellUI.class.php 5457 2007-04-02 08:07:35Z pachanga $
 * @package    tests_runner
 */
require_once(dirname(__FILE__) . '/lmbTestGetopt.class.php');

class lmbTestShellUI
{
  protected $tree;
  protected $argv;

  function __construct($argv = null)
  {
    try
    {
      $this->argv = is_null($argv) ? lmbTestGetopt::readPHPArgv() : $argv;
    }
    catch(Exception $e)
    {
      echo('Fatal Error: ' . $e->getMessage() . "\n");
      exit(1);
    }
  }

  function help($script = '')
  {
    global $argv;
    if(!$script && isset($argv[0]))
      $script = basename($argv[0]);

    $usage = <<<EOD
Usage:
  $script [-c|--config,-h|--help] <file|dir>
  Executes SimpleTest based unit tests within filesystem
Options:
  -c, --config=/file.php  PHP configuration file path
  -h, --help              displays this help and exit

EOD;
    return $usage;
  }

  protected function _help($code = 0)
  {
    echo $this->help();
    exit($code);
  }

  static function getShortOpts()
  {
    return 'ht:b:c:';
  }

  static function getLongOpts()
  {
    return array('help', 'config=');
  }

  function run()
  {
    $res = $this->_doRun();
    exit($res ? 0 : 1);
  }

  function runEmbedded()
  {
    return $this->_doRun(false);
  }

  protected function _doRun($posix_opts = true)
  {
    $short_opts = self :: getShortOpts();
    $long_opts = self :: getLongOpts();

    try
    {
      if($posix_opts)
        $options = lmbTestGetopt::getopt($this->argv, $short_opts, $long_opts);
      else
        $options = lmbTestGetopt::getopt2($this->argv, $short_opts, $long_opts);
    }
    catch(Exception $e)
    {
      $this->_help(1);
    }

    $configured = false;

    foreach($options[0] as $option)
    {
      switch($option[0])
      {
        case 'h':
        case '--help':
          $this->_help(0);
          break;
        case 'c':
        case '--config':
          include_once(realpath($option[1]));
          $configured = true;
          break;
      }
    }

    if(!$configured && $config = getenv('LIMB_TESTS_RUNNER_CONFIG'))
      include_once($config);

    $res = true;
    $found = false;

    if(!isset($options[1][0]))
      $this->_help(1);

    include_once(dirname(__FILE__) . '/../simpletest.inc.php');

    foreach(glob($this->_normalizePath($options[1][0])) as $file)
    {
      $found = true;
      $root_dir = $this->_getRootDir($file);
      $node = $this->_mapFileToNode($root_dir, $file);
      $tree = $this->_initTree($root_dir);
      $res = $res & $tree->perform($node, $this->_getReporter());
    }

    if(!$found)
      $this->_help(1);

    return $res;
  }

  protected function _normalizePath($path)
  {
    if($this->_isAbsolutePath($path))
      return $path;
    else
      return $this->_getcwd() . DIRECTORY_SEPARATOR . $path;
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

  protected function _initTree($root_node)
  {
    require_once(dirname(__FILE__) . '/lmbTestTree.class.php');
    require_once(dirname(__FILE__) . '/lmbTestTreeDirNode.class.php');
    return new lmbTestTree(new lmbTestTreeDirNode($root_node));
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

  protected function _getReporter()
  {
    require_once(dirname(__FILE__) . '/lmbTestShellReporter.class.php');
    return new lmbTestShellReporter();
  }
}

?>