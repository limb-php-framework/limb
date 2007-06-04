<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    $package$
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
    lmbDBAL :: setDefaultDSN($boo = new lmbObject());
    $this->assertEqual($this->toolkit->getDefaultDbDSN(), $boo);
  }

  function testNewConnection()
  {
    $conn = lmbDBAL :: newConnection($this->dsn);
    $this->assertIsA($conn, 'lmbDbConnection');
  }

  function testExecute()
  {
    $this->conn->expectOnce('execute', array($sql = 'SELECT 1=1'));
    lmbDBAL :: execute($sql, $this->conn);
  }

  function testExecuteDefaultConnection()
  {
    $this->toolkit->setDefaultDbConnection($this->conn);
    $this->conn->expectOnce('execute', array($sql = 'SELECT 1=1'));
    lmbDBAL :: execute('SELECT 1=1');
  }

  function testQuery()
  {
    $stmt = new MockDbQueryStatement();
    $this->conn->expectOnce('newStatement', array($sql = 'SELECT 1=1'));
    $this->conn->setReturnValue('newStatement', $stmt, array($sql));
    $stmt->expectOnce('getRecordSet');
    $stmt->setReturnValue('getRecordSet', 'result');

    $rs = lmbDBAL :: query($sql, $this->conn);
    $this->assertEqual($rs, 'result');
  }

  function testQueryDefaultConnection()
  {
    $this->toolkit->setDefaultDbConnection($this->conn);
    $stmt = new MockDbQueryStatement();
    $this->conn->expectOnce('newStatement', array($sql = 'SELECT 1=1'));
    $this->conn->setReturnValue('newStatement', $stmt, array($sql));
    $stmt->expectOnce('getRecordSet');
    $stmt->setReturnValue('getRecordSet', 'result');

    $rs = lmbDBAL :: query($sql);
    $this->assertEqual($rs, 'result');
  }
}
?>
