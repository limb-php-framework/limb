<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/core/src/lmbCollectionInterface.interface.php');

/**
 * class lmbCollectionDecorator.
 *
 * @package core
 * @version $Id$
 */
class lmbCollectionDecorator implements lmbCollectionInterface
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
    $result = array();
    foreach($this as $object)
      $result[] = $object;
    return $result;
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
    return (int) $this->iterator->count();
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

