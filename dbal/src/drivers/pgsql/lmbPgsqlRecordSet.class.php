<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

lmb_require('limb/dbal/src/drivers/lmbDbBaseRecordSet.class.php');
lmb_require('limb/dbal/src/drivers/pgsql/lmbPgsqlRecord.class.php');

/**
 * class lmbPgsqlRecordSet.
 *
 * @package dbal
 * @version $Id: lmbPgsqlRecordSet.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbPgsqlRecordSet extends lmbDbBaseRecordSet
{
  protected $connection;
  protected $stmt;

  protected $current;
  protected $valid;
  protected $key;

  function __construct($connection, $statement)
  {
    $this->connection = $connection;
    $this->stmt = $statement;
  }

  function freeQuery()
  {
    if(isset($this->queryId) && is_resource($this->queryId))
    {
      pg_free_result($this->queryId);
      $this->queryId = null;
      $this->stmt->free();
    }
  }

  function rewind()
  {
    if(isset($this->queryId) && is_resource($this->queryId) && pg_num_rows($this->queryId))
    {
      if(pg_result_seek($this->queryId, 0) === false)
        $this->connection->_raiseError("");
    }
    elseif(!$this->queryId)
    {

      $this->stmt->free();
      if(is_array($this->sort_params))
      {
        $this->stmt->addOrder($this->sort_params);
      }

      if($this->limit)
      {
        $this->stmt->addLimit($this->offset, $this->limit);
      }
      $this->queryId = $this->stmt->execute();
    }
    $this->key = 0;
    $this->next();
  }

  function next()
  {
    $this->current = new lmbPgsqlRecord();
    $values = pg_fetch_assoc($this->queryId);
    $this->current->import($values);
    $this->valid = is_array($values);
    $this->key++;
  }

  function valid()
  {
    return $this->valid;
  }

  function current()
  {
    return $this->current;
  }

  function key()
  {
    return $this->key;
  }
  
  function at($pos)
  {
    $stmt = clone $this->stmt;
    $stmt->free();
    if($this->sort_params)
      $stmt->addOrder($this->sort_params);
    $stmt->addLimit($pos, 1);

    $queryId = $stmt->execute();
    $res = pg_fetch_assoc($queryId);
    pg_free_result($queryId);
    
    if($res)
    {
      $record = new lmbPgsqlRecord();
      $record->import($res);
      return $record;
    }
  }
  

  function countPaginated()
  {
    if(is_null($this->queryId))
      $this->rewind();
    return pg_num_rows($this->queryId);
  }

  function count()
  {
    return $this->stmt->count();
  }
}


