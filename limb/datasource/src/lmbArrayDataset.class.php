<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbArrayDataset.class.php 5621 2007-04-11 09:36:16Z pachanga $
 * @package    datasource
 */
lmb_require('limb/datasource/src/lmbComplexArray.class.php');
lmb_require('limb/datasource/src/lmbDataspace.class.php');
lmb_require('limb/datasource/src/lmbIterator.class.php');

class lmbArrayDataset extends lmbIterator
{
  protected $dataset;

  function __construct($array = array())
  {
    $this->dataset = $array;
  }

  function getArray()
  {
    return $this->dataset;
  }

  function export()
  {
    return $this->dataset;
  }

  function sort($params)
  {
    $this->dataset = lmbComplexArray :: sortArray($this->dataset, $params, false);
    return $this;
  }

  function at($pos)
  {
    if(isset($this->dataset[$pos]))
      return $this->dataset[$pos];
  }

  function rewind()
  {
    $values = reset($this->dataset);
    $this->current = $this->_getCurrent($values);
    $this->key = key($this->dataset);
    $this->valid = $this->_isValid($values);
  }

  function next()
  {
    $values = next($this->dataset);
    $this->current = $this->_getCurrent($values);
    $this->key = key($this->dataset);
    $this->valid = $this->_isValid($values);
  }

  protected function _getCurrent($values)
  {
    if(is_object($values))
      return $values;
    else
      return new lmbDataspace($values);
  }

  protected function _isValid($values)
  {
    return (is_array($values) || is_object($values));
  }

  function add($item)
  {
    $this->dataset[] = $item;
  }

  function count()
  {
    return sizeof($this->dataset);
  }

  function isEmpty()
  {
    return sizeof($this->dataset) == 0;
  }
}
?>
