<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
require_once('limb/dbal/src/drivers/lmbDbTypeInfo.class.php');
lmb_require('limb/dbal/src/criteria/lmbSQLFieldCriteria.class.php');
lmb_require('limb/dbal/src/lmbTableGateway.class.php');

class lmbTableGatewayTest extends UnitTestCase
{
  var $conn = null;
  var $db_table_test = null;

  function setUp()
  {
    $toolkit = lmbToolkit :: save();
    $this->conn = $toolkit->getDefaultDbConnection();
    $this->db_table_test = new lmbTableGateway('test_db_table', $this->conn);

    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->_cleanUp();

    lmbToolkit :: restore();
  }

  function _cleanUp()
  {
    $stmt = $this->conn->newStatement('DELETE FROM test_db_table');
    $stmt->execute();
  }

  function testCorrectTableProperties()
  {
    $this->assertEqual($this->db_table_test->getTableName(), 'test_db_table');
    $this->assertEqual($this->db_table_test->getPrimaryKeyName(), 'id');
    $this->assertEqual($this->db_table_test->getColumnType('id'), lmbDbTypeInfo::TYPE_INTEGER);
    $this->assertIdentical($this->db_table_test->getColumnType('no_column'), false);
    $this->assertTrue($this->db_table_test->hasColumn('id'));
    $this->assertTrue($this->db_table_test->hasColumn('description'));
    $this->assertTrue($this->db_table_test->hasColumn('title'));
    $this->assertFalse($this->db_table_test->hasColumn('no_such_a_field'));
  }

  function testInsert()
  {
    $id = $this->db_table_test->insert(array('title' =>  'wow',
                                             'description' => 'wow!',
                                             'junk!!!' => 'junk!!!'));

    $stmt = $this->conn->newStatement("SELECT * FROM test_db_table");
    $record = $stmt->getOneRecord();

    $this->assertEqual($record->get('title'), 'wow');
    $this->assertEqual($record->get('description'), 'wow!');
    $this->assertEqual($record->get('id'), $id);
  }

  function testInsertOnDuplicateKeyUpdate()
  {
    $current_connection = lmbToolkit::instance()->getDefaultDbConnection();
    $is_supported = lmbInsertOnDuplicateUpdateQuery::isSupportedByDbConnection($current_connection);
    if(!$is_supported)
    {
      echo "Skip: ".$current_connection->getType()." not support insert on duplicate update queries \n";
      return;
    }

    $id = $this->db_table_test->insertOnDuplicateUpdate(array('title' =>  'wow',
                                             'description' => 'wow!',
                                             'junk!!!' => 'junk!!!'));

    $stmt = $this->conn->newStatement("SELECT * FROM test_db_table");
    $record = $stmt->getOneRecord();

    $this->assertEqual($record->get('title'), 'wow');
    $this->assertEqual($record->get('description'), 'wow!');
    $this->assertEqual($record->get('id'), $id);

    $id = $this->db_table_test->insertOnDuplicateUpdate(array('id' => $id,
                                             'title' =>  'wow',
                                             'description' => 'new wow!',
                                             'junk!!!' => 'junk!!!'));

    $stmt = $this->conn->newStatement("SELECT * FROM test_db_table");
    $record = $stmt->getOneRecord();

    $this->assertEqual($record->get('title'), 'wow');
    $this->assertEqual($record->get('description'), 'new wow!');
    $this->assertEqual($record->get('id'), $id);
  }

//  function testInsertUpdatesSequenceIfAutoIncrementFieldWasSet()
//  {
//    $id = $this->db_table_test->insert(array('id' => 4, 'title' =>  'wow', 'description' => 'wow!'));
//    $this->assertEqual($id, 4);
//  }

  function testInsertThrowsExceptionIfAllFieldsWereFiltered()
  {
    try
    {
      $this->db_table_test->insert(array('junk!!!' => 'junk!!!'));
      $this->assertTrue(false);
    }
    catch(lmbException $e){}
  }

