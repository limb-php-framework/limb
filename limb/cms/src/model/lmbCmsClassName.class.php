<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCmsClassName.class.php 4989 2007-02-08 15:35:27Z pachanga $
 * @package    cms
 */
lmb_require('limb/active_record/src/lmbActiveRecord.class.php');
lmb_require('limb/dbal/src/criteria/lmbSQLFieldCriteria.class.php');

class lmbCmsClassName extends lmbActiveRecord
{
  protected $_db_table_name = 'class_name';

  static function generateIdFor($object)
  {
    if(is_object($object))
      $title = get_class($object);
    else
      $title = $object;

    $criteria = new lmbSQLFieldCriteria('title', $title);
    if($obj = lmbActiveRecord :: findFirst('lmbCmsClassName', array('criteria' => $criteria)))
    {
      return $obj->id;
    }
    else
    {
      $class_name = new lmbCmsClassName();
      $class_name->title = $title;
      $class_name->save();
      return $class_name->id;
    }

  }
}

?>
