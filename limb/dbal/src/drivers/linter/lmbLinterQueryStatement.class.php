<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
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
}


