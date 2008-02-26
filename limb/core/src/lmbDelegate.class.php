<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * Object form of invoking an object method
 * @package core
 * @version $Id: lmbDelegate.class.php 6805 2008-02-26 09:05:08Z cmz $
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
    
  function equal($delegate)
  {
    $delegate = self::objectify($delegate);
    if(!$this->isValid() || !$delegate->isValid())
      return false;
      
    $callback1 = $this->getCallback();
    $callback2 = $delegate->getCallback();
    
    $array_cb1 = is_array($callback1);
    $array_cb2 = is_array($callback2);
    if($array_cb1 != $array_cb2)
      return false;
      
    if($array_cb1)
    {
      return $callback1[0] === $callback2[0] && $callback1[1] == $callback2[1];
    }
    else
    {
      return $callback1 == $callback2;
    }    
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

