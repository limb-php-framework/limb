<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/dbal/src/lmbSimpleDb.class.php');
lmb_require('limb/dbal/src/dump/lmbSQLDumpLoader.class.php');

abstract class lmbSQLDumpLoaderTestBase extends UnitTestCase
{
  var $db;
  var $conn;
  var $file_path;

  function setUp()
  {
    $toolkit = lmbToolkit :: instance();
    $this->conn = $toolkit->getDefaultDbConnection();
    $this->db = new lmbSimpleDb($this->conn);

    $sql = <<<EOD
/* test_one_table_object table records */
insert into test_one_table_object (id, annotation, content) values (1, 'whatever;', 'whatever;');
insert into test_one_table_object (id, annotation, content) values (2, 'whatever;', 'whatever;');

/* test_db_table table records */
insert into test_db_table (id, description, title) values (1, 'whatever;', 'whatever;');
insert into test_db_table (id, description, title) values (2, 'whatever;', 'whatever;');
EOD;

    $this->file_path = LIMB_VAR_DIR . '/sql_dump_loader.tmp';

    $this->_writeDump($sql, $this->file_path);

    $this->_dbCleanUp();
  }

  function tearDown()
  {
    unlink($this->file_path);
    $this->_dbCleanUp();
  }

  function _createLoader($file = null)
  {
    return new lmbSQLDumpLoader($file);
  }

  function _writeDump($sql, $file)
  {
    $fh = fopen($file, 'w');
    fwrite($fh, $sql);
    fclose($fh);
  }

  function _dbCleanUp()
  {
    $this->db->delete('test_one_table_object');
    $this->db->delete('test_db_table');
  }

  function testCreate()
  {
    $loader = $this->_createLoader($this->file_path);
    $this->assertEqual($loader->getAffectedTables(), array('test_one_table_object', 'test_db_table'));
    $this->assertEqual(sizeof($loader->getStatements()), 4);
  }

  function testCreateWithEmptyFile()
  {
    $loader = $this->_createLoader();
    $this->assertEqual($loader->getAffectedTables(), array());
    $this->assertEqual($loader->getStatements(), array());
  }

  function testLoad()
  {
    $loader = $this->_createLoader();
    $loader->loadFile($this->file_path);
    $this->assertEqual($loader->getAffectedTables(), array('test_one_table_object', 'test_db_table'));
    $this->assertEqual(sizeof($loader->getStatements()), 4);
  }

  function testLoadTwice()
  {
    $loader = $this->_createLoader();
    $loader->loadFile($this->file_path);
    $this->assertEqual($loader->getAffectedTables(), array('test_one_table_object', 'test_db_table'));
    $this->assertEqual(sizeof($loader->getStatements()), 4);

    $new_sql = <<<EOD
insert into foo (id, annotation, content) values (1, 'whatever;', 'whatever;');
EOD;

    $second_file = LIMB_VAR_DIR . '/sql_dump_loader.new';
    $this->_writeDump($new_sql, $second_file);

    $loader->loadFile($second_file);

    $this->assertEqual($loader->getAffectedTables(), array('foo'));
    $this->assertEqual(sizeof($loader->getStatements()), 1);
  }

  function testExecute()
  {
    $loader = $this->_createLoader($this->file_path);
    $this->assertEqual($loader->getAffectedTables(), array('test_one_table_object', 'test_db_table'));

    $loader->execute($this->conn);

    $rs1 = $this->db->select('test_one_table_object');
    $this->assertEqual($rs1->count(), 2);

    $rs2 = $this->db->select('test_db_table');
    $this->assertEqual($rs2->count(), 2);
  }

  function testExecutePattern()
  {
    $loader = $this->_createLoader($this->file_path);

    $loader->execute($this->conn, '/test_one_table_object/');

    $rs1 = $this->db->select('test_one_table_object');
    $this->assertEqual($rs1->count(), 2);

    $rs2 = $this->db->select('test_db_table');
    $this->assertEqual($rs2->count(), 0);
  }

  function testFreeDataBase()
  {
    $this->db->insert('test_one_table_object', array('id' => 10,
                                                     'annotation' => 'some annotation',
                                                     'content' => 'some content'));

    $this->db->insert('test_db_table', array('id' => 10,
                                             'description' => 'some description',
                                             'title' => 'some title'));


    $loader = $this->_createLoader($this->file_path);
    $this->assertEqual($loader->getAffectedTables(), array('test_one_table_object', 'test_db_table'));

    $loader->cleanTables($this->conn);

    $rs1 = $this->db->select('test_one_table_object');
    $this->assertEqual($rs1->count(), 0);

    $rs2 = $this->db->select('test_db_table');
    $this->assertEqual($rs2->count(), 0);
  }
}

