<?php
lmb_require('limb/cache2/src/macro/cache.tag.php');
lmb_require('limb/cache2/src/drivers/lmbCacheAbstractConnection.class.php');
lmb_require('limb/macro/src/lmbMacroTemplate.class.php');
lmb_require('limb/macro/tests/cases/lmbBaseMacroTest.class.php');
lmb_require('limb/toolkit/src/lmbAbstractTools.class.php');


class FakePartialCacheStorageTools extends lmbAbstractTools
{
  public $partial_storage;
  public $manual_partial_storage;

  function setPartialHtmlCacheStorage($storage)
  {
    $this->partial_storage = $storage;
  }

  function getPartialHtmlCacheStorage()
  {
    return $this->partial_storage;
  }

}

lmbToolkit::merge(new FakePartialCacheStorageTools);

class CacheTagTest extends lmbBaseMacroTest
{
  protected $_cache_storage;

  function setUp()
  {
    Mock :: generate('lmbCacheAbstractConnection', 'MockCacheStorage');
    $this->_cache_storage = new MockCacheStorage($this);
    lmbToolkit::instance()->setPartialHtmlCacheStorage($this->_cache_storage);
    parent::setUp();
  }

  function tearDown() {}

  protected function _createMacroByText($string)
  {
    $tpl = $this->_createTemplate($string, 'cache_tag'.time().'.html');
    return $this->_createMacro($tpl);
  }

  function testWithCache()
  {
    $macro = $this->_createMacroByText('{{cache key="foo"}}render{{/cache}}');

    $this->_cache_storage->expectOnce('get', array('foo'));
    $this->_cache_storage->setReturnValue('get', 'cache');

    $out = $macro->render();
    $this->assertEqual('cache', $out);
  }

  function testWithoutCache()
  {
    $macro = $this->_createMacroByText('{{cache key="foo"}}template{{/cache}}');

    $this->_cache_storage->expectOnce('get', array('foo'));
    $this->_cache_storage->expectOnce('set', array('foo', 'template'));

    $out = $macro->render();
    $this->assertEqual('template', $out);
  }

  function testTtl()
  {
    $macro = $this->_createMacroByText('{{cache key="foo" ttl="15"}}template{{/cache}}');

    $this->_cache_storage->expectOnce('get', array('foo'));
    $this->_cache_storage->expectOnce('set', array('foo', 'template', '15'));

    $out = $macro->render();
    $this->assertEqual('template', $out);
  }

  function testManual()
  {
    Mock :: generate('lmbCacheAbstractConnection', 'MockManualCacheStorage');
    $manual_storage = new MockManualCacheStorage($this);

    $macro = $this->_createMacroByText('{{cache key="foo" storage="$this->storage"}}template{{/cache}}');
    $macro->set('storage', $manual_storage);

    $manual_storage->expectOnce('get', array('foo'));
    $manual_storage->expectOnce('set', array('foo', 'template'));

    $out = $macro->render();
    $this->assertEqual('template', $out);
  }
}

?>
