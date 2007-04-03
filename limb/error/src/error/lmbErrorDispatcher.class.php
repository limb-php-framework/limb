<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbErrorDispatcher.class.php 4995 2007-02-08 15:36:14Z pachanga $
 * @package    error
 */
lmb_require('limb/core/src/exception/lmbPhpErrorException.class.php');

class lmbErrorDispatcher
{
  /**
   * Exception Dispatch method
   */
  function exceptionDispatch($exception)
  {
    if ( $exception instanceof Exception )
    {
      echo "Exception catched type of " . get_class($exception) . "<BR/>\n";
      echo "Message: ".$exception->getMessage()."<BR/>\n";
      echo nl2br($exception->getTraceAsString());
    }
    else
    {
      echo "Huh? Really?";
    }
    exit();
  }

  /**
   * Error Dispatch method
   */
  function errorDispatch($errorNumber, $errorMessage, $fileName, $lineNumber, $arguments)
  {
    if ( (error_reporting() & $errorNumber) != 0 )
    {
      throw new lmbPhpErrorException($errorMessage, $errorNumber, $fileName, $lineNumber);
    }
  }

  /**
   * Setting exception/error handler functions
   *
   * @param lmbErrorDispatcher $handler lmbErrorDispatcher class
   */
  static function setErrorDispatcher($handler)
  {
    set_error_handler(array($handler, 'errorDispatch'));
    set_exception_handler(array($handler, 'exceptionDispatch'));
  }

  /**
   * Restore exception/error handler functions
   */
  static function restoreErrorDispatcher()
  {
    restore_error_handler();
    restore_exception_handler();
  }
}
?>