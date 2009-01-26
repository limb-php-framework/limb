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
 * class lmbSQLFieldBetweenCriteria.
 *
 * @package dbal
 * @version $Id: lmbSQLFieldBetweenCriteria.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbSQLFieldBetweenCriteria extends lmbSQLBaseCriteria
{
  protected $value_from;
  protected $value_to;
  protected $column;

  function __construct($column, $value_from, $value_to)
  {
    $this->column = $column;
    $this->value_from = $value_from;
    $this->value_to = $value_to;
  }

  protected function _makePlaceHolder($holder)
  {
    return 'p' . str_replace('.', '_', $holder);
  }

  protected function _appendExpressionToStatement(&$str, &$values, $conn)
  {
    $str .= $conn->quoteIdentifier($this->column) . ' BETWEEN ';

    $from_placeholder = $this->_makePlaceHolder('f' .$this->column . sizeof($values));
    $to_placeholder = $this->_makePlaceHolder('t' .$this->column . sizeof($values));

    $str .= ':' . $from_placeholder . ': AND :' . $to_placeholder . ':';
    $values[$from_placeholder] = $this->value_from;
    $values[$to_placeholder] = $this->value_to;
  }
}


