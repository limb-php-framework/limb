<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/core/src/lmbSetInterface.interface.php');
/**
 * Generic container for data with magic accessors.
 *
 * NOTE: All properties are stored as attributes of an object. If you need to
 * make certain properties 'guarded', you should prefix these properties
 * with "_" symbol.
 *
 * <b>Basic usage</b>
 * <code>
 * //filling object
 * $obj = new lmbObject(array('foo' => 'bar'));
 * //the getter calls below are equal
 * $obj->get('foo');
 * $obj->getFoo();
 * $obj['foo'];
 * //the setter calls below are equal
 * $obj->set('foo', 'hey');
 * $obj->setFoo('hey');
 * $obj['foo'] = 'hey';
 * </code>
 *
 * <b>Mapping generic getters to fine-grained methods</b>
 * <code>
 * class Foo extends lmbObject
 * {
 *    function getBar()
 *    {
 *      return 'bar';
 *    }
 * }
 * $foo = new Foo();
 * //the call below will be magically mapped to getBar() method
 * //this can be very useful for WACT templates, e.g. {$bar} in
 * //template will trigger the same method getBar() as well
 * $foo->get('bar');
 * </code>
 *
 * <b>Mapping generic setters to fine-grained methods</b>
 * <code>
 * class Foo extends lmbObject
 * {
 *    function setBar($value)
 *    {
 *      $this->_setRaw('bar', (int)$value);
 *    }
 * }
 * $foo = new Foo();
 * //the call below will be magically mapped to setBar($value) method
 * //this can be useful if some property processing is required
 * $foo->set('bar', '10.0');
 * </code>
 *
 * <b>Working with deep nested values</b>
 * <code>
 * $obj = new lmbObject(array('foo' => array('bar' => 'hey')));
 * //would print 'hey'
 * echo $obj->getByPath('foo.bar');
 * </code>
 *
 * @version $Id: lmbObject.class.php 5567 2007-04-06 14:37:24Z serega $
 * @package core
 */
class lmbObject implements lmbSetInterface
{
  /**
   * Constructor.
   * Fills internals properties if any
   * @param array properties array
   */
  function __construct($attributes = array())
  {
    if($attributes)
      $this->import($attributes);
  }
  /**
   * Returns class name using PHP built in get_class
   * @see get_class
   * @return string
   */
  final function getClass()
  {
    return get_class($this);
  }
  /**
   * Returns object's hash in md5 form
   * @see md5
   * @return string
   */
  function getHash()
  {
    return md5(serialize($this));
  }
  /**
   * Merges existing properties with new ones
   * @param array
   */
  function import($values)
  {
    if(!is_array($values))
      return;

    foreach($values as $name => $value)
    {
      if(!$this->_isGuarded($name))
        $this->_setRaw($name, $value);
    }
  }
  /**
   * Exports all object properties as an array
   * @return array
   */
  function export()
  {
    $exported = array();
    foreach($this->_getObjectVars() as $name => $var)
    {
      if(!$this->_isGuarded($name))
        $exported[$name] = $var;
    }
    return $exported;
  }
  /**
   * Checks if such attribute exists
   * @return bool returns true even if attribute is null
   */
  function has($name)
  {
    return in_array($name, $this->getAttributesNames());
  }
  /**
   * @deprecated
   * @see has()
   */
  function hasAttribute($name)
  {
    return $this->has($name);
  }
  /**
   * Returns array filled with attribute names
   * @return array
   */
  function getAttributesNames()
  {
    $names = array();
    foreach($this->_getObjectVars() as $name => $value)
    {
      if(!$this->_isGuarded($name))
        $names[] = $name;
    }
    return $names;
  }
  /**
   * Returns property value if it exists and not guarded.
   * Magically maps getter to fine-grained method if it exists, e.g. get('foo') => getFoo()
   * @param string property name
   * @return mixed|null
   */
  function get($name)
  {
    if($method = $this->_mapPropertyToMethod($name))
      return $this->$method();

    if(!$this->_isGuarded($name))
      return $this->_getRaw($name);
  }
  /**
   * Removes specified property
   * @param string
   */
  function remove($name)
  {
    if($this->hasAttribute($name))
      unset($this->$name);
  }
  /**
   * @deprecated
   * @see reset()
   */
  function removeAll()
  {
    $this->reset();
  }
  /**
   * Removes all object properties
   */
  function reset()
  {
    foreach($this->_getObjectVars() as $name => $var)
      $this->remove($name);
  }

  protected function _getRaw($name)
  {
    if(isset($this->$name))
      return $this->$name;
  }

  protected function _getObjectVars()
  {
    return get_object_vars($this);
  }
  /**
   * Sets property value
   * Magically maps setter to fine-grained method if it exists, e.g. set('foo', $value) => setFoo($value)
   * @param string property name
   * @param mixed value
   */
  function set($name, $value)
  {
    if($method = $this->_mapPropertyToSetMethod($name))
      return $this->$method($value);

    if(!$this->_isGuarded($name))
      $this->_setRaw($name, $value);
  }

  /**#@+
   * Implements ArrayAccess interface
   * @see ArrayAccess
   */
  function offsetExists($offset)
  {
    return $this->hasAttribute($offset);
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
  /**#@-*/

  protected function _setRaw($name, $value)
  {
    $this->$name = $value;
  }

  protected function _isGuarded($property)
  {
    return $property{0} == '_';
  }

  protected function __call($method, $args = array())
  {
    if($property = $this->_mapGetToProperty($method))
    {
      return $this->get($property);
    }
    elseif($property = $this->_mapSetToProperty($method))
    {
      $this->set($property, $args[0]);
      return;
    }

    throw new lmbException("No such method '$method' in " . get_class($this));
  }

  protected function _mapGetToProperty($method)
  {
    if(substr($method, 0, 3) == 'get')
      return lmb_under_scores(substr($method, 3));
  }

  protected function _mapSetToProperty($method)
  {
    if(substr($method, 0, 3) == 'set')
      return lmb_under_scores(substr($method, 3));
  }

  protected function _mapPropertyToMethod($property)
  {
    $capsed = lmb_camel_case($property);
    $method = 'get' . $capsed;
    if(method_exists($this, $method))
      return $method;
    //'is_foo' property is mapped to 'isFoo' method if it exists
    if(strpos($property, 'is_') === 0 && method_exists($this, $capsed))
      return $capsed;
  }

  protected function _mapPropertyToSetMethod($property)
  {
    $method = 'set' . lmb_camel_case($property);
    if(method_exists($this, $method))
      return $method;
  }
}

