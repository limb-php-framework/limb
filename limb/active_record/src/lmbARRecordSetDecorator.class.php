<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/core/src/lmbCollectionDecorator.class.php');
lmb_require('limb/core/src/lmbClassPath.class.php');
lmb_require('limb/core/src/lmbSet.class.php');

/**
 * class lmbARRecordSetDecorator.
 *
 * @package active_record
 * @version $Id: lmbARRecordSetDecorator.class.php 7018 2008-05-15 11:53:30Z serega $
 */
class lmbARRecordSetDecorator extends lmbCollectionDecorator
{
  protected $class_path;
  protected $conn;

  function __construct($record_set, $class_path, $conn = null)
  {
    $this->class_path = $class_path;
    $this->conn = $conn;

    parent :: __construct($record_set);
  }

  function current()
  {
    if(!$record = parent :: current())
      return null;

    return self :: createObjectFromRecord($record, $this->class_path, $this->conn);
  }

  static function createObjectFromRecord($record, $default_class_name, $conn)
  {
    if($path = $record->get(lmbActiveRecord :: getInheritanceField()))
    {
      $class_name = end(lmbActiveRecord :: decodeInheritancePath($path));
      if(!class_exists($class_name))
        throw new lmbException("Class '$class_name' not found");
    }
    else
      $class_name = $default_class_name;
      
    $object = new $class_name(null, $conn);
    
    $object->loadFromRecord($record);
    
    return $object;
  }  

  function at($pos)
  {
    if(!$record = parent :: at($pos))
      return null;

    return self :: createObjectFromRecord($record, $this->class_path, $this->conn);
  }
}


