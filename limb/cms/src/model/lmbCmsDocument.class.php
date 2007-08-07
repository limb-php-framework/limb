<?php

/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/active_record/src/lmbActiveRecord.class.php');

/**
 * class lmbCmsDocument.
 *
 * @package cms
 * @version $Id$
 */
class lmbCmsDocument extends lmbActiveRecord
{
  protected $_db_table_name = 'document';

  protected $_lazy_attributes = array('content');

  protected $_has_one = array('node' => array('field' => 'node_id',
                                              'class' => 'lmbCmsNode'));

  function _createValidator()
  {
    $validator = new lmbValidator();
    $validator->addRequiredRule('content');
    return $validator;
  }

  static function findKidsForParent($parent_id, $conn = null)
  {
    if(!$parent_id)
      $parent_id = 0;

    $sql = 'SELECT document.* '.
           ' FROM document LEFT JOIN node ON node.id = document.node_id '.
           ' WHERE node.parent_id = '. $parent_id;

    return lmbActiveRecord :: findBySql('lmbCmsDocument', $sql, $conn);
  }

  function getPublishedKids()
  {
    $sql = 'SELECT document.* '.
           ' FROM document LEFT JOIN node ON node.object_id = document.id '.
           ' WHERE node.parent_id = '. $this->getNode()->id .
           ' AND document.is_published = 1';

    return lmbActiveRecord :: findBySql('lmbCmsDocument', $sql, $this->_db_conn);
  }
}


