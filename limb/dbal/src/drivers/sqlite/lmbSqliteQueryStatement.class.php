<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    $package$
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
    $values = sqlite_fetch_array($queryId, SQLITE_ASSOC);       
    if(is_array($values))
    {
      $record->import($values);
      return $record;
    }
  }

  function getOneValue()
  {
    $queryId = $this->connection->execute($this->getSQL());
    $row = sqlite_fetch_single($queryId);    
    if(is_array($row))
      return $row[0];
  }

  function getOneColumnAsArray()
  {
    $column = array();
    $queryId = $this->connection->execute($this->getSQL());
    while($value = sqlite_fetch_single($queryId))
      $column[] = $value;
    return $column;
  }

  function getRecordSet()
  {
    return new lmbSqliteRecordSet($this->connection, $this->getSQL());
  }
}

?>
