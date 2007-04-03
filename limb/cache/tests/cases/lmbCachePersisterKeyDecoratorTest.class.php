<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCachePersisterKeyDecoratorTest.class.php 4985 2007-02-08 15:35:06Z pachanga $
 * @package    cache
 */
lmb_require('limb/cache/src/lmbCachePersisterKeyDecorator.class.php');
lmb_require(dirname(__FILE__) . '/lmbCacheTestBase.class.php');

class CacheKeyFooClass{}
class CacheableFooClass{}

class lmbCachePersisterKeyDecoratorTest extends lmbCacheTestBase
{
  function _createPersisterImp()
  {
    return new lmbCachePersisterKeyDecorator(new lmbCacheMemoryPersister());
  }

  function testGetArrayKeyFalse()
  {
    $this->_testGetFalse(array(1));
  }

  function testGetArrayKeyTrue()
  {
    $this->_testGetTrue(array(1));
  }

  function testGetObjectKeyFalse()
  {
    $this->_testGetFalse(new CacheKeyFooClass());
  }

  function testGetObjectKeyTrue()
  {
    $this->_testGetTrue(new CacheKeyFooClass());
  }

  function testPutToCacheUsingArrayKey()
  {
    $this->_testPutToCache(array(1));
  }

  function testPutToCacheUsingObjectKey()
  {
    $this->_testPutToCache(new CacheKeyFooClass());
  }

  function testPutToCacheWithGroupUsingArrayKey()
  {
    $this->_testPutToCacheWithGroup(array(1));
  }

  function testPutToCacheWithGroupUsingObjectKey()
  {
    $this->_testPutToCacheWithGroup(new CacheKeyFooClass());
  }

  function testFlushValueWithArrayKey()
  {
    $this->_testFlushValue(array(1), array(2));
  }

  function testFlushValueWithObjectKey()
  {
    $key1 = new CacheKeyFooClass();
    $key2 = new CacheKeyFooClass();
    $key2->im_different = 1;

    $this->_testFlushValue($key1, $key2);
  }
}

?>