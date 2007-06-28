<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
require_once(dirname(__FILE__) . '/lmbTestGetopt.class.php');
require_once(dirname(__FILE__) . '/lmbTestRunner.class.php');
require_once(dirname(__FILE__) . '/lmbTestTreeGlobNode.class.php');

/**
 * class lmbTestShellUI.
 *
 * @package tests_runner
 * @version $Id: lmbTestShellUI.class.php 6021 2007-06-28 13:18:44Z pachanga $
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

$version

Usage:
  limb_unit OPTIONS <file|dir> [<file|dir>, <file|dir>, ...]
  Advanced SimpleTest unit tests runner. Finds and executes unit tests within filesystem.
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
    echo $this->help();
    exit($code);
  }

  protected function _version()
  {
    echo $this->_getVersion();
    exit();
  }

  protected function _getVersion()
  {
    list(, $number, $status) = explode('-', trim(file_get_contents(dirname(__FILE__) . '/../VERSION')));
    return "limb_unit-$number-$status";
  }

  static function getShortOpts()
  {
    return 'hvt:b:c:';
  }

  static function getLongOpts()
  {
    return array('help', 'version', 'config=', 'cover=', 'cover-report=', 'cover-exclude=');
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
        case 'v':
        case '--version':
          $this->_version();
          break;
        case 'c':
        case '--config':
          if(!@include_once(realpath($option[1])))
            $this->_error("Could not include configuration file '{$option[1]}'\n");
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

    if(!is_array($options[1]))
      $this->_help(1);

    if(!$cover_report_dir && defined('LIMB_TESTS_RUNNER_COVERAGE_REPORT_DIR'))
      $cover_report_dir = LIMB_TESTS_RUNNER_COVERAGE_REPORT_DIR;

    $runner = new lmbTestRunner();

    if($this->reporter)
      $runner->setReporter($this->reporter);

    if($cover_include)
      $runner->useCoverage($cover_include, $cover_exclude, $cover_report_dir);

    try
    {
      $node = new lmbTestTreeGlobNode($options[1]);
      $res = $runner->run($node);
    }
    catch(Exception $e)
    {
      $this->_error($e->getMessage());
    }

    echo $runner->getRuntime() . " sec.\n";

    return $res;
  }
}

?>