<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbInsertQuery.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */
lmb_require('limb/dbal/src/query/lmbTemplateQuery.class.php');

define('LIMB_INSERT_QUERY_NON_VALUE', uniqid());

class lmbInsertQuery extends lmbTemplateQuery
{
  protected $_table;
  protected $_fields = array();
  protected $_set_values = array();

  function __construct($table, $conn)
  {
    $this->_table = $table;
    parent :: __construct("INSERT INTO %table% (%fields%) VALUES (%values%)", $conn);
  }

  protected function _declareHints()
  {
    return array('table', 'fields', 'values');
  }

  function addField($field, $value = LIMB_INSERT_QUERY_NON_VALUE)
  {
    $this->_fields[$field] = $value;
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
      if($value !== LIMB_INSERT_QUERY_NON_VALUE)
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
?>
