<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbSqliteExtensionTest extends UnitTestCase
{
  function setUp()
  {
    $this->connection = lmbToolkit :: instance()->getDefaultDbConnection();
    DriverSqliteSetup($this->connection->getConnectionId());
  }

  function testConcat()
  {
    $stmt = $this->connection->newStatement("SELECT " . $this->connection->getExtension()->concat(array('"1"', '"2"', '"foo"')) . " AS c ");
    $record = $stmt->getOneRecord();
    $this->assertEqual($record->get('c'), "12foo");
  }

  function testSubstring()
  {
    $stmt = $this->connection->newStatement("SELECT " . $this->connection->getExtension()->substr('"fco"', 2, 1) . " AS c ");
    $record = $stmt->getOneRecord();
    $this->assertEqual($record->get('c'), "c");
  }

  function testSubstringWithoutLimit()
  {
    $stmt = $this->connection->newStatement("SELECT " . $this->connection->getExtension()->substr('"fco"', 2) . " AS c ");
    $record = $stmt->getOneRecord();
    $this->assertEqual($record->get('c'), "co");
  }
}


