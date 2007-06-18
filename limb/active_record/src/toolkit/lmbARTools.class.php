<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/toolkit/src/lmbAbstractTools.class.php');
lmb_require('limb/active_record/src/lmbARMetaInfo.class.php');

/**
 * class lmbARTools.
 *
 * @package active_record
 * @version $Id: lmbARTools.class.php 5997 2007-06-18 12:27:21Z pachanga $
 */
class lmbARTools extends lmbAbstractTools
{
  protected $metas = array();

  function getActiveRecordMetaInfo($active_record, $conn = null)
  {
    $class_name = get_class($active_record);
    if(isset($this->metas[$class_name]))
      return $this->metas[$class_name];

    $meta = new lmbARMetaInfo($active_record, $conn);
    $this->metas[$class_name] = $meta;
    return $meta;
  }
}
?>
