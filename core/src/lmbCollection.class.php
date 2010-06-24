<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/core/src/lmbCollectionInterface.interface.php');
lmb_require('limb/core/src/lmbArrayHelper.class.php');
lmb_require('limb/core/src/lmbSet.class.php');

/**
 * class lmbCollection.
 *
 * @package core
 * @version $Id$
 */
class lmbCollection implements lmbCollectionInterface
{
  protected $dataset;
  protected $iteratedDataset;
  protected $offset = 0;
  protected $limit = 0;
  protected $current;
  protected $valid = false;

  function __construct($array = array())
  {
    $this->dataset = $array;
  }

  static function concat()
  {
    $args = func_get_args();
    $result = array();
    foreach($args as $col)
    {
      foreach($col as $value)
        $result[] = $value;
    }
    return new lmbCollection($result);
  }

  function getArray()
  {
    $result = array();
    foreach($this as $object)
      $result[] = $object;
    return $result;
  }

  static function toFlatArray($iterator, $key_field = '', $export_each = true)
  {
    $result = array();
    foreach($iterator as $record)
    {
      $data = null;
      if(is_object($record) && method_exists($record, 'export') && $export_each)
        $data = $record->export();
      else
        $data = $record;

      if($key_field && isset($data[$key_field]) && ($key = $data[$key_field]))
        $result[$key] = $data;
      else
        $result[] = $data;
    }
    return $result;
  }

  function export()
  {
    return $this->dataset;
  }

  function sort($params)
  {
    if(count($this->dataset))
    {
      $this->dataset = lmbArrayHelper :: sortArray($this->dataset, $params, false);
      $this->iteratedDataset = null;
    }
    return $this;
  }

  function at($pos)
  {
    if(isset($this->dataset[$pos]))
      return $this->dataset[$pos];
  }

  function rewind()
  {
    $this->_setupIteratedDataset();

    $values = reset($this->iteratedDataset);
    $this->current = $this->_getCurrent($values);
    $this->key = key($this->iteratedDataset);
    $this->valid = $this->_isValid($values);
  }

  function next()
  {
    $this->_setupIteratedDataset();

    $values = next($this->iteratedDataset);
    $this->current = $this->_getCurrent($values);
    $this->key = key($this->iteratedDataset);
    $this->valid = $this->_isValid($values);
  }

  function sortByKeys($sort_type = SORT_NUMERIC)
  {
    if(is_array($this->dataset))
      ksort($this->dataset, $sort_type);
  }

  protected function _setupIteratedDataset()
  {
    if(!is_null($this->iteratedDataset))
      return;

    if(!$this->limit)
    {
      $this->iteratedDataset = $this->dataset;
      return;
    }

    if($this->offset < 0 || $this->offset >= count($this->dataset))
    {
      $this->iteratedDataset = array();
      return;
    }

    $to_splice_array = $this->dataset;
    $this->iteratedDataset = array_splice($to_splice_array, $this->offset, $this->limit);

    if(!$this->iteratedDataset)
      $this->iteratedDataset = array();
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

  function paginate($offset, $limit)
  {
    $this->iteratedDataset = null;
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

  protected function _getCurrent($values)
  {
    if(is_object($values))
      return $values;
    else
      return new lmbSet($values);
  }

  protected function _isValid($values)
  {
    return (is_array($values) || is_object($values));
  }

  function add($item, $offset = NULL)
  {
    if(NULL !== $offset)
      $this->dataset[$offset] = $item;
    else
      $this->dataset[] = $item;

    $this->iteratedDataset = null;
  }

  function isEmpty()
  {
    return sizeof($this->dataset) == 0;
  }

  //Countable interface
  function count()
  {
    return sizeof($this->dataset);
  }
  //

  function countPaginated()
  {
    $this->_setupIteratedDataset();
    return count($this->iteratedDataset);
  }

  //ArrayAccess interface
  function offsetExists($offset)
  {
    return isset($this->dataset[$offset]);
  }

  function offsetGet($offset)
  {
    return $this->at($offset);
  }

  function offsetSet($offset, $value)
  {
    $this->add($value, $offset);
  }

  function offsetUnset($offset){}
  //end
}

