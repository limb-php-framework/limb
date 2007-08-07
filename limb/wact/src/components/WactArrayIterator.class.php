<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class WactArrayIterator.
 *
 * @package wact
 * @version $Id: WactArrayIterator.class.php 6221 2007-08-07 07:24:35Z pachanga $
 */
class WactArrayIterator extends ArrayIterator
{
  public $position = 0;
  public $offset = 0;
  public $limit = 0;
  protected $paginated = false;

  function rewind()
  {
    $this->position = 0;
    parent :: rewind();
    if($this->offset)
      $this->seek($this->offset);
  }

  function current()
  {
    return new WactArrayObject(parent :: current());
  }

  function next()
  {
    $this->position++;
    return parent :: next();
  }

  function valid()
  {
    if($this->limit && ($this->position >= $this->limit))
      return false;
    return parent :: valid();
  }

  function getOffset()
  {
    return $this->offset;
  }

  function getLimit()
  {
    return $this->limit;
  }

  function paginate($offset, $limit)
  {
    $this->offset = $offset;
    $this->limit = $limit;
    $this->paginated = true;
  }

  function countPaginated()
  {
    if(!$this->paginated)
      return $this->count();

    $total = $this->count();
    if(($this->offset + $this->limit) < $total)
      return $this->limit;
    else
      return $total - $this->offset;
  }
}

