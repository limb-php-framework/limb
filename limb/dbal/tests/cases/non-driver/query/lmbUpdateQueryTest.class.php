<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/dbal/src/criteria/lmbSQLFieldCriteria.class.php');
lmb_require('limb/dbal/src/lmbSimpleDb.class.php');
lmb_require('limb/dbal/src/query/lmbUpdateQuery.class.php');

class lmbUpdateQueryTest extends UnitTestCase
{
  var $query;
  var $db;

  function setUp()
  {
    $toolkit = lmbToolkit :: instance();
    $this->conn = $toolkit->getDefaultDbConnection();
    $this->db = new lmbSimpleDb($this->conn);

    $this->_dbCleanUp();
  }

  function tearDown()
  {
    $this->_dbCleanUp();
  }

  function _dbCleanUp()
  {
    $this->db->delete('test_db_table');
  }

  function testUpdate()
  {
    $this->db->insert('test_db_table', array('id' => 100));
    $this->db->insert('test_db_table', array('id' => 101));

    $query = new lmbUpdateQuery('test_db_table', $this->conn);
    $query->addField('description', $description = 'Some description');
    $query->addField('title', $title = 'Some title');

    $stmt = $query->getStatement($this->conn);
    $stmt->execute();

    $rs = $this->db->select('test_db_table');
    $rs->rewind();
    $record = $rs->current();
    $this->assertEqual($record->get('title'), $title);
    $this->assertEqual($record->get('description'), $description);

    $rs->next();
    $record = $rs->current();
    $this->assertEqual($record->get('title'), $title);
    $this->assertEqual($record->get('description'), $description);
  }

  function testUpdateAddFieldWithoutValueOnlyReservesAPlaceholder()
  {
    $this->db->insert('test_db_table', array('id' => 101));

    $query = new lmbUpdateQuery('test_db_table', $this->conn);
    $query->addField('description');
    $query->addField('title');

    $stmt = $query->getStatement($this->conn);
    $stmt->set('description', $description = 'Some \'description\'');
    $stmt->set('title', $title = 'Some title');
    $stmt->execute();

    $rs = $this->db->select('test_db_table');
    $rs->rewind();
    $record = $rs->current();
    $this->assertEqual($record->get('title'), $title);
    $this->assertEqual($record->get('description'), $description);
  }

  function testUpdateSpecialCase()
  {
    $this->db->insert('test_db_table', array('id' => 100));

    $query = new lmbUpdateQuery('test_db_table', $this->conn);
    $query->addRawField('id = id + 1');

    $stmt = $query->getStatement($this->conn);
    $stmt->execute();

    $rs = $this->db->select('test_db_table');
    $rs->rewind();
    $record = $rs->current();
    $this->assertEqual($record->get('id'), 101);
  }

  function testUpdateWithCriteria()
  {
    $this->db->insert('test_db_table', array('id' => 100));
    $this->db->insert('test_db_table', array('id' => 101));

    $query = new lmbUpdateQuery('test_db_table', $this->conn);
    $query->addField('description', $description = 'Some description');
    $query->addField('title', $title = 'Some title');
    $query->addCriteria(new lmbSQLFieldCriteria('id', 101));

    $stmt = $query->getStatement($this->conn);
    $stmt->execute();

    $rs = $this->db->select('test_db_table');
    $rs->rewind();
    $record = $rs->current(); //this one is not changed
    $this->assertEqual($record->get('id'), 100);
    $this->assertEqual($record->get('title'), '');
    $this->assertEqual($record->get('description'), '');

    $rs->next();
    $record = $rs->current();
    $this->assertEqual($record->get('id'), 101);
    $this->assertEqual($record->get('title'), $title);
    $this->assertEqual($record->get('description'), $description);
  }

  function testChaining()
  {
    $this->db->insert('test_db_table', array('id' => 100));
    $this->db->insert('test_db_table', array('id' => 101));

    $description = 'Some description';
    $title = 'Some title';

    $query = new lmbUpdateQuery('test_db_table', $this->conn);
    $query->set(array('description' => $description))->
            field('title', $title)->
            rawField('id = id + 10')->
            where('id=101')->
            execute();

    $rs = $this->db->select('test_db_table');
    $rs->rewind();
    $record = $rs->current(); //this one is not changed
    $this->assertEqual($record->get('id'), 100);
    $this->assertEqual($record->get('title'), '');
    $this->assertEqual($record->get('description'), '');

    $rs->next();
    $record = $rs->current();
    $this->assertEqual($record->get('id'), 111);
    $this->assertEqual($record->get('title'), $title);
    $this->assertEqual($record->get('description'), $description);
  }
}
?>
