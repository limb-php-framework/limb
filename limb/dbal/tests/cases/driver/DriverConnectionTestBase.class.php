<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

abstract class DriverConnectionTestBase extends UnitTestCase
{
  var $query_stmt_class;
  var $insert_stmt_class;
  var $manip_stmt_class;
  var $default_stmt_class;
  var $connection;

  function DriverConnectionTestBase($query_stmt_class, $insert_stmt_class, $manip_stmt_class, $default_stmt_class)
  {
    $this->query_stmt_class = $query_stmt_class;
    $this->insert_stmt_class = $insert_stmt_class;
    $this->manip_stmt_class = $manip_stmt_class;
    $this->default_stmt_class = $default_stmt_class;
  }

  function tearDown()
  {
    $this->connection->disconnect();
    unset($this->connection);
  }

  function getSocket() {
    $this->skipIf(true, 'Socket guessing is not implemented for this connection');
  }

  function testSocketConnection() {
    lmb_require('core/src/lmbSys.class.php');
    $this->skipIf(lmbSys::isWin32(), "Windows platform doesn't support sockets.");

    $config = $this->connection->getConfig()->export();
    $config['socket'] = $this->getSocket();
    $connection_class = get_class($this->connection);
    try {
      $connection = new $connection_class($config);
      $connection->connect();
    } catch (Exception $e) {
      $this->fail("Connection through socket $config[socket] failed.");
    }

    if (isset($connection)) {
      $connection->disconnect();
      unset($connection);
    }
  }

  function testNewStatementSelect()
  {
    $stmt = $this->connection->newStatement('SELECT ');
    $this->assertIsA($stmt, $this->query_stmt_class);
    $stmt = $this->connection->newStatement('SeLeCt');
    $this->assertIsA($stmt, $this->query_stmt_class);
    $stmt = $this->connection->newStatement(' select');
    $this->assertIsA($stmt, $this->query_stmt_class);
    $stmt = $this->connection->newStatement("\nSELECT");
    $this->assertIsA($stmt, $this->query_stmt_class);
  }

  function testNewStatementInsert()
  {
    $stmt = $this->connection->newStatement('INSERT ');
    $this->assertIsA($stmt, $this->insert_stmt_class);
  }

  function testNewStatementUpdate()
  {
    $stmt = $this->connection->newStatement('UPDATE ');
    $this->assertIsA($stmt, $this->manip_stmt_class);
  }

  function testNewStatementDelete()
  {
    $stmt = $this->connection->newStatement('DELETE ');
    $this->assertIsA($stmt, $this->manip_stmt_class);
  }

  function testNewStatementSet()
  {
    $stmt = $this->connection->newStatement('SET ');
    $this->assertIsA($stmt, $this->default_stmt_class);
  }

  function testGetTypeInfo()
  {
    $ti = $this->connection->getTypeInfo();
    $this->assertIsA($ti, 'lmbDbTypeInfo');
  }

  function testGetDatabaseInfo()
  {
    $di = $this->connection->getDatabaseInfo();
    $this->assertIsA($di, 'lmbDbInfo');
  }
}


