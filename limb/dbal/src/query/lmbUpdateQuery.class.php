<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/dbal/src/query/lmbCriteriaQuery.class.php');

/**
 * class lmbUpdateQuery.
 *
 * @package dbal
 * @version $Id: lmbUpdateQuery.class.php 7890 2009-04-17 14:55:29Z vasiatka $
 */
class lmbUpdateQuery extends lmbCriteriaQuery
{
  protected $_table;
  protected $_fields = array();
  protected $_raw_fields = array();
  protected $_set_values = array();

  function __construct($table, $conn=null)
  {
    $this->_table = $table;
    parent :: __construct("UPDATE %table% SET %fields% %where%", $conn);
    $this->_registerHint('table');
  }

  function getTable()
  {
    return $this->_table;
  }

  function addField($field, $value = LIMB_UNDEFINED)
  {
    $this->_fields[$field] = $value;
    $this->_registerHint('fields');
    return $this;
  }

  function field($field, $value = LIMB_UNDEFINED)
  {
    return $this->addField($field, $value);
  }

  function getFields()
  {
    return $this->_fields;
  }

  function set($values)
  {
    foreach($values as $field => $value)
      $this->addField($field, $value);
    return $this;
  }

  function addRawField($field)
  {
    $this->_raw_fields[] = $field;
    $this->_registerHint('fields');
    return $this;
  }

  function rawField($field)
  {
    return $this->addRawField($field);
  }

  function getRawFields()
  {
    return $this->_raw_fields;
  }

  protected function _getTableHint()
  {
    return $this->_conn->quoteIdentifier($this->_table);
  }

  protected function _getFieldsHint()
  {
    $values = array();
    foreach($this->_fields as $field => $value)
    {
      if($value !== LIMB_UNDEFINED)
        $this->_set_values[$field] = $value;

      $values[] = $this->_conn->quoteIdentifier($field) . " = :{$field}:";
    }

    foreach($this->_raw_fields as $field)
    {
      $values[] = $field;
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

