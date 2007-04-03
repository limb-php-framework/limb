<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSQLTableFieldCriteriaTest.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */
require_once('limb/dbal/tests/common.inc.php');
lmb_require('limb/dbal/src/criteria/lmbSQLTableFieldCriteria.class.php');

class lmbSQLTableFieldCriteriaTest extends UnitTestCase
{
  var $conn;

  function setUp()
  {
    $this->conn = new ConnectionTestStub();
  }

  function testEqual()
  {
    $c = new lmbSQLTableFieldCriteria('t.id', 4);

    $values = array();
    $this->assertEqual($c->toStatementString($values, $this->conn), "'t.id'=:p0t_id:");
    $this->assertEqual($values, array('p0t_id' => 4));
  }

  function testNotEqual()
  {
    $c = new lmbSQLTableFieldCriteria('t.id', 4, lmbSQLFieldCriteria::NOT_EQUAL);

    $values = array();
    $this->assertEqual($c->toStatementString($values, $this->conn), "'t.id'<>:p0t_id:");
    $this->assertEqual($values, array('p0t_id' => 4));
  }

  function testGreater()
  {
    $c = new lmbSQLTableFieldCriteria('t.id', 4, lmbSQLFieldCriteria::GREATER);

    $values = array();
    $this->assertEqual($c->toStatementString($values, $this->conn), "'t.id'>:p0t_id:");
    $this->assertEqual($values, array('p0t_id' => 4));
  }

  function testLess()
  {
    $c = new lmbSQLTableFieldCriteria('t.id', 4, lmbSQLFieldCriteria::LESS);

    $values = array();
    $this->assertEqual($c->toStatementString($values, $this->conn), "'t.id'<:p0t_id:");
    $this->assertEqual($values, array('p0t_id' => 4));
  }

  function testGreaterEqual()
  {
    $c = new lmbSQLTableFieldCriteria('t.id', 4, lmbSQLFieldCriteria::GREATER_EQUAL);

    $values = array();
    $this->assertEqual($c->toStatementString($values, $this->conn), "'t.id'>=:p0t_id:");
    $this->assertEqual($values, array('p0t_id' => 4));
  }

  function testLessEqual()
  {
    $c = new lmbSQLTableFieldCriteria('t.id', 4, lmbSQLFieldCriteria::LESS_EQUAL);

    $values = array();
    $this->assertEqual($c->toStatementString($values, $this->conn), "'t.id'<=:p0t_id:");
    $this->assertEqual($values, array('p0t_id' => 4));
  }

  function testIn()
  {
    $c = new lmbSQLTableFieldCriteria('t.id', array(1, 2, 3), lmbSQLFieldCriteria::IN);

    $values = array();
    $this->assertEqual($c->toStatementString($values, $this->conn), "'t.id' IN (:p0_p0t_id:,:p1_p0t_id:,:p2_p0t_id:)");
    $this->assertEqual($values, array('p0_p0t_id' => 1, 'p1_p0t_id' => 2, 'p2_p0t_id' => 3));
  }

  function testNotIn()
  {
    $c = new lmbSQLTableFieldCriteria('t.id', array(1, 2, 3), lmbSQLFieldCriteria::NOT_IN);

    $values = array();
    $this->assertEqual($c->toStatementString($values, $this->conn), "'t.id' NOT IN (:p0_p0t_id:,:p1_p0t_id:,:p2_p0t_id:)");
    $this->assertEqual($values, array('p0_p0t_id' => 1, 'p1_p0t_id' => 2, 'p2_p0t_id' => 3));
  }

  function testIsNull()
  {
    $c = new lmbSQLTableFieldCriteria('t.id', null);
    $values = array();
    $this->assertEqual($c->toStatementString($values, $this->conn), "'t.id' IS null");
  }

  function testIsNotNull()
  {
    $c = new lmbSQLTableFieldCriteria('t.id', null, lmbSQLFieldCriteria::NOT_EQUAL);
    $values = array();
    $this->assertEqual($c->toStatementString($values, $this->conn), "'t.id' IS NOT null");
  }

  function testAnd()
  {
    $c = new lmbSQLTableFieldCriteria('t.id', 4);
    $c->addAnd(new lmbSQLTableFieldCriteria('t.name', 'test'));

    $values = array();
    $this->assertEqual($c->toStatementString($values, $this->conn), "('t.id'=:p0t_id: AND 't.name'=:p1t_name:)");
    $this->assertEqual($values, array('p0t_id' => 4, 'p1t_name' => 'test'));
  }

  function testOr()
  {
    $c = new lmbSQLTableFieldCriteria('t.id', 4);
    $c->addOr(new lmbSQLTableFieldCriteria('t.id', 5));

    $values = array();
    $this->assertEqual($c->toStatementString($values, $this->conn), "('t.id'=:p0t_id: OR 't.id'=:p1t_id:)");
    $this->assertEqual($values, array('p0t_id' => 4, 'p1t_id' => 5));
  }

  function testException()
  {
    try
    {
      $c = new lmbSQLTableFieldCriteria('id', 4);
      $this->assertTrue(false);
    }
    catch(lmbException $e){}
  }
}

?>
