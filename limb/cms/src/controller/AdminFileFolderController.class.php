<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/cms/src/controller/AdminNodeController.class.php');
lmb_require('limb/cms/src/model/lmbCmsFileFolder.class.php');
lmb_require('limb/cms/src/model/lmbCmsFileObject.class.php');

class AdminFileFolderController extends AdminNodeController
{
  protected $_form_id = 'file_folder_form';
  protected $_controller_name = 'file_folder';
  protected $_node_class_name = 'lmbCmsFileFolder';
}

?>
