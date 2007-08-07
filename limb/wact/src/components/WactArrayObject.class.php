<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class WactArrayObject.
 *
 * @package wact
 * @version $Id: WactArrayObject.class.php 6221 2007-08-07 07:24:35Z pachanga $
 */
class WactArrayObject implements ArrayAccess
{
  /**
   * @var ArrayAccess
   */
  protected $innerObject;

  protected $use_getter;

  /**
   * @param $object ArrayAccess/array
   */
  public function __construct($object = array())
  {
    if(is_object($object))
    {
      $this->innerObject = $object;
      $this->use_getter = method_exists($object, 'get');
      return;
    }

    if(is_null($object) || is_scalar($object))
      $object = array();

    $this->innerObject = $object;
  }

  function getInnerObject()
  {
    return $this->innerObject;
  }

  function offsetGet($key)
  {
    return $this->get($key);
  }

  function offsetSet($key, $value)
  {
    return $this->set($key, $value);
  }

  function offsetUnset($key)
  {
    unset($this->innerObject[$key]);
  }

  function offsetExists($key)
  {
    return isset($this->innerObject[$key]);
  }

  function export()
  {
    if(is_array($this->innerObject))
      return $this->innerObject;
    if(method_exists($this->innerObject, 'export'))
      return $this->innerObject->export();
    elseif(method_exists($this->innerObject, 'getArrayCopy'))
      return $this->innerObject->getArrayCopy();
  }

  function get($name)
  {
    if($this->use_getter)
      return $this->innerObject->get($name);
    else
    {
      if(isset($this->innerObject[$name]))
        return $this->innerObject[$name];
      else
        return NULL;
    }
  }

  function set($name, $value)
  {
    if($this->use_getter)
      return $this->innerObject->set($name, $value);

    return $this->innerObject[$name] = $value;
  }

  function __call($methodName, $arguments)
  {
    return call_user_func_array(array($this->innerObject, $methodName), $arguments);
  }
}

