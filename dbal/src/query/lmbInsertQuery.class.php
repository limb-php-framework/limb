<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/dbal/src/query/lmbTemplateQuery.class.php');

/**
 * class lmbInsertQuery.
 *
 * @package dbal
 * @version $Id: lmbInsertQuery.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbInsertQuery extends lmbTemplateQuery
{
  protected $_table;
  protected $_fields = array();
  protected $_set_values = array();

  function __construct($table, $conn)
  {
    $this->_table = $table;
    parent :: __construct("INSERT INTO %table% (%fields%) VALUES (%values%)", $conn);
    $this->_registerHint('table');
    $this->_registerHint('values');
  }

  function addField($field, $value = LIMB_UNDEFINED)
  {
    $this->_fields[$field] = $value;
    $this->_registerHint('fields');
    return $this;
  }

  protected function _getTableHint()
  {
    return $this->_conn->quoteIdentifier($this->_table);
  }

  protected function _getFieldsHint()
  {
    return implode(',', array_map(array($this->_conn, 'quoteIdentifier'), array_keys($this->_fields)));
  }

  protected function _getValuesHint()
  {
    $values = array();
    foreach($this->_fields as $field => $value)
    {
      if($value !== LIMB_UNDEFINED)
        $this->_set_values[$field] = $value;

      $values[] = ":{$field}:";
    }

    return implode(',', $values);
  }

  function getStatement()
  {
    $stmt = parent :: getStatement();
    foreach($this->_set_values as $key => $value)
      $stmt->set($key, $value);

    return $stmt;
  }

}

