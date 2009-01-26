<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/dbal/src/filter/lmbAutoDbTransactionFilter.class.php');
lmb_require('limb/filter_chain/src/lmbFilterChain.class.php');
lmb_require('limb/dbal/src/lmbSimpleDb.class.php');

Mock :: generate('lmbFilterChain', 'MockFilterChain');

class FilterWorkingWithDbStub
{
  var $sql;
  var $exception;

  function run($chain)
  {
    if($this->sql)
    {
      $stmt = lmbToolkit :: instance()->getDefaultDbConnection()->newStatement($this->sql);
      $stmt->execute();
    }

    if($this->exception)
      throw $this->exception;
  }
}

class lmbAutoDbTransactionFilterTest extends UnitTestCase
{
  var $toolkit;
  var $db;

  function setUp()
  {
    $this->toolkit = lmbToolkit :: save();
    $this->conn = $this->toolkit->getDefaultDbConnection();
    $this->db = new lmbSimpleDb($this->conn);
    $this->db->delete('test_db_table');
  }

  function tearDown()
  {
    $this->db->delete('test_db_table');
    lmbToolkit :: restore();
  }

  function testOldConnectionIsRestored()
  {
    $this->assertFalse($this->conn instanceof lmbAutoTransactionConnection);

    $filter = new lmbAutoDbTransactionFilter();
    $chain = new MockFilterChain();
    $chain->expectOnce('next');
    $filter->run($chain);

    $this->assertIdentical($this->conn, $this->toolkit->getDefaultDbConnection());
  }

  function testAutoCommitTransaction()
  {
    $stub = new FilterWorkingWithDbStub();
    $stub->sql = "INSERT INTO test_db_table (title) VALUES ('hey')";

    $this->assertEqual($this->db->count('test_db_table'), 0);

    $chain = new lmbFilterChain();
    $chain->registerFilter(new lmbAutoDbTransactionFilter());
    $chain->registerFilter($stub);
    $chain->process();

    $this->conn->rollbackTransaction();

    $this->assertEqual($this->db->count('test_db_table'), 1);
    $this->assertIdentical($this->conn, $this->toolkit->getDefaultDbConnection());
  }

  function testRollBackOnException()
  {
    $stub = new FilterWorkingWithDbStub();
    $stub->sql = "INSERT INTO test_db_table (title) VALUES ('hey')";
    $stub->exception = new Exception('foo');

    $this->assertEqual($this->db->count('test_db_table'), 0);

    $chain = new lmbFilterChain();
    $chain->registerFilter(new lmbAutoDbTransactionFilter());
    $chain->registerFilter($stub);

    try
    {
      $chain->process();
      $this->assertTrue(false);
    }
    catch(Exception $e){}

    $this->assertEqual($this->db->count('test_db_table'), 0);
    $this->assertIdentical($this->conn, $this->toolkit->getDefaultDbConnection());
  }

}

