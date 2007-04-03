<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSimpleDb.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */
lmb_require('limb/dbal/src/query/lmbInsertQuery.class.php');
lmb_require('limb/dbal/src/query/lmbSelectQuery.class.php');
lmb_require('limb/dbal/src/query/lmbUpdateQuery.class.php');
lmb_require('limb/dbal/src/query/lmbDeleteQuery.class.php');
lmb_require('limb/dbal/src/criteria/lmbSQLFieldCriteria.class.php');
lmb_require('limb/datasource/src/lmbDatasetHelper.class.php');

class lmbSimpleDb
{
  protected $conn;

  function __construct($conn)
  {
    $this->conn = $conn;
  }

  function getType()
  {
    return $this->conn->getType();
  }

  function select($table, $criteria = null, $order = '')
  {
    $query = $this->getSelectQuery($table);

    if($criteria)
      $query->addCriteria(lmbSQLCriteria :: objectify($criteria));

    if($order)
      $query->addOrder($order);

    return new lmbDatasetHelper($query->getRecordSet($this->conn));
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
      return new lmbDataspace();
  }

  function count($table_name, $criteria = null)
  {
    $rs = $this->select($table_name, $criteria);
    return $rs->count();
  }

  function getSelectQuery($table)
  {
    $query = new lmbSelectQuery(null, $this->conn);
    $query->addTable($table);
    return $query;
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

    $stmt = $query->getStatement($this->conn);
    $stmt->execute();
    return $stmt->getAffectedRowCount();
  }

  function delete($table, $criteria = null)
  {
    $query = new lmbDeleteQuery($table, $this->conn);

    if($criteria)
      $query->addCriteria(lmbSQLCriteria :: objectify($criteria));

    $stmt = $query->getStatement($this->conn);
    $stmt->execute();
    return $stmt->getAffectedRowCount();
  }

  function truncateDb()
  {
    $info = $this->conn->getDatabaseInfo();
    foreach($info->getTableList() as $table)
      $this->conn->newStatement("DELETE FROM $table")->execute();
  }

  function disconnect()
  {
    $this->conn->disconnect();
  }

  function begin()
  {
    $this->conn->beginTransaction();
  }

  function commit()
  {
    $this->conn->commitTransaction();
  }

  function rollback()
  {
    $this->conn->rollbackTransaction();
  }
}

?>
