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
  protected $query_stmt_class;
  protected $insert_stmt_class;
  protected $manip_stmt_class;
  protected $default_stmt_class;
  protected $connection;

  function tearDown()
  {
    $this->connection->disconnect();
    unset($this->connection);
  }

  function getSocket() {
    $this->skipIf(true, 'Socket guessing is not implemented for this connection');
  }

  function testSocketConnection()
  {
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

  function testConnectionWithoutDbSelect()
  {
    $type = $this->connection->getType();
    $should_skip = !in_array($type, array('mysql', 'mysqli'));
    if ($should_skip)
    {
      $this->skipIf($should_skip, "testConnectionWithoutDbSelect not implemented for this driver");
      return;
    }

    $toolkit = lmbToolkit :: instance();
    $new_dsn = clone($toolkit->getDefaultDbDSN());
    $new_dsn->database = false;

    try {
      $conn = $toolkit->createDbConnection($new_dsn);
      $conn->execute('SELECT 1');
    } catch (Exception $e) {
      $this->fail("Connection without DB select failed.");
    }

    if (isset($conn))
    {
      $conn->disconnect();
      unset($conn);
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


