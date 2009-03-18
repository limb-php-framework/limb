<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/core/src/lmbCollectionDecorator.class.php');
lmb_require('limb/core/src/lmbClassPath.class.php');
lmb_require('limb/core/src/lmbSet.class.php');
lmb_require('limb/active_record/src/lmbARProxy.class.php');

/**
 * class lmbARRecordSetDecorator.
 *
 * @package active_record
 * @version $Id: lmbARRecordSetDecorator.class.php 7785 2009-03-18 05:49:34Z pachanga $
 */
class lmbARRecordSetDecorator extends lmbCollectionDecorator
{
  protected $class_path;
  protected $conn;
  protected $lazy_attributes;
  protected $use_proxy = false;

  function __construct($record_set, $class_path, $conn = null, $lazy_attributes = null, $use_proxy = false)
  {
    $this->class_path = $class_path;
    $this->conn = $conn;
    $this->lazy_attributes = $lazy_attributes;
    $this->use_proxy = $use_proxy;

    parent :: __construct($record_set);
  }

  function current()
  {
    if(!$record = parent :: current())
      return null;

    return self :: createObjectFromRecord($record, $this->class_path, $this->conn, $this->lazy_attributes, $this->use_proxy);
  }

  function at($pos)
  {
    if(!$record = parent :: at($pos))
      return null;

    return self :: createObjectFromRecord($record, $this->class_path, $this->conn, $this->lazy_attributes, $this->use_proxy);
  }

  function getIds()
  {
    $result = array();
    foreach($this->getArray() as $record)
      $result[] = $record->getId();
    return $result;
  }

  static function createObjectFromRecord($record, $default_class_name, $conn, $lazy_attributes = null, $use_proxy = false)
  {
    if($use_proxy)
      return new lmbARProxy($record, $default_class_name, $conn, $lazy_attributes);

    if($path = $record->get(lmbActiveRecord :: getInheritanceField()))
    {
      $class_name = lmbActiveRecord :: getInheritanceClass($record);

      if(!class_exists($class_name))
        throw new lmbException("Class '$class_name' not found");
    }
    else
      $class_name = $default_class_name;

    $object = new $class_name(null, $conn);
    if(is_array($lazy_attributes))
      $object->setLazyAttributes($lazy_attributes);

    $object->loadFromRecord($record);

    return $object;
  }
}


