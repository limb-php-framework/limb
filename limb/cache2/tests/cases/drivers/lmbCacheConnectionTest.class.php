<?php
/*
* Limb PHP Framework
*
* @link http://limb-project.com
* @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
* @license    LGPL http://www.gnu.org/copyleft/lesser.html
*/
lmb_require('limb/core/src/lmbObject.class.php');
lmb_require('limb/cache2/src/lmbCache.class.php');

class CacheableFooBarClass{}

abstract class lmbCacheConnectionTest extends UnitTestCase
{
  /**
   * @var lmbUri
   */
  protected $dsn;
  /**
   * @var lmbCacheAbstractConnection
   */
  protected $cache;

  function setUp()
  {
    $this->cache = lmbCache::createConnection($this->dsn);
  }

  function tearDownAll()
  {
    $this->cache->flush();
  }

  protected function _getUniqueId()
  {
    return mt_rand();
  }

  function testGet_Negative()
  {
    $this->assertNull($this->cache->get($id = $this->_getUniqueId()));
  }

  function testGet_Positive()
  {
    $this->cache->set($id = $this->_getUniqueId(), $v = 'value');
    $var = $this->cache->get($id);
    $this->assertEqual($v, $var);
  }

  function testGet_Positive_Multiple()
  {
    $id1 = $this->_getUniqueId();
    $id2 = $this->_getUniqueId();
    $this->cache->set($id1, $v1 = 'value1');
    $this->cache->set($id2, $v2 = 'value2');

    $var = $this->cache->get(array($id1, $id2, $this->_getUniqueId()));

    $this->assertEqual($v1, $var[$id1]);
    $this->assertEqual($v2, $var[$id2]);
  }

  function testGet_Positive_FalseValue()
  {
    $this->cache->set($id = $this->_getUniqueId(), $v = false);
    $var = $this->cache->get($id);
    $this->assertIdentical($var, $v);

  }

  function testAdd()
  {
    $this->cache->add($id = $this->_getUniqueId(), $v = 'value');
    $var = $this->cache->get($id);
    $this->assertEqual($v, $var);
  }

  function testAddNonUnique()
  {
    $this->assertTrue($this->cache->add($id = $this->_getUniqueId(), $v = 'value'));
    $this->assertFalse($this->cache->add($id, $v = 'value'));
  }

  function testSet()
  {
    $this->cache->set($first_id = $this->_getUniqueId(), $v1 = 'value1');

    foreach($this->_getCachedValues() as $v2)
    {
      $this->cache->set($id = $this->_getUniqueId(), $v2);
      $cache_value = $this->cache->get($id);
      $this->assertEqual($cache_value, $v2);
    }

    $cache_value = $this->cache->get($first_id);
    $this->assertEqual($cache_value, $v1);
  }

  function testDelete()
  {
    $this->cache->set($id1 = $this->_getUniqueId(), $v1 = 'value1');
    $this->cache->set($id2 = $this->_getUniqueId(), $v2 = 'value2');

    $this->cache->delete($id1);

    $this->assertFalse($this->cache->get($id1));

    $cache_value = $this->cache->get($id2);
    $this->assertEqual($cache_value, $v2);
  }

  function testFlush()
  {
    $this->cache->set($id1 = $this->_getUniqueId(), $v1 = 'value1');
    $this->cache->set($id2 = $this->_getUniqueId(), $v2 = 'value2');

    $this->cache->flush();

    $this->assertFalse($this->cache->get($id1));
    $this->assertFalse($this->cache->get($id2));
  }

  function testGetWithTtlFalse()
  {
    $this->cache->set($id = $this->_getUniqueId(), 'value', $ttl = 1);
    sleep(2);
    $this->assertFalse($this->_makeGetFromDifferentThread($id));
  }

  function testGetWithTtlTrue()
  {
    $val = 'value';
    $this->cache->set($id = $this->_getUniqueId(), $val, $ttl = 3600);
    $this->assertEqual($val, $this->cache->get($id));
  }

  function testProperSerializing()
  {
    $obj = new lmbObject();
    $obj->set('foo', 'wow');

    $this->cache->set($id = $this->_getUniqueId(), $obj);

    $this->assertEqual($obj, $this->cache->get($id));
  }

  function testObjectClone()
  {
    $value = 'bar';

    $obj = new lmbObject();
    $obj->set('foo', $value);

    $this->cache->set($id = $this->_getUniqueId(), $obj);

    $obj->set('foo', 'new value');

    $cached_obj = $this->cache->get($id);
    $this->assertIsA($cached_obj, 'lmbObject');
    $this->assertEqual($value, $cached_obj->get('foo'));
  }

  function testWithPrefix_NotIntercepting()
  {
    $dsn = $this->dsn;
    if(!is_object($dsn))
    $dsn = new lmbUri($dsn);

    $cache = lmbCache::createConnection($dsn);

    $dsn_with_prefix = clone($dsn);
    $dsn_with_prefix->addQueryItem('prefix', 'foo');
    $cache_with_prefix = lmbCache::createConnection($dsn_with_prefix);

    $cache->set('bar', 42);
    $cache_with_prefix->set('bar', 24);

    $this->assertEqual(42, $cache->get('bar'));
  }

  protected function _makeGetFromDifferentThread($id)
  {
    return;
    $request_code = '
    //require_once("'.'/tests/cases/common_tests_setup.php");
    //require_once("'.'/setup.php");
    lmb_require("limb/cache2/src/lmbCache.class.php");
    $cache = lmbCache::createConnection("'.$this->dsn.'");
    exit($cache->get('.$id.'));';

    $status = '';
    passthru('php -r \''.$request_code . '\'', $status);
    return (bool) $status;
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
    return new CacheableFooBarClass();
  }

}
