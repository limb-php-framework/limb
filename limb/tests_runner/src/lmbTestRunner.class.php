<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * class lmbTestRunner.
 *
 * @package tests_runner
 * @version $Id$
 */
class lmbTestRunner
{
  protected $test_paths = array();
  protected $reporter;
  protected $coverage;
  protected $coverage_reporter;
  protected $coverage_include;
  protected $coverage_exclude;
  protected $coverage_report_dir;
  protected $start_time = 0;
  protected $end_time = 0;

  function __construct($test_path)
  {
    if(!is_array($test_path))
      $this->test_paths[] = $test_path;
    else
      $this->test_paths = $test_path;
  }

  function setReporter($reporter)
  {
    $this->reporter = $reporter;
  }

  function useCoverage($coverage_include, $coverage_exclude, $coverage_report_dir)
  {
    $this->coverage_include = $coverage_include;
    $this->coverage_exclude = $coverage_exclude;
    $this->coverage_report_dir = $coverage_report_dir;
  }

  function run(&$tests_found = false)
  {
    $this->_startTimer();
    $this->_startCoverage();
    $res = $this->_runForTestPath($tests_found);
    $this->_endCoverage();
    $this->_stopTimer();
    return $res;
  }

  protected function _runForTestPath(&$tests_found = false)
  {
    require_once(dirname(__FILE__) . '/../simpletest.inc.php');

    $res = true;
    foreach($this->test_paths as $test_path)
    {
      foreach(glob($this->_normalizePath($test_path)) as $file)
      {
        $tests_found = true;
        $root_dir = $this->_getRootDir($file);
        $node = $this->_mapFileToNode($root_dir, $file);
        $tree = $this->_initTree($root_dir);
        $res = $res & $tree->perform($node, $this->_getReporter());
      }
    }
    return $res;
  }

  protected function _startTimer()
  {
    $this->start_time = microtime(true);
  }

  protected function _stopTimer()
  {
    $this->end_time = microtime(true);
  }

  function getRunTime()
  {
    return round($this->end_time - $this->start_time, 3);
  }

  protected function _startCoverage()
  {
    if(!$this->coverage_include)
      return;

    @define('__PHPCOVERAGE_HOME', dirname(__FILE__) . '/../lib/spikephpcoverage/src/');
    require_once(__PHPCOVERAGE_HOME . '/CoverageRecorder.php');
    require_once(__PHPCOVERAGE_HOME . '/reporter/HtmlCoverageReporter.php');

    $this->coverage_reporter = new HtmlCoverageReporter("Code Coverage Report", "",
                                                        $this->coverage_report_dir);

    $include_paths = explode(';', $this->coverage_include);
    $exclude_paths = explode(';', $this->coverage_exclude);
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
    if(!$this->reporter)
    {
      if($this->_simpleTestDefaultReporterInstalled())
      {
        require_once(dirname(__FILE__) . '/lmbTestShellReporter.class.php');
        SimpleTest :: prefer(new lmbTestShellReporter());
      }
      return clone(SimpleTest :: preferred(array('SimpleReporter', 'SimpleReporterDecorator')));
    }
    else
      return clone($this->reporter);
  }

  protected function _simpleTestDefaultReporterInstalled()
  {
    $reporter = SimpleTest :: preferred(array('SimpleReporter', 'SimpleReporterDecorator'));
    return get_class($reporter) == 'DefaultReporter';
  }
}

?>