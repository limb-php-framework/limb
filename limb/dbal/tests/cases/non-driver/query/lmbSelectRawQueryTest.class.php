<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/dbal/tests/common.inc.php');
lmb_require('limb/dbal/src/criteria/lmbSQLRawCriteria.class.php');
lmb_require('limb/dbal/src/criteria/lmbSQLTableFieldCriteria.class.php');
lmb_require('limb/dbal/src/query/lmbSelectRawQuery.class.php');
lmb_require('limb/dbal/src/drivers/lmbDbConnection.interface.php');
lmb_require('limb/dbal/src/drivers/lmbDbStatement.interface.php');

Mock :: generate('lmbDbConnection', 'MockConnection');
Mock :: generate('lmbDbStatement', 'MockStatement');

class lmbSelectRawQueryTest extends UnitTestCase
{
  protected $conn;

  function setUp()
  {
    //this stub uses ' quoting for simpler testing
    $this->conn = new ConnectionTestStub();
  }

  function testSimpleSelect()
  {
    $sql = new lmbSelectRawQuery('SELECT * FROM test', $this->conn);

    $this->assertEqual($sql->toString(), 'SELECT * FROM test');
  }

  function testReplaceFieldsHintByDefault()
  {
    $sql = new lmbSelectRawQuery('SELECT %fields% FROM test', $this->conn);
    $this->assertEqual($sql->toString(), 'SELECT * FROM test');
  }

  function testReplaceFieldsHintWhenFieldsExistInTemplate()
  {
    $sql = new lmbSelectRawQuery("SELECT t3 \n%fields%,t4 FROM test", $this->conn);

    $this->assertEqual($sql->toString(), "SELECT t3 \n,t4 FROM test");
  }

  function testAddFieldWhenNoFieldsExistInTemplate()
  {
    $sql = new lmbSelectRawQuery('SELECT %fields% FROM test', $this->conn);

    $sql->addField('t1');
    $sql->addField('t2');

    $this->assertEqual($sql->toString(), "SELECT 't1','t2' FROM test");
  }

  function testAddRawFieldWhenNoFieldsExistInTemplate()
  {
    $sql = new lmbSelectRawQuery('SELECT %fields% FROM test', $this->conn);

    $sql->addRawField('t1');
    $sql->addRawField('t2');

    $this->assertEqual($sql->toString(), "SELECT t1,t2 FROM test");
  }

  function testAddFieldWhenFieldsExistInTemplate()
  {
    $sql = new lmbSelectRawQuery("SELECT t3 \n%fields%,t4 FROM test", $this->conn);

    $sql->addField('t1');
    $sql->addField('t2');

    $this->assertEqual($sql->toString(), "SELECT t3 \n,'t1','t2',t4 FROM test");
  }

  function testAddRawFieldWhenFieldsExistInTemplate()
  {
    $sql = new lmbSelectRawQuery("SELECT t3 \n%fields%,t4 FROM test", $this->conn);

    $sql->addRawField('t1');
    $sql->addRawField('t2');

    $this->assertEqual($sql->toString(), "SELECT t3 \n,t1,t2,t4 FROM test");
  }

  function testAddFieldWithAlias()
  {
    $sql = new lmbSelectRawQuery('SELECT %fields% FROM test', $this->conn);

    $sql->addField('t1', 'a1');
    $sql->addField('t2', 'a2');

    $this->assertEqual($sql->toString(), "SELECT 't1' as 'a1','t2' as 'a2' FROM test");
  }
  
  function testAddRawFieldWithAlias()
  {
    $sql = new lmbSelectRawQuery('SELECT %fields% FROM test', $this->conn);

    $sql->addRawField('t1', 'a1');
    $sql->addRawField('t2', 'a2');

    $this->assertEqual($sql->toString(), "SELECT t1 as a1,t2 as a2 FROM test");
  }

  function testMixAddingRawAndRegularFields()
  {
    $sql = new lmbSelectRawQuery("SELECT %fields% FROM test", $this->conn);

    $sql->addRawField('t1');
    $sql->addField('t2', 'a2');
    $sql->addRawField('t3', 'a3');
    $sql->addField('t4');

    $this->assertEqual($sql->toString(), "SELECT 't2' as 'a2','t4',t1,t3 as a3 FROM test");
  }
  
