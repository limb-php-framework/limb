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
 * class WactArrayIterator.
 *
 * @package wact
 * @version $Id: WactArrayIterator.class.php 6386 2007-10-05 14:22:21Z serega $
 */
class lmbArrayIterator extends ArrayIterator implements lmbCollectionInterface
{
  public $position = 0;
  public $offset = 0;
  public $limit = 0;
  protected $paginated = false;

  function rewind()
  {
    $this->position = 0;
    parent :: rewind();
    
    // goto offset item if possible
    if($this->offset)
    {
      try
      {
        $this->seek($this->offset);
      }
      catch(OutOfBoundsException $e)
      {
        $this->seek($this->count()-1);
        $this->next();
      }
    }
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
  
  function getArray()
  {
    return $this->getArrayCopy();
  }
  
  function at($pos)
  {
    try
    {
      $this->seek($pos);
      return $this->current();
    }
    catch(OutOfBoundsException $e)
    {
      $this->seek($this->count()-1);
      return null;
    }
  }
  
  function sort($params)
  {
    throw new lmbException('Doesn\'t support sorting since ArrayIterator is immutable object');
  }

  function countPaginated()
  {
    if(!$this->paginated)
      return $this->count();

    $total = $this->count();
    
    if($total <= $this->offset || $this->offset < 0)
      return 0;
    
    if(($this->offset + $this->limit) < $total)
      return $this->limit;
    else
      return $total - $this->offset;
  }
}

