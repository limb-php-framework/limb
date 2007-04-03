<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: DriverConnectionTestBase.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
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

?>
