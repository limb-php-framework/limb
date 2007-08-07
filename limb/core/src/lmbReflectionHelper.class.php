<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class lmbReflectionHelper.
 *
 * @package core
 * @version $Id$
 */
class lmbReflectionHelper
{
  static function getMethods($name)
  {
    return array_unique(get_class_methods($name));
  }

  static function getInterfaces($name)
  {
    $reflection = new ReflectionClass($name);
    if($reflection->isInterface())
      return array($name);

    return self :: _onlyParents($reflection->getInterfaces());
  }

  static function getInterfaceMethods($name)
  {
    $methods = array();
    foreach(self :: getInterfaces($name) as $interface)
      $methods = array_merge($methods, get_class_methods($interface));
    return array_unique($methods);
  }

  protected function _isInterfaceMethod($name, $method)
  {
    return in_array($method, self :: getInterfaceMethods($name));
  }

  static function getParent($name)
  {
    $reflection = new ReflectionClass($name);
    $parent = $reflection->getParentClass();
    if($parent)
      return $parent->getName();
    return false;
  }

  static function isAbstract($name)
  {
    $reflection = new ReflectionClass($name);
    return $reflection->isAbstract();
  }

  protected static function _onlyParents($interfaces)
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

  static function getSignature($name, $method)
  {
    if($method == '__get')
      return 'function __get($key)';

    if($method == '__set')
      return 'function __set($key, $value)';

    if(!is_callable(array($name, $method)))
      return "function $method()";

    if(self :: _isInterfaceMethod($name, $method))
      return self :: _getFullSignature($name, $method);

    return "function $method()";
  }

  static protected function _getFullSignature($name, $method_name)
  {
    $interface = new ReflectionClass($name);
    $method = $interface->getMethod($method_name);
    $reference = $method->returnsReference() ? '&' : '';
    return "function $reference $method_name(" .
          implode(', ', self :: _getParameterSignatures($method)) .
          ")";
  }

  static protected function _getParameterSignatures($method)
  {
    $signatures = array();
    foreach($method->getParameters() as $parameter)
    {
      $type = $parameter->getClass();
      $signatures[] =
          (! is_null($type) ? $type->getName() . ' ' : '') .
          ($parameter->isPassedByReference() ? '&' : '') .
          '$' . self :: _suppressSpurious($parameter->getName()) .
          ($parameter->isOptional() ? ' = null' : '');
    }
    return $signatures;
  }

  protected function _suppressSpurious($name)
  {
    return str_replace(array('[', ']', ' '), '', $name);
  }
}

