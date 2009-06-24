<?php
lmb_require('limb/cache2/src/lmbTaggableCache.class.php');
lmb_require('limb/cache2/src/lmbCacheFactory.class.php');
lmb_require('limb/cache2/src/drivers/lmbCacheMemoryConnection.class.php');

class lmbTaggableCacheTest extends UnitTestCase
{
  /**
   * @var lmbTaggableCache
   */
  protected $cache;

  function setUp()
  {
    $this->cache = new lmbTaggableCache(lmbCacheFactory::createConnection('file:///'.lmb_env_get('LIMB_VAR_DIR').'/cache'));
  }

  function tearDown()
  {
    $this->cache->flush();
  }

  protected function _createId()
  {
    return 'id_'.mt_rand();
  }

  function testAdd()
  {
    $this->assertTrue($this->cache->add($key = $this->_createId(), $value = 'value', false, 'tag'));
    $this->assertFalse($this->cache->add($key, 'another_value', false, 'tag'));
    $this->assertEqual($this->cache->get($key), $value);
  }

  function testDeleteByTags_SingleTag()
  {
    $this->cache->set($key = $this->_createId(), $value = 'value', false, 'tag_delete');

    $this->cache->deleteByTag('tag_delete');

    $this->assertNull($this->cache->get($key));
  }

  function testDeleteByTags_MultipleTag()
  {
    $this->cache->set($key = $this->_createId(), $value = 'value', false, array('tag1','tag2'));

    $this->cache->deleteByTag('tag1');

    $this->assertNull($this->cache->get($key));
  }

  function testDeleteByTags_DifferentTag()
  {
    $this->cache->set($key = $this->_createId(), $value = 'value', false, 'tag');

    $this->cache->deleteByTag('different_tag');

    $this->assertIdentical($this->cache->get($key), $value);
  }

  function testTagCoincidesWithKey()
  {
    $this->assertTrue($this->cache->add($key = $this->_createId(), $value = 'value', false, $key));
    $this->assertIdentical($this->cache->get($key), $value);
  }
}
