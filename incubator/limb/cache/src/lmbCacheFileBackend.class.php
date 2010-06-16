<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
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
class lmbCacheFileBackend implements lmbCacheBackend
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

    $file = $this->getCacheDir() . '/' . $this->_getCacheFileName($key, $params);

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

    $res = array();
    if (preg_match('/\/' . $key . '_(\d+)\.cache$/', $file, $res) and isset($res[1]))
    {
      if ($res[1] - time() < 0)
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
      @unlink($this->_findCacheFile($key));
  }

  protected function _getCacheFileName($key, $params)
  {
    $ttl = '';
    if (isset($params['ttl']))
      $ttl = time() + $params['ttl'];

    return $key . '_' . $ttl . '.cache';
  }

  protected function _findCacheFile($key)
  {
    $files = lmbFs::find($this->getCacheDir(), 'f', '/^' . $key . '_?\d*\.cache$/');
    if (count($files))
      return $files[0];
  }
}
