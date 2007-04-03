<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbMysqlManipulationStatement.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
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