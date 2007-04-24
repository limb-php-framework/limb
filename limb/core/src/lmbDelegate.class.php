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

/**
* Object form of invoking an object method
*/
class lmbDelegate
{
  /**
  * @var mixed PHP callback
  */
  protected $php_callback;
  /**
   * @var bool cached validity check result
   */
  protected $is_valid;

  /**
  * Constructor.
  * @param mixed Object which method will be invoked
  * @param string Object method to call
  */
  function __construct($object, $method = null)
  {
    if(is_array($object))
    {
      $this->php_callback = $object;
    }
    else
    {
      if(!$method)
        $this->php_callback = $object;
      else
        $this->php_callback = array($object, $method);
    }
  }

  /**
   * Returns PHP callback
   * @return mixed PHP callback
   */
  function getCallback()
  {
    return $this->php_callback;
  }

  /**
  * Invokes object method with $args
  */
  function invoke()
  {
    if(!$this->isValid())
      throw new lmbException("Invalid callback", array('callback' => $this->php_callback));

    $args = func_get_args();
    return call_user_func_array($this->php_callback, $args);
  }

  function invokeArray($args = array())
  {
    if(!$this->isValid())
      throw new lmbException("Invalid callback", array('callback' => $this->php_callback));
    return call_user_func_array($this->php_callback, $args);
  }

  function isValid()
  {
    if($this->is_valid !== null)
      return $this->is_valid;
    $this->is_valid = is_callable($this->php_callback);
    return $this->is_valid;
  }

  static function objectify($delegate)
  {
    if(is_object($delegate) && $delegate instanceof lmbDelegate)
      return $delegate;
    return new lmbDelegate($delegate);
  }

  /**
  * Invokes all delegates in a list with some args
  * @param array Array of lmbDelegate objects that
  * @param array Invoke arguments
  */
  static function invokeAll($list, $args = array())
  {
    foreach($list as $item)
      $item->invokeArray($args);
  }

  /**
  * Invokes delegates in a list one by one. Stops invoking if delegate return a not null result.
  * @param array Array of lmbDelegate objects
  * @param array Invoke arguments
  */
  static function invokeChain($list, $args = array())
  {
    foreach($list as $item)
    {
      $result = $item->invokeArray($args);
      if(!is_null($result))
        return $result;
    }
  }
}
?>
