<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbActiveRecordTools.class.php 4984 2007-02-08 15:35:02Z pachanga $
 * @package    active_record
 */
lmb_require('limb/toolkit/src/lmbAbstractTools.class.php');
lmb_require('limb/active_record/src/lmbActiveRecordMetaInfo.class.php');

class lmbActiveRecordTools extends lmbAbstractTools
{
  protected $metas = array();

  function getActiveRecordMetaInfo($active_record)
  {
    $class_name = get_class($active_record);
    if(isset($this->metas[$class_name]))
      return $this->metas[$class_name];

    $meta = new lmbActiveRecordMetaInfo($active_record);
    $this->metas[$class_name] = $meta;
    return $meta;
  }
}
?>
