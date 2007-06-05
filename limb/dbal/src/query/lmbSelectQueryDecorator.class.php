<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
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
