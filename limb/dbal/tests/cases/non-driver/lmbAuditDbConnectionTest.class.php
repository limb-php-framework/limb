<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/dbal/src/drivers/lmbAuditDbConnection.class.php');
lmb_require('limb/dbal/src/drivers/lmbDbConnection.interface.php');

Mock :: generate('lmbDbConnection', 'MockDbConnection');

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
    
    $this->assertEqual($this->connection->count(), 1);
  }

  function testResetQueryCounter()
  {
    $sql = 'Some sql query'; 
    $this->connection->execute($sql);
    $this->connection->execute($sql);
    
    $this->assertEqual($this->connection->count(), 2);
    
    $this->connection->reset();
    
    $this->assertEqual($this->connection->count(), 0);
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

