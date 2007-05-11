<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbActiveRecordTest.class.php 5863 2007-05-11 12:56:42Z pachanga $
 * @package    active_record
 */
lmb_require('limb/active_record/src/lmbActiveRecord.class.php');
lmb_require('limb/dbal/src/lmbSimpleDb.class.php');

class TestOneTableObjectFailing extends lmbActiveRecord
{
  var $fail = false;
  protected $_db_table_name = 'test_one_table_object';

  protected function _onAfterSave()
  {
    if($this->fail)
      throw new Exception('catch me');
  }
}

class lmbActiveRecordTransactionTest extends UnitTestCase
{
  function setUp()
  {
    $this->conn = lmbToolkit :: instance()->getDefaultDbConnection();
    $this->db = new lmbSimpleDb($this->conn);

    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $this->db->delete('test_one_table_object');
  }

  function  testSaveInTransaction()
  {
    $this->conn->beginTransaction();

    $obj = new TestOneTableObjectFailing();
    $obj->setContent('hey');

    $this->assertTrue($obj->trySave());

    $this->conn->commitTransaction();

    $this->assertEqual($this->db->count('test_one_table_object'), 1);
  }

  function  testSaveRollbacksTransaction()
  {
    $this->conn->beginTransaction();

    $obj = new TestOneTableObjectFailing();
    $obj->setContent('hey');
    $obj->fail = true;

    $this->assertFalse($obj->trySave());

    $this->conn->commitTransaction();

    $this->assertEqual($this->db->count('test_one_table_object'), 0);
  }
}
?>
