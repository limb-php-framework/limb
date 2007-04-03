<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: AdminImageController.class.php 5166 2007-02-28 14:10:28Z tony $
 * @package    cms
 */
lmb_require('limb/cms/src/controller/AdminNodeWithObjectController.class.php');
lmb_require('limb/cms/src/model/lmbCmsImage.class.php');
lmb_require('limb/cms/src/model/lmbCmsImageFileObject.class.php');

class AdminImageController extends AdminNodeWithObjectController
{
  protected $_object_class_name = 'lmbCmsImage';
  protected $_controller_name = 'image';
  protected $_form_name = 'image_form';
  protected $_generate_identifier = true;

  protected function _import()
  {
    $this->item->import($this->request);
    $this->node->import($this->request);

    if($original = $this->_uploadFile('original_image'))
    {
      if($original_size = $this->request->get('original_size'))
        $original->resize($original_size);

      $this->item->setOriginal($original);
    }

    if($thumbnail = $this->_uploadFile('thumbnail_image'))
    {
      if($thumbnail_size = $this->request->get('thumbnail_size'))
        $thumbnail->resize($thumbnail_size);
      $this->item->setThumbnail($thumbnail);
    }
    else
    {
      $thumbnail = clone($original);

      if($thumbnail_size = $this->request->get('thumbnail_size'))
        $thumbnail->resize($thumbnail_size);
      else
        $thumbnail->resize(150);

      $this->item->setThumbnail($thumbnail);
    }

    if($icon = $this->_uploadFile('icon_image'))
    {
      if($icon_size = $this->request->get('icon_size'))
        $icon->resize($icon_size);

      $this->item->setIcon($icon);
    }
    else
    {
      $icon = clone($original);

      if($icon_size = $this->request->get('icon_size'))
        $icon->resize($icon_size);
      else
        $icon->resize(50);

      $this->item->setIcon($icon);
    }
  }

  function _uploadFile($field)
  {
    if (isset($_FILES[$field]) &&
        !is_null($_FILES[$field]['tmp_name']) &&
        is_uploaded_file($_FILES[$field]['tmp_name']))
    {
      $file = $_FILES[$field];
      $file_name = $file['name'];

      $image = new lmbCmsImageFileObject();
      $image->setFileName($file_name);
      $image->setMimeType($file['type']);

      try
      {
        $image->loadFile($file['tmp_name']);
      }
      catch(lmbIOException $e)
      {
        $this->toolkit->flashError('File upload error!');
      }

      return $image;
    }
  }

  function doShow()
  {
    if($image = FileObject :: findById('lmbCmsImage', (int)$this->request->get('id')))
    {
      header('Content-type: ' . $image->thumbnail->getMimeType());
      header('Content-Disposition: filename=' . $image->thumbnail->getName());
      print(file_get_contents($image->thumbnail->getFilePath()));
      exit();
    }
  }
}

?>
