<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/dbal/src/query/lmbInsertOnDuplicateUpdateQuery.class.php');

class lmbInsertOnDuplicateUpdateQueryTest extends lmbQueryBaseTestCase
{

  function skip()
  {
    $current_connection = lmbToolkit::instance()->getDefaultDbConnection();
    $this->skipIf(!lmbInsertOnDuplicateUpdateQuery::isSupportedByDbConnection($current_connection));
  }

  function testInsert()
  {
    $startId = $this->db->insert('test_db_table', array('description' => 'text1'));

    $query = new lmbInsertOnDuplicateUpdateQuery('test_db_table', $this->conn);
    $query->addField('id', $id = $startId+1);
    $query->addField('description', $description = 'Some \'description\'');
    $query->addField('title', $title = 'Some title');

    $stmt = $query->getStatement();
    $stmt->execute();

    $rs = $this->db->select('test_db_table')->sort(array('id' => 'ASC'));
    $arr = $rs->getArray();

    $this->assertEqual(sizeof($arr), 2);
    $this->assertEqual($arr[0]['id'], $startId);
    $this->assertEqual($arr[1]['id'], $id);
    $this->assertEqual($arr[1]['description'], $description);
    $this->assertEqual($arr[1]['title'], $title);
  }

  function testUpdate()
  {
    $startId = $this->db->insert('test_db_table', array('description' => 'text1'));

    $query = new lmbInsertOnDuplicateUpdateQuery('test_db_table', $this->conn);
    $query->addField('id', $id = $startId+1);
    $query->addField('description', 'Some \'description\'');
    $query->addField('title', 'Some title');

    $stmt = $query->getStatement()->execute();

    $query = new lmbInsertOnDuplicateUpdateQuery('test_db_table', $this->conn);
    $query->addField('id', $id);
    $query->addField('description', $description = 'Some another \'description\'');
    $query->addField('title', $title = 'Some another title');

    $stmt = $query->getStatement()->execute();

    $rs = $this->db->select('test_db_table')->sort(array('id' => 'ASC'));
    $arr = $rs->getArray();

    $this->assertEqual(sizeof($arr), 2);
    $this->assertEqual($arr[0]['id'], $startId);
    $this->assertEqual($arr[1]['id'], $id);
    $this->assertEqual($arr[1]['description'], $description);
    $this->assertEqual($arr[1]['title'], $title);
  }

  function testAddFieldWithoutValueOnlyReservesAPlaceholder()
  {
    $query = new lmbInsertOnDuplicateUpdateQuery('test_db_table', $this->conn);
    $query->addField('description');
    $query->addField('title');

    $stmt = $query->getStatement();
    $stmt->set('description', $description = 'Some \'description\'');
    $stmt->set('title', $title = 'Some title');
    $stmt->execute();

    $rs = $this->db->select('test_db_table');
    $arr = $rs->getArray();

    $this->assertEqual(sizeof($arr), 1);
    $this->assertEqual($arr[0]['description'], $description);
    $this->assertEqual($arr[0]['title'], $title);

  }
}

