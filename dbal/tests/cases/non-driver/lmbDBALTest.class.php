<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/dbal/src/lmbDBAL.class.php');
lmb_require('limb/dbal/src/drivers/lmbDbConnection.interface.php');
lmb_require('limb/dbal/src/drivers/lmbDbQueryStatement.interface.php');

Mock::generate('lmbDbConnection', 'MockDbConnection');
Mock::generate('lmbDbQueryStatement', 'MockDbQueryStatement');

class lmbDBALTest extends UnitTestCase
{
  protected $toolkit;
  protected $dsn;
  protected $conn;

  function setUp()
  {
    $this->toolkit = lmbToolkit :: save();
    $this->dsn = $this->toolkit->getDefaultDbDSN();
    $this->conn = new MockDbConnection();
  }

  function tearDown()
  {
    lmbToolkit :: restore();
  }

  function testSetDefaultDSN()
  {
    lmbDBAL :: setDefaultDSN($boo = new lmbDbDSN('mysql://localhost/db_name'));
    $this->assertEqual($this->toolkit->getDefaultDbDSN(), $boo);
  }

  function testDefaultConnection()
  {
    $this->assertIdentical($this->toolkit->getDefaultDbConnection(),
                           lmbDBAL :: defaultConnection());
  }

  function testNewConnection()
  {
    $conn = lmbDBAL :: newConnection($this->dsn);
    $this->assertIsA($conn, 'lmbDbConnection');
  }

  function testNewStatement()
  {
    $this->toolkit->setDefaultDbConnection($this->conn);
    $this->conn->expectOnce('newStatement', array($sql = 'SELECT 1=1'));
    $this->conn->setReturnValue('newStatement', 'whatever', array($sql));
    $this->assertEqual(lmbDBAL :: newStatement($sql), 'whatever');
  }

  function testExecute()
  {
    $this->conn->expectOnce('execute', array($sql = 'SELECT 1=1'));
    lmbDBAL :: execute($sql, $this->conn);
  }

  function testExecuteUsingDefaultConnection()
  {
    $this->toolkit->setDefaultDbConnection($this->conn);
    $this->conn->expectOnce('execute', array($sql = 'SELECT 1=1'));
    lmbDBAL :: execute('SELECT 1=1');
  }

  function testFetch()
  {
    $stmt = new MockDbQueryStatement();
    $this->conn->expectOnce('newStatement', array($sql = 'SELECT 1=1'));
    $this->conn->setReturnValue('newStatement', $stmt, array($sql));
    $stmt->expectOnce('getRecordSet');
    $stmt->setReturnValue('getRecordSet', 'result');

    $rs = lmbDBAL :: fetch($sql, $this->conn);
    $this->assertEqual($rs, 'result');
  }

  function testFetchUsingDefaultConnection()
  {
    $this->toolkit->setDefaultDbConnection($this->conn);
    $stmt = new MockDbQueryStatement();
    $this->conn->expectOnce('newStatement', array($sql = 'SELECT 1=1'));
    $this->conn->setReturnValue('newStatement', $stmt, array($sql));
    $stmt->expectOnce('getRecordSet');
    $stmt->setReturnValue('getRecordSet', 'result');

    $rs = lmbDBAL :: fetch($sql);
    $this->assertEqual($rs, 'result');
  }

  function testFetchWithWrongSQL()
  {
    try
    {
      $rs = lmbDBAL :: fetch($sql = 'SLECT 1=1');
      $this->fail();
    }
    catch(lmbDbException $e)
    {
      $this->assertPattern('/The result of this SQL query can not be fetched./', $e->getMessage());
      $this->assertEqual($e->getParam('query'), $sql);
    }
  }

  function testDbMethod()
  {
    $db = lmbDBAL :: db($this->conn);
    $this->assertIsA($db, 'lmbSimpleDb');
    $this->assertIdentical($db->getConnection(), $this->conn);
  }

  function testDbMethodUsingDefaultConnection()
  {
    $db = lmbDBAL :: db();
    $this->assertIsA($db, 'lmbSimpleDb');
  }

  function testTableMethod()
  {
    $table = lmbDBAL :: table('test_db_table', $this->conn);
    $this->assertIsA($table, 'lmbTableGateway');
    $this->assertEqual($table->getTableName(), 'test_db_table');
    $this->assertIdentical($this->conn, $table->getConnection());
  }

  function testTableMethodUsingDefaultConnection()
  {
    $table = lmbDBAL :: table('test_db_table');
    $this->assertIsA($table, 'lmbTableGateway');
    $this->assertEqual($table->getTableName(), 'test_db_table');
  }

  function testSelectQueryUsingDefaultConnection()
  {
    $query = lmbDBAL :: selectQuery('test_db_table');
    $this->assertIsA($query, 'lmbSelectQuery');
    $this->assertEqual($query->getTables(), array('test_db_table'));
  }

  function testSelectQuery()
  {
    $query = lmbDBAL :: selectQuery('test_db_table', $this->conn);
    $this->assertIsA($query, 'lmbSelectQuery');
    $this->assertEqual($query->getTables(), array('test_db_table'));
    $this->assertIdentical($this->conn, $query->getConnection());
  }

  function testUpdateQuery()
  {
    $query = lmbDBAL :: updateQuery('test_db_table', $this->conn);
    $this->assertIsA($query, 'lmbUpdateQuery');
    $this->assertEqual($query->getTable(), 'test_db_table');
    $this->assertIdentical($this->conn, $query->getConnection());
  }

  function testUpdateQueryUsingDefaultConnection()
  {
    $query = lmbDBAL :: updateQuery('test_db_table');
    $this->assertIsA($query, 'lmbUpdateQuery');
    $this->assertEqual($query->getTable(), 'test_db_table');
  }

  function testDeleteQuery()
  {
    $query = lmbDBAL :: deleteQuery('test_db_table', $this->conn);
    $this->assertIsA($query, 'lmbDeleteQuery');
    $this->assertEqual($query->getTable(), 'test_db_table');
    $this->assertIdentical($this->conn, $query->getConnection());
  }

  function testDeleteQueryUsingDefaultConnection()
  {
    $query = lmbDBAL :: deleteQuery('test_db_table');
    $this->assertIsA($query, 'lmbDeleteQuery');
    $this->assertEqual($query->getTable(), 'test_db_table');
  }
}

