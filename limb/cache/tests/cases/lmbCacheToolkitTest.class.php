<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/core/src/lmbObject.class.php');

class lmbCacheToolkitTest extends UnitTestCase
{

  function testCreateCacheConnectionByDSN()
  {
    lmbToolkit :: save();
    $connection = lmbToolkit::instance()->createCacheConnectionByDSN('fake://localhost/');
    $this->assertEqual($connection->getType(),'fake');
    lmbToolkit :: restore();
  }

  function testCreateCacheFakeConnection()
  {
    lmbToolkit :: save();
    $connection = lmbToolkit::instance()->createCacheFakeConnection();
    $this->assertEqual($connection->getType(),'fake');
    lmbToolkit :: restore();
  }

  function testCreateConnectionByNameCacheDisabled()
  {
    lmbToolkit :: save();
    $config = new lmbObject();
    $config->set('cache_enabled',false);
    lmbToolkit::instance()->setConf('cache',$config);
    $connection = lmbToolkit::instance()->createCacheConnectionByName('some_name');
    $this->assertEqual($connection->getType(),'fake');
    lmbToolkit :: restore();
  }

  
  function testCreateConnectionByNameCacheEnabledAndDsnNotFound()
  {
    lmbToolkit :: save();
    $config = new lmbObject();
    $config->set('cache_enabled',true);
    lmbToolkit::instance()->setConf('cache',$config);
    $connection = lmbToolkit::instance()->createCacheConnectionByName('some_name');
    $this->assertEqual($connection->getType(),'fake');
    lmbToolkit :: restore();
  }

  function testCreateConnectionByNameCacheEnabled()
  {
    lmbToolkit :: save();
    $config = new lmbObject();
    $config->set('cache_enabled',true);
    $config->set('dsn_cache_dsn',"file:///" . LIMB_VAR_DIR . "/cache/");
    lmbToolkit::instance()->setConf('cache',$config);
    $connection = lmbToolkit::instance()->createCacheConnectionByName('dsn');
    $this->assertEqual($connection->getType(),'file');
    $connection->set('var','test');
    $this->assertEqual($connection->get('var'),'test');
    lmbToolkit :: restore();
  }
  function testCreateCache()
  {
    lmbToolkit :: save();
    $config = new lmbObject();
    $config->set('cache_enabled',true);
    $config->set('mint_cache_enabled',false);
    $config->set('cache_log_enabled',false);
    $config->set('dsn_cache_dsn',"file:///" . LIMB_VAR_DIR . "/cache/");
    lmbToolkit::instance()->setConf('cache',$config);
    $connection = lmbToolkit::instance()->createCache('dsn');
    $this->assertTrue($connection instanceof lmbCacheFileConnection);
    $this->assertEqual($connection->getType(),'file');
    $connection->set('var','test');
    $this->assertEqual($connection->get('var'),'test');
    lmbToolkit :: restore();
  }
  function testCreateMintCache()
  {
    lmbToolkit :: save();
    $config = new lmbObject();
    $config->set('cache_enabled',true);
    $config->set('mint_cache_enabled',true);
    $config->set('cache_log_enabled',false);
    $config->set('dsn_cache_dsn',"file:///" . LIMB_VAR_DIR . "/cache/");
    lmbToolkit::instance()->setConf('cache',$config);
    $connection = lmbToolkit::instance()->createCache('dsn');
    $this->assertTrue($connection instanceof lmbMintCache);
    $connection->set('var','test');
    $this->assertEqual($connection->get('var'),'test');
    lmbToolkit :: restore();
  }

  function testCreateLoggedCache()
  {
    lmbToolkit :: save();
    $config = new lmbObject();
    $config->set('cache_enabled',true);
    $config->set('mint_cache_enabled',true);
    $config->set('cache_log_enabled',true);
    $config->set('dsn_cache_dsn',"file:///" . LIMB_VAR_DIR . "/cache/");
    lmbToolkit::instance()->setConf('cache',$config);
    $connection = lmbToolkit::instance()->createCache('dsn');
    $this->assertTrue($connection instanceof lmbLoggedCache);
    $this->assertEqual($connection->getName(),'dsn');
    $connection->set('var','test');
    $this->assertEqual($connection->get('var'),'test');
    lmbToolkit :: restore();
  }
  
  function testCreateLoggedCacheWithOutMintCache()
  {
    lmbToolkit :: save();
    $config = new lmbObject();
    $config->set('cache_enabled',true);
    $config->set('mint_cache_enabled',false);
    $config->set('cache_log_enabled',true);
    $config->set('dsn_cache_dsn',"file:///" . LIMB_VAR_DIR . "/cache/");
    lmbToolkit::instance()->setConf('cache',$config);
    $connection = lmbToolkit::instance()->createCache('dsn');
    $this->assertTrue($connection instanceof lmbLoggedCache);
    $this->assertEqual($connection->getName(),'dsn');
    $connection->set('var','test');
    $this->assertEqual($connection->get('var'),'test');
    lmbToolkit :: restore();
  }

  function testGetCacheByName()
  {
    lmbToolkit :: save();
    $config = new lmbObject();
    $config->set('cache_enabled',true);
    $config->set('mint_cache_enabled',false);
    $config->set('cache_log_enabled',false);
    $config->set('dsn_cache_dsn',"file:///" . LIMB_VAR_DIR . "/cache/");
    lmbToolkit::instance()->setConf('cache',$config);
    $connection = lmbToolkit::instance()->getCacheByName('dsn');
    $this->assertEqual($connection->getType(),'file');
    $connection->set('var','test');
    $this->assertEqual($connection->get('var'),'test');
    lmbToolkit :: restore();
  }

  function testGetCacheDefaultFake()
  {
    lmbToolkit :: save();
    $config = new lmbObject();
    $config->set('cache_enabled',true);
    $config->set('mint_cache_enabled',false);
    $config->set('cache_log_enabled',false);
    lmbToolkit::instance()->setConf('cache',$config);
    $connection = lmbToolkit::instance()->getCache();
    $this->assertEqual($connection->getType(),'fake');
    lmbToolkit :: restore();
  }

  function testGetCacheDefault()
  {
    lmbToolkit :: save();
    $config = new lmbObject();
    $config->set('cache_enabled',true);
    $config->set('mint_cache_enabled',false);
    $config->set('cache_log_enabled',false);
    $config->set('default_cache_dsn',"file:///" . LIMB_VAR_DIR . "/cache/");
    lmbToolkit::instance()->setConf('cache',$config);
    $connection = lmbToolkit::instance()->getCache();
    $this->assertEqual($connection->getType(),'file');
    $connection->set('var','test');
    $connection = lmbToolkit::instance()->getCache();
    $this->assertEqual($connection->get('var'),'test');
    lmbToolkit :: restore();
  }

  function testGetCache()
  {
    lmbToolkit :: save();
    $config = new lmbObject();
    $config->set('cache_enabled',true);
    $config->set('mint_cache_enabled',false);
    $config->set('cache_log_enabled',false);
    $config->set('dsn_cache_dsn',"file:///" . LIMB_VAR_DIR . "/cache/");
    lmbToolkit::instance()->setConf('cache',$config);
    $connection = lmbToolkit::instance()->getCache('dsn');
    $this->assertEqual($connection->getType(),'file');
    $connection->set('var','test');
    $connection = lmbToolkit::instance()->getCache('dsn');
    $this->assertEqual($connection->get('var'),'test');
    lmbToolkit :: restore();
  }

}
