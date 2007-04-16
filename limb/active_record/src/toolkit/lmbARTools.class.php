<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbARTools.class.php 5664 2007-04-16 09:52:49Z pachanga $
 * @package    active_record
 */
lmb_require('limb/toolkit/src/lmbAbstractTools.class.php');
lmb_require('limb/active_record/src/lmbARMetaInfo.class.php');

class lmbARTools extends lmbAbstractTools
{
  protected $metas = array();

  function getActiveRecordMetaInfo($active_record)
  {
    $class_name = get_class($active_record);
    if(isset($this->metas[$class_name]))
      return $this->metas[$class_name];

    $meta = new lmbARMetaInfo($active_record);
    $this->metas[$class_name] = $meta;
    return $meta;
  }
}
?>
