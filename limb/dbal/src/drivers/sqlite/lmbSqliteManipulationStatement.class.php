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

class lmbSqliteManipulationStatement extends lmbSqliteStatement implements lmbDbManipulationStatement
{
  function getAffectedRowCount()
  {
    return sqlite_changes($this->connection->getConnectionId());
  }
}

?>