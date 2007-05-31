<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: AdminFileObjectController.class.php 5921 2007-05-31 12:22:12Z pachanga $
 * @package    cms
 */
lmb_require('limb/cms/src/controller/AdminNodeWithObjectController.class.php');
lmb_require('limb/cms/src/model/lmbCmsFileObject.class.php');

class AdminFileObjectController extends AdminNodeWithObjectController
{
  protected $_object_class_name = 'lmbCmsFileObject';
  protected $_controller_name = 'file_object';
  protected $_form_name = 'file_object_form';
  protected $_generate_identifier = true;

  protected function _onBeforeSave()
  {
    $this->_uploadFile('new_file');
  }

  function _uploadFile($field)
  {
    if (isset($_FILES[$field]) &&
        !is_null($_FILES[$field]['tmp_name']) &&
        is_uploaded_file($_FILES[$field]['tmp_name']))
    {
      $file = $_FILES[$field];
      $file_name = $file['name'];

      $this->item->setFileName($file_name);
      $this->item->setMimeType($file['type']);

      try
      {
        $this->item->loadFile($file['tmp_name']);
      }
      catch(lmbFsException $e)
      {
        $this->toolkit->flashError('File upload error!');
      }
    }
  }
}

?>
