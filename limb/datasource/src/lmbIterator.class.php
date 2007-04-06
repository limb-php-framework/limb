<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbIterator.class.php 5558 2007-04-06 13:02:07Z pachanga $
 * @package    datasource
 */
lmb_require('limb/datasource/src/lmbDataset.interface.php');

class lmbIterator implements lmbDataset
{
  protected $current;
  protected $valid = false;

  function valid()
  {
    return $this->valid;
  }

  function getArray()
  {
    return array();
  }

  function sort($params)
  {
    return $this;
  }

  function current()
  {
    return $this->current;
  }

  function next()
  {
  }

  function rewind()
  {
  }

  function key()
  {
    return null;
  }

  function at($pos)
  {
    return null;
  }

  function add($item)
  {
  }

  //Countable interface
  function count()
  {
    return 0;
  }
  //end

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