<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    core
 */
lmb_require('limb/core/src/lmbDelegate.class.php');

class lmbErrorGuard
{
  static protected $fatal_error_delegate;

  static function registerExceptionHandler()
  {
    $delegate = func_get_args();
    set_exception_handler(array(lmbDelegate :: objectify($delegate), 'invoke'));
  }

  static function registerFatalErrorHandler()
  {
    static $shutdown_registered = false;

    $delegate = func_get_args();
    self :: $fatal_error_delegate = lmbDelegate :: objectify($delegate);

    if(!$shutdown_registered)
    {
      register_shutdown_function(array('lmbErrorGuard', '_shutdownHandler'));
      $shutdown_registered = true;
    }
  }

  static function registerErrorHandler()
  {
    $delegate = func_get_args();
    set_error_handler(array(lmbDelegate :: objectify($delegate), 'invoke'));
  }

  static function _shutdownHandler()
  {
    if(!function_exists('error_get_last'))
      return;

    if(!$error = error_get_last())
      return;

    if($error['type'] == E_ERROR)
      self :: $fatal_error_delegate->invoke($error);
  }
}
?>