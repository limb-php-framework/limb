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

class lmbCacheTest extends UnitTestCase
{
  function testCacheMemcacheCreation()
  {
    $cache = lmbCache::createConnection('memcache://some_host:1112');
    $this->assertTrue('memcache' , $cache->getType());
  }

  function testCacheFileCreation()
  {
    $cache_dir = LIMB_VAR_DIR . '/some_dir';
    $cache = lmbCache::createConnection('file://' . $cache_dir);
    $this->assertTrue('file' , $cache->getType());
    $this->assertEqual($cache_dir , $cache->getCacheDir());
  }

  function testCacheApcCreation()
  {
    $cache = lmbCache::createConnection('apc:');
    $this->assertTrue('apc:' , $cache->getType());
  }
}
