<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/dbal/src/drivers/lmbDbManipulationStatement.interface.php');
lmb_require(dirname(__FILE__) . '/lmbSqliteStatement.class.php');

/**
 * class lmbSqliteManipulationStatement.
 *
 * @package dbal
 * @version $Id$
 */
class lmbSqliteManipulationStatement extends lmbSqliteStatement implements lmbDbManipulationStatement
{
  function getAffectedRowCount()
  {
    return sqlite_changes($this->connection->getConnectionId());
  }

  function execute()
  {
    $sql = ltrim($this->getSQL());

    //this is a dirty hack for sqlite_changes which
    //doesn't return proper value if there was not
    if((stripos($sql, 'delete ') === 0 || stripos($sql, 'update '))
       && stripos($sql, ' where ') === false)
      $sql .= " WHERE 1=1";

    return (bool)$this->connection->execute($sql);
  }
}


