<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/active_record/src/lmbARRelationCollection.class.php');
lmb_require('limb/dbal/src/query/lmbSelectRawQuery.class.php');

/**
 * class lmbARManyToManyCollection.
 *
 * @package active_record
 * @version $Id: lmbARManyToManyCollection.class.php 6691 2008-01-15 14:55:59Z serega $
 */
class lmbARManyToManyCollection extends lmbARRelationCollection
{
  protected function _createARQuery($magic_params = array())
  {
    $query = self :: createFullARQueryForRelation($this->relation_info, $this->conn, $magic_params);
    
    $join_table = $this->conn->quoteIdentifier($this->relation_info['table']);
    $field = $this->conn->quoteIdentifier($this->relation_info['field']);
    $query->addCriteria("{$join_table}.{$field} = {$this->owner->getId()}");

    return $query; 
  }
  
  static function createFullARQueryForRelation($relation_info, $conn, $magic_params = array())
  {
    return parent :: createFullARQueryForRelation(__CLASS__, $relation_info, $conn, $magic_params);
  }
  
  static function createCoreARQueryForRelation($relation_info, $conn)
  {
    $class = $relation_info['class'];
    $object = new $class();

    $table = $conn->quoteIdentifier($object->getTableName());
    $join_table = $conn->quoteIdentifier($relation_info['table']);
    $field = $conn->quoteIdentifier($relation_info['field']);
    $foreign_field = $conn->quoteIdentifier($relation_info['foreign_field']);
    
    $sql = "SELECT %fields% FROM {$table} INNER JOIN {$join_table} ON {$table}.{$object->getPrimaryKeyName()} = {$join_table}.{$foreign_field}" . 
           " %tables% %left_join% %where% %group% %having% %order%";

    $query = new lmbARQuery($class, $conn, $sql);

    $fields = $object->getDbTable()->getColumnsForSelect();
    foreach($fields as $field => $alias)
      $query->addField($field, $alias);
    
    return $query;
  }

  function set($objects)
  {
    $this->removeAll();
    foreach($objects as $object)
      $this->add($object);
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
    $table = new lmbTableGateway($this->relation_info['table'], $this->conn);
    $object->save($error_list);
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


