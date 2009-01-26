<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package swishe
 * @version $Id$
 */

class lmbSwishSearch
{
	protected $index;
  protected $total;
  protected $result_processor;
  protected $debug = false;
  protected $sort;

	function __construct($index)
	{
    $this->index = $index;
    if(!is_file($this->index))
      throw new Exception("Index file '{$this->index}' not found");
	}

  function debug($flag = true)
  {
    $this->debug = $flag;
  }

  function setResultProcessor($callback)
  {
    $this->result_processor = $callback;
  }

  function setSort($sort)
  {
    $this->sort = $sort;
  }

	function query($query)
	{
    $query = $this->_sanitize($query);
    $result = new lmbSwishResult($this->index, $query);

    if($this->sort)      
      $result->sort($this->sort);

    if($this->result_processor)
      $result->setResultProcessor($this->result_processor);

    return $result;
	}

  function _sanitize($query)
  {
		$query = strip_tags($query);
    $query = preg_replace('~\s+~', ' ', $query);
    $query = $this->_globQuery($query);
    return $query;
  }

  function _globQuery($query)
  {
    $last = strlen($query)-1;
    if($query{0} != '"' && $query{$last} != '"')//ignore quoted strings
    {
      $items = explode(' ', $query);
      $query = '';
      foreach($items as $item)
      {
        if(strpos($item, '*') === false && 
           strcasecmp($item, 'and') != 0 &&
           strcasecmp($item, 'or') != 0)
          $query .= ' ' . $item . '*';
        else   
          $query .= ' ' . $item;  
      }
      $query = ltrim($query);
    }
    return $query;
  }
}

