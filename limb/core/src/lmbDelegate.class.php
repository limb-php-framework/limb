<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDelegate.class.php 5143 2007-02-20 21:40:01Z serega $
 * @package    core
 */
lmb_require('limb/core/src/lmbBaseDelegate.interface.php');
lmb_require('limb/core/src/lmbDelegateList.class.php');

/**
* Object form of invoking an object method
*/
class lmbDelegate implements lmbBaseDelegate
{
  /**
  * @var mixed Object which method will be invoked
  */
  protected $object;
  /**
  * @var string Method name to call
  */
  protected $method;

  /**
  * Constructor.
  * @param mixed Object which method will be invoked
  * @param string Object method to call
  */
  function __construct(&$object, $method)
  {
    $this->object =& $object;
    $this->method = $method;
  }

  /**
  * Invokes object method with $args
  * @see lmbBaseDelegate :: invoke
  */
  function invoke($args)
  {
    return call_user_func_array(array(&$this->object, $this->method), $args);
  }
}
?>
