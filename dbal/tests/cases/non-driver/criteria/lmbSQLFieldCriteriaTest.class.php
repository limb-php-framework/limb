<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
require_once('limb/dbal/tests/common.inc.php');
lmb_require('limb/dbal/src/query/lmbSelectRawQuery.class.php');
lmb_require('limb/dbal/src/criteria/lmbSQLFieldCriteria.class.php');

class lmbSQLFieldCriteriaTest extends UnitTestCase
{
  var $conn;
  var $query;

  function setUp()
  {
    $this->conn = new ConnectionTestStub();
    $this->query = new lmbSelectRawQuery('SELECT * FROM any_table WHERE %where%', $this->conn);
  }

  function testEqual()
  {
    $c = new lmbSQLFieldCriteria('count', 4);

    $values = array();
    $this->assertEqual($c->toStatementString($values, $this->conn), "'count'=:p0count:");
    $this->assertEqual($values, array('p0count' => 4));
  }

  function testNotEqual()
  {
    $c = new lmbSQLFieldCriteria('count', 4, lmbSQLFieldCriteria::NOT_EQUAL);

    $values = array();
    $this->assertEqual($c->toStatementString($values, $this->conn), "'count'<>:p0count:");
    $this->assertEqual($values, array('p0count' => 4));
  }

  function testGreater()
  {
    $c = new lmbSQLFieldCriteria('count', 4, lmbSQLFieldCriteria::GREATER);

    $values = array();
    $this->assertEqual($c->toStatementString($values, $this->conn), "'count'>:p0count:");
    $this->assertEqual($values, array('p0count' => 4));
  }

  function testLess()
  {
    $c = new lmbSQLFieldCriteria('count', 4, lmbSQLFieldCriteria::LESS);

    $values = array();
    $this->assertEqual($c->toStatementString($values, $this->conn), "'count'<:p0count:");
    $this->assertEqual($values, array('p0count' => 4));
  }

  function testGreaterEqual()
  {
    $c = new lmbSQLFieldCriteria('count', 4, lmbSQLFieldCriteria::GREATER_EQUAL);

    $values = array();
    $this->assertEqual($c->toStatementString($values, $this->conn), "'count'>=:p0count:");
    $this->assertEqual($values, array('p0count' => 4));
  }

  function testLessEqual()
  {
    $c = new lmbSQLFieldCriteria('count', 4, lmbSQLFieldCriteria::LESS_EQUAL);

    $values = array();
    $this->assertEqual($c->toStatementString($values, $this->conn), "'count'<=:p0count:");
    $this->assertEqual($values, array('p0count' => 4));
  }

  function testIn()
  {
    $c = new lmbSQLFieldCriteria('count', array(1, 2, 3), lmbSQLFieldCriteria::IN);

    $values = array();
    $this->assertEqual($c->toStatementString($values, $this->conn), "'count' IN (:p0_p0count:,:p1_p0count:,:p2_p0count:)");
    $this->assertEqual($values, array('p0_p0count' => 1, 'p1_p0count' => 2, 'p2_p0count' => 3));
  }

  function testInWithCriteriaValuesAsKeys()
  {
    $c = new lmbSQLFieldCriteria('count', array(1 => 1, 2 => 2, 3 => 3), lmbSQLFieldCriteria::IN);

    $values = array();
    $this->assertEqual($c->toStatementString($values, $this->conn), "'count' IN (:p1_p0count:,:p2_p0count:,:p3_p0count:)");
    $this->assertEqual($values, array('p1_p0count' => 1, 'p2_p0count' => 2, 'p3_p0count' => 3));
  }

  function testNotIn()
  {
    $c = new lmbSQLFieldCriteria('count', array(1, 2, 3), lmbSQLFieldCriteria::NOT_IN);

    $values = array();
    $this->assertEqual($c->toStatementString($values, $this->conn), "'count' NOT IN (:p0_p0count:,:p1_p0count:,:p2_p0count:)");
    $this->assertEqual($values, array('p0_p0count' => 1, 'p1_p0count' => 2, 'p2_p0count' => 3));
  }

  function testIsNull()
  {
    $c = new lmbSQLFieldCriteria('count', null);
    $values = array();
    $this->assertEqual($c->toStatementString($values, $this->conn), "'count' IS NULL");
  }

  function testIsNotNull()
  {
    $c = new lmbSQLFieldCriteria('count', null, lmbSQLFieldCriteria::NOT_EQUAL);
    $values = array();
    $this->assertEqual($c->toStatementString($values, $this->conn), "'count' IS NOT NULL");
  }

  function testAnd()
  {
    $c = new lmbSQLFieldCriteria('count', 4);
    $c->addAnd(new lmbSQLFieldCriteria('count2', 'test'));

    $values = array();
    $this->assertEqual($c->toStatementString($values, $this->conn), "'count'=:p0count: AND 'count2'=:p1count2:");
    $this->assertEqual($values, array('p0count' => 4, 'p1count2' => 'test'));
  }

  function testOr()
  {
    $c = new lmbSQLFieldCriteria('count', 4);
    $c->addOr(new lmbSQLFieldCriteria('count', 5));

    $values = array();
    $this->assertEqual($c->toStatementString($values, $this->conn), "'count'=:p0count: OR 'count'=:p1count:");
    $this->assertEqual($values, array('p0count' => 4, 'p1count' => 5));
  }

  function testNestedCriterias()
  {
    $c1 = new lmbSQLFieldCriteria('name', "Leo");
    $c2 = new lmbSQLFieldCriteria('last_name',
                          array("Tolstoy", "Dostoevsky", "Bakhtin"), lmbSQLFieldCriteria::IN);
    $c3 = new lmbSQLFieldCriteria('age', 36);

    $c1->addOr($c2->addAnd($c3));

    $this->assertEqual($c1->toStatementString($values, $this->conn),
                       "'name'=:p0name: OR ('last_name' IN (:p0_p1last_name:,:p1_p1last_name:,:p2_p1last_name:) AND 'age'=:p4age:)");

    $this->assertEqual($values, array('p0name' => "Leo",
                                      'p0_p1last_name' => "Tolstoy",
                                      'p1_p1last_name' => "Dostoevsky",
                                      'p2_p1last_name' => "Bakhtin",
                                      'p4age' => 36));
  }

  function testNestedCriteriasOneField()
  {
    $c1 = new lmbSQLFieldCriteria('name', "Leo");
    $c2 = new lmbSQLFieldCriteria('name', "Ivan");
    $c1->addOr($c2);

    $this->assertEqual($c1->toStatementString($values, $this->conn),
                       "'name'=:p0name: OR 'name'=:p1name:");

    $this->assertEqual($values, array('p0name' => "Leo",
                                      'p1name' => "Ivan"));
  }

}


