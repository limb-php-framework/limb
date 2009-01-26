<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

lmb_require('limb/dbal/src/drivers/lmbDbQueryStatement.interface.php');
lmb_require(dirname(__FILE__) . '/lmbMysqlStatement.class.php');

/**
 * class lmbMysqlQueryStatement.
 *
 * @package dbal
 * @version $Id: lmbMysqlQueryStatement.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbMysqlQueryStatement extends lmbMysqlStatement implements lmbDbQueryStatement
{
  function getOneRecord()
  {
    $record = new lmbMysqlRecord();
    $queryId = $this->connection->execute($this->getSQL());
    $values = mysql_fetch_assoc($queryId);
    $record->import($values);
    mysql_free_result($queryId);
    if(is_array($values))
      return $record;
  }

  function getOneValue()
  {
    $queryId = $this->connection->execute($this->getSQL());
    $row = mysql_fetch_row($queryId);
    mysql_free_result($queryId);
    if(is_array($row))
      return $row[0];
  }

  function getOneColumnAsArray()
  {
    $column = array();
    $queryId = $this->connection->execute($this->getSQL());
    while(is_array($row = mysql_fetch_row($queryId)))
      $column[] = $row[0];
    mysql_free_result($queryId);
    return $column;
  }

  function getRecordSet()
  {
    return new lmbMysqlRecordSet($this->connection, $this->getSQL());
  }
}


