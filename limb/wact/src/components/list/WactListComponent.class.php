<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactListComponent.class.php 5188 2007-03-06 07:42:21Z serega $
 * @package    wact
 */

/**
* Represents list tags at runtime, providing an API for preparing the data set
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

  function current()
  {
    return $this->dataset->current();
  }

  function getOffset()
  {
    return $this->dataset->getOffset();
  }
}
?>