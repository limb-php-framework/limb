<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/dbal/src/query/lmbBulkInsertQuery.class.php');

class lmbBulkInsertQueryTest extends lmbQueryBaseTestCase
{
  function testInsert()
  { 
    $query = new lmbBulkInsertQuery('test_db_table', $this->conn);
    $query->addSet(array('id' => 2, 'title' => 'some title', 'description' => 'some description'));
    $query->addSet(array('id' => 4, 'title' => 'some other title', 'description' => 'some other description'));
    $stmt = $query->getStatement();
    $stmt->execute();

    $rs = $this->db->select('test_db_table')->sort(array('id' => 'ASC'));
    $arr = $rs->getArray();

    $this->assertEqual(sizeof($arr), 2);
    $this->assertEqual($arr[0]['id'], 2);
    $this->assertEqual($arr[0]['title'], 'some title');
    $this->assertEqual($arr[0]['description'], 'some description');
    $this->assertEqual($arr[1]['id'], 4);
    $this->assertEqual($arr[1]['description'], 'some other description');
    $this->assertEqual($arr[1]['title'], 'some other title');
  }
}

