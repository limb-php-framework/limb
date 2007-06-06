<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/dbal/src/query/lmbCriteriaQuery.class.php');

define('LIMB_UPDATE_QUERY_NON_VALUE', uniqid());

/**
 * class lmbUpdateQuery.
 *
 * @package dbal
 * @version $Id: lmbUpdateQuery.class.php 5945 2007-06-06 08:31:43Z pachanga $
 */
class lmbUpdateQuery extends lmbCriteriaQuery
{
  protected $_table;
  protected $_fields = array();
  protected $_raw_fields = array();
  protected $_set_values = array();

  function __construct($table, $conn)
  {
    $this->_table = $table;
    parent :: __construct("UPDATE %table% SET %fields% %where%", $conn);
  }

  protected function _declareHints()
  {
    return array('table', 'fields', 'where');
  }

  function addField($field, $value = LIMB_UPDATE_QUERY_NON_VALUE)
  {
    $this->_fields[$field] = $value;
  }

  function addRawField($field)
  {
    $this->_raw_fields[] = $field;
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
      if($value !== LIMB_UPDATE_QUERY_NON_VALUE)
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
?>
