<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/web_app/src/controller/lmbController.class.php');
lmb_require('limb/cms/src/model/lmbCmsFileObject.class.php');

/**
 * class FileObjectController.
 *
 * @package cms
 * @version $Id: FileObjectController.class.php 5945 2007-06-06 08:31:43Z pachanga $
 */
class FileObjectController extends lmbController
{
  function doShow()
  {
    if($file_object = lmbCmsFileObject :: findByUid('lmbCmsFileObject', $this->request->get('id')))
    {
      header('Content-type: ' . $file_object->getMimeType());
      header('Content-Disposition: filename=' . $file_object->getName());
      print(file_get_contents($file_object->getFilePath()));
      exit();
    }
  }
}
?>