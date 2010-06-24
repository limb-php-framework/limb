<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
require_once(dirname(__FILE__) . '/../DriverInsertTestBase.class.php');
require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbOciLobTest extends UnitTestCase
{
  function setUp()
  {
    $this->connection = lmbToolkit :: instance()->getDefaultDbConnection();
    DriverOciSetup($this->connection->getConnectionId());
    parent::setUp();
  }

  function testInsertClob()
  {
    $value = file_get_contents(dirname(__FILE__) . '/../clob.txt');

    $sql = "
        INSERT INTO standard_types (
            type_clob,
            type_varchar
        ) VALUES (
            :a:,
            :b:
        )";
    $stmt = $this->connection->newStatement($sql);

    $stmt->setClob('a', $value);
    $stmt->set('b', 'junk');
    $stmt->execute();

    $stmt = $this->connection->newStatement("SELECT * FROM standard_types");
    $record = $stmt->getOneRecord();
    $this->assertEqual($record->get('type_clob'), $value);
    $this->assertEqual($record->get('type_varchar'), 'junk');
  }

  function testInsertBlob()
  {
    $value = file_get_contents(dirname(__FILE__) . '/../blob.jpg');

    $sql = "
        INSERT INTO standard_types (
            type_blob,
            type_varchar
        ) VALUES (
            :a:,
            :b:
        )";
    $stmt = $this->connection->newStatement($sql);

    $stmt->setBlob('a', $value);
    $stmt->set('b', 'junk');
    $stmt->execute();

    $stmt = $this->connection->newStatement("SELECT * FROM standard_types");
    $record = $stmt->getOneRecord();
    $this->assertEqual($record->get('type_blob'), $value);
    $this->assertEqual($record->get('type_varchar'), 'junk');
  }

  function testInsertBlobAndClob()
  {
    $blob = file_get_contents(dirname(__FILE__) . '/../blob.jpg');
    $clob = file_get_contents(dirname(__FILE__) . '/../clob.txt');

    $sql = "
        INSERT INTO standard_types (
            type_blob,
            type_varchar,
            type_clob
        ) VALUES (
            :a:,
            :b:,
            :c:
        )";
    $stmt = $this->connection->newStatement($sql);

    $stmt->setBlob('a', $blob);
    $stmt->set('b', 'junk');
    $stmt->setClob('c', $clob);
    $stmt->execute();

    $stmt = $this->connection->newStatement("SELECT * FROM standard_types");
    $record = $stmt->getOneRecord();
    $this->assertEqual($record->get('type_blob'), $blob);
    $this->assertEqual($record->get('type_varchar'), 'junk');
    $this->assertEqual($record->get('type_clob'), $clob);
  }

  function testUpdateClob()
  {
    $value = file_get_contents(dirname(__FILE__) . '/../clob.txt');

    $sql = "
        INSERT INTO standard_types (
            type_blob
        ) VALUES (
            :a:
        )";
    $stmt = $this->connection->newStatement($sql);

    $stmt->setBlob('a', $value);
    $stmt->execute();

    $sql = "
        UPDATE standard_types SET
            type_clob = :a:,
            type_varchar = :b:
        ";
    $stmt = $this->connection->newStatement($sql);

    $newvalue = substr($value, 0, strlen($value - 30));//checking for truncation

    $stmt->setClob('a', $newvalue);
    $stmt->set('b', 'junk');
    $stmt->execute();

    $stmt = $this->connection->newStatement("SELECT * FROM standard_types");
    $record = $stmt->getOneRecord();
    $this->assertEqual($record->get('type_clob'), $newvalue);
    $this->assertEqual($record->get('type_varchar'), 'junk');
  }

  function testUpdateBlob()
  {
    $value = file_get_contents(dirname(__FILE__) . '/../blob.jpg');

    $sql = "
        INSERT INTO standard_types (
            type_blob
        ) VALUES (
            :type_blob:
        )";
    $stmt = $this->connection->newStatement($sql);

    $stmt->setBlob('type_blob', $value);
    $stmt->execute();

    $sql = "
        UPDATE standard_types SET
            type_blob = :a:,
            type_varchar = :b:
        ";
    $stmt = $this->connection->newStatement($sql);

    $newvalue = substr($value, 0, strlen($value - 30));//checking for truncation

    $stmt->setBlob('a', $newvalue);
    $stmt->set('b', 'junk');
    $stmt->execute();

    $stmt = $this->connection->newStatement("SELECT * FROM standard_types");
    $record = $stmt->getOneRecord();
    $this->assertEqual($record->get('type_blob'), $newvalue);
    $this->assertEqual($record->get('type_varchar'), 'junk');
  }

  function testUpdateBlobAndClob()
  {
    $blob = file_get_contents(dirname(__FILE__) . '/../blob.jpg');
    $clob = file_get_contents(dirname(__FILE__) . '/../blob.jpg');

    $sql = "
        INSERT INTO standard_types (
            type_blob,
            type_clob
        ) VALUES (
            :type_blob:,
            :type_clob:
        )";
    $stmt = $this->connection->newStatement($sql);

    $stmt->setBlob('type_blob', $blob);
    $stmt->setClob('type_clob', $clob);
    $stmt->execute();

    $sql = "
        UPDATE standard_types SET
            type_blob = :a:,
            type_varchar = :b:,
            type_clob = :c:
        ";
    $stmt = $this->connection->newStatement($sql);

    $new_blob = substr($blob, 0, strlen($blob - 30));//checking for truncation
    $new_clob = substr($clob, 0, strlen($clob - 30));

    $stmt->setBlob('a', $new_blob);
    $stmt->set('b', 'junk');
    $stmt->setClob('c', $new_clob);
    $stmt->execute();

    $stmt = $this->connection->newStatement("SELECT * FROM standard_types");
    $record = $stmt->getOneRecord();
    $this->assertEqual($record->get('type_blob'), $new_blob);
    $this->assertEqual($record->get('type_varchar'), 'junk');
    $this->assertEqual($record->get('type_blob'), $new_clob);
  }
}


