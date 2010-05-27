<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/core/src/lmbErrorGuard.class.php');

class lmbErrorGuardTest extends UnitTestCase
{

  function setUp()
  {
    lmbErrorGuard::registerErrorHandler('lmbErrorGuard', 'convertErrorsToExceptions');
  }

  function tearDown()
  {
    restore_error_handler();
  }

  function testConvertingErrorsToExceptions()
  {
    try {
      strpos();
      $this->fail('Warning should be converted to an Exception');
    } catch(lmbException $e) {
      $this->pass();
    }
  }

  function testDisabledErrorReportingDoesNotThrowExceptionOnError()
  {
    $old_reporting = error_reporting();
    error_reporting(0);

    try {
      strpos();
    } catch(lmbException $e) {
      $this->fail('No converted from error exception expected!');
    }

    error_reporting($old_reporting);
  }
}
