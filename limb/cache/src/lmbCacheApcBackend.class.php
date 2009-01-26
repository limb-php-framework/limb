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

/**
 * class lmbCacheApcBackend.
 *
 * @package cache
 * @version $Id$
 */
class lmbCacheApcBackend implements lmbCacheBackend
{
  function add($key, $value, $params = array()) 
  {
    if (array_key_exists("raw", $params))
    {
      return apc_add($key, $value, $this->_getTtl($params));
    }
    else
    {
      $container = new lmbSerializable($value);
      return apc_add($key, serialize($container), $this->_getTtl($params));
    }

  }
  
  function set($key, $value, $params = array()) 
  {
    if (array_key_exists("raw", $params))
    {
      return apc_store($key, $value, $this->_getTtl($params));
    }
    else
    {
      $container = new lmbSerializable($value);
      return apc_store($key, serialize($container), $this->_getTtl($params));
    }
  }

  function get($key, $params = array())
  {
    if (!$value = apc_fetch($key))
      return false;

    if (array_key_exists("raq", $params))
    {
      return $value;
    }
    else
    {
      $container = unserialize($value);
      return $container->getSubject();
    }
  }

  function delete($key, $params = array())
  {
    apc_delete($key);
  }

  function flush()
  {
    apc_clear_cache('user');
  }
  
  function stat($params = array())
  {
    return apc_cache_info(
        isset($params['cache_type']) ? $params['cache_type'] : "user",
        isset($params['limited']) ? (bool) $params['limited'] : true
    );
  }
  
  protected function _getTtl($params)
  {
    if (!isset($params['ttl']))
      $params['ttl'] = 0;

    return $params['ttl'];
  }
}

