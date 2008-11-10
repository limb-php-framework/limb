<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/cache2/src/drivers/lmbCacheMemcacheConnection.class.php');
lmb_require(dirname(__FILE__) . '/lmbCacheConnectionTest.class.php');

class lmbCacheMemcacheConnectionTest extends lmbCacheConnectionTest
{
  function __construct()
  {
    $this->dsn = 'memcache://localhost/';
  }

  function skip()
  {
    $this->skipIf(!extension_loaded('memcache'), 'Memcache extension not found. Test skipped.');
    $this->skipIf(!class_exists('Memcache'), 'Memcache class not found. Test skipped.');
  }

  function testIncrementAndDecrement()
  {
    $key = $this->_getUniqueId();

    $this->assertFalse($this->cache->increment($key));

    $this->cache->set($key, "string");
    $this->assertEqual(1, $this->cache->increment($key));
    
    $this->cache->set($key, 0);
    $this->assertEqual(1, $this->cache->increment($key));
    
    $this->cache->increment($key, 10);
    $this->assertEqual(11, $this->cache->get($key));

    $this->cache->decrement($key, 1);
    $this->assertEqual(10, $this->cache->get($key));

    $this->cache->decrement($key, 100);
    $this->assertEqual(0, $this->cache->get($key));
  }

  function testSafeIncrement()
  {
    $key = $this->_getUniqueId();
    $this->assertEqual(1, $this->cache->safeIncrement($key));
  }

  function testSafeDecrement()
  {
    $key = $this->_getUniqueId();
    $this->assertEqual(0, $this->cache->safeDecrement($key));
    $this->assertFalse(null === $this->cache->get($key));
  }
}