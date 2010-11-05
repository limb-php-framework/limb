<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once(dirname(__FILE__) . '/../DriverRecordSetTestBase.class.php');
require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbMssqlRecordSetTest extends DriverRecordSetTestBase
{
  function lmbMssqlRecordSetTest()
  {
    parent :: DriverRecordSetTestBase('lmbMssqlRecord');
  }

  function setUp()
  {
    $this->connection = lmbToolkit :: instance()->getDefaultDbConnection();
    DriverMssqlSetup($this->connection->getConnectionId());
    parent::setUp();
  }
  
  function testPagination()
  {
    $stmt = $this->connection->newStatement('DELETE FROM founding_fathers');
    $stmt->execute();
    $this->assertEqual($this->_countRecords(), 0);
    $stmt = $this->connection->newStatement("INSERT INTO founding_fathers (id, first, last) VALUES (1, 'George', 'Washington')");
    $stmt->execute();
    $stmt = $this->connection->newStatement("INSERT INTO founding_fathers (id, first, last) VALUES (2, 'George', 'Washington')");
    $stmt->execute();
    $stmt = $this->connection->newStatement("INSERT INTO founding_fathers (id, first, last) VALUES (3, 'George', 'Washington')");
    $stmt->execute();
    $stmt = $this->connection->newStatement("INSERT INTO founding_fathers (id, first, last) VALUES (4, 'George', 'Washington')");
    $stmt->execute();
    $stmt = $this->connection->newStatement("INSERT INTO founding_fathers (id, first, last) VALUES (5, 'George', 'Washington')");
    $stmt->execute();
    $stmt = $this->connection->newStatement("INSERT INTO founding_fathers (id, first, last) VALUES (6, 'George', 'Washington')");
    $stmt->execute();
    $stmt = $this->connection->newStatement("INSERT INTO founding_fathers (id, first, last) VALUES (7, 'George', 'Washington')");
    $stmt->execute();
    $stmt = $this->connection->newStatement("INSERT INTO founding_fathers (id, first, last) VALUES (8, 'George', 'Washington')");
    $stmt->execute();
    $stmt = $this->connection->newStatement("INSERT INTO founding_fathers (id, first, last) VALUES (9, 'George', 'Washington')");
    $stmt->execute();
    $stmt = $this->connection->newStatement("INSERT INTO founding_fathers (id, first, last) VALUES (10, 'George', 'Washington')");
    $stmt->execute();
    $this->assertEqual($this->_countRecords(), 10);
    
    $stmt = $this->connection->newStatement("select * from founding_fathers order by id");
    $rs = $stmt->getRecordset();
    $rs->rewind();
    $rec = $rs->current();
    $this->assertEqual($rec->get('id'), 1);
    
    $rec = $rs->at(4);
    $this->assertEqual($rec->get('id'), 5);
    
    $rec = $rs->at(0);
    $this->assertEqual($rec->get('id'), 1);
    
    $rec = $rs->at(9);
    $this->assertEqual($rec->get('id'), 10);
    $this->assertEqual($rs->count(), 10);
    
    $rs->paginate(0, 4);
    $this->assertEqual($rs->countPaginated(), 4);
    $this->assertEqual($rs->count(), 10);
    $rs->rewind();
    $rec = $rs->current();
    $this->assertEqual($rec->get('id'), 1);
    $rs->next();
    $rec = $rs->current();
    $this->assertEqual($rec->get('id'), 2);
    $rs->next();
    $rec = $rs->current();
    $this->assertEqual($rec->get('id'), 3);
    $rs->next();
    $rec = $rs->current();
    $this->assertEqual($rec->get('id'), 4);
    
    $rs->paginate(4, 4);
    $index = 1;
    foreach ($rs as $rec)
    {
      $this->assertEqual($rec->get('id'), 4+$index);
      $index++;
    }
    $this->assertEqual($index, 5);
    
    $rs->paginate(8, 4);
    $index = 1;
    foreach ($rs as $rec)
    {
      $this->assertEqual($rec->get('id'), 8+$index);
      $index++;
    }
    $this->assertEqual($index, 3);
    
  }
  
  function _countRecords()
  {
    $stmt = $this->connection->newStatement('SELECT COUNT(*) as cnt FROM founding_fathers');
    return $stmt->getOneValue();
  }
  
}


