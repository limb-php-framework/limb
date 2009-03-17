<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * class lmbARProxy.
 *
 * @package active_record
 * @version $Id$
 */
class lmbARProxy
{
  private $__record;
  private $__default_class_name;
  private $__conn;
  private $__lazy_attributes;
  private $__original;
  private $__exported = false;

  function __construct($record, $default_class_name, $conn, $lazy_attributes)
  {
    $this->__record = $record;
    $this->__default_class_name = $default_class_name;
    $this->__conn = $conn;
    $this->__lazy_attributes = $lazy_attributes;
  }

  function get($name)
  {
    return $this->__record->get($name);
  }

  function __get($name)
  {
    $just_exported = false;
    if(!$this->__exported)
    {
      foreach($this->__record as $key => $val)
        $this->$key = $val;
      $just_exported = true;
      $this->__exported = false;
    }

    if($just_exported && isset($this->$name))
      return $this->$name;

    if(!$this->__original)
      $this->_loadOriginal();

    return $this->__original->$name;
  }

  function __call($method, $args = array())
  {
    if(!$this->__original)
      $this->_loadOriginal();

    return call_user_func_array(array($this->__original, $method), $args);
  }

  private function _loadOriginal()
  {
    if($path = $this->__record->get(lmbActiveRecord :: getInheritanceField()))
    {
      $class_name = lmbActiveRecord :: getInheritanceClass($this->__record);

      if(!class_exists($class_name))
        throw new lmbException("Class '$class_name' not found");
    }
    else
      $class_name = $this->__default_class_name;

    $this->__original = new $class_name(null, $this->__conn);
    if(is_array($this->__lazy_attributes))
      $this->__original->setLazyAttributes($this->__lazy_attributes);

    $this->__original->loadFromRecord($this->__record);
  }
}
