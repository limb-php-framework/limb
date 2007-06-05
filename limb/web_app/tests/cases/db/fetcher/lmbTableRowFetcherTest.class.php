<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/web_app/src/fetcher/lmbTableRowFetcher.class.php');
lmb_require('limb/dbal/src/lmbTableGateway.class.php');
lmb_require('limb/dbal/src/lmbSimpleDb.class.php');
lmb_require('limb/dbal/tests/cases/lmbTestDbTable.class.php');

class lmbTableRowFetcherTest extends UnitTestCase
{
  protected $db;
  protected $toolkit;
  protected $request;

  function setUp()
  {
    $this->toolkit = lmbToolkit :: save();
    $this->request = $this->toolkit->getRequest();
    $this->db = new lmbSimpleDb($this->toolkit->getDefaultDbConnection());

    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->_cleanUp();

    lmbToolkit :: restore();
  }

  function _cleanUp()
  {
    $this->db->delete('test_db_table');
  }

  function testFetchWithDefaultFieldName()
  {
    list($id1, $id2) = $this->_insertTwoRecordsInTestDbTable();

    $fetcher = new lmbTableRowFetcher();
    $fetcher->setTableClass('lmbTestDbTable');
    $fetcher->setValue($id2);

    $this->_verifySecondRecordIsFetchedOnly($fetcher->fetch());
  }

  function testFetchWithDefinedFieldName()
  {
    list($id1, $id2) = $this->_insertTwoRecordsInTestDbTable();

    $fetcher = new lmbTableRowFetcher();
    $fetcher->setTableClass('lmbTestDbTable');
    $fetcher->setField('title');
    $fetcher->setValue('Some title');

    $this->_verifySecondRecordIsFetchedOnly($fetcher->fetch());
  }

  function testFetchWithTableNameInsteadOfTableClass()
  {
    list($id1, $id2) = $this->_insertTwoRecordsInTestDbTable();

    $fetcher = new lmbTableRowFetcher();
    $fetcher->setTableName('test_db_table');
    $fetcher->setValue($id2);

    $this->_verifySecondRecordIsFetchedOnly($fetcher->fetch());
  }

  protected function _verifySecondRecordIsFetchedOnly($record_set)
  {
    $record_set->rewind();
    $record = $record_set->current();
    $this->assertEqual($record->get('id'), 10);
    $this->assertEqual($record->get('title'), 'Some title');
    $this->assertEqual($record->get('description'), 'Some description');

    $record_set->next();
    $this->assertFalse($record_set->valid());
  }

  protected function _insertTwoRecordsInTestDbTable()
  {
    $this->db->insert('test_db_table', array('id' => $id1 =9,
                                             'title' => 'Some other title',
                                             'description' => 'Some other description'));

    $this->db->insert('test_db_table', array('id' => $id2 = 10,
                                             'title' => 'Some title',
                                             'description' => 'Some description'));
    return array($id1, $id2);
  }
}
?>
