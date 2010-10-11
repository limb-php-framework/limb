<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

require_once(dirname(__FILE__) . '/../DriverDatabaseInfoTestBase.class.php');
require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbPgsqlDbInfoTest extends DriverDatabaseInfoTestBase
{
  function setUp()
  {
    $this->toolkit = lmbToolkit :: save();
    $this->connection = lmbToolkit :: instance()->getDefaultDbConnection();
    DriverPgsqlSetup($this->connection->getConnectionId());
    parent::setUp();
  }

  function tearDown()
  {
    lmbToolkit :: restore();
    parent :: tearDown();
  }

  function testDefaultSchemaNameForUser()
  {
    $user = 'foo';
    $password = 'test';

    $default_schema = $user;
    $another_schema = 'bar';

    $this->_dropSchema($default_schema);
    $this->_dropSchema($another_schema);

    $this->_dropUser($user);

    $this->_createSchema($default_schema);
    $this->_createSchema($another_schema);

    $this->_createUser($user, $password);

    $sql = 'CREATE TABLE %schema_name%.test_default_schema_name (
      "id" SERIAL,
      "value" integer,
      PRIMARY KEY (id)
    )';

    $this->_executeForSchema($sql, $default_schema);
    $this->_executeForSchema($sql, $another_schema);

    $this->_changeTableOwner($default_schema.'.test_default_schema_name', $user);
    $this->_changeTableOwner($another_schema.'.test_default_schema_name', $user);

    $sql = "INSERT INTO %schema_name%.test_default_schema_name(value) VALUES(1)";
    $this->_executeForSchema($sql, $default_schema);

    $dsn = $this->_getDsnForUser($user, $password);
    $new_connection = $this->toolkit->createDbConnection($dsn);

    lmbToolkit::instance()->setDefaultDbConnection($new_connection);

    $sql = "SELECT COUNT(*) as count from test_default_schema_name";

    $this->assertEqual(lmbDBAL::fetchOneValue($sql), 1);

    $dbinfo = $new_connection->getDatabaseInfo();
    $this->assertTrue($dbinfo->hasTable('test_default_schema_name'));
  }

  protected function _createUser($user, $password)
  {
    $sql = "CREATE USER {$user} UNENCRYPTED PASSWORD '{$password}'";
    $this->_execute($sql);

    $sql = "ALTER USER {$user} SET search_path = '{$user}'";
    $this->_execute($sql);

    $sql = "GRANT USAGE ON SCHEMA {$user} TO {$user}";
    $this->_execute($sql);
  }

  protected function _createSchema($name)
  {
    $sql = "CREATE SCHEMA {$name}";
    $this->_execute($sql);
  }

  protected function _changeTableOwner($table, $owner)
  {
    $sql = "ALTER TABLE {$table} OWNER TO {$owner}";
    $this->_execute($sql);
  }


  protected function _executeForSchema($sql, $name)
  {
    $sql = str_replace('%schema_name%', $name, $sql);
    $this->_execute($sql);
  }

  protected function _getDsnForUser($user, $password)
  {
    $this->toolkit = lmbToolkit::instance();
    $dsn_uri = clone $this->toolkit->getDefaultDbDSN()->_getUri();
    $dsn_uri->setUser($user);
    $dsn_uri->setPassword($password);

    return new lmbDbDSN($dsn_uri->toString());
  }

  protected function _dropSchema($schema)
  {
    $sql = "DROP SCHEMA {$schema} CASCADE";
    $this->_execute($sql);
  }

  protected function _dropUser($user)
  {
    $sql = "DROP USER {$user}";
    $this->_execute($sql);
  }

  protected function _execute($sql, $conn = null)
  {
    if(!$conn)
      $conn = $this->connection;

    DriverPgsqlExec($conn->getConnectionId(), $sql);
  }
}


