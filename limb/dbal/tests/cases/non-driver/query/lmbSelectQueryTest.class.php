<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSelectQueryTest.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */
require_once('limb/dbal/tests/common.inc.php');
lmb_require('limb/dbal/src/criteria/lmbSQLRawCriteria.class.php');
lmb_require('limb/dbal/src/criteria/lmbSQLTableFieldCriteria.class.php');
lmb_require('limb/dbal/src/query/lmbSelectQuery.class.php');
lmb_require('limb/dbal/src/drivers/lmbDbConnection.interface.php');
lmb_require('limb/dbal/src/drivers/lmbDbStatement.interface.php');

Mock :: generate('lmbDbConnection', 'MockConnection');
Mock :: generate('lmbDbStatement', 'MockStatement');

class lmbSelectQueryTest extends UnitTestCase
{
  var $conn;

  function setUp()
  {
    $this->conn = new ConnectionTestStub();
  }

  function testSelect()
  {
    $sql = new lmbSelectQuery('SELECT * FROM test', $this->conn);

    $this->assertEqual($sql->toString(), 'SELECT * FROM test');
  }

  function testNoFields()
  {
    $sql = new lmbSelectQuery('SELECT %fields% FROM test', $this->conn);

    $this->assertEqual($sql->toString(), 'SELECT * FROM test');
  }

  function testAddFieldWithFields()
  {
    $sql = new lmbSelectQuery("SELECT t3 \n%fields%,t4 FROM test", $this->conn);

    $sql->addField('t1');
    $sql->addField('t2');

    $this->assertEqual($sql->toString(), "SELECT t3 \n,'t1','t2',t4 FROM test");
  }

  function testNoFieldsAdded()
  {
    $sql = new lmbSelectQuery("SELECT t3 \n%fields%,t4 FROM test", $this->conn);

    $this->assertEqual($sql->toString(), "SELECT t3 \n,t4 FROM test");
  }

  function testAddFieldNoFields()
  {
    $sql = new lmbSelectQuery('SELECT %fields% FROM test', $this->conn);

    $sql->addField('t1');
    $sql->addField('t2');

    $this->assertEqual($sql->toString(), "SELECT 't1','t2' FROM test");
  }

  function testAddFieldWithAlias()
  {
    $sql = new lmbSelectQuery('SELECT %fields% FROM test', $this->conn);

    $sql->addField('t1', 'a1');
    $sql->addField('t2', 'a2');

    $this->assertEqual($sql->toString(), "SELECT 't1' as 'a1','t2' as 'a2' FROM test");
  }

  function testNoAddTable()
  {
    $sql = new lmbSelectQuery('SELECT * FROM test %tables%', $this->conn);

    $this->assertEqual($sql->toString(), 'SELECT * FROM test');
  }

  function testAddTable()
  {
    $sql = new lmbSelectQuery("SELECT * FROM test \n\t%tables%", $this->conn);

    $sql->addTable('test2');
    $sql->addTable('test3');

    $this->assertEqual($sql->toString(), "SELECT * FROM test \n\t,'test2','test3'");
  }

  function testAddTableWithAlias()
  {
    $sql = new lmbSelectQuery("SELECT * FROM test \n\t%tables%", $this->conn);

    $sql->addTable('test2', 't2');
    $sql->addTable('test3');

    $this->assertEqual($sql->toString(), "SELECT * FROM test \n\t,'test2' 't2','test3'");
  }

  function testAddLeftJoin()
  {
    $sql = new lmbSelectQuery('SELECT * FROM test %left_join%', $this->conn);

    $sql->addLeftJoin('article', array('test.article_id' => 'article.id'));

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test LEFT JOIN 'article' ON 'test.article_id'='article.id'");
  }

  function testEmptyCondition()
  {
    $sql = new lmbSelectQuery('SELECT * FROM test %where%', $this->conn);

    $this->assertEqual($sql->toString(),
                       'SELECT * FROM test');
  }

