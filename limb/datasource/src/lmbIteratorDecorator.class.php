<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbIteratorDecorator.class.php 4992 2007-02-08 15:35:40Z pachanga $
 * @package    datasource
 */

class lmbIteratorDecorator implements Iterator
{
  var $iterator;

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
}
?>