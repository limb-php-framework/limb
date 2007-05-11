<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbAROneToManyCollection.class.php 5866 2007-05-11 14:13:24Z pachanga $
 * @package    active_record
 */
lmb_require('limb/active_record/src/lmbARRelationCollection.class.php');

class lmbAROneToManyCollection extends lmbARRelationCollection
{
  protected function _createDbRecordSet($extra_criteria = null)
  {
    $class = $this->relation_info['class'];
    $object = new $class();
    $criteria = new lmbSQLFieldCriteria($this->relation_info['field'], $this->owner->getId());

    if($extra_criteria)
      $criteria->addAnd($extra_criteria);

    return $object->getDbTable()->select($criteria);
  }

  function set($objects)
  {
    $old_objects = array();
    foreach($this as $obj)
      $old_objects[$obj->getId()] = $obj;

    foreach($objects as $obj)
    {
      if(!isset($old_objects[$obj->getId()]))
        $this->add($obj);
      else
      {
        $obj->save();
        unset($old_objects[$obj->getId()]);
      }
    }

    foreach($old_objects as $obj)
      $obj->destroy();
  }

  protected function _removeRelatedRecords()
  {
    lmbActiveRecord :: delete($this->relation_info['class'],
                              new lmbSQLFieldCriteria($this->relation_info['field'], $this->owner->getId()));
  }

  protected function _saveObject($object, $error_list = null)
  {
    $object->set($this->relation_info['field'], $this->owner->getId());
    $object->save($error_list);
  }
}

?>
