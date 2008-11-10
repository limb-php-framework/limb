<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/cache2/src/lmbMintCache.class.php');
lmb_require('limb/cache2/src/drivers/lmbCacheAbstractConnection.class.php');

Mock :: generate('lmbCacheAbstractConnection', 'MockCacheConnection');

class lmbMintCacheTest extends UnitTestCase
{
  protected $cache;
  protected $cache_backend;
  protected $fake_ttl = 1000;
  protected $cooled_ttl = 30;

  function setUp()
  {
    $this->cache_backend = new MockCacheConnection();
    $this->cache = new lmbMintCache($this->cache_backend, 300, $this->fake_ttl, $this->cooled_ttl);
  }

  function testSet_SetsChangedValueToBackend_WithCachedValueAndExpirationTime()
  {
    $ttl = 10;
    $value = "my_value";
    $key = 'value1';
    $this->cache_backend->expectOnce('set', array($key, array($value, time() + $ttl), $this->fake_ttl));
    $this->cache->set($key, $value, $ttl);
  }

  function testAdd_SetsChangedValueToBackend_WithCachedValueAndExpirationTime()
  {
    $ttl = 10;
    $value = "my_value";
    $key = 'value1';
    $this->cache_backend->expectOnce('add', array($key, array($value, time() + $ttl), $this->fake_ttl));
    $this->cache->add($key, $value, $ttl);
  }

  function testGetReturnNullIfCacheBackendReturnsNull()
  {
    $ttl = 10;
    $value = "my_value";
    $key = 'value1';
    $not_expired_time = time() + 100;
    $this->cache_backend->setReturnValue('get', null, array($key));
    $this->cache_backend->expectOnce('get', array($key));
    $this->assertNull($this->cache->get($key));
  }

  function testGetReturnValueIfExpirationTimeIsNotPassed()
  {
    $ttl = 10;
    $value = "my_value";
    $key = 'value1';
    $not_expired_time = time() + 100;
    $this->cache_backend->setReturnValue('get', array($value, $not_expired_time), array($key));
    $this->cache_backend->expectOnce('get', array($key));
    $this->assertEqual($value, $this->cache->get($key));
  }

  function testGetReturnNullAndCallesSetAnewWith60SecondTtl()
  {
    $value = "my_value";
    $key = 'value1';
    $expired_time = time() - 10;
    $this->cache_backend->setReturnValue('get', null, array($key));
    $this->cache_backend->expectOnce('get', array($key));
    $this->cache_backend->expectNever('set');
    $this->assertNull($this->cache->get($key));
  }

  function testCoolDownKeyCallSetWithExpiredTtl()
  {
    $value = "my_value";
    $key = 'value1';
    $not_expired_time = time() + 100;
    $this->cache_backend->expectOnce('get', array($key));
    $this->cache_backend->setReturnValue('get', array($value, $not_expired_time), array($key));
    $this->cache_backend->expectOnce('set', array($key, array($value, time() - 1), $this->cooled_ttl));
    $this->cache->cooldownKey($key);
  }

}
