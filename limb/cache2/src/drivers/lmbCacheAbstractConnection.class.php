<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/cache2/src/drivers/lmbCacheConnection.interface.php');

/**
 * interface lmbCacheAbstractConnection.
 *
 * @package cache2
 * @version $Id$
 */
abstract class lmbCacheAbstractConnection implements lmbCacheConnection
{
  protected $dsn;
  protected $prefix;
  protected $need_serialization = true;

  const LOCK_NAME_INC_DEC = 'inc_dec';
  const TTL_INC_DEC = 1;

  function __construct($dsn)
  {
    if(!is_object($dsn))
      $dsn = new lmbUri($dsn);

    $this->dsn = $dsn;

    foreach($dsn as $option_name => $option_value)
    {
      if(!is_null($option_value))
        $this->$option_name = $option_value;
    }

    foreach($dsn->getQueryItems() as $option_name => $option_value)
    {
      if(!is_null($option_value))
        $this->$option_name = $option_value;
    }
  }

  protected function _resolveKey($keys)
  {
    if(!$this->prefix)
      return $keys;

    if(is_array($keys))
    {
      $new_keys = array();
      foreach($keys as $pos => $key)
        $new_keys[$pos] = $this->prefix . $key;
    }
    else
    {
      $new_keys = $this->prefix . $keys;
    }

    return $new_keys;
  }

  function _getDataFromContainer($container)
  {
    if($this->need_serialization)
      return lmbSerializable::unserialize($container);
    else
      return $container;
  }

  function _createContainer($data)
  {
    if($this->need_serialization)
      return lmbSerializable::serialize($data);
    else
      return $data;
  }

  function get($keys)
  {
    if(!is_array($keys))
    {
      $values = $this->_getSingleKeyValue($this->_resolveKey($keys));
    }
    else
    {
      $resolved_keys = $this->_resolveKey($keys);
      $values = array();
      foreach($resolved_keys as $key_index => $resolved_key)
        $values[$keys[$key_index]] = $this->_getSingleKeyValue($resolved_key);
    }

    return $values;
  }

  protected function _getLockName($key, $lock_name = 'lock')
  {
    return $key.'_'.$lock_name;
  }

  function lock($key, $ttl = false, $lock_name = false)
  {
    return $this->add($this->_getLockName($key,$lock_name), '1', $ttl);
  }

  function unlock($key, $lock_name = false)
  {
    return $this->delete($this->_getLockName($key, $lock_name));
  }

  function increment($key, $value = 1, $ttl = false)
  {
    if(is_null($current_value = $this->get($key)))
      return false;

    if(!$this->lock($key, self::TTL_INC_DEC, self::LOCK_NAME_INC_DEC))
      return false;

    if(is_array($current_value))
      throw new lmbInvalidArgumentException("The value can't be a array", array('value' => $current_value));

    if(is_object($current_value))
      throw new lmbInvalidArgumentException("The value can't be a object", array('value' => $current_value));

    $new_value = $current_value + $value;

    $this->set($key, $new_value, $ttl);

    $this->unlock($key, self::LOCK_NAME_INC_DEC);

    return $new_value;
  }

  function decrement($key, $value = 1, $ttl = false)
  {
    if(is_null($current_value = $this->get($key)))
      return false;

    if(!$this->lock($key, self::TTL_INC_DEC, self::LOCK_NAME_INC_DEC))
      return false;

    $new_value = $current_value - $value;

    if($new_value < 0)
      $new_value = 0;

    $this->set($key, $new_value, $ttl);

    $this->unlock($key, self::LOCK_NAME_INC_DEC);

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
}
