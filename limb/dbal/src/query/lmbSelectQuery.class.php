<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSelectQuery.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */
lmb_require('limb/dbal/src/query/lmbCriteriaQuery.class.php');

//TODO: use primitive lexer for parsing sql templates someday...

class lmbSelectQuery extends lmbCriteriaQuery
{
  protected $_fields = array();
  protected $_tables = array();
  protected $_left_join_constraints  = array();
  protected $_order = array();
  protected $_having = array();
  protected $_group_by = array();

  function __construct($sql = null, $conn)
  {
    if(is_null($sql))
      $sql = "SELECT %fields% FROM %tables% %left_join% %where% %group% %having% %order%";

    parent :: __construct($sql, $conn);
  }

  protected function _declareHints()
  {
    return array('fields', 'tables', 'left_join', 'where', 'group', 'having', 'order');
  }

  function addField($field, $alias = null)
  {
    $this->_fields[$field] = $alias;
  }

  function addTable($table, $alias = null)
  {
    $this->_tables[$table] = $alias;
  }

  function addOrder($field, $type='ASC')
  {
    $this->_order[] = "$field $type";
  }

  function addGroupBy($group)
  {
    $this->_group_by[] = $group;
  }

  function addHaving($criteria)
  {
    $this->_having[] = $criteria;
  }

  function addLeftJoin($table, $connect_by)
  {
    $this->_left_join_constraints[$table] = $connect_by;
  }

  function getRecordSet()
  {
    $stmt = $this->getStatement();
    return $stmt->getRecordSet();
  }

  protected function _getFieldsHint()
  {
    $fields = '';
    foreach($this->_fields as $field => $alias)
    {
      $fields .= $this->_conn->quoteIdentifier($field) .
                 ($alias ? ' as ' . $this->_conn->quoteIdentifier($alias) : '') . ',';
    }
    $fields = rtrim($fields, ',');

    if($this->_selectFieldsExists())
    {
      if(count($this->_fields))
        return ',' . $fields;
      else
        return '';
    }
    elseif(count($this->_fields) == 0)
      return '*';
    else
      return $fields;
  }

  protected function _getTablesHint()
  {
    if(count($this->_tables) == 0)
      return '';

    $tables = '';
    foreach($this->_tables as $table => $alias)
    {
      $tables .= $this->_conn->quoteIdentifier($table) .
                 ($alias ? ' ' . $this->_conn->quoteIdentifier($alias) : '') . ',';
    }
    $tables = rtrim($tables, ',');

    if($this->_selectTablesExists())
      $tables = ',' . $tables;

    return $tables;
  }

  protected function _getLeftJoinHint()
  {
    $join = array();
    foreach($this->_left_join_constraints as $table => $connect_by)
    {
      $foreign_key = $this->_conn->quoteIdentifier(key($connect_by));
      $alias_key = $this->_conn->quoteIdentifier(reset($connect_by));
      $join[] = "LEFT JOIN " . $this->_conn->quoteIdentifier($table) . " ON $foreign_key=$alias_key";
    }

    return implode(' ', $join);
  }

  protected function _getGroupHint()
  {
    if (count($this->_group_by) == 0)
      return '';

    $group = implode(',', array_map(array($this->_conn, 'quoteIdentifier'), $this->_group_by));

    if($this->_groupByClauseExists($group_by_args))
    {
      //primitive check if comma is required
      if($group_by_args)
        return ',' . $group;
      else
        return $group;
    }
    else
      return 'GROUP BY ' . $group;
  }

  protected function _getHavingHint()
  {
    if (count($this->_having) == 0)
      return '';

    if(!count($this->_group_by) && !$this->_groupByClauseExists())
      throw new lmbException('can not generate HAVING condition GROUP BY missing');

    $implode = array();
    foreach($this->_having as $criteria)
    {
      $implode[] = $criteria->toStatementString($this->_stmt_values);
    }

    $having = implode(' AND ', $implode);

    if($this->_havingClauseExists($having_args))
    {
      if($having_args)
        return 'AND ' . $having;
      else
        return $having;
    }
    else
      return 'HAVING ' . $having;
  }

  protected function _getOrderHint()
  {
    if (count($this->_order) == 0)
      return '';

    $order = implode(',', $this->_order);

    if($this->_orderByClauseExists($order_by_args))
    {
      //primitive check if comma is required
      if($order_by_args)
        return ',' . $order;
      else
        return $order;
    }
    else
      return 'ORDER BY ' . $order;
  }

  protected function _orderByClauseExists(&$args = '')
  {
    //!!!make it better later
    if(preg_match('~(?<=from).+order\s+by\s(.*)$~si', $this->_getNoHintsSQL(), $matches))
    {
      $args = trim($matches[1]);
      return true;
    }

    return false;
  }

  protected function _groupByClauseExists(&$args = '')
  {
    //!!!make it better later
    if(preg_match('~(?<=\Wfrom).+group\s+by\s(.*)$~si', $this->_getNoHintsSQL(), $matches))
    {
      $args = trim($matches[1]);
      return true;
    }

    return false;
  }

  protected function _havingClauseExists(&$args = '')
  {
    //!!!make it better later
    if(preg_match('~(?<=\Wgroup)\s+by\s+.+?having\s(.*)(order)?$~si', $this->_getNoHintsSQL(), $matches))
    {
      $args = trim($matches[1]);
      return true;
    }

    return false;
  }

  protected function _selectFieldsExists()
  {
    //!!!make it better later
    return preg_match('~^select\s+\S+.+?from~si', $this->_getNoHintsSQL());
  }

  protected function _selectTablesExists()
  {
    return preg_match('~(?<=\Wfrom)\s+\S+(where|order|group)?~si', $this->_getNoHintsSQL());
  }

  protected function _whereClauseExists(&$args = '')
  {
    //primitive check if WHERE was already in sql
    //!!!make it better later
    if(preg_match('~(?<=\Wfrom).+where\s+(.*)~si', $this->_getNoHintsSQL(), $matches))
    {
      if(preg_match('~([a-zA-Z].*)(group|order)?$~si', $matches[1], $args_matches))
        $args = $args_matches[1];

      return true;
    }
    return false;
  }
}
?>
