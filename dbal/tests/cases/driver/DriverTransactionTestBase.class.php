<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once(dirname(__FILE__) . '/DriverManipTestBase.class.php');

abstract class DriverTransactionTestBase extends DriverManipTestBase
{

  function setUp()
  {
    parent :: setUp();
    $stmt = $this->connection->newStatement('DELETE FROM founding_fathers');
    $stmt->execute();
  }

  function testCommitTransaction()
  {
    $this->assertEqual($this->_countRecords(), 0);

    $this->connection->beginTransaction();
    $stmt = $this->connection->newStatement("INSERT INTO founding_fathers VALUES (1, 'George', 'Washington')");
    $stmt->execute();
    $this->connection->commitTransaction();

    $this->assertEqual($this->_countRecords(), 1);
  }

  function testRollbackTransaction()
  {
    $this->assertEqual($this->_countRecords(), 0);

    $this->connection->beginTransaction();
    $stmt = $this->connection->newStatement("INSERT INTO founding_fathers VALUES (1, 'George', 'Washington')");
    $stmt->execute();
    $this->connection->rollbackTransaction();

    $this->assertEqual($this->_countRecords(), 0);
  }

  function _countRecords()
  {
    $stmt = $this->connection->newStatement('SELECT COUNT(*) as cnt FROM founding_fathers');
    return $stmt->getOneValue();
  }
}


