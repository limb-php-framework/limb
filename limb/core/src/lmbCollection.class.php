<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCollection.class.php 5626 2007-04-11 11:50:45Z pachanga $
 * @package    core
 */
lmb_require('limb/core/src/lmbCollectionInterface.interface.php');
lmb_require('limb/core/src/lmbArrayHelper.class.php');
lmb_require('limb/core/src/lmbSet.class.php');

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

  static function toFlatArray($iterator)
  {
    $result = array();
    foreach($iterator as $record)
    {
      if(is_object($record) && method_exists($record, 'export'))
        $result[] = $record->export();
      else
        $result[] = $record;
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
    $values = next($this->iteratedDataset);
    $this->current = $this->_getCurrent($values);
    $this->key = key($this->iteratedDataset);
    $this->valid = $this->_isValid($values);
  }

  function _setupIteratedDataset()
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

  function add($item)
  {
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
    return !is_null($this->offsetGet($offset));
  }

  function offsetGet($offset)
  {
    if(is_numeric($offset))
      return $this->at((int)$offset);
  }

  function offsetSet($offset, $value)
  {
    if(!isset($offset))
      $this->add($value);
  }

  function offsetUnset($offset){}
  //end
}
?>