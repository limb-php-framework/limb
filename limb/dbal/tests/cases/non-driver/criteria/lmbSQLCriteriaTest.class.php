<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSQLCriteriaTest.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
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

}

?>
