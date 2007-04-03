<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbOciQueryStatementTest.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
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

?>
