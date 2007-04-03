<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbOciRecordSet.class.php 5158 2007-02-27 08:00:25Z tony $
 * @package    dbal
 */
lmb_require('limb/dbal/src/drivers/lmbDbRecordSet.interface.php');
lmb_require(dirname(__FILE__) . '/lmbOciRecord.class.php');

class lmbOciRecordSet implements lmbDbRecordSet
{
  protected $queryId;
  protected $query;
  protected $original_stmt;
  protected $stmt;
  protected $connection;

  protected $offset;
  protected $limit;
  protected $need_pager = false;
  protected $sort_params;

  protected $current;
  protected $valid;
  protected $key;

  function __construct($connection, $stmt)
  {
    $this->connection = $connection;
    $this->stmt = $stmt;
    $this->original_stmt = clone $stmt; //used for getting total count
    $this->query = $stmt->getSQL();
  }

  function paginate($offset, $limit)
  {
    $this->offset = $offset;
    $this->limit = $limit;
    $this->need_pager = true;
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
      oci_free_statement($this->queryId);
      $this->queryId = null;
    }
  }

  function rewind()
  {
    $stmt = $this->_prepareStatement();
    $this->queryId = $stmt->execute();
    $this->_prefetch();

    $this->key = 0;
    $this->next();
  }

  protected function _prepareStatement()
  {
    if($this->sort_params)
      $this->stmt->addOrder($this->sort_params);

    if($this->need_pager)
      $this->stmt->paginate($this->offset, $this->limit);

    return $this->stmt;
  }

  protected function _prefetch()
  {
    if(!$this->need_pager)
    {
      if($total_row_count = $this->stmt->count())
        oci_set_prefetch($this->queryId, $total_row_count);
    }
    else
      oci_set_prefetch($this->queryId, $this->limit);
  }

  function next()
  {
    $this->current = new lmbOciRecord();
    $values = oci_fetch_array($this->queryId, OCI_ASSOC+OCI_RETURN_NULLS);
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
    $stmt = clone $this->stmt;
    if($this->sort_params)
      $stmt->addOrder($this->sort_params);
    $stmt->paginate($pos, 1);

    $queryId = $stmt->execute();

    $arr = oci_fetch_array($queryId, OCI_ASSOC+OCI_RETURN_NULLS);
    oci_free_statement($queryId);
    if(is_array($arr))
      return new lmbOciRecord($arr);
  }

  function countPaginated()
  {
    if($this->need_pager)
      $this->stmt->paginate($this->offset, $this->limit);

    return $this->stmt->count();
  }

  function count()
  {
    return $this->original_stmt->count();
  }
}

?>
