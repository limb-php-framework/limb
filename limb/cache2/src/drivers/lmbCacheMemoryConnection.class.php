<?php

lmb_require('limb/cache2/src/drivers/lmbCacheAbstractConnection.class.php');
lmb_require('limb/core/src/lmbSerializable.class.php');

class lmbCacheMemoryConnection extends lmbCacheAbstractConnection
{
  protected $_caches = array();

  function add ($key, $value, $ttl = false)
  {
    $key = $this->_resolveKey($key);

    if(isset($this->_caches[$key]) && (
      !isset($this->_caches[$key]['ttl']) || $this->_caches[$key]['ttl'] > time())
    )
      return false;

    return $this->set($key, $value, $ttl);
  }

  function set($key, $value, $ttl = false)
  {
    $key = $this->_resolveKey($key);

    $container = new lmbSerializable($value);

    $cache = array('value' => serialize($container));

    if($ttl)
      $cache['ttl'] = time() + $ttl;

    $this->_caches[$key] = $cache;

    return true;
  }

  function _getSingleKeyValue($resolved_key)
  {
    if(!isset($this->_caches[$resolved_key]))
      return null;

    $cache = $this->_caches[$resolved_key];

    if(isset($cache['ttl']) && $cache['ttl'] <= time())
      return null;

    $container = unserialize($cache['value']);
    return $container->getSubject();
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