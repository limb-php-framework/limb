<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
//inspired by Propel Criteria(http://propel.phpdb.org)
lmb_require('limb/dbal/src/criteria/lmbSQLCriteria.class.php');

/**
 * abstract class lmbSQLBaseCriteria.
 *
 * @package dbal
 * @version $Id: lmbSQLBaseCriteria.class.php 6047 2007-07-02 22:30:59Z pachanga $
 */
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

  function add($criteria)
  {
    return $this->addAnd($criteria);
  }

  function addOr($criteria)
  {
    $this->clauses[] = lmbSQLCriteria :: objectify($criteria);
    $this->conjunctions[] = self::_OR_;
    return $this;
  }

  function isComplex()
  {
    return sizeof($this->clauses) > 0;
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

    $this->_appendExpressionToStatement($str, $values, $conn);

    for($i=0; $i < count($this->clauses); $i++)
    {
      $criteria = $this->clauses[$i];
      $str .= $this->conjunctions[$i];

      if($criteria->isComplex())
        $str .= '(';

      $criteria->appendStatementTo($str, $values, $conn);

      if($criteria->isComplex())
        $str .= ')';
    }
  }

  protected function _appendExpressionToStatement(&$str, &$values, $conn){}
}
?>
