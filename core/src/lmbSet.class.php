<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/core/src/lmbSetInterface.interface.php');

/**
 * class lmbSet.
 *
 * @package core
 * @version $Id$
 */
class lmbSet implements lmbSetInterface
{
  function __construct($properties = array())
  {
    if(is_array($properties))
      $this->import($properties);
  }

  function get($name, $default = LIMB_UNDEFINED)
  {
    if(isset($this->$name) && !$this->_isGuarded($name))
      return $this->$name;

    if(LIMB_UNDEFINED !== $default)
      return $default;
  }

  function getInteger($name)
  {
    return (int)$this->get($name);
  }

  function getNumeric($name)
  {
    return (0 + $this->get($name));
  }

  function getArray($name)
  {
    if(!is_array($value = $this->get($name)))
      return array();

    return $value;
  }
  
  function getFloat($name)
  {
    return (float) str_replace(',', '.', $this->get($name)); 
  }

  function set($name, $value)
  {
    if(!$this->_isGuarded($name))
      $this->$name = $value;
  }

  function remove($name)
  {
    if(isset($this->$name) && !$this->_isGuarded($name))
      unset($this->$name);
  }

  function removeAll()
  {
    foreach($this->_getUnguardedVars($this) as $name => $var)
      $this->remove($name);
  }

  function reset()
  {
    return $this->removeAll();
  }

  function merge($values)
  {
    if(is_array($values) || ($values instanceof ArrayAccess))
    {
      foreach($values as $name => $value)
        $this->set($name, $value);
    }
  }

  function import($values)
  {
    $this->merge($values);
  }

  function export()
  {
    $exported = array();
    foreach($this->_getUnguardedVars($this) as $name => $var)
      $exported[$name] = $var;
    return $exported;
  }

  function has($name)
  {
    if(!$this->_isGuarded($name))
      return property_exists($this, $name);
  }

  function isEmpty()
  {
    return sizeof($this->_getUnguardedVars($this)) == 0;
  }

  function getPropertyList()
  {
    return array_keys($this->_getUnguardedVars());
  }

  protected function _getUnguardedVars()
  {
    $vars = array();
    foreach(get_object_vars($this) as $name => $var)
    {
      if(!$this->_isGuarded($name))
        $vars[$name] = $var;
    }
    return $vars;
  }

  protected function _isGuarded($property)
  {
    return $property && $property{0} == '_';
  }

  //ArrayAccess interface
  function offsetExists($offset)
  {
    return $this->has($offset);
  }

  function offsetGet($offset)
  {
    return $this->get($offset);
  }

  function offsetSet($offset, $value)
  {
    $this->set($offset, $value);
  }

  function offsetUnset($offset)
  {
    $this->remove($offset);
  }

  //Iterator interface
  function valid()
  {
    if(!$this->__valid)
    {
      //removing temporary helpers
      unset($this->__valid);
      unset($this->__properties);
      unset($this->__current);
      return false;
    }
    return true;
  }

  function current()
  {
    return $this->__current;
  }

  function next()
  {
    $this->__current = next($this->__properties);
    $this->__counter++;
    $this->__valid = $this->__size > $this->__counter;
  }

  function rewind()
  {
    $this->__properties = $this->_getUnguardedVars($this);
    $this->__current = reset($this->__properties);
    $this->__size = count($this->__properties);
    $this->__counter = 0;
    $this->__valid = $this->__size > $this->__counter;
  }

  function key()
  {
    return key($this->__properties);
  }
}



