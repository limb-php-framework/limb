<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/active_record/src/lmbARRelationCollection.class.php');

/**
 * class lmbAROneToManyCollection.
 *
 * @package active_record
 * @version $Id: lmbAROneToManyCollection.class.php 6694 2008-01-17 15:41:51Z serega $
 */
class lmbAROneToManyCollection extends lmbARRelationCollection
{
  protected function _createARQuery($params = array())
  {
    $query = self :: createFullARQueryForRelation($this->relation_info, $this->conn, $params);
    $query->addCriteria(new lmbSQLFieldCriteria($this->relation_info['field'], $this->owner->getId()));
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
      $obj->destroy();
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


