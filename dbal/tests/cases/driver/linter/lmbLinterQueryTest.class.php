<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once(dirname(__FILE__) . '/../DriverQueryTestBase.class.php');
require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbLinterQueryTest extends DriverQueryTestBase
{

  function lmbLinterQueryTest()
  {
    parent :: DriverQueryTestBase('lmbLinterRecord');
  }

  function setUp()
  {
    $this->connection = lmbToolkit :: instance()->getDefaultDbConnection();
    DriverLinterSetup($this->connection->getConnectionId());
    parent::setUp();
  }


  function testGetOneRecord()
  {
    $sql = 'SELECT * FROM founding_fathers WHERE "id" = 1';
    $stmt = $this->connection->newStatement($sql);
    $record = $stmt->getOneRecord();
    $this->assertIsA($record, $this->record_class);
    $this->assertEqual($record->get('id'), 1);
    $this->assertEqual($record->get('first'), 'George');
    $this->assertEqual($record->get('last'), 'Washington');
  }

  function testGetOneValue()
  {
    $sql = 'SELECT "first" FROM founding_fathers';
    $stmt = $this->connection->newStatement($sql);
    $this->assertEqual($stmt->getOneValue(), 'George');
  }

  function testGetOneColumnArray()
  {
    $sql = 'SELECT "first" FROM founding_fathers';
    $stmt = $this->connection->newStatement($sql);
    $testarray = array('George', 'Alexander', 'Benjamin');
    $this->assertEqual($stmt->getOneColumnAsArray($sql), $testarray);
  }
}
