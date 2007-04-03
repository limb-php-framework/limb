<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCriteriaQuery.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */
lmb_require('limb/dbal/src/query/lmbTemplateQuery.class.php');
lmb_require('limb/dbal/src/criteria/lmbSQLCriteria.class.php');

class lmbCriteriaQuery extends lmbTemplateQuery
{
  protected $_criterias;
  protected $_stmt_values = array();

  protected function _declareHints()
  {
    return array('where');
  }

  function addCriteria($criteria)
  {
    $this->_criterias[] = lmbSQLCriteria :: objectify($criteria);
  }

  function getStatementValues()
  {
    return $this->_stmt_values;
  }

  function getStatement()
  {
    $stmt = parent :: getStatement();

    foreach($this->_stmt_values as $key => $value)
      $stmt->set($key, $value);

    return $stmt;
  }

  protected function _getWhereHint()
  {
    if(count($this->_criterias) == 0)
      return '';

    $implode = array();
    foreach($this->_criterias as $criteria)
    {
      $implode[] = $criteria->toStatementString($this->_stmt_values);
    }

    $where = implode(' AND ', $implode);

    if($this->_whereClauseExists($where_args))
    {
      if($where_args)
        return 'AND ' . $where;
      else
        return $where;
    }
    else
      return 'WHERE ' . $where;
  }

  protected function _whereClauseExists(&$args = array())
  {
    //primitive check if WHERE was already in sql
    //!!!make it better later
    if(preg_match('~(?<=\Wfrom).+\Wwhere\s+(.*)~si', $this->_getNoHintsSQL(), $matches))
    {
      if(preg_match('~([a-zA-Z].*)$~si', $matches[1], $args_matches))
        $args = $args_matches[1];
      return true;
    }
    return false;
  }
}
?>
