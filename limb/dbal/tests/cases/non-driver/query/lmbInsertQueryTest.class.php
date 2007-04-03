<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbInsertQueryTest.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */
lmb_require('limb/dbal/src/query/lmbInsertQuery.class.php');
lmb_require('limb/dbal/src/lmbSimpleDb.class.php');

class lmbInsertQueryTest extends UnitTestCase
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

  function testInsert()
  {
    $this->db->insert('test_db_table', array('id' => 100));

    $query = new lmbInsertQuery('test_db_table', $this->conn);
    $query->addField('id', $id = 101);
    $query->addField('description', $description = 'Some \'description\'');
    $query->addField('title', $title = 'Some title');

    $stmt = $query->getStatement();
    $stmt->execute();

    $rs = $this->db->select('test_db_table')->sort(array('id' => 'ASC'));
    $arr = $rs->getArray();

    $this->assertEqual(sizeof($arr), 2);
    $this->assertEqual($arr[0]['id'], 100);
    $this->assertEqual($arr[1]['id'], $id);
    $this->assertEqual($arr[1]['description'], $description);
    $this->assertEqual($arr[1]['title'], $title);
  }

  function testAddFieldWithoutValueOnlyReservesAPlaceholder()
  {
    $query = new lmbInsertQuery('test_db_table', $this->conn);
    $query->addField('id');
    $query->addField('description');
    $query->addField('title');

    $stmt = $query->getStatement();
    $stmt->set('id', $id = 101);
    $stmt->set('description', $description = 'Some \'description\'');
    $stmt->set('title', $title = 'Some title');
    $stmt->execute();

    $rs = $this->db->select('test_db_table');
    $arr = $rs->getArray();

    $this->assertEqual(sizeof($arr), 1);
    $this->assertEqual($arr[0]['id'], $id);
    $this->assertEqual($arr[0]['description'], $description);
    $this->assertEqual($arr[0]['title'], $title);

  }
}
?>
