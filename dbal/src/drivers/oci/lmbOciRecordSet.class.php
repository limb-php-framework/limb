<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/dbal/src/drivers/lmbDbBaseRecordSet.class.php');
lmb_require('limb/dbal/src/drivers/oci/lmbOciRecord.class.php');

/**
 * class lmbOciRecordSet.
 *
 * @package dbal
 * @version $Id: lmbOciRecordSet.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbOciRecordSet extends lmbDbBaseRecordSet
{
  protected $query;
  protected $original_stmt;
  protected $stmt;
  protected $connection;

  protected $need_pager = false;

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
    parent :: paginate($offset, $limit);
    $this->need_pager = true;
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


