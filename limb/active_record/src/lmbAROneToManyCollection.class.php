<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/active_record/src/lmbARRelationCollection.class.php');

/**
 * class lmbAROneToManyCollection.
 *
 * @package active_record
 * @version $Id: lmbAROneToManyCollection.class.php 8004 2009-11-27 08:12:16Z slevin $
 */
class lmbAROneToManyCollection extends lmbARRelationCollection
{
  protected function _createARQuery($params = array())
  {
    $query = self :: createFullARQueryForRelation($this->relation_info, $this->conn, $params);
    
    $relation_field = $this->relation_info['field'];
    if(!strstr($relation_field, '.'))
    {
      $related_base_object = new $this->relation_info['class'];
      $relation_field = $related_base_object->getTableName() . '.' . $relation_field;
    }
      
    $query->addCriteria(new lmbSQLFieldCriteria($relation_field, $this->owner->getId()));

    if(isset($this->relation_info['criteria']))
      $query->addCriteria($this->relation_info['criteria']);
    
    return $query;
  }
  
  static function createFullARQueryForRelation($relation_info, $conn, $params = array())
  {
    return parent :: createFullARQueryForRelation(__CLASS__, $relation_info, $conn, $params);
  }
  
  static function createCoreARQueryForRelation($relation_info, $conn, $params = array())
  {
    return lmbARQuery :: create($relation_info['class'], $params, $conn);
  }
  
  function add($object)
  {
    $property = $object->mapFieldToProperty($this->relation_info['field']);
    $object->set($property, $this->owner);

    parent :: add($object);
  }

  function set($objects)
  {
    $old_objects = array();
    foreach($this as $obj)
      $old_objects[$obj->getId()] = $obj;

    foreach($objects as $obj)
    {
      if(!isset($old_objects[$obj->getId()]))
        $this->add($obj);
      else
      {
        $obj->save();
        unset($old_objects[$obj->getId()]);
      }
    }

    foreach($old_objects as $obj)
    {
      if (array_key_exists('nullify', $this->relation_info) && $this->relation_info['nullify'])
      {
        $obj->set($this->relation_info['field'], null);
        $obj->save();
      }
      else
        $obj->destroy();
    }
  }

  protected function _removeRelatedRecords()
  {
    lmbActiveRecord :: delete($this->relation_info['class'],
                              new lmbSQLFieldCriteria($this->relation_info['field'], $this->owner->getId()),
                              $this->conn);
  }

  protected function _saveObject($object, $error_list = null)
  {
    $object->set($this->relation_info['field'], $this->owner->getId());
    $object->save($error_list);
  }

  function nullify()
  {
    $rs = $this->find();
    foreach($rs as $object)
    {
      $object->set($this->relation_info['field'], null);
      $object->save();
    }

  }
}


