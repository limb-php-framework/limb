<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once(dirname(__FILE__) . '/../DriverUpdateTestBase.class.php');
require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbLinterUpdateTest extends DriverUpdateTestBase
{

  function lmbLinterUpdateTest()
  {
    parent :: DriverUpdateTestBase('lmbLinterManipulationStatement');
  }

  function setUp()
  {
    $this->connection = lmbToolkit :: instance()->getDefaultDbConnection();
    DriverLinterSetup($this->connection->getConnectionId());
    parent::setUp();
  }
  
  function testUpdate()
  {
    $sql = '
          UPDATE founding_fathers SET
              "first" = :first:,
              "last" = :last:
          WHERE
              "id" = :id:';
    $stmt = $this->connection->newStatement($sql);
    $this->assertIsA($stmt, $this->manip_stmt_class);

    $stmt->setVarChar('first', 'Richard');
    $stmt->setVarChar('last', 'Nixon');
    $stmt->setInteger('id', 3);

    $stmt->execute();
    $this->assertEqual($stmt->getAffectedRowCount(), 1);

    $this->checkRecord(3);
  }

  function testAffectedRowCount()
  {
    $sql = '
          UPDATE founding_fathers SET
              "first" = :first:,
              "last" = :last:';
    $stmt = $this->connection->newStatement($sql);
    $this->assertIsA($stmt, $this->manip_stmt_class);

    $stmt->setVarChar('first', 'Richard');
    $stmt->setVarChar('last', 'Nixon');

    $stmt->execute();
    $this->assertEqual($stmt->getAffectedRowCount(), 3);
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


