<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbPgsqlInsertStatement.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */
lmb_require('limb/dbal/src/drivers/lmbDbInsertStatement.interface.php');
lmb_require(dirname(__FILE__) . '/lmbPgsqlManipulationStatement.class.php');

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