  function testMixAddingRawAndRegularFieldsWhenFieldsExistInTemplate()
  {
    $sql = new lmbSelectRawQuery("SELECT a \n%fields%,b FROM test", $this->conn);

    $sql->addRawField('t1');
    $sql->addField('t2', 'a2');
    $sql->addRawField('t3', 'a3');
    $sql->addField('t4');

    $this->assertEqual($sql->toString(), "SELECT a \n,'t2' as 'a2','t4',t1,t3 as a3,b FROM test");
  }
  
  function testAddStarredFieldFromTable()
  {
    $sql = new lmbSelectRawQuery('SELECT %fields% FROM test', $this->conn);
    $sql->addField('t1.*');

    $this->assertEqual($sql->toString(), 'SELECT t1.* FROM test');
  }

  function testReplaceTableHintInTemplate()
  {
    $sql = new lmbSelectRawQuery('SELECT * FROM test %tables%', $this->conn);

    $this->assertEqual($sql->toString(), 'SELECT * FROM test');
  }

  function testAddTable()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test \n\t%tables%", $this->conn);

    $sql->addTable('test2');
    $sql->addTable('test3');

    $this->assertEqual($sql->toString(), "SELECT * FROM test \n\t,'test2','test3'");
  }

  function testAddTableWithAlias()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test \n\t%tables%", $this->conn);

    $sql->addTable('test2', 't2');
    $sql->addTable('test3');

    $this->assertEqual($sql->toString(), "SELECT * FROM test \n\t,'test2' 't2','test3'");
  }

  function testAddSameTable()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test \n\t%tables%", $this->conn);

    $sql->addTable('test2', 'a');
    $sql->addTable('test2', 'b');

    $this->assertEqual($sql->toString(), "SELECT * FROM test \n\t,'test2' 'a','test2' 'b'");
  }

  function testAddLeftJoin()
  {
    $sql = new lmbSelectRawQuery('SELECT * FROM test %left_join%', $this->conn);

    $sql->addLeftJoin('article', 'id', 'test', 'article_id');

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test LEFT JOIN 'article' ON 'article.id'='test.article_id'");
  }

  function testAddLeftJoinTwiceForTheSameTable()
  {
    $sql = new lmbSelectRawQuery('SELECT * FROM test %left_join%', $this->conn);

    $sql->addLeftJoin('article', 'id', 'test', 'article_id');
    $sql->addLeftJoin('article', 'id', 'test', 'other_article_id', 'next_article');

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test LEFT JOIN 'article' ON 'article.id'='test.article_id'".
                       " LEFT JOIN 'article' AS 'next_article' ON 'next_article.id'='test.other_article_id'");
  }
  
  function testEmptyCondition()
  {
    $sql = new lmbSelectRawQuery('SELECT * FROM test %where%', $this->conn);

    $this->assertEqual($sql->toString(),
                       'SELECT * FROM test');
  }

  function testAddCondition()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test WHERE \n%where%", $this->conn);

    $sql->addCriteria(new lmbSQLRawCriteria('c1=:c1:'));

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test WHERE \nc1=:c1:");
  }

  function testAddConditionNoWhereClause()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test \n%where%", $this->conn);

    $sql->addCriteria(new lmbSQLRawCriteria('c1=:c1:'));

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test \nWHERE c1=:c1:");
  }

  function testAddConditionNoHintThrowsException()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test WHERE 1=1", $this->conn);

    $sql->addCriteria(new lmbSQLRawCriteria('c1=:c1:'));
    try
    {
      $sql->toString();
      $this->assertTrue(false);
    }
    catch(lmbException $e)
    {
      $this->assertTrue(true);
    }
  }

  function testAddSeveralConditions()
  {
    $sql = new lmbSelectRawQuery('SELECT * FROM test %where%', $this->conn);

    $sql->addCriteria(new lmbSQLRawCriteria('c1=:c1:'));
    $sql->addCriteria(new lmbSQLRawCriteria('c2=:c2:'));

    $this->assertEqual($sql->toString(),
                       'SELECT * FROM test WHERE c1=:c1: AND c2=:c2:');
  }

  function testAddConditionToExistingConditions()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test WHERE t1=t1\n %where%", $this->conn);

    $sql->addCriteria(new lmbSQLRawCriteria('c1=:c1:'));
    $sql->addCriteria(new lmbSQLRawCriteria('c2=:c2:'));

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test WHERE t1=t1\n AND c1=:c1: AND c2=:c2:");
  }

  function testAddConditionToExistingConditionsWithOrder()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test WHERE t1=t1\n\n %where% \n\tORDER BY t1", $this->conn);

    $sql->addCriteria(new lmbSQLRawCriteria('c1=:c1:'));
    $sql->addCriteria(new lmbSQLRawCriteria('c2=:c2:'));

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test WHERE t1=t1\n\n AND c1=:c1: AND c2=:c2: \n\tORDER BY t1");
  }

  function testAddConditionToExistingConditionsWithGroup()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test WHERE t1=t1\n\n %where% \n\tGROUP BY t1", $this->conn);

    $sql->addCriteria(new lmbSQLRawCriteria('c1=:c1:'));
    $sql->addCriteria(new lmbSQLRawCriteria('c2=:c2:'));

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test WHERE t1=t1\n\n AND c1=:c1: AND c2=:c2: \n\tGROUP BY t1");
  }

  function testEmptyOrder()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test \n%order%", $this->conn);

    $this->assertEqual($sql->toString(),
                       'SELECT * FROM test');
  }

  function testAddOrderNoOrderClause()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test \n%order%", $this->conn);

    $sql->addOrder('t1');
    $sql->addOrder('t2', 'DESC');

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test \nORDER BY 't1' ASC,'t2' DESC");
  }

  function testAddRawOrderNoOrderClause()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test \n%order%", $this->conn);

    $sql->addRawOrder('rand1()');
    $sql->addRawOrder('rand2()');

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test \nORDER BY rand1(),rand2()");
  }

  function testAddOrderAsArray()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test \n%order%", $this->conn);

    $sql->addOrder(array('t1' => 'ASC', 't2' => 'DESC'));
    $this->assertEqual($sql->toString(), "SELECT * FROM test \nORDER BY 't1' ASC,'t2' DESC");
  }
  
  function testAddOrderWithOrderClause()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test ORDER BY\n %order%", $this->conn);

    $sql->addOrder('t1');
    $sql->addOrder('t2', 'DESC');

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test ORDER BY\n 't1' ASC,'t2' DESC");
  }

  function testAddRawOrderWithOrderClause()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test ORDER BY\n %order%", $this->conn);

    $sql->addRawOrder('rand1()');
    $sql->addRawOrder('rand2()');

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test ORDER BY\n rand1(),rand2()");
  }

  function testAddOrderWithOrderClause2()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test ORDER BY t0 DESC\n %order%", $this->conn);

    $sql->addOrder('t1');
    $sql->addOrder('t2', 'DESC');

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test ORDER BY t0 DESC\n ,'t1' ASC,'t2' DESC");
  }

  function testAddOrderWithOrderClause3()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test ORDER BY t0 DESC\n %order%", $this->conn);

    $this->assertEqual($sql->toString(),
                       'SELECT * FROM test ORDER BY t0 DESC');
  }

  function testMixRawAndRegularOrder()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test \n%order%", $this->conn);

    $sql->addRawOrder('rand1()');
    $sql->addOrder('t1');
    $sql->addRawOrder('rand2()');
    $sql->addOrder('t2', 'DESC');

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test \nORDER BY rand1(),'t1' ASC,rand2(),'t2' DESC");
  }


  function testNoGroupsAdded()
  {
    $sql = new lmbSelectRawQuery('SELECT * FROM test', $this->conn);

    $this->assertEqual($sql->toString(),
                       'SELECT * FROM test');
  }

  function testNoGroupsAdded2()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test GROUP BY t0 \n%group%", $this->conn);

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test GROUP BY t0");
  }

  function testAddGroupBy()
  {
    $sql = new lmbSelectRawQuery('SELECT * FROM test %group%', $this->conn);

    $sql->addGroupBy('t1');
    $sql->addGroupBy('t2');

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test GROUP BY 't1','t2'");
  }

  function testAddGroupByWithGroupByClause()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test GROUP BY \n%group%", $this->conn);

    $sql->addGroupBy('t1');
    $sql->addGroupBy('t2');

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test GROUP BY \n't1','t2'");
  }

  function testAddGroupByWithGroupByClause2()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test GROUP BY t0 \n%group%", $this->conn);

    $sql->addGroupBy('t1');
    $sql->addGroupBy('t2');

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test GROUP BY t0 \n,'t1','t2'");
  }

  function testAddHavingNoGroupBy()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test %having%", $this->conn);

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
    $sql = new lmbSelectRawQuery("SELECT * FROM test GROUP BY id %having%", $this->conn);

    $sql->addHaving(new lmbSQLRawCriteria('c1=:c1:'));
    $sql->addHaving(new lmbSQLRawCriteria('c2=:c2:'));

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test GROUP BY id HAVING c1=:c1: AND c2=:c2:");
  }

  function testAddHavingToExistingHaving()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test GROUP BY id HAVING id=1 %having%", $this->conn);

    $sql->addHaving(new lmbSQLRawCriteria('c1=:c1:'));
    $sql->addHaving(new lmbSQLRawCriteria('c2=:c2:'));

    $this->assertEqual($sql->toString(),
                       "SELECT * FROM test GROUP BY id HAVING id=1 AND c1=:c1: AND c2=:c2:");
  }

  function testAddHavingWithExistingOrder()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test GROUP BY id HAVING id=1 %having% ORDER BY id", $this->conn);

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

    $sql = new lmbSelectRawQuery('SELECT * FROM test %where%', $conn);
    $sql->addCriteria(new lmbSQLFieldCriteria('t.id', 5));

    $sql->getStatement();
  }

  function testChaining()
  {
    $sql = new lmbSelectRawQuery($this->conn);
    $string = $sql->from('test')->
              from('test', 'test2')->
              field('foo', 'f')->
              join('test', 'id', 'test2', 'id')->
              order('id', 'desc')->
              group('id')->
              having('id=1')->
              where('id=2')->
              toString();

   $this->assertEqual($string,
                      "SELECT 'foo' as 'f' FROM 'test','test' 'test2' " .
                      "LEFT JOIN 'test' ON 'test.id'='test2.id' " .
                      "WHERE id=2 GROUP BY 'id' HAVING id=1 ORDER BY 'id' desc");
  }

  function testQueryWithoutWhere()
  {
    $sql = new lmbSelectRawQuery('SELECT 2', lmbToolkit :: instance()->getDefaultDbConnection());
    $rs = $sql->fetch();
    $this->assertEqual($rs->count(), 1);
  }

  function testQueryWithoutWhereUsingDefaultConnection()
  {
    $sql = new lmbSelectRawQuery('SELECT 1=1');
    $rs = $sql->fetch();
    $this->assertEqual($rs->count(), 1);
  }

  function testQueryWithLimit()
  {
    $sql = new lmbSelectRawQuery('SELECT %fields% FROM %tables% %left_join% %where% %group% %having% %order% LIMIT 10',
                                 $this->conn);

    $string = $sql->addTable('test_db_table')->field('id')->where('id=2')->toString();
    $this->assertEqual($string, "SELECT 'id' FROM 'test_db_table'  WHERE id=2    LIMIT 10");
  }
  
  function testThrowExceptionOnActionWIthNotExistingPlaholder()
  {
    $sql = new lmbSelectRawQuery("SELECT * FROM test ", $this->conn);

    $sql->addTable('test2');
    $sql->addTable('test3');

    try
    {
      $sql->toString();
      $this->assertTrue(false, 'An exception should be thrown');
    }
    catch(lmbException $e)
    {
      $this->assertTrue(true);
    }
  }
  
}
