<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCompositeDispatcher.class.php 4995 2007-02-08 15:36:14Z pachanga $
 * @package    error
 */
lmb_require('limb/error/src/error/lmbErrorDispatcher.class.php');

/**
 * Composite exception Dispatcher
 */
class lmbCompositeDispatcher extends lmbErrorDispatcher
{
  /**
   * Exception Dispatchers
   *
   * @var array
   */
  protected $Dispatchers = array();

  function exceptionDispatch($exception)
  {
    foreach ( $this->Dispatchers as $Dispatcher )
    {
      $Dispatcher->exceptionDispatch($exception);
    }
    return 0;
  }

  function addDispatcher($Dispatcher)
  {
    $this->Dispatchers[] = $Dispatcher;
  }
}
?>