<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/dbal/src/drivers/lmbAuditDbConnection.class.php');
lmb_require('limb/dbal/src/drivers/lmbDbConnection.interface.php');
lmb_require('limb/dbal/src/drivers/lmbDbStatement.interface.php');

Mock :: generate('lmbDbConnection', 'MockDbConnection');
Mock :: generate('lmbDbStatement', 'MockDbStatement');

class lmbAuditDbConnectionTest extends UnitTestCase
{
  protected $wrapped;
  protected $connection;

  function setUp()
  {
    $this->wrapped = new MockDbConnection();
    $this->connection = new lmbAuditDbConnection($this->wrapped);
  }

  function testExecuteIncreasesQueryCounter()
  {
    $sql = 'Some sql query'; 
    $this->wrapped->expectOnce('execute', array($sql));
    $this->connection->execute($sql);
    
    $this->assertEqual($this->connection->countQueries(), 1);
  }

  function testResetQueryCounter()
  {
    $sql = 'Some sql query'; 
    $this->connection->execute($sql);
    $this->connection->execute($sql);
    
    $this->assertEqual($this->connection->countQueries(), 2);
    
    $this->connection->resetStats();
    
    $this->assertEqual($this->connection->countQueries(), 0);
  }
  
  function testNewStatementSetSelfAsConnection()
  {
    $sql = 'whatever sql';
    
    $this->wrapped->expectOnce('newStatement', array($sql));
    
    $statement = new MockDbStatement();
    $statement->expectOnce('setConnection', array(new ReferenceExpectation($this->connection)));
    $this->wrapped->expectOnce('newStatement', array($sql));
    $this->wrapped->setReturnValue('newStatement', $statement, array($sql));
    
    $refreshed_statement = $this->connection->newStatement($sql);
    $this->assertReference($statement, $refreshed_statement);
  }
  
  function testGetQueries()
  {
    $sql1 = 'SELECT program.* FROM program';
    $sql2 = 'select program.* FROM program';
    $sql3 = 'select course.* FROM course';
    $this->connection->execute($sql1);
    $this->connection->execute($sql2);
    $this->connection->execute($sql3);
   
    $this->assertEqual(count($this->connection->getQueries('select program.*')), 2);
  }
}

