<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCompositeDispatcherTest.class.php 4995 2007-02-08 15:36:14Z pachanga $
 * @package    error
 */
lmb_require('limb/error/src/error/lmbCompositeDispatcher.class.php');
lmb_require('limb/error/src/error/lmbErrorDispatcher.class.php');

Mock::generate('lmbErrorDispatcher', 'MockErrorDispatcher');

class lmbCompositeDispatcherTest extends UnitTestCase
{
  function testCompositeDispatcher()
  {
    $exception = new Exception("TestError");

    $firstHandler = new MockErrorDispatcher();
    $firstHandler->expectOnce('exceptionDispatch', array($exception));

    $secondHandler = new MockErrorDispatcher();
    $secondHandler->expectOnce('exceptionDispatch', array($exception));

    $composite = new lmbCompositeDispatcher();
    $composite->addDispatcher($firstHandler);
    $composite->addDispatcher($secondHandler);

    $composite->exceptionDispatch($exception);
  }
}

?>