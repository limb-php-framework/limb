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
 * @version $Id: lmbARRecordSetDecorator.class.php 6691 2008-01-15 14:55:59Z serega $
 */
class lmbARRecordSetDecorator extends lmbCollectionDecorator
{
  protected $class_path;
  protected $conn;
  protected $with_relations = array();

  function __construct($record_set, $class_path, $conn = null, $with_relations = array())
  {
    $this->class_path = $class_path;
    $this->conn = $conn;
    $this->with_relations = $with_relations;

    parent :: __construct($record_set);
  }

  function current()
  {
    if(!$record = parent :: current())
      return null;

    return $this->_createObjectFromRecord($record);
  }

  protected function _createObjectFromRecord($record)
  {
    $object = $this->_createObject($record);
    $this->_extractPrefixedFieldsAsActiveRecords($record);
    $object->loadFromRecord($record);
    return $object;
  }
  
  protected function _extractPrefixedFieldsAsActiveRecords($record)
  {
    foreach($this->with_relations as $relation_name => $relation_info)
    {
      $fields = new lmbSet();
      $prefix = $relation_name . '__';
      
      foreach($record->export() as $field => $value)
      {
        if(strpos($field, $prefix) === 0)
        {
          $non_prefixes_field_name = substr($field, strlen($prefix));
          $fields->set($non_prefixes_field_name, $value);
          $record->remove($field);
        }
      }
      
      $related_object_class = $relation_info['class'];
      $related_object = new $related_object_class(null, $this->conn);
      $related_object->loadFromRecord($fields);
      $record->set($relation_name, $related_object);
    }
  }

  protected function _createObject($record)
  {
    if($path = $record->get(lmbActiveRecord :: getInheritanceField()))
    {
      $class = end(lmbActiveRecord :: decodeInheritancePath($path));
      if(!class_exists($class))
        throw new lmbException("Class '$class' not found");
      return new $class(null, $this->conn);
    }
    else
      return lmbClassPath :: create($this->class_path, array(null, $this->conn));
  }

  function at($pos)
  {
    if(!$record = parent :: at($pos))
      return null;

    return $this->_createObjectFromRecord($record);
  }
}


