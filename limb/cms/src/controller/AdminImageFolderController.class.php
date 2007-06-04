<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: AdminImageFolderController.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
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
