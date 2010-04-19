<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/cache2/src/lmbCacheBaseWrapper.class.php');

// inspired by MintCache by gfranxman (see http://www.djangosnippets.org/snippets/155/)

class lmbMintCache extends lmbCacheBaseWrapper
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

  function get($key)
  {
    $real_value = $this->wrapped_cache->get($key);
    if(!$real_value || !is_array($real_value))
      return $real_value;

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

  function delete($key)
  {
    return $this->wrapped_cache->delete($key);
  }

  function flush()
  {
    $this->wrapped_cache->flush();
  }
}


