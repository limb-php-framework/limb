<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/dbal/src/query/lmbDeleteQuery.class.php');

class lmbDeleteQueryTest extends lmbQueryBaseTestCase
{
  function testDelete()
  {
    $startId = $this->db->insert('test_db_table', array('description' => 'text1'));
    $this->db->insert('test_db_table', array('description' => 'text2'));

    $query = new lmbDeleteQuery('test_db_table', $this->conn);
    $stmt = $query->getStatement();
    $stmt->execute();

    $rs = $this->db->select('test_db_table');
    $this->assertEqual($rs->count(), 0);
  }

  function testDeleteWithCondition()
  {
    $startId = $this->db->insert('test_db_table', array('description' => 'text1'));
    $this->db->insert('test_db_table', array('description' => 'text2'));
    $this->db->insert('test_db_table', array('description' => 'text3'));

    $query = new lmbDeleteQuery('test_db_table', $this->conn);
    $query->addCriteria(new lmbSQLFieldCriteria('id', $startId));
    $stmt = $query->getStatement();
    $stmt->execute();

    $rs = $this->db->select('test_db_table')->sort(array('id' => 'ASC'));
    $arr = $rs->getArray();
    $this->assertEqual($arr[0]['id'], $startId+1);
    $this->assertEqual($arr[1]['id'], $startId+2);
    $this->assertEqual(sizeof($arr), 2);
  }

  function testChaining()
  {
    $startId = $this->db->insert('test_db_table', array('description' => 'text1'));
    $this->db->insert('test_db_table', array('description' => 'text2'));

    $query = new lmbDeleteQuery('test_db_table', $this->conn);
    $query->where($this->conn->quoteIdentifier('id') . '=' . intval($startId))->execute();

    $rs = $this->db->select('test_db_table');
    $arr = $rs->getArray();
    $this->assertEqual($arr[0]['id'], $startId+1);
    $this->assertEqual(sizeof($arr), 1);
  }
}

