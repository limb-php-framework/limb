<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class lmbTestShellReporter.
 *
 * @package tests_runner
 * @version $Id: lmbTestShellReporter.class.php 6926 2008-04-13 07:15:26Z pachanga $
 */
class lmbTestShellReporter extends TextReporter
{
  protected $failed_tests = array();
  protected $first_group_test = false;

  function paintGroupStart($test_name, $size)
  {
    parent :: paintGroupStart($test_name, $size);

    //TODO: make this less fragile
    if(!$this->first_group_test && strpos($test_name, "Group test in") === 0)
    {
      $this->first_group_test = true;
      print "=========== $test_name ===========\n";
    }
  }

  function paintGroupEnd($test_name)
  {
    parent :: paintGroupEnd($test_name);
  }

  function paintCaseStart($test_name)
  {
    parent :: paintCaseStart($test_name);

    if(lmbTestOptions :: get('verbose'))
      print "======== $test_name ========\n";
  }

  function paintCaseEnd($test_name)
  {
    parent :: paintCaseEnd($test_name);

    print $this->getTestCaseProgress() . " of " . $this->getTestCaseCount() . " done({$test_name})\n";
  }

  function paintMethodStart($test_name)
  {
    parent :: paintMethodStart($test_name);

    if(lmbTestOptions :: get('verbose'))
      print "===== [$test_name] =====\n";
  }

  function paintMethodEnd($test_name)
  {
    parent :: paintMethodEnd($test_name);
  }

  function paintSkip($message)
  {
    parent :: paintSkip($message);
  }

  function paintHeader($test_name) 
  {
    //don't show any header since it's shown in paintGroupStart
  }

  function paintFooter($test_name) 
  {
    parent :: paintFooter($test_name);

    $runner = lmbTestRunner :: getCurrent();
    print 'Tests time: ' . $runner->getRuntime() . " sec.\n";
    if($memory = $runner->getMemoryUsage())
      print 'Tests memory usage: ' . $memory . " Mb.\n";

    if($this->failed_tests)
    {
      print "=========== FAILED TESTS  ===========\n";
      print implode("\n", $this->failed_tests) . "\n";
    }
  }

  function paintFail($message) 
  {
    parent :: paintFail($message);
    $this->failed_tests[] = $this->_extractFileNameAndLine($message);
  }

  function paintError($message) 
  {
    parent :: paintError($message);
    $this->failed_tests[] = $this->_extractFileNameAndLine($message);
  }

  function paintException($exception) 
  {
    parent::paintException($exception);
    $message = 'Unexpected exception of type [' . get_class($exception) .
            '] with message ['. $exception->getMessage() .
            '] in ['. $exception->getFile() .
            ' line ' . $exception->getLine() . ']';
    print "Exception " . $this->getExceptionCount() . "!\n$message\n";
    $breadcrumb = $this->getTestList();
    array_shift($breadcrumb);
    print "\tin " . implode("\n\tin ", array_reverse($breadcrumb));
    print "\n";
    print "Exception full message:\n";
    print $exception->__toString();
  }  

  protected function _extractFileNameAndLine($message)
  {
    $regex = "~.*\[([^\]]+)\s+line\s+(\d+)\].*~";
    preg_match($regex, $message, $m);
    return $m[1] . ':' . $m[2];
  }
}

