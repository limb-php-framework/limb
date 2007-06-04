<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSQLCriteriaTest.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
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
