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

class lmbLinterInsertTest extends DriverInsertTestBase
{

  function lmbLinterInsertTest()
  {
    parent :: DriverInsertTestBase('lmbLinterInsertStatement');
  }

  function setUp()
  {
    $this->connection = lmbToolkit :: instance()->getDefaultDbConnection();
    DriverLinterSetup($this->connection->getConnectionId());
    parent::setUp();
  }

  function testInsertIdShouldUseSequence()
  {
    $sql = '
        INSERT INTO founding_fathers (
            "first", "last"
        ) VALUES (
            :first:, :last:
        )';
    $stmt = $this->connection->newStatement($sql);
    $stmt->setVarChar('first', 'Richard');
    $stmt->setVarChar('last', 'Nixon');

    $id = $stmt->insertId('id');
    $this->assertTrue($id > 0);

    $this->connection->newStatement("DELETE FROM founding_fathers")->execute();

    $new_id = $stmt->insertId('id');
    $this->assertEqual($new_id - $id, 1);
  }
  
  function testInsert()
  {
    $sql = '
          INSERT INTO founding_fathers (
              "first", "last"
          ) VALUES (
              :first:, :last:
          )';
    $stmt = $this->connection->newStatement($sql);
    $stmt->setVarChar('first', 'Richard');
    $stmt->setVarChar('last', 'Nixon');
    $stmt->execute();
    $this->assertEqual($stmt->getAffectedRowCount(), 1);
    $this->checkRecord(4);
  }

  function testInsertId()
  {
    $sql = '
        INSERT INTO founding_fathers (
            "first", "last"
        ) VALUES (
            :first:, :last:
        )';
    $stmt = $this->connection->newStatement($sql);
    $this->assertIsA($stmt, $this->insert_stmt_class);

    $stmt->setVarChar('first', 'Richard');
    $stmt->setVarChar('last', 'Nixon');

    $id = $stmt->insertId('id');
    $this->assertEqual($stmt->getAffectedRowCount(), 1);
    $this->assertIdentical($id, 4);
    $this->checkRecord(4);
  }
  
  function checkRecord($id)
  {
    $sql = 'SELECT * FROM founding_fathers WHERE "id" = :id:';
    $stmt = $this->connection->newStatement($sql);
    $stmt->setInteger('id', $id);
    $record = $stmt->getOneRecord();
    $this->assertNotNull($record);
    if($record)
    {
      $this->assertEqual($record->get('id'), $id);
      $this->assertEqual($record->get('first'), 'Richard');
      $this->assertEqual($record->get('last'), 'Nixon');
    }
  }
  
  
}


