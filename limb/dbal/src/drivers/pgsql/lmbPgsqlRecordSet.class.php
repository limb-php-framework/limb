<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbPgsqlRecordSet.class.php 5557 2007-04-06 11:35:08Z pachanga $
 * @package    dbal
 */

lmb_require('limb/dbal/src/drivers/lmbDbRecordSet.interface.php');
lmb_require(dirname(__FILE__) . '/lmbPgsqlRecord.class.php');

class lmbPgsqlRecordSet implements lmbDbRecordSet
{
  protected $queryId;
  protected $query;
  protected $connection;

  protected $offset;
  protected $limit;
  protected $sort_params;

  protected $current;
  protected $valid;
  protected $key;

  function __construct($connection, $queryString)
  {
    $this->connection = $connection;
    $this->query = $queryString;
  }

  function paginate($offset, $limit)
  {
    $this->offset = $offset;
    $this->limit = $limit;
    return $this;
  }

  function getOffset()
  {
    return $this->offset;
  }

  function getLimit()
  {
    return $this->limit;
  }

  function sort($params)
  {
    $this->sort_params = $params;
    return $this;
  }

  function freeQuery()
  {
    if(isset($this->queryId) && is_resource($this->queryId))
    {
      pg_free_result($this->queryId);
      $this->queryId = null;
    }
  }

  function rewind()
  {
    if(isset($this->queryId) && is_resource($this->queryId) && pg_num_rows($this->queryId))
    {
      if(pg_result_seek($this->queryId, 0) === false)
        $this->connection->_raiseError();
    }
    elseif(!$this->queryId)
    {
      $query = $this->query;

      if(is_array($this->sort_params))
      {
        if(preg_match('~\s+ORDER\s+BY\s+~i', $query))
          $query .= ',';
        else
          $query .= ' ORDER BY ';
        foreach($this->sort_params as $field => $order)
          $query .= $this->connection->quoteIdentifier($field) . " $order,";

        $query = rtrim($query, ',');
      }

      if($this->limit)
      {
        $query .= ' LIMIT ' . $this->limit;
        $query .= ' OFFSET ' . $this->offset;
      }
      $this->queryId = $this->connection->execute($query);
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

  function getArray()
  {
    $array = array();
    foreach($this as $record)
      $array[] = $record->export();
    return $array;
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

    $queryId = $this->connection->execute($query . " LIMIT 1 OFFSET $pos");

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
    if(!(preg_match("/^\s*SELECT\s+DISTINCT/is", $this->query) || preg_match('/\s+GROUP\s+BY\s+/is',$this->query)))
    {
      $rewritesql = preg_replace('/^\s*SELECT\s.*\s+FROM\s/Uis','SELECT COUNT(*) FROM ', $this->query);
      $rewritesql = preg_replace('/(\sORDER\s+BY\s.*)/is', '', $rewritesql);

      $queryId = $this->connection->execute($rewritesql);
      $row = pg_fetch_row($queryId);
      pg_free_result($queryId);
      if(is_array($row))
        return $row[0];
    }

    // could not re-write the query, try a different method.
    $queryId = $this->connection->execute($this->query);
    $count = pg_num_rows($queryId);
    pg_free_result($queryId);
    return $count;
  }
}

?>
