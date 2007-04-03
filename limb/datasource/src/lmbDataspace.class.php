<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDataspace.class.php 4992 2007-02-08 15:35:40Z pachanga $
 * @package    datasource
 */
lmb_require('limb/datasource/src/lmbDatasource.interface.php');

class lmbDataspace implements ArrayAccess, lmbDatasource
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

  function getByPath($path)
  {
    if(($pos = strpos($path, '.')) === FALSE)
      return $this->get($path);

    $var =& $this->_getPathReference($pos, $path); // can modify $path
    if(is_object($var))
    {
       if(method_exists($var, 'isDatasource'))
         return $var->getByPath($path);
       else
         return $var->$path;
    }
    elseif(is_array($var))
      if(isset($var[$path]))
        return $var[$path];
      else
        return null;
  }

  function setByPath($path, $value)
  {
    if(($pos = strpos($path, '.')) === FALSE)
      return $this->set($path, $value);

    $var =& $this->_getPathReference($pos, $path, TRUE); // can modify $path
    if(is_object($var))
    {
       if(method_exists($var, 'isDatasource'))
         $var->setByPath($path, $value);
       else
         $var->$path = $value;
    }
    else if(is_array($var))
    {
      $var[$path] = $value;
    }
    else
    {
      $var = array();
      $var[$path] = $value;
    }
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
    if(is_array($property_list) || is_a($property_list, 'datasource'))
    {
      foreach($property_list as $name => $value)
        $this->set($name, $value);
    }
  }

  function export()
  {
    return $this->properties;
  }

  function isDatasource()
  {
    return true;
  }

  function hasProperty($name)
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
    return $this->hasProperty($offset);
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

  function &_getPathReference($pos, &$path)
  {
    $var =& $this->properties;
    do
    {
      $key = substr($path, 0, $pos);
      if(is_object($var))
      {
        if(method_exists($var, 'isDatasource'))
          return $var;
        else
          $var =& $var->$key;
      }
      else
      {
        if(is_array($var) && isset($var[$key]))
          $var =& $var[$key];
        else
          $var = NULL;
      }
      $path = substr($path, $pos + 1);
      $pos = strpos($path, '.');
    } while ($pos !== FALSE);

    return $var;
  }
}

?>