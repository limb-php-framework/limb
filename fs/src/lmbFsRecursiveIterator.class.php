<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/fs/src/exception/lmbFsException.class.php');

/**
 * class lmbFsRecursiveIterator.
 *
 * @package fs
 * @version $Id$
 */
class lmbFsRecursiveIterator implements Iterator
{
  protected $start_dir;
  protected $open_dirs = array();
  protected $valid;
  protected $dir;
  protected $item;
  protected $counter = 0;

  function __construct($dir)
  {
    $this->start_dir = $dir;
  }

  function rewind()
  {
    $this->open_dirs = array();
    $this->valid = true;
    $this->counter = 0;
    $this->_openDir($this->start_dir);

    $this->next();
  }

  protected function _openDir($dir)
  {
    $dir = rtrim($dir, '/') . '/';
    if(($h = @opendir($dir)) === false)
      throw new lmbFsException('can not open directory for scanning', array('dir' => $dir));

    $this->open_dirs[] = array('handle' => $h, 'dir' => $dir);
  }

  protected function _openDirIfNeccessary()
  {
    if(!$this->isDot() && $this->isDir())
      $this->_openDir($this->getPath());
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
    $this->counter++;

    if($this->item !== false)
      $this->_openDirIfNeccessary();

    return $this->item;
  }

  function current()
  {
    return $this->getPath();
  }

  function key()
  {
    return $this->counter;
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
    return is_dir($this->getPath());
  }

  function isFile()
  {
    return is_file($this->getPath()) && !is_dir($this->getPath());
  }

  function getPath()
  {
    return $this->dir . $this->item;
  }

  /**
   * @deprecated
   */
  function getPathName()
  {
    return $this->getPath();
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


