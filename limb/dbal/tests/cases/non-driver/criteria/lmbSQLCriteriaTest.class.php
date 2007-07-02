<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/dbal/src/criteria/lmbSQLCriteria.class.php');

class lmbSQLCriteriaTest extends UnitTestCase
{
  function testObjectifyString()
  {
    $criteria = lmbSQLCriteria :: objectify("id = 1");
    $this->assertEqual($criteria->toStatementString(), "id = 1");
  }

  function testObjectifyObject()
  {
    $criteria = lmbSQLCriteria :: objectify(new lmbSQLRawCriteria("id = 1"));
    $this->assertEqual($criteria->toStatementString(), "id = 1");
  }

  function testObjectifyNull()
  {
    $criteria = lmbSQLCriteria :: objectify(null);
    $this->assertEqual($criteria->toStatementString(), "1 = 1");
  }

  function testPassRawCriteriaToConstructor()
  {
    $criteria = new lmbSQLCriteria('2 = 2');
    $this->assertEqual($criteria->toStatementString(), '2 = 2');
  }

  function testCreate()
  {
    $criteria = lmbSQLCriteria :: create('2 = 2');
    $this->assertEqual($criteria->toStatementString(), '2 = 2');
  }

  function testNot()
  {
    $criteria = lmbSQLCriteria :: not(new lmbSQLRawCriteria("id = 1"));
    $this->assertEqual($criteria->toStatementString(), "!(id = 1)");
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
}

?>
