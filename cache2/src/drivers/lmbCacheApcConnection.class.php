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
 * @package cache2
 * @version $Id$
 */
class lmbCacheApcConnection extends lmbCacheAbstractConnection
{
  protected $_was_delete = false;
  protected $_deleted = array();

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
    if($this->_was_delete && in_array($resolved_key, $this->_deleted))
      return null;

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
    $this->_deleted[] = $key;
    $this->_was_delete = true;
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

  protected function _resolveKey($keys)
  {
    if(is_array($keys))
    {
      $new_keys = array();
      foreach($keys as $pos => $key)
        $new_keys[$pos] = (string) $this->prefix . $key;
    }
    else
    {
      $new_keys  = (string) $this->prefix . $keys;
    }

    return $new_keys;
  }

}

