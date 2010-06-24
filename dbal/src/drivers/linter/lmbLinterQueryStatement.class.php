<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

lmb_require('limb/dbal/src/drivers/lmbDbQueryStatement.interface.php');
lmb_require(dirname(__FILE__) . '/lmbLinterStatement.class.php');
lmb_require(dirname(__FILE__) . '/lmbLinterRecord.class.php');
lmb_require(dirname(__FILE__) . '/lmbLinterRecordSet.class.php');
lmb_require(dirname(__FILE__) . '/lmbLinterArraySet.class.php');

/**
 * class lmbLinterQueryStatement.
 *
 * @package dbal
 * @version $Id: $
 */
class lmbLinterQueryStatement extends lmbLinterStatement implements lmbDbQueryStatement
{
  function getOneRecord()
  {
    $record = new lmbLinterRecord();
    $queryId = $this->execute();
    $values = linter_get_data_array($queryId);
    $record->import($values);
    $this->free();
    return $record;
  }

  function getOneValue()
  {
    $queryId = $this->execute();
    $row = linter_get_data_row($queryId);
    $this->free();
    if(is_array($row) && count($row))
      return $row[0];
  }

  function getOneColumnAsArray()
  {
    $column = array();
    $queryId = $this->execute();
    while(is_array($row = linter_fetch_row($queryId)))
      $column[] = $row[0];
    $this->free();
    return $column;
  }

  function getRecordSet()
  {
    return new lmbLinterRecordSet($this->connection, $this);
  }
  
  function count()
  {
    if(!(preg_match("/^\s*SELECT\s+DISTINCT/is", $this->original_sql) || preg_match('/\s+GROUP\s+BY\s+/is',$this->original_sql)) && preg_match("/^\s*SELECT\s+.+\s+FROM\s+/Uis", $this->original_sql))
    {
      $rewritesql = preg_replace('/^\s*SELECT\s.*\s+FROM\s/Uis','SELECT COUNT(*) FROM ', $this->original_sql);
      $rewritesql = preg_replace('/(\sORDER\s+BY\s.*)/is','', $rewritesql);

      $queryId = $this->execute($rewritesql);
      $count = linter_get_cursor_opt($queryId, CO_ROW_COUNT);
      $row = linter_get_data_row($queryId);
      $this->connection->closeCursor($queryId);
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
    $count = linter_get_cursor_opt($queryId, CO_ROW_COUNT);
    $this->connection->closeCursor($queryId);
    return $count;
  }
  
   
}


