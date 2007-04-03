<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    cms
 */

lmb_require('limb/active_record/src/lmbActiveRecord.class.php');

class lmbCmsNewsItem extends lmbActiveRecord
{
  protected $_db_table_name = 'news_item';

  protected $_lazy_attributes = array('content');

  protected $_has_one = array('node' => array('field' => 'node_id',
                                              'class' => 'lmbCmsNode'));

  protected $_many_belongs_to = array('image' => array('field' => 'image_id',
                                                       'class' => 'lmbCmsImage',
                                                       'cascade_delete' => false,
                                                       'can_be_null' => true));

  protected function _createValidator()
  {
    $validator = new lmbValidator();
    $validator->addRequiredRule('news_date');
    $validator->addRequiredRule('annotation');
    return $validator;
  }

}
?>