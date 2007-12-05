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

/**
 * class lmbCacheApcBackend.
 *
 * @package cache
 * @version $Id$
 */
class lmbCacheApcBackend implements lmbCacheBackend
{
  function set($key, $value, $params = array()) 
  {
    $container = new lmbSerializable($value);

    apc_store($key, serialize($container), $this->_getTtl($params));
  }

  function get($key, $params = array())
  {
    if (!$value = apc_fetch($key))
      return false;

    $container = unserialize($value);
    return $container->getSubject();
  }

  function delete($key, $params = array())
  {
    apc_delete($key);
  }

  function flush()
  {
    apc_clear_cache('user');
  }
  
  protected function _getTtl($params)
  {
    if (!isset($params['ttl']))
      $params['ttl'] = 0;

    return $params['ttl'];
  }
}

