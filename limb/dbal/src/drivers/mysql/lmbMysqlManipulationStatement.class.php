<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbMysqlManipulationStatement.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

lmb_require('limb/dbal/src/drivers/lmbDbManipulationStatement.interface.php');
lmb_require(dirname(__FILE__) . '/lmbMysqlStatement.class.php');

class lmbMysqlManipulationStatement extends lmbMysqlStatement implements lmbDbManipulationStatement
{
  function getAffectedRowCount()
  {
    return mysql_affected_rows($this->connection->getConnectionId());
  }
}

?>