<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/dbal/src/criteria/lmbSQLCriteria.class.php');

class lmbSQLCriteriaTest extends UnitTestCase
{
  function testBuildCriteriaFromString()
  {
    $c = new lmbSQLCriteria('a=1');

    $this->assertEqual($c->toStatementString(), 'a=1');
  }

  function testBuildCriteriaFromStringWithNamedValues()
  {
    $c = new lmbSQLCriteria('a=:id:', array('id' => 2));

    $this->assertEqual($c->toStatementString($values), 'a=:id:');
    $this->assertEqual($values, array('id' => 2));
  }

  function testBuildCriteriaFromStringWithNonNamedValues()
  {
    $c = new lmbSQLCriteria('a=? OR b=?', array(2, 3));

    $str = $c->toStatementString($values);

    $key1 = key($values);
    next($values);
    $key2 = key($values);

    $this->assertEqual($str, 'a=:' . $key1 . ': OR b=:' . $key2 . ':');

    $this->assertEqual($values[$key1], 2);
    $this->assertEqual($values[$key2], 3);
  }

  function testAndChaining()
  {
    $a = new lmbSQLCriteria('a');
    $b = new lmbSQLCriteria('b');
    $a->addAnd($b);
    $this->assertEqual($a->toStatementString(), 'a AND b');
  }

  function testAndChainingViaAdd()
  {
    $a = new lmbSQLCriteria('a');
    $b = new lmbSQLCriteria('b');
    $a->add($b);
    $this->assertEqual($a->toStatementString(), 'a AND b');
  }

  function testOrChaining()
  {
    $a = new lmbSQLCriteria('a');
    $b = new lmbSQLCriteria('b');
    $a->addOr($b);
    $this->assertEqual($a->toStatementString(), 'a OR b');
  }

  function testComplexCriteriaIsSurroundedWithParenthesis()
  {
    $a = new lmbSQLCriteria('a');
    $b = new lmbSQLCriteria('b');
    $c = new lmbSQLCriteria('c');
    $a->add($b->add($c));
    $this->assertEqual($a->toStatementString(), 'a AND (b AND c)');
  }

  function testComplexChaining()
  {
    $a = new lmbSQLCriteria('a');
    $b = new lmbSQLCriteria('b');
    $c = new lmbSQLCriteria('c');
    $d = new lmbSQLCriteria('d');
    $e = new lmbSQLCriteria('e');
    $g = new lmbSQLCriteria('g');
    $h = new lmbSQLCriteria('h');
    $a->addOr($b->addAnd($c))->addAnd($d->addOr($e)->addAnd($g->addOr($h)));
    $this->assertEqual($a->toStatementString(), 'a OR (b AND c) AND (d OR e AND (g OR h))');
  }

  function testCreate()
  {
    $criteria = lmbSQLCriteria :: create('2 = 2');
    $this->assertEqual($criteria->toStatementString(), '2 = 2');
  }

  function testEmptyCriteriaChainingIsSafe()
  {
    $c = lmbSQLCriteria :: create()->add(new lmbSQLCriteria());
    $this->assertEqual($c->toStatementString(), '1 = 1 AND 1 = 1');
  }

  function testNot()
  {
    $a = new lmbSQLCriteria('a');
    $a->not();
    $this->assertEqual($a->toStatementString(), 'NOT(a)');
  }

  function testToggleNot()
  {
    $a = new lmbSQLCriteria('a');
    $a->not()->not();
    $this->assertEqual($a->toStatementString(), 'a');
  }

  function testNotWithChaining()
  {
    $a = new lmbSQLCriteria('a');
    $b = new lmbSQLCriteria('b');
    $a->not()->add($b);
    $this->assertEqual($a->toStatementString(), 'NOT(a) AND b');
  }

  function testNotAll()
  {
    $a = new lmbSQLCriteria('a');
    $a->notAll();
    $this->assertEqual($a->toStatementString(), 'NOT(a)');
  }

  function testToggleNotAll()
  {
    $a = new lmbSQLCriteria('a');
    $a->notAll()->notAll();
    $this->assertEqual($a->toStatementString(), 'a');
  }

  function testNotAllWithChaining()
  {
    $a = new lmbSQLCriteria('a');
    $b = new lmbSQLCriteria('b');
    $a->notAll()->add($b);
    $this->assertEqual($a->toStatementString(), 'NOT(a AND b)');
  }

  function testNotAllAndNotAtTheSameTime()
  {
    $a = new lmbSQLCriteria('a');
    $b = new lmbSQLCriteria('b');
    $a->notAll()->not()->add($b);
    $this->assertEqual($a->toStatementString(), 'NOT(NOT(a) AND b)');
  }

  function testObjectifyString()
  {
    $criteria = lmbSQLCriteria :: objectify("id = 1");
    $this->assertEqual($criteria->toStatementString(), "id = 1");
  }

  function testObjectifyObject()
  {
    $criteria = lmbSQLCriteria :: objectify(new lmbSQLCriteria("id = 1"));
    $this->assertEqual($criteria->toStatementString(), "id = 1");
  }

  function testObjectifyNull()
  {
    $criteria = lmbSQLCriteria :: objectify(null);
    $this->assertEqual($criteria->toStatementString(), "1 = 1");
  }

  function testObjectifyNotSupportedArrayFormatException()
  {
    try
    {
      //actually this format could be useful as well...
      $criteria = lmbSQLCriteria :: objectify(array('id' => 1));
      $this->assertTrue(false);
    }
    catch(lmbDbException $e){}
  }

  function testPassRawCriteriaToConstructor()
  {
    $criteria = new lmbSQLCriteria('2 = 2');
    $this->assertEqual($criteria->toStatementString(), '2 = 2');
  }

  function testBetween()
  {
    $criteria = lmbSQLCriteria :: between('id', 1, 100);
    $this->assertEqual($criteria, new lmbSQLFieldBetweenCriteria('id', 1, 100));
  }

  function testIn()
  {
    $criteria = lmbSQLCriteria :: in('id', array(1, 2));
    $this->assertEqual($criteria, new lmbSQLFieldCriteria('id', array(1, 2), lmbSQLFieldCriteria :: IN));
  }

  function testInWithArrayProcessor()
  {
    $criteria = lmbSQLCriteria :: in('id', array("10foo", "20bar"), 'intval');
    $this->assertEqual($criteria, new lmbSQLFieldCriteria('id', array(10, 20), lmbSQLFieldCriteria :: IN));
  }

  function testEqual()
  {
    $criteria = lmbSQLCriteria :: equal('id', 1);
    $this->assertEqual($criteria, new lmbSQLFieldCriteria('id', 1, lmbSQLFieldCriteria :: EQUAL));
  }

  function testLike()
  {
    $criteria = lmbSQLCriteria :: like('id', '%foo%');
    $this->assertEqual($criteria, new lmbSQLFieldCriteria('id', '%foo%', lmbSQLFieldCriteria :: LIKE));
  }

  function testIsNull()
  {
    $criteria = lmbSQLCriteria :: isNull('id');
    $this->assertEqual($criteria, new lmbSQLFieldCriteria('id', null, lmbSQLFieldCriteria :: IS_NULL));
  }

  function testGreater()
  {
    $criteria = lmbSQLCriteria :: greater('id', 11);
    $this->assertEqual($criteria, new lmbSQLFieldCriteria('id', 11, lmbSQLFieldCriteria :: GREATER));
  }

  function testLess()
  {
    $criteria = lmbSQLCriteria :: less('id', 12);
    $this->assertEqual($criteria, new lmbSQLFieldCriteria('id', 12, lmbSQLFieldCriteria :: LESS));
  }
}


