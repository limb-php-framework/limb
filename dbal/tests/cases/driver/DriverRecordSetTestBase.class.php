<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

abstract class DriverRecordSetTestBase extends UnitTestCase
{
  var $record_class;

  function DriverRecordSetTestBase($record_class)
  {
    $this->record_class = $record_class;
  }

  function setUp()
  {
    $sql = "SELECT id, first FROM founding_fathers ORDER BY id";
    $this->stmt = $this->connection->newStatement($sql);
    $this->cursor = $this->stmt->getRecordSet();
  }

  function tearDown()
  {
    $this->connection->disconnect();
  }

  function testRewind()
  {
    $this->cursor->rewind();
    $this->assertTrue($this->cursor->valid());
    $record = $this->cursor->current();
    $this->assertIsA($record, $this->record_class);
    $this->assertEqual($record->get('id'), 1);
    $this->assertEqual($record->get('first'), 'George');
    $this->cursor->next();
    $this->cursor->next();
    $this->cursor->rewind();
    $record = $this->cursor->current();
    $this->assertIsA($record, $this->record_class);
    $this->assertEqual($record->get('id'), 1);
    $this->assertEqual($record->get('first'), 'George');
  }

  function testIteration()
  {
    for($this->cursor->rewind(), $i = 0; $this->cursor->valid(); $this->cursor->next(), $i++)
    {
      $record = $this->cursor->current();
      $this->assertIsA($record, $this->record_class);
    }
    $this->assertEqual($i, 3);
  }

  function testIteratorInterface()
  {
    $i = 0;
    foreach($this->cursor as $record)
    {
      $this->assertIsA($record, $this->record_class);
      $i++;
    }
    $this->assertEqual($i, 3);
  }

  function testPagerIteration()
  {
    $this->cursor->paginate($offset = 0, $limit = 2);
    for($this->cursor->rewind(), $i = 0; $this->cursor->valid(); $this->cursor->next(), $i++);
    $this->assertEqual($i, 2);
  }

  function testPaganationAfterIterating()
  {
    for($this->cursor->rewind(), $i = 0; $this->cursor->valid(); $this->cursor->next(), $i++);
    $this->assertEqual($i, 3);
    $this->cursor->paginate($offset = 0, $limit = 2);
    for($this->cursor->rewind(), $i = 0; $this->cursor->valid(); $this->cursor->next(), $i++);
    $this->assertEqual($i, 2);
  }

  function testPagerIterationPassingStringInsteadOfNumber()
  {
    $this->cursor->paginate($offset = ';Select * FROM some_table', $limit = 2);
    for($this->cursor->rewind(), $i = 0; $this->cursor->valid(); $this->cursor->next(), $i++);
    $this->assertEqual($i, 2);
  }

  function testCount()
  {
    $sql = "SELECT * FROM founding_fathers";
    $rs = $this->connection->newStatement($sql)->getRecordSet();
    $rs->paginate(0, 2);

    $this->assertEqual($rs->count(), 3);
    $this->assertEqual($rs->countPaginated(), 2);
    //double test driver internal state
    $this->assertEqual($rs->count(), 3);
    $this->assertEqual($rs->countPaginated(), 2);
  }

  function testSort()
  {
    $sql = "SELECT id, first FROM founding_fathers";
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
    $sql = "SELECT id, first FROM founding_fathers";
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
    $sql = "SELECT id, first FROM founding_fathers ORdeR By first";
    $rs = $this->connection->newStatement($sql)->getRecordSet();
    $rs->sort(array('id' => 'DESC'));

    $rs->rewind();
    $this->assertEqual($rs->current()->get('first'), 'Alexander');
    $rs->next();
    $this->assertEqual($rs->current()->get('first'), 'Benjamin');
    $rs->next();
    $this->assertEqual($rs->current()->get('first'), 'George');
  }

  function testAt()
  {
    $this->assertEqual($this->cursor->at(1)->get('first'), 'Alexander');
    $this->assertEqual($this->cursor->at(0)->get('first'), 'George');
    $this->assertNull($this->cursor->at(100));
  }

  function testsAtAfterPagination()
  {
    $sql = "SELECT id, first FROM founding_fathers";
    $rs = $this->connection->newStatement($sql)->getRecordSet();
    $rs->paginate(1, 1);

    $this->assertEqual($rs->at(0)->get('first'), 'George');
  }

  function testsAtAfterSort()
  {
    $sql = "SELECT id, first FROM founding_fathers";
    $rs = $this->connection->newStatement($sql)->getRecordSet();
    $rs->sort(array('id' => 'DESC'));

    $this->assertEqual($rs->at(0)->get('first'), 'Benjamin');
    $this->assertEqual($rs->at(1)->get('first'), 'Alexander');
    $this->assertEqual($rs->at(2)->get('first'), 'George');
  }

  function testGetFlatArray()
  {
    $sql = "SELECT first FROM founding_fathers";
    $rs = $this->connection->newStatement($sql)->getRecordSet();

    $flat_array = array(
      array('first' => 'George'),
      array('first' => 'Alexander'),
      array('first' => 'Benjamin'),
    );

    $this->assertIdentical($flat_array, $rs->getFlatArray());
  }
}


