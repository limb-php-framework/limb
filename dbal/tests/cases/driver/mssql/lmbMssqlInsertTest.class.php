<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once(dirname(__FILE__) . '/../DriverInsertTestBase.class.php');
require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbMssqlInsertTest extends DriverInsertTestBase
{

  function lmbMssqlInsertTest()
  {
    parent :: DriverInsertTestBase('lmbMssqlInsertStatement');
  }

  function setUp()
  {
    $this->connection = lmbToolkit :: instance()->getDefaultDbConnection();
    DriverMssqlSetup($this->connection->getConnectionId());
    DriverMssqlExec($this->connection->getConnectionId(), "SET IDENTITY_INSERT founding_fathers OFF");
    parent::setUp();
  }
  
  function testInsert()
  {
    $sql = "
          INSERT INTO founding_fathers (
              first, last
          ) VALUES (
              :first:, :last:
          )";
    $stmt = $this->connection->newStatement($sql);
    $stmt->setVarChar('first', 'Richard');
    $stmt->setVarChar('last', 'Nixon');
    $stmt->execute();
    $this->assertEqual($stmt->getAffectedRowCount(), 1);
    $this->checkRecord(5);
  }
  
  function testInsertId()
  {
    $sql = "
        INSERT INTO founding_fathers (
            first, last
        ) VALUES (
            :first:, :last:
        )";
    $stmt = $this->connection->newStatement($sql);
    $this->assertIsA($stmt, $this->insert_stmt_class);

    $stmt->setVarChar('first', 'Richard');
    $stmt->setVarChar('last', 'Nixon');

    $id = $stmt->insertId('id');
    $this->assertEqual($stmt->getAffectedRowCount(), 1);
    $this->assertIdentical($id, 5);
    $this->checkRecord(5);
  }
}


