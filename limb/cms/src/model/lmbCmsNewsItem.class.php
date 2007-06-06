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
 * class lmbCmsNewsItem.
 *
 * @package cms
 * @version $Id$
 */
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