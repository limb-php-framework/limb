<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSQLBaseCriteria.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */
//inspired by Propel Criteria(http://propel.phpdb.org)
lmb_require('limb/dbal/src/criteria/lmbSQLCriteria.class.php');

abstract class lmbSQLBaseCriteria
{
  const _AND_ = " AND ";
  const _OR_ = " OR ";

  protected $clauses = array();
  protected $conjunctions = array();

  //'and' & 'or' are keywords in php :(
  function addAnd($criteria)
  {
    $this->clauses[] = lmbSQLCriteria :: objectify($criteria);
    $this->conjunctions[] = self::_AND_;
    return $this;
  }

  function addOr($criteria)
  {
    $this->clauses[] = lmbSQLCriteria :: objectify($criteria);
    $this->conjunctions[] = self::_OR_;
    return $this;
  }

  protected function _getClauses()
  {
    return $this->clauses;
  }

  protected function _getConjunctions()
  {
    return $this->conjunctions;
  }

  function toStatementString(&$values = array(), $conn = null)
  {
    $str = '';
    $this->appendStatementTo($str, $values, $conn);
    return $str;
  }

  function appendStatementTo(&$str, &$values = array(), $conn = null)
  {
    if(!is_object($conn))
      $conn = lmbToolkit :: instance()->getDefaultDbConnection();

    $this->_appendOpeningParenthesisToStatement($str, $values, $conn);

    $this->_appendExpressionToStatement($str, $values, $conn);

    $this->_appendClosingParenthesisToStatement($str, $values, $conn);
  }

  protected function _appendExpressionToStatement(&$str, &$values, $conn){}

  protected function _appendOpeningParenthesisToStatement(&$str, &$values, $conn)
  {
    for($j = 0; $j < count($this->clauses); $j++)
      $str .= '(';
  }

  protected function _appendClosingParenthesisToStatement(&$str, &$values, $conn)
  {
    for($i=0; $i < count($this->clauses); $i++)
    {
      $str .= $this->conjunctions[$i];
      $this->clauses[$i]->appendStatementTo($str, $values, $conn);
      $str .= ')';
    }
  }

  function getAttachedCriterias()
  {
      $crits = array();
      $this->_traverseCriteria($this, $crits);
      return $crits;
  }

  protected function _traverseCriteria($c, &$a)
  {
    $a[] = $c;
    $clauses = $c->_getClauses();
    $clausesLength = count($clauses);
    for($i=0; $i < $clausesLength; $i++)
      $this->_traverseCriteria($clauses[$i], $a);
  }

  protected function _makePlaceHolder($holder)
  {
    return 'p' . str_replace('.', '_', $holder);
  }
}
?>
