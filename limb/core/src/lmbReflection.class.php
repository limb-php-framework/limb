<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbReflection.class.php 4987 2007-02-08 15:35:15Z pachanga $
 * @package    classkit
 */
//code is based on SimpleReflection class from SimpleTest test suite

class lmbReflection
{
  protected $_interface;

  function __construct($interface)
  {
    $this->_interface = $interface;
  }

  function classExists()
  {
    if(!class_exists($this->_interface))
      return false;

    $reflection = new ReflectionClass($this->_interface);
    return ! $reflection->isInterface();
  }

  function classOrInterfaceExists()
  {
    return $this->_classOrInterfaceExistsWithAutoload($this->_interface, true);
  }

  protected function _classOrInterfaceExistsWithAutoload($interface, $autoload)
  {
    if(function_exists('interface_exists'))
    {
      if(interface_exists($this->_interface, $autoload))
        return true;
    }
    return class_exists($this->_interface, $autoload);
  }

  function getMethods()
  {
    return array_unique(get_class_methods($this->_interface));
  }

  function getInterfaces()
  {
    $reflection = new ReflectionClass($this->_interface);
    if($reflection->isInterface())
      return array($this->_interface);

    return $this->_onlyParents($reflection->getInterfaces());
  }

  function getInterfaceMethods()
  {
    $methods = array();
    foreach($this->getInterfaces() as $interface)
      $methods = array_merge($methods, get_class_methods($interface));
    return array_unique($methods);
  }

  protected function _isInterfaceMethod($method)
  {
    return in_array($method, $this->getInterfaceMethods());
  }

  function getParent()
  {
    $reflection = new ReflectionClass($this->_interface);
    $parent = $reflection->getParentClass();
    if($parent)
      return $parent->getName();
    return false;
  }

  function isAbstract()
  {
    $reflection = new ReflectionClass($this->_interface);
    return $reflection->isAbstract();
  }

  protected function _onlyParents($interfaces)
  {
    $parents = array();
    foreach($interfaces as $interface)
    {
      foreach($interfaces as $possible_parent)
      {
        if($interface->getName() == $possible_parent->getName())
          continue;

        if($interface->isSubClassOf($possible_parent))
          break;
      }
      $parents[] = $interface->getName();
    }
    return $parents;
  }

  function getSignature($name)
  {
    if($name == '__get')
      return 'function __get($key)';

    if($name == '__set')
      return 'function __set($key, $value)';

    if(!is_callable(array($this->_interface, $name)))
      return "function $name()";

    if($this->_isInterfaceMethod($name))
      return $this->_getFullSignature($name);

    return "function $name()";
  }

  protected function _getFullSignature($name)
  {
    $interface = new ReflectionClass($this->_interface);
    $method = $interface->getMethod($name);
    $reference = $method->returnsReference() ? '&' : '';
    return "function $reference$name(" .
          implode(', ', $this->_getParameterSignatures($method)) .
          ")";
  }

  protected function _getParameterSignatures($method)
  {
    $signatures = array();
    foreach($method->getParameters() as $parameter)
    {
      $type = $parameter->getClass();
      $signatures[] =
          (! is_null($type) ? $type->getName() . ' ' : '') .
          ($parameter->isPassedByReference() ? '&' : '') .
          '$' . $this->_suppressSpurious($parameter->getName()) .
          ($this->_isOptional($parameter) ? ' = null' : '');
    }
    return $signatures;
  }

  protected function _suppressSpurious($name)
  {
    return str_replace(array('[', ']', ' '), '', $name);
  }

  protected function _isOptional($parameter)
  {
    if(method_exists($parameter, 'isOptional'))
      return $parameter->isOptional();

    return false;
  }
}
?>