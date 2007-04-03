<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: DriverTransactionTestBase.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
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

?>
