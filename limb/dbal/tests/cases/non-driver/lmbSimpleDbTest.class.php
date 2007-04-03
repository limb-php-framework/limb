<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSimpleDbTest.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */
lmb_require('limb/dbal/src/criteria/lmbSQLFieldCriteria.class.php');
lmb_require('limb/dbal/src/lmbSimpleDb.class.php');

class lmbSimpleDbTest extends UnitTestCase
{
  var $db = null;
  var $conn = null;

  function setUp()
  {
    $toolkit = lmbToolkit :: instance();
    $this->conn = $toolkit->getDefaultDbConnection();
    $this->db = new lmbSimpleDb($this->conn);

    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $stmt = $this->conn->newStatement('DELETE FROM test_db_table');
    $stmt->execute();
  }

  function testGetType()
  {
    $this->assertEqual($this->db->getType(), $this->conn->getType());
    $this->assertNotNull($this->db->getType());
  }

  function testInsert()
  {
    $id = $this->db->insert('test_db_table', array('title' =>  'wow',
                                                   'description' => 'wow!'));

    $stmt = $this->conn->newStatement("SELECT * FROM test_db_table");
    $record = $stmt->getOneRecord();

    $this->assertEqual($record->get('title'), 'wow');
    $this->assertEqual($record->get('description'), 'wow!');
    $this->assertEqual($record->get('id'), $id);
  }

  //we test sequence based fields here
  function testInsertPrimaryKeyValue()
  {
    $id = $this->db->insert('test_db_table', array('id' => 20,
                                                   'title' =>  'wow',
                                                   'description' => 'wow!'));

    $stmt = $this->conn->newStatement("SELECT * FROM test_db_table");
    $record = $stmt->getOneRecord();

    $this->assertEqual($record->get('title'), 'wow');
    $this->assertEqual($record->get('description'), 'wow!');
    $this->assertEqual("$id", '20');
    $this->assertEqual($record->get('id'), $id);
  }

  function testUpdateAll()
  {
    $this->db->insert('test_db_table', array('title' =>  'wow', 'description' => 'description'));
    $this->db->insert('test_db_table', array('title' =>  'wow', 'description' => 'description2'));

    $this->assertEqual($this->db->update('test_db_table', array('description' =>  'new_description')), 2);

    $stmt = $this->conn->newStatement("SELECT * FROM test_db_table");
    $records = $stmt->getRecordSet();

    $records->rewind();
    $record = $records->current();
    $this->assertEqual($record->get('description'), 'new_description');

    $records->next();
    $record = $records->current();
    $this->assertEqual($record->get('description'), 'new_description');
  }

  function testUpdateByCondition()
  {
    $this->db->insert('test_db_table', array('title' =>  'wow', 'description' => 'description'));
    $this->db->insert('test_db_table', array('title' =>  'wow', 'description' => 'description2'));
    $this->db->insert('test_db_table', array('title' =>  'yo', 'description' => 'description3'));

    $res = $this->db->update('test_db_table',
                              array('description' =>  'new_description', 'title' => 'wow2'),
                              new lmbSQLFieldCriteria('title', 'wow'));

    $this->assertEqual($res, 2);

    $stmt = $this->conn->newStatement("SELECT * FROM test_db_table ORDER BY id");
    $records = $stmt->getRecordSet();

    $records->rewind();
    $record = $records->current();
    $this->assertEqual($record->get('description'), 'new_description');
    $this->assertEqual($record->get('title'), 'wow2');

    $records->next();
    $record = $records->current();
    $this->assertEqual($record->get('description'), 'new_description');
    $this->assertEqual($record->get('title'), 'wow2');
  }

  function testSelectAll()
  {
    $data = array(
      0 => array('title' =>  'wow', 'description' => 'description'),
      1 => array('title' =>  'wow', 'description' => 'description2')
    );

    $this->db->insert('test_db_table', $data[0]);
    $this->db->insert('test_db_table', $data[1]);

    $result = $this->db->select('test_db_table');

    $this->assertEqual($result->count(), 2);

    $result->rewind();
    $record = $result->current();
    $this->assertEqual($record->get('description'), 'description');

    $result->next();
    $record = $result->current();
    $this->assertEqual($record->get('description'), 'description2');
  }

  function testDeleteAll()
  {
    $data = array(
      0 => array('title' =>  'wow', 'description' => 'description'),
      1 => array('title' =>  'wow!', 'description' => 'description2')
    );

    $this->db->insert('test_db_table', $data[0]);
    $this->db->insert('test_db_table', $data[1]);

    $this->assertEqual($this->db->delete('test_db_table'), 2);

    $stmt = $this->conn->newStatement("SELECT * FROM test_db_table");
    $records = $stmt->getRecordSet();

    $this->assertEqual($records->count(), 0);
  }

  function testDeleteByStringCondition()
  {
    $data = array(
      0 => array('title' =>  'wow', 'description' => 'description'),
      1 => array('title' =>  'wow!', 'description' => 'description2')
    );

    $this->db->insert('test_db_table', $data[0]);
    $this->db->insert('test_db_table', $data[1]);

    $this->assertEqual($this->db->delete('test_db_table',
                                         new lmbSQLFieldCriteria('description', 'description')), 1);

    $stmt = $this->conn->newStatement("SELECT * FROM test_db_table");
    $records = $stmt->getRecordSet();

    $this->assertEqual($records->count(), 1);
  }

}
?>
