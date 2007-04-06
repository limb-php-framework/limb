<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbIteratorDecorator.class.php 5558 2007-04-06 13:02:07Z pachanga $
 * @package    datasource
 */
lmb_require('limb/datasource/src/lmbDataset.interface.php');

class lmbIteratorDecorator implements lmbDataset
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
  }

  function getArray()
  {
    return $this->iterator->getArray();
  }

  function at($pos)
  {
    return $this->iterator->at($pos);
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
    return $this->iterator->offsetExists($offset);
  }

  function offsetGet($offset)
  {
    return $this->iterator->offsetGet($offset);
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