<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/cms/src/model/lmbCmsNode.class.php');
lmb_require('limb/cms/src/model/lmbCmsImage.class.php');
lmb_require('limb/cms/src/model/lmbCmsImageFolder.class.php');

/**
 * class lmbCmsImageRepositoryBrowser.
 *
 * @package cms
 * @version $Id: lmbCmsImageRepositoryBrowser.class.php 5945 2007-06-06 08:31:43Z pachanga $
 */
class lmbCmsImageRepositoryBrowser
{
  protected $current_folder;

  function setCurrentFolderPath($path)
  {
    $id = $this->_getLastId($path);
    $this->current_folder = $this->_getNode($id);
  }

  protected function _getNode($id)
  {
    if($id)
      return new lmbCmsNode($id);

    return lmbCmsNode :: findByPath('/images');
  }

  protected function _getLastId($path)
  {
    $path = rtrim($path, '/');
    return (int)end(explode('/', $path));
  }

  function renderFolders()
  {
    $result = '';

    $folders = lmbActiveRecord :: find('lmbCmsImageFolder', 'parent_id = '. $this->current_folder->id);
    foreach($folders as $folder)
      $result .= "<Folder id='{$folder->id}' name='{$folder->title}' />";

    return $result;
  }

  function renderFiles()
  {
    $result = '';

    $files = lmbCmsImage :: findForParentNode($this->current_folder);
    foreach($files as $file)
    {
      $title = htmlspecialchars($file->getNode()->getTitle(), ENT_QUOTES);
      $result .= "<File id='$file->id' thumbnail_url='/file_object/show/{$file->thumbnail->uid}' ";
      $result .= "icon_url='/file_object/show/{$file->icon->uid}' ";
      $result .= "original_url='/file_object/show/{$file->original->uid}' ";
      $result .= "name='{$title}' size='{$file->thumbnail->size}'/>";
    }
    return $result;
  }
}

?>
