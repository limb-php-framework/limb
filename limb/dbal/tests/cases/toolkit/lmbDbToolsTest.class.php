<?php
lmb_require('limb/core/src/lmbSet.class.php');
lmb_require('limb/toolkit/src/lmbToolkit.class.php');
lmb_require('limb/dbal/src/toolkit/lmbDbTools.class.php');

class ExceptionalDbConfStub extends lmbConf
{
  function __construct(){}

  function get($name, $default = LIMB_UNDEFINED)
  {
    throw new lmbException("Ooops!");
  }
}

class lmbDbToolsTest extends UnitTestCase
{
  var $tools;
  var $config;
  var $conn;

  function setUp()
  {
    parent::setUp();

    $this->tools = new lmbDbTools();
    $this->conn = $this->tools->getDefaultDbConnection();
    $this->config = array(
      'dsn' => 'mysql://root:test@localhost/hello_from_foo?charset=cp1251',
      'another_dsn' => 'sqlite://kraynopp:pasha@ksu/kadrs?charset=utf8'
    );
    lmbToolkit::instance()->setConf('db', new lmbSet($this->config));
    $this->tools->setDefaultDbConnection($this->tools->createDbConnection(new lmbDbDSN($this->config['dsn'])));
  }
  
  function tearDown()
  {
    $this->tools->setDefaultDbConnection($this->conn);    
  }

  function testGetDbDSNByName()
  {
    $this->assertEqual($this->tools->getDbDSNByName('another_dsn'), new lmbDbDSN($this->config['another_dsn']));
  }

  function testGetDefaultDbDSN()
  {
    $this->assertEqual(
      $this->tools->getDefaultDbDSN(),
      $this->tools->getDbDSNByName('dsn')
    );
  }

  function testIsDefaultDbDSNAvailable()
  {
    $tools = new lmbDbTools();
    $tools->setDefaultDbDSN("mysql://localhost/test");
    $this->assertTrue($tools->isDefaultDbDSNAvailable());

    $toolkit = lmbToolkit :: save();
    $tools = new lmbDbTools();
    $toolkit->setConf('db', new ExceptionalDbConfStub());
    $this->assertFalse($tools->isDefaultDbDSNAvailable());
    lmbToolkit :: restore();
  }

  function testSetDbDSNByName()
  {
    $this->assertEqual($this->tools->getDbDSNByName('another_dsn'), new lmbDbDSN($this->config['another_dsn']));
    $dsn = new lmbDbDSN($this->config['dsn']);
    $this->tools->setDbDSNByName('another_dsn', $dsn);
    $this->assertEqual($this->tools->getDbDSNByName('another_dsn'), new lmbDbDSN($this->config['dsn']));
  }

  function testGetDbConnectionByName()
  {
    $connection = $this->tools->createDbConnection(new lmbDbDSN($this->config['another_dsn']));
    $this->assertIdentical($connection, $this->tools->getDbConnectionByName('another_dsn'));
  }

  function testSetDbConnectionByName()
  {
    $dsn = new lmbDbDSN($this->config['dsn']);
    $another_dsn = new lmbDbDSN($this->config['another_dsn']);
    
    $connection = $this->tools->createDbConnection($dsn);
    $another_connection = $this->tools->createDbConnection($another_dsn);

    $this->assertIdentical($connection, $this->tools->getDbConnectionByName('dsn'));

    $this->tools->setDbConnectionByName('dsn', $another_connection);

    $this->assertIdentical($another_connection, $this->tools->getDbConnectionByName('dsn'));
  }  
  
  function testGetDbConnectionByDsn()
  {
    $connection = $this->tools->createDbConnection(new lmbDbDSN($this->config['another_dsn']));
    $this->assertIdentical($connection, $this->tools->getDbConnectionByDsn(new lmbDbDSN($this->config['another_dsn'])));
  }
  
  function testSetDbConnectionByDsn()
  {
    $dsn = new lmbDbDSN($this->config['dsn']);
    $another_dsn = new lmbDbDSN($this->config['another_dsn']);
    
    $connection = $this->tools->createDbConnection($dsn);
    $another_connection = $this->tools->createDbConnection($another_dsn);

    $this->assertIdentical($connection, $this->tools->getDbConnectionByDsn($dsn));

    $this->tools->setDbConnectionByDsn($dsn, $another_connection);

    $this->assertIdentical($another_connection, $this->tools->getDbConnectionByDsn($dsn));
  }  
}
