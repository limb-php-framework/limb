<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
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
 * @version $Id: lmbPgsqlQueryStatement.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbPgsqlQueryStatement extends lmbPgsqlStatement implements lmbDbQueryStatement
{
  function getOneRecord()
  {
    $record = new lmbPgsqlRecord();
    $queryId = $this->connection->executeStatement($this);
    $values = pg_fetch_assoc($queryId);
    $record->import($values);
    pg_free_result($queryId);
    if(is_array($values))
      return $record;
  }

  function getOneValue()
  {
    $queryId = $this->connection->executeStatement($this);
    $row = pg_fetch_row($queryId);
    pg_free_result($queryId);
    if(is_array($row))
      return $row[0];
  }

  function getOneColumnAsArray()
  {
    $column = array();
    $queryId = $this->connection->executeStatement($this);
    while(is_array($row = pg_fetch_row($queryId)))
      $column[] = $row[0];
    pg_free_result($queryId);
    return $column;
  }

  function getRecordSet()
  {
    return new lmbPgsqlRecordSet($this->connection, $this);
  }
  
  function count()
  {
    if(!(preg_match("/^\s*SELECT\s+DISTINCT/is", $this->sql) || preg_match('/\s+GROUP\s+BY\s+/is',$this->sql)) && preg_match("/^\s*SELECT\s+.+\s+FROM\s+/Uis", $this->sql))
    {
      $rewritesql = preg_replace('/^\s*SELECT\s.*\s+FROM\s/Uis','SELECT COUNT(*) FROM ', $this->sql);
      $rewritesql = preg_replace('/(\sORDER\s+BY\s.*)/is','', $rewritesql);

      $queryId = $this->execute($rewritesql);
      $row = pg_fetch_row($queryId);
      pg_free_result($queryId);
      if (is_array($row))
      {
        return $row[0];
      }
      else
      {
        return 0;
      }
    }

    // could not re-write the query, try a different method.
    $queryId = $this->execute($this->sql);
    $count = pg_num_rows($queryId);
    pg_free_result($queryId);
    return $count;
  }
  
}