  function testAddCondition()
  {
    $sql = new lmbSelectQuery("SELECT * FROM test WHERE \n%where%", $this->conn);

    $sql->addCriteria(new lmbSQLRawCriteria('c1=:c1:'));

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test WHERE \nc1=:c1:");
  }

  function testAddConditionNoWhereClause()
  {
    $sql = new lmbSelectQuery("SELECT * FROM test \n%where%", $this->conn);

    $sql->addCriteria(new lmbSQLRawCriteria('c1=:c1:'));

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test \nWHERE c1=:c1:");
  }

  function testAddConditionNoHint()
  {
    $sql = new lmbSelectQuery("SELECT * FROM test WHERE 1=1", $this->conn);

    $sql->addCriteria(new lmbSQLRawCriteria('c1=:c1:'));

    try
    {
      $sql->toString();
      $this->assertTrue(false);
    }
    catch(lmbException $e){}
  }

  function testAddSeveralConditions()
  {
    $sql = new lmbSelectQuery('SELECT * FROM test %where%', $this->conn);

    $sql->addCriteria(new lmbSQLRawCriteria('c1=:c1:'));
    $sql->addCriteria(new lmbSQLRawCriteria('c2=:c2:'));

    $this->assertEqual($sql->toString(),
                       'SELECT * FROM test WHERE c1=:c1: AND c2=:c2:');
  }

  function testAddConditionToExistingConditions()
  {
    $sql = new lmbSelectQuery("SELECT * FROM test WHERE t1=t1\n %where%", $this->conn);

    $sql->addCriteria(new lmbSQLRawCriteria('c1=:c1:'));
    $sql->addCriteria(new lmbSQLRawCriteria('c2=:c2:'));

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test WHERE t1=t1\n AND c1=:c1: AND c2=:c2:");
  }

  function testAddConditionToExistingConditionsWithOrder()
  {
    $sql = new lmbSelectQuery("SELECT * FROM test WHERE t1=t1\n\n %where% \n\tORDER BY t1", $this->conn);

    $sql->addCriteria(new lmbSQLRawCriteria('c1=:c1:'));
    $sql->addCriteria(new lmbSQLRawCriteria('c2=:c2:'));

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test WHERE t1=t1\n\n AND c1=:c1: AND c2=:c2: \n\tORDER BY t1");
  }

  function testAddConditionToExistingConditionsWithGroup()
  {
    $sql = new lmbSelectQuery("SELECT * FROM test WHERE t1=t1\n\n %where% \n\tGROUP BY t1", $this->conn);

    $sql->addCriteria(new lmbSQLRawCriteria('c1=:c1:'));
    $sql->addCriteria(new lmbSQLRawCriteria('c2=:c2:'));

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test WHERE t1=t1\n\n AND c1=:c1: AND c2=:c2: \n\tGROUP BY t1");
  }

  function testEmptyOrder()
  {
    $sql = new lmbSelectQuery("SELECT * FROM test \n%order%", $this->conn);

    $this->assertEqual($sql->toString(),
                       'SELECT * FROM test');
  }

  function testAddOrderNoOrderClause()
  {
    $sql = new lmbSelectQuery("SELECT * FROM test \n%order%", $this->conn);

    $sql->addOrder('t1');
    $sql->addOrder('t2', 'DESC');

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test \nORDER BY t1 ASC,t2 DESC");
  }

  function testAddOrderWithOrderClause()
  {
    $sql = new lmbSelectQuery("SELECT * FROM test ORDER BY\n %order%", $this->conn);

    $sql->addOrder('t1');
    $sql->addOrder('t2', 'DESC');

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test ORDER BY\n t1 ASC,t2 DESC");
  }

  function testAddOrderWithOrderClause2()
  {
    $sql = new lmbSelectQuery("SELECT * FROM test ORDER BY t0 DESC\n %order%", $this->conn);

    $sql->addOrder('t1');
    $sql->addOrder('t2', 'DESC');

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test ORDER BY t0 DESC\n ,t1 ASC,t2 DESC");
  }

