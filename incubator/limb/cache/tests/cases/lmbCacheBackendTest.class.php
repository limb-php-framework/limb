<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/core/src/lmbObject.class.php');

class CacheableFooBarClass{}

abstract class lmbCacheBackendTest extends UnitTestCase
{
  protected $cache;

  abstract function _createPersisterImp();

  function setUp()
  {
    $this->cache = $this->_createPersisterImp();
    $this->cache->flush();
  }

  function tearDown()
  {
    $this->cache->flush();
  }

  
  function testGetFalse()
  {
    $this->assertFalse($this->cache->get(1));
  }

  function testGetTrue()
  {
    $this->cache->set(1, $v = 'value');
    $var = $this->cache->get(1);
    $this->assertEqual($v, $var);
  }
  
  function testAddLock()
  {
    $this->assertTrue($this->cache->set(1, $v = 'value'));
    $this->assertFalse($this->cache->add(1, 'value_add'));
    
    $this->assertEqual($this->cache->get(1), $v);
    
    $this->assertTrue($this->cache->add(2, 'value2'));
    
    $this->cache->set(2, 'new value');
    $this->assertEqual($this->cache->get(2), 'new value');
  }

  function testSetToCache()
  {
    $rnd_key = mt_rand();
    $this->cache->set($rnd_key, $v1 = 'value1');

    foreach($this->_getCachedValues() as $v2)
    {
      $this->cache->set(1, $v2);
      $cache_value = $this->cache->get(1);
      $this->assertEqual($cache_value, $v2);
    }
      $cache_value = $this->cache->get($rnd_key);
      $this->assertEqual($cache_value, $v1);
  }

  function testDeleteValue()
  {
    $this->cache->set(1, $v1 = 'value1');
    $this->cache->set(2, $v2 = 'value2');

    $this->cache->delete(1);

    $this->assertFalse($this->cache->get(1));

    $cache_value = $this->cache->get(2);
    $this->assertEqual($cache_value, $v2);
  }

  function testFlush()
  {
    $this->cache->set(1, $v1 = 'value1');
    $this->cache->set(2, $v2 = 'value2');

    $this->cache->flush();

    $this->assertFalse($this->cache->get(1));
    $this->assertFalse($this->cache->get(2));
  }

  function testGetWithTtlFalse()
  {
    $this->cache->set(1, 'value', array('ttl' => 1));
    sleep(2);
    $this->assertFalse($this->cache->get(1));
  }

  function testGetWithTtlTrue()
  {
    $val = 'value';
    $this->cache->set(1, $val, array('ttl'=> 3600));
    $this->assertEqual($val, $this->cache->get(1));
  }

  function testProperSerializing()
  {
    $obj = new lmbObject();
    $obj->set('foo', 'wow');

    $this->cache->set(1, $obj);

    $this->assertEqual($obj, $this->cache->get(1));
  }

  function testObjectClone()
  {
    $value = 'bar';
    
    $obj = new lmbObject();
    $obj->set('foo', $value);
    
    $this->cache->set(1, $obj);
    
    $obj->set('foo', 'new value');
    
    $this->assertEqual($value, $this->cache->get(1)->get('foo'));
    
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
