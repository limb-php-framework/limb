<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

lmb_require('limb/dbal/src/drivers/lmbDbBaseRecordSet.class.php');
lmb_require('limb/dbal/src/drivers/pgsql/lmbPgsqlRecord.class.php');

class lmbPgsqlRecordSet extends lmbDbBaseRecordSet
{
  protected $queryId;
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
