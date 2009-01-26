<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
require_once(dirname(__FILE__) . '/../DriverRecordSetTestBase.class.php');
require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbLinterRecordSetTest extends DriverRecordSetTestBase
{

  function lmbLinterRecordSetTest()
  {
    parent :: DriverRecordSetTestBase('lmbLinterRecord');
  }

  function setUp()
  {
    $this->connection = lmbToolkit :: instance()->getDefaultDbConnection();
    DriverLinterSetup($this->connection->getConnectionId());
    $sql = 'SELECT "id", "first" FROM founding_fathers ORDER BY "id"';
    $this->stmt = $this->connection->newStatement($sql);
    $this->cursor = $this->stmt->getRecordSet();
  }
  
  function testSort()
  {
    $sql = 'SELECT "id", "first" FROM founding_fathers';
    $rs = $this->connection->newStatement($sql)->getRecordSet();
    $rs->sort(array('id' => 'DESC'));

    $rs->rewind();
    $this->assertEqual($rs->current()->get('first'), 'Benjamin');
    $rs->next();
    $this->assertEqual($rs->current()->get('first'), 'Alexander');
    $rs->next();
    $this->assertEqual($rs->current()->get('first'), 'George');
  }

  function testSortPaginated()
  {
    $sql = 'SELECT "id", "first" FROM founding_fathers';
    $rs = $this->connection->newStatement($sql)->getRecordSet();
    $rs->sort(array('id' => 'DESC'));
    $rs->paginate(0, 1);

    $rs->rewind();
    $this->assertEqual($rs->current()->get('first'), 'Benjamin');
    $rs->next();
    $this->assertFalse($rs->valid());
  }

  function testSortPreservesExistingOrderBy()
  {
    $sql = 'SELECT "id", "first" FROM founding_fathers ORdeR By "first"';
    $rs = $this->connection->newStatement($sql)->getRecordSet();
    $rs->sort(array('id' => 'DESC'));

    $rs->rewind();
    $this->assertEqual($rs->current()->get('first'), 'Alexander');
    $rs->next();
    $this->assertEqual($rs->current()->get('first'), 'Benjamin');
    $rs->next();
    $this->assertEqual($rs->current()->get('first'), 'George');
  }


  function testsAtAfterPagination()
  {
    $sql = 'SELECT "id", "first" FROM founding_fathers';
    $rs = $this->connection->newStatement($sql)->getRecordSet();
    $rs->paginate(1, 1);

    $this->assertEqual($rs->at(0)->get('first'), 'George');
  }

  function testsAtAfterSort()
  {
    $sql = 'SELECT "id", "first" FROM founding_fathers';
    $rs = $this->connection->newStatement($sql)->getRecordSet();
    $rs->sort(array('id' => 'DESC'));

    $this->assertEqual($rs->at(0)->get('first'), 'Benjamin');
    $this->assertEqual($rs->at(1)->get('first'), 'Alexander');
    $this->assertEqual($rs->at(2)->get('first'), 'George');
  }
  
}


