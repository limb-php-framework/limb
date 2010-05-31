<?php
lmb_require('limb/cache2/src/wrappers/lmbCacheBaseWrapper.class.php');

class lmbTaggableCacheWrapper extends lmbCacheBaseWrapper
{
  public $tags_prefix = 'tag42_';

  function __construct(lmbCacheConnection $connection)
  {
    parent::__construct($connection);
  }

  protected function _resolveTagsKeys($tags_keys)
  {
    if(is_array($tags_keys))
    {
      $new_keys = array();
      foreach($tags_keys as $pos => $key)
        $new_keys[] = $this->tags_prefix . $key;
    }
    else
      $new_keys = $this->tags_prefix . $tags_keys;

    return $new_keys;
  }

  protected function _createContainer($value, $tags)
  {
    $tags = $this->_resolveTagsKeys($tags);
    $tags_values = (array) $this->wrapped_cache->get($tags);

    foreach($tags as $tag_key )
      if(!isset($tags_values[$tag_key]) || is_null($tags_values[$tag_key]))
    {
        $tags_values[$tag_key] = 0;
        $this->wrapped_cache->add($tag_key, 0);
    }

    return array('tags' => $tags_values, 'value' => $value);
  }

  protected function _isTagsValid($tags)
  {
    lmb_assert_type($tags, 'array');
    $tags_versions = (array) $this->wrapped_cache->get(array_keys($tags));

    foreach($tags_versions as $tag_key => $tag_version)
      if(is_null($tag_version) || $tags[$tag_key] != $tag_version)
        return false;

    return true;
  }

  protected function _getFromContainer($key, $container)
  {
    if($this->_isTagsValid($container['tags']))
      return $container['value'];
    else
    {
      $this->wrapped_cache->delete($key);
      return NULL;
    }
  }

  protected function _prepareValue($value, $tags_keys)
  {
    if(!is_array($tags_keys))
      $tags_keys = array($tags_keys);

    return $this->_createContainer($value, $tags_keys);
  }

  function add($key, $value, $ttl = false, $tags_keys = 'default')
  {
    return $this->wrapped_cache->add($key, $this->_prepareValue ($value, $tags_keys), $ttl);
  }

  function set($key, $value, $ttl = false, $tags_keys = 'default')
  {
    return $this->wrapped_cache->set($key, $this->_prepareValue ($value, $tags_keys), $ttl);
  }

  function get($keys)
  {
    if(!$containers = $this->wrapped_cache->get($keys))
      return NULL;

    if(!is_array($keys))
      return $this->_getFromContainer($keys, $containers);

    $result = array();
    foreach($containers as $key => $container)
    {
      if($container)
        $result[$key] = $this->_getFromContainer($key, $container);
      else
        $result[$key] = NULL;
    }

    return $result;
  }

  function delete($key)
  {
    $this->wrapped_cache->delete($key);
  }

  function deleteByTag($tag)
  {
    $tag = $this->_resolveTagsKeys($tag);
    $this->wrapped_cache->safeIncrement($tag);
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

  function flush()
  {
    $this->wrapped_cache->flush();
  }
}
