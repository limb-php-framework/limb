<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbPagedDatasetDecorator.class.php 5558 2007-04-06 13:02:07Z pachanga $
 * @package    datasource
 */
lmb_require('limb/datasource/src/lmbIteratorDecorator.class.php');
lmb_require('limb/datasource/src/lmbPagedDataset.interface.php');

class lmbPagedDatasetDecorator extends lmbIteratorDecorator implements lmbPagedDataset
{
  function paginate($offset, $limit)
  {
    $this->iterator->paginate($offset, $limit);
    return $this;
  }

  function sort($params)
  {
    $this->iterator->sort($params);
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
}
?>
