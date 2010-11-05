<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

lmb_require('limb/dbal/src/drivers/lmbDbQueryStatement.interface.php');
lmb_require(dirname(__FILE__) . '/lmbMssqlStatement.class.php');

/**
 * class lmbMssqlQueryStatement.
 *
 * @package dbal
 * @version $Id: lmbMssqlQueryStatement.class.php,v 1.1.1.1 2009/06/08 11:57:21 mike Exp $
 */
class lmbMssqlQueryStatement extends lmbMssqlStatement implements lmbDbQueryStatement
{
  function getOneRecord()
  {
    $record = new lmbMssqlRecord();
    $queryId = $this->connection->execute($this->getSQL());
    $values = mssql_fetch_assoc($queryId);
    $record->import($values);
    mssql_free_result($queryId);
    if(is_array($values))
      return $record;
  }

  function getOneValue()
  {
    $queryId = $this->connection->execute($this->getSQL());
    $row = mssql_fetch_row($queryId);
    mssql_free_result($queryId);
    if(is_array($row))
      return is_null($row[0]) ? null : (is_numeric($row[0]) ? $row[0] : mb_convert_encoding($row[0], 'UTF-8', 'Windows-1251'));
  }

  function getOneColumnAsArray()
  {
    $column = array();
    $queryId = $this->connection->execute($this->getSQL());
    while(is_array($row = mssql_fetch_row($queryId)))
      $column[] = is_numeric($row[0]) ? $row[0] : mb_convert_encoding($row[0], 'UTF-8', 'Windows-1251');
    mssql_free_result($queryId);
    return $column;
  }

  function getRecordSet()
  {
    return new lmbMssqlRecordSet($this->connection, $this->getSQL());
  }
}


