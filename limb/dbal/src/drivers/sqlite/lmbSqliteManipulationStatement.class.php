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

lmb_require('limb/dbal/src/drivers/lmbDbManipulationStatement.interface.php');
lmb_require(dirname(__FILE__) . '/lmbSqliteStatement.class.php');

class lmbSqliteManipulationStatement extends lmbSqliteStatement implements lmbDbManipulationStatement
{
  function getAffectedRowCount()
  {
    return mysql_affected_rows($this->connection->getConnectionId());
  }
}

?>