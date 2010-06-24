<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/dbal/src/drivers/lmbDbManipulationStatement.interface.php');
lmb_require(dirname(__FILE__) . '/lmbMysqliStatement.class.php');

/**
 * class lmbMysqliManipulationStatement.
 *
 * @package dbal
 * @version $Id: lmbMysqliManipulationStatement.class.php 6243 2007-08-29 11:53:10Z pachanga $
 */
class lmbMysqliManipulationStatement extends lmbMysqliStatement implements lmbDbManipulationStatement
{
  function getAffectedRowCount()
  {
    return mysqli_affected_rows($this->connection->getConnectionId());
  }
}


