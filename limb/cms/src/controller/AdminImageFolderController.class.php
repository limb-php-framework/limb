<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/cms/src/controller/AdminNodeController.class.php');
lmb_require('limb/cms/src/model/lmbCmsImageFolder.class.php');
lmb_require('limb/cms/src/model/lmbCmsImage.class.php');

class AdminImageFolderController extends AdminNodeController
{
  protected $_form_id = 'image_folder_form';
  protected $_controller_name = 'image_folder';
  protected $_node_class_name = 'lmbCmsImageFolder';
}

?>
