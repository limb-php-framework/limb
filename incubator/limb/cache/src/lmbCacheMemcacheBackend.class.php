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
 * class lmbCacheMemcacheBackend.
 *
 * @package cache
 * @version $Id: lmbCacheFilePersister.class.php 6243 2007-08-29 11:53:10Z pachanga $
 */
class lmbCacheMemcacheBackend implements lmbCacheBackend
{
  protected $_memcache;

  function __construct($host = 'localhost', $port = '11211')
  {
    $this->_memcache = new Memcache();
    $this->_memcache->connect($host, $port);
  }

  function add($key, $value, $params = array()) 
  {
    if(array_key_exists("raw", $params))
      return $this->_memcache->add($key, $value, null, $this->_getTtl($params));
    else
      return $this->_memcache->add($key, new lmbSerializable($value), null, $this->_getTtl($params));
  }
  
  function set($key, $value, $params = array()) 
  {
    if(array_key_exists("raw", $params))
      return $this->_memcache->set($key, $value, null, $this->_getTtl($params));
    else
      return $this->_memcache->set($key, new lmbSerializable($value), null, $this->_getTtl($params));
  }

  function get($key, $params = array())
  {
    if(false === ($value = $this->_memcache->get($key)))
      return false;
        
    if(array_key_exists("raw", $params))
      return $value;
    else
      return $value->getSubject();
  }

  function delete($key, $params = array())
  {
    $this->_memcache->delete($key);
  }

  function flush()
  {
    $this->_memcache->flush();
  }
  
  function stat($params = array())
  {
    return $this->_memcache->getStats(
      isset($params['cache_type']) ? $params['cache_type'] : null,
      isset($params['slabid']) ? $params['slabid'] : null,
      isset($params['limit']) ? $params['limit'] : 100
    );
  }
  
  protected function _getTtl($params)
  {
    if(!isset($params['ttl']))
      $params['ttl'] = 0;

    return $params['ttl'];
  }
}
