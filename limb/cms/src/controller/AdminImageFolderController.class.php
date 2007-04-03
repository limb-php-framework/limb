<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: AdminImageFolderController.class.php 4989 2007-02-08 15:35:27Z pachanga $
 * @package    cms
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
