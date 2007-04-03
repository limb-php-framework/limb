<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFsRecursiveIterator.class.php 5009 2007-02-08 15:37:31Z pachanga $
 * @package    util
 */
lmb_require('limb/util/src/exception/lmbIOException.class.php');

class lmbFsRecursiveIterator
{
  protected $start_dir;
  protected $open_dirs = array();
  protected $look_ahead_buffer;
  protected $valid;
  protected $dir;
  protected $item;

  function __construct($dir)
  {
    $this->start_dir = $dir;
  }

  function rewind()
  {
    $this->open_dirs = array();
    $this->look_ahead_buffer = null;
    $this->valid = true;
    $this->_openDir($this->start_dir);

    $this->next();
  }

  protected function _openDir($dir)
  {
    $dir = rtrim($dir, '/') . '/';
    if(($h = @opendir($dir)) === false)
      throw new lmbIOException('can not open directory for scanning', array('dir' => $dir));

    $this->open_dirs[] = array('handle' => $h, 'dir' => $dir);
  }

  protected function _openDirIfNeccessary()
  {
    if(!$this->isDot() && $this->isDir())
      $this->_openDir($this->getPathName());
  }

  protected function _closeTerminalDirs()
  {
    if(!$this->_readItem() && $this->_hasOpenDirs())
    {
      $h = $this->_getLastOpenDirHandle();
      closedir($h);
      array_pop($this->open_dirs);
      $this->_closeTerminalDirs();
    }
  }

  protected function _hasOpenDirs()
  {
    return sizeof($this->open_dirs) > 0;
  }

  protected function _getLastOpenDirHandle()
  {
    $index = sizeof($this->open_dirs) - 1;
    if(isset($this->open_dirs[$index]))
      return $this->open_dirs[$index]['handle'];
  }

  protected function _getLastOpenDir()
  {
    $index = sizeof($this->open_dirs) - 1;
    if(isset($this->open_dirs[$index]))
      return $this->open_dirs[$index]['dir'];
  }

  protected function _readItem()
  {
    if(!$handle = $this->_getLastOpenDirHandle())
      return false;

    $this->dir = $this->_getLastOpenDir();

    $this->item = readdir($handle);

    if($this->item !== false)
      $this->_openDirIfNeccessary();

    return $this->item;
  }

  function current()
  {
    return $this;//???
  }

  function valid()
  {
    return $this->valid;
  }

  function next()
  {
    $this->valid = true;

    if($this->_readItem() === false)
    {
      $this->_closeTerminalDirs();

      if(!$this->_hasOpenDirs())
        $this->valid = false;
    }
  }

  function isDot()
  {
    return ($this->item == '.' || $this->item == '..');
  }

  function isDir()
  {
    return is_dir($this->getPathName());
  }

  function isFile()
  {
    return is_file($this->getPathName()) && !is_dir($this->getPathName());
  }

  function getPathName()
  {
    return $this->dir . $this->item;
  }

  function getCurrentDirectoryName()
  {
    return $this->dir;
  }

  function getCurrentFileName()
  {
    return $this->item;
  }
}

?>
