<?php
/*
* Limb PHP Framework
*
* @link http://limb-project.com
* @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
* @license    LGPL http://www.gnu.org/copyleft/lesser.html*/

lmb_require('limb/cache2/src/drivers/lmbCacheAbstractConnection.class.php');
lmb_require('limb/net/src/lmbUri.class.php');
lmb_require('limb/core/src/lmbSerializable.class.php');
lmb_require('limb/fs/src/lmbFs.class.php');

/**
 * class lmbCacheFileConnection.
 *
 * @package cache2
 * @version $Id$
 */
class lmbCacheFileConnection extends lmbCacheAbstractConnection
{
  protected $_cache_dir;
  const EXT_CACHE = 'cache';
  const EXT_TTL = 'ttl';
  const EXT_LOCK = 'lock';

  /**
   * @param lmbUri $dsn
   */
  function __construct($dsn)
  {
    parent::__construct($dsn);

    $cache_dir = $dsn->getPath();

    $this->_cache_dir = $cache_dir;

    lmbFs :: mkdir($this->_cache_dir);
  }

  function getType()
  {
    return 'file';
  }

  function getCacheDir()
  {
    return $this->_cache_dir;
  }

  function add($key, $value, $ttl = false)
  {
    $key = $this->_resolveKey($key);

    if ($file = $this->_getCacheFile($key))
      return false;

    return $this->set($key, $value, $ttl);
  }

  function set($key, $value, $ttl = false)
  {
    $key = $this->_resolveKey($key);

    $this->delete($key);
    
    if($ttl)
    {
      $ttl_file = $this->_getCacheTtlFileName($key);
      lmbFs :: safeWrite($ttl_file, (string) ($ttl + time()));      
    }
    
    $file = $this->_getCacheFileName($key);    
    lmbFs :: safeWrite($file, $this->_createContainer($value));
    
    return true;    
  }

  function _getSingleKeyValue($resolved_key)
  {
    if (is_null($file = $this->_getCacheFile($resolved_key)))
      return NULL;

    return $this->_getDataFromContainer(file_get_contents($file));
  }

  function delete($key)
  {
    $key = $this->_resolveKey($key);

    lmbFs::rm($this->_getCacheFile($key));
    lmbFs::rm($this->_getCacheTtlFileName($key));
    return true;
  }

  function flush()
  {
    $files = lmbFs :: find($this->getCacheDir(), 'f');
    foreach($files as $file)
      lmbFs::rm($file);
  }

  protected function _getCacheFileName($key)
  {
    return $this->getCacheDir() . '/' . $key . '.' . self::EXT_CACHE;
  }

  protected function _getCacheTtlFileName($key)
  {
    return $this->getCacheDir() . '/' . $key . '.' . self::EXT_TTL;
  }

  protected function _getCacheFile($key)
  {
    $cache_file_name = $this->_getCacheFileName($key);
    $ttl_file_name = $this->_getCacheTtlFileName($key);

    if(!file_exists($cache_file_name))
      return NULL;

    if(!file_exists($ttl_file_name))
      return $cache_file_name;

    if((int) file_get_contents($ttl_file_name) <= time())
        return NULL;
      else
        return $cache_file_name;
  }
}
