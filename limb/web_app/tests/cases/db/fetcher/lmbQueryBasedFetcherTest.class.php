<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbQueryBasedFetcherTest.class.php 5421 2007-03-29 12:49:10Z serega $
 * @package    web_app
 */
lmb_require('limb/dbal/src/query/lmbSelectQuery.class.php');
lmb_require('limb/dbal/src/lmbSimpleDb.class.php');
lmb_require('limb/web_app/src/fetcher/lmbQueryBasedFetcher.class.php');

class QueryBasedRecordSetBuilderTestingQuery extends lmbSelectQuery
{
  function __construct($conn)
  {
    $sql = 'select * from test_db_table %order%';
    parent :: __construct($sql, $conn);
  }
}

class lmbQueryBasedFetcherTest extends UnitTestCase
{
  var $db;
  var $conn;

  function setUp()
  {
    $toolkit = lmbToolkit :: save();
    $this->conn = $toolkit->getDefaultDbConnection();
    $this->db = new lmbSimpleDb($this->conn);

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

  function testIterate()
  {
    $this->db->insert('test_db_table', array('title' => 'Some title',
                                             'description' => 'Some description'));

    $this->db->insert('test_db_table', array('title' => 'Some other title',
                                             'description' => 'Some other description'));

    $fetcher = new lmbQueryBasedFetcher();
    $fetcher->setQueryName('QueryBasedRecordSetBuilderTestingQuery');

    $record_set = $fetcher->fetch();
    $this->assertEqual($record_set->count(), 2);
    $this->assertEqual($record_set->countPaginated(), 2);

    $record_set->rewind();
    $record = $record_set->current();
    $this->assertEqual($record->get('title'), 'Some title');
    $this->assertEqual($record->get('description'), 'Some description');

    $record_set->next();
    $record = $record_set->current();
    $this->assertEqual($record->get('title'), 'Some other title');
    $this->assertEqual($record->get('description'), 'Some other description');

    $record_set->next();
    $this->assertFalse($record_set->valid());
  }

  function testSetOrder()
  {
    $this->db->insert('test_db_table', array('title' => 'A',
                                             'description' => 'Some description'));

    $this->db->insert('test_db_table', array('title' => 'C',
                                             'description' => 'Any description'));

    $this->db->insert('test_db_table', array('title' => 'C',
                                             'description' => 'Other description'));

    $this->db->insert('test_db_table', array('title' => 'B',
                                             'description' => 'Some other description'));

    $fetcher = new lmbQueryBasedFetcher('QueryBasedRecordSetBuilderTestingQuery');
    $order_line = 'title=ASC,description=DESC';
    $fetcher->setOrder($order_line);

    $record_set = $fetcher->fetch();

    $record_set->rewind();
    $record = $record_set->current();
    $this->assertEqual($record->get('title'), 'A');

    $record_set->next();
    $record = $record_set->current();
    $this->assertEqual($record->get('title'), 'B');

    $record_set->next();
    $record = $record_set->current();
    $this->assertEqual($record->get('title'), 'C');
    $this->assertEqual($record->get('description'), 'Other description');

    $record_set->next();
    $record = $record_set->current();
    $this->assertEqual($record->get('title'), 'C');
    $this->assertEqual($record->get('description'), 'Any description');
  }
}
?>
