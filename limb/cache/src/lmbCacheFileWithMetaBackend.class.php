<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/cache/src/lmbCacheBackend.interface.php');
lmb_require('limb/core/src/lmbSerializable.class.php');
lmb_require('limb/fs/src/lmbFs.class.php');

/**
 * class lmbCacheFileBackend.
 *
 * @package cache
 * @version $Id$
 */
class lmbCacheFileWithMetaBackend implements lmbCacheBackend
{
  protected $_cache_dir;

  function __construct($cache_dir)
  {
    $this->_cache_dir = lmbFs::normalizePath($cache_dir);

    lmbFs :: mkdir($this->_cache_dir);
  }

  function getCacheDir()
  {
    return $this->_cache_dir;
  }

  function add($key, $value, $params = array())
  {
    $file = $this->getCacheDir() . '/' . $this->_getCacheFileName($key, $params);
    if (file_exists($file))
      return false;

    if(array_key_exists("ttl", $params))
    {
    	$meta=array('ttl' => $params['ttl']+time());
    	$this->_setMetaData($key,$meta);
    }

    if (array_key_exists("raw", $params))
    {
      lmbFs :: safeWrite($file, $value);
      return true;
    }
    else
    {
      $container = new lmbSerializable($value);
      lmbFs :: safeWrite($file, serialize($container));
      return true;
    }
  }

  function set($key, $value, $params = array())
  {
    $this->delete($key);

    $file = $this->getCacheDir() . '/' . $this->_getCacheFileName($key);

   if(isset($params['ttl']))
    {    	$meta=array('ttl' => $params['ttl']+time());
    	$this->_setMetaData($key,$meta);
    }

    if (array_key_exists("raw", $params))
    {
      lmbFs :: safeWrite($file, $value);
      return true;
    }
    else
    {
      $container = new lmbSerializable($value);
      lmbFs :: safeWrite($file, serialize($container));
      return true;
    }
  }

  function get($key, $params = array())
  {
    if (!$file = $this->_findCacheFile($key))
      return false;

    if($meta=$this->_getMetaData($key) and isset($meta['ttl']))
    {    	if ($meta['ttl'] - time() < 0)
        return false;
    }

    if (array_key_exists("raw", $params))
    {
      return file_get_contents($file);
    }
    else
    {
      $container = unserialize(file_get_contents($file));
      return $container->getSubject();
    }
  }

  function delete($key, $params = array())
  {
    $this->_removeFileCache($key);
  }

  function flush()
  {
    $this->_removeFileCache();
  }

  function stat($params = array())
  {
    return array();
  }

  protected function _removeFileCache($key = false)
  {
    if($key === false)
    {
      $files = lmbFs :: find($this->getCacheDir(), 'f');
      foreach($files as $file)
        @unlink($file);
    }
    else
    {
      @unlink($this->getCacheDir()."/".$this->_getCacheFileName($key));
      $this->_removeFileMeta($key);
    }
  }

  protected function _findCacheFile($key)
  {  	$file=$this->getCacheDir()."/".$this->_getCacheFileName($key);
    if(file_exists($file))
    {
      return $file;
    }
    else
      return false;
  }

  protected function _getMetaData($key)
  {
    $file=$this->_getMetaFilePath($key);
    if(file_exists($file))
    {
      $data = unserialize(file_get_contents($file));
      return $data;
    }
    else
      return false;
  }

  protected function _setMetaData($key,$data)
  {  	$file=$this->_getMetaFilePath($key);
 	  lmbFs :: safeWrite($file, serialize($data));
  }

  protected function _removeFileMeta($key = false)
  {
    if($key)
      @unlink($this->_getMetaFilePath($key));
  }

  protected function _getMetaFilePath($key)
  {  	$file=$this->getCacheDir()."/".$key.".meta";
  	return $file;
  }

  protected function _getCacheFileName($key)
  {
    return $key. '_' .'.cache';
  }

}
