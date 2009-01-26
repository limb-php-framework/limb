<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/core/src/exception/lmbException.class.php');

/**
 * Registry pattern implementation.
 * Allows to store any information and get access to it in any point of application
 * Supports saving and restoring (acts like programming stack) that is usefull for testing
 * Completely static class
 * @link http://www.phppatterns.com/docs/design/the_registry
 * @package toolkit
 * @version $Id: lmbRegistry.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbRegistry
{
  protected static $cache = array();

  /**
  * Adds a value to the registry
  * @param string key
  * @param mixed value
  */
  static function set($name, $value)
  {
    self :: $cache[$name][0] = $value;
  }

  /**
  * Returns value from the registry
  * @param string key
  * @return mixed
  */
  static function get($name)
  {
    if(isset(self :: $cache[$name][0]))
      return self :: $cache[$name][0];
  }

  /**
  * Moves all registry entries one level deeper in cache stack
  * @param string key
  * @return void
  */
  static function save($name)
  {
    if(isset(self :: $cache[$name]))
      array_unshift(self :: $cache[$name], array());
    else
      throw new lmbException("No such registry entry '$name'");
  }

  /**
  * Moves all registry entries one level up in cache stack
  * @param string key
  * @return void
  */
  static function restore($name)
  {
    if(isset(self :: $cache[$name]))
      array_shift(self :: $cache[$name]);
    else
      throw new lmbException("No such registry entry '$name'");
  }
}

