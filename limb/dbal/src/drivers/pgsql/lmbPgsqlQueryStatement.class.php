<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

lmb_require('limb/dbal/src/drivers/lmbDbQueryStatement.interface.php');
lmb_require(dirname(__FILE__) . '/lmbPgsqlStatement.class.php');
lmb_require(dirname(__FILE__) . '/lmbPgsqlRecord.class.php');
lmb_require(dirname(__FILE__) . '/lmbPgsqlRecordSet.class.php');
lmb_require(dirname(__FILE__) . '/lmbPgsqlArraySet.class.php');

/**
 * class lmbPgsqlQueryStatement.
 *
 * @package dbal
 * @version $Id: lmbPgsqlQueryStatement.class.php 5945 2007-06-06 08:31:43Z pachanga $
 */
class lmbPgsqlQueryStatement extends lmbPgsqlStatement implements lmbDbQueryStatement
{
  function getOneRecord()
  {
    $record = new lmbPgsqlRecord();
    $queryId = $this->connection->execute($this->getSQL());
    $values = pg_fetch_assoc($queryId);
    $record->import($values);
    pg_free_result($queryId);
    if(is_array($values))
      return $record;
  }

  function getOneValue()
  {
    $queryId = $this->connection->execute($this->getSQL());
    $row = pg_fetch_row($queryId);
    pg_free_result($queryId);
    if(is_array($row))
      return $row[0];
  }

  function getOneColumnAsArray()
  {
    $column = array();
    $queryId = $this->connection->execute($this->getSQL());
    while(is_array($row = pg_fetch_row($queryId)))
      $column[] = $row[0];
    pg_free_result($queryId);
    return $column;
  }

  function getRecordSet()
  {
    return new lmbPgsqlRecordSet($this->connection, $this->getSQL());
  }
}

?>
