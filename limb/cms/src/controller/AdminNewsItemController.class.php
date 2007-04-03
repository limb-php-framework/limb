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
lmb_require('limb/cms/src/controller/AdminNodeWithObjectController.class.php');
lmb_require('limb/cms/src/model/lmbCmsNewsItem.class.php');

class AdminNewsItemController extends AdminNodeWithObjectController
{
  protected $_object_class_name = 'lmbCmsNewsItem';
  protected $_controller_name = 'news_item';
  protected $_form_name = 'news_item_form';
  protected $_generate_identifier = true;

  protected function _initCreateForm()
  {
    $this->item->setNewsDate(time());
    $this->request->merge($this->item->export());
  }
}

?>
