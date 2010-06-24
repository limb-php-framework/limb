<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/dbal/src/drivers/lmbDbConnection.interface.php');
lmb_require('limb/core/src/lmbBacktrace.class.php');
lmb_require('limb/core/src/lmbDecorator.class.php');

lmbDecorator :: generate('lmbDbConnection', 'lmbDbConnectionDecorator');

/**
 * class lmbAuditDbConnection.
 * Remembers stats for later analysis, especially useful in tests
 * @package dbal
 * @version $Id$
 */
class lmbAuditDbConnection extends lmbDbConnectionDecorator
{
  protected $stats = array();
  
  function execute($sql)
  {
    $info = array('query' => $sql);
    $info['trace'] = $this->getTrace();
    $start_time = microtime(true);
    $res = parent :: execute($sql);
    $info['time'] = round(microtime(true) - $start_time, 6);
    $this->stats[] = $info;
    return $res;
  }
  
  function executeStatement($stmt)
  {
    $info = array('query' => $stmt->getSQL());
    $info['trace'] = $this->getTrace();
    $start_time = microtime(true);
    $res = parent :: executeStatement($stmt);
    $info['time'] = round(microtime(true) - $start_time, 6);
    $this->stats[] = $info;
    return $res;
  }
  
  function newStatement($sql)
  {
    $statement = parent :: newStatement($sql);
    $statement->setConnection($this);
    return $statement;
  }
  
  function countQueries()
  {
    return sizeof($this->stats);
  }
  
  function resetStats()
  {
    $this->stats = array();
  }
  
  function getQueries($reg_exp = '')
  {
    $res = array();
    foreach($this->stats as $info)
    {
      $query = $info['query'];
      if(!$reg_exp || preg_match('/' . $reg_exp . '/i', $query))
        $res[] = $query;
    }

    return $res;
  }
  
  function getTrace() 
  {
  	$trace_length = 8;
  	$offset = 4; // getting rid of useless trace elements
  	
  	$trace = new lmbBacktrace($trace_length, $offset);
  	return $trace->toString();
  }
  
  function getStats()
  {
    return $this->stats; 
  }
  
  
}


