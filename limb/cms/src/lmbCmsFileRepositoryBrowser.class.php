<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCmsFileRepositoryBrowser.class.php 5752 2007-04-23 14:14:56Z serega $
 * @package    cms
 */
lmb_require('limb/cms/src/model/lmbCmsNode.class.php');
lmb_require('limb/cms/src/model/lmbCmsFileObject.class.php');
lmb_require('limb/cms/src/model/lmbCmsFileFolder.class.php');

class lmbCmsFileRepositoryBrowser
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

    return lmbCmsNode :: findByPath('/files');
  }

  protected function _getLastId($path)
  {
    $path = rtrim($path, '/');
    return (int)end(explode('/', $path));
  }

  function renderFolders()
  {
    $result = '';

    $folders = lmbActiveRecord :: find('lmbCmsFileFolder', 'parent_id = '. $this->current_folder->id);
    foreach($folders as $folder)
      $result .= "<Folder id='{$folder->id}' name='{$folder->title}' />";

    return $result;
  }

  function renderFiles()
  {
    $result = '';

    $files = lmbCmsFileObject :: findForParentNode($this->current_folder);
    foreach($files as $file)
    {
      $filename = htmlspecialchars($file->getFileName(), ENT_QUOTES);
      $title = htmlspecialchars($file->getNode()->getTitle(), ENT_QUOTES);
      $result .= "<File id='$file->id' url='/file_object/show/{$file->uid}' name='{$title} ($filename)' size='{$file->size}' />";
    }
    return $result;
  }
}

?>
