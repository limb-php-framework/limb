<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * Represents list tags at runtime, providing an API for preparing the data set
 * @package wact
 * @version $Id: WactListComponent.class.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactListComponent extends WactRuntimeComponent
{
  protected $dataset;
  protected $cached_count = null;

  function __construct($id)
  {
    parent :: __construct($id);

    $this->dataset = new WactArrayIterator(array());
  }

  function registerDataset($dataset)
  {
    $this->dataset = WactTemplate :: castToIterator($dataset);
    $this->cached_count = null;
  }

  function getDataset()
  {
    return $this->dataset;
  }

  function rewind()
  {
    $this->dataset->rewind();
  }

  function count()
  {
    if(is_null($this->cached_count))
      $this->cached_count = $this->dataset->count();

    return $this->cached_count;
  }

  function countPaginated()
  {
    if(method_exists($this->dataset, 'countPaginated'))
      return $this->dataset->countPaginated();
    else
      return $this->count();
  }

  function next()
  {
    $this->dataset->next();
  }

  function valid()
  {
    return $this->dataset->valid();
  }

  function key()
  {
    return $this->dataset->key();
  }

  function current()
  {
    return $this->dataset->current();
  }

  function getOffset()
  {
    return $this->dataset->getOffset();
  }
}

