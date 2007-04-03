<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbPgsqlQueryStatement.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */

lmb_require('limb/dbal/src/drivers/lmbDbQueryStatement.interface.php');
lmb_require(dirname(__FILE__) . '/lmbPgsqlStatement.class.php');
lmb_require(dirname(__FILE__) . '/lmbPgsqlRecord.class.php');
lmb_require(dirname(__FILE__) . '/lmbPgsqlRecordSet.class.php');
lmb_require(dirname(__FILE__) . '/lmbPgsqlArraySet.class.php');

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
