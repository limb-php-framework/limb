<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSelectQueryDecorator.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */
lmb_require('limb/dbal/src/query/lmbSelectQuery.class.php');

class lmbSelectQueryDecorator
{
  protected $query;

  function __construct($query)
  {
    $this->query = $query;
  }

  function reset()
  {
    $this->query->reset();
  }

  function addField($field)
  {
    $this->query->addField($field);
  }

  function addTable($table)
  {
    $this->query->addTable($table);
  }

  function addOrder($field, $type='ASC')
  {
    $this->query->addOrder($field, $type);
  }

  function addGroupBy($group)
  {
    $this->query->addGroupBy($group);
  }

  function addLeftJoin($table, $connect_by)
  {
    $this->query->addLeftJoin($table, $connect_by);
  }

  function addCriteria($criteria)
  {
    $this->query->addCriteria($criteria);
  }

  function toString()
  {
    return $this->query->toString();
  }

  function getStatement()
  {
    return $this->query->getStatement();
  }

  function getRecordSet()
  {
    return $this->query->getRecordSet();
  }

  function getStatementValues()
  {
    return $this->query->getStatementValues();
  }
}
?>
