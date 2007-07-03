<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/dbal/src/query/lmbInsertQuery.class.php');
lmb_require('limb/dbal/src/query/lmbSelectQuery.class.php');
lmb_require('limb/dbal/src/query/lmbUpdateQuery.class.php');
lmb_require('limb/dbal/src/query/lmbDeleteQuery.class.php');
lmb_require('limb/dbal/src/criteria/lmbSQLCriteria.class.php');

/**
 * class lmbSimpleDb.
 *
 * @package dbal
 * @version $Id: lmbSimpleDb.class.php 6049 2007-07-03 08:45:17Z pachanga $
 */
class lmbSimpleDb
{
  protected $conn;
  protected $stmt;

  function __construct($conn)
  {
    $this->conn = $conn;
  }

  function getConnection()
  {
    return $this->conn;
  }

  function getType()
  {
    return $this->conn->getType();
  }

  function select($table, $criteria = null, $order = '')
  {
    $query = new lmbSelectQuery($table);

    if($criteria)
      $query->addCriteria(lmbSQLCriteria :: objectify($criteria));

    if($order)
      $query->addOrder($order);

    return $query->getRecordSet($this->conn);
  }

  function selectAsArray($table, $criteria = null, $order = '', $key_field = '')
  {
    $rs = $this->select($table, $criteria, $order);
    return $rs->getArray($key_field);
  }

  function getFirstRecordFrom($table_name, $criteria = null, $order = '')
  {
    $rs = $this->select($table_name, $criteria, $order);
    $rs->rewind();
    if($rs->valid())
      return $rs->current();
    else
      return new lmbSet();
  }

  function count($table_name, $criteria = null)
  {
    $rs = $this->select($table_name, $criteria);
    return $rs->count();
  }

  function countAffected()
  {
    if($this->stmt)
      return $this->stmt->getAffectedRowCount();
    else
      return 0;
  }

  function insert($table, $values, $primary_key = 'id')
  {
    $query = new lmbInsertQuery($table, $this->conn);

    foreach($values as $key => $value)
      $query->addField($key , $value);

    $stmt = $query->getStatement($this->conn);

    if($primary_key)
    {
      if(isset($values[$primary_key]))
      {
        $stmt->execute();
        return $values[$primary_key];
      }
      else
        return $stmt->insertId($primary_key);
    }
    else
      $stmt->execute();
  }

  function update($table, $values, $criteria = null)
  {
    $query = new lmbUpdateQuery($table, $this->conn);

    if($criteria)
      $query->addCriteria(lmbSQLCriteria :: objectify($criteria));

    foreach($values as $key => $value)
      $query->addField($key, $value);

    $this->stmt = $query->getStatement($this->conn);
    $this->stmt->execute();
    return $this;
  }

  function delete($table, $criteria = null)
  {
    $query = new lmbDeleteQuery($table, $this->conn);

    if($criteria)
      $query->addCriteria(lmbSQLCriteria :: objectify($criteria));

    $this->stmt = $query->getStatement($this->conn);
    $this->stmt->execute();
    return $this;
  }

  function truncateDb()
  {
    $info = $this->conn->getDatabaseInfo();
    foreach($info->getTableList() as $table)
      $this->conn->newStatement("DELETE FROM $table")->execute();
    return $this;
  }

  function disconnect()
  {
    $this->conn->disconnect();
    return $this;
  }

  function begin()
  {
    $this->conn->beginTransaction();
    return $this;
  }

  function commit()
  {
    $this->conn->commitTransaction();
    return $this;
  }

  function rollback()
  {
    $this->conn->rollbackTransaction();
    return $this;
  }

  function quote($id)
  {
    return $this->conn->quoteIdentifier($id);
  }
}

?>
