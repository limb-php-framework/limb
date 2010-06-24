<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once(dirname(__FILE__) . '/../DriverTransactionTestBase.class.php');
require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbLinterDriverTransactionTest extends DriverTransactionTestBase
{
  function setUp()
  {
    $this->connection = lmbToolkit :: instance()->getDefaultDbConnection();
    DriverLinterSetup($this->connection->getConnectionId());
    parent::setUp();
  }
  
  function testCommitTransaction()
  {
    $this->assertEqual($this->_countRecords(), 0);

    $this->connection->beginTransaction();
    $stmt = $this->connection->newStatement('INSERT INTO founding_fathers ("first", "last") VALUES (\'George\', \'Washington\');');
    $stmt->execute();
    $this->connection->commitTransaction();

    $this->assertEqual($this->_countRecords(), 1);
  }

  function testRollbackTransaction()
  {
    $this->assertEqual($this->_countRecords(), 0);

    $this->connection->beginTransaction();
    $stmt = $this->connection->newStatement('INSERT INTO founding_fathers ("first", "last") VALUES (\'George\', \'Washington\')');
    $stmt->execute();
    $this->connection->rollbackTransaction();

    $this->assertEqual($this->_countRecords(), 0);
  }
  
}


