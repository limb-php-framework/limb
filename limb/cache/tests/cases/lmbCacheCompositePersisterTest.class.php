<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCacheCompositePersisterTest.class.php 4985 2007-02-08 15:35:06Z pachanga $
 * @package    cache
 */
lmb_require('limb/cache/src/lmbCachePersister.interface.php');
lmb_require('limb/cache/src/lmbCacheMemoryPersister.class.php');
lmb_require('limb/cache/src/lmbCacheCompositePersister.class.php');

Mock :: generate('lmbCachePersister', 'MockCachePersister');

class lmbCacheCompositePersisterTest extends UnitTestCase
{
  var $cache;

  function setUp()
  {
    $this->cache = new lmbCacheCompositePersister();
  }

  function testGetFailure()
  {
    $this->assertEqual($this->cache->get(1, 'group'), LIMB_CACHE_NULL_RESULT);
  }

  function testGetSuccess()
  {
    $p1 = new MockCachePersister();
    $p2 = new MockCachePersister();

    $this->cache->registerPersister($p1);
    $this->cache->registerPersister($p2);

    $p1->expectOnce('get', array($key = 1, $group = 'some_group'));
    $p1->setReturnValue('get', $value = 'value');

    $p2->expectNever('get');

    $this->assertEqual($value, $this->cache->get($key, $group));
  }

  function testGetSuccessCacheValueForUpperPersister()
  {
    $p1 = new MockCachePersister();
    $p2 = new MockCachePersister();

    $this->cache->registerPersister($p1);
    $this->cache->registerPersister($p2);

    $p1->expectOnce('get');
    $p1->setReturnValue('get', LIMB_CACHE_NULL_RESULT, array($key = 1, $group = 'some_group'));

    $p2->expectOnce('get');
    $p2->setReturnValue('get', $value = 'value', array($key, $group));

    $p1->expectOnce('put', array($key, $value, $group));

    $this->assertEqual($value, $this->cache->get($key, $group));
  }

  function testPutValue()
  {
    $p1 = new MockCachePersister();
    $p2 = new MockCachePersister();

    $this->cache->registerPersister($p1);
    $this->cache->registerPersister($p2);

    $p1->expectOnce('put', array($key = 1, $value = 'whatever', $group = 'some_group'));
    $p2->expectOnce('put', array($key, $value, $group));

    $this->cache->put($key, $value, $group);
  }

  function testFlushValue()
  {
    $p1 = new MockCachePersister();
    $p2 = new MockCachePersister();

    $this->cache->registerPersister($p1);
    $this->cache->registerPersister($p2);

    $p1->expectOnce('flushValue', array($key = 1, $group = 'some_group'));
    $p2->expectOnce('flushValue', array($key, $group));

    $this->cache->flushValue($key, $group);
  }

  function testFlushGroup()
  {
    $p1 = new MockCachePersister();
    $p2 = new MockCachePersister();

    $this->cache->registerPersister($p1);
    $this->cache->registerPersister($p2);

    $p1->expectOnce('flushGroup', array($group = 'some_group'));
    $p2->expectOnce('flushGroup', array($group));

    $this->cache->flushGroup($group);
  }

  function testFlushAll()
  {
    $p1 = new MockCachePersister();
    $p2 = new MockCachePersister();

    $this->cache->registerPersister($p1);
    $this->cache->registerPersister($p2);

    $p1->expectOnce('flushAll', array());
    $p2->expectOnce('flushAll', array());

    $this->cache->flushAll();
  }

  function testRealGet()
  {
    $p1 = new lmbCacheMemoryPersister();
    $p2 = new lmbCacheMemoryPersister();
    $p3 = new lmbCacheMemoryPersister();

    $p3->put($key = 1, $value='yahoo', $group = 'group');

    $this->cache->registerPersister($p1);
    $this->cache->registerPersister($p2);
    $this->cache->registerPersister($p3);

    $cache_value = $this->cache->get($key, $group);

    $this->assertEqual($value, $cache_value);
  }
}

?>