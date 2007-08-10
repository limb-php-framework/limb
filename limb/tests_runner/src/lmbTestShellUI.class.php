<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
require_once(dirname(__FILE__) . '/lmbTestGetopt.class.php');

/**
 * class lmbTestShellUI.
 *
 * @package tests_runner
 * @version $Id: lmbTestShellUI.class.php 6231 2007-08-10 06:08:05Z pachanga $
 */
class lmbTestShellUI
{
  protected $test_path;
  protected $argv;
  protected $posix_opts = true;
  protected $call_exit = true;
  protected $reporter;

  function __construct($argv = null)
  {
    try
    {
      $this->argv = is_array($argv) ? $argv : lmbTestGetopt::readPHPArgv();
    }
    catch(Exception $e)
    {
      $this->_error($e->getMessage() . "\n");
    }
  }

  function setReporter($reporter)
  {
    $this->reporter = $reporter;
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
    $version = $this->_getVersion();

    $usage = <<<EOD
Usage:
  limb_unit [OPTIONS] <file|dir> [<file1|dir1>, ... <fileN|dirN>]
  Advanced SimpleTest unit tests runner. Finds and executes unit tests within filesystem.
Arguments:
  <file|dir> [<file1|dir1>, ... <fileN|dirN>] - a list of files/directories, globs are supported(e.g. '*')
Options:
  -h, --help                      Displays this help and exit
  -c, --config=/file.php          PHP configuration file path
  -I, --include='filter1;filter2' Sets file filters used for including test files during
                                  recursive traversal of directories.
                                  '*Test.class.php;*test.php;*Test.php' by default.
  -C, --cover=path1;path2         Sets paths delimitered with ';' which should be analyzed
                                  for test coverage(requires XDebug extension!)
  --cover-report=dir              Sets coverage report directory
  --cover-exclude=path1;path2     Sets paths delimitered with ';' which should be excluded
                                  from coverage analysis

$version

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
    echo "ERROR: $message\n\n";
    echo $this->_getVersion();
    echo "\n";
    exit($code);
  }

  protected function _version()
  {
    echo $this->_getVersion() . "\n";
    exit();
  }

  protected function _getVersion()
  {
    list(, $number, $status) = explode('-', trim(file_get_contents(dirname(__FILE__) . '/../VERSION')));
    $version = "limb_unit-$number-$status";
    
    if(is_dir(dirname(__FILE__) . '/.svn'))
      $version .= "-dev";

    return $version;
  }

  static function getShortOpts()
  {
    return 'hvI:c:C:';
  }

  static function getLongOpts()
  {
    return array('help', 'version', 'include=', 'config=', 'cover=', 'cover-report=', 'cover-exclude=');
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
    $short_opts = self :: getShortOpts();
    $long_opts = self :: getLongOpts();

    try
    {
      if($this->posix_opts)
        $options = lmbTestGetopt :: getopt($this->argv, $short_opts, $long_opts);
      else
        $options = lmbTestGetopt :: getopt2($this->argv, $short_opts, $long_opts);
    }
    catch(Exception $e)
    {
      $this->_help(1);
    }

    lmbTestGetopt :: defineConstants($this->argv);

    $config_file = null;
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
        case 'v':
        case '--version':
          $this->_version();
          break;
        case 'c':
        case '--config':
          $config_file = $option[1];
          break;
        case 'I':
        case '--include':
          @define('LIMB_TESTS_RUNNER_FILE_FILTER', $option[1]);
          break;
        case 'C':
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

    if($config_file)
    {
      if(!@include_once(realpath($config_file)))
        $this->_error("Could not include configuration file '$config_file'\n");
    }
    else if($config_file = getenv('LIMB_TESTS_RUNNER_CONFIG'))
    {
      if(!@include_once($config_file))
        $this->_error("Could not include configuration file specified in LIMB_TESTS_RUNNER_CONFIG env. variable as '$config_file'\n");
    }

    if(!is_array($options[1]) || !count($options[1]))
      $paths = array('.');
    else
      $paths = $options[1];

    if(!$cover_report_dir && defined('LIMB_TESTS_RUNNER_COVERAGE_REPORT_DIR'))
      $cover_report_dir = LIMB_TESTS_RUNNER_COVERAGE_REPORT_DIR;

    require_once(dirname(__FILE__) . '/lmbTestRunner.class.php');
    $runner = new lmbTestRunner();

    if($this->reporter)
      $runner->setReporter($this->reporter);

    if($cover_include)
      $runner->useCoverage($cover_include, $cover_exclude, $cover_report_dir);

    try
    {
      require_once(dirname(__FILE__) . '/lmbTestTreeGlobNode.class.php');
      $node = new lmbTestTreeGlobNode($paths);
      $res = $runner->run($node);
    }
    //it's an exception which is used to pass user errors up to the interface,
    //we don't need to show backtrace in this case, only error message
    catch(lmbTestUserException $e)
    {
      $this->_error($e->getMessage());
    }
    catch(Exception $e)
    {
      $this->_error($e->__toString());
    }

    echo $runner->getRuntime() . " sec.\n";

    return $res;
  }
}


