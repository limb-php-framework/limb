<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbARManyToManyCollection.class.php 4984 2007-02-08 15:35:02Z pachanga $
 * @package    active_record
 */
lmb_require('limb/active_record/src/lmbARRelationCollection.class.php');

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

    $query = new lmbSelectQuery($sql, lmbToolkit :: instance()->getDefaultDbConnection());
    if($criteria)
      $query->addCriteria($criteria);
    return $query->getRecordSet();
  }

  protected function _createDbRecordSet($criteria = null)
  {
    $conn = lmbToolkit :: instance()->getDefaultDbConnection();

    $class = $this->relation_info['class'];
    $object = new $class();
    $table = $conn->quoteIdentifier($object->getTableName());

    $join_table = $conn->quoteIdentifier($this->relation_info['table']);
    $field = $conn->quoteIdentifier($this->relation_info['field']);
    $foreign_field = $conn->quoteIdentifier($this->relation_info['foreign_field']);

    $sql = "SELECT $table.* FROM $table, $join_table
            WHERE $table.id=$join_table.$foreign_field AND
            $join_table.$field=" . $this->owner->getId() . ' %where%';

    $query = new lmbSelectQuery($sql, $conn);
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
    $table = new lmbTableGateway($this->relation_info['table']);
    $table->delete(new lmbSQLFieldCriteria($this->relation_info['field'], $this->owner->getId()));
  }

  protected function _saveObject($object)
  {
    $table = new lmbTableGateway($this->relation_info['table']);
    $object->save();
    $table->insert(array($this->relation_info['field'] => $this->owner->getId(),
                         $this->relation_info['foreign_field'] => $object->getId()));
  }
}

?>
