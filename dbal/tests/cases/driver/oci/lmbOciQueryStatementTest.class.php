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
require_once('limb/dbal/src/drivers/oci/lmbOciQueryStatement.class.php');

class lmbOciQueryStatementTest extends UnitTestCase
{
  function setUp()
  {
    $this->connection = lmbToolkit :: instance()->getDefaultDbConnection();
    DriverOciSetup($this->connection->getConnectionId());
  }

  function testPaginate()
  {
    $stmt = new lmbOciQueryStatement($this->connection, 'SELECT * FROM founding_fathers');
    $stmt->paginate(1, 1);
    $rs = $stmt->getRecordSet();

    $rs->rewind();
    $record = $rs->current();
    $this->assertEqual($record->get('first'), 'Alexander');

    $rs->next();
    $this->assertFalse($rs->valid());

    $this->assertEqual($rs->count(), 1);
  }

  function testPaginateBindedStatement()
  {
    $stmt = new lmbOciQueryStatement($this->connection, 'SELECT * FROM founding_fathers WHERE first=:first:');
    $stmt->set('first', 'Alexander');
    $stmt->paginate(0, 1);
    $rs = $stmt->getRecordSet();

    $rs->rewind();
    $record = $rs->current();
    $this->assertEqual($record->get('first'), 'Alexander');

    $rs->next();
    $this->assertFalse($rs->valid());

    $this->assertEqual($rs->count(), 1);
  }
}


