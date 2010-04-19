<?php

/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/core/src/lmbObject.class.php');
lmb_require('limb/cache2/src/lmbCacheFactory.class.php');

class lmbCacheFactoryTest extends UnitTestCase
{
  function testCacheMemcacheCreation()
  {
    if(!extension_loaded('memcache'))
      return $this->pass('Memcache extension not found. Test skipped.');

    if(!class_exists('Memcache'))
      return $this->pass('Memcache class not found. Test skipped.');

    $cache = lmbCacheFactory::createConnection('memcache://some_host:1112');
    $this->assertEqual('memcache' , $cache->getType());
  }

  function testCacheFileCreation()
  {
    $cache_dir = lmb_var_dir() . '/some_dir';
    $cache = lmbCacheFactory::createConnection('file://' . $cache_dir);
    $this->assertEqual('file' , $cache->getType());
    $this->assertEqual($cache_dir, $cache->getCacheDir());
  }

  function testCacheCreation_WithOneWrapper()
  {
    $cache_dir = lmb_var_dir() . '/some_dir';
    $cache = lmbCacheFactory::createConnection('file://' . $cache_dir.'?wrapper=lmbMintCache');
    $this->assertIsA($cache, 'lmbMintCache');

    $this->assertTrue('file' , $cache->getType());
    $this->assertEqual($cache_dir, $cache->getCacheDir());
  }

  function testCacheCreation_WithMultipleWrappers()
  {
    $cache_dir = lmb_var_dir() . '/some_dir';
    $cache = lmbCacheFactory::createConnection(
      'file://' . $cache_dir.'?wrapper[]=lmbMintCache&wrapper[]=lmbTaggableCache'
    );
    $this->assertIsA($cache, 'lmbTaggableCache');

    $this->assertTrue('file' , $cache->getType());
    $this->assertEqual($cache_dir, $cache->getCacheDir());
  }

  function testCacheApcCreation()
  {
    $cache = lmbCacheFactory::createConnection('apc:');
    $this->assertTrue('apc:' , $cache->getType());
  }
}
