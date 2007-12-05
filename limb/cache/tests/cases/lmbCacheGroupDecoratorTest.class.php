<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require(dirname(__FILE__) . '/lmbCacheFileBackendTest.class.php');
lmb_require('limb/cache/src/lmbCacheGroupDecorator.class.php');

class lmbCacheGroupDecoratorTest extends lmbCacheFileBackendTest
{
  function _createPersisterImp()
  {
    return new lmbCacheGroupDecorator(parent::_createPersisterImp() , 'default_group');
  }

  function testPutToCacheWithGroup()
  {
    $key = 1;
    $this->cache->set($key, $v1 = 'value1');
    $this->cache->set($key, $v2 = 'value2', array('group' => 'test-group'));

    $cache_value = $this->cache->get($key);
    $this->assertEqual($cache_value, $v1);

    $cache_value = $this->cache->get($key, array('group' => 'test-group'));
    $this->assertEqual($cache_value, $v2);
  }

  function testFlushGroup()
  {
    $key = 1;
    $this->cache->set($key, $v1 = 'value1');
    $this->cache->set($key, $v2 = 'value2', array('group' => 'test-group'));

    $this->cache->flushGroup('test-group');

    $this->assertFalse($this->cache->get($key, array('group' => 'test-group')));

    $var = $this->cache->get($key);
    $this->assertEqual($var, $v1);
  }

}
