<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/cache2/src/wrappers/lmbCacheBaseWrapper.class.php');

// inspired by MintCache by gfranxman (see http://www.djangosnippets.org/snippets/155/)

class lmbMintCacheWrapper extends lmbCacheBaseWrapper
{
  protected $fake_ttl;
  protected $default_ttl;

  function __construct($cache, $default_ttl = 300, $fake_ttl = 86400, $cooled_ttl = 60)
  {
    parent::__construct($cache);
    $this->fake_ttl = $fake_ttl;
    $this->default_ttl = $default_ttl;
    $this->cooled_ttl = $cooled_ttl;
  }

  function add($key, $value, $ttl = false)
  {
    return $this->wrapped_cache->add($key, $this->_getRealValue($value, $ttl), $this->fake_ttl);
  }

  function set($key, $value, $ttl = false)
  {
    return $this->wrapped_cache->set($key, $this->_getRealValue($value, $ttl), $this->fake_ttl);
  }

  function coolDownKey($key)
  {
    $real_value = $this->wrapped_cache->get($key);

    if(!$real_value || !is_array($real_value))
      return;

    list($value, $expire_time) = $real_value;

    // "-1" is a second before now. Means next time anyone gets this cached item it should receive null and so to refresh cached item
    $this->wrapped_cache->set($key, $this->_getRealValue($value, -1), $this->cooled_ttl);
  }

  protected function _getRealValue($value, $ttl)
  {
    if(!$ttl)
      $ttl = $this->default_ttl;

    $expire_time = time() + $ttl;
    return array($value, $expire_time);
  }

  protected function _extractRealValue($key, $real_value)
  {
    if(!$real_value)
      return NULL;

    list($value, $expire_time) = $real_value;

    if($expire_time > time())
      return $value;
    else
    {
      // now we refresh ttl for this item and return null. We hope that controller will refresh the cached item in this case.
      // $this->cooled_ttl seconds should be enough for any process to refresh cached item.
      $this->wrapped_cache->set($key, $this->_getRealValue($value, $this->cooled_ttl), $this->cooled_ttl);
      return NULL;
    }
  }

  function get($keys)
  {
    $real_values = $this->wrapped_cache->get($keys);
    if(!$real_values || !is_array($real_values))
      return null;

    if(!is_array($keys))
      return $this->_extractRealValue($keys, $real_values);

    $result = array();
    foreach($real_values as $key => $real_value)
      $result[$key] = $this->_extractRealValue($key, $real_value);

    return $result;
  }

  function increment($key, $value = 1, $ttl = false)
  {
    if(is_null($current_value = $this->get($key)))
      return false;

    if(!$this->lock($key, lmbCacheAbstractConnection::TTL_INC_DEC, lmbCacheAbstractConnection::LOCK_NAME_INC_DEC))
      return false;

    if(is_array($current_value))
      throw new lmbInvalidArgumentException("The value can't be a array", array('value' => $current_value));

    if(is_object($current_value))
      throw new lmbInvalidArgumentException("The value can't be a object", array('value' => $current_value));

    $new_value = $current_value + $value;

    $this->set($key, $new_value, $ttl);

    $this->unlock($key, lmbCacheAbstractConnection::LOCK_NAME_INC_DEC);

    return $new_value;
  }

  function decrement($key, $value = 1, $ttl = false)
  {
    if(is_null($current_value = $this->get($key)))
      return false;

    if(!$this->lock($key, lmbCacheAbstractConnection::TTL_INC_DEC, lmbCacheAbstractConnection::LOCK_NAME_INC_DEC))
      return false;

    $new_value = $current_value - $value;

    if($new_value < 0)
      $new_value = 0;

    $this->set($key, $new_value, $ttl);

    $this->unlock($key, lmbCacheAbstractConnection::LOCK_NAME_INC_DEC);

    return $new_value;
  }

  function safeIncrement($key, $value = 1, $ttl = false)
  {
    if($result = $this->increment($key, $value))
      return $result;

    $this->add($key, 0, $ttl);

    return $this->increment($key, $value);
  }

  function safeDecrement($key, $value = 1, $ttl = false)
  {
    if($result = $this->decrement($key, $value))
      return $result;

    $this->add($key, 0, $ttl);

    return $this->decrement($key, $value);
  }

  function delete($key)
  {
    return $this->wrapped_cache->delete($key);
  }

  function flush()
  {
    $this->wrapped_cache->flush();
  }
}


