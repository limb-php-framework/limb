<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSQLFieldBetweenCriteria.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */
lmb_require('limb/dbal/src/criteria/lmbSQLBaseCriteria.class.php');

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

?>
