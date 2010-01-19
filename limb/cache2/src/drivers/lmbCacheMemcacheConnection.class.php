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
 * class lmbCacheMemcacheBackend.
 *
 * @package cache2
 * @version $Id: lmbCacheFilePersister.class.php 6243 2007-08-29 11:53:10Z pachanga $
 */

class lmbCacheMemcacheConnection extends lmbCacheAbstractConnection
{
  static $_connected_servers;
  public $default_host = 'localhost';
  public $default_port = 11211;
  public $flush_pause = 1000000;
  const FALSE_VALUE = '$oU$@Ge';
  protected $_server_id;

  function __construct(lmbUri $dsn)
  {
    parent::__construct($dsn);

    if(!$this->dsn->getHost())
      $this->dsn->setHost($this->default_host);

    if(!$this->dsn->getPort())
      $this->dsn->setPort($this->default_port);

    $this->_server_id = $dsn->toString(array('host', 'port'));
  }

  function getType()
  {
    return 'memcache';
  }

  protected function _getMemcache()
  {
    if(!self::$_connected_servers[$this->_server_id])
    {
      $server = new Memcache();
      if(!$server->connect($this->dsn->getHost(), $this->dsn->getPort()))
        throw new lmbException("Can't connect to memcache", array('host' => $this->dsn->getHost(), 'post' => $this->dsn->getPort()));

      self::$_connected_servers[$this->_server_id] = $server;
    }
    return self::$_connected_servers[$this->_server_id];
  }

  function add($key, $value, $ttl = false)
  {
    if(false === $value)
       $value = self::FALSE_VALUE;
    return $this->_getMemcache()->add($this->_resolveKey($key), $value, false, (int) $ttl);
  }

  function set($key, $value, $ttl = false)
  {
    if(false === $value)
      $value = self::FALSE_VALUE;
    return $this->_getMemcache()->set($this->_resolveKey($key), $value, false, (int) $ttl);
  }

  function get($key)
  {
    $value = $this->_getMemcache()->get($this->_resolveKey($key));

    if(false === $value)
      return NULL;

    if(self::FALSE_VALUE === $value)
      return false;

    if(is_array($key))
      foreach ($key as $one_key)
        if(!isset($value[$one_key]))
          $value[$one_key] = NULL;

    return $value;
  }

  function delete($key, $ttl = 0)
  {
    return $this->_getMemcache()->delete($this->_resolveKey($key), $ttl);
  }

  function increment($key, $value = 1)
  {
    return $this->_getMemcache()->increment($this->_resolveKey($key), $value);
  }

  function decrement($key, $value = 1)
  {
    return $this->_getMemcache()->decrement($this->_resolveKey($key), $value);
  }

  function flush()
  {
    $this->_getMemcache()->flush();
    usleep($this->flush_pause);
  }
}
