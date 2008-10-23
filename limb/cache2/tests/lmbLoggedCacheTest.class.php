<?php
/*
 * Limb PHP Framework
 *
 * @link http:limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http:bit-creative.com)
 * @license    LGPL http:www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/cache2/src/lmbLoggedCache.class.php');
lmb_require(dirname(__FILE__) . '/drivers/lmbCacheConnectionTest.class.php');
lmb_require('limb/cache2/src/drivers/lmbCacheAbstractConnection.class.php');

Mock :: generate('lmbCacheAbstractConnection', 'MockCacheConnection');

class lmbLoggedCacheTest extends UnitTestCase
{
  /**
   * @var lmbLoggedCache
   */
  protected $cache;
  protected $cache_name;
  protected $cache_backend;

  protected $tables_to_cleanup = array();

  function skip()
  {
    $this->skipIf(true, 'Not ready yet');
  }

  function setUp()
  {
    $this->cache_backend = new MockCacheConnection();
    $this->cache = new lmbLoggedCache($this->cache_backend, $this->cache_name = 'test_cache');

    parent::setUp();
  }

  function testProperCallToDriver()
  {
    $this->cache_backend->expectOnce('add', array($key = 'key1', $value = "my_value1", $ttl = 11));
    $this->cache->add($key, $value, $ttl);

    $this->cache_backend->expectOnce('set', array($key = 'key2', $value = "my_value2", $ttl = 22));
    $this->cache->set($key, $value, $ttl);

    $this->cache_backend->expectOnce('get', array($key = 'key3'));
    $this->cache->get($key);

    $this->cache_backend->expectOnce('delete', array($key = 'key4'));
    $this->cache->delete($key);

    $this->cache_backend->expectOnce('flush');
    $this->cache->flush();
  }

  function testAdd_makeLogRecordForNewValue()
  {

    $this->cache_backend->setReturnValue('add', true);
    $this->cache->add($key = 'key1', $value = 'value1');

    $log_records = $this->cache->getLogRecords();
    $log = array(
      array(
        'name' => $this->cache_name,
        'key'  => $key,
        'operation' => $this->cache->OPERATION_ADD,
        'time' => $log_records[0]['time'],
        'result' => true
      )
    );

    $this->assertEqual($log, $log_records);
  }

  function testAdd_makeLogRecordForExistValue()
  {
    $this->cache_backend->setReturnValue('add', false);
    $this->cache->add($key = 'key1', $value = 'value1');

    $log_records = $this->cache->getLogRecords();
    $log = array(
      array(
        'name' => $this->cache_name,
        'key'  => $key,
        'operation' => $this->cache->OPERATION_ADD,
        'time' => $log_records[0]['time'],
        'result' => false
      )
    );

    $this->assertEqual($log, $log_records);
  }

  function testSet_withExistKeyInCache()
  {
    $this->cache_backend->setReturnValue('set', true);
    $result = $this->cache->set($key = 'key1', $value = 'value1');
    $this->assertEqual($result, $value);

    $log_records = $this->cache->getLogRecords();
    $log = array(
      array(
        'name' => $this->cache_name,
        'key'  => $key,
        'operation' => $this->cache->OPERATION_SET,
        'time' => $log_records[0]['time'],
        'result' => true
      )
    );

    $this->assertEqual($log, $log_records);
  }

  function testGet_withKeyInCache()
  {
    $this->cache_backend->setReturnValue('get', $value = 'value1');
    $result = $this->cache->get($key = 'key1');
    $this->assertEqual($result, $value);

    $log_records = $this->cache->getLogRecords();
    $log = array(
      array(
        'name' => $this->cache_name,
        'key'  => $key,
        'operation' => $this->cache->OPERATION_GET,
        'time' => $log_records[0]['time'],
        'result' => true
      )
    );

    $this->assertEqual($log, $log_records);
  }

    function testGet_withoutKeyInCache()
  {
    $this->cache_backend->setReturnValue('get', $value = null);
    $result = $this->cache->get($key = 'key1');

    $log_records = $this->cache->getLogRecords();
    $log = array(
      array(
        'name' => $this->cache_name,
        'key'  => $key,
        'operation' => $this->cache->OPERATION_GET,
        'time' => $log_records[0]['time'],
        'result' => false
      )
    );

    $this->assertEqual($log, $log_records);
  }

  function testDelete()
  {
    $this->cache_backend->setReturnValue('delete', $value = true);
    $result = $this->cache->delete($key = 'key1');

    $log_records = $this->cache->getLogRecords();
    $log = array(
      array(
        'name' => $this->cache_name,
        'key'  => $key,
        'operation' => $this->cache->OPERATION_DELETE,
        'time' => $log_records[0]['time'],
        'result' => true
      )
    );

    $this->assertEqual($log, $log_records);
  }

  function testGetStats()
  {
    $this->cache_backend->setReturnValueAt(0, 'add', true);
    $this->cache->add($key = 'key1', $value = 'value1');

    $this->cache_backend->setReturnValueAt(1, 'add', false);
    $this->cache->add($key, $value);

    $this->cache_backend->setReturnValueAt(0, 'get', true);
    $this->cache->get($key);

    $this->cache_backend->setReturnValueAt(1, 'get', null);
    $this->cache->get($key);

    $this->cache_backend->setReturnValue('set', true);
    $this->cache->set($key, $value);

    $this->cache_backend->setReturnValue('delete', true);
    $this->cache->delete($key);

    $stat = array(
      'add_count' => 2,
      'get_count' => 2,
      'set_count' => 1,
      'delete_count' => 1,
      'expected_items_count' => 0,
      'misses' => 1,
      'hits' => 1,
      'repeated_add' => 1
    );

    $this->assertEqual($stat, $this->cache->getStats());
  }
}

