<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/dbal/src/drivers/lmbDbRecordSet.interface.php');

/**
 * abstract class lmbDbBaseRecordSet.
 *
 * @package dbal
 * @version $Id$
 */
abstract class lmbDbBaseRecordSet implements lmbDbRecordSet
{
  protected $queryId;
  protected $offset;
  protected $limit;
  protected $sort_params;

  function paginate($offset, $limit)
  {
    $this->offset = (int)$offset;
    $this->limit = (int)$limit;
    $this->freeQuery();
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

  function getArray()
  {
    $array = array();
    foreach($this as $record)
      $array[] = $record;
    return $array;
  }

  function getFlatArray()
  {
    $flat_array = array();
    foreach ($this as $record)
      $flat_array[] = $record->export();
    return $flat_array;
  }

  //ArrayAccess interface
  function offsetExists($offset)
  {
    return !is_null($this->offsetGet($offset));
  }

  function offsetGet($offset)
  {
    if(is_numeric($offset))
      return $this->at((int)$offset);
  }

  function offsetSet($offset, $value){}

  function offsetUnset($offset){}
  //end
}


