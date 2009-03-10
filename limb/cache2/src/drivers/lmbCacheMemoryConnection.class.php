<?php

lmb_require('limb/cache2/src/drivers/lmbCacheAbstractConnection.class.php');
lmb_require('limb/core/src/lmbSerializable.class.php');

class lmbCacheMemoryConnection extends lmbCacheAbstractConnection
{
  protected $_caches = array();
  protected $_cache_ttls = array();

  function add ($key, $value, $ttl = false)
  {
    $key = $this->_resolveKey($key);

    if(isset($this->_caches[$key]) && (
      !isset($this->_cache_ttls[$key]) || $this->_cache_ttls[$key] > time())
    )
      return false;

    return $this->set($key, $value, $ttl);
  }  

  function set($key, $value, $ttl = false)
  {
    $key = $this->_resolveKey($key);

    if($ttl)
      $this->_cache_ttls[$key] = $ttl + time();
      
    $this->_caches[$key] = $this->_createContainer($value);

    return true;
  }

  function _getSingleKeyValue($resolved_key)
  {
    if(!isset($this->_caches[$resolved_key]))
      return null;
      
    if(
      isset($this->_cache_ttls[$resolved_key])
      && $this->_cache_ttls[$resolved_key] <= time()
    )
      return null;

    $container = $this->_caches[$resolved_key];
    
    return $this->_getDataFromContainer($container);
  }

  function delete($key)
  {
    $key = $this->_resolveKey($key);

    if(isset($this->_caches[$key]))
      unset($this->_caches[$key]);
  }

  function flush()
  {
    $this->_caches = array();
  }

  function getType()
  {
    return 'memory';
  }

}
