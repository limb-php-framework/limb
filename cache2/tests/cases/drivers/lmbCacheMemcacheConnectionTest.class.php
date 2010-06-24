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

  function testAddAfterDelete() {
    $id = $this->_getUniqueId('testAddAfterDelete');
    $this->assertTrue($this->cache->add($id, $v = 'value'));

    $this->assertEqual($this->cache->get($id), $v);

    $this->assertTrue($this->cache->delete($id));

    $this->assertNull($this->cache->get($id));

    $this->assertTrue($this->cache->add($id, $v = 'another value'));

    $this->assertEqual($this->cache->get($id), $v);
  }

  function testInvalidKeyOnMultipleGet()
  {
    $id1 = 'white space';
    $id2 = 'nowhitespace';
    $id3 = 'with_under_score';

    $this->cache->set($id1, 'value1');
    $this->cache->set($id2, 'value2');
    $this->cache->set($id3, 'value3');

    $result = $this->cache->get(array($id1, $id2, $id3));

    $this->assertNotEqual('value1', $result[$id1]);
    $this->assertEqual('value2', $result[$id2]);
    $this->assertEqual('value3', $result[$id3]);
  }
}
