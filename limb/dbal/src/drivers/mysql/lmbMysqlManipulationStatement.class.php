<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

lmb_require('limb/dbal/src/drivers/lmbDbManipulationStatement.interface.php');
lmb_require(dirname(__FILE__) . '/lmbMysqlStatement.class.php');

/**
 * class lmbMysqlManipulationStatement.
 *
 * @package dbal
 * @version $Id: lmbMysqlManipulationStatement.class.php 5945 2007-06-06 08:31:43Z pachanga $
 */
class lmbMysqlManipulationStatement extends lmbMysqlStatement implements lmbDbManipulationStatement
{
  function getAffectedRowCount()
  {
    return mysql_affected_rows($this->connection->getConnectionId());
  }
}

?>