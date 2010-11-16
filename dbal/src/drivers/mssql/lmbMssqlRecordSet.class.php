<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/dbal/src/drivers/lmbDbBaseRecordSet.class.php');
lmb_require('limb/dbal/src/drivers/mssql/lmbMssqlRecord.class.php');

/**
 * class lmbMssqlRecordSet.
 *
 * @package dbal
 * @version $Id: lmbMssqlRecordSet.class.php,v 1.1.1.1 2009/06/08 11:57:21 mike Exp $
 */
class lmbMssqlRecordSet extends lmbDbBaseRecordSet
{
  protected $query;
  protected $connection;

  protected $current;
  protected $valid;
  protected $key;

  function __construct($connection, $queryString)
  {
    $this->connection = $connection;
    $this->query = $queryString;
  }

  function freeQuery()
  {
    if(isset($this->queryId) && is_resource($this->queryId))
    {
      @mssql_free_result($this->queryId);
      $this->queryId = null;
    }
  }

  function rewind()
  {
    if(isset($this->queryId) && is_resource($this->queryId) && mssql_num_rows($this->queryId))
    {
      if(@mssql_data_seek($this->queryId, 0) === false)
      {
        $this->connection->_raiseError();
      }
    }
    elseif(!$this->queryId)
    {
      $query = $this->query;

      if(is_array($this->sort_params))
      {
        if(preg_match('~(?<=FROM).+\s+ORDER\s+BY\s+~i', $query))
          $query .= ',';
        else
          $query .= ' ORDER BY ';
        foreach($this->sort_params as $field => $order)
          $query .= $this->connection->quoteIdentifier($field) . " $order,";

        $query = rtrim($query, ',');
      }

      $this->queryId = $this->connection->execute($query);
      if($this->limit)
      {
          @mssql_data_seek($this->queryId, $this->offset);
        //$query .= ' LIMIT ' .
        //$this->offset . ',' .
        //$this->limit;
//        if(preg_match('~(?<=FROM).+\s+ORDER\s+BY\s+(.+)~smi', $query, $matches))
//        {
//            
//        }
//        $query = "select *, row_number() over (order by $order) as row_num from (".$query.") as qtbl order by $order";
      }

    }
    $this->key = 0;
    $this->next();
  }

  private function _trim($data)
  {
    if (is_array($data))
    {
      foreach ($data as $key => $value)
      {
        if (is_numeric($value))
        {
          $data[$key] = $value;
        }
        elseif (is_null($value))
        {
          $data[$key] = null;
        }
        elseif (strlen($value) == 1 && $value == ' ')
        {
          $data[$key] = rtrim($value);
          //$data[$key] = mb_convert_encoding(rtrim($value), 'UTF-8', 'Windows-1251');
        }
        else
        {
          $data[$key] = $value;
        }
      }
    }
    return $data;
  }
  
  function next()
  {
    $this->current = new lmbMssqlRecord();
    $values = $this->_trim(mssql_fetch_assoc($this->queryId));
    $this->current->import($values);
    $this->valid = is_array($values) && (!$this->limit || $this->key < $this->limit);
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
    $query = $this->query;

    if(is_array($this->sort_params))
    {
      $query .= ' ORDER BY ';
      foreach($this->sort_params as $field => $order)
        $query .= $this->connection->quoteIdentifier($field) . " $order,";
      $query = rtrim($query, ',');
    }

    $queryId = $this->connection->execute($query);
    if (mssql_num_rows($queryId) > $pos)
    {
      mssql_data_seek($queryId, $pos);
      $res = mssql_fetch_assoc($queryId);
      mssql_free_result($queryId);
    }
    else
    {
      $res = false;
    }

    if($res)
    {
      $record = new lmbMssqlRecord();
      $record->import($res);
      return $record;
    }
  }

  function countPaginated()
  {
    if(is_null($this->queryId))
      $this->rewind();
    if ($this->limit)
    {
      $tot = mssql_num_rows($this->queryId);
      if ($this->offset+$this->limit < $tot)
      {
        return $this->limit;
      }
      else
      {
        return $tot - $this->offset + $this->limit;
      }
    }
    else
    {
      return mssql_num_rows($this->queryId);
    }
  }

  function count()
  {
    if(!(preg_match("/^\s*SELECT\s+DISTINCT/is", $this->query) || preg_match('/\s+GROUP\s+BY\s+/is', $this->query)) && 
       preg_match("/^\s*SELECT\s+.+\s+FROM\s+/Uis", $this->query))
    {
      $rewritesql = preg_replace('/^\s*SELECT\s.*\s+FROM\s/Uis','SELECT COUNT(*) FROM ', $this->query);
      $rewritesql = preg_replace('/(\sORDER\s+BY\s.*)/is','', $rewritesql);

      $queryId = $this->connection->execute($rewritesql);
      $row = mssql_fetch_row($queryId);
      mssql_free_result($queryId);
      if(is_array($row))
        return $row[0];
    }

    // could not re-write the query, try a different method.
    $queryId = $this->connection->execute($this->query);
    $count = mssql_num_rows($queryId);
    mssql_free_result($queryId);
    return $count;
  }
  
  function cleanup()
  {
    
  }
}


