<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/active_record/src/lmbActiveRecord.class.php');
lmb_require('limb/cms/src/model/lmbCmsImageFileObject.class.php');
lmb_require('limb/cms/src/model/lmbCmsNode.class.php');

/**
 * class lmbCmsImage.
 *
 * @package cms
 * @version $Id: lmbCmsImage.class.php 5998 2007-06-18 12:28:49Z pachanga $
 */
class lmbCmsImage extends lmbActiveRecord
{
  protected $_has_one = array('original' => array('field' => 'original_id',
                                                     'class' => 'lmbCmsImageFileObject'),
                              'thumbnail' => array('field' => 'thumbnail_id',
                                                     'class' => 'lmbCmsImageFileObject'),
                              'icon' => array('field' => 'icon_id',
                                              'class' => 'lmbCmsImageFileObject'),
                              'node' => array('field' => 'node_id',
                                              'class' => 'lmbCmsNode'));

  protected $_db_table_name = 'image';

  protected function _createValidator()
  {
    $validator = new lmbValidator();
    $validator->addRequiredObjectRule('original');
    $validator->addRequiredObjectRule('thumbnail');
    $validator->addRequiredObjectRule('icon');
    return $validator;
  }

  static function findForParentNode($parent, $conn = null)
  {
    $sql = 'SELECT image.* '.
           ' FROM image LEFT JOIN node ON node.id = image.node_id '.
           ' WHERE node.parent_id = '. $parent->id;

    if(!is_object($conn))
      $conn = lmbActiveRecord :: getDefaultConnection();

    $stmt = $conn->newStatement($sql);
    return lmbActiveRecord :: decorateRecordSet($stmt->getRecordSet(), 'lmbCmsImage', $conn);
  }
}
?>