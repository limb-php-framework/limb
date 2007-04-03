<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCacheFilePersisterTest.class.php 4985 2007-02-08 15:35:06Z pachanga $
 * @package    cache
 */
lmb_require('limb/classkit/src/lmbObject.class.php');
lmb_require('limb/cache/src/lmbCacheFilePersister.class.php');
lmb_require('limb/util/src/system/lmbFs.class.php');
lmb_require(dirname(__FILE__) . '/lmbCacheTestBase.class.php');

class lmbCacheFilePersisterTest extends lmbCacheTestBase
{
  var $cache_dir;

  function _createPersisterImp()
  {
    return new lmbCacheFilePersister();
  }

  function testCachedDiskFiles()
  {
    $cache = new lmbCacheFilePersister('whatever');
    $cache_dir = $cache->getCacheDir();

    $items = lmbFs :: ls($cache_dir);
    $this->assertEqual(sizeof($items), 0);

    $cache->put(1, $cache_value = 'value');

    $items = lmbFs :: ls($cache_dir);
    $this->assertEqual(sizeof($items), 1);

    $this->assertEqual($cache->get(1), $cache_value);

    $cache->flushAll();
    rmdir($cache_dir);
  }

  function testProperSerializing()
  {
    $obj = new lmbObject();
    $obj->set('foo', 'wow');

    $this->cache->put(1, $obj);

    $this->assertEqual($obj, $this->cache->get(1));
  }
}

?>