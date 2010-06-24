<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/dbal/src/criteria/lmbSQLBaseCriteria.class.php');

/**
 * class lmbSQLFieldCriteria.
 *
 * @package dbal
 * @version $Id: lmbSQLFieldCriteria.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbSQLFieldCriteria extends lmbSQLBaseCriteria
{
  const EQUAL = "=";
  const NOT_EQUAL = "<>";
  const GREATER = ">";
  const LESS = "<";
  const GREATER_EQUAL = ">=";
  const LESS_EQUAL = "<=";
  const LIKE = " LIKE ";
  const NOT_LIKE = " NOT LIKE ";
  const IN = " IN ";
  const NOT_IN = " NOT IN ";
  const IS_NULL = " IS NULL";
  const IS_NOT_NULL = " IS NOT NULL";

  protected $value;
  protected $comparison;
  protected $column;

  function __construct($column, $value, $comparison = null)
  {
    $this->column = $column;
    $this->value = $value;
    $this->comparison = ($comparison === null ? self::EQUAL : $comparison);
  }

  function getColumn()
  {
    return $this->column;
  }

  function getComparison()
  {
    return $this->comparison;
  }

  function getValue()
  {
    return $this->value;
  }

  protected function _makePlaceHolder($holder)
  {
    return 'p' . str_replace('.', '_', $holder);
  }

  protected function _appendExpressionToStatement(&$str, &$values, $conn)
  {
    $field = $conn->quoteIdentifier($this->column);

    $stmt_placeholder = $this->_makePlaceHolder(sizeof($values) . $this->column);

    // OPTION 1:  column IN (?, ?) or column NOT IN (?, ?)
    if ($this->comparison === self::IN || $this->comparison === self::NOT_IN)
    {
      $str .= $field . $this->comparison;

      $inString = '(';
      foreach($this->value as $key => $value)
      {
        $stmt_in_placeholder = $this->_makePlaceHolder($key . '_' . $stmt_placeholder);
        $values[$stmt_in_placeholder] = $value;
        $inString .= ':' . $stmt_in_placeholder . ':,';
      }
      $str .= rtrim($inString, ',') . ')';
    }
    // OPTION 2: column LIKE ? or column NOT LIKE ?
    elseif($this->comparison === self::LIKE || $this->comparison === self::NOT_LIKE)
    {
      $str .= $field . $this->comparison . ':' . $stmt_placeholder . ':';
      $values[$stmt_placeholder] = $this->value;
    }
    // OPTION 3:  table.column = ? or table.column >= ? etc. (traditional expressions, the default)
    else
    {
      // null VALUES need special treatment because the SQL syntax is different
      // i.e. column IS null rather than column = null
      if($this->value !== null)
      {
        $str .= $field . $this->comparison . ':' . $stmt_placeholder . ':';
        $values[$stmt_placeholder] = $this->value;
      }
      else
      {
        // value is null, which means it was either not specified or specifically
        // set to null.
        if($this->comparison === self::EQUAL || $this->comparison === self::IS_NULL)
        {
          $str .= $field . self::IS_NULL;
        }
        elseif($this->comparison === self::NOT_EQUAL || $this->comparison === self::IS_NOT_NULL)
        {
          $str .= $field . self::IS_NOT_NULL;
        }
        else
        {
          // for now throw an exception, because not sure how to interpret this
          throw new lmbException("Could not build SQL for expression: $field " . $this->comparison . " null");
        }
      }
    }
  }
}

