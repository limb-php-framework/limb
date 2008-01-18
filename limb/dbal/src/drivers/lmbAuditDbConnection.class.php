<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/dbal/src/drivers/lmbDbConnection.interface.php');
lmb_require('limb/core/src/lmbDecorator.class.php');

lmbDecorator :: generate('lmbDbConnection', 'lmbDbConnectionDecorator');

/**
 * class lmbAuditDbConnection.
 * Uses in tests to ensure performed sql queries.
 * @package dbal
 * @version $Id$
 */
class lmbAuditDbConnection extends lmbDbConnectionDecorator
{
  protected $queries = null;
  
  function execute($sql)
  {
    $this->queries[] = $sql;
    return parent :: execute($sql);
  }
  
  function newStatement($sql)
  {
    $statement = parent :: newStatement($sql);
    $statement->setConnection($this);
    return $statement;
  }
  
  function count()
  {
    return sizeof($this->queries);
  }
  
  function reset()
  {
    $this->queries = array();
  }
  
  function getQueries($reg_exp = '')
  {
    if(!$reg_exp)
      return $this->queries;
    
    $res = array();
    foreach($this->queries as $query)
    {
      if(preg_match('/' . $reg_exp . '/i', $query))
        $res[] = $query;
    }
    return $res;
  }
}