  function testAddOrderWithOrderClause3()
  {
    $sql = new lmbSelectQuery("SELECT * FROM test ORDER BY t0 DESC\n %order%", $this->conn);

    $this->assertEqual($sql->toString(),
                       'SELECT * FROM test ORDER BY t0 DESC');
  }

  function testNoGroupsAdded()
  {
    $sql = new lmbSelectQuery('SELECT * FROM test', $this->conn);

    $this->assertEqual($sql->toString(),
                       'SELECT * FROM test');
  }

  function testNoGroupsAdded2()
  {
    $sql = new lmbSelectQuery("SELECT * FROM test GROUP BY t0 \n%group%", $this->conn);

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test GROUP BY t0");
  }

  function testAddGroupBy()
  {
    $sql = new lmbSelectQuery('SELECT * FROM test %group%', $this->conn);

    $sql->addGroupBy('t1');
    $sql->addGroupBy('t2');

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test GROUP BY 't1','t2'");
  }

  function testAddGroupByWithGroupByClause()
  {
    $sql = new lmbSelectQuery("SELECT * FROM test GROUP BY \n%group%", $this->conn);

    $sql->addGroupBy('t1');
    $sql->addGroupBy('t2');

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test GROUP BY \n't1','t2'");
  }

  function testAddGroupByWithGroupByClause2()
  {
    $sql = new lmbSelectQuery("SELECT * FROM test GROUP BY t0 \n%group%", $this->conn);

    $sql->addGroupBy('t1');
    $sql->addGroupBy('t2');

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test GROUP BY t0 \n,'t1','t2'");
  }

  function testAddHavingNoGroupBy()
  {
    $sql = new lmbSelectQuery("SELECT * FROM test %having%", $this->conn);

    $sql->addHaving(new lmbSQLRawCriteria('c1=:c1:'));

    try
    {
      $sql->toString();
      $this->assertTrue(false);
    }
    catch(lmbException $e){}
  }

  function testAddHaving()
  {
    $sql = new lmbSelectQuery("SELECT * FROM test GROUP BY id %having%", $this->conn);

    $sql->addHaving(new lmbSQLRawCriteria('c1=:c1:'));
    $sql->addHaving(new lmbSQLRawCriteria('c2=:c2:'));

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test GROUP BY id HAVING c1=:c1: AND c2=:c2:");
  }

  function testAddHavingToExistingHaving()
  {
    $sql = new lmbSelectQuery("SELECT * FROM test GROUP BY id HAVING id=1 %having%", $this->conn);

    $sql->addHaving(new lmbSQLRawCriteria('c1=:c1:'));
    $sql->addHaving(new lmbSQLRawCriteria('c2=:c2:'));

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test GROUP BY id HAVING id=1 AND c1=:c1: AND c2=:c2:");
  }

  function testAddHavingWithExistingOrder()
  {
    $sql = new lmbSelectQuery("SELECT * FROM test GROUP BY id HAVING id=1 %having% ORDER BY id", $this->conn);

    $sql->addHaving(new lmbSQLRawCriteria('c1=:c1:'));
    $sql->addHaving(new lmbSQLRawCriteria('c2=:c2:'));

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test GROUP BY id HAVING id=1 AND c1=:c1: AND c2=:c2: ORDER BY id");
  }

  function testGetStatement()
  {
    $conn = new MockConnection();
    $stmt = new MockStatement();

    $conn->expectOnce('newStatement');
    $stmt->expectOnce('set', array('p0t_id', 5));
    $conn->setReturnReference('newStatement', $stmt);

    $sql = new lmbSelectQuery('SELECT * FROM test %where%', $conn);
    $sql->addCriteria(new lmbSQLTableFieldCriteria('t.id', 5));

    $sql->getStatement();
  }
}
?>
