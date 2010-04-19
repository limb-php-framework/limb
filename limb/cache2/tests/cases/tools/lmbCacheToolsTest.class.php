<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/core/src/lmbObject.class.php');

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
    $connection = lmbToolkit::instance()->createCacheConnectionByDSN('fake://localhost/');
    $this->assertEqual($connection->getType(),'fake');
  }

  function testCreateCacheFakeConnection()
  {
    $connection = lmbToolkit::instance()->createCacheFakeConnection();
    $this->assertEqual($connection->getType(),'fake');
  }

  function testCreateConnectionByNameCacheDisabled()
  {
    $config = new lmbObject();
    $config->set('cache_enabled',false);
    lmbToolkit::instance()->setConf('cache',$config);
    $connection = lmbToolkit::instance()->createCacheConnectionByName('some_name');
    $this->assertEqual($connection->getType(),'fake');
  }

  function testCreateConnectionByNameCacheEnabledAndDsnNotFound()
  {
    $config = new lmbObject();
    $config->set('cache_enabled',true);
    lmbToolkit::instance()->setConf('cache',$config);
    $connection = lmbToolkit::instance()->createCacheConnectionByName('some_name');
    $this->assertEqual($connection->getType(),'fake');
  }

  function testCreateConnectionByNameCacheEnabled()
  {
    $config = $this->_getConfig();
    lmbToolkit::instance()->setConf('cache',$config);
    $connection = lmbToolkit::instance()->createCacheConnectionByName('dsn');
    $this->assertEqual($connection->getType(),'file');
    $connection->set('var','test');
    $this->assertEqual($connection->get('var'),'test');
  }

  function testCreateCache()
  {
    $config = $this->_getConfig();
    lmbToolkit::instance()->setConf('cache',$config);
    $connection = lmbToolkit::instance()->createCache('dsn');
    $this->assertisA($connection, 'lmbCacheFileConnection');
    $this->assertEqual($connection->getType(),'file');
    $connection->set('var','test');
    $this->assertEqual($connection->get('var'),'test');
  }

  function testCreateMintCache()
  {
    $config = $this->_getConfig();
    $config->set('mint_cache_enabled',true);
    lmbToolkit::instance()->setConf('cache',$config);
    $connection = lmbToolkit::instance()->createCache('dsn');
    $this->assertIsA($connection, 'lmbMintCache');
    $this->assertIsA($connection->getWrappedConnection(), 'lmbCacheFileConnection');
    $connection->set('var','test');
    $this->assertEqual($connection->get('var'),'test');
  }

  function testCreateLoggedCache()
  {
    $config = $this->_getConfig();
    $config->set('mint_cache_enabled',true);
    $config->set('cache_log_enabled',true);
    lmbToolkit::instance()->setConf('cache',$config);

    $connection = lmbToolkit::instance()->createCache('dsn');

    $this->assertIsA($connection, 'lmbLoggedCache');
    $this->assertIsA($connection->getWrappedConnection(), 'lmbMintCache');
    $this->assertIsA($connection->getWrappedConnection()->getWrappedConnection(), 'lmbCacheFileConnection');

    $connection->set('var', 'test');
    $this->assertEqual($connection->get('var'), 'test');
  }

  function testCreateLoggedCacheWithOutMintCache()
  {
    $config = $this->_getConfig();
    $config->set('cache_log_enabled',true);
    lmbToolkit::instance()->setConf('cache',$config);
    $connection = lmbToolkit::instance()->createCache('dsn');

    $this->assertIsA($connection, 'lmbLoggedCache');

    $connection->set('var', 'test');
    $this->assertEqual($connection->get('var'), 'test');
  }

  function testGetCacheByName()
  {
    $config = $this->_getConfig();
    lmbToolkit::instance()->setConf('cache',$config);
    $connection = lmbToolkit::instance()->getCacheByName('dsn');
    $this->assertEqual($connection->getType(),'file');
    $connection->set('var','test');
    $this->assertEqual($connection->get('var'),'test');
  }

  function testGetCacheDefaultFake()
  {
    $config = $this->_getConfig($without_dsn = true);
    lmbToolkit::instance()->setConf('cache',$config);
    $connection = lmbToolkit::instance()->getCache();
    $this->assertEqual($connection->getType(),'fake');
  }

  function testGetCacheDefault()
  {
    $config = $this->_getConfig($without_dsn = true);
    $config->set('default_cache_dsn',"file:///" . LIMB_VAR_DIR . "/cache2/");
    lmbToolkit::instance()->setConf('cache',$config);
    $connection = lmbToolkit::instance()->getCache();
    $this->assertEqual($connection->getType(),'file');
    $connection->set('var','test');
    $connection = lmbToolkit::instance()->getCache();
    $this->assertEqual($connection->get('var'),'test');
  }

  function testGetCache()
  {
    $config = $this->_getConfig();
    lmbToolkit::instance()->setConf('cache',$config);
    $connection = lmbToolkit::instance()->getCache('dsn');
    $this->assertEqual($connection->getType(),'file');
    $connection->set('var','test');
    $connection = lmbToolkit::instance()->getCache('dsn');
    $this->assertEqual($connection->get('var'),'test');
  }

  protected function _getConfig($without_dsn = false) {
    $config = new lmbObject();
    $config->set('cache_enabled', true);
    if (!$without_dsn)
      $config->set('dsn_cache_dsn',"file:///" . LIMB_VAR_DIR . "/cache2/");
    return $config;
  }

}
