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
 * @version $Id: lmbTestShellReporter.class.php 6531 2007-11-20 12:22:57Z serega $
 */
class lmbTestShellReporter extends TextReporter
{
  function paintCaseEnd($test_name)
  {
    parent :: paintCaseEnd($test_name);

    echo $this->getTestCaseProgress() . " of " . $this->getTestCaseCount() . " done({$test_name})\n";
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
}

