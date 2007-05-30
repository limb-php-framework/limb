<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSqliteQueryStatement.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */

lmb_require('limb/dbal/src/drivers/lmbDbQueryStatement.interface.php');
lmb_require(dirname(__FILE__) . '/lmbSqliteStatement.class.php');
lmb_require(dirname(__FILE__) . '/lmbSqliteRecord.class.php');
lmb_require(dirname(__FILE__) . '/lmbSqliteRecordSet.class.php');

class lmbSqliteQueryStatement extends lmbSqliteStatement implements lmbDbQueryStatement
{
  function getOneRecord()
  {
    $record = new lmbSqliteRecord();
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
    return new lmbSqliteRecordSet($this->connection, $this->getSQL());
  }
}

?>