  function testUpdateAll()
  {
    $this->db_table_test->insert(array('title' =>  'wow', 'description' => 'description'));
    $this->db_table_test->insert(array('title' =>  'wow', 'description' => 'description2'));

    $updated_rows_count = $this->db_table_test->update(array('description' =>  'new_description', 'junk!!!' => 'junk!!!'));

    $this->assertEqual($this->db_table_test->getAffectedRowCount(), 2);
    $this->assertEqual($updated_rows_count, 2);

    $stmt = $this->conn->newStatement("SELECT * FROM test_db_table");
    $records = $stmt->getRecordSet();

    $records->rewind();
    $record = $records->current();
    $this->assertEqual($record->get('description'), 'new_description');

    $records->next();
    $record = $records->current();
    $this->assertEqual($record->get('description'), 'new_description');
  }

  function testUpdateAllWithRawSet()
  {
    $this->db_table_test->insert(array('ordr' =>  '1'));
    $this->db_table_test->insert(array('ordr' =>  '10'));

    $raw_criteria = $this->conn->quoteIdentifier('ordr') . '=' . $this->conn->quoteIdentifier('ordr') . '+1';
    $updated_rows_count = $this->db_table_test->update($raw_criteria);

    $this->assertEqual($this->db_table_test->getAffectedRowCount(), 2);
    $this->assertEqual($updated_rows_count, 2);

    $stmt = $this->conn->newStatement("SELECT * FROM test_db_table");
    $records = $stmt->getRecordSet();

    $records->rewind();
    $record = $records->current();
    $this->assertEqual($record->get('ordr'), '2');

    $records->next();
    $record = $records->current();
    $this->assertEqual($record->get('ordr'), '11');
  }

