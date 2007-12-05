<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/core/src/lmbObject.class.php');
lmb_require('limb/cache/src/lmbCacheFilePersister.class.php');
lmb_require('limb/fs/src/lmbFs.class.php');
lmb_require(dirname(__FILE__) . '/lmbCacheTestBase.class.php');

class lmbCacheFilePersisterTest extends lmbCacheTestBase
{
  var $cache_dir;

  function _createPersisterImp()
  {
    $this->cache_dir = LIMB_VAR_DIR . '/cache';
    return new lmbCacheFilePersister($this->cache_dir);
  }

  function testCachedDiskFiles()
  {
    $items = lmbFs :: ls($this->cache_dir);
    $this->assertEqual(sizeof($items), 0);

    $this->cache->put(1, $cache_value = 'value');

    $items = lmbFs :: ls($this->cache_dir);
    $this->assertEqual(sizeof($items), 1);

    $this->assertEqual($this->cache->get(1), $cache_value);

    $this->cache->flushAll();
    rmdir($this->cache_dir);
  }

  function testProperSerializing()
  {
    $obj = new lmbObject();
    $obj->set('foo', 'wow');

    $this->cache->put(1, $obj);

    $this->assertEqual($obj, $this->cache->get(1));
  }
}


