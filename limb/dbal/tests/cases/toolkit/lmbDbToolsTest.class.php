<?php
lmb_require('limb/core/src/lmbSet.class.php');
lmb_require('limb/toolkit/src/lmbToolkit.class.php');
lmb_require('limb/dbal/src/toolkit/lmbDbTools.class.php');

class lmbDbToolsTest extends UnitTestCase
{
  public $toolkit;
  public $config;
  protected $conn;

  function setUp()
  {
    parent::setUp();
    $this->toolkit = new lmbDbTools();
    $this->conn = $this->toolkit->getDefaultDbConnection();
    $this->config = array(
      'dsn' => 'mysql://root:test@localhost/hello_from_foo?charset=cp1251',
      'another_dsn' => 'sqlite://kraynopp:pasha@ksu/kadrs?charset=utf8'
    );
    lmbToolkit::instance()->setConf('db', new lmbSet($this->config));
    $this->toolkit->setDefaultDbConnection($this->toolkit->createDbConnection(new lmbDbDSN($this->config['dsn'])));
  }
  
  function tearDown()
  {
    $this->toolkit->setDefaultDbConnection($this->conn);    
  }

  function testGetDbDSNByName()
  {
    $this->assertEqual($this->toolkit->getDbDSNByName('another_dsn'), new lmbDbDSN($this->config['another_dsn']));
  }

  function testGetDefaultDbDSN()
  {
    $this->assertEqual(
      $this->toolkit->getDefaultDbDSN(),
      $this->toolkit->getDbDSNByName('dsn')
    );
  }

  function testSetDbDSNByName()
  {
    $this->assertEqual($this->toolkit->getDbDSNByName('another_dsn'), new lmbDbDSN($this->config['another_dsn']));
    $dsn = new lmbDbDSN($this->config['dsn']);
    $this->toolkit->setDbDSNByName('another_dsn', $dsn);
    $this->assertEqual($this->toolkit->getDbDSNByName('another_dsn'), new lmbDbDSN($this->config['dsn']));
  }

  function testGetDbConnectionByName()
  {
    $connection = $this->toolkit->createDbConnection(new lmbDbDSN($this->config['another_dsn']));
    $this->assertIdentical($connection, $this->toolkit->getDbConnectionByName('another_dsn'));
  }

  function testSetDbConnectionByName()
  {
    $connection = $this->toolkit->createDbConnection(new lmbDbDSN($this->config['dsn']));
    $another_connection = $this->toolkit->createDbConnection(new lmbDbDSN($this->config['another_dsn']));

    $this->assertEqual($connection, $this->toolkit->getDefaultDbConnection());

    $this->toolkit->setDbConnectionByName('dsn', $another_connection);

    $this->assertIdentical($another_connection, $this->toolkit->getDefaultDbConnection());
  }
}