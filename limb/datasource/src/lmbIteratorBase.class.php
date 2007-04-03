<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbIteratorBase.class.php 5227 2007-03-13 14:13:56Z serega $
 * @package    datasource
 */
lmb_require('limb/datasource/src/lmbDataset.interface.php');


abstract class lmbIteratorBase implements lmbDataset
{
  protected $current;
  protected $valid = false;

  function valid()
  {
    return $this->valid;
  }

  function getArray()
  {
    return array();
  }

  function sort($params)
  {
    return $this;
  }

  function current()
  {
    return $this->current;
  }

  function next()
  {
  }

  function rewind()
  {
  }

  function key()
  {
    return null;
  }

  function at($pos)
  {
    return null;
  }

  function count()
  {
    return 0;
  }
}
?>