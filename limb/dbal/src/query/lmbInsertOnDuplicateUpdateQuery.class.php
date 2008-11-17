<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/dbal/src/query/lmbTemplateQuery.class.php');

define('LIMB_INSERT_OR_UPDATE_QUERY_NON_VALUE', uniqid());

/**
 * class lmbInsertQuery.
 *
 * @package dbal
 * @version $Id: lmbInsertQuery.class.php 7165 2008-08-29 11:15:26Z serega $
 */
class lmbInsertOnDuplicateUpdateQuery extends lmbTemplateQuery
{
  protected $_table;
  protected $_fields = array();
  protected $_set_values = array();

  function __construct($table, $conn)
  {
    $this->_table = $table;
    parent :: __construct("INSERT INTO %table% (%fields%) VALUES (%values%) ON DUPLICATE KEY UPDATE %new_values%", $conn);
    $this->_registerHint('table');
    $this->_registerHint('values');
    $this->_registerHint('new_values');
  }

  function addField($field, $value = LIMB_INSERT_OR_UPDATE_QUERY_NON_VALUE)
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
      if($value !== LIMB_INSERT_OR_UPDATE_QUERY_NON_VALUE)
        $this->_set_values[$field] = $value;

      $values[] = ":{$field}:";
    }

    return implode(',', $values);
  }

  protected function _getNewValuesHint()
  {
    $values = array();
    foreach($this->_fields as $field => $value)
      $values[] = '`'.$field . '`' . " = :{$field}:";

    return implode(',', $values);
  }

  function getStatement()
  {
    $stmt = parent :: getStatement();
    foreach($this->_set_values as $key => $value)
      $stmt->set($key, $value);

    return $stmt;
  }

  /**
   * @param lmbDbConnection $connection
   */
  static function isSupportedByDbConnection($connection)
  {
    $supported_types = array('mysql', 'mysqli');
    return in_array($connection->getType(), $supported_types);
  }

}

