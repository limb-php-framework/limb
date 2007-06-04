<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbValidationExceptionTest.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */
lmb_require('limb/validation/src/lmbErrorList.class.php');
lmb_require('limb/validation/src/exception/lmbValidationException.class.php');

class lmbValidationExceptionTest extends UnitTestCase
{
  function testErrorListAttachedToErrorMessage()
  {
    $error_list = new lmbErrorList();
    $error_list->addError('error1');
    $error_list->addError('error2');
    $exception = new lmbValidationException('Message.', $error_list, $params = array());
    $this->assertEqual($exception->getMessage(), 'Message. Errors list : error1, error2');
  }
}
?>