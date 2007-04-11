<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbARRecordSetDecorator.class.php 5627 2007-04-11 11:51:34Z pachanga $
 * @package    active_record
 */
lmb_require('limb/datasource/src/lmbIteratorDecorator.class.php');
lmb_require('limb/classkit/src/lmbClassPath.class.php');

class lmbARRecordSetDecorator extends lmbIteratorDecorator
{
  protected $class_path;

  function __construct($record_set, $class_path = '')
  {
    $this->class_path = $class_path;

    parent :: __construct($record_set);
  }

  function setClassPath($class_path)
  {
    $this->class_path = $class_path;
  }

  function current()
  {
    if(!$this->class_path)
      throw new lmbException('ActiveRecord class path is not defined');

    if(!$record = parent :: current())
      return null;

    return $this->_createObjectFromRecord($record);
  }

  protected function _createObjectFromRecord($record)
  {
    $object = $this->_createObject($record);
    $object->loadFromRecord($record);
    return $object;
  }

  protected function _createObject($record)
  {
    if($path = $record->get(lmbActiveRecord :: getInheritanceField()))
    {
      $class = end(lmbActiveRecord :: decodeInheritancePath($path));
      if(!class_exists($class))
        throw new lmbException("Class '$class' not found");
      return new $class;
    }
    else
      return lmbClassPath :: create($this->class_path);
  }

  function at($pos)
  {
    if(!$record = parent :: at($pos))
      return null;

    return $this->_createObjectFromRecord($record);
  }

  function getArray()
  {
    $result = array();
    foreach($this as $object)
      $result[] = $object;
    return $result;
  }
}

?>
