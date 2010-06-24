<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/dbal/src/drivers/lmbDbInsertStatement.interface.php');
lmb_require(dirname(__FILE__) . '/lmbOciManipulationStatement.class.php');

/**
 * class lmbOciInsertStatement.
 *
 * @package dbal
 * @version $Id: lmbOciInsertStatement.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbOciInsertStatement extends lmbOciManipulationStatement implements lmbDbInsertStatement
{
  protected function _mapHolderToField($name, $sql)
  {
    // a very basic implementation
    if(!preg_match('~INSERT[^\(]+\(([^\)]+)\)[^\(]+\(([^\)]+)~i', $sql, $m))
       throw new lmbDbException("Could not map placeholder :p_$name to field in '$sql'");

    $fields = array_map('trim', explode(',', $m[1]));
    $values = array_map('trim', explode(',', $m[2]));

    if(sizeof($fields) !== sizeof($values))
      throw new lmbDbException("Amount of fields does not match amount of values in '$sql'");

    for($i=0;$i<sizeof($values);$i++)
    {
      if($values[$i] == ":p_$name")
        return strtolower(trim($fields[$i], '"'));
    }

    throw new lmbDbException("Could not map placeholder :p_$name to field in '$sql'");
  }

  function insertId($field_name = 'id')
  {
    $this->execute();

    if(isset($this->parameters[$field_name]) && !empty($this->parameters[$field_name]))
      return $this->parameters[$field_name];
    else
      return $this->connection->getSequenceValue($this->_retriveTableName($this->getSQL()), $field_name);
  }

  protected function _retriveTableName($sql)
  {
    if(!preg_match('/INSERT\s+INTO\s+(\S+)/i', $sql, $m))
      throw new lmbDbException("Could not retrieve table name from query '$sql'");
    return $m[1];
  }
}


