<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbMysqlRecordSet.class.php 5557 2007-04-06 11:35:08Z pachanga $
 * @package    dbal
 */
lmb_require('limb/dbal/src/drivers/lmbDbRecordSet.interface.php');

abstract class lmbDbBaseRecordSet implements lmbDbRecordSet
{
  protected $offset;
  protected $limit;
  protected $sort_params;

  function paginate($offset, $limit)
  {
    $this->offset = $offset;
    $this->limit = $limit;
    return $this;
  }

  function getOffset()
  {
    return $this->offset;
  }

  function getLimit()
  {
    return $this->limit;
  }

  function sort($params)
  {
    $this->sort_params = $params;
    return $this;
  }

  function getArray()
  {
    $array = array();
    foreach($this as $record)
      $array[] = $record->export();
    return $array;
  }

  //ArrayAccess interface
  function offsetExists($offset)
  {
    return !is_null($this->offsetGet($offset));
  }

  function offsetGet($offset)
  {
    if(is_numeric($offset))
      return $this->at((int)$offset);
  }

  function offsetSet($offset, $value){}

  function offsetUnset($offset){}
  //end
}

?>
