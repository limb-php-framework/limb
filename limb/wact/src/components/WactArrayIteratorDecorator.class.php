<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class WactArrayIteratorDecorator.
 *
 * @package wact
 * @version $Id$
 */
class WactArrayIteratorDecorator implements Iterator, Countable
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

  function paginate($offset, $limit)
  {
    $this->iterator->paginate($offset, $limit);
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

  function count()
  {
    return $this->iterator->count();
  }
}

