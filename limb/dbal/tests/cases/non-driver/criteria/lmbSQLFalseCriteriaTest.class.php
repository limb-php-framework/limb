<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSQLFalseCriteriaTest.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */
lmb_require('limb/dbal/src/criteria/lmbSQLFalseCriteria.class.php');

class lmbSQLFalseCriteriaTest extends UnitTestCase
{
  function testAppendStatementTo()
  {
    $c = new lmbSQLFalseCriteria();

    $str = '';
    $c->appendStatementTo($str);
    $this->assertEqual($str, '1 = 0');
  }
}

?>
