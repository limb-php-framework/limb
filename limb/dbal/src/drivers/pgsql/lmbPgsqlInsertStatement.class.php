<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/dbal/src/drivers/lmbDbInsertStatement.interface.php');
lmb_require(dirname(__FILE__) . '/lmbPgsqlManipulationStatement.class.php');

/**
 * class lmbPgsqlInsertStatement.
 *
 * @package dbal
 * @version $Id: lmbPgsqlInsertStatement.class.php 5945 2007-06-06 08:31:43Z pachanga $
 */
class lmbPgsqlInsertStatement extends lmbPgsqlManipulationStatement implements lmbDbInsertStatement
{
  function insertId($field_name = 'id')
  {
    $this->execute();

    if(isset($this->parameters[$field_name]) && !empty($this->parameters[$field_name]))
      return $this->parameters[$field_name];
    else
      return $this->connection->getSequenceValue($this->_retriveTableName($this->getSQL()), $field_name);
  }

  function _retriveTableName($sql)
  {
    preg_match('/INSERT\s+INTO\s+(\S+)/i', $sql, $m);
    return $m[1];
  }
}

?>
