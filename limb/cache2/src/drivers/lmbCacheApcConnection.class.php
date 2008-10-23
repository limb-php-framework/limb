<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/cache2/src/drivers/lmbCacheAbstractConnection.class.php');

/**
 * class lmbCacheApcConnection.
 *
 * @package cache
 * @version $Id$
 */
class lmbCacheApcConnection extends lmbCacheAbstractConnection
{
  function getType()
  {
    return 'apc';
  }

  function add($key, $value, $ttl = false)
  {
    $key = $this->_resolveKey($key);
    return apc_add($key, $value, $ttl);
  }

  function set($key, $value, $ttl = false)
  {
    $key = $this->_resolveKey($key);
    if($value === false) $value = LIMB_UNDEFINED;
    return apc_store($key, $value, $ttl);
  }

  function _getSingleKeyValue($resolved_key)
  {
    $value = apc_fetch($resolved_key);
    if($value === false)
      return NULL;
    elseif($value === LIMB_UNDEFINED)
      return false;
    else
      return $value;
  }

  function delete($key)
  {
    $key = $this->_resolveKey($key);
    return apc_delete($key);
  }

  function flush()
  {
    return apc_clear_cache('user');
  }

  function stat($limited = true)
  {
    return apc_cache_info(
        "user",
        $limited
    );
  }

}

