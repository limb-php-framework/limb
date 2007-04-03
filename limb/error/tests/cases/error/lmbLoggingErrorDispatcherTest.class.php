<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbLoggingErrorDispatcherTest.class.php 4995 2007-02-08 15:36:14Z pachanga $
 * @package    error
 */
lmb_require('limb/error/src/error/lmbLoggingErrorDispatcher.class.php');

class lmbLoggingErrorDispatcherTest extends UnitTestCase
{
  public $oldEr;
  public $dispatcher;

  function setUp()
  {
    $this->dispatcher = new lmbLoggingErrorDispatcher();
    $this->dispatcher->addErrorLoggingRule(E_USER_ERROR | E_ERROR, LIMB_VAR_DIR.'/tmp/error.log');
    $this->dispatcher->addErrorLoggingRule(E_USER_NOTICE | E_NOTICE, LIMB_VAR_DIR.'/tmp/notice.log');
    $this->dispatcher->setExceptionLogFileName(LIMB_VAR_DIR.'/tmp/exception.log');
    lmbErrorDispatcher :: setErrorDispatcher($this->dispatcher);
    $this->oldEr = error_reporting(0);

    $this->_cleanUp();
  }

  function tearDown()
  {
    lmbErrorDispatcher::restoreErrorDispatcher();
    error_reporting($this->oldEr);

    $this->_cleanUp();
  }

  function _cleanUp()
  {
    @unlink(LIMB_VAR_DIR.'/tmp/notice.log');
    @unlink(LIMB_VAR_DIR.'/tmp/error.log');
    @unlink(LIMB_VAR_DIR.'/tmp/exception.log');
  }

  function testNoticeLogging()
  {
    trigger_error('notice_message1', E_USER_NOTICE);
    $this->assertWantedPattern("|notice_message1|", file_get_contents(LIMB_VAR_DIR.'/tmp/notice.log'));

    trigger_error('notice_message2', E_USER_NOTICE);
    $this->assertWantedPattern("|notice_message1(.*)notice_message2|s", file_get_contents(LIMB_VAR_DIR.'/tmp/notice.log'));
  }

  function testErrorLogging()
  {
    trigger_error('error_message1', E_USER_ERROR);
    $this->assertWantedPattern("|error_message|", file_get_contents(LIMB_VAR_DIR.'/tmp/error.log'));

    trigger_error('error_message2', E_USER_ERROR);
    $this->assertWantedPattern("|error_message1(.*)error_message2|s", file_get_contents(LIMB_VAR_DIR.'/tmp/error.log'));
  }

  function testExceptionLogging()
  {
    $this->dispatcher->exceptionDispatch(new Exception("exception_message1"));
    $this->assertWantedPattern("|exception_message1|", file_get_contents(LIMB_VAR_DIR.'/tmp/exception.log'));

    $this->dispatcher->exceptionDispatch(new Exception("exception_message2"));
    $this->assertWantedPattern("|exception_message1(.*)exception_message2|s", file_get_contents(LIMB_VAR_DIR.'/tmp/exception.log'));
  }
}

?>