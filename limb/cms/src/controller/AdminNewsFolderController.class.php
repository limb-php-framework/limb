<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    $package$
 */
lmb_require('limb/cms/src/controller/AdminNodeController.class.php');
lmb_require('limb/cms/src/model/lmbCmsNewsFolder.class.php');

class AdminNewsFolderController extends AdminNodeController
{
  protected $_form_id = 'news_folder_form';
  protected $_controller_name = 'news_folder';
  protected $_node_class_name = 'lmbCmsNewsFolder';
}

?>
