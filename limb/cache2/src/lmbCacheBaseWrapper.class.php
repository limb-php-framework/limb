<?php
lmb_require('limb/cache2/src/lmbCacheWrapper.interface.php');

abstract class lmbCacheBaseWrapper implements lmbCacheWrapper
{
  /**
  * @var lmbCacheConnection
  */
  protected $wrapped_cache;

  function __construct($cache)
  {
    $this->wrapped_cache = $cache;
  }

  function getWrappedConnection()
  {
    return $this->wrapped_cache;
  }

  function __call($method, $args)
  {
    if(!is_callable(array($this->wrapped_cache, $method)))
      throw new lmbException('Decorated cache driver does not support method "' . $method . '"');

    return call_user_func_array(array($this->wrapped_cache, $method), $args);
  }
}