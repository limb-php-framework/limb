<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSQLCompositeCriteriaTest.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */
lmb_require('limb/dbal/src/criteria/lmbSQLCompositeCriteria.class.php');
lmb_require('limb/dbal/src/criteria/lmbSQLRawCriteria.class.php');

class lmbSQLCompositeCriteriaTest extends UnitTestCase
{
  function testEmpty()
  {
    $c = new lmbSQLCompositeCriteria();
    $this->assertEqual($c->toStatementString($values), '');
  }

  function testOneCriteria()
  {
    $c = new lmbSQLCompositeCriteria();
    $c->addAnd(new lmbSQLRawCriteria('a=1'));

    $values = array();
    $this->assertEqual($c->toStatementString($values), 'a=1');

    $c = new lmbSQLCompositeCriteria();
    $c->addOr(new lmbSQLRawCriteria('a=1'));

    $values = array();
    $this->assertEqual($c->toStatementString($values), 'a=1');
  }

  function testFirstConjunctionIsOmmitted()
  {
    $c = new lmbSQLCompositeCriteria();
    $c->addAnd(new lmbSQLRawCriteria('a=1'));
    $c->addAnd(new lmbSQLRawCriteria('b=2'));
    $c->addOr(new lmbSQLRawCriteria('c=3'));

    $values = array();
    $this->assertEqual($c->toStatementString($values), '(a=1 AND b=2 OR c=3)');
  }

  function testNestedCriterias()
  {
    $c = new lmbSQLCompositeCriteria();
    $c1 = new lmbSQLRawCriteria('a=1');
    $c2 = new lmbSQLRawCriteria('b=2');
    $c2->addOr(new lmbSQLRawCriteria('c=3'));

    $c->addAnd($c1);
    $c->addAnd($c2);

    $values = array();
    $this->assertEqual($c->toStatementString($values), '(a=1 AND (b=2 OR c=3))');
  }
}

?>
