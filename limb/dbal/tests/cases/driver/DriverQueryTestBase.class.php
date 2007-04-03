<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: DriverQueryTestBase.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */

abstract class DriverQueryTestBase extends UnitTestCase
{

  var $record_class;

  function DriverQueryTestBase($record_class)
  {
    $this->record_class = $record_class;
  }

  function testGetOneRecord()
  {
    $sql = "SELECT * FROM founding_fathers WHERE id = 1";
    $stmt = $this->connection->newStatement($sql);
    $record = $stmt->getOneRecord();
    $this->assertIsA($record, $this->record_class);
    $this->assertEqual($record->get('id'), 1);
    $this->assertEqual($record->get('first'), 'George');
    $this->assertEqual($record->get('last'), 'Washington');
  }

  function testGetOneValue()
  {
    $sql = "SELECT first FROM founding_fathers";
    $stmt = $this->connection->newStatement($sql);
    $this->assertEqual($stmt->getOneValue(), 'George');
  }

  function testGetOneColumnArray()
  {
    $sql = "SELECT first FROM founding_fathers";
    $stmt = $this->connection->newStatement($sql);
    $testarray = array('George', 'Alexander', 'Benjamin');
    $this->assertEqual($stmt->getOneColumnAsArray($sql), $testarray);
  }
}

?>
