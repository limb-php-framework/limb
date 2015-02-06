<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2012 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/config/src/lmbIni.class.php');
lmb_require('limb/fs/src/lmbFs.class.php');

/**
 * class lmbCachedIni.
 *
 * @package config
 */
class lmbCachedIni extends lmbIni
{
  protected $_cache_dir;

  function __construct($file, $cache_dir)
  {
    $this->_file_path = $file;
    $this->_cache_dir = $cache_dir;

    if(!$this->_loadCache())
    {
      $this->import($this->_createIni($file)->export());
      $this->_saveCache();
    }
  }

  protected function _createIni($file)
  {
    return new lmbIni($file);
  }

  function getCacheFile()
  {
    return $this->_cache_dir . md5($this->_file_path) . '.cache';
  }

  protected function _loadCache()
  {
    $cache_dir = $this->_cache_dir;

    lmbFs :: mkdir($cache_dir);

    $cache_file = $this->getCacheFile();

    if(!$this->_isCacheValid($cache_file))
      return false;

    $this->import(unserialize(file_get_contents($cache_file)));

    return true;
  }

  protected function _isCacheValid($cache_file)
  {
    if(!file_exists($cache_file))
      return false;

    if(filemtime($cache_file) >= $this->_getIniLastModificationTime())
      return true;
    else
      return false;
  }

  protected function _getIniLastModificationTime()
  {
    if(!$override = $this->getOverrideFile())
      return filemtime($this->_file_path);

    return filemtime($override);
  }

  protected function _saveCache()
  {
    lmbFs :: safeWrite($this->getCacheFile(),
                       serialize($this->export()));
  }

  function removeCache()
  {
    if(file_exists($cache_file = $this->getCacheFile()))
      unlink($cache_file);
  }
}


