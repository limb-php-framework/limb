<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDataspace.class.php 5616 2007-04-11 08:10:36Z pachanga $
 * @package    datasource
 */
lmb_require('limb/datasource/src/lmbSetInterface.interface.php');

class lmbSet implements lmbSetInterface, Iterator//should Iterator be a part of lmbSetInterface?
{
  protected $properties = array();

  function __construct($properties = array())
  {
    if(is_array($properties))
      $this->properties = $properties;
    else
      $this->properties = array();
  }

  function get($name)
  {
    if(isset($this->properties[$name]))
      return $this->properties[$name];
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

  function set($name, $value)
  {
    $this->properties[$name] = $value;
  }

  function remove($name)
  {
    if(isset($this->properties[$name]))
      unset($this->properties[$name]);
  }

  function removeAll()
  {
    $this->properties = array();
  }

  function reset()
  {
    return $this->removeAll();
  }

  function import($property_list)
  {
    $this->properties = $property_list;
  }

  function merge($property_list)
  {
    if(is_array($property_list) || is_a($property_list, 'ArrayAccess'))
    {
      foreach($property_list as $name => $value)
        $this->set($name, $value);
    }
  }

  function export()
  {
    return $this->properties;
  }

  function has($name)
  {
    return isset($this->properties[$name]);
  }

  function isEmpty()
  {
    return sizeof($this->properties) == 0;
  }

  function getPropertyList()
  {
    return array_keys($this->properties);
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
    return $this->valid;
  }

  function current()
  {
    return $this->current;
  }

  function next()
  {
    $this->current = next($this->properties);
    $this->valid = $this->current !== false;
  }

  function rewind()
  {
    $this->current = reset($this->properties);
    $this->valid = $this->current !== false;
  }

  function key()
  {
    return key($this->properties);
  }
}

?>