  function testUpdateByCriteria()
  {
    $this->db_table_test->insert(array('title' =>  'wow', 'description' => 'description'));//should be changed
    $this->db_table_test->insert(array('title' =>  'wow', 'description' => 'description2'));//should be changed
    $this->db_table_test->insert(array('title' =>  'yo', 'description' => 'description3'));

    $updated_rows_count = $this->db_table_test->update(
      array('description' =>  'new_description', 'title' => 'wow2', 'junk!!!' => 'junk!!!'),
      new lmbSQLFieldCriteria('title', 'wow')
    );

    $this->assertEqual($this->db_table_test->getAffectedRowCount(), 2);
    $this->assertEqual($updated_rows_count, 2);

    $stmt = $this->conn->newStatement("SELECT * FROM test_db_table ORDER BY " . $this->conn->quoteIdentifier('id'));
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

  function testUpdateById()
  {
    $id = $this->db_table_test->insert(array('id' => null, 'title' =>  'wow', 'description' => 'description'));
    $this->db_table_test->insert(array('id' => null, 'title' =>  'wow2', 'description' => 'description2'));

    $this->db_table_test->updateById($id, array('description' =>  'new_description'));

    $this->assertEqual($this->db_table_test->getAffectedRowCount(), 1);

    $stmt = $this->conn->newStatement('SELECT * FROM test_db_table WHERE ' . $this->conn->quoteIdentifier('id') . '=' . $id);
    $records = $stmt->getRecordSet();
    $records->rewind();
    $record = $records->current();
    $this->assertEqual($record->get('description'), 'new_description');
  }

  function testSelectAll()
  {
    $data = array(
      0 => array('title' =>  'wow', 'description' => 'description'),
      1 => array('title' =>  'wow', 'description' => 'description2')
    );

    $this->db_table_test->insert($data[0]);
    $this->db_table_test->insert($data[1]);

    $result = $this->db_table_test->select();

    $this->assertEqual($result->count(), 2);

    $result->rewind();
    $record = $result->current();
    $this->assertEqual($record->get('description'), 'description');

    $result->next();
    $record = $result->current();
    $this->assertEqual($record->get('description'), 'description2');
  }

  function testSelectAllLimitFields()
  {
    $this->db_table_test->insert(array('title' =>  'wow', 'description' => 'description'));

    $result = $this->db_table_test->select(null, array(), array('title'));

    $this->assertEqual($result->count(), 1);

    $this->assertEqual($result->at(0)->get('title'), 'wow');
    $this->assertNull($result->at(0)->get('description'));
  }

  function testSelectRecordById()
  {
    $data = array(
      0 => array('title' =>  'wow', 'description' => 'description'),
      1 => array('title' =>  'wow!', 'description' => 'description2')
    );

    $this->db_table_test->insert($data[0]);
    $id = $this->db_table_test->insert($data[1]);

    $record = $this->db_table_test->selectRecordById($id);
    $this->assertEqual($record->get('description'), 'description2');
  }

  function testSelectRecordByIdLimitFields()
  {
    $id = $this->db_table_test->insert(array('title' =>  'wow', 'description' => 'description'));

    $record = $this->db_table_test->selectRecordById($id, array('title'));
    $this->assertEqual($record->get('title'), 'wow');
    $this->assertNull($record->get('description'));
  }

  function testSelectRecordByIdNotFound()
  {
    $this->assertNull($this->db_table_test->selectRecordById(1));
  }

  function testSelectFirstRecord()
  {
    $data = array(
      0 => array('title' =>  'wow', 'description' => 'description'),
      1 => array('title' =>  'wow!', 'description' => 'description2')
    );

    $this->db_table_test->insert($data[0]);
    $this->db_table_test->insert($data[1]);

    $record = $this->db_table_test->selectFirstRecord();
    $this->assertEqual($record->get('title'), 'wow');
  }

  function testSelectFirstRecordLimitFields()
  {
    $id = $this->db_table_test->insert(array('title' =>  'wow', 'description' => 'description'));

    $record = $this->db_table_test->selectFirstRecord(null, array(), array('title'));
    $this->assertEqual($record->get('title'), 'wow');
    $this->assertNull($record->get('description'));
  }

  function testSelectFirstRecordReturnNullIfNothingIsFound()
  {
    $this->assertNull($this->db_table_test->selectFirstRecord($this->conn->quoteIdentifier('id') . '= -10000'));
  }

  function testDeleteAll()
  {
    $data = array(
      0 => array('title' =>  'wow', 'description' => 'description'),
      1 => array('title' =>  'wow!', 'description' => 'description2')
    );

    $this->db_table_test->insert($data[0]);
    $this->db_table_test->insert($data[1]);

    $this->db_table_test->delete();

    $this->assertEqual($this->db_table_test->getAffectedRowCount(), 2);

    $stmt = $this->conn->newStatement("SELECT * FROM test_db_table");
    $records = $stmt->getRecordSet();

    $this->assertEqual($records->count(), 0);
  }

  function testDeleteByCriteria()
  {
    $data = array(
      0 => array('title' =>  'wow', 'description' => 'description'),
      1 => array('title' =>  'wow!', 'description' => 'description2')
    );

    $this->db_table_test->insert($data[0]);
    $this->db_table_test->insert($data[1]);

    $this->db_table_test->delete(new lmbSQLFieldCriteria('title', 'hey'));

    $this->assertEqual($this->db_table_test->getAffectedRowCount(), 0);

    $stmt = $this->conn->newStatement("SELECT * FROM test_db_table");
    $records = $stmt->getRecordSet();

    $this->assertEqual($records->count(), 2);
  }

  function testDeleteById()
  {
    $data = array(
      0 => array('title' =>  'wow', 'description' => 'description'),
      1 => array('title' =>  'wow!', 'description' => 'description2')
    );

    $id = $this->db_table_test->insert($data[0]);
    $this->db_table_test->insert($data[1]);

    $this->db_table_test->deleteById($id);

    $stmt = $this->conn->newStatement("SELECT * FROM test_db_table");
    $records = $stmt->getRecordSet();

    $this->assertEqual($records->count(), 1);

    $records->rewind();

    $record = $records->current();
    $this->assertEqual($record->get('title'), 'wow!');
  }

  function testGetColumnsForSelectDefaultName()
  {
    $this->assertEqual($this->db_table_test->getColumnsForSelect(), array('test_db_table.id' => 'id',
                                                                          'test_db_table.description' => 'description',
                                                                          'test_db_table.title' => 'title',
                                                                          'test_db_table.ordr' => 'ordr'));
  }

  function testGetColumnsForSelectSpecificNameAndPrefix()
  {
    $this->assertEqual($this->db_table_test->getColumnsForSelect('tn', array(), '_'),
                       array('tn.id' => '_id',
                            'tn.description' => '_description',
                            'tn.title' => '_title',
                            'tn.ordr' => '_ordr'));
  }

  function testGetColumnsForSelectSpecificNameWithExcludes()
  {
    $this->assertEqual($this->db_table_test->getColumnsForSelect('tn', array('id', 'description')),
                       array('tn.title' => 'title', 'tn.ordr' => 'ordr'));

  }
}

