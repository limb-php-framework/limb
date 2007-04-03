<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCachedPagedIteratorTest.class.php 4985 2007-02-08 15:35:06Z pachanga $
 * @package    cache
 */
lmb_require('limb/cache/src/lmbCachedPagedIterator.class.php');
lmb_require('limb/cache/src/lmbCachePersisterKeyDecorator.class.php');
lmb_require('limb/datasource/src/lmbPagedArrayDataset.class.php');

class ArrayDataSetCacheStub extends lmbPagedArrayDataset
{
  var $calls = array('rewind' => 0,
                     'next' => 0,
                     'valid' => 0,
                     'count' => 0);

  function rewind()
  {
    $this->calls['rewind']++;
    return parent :: rewind();
  }

  function next()
  {
    $this->calls['next']++;
    return parent :: next();
  }

  function valid()
  {
    $this->calls['valid']++;
    return parent :: valid();
  }

  function count()
  {
    $this->calls['count']++;
    return parent :: count();
  }
}

class lmbCachedPagedIteratorTest extends UnitTestCase
{
  var $cache;

  function setUp()
  {
    $this->cache = new lmbCachePersisterKeyDecorator(new lmbCacheMemoryPersister());
    $this->cache->flushAll();
  }

  function tearDown()
  {
    $this->cache->flushAll();
  }

  function testCheckDecoratedObjectMethodsCalledOnlyOnceIfIterateTwice()
  {
    $arr = array(array('foo'),
                 array('bar'),
                 array('wow'));

    $stub = new ArrayDataSetCacheStub($arr);
    $rs = new lmbCachedPagedIterator($stub, $this->cache);

    $clean_stub = $stub;

    $this->_verifyIteratorMatchesArray($rs, $arr);
    $this->_verifyIteratorMatchesArray($rs, $arr);

    $this->assertEqual($stub->calls['rewind'], 1);
    $this->assertEqual($stub->calls['valid'], 4);
    $this->assertEqual($stub->calls['next'], 3);

    $var = $this->cache->get($clean_stub, LIMB_RS_CACHE_COMMON_GROUP);
    $this->assertEqual($var->getArray(), $arr);
  }

  function testDecoratedMethodsNotCalledSinceUsingPreCachedVersion()
  {
    $arr = array(array('foo'),
                 array('bar'),
                 array('wow'));

    $stub = new ArrayDataSetCacheStub($arr);

    $this->cache->put($stub, new lmbPagedArrayDataset($arr), LIMB_RS_CACHE_COMMON_GROUP);

    $rs = new lmbCachedPagedIterator($stub, $this->cache);

    $this->_verifyIteratorMatchesArray($rs, $arr);
    $this->_verifyIteratorMatchesArray($rs, $arr);

    $this->assertEqual($stub->calls['rewind'], 0);
    $this->assertEqual($stub->calls['valid'], 0);
    $this->assertEqual($stub->calls['next'], 0);
  }

  function testNewPaginationCleansPreviousCache()
  {
    $arr = array(array('foo'),
                 array('bar'),
                 array('wow'));

    $arr_for_pager1 = array(array('bar'));
    $arr_for_pager2 = array(array('bar'),
                            array('wow'));

    $stub = new ArrayDataSetCacheStub($arr);
    $rs = new lmbCachedPagedIterator($stub, $this->cache);

    $this->_verifyIteratorMatchesArray($rs, $arr);
    $this->_verifyIteratorMatchesArray($rs, $arr);

    $rs->paginate(1, 1);

    $this->_verifyIteratorMatchesArray($rs, $arr_for_pager1);
    $this->_verifyIteratorMatchesArray($rs, $arr_for_pager1);

    $rs->paginate(1, 2);

    $this->_verifyIteratorMatchesArray($rs, $arr_for_pager2);
    $this->_verifyIteratorMatchesArray($rs, $arr_for_pager2);
  }

  function testCachedTotalRowCount()
  {
    $arr = array(array('foo'),
                 array('bar'),
                 array('wow'));

    $stub = new ArrayDataSetCacheStub($arr);
    $rs = new lmbCachedPagedIterator($stub, $this->cache);

    $this->assertEqual($rs->count(), 3);
    $this->assertEqual($rs->count(), 3);

    $this->assertEqual($stub->calls['count'], 1);
  }

  function testTheSamePagerDontCleanCache()
  {
    $arr = array(array('foo'),
                 array('bar'),
                 array('wow'));

    $arr_for_pager = array(array('bar'));

    $stub = new ArrayDataSetCacheStub($arr);
    $rs = new lmbCachedPagedIterator($stub, $this->cache);

    $this->assertEqual($rs->count(), 3);

    $rs->paginate(0, 10);
    $this->assertEqual($rs->count(), 3);

    $this->assertEqual($stub->calls['count'], 2);

    $rs->paginate(0, 10);
    $this->assertEqual($rs->count(), 3);

    $this->assertEqual($stub->calls['count'], 2);
  }

  protected function _verifyIteratorMatchesArray($iterator, $array)
  {
    $counter = 0;
    foreach($iterator as $record)
    {
      $this->assertEqual($record->export(), $array[$counter]);
      $counter++;
    }

    $this->assertEqual($counter, sizeof($array));
    $this->assertEqual($counter, $iterator->countPaginated());
  }
}

?>