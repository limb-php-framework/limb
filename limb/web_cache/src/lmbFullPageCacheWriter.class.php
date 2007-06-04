<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFullPageCacheWriter.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */
lmb_require('limb/fs/src/lmbFs.class.php');

class lmbFullPageCacheWriter
{
  protected $storage_dir;
  protected $cache_file = 'cache.html';

  function __construct($dir)
  {
    $this->storage_dir = $dir;
  }

  function setStorageDirectory($dir)
  {
    $this->storage_dir = $dir;
  }

  function getCacheFile()
  {
    return $this->cache_file;
  }

  function setCacheFile($file)
  {
    $this->cache_file = $file;
  }

  function _getFilePath($cache)
  {
    return $this->storage_dir . '/' . $cache . '/' . $this->cache_file;
  }

  function flush($cache = null)
  {
    if(!$cache)
      return $this->flushAll();

    if(file_exists($file = $this->_getFilePath($cache)))
    {
      unlink($file);
      return true;
    }

    return false;
  }

  function flushAll()
  {
    return lmbFs :: rm($this->storage_dir);
  }

  function getCacheSize()
  {
    $files = lmbFs :: findRecursive($this->storage_dir, 'f');

    $size = 0;
    foreach($files as $file)
      $size += filesize($file);

    return $size;
  }

  function save($cache, $contents)
  {
    $file = $this->_getFilePath($cache);
    $dir = dirname($file);

    //cache conflict
    if(basename($dir) == $this->cache_file)
      return false;

    lmbFs :: mkdir($dir);
    lmbFs :: safeWrite($file, $contents);
  }

  function get($cache)
  {
    if(file_exists($file = $this->_getFilePath($cache)))
      return file_get_contents($file);
    else
      return false;
  }
}

?>
