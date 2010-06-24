<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/dbal/src/drivers/lmbAutoTransactionConnection.class.php');
lmb_require('limb/dbal/src/drivers/lmbDbConnection.interface.php');

Mock :: generate('lmbDbConnection', 'MockDbConnection');

class lmbAutoTransactionConnectionTest extends UnitTestCase
{
  protected $wrapped;
  protected $connection;

  function setUp()
  {
    $this->wrapped = new MockDbConnection();
    $this->connection = new lmbAutoTransactionConnection($this->wrapped);
  }

  function testCommitIfTransactionStartedOnly()
  {
    $this->wrapped->expectNever('commitTransaction');
    $this->connection->commitTransaction();
  }

  function testBeginTransactionOnce()
  {
    $this->wrapped->expectCallCount('beginTransaction', 1);
    $this->connection->beginTransaction();
    $this->connection->beginTransaction();
  }

  function testBeginAndCommitTransaction()
  {
    $this->wrapped->expectCallCount('beginTransaction', 2);
    $this->connection->beginTransaction();
    $this->connection->commitTransaction();
    $this->connection->beginTransaction();
  }

  function testRollbackIfTransactionStartedOnly()
  {
    $this->wrapped->expectNever('rollbackTransaction');
    $this->connection->rollbackTransaction();
  }

  function testBeginAndRollbackTransaction()
  {
    $this->wrapped->expectCallCount('beginTransaction', 2);
    $this->connection->beginTransaction();
    $this->connection->rollbackTransaction();
    $this->connection->beginTransaction();
  }

  function testDontBeginTransactionOnSelect()
  {
    $this->wrapped->expectNever('beginTransaction');
    $this->connection->newStatement('SELECT ...');
  }

  function testDelete()
  {
    $this->_assertBeginForStatement('DELETE ...');
  }

  function testDeleteIgnoreCase()
  {
    $this->_assertBeginForStatement('DeLeTE ...');
  }

  function testDeleteNonTrimmed()
  {
    $this->_assertBeginForStatement(' DELETE ...');
  }

  function testUpdate()
  {
    $this->_assertBeginForStatement('UPDATE ...');
  }

  function testUpdateIgnoreCase()
  {
    $this->_assertBeginForStatement('UpDaTe ...');
  }

  function testUpdateNonTrimmed()
  {
    $this->_assertBeginForStatement(' UPDATE ...');
  }

  function testInsert()
  {
    $this->_assertBeginForStatement('INSERT ...');
  }

  function testInsertIgnoreCase()
  {
    $this->_assertBeginForStatement('InseRt ...');
  }

  function testInsertNonTrimmed()
  {
    $this->_assertBeginForStatement(' INSERT ...');
  }

  function testDrop()
  {
    $this->_assertBeginForStatement('DROP ...');
  }

  function testDropIgnoreCase()
  {
    $this->_assertBeginForStatement('DrOp ...');
  }

  function testDropNonTrimmed()
  {
    $this->_assertBeginForStatement(' DROP ...');
  }

  function testInTransaction()
  {
    $this->assertFalse($this->connection->isInTransaction());
    $this->connection->beginTransaction();
    $this->assertTrue($this->connection->isInTransaction());
    $this->connection->rollbackTransaction();
    $this->assertFalse($this->connection->isInTransaction());
  }

  function _assertBeginForStatement($sql)
  {
    $this->wrapped->expectOnce('newStatement', array($sql));
    $this->wrapped->setReturnValue('newStatement', $stmt = 'whatever', array($sql));
    $this->wrapped->expectOnce('beginTransaction');
    $this->assertEqual($this->connection->newStatement($sql), $stmt);
  }
}

