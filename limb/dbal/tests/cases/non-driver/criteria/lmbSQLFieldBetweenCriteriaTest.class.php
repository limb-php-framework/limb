<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSQLFieldBetweenCriteriaTest.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */
require_once('limb/dbal/tests/common.inc.php');
lmb_require('limb/dbal/src/criteria/lmbSQLFieldBetweenCriteria.class.php');

class lmbSQLFieldBetweenCriteriaTest extends UnitTestCase
{
  var $conn;
  var $query;

  function setUp()
  {
    $this->conn = new ConnectionTestStub();
  }

  function testSimple()
  {
    $c1 = new lmbSQLFieldBetweenCriteria('a', 1, 10);

    $this->assertEqual($c1->toStatementString($values, $this->conn),
                       "'a' BETWEEN :pfa0: AND :pta0:");

    $this->assertEqual($values, array('pfa0' => 1,
                                      'pta0' => 10));
  }

  function _testNested()
  {
    $c1 = new lmbSQLFieldBetweenCriteria('a', 1, 10);
    $c2 = new lmbSQLFieldBetweenCriteria('a', 20, 30);
    $c1->addOr($c2);

    $this->assertEqual($c1->toStatementString($values, $this->conn),
                       "('a' BETWEEN :pfa0: AND :pta0: OR a BETWEEN :pfa1: AND :pta1:)");

    $this->assertEqual($values, array('pfa0' => 1,
                                      'pta0' => 10,
                                      'pfa1' => 20,
                                      'pta1' => 30));
  }
}

?>
