<?php

lmb_require('limb/cache2/src/drivers/lmbCacheAbstractConnection.class.php');
lmb_require('limb/core/src/lmbSerializable.class.php');

class lmbCacheSessionConnection extends lmbCacheAbstractConnection
{
  /**
   * @var lmbSession
   */
  protected $_session;  
  
  /**
   * @return lmbSession
   */
  protected function _getSession()
  {
    if(is_null($this->_session))
      $this->_session = lmbToolkit::instance()->getSession();
      
    return $this->_session;
  }  
  
  function add($key, $value, $ttl = false)
  {
    $key = $this->_resolveKey($key);    
    
    if($this->_getSession()->exists($key))
      return false;
      
    $this->set($key, $value, $ttl);
    
    return true;
  }
  
  function set ($key, $value, $ttl = false)
  {    
    if($ttl)
      $ttl += time();
      
    if($this->need_serialization)
      $value = lmbSerializable::serialize($value);
      
    $this->_getSession()->set($this->_resolveKey($key), $value);    
    $this->_getSession()->set($this->_resolveKey($key) . 'ttl', $ttl);
  }
  
  function _getSingleKeyValue($resolved_key)
  {    
    if(is_null($value = $this->_getSession()->get($resolved_key)))
      return NULL;
    
    if($this->need_serialization)    
      $value = lmbSerializable::unserialize($value);
    
    $ttl = $this->_getSession()->get($resolved_key . 'ttl');
    
    return ($ttl && $ttl <= time()) ? NULL : $value;
  }
  
  function delete($key)
  {
    $this->_getSession()->remove($this->_resolveKey($key));
  }
  
  function flush()
  {
    $this->_getSession()->reset();
  }
  
  function getType()
  {
    return 'session';
  }
}
