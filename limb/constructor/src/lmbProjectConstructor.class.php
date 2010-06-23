<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/taskman/taskman.inc.php');

class lmbProjectConstructor
{
  protected $_project_dir;
  protected $_override_files;

  function __construct($project_dir, $override_files)
  {
    $this->_project_dir = $project_dir;
    $this->_override_files = $override_files;
  }

  protected function _addFile($dir, $name, $content,$always_override = false)
  {
    $file = $dir.'/'.$name;

    if(!$always_override && !$this->_override_files && file_exists($file))
      if(!lmb_cli_ask_for_accept("Overwrite '$file'"))
        return false;

    return lmbFs::safeWrite($file, $content);
  }

  protected function _getTemplatesDir()
  {
    return $this->_project_dir.'/template/';
  }

  function addTemplate($name, $content)
  {
    $content = str_replace('\[','010101010100010001qwertyuiit010001001', $content);
    $content = str_replace('\]','010101010100010001zxccvbnmkj010001001', $content);

    $content = str_replace('[','{', $content);
    $content = str_replace(']','}', $content);

    $content = str_replace('010101010100010001qwertyuiit010001001', '[', $content);
    $content = str_replace('010101010100010001zxccvbnmkj010001001', ']', $content);

    $this->_addFile($this->_getTemplatesDir(), $name, $content);
  }

  protected function _getControllersDir()
  {
    return $this->_project_dir.'/src/controller';
  }

  function addController($name, $content)
  {
    $this->_addFile($this->_getControllersDir(), $name, $content);
  }

  protected function _getModelsDir()
  {
    return $this->_project_dir.'/src/model';
  }

  function addModel($name, $content)
  {
    $this->_addFile($this->_getModelsDir(), $name, $content);
  }

  protected function _getTestsDir()
  {
    return $this->_project_dir.'/tests/cases';
  }

  function addTest($name, $content)
  {
    $this->_addFile($this->_getTestsDir(), $name, $content);
  }

  protected function _getConfigsDir()
  {
    return $this->_project_dir.'/settings';
  }

  function addConfig($name, $content)
  {
    $this->_addFile($this->_getConfigsDir(), $name, $content);
  }

  function getProjectDir()
  {
    return $this->_project_dir;
  }

  function setProjectDir($project_dir)
  {
    $this->_project_dir = $project_dir;
  }
}
