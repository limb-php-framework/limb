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
  protected $reporter;
  protected $coverage;
  protected $coverage_reporter;
  protected $coverage_include;
  protected $coverage_exclude;
  protected $coverage_report_dir;

  protected $start_time = 0;
  protected $end_time = 0;
  protected $start_memory_usage = 0;
  protected $end_memory_usage = 0;

  function setReporter($reporter)
  {
    $this->reporter = $reporter;
  }

  function useCoverage($coverage_include, $coverage_exclude, $coverage_report_dir)
  {
    if(is_string($coverage_include))
      $this->coverage_include = explode(';', $coverage_include);

    if(is_string($coverage_exclude))
      $this->coverage_exclude = explode(';', $coverage_exclude);

    $this->coverage_report_dir = $coverage_report_dir;
  }

  function run($root_node, $path='/')
  {
    require_once(dirname(__FILE__) . '/../simpletest.inc.php');

    $this->_startStatsCheck();
    $this->_startCoverage();

    $res = $this->_doRun($root_node, $path);

    $this->_endCoverage();
    $this->_stopStatsCheck();
    return $res;
  }

  protected function _doRun($node, $path)
  {
    if(!$sub_node = $node->findChildByPath($path))
      throw new Exception("Test node '$path' not found!");

    $test = $sub_node->createTestCase();
    return $test->run($this->_getReporter());
  }

  protected function _startStatsCheck()
  {
    $this->start_time = microtime(true);
    $this->start_memory_usage = memory_get_usage();
  }

  protected function _stopStatsCheck()
  {
    $this->end_time = microtime(true);
    $this->end_memory_usage = memory_get_usage();
  }

  function getRunTime()
  {
    return round($this->end_time - $this->start_time, 3);
  }

  function getMemoryUsage()
  {
    return round(($this->end_memory_usage - $this->start_memory_usage) / 1024 /1024, 3);
  }

  protected function _startCoverage()
  {
    if(!$this->coverage_include)
      return;

    @define('__PHPCOVERAGE_HOME', dirname(__FILE__) . '/../lib/spikephpcoverage/src/');
    require_once(__PHPCOVERAGE_HOME . '/CoverageRecorder.php');

    if($this->coverage_report_dir)
    {
      require_once(__PHPCOVERAGE_HOME . '/reporter/HtmlCoverageReporter.php');
      $this->coverage_reporter = new HtmlCoverageReporter("limb_unit coverage report", "", $this->coverage_report_dir);
    }
    else
    {
      //this reporter just collects stats and doesn't write anything, only prints summary
      require_once(dirname(__FILE__) . '/lmbSummaryCoverageReporter.class.php');
      $this->coverage_reporter = new lmbSummaryCoverageReporter();
    }

    $this->coverage = new CoverageRecorder($this->coverage_include, $this->coverage_exclude, $this->coverage_reporter);
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
