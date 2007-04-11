<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbIteratorDecorator.class.php 5626 2007-04-11 11:50:45Z pachanga $
 * @package    datasource
 */
lmb_require('limb/datasource/src/lmbIteratorInterface.interface.php');

class lmbIteratorDecorator implements lmbIteratorInterface
{
  protected $iterator;

  function __construct($iterator)
  {
    $this->iterator = $iterator;
  }

  function valid()
  {
    return $this->iterator->valid();
  }

  function current()
  {
    return $this->iterator->current();
  }

  function next()
  {
    $this->iterator->next();
  }

  function rewind()
  {
    $this->iterator->rewind();
  }

  function key()
  {
    return $this->iterator->key();
  }

  function sort($params)
  {
    $this->iterator->sort($params);
    return $this;
  }

  function getArray()
  {
    return $this->iterator->getArray();
  }

  function at($pos)
  {
    return $this->iterator->at($pos);
  }

  function paginate($offset, $limit)
  {
    $this->iterator->paginate($offset, $limit);
    return $this;
  }

  function getOffset()
  {
    return $this->iterator->getOffset();
  }

  function getLimit()
  {
    return $this->iterator->getLimit();
  }

  function countPaginated()
  {
    return $this->iterator->countPaginated();
  }

  //Countable interface
  function count()
  {
    return $this->iterator->count();
  }
  //end

  //ArrayAccess interface
  function offsetExists($offset)
  {
    return !is_null($this->at($offset));
  }

  function offsetGet($offset)
  {
    return $this->at($offset);
  }

  function offsetSet($offset, $value)
  {
    return $this->iterator->offsetSet($offset, $value);
  }

  function offsetUnset($offset)
  {
    return $this->iterator->offsetUnset($offset);
  }
  //end
}
?>