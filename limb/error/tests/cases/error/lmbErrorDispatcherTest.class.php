<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbErrorDispatcherTest.class.php 4995 2007-02-08 15:36:14Z pachanga $
 * @package    error
 */
lmb_require('limb/error/src/error/lmbErrorDispatcher.class.php');

class lmbErrorDispatcherTest extends UnitTestCase
{
  var $oldER;

  function setUp()
  {
    lmbErrorDispatcher::setErrorDispatcher(new lmbErrorDispatcher());
    $this->oldER = error_reporting();
  }

  function tearDown()
  {
    error_reporting($this->oldER);
    lmbErrorDispatcher::restoreErrorDispatcher();
  }

  function testErrorDispatching()
  {
    try
    {
      error_reporting(E_ALL);
      trigger_error('UserError', E_USER_NOTICE);
      $this->assertTrue(false);
    }
    catch ( lmbPhpErrorException $exception )
    {
      $this->assertEqual($exception->getMessage(), 'UserError');
    }
  }

  function testErrorDispatchingIsOff()
  {
    try
    {
      error_reporting(0);
      trigger_error('UserError', E_USER_ERROR);
      $this->assertTrue(true);
    }
    catch ( lmbPhpErrorException $exception )
    {
      $this->assertTrue(false);
    }
  }
}

?>