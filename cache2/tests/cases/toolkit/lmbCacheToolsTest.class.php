<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/core/src/lmbObject.class.php');
lmb_require('limb/cache2/src/drivers/lmbCacheFileConnection.class.php');

class lmbCacheToolsTest extends UnitTestCase
{
  function setUp()
  {
  	parent::setUp();
    lmbToolkit :: save();
  }

  function tearDown()
  {
  	lmbToolkit::restore();
  	parent::tearDown();
  }

  function testCreateCacheConnectionByDSN()
  {
    $connection = lmbToolkit::instance()->createCacheConnectionByDSN('fake:localhost/');
    $this->assertEqual($connection->getType(),'fake');
  }

  function testCreateCacheConnectionByDSN_WithWrapper()
  {
  	$dsn = new lmbUri('memory:');
  	$dsn->addQueryItem('wrapper', array('mint', 'logged'));

    $connection = lmbToolkit::instance()->createCacheConnectionByDSN($dsn);

    $this->assertEqual($connection->getType(),'memory');
    $this->assertIsA($connection, 'lmbLoggedCacheWrapper');
    $this->assertIsA($connection->getWrappedConnection(), 'lmbMintCacheWrapper');
    $this->assertIsA($connection->getWrappedConnection()->getWrappedConnection(), 'lmbCacheMemoryConnection');
  }

  function testCreateCacheFakeConnection()
  {
    $connection = lmbToolkit::instance()->createCacheFakeConnection();
    $this->assertEqual($connection->getType(),'fake');
  }

  function testCreateConnectionByName_CacheDisabled()
  {
    $config = new lmbObject();
    $config->set('cache_enabled',false);
    lmbToolkit::instance()->setConf('cache',$config);

    $connection = lmbToolkit::instance()->createCacheConnectionByName('some_name');
    $this->assertEqual($connection->getType(),'fake');
  }

  function testCreateConnectionByName_CacheEnabledAndDsnNotFound()
  {
    $config = new lmbObject();
    $config->set('cache_enabled',true);
    lmbToolkit::instance()->setConf('cache',$config);

    $connection = lmbToolkit::instance()->createCacheConnectionByName('some_name');
    $this->assertEqual($connection->getType(),'fake');
  }

  function testCreateConnectionByName_CacheEnabled()
  {
    $config = $this->_getConfig();
    lmbToolkit::instance()->setConf('cache',$config);

    $connection = lmbToolkit::instance()->createCacheConnectionByName('test');
    $this->assertEqual($connection->getType(), 'memory');

    $connection->set('var','test');

    $this->assertEqual($connection->get('var'),'test');
  }

  function testCreateCache()
  {
    $config = $this->_getConfig();
    lmbToolkit::instance()->setConf('cache',$config);

    $connection = lmbToolkit::instance()->createCache('test');
    if($this->assertisA($connection, 'lmbCacheMemoryConnection'))
      $this->assertEqual($connection->getType(),'memory');

    $connection->set('var','test');

    $this->assertEqual($connection->get('var'),'test');
  }

  function testCreate_MintCacheByDSN()
  {
    $config = $this->_getConfig('memory:?wrapper=mint');
    lmbToolkit::instance()->setConf('cache', $config);

    $connection = lmbToolkit::instance()->createCache('test');

    if($this->assertIsA($connection, 'lmbMintCacheWrapper'))
      $this->assertIsA($connection->getWrappedConnection(), 'lmbCacheMemoryConnection');
  }

  /**
   * @deprecated
   */
  function testCreate_MintCacheByGlobalSetting()
  {
    $config = $this->_getConfig();
    $config->set('mint_cache_enabled', true);
    lmbToolkit::instance()->setConf('cache',$config);

    $connection = lmbToolkit::instance()->createCache('test');

    if($this->assertIsA($connection, 'lmbMintCacheWrapper'))
      $this->assertIsA($connection->getWrappedConnection(), 'lmbCacheMemoryConnection');
  }

  function testCreate_LoggedCache()
  {
    $config = $this->_getConfig();
    $config->set('mint_cache_enabled', true);
    $config->set('cache_log_enabled', true);
    lmbToolkit::instance()->setConf('cache',$config);

    $connection = lmbToolkit::instance()->createCache('test');

    if($this->assertIsA($connection, 'lmbLoggedCacheWrapper'))
    {
      $this->assertIsA($connection->getWrappedConnection(), 'lmbMintCacheWrapper');
      $this->assertIsA($connection->getWrappedConnection()->getWrappedConnection(), 'lmbCacheMemoryConnection');
    }
  }

  function testCreate_LoggedCacheWithOutMintCache()
  {
    $config = $this->_getConfig();
    $config->set('cache_log_enabled', true);
    lmbToolkit::instance()->setConf('cache',$config);

    $connection = lmbToolkit::instance()->createCache('test');

    if($this->assertIsA($connection, 'lmbLoggedCacheWrapper'))
      $this->assertIsA($connection->getWrappedConnection(), 'lmbCacheMemoryConnection');
  }

  function testGetCacheByName()
  {
    $config = $this->_getConfig();
    lmbToolkit::instance()->setConf('cache',$config);

    $connection = lmbToolkit::instance()->getCacheByName('test');
    $this->assertEqual($connection->getType(),'memory');
  }

  function testGetCache_DefaultFake()
  {
    $config = $this->_getConfig($dsn = null);
    lmbToolkit::instance()->setConf('cache', $config);

    $connection = lmbToolkit::instance()->getCache();
    $this->assertEqual($connection->getType(),'fake');
  }

  function testGetCache_Default()
  {
  	$cache = new lmbCacheFileConnection('file://' . lmb_var_dir() . '/cache2/testGetCache_Default');
    lmbToolkit::instance()->setCache('default', $cache);

    $connection = lmbToolkit::instance()->getCache();
    $this->assertEqual($connection->getType(), 'file');

    $connection->set('var','test');

    $connection = lmbToolkit::instance()->getCache();
    $this->assertEqual($connection->get('var'),'test');
  }

  function testGetCache()
  {
    $config = $this->_getConfig();
    lmbToolkit::instance()->setConf('cache',$config);

    $connection = lmbToolkit::instance()->getCache('test');
    $this->assertEqual($connection->getType(),'memory');

    $connection->set('var','test');

    $connection = lmbToolkit::instance()->getCache('test');
    $this->assertEqual($connection->get('var'),'test');
  }

  function testGetPartialHtmlCacheStorage()
  {
    $cache = new lmbCacheFileConnection('file://' . lmb_var_dir() . '/cache2/testGetPartialHtmlCacheStorage');
    lmbToolkit::instance()->setCache('default', $cache);

    $this->assertEqual(
      lmbToolkit::instance()->getPartialHtmlCacheStorage(),
      $cache
    );
  }

  protected function _getConfig($dsn = null, $cache_name = 'test')
  {
    $config = new lmbObject();
    $config->set('cache_enabled', true);
    if ($dsn)
      $config->set($cache_name . '_cache_dsn', $dsn);
    else
      $config->set($cache_name . '_cache_dsn', 'memory:');
    return $config;
  }
}