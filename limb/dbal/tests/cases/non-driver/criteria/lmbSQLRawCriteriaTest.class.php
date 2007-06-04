<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSQLRawCriteriaTest.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */
lmb_require('limb/dbal/src/criteria/lmbSQLRawCriteria.class.php');

class lmbSQLRawCriteriaTest extends UnitTestCase
{
  function testBuildCriteriaFromString()
  {
    $c = new lmbSQLRawCriteria('a=1');

    $this->assertEqual($c->toStatementString(), 'a=1');
  }

  function testBuildCriteriaFromStringWithNamedValues()
  {
    $c = new lmbSQLRawCriteria('a=:id:', array('id' => 2));

    $this->assertEqual($c->toStatementString($values), 'a=:id:');
    $this->assertEqual($values, array('id' => 2));
  }

  function testBuildCriteriaFromStringWithNonNamedValues()
  {
    $c = new lmbSQLRawCriteria('a=? OR b=?', array(2, 3));

    $str = $c->toStatementString($values);

    $key1 = key($values);
    next($values);
    $key2 = key($values);

    $this->assertEqual($str, 'a=:' . $key1 . ': OR b=:' . $key2 . ':');

    $this->assertEqual($values[$key1], 2);
    $this->assertEqual($values[$key2], 3);
  }

  function testNesting()
  {
    $c1 = new lmbSQLRawCriteria('a=1');
    $c2 = new lmbSQLRawCriteria('b=2');
    $c3 = new lmbSQLRawCriteria('c=3');

    $c1->addOr($c2);
    $c2->addAnd($c3);

    $this->assertEqual($c1->toStatementString(), '(a=1 OR (b=2 AND c=3))');
  }
}

?>
