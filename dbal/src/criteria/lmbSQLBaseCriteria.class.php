<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
//inspired by Propel Criteria(http://propel.phpdb.org)
lmb_require('limb/dbal/src/criteria/lmbSQLCriteria.class.php');

/**
 * abstract class lmbSQLBaseCriteria.
 *
 * @package dbal
 * @version $Id: lmbSQLBaseCriteria.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
abstract class lmbSQLBaseCriteria
{
  const _AND_ = " AND ";
  const _OR_ = " OR ";

  protected $criteria = array();
  protected $conjunctions = array();
  protected $not = false;
  protected $not_all = false;

  //'and' & 'or' are keywords in php :(
  function addAnd($criteria)
  {
    $this->criteria[] = lmbSQLCriteria :: objectify($criteria);
    $this->conjunctions[] = self::_AND_;
    return $this;
  }

  function add($criteria)
  {
    return $this->addAnd($criteria);
  }

  function addOr($criteria)
  {
    $this->criteria[] = lmbSQLCriteria :: objectify($criteria);
    $this->conjunctions[] = self::_OR_;
    return $this;
  }

  function not()
  {
    $this->not = !$this->not;
    return $this;
  }

  function notAll()
  {
    $this->not_all = !$this->not_all;
    return $this;
  }

  function isComplex()
  {
    return sizeof($this->criteria) > 0;
  }

  protected function _getClauses()
  {
    return $this->criteria;
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

    if($this->not_all)
      $str .= 'NOT(';

    if($this->not)
      $str .= 'NOT(';

    $this->_appendExpressionToStatement($str, $values, $conn);

    if($this->not)
      $str .= ')';

    for($i=0; $i < count($this->criteria); $i++)
    {
      $criteria = $this->criteria[$i];
      $str .= $this->conjunctions[$i];

      if($criteria->isComplex())
        $str .= '(';

      $criteria->appendStatementTo($str, $values, $conn);

      if($criteria->isComplex())
        $str .= ')';
    }

    if($this->not_all)
      $str .= ')';
  }

  protected function _appendExpressionToStatement(&$str, &$values, $conn){}
}

