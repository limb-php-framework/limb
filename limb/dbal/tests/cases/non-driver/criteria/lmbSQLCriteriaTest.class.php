<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/dbal/src/criteria/lmbSQLCriteria.class.php');
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
}

?>
