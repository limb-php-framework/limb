<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/dbal/src/drivers/lmbDbInsertStatement.interface.php');
lmb_require(dirname(__FILE__) . '/lmbMysqlManipulationStatement.class.php');

/**
 * class lmbMysqlInsertStatement.
 *
 * @package dbal
 * @version $Id: lmbMysqlInsertStatement.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbMysqlInsertStatement extends lmbMysqlManipulationStatement implements lmbDbInsertStatement
{
  function insertId($field_name = 'id')
  {
    $this->execute();

    if(isset($this->parameters[$field_name]) && !empty($this->parameters[$field_name]))
      return $this->parameters[$field_name];
    else
      return $this->connection->getSequenceValue(null, null);
  }
}


