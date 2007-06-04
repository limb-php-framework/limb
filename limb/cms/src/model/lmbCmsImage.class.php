<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCmsImage.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */
lmb_require('limb/active_record/src/lmbActiveRecord.class.php');
lmb_require('limb/cms/src/model/lmbCmsImageFileObject.class.php');
lmb_require('limb/cms/src/model/lmbCmsNode.class.php');

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

  static function findForParentNode($parent)
  {
    $sql = 'SELECT image.* '.
           ' FROM image LEFT JOIN node ON node.id = image.node_id '.
           ' WHERE node.parent_id = '. $parent->id;

    $stmt = lmbToolkit :: instance()->getDefaultDbConnection()->newStatement($sql);
    return lmbActiveRecord :: decorateRecordSet($stmt->getRecordSet(), 'lmbCmsImage');
  }
}
?>