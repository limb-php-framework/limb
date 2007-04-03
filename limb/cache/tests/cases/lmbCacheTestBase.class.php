<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCacheTestBase.class.php 4985 2007-02-08 15:35:06Z pachanga $
 * @package    cache
 */

abstract class lmbCacheTestBase extends UnitTestCase
{
  var $cache;

  function _createPersisterImp()
  {
    return null;
  }

  function setUp()
  {
    $this->cache = $this->_createPersisterImp();
    $this->cache->flushAll();
  }

  function testGetId()
  {
    $this->assertNotNull($this->cache->getId());
  }

  function testGetFalse()
  {
    $this->_testGetFalse(1);
  }

  function testGetTrue()
  {
    $this->_testGetTrue(1);
  }

  function testPutToCache()
  {
    $this->_testPutToCache(1);
  }

  function testPutToCacheWithGroup()
  {
    $this->_testPutToCacheWithGroup(1);
  }

  function testFlushValue()
  {
    $this->_testFlushValue(1, 2);
  }

  function testFlushGroup()
  {
    $key = 1;
    $this->cache->put($key, $v1 = 'value1');
    $this->cache->put($key, $v2 = 'value2', 'test-group');

    $this->cache->flushGroup('test-group');

    $this->assertCacheNotValid($this->cache->get($key, 'test-group'));

    $var = $this->cache->get($key);
    $this->assertEqual($var, $v1);
  }

  function testFlushAll()
  {
    $this->cache->put(1, $v1 = 'value1');
    $this->cache->put(2, $v2 = 'value2');

    $this->cache->flushAll();

    $this->assertCacheNotValid($this->cache->get(1));
    $this->assertCacheNotValid($this->cache->get(2));
  }

  function _testFlushValue($key1, $key2)
  {
    $this->cache->put($key1, $v1 = 'value1');
    $this->cache->put($key2, $v2 = 'value2');

    $this->cache->flushValue($key1);

    $this->assertCacheNotValid($this->cache->get($key1));

    $cache_value = $this->cache->get($key2);
    $this->assertEqual($cache_value, $v2);
  }

  function _testGetFalse($key)
  {
    $this->assertCacheNotValid($this->cache->get($key));
  }

  function _testGetTrue($key)
  {
    $this->cache->put($key, $v = 'value');
    $var = $this->cache->get($key);
    $this->assertEqual($v, $var);
  }

  function _testPutToCache($key)
  {
    $rnd_key = mt_rand();
    $this->cache->put($rnd_key, $v1 = 'value1');

    foreach($this->_getCachedValues() as $v2)
    {
      $this->cache->put($key, $v2);
      $cache_value = $this->cache->get($key);
      $this->assertEqual($cache_value, $v2);

      $cache_value = $this->cache->get($rnd_key);
      $this->assertEqual($cache_value, $v1);
    }
  }

  function _testPutToCacheWithGroup($key)
  {
    $this->cache->put($key, $v1 = 'value1');
    $this->cache->put($key, $v2 = 'value2', 'test-group');

    $cache_value = $this->cache->get($key);
    $this->assertEqual($cache_value, $v1);

    $cache_value = $this->cache->get($key, 'test-group');
    $this->assertEqual($cache_value, $v2);
  }

  function _getCachedValues()
  {
    return array($this->_createNullValue(),
                 $this->_createScalarValue(),
                 $this->_createArrayValue(),
                 $this->_createObjectValue());
  }

  function _createNullValue()
  {
    return null;
  }

  function _createScalarValue()
  {
    return 'some value';
  }

  function _createArrayValue()
  {
    return array('some value');
  }

  function _createObjectValue()
  {
    return new CacheableFooClass();
  }

  function assertCacheNotValid($value)
  {
    $this->assertEqual($value, LIMB_CACHE_NULL_RESULT);
  }
}

?>