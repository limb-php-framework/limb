<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

lmb_require('limb/cache/src/lmbCacheFileBackend.class.php');
lmb_require(dirname(__FILE__) . '/lmbCacheBackendTest.class.php');

class lmbCacheFileBackendTest extends lmbCacheBackendTest
{
  var $cache_dir;

  function _createPersisterImp()
  {
    $this->cache_dir = LIMB_VAR_DIR . '/cache';
    return new lmbCacheFileBackend($this->cache_dir);
  }

  function testCachedDiskFiles()
  {
    $items = lmbFs :: ls($this->cache_dir);
    $this->assertEqual(sizeof($items), 0);

    $this->cache->set(1, $cache_value = 'value');

    $items = lmbFs :: ls($this->cache_dir);
    $this->assertEqual(sizeof($items), 1);

    $this->assertEqual($this->cache->get(1), $cache_value);

    $this->cache->flush();
    rmdir($this->cache_dir);
  }
}
