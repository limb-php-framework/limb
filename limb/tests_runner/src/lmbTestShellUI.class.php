<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbTestShellUI.class.php 5663 2007-04-16 08:56:20Z pachanga $
 * @package    tests_runner
 */
require_once(dirname(__FILE__) . '/lmbTestGetopt.class.php');

class lmbTestShellUI
{
  protected $test_path;
  protected $argv;
  protected $posix_opts = true;
  protected $call_exit = true;
  protected $coverage;
  protected $coverage_reporter;

  function __construct($magic_args = null)
  {
    if(is_string($magic_args))
    {
      $this->test_path = $magic_args;
    }
    else
    {
      try
      {
        $this->argv = is_array($magic_args) ? $magic_args : lmbTestGetopt::readPHPArgv();
      }
      catch(Exception $e)
      {
        $this->_error($e->getMessage() . "\n");
      }
    }
  }

  function setPosixMode($flag = true)
  {
    $this->posix_opts = $flag;
  }

  function exitAfterRun($flag = true)
  {
    $this->call_exit = $flag;
  }

  function help($script = '')
  {
    global $argv;
    if(!$script && isset($argv[0]))
      $script = basename($argv[0]);

    $usage = <<<EOD
Usage:
  $script OPTIONS <file|dir>
  Executes SimpleTest based unit tests within filesystem
Options:
  -c, --config=/file.php        PHP configuration file path
  -h, --help                    Displays this help and exit
  --cover=path1;path2           Sets paths delimitered with ';' which should be analyzed for coverage
  --cover-report=dir            Sets coverage report directory
  --cover-exclude=path1;path2   Sets paths delimitered with ';' which should be excluded from coverage analysis

EOD;
    return $usage;
  }

  protected function _help($code = 0)
  {
    echo $this->help();
    exit($code);
  }

  protected function _error($message, $code = 1)
  {
    echo "ERROR: $message";
    echo $this->_help();
    exit($code);
  }

  static function getShortOpts()
  {
    return 'ht:b:c:';
  }

  static function getLongOpts()
  {
    return array('help', 'config=', 'cover=', 'cover-report=', 'cover-exclude=');
  }

  function run()
  {
    $res = $this->_doRun();

    if($this->call_exit)
      exit($res ? 0 : 1);
    else
      return $res;
  }

  function runEmbedded()
  {
    return $this->_doRun();
  }

  protected function _doRun()
  {
    if($this->test_path)
      return $this->_runForTestPath($this->test_path);
    else
      return  $this->_runForArgv($this->argv);
  }

  protected function _runForArgv($argv)
  {
    $short_opts = self :: getShortOpts();
    $long_opts = self :: getLongOpts();

    try
    {
      if($this->posix_opts)
        $options = lmbTestGetopt::getopt($argv, $short_opts, $long_opts);
      else
        $options = lmbTestGetopt::getopt2($argv, $short_opts, $long_opts);
    }
    catch(Exception $e)
    {
      $this->_help(1);
    }

    $configured = false;
    $cover_include = '';
    $cover_exclude = '';
    $cover_report_dir = null;

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
        case '--cover':
          $cover_include = $option[1];
          break;
        case '--cover-report':
          $cover_report_dir = $option[1];
          break;
        case '--cover-exclude':
          $cover_exclude = $option[1];
          break;
      }
    }

    if(!$configured && $config = getenv('LIMB_TESTS_RUNNER_CONFIG'))
      include_once($config);

    $res = true;
    $found = false;

    if(!isset($options[1][0]))
      $this->_help(1);

    if(!$cover_report_dir && defined('LIMB_TESTS_RUNNER_COVERAGE_REPORT_DIR'))
      $cover_report_dir = LIMB_TESTS_RUNNER_COVERAGE_REPORT_DIR;

    $this->_startCoverage($cover_include, $cover_exclude, $cover_report_dir);

    $res = $this->_runForTestPath($options[1][0], $found);

    $this->_endCoverage();

    if(!$found)
      $this->_help(1);

    return $res;
  }

  protected function _runForTestPath($path, &$found = false)
  {
    include_once(dirname(__FILE__) . '/../simpletest.inc.php');

    $res = true;
    foreach(glob($this->_normalizePath($path)) as $file)
    {
      $found = true;
      $root_dir = $this->_getRootDir($file);
      $node = $this->_mapFileToNode($root_dir, $file);
      $tree = $this->_initTree($root_dir);
      $res = $res & $tree->perform($node, $this->_getReporter());
    }
    return $res;
  }

  protected function _startCoverage($cover_include, $cover_exclude='', $cover_report_dir=null)
  {
    if(!$cover_include)
      return;

    if(!$cover_report_dir)
      $this->_error("Coverage report directory is required(try --cover-report option)\n");

    @define('__PHPCOVERAGE_HOME', dirname(__FILE__) . '/../lib/spikephpcoverage/src/');
    require_once(__PHPCOVERAGE_HOME . '/CoverageRecorder.php');
    require_once(__PHPCOVERAGE_HOME . '/reporter/HtmlCoverageReporter.php');

    $this->coverage_reporter = new HtmlCoverageReporter("Code Coverage Report", "", $cover_report_dir);

    $include_paths = explode(';', $cover_include);
    $exclude_paths = explode(';', $cover_exclude);
    $this->coverage = new CoverageRecorder($include_paths, $exclude_paths, $this->coverage_reporter);
    $this->coverage->startInstrumentation();
  }

  protected function _endCoverage()
  {
    if($this->coverage)
    {
      $this->coverage->stopInstrumentation();
      $this->coverage->generateReport();
      $this->coverage_reporter->printTextSummary();
    }
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