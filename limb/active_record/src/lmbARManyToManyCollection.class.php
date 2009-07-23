<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/active_record/src/lmbARRelationCollection.class.php');
lmb_require('limb/dbal/src/query/lmbSelectRawQuery.class.php');

/**
 * class lmbARManyToManyCollection.
 *
 * @package active_record
 * @version $Id: lmbARManyToManyCollection.class.php 7972 2009-07-23 20:30:23Z idler $
 */
class lmbARManyToManyCollection extends lmbARRelationCollection
{
  protected function _createARQuery($params = array())
  {
    $query = self :: createFullARQueryForRelation($this->relation_info, $this->conn, $params);
    
    $join_table = $this->conn->quoteIdentifier($this->relation_info['table']);
    $field = $this->conn->quoteIdentifier($this->relation_info['field']);
    $query->addCriteria("{$join_table}.{$field} = {$this->owner->getId()}");
    if(isset($this->relation_info['criteria']))
    {
      $query->addCriteria($this->relation_info['criteria']);
    }
    return $query; 
  }
  
  static function createFullARQueryForRelation($relation_info, $conn, $params = array())
  {
    return parent :: createFullARQueryForRelation(__CLASS__, $relation_info, $conn, $params);
  }
  
  static function createCoreARQueryForRelation($relation_info, $conn, $params = array())
  {
    $class = $relation_info['class'];
    $object = new $class();

    $table = $conn->quoteIdentifier($object->getTableName());
    $join_table = $conn->quoteIdentifier($relation_info['table']);
    $field = $conn->quoteIdentifier($relation_info['field']);
    $foreign_field = $conn->quoteIdentifier($relation_info['foreign_field']);
    $primary_field = $conn->quoteIdentifier($object->getPrimaryKeyName());
    
    $sql = "SELECT %fields% FROM {$table} INNER JOIN {$join_table} ON {$table}.{$primary_field} = {$join_table}.{$foreign_field}" . 
           " %tables% %left_join% %where% %group% %having% %order%";

    $query = lmbARQuery :: create($class, $params, $conn, $sql); 

    $fields = $object->getDbTable()->getColumnsForSelect();
    foreach($fields as $field => $alias)
      $query->addField($field, $alias);
    
    return $query;
  }

  function set($objects)
  {
    $existing_records = $this->_getExistingRecords($objects);
    $linked_objects_ids = array_keys($existing_records);
    
    foreach($objects as $object)
    {
      $id = $object->getId();
      if(!isset($existing_records[$id]))
        $this->add($object);
      else
        unset($existing_records[$id]);
    }
    
    $to_remove_ids = array_keys($existing_records);
    if(count($to_remove_ids))
    {
      $table = new lmbTableGateway($this->relation_info['table'], $this->conn);
      //$table->delete(lmbSQLCriteria :: in($this->relation_info['foreign_field'], $to_remove_ids));
      $criteria = new lmbSqlCriteria();
      $criteria->addAnd(new lmbSQLFieldCriteria($this->relation_info['field'], $this->owner->getId()));
      $criteria->addAnd(new lmbSQLFieldCriteria($this->relation_info['foreign_field'], $to_remove_ids, lmbSQLFieldCriteria :: IN));
      $table->delete($criteria);
    }
  }
  
  protected function _getExistingRecords($objects)
  {
    $table = new lmbTableGateway($this->relation_info['table'], $this->conn);
    $criteria = new lmbSQLCriteria();
    $criteria->addAnd(new lmbSQLFieldCriteria($this->relation_info['field'], $this->owner->getId()));
    $criteria->addAnd(new lmbSQLFieldCriteria($this->relation_info['foreign_field'], null, lmbSQLFieldCriteria :: IS_NOT_NULL));
    $existing_records = $table->select($criteria);
    
    return lmbCollection :: toFlatArray($existing_records, $this->relation_info['foreign_field']);
  }
  
  protected function _removeRelatedRecords()
  {
    $table = new lmbTableGateway($this->relation_info['table'], $this->conn);
    $criteria = new lmbSQLCriteria();
    $criteria->addAnd(new lmbSQLFieldCriteria($this->relation_info['field'], $this->owner->getId()));
    $criteria->addAnd(new lmbSQLFieldCriteria($this->relation_info['foreign_field'], null, lmbSQLFieldCriteria :: IS_NOT_NULL));

    $table->delete($criteria);
  }

  protected function _saveObject($object, $error_list = null)
  {
    $object->save($error_list);
    $table = new lmbTableGateway($this->relation_info['table'], $this->conn);
    $table->insert(array($this->relation_info['field'] => $this->owner->getId(),
                         $this->relation_info['foreign_field'] => $object->getId()));
  }
  
  function remove($object)
  {
    $table = new lmbTableGateway($this->relation_info['table'], $this->conn);
    $criteria = new lmbSQLCriteria();
    $criteria->addAnd(lmbSQLCriteria :: equal($this->relation_info['field'], $this->owner->getId()));
    $criteria->addAnd(lmbSQLCriteria :: equal($this->relation_info['foreign_field'], $object->getId()));
    $table->delete($criteria);
    $this->reset();
  }
}


