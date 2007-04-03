<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: AdminFileFolderController.class.php 4989 2007-02-08 15:35:27Z pachanga $
 * @package    cms
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
