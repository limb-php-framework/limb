<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/session/src/lmbSessionDbStorage.class.php');
lmb_require('limb/dbal/src/lmbSimpleDb.class.php');

Mock :: generate('TestSessionObject');

class lmbSessionDbStorageTest extends UnitTestCase
{
  protected $db;
  protected $conn;
  protected $driver;

  function setUp()
  {
    $toolkit = lmbToolkit :: save();
    $this->conn = $toolkit->getDefaultDbConnection();
    $this->db = new lmbSimpleDb($this->conn);

    $this->db->delete('lmb_session');

    $this->driver = new lmbSessionDbStorage($this->conn);
  }

  function tearDown()
  {
    $this->db->delete('lmb_session');

    lmbToolkit :: restore();
  }

  function testStorageOpen()
  {
    $this->assertTrue($this->driver->storageOpen());
  }

  function testStorageClose()
  {
    $this->assertTrue($this->driver->storageClose());
  }

  function testStorageReadOk()
  {
    $this->db->insert('lmb_session',
                          array('session_id' => $id = 'fghprty121as',
                                'session_data' => $data = 'global_user|O:4:"user":12:{s:3:"_id";...',
                                'last_activity_time' => 10), null);

    $this->db->insert('lmb_session',
                          array('session_id' => 'junk',
                                'session_data' => 'global_user|O:4:"user":12:{s:3:"_id";...',
                                'last_activity_time' => 10), null);

    $this->assertEqual($data, $this->driver->storageRead($id));
  }

  function testStorageReadBadSessionId()
  {
    $this->assertFalse($this->driver->storageRead("'bad';DROP lmb_session;"));
  }

  function testStorageReadFalse()
  {
    $this->db->insert('lmb_session',
                          array('session_id' => 'junk',
                                'session_data' => 'global_user|O:4:"user":12:{s:3:"_id";...',
                                'last_activity_time' => 10), null);


    $this->assertIdentical(false, $this->driver->storageRead('no_such_session'));
  }

  function testStorageWriteInsert()
  {
    $value = 'whatever';
    $id = 20;

    $this->driver->storageWrite($id, $value);

    $rs = $this->db->select('lmb_session');

    $this->assertEqual($rs->count(), 1);

    $rs->rewind();
    $record = $rs->current();

    $this->assertEqual($record->get('session_id'), $id);
    $this->assertEqual($record->get('session_data'), $value);
    $this->assertTrue($record->get('last_activity_time') > 0 &&  $record->get('last_activity_time') <= time());
  }

  function testStorageWriteUpdate()
  {
    $this->db->insert('lmb_session',
                          array('session_id' => $id = 'fghprty121as',
                                'session_data' => $value = 'global_user|O:4:"user":12:{s:3:"_id";...',
                                'last_activity_time' => $time = 10), null);

    $this->driver->storageWrite($id, $value);

    $rs = $this->db->select('lmb_session');

    $this->assertEqual($rs->count(), 1);

    $rs->rewind();
    $record = $rs->current();

    $this->assertEqual($record->get('session_id'), $id);
    $this->assertEqual($record->get('session_data'), $value);
    $this->assertTrue($record->get('last_activity_time') > $time &&  $record->get('last_activity_time') <= time());
  }

  function testStorageWriteInsertBadSessionId()
  {
    $id = "'fghprty121as';SELECT * FROM test;";
    $value = "'data';DROP lmb_session;";

    $this->driver->storageWrite($id, $value);

    $rs = $this->db->select('lmb_session');
    $rs->rewind();
    $record = $rs->current();

    $this->assertEqual($record->get('session_id'), $id);
    $this->assertEqual($record->get('session_data'), $value);
  }

  function testStorageWriteUpdateBadSessionId()
  {
    $this->db->insert('lmb_session',
                          array('session_id' => $id = "'fghprty121as';SELECT * FROM test;",
                                'session_data' => $value = "'data';DROP lmb_session;"), null);

    $this->driver->storageWrite($id, $value);

    $rs = $this->db->select('lmb_session');
    $rs->rewind();
    $record = $rs->current();

    $this->assertEqual($record->get('session_id'), $id);
    $this->assertEqual($record->get('session_data'), $value);
  }

  function testStorageDestroy()
  {
    $this->db->insert('lmb_session',
                          array('session_id' => $id = "'fghprty121as';SELECT * FROM test;",
                                'session_data' => "data"), null);

    $this->db->insert('lmb_session',
                          array('session_id' => 'junk',
                                'session_data' => 'junk'), null);

    $this->driver->storageDestroy($id);

    $rs = $this->db->select('lmb_session');

    $this->assertEqual($rs->count(), 1);
    $rs->rewind();
    $record = $rs->current();
    $this->assertEqual($record->get('session_id'), 'junk');
  }

  function testStorageGcTrue()
  {
    $this->db->insert('lmb_session',
                          array('session_id' => "whatever",
                                'session_data' => "data",
                                'last_activity_time' => time() - 301), null);

    $this->driver->storageGc(300);

    $rs = $this->db->select('lmb_session');
    $rs->rewind();
    $this->assertFalse($rs->valid());
  }

  function testStorageGcUseSettedMaxLifeTime()
  {
    $driver = new lmbSessionDbStorage($this->conn, $max_life_time = 500);

    $this->db->insert('lmb_session',
                          array('session_id' => "whatever",
                                'session_data' => "data",
                                'last_activity_time' => time() - 400), null);

    $driver->storageGc(300);

    $rs = $this->db->select('lmb_session');
    $rs->rewind();
    $this->assertTrue($rs->valid());
  }

  function testStorageGcFalse()
  {
    $this->db->insert('lmb_session',
                          array('session_id' => "whatever",
                                'session_data' => "data",
                                'last_activity_time' => time() - 298), null);

    $this->driver->storageGc(300);

    $rs = $this->db->select('lmb_session');
    $rs->rewind();
    $this->assertFalse(!$rs->valid());
  }
}


