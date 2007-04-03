<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: FileObjectController.class.php 4989 2007-02-08 15:35:27Z pachanga $
 * @package    cms
 */
lmb_require('limb/web_app/src/controller/lmbController.class.php');
lmb_require('limb/cms/src/model/lmbCmsFileObject.class.php');

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