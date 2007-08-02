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
 * @version $Id: lmbARManyToManyCollection.class.php 6210 2007-08-02 09:05:55Z pachanga $
 */
class lmbARManyToManyCollection extends lmbARRelationCollection
{
  protected function _createDbRecordSet2($criteria = null)
  {
    $class = $this->relation_info['class'];
    $object = new $class();
    $table = $object->getTableName();

    $join_table = $this->relation_info['table'];
    $field = $this->relation_info['field'];
    $foreign_field = $this->relation_info['foreign_field'];

    $sql = "SELECT {$table}.* FROM {$table}, {$join_table}
            WHERE {$table}.id={$join_table}.$foreign_field AND
            {$join_table}.{$field}=" . $this->owner->getId() . ' %where%';

    $query = new lmbSelectRawQuery($sql, $this->conn);
    if($criteria)
      $query->addCriteria($criteria);
    return $query->getRecordSet();
  }

  protected function _createDbRecordSet($criteria = null)
  {
    $class = $this->relation_info['class'];
    $object = new $class();
    $table = $this->conn->quoteIdentifier($object->getTableName());

    $join_table = $this->conn->quoteIdentifier($this->relation_info['table']);
    $field = $this->conn->quoteIdentifier($this->relation_info['field']);
    $foreign_field = $this->conn->quoteIdentifier($this->relation_info['foreign_field']);

    $sql = "SELECT $table.* FROM $table, $join_table
            WHERE $table.id=$join_table.$foreign_field AND
            $join_table.$field=" . $this->owner->getId() . ' %where%';

    $query = new lmbSelectRawQuery($sql, $this->conn);
    if($criteria)
      $query->addCriteria($criteria);
    return $query->getRecordSet();
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
}

?>
