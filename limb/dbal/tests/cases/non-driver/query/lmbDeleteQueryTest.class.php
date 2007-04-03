<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDeleteQueryTest.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */
lmb_require('limb/dbal/src/criteria/lmbSQLFieldCriteria.class.php');
lmb_require('limb/dbal/src/lmbSimpleDb.class.php');
lmb_require('limb/dbal/src/query/lmbDeleteQuery.class.php');

class lmbDeleteQueryTest extends UnitTestCase
{
  var $query;
  var $db;

  function setUp()
  {
    $toolkit = lmbToolkit :: save();
    $this->conn = $toolkit->getDefaultDbConnection();
    $this->db = new lmbSimpleDb($this->conn);

    $this->_dbCleanUp();
  }

  function tearDown()
  {
    $this->_dbCleanUp();
  }

  function _dbCleanUp()
  {
    $this->db->delete('test_db_table');
  }

  function testDelete()
  {
    $this->db->insert('test_db_table', array('id' => 100));
    $this->db->insert('test_db_table', array('id' => 101));

    $query = new lmbDeleteQuery('test_db_table', $this->conn);
    $stmt = $query->getStatement();
    $stmt->execute();

    $rs = $this->db->select('test_db_table');
    $this->assertEqual($rs->count(), 0);
  }

  function testDeleteFiltered()
  {
    $this->db->insert('test_db_table', array('id' => 100));
    $this->db->insert('test_db_table', array('id' => 101));
    $this->db->insert('test_db_table', array('id' => 102));

    $query = new lmbDeleteQuery('test_db_table', $this->conn);
    $query->addCriteria(new lmbSQLFieldCriteria('id', 100));
    $stmt = $query->getStatement();
    $stmt->execute();

    $rs = $this->db->select('test_db_table')->sort(array('id' => 'ASC'));
    $arr = $rs->getArray();
    $this->assertEqual($arr[0]['id'], 101);
    $this->assertEqual($arr[1]['id'], 102);
    $this->assertEqual(sizeof($arr), 2);
  }
}
?>
