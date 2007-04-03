<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCmsTreeBrowser.class.php 4989 2007-02-08 15:35:27Z pachanga $
 * @package    cms
 */
lmb_require('limb/cms/src/model/lmbCmsNode.class.php');

class lmbCmsTreeBrowser
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

    return new lmbCmsNode();
  }

  protected function _getLastId($path)
  {
    $path = rtrim($path, '/');
    return (int)end(explode('/', $path));
  }

  function renderFolders()
  {
    $result = '';

    if($this->current_folder->id)
       $folders = lmbActiveRecord :: find('lmbCmsNode', 'parent_id = '. $this->current_folder->id);
    else
      $folders = $this->current_folder->getRoots();

    foreach($folders as $folder)
    {
      $title = htmlspecialchars($folder->title, ENT_QUOTES);
      $result .= "<Folder id='{$folder->id}' name='{$title}' url='{$folder->url_path}'/>";
    }

    return $result;
  }
}

?>
