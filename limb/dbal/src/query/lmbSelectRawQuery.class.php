<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/dbal/src/query/lmbCriteriaQuery.class.php');

//TODO: use primitive lexer for parsing sql templates someday...

/**
 * class lmbSelectRawQuery.
 *
 * @package dbal
 * @version $Id: lmbSelectRawQuery.class.php 6005 2007-06-19 21:14:49Z pachanga $
 */
class lmbSelectRawQuery extends lmbCriteriaQuery
{
  const DEFAULT_SQL_TEMPLATE = "SELECT %fields% FROM %tables% %left_join% %where% %group% %having% %order%";

  protected $_fields = array();
  protected $_raw_fields = array();
  protected $_tables = array();
  protected $_left_join_constraints  = array();
  protected $_order = array();
  protected $_having = array();
  protected $_group_by = array();

  function __construct($sql_or_conn = null, $conn = null)
  {
    if(is_object($sql_or_conn))
      parent :: __construct(self :: DEFAULT_SQL_TEMPLATE, $sql_or_conn);
    else
      parent :: __construct($sql_or_conn, $conn);
  }

  function addField($field, $alias = null)
  {
    $this->_fields[$field] = $alias;
    $this->_registerHint('fields');
    return $this;
  }

  function field($field, $alias = null)
  {
    return $this->addField($field, $alias);
  }

  function getFields()
  {
    return array_keys($this->_fields);
  }

  function setFields($fields)
  {
    $this->_fields = array();
    foreach($fields as $alias => $field)
      $this->_fields[$field] = $alias;
    return $this;
  }

  function addRawField($field, $alias = null)
  {
    $this->_raw_fields[$field] = $alias;
    $this->_registerHint('fields');
    return $this;
  }

  function addTable($table, $alias = null)
  {
    $this->_tables[] = array($table, $alias);
    $this->_registerHint('tables');
    return $this;
  }

  function from($table, $alias = null)
  {
    return $this->addTable($table, $alias);
  }

  function getTables()
  {
    $tables = array();
    foreach($this->_tables as $item)
      $tables[] = $item[0];
    return $tables;
  }

  function addOrder($field, $type='ASC')
  {
    if(is_array($field))
    {
      foreach($field as $field_name => $type)
        $this->_order[] = $this->_conn->quoteIdentifier($field_name) . " $type";
    }
    else
      $this->_order[] = $this->_conn->quoteIdentifier($field) . " $type";

    $this->_registerHint('order');
    return $this;
  }

  function addRawOrder($field)
  {
    $this->_order[] = $field;
    $this->_registerHint('order');
    return $this;
  }

  function order($field, $type='ASC')
  {
    return $this->addOrder($field, $type);
  }

  function getOrder()
  {
    return $this->_order;
  }

  function addGroupBy($group)
  {
    $this->_group_by[] = $group;
    $this->_registerHint('group');
    return $this;
  }

  function group($group)
  {
    return $this->addGroupBy($group);
  }

  function getGroupBy()
  {
    return $this->_group_by;
  }

  function addHaving($criteria)
  {
    $this->_having[] = lmbSQLCriteria :: objectify($criteria);
    $this->_registerHint('having');
    return $this;
  }

  function having($criteria)
  {
    return $this->addHaving($criteria);
  }

  function getHaving()
  {
    return $this->_having;
  }

  function addLeftJoin($table, $field, $connect_table, $connect_field, $table_alias = '')
  {
    if(!$table_alias)
      $connect_by = array($table . '.' . $field => $connect_table . '.' . $connect_field);
    else
      $connect_by = array($table_alias . '.' . $field => $connect_table . '.' . $connect_field);

    $this->_left_join_constraints[] = array('table' => $table,
                                            'connect_by' => $connect_by,
                                            'alias' => $table_alias);
    $this->_registerHint('left_join');    
    return $this;
  }

  function join($table, $field, $connect_table, $connect_field)
  {
    return $this->addLeftJoin($table, $field, $connect_table, $connect_field);
  }

  function getJoins()
  {
    return $this->_left_join_constraints;
  }
  
  function toString()
  {
    if(!count($this->_fields) && !count($this->_raw_fields) && !$this->_selectFieldsExists() && $this->_fromClauseExists())
      $this->addRawField('*');
    
    return parent :: toString();
  }

  function getRecordSet()
  {
    $stmt = $this->getStatement();
    return $stmt->getRecordSet();
  }

  function fetch()
  {
    return $this->getRecordSet();
  }

  protected function _getFieldsHint()
  {
    $fields = '';
    foreach($this->_fields as $field => $alias)
    {
      if(strpos($field, '*') !== false)
      {
        $fields .= $field . ',';
        continue;
      }

      $fields .= $this->_conn->quoteIdentifier($field) .
                 ($alias ? ' as ' . $this->_conn->quoteIdentifier($alias) : '') . ',';
    }
    if(count($this->_raw_fields) == 0)
      $fields = rtrim($fields, ',');

    foreach($this->_raw_fields as $field => $alias)
    {
      if(strpos($field, '*') !== false)
      {
        $fields .= $field . ',';
        continue;
      }

      $fields .= $field .  ($alias ? ' as ' . $alias : '') . ',';
    }
    $fields = rtrim($fields, ',');

    if($this->_selectFieldsExists())
    {
      if(count($this->_fields) || count($this->_raw_fields))
        return ',' . $fields;
      else
        return '';
    }
    elseif(count($this->_fields) == 0 && count($this->_raw_fields) == 0)
      return '*';
    else
      return $fields;
  }

  protected function _getTablesHint()
  {
    if(count($this->_tables) == 0)
      return '';

    $tables = '';
    foreach($this->_tables as $item)
    {
      $tables .= $this->_conn->quoteIdentifier($item[0]) .
                 ($item[1] ? ' ' . $this->_conn->quoteIdentifier($item[1]) : '') . ',';
    }
    $tables = rtrim($tables, ',');

    if($this->_selectTablesExists())
      $tables = ',' . $tables;

    return $tables;
  }

  protected function _getLeftJoinHint()
  {
    $join = array();
    foreach($this->_left_join_constraints as $info)
    {
      $table = $info['table'];
      $connect_by = $info['connect_by'];
      $alias = $info['alias'] ? ' AS ' . $this->_conn->quoteIdentifier($info['alias']) : '';
      $foreign_key = $this->_conn->quoteIdentifier(key($connect_by));
      $alias_key = $this->_conn->quoteIdentifier(reset($connect_by));
      $join[] = "LEFT JOIN " . $this->_conn->quoteIdentifier($table) . "$alias ON $foreign_key=$alias_key";
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
    if(!preg_match('~(?<=\Wfrom)((.*?)(?=(where|order|group|limit))|.*)~si', $this->_getNoHintsSQL(), $matches))
      return false;
    
    return (boolean)strlen(trim($matches[1]));
  }

  protected function _fromClauseExists()
  {
    return preg_match('~from\s+~si', $this->_getNoHintsSQL());
  }
}

