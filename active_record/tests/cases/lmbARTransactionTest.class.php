<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
require_once(dirname(__FILE__) . '/lmbActiveRecordTest.class.php');//need TestOneTableObjectFailing

class lmbARTransactionTest extends lmbARBaseTestCase
{
  protected $tables_to_cleanup = array('test_one_table_object');
  
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
    $obj->fail = new Exception('whatever');

    $this->assertFalse($obj->trySave());

    $this->conn->commitTransaction();

    $this->assertEqual($this->db->count('test_one_table_object'), 0);
  }
}

