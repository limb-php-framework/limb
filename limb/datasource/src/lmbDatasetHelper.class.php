<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDatasetHelper.class.php 4992 2007-02-08 15:35:40Z pachanga $
 * @package    datasource
 */
lmb_require('limb/datasource/src/lmbPagedDatasetDecorator.class.php');

class lmbDatasetHelper extends lmbPagedDatasetDecorator
{
  function getArray($index_field = '')
  {
    return self :: iteratorToArray($this->iterator, $index_field);
  }

  function getRow()
  {
    $this->iterator->rewind();
    $record = $this->iterator->current();
    return $record->export();
  }

  function getValue()
  {
    $this->iterator->rewind();
    $record = $this->iterator->current();
    if($arr = $record->export())
      return $arr[key($arr)];
  }

  static function iteratorToArray($iterator, $index_field = '')
  {
    $arr = array();

    foreach($iterator as $record)
    {
      $record = $iterator->current();

      if($index_field)
        $arr[$record->get($index_field)] = $record->export();
      else
        $arr[] = $record->export();
    }
    return $arr;
  }
}

?>
