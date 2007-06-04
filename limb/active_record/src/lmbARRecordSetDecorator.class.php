<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbARRecordSetDecorator.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */
lmb_require('limb/core/src/lmbCollectionDecorator.class.php');
lmb_require('limb/core/src/lmbClassPath.class.php');

class lmbARRecordSetDecorator extends lmbCollectionDecorator
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
}

?>
